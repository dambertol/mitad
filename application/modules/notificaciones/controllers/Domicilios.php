<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Domicilios extends MY_Controller
{

	/**
	 * Controlador de Domicilios
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('notificaciones/Domicilios_model');
		$this->load->model('notificaciones/Departamentos_model');
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
				array('label' => 'Direccion', 'data' => 'direccion', 'width' => 10),
				array('label' => 'Num', 'data' => 'num', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Localidad', 'data' => 'localidad', 'width' => 10),
				array('label' => 'Coordenadas', 'data' => 'coordenadas', 'width' => 10),
				array('label' => 'Departamento', 'data' => 'departamento_id', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Fecha de Creacion', 'data' => 'fecha_creacion', 'width' => 10, 'render' => 'datetime'),
				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'domicilios_table',
			'source_url' => 'notificaciones/domicilios/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_domicilios_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Domicilios';
		$data['title'] = TITLE . ' - Domicilios';
		$this->load_template('notificaciones/domicilios/domicilios_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
			->select('id, direccion, num, localidad, coordenadas, departamento, fecha_creacion, audi_usuario, audi_fecha, audi_accion')
			->from('nv_domicilios')
			->add_column('ver', '<a href="notificaciones/domicilios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="notificaciones/domicilios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="notificaciones/domicilios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('notificaciones/domicilios/listar', 'refresh');
		}
		
		$this->array_departamento_control = $array_departamento = $this->get_array('Departamentos');
		$this->set_model_validation_rules($this->Domicilios_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Domicilios_model->create(array(
				'direccion' => $this->input->post('direccion'),
				'num' => $this->input->post('num'),
				'localidad' => $this->input->post('localidad'),
				'coordenadas' => $this->input->post('coordenadas'),
				'departamento_id' => $this->input->post('departamento'),
				'fecha_creacion' => $this->input->post('fecha_creacion'),
				'audi_usuario' => $this->input->post('audi_usuario'),
				'audi_fecha' => $this->input->post('audi_fecha'),
				'audi_accion' => $this->input->post('audi_accion')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Domicilios_model->get_msg());
				redirect('notificaciones/domicilios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Domicilios_model->get_error())
				{
					$error_msg .= $this->Domicilios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Domicilios_model->fields['departamento']['array'] = $array_departamento;
		$data['fields'] = $this->build_fields($this->Domicilios_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Domicilio';
		$data['title'] = TITLE . ' - Agregar Domicilio';
		$this->load_template('notificaciones/domicilios/domicilios_abm', $data);
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
			redirect("notificaciones/domicilios/ver/$id", 'refresh');
		}
		
		$this->array_departamento_control = $array_departamento = $this->get_array('Departamentos');
		$domicilio = $this->Domicilios_model->get(array('id' => $id));
		if (empty($domicilio))
		{
			show_error('No se encontró el Domicilio', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Domicilios_model);
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
				$trans_ok &= $this->Domicilios_model->update(array(
					'id' => $this->input->post('id'),
					'direccion' => $this->input->post('direccion'),
					'num' => $this->input->post('num'),
					'localidad' => $this->input->post('localidad'),
					'coordenadas' => $this->input->post('coordenadas'),
					'departamento_id' => $this->input->post('departamento'),
					'fecha_creacion' => $this->input->post('fecha_creacion'),
					'audi_usuario' => $this->input->post('audi_usuario'),
					'audi_fecha' => $this->input->post('audi_fecha'),
					'audi_accion' => $this->input->post('audi_accion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Domicilios_model->get_msg());
					redirect('notificaciones/domicilios/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Domicilios_model->get_error())
					{
						$error_msg .= $this->Domicilios_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Domicilios_model->fields['departamento']['array'] = $array_departamento;
		$data['fields'] = $this->build_fields($this->Domicilios_model->fields, $domicilio);
		$data['domicilio'] = $domicilio;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Domicilio';
		$data['title'] = TITLE . ' - Editar Domicilio';
		$this->load_template('notificaciones/domicilios/domicilios_abm', $data);
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
			redirect("notificaciones/domicilios/ver/$id", 'refresh');
		}

		$domicilio = $this->Domicilios_model->get_one($id);
		if (empty($domicilio))
		{
			show_error('No se encontró el Domicilio', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Domicilios_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Domicilios_model->get_msg());
				redirect('notificaciones/domicilios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Domicilios_model->get_error())
				{
					$error_msg .= $this->Domicilios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Domicilios_model->fields, $domicilio, TRUE);
		$data['domicilio'] = $domicilio;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Domicilio';
		$data['title'] = TITLE . ' - Eliminar Domicilio';
		$this->load_template('notificaciones/domicilios/domicilios_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$domicilio = $this->Domicilios_model->get_one($id);
		if (empty($domicilio))
		{
			show_error('No se encontró el Domicilio', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Domicilios_model->fields, $domicilio, TRUE);
		$data['domicilio'] = $domicilio;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Domicilio';
		$data['title'] = TITLE . ' - Ver Domicilio';
		$this->load_template('notificaciones/domicilios/domicilios_abm', $data);
	}
}