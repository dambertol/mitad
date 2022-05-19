<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estados extends MY_Controller
{

    /**
     * Controlador de Estados
     * Autor: Leandro
     * Creado: 27/04/2021
     * Modificado: 03/07/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Estados_model');
        $this->load->model('tramites_online/Estados_secuencias_model');
        $this->load->model('tramites_online/Formularios_model');
        $this->load->model('tramites_online/Pasos_model');
        $this->load->model('tramites_online/Procesos_model');
        $this->load->model('tramites_online/Oficinas_model');
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
                array('label' => '#', 'data' => 'id', 'width' => 2),
                array('label' => 'Oficina Proceso', 'data' => 'oficina_proceso', 'width' => 18),
                array('label' => 'Proceso', 'data' => 'proceso', 'width' => 18),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 24),
                array('label' => 'Inicial', 'data' => 'inicial', 'width' => 6),
                array('label' => 'Final', 'data' => 'final', 'width' => 6),
                array('label' => 'Editable', 'data' => 'editable', 'width' => 6),
                array('label' => 'Imprimible', 'data' => 'imprimible', 'width' => 6),
                array('label' => 'Oficina Estado', 'data' => 'oficina', 'width' => 18),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'estados_table',
            'source_url' => 'tramites_online/estados/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_estados_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Estados';
        $data['title'] = TITLE . ' - Estados';
        $this->load_template('tramites_online/estados/estados_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
            ->select('to2_estados.id, OP.nombre as oficina_proceso, to2_procesos.nombre as proceso, to2_estados.nombre, to2_estados.inicial as inicial, to2_estados.final as final, to2_estados.editable as editable, to2_estados.imprimible as imprimible, OE.nombre as oficina')
            ->from('to2_estados')
            ->join('to2_procesos', 'to2_procesos.id = to2_estados.proceso_id', 'left')
            ->join('to2_oficinas OP', 'OP.id = to2_procesos.oficina_id', 'left')
            ->join('to2_oficinas OE', 'OE.id = to2_estados.oficina_id', 'left')
            ->add_column('ver', '<a href="tramites_online/estados/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            ->add_column('editar', '<a href="tramites_online/estados/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            ->add_column('eliminar', '<a href="tramites_online/estados/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('tramites_online/estados/listar', 'refresh');
        }

        $this->array_proceso_control = $array_proceso = $this->get_array('Procesos', 'nombre');
        $this->array_inicial_control = $array_inicial = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_final_control = $array_final = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_editable_control = $array_editable = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_imprimible_control = $array_imprimible = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_oficina_control = $array_oficina = $this->get_array('Oficinas', 'nombre', 'id', [], ['' => '-- Sin Oficina (Público) --']);
        $this->set_model_validation_rules($this->Estados_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE) {
            if ($this->input->post('inicial') === 'SI') {
                $estado_inicial = $this->Estados_model->get(array('proceso_id' => $this->input->post('proceso'), 'inicial' => 'SI'));
                if (!empty($estado_inicial)) {
                    $error_msg = '<br />Sólo puede existir un estado inicial por proceso.';
                }
            }

            if ($this->input->post('inicial') === 'SI' && $this->input->post('final') === 'SI') {
                $error_msg = '<br />Un estado no puede ser inicial y final a la vez.';
            }

            if (empty($error_msg)) {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Estados_model->create(
                    array(
                        'proceso_id' => $this->input->post('proceso'),
                        'inicial' => $this->input->post('inicial'),
                        'final' => $this->input->post('final'),
                        'editable' => $this->input->post('editable'),
                        'imprimible' => $this->input->post('imprimible'),
                        'nombre' => $this->input->post('nombre'),
                        'oficina_id' => $this->input->post('oficina'),
                        'mensaje' => $this->input->post('mensaje')
                    ), FALSE);

                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Estados_model->get_msg());
                    redirect('tramites_online/estados/listar', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Estados_model->get_error()) {
                        $error_msg .= $this->Estados_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Estados_model->fields['proceso']['array'] = $array_proceso;
        $this->Estados_model->fields['inicial']['array'] = $array_inicial;
        $this->Estados_model->fields['final']['array'] = $array_final;
        $this->Estados_model->fields['editable']['array'] = $array_editable;
        $this->Estados_model->fields['imprimible']['array'] = $array_imprimible;
        $this->Estados_model->fields['oficina']['array'] = $array_oficina;
        $data['fields'] = $this->build_fields($this->Estados_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Estado';
        $data['title'] = TITLE . ' - Agregar Estado';
        $this->load_template('tramites_online/estados/estados_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("tramites_online/estados/ver/$id", 'refresh');
        }

        $this->array_proceso_control = $array_proceso = $this->get_array('Procesos', 'nombre');
        $this->array_inicial_control = $array_inicial = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_final_control = $array_final = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_editable_control = $array_editable = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_imprimible_control = $array_imprimible = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_oficina_control = $array_oficina = $this->get_array('Oficinas', 'nombre', 'id', [], ['' => '-- Sin Oficina (Público) --']);
        $estado = $this->Estados_model->get(array('id' => $id));
        if (empty($estado)) {
            show_error('No se encontró el Estado', 500, 'Registro no encontrado');
        }

        $detalles_pasos_actuales = $this->Pasos_model->get(array('estado_id' => $estado->id));
        if (empty($detalles_pasos_actuales)) {
            $detalles_pasos_actuales = array();
        }

        $this->array_modo_control = $array_modo = array('Edicion' => 'Edicion', 'Visualizacion' => 'Visualizacion');
        $this->array_regla_p_control = $array_regla_p = array('Simple' => 'Simple', 'Multiple' => 'Multiple');
        $this->array_padron_control = $array_padron = array(NULL => '-- Sin Padrón (Usar Formulario) --', 'Obligatorio' => 'Obligatorio');
        $this->array_formulario_control = $array_formulario = $this->get_array('Formularios', 'nombre', 'id', array('where' => array(array('column' => 'proceso_id', 'value' => $estado->proceso_id))), array(NULL => '-- Sin Formulario (Usar Padrón) --'));
        $this->form_validation->set_rules('cant_rows_pasos', 'Cantidad de Pasos', 'required|integer');
        $cant_rows_pasos = 0;
        if ($this->input->post('cant_rows_pasos')) {
            if ($this->input->post('orden_1')) {
                $cant_rows_pasos = $this->input->post('cant_rows_pasos');
            }

            for ($i = 1; $i <= $cant_rows_pasos; $i++) {
                $this->form_validation->set_rules('orden_' . $i, 'Orden ' . $i, 'required|integer');
                $this->form_validation->set_rules('modo_' . $i, 'Modo ' . $i, 'required|callback_control_combo[modo]');
                $this->form_validation->set_rules('regla_p_' . $i, 'Regla ' . $i, 'required|callback_control_combo[regla_p]');
                $this->form_validation->set_rules('padron_' . $i, 'Padrón ' . $i, 'callback_control_combo[padron]');
                $this->form_validation->set_rules('formulario_' . $i, 'Formulario ' . $i, 'callback_control_combo[formulario]');
                $this->form_validation->set_rules('mensaje_p_' . $i, 'Mensaje ' . $i, 'max_length[99999]');
            }
        }

        $detalles_actuales = $this->Estados_secuencias_model->get(array('estado_id' => $estado->id));
        if (empty($detalles_actuales)) {
            $detalles_actuales = array();
        }

        $this->array_estado_posterior_control = $array_estado_posterior = $this->get_array(
            'Estados',
            'nombre',
            'id',
            array(
                'where' => array(
                    array(
                        'column' => 'id <>',
                        'value' => $estado->id
                    ),
                    [
                        'column' => 'proceso_id =',
                        'value' => $estado->proceso_id
                    ]
                )
            ),
            array(NULL => '-- Sin Posterior --')
        );


        $this->array_tipo_control = $array_tipo = array(NULL => '-- Sin Tipo --', 'Secuencial' => 'Secuencial');

        $this->array_icono_control = $array_icono = array(
            //NULL => '-- Sin Icono --',
            'fa fa-arrow-right' => 'fa fa-arrow-right',
            'fa fa-arrow-left' => 'fa fa-arrow-left',
            'fa fa-ban' => 'fa fa-ban',
        );

        $this->form_validation->set_rules('cant_rows', 'Cantidad de Campos', 'required|integer');
        $cant_rows = 0;
        if ($this->input->post('cant_rows')) {
            if ($this->input->post('estado_posterior_1')) {
                $cant_rows = $this->input->post('cant_rows');
            }

            for ($i = 1; $i <= $cant_rows; $i++) {
                $this->form_validation->set_rules('id_detalle_' . $i, 'ID ' . $i, 'integer');
                $this->form_validation->set_rules('estado_posterior_' . $i, 'Posterior ' . $i, 'callback_control_combo[estado_posterior]');
                $this->form_validation->set_rules('tipo_' . $i, 'Tipo ' . $i, 'callback_control_combo[tipo]');
                $this->form_validation->set_rules('regla_' . $i, 'Regla ' . $i, 'max_length[9999]');
                //$this->form_validation->set_rules('icono_' . $i, 'Ícono ' . $i, 'max_length[50]');
                $this->form_validation->set_rules('icono_' . $i, 'Ícono ' . $i, 'callback_control_combo[icono]');
            }
        }

        $this->set_model_validation_rules($this->Estados_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE) {
                if ($this->input->post('inicial') === 'SI') {
                    $estado_inicial = $this->Estados_model->get(array('id !=' => $id, 'proceso_id' => $this->input->post('proceso'), 'inicial' => 'SI'));
                    if (!empty($estado_inicial)) {
                        $error_msg = '<br />Sólo puede existir un estado inicial por proceso.';
                    }
                    if ($cant_rows > 1) {
                        $error_msg = '<br />Sólo puede existir un estado posterior para los estados iniciales.';
                    }
                }

                if ($this->input->post('inicial') === 'SI' && $this->input->post('final') === 'SI') {
                    $error_msg = '<br />Un estado no puede ser inicial y final a la vez.';
                }

                if (empty($error_msg)) {
                    $this->db->trans_begin();
                    $trans_ok = TRUE;
                    $trans_ok &= $this->Estados_model->update(
                        array(
                            'id' => $this->input->post('id'),
                            'proceso_id' => $this->input->post('proceso'),
                            'inicial' => $this->input->post('inicial'),
                            'final' => $this->input->post('final'),
                            'editable' => $this->input->post('editable'),
                            'imprimible' => $this->input->post('imprimible'),
                            'nombre' => $this->input->post('nombre'),
                            'oficina_id' => $this->input->post('oficina'),
                            'mensaje' => $this->input->post('mensaje')
                        ), FALSE);

                    $ordenes = array();
                    $formularios = array();
                    $padrones = array();
                    $post_detalles_pasos_update = array();
                    for ($i = 1; $i <= $cant_rows_pasos; $i++) {
                        if (!isset($ordenes[$this->input->post('orden_' . $i)])) {
                            if (!isset($formularios[$this->input->post('formulario_' . $i)])) {
                                if (!isset($padrones[$this->input->post('padron_' . $i)])) {
                                    if (($this->input->post('padron_' . $i) !== '' && $this->input->post('formulario_' . $i) !== '') ||
                                        ($this->input->post('padron_' . $i) === '' && $this->input->post('formulario_' . $i) === '')) {
                                        $trans_ok = FALSE;
                                        $error_msg = '<br />Debe seleccionar Padrón o Formulario (no es posible ambas).';
                                        break;
                                    }

                                    $ordenes[$this->input->post('orden_' . $i)] = TRUE;
                                    if ($this->input->post('formulario_' . $i) !== '') {
                                        $formularios[$this->input->post('formulario_' . $i)] = TRUE;
                                    }
                                    if ($this->input->post('padron_' . $i) !== '') {
                                        $padrones[$this->input->post('padron_' . $i)] = TRUE;
                                    }

                                    $detalle_pasos_post = new stdClass();
                                    $detalle_pasos_post->id = $this->input->post('id_detalle_pasos_' . $i);
                                    $detalle_pasos_post->orden = $this->input->post('orden_' . $i);
                                    $detalle_pasos_post->modo = $this->input->post('modo_' . $i);
                                    $detalle_pasos_post->regla_p = $this->input->post('regla_p_' . $i);
                                    $detalle_pasos_post->padron = $this->input->post('padron_' . $i);
                                    $detalle_pasos_post->formulario = $this->input->post('formulario_' . $i);
                                    $detalle_pasos_post->mensaje_p = $this->input->post('mensaje_p_' . $i);
                                    if (!empty($detalle_pasos_post->id)) {
                                        $post_detalles_pasos_update[$detalle_pasos_post->id] = $detalle_pasos_post;
                                    } else {
                                        $trans_ok &= $this->Pasos_model->create(
                                            array(
                                                'orden' => $detalle_pasos_post->orden,
                                                'modo' => $detalle_pasos_post->modo,
                                                'regla' => $detalle_pasos_post->regla_p,
                                                'padron' => $detalle_pasos_post->padron,
                                                'formulario_id' => $detalle_pasos_post->formulario,
                                                'estado_id' => $estado->id,
                                                'mensaje' => $detalle_pasos_post->mensaje_p
                                            ), FALSE);
                                    }
                                } else {
                                    $trans_ok = FALSE;
                                    $error_msg = '<br />No puede repetirse el uso de padron.';
                                }
                            } else {
                                $trans_ok = FALSE;
                                $error_msg = '<br />No puede repetirse el formulario usado.';
                            }
                        } else {
                            $trans_ok = FALSE;
                            $error_msg = '<br />No puede repetirse el orden de los pasos.';
                        }
                    }

                    if (!empty($detalles_pasos_actuales)) {
                        foreach ($detalles_pasos_actuales as $Detalle_actual) {
                            // SI YA EXISTE EL CAMPO
                            if (isset($post_detalles_pasos_update[$Detalle_actual->id])) {
                                $trans_ok &= $this->Pasos_model->update(
                                    array(
                                        'id' => $Detalle_actual->id,
                                        'orden' => $post_detalles_pasos_update[$Detalle_actual->id]->orden,
                                        'modo' => $post_detalles_pasos_update[$Detalle_actual->id]->modo,
                                        'regla' => $post_detalles_pasos_update[$Detalle_actual->id]->regla_p,
                                        'padron' => $post_detalles_pasos_update[$Detalle_actual->id]->padron,
                                        'formulario_id' => $post_detalles_pasos_update[$Detalle_actual->id]->formulario,
                                        'estado_id' => $estado->id,
                                        'mensaje' => $post_detalles_pasos_update[$Detalle_actual->id]->mensaje_p
                                    ), FALSE);
                            } else {
                                $trans_ok &= $this->Pasos_model->delete(array('id' => $Detalle_actual->id), FALSE);
                            }
                        }
                    }

                    $post_detalles_update = array();
                    for ($i = 1; $i <= $cant_rows; $i++) {
                        $detalle_post = new stdClass();
                        $detalle_post->id = $this->input->post('id_detalle_' . $i);
                        $detalle_post->estado_posterior = $this->input->post('estado_posterior_' . $i);
                        $detalle_post->tipo = $this->input->post('tipo_' . $i);
                        $detalle_post->regla = $this->input->post('regla_' . $i);
                        $detalle_post->icono = $this->input->post('icono_' . $i);
                        if (!empty($detalle_post->id)) {
                            $post_detalles_update[$detalle_post->id] = $detalle_post;
                        } else {
                            $trans_ok &= $this->Estados_secuencias_model->create(
                                array(
                                    'estado_id' => $estado->id,
                                    'estado_posterior_id' => $detalle_post->estado_posterior,
                                    'tipo' => $detalle_post->tipo,
                                    'regla' => $detalle_post->regla,
                                    'icono' => $detalle_post->icono
                                ), FALSE);
                        }
                    }

                    if (!empty($detalles_actuales)) {
                        foreach ($detalles_actuales as $Detalle_actual) {
                            // SI YA EXISTE EL CAMPO
                            if (isset($post_detalles_update[$Detalle_actual->id])) {
                                $trans_ok &= $this->Estados_secuencias_model->update(
                                    array(
                                        'id' => $Detalle_actual->id,
                                        'estado_id' => $estado->id,
                                        'estado_posterior_id' => $post_detalles_update[$Detalle_actual->id]->estado_posterior,
                                        'tipo' => $post_detalles_update[$Detalle_actual->id]->tipo,
                                        'regla' => $post_detalles_update[$Detalle_actual->id]->regla,
                                        'icono' => $post_detalles_update[$Detalle_actual->id]->icono
                                    ), FALSE);
                            } else {
                                $trans_ok &= $this->Estados_secuencias_model->delete(array('id' => $Detalle_actual->id), FALSE);
                            }
                        }
                    }

                    if ($this->db->trans_status() && $trans_ok) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('message', $this->Estados_model->get_msg());
                        redirect('tramites_online/estados/listar', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        if (empty($error_msg)) {
                            $error_msg = '<br />Se ha producido un error con la base de datos.';
                        }
                        if ($this->Estados_model->get_error()) {
                            $error_msg .= $this->Estados_model->get_error();
                        }
                        if ($this->Estados_secuencias_model->get_error()) {
                            $error_msg .= $this->Estados_secuencias_model->get_error();
                        }
                        if ($this->Pasos_model->get_error()) {
                            $error_msg .= $this->Pasos_model->get_error();
                        }
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Estados_model->fields['proceso']['array'] = $array_proceso;
        $this->Estados_model->fields['inicial']['array'] = $array_inicial;
        $this->Estados_model->fields['final']['array'] = $array_final;
        $this->Estados_model->fields['editable']['array'] = $array_editable;
        $this->Estados_model->fields['imprimible']['array'] = $array_imprimible;
        $this->Estados_model->fields['oficina']['array'] = $array_oficina;
        $data['fields'] = $this->build_fields($this->Estados_model->fields, $estado);

        if (empty($_POST)) {
            $detalles = $detalles_actuales;
            $detalles_pasos = $detalles_pasos_actuales;
        } else {
            $detalles = array();
            $detalles_pasos = array();
        }

        $rows_pasos = $this->form_validation->set_value('cant_rows_pasos', max(sizeof($detalles_pasos), 1));
        $data['fields_detalle_pasos_array'] = array();
        for ($i = 1; $i <= $rows_pasos; $i++) {
            $fake_model_pasos_fields = array(
                "id_detalle_pasos_$i" => array('label' => 'ID', 'type' => 'hidden'),
                "orden_$i" => array('label' => 'Orden', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
                "modo_$i" => array('label' => 'Modo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "modo_$i", 'required' => TRUE),
                "regla_p_$i" => array('label' => 'Regla', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "regla_p_$i", 'required' => TRUE),
                "padron_$i" => array('label' => 'Padrón', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "padron_$i"),
                "formulario_$i" => array('label' => 'Formulario', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "formulario_$i", 'required' => TRUE),
                "mensaje_p_$i" => array('label' => 'Mensaje', 'type' => 'text')
            );

            if (empty($_POST) && !empty($detalles_pasos[$i - 1])) {
                $temp_detalle_pasos = new stdClass();
                $temp_detalle_pasos->{"id_detalle_pasos_{$i}"} = $detalles_pasos[$i - 1]->id;
                $temp_detalle_pasos->{"orden_{$i}"} = $detalles_pasos[$i - 1]->orden;
                $temp_detalle_pasos->{"modo_{$i}"} = $detalles_pasos[$i - 1]->modo;
                $temp_detalle_pasos->{"regla_p_{$i}"} = $detalles_pasos[$i - 1]->regla;
                $temp_detalle_pasos->{"padron_{$i}"} = $detalles_pasos[$i - 1]->padron;
                $temp_detalle_pasos->{"formulario_{$i}"} = $detalles_pasos[$i - 1]->formulario_id;
                $temp_detalle_pasos->{"mensaje_p_{$i}"} = $detalles_pasos[$i - 1]->mensaje;
            } else {
                $temp_detalle_pasos = NULL;
            }

            $fake_model_pasos_fields["modo_$i"]['array'] = $array_modo;
            $fake_model_pasos_fields["regla_p_$i"]['array'] = $array_regla_p;
            $fake_model_pasos_fields["padron_$i"]['array'] = $array_padron;
            $fake_model_pasos_fields["formulario_$i"]['array'] = $array_formulario;
            $data['fields_detalle_pasos_array'][] = $this->build_fields($fake_model_pasos_fields, $temp_detalle_pasos, FALSE, 'table');
        }

        $data['cant_rows_pasos'] = array(
            'name' => 'cant_rows_pasos',
            'id' => 'cant_rows_pasos',
            'type' => 'hidden',
            'value' => $rows_pasos
        );

        $rows = $this->form_validation->set_value('cant_rows', max(sizeof($detalles), 1));
        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++) {
            $fake_model_fields = array(
                "id_detalle_$i" => array('label' => 'ID', 'type' => 'hidden'),
                "estado_posterior_$i" => array('label' => 'Posterior', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "estado_posterior_$i"),
                "tipo_$i" => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "tipo_$i"),
                "regla_$i" => array('label' => 'Regla', 'type' => 'text'),
                "icono_$i" => array('label' => 'Icono', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "icono_$i"),
                //"icono_$i" => array('label' => 'Ícono', 'type' => 'text')
            );

            if (empty($_POST) && !empty($detalles[$i - 1])) {
                $temp_detalle = new stdClass();
                $temp_detalle->{"id_detalle_{$i}"} = $detalles[$i - 1]->id;
                $temp_detalle->{"estado_posterior_{$i}"} = $detalles[$i - 1]->estado_posterior_id;
                $temp_detalle->{"tipo_{$i}"} = $detalles[$i - 1]->tipo;
                $temp_detalle->{"regla_{$i}"} = $detalles[$i - 1]->regla;
                $temp_detalle->{"icono_{$i}"} = $detalles[$i - 1]->icono;
            } else {
                $temp_detalle = NULL;
            }

            $fake_model_fields["estado_posterior_$i"]['array'] = $array_estado_posterior;
            $fake_model_fields["tipo_$i"]['array'] = $array_tipo;
            $fake_model_fields["icono_$i"]['array'] = $array_icono;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, FALSE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $data['estado'] = $estado;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Estado';
        $data['title'] = TITLE . ' - Editar Estado';
        $this->load_template('tramites_online/estados/estados_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("tramites_online/estados/ver/$id", 'refresh');
        }

        $estado = $this->Estados_model->get_one($id);
        if (empty($estado)) {
            show_error('No se encontró el Estado', 500, 'Registro no encontrado');
        }

        $detalles_pasos = $this->Pasos_model->get(array(
            'estado_id' => $estado->id,
            'join' => array(
                array('to2_formularios', 'to2_formularios.id = to2_pasos.formulario_id', 'left', 'to2_formularios.nombre as formulario')
            )
        ));
        if (empty($detalles_pasos)) {
            $rows_pasos = 0;
        } else {
            $rows_pasos = count($detalles_pasos);
        }

        $detalles = $this->Estados_secuencias_model->get(array(
            'estado_id' => $estado->id,
            'join' => array(
                array('to2_estados', 'to2_estados.id = to2_estados_secuencias.estado_posterior_id', 'left', 'to2_estados.nombre as estado_posterior')
            )
        ));
        if (empty($detalles)) {
            $rows = 0;
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
            foreach ($detalles_pasos as $Detalle) {
                $trans_ok &= $this->Pasos_model->delete(array('id' => $Detalle->id), FALSE);
            }
            foreach ($detalles as $Detalle) {
                $trans_ok &= $this->Estados_secuencias_model->delete(array('id' => $Detalle->id), FALSE);
            }
            $trans_ok &= $this->Estados_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Estados_model->get_msg());
                redirect('tramites_online/estados/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Estados_model->get_error()) {
                    $error_msg .= $this->Estados_model->get_error();
                }
                if ($this->Estados_secuencias_model->get_error()) {
                    $error_msg .= $this->Estados_secuencias_model->get_error();
                }
                if ($this->Pasos_model->get_error()) {
                    $error_msg .= $this->Pasos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Estados_model->fields, $estado, TRUE);

        $data['fields_detalle_pasos_array'] = array();
        for ($i = 1; $i <= $rows_pasos; $i++) {
            $fake_model_pasos_fields = array(
                "id_detalle_pasos_$i" => array('label' => 'ID', 'type' => 'hidden'),
                "orden_$i" => array('label' => 'Orden', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
                "modo_$i" => array('label' => 'Modo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "modo_$i", 'required' => TRUE),
                "regla_p_$i" => array('label' => 'Regla', 'type' => 'text', 'maxlength' => '50'),
                "padron_$i" => array('label' => 'Padrón', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "padron_$i"),
                "formulario_$i" => array('label' => 'Formulario', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "formulario_$i", 'required' => TRUE),
                "mensaje_p_$i" => array('label' => 'Mensaje', 'type' => 'text')
            );

            $temp_detalle_pasos = new stdClass();
            $temp_detalle_pasos->{"id_detalle_pasos_{$i}"} = $detalles_pasos[$i - 1]->id;
            $temp_detalle_pasos->{"orden_{$i}"} = $detalles_pasos[$i - 1]->orden;
            $temp_detalle_pasos->{"modo_{$i}"} = $detalles_pasos[$i - 1]->modo;
            $temp_detalle_pasos->{"regla_p_{$i}"} = $detalles_pasos[$i - 1]->regla;
            $temp_detalle_pasos->{"padron_{$i}"} = $detalles_pasos[$i - 1]->padron;
            $temp_detalle_pasos->{"formulario_{$i}"} = $detalles_pasos[$i - 1]->formulario;
            $temp_detalle_pasos->{"mensaje_p_{$i}"} = $detalles_pasos[$i - 1]->mensaje;
            $data['fields_detalle_pasos_array'][] = $this->build_fields($fake_model_pasos_fields, $temp_detalle_pasos, TRUE, 'table');
        }

        $data['cant_rows_pasos'] = array(
            'name' => 'cant_rows_pasos',
            'id' => 'cant_rows_pasos',
            'type' => 'hidden',
            'value' => $rows_pasos
        );

        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++) {
            $fake_model_fields = array(
                "id_detalle_$i" => array('label' => 'ID', 'type' => 'hidden', 'readonly' => TRUE),
                "estado_posterior_$i" => array('label' => 'Posterior', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "estado_posterior_$i", 'required' => TRUE),
                "tipo_$i" => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "tipo_$i", 'required' => TRUE),
                "regla_$i" => array('label' => 'Regla', 'type' => 'text'),
                "icono_$i" => array('label' => 'Ícono', 'type' => 'text', 'required' => TRUE)
            );

            $temp_detalle = new stdClass();
            $temp_detalle = new stdClass();
            $temp_detalle->{"id_detalle_{$i}"} = $detalles[$i - 1]->id;
            $temp_detalle->{"estado_posterior_{$i}"} = $detalles[$i - 1]->estado_posterior;
            $temp_detalle->{"tipo_{$i}"} = $detalles[$i - 1]->tipo;
            $temp_detalle->{"regla_{$i}"} = $detalles[$i - 1]->regla;
            $temp_detalle->{"icono_{$i}"} = $detalles[$i - 1]->icono;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $data['estado'] = $estado;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Estado';
        $data['title'] = TITLE . ' - Eliminar Estado';
        $this->load_template('tramites_online/estados/estados_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $estado = $this->Estados_model->get_one($id);
        if (empty($estado)) {
            show_error('No se encontró el Estado', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Estados_model->fields, $estado, TRUE);

        $detalles_pasos = $this->Pasos_model->get(array(
            'estado_id' => $estado->id,
            'join' => array(
                array('to2_formularios', 'to2_formularios.id = to2_pasos.formulario_id', 'left', 'to2_formularios.nombre as formulario')
            )
        ));
        if (empty($detalles_pasos)) {
            $rows_pasos = 0;
        } else {
            $rows_pasos = count($detalles_pasos);
        }

        $data['fields_detalle_pasos_array'] = array();
        for ($i = 1; $i <= $rows_pasos; $i++) {
            $fake_model_pasos_fields = array(
                "id_detalle_pasos_$i" => array('label' => 'ID', 'type' => 'hidden'),
                "orden_$i" => array('label' => 'Orden', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
                "modo_$i" => array('label' => 'Modo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "modo_$i", 'required' => TRUE),
                "regla_p_$i" => array('label' => 'Regla', 'type' => 'text', 'maxlength' => '50'),
                "padron_$i" => array('label' => 'Padrón', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "padron_$i"),
                "formulario_$i" => array('label' => 'Formulario', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "formulario_$i", 'required' => TRUE),
                "mensaje_p_$i" => array('label' => 'Mensaje', 'type' => 'text')
            );

            $temp_detalle_pasos = new stdClass();
            $temp_detalle_pasos->{"id_detalle_pasos_{$i}"} = $detalles_pasos[$i - 1]->id;
            $temp_detalle_pasos->{"orden_{$i}"} = $detalles_pasos[$i - 1]->orden;
            $temp_detalle_pasos->{"modo_{$i}"} = $detalles_pasos[$i - 1]->modo;
            $temp_detalle_pasos->{"regla_p_{$i}"} = $detalles_pasos[$i - 1]->regla;
            $temp_detalle_pasos->{"padron_{$i}"} = $detalles_pasos[$i - 1]->padron;
            $temp_detalle_pasos->{"formulario_{$i}"} = $detalles_pasos[$i - 1]->formulario;
            $temp_detalle_pasos->{"mensaje_p_{$i}"} = $detalles_pasos[$i - 1]->mensaje;
            $data['fields_detalle_pasos_array'][] = $this->build_fields($fake_model_pasos_fields, $temp_detalle_pasos, TRUE, 'table');
        }

        $data['cant_rows_pasos'] = array(
            'name' => 'cant_rows_pasos',
            'id' => 'cant_rows_pasos',
            'type' => 'hidden',
            'value' => $rows_pasos
        );

        $detalles = $this->Estados_secuencias_model->get(array(
            'estado_id' => $estado->id,
            'join' => array(
                array('to2_estados', 'to2_estados.id = to2_estados_secuencias.estado_posterior_id', 'left', 'to2_estados.nombre as estado_posterior')
            )
        ));
        if (empty($detalles)) {
            $rows = 0;
        } else {
            $rows = count($detalles);
        }

        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++) {
            $fake_model_fields = array(
                "id_detalle_$i" => array('label' => 'ID', 'type' => 'hidden', 'readonly' => TRUE),
                "estado_posterior_$i" => array('label' => 'Posterior', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "estado_posterior_$i", 'required' => TRUE),
                "tipo_$i" => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => "tipo_$i", 'required' => TRUE),
                "regla_$i" => array('label' => 'Regla', 'type' => 'text'),
                "icono_$i" => array('label' => 'Ícono', 'type' => 'text', 'required' => TRUE)
            );

            $temp_detalle = new stdClass();
            $temp_detalle = new stdClass();
            $temp_detalle->{"id_detalle_{$i}"} = $detalles[$i - 1]->id;
            $temp_detalle->{"estado_posterior_{$i}"} = $detalles[$i - 1]->estado_posterior;
            $temp_detalle->{"tipo_{$i}"} = $detalles[$i - 1]->tipo;
            $temp_detalle->{"regla_{$i}"} = $detalles[$i - 1]->regla;
            $temp_detalle->{"icono_{$i}"} = $detalles[$i - 1]->icono;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $data['estado'] = $estado;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Estado';
        $data['title'] = TITLE . ' - Ver Estado';
        $this->load_template('tramites_online/estados/estados_abm', $data);
    }
}
