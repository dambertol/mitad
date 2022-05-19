<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partes extends MY_Controller
{

    /**
     * Controlador de Partes
     * Autor: Leandro
     * Creado: 08/01/2020
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('gobierno/Partes_model');
        $this->load->model('Personas_model');
        $this->load->model('Nacionalidades_model');
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
        $this->load->model('Oro_model');
        $this->grupos_permitidos = array('admin', 'gobierno_user', 'gobierno_consulta_general');
        $this->grupos_solo_consulta = array('gobierno_consulta_general');
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
                array('label' => 'Nombre Parte', 'data' => 'nombre', 'width' => 25),
                array('label' => 'DNI', 'data' => 'persona_dni', 'width' => 16, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'persona_nombre', 'width' => 25),
                array('label' => 'Apellido', 'data' => 'persona_apellido', 'width' => 25),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'partes_table',
            'source_url' => 'gobierno/partes/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_partes_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Partes';
        $data['title'] = TITLE . ' - Partes';
        $this->load_template('gobierno/partes/partes_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('go_partes.id, go_partes.nombre, personas.dni as persona_dni, personas.nombre as persona_nombre, personas.apellido as persona_apellido')
                ->from('go_partes')
                ->join('personas', 'personas.id = go_partes.persona_id', 'left')
                ->add_column('ver', '<a href="gobierno/partes/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="gobierno/partes/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="gobierno/partes/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('gobierno/partes/listar', 'refresh');
        }

        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array(
            'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona",
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array('sin_persona' => '-- Sin Persona --', 'agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        if (!empty($_POST) && $_POST['persona'] === 'agregar')
        {
            $this->set_model_validation_rules($this->Personas_model);
            if ($this->input->post('carga_domicilio') === 'SI')
            {
                $this->set_model_validation_rules($this->Domicilios_model);
            }
            $this->set_model_validation_rules($this->Partes_model);
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

                $trans_ok &= $this->Partes_model->create(array(
                    'nombre' => $this->input->post('nombre_parte'),
                    'persona_id' => $persona_id), FALSE);

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
                        $datos['tags'] = 'Sistema MLC';
                        $this->Oro_model->send_data($datos);
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Partes_model->get_msg());
                    redirect('gobierno/partes/listar', 'refresh');
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
                    if ($this->Partes_model->get_error())
                    {
                        $error_msg .= $this->Partes_model->get_error();
                    }
                }
            }
        }
        else
        {
            $this->set_model_validation_rules($this->Partes_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;

                if ($_POST['persona'] === 'sin_persona')
                {
                    $persona_id = NULL;
                }
                else
                {
                    $persona_id = $this->input->post('persona');
                }

                $trans_ok &= $this->Partes_model->create(array(
                    'nombre' => $this->input->post('nombre_parte'),
                    'persona_id' => $persona_id), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Partes_model->get_msg());
                    redirect('gobierno/partes/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Partes_model->get_error())
                    {
                        $error_msg .= $this->Partes_model->get_error();
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
        $this->Partes_model->fields['persona']['array'] = $array_persona;
        $data['fields'] = $this->build_fields($this->Partes_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Parte';
        $data['title'] = TITLE . ' - Agregar Parte';
        $data['js'] = 'js/gobierno/base.js';
        $this->load_template('gobierno/partes/partes_abm', $data);
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
            redirect("gobierno/partes/ver/$id", 'refresh');
        }

        $part = $this->Partes_model->get(array('id' => $id));
        if (empty($part))
        {
            show_error('No se encontró la Parte', 500, 'Registro no encontrado');
        }

        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array(
            'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona",
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array('sin_persona' => '-- Sin Persona --', 'agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        if (!empty($_POST) && $_POST['persona'] === 'agregar')
        {
            $this->set_model_validation_rules($this->Personas_model);
            if ($this->input->post('carga_domicilio') === 'SI')
            {
                $this->set_model_validation_rules($this->Domicilios_model);
            }
            $this->set_model_validation_rules($this->Partes_model);
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

                $trans_ok &= $this->Partes_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre_parte'),
                    'persona_id' => $persona_id), FALSE);

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
                        $this->Oro_model->send_data($datos);
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Partes_model->get_msg());
                    redirect('gobierno/partes/listar', 'refresh');
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
                    if ($this->Partes_model->get_error())
                    {
                        $error_msg .= $this->Partes_model->get_error();
                    }
                }
            }
        }
        else
        {
            $this->set_model_validation_rules($this->Partes_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;

                if ($_POST['persona'] === 'sin_persona')
                {
                    $persona_id = NULL;
                }
                else
                {
                    $persona_id = $this->input->post('persona');
                }

                $trans_ok &= $this->Partes_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre_parte'),
                    'persona_id' => $persona_id), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Partes_model->get_msg());
                    redirect('gobierno/partes/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Partes_model->get_error())
                    {
                        $error_msg .= $this->Partes_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $part->nombre_parte = $part->nombre;
        if (empty($part->persona_id))
        {
            $part->persona_id = 'sin_persona';
        }

        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields);
        $this->Partes_model->fields['persona']['array'] = $array_persona;
        $data['fields'] = $this->build_fields($this->Partes_model->fields, $part);
        $data['part'] = $part;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Parte';
        $data['title'] = TITLE . ' - Editar Parte';
        $data['js'] = 'js/gobierno/base.js';
        $this->load_template('gobierno/partes/partes_abm', $data);
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
            redirect("gobierno/partes/ver/$id", 'refresh');
        }

        $part = $this->Partes_model->get_one($id);
        if (empty($part))
        {
            show_error('No se encontró la Parte', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Partes_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Partes_model->get_msg());
                redirect('gobierno/partes/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Partes_model->get_error())
                {
                    $error_msg .= $this->Partes_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        unset($this->Partes_model->fields['persona']);
        $part->nombre_parte = $part->nombre;
        $part->nombre = $part->nombre_persona;

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $part, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $part, TRUE);
        $data['fields'] = $this->build_fields($this->Partes_model->fields, $part, TRUE);
        $data['part'] = $part;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Parte';
        $data['title'] = TITLE . ' - Eliminar Parte';
        $this->load_template('gobierno/partes/partes_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $part = $this->Partes_model->get_one($id);
        if (empty($part))
        {
            show_error('No se encontró la Parte', 500, 'Registro no encontrado');
        }

        unset($this->Partes_model->fields['persona']);
        $part->nombre_parte = $part->nombre;
        $part->nombre = $part->nombre_persona;

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $part, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $part, TRUE);
        $data['fields'] = $this->build_fields($this->Partes_model->fields, $part, TRUE);
        $data['part'] = $part;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Parte';
        $data['title'] = TITLE . ' - Ver Parte';
        $this->load_template('gobierno/partes/partes_abm', $data);
    }

    public function get_parte()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->form_validation->set_rules('id', 'ID', 'required|integer|max_length[8]');

        if ($this->form_validation->run() === TRUE)
        {
            $parte = $this->Partes_model->get_one($this->input->post('id'));
            if (!empty($parte))
            {
                if (empty($parte->persona_id))
                {
                    $parte->persona_id = 'sin_persona';
                }
                $data['parte'] = $parte;
            }
            else
            {
                $data['error'] = 'Parte no encontrada';
            }
        }
        else
        {
            $data['error'] = 'Debe ingresar un ID válido';
        }

        echo json_encode($data);
    }
}
