<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos extends MY_Controller
{

	/**
	 * Controlador de Movimientos
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 27/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toner/Consumibles_model');
		$this->load->model('toner/Impresoras_model');
		$this->load->model('toner/Movimientos_model');
		$this->load->model('toner/Movimientos_detalles_model');
		$this->grupos_permitidos = array('admin', 'toner_admin', 'toner_consulta_general');
		$this->grupos_solo_consulta = array('toner_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tableData = array(
				'columns' => array(
						array('label' => 'Fecha', 'data' => 'fecha_movimiento', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => 'Orden de Compra', 'data' => 'orden_compra', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Fecha OC', 'data' => 'fecha_oc', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Monto OC', 'data' => 'monto_oc', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 44),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 10),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'anular', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'movimientos_table',
				'source_url' => 'toner/movimientos/listar_data',
				'order' => array(array(0, 'desc')),
				'reuse_var' => TRUE,
				'initComplete' => "complete_movimientos_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Movimientos';
		$data['title'] = TITLE . ' - Movimientos';
		$this->load_template('toner/movimientos/movimientos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('toner/datatables_functions_helper');
		$this->datatables
				->select('id, fecha_movimiento, observaciones, estado, orden_compra, fecha_oc, monto_oc')
				->from('gt_movimientos')
				->edit_column('estado', '$1', 'dt_column_movimientos_estado(estado)', TRUE)
				->add_column('ver', '<a href="toner/movimientos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('anular', '$1', 'dt_column_movimientos_anular(estado, id)');

		echo $this->datatables->generate();
	}

	public function agregar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect('toner/movimientos/listar', 'refresh');
		}

		$this->array_impresora_control = $array_impresora = $this->get_array('Impresoras', 'impresora', 'id', array('select' => array("gt_impresoras.id, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"), 'join' => array(array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT'))));
		$this->array_consumible_control = $array_consumible = $this->get_array('Consumibles', 'consumible', 'id', array('select' => array("gt_consumibles.id, CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"), 'where' => array(array('column' => 'estado', 'value' => 'Activo'))));

		$this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
		if ($this->input->post('cant_rows'))
		{
			$cant_rows = $this->input->post('cant_rows');
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$this->form_validation->set_rules('impresora_' . $i, 'Impresora ' . $i, 'callback_control_combo[impresora]');
				$this->form_validation->set_rules('consumible_' . $i, 'Consumible ' . $i, 'required|callback_control_combo[consumible]');
				$this->form_validation->set_rules('llenos_' . $i, 'Llenos ' . $i, 'required|integer');
				$this->form_validation->set_rules('vacios_' . $i, 'Vacíos ' . $i, 'required|integer');
				$this->form_validation->set_rules('fuera_servicio_' . $i, 'Fuera de Servicio ' . $i, 'required|integer');
			}
		}
		$this->set_model_validation_rules($this->Movimientos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Movimientos_model->create(array(
					'fecha_movimiento' => $this->get_datetime_sql('fecha_movimiento'),
					'orden_compra' => $this->input->post('orden_compra'),
					'fecha_oc' => $this->get_date_sql('fecha_oc'),
					'monto_oc' => $this->input->post('monto_oc'),
					'observaciones' => $this->input->post('observaciones'),
					'estado' => 'Activo'), FALSE);

			$movimiento_id = $this->Movimientos_model->get_row_id();
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$consumible_id = $this->input->post('consumible_' . $i);
				$llenos = $this->input->post('llenos_' . $i);
				$vacios = $this->input->post('vacios_' . $i);
				$fuera_s = $this->input->post('fuera_servicio_' . $i);
				$trans_ok &= $this->Movimientos_detalles_model->create(array(
						'movimiento_id' => $movimiento_id,
						'consumible_id' => $consumible_id,
						'cantidad_llenos' => $llenos,
						'cantidad_vacios' => $vacios,
						'cantidad_fuera_servicio' => $fuera_s), FALSE);

				$trans_ok &= $this->Consumibles_model->update_stock($consumible_id, $llenos, $vacios, $fuera_s);
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Movimientos_model->get_msg());
				redirect('toner/movimientos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Movimientos_model->get_error())
				{
					$error_msg .= $this->Movimientos_model->get_error();
				}
				if ($this->Movimientos_detalles_model->get_error())
				{
					$error_msg .= $this->Movimientos_detalles_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Movimientos_model->fields);

		$rows = $this->form_validation->set_value('cant_rows', 1);
		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"impresora_$i" => array('label' => 'Impresora', 'input_type' => 'combo', 'type' => 'bselect', 'class' => 'select_impresora'),
					"consumible_$i" => array('label' => 'Consumible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"llenos_$i" => array('label' => 'Llenos', 'type' => 'integer_with_neg', 'required' => TRUE),
					"vacios_$i" => array('label' => 'Vacíos', 'type' => 'integer_with_neg', 'required' => TRUE),
					"fuera_servicio_$i" => array('label' => 'Fuera de Servicio', 'type' => 'integer_with_neg', 'required' => TRUE)
			);

			$fake_model_fields["impresora_$i"]['array'] = $array_impresora;
			$fake_model_fields["consumible_$i"]['array'] = $array_consumible;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, NULL, FALSE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Movimiento';
		$data['title'] = TITLE . ' - Agregar Movimiento';
		$data['js'] = 'js/toner/base.js';
		$this->load_template('toner/movimientos/movimientos_abm', $data);
	}

	public function anular($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("toner/movimientos/ver/$id", 'refresh');
		}

		$movimiento = $this->Movimientos_model->get_one($id);
		if (empty($movimiento) || $movimiento->estado === 'Anulado')
		{
			show_error('No se encontró el Movimiento', 500, 'Registro no encontrado');
		}

		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$detalles = $this->Movimientos_detalles_model->get(array('movimiento_id' => $id));
			$trans_ok &= $this->Movimientos_model->update(array('id' => $this->input->post('id'), 'estado' => 'Anulado'), FALSE);
			foreach ($detalles as $Detalle)
			{
				$trans_ok &= $this->Consumibles_model->update_stock($Detalle->consumible_id, $Detalle->cantidad_llenos * (-1), $Detalle->cantidad_vacios * (-1), $Detalle->cantidad_fuera_servicio * (-1));
			}
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Movimientos_model->get_msg());
				redirect('toner/movimientos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Movimientos_model->get_error())
				{
					$error_msg .= $this->Movimientos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$detalles = $this->Movimientos_detalles_model->get(array(
				'movimiento_id' => $movimiento->id,
				'join' => array(
						array('gt_consumibles', 'gt_consumibles.id = gt_movimientos_detalles.consumible_id', 'LEFT', array("CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle del Movimiento', 500, 'Registro no encontrado');
		}
		else
		{
			$rows = count($detalles);
		}

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"consumible_$i" => array('label' => 'Consumible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"llenos_$i" => array('label' => 'Llenos', 'type' => 'integer', 'required' => TRUE),
					"vacios_$i" => array('label' => 'Vacíos', 'type' => 'integer', 'required' => TRUE),
					"fuera_servicio_$i" => array('label' => 'Fuera de Servicio', 'type' => 'integer', 'required' => TRUE)
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"consumible_{$i}"} = $detalles[$i - 1]->consumible;
			$temp_detalle->{"llenos_{$i}"} = $detalles[$i - 1]->cantidad_llenos;
			$temp_detalle->{"vacios_{$i}"} = $detalles[$i - 1]->cantidad_vacios;
			$temp_detalle->{"fuera_servicio_{$i}"} = $detalles[$i - 1]->cantidad_fuera_servicio;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Movimientos_model->fields['estado'] = array('label' => 'Estado', 'maxlength' => '50');
		$data['fields'] = $this->build_fields($this->Movimientos_model->fields, $movimiento, TRUE);
		$data['movimiento'] = $movimiento;
		$data['txt_btn'] = 'Anular';
		$data['title_view'] = 'Anular Movimiento';
		$data['title'] = TITLE . ' - Anular Movimiento';
		$this->load_template('toner/movimientos/movimientos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$movimiento = $this->Movimientos_model->get_one($id);
		if (empty($movimiento))
		{
			show_error('No se encontró el Movimiento', 500, 'Registro no encontrado');
		}

		$detalles = $this->Movimientos_detalles_model->get(array(
				'movimiento_id' => $movimiento->id,
				'join' => array(
						array('gt_consumibles', 'gt_consumibles.id = gt_movimientos_detalles.consumible_id', 'LEFT', array("CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle del Movimiento', 500, 'Registro no encontrado');
		}
		else
		{
			$rows = count($detalles);
		}

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"consumible_$i" => array('label' => 'Consumible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"llenos_$i" => array('label' => 'Llenos', 'type' => 'integer', 'required' => TRUE),
					"vacios_$i" => array('label' => 'Vacíos', 'type' => 'integer', 'required' => TRUE),
					"fuera_servicio_$i" => array('label' => 'Fuera de Servicio', 'type' => 'integer', 'required' => TRUE)
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"consumible_{$i}"} = $detalles[$i - 1]->consumible;
			$temp_detalle->{"llenos_{$i}"} = $detalles[$i - 1]->cantidad_llenos;
			$temp_detalle->{"vacios_{$i}"} = $detalles[$i - 1]->cantidad_vacios;
			$temp_detalle->{"fuera_servicio_{$i}"} = $detalles[$i - 1]->cantidad_fuera_servicio;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Movimientos_model->fields['estado'] = array('label' => 'Estado', 'maxlength' => '50');
		$data['fields'] = $this->build_fields($this->Movimientos_model->fields, $movimiento, TRUE);
		$data['movimiento'] = $movimiento;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Movimiento';
		$data['title'] = TITLE . ' - Ver Movimiento';
		$this->load_template('toner/movimientos/movimientos_abm', $data);
	}
}