<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_legajos extends MY_Controller
{

	/**
	 * Controlador de Usuario Legajos
	 * Autor: Leandro
	 * Creado: 10/12/2019
	 * Modificado: 10/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('recursos_humanos/Usuarios_legajos_model');
		$this->load->model('Usuarios_model');
		$this->load->model('recursos_humanos/Legajos_model');
		$this->grupos_permitidos = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_consulta_general');
		$this->grupos_solo_consulta = array('recursos_humanos_consulta_general');
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
						array('label' => 'Usuario', 'data' => 'username', 'width' => 14, 'class' => 'dt-body-right'),
						array('label' => 'Apellido', 'data' => 'apellido', 'width' => 35),
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 35),
						array('label' => 'Legajos', 'data' => 'legajos', 'width' => 10),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'usuarios_legajos_table',
				'source_url' => 'recursos_humanos/usuarios_legajos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_usuarios_legajos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Asignaciones de Legajos';
		$data['title'] = TITLE . ' - Asignaciones de Legajos';
		$this->load_template('recursos_humanos/usuarios_legajos/usuarios_legajos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('recursos_humanos/datatables_functions_helper');
		$this->datatables
				->select("users.id, users.username, personas.apellido, personas.nombre, (SELECT COUNT(*) FROM rh_usuarios_legajos WHERE user_id = users.id) as legajos")
				->custom_sort('legajos', '(SELECT COUNT(*) FROM rh_usuarios_legajos WHERE user_id = users.id)')
				->from('users')
				->join('users_groups', 'users.id = users_groups.user_id', 'left')
				->join('groups', 'groups.id = users_groups.group_id', 'left')
				->join('personas', 'personas.id = users.persona_id', 'left')
				->where('groups.name', 'recursos_humanos_director', 'left')
				->add_column('legajos', '$1', 'dt_column_usuarios_legajos_legajos(legajos)', TRUE)
				->add_column('ver', '<a href="recursos_humanos/usuarios_legajos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="recursos_humanos/usuarios_legajos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function editar($usuario_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $usuario_id == NULL || !ctype_digit($usuario_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("recursos_humanos/usuarios_legajos/ver/$usuario_id", 'refresh');
		}

		$this->array_user_control = $array_user = $this->get_array('Usuarios', 'usuario', 'id', array(
				'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as usuario",
				'join' => array(
						array('personas', 'personas.id = users.persona_id', 'LEFT'),
						array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
						array('groups', 'users_groups.group_id = groups.id', 'LEFT')
				),
				'where' => array(
						array('column' => 'groups.name IN', 'value' => "('recursos_humanos_director')", 'override' => TRUE),
						array('column' => 'users.active', 'value' => '1'),
						array('column' => 'users.id NOT IN', 'value' => '(SELECT user_id FROM rh_usuarios_legajos)', 'override' => TRUE)
				),
				'sort_by' => 'personas.apellido, personas.nombre, username'
				), array('' => '')
		);
		$this->array_legajos_control = $array_legajos = $this->get_array('Legajos', 'legajo', 'id', array('select' => "id, CONCAT(apellido, ', ', nombre, ' (', legajo, ')') as legajo"));
		$this->array_legajos_control[''] = '';

		$usuario = $this->Usuarios_model->get(array(
				'id' => $usuario_id,
				'select' => array("users.id, users.id as user_id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as user"),
				'join' => array(
						array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
						array('groups', 'groups.id = users_groups.group_id', 'LEFT'),
						array('personas', 'personas.id = users.persona_id', 'LEFT')
				),
				'where' => array("groups.name IN ('recursos_humanos_director')")
		));
		if (empty($usuario))
		{
			show_error('No se encontró el usuario', 500, 'Registro no encontrado');
		}

		$usuarios_legajo = $this->Usuarios_legajos_model->get(array('user_id' => $usuario_id));
		if (!empty($usuarios_legajo))
		{
			foreach ($usuarios_legajo as $Legajo)
			{
				$usuario->legajos[] = $Legajo->legajo_id;
			}
		}
		else
		{
			$usuario->legajos = array();
		}

		$this->set_model_validation_rules($this->Usuarios_legajos_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($usuario_id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$legajo_data = $this->input->post('legajos[]');
				if (empty($legajo_data))
				{
					$legajo_data = array();
				}
				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Usuarios_legajos_model->intersect_asignaciones($usuario_id, $legajo_data, FALSE);

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Usuarios_legajos_model->get_msg());
					redirect('recursos_humanos/usuarios_legajos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Usuarios_legajos_model->get_error())
					{
						$error_msg .= $this->Usuarios_legajos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Usuarios_legajos_model->fields['user']['array'] = $array_user;
		$this->Usuarios_legajos_model->fields['legajos']['array'] = $array_legajos;
		$data['fields'] = $this->build_fields($this->Usuarios_legajos_model->fields, $usuario);
		$data['usuarios_legajo'] = $usuario;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Asignación de Legajos';
		$data['title'] = TITLE . ' - Editar Asignación de Legajos';
		$data['css'] = 'vendor/duallistbox/css/bootstrap-duallistbox.min.css';
		$data['js'] = 'vendor/duallistbox/js/jquery.bootstrap-duallistbox.min.js';
		$this->load_template('recursos_humanos/usuarios_legajos/usuarios_legajos_abm', $data);
	}

	public function ver($usuario_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $usuario_id == NULL || !ctype_digit($usuario_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$usuario = $this->Usuarios_model->get(array(
				'id' => $usuario_id,
				'select' => array("users.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as user"),
				'join' => array(
						array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
						array('groups', 'groups.id = users_groups.group_id', 'LEFT'),
						array('personas', 'personas.id = users.persona_id', 'LEFT')
				),
				'where' => array("groups.name IN ('recursos_humanos_director')")
		));
		if (empty($usuario))
		{
			show_error('No se encontró el Usuario', 500, 'Registro no encontrado');
		}

		$usuarios_legajo = $this->Usuarios_legajos_model->get(array('user_id' => $usuario_id));
		if (!empty($usuarios_legajo))
		{
			foreach ($usuarios_legajo as $Legajo)
			{
				$usuario->legajos[] = $Legajo->legajo_id;
			}
		}
		else
		{
			$usuario->legajos = array();
		}

		$array_legajos = $this->get_array('Legajos', 'legajo', 'id', array('select' => "id, CONCAT(apellido, ', ', nombre, ' (', legajo, ')') as legajo"));
		$this->Usuarios_legajos_model->fields['legajos']['array'] = $array_legajos;
		$data['fields'] = $this->build_fields($this->Usuarios_legajos_model->fields, $usuario, TRUE);
		$data['usuarios_legajo'] = $usuarios_legajo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Asignación de Legajos';
		$data['title'] = TITLE . ' - Ver Asignación de Legajos';
		$data['css'] = 'vendor/duallistbox/css/bootstrap-duallistbox.min.css';
		$data['js'] = 'vendor/duallistbox/js/jquery.bootstrap-duallistbox.min.js';
		$this->load_template('recursos_humanos/usuarios_legajos/usuarios_legajos_abm', $data);
	}
}