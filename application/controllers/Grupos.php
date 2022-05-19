<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Grupos extends MY_Controller
{

	/**
	 * Controlador de Grupos
	 * Autor: Leandro
	 * Creado: 26/01/2017
	 * Modificado: 21/03/2017 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Grupos_model');
		$this->load->model('Modulos_model');
		$this->grupos_permitidos = array('admin');
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
				array('label' => 'Nombre', 'data' => 'name', 'width' => 20, 'responsive_class' => 'all'),
				array('label' => 'Descripción', 'data' => 'description', 'width' => 35),
				array('label' => 'Módulo', 'data' => 'modulo', 'width' => 16),
				array('label' => 'Usuarios Activos', 'data' => 'usuarios_activos', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => 'Usuarios Inactivos', 'data' => 'usuarios_inactivos', 'width' => 10, 'class' => 'dt-body-right'),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'grupos_table',
			'source_url' => 'grupos/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => "complete_grupos_table",
			'footer' => TRUE,
			'dom' => 't<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de grupos';
		$data['title'] = TITLE . ' - Grupos';
		$this->load_template('grupos/grupos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('datatables_functions_helper');
		$this->datatables
			->select('groups.id, name, description, modulos.nombre as modulo, (SELECT COUNT(*) FROM users_groups LEFT JOIN users ON users_groups.user_id = users.id WHERE group_id = groups.id AND users.active = 1) as usuarios_activos, (SELECT COUNT(*) FROM users_groups LEFT JOIN users ON users_groups.user_id = users.id WHERE group_id = groups.id AND users.active = 0) as usuarios_inactivos')
			->unset_column('id')
			->custom_sort('modulo', 'modulos.nombre')
			->custom_sort('usuarios_activos', '(SELECT COUNT(*) FROM users_groups LEFT JOIN users ON users_groups.user_id = users.id WHERE group_id = groups.id AND users.active = 1)')
			->custom_sort('usuarios_inactivos', '(SELECT COUNT(*) FROM users_groups LEFT JOIN users ON users_groups.user_id = users.id WHERE group_id = groups.id AND users.active = 0)')
			->from('groups')
			->join('modulos', 'modulos.id = groups.modulo_id', 'left')
			->add_column('usuarios_activos', '$1', 'dt_column_grupos_usuarios(1, usuarios_activos)', TRUE)
			->add_column('usuarios_inactivos', '$1', 'dt_column_grupos_usuarios(0, usuarios_inactivos)', TRUE)
			->add_column('ver', '<a href="grupos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="grupos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="grupos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function agregar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->array_modulo_control = $array_modulo = $this->get_array('Modulos', 'nombre');
		$this->set_model_validation_rules($this->Grupos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$ok = $this->Grupos_model->create(array(
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'modulo_id' => $this->input->post('modulo'))
			);
			if ($ok)
			{
				$this->session->set_flashdata('message', $this->Grupos_model->get_msg());
				redirect('grupos/listar', 'refresh');
			}
			else
			{
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Grupos_model->get_error())
				{
					$error_msg .= $this->Grupos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Grupos_model->fields['modulo']['array'] = $array_modulo;
		$data['fields'] = $this->build_fields($this->Grupos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar grupo';
		$data['title'] = TITLE . ' - Agregar grupo';
		$this->load_template('grupos/grupos_abm', $data);
	}

	public function editar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$grupo = $this->Grupos_model->get(array('id' => $id));
		if (empty($grupo))
		{
			show_error('No se encontró el grupo', 500, 'Registro no encontrado');
		}

		$this->array_modulo_control = $array_modulo = $this->get_array('Modulos', 'nombre');
		$this->set_model_validation_rules($this->Grupos_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id !== $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$ok = $this->Grupos_model->update(array(
					'id' => $this->input->post('id'),
					'name' => $this->input->post('name'),
					'description' => $this->input->post('description'),
					'modulo_id' => $this->input->post('modulo'))
				);
				if ($ok)
				{
					$this->session->set_flashdata('message', $this->Grupos_model->get_msg());
					redirect('grupos/listar', 'refresh');
				}
				else
				{
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Grupos_model->get_error())
					{
						$error_msg .= $this->Grupos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Grupos_model->fields['modulo']['array'] = $array_modulo;
		$data['fields'] = $this->build_fields($this->Grupos_model->fields, $grupo);
		$data['grupo'] = $grupo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar grupo';
		$data['title'] = TITLE . ' - Editar grupo';
		$this->load_template('grupos/grupos_abm', $data);
	}

	public function eliminar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$grupo = $this->Grupos_model->get(array('id' => $id));
		if (empty($grupo))
		{
			show_error('No se encontró el grupo', 500, 'Registro no encontrado');
		}

		$array_modulo = $this->get_array('Modulos', 'nombre');
		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$ok = $this->Grupos_model->delete(array('id' => $this->input->post('id')));
			if ($ok)
			{
				$this->session->set_flashdata('message', $this->Grupos_model->get_msg());
				redirect('grupos/listar', 'refresh');
			}
			else
			{
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Grupos_model->get_error())
				{
					$error_msg .= $this->Grupos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Grupos_model->fields['modulo']['array'] = $array_modulo;
		$data['fields'] = $this->build_fields($this->Grupos_model->fields, $grupo, TRUE);
		$data['grupo'] = $grupo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar grupo';
		$data['title'] = TITLE . ' - Eliminar grupo';
		$this->load_template('grupos/grupos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$grupo = $this->Grupos_model->get(array('id' => $id));
		if (empty($grupo))
		{
			show_error('No se encontró el grupo', 500, 'Registro no encontrado');
		}

		$array_modulo = $this->get_array('Modulos', 'nombre');

		$this->Grupos_model->fields['modulo']['array'] = $array_modulo;
		$data['fields'] = $this->build_fields($this->Grupos_model->fields, $grupo, TRUE);
		$data['grupo'] = $grupo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver grupo';
		$data['title'] = TITLE . ' - Ver grupo';
		$this->load_template('grupos/grupos_abm', $data);
	}
}