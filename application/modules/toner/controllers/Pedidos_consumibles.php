<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos_consumibles extends MY_Controller
{

	/**
	 * Controlador de Pedidos Consumibles
	 * Autor: Leandro
	 * Creado: 09/05/2019
	 * Modificado: 26/08/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Areas_model');
		$this->load->model('toner/Consumibles_model');
		$this->load->model('toner/Impresoras_model');
		$this->load->model('toner/Pedidos_consumibles_model');
		$this->load->model('toner/Pedidos_consumibles_detalles_model');
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
						array('label' => 'Fecha', 'data' => 'fecha_solicitud', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => 'Área', 'data' => 'area', 'width' => 20),
						array('label' => 'Responsable', 'data' => 'resp_solicitud', 'width' => 20),
						array('label' => 'Observaciones', 'data' => 'observacion', 'width' => 31),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 10),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'anular', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'pedidos_consumibles_table',
				'source_url' => 'toner/pedidos_consumibles/listar_data',
				'order' => array(array(0, 'desc')),
				'reuse_var' => TRUE,
				'initComplete' => "complete_pedidos_consumibles_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Pedidos Consumibles';
		$data['title'] = TITLE . ' - Pedidos Consumibles';
		$this->load_template('toner/pedidos_consumibles/pedidos_consumibles_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('toner/datatables_functions_helper');
		$this->datatables
				->select("gt_pedidos_consumibles.id, gt_pedidos_consumibles.fecha_solicitud, CONCAT(areas.codigo, ' - ', areas.nombre) as area, gt_pedidos_consumibles.resp_solicitud, gt_pedidos_consumibles.observacion, gt_pedidos_consumibles.estado")
				->from('gt_pedidos_consumibles')
				->join('areas', 'areas.id = gt_pedidos_consumibles.area_id', 'left')
				->edit_column('estado', '$1', 'dt_column_pedidos_consumibles_estado(estado)', TRUE)
				->add_column('ver', '<a href="toner/pedidos_consumibles/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '$1', 'dt_column_pedidos_consumibles_editar(estado, id)')
				->add_column('anular', '$1', 'dt_column_pedidos_consumibles_anular(estado, id)');

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
			redirect('toner/pedidos_consumibles/listar', 'refresh');
		}

		$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
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
				$this->form_validation->set_rules('oc_' . $i, 'Orden de Compra ' . $i, 'max_length[50]');
			}
		}
		$this->set_model_validation_rules($this->Pedidos_consumibles_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Pedidos_consumibles_model->create(array(
					'area_id' => $this->input->post('area'),
					'fecha_solicitud' => $this->get_datetime_sql('fecha_solicitud'),
					'resp_solicitud' => $this->input->post('resp_solicitud'),
					'observacion' => $this->input->post('observacion'),
					'user_id' => $this->session->userdata('user_id'),
					'estado' => 'Pendiente'), FALSE);

			$pedido_consumibles_id = $this->Pedidos_consumibles_model->get_row_id();
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$consumible_id = $this->input->post('consumible_' . $i);
				$oc = $this->input->post('oc_' . $i);
				$trans_ok &= $this->Pedidos_consumibles_detalles_model->create(array(
						'pedido_consumibles_id' => $pedido_consumibles_id,
						'consumible_id' => $consumible_id,
						'orden_compra' => $oc), FALSE);
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Pedidos_consumibles_model->get_msg());
				redirect('toner/pedidos_consumibles/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Pedidos_consumibles_model->get_error())
				{
					$error_msg .= $this->Pedidos_consumibles_model->get_error();
				}
				if ($this->Pedidos_consumibles_detalles_model->get_error())
				{
					$error_msg .= $this->Pedidos_consumibles_detalles_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Pedidos_consumibles_model->fields['area']['array'] = $array_area;
		$data['fields'] = $this->build_fields($this->Pedidos_consumibles_model->fields);

		$rows = $this->form_validation->set_value('cant_rows', 1);
		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"impresora_$i" => array('label' => 'Impresora', 'input_type' => 'combo', 'type' => 'bselect', 'class' => 'select_impresora'),
					"consumible_$i" => array('label' => 'Consumible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"oc_$i" => array('label' => 'Orden de Compra', 'type' => 'text', 'required' => TRUE),
			);

			$fake_model_fields["impresora_$i"]['array'] = array();
			$fake_model_fields["consumible_$i"]['array'] = array();
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, NULL, FALSE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Pedido Consumibles';
		$data['title'] = TITLE . ' - Agregar Pedido Consumibles';
		$data['js'] = 'js/toner/base.js';
		$this->load_template('toner/pedidos_consumibles/pedidos_consumibles_abm', $data);
	}

	public function editar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("toner/pedidos_consumibles/ver/$id", 'refresh');
		}

		$pedido_consumibles = $this->Pedidos_consumibles_model->get_one($id);
		if (empty($pedido_consumibles) || $pedido_consumibles->estado === 'Anulado')
		{
			show_error('No se encontró el Pedido Consumibles', 500, 'Registro no encontrado');
		}

		$detalles_actuales = $this->Pedidos_consumibles_detalles_model->get(array(
				'pedido_consumibles_id' => $id,
				'join' => array(
						array('gt_consumibles', 'gt_consumibles.id = gt_pedidos_consumibles_detalles.consumible_id', 'LEFT', array("CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"))
				)
		));

		$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
		$this->array_impresora_control = $array_impresora = $this->get_array('Impresoras', 'impresora', 'id', array('select' => array("gt_impresoras.id, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"), 'join' => array(array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT'))));
		$this->array_consumible_control = $array_consumible = $this->get_array('Consumibles', 'consumible', 'id', array('select' => array("gt_consumibles.id, CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible")));

		$this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
		if ($this->input->post('cant_rows'))
		{
			$cant_rows = $this->input->post('cant_rows');
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$this->form_validation->set_rules('resp_entrega_' . $i, 'Recibe ' . $i, 'max_length[50]');
				$this->form_validation->set_rules('fecha_entrega_' . $i, 'Fecha ' . $i, 'validate_datetime');
				$this->form_validation->set_rules('oc_' . $i, 'Orden de Compra ' . $i, 'max_length[50]');
			}
		}

		unset($this->Pedidos_consumibles_model->fields['area']['input_type']);
		$this->Pedidos_consumibles_model->fields['area']['required'] = FALSE;
		$this->Pedidos_consumibles_model->fields['area']['readonly'] = TRUE;
		$this->Pedidos_consumibles_model->fields['fecha_solicitud']['required'] = FALSE;
		$this->Pedidos_consumibles_model->fields['fecha_solicitud']['readonly'] = TRUE;
		$this->Pedidos_consumibles_model->fields['resp_solicitud']['required'] = FALSE;
		$this->Pedidos_consumibles_model->fields['resp_solicitud']['readonly'] = TRUE;
		$this->set_model_validation_rules($this->Pedidos_consumibles_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$this->db->trans_begin();
				$trans_ok = TRUE;

				$cant_rows = $this->input->post('cant_rows');
				$estado = 'Finalizado';
				for ($i = 1; $i <= $cant_rows; $i++)
				{
					$fecha_entrega = $this->input->post('fecha_entrega_' . $i);
					if (empty($fecha_entrega))
					{
						$estado = 'Pendiente';
						break;
					}
				}

				$trans_ok &= $this->Pedidos_consumibles_model->update(array(
						'id' => $this->input->post('id'),
						'observacion' => $this->input->post('observacion'),
						'estado' => $estado), FALSE);

				$post_detalles_update = array();
				for ($i = 1; $i <= $cant_rows; $i++)
				{
					$detalle_post = new stdClass();
					$detalle_post->id = $this->input->post('id_detalle_' . $i);
					$detalle_post->resp_entrega = $this->input->post('resp_entrega_' . $i);
					$detalle_post->fecha_entrega = $this->get_datetime_sql('fecha_entrega_' . $i);
					$detalle_post->orden_compra = $this->input->post('oc_' . $i);
					$post_detalles_update[$detalle_post->id] = $detalle_post;
				}

				if (!empty($detalles_actuales))
				{
					foreach ($detalles_actuales as $Detalle_actual)
					{
						if (isset($post_detalles_update[$Detalle_actual->id]))
						{
							$trans_ok &= $this->Pedidos_consumibles_detalles_model->update(array(
									'id' => $Detalle_actual->id,
									'resp_entrega' => $post_detalles_update[$Detalle_actual->id]->resp_entrega,
									'fecha_entrega' => $post_detalles_update[$Detalle_actual->id]->fecha_entrega,
									'orden_compra' => $post_detalles_update[$Detalle_actual->id]->orden_compra), FALSE);

							if (!empty($post_detalles_update[$Detalle_actual->id]->fecha_entrega) && $post_detalles_update[$Detalle_actual->id]->fecha_entrega !== 'NULL')
							{
								if (empty($Detalle_actual->fecha_entrega))
								{
									$trans_ok &= $this->Consumibles_model->update_stock($Detalle_actual->consumible_id, -1, 0, 0);
								}
							}
							else
							{
								if (!empty($Detalle_actual->fecha_entrega))
								{
									$trans_ok &= $this->Consumibles_model->update_stock($Detalle_actual->consumible_id, 1, 0, 0);
								}
							}
						}
					}
				}

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Pedidos_consumibles_model->get_msg());
					redirect('toner/pedidos_consumibles/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Pedidos_consumibles_model->get_error())
					{
						$error_msg .= $this->Pedidos_consumibles_model->get_error();
					}
					if ($this->Pedidos_consumibles_detalles_model->get_error())
					{
						$error_msg .= $this->Pedidos_consumibles_detalles_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Pedidos_consumibles_model->fields, $pedido_consumibles);

		if (empty($_POST))
		{
			$detalles = $detalles_actuales;
		}
		else
		{
			$detalles = array();
		}
		$rows = $this->form_validation->set_value('cant_rows', sizeof($detalles));
		$data['fields_detalle_array'] = array();

		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"id_detalle_$i" => array('label' => 'ID', 'type' => 'hidden', 'readonly' => TRUE),
					"consumible_$i" => array('label' => 'Consumible', 'type' => 'text', 'readonly' => TRUE),
					"resp_entrega_$i" => array('label' => 'Recibe', 'type' => 'text'),
					"fecha_entrega_$i" => array('label' => 'Fecha', 'type' => 'datetime'),
					"oc_$i" => array('label' => 'Orden de Compra', 'type' => 'text')
			);

			if (empty($_POST))
			{
				$temp_detalle = new stdClass();
				$temp_detalle->{"id_detalle_{$i}"} = $detalles[$i - 1]->id;
				$temp_detalle->{"consumible_{$i}"} = $detalles[$i - 1]->consumible;
				$temp_detalle->{"resp_entrega_{$i}"} = $detalles[$i - 1]->resp_entrega;
				$temp_detalle->{"fecha_entrega_{$i}"} = $detalles[$i - 1]->fecha_entrega;
				$temp_detalle->{"oc_{$i}"} = $detalles[$i - 1]->orden_compra;
			}
			else
			{
				$temp_detalle = NULL;
			}

			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, FALSE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['pedido_consumibles'] = $pedido_consumibles;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Pedido Consumibles';
		$data['title'] = TITLE . ' - Pedido Consumibles';
		$data['js'] = 'js/toner/base.js';
		$this->load_template('toner/pedidos_consumibles/pedidos_consumibles_abm', $data);
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
			redirect("toner/pedidos_consumibles/ver/$id", 'refresh');
		}

		$pedido_consumibles = $this->Pedidos_consumibles_model->get_one($id);
		if (empty($pedido_consumibles) || $pedido_consumibles->estado === 'Anulado')
		{
			show_error('No se encontró el Pedido Consumibles', 500, 'Registro no encontrado');
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
			$detalles = $this->Pedidos_consumibles_detalles_model->get(array('pedido_consumibles_id' => $id));
			$trans_ok &= $this->Pedidos_consumibles_model->update(array('id' => $this->input->post('id'), 'estado' => 'Anulado'), FALSE);
			foreach ($detalles as $Detalle)
			{
				if (!empty($Detalle->fecha_entrega))
				{
					$trans_ok &= $this->Consumibles_model->update_stock($Detalle->consumible_id, 1, 0, 0);
				}
			}
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Pedidos_consumibles_model->get_msg());
				redirect('toner/pedidos_consumibles/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Pedidos_consumibles_model->get_error())
				{
					$error_msg .= $this->Pedidos_consumibles_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$detalles = $this->Pedidos_consumibles_detalles_model->get(array(
				'pedido_consumibles_id' => $pedido_consumibles->id,
				'join' => array(
						array('gt_consumibles', 'gt_consumibles.id = gt_pedidos_consumibles_detalles.consumible_id', 'LEFT', array("CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle del Pedido Consumibles', 500, 'Registro no encontrado');
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
					"resp_entrega_$i" => array('label' => 'Recibe', 'type' => 'text'),
					"fecha_entrega_$i" => array('label' => 'Fecha', 'type' => 'datetime'),
					"oc_$i" => array('label' => 'Orden de Compra', 'type' => 'text')
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"consumible_{$i}"} = $detalles[$i - 1]->consumible;
			$temp_detalle->{"resp_entrega_{$i}"} = $detalles[$i - 1]->resp_entrega;
			$temp_detalle->{"fecha_entrega_{$i}"} = $detalles[$i - 1]->fecha_entrega;
			$temp_detalle->{"oc_{$i}"} = $detalles[$i - 1]->orden_compra;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Pedidos_consumibles_model->fields['estado'] = array('label' => 'Estado', 'maxlength' => '50');
		$data['fields'] = $this->build_fields($this->Pedidos_consumibles_model->fields, $pedido_consumibles, TRUE);
		$data['pedido_consumibles'] = $pedido_consumibles;
		$data['txt_btn'] = 'Anular';
		$data['title_view'] = 'Anular Pedido Consumibles';
		$data['title'] = TITLE . ' - Anular Pedido Consumibles';
		$this->load_template('toner/pedidos_consumibles/pedidos_consumibles_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$pedido_consumibles = $this->Pedidos_consumibles_model->get_one($id);
		if (empty($pedido_consumibles))
		{
			show_error('No se encontró el Pedido Consumibles', 500, 'Registro no encontrado');
		}

		$detalles = $this->Pedidos_consumibles_detalles_model->get(array(
				'pedido_consumibles_id' => $pedido_consumibles->id,
				'join' => array(
						array('gt_consumibles', 'gt_consumibles.id = gt_pedidos_consumibles_detalles.consumible_id', 'LEFT', array("CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle del Pedido Consumibles', 500, 'Registro no encontrado');
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
					"resp_entrega_$i" => array('label' => 'Recibe', 'type' => 'text'),
					"fecha_entrega_$i" => array('label' => 'Fecha', 'type' => 'datetime'),
					"oc_$i" => array('label' => 'Orden de Compra', 'type' => 'text')
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"consumible_{$i}"} = $detalles[$i - 1]->consumible;
			$temp_detalle->{"resp_entrega_{$i}"} = $detalles[$i - 1]->resp_entrega;
			$temp_detalle->{"fecha_entrega_{$i}"} = $detalles[$i - 1]->fecha_entrega;
			$temp_detalle->{"oc_{$i}"} = $detalles[$i - 1]->orden_compra;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Pedidos_consumibles_model->fields['estado'] = array('label' => 'Estado', 'maxlength' => '50');
		$data['fields'] = $this->build_fields($this->Pedidos_consumibles_model->fields, $pedido_consumibles, TRUE);
		$data['pedido_consumibles'] = $pedido_consumibles;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Pedido Consumibles';
		$data['title'] = TITLE . ' - Ver Pedido Consumibles';
		$this->load_template('toner/pedidos_consumibles/pedidos_consumibles_abm', $data);
	}
}