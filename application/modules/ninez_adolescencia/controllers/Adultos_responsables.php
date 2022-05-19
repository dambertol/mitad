<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adultos_responsables extends MY_Controller
{

    /**
     * Controlador de Adultos Responsables
     * Autor: Leandro
     * Creado: 12/09/2019
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ninez_adolescencia/Adultos_responsables_model');
        $this->load->model('Personas_model');
        $this->load->model('Nacionalidades_model');
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
        $this->load->model('Auth0_model');
        $this->load->model('Oro_model');
        $this->grupos_permitidos = array('admin', 'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general');
        $this->grupos_solo_consulta = array('ninez_adolescencia_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar_data($expediente_id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $expediente_id == NULL || !ctype_digit($expediente_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("na_adultos_responsables.id, CONCAT(personas.apellido, ', ', personas.nombre,  ' (', personas.dni, ')') as persona, na_adultos_responsables.hasta")
                ->from('na_adultos_responsables')
                ->join('personas', 'personas.id = na_adultos_responsables.persona_id', 'left')
                ->where('expediente_id', $expediente_id)
                ->add_column('ver', '<a href="ninez_adolescencia/adultos_responsables/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="ninez_adolescencia/adultos_responsables/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="ninez_adolescencia/adultos_responsables/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar($expediente_id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $expediente_id == NULL || !ctype_digit($expediente_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('ninez_adolescencia/adultos_responsables/listar', 'refresh');
        }

        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array('select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona", 'sort_by' => 'personas.apellido, personas.nombre'), array('agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);

        if (!empty($_POST))
        {
            if ($this->input->post('persona') === 'agregar')
            {
                $this->set_model_validation_rules($this->Personas_model);
                if ($this->input->post('carga_domicilio') === 'SI')
                {
                    $this->set_model_validation_rules($this->Domicilios_model);
                }
            }

            $this->set_model_validation_rules($this->Adultos_responsables_model);
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

                if ($this->input->post('persona') === 'agregar')
                {
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
                }
                else
                {
                    $persona_id = $this->input->post('persona');
                }

                $trans_ok &= $this->Adultos_responsables_model->create(array(
                    'persona_id' => $persona_id,
                    'expediente_id' => $expediente_id,
                    'hasta' => $this->get_date_sql('hasta')), FALSE);

                if (SIS_ORO_ACTIVE)
                {
                    // ORO CRM
                    if ($this->input->post('persona') === 'agregar')
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
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Adultos_responsables_model->get_msg());
                    redirect("ninez_adolescencia/expedientes/ver/$expediente_id", 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Domicilios_model->get_error())
                    {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Personas_model->get_error())
                    {
                        $error_msg .= $this->Personas_model->get_error();
                    }
                    if ($this->Adultos_responsables_model->get_error())
                    {
                        $error_msg .= $this->Adultos_responsables_model->get_error();
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

        $this->Adultos_responsables_model->fields['persona']['array'] = $array_persona;
        $data['fields'] = $this->build_fields($this->Adultos_responsables_model->fields);
        $data['expediente_id'] = $expediente_id;
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Adulto Responsable';
        $data['title'] = TITLE . ' - Agregar Adulto Responsable';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/adultos_responsables/adultos_responsables_abm', $data);
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
            redirect("ninez_adolescencia/adultos_responsables/ver/$id", 'refresh');
        }

        $adultos_responsabl = $this->Adultos_responsables_model->get_one($id);
        if (empty($adultos_responsabl))
        {
            show_error('No se encontró el Adulto Responsable', 500, 'Registro no encontrado');
        }

        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        unset($this->Adultos_responsables_model->fields['persona']);
        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);

        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            if ($this->input->post('carga_domicilio') === 'SI')
            {
                $this->set_model_validation_rules($this->Domicilios_model);
            }

            $this->set_model_validation_rules($this->Adultos_responsables_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                if ($this->input->post('carga_domicilio') === 'SI')
                {
                    if (!empty($adultos_responsabl->domicilio_id))
                    {
                        $trans_ok &= $this->Domicilios_model->update(array(
                            'id' => $adultos_responsabl->domicilio_id,
                            'calle' => $this->input->post('calle'),
                            'barrio' => $this->input->post('barrio'),
                            'altura' => $this->input->post('altura'),
                            'piso' => $this->input->post('piso'),
                            'dpto' => $this->input->post('dpto'),
                            'manzana' => $this->input->post('manzana'),
                            'casa' => $this->input->post('casa'),
                            'localidad_id' => $this->input->post('localidad')), FALSE);

                        $domicilio_id = $adultos_responsabl->domicilio_id;
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
                    'id' => $adultos_responsabl->persona_id,
                    'dni' => $this->input->post('dni'),
                    'sexo' => $this->input->post('sexo'),
                    'cuil' => $this->input->post('cuil'),
                    'nombre' => $this->input->post('nombre'),
                    'apellido' => $this->input->post('apellido'),
                    'telefono' => $this->input->post('telefono'),
                    'celular' => $this->input->post('celular'),
                    'email' => $this->input->post('email'),
                    'fecha_nacimiento' => $this->get_date_sql('fecha_nacimiento'),
                    'nacionalidad_id' => $this->input->post('nacionalidad')), FALSE);

                $trans_ok &= $this->Adultos_responsables_model->update(array(
                    'id' => $this->input->post('id'),
                    'persona_id' => $adultos_responsabl->persona_id,
                    'expediente_id' => $adultos_responsabl->expediente_id,
                    'hasta' => $this->get_date_sql('hasta')), FALSE);

                $user_id = $this->Personas_model->get_user_id($adultos_responsabl->persona_id);
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
                        $datos['id'] = $adultos_responsabl->persona_id;
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
                    $this->session->set_flashdata('message', $this->Adultos_responsables_model->get_msg());
                    redirect("ninez_adolescencia/expedientes/ver/$adultos_responsabl->expediente_id", 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Domicilios_model->get_error())
                    {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Personas_model->get_error())
                    {
                        $error_msg .= $this->Personas_model->get_error();
                    }
                    if ($this->Adultos_responsables_model->get_error())
                    {
                        $error_msg .= $this->Adultos_responsables_model->get_error();
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
        if (!empty($adultos_responsabl->domicilio_id))
        {
            $adultos_responsabl->carga_domicilio = 'SI';
        }
        else
        {
            $adultos_responsabl->carga_domicilio = 'NO';
        }
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $adultos_responsabl);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $adultos_responsabl);

        $data['fields'] = $this->build_fields($this->Adultos_responsables_model->fields, $adultos_responsabl);
        $data['adultos_responsabl'] = $adultos_responsabl;
        $data['expediente_id'] = $adultos_responsabl->expediente_id;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Adulto Responsable';
        $data['title'] = TITLE . ' - Editar Adulto Responsable';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/adultos_responsables/adultos_responsables_abm', $data);
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
            redirect("ninez_adolescencia/adultos_responsables/ver/$id", 'refresh');
        }

        $adultos_responsabl = $this->Adultos_responsables_model->get_one($id);
        if (empty($adultos_responsabl))
        {
            show_error('No se encontró el Adulto Responsable', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Adultos_responsables_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Adultos_responsables_model->get_msg());
                redirect('ninez_adolescencia/adultos_responsables/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Adultos_responsables_model->get_error())
                {
                    $error_msg .= $this->Adultos_responsables_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        unset($this->Adultos_responsables_model->fields['persona']);

        if (!empty($adultos_responsabl->domicilio_id))
        {
            $adultos_responsabl->carga_domicilio = 'SI';
        }
        else
        {
            $adultos_responsabl->carga_domicilio = 'NO';
        }

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $adultos_responsabl, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $adultos_responsabl, TRUE);
        $data['fields'] = $this->build_fields($this->Adultos_responsables_model->fields, $adultos_responsabl, TRUE);
        $data['adultos_responsabl'] = $adultos_responsabl;
        $data['expediente_id'] = $adultos_responsabl->expediente_id;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Adulto Responsable';
        $data['title'] = TITLE . ' - Eliminar Adulto Responsable';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/adultos_responsables/adultos_responsables_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $adultos_responsabl = $this->Adultos_responsables_model->get_one($id);
        if (empty($adultos_responsabl))
        {
            show_error('No se encontró el Adulto Responsable', 500, 'Registro no encontrado');
        }
        unset($this->Adultos_responsables_model->fields['persona']);

        if (!empty($adultos_responsabl->domicilio_id))
        {
            $adultos_responsabl->carga_domicilio = 'SI';
        }
        else
        {
            $adultos_responsabl->carga_domicilio = 'NO';
        }

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $adultos_responsabl, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $adultos_responsabl, TRUE);
        $data['fields'] = $this->build_fields($this->Adultos_responsables_model->fields, $adultos_responsabl, TRUE);
        $data['adultos_responsabl'] = $adultos_responsabl;
        $data['expediente_id'] = $adultos_responsabl->expediente_id;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Adulto Responsable';
        $data['title'] = TITLE . ' - Ver Adulto Responsable';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/adultos_responsables/adultos_responsables_abm', $data);
    }
}
