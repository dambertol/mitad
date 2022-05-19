<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compras extends MY_Controller
{

	/**
	 * Controlador de Compras
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 22/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('desarrollo_social/Articulos_model');
		$this->load->model('desarrollo_social/Compras_model');
		$this->load->model('desarrollo_social/Detalle_compras_model');
		$this->load->model('desarrollo_social/Proveedores_model');
		$this->grupos_permitidos = array('admin', 'desarrollo_social_user', 'desarrollo_social_consulta_general');
		$this->grupos_solo_consulta = array('desarrollo_social_consulta_general');
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
						array('label' => 'ID', 'data' => 'id', 'width' => 6, 'class' => 'dt-body-right'),
						array('label' => 'Fecha Recepción', 'data' => 'fecha_recepcion', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => 'Proveedor', 'data' => 'proveedor', 'width' => 18),
						array('label' => 'Lugar Físico', 'data' => 'lugar_fisico', 'width' => 16),
						array('label' => 'Nro Orden', 'data' => 'nro_orden', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Recepcionista', 'data' => 'recepcionista', 'width' => 16),
						array('label' => 'Nro Remito', 'data' => 'nro_remito', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
						array('label' => '', 'data' => 'imprimir', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'anular', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'compras_table',
				'source_url' => 'desarrollo_social/compras/listar_data',
				'order' => array(array(0, 'desc')),
				'reuse_var' => TRUE,
				'initComplete' => 'complete_compras_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['array_estados'] = array('' => 'Todos', 'Activa' => 'Activa', 'Anulada' => 'Anulada');
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Compras';
		$data['title'] = TITLE . ' - Compras';
		$this->load_template('desarrollo_social/compras/compras_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('desarrollo_social/datatables_functions_helper');
		$this->datatables
				->select('ds_compras.id, ds_compras.fecha_recepcion, ds_proveedores.razon_social as proveedor, ds_compras.lugar_fisico, ds_compras.nro_orden, ds_compras.recepcionista, ds_compras.nro_remito, ds_compras.estado')
				->from('ds_compras')
				->join('ds_proveedores', 'ds_proveedores.id = ds_compras.proveedor_id', 'left')
				->edit_column('estado', '$1', 'dt_column_compras_estado(estado)', TRUE)
				->add_column('imprimir', '<a href="desarrollo_social/compras/imprimir/$1" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>', 'id')
				->add_column('ver', '<a href="desarrollo_social/compras/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '$1', 'dt_column_compras_editar(estado, id)')
				->add_column('anular', '$1', 'dt_column_compras_anular(estado, id)');

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
			redirect('desarrollo_social/compras/listar', 'refresh');
		}

		$this->array_proveedor_control = $array_proveedor = $this->get_array('Proveedores', 'razon_social');
		$this->array_articulo_control = $array_articulo = $this->get_array('Articulos', 'articulo', 'id', array('select' => array("id, CONCAT(id, ' - ', nombre, ' - ', COALESCE(cantidad_real, 0)) as articulo")));

		$this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
		if ($this->input->post('cant_rows'))
		{
			$cant_rows = $this->input->post('cant_rows');
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$this->form_validation->set_rules('articulo_' . $i, 'Artículo ' . $i, 'required|callback_control_combo[articulo]');
				$this->form_validation->set_rules('cantidad_' . $i, 'Cantidad ' . $i, 'required|numeric');
				$this->form_validation->set_rules('valor_' . $i, 'Valor Unitario ' . $i, 'required|numeric');
				$this->form_validation->set_rules('expediente_' . $i, 'Expediente ' . $i, 'max_length[20]');
			}
		}
		$this->set_model_validation_rules($this->Compras_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Compras_model->create(array(
					'fecha_recepcion' => $this->get_datetime_sql('fecha_recepcion'),
					'proveedor_id' => $this->input->post('proveedor'),
					'lugar_fisico' => $this->input->post('lugar_fisico'),
					'nro_orden' => $this->input->post('nro_orden'),
					'recepcionista' => $this->input->post('recepcionista'),
					'ubicacion' => $this->input->post('ubicacion'),
					'nro_remito' => $this->input->post('nro_remito'),
					'estado' => 'Activa'), FALSE);

			$compras_id = $this->Compras_model->get_row_id();
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$articulo_id = $this->input->post('articulo_' . $i);
				$cantidad = $this->input->post('cantidad_' . $i);
				$valor = $this->input->post('valor_' . $i);
				$expediente = $this->input->post('expediente_' . $i);
				$trans_ok &= $this->Detalle_compras_model->create(array(
						'compra_id' => $compras_id,
						'articulo_id' => $articulo_id,
						'cantidad' => $cantidad,
						'valor' => $valor,
						'expediente' => $expediente), FALSE);

				$trans_ok &= $this->Articulos_model->update(array(
						'id' => $articulo_id,
						'cantidad_real' => 'cantidad_real + ' . $cantidad), FALSE, FALSE);
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Compras_model->get_msg());
				redirect('desarrollo_social/compras/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Compras_model->get_error())
				{
					$error_msg .= $this->Compras_model->get_error();
				}
				if ($this->Detalle_compras_model->get_error())
				{
					$error_msg .= $this->Detalle_compras_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Compras_model->fields['proveedor']['array'] = $array_proveedor;
		$data['fields'] = $this->build_fields($this->Compras_model->fields);

		$rows = $this->form_validation->set_value('cant_rows', 1);
		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE),
					"valor_$i" => array('label' => 'Valor Unitario', 'type' => 'numeric', 'required' => TRUE),
					"expediente_$i" => array('label' => 'Expediente', 'type' => 'text')
			);

			$fake_model_fields["articulo_$i"]['array'] = $array_articulo;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, NULL, FALSE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Compra';
		$data['title'] = TITLE . ' - Agregar Compra';
		$data['js'] = 'js/desarrollo_social/base.js';
		$this->load_template('desarrollo_social/compras/compras_abm', $data);
	}

	public function editar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("desarrollo_social/compras/ver/$id", 'refresh');
		}

		$this->array_proveedor_control = $array_proveedor = $this->get_array('Proveedores', 'razon_social');
		$compra = $this->Compras_model->get(array('id' => $id));
		if (empty($compra))
		{
			show_error('No se encontró la Compra', 500, 'Registro no encontrado');
		}
		if ($compra->estado === 'Anulada')
		{
			redirect("desarrollo_social/compras/ver/$id", 'refresh');
		}

		$this->set_model_validation_rules($this->Compras_model);
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
				$trans_ok &= $this->Compras_model->update(array(
						'id' => $this->input->post('id'),
						'fecha_recepcion' => $this->get_datetime_sql('fecha_recepcion'),
						'proveedor_id' => $this->input->post('proveedor'),
						'lugar_fisico' => $this->input->post('lugar_fisico'),
						'nro_orden' => $this->input->post('nro_orden'),
						'recepcionista' => $this->input->post('recepcionista'),
						'nro_remito' => $this->input->post('nro_remito')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Compras_model->get_msg());
					redirect('desarrollo_social/compras/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Compras_model->get_error())
					{
						$error_msg .= $this->Compras_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$detalles = $this->Detalle_compras_model->get(array(
				'compra_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_compras.articulo_id', 'LEFT', array("CONCAT(ds_articulos.id, ' - ', ds_articulos.nombre, ' - ', COALESCE(ds_articulos.cantidad_real, 0)) as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Compra', 500, 'Registro no encontrado');
		}
		else
		{
			$rows = count($detalles);
		}

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE),
					"valor_$i" => array('label' => 'Valor Unitario', 'type' => 'numeric', 'required' => TRUE),
					"expediente_$i" => array('label' => 'Expediente', 'type' => 'text')
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"articulo_{$i}"} = $detalles[$i - 1]->articulo;
			$temp_detalle->{"cantidad_{$i}"} = $detalles[$i - 1]->cantidad;
			$temp_detalle->{"valor_{$i}"} = $detalles[$i - 1]->valor;
			$temp_detalle->{"expediente_{$i}"} = $detalles[$i - 1]->expediente;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Compras_model->fields['proveedor']['array'] = $array_proveedor;
		$data['fields'] = $this->build_fields($this->Compras_model->fields, $compra);
		$data['compra'] = $compra;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Compra';
		$data['title'] = TITLE . ' - Editar Compra';
		$this->load_template('desarrollo_social/compras/compras_abm', $data);
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
			redirect("desarrollo_social/compras/ver/$id", 'refresh');
		}

		$compra = $this->Compras_model->get_one($id);
		if (empty($compra))
		{
			show_error('No se encontró la Compra', 500, 'Registro no encontrado');
		}
		if ($compra->estado === 'Anulada')
		{
			redirect("desarrollo_social/compras/ver/$id", 'refresh');
		}

		$detalles = $this->Detalle_compras_model->get(array(
				'compra_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_compras.articulo_id', 'LEFT', array("CONCAT(ds_articulos.id, ' - ', ds_articulos.nombre, ' - ', COALESCE(ds_articulos.cantidad_real, 0)) as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Compra', 500, 'Registro no encontrado');
		}
		else
		{
			$rows = count($detalles);
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
			$trans_ok &= $this->Compras_model->update(array('id' => $this->input->post('id'), 'estado' => 'Anulada'), FALSE);
			foreach ($detalles as $Articulo)
			{
				$trans_ok &= $this->Articulos_model->update(array(
						'id' => $Articulo->articulo_id,
						'cantidad_real' => 'cantidad_real - ' . $Articulo->cantidad), FALSE, FALSE);
			}
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Compras_model->get_msg());
				redirect('desarrollo_social/compras/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Compras_model->get_error())
				{
					$error_msg .= $this->Compras_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE),
					"valor_$i" => array('label' => 'Valor Unitario', 'type' => 'numeric', 'required' => TRUE),
					"expediente_$i" => array('label' => 'Expediente', 'type' => 'text')
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"articulo_{$i}"} = $detalles[$i - 1]->articulo;
			$temp_detalle->{"cantidad_{$i}"} = $detalles[$i - 1]->cantidad;
			$temp_detalle->{"valor_{$i}"} = $detalles[$i - 1]->valor;
			$temp_detalle->{"expediente_{$i}"} = $detalles[$i - 1]->expediente;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Compras_model->fields['estado'] = array('label' => 'Estado', 'required' => TRUE);
		$data['fields'] = $this->build_fields($this->Compras_model->fields, $compra, TRUE);
		$data['compra'] = $compra;
		$data['txt_btn'] = 'Anular';
		$data['title_view'] = 'Anular Compra';
		$data['title'] = TITLE . ' - Anular Compra';
		$this->load_template('desarrollo_social/compras/compras_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$compra = $this->Compras_model->get_one($id);
		if (empty($compra))
		{
			show_error('No se encontró la Compra', 500, 'Registro no encontrado');
		}

		$detalles = $this->Detalle_compras_model->get(array(
				'compra_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_compras.articulo_id', 'LEFT', array("CONCAT(ds_articulos.id, ' - ', ds_articulos.nombre, ' - ', COALESCE(ds_articulos.cantidad_real, 0)) as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Compra', 500, 'Registro no encontrado');
		}
		else
		{
			$rows = count($detalles);
		}

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE),
					"valor_$i" => array('label' => 'Valor Unitario', 'type' => 'numeric', 'required' => TRUE),
					"expediente_$i" => array('label' => 'Expediente', 'type' => 'text')
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"articulo_{$i}"} = $detalles[$i - 1]->articulo;
			$temp_detalle->{"cantidad_{$i}"} = $detalles[$i - 1]->cantidad;
			$temp_detalle->{"valor_{$i}"} = $detalles[$i - 1]->valor;
			$temp_detalle->{"expediente_{$i}"} = $detalles[$i - 1]->expediente;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['fields'] = $this->build_fields($this->Compras_model->fields, $compra, TRUE);
		$data['compra'] = $compra;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Compra';
		$data['title'] = TITLE . ' - Ver Compra';
		$this->load_template('desarrollo_social/compras/compras_abm', $data);
	}

	public function imprimir($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$compra = $this->Compras_model->get_one($id);
		if (empty($compra))
		{
			show_error('No se encontró la Compra', 500, 'Registro no encontrado');
		}

		$detalles = $this->Detalle_compras_model->get(array(
				'compra_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_compras.articulo_id', 'LEFT', array("ds_articulos.nombre as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Compra', 500, 'Registro no encontrado');
		}

		$data['compra'] = $compra;
		$data['detalles'] = $detalles;
		$data['title_view'] = 'Imprimir Compra';
		$data['title'] = TITLE . ' - Imprimir Compra';
		$data['css'][] = 'css/desarrollo_social/imprimir.css';
		$this->load_template('desarrollo_social/compras/compras_print', $data);
	}
}