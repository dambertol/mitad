<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas_movimientos extends MY_Controller
{

	/**
	 * Controlador de Movimientos de Cédula
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('notificaciones/Cedulas_movimientos_model');
		$this->load->model('notificaciones/Tipo_movimientos_model');
		$this->load->model('notificaciones/Cedulas_model');
		$this->load->model('notificaciones/Usuarios_model');
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
				array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 10),
				array('label' => 'Fecha', 'data' => 'fecha', 'width' => 10, 'render' => 'datetime'),
				array('label' => 'Tipo de Movimiento', 'data' => 'tipo_movimiento_id', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Cedula', 'data' => 'cedula_id', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Usuario', 'data' => 'usuario_id', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'cedulas_movimientos_table',
			'source_url' => 'notificaciones/cedulas_movimientos/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_cedulas_movimientos_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Movimientos de Cédula';
		$data['title'] = TITLE . ' - Movimientos de Cédula';
		$this->load_template('notificaciones/cedulas_movimientos/cedulas_movimientos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
			->select('id, observaciones, fecha, tipo_movimiento, cedula, usuario, audi_usuario, audi_fecha, audi_accion')
			->from('nv_cedulas_movimientos')
			->add_column('ver', '<a href="notificaciones/cedulas_movimientos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="notificaciones/cedulas_movimientos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="notificaciones/cedulas_movimientos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('notificaciones/cedulas_movimientos/listar', 'refresh');
		}
		
		$this->array_tipo_movimiento_control = $array_tipo_movimiento = $this->get_array('Tipo_movimientos');
		$this->array_cedula_control = $array_cedula = $this->get_array('Cedulas');
		$this->array_usuario_control = $array_usuario = $this->get_array('Usuarios');
		$this->set_model_validation_rules($this->Cedulas_movimientos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Cedulas_movimientos_model->create(array(
				'observaciones' => $this->input->post('observaciones'),
				'fecha' => $this->input->post('fecha'),
				'tipo_movimiento_id' => $this->input->post('tipo_movimiento'),
				'cedula_id' => $this->input->post('cedula'),
				'usuario_id' => $this->input->post('usuario'),
				'audi_usuario' => $this->input->post('audi_usuario'),
				'audi_fecha' => $this->input->post('audi_fecha'),
				'audi_accion' => $this->input->post('audi_accion')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Cedulas_movimientos_model->get_msg());
				redirect('notificaciones/cedulas_movimientos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Cedulas_movimientos_model->get_error())
				{
					$error_msg .= $this->Cedulas_movimientos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Cedulas_movimientos_model->fields['tipo_movimiento']['array'] = $array_tipo_movimiento;$this->Cedulas_movimientos_model->fields['cedula']['array'] = $array_cedula;$this->Cedulas_movimientos_model->fields['usuario']['array'] = $array_usuario;
		$data['fields'] = $this->build_fields($this->Cedulas_movimientos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Movimiento de Cédula';
		$data['title'] = TITLE . ' - Agregar Movimiento de Cédula';
		$this->load_template('notificaciones/cedulas_movimientos/cedulas_movimientos_abm', $data);
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
			redirect("notificaciones/cedulas_movimientos/ver/$id", 'refresh');
		}
		
		$this->array_tipo_movimiento_control = $array_tipo_movimiento = $this->get_array('Tipo_movimientos');
		$this->array_cedula_control = $array_cedula = $this->get_array('Cedulas');
		$this->array_usuario_control = $array_usuario = $this->get_array('Usuarios');
		$cedulas_movimiento = $this->Cedulas_movimientos_model->get(array('id' => $id));
		if (empty($cedulas_movimiento))
		{
			show_error('No se encontró el Movimiento de Cédula', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Cedulas_movimientos_model);
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
				$trans_ok &= $this->Cedulas_movimientos_model->update(array(
					'id' => $this->input->post('id'),
					'observaciones' => $this->input->post('observaciones'),
					'fecha' => $this->input->post('fecha'),
					'tipo_movimiento_id' => $this->input->post('tipo_movimiento'),
					'cedula_id' => $this->input->post('cedula'),
					'usuario_id' => $this->input->post('usuario'),
					'audi_usuario' => $this->input->post('audi_usuario'),
					'audi_fecha' => $this->input->post('audi_fecha'),
					'audi_accion' => $this->input->post('audi_accion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Cedulas_movimientos_model->get_msg());
					redirect('notificaciones/cedulas_movimientos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Cedulas_movimientos_model->get_error())
					{
						$error_msg .= $this->Cedulas_movimientos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Cedulas_movimientos_model->fields['tipo_movimiento']['array'] = $array_tipo_movimiento;$this->Cedulas_movimientos_model->fields['cedula']['array'] = $array_cedula;$this->Cedulas_movimientos_model->fields['usuario']['array'] = $array_usuario;
		$data['fields'] = $this->build_fields($this->Cedulas_movimientos_model->fields, $cedulas_movimiento);
		$data['cedulas_movimiento'] = $cedulas_movimiento;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Movimiento de Cédula';
		$data['title'] = TITLE . ' - Editar Movimiento de Cédula';
		$this->load_template('notificaciones/cedulas_movimientos/cedulas_movimientos_abm', $data);
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
			redirect("notificaciones/cedulas_movimientos/ver/$id", 'refresh');
		}

		$cedulas_movimiento = $this->Cedulas_movimientos_model->get_one($id);
		if (empty($cedulas_movimiento))
		{
			show_error('No se encontró el Movimiento de Cédula', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Cedulas_movimientos_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Cedulas_movimientos_model->get_msg());
				redirect('notificaciones/cedulas_movimientos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Cedulas_movimientos_model->get_error())
				{
					$error_msg .= $this->Cedulas_movimientos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Cedulas_movimientos_model->fields, $cedulas_movimiento, TRUE);
		$data['cedulas_movimiento'] = $cedulas_movimiento;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Movimiento de Cédula';
		$data['title'] = TITLE . ' - Eliminar Movimiento de Cédula';
		$this->load_template('notificaciones/cedulas_movimientos/cedulas_movimientos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$cedulas_movimiento = $this->Cedulas_movimientos_model->get_one($id);
		if (empty($cedulas_movimiento))
		{
			show_error('No se encontró el Movimiento de Cédula', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Cedulas_movimientos_model->fields, $cedulas_movimiento, TRUE);
		$data['cedulas_movimiento'] = $cedulas_movimiento;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Movimiento de Cédula';
		$data['title'] = TITLE . ' - Ver Movimiento de Cédula';
		$this->load_template('notificaciones/cedulas_movimientos/cedulas_movimientos_abm', $data);
	}
}