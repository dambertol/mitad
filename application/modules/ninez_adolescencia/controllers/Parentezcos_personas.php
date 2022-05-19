<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Parentezcos_personas extends MY_Controller
{

    /**
     * Controlador de Parentezcos
     * Autor: Leandro
     * Creado: 09/09/2019
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ninez_adolescencia/Parentezcos_personas_model');
        $this->load->model('ninez_adolescencia/Tipos_parentezcos_model');
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

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Persona', 'data' => 'persona', 'width' => 35),
                array('label' => 'Tipo de Parentezco', 'data' => 'tipo_parentezco', 'width' => 21),
                array('label' => 'Pariente', 'data' => 'pariente', 'width' => 35),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'parentezcos_personas_table',
            'source_url' => 'ninez_adolescencia/parentezcos_personas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_parentezcos_personas_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Parentezcos';
        $data['title'] = TITLE . ' - Parentezcos';
        $this->load_template('ninez_adolescencia/parentezcos_personas/parentezcos_personas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("na_parentezcos_personas.id, na_tipos_parentezcos.nombre as tipo_parentezco, CONCAT(PE.apellido, ', ', PE.nombre,  ' (', PE.dni, ')') as persona, CONCAT(PA.apellido, ', ', PA.nombre,  ' (', PA.dni, ')') as pariente")
                ->from('na_parentezcos_personas')
                ->join('na_tipos_parentezcos', 'na_tipos_parentezcos.id = na_parentezcos_personas.tipo_parentezco_id', 'left')
                ->join('personas PE', 'PE.id = na_parentezcos_personas.persona_id', 'left')
                ->join('personas PA', 'PA.id = na_parentezcos_personas.pariente_id', 'left')
                ->add_column('ver', '<a href="ninez_adolescencia/parentezcos_personas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="ninez_adolescencia/parentezcos_personas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="ninez_adolescencia/parentezcos_personas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('ninez_adolescencia/parentezcos_personas/listar', 'refresh');
        }

        $this->array_tipo_parentezco_control = $array_tipo_parentezco = $this->get_array('Tipos_parentezcos', 'nombre');
        $this->array_persona_control = $this->array_pariente_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array('select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona", 'sort_by' => 'personas.apellido, personas.nombre'), array('agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $this->array_pa_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $this->array_pa_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $this->array_pa_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->array_carga_domicilio_control = $this->array_pa_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);

        $pariente_model = new stdClass();
        $pariente_model->fields = array(
            'pa_dni' => array('label' => 'DNI', 'type' => 'natural', 'minlength' => '7', 'maxlength' => '8', 'required' => TRUE),
            'pa_sexo' => array('label' => 'Sexo', 'input_type' => 'combo', 'id_name' => 'sexo', 'type' => 'bselect', 'required' => TRUE),
            'pa_cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13', 'required' => TRUE),
            'pa_nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'pa_apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
            'pa_telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'pa_celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'pa_email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
            'pa_fecha_nacimiento' => array('label' => 'Fecha Nacimiento', 'type' => 'date'),
            'pa_nacionalidad' => array('label' => 'Nacionalidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'pa_carga_domicilio' => array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'pa_carga_domicilio', 'type' => 'bselect', 'required' => TRUE)
        );

        $domicilio_pariente_model = new stdClass();
        $domicilio_pariente_model->fields = array(
            'pa_calle' => array('label' => 'Calle', 'maxlength' => '50', 'required' => TRUE),
            'pa_barrio' => array('label' => 'Barrio', 'maxlength' => '50'),
            'pa_altura' => array('label' => 'Altura', 'maxlength' => '10', 'required' => TRUE),
            'pa_piso' => array('label' => 'Piso', 'maxlength' => '10'),
            'pa_dpto' => array('label' => 'Dpto', 'maxlength' => '10'),
            'pa_manzana' => array('label' => 'Manzana', 'maxlength' => '10'),
            'pa_casa' => array('label' => 'Casa', 'maxlength' => '10'),
            'pa_localidad' => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

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

            if ($this->input->post('pariente') === 'agregar')
            {
                $this->set_model_validation_rules($pariente_model);
                if ($this->input->post('pa_carga_domicilio') === 'SI')
                {
                    $this->set_model_validation_rules($domicilio_pariente_model);
                }
            }

            $this->set_model_validation_rules($this->Parentezcos_personas_model);
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

                if ($this->input->post('pa_carga_domicilio') === 'SI')
                {
                    $trans_ok &= $this->Domicilios_model->create(array(
                        'calle' => $this->input->post('pa_calle'),
                        'barrio' => $this->input->post('pa_barrio'),
                        'altura' => $this->input->post('pa_altura'),
                        'piso' => $this->input->post('pa_piso'),
                        'dpto' => $this->input->post('pa_dpto'),
                        'manzana' => $this->input->post('pa_manzana'),
                        'casa' => $this->input->post('pa_casa'),
                        'localidad_id' => $this->input->post('pa_localidad')), FALSE);

                    $pa_domicilio_id = $this->Domicilios_model->get_row_id();
                }
                else
                {
                    $pa_domicilio_id = 'NULL';
                }

                if ($this->input->post('pariente') === 'agregar')
                {
                    $trans_ok &= $this->Personas_model->create(array(
                        'dni' => $this->input->post('pa_dni'),
                        'sexo' => $this->input->post('pa_sexo'),
                        'cuil' => $this->input->post('pa_cuil'),
                        'nombre' => $this->input->post('pa_nombre'),
                        'apellido' => $this->input->post('pa_apellido'),
                        'telefono' => $this->input->post('pa_telefono'),
                        'celular' => $this->input->post('pa_celular'),
                        'email' => $this->input->post('pa_email'),
                        'fecha_nacimiento' => $this->get_date_sql('pa_fecha_nacimiento'),
                        'nacionalidad_id' => $this->input->post('pa_nacionalidad'),
                        'domicilio_id' => $pa_domicilio_id), FALSE);

                    $pariente_id = $this->Personas_model->get_row_id();
                }
                else
                {
                    $pariente_id = $this->input->post('pariente');
                }

                $trans_ok &= $this->Parentezcos_personas_model->create(array(
                    'tipo_parentezco_id' => $this->input->post('tipo_parentezco'),
                    'persona_id' => $persona_id,
                    'pariente_id' => $pariente_id), FALSE);

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

                if (SIS_ORO_ACTIVE)
                {
                    // ORO CRM
                    if ($this->input->post('pariente') === 'agregar')
                    {
                        if ($this->db->trans_status() && $trans_ok)
                        {
                            $datos_pa['id'] = $pariente_id;
                            $datos_pa['dni'] = $this->input->post('pa_dni');
                            $datos_pa['sexo'] = $this->input->post('pa_sexo');
                            $datos_pa['cuil'] = $this->input->post('pa_cuil');
                            $datos_pa['nombre'] = $this->input->post('pa_nombre');
                            $datos_pa['apellido'] = $this->input->post('pa_apellido');
                            $datos_pa['telefono'] = $this->input->post('pa_telefono');
                            $datos_pa['celular'] = $this->input->post('pa_celular');
                            $datos_pa['email'] = $this->input->post('pa_email');
                            $datos_pa['fecha_nacimiento'] = $this->get_date_sql('pa_fecha_nacimiento');
                            $datos_pa['nacionalidad_id'] = $this->input->post('pa_nacionalidad');
                            if ($this->input->post('pa_carga_domicilio') === 'SI')
                            {
                                $datos_pa['calle'] = $this->input->post('pa_calle');
                                $datos_pa['barrio'] = $this->input->post('pa_barrio');
                                $datos_pa['altura'] = $this->input->post('pa_altura');
                                $datos_pa['piso'] = $this->input->post('pa_piso');
                                $datos_pa['dpto'] = $this->input->post('pa_dpto');
                                $datos_pa['manzana'] = $this->input->post('pa_manzana');
                                $datos_pa['casa'] = $this->input->post('pa_casa');
                                $datos_pa['localidad_id'] = $this->input->post('pa_localidad');
                            }
                            $datos_pa['tags'] = 'Sistema MLC';
                            $this->Oro_model->send_data($datos_pa);
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Parentezcos_personas_model->get_msg());
                    redirect('ninez_adolescencia/parentezcos_personas/listar', 'refresh');
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
                    if ($this->Parentezcos_personas_model->get_error())
                    {
                        $error_msg .= $this->Parentezcos_personas_model->get_error();
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

        $pariente_model->fields['pa_sexo']['array'] = $array_sexo;
        $pariente_model->fields['pa_nacionalidad']['array'] = $array_nacionalidad;
        $pariente_model->fields['pa_carga_domicilio']['array'] = $array_carga_domicilio;
        $data['fields_pariente'] = $this->build_fields($pariente_model->fields);
        $domicilio_pariente_model->fields['pa_localidad']['array'] = $array_localidad;
        $data['fields_domicilio_pariente'] = $this->build_fields($domicilio_pariente_model->fields);

        $this->Parentezcos_personas_model->fields['tipo_parentezco']['array'] = $array_tipo_parentezco;
        $this->Parentezcos_personas_model->fields['persona']['array'] = $array_persona;
        $this->Parentezcos_personas_model->fields['pariente']['array'] = $array_persona;
        $data['fields'] = $this->build_fields($this->Parentezcos_personas_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Parentezco';
        $data['title'] = TITLE . ' - Agregar Parentezco';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/parentezcos_personas/parentezcos_personas_abm', $data);
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
            redirect("ninez_adolescencia/parentezcos_personas/ver/$id", 'refresh');
        }

        $parentezcos_persona = $this->Parentezcos_personas_model->get_one($id);
        if (empty($parentezcos_persona))
        {
            show_error('No se encontró el Parentezco', 500, 'Registro no encontrado');
        }

        $this->array_tipo_parentezco_control = $array_tipo_parentezco = $this->get_array('Tipos_parentezcos', 'nombre');
        $this->array_sexo_control = $this->array_pa_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $this->array_pa_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $this->array_pa_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->array_carga_domicilio_control = $this->array_pa_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        unset($this->Parentezcos_personas_model->fields['persona']);
        unset($this->Parentezcos_personas_model->fields['pariente']);
        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);

        $pariente_model = new stdClass();
        $pariente_model->fields = array(
            'pa_dni' => array('label' => 'DNI', 'type' => 'natural', 'minlength' => '7', 'maxlength' => '8', 'required' => TRUE),
            'pa_sexo' => array('label' => 'Sexo', 'input_type' => 'combo', 'id_name' => 'sexo', 'type' => 'bselect', 'required' => TRUE),
            'pa_cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13', 'required' => TRUE),
            'pa_nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'pa_apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
            'pa_telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'pa_celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'pa_email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100'),
            'pa_fecha_nacimiento' => array('label' => 'Fecha Nacimiento', 'type' => 'date'),
            'pa_nacionalidad' => array('label' => 'Nacionalidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'pa_carga_domicilio' => array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'pa_carga_domicilio', 'type' => 'bselect', 'required' => TRUE)
        );

        $domicilio_pariente_model = new stdClass();
        $domicilio_pariente_model->fields = array(
            'pa_calle' => array('label' => 'Calle', 'maxlength' => '50', 'required' => TRUE),
            'pa_barrio' => array('label' => 'Barrio', 'maxlength' => '50'),
            'pa_altura' => array('label' => 'Altura', 'maxlength' => '10', 'required' => TRUE),
            'pa_piso' => array('label' => 'Piso', 'maxlength' => '10'),
            'pa_dpto' => array('label' => 'Dpto', 'maxlength' => '10'),
            'pa_manzana' => array('label' => 'Manzana', 'maxlength' => '10'),
            'pa_casa' => array('label' => 'Casa', 'maxlength' => '10'),
            'pa_localidad' => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

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

            if ($this->input->post('pa_carga_domicilio') === 'SI')
            {
                $this->set_model_validation_rules($domicilio_pariente_model);
            }

            $this->set_model_validation_rules($this->Parentezcos_personas_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                if ($this->input->post('carga_domicilio') === 'SI')
                {
                    if (!empty($parentezcos_persona->domicilio_id))
                    {
                        $trans_ok &= $this->Domicilios_model->update(array(
                            'id' => $parentezcos_persona->domicilio_id,
                            'calle' => $this->input->post('calle'),
                            'barrio' => $this->input->post('barrio'),
                            'altura' => $this->input->post('altura'),
                            'piso' => $this->input->post('piso'),
                            'dpto' => $this->input->post('dpto'),
                            'manzana' => $this->input->post('manzana'),
                            'casa' => $this->input->post('casa'),
                            'localidad_id' => $this->input->post('localidad')), FALSE);

                        $domicilio_id = $parentezcos_persona->domicilio_id;
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
                    'id' => $parentezcos_persona->persona_id,
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


                if ($this->input->post('pa_carga_domicilio') === 'SI')
                {
                    if (!empty($parentezcos_persona->pa_domicilio_id))
                    {
                        $trans_ok &= $this->Domicilios_model->update(array(
                            'id' => $parentezcos_persona->pa_domicilio_id,
                            'calle' => $this->input->post('pa_calle'),
                            'barrio' => $this->input->post('pa_barrio'),
                            'altura' => $this->input->post('pa_altura'),
                            'piso' => $this->input->post('pa_piso'),
                            'dpto' => $this->input->post('pa_dpto'),
                            'manzana' => $this->input->post('pa_manzana'),
                            'casa' => $this->input->post('pa_casa'),
                            'localidad_id' => $this->input->post('pa_localidad')), FALSE);

                        $pa_domicilio_id = $parentezcos_persona->pa_domicilio_id;
                    }
                    else
                    {
                        $trans_ok &= $this->Domicilios_model->create(array(
                            'calle' => $this->input->post('pa_calle'),
                            'barrio' => $this->input->post('pa_barrio'),
                            'altura' => $this->input->post('pa_altura'),
                            'piso' => $this->input->post('pa_piso'),
                            'dpto' => $this->input->post('pa_dpto'),
                            'manzana' => $this->input->post('pa_manzana'),
                            'casa' => $this->input->post('pa_casa'),
                            'localidad_id' => $this->input->post('pa_localidad')), FALSE);

                        $pa_domicilio_id = $this->Domicilios_model->get_row_id();
                    }
                }
                else
                {
                    $pa_domicilio_id = 'NULL';
                }

                $trans_ok &= $this->Personas_model->update(array(
                    'id' => $parentezcos_persona->pariente_id,
                    'dni' => $this->input->post('pa_dni'),
                    'sexo' => $this->input->post('pa_sexo'),
                    'cuil' => $this->input->post('pa_cuil'),
                    'nombre' => $this->input->post('pa_nombre'),
                    'apellido' => $this->input->post('pa_apellido'),
                    'telefono' => $this->input->post('pa_telefono'),
                    'celular' => $this->input->post('pa_celular'),
                    'email' => $this->input->post('pa_email'),
                    'fecha_nacimiento' => $this->get_date_sql('pa_fecha_nacimiento'),
                    'nacionalidad_id' => $this->input->post('pa_nacionalidad'),
                    'domicilio_id' => $pa_domicilio_id), FALSE);

                $trans_ok &= $this->Parentezcos_personas_model->update(array(
                    'id' => $this->input->post('id'),
                    'tipo_parentezco_id' => $this->input->post('tipo_parentezco')), FALSE);

                $user_id = $this->Personas_model->get_user_id($parentezcos_persona->persona_id);
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

                $pa_user_id = $this->Personas_model->get_user_id($parentezcos_persona->pariente_id);
                if ($pa_user_id !== 0) //Persona con usuario
                {
                    if (SIS_AUTH_MODE === 'auth0')
                    {
                        // AUTH0
                        if ($this->db->trans_status() && $trans_ok)
                        {
                            $data['nombre'] = $this->input->post('pa_nombre');
                            $data['apellido'] = $this->input->post('pa_apellido');
                            $data['email'] = $this->input->post('pa_email');
                            $trans_ok = $this->Auth0_model->update_user($pa_user_id, $data);
                        }
                    }
                }

                if (SIS_ORO_ACTIVE)
                {
                    // ORO CRM
                    if ($this->input->post('persona') === 'agregar')
                    {
                        if ($this->db->trans_status() && $trans_ok)
                        {
                            $datos['id'] = $parentezcos_persona->persona_id;
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
                }

                if (SIS_ORO_ACTIVE)
                {
                    // ORO CRM
                    if ($this->input->post('pariente') === 'agregar')
                    {
                        if ($this->db->trans_status() && $trans_ok)
                        {
                            $datos_pa['id'] = $parentezcos_persona->pariente_id;
                            $datos_pa['dni'] = $this->input->post('pa_dni');
                            $datos_pa['sexo'] = $this->input->post('pa_sexo');
                            $datos_pa['cuil'] = $this->input->post('pa_cuil');
                            $datos_pa['nombre'] = $this->input->post('pa_nombre');
                            $datos_pa['apellido'] = $this->input->post('pa_apellido');
                            $datos_pa['telefono'] = $this->input->post('pa_telefono');
                            $datos_pa['celular'] = $this->input->post('pa_celular');
                            $datos_pa['email'] = $this->input->post('pa_email');
                            $datos_pa['fecha_nacimiento'] = $this->get_date_sql('pa_fecha_nacimiento');
                            $datos_pa['nacionalidad_id'] = $this->input->post('pa_nacionalidad');
                            if ($this->input->post('pa_carga_domicilio') === 'SI')
                            {
                                $datos_pa['calle'] = $this->input->post('pa_calle');
                                $datos_pa['barrio'] = $this->input->post('pa_barrio');
                                $datos_pa['altura'] = $this->input->post('pa_altura');
                                $datos_pa['piso'] = $this->input->post('pa_piso');
                                $datos_pa['dpto'] = $this->input->post('pa_dpto');
                                $datos_pa['manzana'] = $this->input->post('pa_manzana');
                                $datos_pa['casa'] = $this->input->post('pa_casa');
                                $datos_pa['localidad_id'] = $this->input->post('pa_localidad');
                            }
                            $this->Oro_model->send_data($datos_pa);
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Parentezcos_personas_model->get_msg());
                    redirect('ninez_adolescencia/parentezcos_personas/listar', 'refresh');
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
                    if ($this->Parentezcos_personas_model->get_error())
                    {
                        $error_msg .= $this->Parentezcos_personas_model->get_error();
                    }
                    if ($this->Auth0_model->errors())
                    {
                        $error_msg .= $this->Auth0_model->errors();
                    }
                }
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Parentezcos_personas_model->update(array(
                    'id' => $this->input->post('id'),
                    'tipo_parentezco_id' => $this->input->post('tipo_parentezco'),
                    'persona_id' => $this->input->post('persona'),
                    'pariente_id' => $this->input->post('pariente')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Parentezcos_personas_model->get_msg());
                    redirect('ninez_adolescencia/parentezcos_personas/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Parentezcos_personas_model->get_error())
                    {
                        $error_msg .= $this->Parentezcos_personas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
        if (!empty($parentezcos_persona->domicilio_id))
        {
            $parentezcos_persona->carga_domicilio = 'SI';
        }
        else
        {
            $parentezcos_persona->carga_domicilio = 'NO';
        }
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $parentezcos_persona);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $parentezcos_persona);

        $pariente_model->fields['pa_sexo']['array'] = $array_sexo;
        $pariente_model->fields['pa_nacionalidad']['array'] = $array_nacionalidad;
        $pariente_model->fields['pa_carga_domicilio']['array'] = $array_carga_domicilio;
        if (!empty($parentezcos_persona->pa_domicilio_id))
        {
            $parentezcos_persona->pa_carga_domicilio = 'SI';
        }
        else
        {
            $parentezcos_persona->pa_carga_domicilio = 'NO';
        }
        $data['fields_pariente'] = $this->build_fields($pariente_model->fields, $parentezcos_persona);
        $domicilio_pariente_model->fields['pa_localidad']['array'] = $array_localidad;
        $data['fields_domicilio_pariente'] = $this->build_fields($domicilio_pariente_model->fields, $parentezcos_persona);

        $this->Parentezcos_personas_model->fields['tipo_parentezco']['array'] = $array_tipo_parentezco;
        $data['fields'] = $this->build_fields($this->Parentezcos_personas_model->fields, $parentezcos_persona);
        $data['parentezcos_persona'] = $parentezcos_persona;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Parentezco';
        $data['title'] = TITLE . ' - Editar Parentezco';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/parentezcos_personas/parentezcos_personas_abm', $data);
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
            redirect("ninez_adolescencia/parentezcos_personas/ver/$id", 'refresh');
        }

        $parentezcos_persona = $this->Parentezcos_personas_model->get_one($id);
        if (empty($parentezcos_persona))
        {
            show_error('No se encontró el Parentezco', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Parentezcos_personas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Parentezcos_personas_model->get_msg());
                redirect('ninez_adolescencia/parentezcos_personas/listar', 'refresh');
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
                if ($this->Parentezcos_personas_model->get_error())
                {
                    $error_msg .= $this->Parentezcos_personas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        unset($this->Parentezcos_personas_model->fields['persona']);
        unset($this->Parentezcos_personas_model->fields['pariente']);

        $pariente_model = new stdClass();
        $pariente_model->fields = array(
            'pa_dni' => array('label' => 'DNI', 'type' => 'natural', 'minlength' => '7', 'maxlength' => '8', 'required' => TRUE),
            'pa_sexo' => array('label' => 'Sexo', 'input_type' => 'combo', 'id_name' => 'sexo', 'type' => 'bselect', 'required' => TRUE),
            'pa_cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13', 'required' => TRUE),
            'pa_nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'pa_apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
            'pa_telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'pa_celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'pa_email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
            'pa_fecha_nacimiento' => array('label' => 'Fecha Nacimiento', 'type' => 'date'),
            'pa_nacionalidad' => array('label' => 'Nacionalidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $domicilio_pariente_model = new stdClass();
        $domicilio_pariente_model->fields = array(
            'pa_calle' => array('label' => 'Calle', 'maxlength' => '50', 'required' => TRUE),
            'pa_barrio' => array('label' => 'Barrio', 'maxlength' => '50'),
            'pa_altura' => array('label' => 'Altura', 'maxlength' => '10', 'required' => TRUE),
            'pa_piso' => array('label' => 'Piso', 'maxlength' => '10'),
            'pa_dpto' => array('label' => 'Dpto', 'maxlength' => '10'),
            'pa_manzana' => array('label' => 'Manzana', 'maxlength' => '10'),
            'pa_casa' => array('label' => 'Casa', 'maxlength' => '10'),
            'pa_localidad' => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        if (!empty($parentezcos_persona->domicilio_id))
        {
            $parentezcos_persona->carga_domicilio = 'SI';
        }
        else
        {
            $parentezcos_persona->carga_domicilio = 'NO';
        }

        if (!empty($parentezcos_persona->na_domicilio_id))
        {
            $parentezcos_persona->na_carga_domicilio = 'SI';
        }
        else
        {
            $parentezcos_persona->na_carga_domicilio = 'NO';
        }

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $parentezcos_persona, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $parentezcos_persona, TRUE);
        $data['fields_pariente'] = $this->build_fields($pariente_model->fields, $parentezcos_persona, TRUE);
        $data['fields_domicilio_pariente'] = $this->build_fields($domicilio_pariente_model->fields, $parentezcos_persona, TRUE);
        $data['fields'] = $this->build_fields($this->Parentezcos_personas_model->fields, $parentezcos_persona, TRUE);
        $data['parentezcos_persona'] = $parentezcos_persona;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Parentezco';
        $data['title'] = TITLE . ' - Eliminar Parentezco';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/parentezcos_personas/parentezcos_personas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $parentezcos_persona = $this->Parentezcos_personas_model->get_one($id);
        if (empty($parentezcos_persona))
        {
            show_error('No se encontró el Parentezco', 500, 'Registro no encontrado');
        }

        unset($this->Parentezcos_personas_model->fields['persona']);
        unset($this->Parentezcos_personas_model->fields['pariente']);

        $pariente_model = new stdClass();
        $pariente_model->fields = array(
            'pa_dni' => array('label' => 'DNI', 'type' => 'natural', 'minlength' => '7', 'maxlength' => '8', 'required' => TRUE),
            'pa_sexo' => array('label' => 'Sexo', 'input_type' => 'combo', 'id_name' => 'sexo', 'type' => 'bselect', 'required' => TRUE),
            'pa_cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13', 'required' => TRUE),
            'pa_nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'pa_apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
            'pa_telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'pa_celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'pa_email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
            'pa_fecha_nacimiento' => array('label' => 'Fecha Nacimiento', 'type' => 'date'),
            'pa_nacionalidad' => array('label' => 'Nacionalidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $domicilio_pariente_model = new stdClass();
        $domicilio_pariente_model->fields = array(
            'pa_calle' => array('label' => 'Calle', 'maxlength' => '50', 'required' => TRUE),
            'pa_barrio' => array('label' => 'Barrio', 'maxlength' => '50'),
            'pa_altura' => array('label' => 'Altura', 'maxlength' => '10', 'required' => TRUE),
            'pa_piso' => array('label' => 'Piso', 'maxlength' => '10'),
            'pa_dpto' => array('label' => 'Dpto', 'maxlength' => '10'),
            'pa_manzana' => array('label' => 'Manzana', 'maxlength' => '10'),
            'pa_casa' => array('label' => 'Casa', 'maxlength' => '10'),
            'pa_localidad' => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        if (!empty($parentezcos_persona->domicilio_id))
        {
            $parentezcos_persona->carga_domicilio = 'SI';
        }
        else
        {
            $parentezcos_persona->carga_domicilio = 'NO';
        }

        if (!empty($parentezcos_persona->na_domicilio_id))
        {
            $parentezcos_persona->na_carga_domicilio = 'SI';
        }
        else
        {
            $parentezcos_persona->na_carga_domicilio = 'NO';
        }

        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $parentezcos_persona, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $parentezcos_persona, TRUE);
        $data['fields_pariente'] = $this->build_fields($pariente_model->fields, $parentezcos_persona, TRUE);
        $data['fields_domicilio_pariente'] = $this->build_fields($domicilio_pariente_model->fields, $parentezcos_persona, TRUE);
        $data['fields'] = $this->build_fields($this->Parentezcos_personas_model->fields, $parentezcos_persona, TRUE);
        $data['parentezcos_persona'] = $parentezcos_persona;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Parentezco';
        $data['title'] = TITLE . ' - Ver Parentezco';
        $data['js'] = 'js/ninez_adolescencia/base.js';
        $this->load_template('ninez_adolescencia/parentezcos_personas/parentezcos_personas_abm', $data);
    }
}
