<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escribanos extends MY_Controller
{

    /**
     * Controlador de Escribanos
     * Autor: Leandro
     * Creado: 04/06/2018
     * Modificado: 20/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('transferencias/Escribanos_model');
        $this->load->model('transferencias/Usuarios_oficinas_model');
        $this->load->model('Personas_model');
        $this->load->model('Nacionalidades_model');
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
        $this->load->model('Grupos_model');
        $this->load->model('Auth0_model');
        $this->load->model('Oro_model');
        $this->grupos_permitidos = array('admin', 'transferencias_municipal', 'transferencias_consulta_general');
        $this->grupos_admin = array('admin');
        $this->grupos_municipal = array('transferencias_municipal');
        $this->grupos_solo_consulta = array('transferencias_consulta_general');
        // Inicializaciones necesarias colocar acá.
        if (in_groups($this->grupos_municipal, $this->grupos))
        {
            $usuarios_oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id')));
            if (empty($usuarios_oficinas))
            {
                show_error('No se encontró el Usuario', 500, 'Registro no encontrado');
            }
            if ($usuarios_oficinas[0]->oficina_id !== '2')
            {
                $this->permiso_edicion = FALSE;
            }
            else
            {
                $this->permiso_edicion = TRUE;
            }
        }
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_municipal, $this->grupos))
        {
            $data['agregar'] = $this->permiso_edicion;
        }
        else
        {
            $data['agregar'] = TRUE;
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'CUIL', 'data' => 'cuil', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 26),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 26),
                array('label' => 'Matricula N°', 'data' => 'matricula_nro', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Registro N°', 'data' => 'registro_nro', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Registro Tipo', 'data' => 'registro_tipo', 'width' => 12),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'escribanos_table',
            'source_url' => 'transferencias/escribanos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_escribanos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Escribanos';
        $data['title'] = TITLE . ' - Escribanos';
        $this->load_template('transferencias/escribanos/escribanos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $dt = $this->datatables
                ->select('tr_escribanos.id, personas.cuil as cuil, personas.nombre as nombre, personas.apellido as apellido, tr_escribanos.matricula_nro, tr_escribanos.registro_nro, tr_escribanos.registro_tipo')
                ->from('tr_escribanos')
                ->join('personas', 'personas.id = tr_escribanos.persona_id', 'left');

        if (in_groups($this->grupos_municipal, $this->grupos) && !$this->permiso_edicion)
        {
            $dt->add_column('ver', '<a href="transferencias/escribanos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                    ->add_column('editar', '', 'id')
                    ->add_column('eliminar', '', 'id');
        }
        else
        {
            $dt->add_column('ver', '<a href="transferencias/escribanos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                    ->add_column('editar', '<a href="transferencias/escribanos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                    ->add_column('eliminar', '<a href="transferencias/escribanos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');
        }

        echo $dt->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_municipal, $this->grupos) && !$this->permiso_edicion)
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('transferencias/escribanos/listar', 'refresh');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('transferencias/escribanos/listar', 'refresh');
        }

        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array(
            'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona",
            'where' => array(
                "id NOT IN (SELECT tr_escribanos.persona_id FROM tr_escribanos) AND " //Personas sin escribano
                . "(id NOT IN (SELECT persona_id FROM users WHERE persona_id IS NOT NULL) OR id IN (SELECT persona_id FROM users LEFT JOIN users_groups ON users.id = users_groups.user_id LEFT JOIN `groups` ON users_groups.group_id = `groups`.id WHERE `groups`.name = 'transferencias_publico'))"), //Personas sin usuario o con usuario "transferencia_publico"
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array('agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->array_registro_tipo_control = $array_registro_tipo = array('Titular' => 'Titular', 'Adscripto' => 'Adscripto');

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        $this->Personas_model->fields['email']['required'] = TRUE;

        if (!empty($_POST) && $_POST['persona'] === 'agregar')
        {
            $this->set_model_validation_rules($this->Personas_model);
            if ($this->input->post('carga_domicilio') === 'SI')
            {
                $this->set_model_validation_rules($this->Domicilios_model);
            }
            $this->set_model_validation_rules($this->Escribanos_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;

                if ($this->input->post('carga_domicilio') === 'SI')
                {
                    $trans_ok &= $this->Domicilios_model->create(array(
                        'calle' => $this->input->post('calle'),
                        'barrio' => $this->input->post('barrio'),
                        'altura' => $this->input->post('altura'),
                        'piso' => $this->input->post('piso'),
                        'dpto' => $this->input->post('dpto'),
                        'manzana' => $this->input->post('manzana'),
                        'casa' => $this->input->post('casa'),
                        'localidad_id' => $this->input->post('localidad')), FALSE);

                    $domicilio_id = $this->Domicilios_model->get_row_id();
                }
                else
                {
                    $domicilio_id = 'NULL';
                }

                $trans_ok &= $this->Personas_model->create(array(
                    'dni' => $this->input->post('dni'),
                    'sexo' => $this->input->post('sexo'),
                    'cuil' => $this->input->post('cuil'),
                    'nombre' => $this->input->post('nombre'),
                    'apellido' => $this->input->post('apellido'),
                    'telefono' => $this->input->post('telefono'),
                    'celular' => $this->input->post('celular'),
                    'email' => $this->input->post('email'),
                    'fecha_nacimiento' => $this->get_date_sql('fecha_nacimiento'),
                    'nacionalidad_id' => $this->input->post('nacionalidad'),
                    'domicilio_id' => $domicilio_id), FALSE);

                $persona_id = $this->Personas_model->get_row_id();

                $trans_ok &= $this->Escribanos_model->create(array(
                    'persona_id' => $persona_id,
                    'matricula_nro' => $this->input->post('matricula_nro'),
                    'registro_nro' => $this->input->post('registro_nro'),
                    'registro_tipo' => $this->input->post('registro_tipo')), FALSE);

                $grupo = $this->Grupos_model->get(array('name' => 'transferencias_publico'));
                if (empty($grupo))
                {
                    show_error('No se encontró el grupo', 500, 'Registro no encontrado');
                }
                $group_data = array($grupo[0]->id);
                $additional_data = array('persona_id' => $persona_id, 'password_change' => 0);
                $password = random_password(10, 1, "lower_case,upper_case,numbers");
                $user_id = $this->ion_auth->register($this->input->post('dni'), $password[0], strtolower($this->input->post('email')), $additional_data, $group_data);
                if (!$user_id)
                {
                    $trans_ok = FALSE;
                }

                if (SIS_AUTH_MODE === 'auth0')
                {
                    // AUTH0
                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $additional_data['nombre'] = $this->input->post('nombre');
                        $additional_data['apellido'] = $this->input->post('apellido');
                        $additional_data['email'] = strtolower($this->input->post('email'));
                        $additional_data['username'] = $this->input->post('dni');
                        $additional_data['password'] = $password[0];
                        $trans_ok = $this->Auth0_model->create_user($user_id, $additional_data);
                    }
                }

                if (SIS_ORO_ACTIVE)
                {
                    // ORO CRM
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
                        if ($this->input->post('carga_domicilio') === 'SI')
                        {
                            $datos['calle'] = $this->input->post('calle');
                            $datos['barrio'] = $this->input->post('barrio');
                            $datos['altura'] = $this->input->post('altura');
                            $datos['piso'] = $this->input->post('piso');
                            $datos['dpto'] = $this->input->post('dpto');
                            $datos['manzana'] = $this->input->post('manzana');
                            $datos['casa'] = $this->input->post('casa');
                            $datos['localidad_id'] = $this->input->post('localidad');
                        }
                        $datos['tags'] = 'Transferencias';
                        $this->Oro_model->send_data($datos);
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->send_email('transferencias/email/escribanos_alta', 'Cuenta creada', $this->input->post('email'), array('dni' => $this->input->post('dni'), 'password' => $password[0]));
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Escribanos_model->get_msg());
                    redirect('transferencias/escribanos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Personas_model->get_error())
                    {
                        $error_msg .= $this->Personas_model->get_error();
                    }
                    if ($this->Domicilios_model->get_error())
                    {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Escribanos_model->get_error())
                    {
                        $error_msg .= $this->Escribanos_model->get_error();
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
        }
        else
        {
            $this->set_model_validation_rules($this->Escribanos_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;

                $trans_ok &= $this->Escribanos_model->create(array(
                    'persona_id' => $this->input->post('persona'),
                    'matricula_nro' => $this->input->post('matricula_nro'),
                    'registro_nro' => $this->input->post('registro_nro'),
                    'registro_tipo' => $this->input->post('registro_tipo')), FALSE);

                $user_id = $this->Personas_model->get_user_id($this->input->post('persona'));
                if ($user_id === 0) //Persona sin usuario
                {
                    $enviar_mail = TRUE;
                    $persona = $this->Personas_model->get(array('id' => $this->input->post('persona')));
                    if (empty($persona))
                    {
                        show_error('No se encontró la Persona', 500, 'Registro no encontrado');
                    }
                    if (empty($persona->email))
                    {
                        $trans_ok = FALSE;
                        $error_msg .= '<br />La persona debe tener un email cargado.';
                    }

                    $grupo = $this->Grupos_model->get(array('name' => 'transferencias_publico'));
                    if (empty($grupo))
                    {
                        show_error('No se encontró el grupo', 500, 'Registro no encontrado');
                    }
                    $group_data = array($grupo[0]->id);
                    $additional_data = array('persona_id' => $persona->id, 'password_change' => 0);
                    $password = random_password(10, 1, "lower_case,upper_case,numbers");
                    $user_id = $this->ion_auth->register($persona->dni, $password[0], strtolower($persona->email), $additional_data, $group_data);
                    if (!$user_id)
                    {
                        $trans_ok = FALSE;
                    }

                    if (SIS_AUTH_MODE === 'auth0')
                    {
                        // AUTH0
                        if ($this->db->trans_status() && $trans_ok)
                        {
                            $additional_data['nombre'] = $persona->nombre;
                            $additional_data['apellido'] = $persona->apellido;
                            $additional_data['email'] = strtolower($persona->email);
                            $additional_data['username'] = $persona->dni;
                            $additional_data['password'] = $password[0];
                            $trans_ok = $this->Auth0_model->create_user($user_id, $additional_data);
                        }
                    }
                }
                else
                {
                    $enviar_mail = FALSE;
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    if ($enviar_mail)
                    {
                        $this->send_email('transferencias/email/escribanos_alta', 'Cuenta creada', $persona->email, array('dni' => $persona->dni, 'password' => $password[0]));
                    }
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Escribanos_model->get_msg());
                    redirect('transferencias/escribanos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Escribanos_model->get_error())
                    {
                        $error_msg .= $this->Escribanos_model->get_error();
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
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields);
        $this->Escribanos_model->fields['registro_tipo']['array'] = $array_registro_tipo;
        $this->Escribanos_model->fields['persona']['array'] = $array_persona;
        $data['fields'] = $this->build_fields($this->Escribanos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Escribano';
        $data['title'] = TITLE . ' - Agregar Escribano';
        $data['js'] = 'js/transferencias/base.js';
        $this->load_template('transferencias/escribanos/escribanos_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_municipal, $this->grupos) && !$this->permiso_edicion)
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('transferencias/escribanos/listar', 'refresh');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("transferencias/escribanos/ver/$id", 'refresh');
        }

        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        $escribano = $this->Escribanos_model->get_one($id);
        if (empty($escribano))
        {
            show_error('No se encontró el Escribano', 500, 'Registro no encontrado');
        }

        $escribanos_model = $this->Escribanos_model;
        unset($escribanos_model->fields['persona']);
        $this->array_registro_tipo_control = $array_registro_tipo = array('Titular' => 'Titular', 'Adscripto' => 'Adscripto');

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        if (!empty($escribano->domicilio_id))
        {
            $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI');
        }
        else
        {
            $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');
        }

        $this->Personas_model->fields['email']['required'] = TRUE;

        $this->set_model_validation_rules($this->Personas_model);
        if ($this->input->post('carga_domicilio') === 'SI')
        {
            $this->set_model_validation_rules($this->Domicilios_model);
        }
        $this->set_model_validation_rules($this->Escribanos_model);
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

                if ($this->input->post('carga_domicilio') === 'SI')
                {
                    if (!empty($escribano->domicilio_id))
                    {
                        $trans_ok &= $this->Domicilios_model->update(array(
                            'id' => $escribano->domicilio_id,
                            'calle' => $this->input->post('calle'),
                            'barrio' => $this->input->post('barrio'),
                            'altura' => $this->input->post('altura'),
                            'piso' => $this->input->post('piso'),
                            'dpto' => $this->input->post('dpto'),
                            'manzana' => $this->input->post('manzana'),
                            'casa' => $this->input->post('casa'),
                            'localidad_id' => $this->input->post('localidad')), FALSE);

                        $domicilio_id = $escribano->domicilio_id;
                    }
                    else
                    {
                        $trans_ok &= $this->Domicilios_model->create(array(
                            'calle' => $this->input->post('calle'),
                            'barrio' => $this->input->post('barrio'),
                            'altura' => $this->input->post('altura'),
                            'piso' => $this->input->post('piso'),
                            'dpto' => $this->input->post('dpto'),
                            'manzana' => $this->input->post('manzana'),
                            'casa' => $this->input->post('casa'),
                            'localidad_id' => $this->input->post('localidad')), FALSE);

                        $domicilio_id = $this->Domicilios_model->get_row_id();
                    }
                }
                else
                {
                    $domicilio_id = 'NULL';
                }

                $trans_ok &= $this->Personas_model->update(array(
                    'id' => $escribano->persona_id,
                    'dni' => $this->input->post('dni'),
                    'sexo' => $this->input->post('sexo'),
                    'cuil' => $this->input->post('cuil'),
                    'nombre' => $this->input->post('nombre'),
                    'apellido' => $this->input->post('apellido'),
                    'telefono' => $this->input->post('telefono'),
                    'celular' => $this->input->post('celular'),
                    'email' => $this->input->post('email'),
                    'fecha_nacimiento' => $this->get_date_sql('fecha_nacimiento'),
                    'nacionalidad_id' => $this->input->post('nacionalidad'),
                    'domicilio_id' => $domicilio_id), FALSE);

                $trans_ok &= $this->Escribanos_model->update(array(
                    'id' => $this->input->post('id'),
                    'matricula_nro' => $this->input->post('matricula_nro'),
                    'registro_nro' => $this->input->post('registro_nro'),
                    'registro_tipo' => $this->input->post('registro_tipo')), FALSE);

                $user_id = $this->Personas_model->get_user_id($escribano->persona_id);
                if ($user_id !== 0) //Persona con usuario
                {
                    if (SIS_AUTH_MODE === 'auth0')
                    {
                        // AUTH0
                        if ($this->db->trans_status() && $trans_ok)
                        {
                            $data['nombre'] = $this->input->post('nombre');
                            $data['apellido'] = $this->input->post('apellido');
                            $data['email'] = $this->input->post('email');
                            $trans_ok = $this->Auth0_model->update_user($user_id, $data);
                        }
                    }
                }

                if (SIS_ORO_ACTIVE)
                {
                    // ORO CRM
                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $datos['id'] = $escribano->persona_id;
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
                        if ($this->input->post('carga_domicilio') === 'SI')
                        {
                            $datos['calle'] = $this->input->post('calle');
                            $datos['barrio'] = $this->input->post('barrio');
                            $datos['altura'] = $this->input->post('altura');
                            $datos['piso'] = $this->input->post('piso');
                            $datos['dpto'] = $this->input->post('dpto');
                            $datos['manzana'] = $this->input->post('manzana');
                            $datos['casa'] = $this->input->post('casa');
                            $datos['localidad_id'] = $this->input->post('localidad');
                        }
                        $this->Oro_model->send_data($datos);
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Escribanos_model->get_msg());
                    redirect('transferencias/escribanos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Personas_model->get_error())
                    {
                        $error_msg .= $this->Personas_model->get_error();
                    }
                    if ($this->Domicilios_model->get_error())
                    {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Escribanos_model->get_error())
                    {
                        $error_msg .= $this->Escribanos_model->get_error();
                    }
                    if ($this->Auth0_model->errors())
                    {
                        $error_msg .= $this->Auth0_model->errors();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
        if (!empty($escribano->domicilio_id))
        {
            $escribano->carga_domicilio = 'SI';
        }
        else
        {
            $escribano->carga_domicilio = 'NO';
        }
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $escribano);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $escribano);
        $this->Escribanos_model->fields['registro_tipo']['array'] = $array_registro_tipo;
        $data['fields'] = $this->build_fields($this->Escribanos_model->fields, $escribano);
        $data['escribano'] = $escribano;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Escribano';
        $data['title'] = TITLE . ' - Editar Escribano';
        $data['js'] = 'js/transferencias/base.js';
        $this->load_template('transferencias/escribanos/escribanos_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_municipal, $this->grupos) && !$this->permiso_edicion)
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('transferencias/escribanos/listar', 'refresh');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("transferencias/escribanos/ver/$id", 'refresh');
        }

        $escribano = $this->Escribanos_model->get_one($id);
        if (empty($escribano))
        {
            show_error('No se encontró el Escribano', 500, 'Registro no encontrado');
        }

        $escribanos_model = $this->Escribanos_model;
        unset($escribanos_model->fields['persona']);

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Escribanos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Escribanos_model->get_msg());
                redirect('transferencias/escribanos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Escribanos_model->get_error())
                {
                    $error_msg .= $this->Escribanos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        if (!empty($escribano->domicilio_id))
        {
            $escribano->carga_domicilio = 'SI';
        }
        else
        {
            $escribano->carga_domicilio = 'NO';
        }
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $escribano, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $escribano, TRUE);
        $data['fields'] = $this->build_fields($this->Escribanos_model->fields, $escribano, TRUE);
        $data['escribano'] = $escribano;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Escribano';
        $data['title'] = TITLE . ' - Eliminar Escribano';
        $data['js'] = 'js/transferencias/base.js';
        $this->load_template('transferencias/escribanos/escribanos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $escribano = $this->Escribanos_model->get_one($id);
        if (empty($escribano))
        {
            show_error('No se encontró el Escribano', 500, 'Registro no encontrado');
        }

        $escribanos_model = $this->Escribanos_model;
        unset($escribanos_model->fields['persona']);

        $data['error'] = $this->session->flashdata('error');

        if (!empty($escribano->domicilio_id))
        {
            $escribano->carga_domicilio = 'SI';
        }
        else
        {
            $escribano->carga_domicilio = 'NO';
        }
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $escribano, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $escribano, TRUE);
        $data['fields'] = $this->build_fields($this->Escribanos_model->fields, $escribano, TRUE);
        $data['escribano'] = $escribano;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Escribano';
        $data['title'] = TITLE . ' - Ver Escribano';
        $data['js'] = 'js/transferencias/base.js';
        $this->load_template('transferencias/escribanos/escribanos_abm', $data);
    }

    private function send_email($template, $title, $to, $data)
    {
        if (SIS_EMAIL_MODULO)
        {
            $this->email->initialize();
            $message = $this->load->view($template, $data, TRUE);
            $this->email->clear(TRUE);
            $this->email->set_mailtype("html");
            $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->email->to($to);
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $title);
            $this->email->message($message);

            if ($this->email->send())
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            return TRUE;
        }
    }
}
