<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entregas extends MY_Controller
{

	/**
	 * Controlador de Entregas
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 22/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('desarrollo_social/Articulos_model');
		$this->load->model('desarrollo_social/Entregas_model');
		$this->load->model('desarrollo_social/Detalle_entregas_model');
		$this->load->model('desarrollo_social/Beneficiarios_model');
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
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => 'Beneficiario', 'data' => 'beneficiario', 'width' => 14),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 14),
						array('label' => 'Destino', 'data' => 'destino', 'width' => 14),
						array('label' => 'Responsable', 'data' => 'responsable', 'width' => 14),
						array('label' => 'Trabajador Social', 'data' => 'trabajadora_social', 'width' => 10),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
						array('label' => '', 'data' => 'imprimir', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'anular', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'entregas_table',
				'source_url' => 'desarrollo_social/entregas/listar_data',
				'order' => array(array(0, 'desc')),
				'reuse_var' => TRUE,
				'initComplete' => 'complete_entregas_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['array_estados'] = array('' => 'Todos', 'Activa' => 'Activa', 'Anulada' => 'Anulada');
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Entregas';
		$data['title'] = TITLE . ' - Entregas';
		$this->load_template('desarrollo_social/entregas/entregas_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('desarrollo_social/datatables_functions_helper');
		$this->datatables
				->select("ds_entregas.id, ds_entregas.fecha, ds_entregas.descripcion, ds_entregas.destino, CONCAT(ds_beneficiarios.apellido, ', ', ds_beneficiarios.nombre,  ' (', ds_beneficiarios.dni, ')') as beneficiario, ds_entregas.responsable, ds_entregas.trabajadora_social, ds_entregas.estado")
				->from('ds_entregas')
				->join('ds_beneficiarios', 'ds_beneficiarios.id = ds_entregas.beneficiario_id', 'left')
				->edit_column('estado', '$1', 'dt_column_entregas_estado(estado)', TRUE)
				->add_column('imprimir', '<a href="desarrollo_social/entregas/imprimir/$1" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>', 'id')
				->add_column('ver', '<a href="desarrollo_social/entregas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '$1', 'dt_column_entregas_editar(estado, id)')
				->add_column('anular', '$1', 'dt_column_entregas_anular(estado, id)');

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
			redirect('desarrollo_social/entregas/listar', 'refresh');
		}

		$this->array_beneficiario_control = $array_beneficiario = $this->get_array('Beneficiarios', 'beneficiario', 'id', array(
				'select' => "ds_beneficiarios.id, CONCAT(ds_beneficiarios.apellido, ', ', ds_beneficiarios.nombre, ' (', ds_beneficiarios.dni, ')') as beneficiario",
				'sort_by' => 'ds_beneficiarios.apellido, ds_beneficiarios.nombre'
		));
		$this->array_articulo_control = $array_articulo = $this->get_array('Articulos', 'articulo', 'id', array('select' => array("id, CONCAT(id, ' - ', nombre, ' - ', COALESCE(cantidad_real, 0)) as articulo")));

		$this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
		if ($this->input->post('cant_rows'))
		{
			$cant_rows = $this->input->post('cant_rows');
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$this->form_validation->set_rules('articulo_' . $i, 'Artículo ' . $i, 'required|callback_control_combo[articulo]');
				$this->form_validation->set_rules('cantidad_' . $i, 'Cantidad ' . $i, 'required|numeric');
			}
		}

		$this->set_model_validation_rules($this->Entregas_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Entregas_model->create(array(
					'fecha' => $this->get_datetime_sql('fecha'),
					'descripcion' => $this->input->post('descripcion'),
					'destino' => $this->input->post('destino'),
					'beneficiario_id' => $this->input->post('beneficiario'),
					'responsable' => $this->input->post('responsable'),
					'trabajadora_social' => $this->input->post('trabajadora_social'),
					'estado' => 'Activa'), FALSE);

			$entregas_id = $this->Entregas_model->get_row_id();
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$articulo_id = $this->input->post('articulo_' . $i);
				$cantidad = $this->input->post('cantidad_' . $i);
				$trans_ok &= $this->Detalle_entregas_model->create(array(
						'entrega_id' => $entregas_id,
						'articulo_id' => $articulo_id,
						'cantidad' => $cantidad), FALSE);

				$trans_ok &= $this->Articulos_model->update(array(
						'id' => $articulo_id,
						'cantidad_real' => 'cantidad_real - ' . $cantidad), FALSE, FALSE);
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Entregas_model->get_msg());
				redirect('desarrollo_social/entregas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Entregas_model->get_error())
				{
					$error_msg .= $this->Entregas_model->get_error();
				}
				if ($this->Detalle_entregas_model->get_error())
				{
					$error_msg .= $this->Detalle_entregas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Entregas_model->fields['beneficiario']['array'] = $array_beneficiario;
		$data['fields'] = $this->build_fields($this->Entregas_model->fields);

		$rows = $this->form_validation->set_value('cant_rows', 1);
		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE)
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
		$data['title_view'] = 'Agregar Entrega';
		$data['title'] = TITLE . ' - Agregar Entrega';
		$data['js'] = 'js/desarrollo_social/base.js';
		$this->load_template('desarrollo_social/entregas/entregas_abm', $data);
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
			redirect("desarrollo_social/entregas/ver/$id", 'refresh');
		}

		$this->array_beneficiario_control = $array_beneficiario = $this->get_array('Beneficiarios', 'beneficiario', 'id', array(
				'select' => "ds_beneficiarios.id, CONCAT(ds_beneficiarios.apellido, ', ', ds_beneficiarios.nombre, ' (', ds_beneficiarios.dni, ')') as beneficiario",
				'sort_by' => 'ds_beneficiarios.apellido, ds_beneficiarios.nombre'
		));
		$entrega = $this->Entregas_model->get(array('id' => $id));
		if (empty($entrega))
		{
			show_error('No se encontró la Entrega', 500, 'Registro no encontrado');
		}
		if ($entrega->estado === 'Anulada')
		{
			redirect("desarrollo_social/entregas/ver/$id", 'refresh');
		}

		$this->set_model_validation_rules($this->Entregas_model);
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
				$trans_ok &= $this->Entregas_model->update(array(
						'id' => $this->input->post('id'),
						'fecha' => $this->get_datetime_sql('fecha'),
						'descripcion' => $this->input->post('descripcion'),
						'destino' => $this->input->post('destino'),
						'beneficiario_id' => $this->input->post('beneficiario'),
						'responsable' => $this->input->post('responsable'),
						'trabajadora_social' => $this->input->post('trabajadora_social')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Entregas_model->get_msg());
					redirect('desarrollo_social/entregas/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Entregas_model->get_error())
					{
						$error_msg .= $this->Entregas_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$detalles = $this->Detalle_entregas_model->get(array(
				'entrega_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_entregas.articulo_id', 'LEFT', array("CONCAT(ds_articulos.id, ' - ', ds_articulos.nombre, ' - ', COALESCE(ds_articulos.cantidad_real, 0)) as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Entrega', 500, 'Registro no encontrado');
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
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE)
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"articulo_{$i}"} = $detalles[$i - 1]->articulo;
			$temp_detalle->{"cantidad_{$i}"} = $detalles[$i - 1]->cantidad;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Entregas_model->fields['beneficiario']['array'] = $array_beneficiario;
		$data['fields'] = $this->build_fields($this->Entregas_model->fields, $entrega);
		$data['entrega'] = $entrega;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Entrega';
		$data['title'] = TITLE . ' - Editar Entrega';
		$this->load_template('desarrollo_social/entregas/entregas_abm', $data);
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
			redirect("desarrollo_social/entregas/ver/$id", 'refresh');
		}

		$entrega = $this->Entregas_model->get_one($id);
		if (empty($entrega))
		{
			show_error('No se encontró la Entrega', 500, 'Registro no encontrado');
		}
		if ($entrega->estado === 'Anulada')
		{
			redirect("desarrollo_social/entregas/ver/$id", 'refresh');
		}

		$detalles = $this->Detalle_entregas_model->get(array(
				'entrega_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_entregas.articulo_id', 'LEFT', array("CONCAT(ds_articulos.id, ' - ', ds_articulos.nombre, ' - ', COALESCE(ds_articulos.cantidad_real, 0)) as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Entrega', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Entregas_model->update(array('id' => $this->input->post('id'), 'estado' => 'Anulada'), FALSE);
			foreach ($detalles as $Articulo)
			{
				$trans_ok &= $this->Articulos_model->update(array(
						'id' => $Articulo->articulo_id,
						'cantidad_real' => 'cantidad_real + ' . $Articulo->cantidad), FALSE, FALSE);
			}
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Entregas_model->get_msg());
				redirect('desarrollo_social/entregas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Entregas_model->get_error())
				{
					$error_msg .= $this->Entregas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE)
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"articulo_{$i}"} = $detalles[$i - 1]->articulo;
			$temp_detalle->{"cantidad_{$i}"} = $detalles[$i - 1]->cantidad;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Entregas_model->fields['estado'] = array('label' => 'Estado', 'required' => TRUE);
		$data['fields'] = $this->build_fields($this->Entregas_model->fields, $entrega, TRUE);
		$data['entrega'] = $entrega;
		$data['txt_btn'] = 'Anular';
		$data['title_view'] = 'Anular Entrega';
		$data['title'] = TITLE . ' - Anular Entrega';
		$this->load_template('desarrollo_social/entregas/entregas_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$entrega = $this->Entregas_model->get_one($id);
		if (empty($entrega))
		{
			show_error('No se encontró el Entrega', 500, 'Registro no encontrado');
		}

		$detalles = $this->Detalle_entregas_model->get(array(
				'entrega_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_entregas.articulo_id', 'LEFT', array("CONCAT(ds_articulos.id, ' - ', ds_articulos.nombre, ' - ', COALESCE(ds_articulos.cantidad_real, 0)) as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Entrega', 500, 'Registro no encontrado');
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
					"cantidad_$i" => array('label' => 'Cantidad', 'type' => 'numeric', 'required' => TRUE)
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"articulo_{$i}"} = $detalles[$i - 1]->articulo;
			$temp_detalle->{"cantidad_{$i}"} = $detalles[$i - 1]->cantidad;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['fields'] = $this->build_fields($this->Entregas_model->fields, $entrega, TRUE);
		$data['entrega'] = $entrega;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Entrega';
		$data['title'] = TITLE . ' - Ver Entrega';
		$this->load_template('desarrollo_social/entregas/entregas_abm', $data);
	}

	public function imprimir($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$entrega = $this->Entregas_model->get_one($id);
		if (empty($entrega))
		{
			show_error('No se encontró la Entrega', 500, 'Registro no encontrado');
		}

		$detalles = $this->Detalle_entregas_model->get(array(
				'entrega_id' => $id,
				'join' => array(
						array('ds_articulos', 'ds_articulos.id = ds_detalle_entregas.articulo_id', 'LEFT', array("ds_articulos.nombre as articulo"))
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Entrega', 500, 'Registro no encontrado');
		}

		$data['entrega'] = $entrega;
		$data['detalles'] = $detalles;
		$data['title_view'] = 'Imprimir Entrega';
		$data['title'] = TITLE . ' - Imprimir Entrega';
		$data['css'][] = 'css/desarrollo_social/imprimir.css';
		$this->load_template('desarrollo_social/entregas/entregas_print', $data);
	}
}