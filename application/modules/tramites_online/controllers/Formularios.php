<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Formularios extends MY_Controller
{

    /**
     * Controlador de Formularios
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 08/08/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Campos_model');
        $this->load->model('tramites_online/Formularios_model');
        $this->load->model('tramites_online/Procesos_model');
        $this->grupos_permitidos = array('admin', 'tramites_online_user', 'tramites_online_consulta_general');
        $this->grupos_solo_consulta = array('tramites_online_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Proceso', 'data' => 'proceso', 'width' => 25),
                array('label' => 'Orden de Impresion', 'data' => 'orden_impresion', 'width' => 10),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 25),
                array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 44),
                array('label' => 'Imprimible', 'data' => 'imprimible', 'width' => 10),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'formularios_table',
            'source_url' => 'tramites_online/formularios/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_formularios_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Formularios';
        $data['title'] = TITLE . ' - Formularios';
        $this->load_template('tramites_online/formularios/formularios_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
            ->select('to2_formularios.id, to2_formularios.nombre, to2_formularios.descripcion, to2_formularios.imprimible, to2_formularios.orden_impresion, to2_procesos.nombre as proceso')
            ->from('to2_formularios')
            ->join('to2_procesos', 'to2_procesos.id = to2_formularios.proceso_id', 'left')
            ->add_column('ver', '<a href="tramites_online/formularios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            ->add_column('editar', '<a href="tramites_online/formularios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            ->add_column('eliminar', '<a href="tramites_online/formularios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('tramites_online/formularios/listar', 'refresh');
        }

        $this->array_proceso_control = $array_proceso = $this->get_array('Procesos', 'nombre');
        $this->array_formulario_imprimible_control = $array_formulario_imprimible = array('0' => 'NO', '1' => 'SI');
        $this->array_readonly_control = $array_readonly = array('0' => 'NO', '1' => 'SI');
        $this->array_editable_control = $array_editable = array('0' => 'NO', '1' => 'SI');
        $this->array_imprimible_control = $array_imprimible = array('0' => 'NO', '1' => 'SI');
        $this->array_obligatorio_control = $array_obligatorio = array('0' => 'NO', '1' => 'SI');

        $this->array_tipo_control = $array_tipo = array(
            'file' => 'Archivo',
            'combo' => 'Combo',
            'input' => 'Input',
            'textarea' => 'Texto multilinea',
            'h3' => 'Titulo',
            'h4' => 'Subtitulo'
        );

        $this->form_validation->set_rules('cant_rows', 'Cantidad de Campos', 'required|integer');
        if ($this->input->post('cant_rows')) {
            $cant_rows = $this->input->post('cant_rows');
            for ($i = 1; $i <= $cant_rows; $i++) {
                $this->form_validation->set_rules('nombre_' . $i, 'Nombre ' . $i, 'required|max_length[50]');
                $this->form_validation->set_rules('readonly_' . $i, 'Sólo lectura ' . $i, 'required|callback_control_combo[readonly]');
                $this->form_validation->set_rules('valor_default_' . $i, 'Valor Defecto ' . $i, 'max_length[9999]');
                $this->form_validation->set_rules('posicion_' . $i, 'Posición ' . $i, 'required|integer');
                $this->form_validation->set_rules('tipo_' . $i, 'Tipo ' . $i, 'required|callback_control_combo[tipo]');
                $this->form_validation->set_rules('opciones_' . $i, 'Opciones ' . $i, 'max_length[9999]');
                $this->form_validation->set_rules('etiqueta_' . $i, 'Etiqueta ' . $i, 'required|max_length[50]');
                $this->form_validation->set_rules('validacion_' . $i, 'Validación ' . $i, 'required|max_length[50]');
                $this->form_validation->set_rules('editable_' . $i, 'Editable' . $i, 'required|callback_control_combo[editable]');
                $this->form_validation->set_rules('imprimible_' . $i, 'Imprimible' . $i, 'required|callback_control_combo[imprimible]');
                $this->form_validation->set_rules('obligatorio_' . $i, 'Obligatorio' . $i, 'required|callback_control_combo[obligatorio]');
                $this->form_validation->set_rules('funcion_' . $i, 'Función ' . $i, 'max_length[255]');
                $this->form_validation->set_rules('ayuda_' . $i, 'Ayuda ' . $i, 'max_length[9999]');
            }
        }

        $this->set_model_validation_rules($this->Formularios_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE) {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Formularios_model->create(
                array(
                    'nombre' => $this->input->post('nombre'),
                    'descripcion' => $this->input->post('descripcion'),
                    'proceso_id' => $this->input->post('proceso'),
                    'imprimible' => $this->input->post('imprimible'),
                    'orden_impresion' => $this->input->post('orden_impresion')
                ), FALSE);

            $formulario_id = $this->Formularios_model->get_row_id();
            $posiciones = array();
            for ($i = 1; $i <= $cant_rows; $i++) {
                if (!isset($posiciones[$this->input->post('posicion_' . $i)])) {
                    if ($this->input->post('tipo_' . $i) === 'combo' && $this->input->post('opciones_' . $i) === '') {
                        $trans_ok = FALSE;
                        $error_msg = '<br />Para tipo combo debe ingresar opciones (separadas con "|").';
                        break;
                    }

                    $posiciones[$this->input->post('posicion_' . $i)] = TRUE;

                    $trans_ok &= $this->Campos_model->create(
                        array(
                            'nombre' => $this->input->post('nombre_' . $i),
                            'readonly' => $this->input->post('readonly_' . $i),
                            'valor_default' => $this->input->post('valor_default_' . $i),
                            'posicion' => $this->input->post('posicion_' . $i),
                            'tipo' => $this->input->post('tipo_' . $i),
                            'opciones' => $this->input->post('opciones_' . $i),
                            'formulario_id' => $formulario_id,
                            'etiqueta' => $this->input->post('etiqueta_' . $i),
                            'validacion' => $this->input->post('validacion_' . $i),
                            'editable' => $this->input->post('editable_' . $i),
                            'imprimible' => $this->input->post('imprimible_' . $i),
                            'obligatorio' => $this->input->post('obligatorio_' . $i),
                            'funcion' => $this->input->post('funcion_' . $i),
                            'ayuda' => $this->input->post('ayuda_' . $i)
                        ), FALSE);
                } else {
                    $trans_ok = FALSE;
                    $error_msg = '<br />No puede repetirse la posición de los campos.';
                }
            }

            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Formularios_model->get_msg());
                redirect('tramites_online/formularios/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                if (empty($error_msg)) {
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                }
                if ($this->Formularios_model->get_error()) {
                    $error_msg .= $this->Formularios_model->get_error();
                }
                if ($this->Campos_model->get_error()) {
                    $error_msg .= $this->Campos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Formularios_model->fields['proceso']['array'] = $array_proceso;
        $this->Formularios_model->fields['imprimible']['array'] = $array_formulario_imprimible;
        $data['fields'] = $this->build_fields($this->Formularios_model->fields);

        $rows = $this->form_validation->set_value('cant_rows', 1);
        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++) {
            $fake_model_fields = array(
                "nombre_$i" => array('label' => 'Nombre', 'type' => 'text', 'required' => TRUE),
                "readonly_$i" => array('label' => 'Sólo lectura', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "readonly_$i", 'required' => TRUE),
                "valor_default_$i" => array('label' => 'Valor Defecto', 'type' => 'text'),
                "posicion_$i" => array('label' => 'Posición', 'type' => 'text', 'required' => TRUE),
                "tipo_$i" => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "tipo_$i", 'required' => TRUE),
                "opciones_$i" => array('label' => 'Opciones', 'type' => 'text'),
                "etiqueta_$i" => array('label' => 'Etiqueta', 'type' => 'text', 'required' => TRUE),
                "validacion_$i" => array('label' => 'Validación', 'type' => 'text', 'required' => TRUE),
                "editable_$i" => array('label' => 'Editable', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "editable_$i", 'required' => TRUE),
                "imprimible_$i" => array('label' => 'Imprimible', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "imprimible_$i", 'required' => TRUE),
                "obligatorio_$i" => array('label' => 'Obligatorio', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "obligatorio_$i", 'required' => TRUE),
                "funcion_$i" => array('label' => 'Función', 'type' => 'text'),
                "ayuda_$i" => array('label' => 'Ayuda', 'type' => 'text')
            );


            $fake_model_fields["readonly_$i"]['array'] = $array_readonly;
            $fake_model_fields["editable_$i"]['array'] = $array_editable;
            $fake_model_fields["imprimible_$i"]['array'] = $array_imprimible;
            $fake_model_fields["obligatorio_$i"]['array'] = $array_obligatorio;
            $fake_model_fields["tipo_$i"]['array'] = $array_tipo;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, NULL, FALSE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Formulario';
        $data['title'] = TITLE . ' - Agregar Formulario';
        $this->load_template('tramites_online/formularios/formularios_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("tramites_online/formularios/ver/$id", 'refresh');
        }

        $this->array_proceso_control = $array_proceso = $this->get_array('Procesos', 'nombre');
        $formulario = $this->Formularios_model->get(array('id' => $id));
        if (empty($formulario)) {
            show_error('No se encontró el Formulario', 500, 'Registro no encontrado');
        }

        $detalles_actuales = $this->Campos_model->get(array('formulario_id' => $formulario->id));
        if (empty($detalles_actuales)) {
            show_error('No se encontró el Campo', 500, 'Registro no encontrado');
        }


        $this->array_formulario_imprimible_control = $array_formulario_imprimible = array('0' => 'NO', '1' => 'SI');
        $this->array_readonly_control = $array_readonly = array('0' => 'NO', '1' => 'SI');
        $this->array_editable_control = $array_editable = array('0' => 'NO', '1' => 'SI');
        $this->array_imprimible_control = $array_imprimible = array('0' => 'NO', '1' => 'SI');
        $this->array_obligatorio_control = $array_obligatorio = array('0' => 'NO', '1' => 'SI');
        $this->array_tipo_control = $array_tipo = array(
            'file' => 'Archivo',
            'combo' => 'Combo',
            'input' => 'Input',
            'textarea' => 'Texto multilinea',
            'h3' => 'Titulo',
            'h4' => 'Subtitulo'
        );
        $this->form_validation->set_rules('cant_rows', 'Cantidad de Campos', 'required|integer');
        if ($this->input->post('cant_rows')) {
            $cant_rows = $this->input->post('cant_rows');
            for ($i = 1; $i <= $cant_rows; $i++) {
                $this->form_validation->set_rules('id_detalle_' . $i, 'ID ' . $i, 'integer');
                $this->form_validation->set_rules('nombre_' . $i, 'Nombre ' . $i, 'required|max_length[50]');
                $this->form_validation->set_rules('readonly_' . $i, 'Sólo lectura ' . $i, 'required|callback_control_combo[readonly]');
                $this->form_validation->set_rules('valor_default_' . $i, 'Valor Defecto ' . $i, 'max_length[9999]');
                $this->form_validation->set_rules('posicion_' . $i, 'Posición ' . $i, 'required|integer');
                $this->form_validation->set_rules('tipo_' . $i, 'Tipo ' . $i, 'required|callback_control_combo[tipo]');
                $this->form_validation->set_rules('opciones_' . $i, 'Opciones ' . $i, 'max_length[9999]');
                $this->form_validation->set_rules('etiqueta_' . $i, 'Etiqueta ' . $i, 'required|max_length[50]');
                $this->form_validation->set_rules('validacion_' . $i, 'Validación ' . $i, 'required|max_length[50]');
                $this->form_validation->set_rules('editable_' . $i, 'Editable' . $i, 'required|callback_control_combo[editable]');
                $this->form_validation->set_rules('imprimible_' . $i, 'Imprimible' . $i, 'required|callback_control_combo[imprimible]');
                $this->form_validation->set_rules('obligatorio_' . $i, 'Obligatorio' . $i, 'required|callback_control_combo[obligatorio]');
                $this->form_validation->set_rules('funcion_' . $i, 'Función ' . $i, 'max_length[255]');
                $this->form_validation->set_rules('ayuda_' . $i, 'Ayuda ' . $i, 'max_length[9999]');
            }
        }

        $this->set_model_validation_rules($this->Formularios_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE) {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $cant_rows = $this->input->post('cant_rows');
                $trans_ok &= $this->Formularios_model->update(
                    array(
                        'id' => $this->input->post('id'),
                        'nombre' => $this->input->post('nombre'),
                        'descripcion' => $this->input->post('descripcion'),
                        'proceso_id' => $this->input->post('proceso'),
                        'imprimible' => $this->input->post('imprimible'),
                        'orden_impresion' => $this->input->post('orden_impresion')
                    ), FALSE);

                $post_detalles_update = array();
                $posiciones = array();
                for ($i = 1; $i <= $cant_rows; $i++) {
                    if (!isset($posiciones[$this->input->post('posicion_' . $i)])) {
                        if ($this->input->post('tipo_' . $i) === 'combo' && $this->input->post('opciones_' . $i) === '') {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Para tipo combo debe ingresar opciones (separadas con "|").';
                            break;
                        }

                        $posiciones[$this->input->post('posicion_' . $i)] = TRUE;

                        $detalle_post = new stdClass();
                        $detalle_post->id = $this->input->post('id_detalle_' . $i);
                        $detalle_post->nombre = $this->input->post('nombre_' . $i);
                        $detalle_post->readonly = $this->input->post('readonly_' . $i);
                        $detalle_post->valor_default = $this->input->post('valor_default_' . $i);
                        $detalle_post->posicion = $this->input->post('posicion_' . $i);
                        $detalle_post->tipo = $this->input->post('tipo_' . $i);
                        $detalle_post->opciones = $this->input->post('opciones_' . $i);
                        $detalle_post->etiqueta = $this->input->post('etiqueta_' . $i);
                        $detalle_post->validacion = $this->input->post('validacion_' . $i);
                        $detalle_post->editable = $this->input->post('editable_' . $i);
                        $detalle_post->imprimible = $this->input->post('imprimible_' . $i);
                        $detalle_post->obligatorio = $this->input->post('obligatorio_' . $i);
                        $detalle_post->funcion = $this->input->post('funcion_' . $i);
                        $detalle_post->ayuda = $this->input->post('ayuda_' . $i);
                        if (!empty($detalle_post->id)) {
                            $post_detalles_update[$detalle_post->id] = $detalle_post;
                        } else {
                            $trans_ok &= $this->Campos_model->create(
                                array(
                                    'nombre' => $detalle_post->nombre,
                                    'readonly' => $detalle_post->readonly,
                                    'valor_default' => $detalle_post->valor_default,
                                    'posicion' => $detalle_post->posicion,
                                    'tipo' => $detalle_post->tipo,
                                    'opciones' => $detalle_post->opciones,
                                    'formulario_id' => $formulario->id,
                                    'etiqueta' => $detalle_post->etiqueta,
                                    'validacion' => $detalle_post->validacion,
                                    'editable' => $detalle_post->editable,
                                    'imprimible' => $detalle_post->imprimible,
                                    'obligatorio' => $detalle_post->obligatorio,
                                    'funcion' => $detalle_post->funcion,
                                    'ayuda' => $detalle_post->ayuda
                                ), FALSE);
                        }
                    } else {
                        $trans_ok = FALSE;
                        $error_msg = '<br />No puede repetirse la posición de los campos.';
                    }
                }

                if (empty($error_msg) && !empty($detalles_actuales)) {
                    foreach ($detalles_actuales as $Detalle_actual) {
                        // SI YA EXISTE EL CAMPO
                        if (isset($post_detalles_update[$Detalle_actual->id])) {
                            $trans_ok &= $this->Campos_model->update(
                                array(
                                    'id' => $Detalle_actual->id,
                                    'nombre' => $post_detalles_update[$Detalle_actual->id]->nombre,
                                    'readonly' => $post_detalles_update[$Detalle_actual->id]->readonly,
                                    'valor_default' => $post_detalles_update[$Detalle_actual->id]->valor_default,
                                    'posicion' => $post_detalles_update[$Detalle_actual->id]->posicion,
                                    'tipo' => $post_detalles_update[$Detalle_actual->id]->tipo,
                                    'opciones' => $post_detalles_update[$Detalle_actual->id]->opciones,
                                    'formulario_id' => $formulario->id,
                                    'etiqueta' => $post_detalles_update[$Detalle_actual->id]->etiqueta,
                                    'validacion' => $post_detalles_update[$Detalle_actual->id]->validacion,
                                    'editable' => $post_detalles_update[$Detalle_actual->id]->editable,
                                    'imprimible' => $post_detalles_update[$Detalle_actual->id]->imprimible,
                                    'obligatorio' => $post_detalles_update[$Detalle_actual->id]->obligatorio,
                                    'funcion' => $post_detalles_update[$Detalle_actual->id]->funcion,
                                    'ayuda' => $post_detalles_update[$Detalle_actual->id]->ayuda
                                ), FALSE);
                        } else {
                            $trans_ok &= $this->Campos_model->delete(array('id' => $Detalle_actual->id), FALSE);
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Formularios_model->get_msg());
                    redirect('tramites_online/formularios/listar', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    if (empty($error_msg)) {
                        $error_msg = '<br />Se ha producido un error con la base de datos.';
                    }
                    if ($this->Formularios_model->get_error()) {
                        $error_msg .= $this->Formularios_model->get_error();
                    }
                    if ($this->Campos_model->get_error()) {
                        $error_msg .= $this->Campos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Formularios_model->fields['proceso']['array'] = $array_proceso;
        $this->Formularios_model->fields['imprimible']['array'] = $array_formulario_imprimible;
        $data['fields'] = $this->build_fields($this->Formularios_model->fields, $formulario);

        if (empty($_POST)) {
            $detalles = $detalles_actuales;
        } else {
            $detalles = array();
        }
        $rows = $this->form_validation->set_value('cant_rows', sizeof($detalles));
        $data['fields_detalle_array'] = array();

        for ($i = 1; $i <= $rows; $i++) {
            $fake_model_fields = array(
                "id_detalle_$i" => array('label' => 'ID', 'type' => 'hidden', 'readonly' => TRUE),
                "nombre_$i" => array('label' => 'Nombre', 'type' => 'text', 'required' => TRUE),
                "readonly_$i" => array('label' => 'Sólo lectura', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "readonly_$i", 'required' => TRUE),
                "valor_default_$i" => array('label' => 'Valor Defecto', 'type' => 'text'),
                "posicion_$i" => array('label' => 'Posición', 'type' => 'text', 'required' => TRUE),
                "tipo_$i" => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "tipo_$i", 'required' => TRUE),
                "opciones_$i" => array('label' => 'Opciones', 'type' => 'text'),
                "etiqueta_$i" => array('label' => 'Etiqueta', 'type' => 'text', 'required' => TRUE),
                "validacion_$i" => array('label' => 'Validación', 'type' => 'text', 'required' => TRUE),
                "editable_$i" => array('label' => 'Editable', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "editable_$i", 'required' => TRUE),
                "imprimible_$i" => array('label' => 'Imprimible', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "imprimible_$i", 'required' => TRUE),
                "obligatorio_$i" => array('label' => 'Obligatorio', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "obligatorio_$i", 'required' => TRUE),
                "funcion_$i" => array('label' => 'Función', 'type' => 'text'),
                "ayuda_$i" => array('label' => 'Ayuda', 'type' => 'text')
            );

            if (empty($_POST)) {
                $temp_detalle = new stdClass();
                $temp_detalle->{"id_detalle_{$i}"} = $detalles[$i - 1]->id;
                $temp_detalle->{"nombre_{$i}"} = $detalles[$i - 1]->nombre;
                $temp_detalle->{"readonly_{$i}"} = $detalles[$i - 1]->readonly;
                $temp_detalle->{"valor_default_{$i}"} = $detalles[$i - 1]->valor_default;
                $temp_detalle->{"posicion_{$i}"} = $detalles[$i - 1]->posicion;
                $temp_detalle->{"tipo_{$i}"} = $detalles[$i - 1]->tipo;
                $temp_detalle->{"opciones_{$i}"} = $detalles[$i - 1]->opciones;
                $temp_detalle->{"etiqueta_{$i}"} = $detalles[$i - 1]->etiqueta;
                $temp_detalle->{"validacion_{$i}"} = $detalles[$i - 1]->validacion;
                $temp_detalle->{"editable_{$i}"} = $detalles[$i - 1]->editable;
                $temp_detalle->{"imprimible_{$i}"} = $detalles[$i - 1]->imprimible;
                $temp_detalle->{"obligatorio_{$i}"} = $detalles[$i - 1]->obligatorio;
                $temp_detalle->{"funcion_{$i}"} = $detalles[$i - 1]->funcion;
                $temp_detalle->{"ayuda_{$i}"} = $detalles[$i - 1]->ayuda;
            } else {
                $temp_detalle = NULL;
            }

            $fake_model_fields["readonly_$i"]['array'] = $array_readonly;
            $fake_model_fields["editable_$i"]['array'] = $array_editable;
            $fake_model_fields["imprimible_$i"]['array'] = $array_imprimible;
            $fake_model_fields["obligatorio_$i"]['array'] = $array_obligatorio;
            $fake_model_fields["tipo_$i"]['array'] = $array_tipo;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, FALSE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $data['formulario'] = $formulario;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Formulario';
        $data['title'] = TITLE . ' - Editar Formulario';
        $this->load_template('tramites_online/formularios/formularios_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("tramites_online/formularios/ver/$id", 'refresh');
        }

        $formulario = $this->Formularios_model->get_one($id);
        if (empty($formulario)) {
            show_error('No se encontró el Formulario', 500, 'Registro no encontrado');
        }

        $detalles = $this->Campos_model->get(array('formulario_id' => $formulario->id));
        if (empty($detalles)) {
            show_error('No se encontró el Campo', 500, 'Registro no encontrado');
        } else {
            $rows = count($detalles);
        }

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            foreach ($detalles as $Detalle) {
                $trans_ok &= $this->Campos_model->delete(array('id' => $Detalle->id), FALSE);
            }
            $trans_ok &= $this->Formularios_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Formularios_model->get_msg());
                redirect('tramites_online/formularios/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Formularios_model->get_error()) {
                    $error_msg .= $this->Formularios_model->get_error();
                }
                if ($this->Campos_model->get_error()) {
                    $error_msg .= $this->Campos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++) {
            $fake_model_fields = array(
                "nombre_$i" => array('label' => 'Nombre', 'type' => 'text', 'required' => TRUE),
                "readonly_$i" => array('label' => 'Sólo lectura', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "readonly_$i", 'required' => TRUE),
                "valor_default_$i" => array('label' => 'Valor Defecto', 'type' => 'text'),
                "posicion_$i" => array('label' => 'Posición', 'type' => 'text', 'required' => TRUE),
                "tipo_$i" => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "tipo_$i", 'required' => TRUE),
                "opciones_$i" => array('label' => 'Opciones', 'type' => 'text'),
                "etiqueta_$i" => array('label' => 'Etiqueta', 'type' => 'text', 'required' => TRUE),
                "validacion_$i" => array('label' => 'Validación', 'type' => 'text', 'required' => TRUE),
                "funcion_$i" => array('label' => 'Función', 'type' => 'text'),
                "ayuda_$i" => array('label' => 'Ayuda', 'type' => 'text')
            );

            $temp_detalle = new stdClass();
            $temp_detalle->{"nombre_{$i}"} = $detalles[$i - 1]->nombre;
            $temp_detalle->{"readonly_{$i}"} = $detalles[$i - 1]->readonly === '0' ? 'NO' : 'SI';
            $temp_detalle->{"valor_default_{$i}"} = $detalles[$i - 1]->valor_default;
            $temp_detalle->{"posicion_{$i}"} = $detalles[$i - 1]->posicion;
            $temp_detalle->{"tipo_{$i}"} = ucfirst($detalles[$i - 1]->tipo);
            $temp_detalle->{"opciones_{$i}"} = $detalles[$i - 1]->opciones;
            $temp_detalle->{"etiqueta_{$i}"} = $detalles[$i - 1]->etiqueta;
            $temp_detalle->{"validacion_{$i}"} = $detalles[$i - 1]->validacion;
            $temp_detalle->{"funcion_{$i}"} = $detalles[$i - 1]->funcion;
            $temp_detalle->{"ayuda_{$i}"} = $detalles[$i - 1]->ayuda;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $data['fields'] = $this->build_fields($this->Formularios_model->fields, $formulario, TRUE);
        $data['formulario'] = $formulario;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Formulario';
        $data['title'] = TITLE . ' - Eliminar Formulario';
        $this->load_template('tramites_online/formularios/formularios_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $formulario = $this->Formularios_model->get_one($id);
        if (empty($formulario)) {
            show_error('No se encontró el Formulario', 500, 'Registro no encontrado');
        }

        $detalles = $this->Campos_model->get(array('formulario_id' => $formulario->id));
        if (empty($detalles)) {
            show_error('No se encontró el Campo', 500, 'Registro no encontrado');
        } else {
            $rows = count($detalles);
        }

        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++) {
            $fake_model_fields = array(
                "nombre_$i" => array('label' => 'Nombre', 'type' => 'text', 'required' => TRUE),
                "readonly_$i" => array('label' => 'Sólo lectura', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "readonly_$i", 'required' => TRUE),
                "valor_default_$i" => array('label' => 'Valor Defecto', 'type' => 'text'),
                "posicion_$i" => array('label' => 'Posición', 'type' => 'text', 'required' => TRUE),
                "tipo_$i" => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "tipo_$i", 'required' => TRUE),
                "opciones_$i" => array('label' => 'Opciones', 'type' => 'text'),
                "etiqueta_$i" => array('label' => 'Etiqueta', 'type' => 'text', 'required' => TRUE),
                "validacion_$i" => array('label' => 'Validación', 'type' => 'text', 'required' => TRUE),
                "editable_$i" => array('label' => 'Editable', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "editable_$i", 'required' => TRUE),
                "imprimible_$i" => array('label' => 'Imprimible', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "imprimible_$i", 'required' => TRUE),
                "obligatorio_$i" => array('label' => 'Obligatorio', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "obligatorio_$i", 'required' => TRUE),
                "funcion_$i" => array('label' => 'Función', 'type' => 'text'),
                "ayuda_$i" => array('label' => 'Ayuda', 'type' => 'text')
            );

            $temp_detalle = new stdClass();
            $temp_detalle->{"nombre_{$i}"} = $detalles[$i - 1]->nombre;
            $temp_detalle->{"readonly_{$i}"} = $detalles[$i - 1]->readonly === '0' ? 'NO' : 'SI';
            $temp_detalle->{"valor_default_{$i}"} = $detalles[$i - 1]->valor_default;
            $temp_detalle->{"posicion_{$i}"} = $detalles[$i - 1]->posicion;
            $temp_detalle->{"tipo_{$i}"} = ucfirst($detalles[$i - 1]->tipo);
            $temp_detalle->{"opciones_{$i}"} = $detalles[$i - 1]->opciones;
            $temp_detalle->{"etiqueta_{$i}"} = $detalles[$i - 1]->etiqueta;
            $temp_detalle->{"validacion_{$i}"} = $detalles[$i - 1]->validacion;
            $temp_detalle->{"editable_{$i}"} = $detalles[$i - 1]->editable === '0' ? 'NO' : 'SI';
            $temp_detalle->{"imprimible_{$i}"} = $detalles[$i - 1]->imprimible === '0' ? 'NO' : 'SI';
            $temp_detalle->{"obligatorio_{$i}"} = $detalles[$i - 1]->obligatorio === '0' ? 'NO' : 'SI';
            $temp_detalle->{"funcion_{$i}"} = $detalles[$i - 1]->funcion;
            $temp_detalle->{"ayuda_{$i}"} = $detalles[$i - 1]->ayuda;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $formulario->imprimible = $formulario->imprimible ? 'NO' : 'SI';

        $data['fields'] = $this->build_fields($this->Formularios_model->fields, $formulario, TRUE);
        $data['formulario'] = $formulario;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Formulario';
        $data['title'] = TITLE . ' - Ver Formulario';
        $this->load_template('tramites_online/formularios/formularios_abm', $data);
    }
}
