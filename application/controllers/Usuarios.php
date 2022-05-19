<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends MY_Controller
{

    /**
     * Controlador de Usuarios
     * Autor: Leandro
     * Creado: 26/01/2017
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Usuarios_model');
        $this->load->model('Grupos_model');
        $this->load->model('Personas_model');
        $this->load->model('Nacionalidades_model');
        $this->load->model('Usuarios_grupos_model');
        $this->load->model('Auth0_model');
        $this->load->model('Oro_model');
        $this->grupos_permitidos = array('admin');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'grupo' => array('label' => 'Grupo', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'id_name' => 'grupo', 'array' => $this->get_groups_array(FALSE))
        );
        $tableData = array(
            'columns' => array(
                array('label' => 'Legajo', 'data' => 'username', 'width' => 14, 'responsive_class' => 'all'),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 18),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 18),
                array('label' => 'Email', 'data' => 'email', 'width' => 22),
                array('label' => 'Grupos', 'data' => 'grupos', 'width' => 0, 'responsive_class' => 'none'),
                array('label' => 'Estado', 'data' => 'active', 'width' => 10),
                array('label' => 'Último login', 'data' => 'last_login', 'width' => 12, 'render' => 'datetime'),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'usuarios_table',
            'source_url' => 'usuarios/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_usuarios_table",
            'footer' => TRUE,
            'dom' => 't<"row"<"col-sm-6"i><"col-sm-6"p>>',
            'extraData' => 'd.grupos = $("#grupo").val(); '
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_estados'] = array('' => 'Todos', '1' => 'Activo', '0' => 'Inactivo');
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['title_view'] = 'Listado de usuarios';
        $data['title'] = TITLE . ' - Usuarios';
        $this->load_template('usuarios/usuarios_listar', $data);
    }

    public function listar_data($grupo_id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || ($grupo_id !== NULL && !ctype_digit($grupo_id)))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('datatables_functions_helper');
        $grupos = $this->input->post('grupos');
        if (empty($grupos))
        {
            $this->datatables
                    ->select("users.id, users.username, personas.nombre, personas.apellido, personas.email, (SELECT GROUP_CONCAT(`groups`.name) FROM users_groups JOIN `groups` ON `groups`.id = users_groups.group_id WHERE users_groups.user_id = users.id ORDER BY `groups`.name) AS grupos, active, FROM_UNIXTIME(last_login, '%Y-%m-%d %H:%i:%s') as last_login", FALSE)
                    ->unset_column('id')
                    ->from('users')
                    ->join('personas', 'personas.id = users.persona_id', 'left')
                    ->add_column('active', '$1', 'dt_column_usuarios_estado(active, id)', TRUE)
                    ->add_column('ver', '<a href="usuarios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                    ->add_column('editar', '<a href="usuarios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id');
        }
        else
        {
            $grupos_str = implode(',', $grupos);
            $this->datatables
                    ->select("users.id, users.username, personas.nombre, personas.apellido, personas.email, (SELECT GROUP_CONCAT(`groups`.name) FROM users_groups JOIN `groups` ON `groups`.id = users_groups.group_id WHERE users_groups.user_id = users.id ORDER BY `groups`.name) AS grupos, active, FROM_UNIXTIME(last_login, '%Y-%m-%d %H:%i:%s') as last_login", FALSE)
                    ->unset_column('id')
                    ->from('users')
                    ->join('personas', 'personas.id = users.persona_id', 'left')
                    ->where("users.id IN (SELECT users_groups.user_id FROM users_groups WHERE users_groups.group_id IN ($grupos_str))")
                    ->add_column('active', '$1', 'dt_column_usuarios_estado(active, id)', TRUE)
                    ->add_column('ver', '<a href="usuarios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                    ->add_column('editar', '<a href="usuarios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id');
        }
        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array('select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona", 'where' => array('id NOT IN (SELECT persona_id FROM users WHERE persona_id IS NOT NULL)'), 'sort_by' => 'personas.apellido, personas.nombre'), array('agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_groups_control = $this->get_array('Grupos', 'name');
        $array_groups = $this->get_groups_array();
        unset($this->array_groups_control[0]);
        $this->array_password_change_control = $array_password_change = array('S' => 'Si', 'N' => 'No');
        unset($this->Usuarios_model->fields['active']);
        unset($this->Usuarios_model->fields['last_login']);

        if (!empty($_POST) && $_POST['persona'] === 'agregar')
        {
            $this->set_model_validation_rules($this->Personas_model);
        }

        $this->set_model_validation_rules($this->Usuarios_model);
        $this->form_validation->set_rules('password', 'Contraseña', 'required|matches[password_confirm]|callback_control_password');
        $this->form_validation->set_rules('password_confirm', 'Confirmar contraseña', 'required');
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            if (!empty($_POST) && $_POST['persona'] === 'agregar')
            {
                $trans_ok &= $this->Personas_model->create(array(
                    'dni' => $this->input->post('dni'),
                    'sexo' => $this->input->post('sexo'),
                    'cuil' => $this->input->post('cuil'),
                    'nombre' => $this->input->post('nombre'),
                    'apellido' => $this->input->post('apellido'),
                    'telefono' => $this->input->post('telefono'),
                    'celular' => $this->input->post('celular'),
                    'email' => strtolower($this->input->post('email')),
                    'fecha_nacimiento' => $this->get_date_sql('fecha_nacimiento'),
                    'nacionalidad_id' => $this->input->post('nacionalidad')), FALSE);

                $persona_id = $this->Personas_model->get_row_id();
                $dni = $this->input->post('dni');
            }
            else
            {
                $persona_id = $this->input->post('persona');
                $persona = $this->Personas_model->get_one($persona_id);
                if (empty($persona))
                {
                    show_error('No se encontró la Persona', 500, 'Registro no encontrado');
                }
                if (empty($persona->email))
                {
                    $trans_ok = FALSE;
                    $error_msg .= '<br />La persona debe tener un email cargado.';
                }
                $dni = $persona->dni;
            }

            $group_data = array();
            $group_data[] = $this->input->post('groups');
            $additional_data = array(
                'password_change' => $this->input->post('password_change') === 'S' ? 0 : 1,
                'persona_id' => $persona_id
            );

            if ($trans_ok)
            {
                $user_id = $this->ion_auth->register($dni, $this->input->post('password'), NULL, $additional_data, $group_data);
                if (!$user_id)
                {
                    $trans_ok = FALSE;
                }
            }

            if (SIS_AUTH_MODE === 'auth0')
            {
                // AUTH0
                if ($this->db->trans_status() && $trans_ok)
                {
                    $additional_data['nombre'] = $this->input->post('nombre');
                    $additional_data['apellido'] = $this->input->post('apellido');
                    $additional_data['email'] = strtolower($this->input->post('email'));
                    $additional_data['username'] = $dni;
                    $additional_data['password'] = $this->input->post('password');
                    $trans_ok = $this->Auth0_model->create_user($user_id, $additional_data);
                }
            }

            if (SIS_ORO_ACTIVE)
            {
                // ORO CRM
                if (!empty($_POST) && $_POST['persona'] === 'agregar')
                {
                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $datos['id'] = $persona_id;
                        $datos['dni'] = $this->input->post('dni');
                        $datos['sexo'] = $this->input->post('sexo');
                        $datos['cuil'] = $this->input->post('cuil');
                        $datos['nombre'] = $this->input->post('nombre');
                        $datos['apellido'] = $this->input->post('apellido');
                        $datos['telefono'] = $this->input->post('telefono');
                        $datos['celular'] = $this->input->post('celular');
                        $datos['email'] = $this->input->post('email');
                        $datos['fecha_nacimiento'] = $this->get_date_sql('fecha_nacimiento');
                        $datos['nacionalidad_id'] = $this->input->post('nacionalidad');
                        $datos['tags'] = 'Sistema MLC';
                        $this->Oro_model->send_data($datos);
                    }
                }
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->ion_auth->messages());
                redirect('usuarios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg .= '<br />Se ha producido un error con la base de datos.';
                if ($this->Personas_model->get_error())
                {
                    $error_msg .= $this->Personas_model->get_error();
                }
                if ($this->ion_auth->errors())
                {
                    $error_msg .= $this->ion_auth->errors();
                }
                if ($this->Auth0_model->errors())
                {
                    $error_msg .= $this->Auth0_model->errors();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields);

        $this->Usuarios_model->fields['password']['label'] .= ' *';
        $this->Usuarios_model->fields['password_confirm']['label'] .= ' *';
        $this->Usuarios_model->fields['persona']['array'] = $array_persona;
        $this->Usuarios_model->fields['groups']['array'] = $array_groups;
        $this->Usuarios_model->fields['password_change']['array'] = $array_password_change;
        $data['fields'] = $this->build_fields($this->Usuarios_model->fields);

        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar usuario';
        $data['title'] = TITLE . ' - Agregar usuario';
        $data['js'] = 'js/usuarios.js';
        $this->load_template('usuarios/usuarios_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuario = $this->Usuarios_model->get_one($id);
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }
        else
        {
            $usuario->password_change = $usuario->password_change == 0 ? 'S' : 'N'; //Dejar ==
            $usuario->active = $usuario->active == 0 ? 'I' : 'A'; //Dejar ==
        }

        $this->array_groups_control = $this->get_array('Grupos', 'name');
        $array_groups = $this->get_groups_array();
        $users_groups = $this->ion_auth->get_users_groups($id)->result();
        $usuario->groups = array();
        if (!empty($users_groups))
        {
            foreach ($users_groups as $Grupo)
            {
                $usuario->groups[] = $Grupo->id;
            }
        }
        $this->array_password_change_control = $array_password_change = array('S' => 'Si', 'N' => 'No');
        $this->array_active_control = $array_active = array('A' => 'Activo', 'I' => 'Inactivo');

        $usuarios_model = $this->Usuarios_model;
        unset($usuarios_model->fields['persona']);

        $this->set_model_validation_rules($this->Usuarios_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id !== $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            if ($this->input->post('password'))
            {
                $this->form_validation->set_rules('password', 'Contraseña', 'required|matches[password_confirm]|callback_control_password');
                $this->form_validation->set_rules('password_confirm', 'Confirmar contraseña', 'required');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $group_data = $this->input->post('groups');
                $password_change = $this->input->post('password_change');
                $active = $this->input->post('active');
                $data = array(
                    'password_change' => $password_change === 'S' ? 0 : 1,
                    'active' => $active === 'I' ? 0 : 1
                );

                if ($this->input->post('password'))
                {
                    $data['password'] = $this->input->post('password');
                }

                if (empty($group_data))
                {
                    $group_data = array();
                }
                $trans_ok &= $this->Usuarios_grupos_model->intersect_asignaciones($id, $group_data, FALSE);

                $trans_ok &= $this->ion_auth->update($usuario->id, $data);

                if (SIS_AUTH_MODE === 'auth0')
                {
                    // AUTH0
                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $trans_ok = $this->Auth0_model->update_user($usuario->id, $data);
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->ion_auth->messages());
                    redirect('usuarios/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg .= '<br />Se ha producido un error con la base de datos.';
                    if ($this->ion_auth->errors())
                    {
                        $error_msg .= $this->ion_auth->errors();
                    }
                    if ($this->Auth0_model->errors())
                    {
                        $error_msg .= $this->Auth0_model->errors();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $usuario, TRUE);

        unset($this->Usuarios_model->fields['persona']);
        $this->Usuarios_model->fields['last_login']['readonly'] = TRUE;
        $this->Usuarios_model->fields['password']['label'] .= ' (si desea cambiar)';
        $this->Usuarios_model->fields['password_confirm']['label'] .= ' (si desea cambiar)';
        $this->Usuarios_model->fields['groups']['array'] = $array_groups;
        $this->Usuarios_model->fields['password_change']['array'] = $array_password_change;
        $this->Usuarios_model->fields['active']['array'] = $array_active;
        $usuario->last_login = empty($usuario->last_login) ? '' : date('d/m/Y H:i', $usuario->last_login);
        $usuario->password = '';
        $usuario->password_confirm = '';
        $data['fields'] = $this->build_fields($this->Usuarios_model->fields, $usuario);

        $data['usuario'] = $usuario;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar usuario';
        $data['title'] = TITLE . ' - Editar usuario';
        $data['js'] = 'js/usuarios.js';
        $this->load_template('usuarios/usuarios_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuario = $this->Usuarios_model->get_one($id);
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }
        else
        {
            $usuario->password_change = $usuario->password_change == 0 ? 'Si' : 'No'; //Dejar ==
            $usuario->active = $usuario->active == 0 ? 'Inactivo' : 'Activo'; //Dejar ==
        }

        $users_groups = $this->ion_auth->get_users_groups($id)->result();
        if (!empty($users_groups))
        {
            foreach ($users_groups as $Grupo)
            {
                $groups[] = $Grupo->name;
            }
        }
        $usuario->groups = implode(',', $groups);

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $usuario, TRUE);

        unset($this->Usuarios_model->fields['persona']);
        unset($this->Usuarios_model->fields['password']);
        unset($this->Usuarios_model->fields['password_confirm']);
        $usuario->last_login = empty($usuario->last_login) ? '' : date('d/m/Y H:i', $usuario->last_login);
        $data['fields'] = $this->build_fields($this->Usuarios_model->fields, $usuario, TRUE);

        $data['usuario'] = $usuario;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver usuario';
        $data['title'] = TITLE . ' - Ver usuario';
        $this->load_template('usuarios/usuarios_abm', $data);
    }

    public function activar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuario = $this->Usuarios_model->get(array('id' => $id));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }

        $this->db->trans_begin();
        $trans_ok = TRUE;
        $trans_ok &= $this->ion_auth->activate($id);

        if (SIS_AUTH_MODE === 'auth0')
        {
            // AUTH0
            if ($this->db->trans_status() && $trans_ok)
            {
                $data['active'] = 1;
                $trans_ok = $this->Auth0_model->update_user($usuario->id, $data);
            }
        }

        if ($this->db->trans_status() && $trans_ok)
        {
            $this->db->trans_commit();
            $this->session->set_flashdata('message', "<br />Usuario $usuario->username activado correctamente");
        }
        else
        {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', "<br />No se ha podido activar el usuario $usuario->username");
        }

        redirect('usuarios/listar', 'refresh');
    }

    public function desactivar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuario = $this->Usuarios_model->get(array('id' => $id));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }

        $this->db->trans_begin();
        $trans_ok = TRUE;
        $trans_ok &= $this->ion_auth->deactivate($id);
        if (SIS_AUTH_MODE === 'auth0')
        {
            // AUTH0
            if ($this->db->trans_status() && $trans_ok)
            {
                $data['active'] = 0;
                $trans_ok = $this->Auth0_model->update_user($usuario->id, $data);
            }
        }

        if ($this->db->trans_status() && $trans_ok)
        {
            $this->db->trans_commit();
            $this->session->set_flashdata('message', "<br />Usuario $usuario->username desactivado correctamente");
        }
        else
        {
            $this->db->trans_rollback();
            $this->session->set_flashdata('error', "<br />No se ha podido desactivar el usuario $usuario->username");
        }

        redirect('usuarios/listar', 'refresh');
    }

    private function get_groups_array($con_limite = TRUE)
    {
        $array_registros = array();
        $options = array(
            'join' => array(
                array('type' => 'LEFT',
                    'table' => 'modulos',
                    'where' => 'groups.modulo_id = modulos.id',
                    'columnas' => array('modulos.nombre as nombre_modulo', 'modulos.icono as icono_modulo', 'modulos.limite_seleccion as limite_modulo')
                )
            ),
            'sort_by' => 'nombre_modulo, name');

        $registros = $this->Grupos_model->get($options);
        if (!empty($registros))
        {
            foreach ($registros as $Registro)
            {
                $array_registros[$Registro->nombre_modulo]['icono'] = $Registro->icono_modulo;
                if ($con_limite)
                {
                    $array_registros[$Registro->nombre_modulo]['limite'] = $Registro->limite_modulo;
                }
                $array_registros[$Registro->nombre_modulo]['opciones'][$Registro->id] = array('name' => $Registro->name, 'desc' => $Registro->description);
            }
        }
        return $array_registros;
    }

    public function buscar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('legajo', 'Legajo', 'required|integer|max_length[8]');
        if ($this->form_validation->run() === TRUE)
        {
            $labo_Codigo = $this->input->post('legajo');

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            try
            {
                $http_response_empleado = $guzzleHttp->request('GET', "personas/datos", ['query' => ['labo_Codigo' => $labo_Codigo, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
                $empleado = json_decode($http_response_empleado->getBody()->getContents());
            } catch (Exception $e)
            {
                $empleado = NULL;
            }

            if (!empty($empleado))
            {
                $data['empleado'] = $empleado;
            }
            else
            {
                $data['error'] = 'Legajo no encontrado';
            }
        }
        else
        {
            $data['error'] = 'Debe ingresar un legajo válido';
        }
        echo json_encode($data);
    }

    public function desbloquear()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if ($this->ion_auth->desbloquear())
        {
            $this->session->set_flashdata('message', '<br />Usuarios desbloqueados');
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Ocurrió un error al desbloquear usuarios');
        }
        redirect('usuarios/listar', 'refresh');
    }
}
