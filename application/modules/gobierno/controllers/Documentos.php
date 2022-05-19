<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Documentos extends MY_Controller
{

    /**
     * Controlador de Documentos
     * Autor: Leandro
     * Creado: 08/01/2020
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('gobierno/Adjuntos_model');
        $this->load->model('gobierno/Documentos_model');
        $this->load->model('gobierno/Tipos_documentos_model');
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
                array('label' => 'Tipo', 'data' => 'tipo_documento', 'width' => 7),
                array('label' => 'Número', 'data' => 'numero', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Ejercicio', 'data' => 'ejercicio', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Título', 'data' => 'titulo', 'width' => 30),
                array('label' => 'Fecha', 'data' => 'fecha', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Expt N°', 'data' => 'expt_numero', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Expt Ej', 'data' => 'expt_ejercicio', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Expt Mat', 'data' => 'expt_matricula', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Parte', 'data' => 'parte', 'width' => 11),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'documentos_table',
            'source_url' => 'gobierno/documentos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_documentos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_documentos', 'nombre', 'id', array('where' => array('id <> 1')), array('' => 'Todos')); //HC: 1 Decreto
        $data['array_estados'] = array('' => 'Todos', 'Activo' => 'Activo', 'Anulado' => 'Anulado');
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Documentos';
        $data['title'] = TITLE . ' - Documentos';
        $this->load_template('gobierno/documentos/documentos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('gobierno/datatables_functions_helper');
        $this->datatables
                ->select('go_documentos.id, go_tipos_documentos.nombre as tipo_documento, go_documentos.numero, go_documentos.ejercicio, go_documentos.titulo, go_documentos.fecha, go_documentos.expt_ejercicio, go_documentos.expt_numero, go_documentos.expt_matricula, go_partes.nombre as parte, go_documentos.estado as estadodoc')
                ->from('go_documentos')
                ->join('go_tipos_documentos', 'go_tipos_documentos.id = go_documentos.tipo_documento_id', 'left')
                ->join('go_partes', 'go_partes.id = go_documentos.parte_id', 'left')
                ->where('go_documentos.tipo_documento_id <>', 1) //HC: 1 Decreto
                ->add_column('ver', '<a href="gobierno/documentos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_documentos_editar(estadodoc, id, "editar")')
                ->add_column('eliminar', '$1', 'dt_column_documentos_eliminar(estadodoc, id, "eliminar")');

        echo $this->datatables->generate();
    }

    public function listar_decretos()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Número', 'data' => 'numero', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Ejercicio', 'data' => 'ejercicio', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Título', 'data' => 'titulo', 'width' => 41),
                array('label' => 'Fecha', 'data' => 'fecha', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Expt N°', 'data' => 'expt_numero', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Expt Ej', 'data' => 'expt_ejercicio', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Expt Mat', 'data' => 'expt_matricula', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'documentos_table',
            'source_url' => 'gobierno/documentos/listar_decretos_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_documentos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_estados'] = array('' => 'Todos', 'Activo' => 'Activo', 'Anulado' => 'Anulado');
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Decretos';
        $data['title'] = TITLE . ' - Decretos';
        $this->load_template('gobierno/documentos/documentos_decreto_listar', $data);
    }

    public function listar_decretos_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('gobierno/datatables_functions_helper');
        $this->datatables
                ->select('go_documentos.id, go_documentos.numero, go_documentos.ejercicio, go_documentos.titulo, go_documentos.fecha, go_documentos.expt_ejercicio, go_documentos.expt_numero, go_documentos.expt_matricula, go_documentos.estado as estadodoc')
                ->from('go_documentos')
                ->where('go_documentos.tipo_documento_id', 1) //HC: 1 Decreto
                ->add_column('ver', '<a href="gobierno/documentos/ver_decreto/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_documentos_editar(estadodoc, id, "editar_decreto")')
                ->add_column('eliminar', '$1', 'dt_column_documentos_eliminar(estadodoc, id, "eliminar_decreto")');

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
            redirect('gobierno/documentos/listar', 'refresh');
        }

        $this->array_tipo_documento_control = $array_tipo_documento = $this->get_array('Tipos_documentos', 'nombre', 'id', array('where' => array('id <> 1'))); //HC: 1 Decreto
        $this->array_parte_control = $array_parte = $this->get_array('Partes', 'nombre', 'id', array(), array('agregar' => '-- Agregar Parte --'));
        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array(
            'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona",
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array('sin_persona' => '-- Sin Persona --', 'agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        if ($this->input->post('parte') === 'agregar')
        {
            $this->set_model_validation_rules($this->Partes_model);

            if ($this->input->post('persona') === 'agregar')
            {
                $this->set_model_validation_rules($this->Personas_model);
                if ($this->input->post('carga_domicilio') === 'SI')
                {
                    $this->set_model_validation_rules($this->Domicilios_model);
                }
                $this->set_model_validation_rules($this->Documentos_model);
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

                    $parte_id = $this->Partes_model->get_row_id();

                    $trans_ok &= $this->Documentos_model->create(array(
                        'fecha' => $this->get_date_sql('fecha'),
                        'expt_ejercicio' => $this->input->post('expt_ejercicio'),
                        'expt_numero' => $this->input->post('expt_numero'),
                        'expt_matricula' => $this->input->post('expt_matricula'),
                        'titulo' => $this->input->post('titulo'),
                        'tipo_documento_id' => $this->input->post('tipo_documento'),
                        'parte_id' => $parte_id,
                        'ejercicio' => $this->input->post('ejercicio'),
                        'numero' => $this->input->post('numero'),
                        'texto' => $this->input->post('texto'),
                        'usuario_carga' => $this->session->userdata('user_id'),
                        'fecha_carga' => (new DateTime())->format('Y-m-d'),
                        'estado' => 'Activo'), FALSE);

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
                        redirect('gobierno/documentos/listar', 'refresh');
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
                        if ($this->Documentos_model->get_error())
                        {
                            $error_msg .= $this->Documentos_model->get_error();
                        }
                    }
                }
            }
            else
            {
                $this->set_model_validation_rules($this->Documentos_model);
                $error_msg = FALSE;
                if ($this->form_validation->run() === TRUE)
                {
                    $this->db->trans_begin();
                    $trans_ok = TRUE;

                    if ($this->input->post('persona') === 'sin_persona')
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

                    $parte_id = $this->Partes_model->get_row_id();

                    $trans_ok &= $this->Documentos_model->create(array(
                        'fecha' => $this->get_date_sql('fecha'),
                        'expt_ejercicio' => $this->input->post('expt_ejercicio'),
                        'expt_numero' => $this->input->post('expt_numero'),
                        'expt_matricula' => $this->input->post('expt_matricula'),
                        'titulo' => $this->input->post('titulo'),
                        'tipo_documento_id' => $this->input->post('tipo_documento'),
                        'parte_id' => $parte_id,
                        'ejercicio' => $this->input->post('ejercicio'),
                        'numero' => $this->input->post('numero'),
                        'texto' => $this->input->post('texto'),
                        'usuario_carga' => $this->session->userdata('user_id'),
                        'fecha_carga' => (new DateTime())->format('Y-m-d'),
                        'estado' => 'Activo'), FALSE);

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
                        if ($this->Documentos_model->get_error())
                        {
                            $error_msg .= $this->Documentos_model->get_error();
                        }
                    }
                }
            }
        }
        else
        {
            $this->set_model_validation_rules($this->Documentos_model);
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Documentos_model->create(array(
                    'fecha' => $this->get_date_sql('fecha'),
                    'expt_ejercicio' => $this->input->post('expt_ejercicio'),
                    'expt_numero' => $this->input->post('expt_numero'),
                    'expt_matricula' => $this->input->post('expt_matricula'),
                    'titulo' => $this->input->post('titulo'),
                    'tipo_documento_id' => $this->input->post('tipo_documento'),
                    'parte_id' => $this->input->post('parte'),
                    'ejercicio' => $this->input->post('ejercicio'),
                    'numero' => $this->input->post('numero'),
                    'texto' => $this->input->post('texto'),
                    'usuario_carga' => $this->session->userdata('user_id'),
                    'fecha_carga' => (new DateTime())->format('Y-m-d'),
                    'estado' => 'Activo'), FALSE);

                $documento_id = $this->Documentos_model->get_row_id();

                $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
                if (!empty($adjuntos_agregar_post))
                {
                    foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->documento_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/gobierno/documentos/" . str_pad($documento_id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'documento_id' => $documento_id
                                        ), FALSE);
                                $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                                if (!$renombrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                            else
                            {
                                $trans_ok = FALSE;
                                $error_msg = '<br />Se ha producido un error con los adjuntos.';
                            }
                        }
                        else
                        {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Se ha producido un error con los adjuntos.';
                        }
                    }
                }

                $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
                if (!empty($adjuntos_eliminar_post))
                {
                    foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->documento_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                                $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                                if (!$borrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Documentos_model->get_msg());
                    redirect('gobierno/documentos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Documentos_model->get_error())
                    {
                        $error_msg .= $this->Documentos_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->load->model('gobierno/Adjuntos_model');
        $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
        if (!empty($adjuntos_agregar_post))
        {
            $adjuntos_agregar_id = array();
            foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_agregar_id[] = $Adjunto_id;
            }

            $adjuntos_agregar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_agregar = array();
            if (!empty($adjuntos_agregar))
            {
                foreach ($adjuntos_agregar as $Adjunto)
                {
                    $array_adjuntos_agregar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_agregar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_agregar'] = $array_adjuntos_agregar;
        }

        $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
        if (!empty($adjuntos_eliminar_post))
        {
            $adjuntos_eliminar_id = array();
            foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_eliminar_id[] = $Adjunto_id;
            }

            $adjuntos_eliminar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_eliminar = array();
            if (!empty($adjuntos_eliminar))
            {
                foreach ($adjuntos_eliminar as $Adjunto)
                {
                    $array_adjuntos_eliminar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_eliminar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_eliminar'] = $array_adjuntos_eliminar;
        }

        $data['adjuntos_eliminar_existente_post'] = array();

        $this->Documentos_model->fields['tipo_documento']['array'] = $array_tipo_documento;
        $this->Documentos_model->fields['parte']['array'] = $array_parte;
        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields);
        $this->Partes_model->fields['persona']['array'] = $array_persona;
        $data['fields_parte'] = $this->build_fields($this->Partes_model->fields);
        $data['fields'] = $this->build_fields($this->Documentos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['back_url'] = 'listar';
        $data['title_view'] = 'Agregar Documento';
        $data['title'] = TITLE . ' - Agregar Documento';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $data['js'][] = 'js/gobierno/base.js';
        $this->load_template('gobierno/documentos/documentos_abm', $data);
    }

    public function agregar_decreto()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('gobierno/documentos/listar', 'refresh');
        }

        unset($this->Documentos_model->fields['tipo_documento']);
        unset($this->Documentos_model->fields['parte']);
        $this->set_model_validation_rules($this->Documentos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Documentos_model->create(array(
                'fecha' => $this->get_date_sql('fecha'),
                'expt_ejercicio' => $this->input->post('expt_ejercicio'),
                'expt_numero' => $this->input->post('expt_numero'),
                'expt_matricula' => $this->input->post('expt_matricula'),
                'titulo' => $this->input->post('titulo'),
                'tipo_documento_id' => 1, //HC: 1 Decreto
                'ejercicio' => $this->input->post('ejercicio'),
                'numero' => $this->input->post('numero'),
                'texto' => $this->input->post('texto'),
                'usuario_carga' => $this->session->userdata('user_id'),
                'fecha_carga' => (new DateTime())->format('Y-m-d'),
                'estado' => 'Activo'), FALSE);

            $documento_id = $this->Documentos_model->get_row_id();

            $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
            if (!empty($adjuntos_agregar_post))
            {
                foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));

                    if (!empty($adjunto) && empty($adjunto->documento_id))
                    {
                        $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                        if (file_exists($viejo_archivo))
                        {
                            $nueva_ruta = "uploads/gobierno/documentos/" . str_pad($documento_id, 6, "0", STR_PAD_LEFT) . "/";
                            if (!file_exists($nueva_ruta))
                            {
                                mkdir($nueva_ruta, 0755, TRUE);
                            }
                            $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                            $trans_ok &= $this->Adjuntos_model->update(array(
                                'id' => $Adjunto_id,
                                'nombre' => $nuevo_nombre,
                                'ruta' => $nueva_ruta,
                                'documento_id' => $documento_id
                                    ), FALSE);
                            $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                            if (!$renombrado)
                            {
                                $trans_ok = FALSE;
                            }
                        }
                        else
                        {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Se ha producido un error con los adjuntos.';
                        }
                    }
                    else
                    {
                        $trans_ok = FALSE;
                        $error_msg = '<br />Se ha producido un error con los adjuntos.';
                    }
                }
            }

            $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
            if (!empty($adjuntos_eliminar_post))
            {
                foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));

                    if (!empty($adjunto) && empty($adjunto->documento_id))
                    {
                        $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                        if (file_exists($viejo_archivo))
                        {
                            $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                            $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                            if (!$borrado)
                            {
                                $trans_ok = FALSE;
                            }
                        }
                    }
                }
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Documentos_model->get_msg());
                redirect('gobierno/documentos/listar_decretos', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Documentos_model->get_error())
                {
                    $error_msg .= $this->Documentos_model->get_error();
                }
                if ($this->Adjuntos_model->get_error())
                {
                    $error_msg .= $this->Adjuntos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->load->model('gobierno/Adjuntos_model');
        $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
        if (!empty($adjuntos_agregar_post))
        {
            $adjuntos_agregar_id = array();
            foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_agregar_id[] = $Adjunto_id;
            }

            $adjuntos_agregar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_agregar = array();
            if (!empty($adjuntos_agregar))
            {
                foreach ($adjuntos_agregar as $Adjunto)
                {
                    $array_adjuntos_agregar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_agregar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_agregar'] = $array_adjuntos_agregar;
        }

        $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
        if (!empty($adjuntos_eliminar_post))
        {
            $adjuntos_eliminar_id = array();
            foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_eliminar_id[] = $Adjunto_id;
            }

            $adjuntos_eliminar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_eliminar = array();
            if (!empty($adjuntos_eliminar))
            {
                foreach ($adjuntos_eliminar as $Adjunto)
                {
                    $array_adjuntos_eliminar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_eliminar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_eliminar'] = $array_adjuntos_eliminar;
        }

        $data['adjuntos_eliminar_existente_post'] = array();

        $data['fields'] = $this->build_fields($this->Documentos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['back_url'] = 'listar_decretos';
        $data['title_view'] = 'Agregar Decreto';
        $data['title'] = TITLE . ' - Agregar Decreto';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('gobierno/documentos/documentos_decreto_abm', $data);
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
            redirect("gobierno/documentos/ver/$id", 'refresh');
        }

        $this->array_tipo_documento_control = $array_tipo_documento = $this->get_array('Tipos_documentos', 'nombre', 'id', array('where' => array('id <> 1')), array('' => '')); //HC: 1 Decreto
        $this->array_parte_control = $array_parte = $this->get_array('Partes', 'nombre');
        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array(
            'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona",
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array('sin_persona' => '-- Sin Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
        $this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
        $this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

        $documento = $this->Documentos_model->get_one($id);
        if (empty($documento) || $documento->tipo_documento_id === '1') //HC: 1 Decreto
        {
            show_error('No se encontró el Documento', 500, 'Registro no encontrado');
        }

        $adjuntos = $this->Adjuntos_model->get(array(
            'documento_id' => $id,
            'join' => array(
                array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo_documento' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'titulo' => array('label' => 'Título', 'maxlength' => '255', 'required' => TRUE),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5'),
            'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4'),
            'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1'),
            'texto' => array('label' => 'Texto *', 'form_type' => 'textarea', 'rows' => 5),
            'parte' => array('label' => 'Parte', 'input_type' => 'combo', 'type' => 'bselect')
        );

        $this->set_model_validation_rules($fake_model);
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
                $trans_ok &= $this->Documentos_model->update(array(
                    'id' => $this->input->post('id'),
                    'tipo_documento_id' => $documento->tipo_documento_id,
                    'numero' => $documento->numero,
                    'ejercicio' => $documento->ejercicio,
                    'fecha' => $this->get_date_sql('fecha'),
                    'expt_ejercicio' => $this->input->post('expt_ejercicio'),
                    'expt_numero' => $this->input->post('expt_numero'),
                    'expt_matricula' => $this->input->post('expt_matricula'),
                    'titulo' => $this->input->post('titulo'),
                    'parte_id' => $this->input->post('parte'),
                    'texto' => $this->input->post('texto')), FALSE);

                $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
                if (!empty($adjuntos_agregar_post))
                {
                    foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->documento_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/gobierno/documentos/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'documento_id' => $id
                                        ), FALSE);
                                $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                                if (!$renombrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                            else
                            {
                                $trans_ok = FALSE;
                                $error_msg = '<br />Se ha producido un error con los adjuntos.';
                            }
                        }
                        else
                        {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Se ha producido un error con los adjuntos.';
                        }
                    }
                }

                $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
                if (!empty($adjuntos_eliminar_post))
                {
                    foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->documento_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                                $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                                if (!$borrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                        }
                    }
                }

                $adjuntos_eliminar_existente_post = $this->input->post('adjunto_eliminar_existente');
                if (!empty($adjuntos_eliminar_existente_post))
                {
                    foreach ($adjuntos_eliminar_existente_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'documento_id' => $this->input->post('id')
                        ));

                        if (!empty($adjunto))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                                $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                                if (!$borrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Documentos_model->get_msg());
                    redirect('gobierno/documentos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Documentos_model->get_error())
                    {
                        $error_msg .= $this->Documentos_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->name = pathinfo($Adjunto->nombre)['filename'];
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
        if (!empty($adjuntos_agregar_post))
        {
            $adjuntos_agregar_id = array();
            foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_agregar_id[] = $Adjunto_id;
            }

            $adjuntos_agregar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_agregar = array();
            if (!empty($adjuntos_agregar))
            {
                foreach ($adjuntos_agregar as $Adjunto)
                {
                    $array_adjuntos_agregar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_agregar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_agregar'] = $array_adjuntos_agregar;
        }

        $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
        if (!empty($adjuntos_eliminar_post))
        {
            $adjuntos_eliminar_id = array();
            foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_eliminar_id[] = $Adjunto_id;
            }

            $adjuntos_eliminar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_eliminar = array();
            if (!empty($adjuntos_eliminar))
            {
                foreach ($adjuntos_eliminar as $Adjunto)
                {
                    $array_adjuntos_eliminar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_eliminar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_eliminar'] = $array_adjuntos_eliminar;
        }

        if ($this->input->post('adjunto_eliminar_existente'))
        {
            $data['adjuntos_eliminar_existente_post'] = $this->input->post('adjunto_eliminar_existente');
        }
        else
        {
            $data['adjuntos_eliminar_existente_post'] = array();
        }

        $this->Partes_model->fields['persona']['array'] = $array_persona;
        $data['fields_parte'] = $this->build_fields($this->Partes_model->fields, $documento);
        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
        if (!empty($documento->domicilio_id))
        {
            $documento->carga_domicilio = 'SI';
        }
        else
        {
            $documento->carga_domicilio = 'NO';
        }
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $documento);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $documento);
        $fake_model->fields['tipo_documento']['array'] = $array_tipo_documento;
        $fake_model->fields['parte']['array'] = $array_parte;
        $data['fields'] = $this->build_fields($fake_model->fields, $documento);
        $data['documento'] = $documento;
        $data['txt_btn'] = 'Editar';
        $data['back_url'] = 'listar';
        $data['title_view'] = 'Editar Documento';
        $data['title'] = TITLE . ' - Editar Documento';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $data['js'][] = 'js/gobierno/base.js';
        $this->load_template('gobierno/documentos/documentos_abm', $data);
    }

    public function editar_decreto($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("gobierno/documentos/ver/$id", 'refresh');
        }

        $documento = $this->Documentos_model->get(array('id' => $id));
        if (empty($documento) || $documento->tipo_documento_id !== '1') //HC: 1 Decreto
        {
            show_error('No se encontró el Decreto', 500, 'Registro no encontrado');
        }

        $adjuntos = $this->Adjuntos_model->get(array(
            'documento_id' => $id,
            'join' => array(
                array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'titulo' => array('label' => 'Título', 'maxlength' => '255', 'required' => TRUE),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5'),
            'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4'),
            'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1'),
            'texto' => array('label' => 'Texto *', 'form_type' => 'textarea', 'rows' => 5)
        );

        $this->set_model_validation_rules($fake_model);
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
                $trans_ok &= $this->Documentos_model->update(array(
                    'id' => $this->input->post('id'),
                    'tipo_documento_id' => $documento->tipo_documento_id,
                    'numero' => $documento->numero,
                    'ejercicio' => $documento->ejercicio,
                    'fecha' => $this->get_date_sql('fecha'),
                    'expt_ejercicio' => $this->input->post('expt_ejercicio'),
                    'expt_numero' => $this->input->post('expt_numero'),
                    'expt_matricula' => $this->input->post('expt_matricula'),
                    'titulo' => $this->input->post('titulo'),
                    'texto' => $this->input->post('texto')), FALSE);

                $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
                if (!empty($adjuntos_agregar_post))
                {
                    foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->documento_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/gobierno/documentos/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'documento_id' => $id
                                        ), FALSE);
                                $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                                if (!$renombrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                            else
                            {
                                $trans_ok = FALSE;
                                $error_msg = '<br />Se ha producido un error con los adjuntos.';
                            }
                        }
                        else
                        {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Se ha producido un error con los adjuntos.';
                        }
                    }
                }

                $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
                if (!empty($adjuntos_eliminar_post))
                {
                    foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->documento_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                                $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                                if (!$borrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                        }
                    }
                }

                $adjuntos_eliminar_existente_post = $this->input->post('adjunto_eliminar_existente');
                if (!empty($adjuntos_eliminar_existente_post))
                {
                    foreach ($adjuntos_eliminar_existente_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'documento_id' => $this->input->post('id')
                        ));

                        if (!empty($adjunto))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                                $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                                if (!$borrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Documentos_model->get_msg());
                    redirect('gobierno/documentos/listar_decretos', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Documentos_model->get_error())
                    {
                        $error_msg .= $this->Documentos_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->name = pathinfo($Adjunto->nombre)['filename'];
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
        if (!empty($adjuntos_agregar_post))
        {
            $adjuntos_agregar_id = array();
            foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_agregar_id[] = $Adjunto_id;
            }

            $adjuntos_agregar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_agregar = array();
            if (!empty($adjuntos_agregar))
            {
                foreach ($adjuntos_agregar as $Adjunto)
                {
                    $array_adjuntos_agregar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_agregar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_agregar'] = $array_adjuntos_agregar;
        }

        $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
        if (!empty($adjuntos_eliminar_post))
        {
            $adjuntos_eliminar_id = array();
            foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_eliminar_id[] = $Adjunto_id;
            }

            $adjuntos_eliminar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'go_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_eliminar = array();
            if (!empty($adjuntos_eliminar))
            {
                foreach ($adjuntos_eliminar as $Adjunto)
                {
                    $array_adjuntos_eliminar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_eliminar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_eliminar'] = $array_adjuntos_eliminar;
        }

        if ($this->input->post('adjunto_eliminar_existente'))
        {
            $data['adjuntos_eliminar_existente_post'] = $this->input->post('adjunto_eliminar_existente');
        }
        else
        {
            $data['adjuntos_eliminar_existente_post'] = array();
        }

        $data['fields'] = $this->build_fields($fake_model->fields, $documento);
        $data['documento'] = $documento;
        $data['txt_btn'] = 'Editar';
        $data['back_url'] = 'listar_decretos';
        $data['title_view'] = 'Editar Decreto';
        $data['title'] = TITLE . ' - Editar Decreto';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('gobierno/documentos/documentos_decreto_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $documento = $this->Documentos_model->get_one($id);
        if (empty($documento) || $documento->tipo_documento_id === '1') //HC: 1 Decreto
        {
            show_error('No se encontró el Documento', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo_documento' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'titulo' => array('label' => 'Título', 'maxlength' => '255', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5', 'disabled' => 'disabled'),
            'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'maxlength' => '255', 'disabled' => 'disabled'),
            'texto' => array('label' => 'Texto', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled')
        );

        $adjuntos = $this->Adjuntos_model->get(array(
            'documento_id' => $id,
            'join' => array(
                array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $data['adjuntos_eliminar_existente_post'] = array();

        unset($this->Partes_model->fields['persona']);
        $data['fields_parte'] = $this->build_fields($this->Partes_model->fields, $documento, TRUE);
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $documento, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $documento, TRUE);
        $data['fields'] = $this->build_fields($fake_model->fields, $documento, TRUE);
        $data['documento'] = $documento;
        $data['txt_btn'] = NULL;
        $data['back_url'] = 'listar';
        $data['title_view'] = 'Ver Documento';
        $data['title'] = TITLE . ' - Ver Documento';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $data['js'][] = 'js/gobierno/base.js';
        $this->load_template('gobierno/documentos/documentos_abm', $data);
    }

    public function ver_decreto($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $documento = $this->Documentos_model->get_one($id);
        if (empty($documento) || $documento->tipo_documento_id !== '1') //HC: 1 Decreto
        {
            show_error('No se encontró el Decreto', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'titulo' => array('label' => 'Título', 'maxlength' => '255', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5', 'disabled' => 'disabled'),
            'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'maxlength' => '255', 'disabled' => 'disabled'),
            'texto' => array('label' => 'Texto', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled')
        );

        $adjuntos = $this->Adjuntos_model->get(array(
            'documento_id' => $id,
            'join' => array(
                array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $data['adjuntos_eliminar_existente_post'] = array();

        $data['fields'] = $this->build_fields($fake_model->fields, $documento, TRUE);
        $data['documento'] = $documento;
        $data['txt_btn'] = NULL;
        $data['back_url'] = 'listar_decretos';
        $data['title_view'] = 'Ver Decreto';
        $data['title'] = TITLE . ' - Ver Decreto';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('gobierno/documentos/documentos_decreto_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("gobierno/documentos/ver/$id", 'refresh');
        }

        $documento = $this->Documentos_model->get_one($id);
        if (empty($documento) || $documento->tipo_documento_id === '1' || $documento->estado !== 'Activo') //HC: 1 Decreto
        {
            show_error('No se encontró el Documento', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo_documento' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'titulo' => array('label' => 'Título', 'maxlength' => '255', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5', 'disabled' => 'disabled'),
            'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'maxlength' => '255', 'disabled' => 'disabled'),
            'texto' => array('label' => 'Texto', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled')
        );

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Adjuntos_model->delete_adjuntos($this->input->post('id'));
            $trans_ok &= $this->Documentos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();

                $dir = "uploads/gobierno/documentos/" . str_pad($this->input->post('id'), 6, "0", STR_PAD_LEFT) . "/";
                $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $file)
                {
                    if ($file->isDir())
                    {
                        rmdir($file->getRealPath());
                    }
                    else
                    {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($dir);

                $this->session->set_flashdata('message', $this->Documentos_model->get_msg());
                redirect('gobierno/documentos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Documentos_model->get_error())
                {
                    $error_msg .= $this->Documentos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $adjuntos = $this->Adjuntos_model->get(array(
            'documento_id' => $id,
            'join' => array(
                array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $data['adjuntos_eliminar_existente_post'] = array();

        unset($this->Partes_model->fields['persona']);
        $data['fields_parte'] = $this->build_fields($this->Partes_model->fields, $documento, TRUE);
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields, $documento, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $documento, TRUE);
        $data['fields'] = $this->build_fields($fake_model->fields, $documento, TRUE);
        $data['documento'] = $documento;
        $data['txt_btn'] = 'Eliminar';
        $data['back_url'] = 'listar';
        $data['title_view'] = 'Eliminar Documento';
        $data['title'] = TITLE . ' - Eliminar Documento';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $data['js'][] = 'js/gobierno/base.js';
        $this->load_template('gobierno/documentos/documentos_abm', $data);
    }

    public function eliminar_decreto($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("gobierno/documentos/ver/$id", 'refresh');
        }

        $documento = $this->Documentos_model->get_one($id);
        if (empty($documento) || $documento->tipo_documento_id !== '1' || $documento->estado !== 'Activo') //HC: 1 Decreto
        {
            show_error('No se encontró el Documento', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'titulo' => array('label' => 'Título', 'maxlength' => '255', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5', 'disabled' => 'disabled'),
            'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'maxlength' => '255', 'disabled' => 'disabled'),
            'texto' => array('label' => 'Texto', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled')
        );

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Adjuntos_model->delete_adjuntos($this->input->post('id'));
            $trans_ok &= $this->Documentos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();

                $dir = "uploads/gobierno/documentos/" . str_pad($this->input->post('id'), 6, "0", STR_PAD_LEFT) . "/";
                $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $file)
                {
                    if ($file->isDir())
                    {
                        rmdir($file->getRealPath());
                    }
                    else
                    {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($dir);

                $this->session->set_flashdata('message', $this->Documentos_model->get_msg());
                redirect('gobierno/documentos/listar_decretos', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Documentos_model->get_error())
                {
                    $error_msg .= $this->Documentos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $adjuntos = $this->Adjuntos_model->get(array(
            'documento_id' => $id,
            'join' => array(
                array('go_tipos_adjuntos', 'go_tipos_adjuntos.id = go_adjuntos.tipo_id', 'LEFT', array('go_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $data['adjuntos_eliminar_existente_post'] = array();

        $data['fields'] = $this->build_fields($fake_model->fields, $documento);
        $data['documento'] = $documento;
        $data['txt_btn'] = 'Eliminar';
        $data['back_url'] = 'listar_decretos';
        $data['title_view'] = 'Eliminar Decreto';
        $data['title'] = TITLE . ' - Eliminar Decreto';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('gobierno/documentos/documentos_decreto_abm', $data);
    }
}
