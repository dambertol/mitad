<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Destinatarios extends MY_Controller
{

	/**
	 * Controlador de Destinatarios
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('notificaciones/Destinatarios_model');
		$this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_consulta_general');
		$this->grupos_solo_consulta = array('notificaciones_consulta_general');	
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$tableData = array(
			'columns' => array(//@todo arreglar anchos de columnas
				array('label' => 'Id', 'data' => 'id', 'width' => 10),
				array('label' => 'Nombre', 'data' => 'nombre', 'width' => 10),
				array('label' => 'Cuil', 'data' => 'cuil', 'width' => 10),
				array('label' => 'Razon de Social', 'data' => 'razon_social', 'width' => 10),
				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'destinatarios_table',
			'source_url' => 'notificaciones/destinatarios/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_destinatarios_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Destinatarios';
		$data['title'] = TITLE . ' - Destinatarios';
		$this->load_template('notificaciones/destinatarios/destinatarios_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
			->select('id, nombre, cuil, razon_social, audi_usuario, audi_fecha, audi_accion')
			->from('nv_destinatarios')
			->add_column('ver', '<a href="notificaciones/destinatarios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="notificaciones/destinatarios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="notificaciones/destinatarios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('notificaciones/destinatarios/listar', 'refresh');
		}
		
		$this->set_model_validation_rules($this->Destinatarios_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Destinatarios_model->create(array(
				'nombre' => $this->input->post('nombre'),
				'cuil' => $this->input->post('cuil'),
				'razon_social' => $this->input->post('razon_social'),
				'audi_usuario' => $this->input->post('audi_usuario'),
				'audi_fecha' => $this->input->post('audi_fecha'),
				'audi_accion' => $this->input->post('audi_accion')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Destinatarios_model->get_msg());
				redirect('notificaciones/destinatarios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Destinatarios_model->get_error())
				{
					$error_msg .= $this->Destinatarios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Destinatarios_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Destinatario';
		$data['title'] = TITLE . ' - Agregar Destinatario';
		$this->load_template('notificaciones/destinatarios/destinatarios_abm', $data);
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
			redirect("notificaciones/destinatarios/ver/$id", 'refresh');
		}
		
		$destinatario = $this->Destinatarios_model->get(array('id' => $id));
		if (empty($destinatario))
		{
			show_error('No se encontró el Destinatario', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Destinatarios_model);
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
				$trans_ok &= $this->Destinatarios_model->update(array(
					'id' => $this->input->post('id'),
					'nombre' => $this->input->post('nombre'),
					'cuil' => $this->input->post('cuil'),
					'razon_social' => $this->input->post('razon_social'),
					'audi_usuario' => $this->input->post('audi_usuario'),
					'audi_fecha' => $this->input->post('audi_fecha'),
					'audi_accion' => $this->input->post('audi_accion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Destinatarios_model->get_msg());
					redirect('notificaciones/destinatarios/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Destinatarios_model->get_error())
					{
						$error_msg .= $this->Destinatarios_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Destinatarios_model->fields, $destinatario);
		$data['destinatario'] = $destinatario;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Destinatario';
		$data['title'] = TITLE . ' - Editar Destinatario';
		$this->load_template('notificaciones/destinatarios/destinatarios_abm', $data);
	}

	public function eliminar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("notificaciones/destinatarios/ver/$id", 'refresh');
		}

		$destinatario = $this->Destinatarios_model->get_one($id);
		if (empty($destinatario))
		{
			show_error('No se encontró el Destinatario', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Destinatarios_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Destinatarios_model->get_msg());
				redirect('notificaciones/destinatarios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Destinatarios_model->get_error())
				{
					$error_msg .= $this->Destinatarios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Destinatarios_model->fields, $destinatario, TRUE);
		$data['destinatario'] = $destinatario;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Destinatario';
		$data['title'] = TITLE . ' - Eliminar Destinatario';
		$this->load_template('notificaciones/destinatarios/destinatarios_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$destinatario = $this->Destinatarios_model->get_one($id);
		if (empty($destinatario))
		{
			show_error('No se encontró el Destinatario', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Destinatarios_model->fields, $destinatario, TRUE);
		$data['destinatario'] = $destinatario;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Destinatario';
		$data['title'] = TITLE . ' - Ver Destinatario';
		$this->load_template('notificaciones/destinatarios/destinatarios_abm', $data);
	}
}