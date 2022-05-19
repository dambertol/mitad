<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends MY_Controller
{

    /**
     * Controlador de Usuarios
     * Autor: Leandro
     * Creado: 19/09/2016
     * Modificado: 19/08/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('asistencia/Usuarios_model');
        $this->load->model('Grupos_model');
        $this->load->model('Personas_model');
        $this->load->model('Nacionalidades_model');
        $this->load->model('asistencia/Usuarios_oficinas_model');
        $this->load->model('Auth0_model');
        $this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_consulta_general');
        $this->grupos_admin = array('admin', 'asistencia_consulta_general');
        $this->grupos_solo_consulta = array('asistencia_consulta_general');
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
                array('label' => 'Legajo', 'data' => 'username', 'width' => 15, 'class' => 'dt-body-right', 'responsive_class' => 'all'),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 20),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 20),
                array('label' => 'Grupo', 'data' => 'grupos', 'width' => 11),
                array('label' => 'Estado', 'data' => 'active', 'width' => 11),
                array('label' => 'Último login', 'data' => 'last_login', 'width' => 14, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'view', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'edit', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'asignar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'users_table',
            'source_url' => 'asistencia/usuarios/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_users_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_estados'] = array('' => 'Todos', '1' => 'Activo', '0' => 'Inactivo');
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de usuarios';
        $data['title'] = TITLE . ' - Usuarios';
        $this->load_template('asistencia/usuarios/usuarios_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('asistencia/datatables_functions_helper');
        $this->datatables
                ->select('users.id, username, personas.apellido, personas.nombre, groups.name as grupos, groups.id as grupo_id, active, FROM_UNIXTIME(last_login, "%Y-%m-%d %H:%i:%s") as last_login', FALSE)
                ->custom_sort('grupos', 'groups.name')
                ->from('users')
                ->join('personas', 'personas.id = users.persona_id', 'left')
                ->join('users_groups', 'users_groups.user_id = users.id', 'left')
                ->join('groups', 'groups.id = users_groups.group_id', 'left')
                ->where_in('groups.name', array('asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user'))
                ->add_column('active', '$1', 'dt_column_asistencia_usuarios_estado(active, id, "asistencia/")', TRUE)
                ->add_column('view', '<a href="asistencia/usuarios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('edit', '<a href="asistencia/usuarios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('asignar', '$1', 'dt_column_asistencia_usuarios_asignar(grupos, id)');

        echo $this->datatables->generate();
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
            redirect("asistencia/usuarios/ver/$id", 'refresh');
        }

        $usuario = $this->Usuarios_model->get(array(
            'id' => $id,
            'join' => array(
                array(
                    'type' => 'LEFT',
                    'table' => 'users_groups',
                    'where' => 'users_groups.user_id = users.id',
                ),
                array(
                    'type' => 'LEFT',
                    'table' => 'groups',
                    'where' => 'groups.id = users_groups.group_id',
                    'columnas' => array('groups.id as groups')
                ),
                array('personas', 'personas.id = users.persona_id', 'LEFT',
                    array(
                        'personas.dni',
                        'personas.sexo',
                        'personas.cuil',
                        'personas.nombre',
                        'personas.apellido',
                        'personas.telefono',
                        'personas.celular',
                        'personas.email',
                        'personas.fecha_nacimiento',
                        'personas.nacionalidad_id'
                    )
                ),
                array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad'))
            ),
            'where' => array("groups.name IN ('asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user')")
        ));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }
        else
        {
            $usuario->active = $usuario->active == 0 ? 'I' : 'A'; //Dejar ==
            $users_groups = $this->ion_auth->get_users_groups($id)->result();
            if (sizeof($users_groups) > 1)
            {
                $usuario_otros_modulos = TRUE;
            }
            else
            {
                $usuario_otros_modulos = FALSE;
            }
        }

        $usuarios_model = $this->Usuarios_model;
        unset($usuarios_model->fields['persona']);

        $this->array_groups_control = $array_groups = $this->get_array('Grupos', 'grupo', 'id', array('select' => "id, CONCAT(name, ' (', description, ')') as grupo", 'where' => array("name IN ('asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user')")));
        $this->array_active_control = $array_active = array('A' => 'Activo', 'I' => 'Inactivo');

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
                $data = array();
                $group_data = array();
                $group_data[] = $this->input->post('groups');

                if (!empty($group_data) && ((int) $usuario->groups !== (int) $group_data[0]))
                {
                    $this->ion_auth->remove_from_group($usuario->groups, $id);
                    $this->ion_auth->add_to_group($group_data[0], $id);
                }

                $active = $this->input->post('active');
                $data = array(
                    'active' => $active === 'I' ? 0 : 1
                );

                if ($this->input->post('password'))
                {
                    $data['password'] = $this->input->post('password');
                }

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
                    redirect('asistencia/usuarios/listar', 'refresh');
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

        try
        {
            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            $http_response_empleado = $guzzleHttp->request('GET', "personas/datos", ['query' => ['labo_Codigo' => $usuario->username, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
            $empleado = json_decode($http_response_empleado->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleado = NULL;
        }

        if (!empty($empleado))
        {
            $usuario->oficina = $empleado->ofi_Oficina . " - " . $empleado->ofi_Descripcion;
            $usuario->cod_horario = $empleado->hora_Codigo;
            $usuario->horario = $empleado->hora_Descripcion;
        }
        else
        {
            $usuario->oficina = 'Error al conectarse a Major';
            $usuario->cod_horario = 'Error al conectarse a Major';
            $usuario->horario = 'Error al conectarse a Major';
        }

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $usuario, TRUE);

        $this->Usuarios_model->fields['last_login']['readonly'] = TRUE;
        $this->Usuarios_model->fields['password']['label'] .= ' (si desea cambiarla)';
        $this->Usuarios_model->fields['password_confirm']['label'] .= ' (si desea cambiarla)';
        $this->Usuarios_model->fields['groups']['array'] = $array_groups;
        $this->Usuarios_model->fields['active']['array'] = $array_active;
        $usuario->last_login = empty($usuario->last_login) ? '' : date('d/m/Y H:i', $usuario->last_login);
        $usuario->password = '';
        $usuario->password_confirm = '';
        $data['fields'] = $this->build_fields($this->Usuarios_model->fields, $usuario);

        $data['usuario_otros_modulos'] = $usuario_otros_modulos;
        $data['usuario'] = $usuario;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar usuario';
        $data['title'] = TITLE . ' - Editar usuario';
        $this->load_template('asistencia/usuarios/usuarios_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuario = $this->Usuarios_model->get(array(
            'id' => $id,
            'join' => array(
                array(
                    'type' => 'LEFT',
                    'table' => 'users_groups',
                    'where' => 'users_groups.user_id = users.id',
                ),
                array(
                    'type' => 'LEFT',
                    'table' => 'groups',
                    'where' => 'groups.id = users_groups.group_id',
                    'columnas' => array('groups.name as groups')
                ),
                array('personas', 'personas.id = users.persona_id', 'LEFT',
                    array(
                        'personas.dni',
                        'personas.sexo',
                        'personas.cuil',
                        'personas.nombre',
                        'personas.apellido',
                        'personas.telefono',
                        'personas.celular',
                        'personas.email',
                        'personas.fecha_nacimiento',
                        'personas.nacionalidad_id'
                    )
                ),
                array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad'))
            ),
            'where' => array("groups.name IN ('asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user')")
        ));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }
        else
        {
            $usuario->active = $usuario->active == 0 ? 'Inactivo' : 'Activo'; //Dejar ==
        }

        try
        {
            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            $http_response_empleado = $guzzleHttp->request('GET', "personas/datos", ['query' => ['labo_Codigo' => $usuario->username, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
            $empleado = json_decode($http_response_empleado->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleado = NULL;
        }
        if (!empty($empleado))
        {
            $usuario->oficina = $empleado->ofi_Oficina . " - " . $empleado->ofi_Descripcion;
            $usuario->cod_horario = $empleado->hora_Codigo;
            $usuario->horario = $empleado->hora_Descripcion;
        }
        else
        {
            $usuario->oficina = 'Error al conectarse a Major';
            $usuario->cod_horario = 'Error al conectarse a Major';
            $usuario->horario = 'Error al conectarse a Major';
        }

        if ($usuario->groups === 'asistencia_director' || $usuario->groups === 'asistencia_contralor' || $usuario->groups === 'asistencia_control')
        {
            $usuario->oficinas = array();
            $users_oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $id));
            if (!empty($users_oficinas))
            {
                foreach ($users_oficinas as $Oficina)
                {
                    $usuario->oficinas[] = $Oficina->ofi_Oficina;
                }
            }
            $array_oficinas = array();
            if (!empty($usuario->oficinas))
            {
                try
                {
                    $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                        'form_params' => [
                            'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                            'ofi_Tipo' => 'Interna'
                    ]]);
                    $oficinas = json_decode($http_response_oficinas->getBody()->getContents());
                } catch (Exception $e)
                {
                    $oficinas = NULL;
                }

                if (!empty($oficinas))
                {
                    foreach ($oficinas as $Oficina)
                    {
                        $array_oficinas[$Oficina->ofi_Oficina] = "$Oficina->ofi_Oficina - $Oficina->ofi_Descripcion";
                    }
                }
                else
                {
                    $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                    redirect('asistencia/usuarios/listar', 'refresh');
                }
            }
            $data['oficinas'] = $array_oficinas;

            $users_oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $id));
            $usuario->oficinas = array();
            if (!empty($users_oficinas))
            {
                foreach ($users_oficinas as $Oficina)
                {
                    $usuario->oficinas[] = $Oficina->ofi_Oficina;
                }
            }

            $this->Usuarios_oficinas_model->fields['oficinas']['array'] = $array_oficinas;
            $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $usuario, TRUE);
            $data['fields_oficina'] = $this->build_fields($this->Usuarios_oficinas_model->fields, $usuario, TRUE);
        }
        $data['error'] = $this->session->flashdata('error');

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
        $data['css'] = 'vendor/duallistbox/css/bootstrap-duallistbox.min.css';
        $data['js'] = 'vendor/duallistbox/js/jquery.bootstrap-duallistbox.min.js';
        $this->load_template('asistencia/usuarios/usuarios_abm', $data);
    }

    public function activar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("asistencia/usuarios/listar", 'refresh');
        }

        $usuario = $this->Usuarios_model->get(array(
            'id' => $id,
            'join' => array(
                array(
                    'type' => 'LEFT',
                    'table' => 'users_groups',
                    'where' => 'users_groups.user_id = users.id',
                ),
                array(
                    'type' => 'LEFT',
                    'table' => 'groups',
                    'where' => 'groups.id = users_groups.group_id',
                    'columnas' => array('groups.id as groups')
                )
            ),
            'where' => array("groups.name IN ('asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user')")
        ));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }
        else
        {
            $users_groups = $this->ion_auth->get_users_groups($id)->result();
            if (sizeof($users_groups) > 1)
            {
                $usuario_otros_modulos = TRUE;
            }
            else
            {
                $usuario_otros_modulos = FALSE;
            }
        }

        if (!$usuario_otros_modulos) // Si el usuario solo tiene grupo del Módulo Asistencia editar normalmente los datos
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->ion_auth->activate($id);

            if (SIS_AUTH_MODE === 'auth0')
            {
                // AUTH0
                if ($this->db->trans_status() && $trans_ok)
                {
                    $data['active'] = 1;
                    $trans_ok = $this->Auth0_model->update_user($id, $data);
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
        }
        else
        {
            $this->session->set_flashdata('error', "<br />No se ha podido activar el usuario $usuario->username. Contacte al administrador");
        }

        redirect('asistencia/usuarios/listar', 'refresh');
    }

    public function desactivar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("asistencia/usuarios/listar", 'refresh');
        }

        $usuario = $this->Usuarios_model->get(array(
            'id' => $id,
            'join' => array(
                array(
                    'type' => 'LEFT',
                    'table' => 'users_groups',
                    'where' => 'users_groups.user_id = users.id',
                ),
                array(
                    'type' => 'LEFT',
                    'table' => 'groups',
                    'where' => 'groups.id = users_groups.group_id',
                    'columnas' => array('groups.id as groups')
                )
            ),
            'where' => array("groups.name IN ('asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user')")
        ));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }
        else
        {
            $users_groups = $this->ion_auth->get_users_groups($id)->result();
            if (sizeof($users_groups) > 1)
            {
                $usuario_otros_modulos = TRUE;
            }
            else
            {
                $usuario_otros_modulos = FALSE;
            }
        }

        if (!$usuario_otros_modulos) // Si el usuario solo tiene grupo del Módulo Asistencia editar normalmente los datos
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->ion_auth->deactivate($id);
            if (SIS_AUTH_MODE === 'auth0')
            {
                // AUTH0
                if ($this->db->trans_status() && $trans_ok)
                {
                    $data['active'] = 0;
                    $trans_ok = $this->Auth0_model->update_user($id, $data);
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
        }
        else
        {
            $this->session->set_flashdata('error', "<br />No se ha podido desactivar el usuario $usuario->username. Contacte al administrador");
        }

        redirect('asistencia/usuarios/listar', 'refresh');
    }

    public function asignar($id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("asistencia/usuarios/ver/$id", 'refresh');
        }

        $usuario = $this->Usuarios_model->get(array(
            'id' => $id,
            'join' => array(
                array(
                    'type' => 'LEFT',
                    'table' => 'users_groups',
                    'where' => 'users_groups.user_id = users.id',
                ),
                array(
                    'type' => 'LEFT',
                    'table' => 'groups',
                    'where' => 'groups.id = users_groups.group_id',
                    'columnas' => array('groups.name as groups')
                ),
                array('personas', 'personas.id = users.persona_id', 'LEFT',
                    array(
                        'personas.dni',
                        'personas.sexo',
                        'personas.cuil',
                        'personas.nombre',
                        'personas.apellido',
                        'personas.telefono',
                        'personas.celular',
                        'personas.email',
                        'personas.fecha_nacimiento',
                        'personas.nacionalidad_id'
                    )
                ),
                array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad'))
            ),
            'where' => array("groups.name IN ('asistencia_director', 'asistencia_contralor')")
        ));
        if (empty($usuario))
        {
            show_error('No se encontró el usuario', 500, 'Registro no encontrado');
        }
        else
        {
            $usuario->active = $usuario->active == 0 ? 'Inactivo' : 'Activo'; //Dejar ==
        }

        try
        {
            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            $http_response_empleado = $guzzleHttp->request('GET', "personas/datos", ['query' => ['labo_Codigo' => $usuario->username, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
            $empleado = json_decode($http_response_empleado->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleado = NULL;
        }
        if (!empty($empleado))
        {
            $usuario->oficina = $empleado->ofi_Oficina . " - " . $empleado->ofi_Descripcion;
            $usuario->horario = $empleado->hora_Descripcion;
            $usuario->cod_horario = $empleado->hora_Codigo;
        }
        else
        {
            $usuario->oficina = 'Error al conectarse a Major';
            $usuario->horario = 'Error al conectarse a Major';
            $usuario->cod_horario = 'Error al conectarse a Major';
        }

        try
        {
            $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                'form_params' => [
                    'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                    'ofi_Tipo' => 'Interna'
            ]]);
            $oficinas = json_decode($http_response_oficinas->getBody()->getContents());
        } catch (Exception $e)
        {
            $oficinas = NULL;
        }

        $array_oficinas = array();
        if (!empty($oficinas))
        {
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = "$Oficina->ofi_Oficina - $Oficina->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/usuarios/listar', 'refresh');
        }
        $this->array_oficinas_control = $array_oficinas;
        $this->array_oficinas_control[''] = '';
        $users_oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $id));
        $usuario->oficinas = array();
        if (!empty($users_oficinas))
        {
            foreach ($users_oficinas as $Oficina)
            {
                $usuario->oficinas[] = $Oficina->ofi_Oficina;
            }
        }

        $this->set_model_validation_rules($this->Usuarios_oficinas_model);
        if ($this->form_validation->run() === TRUE)
        {
            $oficina_data = $this->input->post('oficinas[]');
            if (empty($oficina_data))
            {
                $oficina_data = array();
            }
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Usuarios_oficinas_model->intersect_asignaciones($id, $oficina_data, FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Usuarios_oficinas_model->get_msg());
                redirect('asistencia/usuarios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br>Se ha producido un error con la base de datos.';
                if ($this->Usuarios_oficinas_model->get_error())
                {
                    $error_msg .= '<br>' . $this->Usuarios_oficinas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $usuario, TRUE);

        unset($this->Usuarios_model->fields['persona']);
        unset($this->Usuarios_model->fields['password']);
        unset($this->Usuarios_model->fields['password_confirm']);
        $this->Usuarios_oficinas_model->fields['oficinas']['array'] = $array_oficinas;
        $usuario->last_login = empty($usuario->last_login) ? '' : date('d/m/Y H:i', $usuario->last_login);
        $data['fields'] = $this->build_fields($this->Usuarios_model->fields, $usuario, TRUE);
        $data['fields_oficina'] = $this->build_fields($this->Usuarios_oficinas_model->fields, $usuario);

        $data['title_view'] = 'Asignar oficinas';
        $data['title'] = TITLE . ' - Asignar oficinas';
        $data['css'] = 'vendor/duallistbox/css/bootstrap-duallistbox.min.css';
        $data['js'] = 'vendor/duallistbox/js/jquery.bootstrap-duallistbox.min.js';
        $this->load_template('asistencia/usuarios/usuarios_asignar', $data);
    }

    public function reporte()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $print_data = $this->Usuarios_model->get(array(
            'select' => array('username', 'personas.apellido', 'personas.nombre', 'groups.name as grupo', 'CASE WHEN active = 0 THEN \'Inactivo\' ELSE \'Activo\' END as estado', 'FROM_UNIXTIME(last_login, "%Y-%m-%d %H:%i:%s") as last_login'),
            'join' => array(
                array(
                    'type' => 'LEFT',
                    'table' => 'personas',
                    'where' => 'personas.id = users.persona_id',
                ),
                array(
                    'type' => 'LEFT',
                    'table' => 'users_groups',
                    'where' => 'users_groups.user_id = users.id',
                ),
                array(
                    'type' => 'LEFT',
                    'table' => 'groups',
                    'where' => 'groups.id = users_groups.group_id'
                )
            ),
            'where' => array("groups.name IN ('asistencia_rrhh', 'asistencia_control', 'asistencia_director', 'asistencia_contralor', 'asistencia_user')"),
            'sort_by' => 'username',
            'sort_direction' => 'asc',
            'return_array' => TRUE
        ));
        if (!empty($print_data))
        {
            //INICIO GENERACION EXCEL
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
            if (!$validLocale)
            {
                lm('Unable to set locale to es - reverting to en_us');
            }
            $spreadsheet->getProperties()
                    ->setCreator("SistemaMLC")
                    ->setLastModifiedBy("SistemaMLC")
                    ->setTitle("Informe de Usuarios Asistencia")
                    ->setDescription("Informe de Usuarios Asistencia");
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Informe de Usuarios Asistencia");

            $cant_filas = sizeof($print_data) + 1; //1 DE ENCABEZADOS
            $sheet->getColumnDimension('A')->setWidth(15); //LEGAJO
            $sheet->getColumnDimension('B')->setWidth(30); //APELLIDO
            $sheet->getColumnDimension('C')->setWidth(30); //NOMBRE
            $sheet->getColumnDimension('D')->setWidth(25); //GRUPO
            $sheet->getColumnDimension('E')->setWidth(15); //ESTADO
            $sheet->getColumnDimension('F')->setWidth(20); //ULTIMO ACCESO
            //TITULOS
            $sheet->fromArray(array(array('LEGAJO', 'APELLIDO', 'NOMBRE', 'GRUPO', 'ESTADO', 'ÚLTIMO ACCESO')), NULL, 'A1');
            $sheet->getStyle("A1:F1")->getFont()->setBold(TRUE);
            $sheet->setAutoFilter('A1:F1');
            $border_allborders_thin = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
            $sheet->getStyle("A1:F$cant_filas")->applyFromArray($border_allborders_thin);

            //DATOS
            $sheet->fromArray($print_data, NULL, 'A2');

            $nombreArchivo = 'informe_usuarios_' . date('YmdHi');

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
            header("Cache-Control: max-age=0");

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Sin datos');
            redirect('asistencia/usuarios/listar', 'refresh');
        }
    }
}
