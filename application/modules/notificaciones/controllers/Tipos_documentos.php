<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_documentos extends MY_Controller
{

	/**
	 * Controlador de Tipos de Documento
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('notificaciones/Tipos_documentos_model');
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
				array('label' => 'Descripcion', 'data' => 'descripcion', 'width' => 10),
				array('label' => 'Nombre de Modelo', 'data' => 'nombre_modelo', 'width' => 10),
//				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
//				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'tipos_documentos_table',
			'source_url' => 'notificaciones/tipos_documentos/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_tipos_documentos_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Tipos de Documento';
		$data['title'] = TITLE . ' - Tipos de Documento';
		$this->load_template('notificaciones/tipos_documentos/tipos_documentos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
			->select('id, descripcion, nombre_modelo')//, audi_usuario, audi_fecha, audi_accion')
			->from('nv_tipos_documentos')
			->add_column('ver', '<a href="notificaciones/tipos_documentos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="notificaciones/tipos_documentos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="notificaciones/tipos_documentos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('notificaciones/tipos_documentos/listar', 'refresh');
		}
		
		$this->set_model_validation_rules($this->Tipos_documentos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Tipos_documentos_model->create(array(
				'descripcion' => $this->input->post('descripcion'),
				'nombre_modelo' => $this->input->post('nombre_modelo'),
//				'audi_usuario' => $this->input->post('audi_usuario'),
//				'audi_fecha' => $this->input->post('audi_fecha'),
//				'audi_accion' => $this->input->post('audi_accion')
            ), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Tipos_documentos_model->get_msg());
				redirect('notificaciones/tipos_documentos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Tipos_documentos_model->get_error())
				{
					$error_msg .= $this->Tipos_documentos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Tipos_documentos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Tipo de Documento';
		$data['title'] = TITLE . ' - Agregar Tipo de Documento';
		$this->load_template('notificaciones/tipos_documentos/tipos_documentos_abm', $data);
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
			redirect("notificaciones/tipos_documentos/ver/$id", 'refresh');
		}
		
		$tipos_documento = $this->Tipos_documentos_model->get(array('id' => $id));
		if (empty($tipos_documento))
		{
			show_error('No se encontró el Tipo de Documento', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Tipos_documentos_model);
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
				$trans_ok &= $this->Tipos_documentos_model->update(array(
					'id' => $this->input->post('id'),
					'descripcion' => $this->input->post('descripcion'),
					'nombre_modelo' => $this->input->post('nombre_modelo'),
//					'audi_usuario' => $this->input->post('audi_usuario'),
//					'audi_fecha' => $this->input->post('audi_fecha'),
//					'audi_accion' => $this->input->post('audi_accion')
                ), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Tipos_documentos_model->get_msg());
					redirect('notificaciones/tipos_documentos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Tipos_documentos_model->get_error())
					{
						$error_msg .= $this->Tipos_documentos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Tipos_documentos_model->fields, $tipos_documento);
		$data['tipos_documento'] = $tipos_documento;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Tipo de Documento';
		$data['title'] = TITLE . ' - Editar Tipo de Documento';
		$this->load_template('notificaciones/tipos_documentos/tipos_documentos_abm', $data);
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
			redirect("notificaciones/tipos_documentos/ver/$id", 'refresh');
		}

		$tipos_documento = $this->Tipos_documentos_model->get_one($id);
		if (empty($tipos_documento))
		{
			show_error('No se encontró el Tipo de Documento', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Tipos_documentos_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Tipos_documentos_model->get_msg());
				redirect('notificaciones/tipos_documentos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Tipos_documentos_model->get_error())
				{
					$error_msg .= $this->Tipos_documentos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Tipos_documentos_model->fields, $tipos_documento, TRUE);
		$data['tipos_documento'] = $tipos_documento;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Tipo de Documento';
		$data['title'] = TITLE . ' - Eliminar Tipo de Documento';
		$this->load_template('notificaciones/tipos_documentos/tipos_documentos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$tipos_documento = $this->Tipos_documentos_model->get_one($id);
		if (empty($tipos_documento))
		{
			show_error('No se encontró el Tipo de Documento', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Tipos_documentos_model->fields, $tipos_documento, TRUE);
		$data['tipos_documento'] = $tipos_documento;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Tipo de Documento';
		$data['title'] = TITLE . ' - Ver Tipo de Documento';
		$this->load_template('notificaciones/tipos_documentos/tipos_documentos_abm', $data);
	}
}