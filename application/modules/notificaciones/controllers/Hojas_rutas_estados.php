<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hojas_rutas_estados extends MY_Controller
{

	/**
	 * Controlador de Estados Hoja de Ruta
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('notificaciones/Hojas_rutas_estados_model');
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
				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'hojas_rutas_estados_table',
			'source_url' => 'notificaciones/hojas_rutas_estados/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_hojas_rutas_estados_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Estados Hoja de Ruta';
		$data['title'] = TITLE . ' - Estados Hoja de Ruta';
		$this->load_template('notificaciones/hojas_rutas_estados/hojas_rutas_estados_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
			->select('id, descripcion, audi_usuario, audi_fecha, audi_accion')
			->from('nv_hojas_rutas_estados')
			->add_column('ver', '<a href="notificaciones/hojas_rutas_estados/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="notificaciones/hojas_rutas_estados/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="notificaciones/hojas_rutas_estados/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('notificaciones/hojas_rutas_estados/listar', 'refresh');
		}
		
		$this->set_model_validation_rules($this->Hojas_rutas_estados_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Hojas_rutas_estados_model->create(array(
				'descripcion' => $this->input->post('descripcion'),
				'audi_usuario' => $this->input->post('audi_usuario'),
				'audi_fecha' => $this->input->post('audi_fecha'),
				'audi_accion' => $this->input->post('audi_accion')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Hojas_rutas_estados_model->get_msg());
				redirect('notificaciones/hojas_rutas_estados/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Hojas_rutas_estados_model->get_error())
				{
					$error_msg .= $this->Hojas_rutas_estados_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Hojas_rutas_estados_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Estado Hoja de Ruta';
		$data['title'] = TITLE . ' - Agregar Estado Hoja de Ruta';
		$this->load_template('notificaciones/hojas_rutas_estados/hojas_rutas_estados_abm', $data);
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
			redirect("notificaciones/hojas_rutas_estados/ver/$id", 'refresh');
		}
		
		$hojas_rutas_estado = $this->Hojas_rutas_estados_model->get(array('id' => $id));
		if (empty($hojas_rutas_estado))
		{
			show_error('No se encontró el Estado Hoja de Ruta', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Hojas_rutas_estados_model);
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
				$trans_ok &= $this->Hojas_rutas_estados_model->update(array(
					'id' => $this->input->post('id'),
					'descripcion' => $this->input->post('descripcion'),
					'audi_usuario' => $this->input->post('audi_usuario'),
					'audi_fecha' => $this->input->post('audi_fecha'),
					'audi_accion' => $this->input->post('audi_accion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Hojas_rutas_estados_model->get_msg());
					redirect('notificaciones/hojas_rutas_estados/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Hojas_rutas_estados_model->get_error())
					{
						$error_msg .= $this->Hojas_rutas_estados_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Hojas_rutas_estados_model->fields, $hojas_rutas_estado);
		$data['hojas_rutas_estado'] = $hojas_rutas_estado;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Estado Hoja de Ruta';
		$data['title'] = TITLE . ' - Editar Estado Hoja de Ruta';
		$this->load_template('notificaciones/hojas_rutas_estados/hojas_rutas_estados_abm', $data);
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
			redirect("notificaciones/hojas_rutas_estados/ver/$id", 'refresh');
		}

		$hojas_rutas_estado = $this->Hojas_rutas_estados_model->get_one($id);
		if (empty($hojas_rutas_estado))
		{
			show_error('No se encontró el Estado Hoja de Ruta', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Hojas_rutas_estados_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Hojas_rutas_estados_model->get_msg());
				redirect('notificaciones/hojas_rutas_estados/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Hojas_rutas_estados_model->get_error())
				{
					$error_msg .= $this->Hojas_rutas_estados_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Hojas_rutas_estados_model->fields, $hojas_rutas_estado, TRUE);
		$data['hojas_rutas_estado'] = $hojas_rutas_estado;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Estado Hoja de Ruta';
		$data['title'] = TITLE . ' - Eliminar Estado Hoja de Ruta';
		$this->load_template('notificaciones/hojas_rutas_estados/hojas_rutas_estados_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$hojas_rutas_estado = $this->Hojas_rutas_estados_model->get_one($id);
		if (empty($hojas_rutas_estado))
		{
			show_error('No se encontró el Estado Hoja de Ruta', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Hojas_rutas_estados_model->fields, $hojas_rutas_estado, TRUE);
		$data['hojas_rutas_estado'] = $hojas_rutas_estado;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Estado Hoja de Ruta';
		$data['title'] = TITLE . ' - Ver Estado Hoja de Ruta';
		$this->load_template('notificaciones/hojas_rutas_estados/hojas_rutas_estados_abm', $data);
	}
}