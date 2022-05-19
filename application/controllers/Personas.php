<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Personas extends MY_Controller
{

    /**
     * Controlador de Personas
     * Autor: Leandro
     * Creado: 01/06/2018
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Personas_model');
        $this->load->model('Nacionalidades_model');
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
        $this->load->model('Auth0_model');
        $this->load->model('Oro_model');
        $this->grupos_permitidos = array('admin', 'consulta_general');
        $this->grupos_get = array('gobierno_user', 'ninez_adolescencia_admin', 'tramites_online_admin', 'transferencias_municipal');
        $this->grupos_solo_consulta = array('consulta_general');
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
                array('label' => 'DNI', 'data' => 'dni', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Sexo', 'data' => 'sexo', 'width' => 8),
                array('label' => 'CUIL', 'data' => 'cuil', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 12),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 12),
                array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Celular', 'data' => 'celular', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Email', 'data' => 'email', 'width' => 19),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'personas_table',
            'source_url' => 'personas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_personas_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Personas';
        $data['title'] = TITLE . ' - Personas';
        $this->load_template('personas/personas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, dni, sexo, cuil, nombre, apellido, telefono, celular, email')
                ->unset_column('id')
                ->from('personas')
                ->add_column('ver', '<a href="personas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="personas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="personas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('personas/listar', 'refresh');
        }

        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        $this->set_model_validation_rules($this->Personas_model);
        if ($this->input->post('carga_domicilio') === 'SI')
        {
            $this->set_model_validation_rules($this->Domicilios_model);
        }
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

            if (SIS_ORO_ACTIVE)
            {
                // ORO CRM
                $persona_id = $this->Personas_model->get_row_id();
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
                    $datos['tags'] = 'Sistema MLC';
                    $this->Oro_model->send_data($datos);
                }
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Personas_model->get_msg());
                redirect('personas/listar', 'refresh');
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
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
        $data['fields'] = $this->build_fields($this->Personas_model->fields);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Persona';
        $data['title'] = TITLE . ' - Agregar Persona';
        $this->load_template('personas/personas_abm', $data);
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
            redirect("personas/ver/$id", 'refresh');
        }

        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        $persona = $this->Personas_model->get_one($id);
        if (empty($persona))
        {
            show_error('No se encontró la Persona', 500, 'Registro no encontrado');
        }

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        if (!empty($persona->domicilio_id))
        {
            $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI');
        }
        else
        {
            $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');
        }

        $this->set_model_validation_rules($this->Personas_model);
        if ($this->input->post('carga_domicilio') === 'SI')
        {
            $this->set_model_validation_rules($this->Domicilios_model);
        }
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
                    if (!empty($persona->domicilio_id))
                    {
                        $trans_ok &= $this->Domicilios_model->update(array(
                            'id' => $persona->domicilio_id,
                            'calle' => $this->input->post('calle'),
                            'barrio' => $this->input->post('barrio'),
                            'altura' => $this->input->post('altura'),
                            'piso' => $this->input->post('piso'),
                            'dpto' => $this->input->post('dpto'),
                            'manzana' => $this->input->post('manzana'),
                            'casa' => $this->input->post('casa'),
                            'localidad_id' => $this->input->post('localidad')), FALSE);

                        $domicilio_id = $persona->domicilio_id;
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
                    'id' => $this->input->post('id'),
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

                if (SIS_ORO_ACTIVE)
                {
                    // ORO CRM
                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $datos['id'] = $this->input->post('id');
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

                $user_id = $this->Personas_model->get_user_id($this->input->post('id'));
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

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Personas_model->get_msg());
                    redirect('personas/listar', 'refresh');
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
        if (!empty($persona->domicilio_id))
        {
            $persona->carga_domicilio = 'SI';
        }
        else
        {
            $persona->carga_domicilio = 'NO';
        }
        $data['fields'] = $this->build_fields($this->Personas_model->fields, $persona);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $persona);
        $data['persona'] = $persona;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Persona';
        $data['title'] = TITLE . ' - Editar Persona';
        $this->load_template('personas/personas_abm', $data);
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
            redirect("personas/ver/$id", 'refresh');
        }

        $persona = $this->Personas_model->get_one($id);
        if (empty($persona))
        {
            show_error('No se encontró la Persona', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Personas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if (!empty($persona->domicilio_id))
            {
                $trans_ok &= $this->Domicilios_model->delete(array('id' => $persona->domicilio_id), FALSE);
            }
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Personas_model->get_msg());
                redirect('personas/listar', 'refresh');
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
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        if (!empty($persona->domicilio_id))
        {
            $persona->carga_domicilio = 'SI';
        }
        else
        {
            $persona->carga_domicilio = 'NO';
        }
        $data['fields'] = $this->build_fields($this->Personas_model->fields, $persona, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $persona, TRUE);
        $data['persona'] = $persona;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Persona';
        $data['title'] = TITLE . ' - Eliminar Persona';
        $this->load_template('personas/personas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $persona = $this->Personas_model->get_one($id);
        if (empty($persona))
        {
            show_error('No se encontró la Persona', 500, 'Registro no encontrado');
        }

        if (!empty($persona->domicilio_id))
        {
            $persona->carga_domicilio = 'SI';
        }
        else
        {
            $persona->carga_domicilio = 'NO';
        }
        $data['fields'] = $this->build_fields($this->Personas_model->fields, $persona, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $persona, TRUE);
        $data['persona'] = $persona;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Persona';
        $data['title'] = TITLE . ' - Ver Persona';
        $this->load_template('personas/personas_abm', $data);
    }

    public function get_persona()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) && !in_groups($this->grupos_get, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->form_validation->set_rules('id', 'ID', 'required|integer|max_length[8]');

        if ($this->form_validation->run() === TRUE)
        {
            $persona = $this->Personas_model->get_one($this->input->post('id'));
            if (!empty($persona))
            {
                $persona->fecha_nacimiento = date_format(new DateTime($persona->fecha_nacimiento), 'd/m/Y');
                if (empty($persona->domicilio_id))
                {
                    $persona->carga_domicilio = 'NO';
                }
                else
                {
                    $persona->carga_domicilio = 'SI';
                }
                $data['persona'] = $persona;
            }
            else
            {
                $data['error'] = 'Persona no encontrada';
            }
        }
        else
        {
            $data['error'] = 'Debe ingresar un ID válido';
        }

        echo json_encode($data);
    }
}
