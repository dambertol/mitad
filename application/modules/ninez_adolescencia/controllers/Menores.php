<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Menores extends MY_Controller
{

    /**
     * Controlador de Menores
     * Autor: Leandro
     * Creado: 12/09/2019
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ninez_adolescencia/Menores_model');
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
                ->select("na_menores.id, CONCAT(personas.apellido, ', ', personas.nombre,  ' (', personas.dni, ')') as persona")
                ->from('na_menores')
                ->join('personas', 'personas.id = na_menores.persona_id', 'left')
                ->where('expediente_id', $expediente_id)
                ->add_column('ver', '<a href="ninez_adolescencia/menores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="ninez_adolescencia/menores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="ninez_adolescencia/menores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('ninez_adolescencia/menores/listar', 'refresh');
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

            $this->set_model_validation_rules($this->Menores_model);
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

                $trans_ok &= $this->Menores_model->create(array(
                    'persona_id' => $persona_id,
                    'expediente_id' => $expediente_id), FALSE);

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
                    $this->session->set_flashdata('message', $this->Menores_model->get_msg());
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
                    if ($this->Menores_model->get_error())
                    {
                        $error_msg .= $this->Menores_model->get_error();
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

        $this->Menores_model->fields['persona']['array'] = $array_persona;
        $data['fields'] = $this->build_fields($this->Menores_model->fields);
        $data['expediente_id'] = $expediente_id;
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Menor';
        $data['title'] = TITLE . ' - Agregar Menor';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/menores/menores_abm', $data);
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
            redirect("ninez_adolescencia/menores/ver/$id", 'refresh');
        }

        $menor = $this->Menores_model->get_one($id);
        if (empty($menor))
        {
            show_error('No se encontró el Menor', 500, 'Registro no encontrado');
        }

        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        unset($this->Menores_model->fields['persona']);
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

            $this->set_model_validation_rules($this->Menores_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                if ($this->input->post('carga_domicilio') === 'SI')
                {
                    if (!empty($menor->domicilio_id))
                    {
                        $trans_ok &= $this->Domicilios_model->update(array(
                            'id' => $menor->domicilio_id,
                            'calle' => $this->input->post('calle'),
                            'barrio' => $this->input->post('barrio'),
                            'altura' => $this->input->post('altura'),
                            'piso' => $this->input->post('piso'),
                            'dpto' => $this->input->post('dpto'),
                            'manzana' => $this->input->post('manzana'),
                            'casa' => $this->input->post('casa'),
                            'localidad_id' => $this->input->post('localidad')), FALSE);

                        $domicilio_id = $menor->domicilio_id;
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
                    'id' => $menor->persona_id,
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

                $trans_ok &= $this->Menores_model->update(array(
                    'id' => $this->input->post('id'),
                    'persona_id' => $menor->persona_id,
                    'expediente_id' => $menor->expediente_id), FALSE);

                $user_id = $this->Personas_model->get_user_id($menor->persona_id);
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
                        $datos['id'] = $menor->persona_id;
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
                    $this->session->set_flashdata('message', $this->Menores_model->get_msg());
                    redirect("ninez_adolescencia/expedientes/ver/$menor->expediente_id", 'refresh');
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
                    if ($this->Menores_model->get_error())
                    {
                        $error_msg .= $this->Menores_model->get_error();
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
        if (!empty($menor->domicilio_id))
        {
            $menor->carga_domicilio = 'SI';
        }
        else
        {
            $menor->carga_domicilio = 'NO';
        }
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $menor);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $menor);

        $data['fields'] = $this->build_fields($this->Menores_model->fields, $menor);
        $data['menor'] = $menor;
        $data['expediente_id'] = $menor->expediente_id;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Menor';
        $data['title'] = TITLE . ' - Editar Menor';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/menores/menores_abm', $data);
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
            redirect("ninez_adolescencia/menores/ver/$id", 'refresh');
        }

        $menor = $this->Menores_model->get_one($id);
        if (empty($menor))
        {
            show_error('No se encontró el Menor', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Menores_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Menores_model->get_msg());
                redirect('ninez_adolescencia/menores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Menores_model->get_error())
                {
                    $error_msg .= $this->Menores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        unset($this->Menores_model->fields['persona']);

        if (!empty($menor->domicilio_id))
        {
            $menor->carga_domicilio = 'SI';
        }
        else
        {
            $menor->carga_domicilio = 'NO';
        }

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $menor, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $menor, TRUE);

        $data['fields'] = $this->build_fields($this->Menores_model->fields, $menor, TRUE);
        $data['menor'] = $menor;
        $data['expediente_id'] = $menor->expediente_id;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Menor';
        $data['title'] = TITLE . ' - Eliminar Menor';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/menores/menores_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $menor = $this->Menores_model->get_one($id);
        if (empty($menor))
        {
            show_error('No se encontró el Menor', 500, 'Registro no encontrado');
        }

        unset($this->Menores_model->fields['persona']);

        if (!empty($menor->domicilio_id))
        {
            $menor->carga_domicilio = 'SI';
        }
        else
        {
            $menor->carga_domicilio = 'NO';
        }

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $menor, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $menor, TRUE);

        $data['fields'] = $this->build_fields($this->Menores_model->fields, $menor, TRUE);
        $data['menor'] = $menor;
        $data['expediente_id'] = $menor->expediente_id;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Menor';
        $data['title'] = TITLE . ' - Ver Menor';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/menores/menores_abm', $data);
    }
}
