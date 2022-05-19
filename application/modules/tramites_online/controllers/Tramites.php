<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites extends MY_Controller
{

    /**
     * Controlador de Trámites
     * Autor: Leandro
     * Creado: 17/03/2020
     * Modificado: 19/08/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Adjuntos_model');
        $this->load->model('tramites_online/Campos_model');
        $this->load->model('tramites_online/Datos_model');
        $this->load->model('tramites_online/Estados_model');
        $this->load->model('tramites_online/Estados_secuencias_model');
        $this->load->model('tramites_online/Iniciadores_model');
        $this->load->model('tramites_online/Formularios_model');
        $this->load->model('tramites_online/Oficinas_model');
        $this->load->model('tramites_online/Padrones_model');
        $this->load->model('tramites_online/Pases_model');
        $this->load->model('tramites_online/Pasos_model');
        $this->load->model('tramites_online/Procesos_model');
        $this->load->model('tramites_online/Tramites_model');
        $this->load->model('tramites_online/Tramites_padrones_model');
        $this->load->model('Personas_model');
        $this->load->model('Oro_model');
        $this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
        $this->grupos_admin = array('admin', 'tramites_online_admin', 'tramites_online_consulta_general');
        $this->grupos_publico = array('tramites_online_publico');
        $this->grupos_area = array('tramites_online_area');
        $this->grupos_solo_consulta = array('tramites_online_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * Bandeja de entrada de la parte privada
     */
    public function bandeja_entrada()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 4, 'class' => 'dt-body-right'),
                array('label' => 'Proceso', 'data' => 'proceso', 'width' => 14),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Persona', 'data' => 'persona', 'width' => 20),
                array('label' => 'Ubicación', 'data' => 'oficina', 'width' => 14),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 14),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'tramites_online/tramites/bandeja_entrada_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
            'extraData' => 'd.tramites_todos = $("#tramites_todos").is(":checked"); '
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        // $data['crear'] = 'agregar';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Bandeja de Entrada';
        $data['title'] = TITLE . ' - Bandeja de Entrada';
        $data['css'][] = 'vendor/switchery/switchery.min.css';
        $data['js'][] = 'vendor/switchery/switchery.min.js';
        $data['js'][] = 'js/tramites_online/base.js';
        $this->load_template('tramites_online/tramites/tramites_listar_bandeja', $data);
    }

    public function bandeja_entrada_data()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramites_todos = $this->input->post('tramites_todos');

        $this->load->helper('tramites_online/datatables_functions_helper');
        $dt = $this->datatables
            ->select("to2_tramites.id, to2_procesos.nombre as proceso, to2_tramites.fecha_inicio, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil,  ')') as persona, (SELECT GROUP_CONCAT(CAST(to2_padrones.padron AS UNSIGNED) SEPARATOR ', ') FROM to2_tramites_padrones JOIN to2_padrones ON to2_padrones.id = to2_tramites_padrones.padron_id WHERE to2_tramites_padrones.pase_id = to2_pases.id ORDER BY to2_padrones.padron) AS padron, COALESCE(to2_oficinas.nombre, 'SOLICITANTE') as oficina, to2_estados.nombre as estado, to2_pases.fecha_inicio as ultimo_mov, to2_estados.id as ult_estado_id, to2_estados.imprimible as estado_imprimible")
            ->from('to2_tramites')
            ->join('to2_procesos', 'to2_procesos.id = to2_tramites.proceso_id', 'left')
            ->join('to2_pases', 'to2_pases.tramite_id = to2_tramites.id', 'left')
            ->join('to2_pases P', 'P.tramite_id = to2_tramites.id AND to2_pases.fecha_inicio < P.fecha_inicio', 'left outer')
            ->join('to2_estados', 'to2_estados.id = to2_pases.estado_destino_id', 'left')
            ->join('to2_oficinas', 'to2_oficinas.id = to2_estados.oficina_id', 'left')
            ->join('to2_iniciadores', 'to2_iniciadores.id = to2_tramites.iniciador_id', 'left')
            ->join('personas', 'personas.id = to2_iniciadores.persona_id', 'left')
            ->join('users', 'users.persona_id = personas.id', 'left')
            ->where('P.id IS NULL');

        if (in_groups($this->grupos_area, $this->grupos)) {
            if ($tramites_todos === 'false') {
                $dt->join('to2_usuarios_oficinas UOE', 'UOE.oficina_id = to2_estados.oficina_id ', 'left')
                    ->where('UOE.user_id', $this->session->userdata('user_id'));

                $dt->where('to2_tramites.fecha_fin IS NULL');

                $dt->add_column('ver', '<a href="tramites_online/tramites/ver/$1/bandeja_entrada" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                    ->add_column('editar', '$1', 'dt_column_tramites_editar(ult_estado_id, id)')
                    ->add_column('imprimir', '$1', 'dt_column_tramites_imprimir(estado_imprimible, id)');
            } else {
                $dt->join('to2_usuarios_oficinas UOE', 'UOE.oficina_id = to2_estados.oficina_id ', 'left')
                    ->join('to2_usuarios_oficinas UOT', 'UOT.oficina_id = to2_procesos.oficina_id ', 'left')
                    ->where('(UOE.user_id', $this->session->userdata('user_id'), FALSE)
                    ->or_where("UOT.user_id = {$this->session->userdata('user_id')})", NULL, FALSE);

                $dt->add_column('ver', '<a href="tramites_online/tramites/ver/$1/bandeja_entrada" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                    ->add_column('editar', '', '')
                    ->add_column('imprimir', '', '');
            }
        } else {
            if ($tramites_todos === 'false') {
                $dt->where('to2_estados.oficina_id IS NOT NULL');
            }

            $dt->add_column('ver', '<a href="tramites_online/tramites/ver/$1/bandeja_entrada" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_tramites_editar(ult_estado_id, id)')
                ->add_column('imprimir', '$1', 'dt_column_tramites_imprimir(estado_imprimible, id)');
        }

        echo $dt->generate();
    }


    /**
     * Bandeja de entrada de la parte publica
     */
    public function bandeja_entrada_publico()
    {
        if (!in_groups($this->grupos_publico, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 4, 'class' => 'dt-body-right'),
                array('label' => 'Proceso', 'data' => 'proceso', 'width' => 14),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Ubicación', 'data' => 'oficina', 'width' => 26),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 24),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'tramites_online/tramites/bandeja_entrada_publico_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
            'extraData' => 'd.tramites_todos = $("#tramites_todos").is(":checked"); '
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['crear'] = 'agregar';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Bandeja de Entrada';
        $data['title'] = TITLE . ' - Bandeja de Entrada';
        $data['css'][] = 'vendor/switchery/switchery.min.css';
        $data['js'][] = 'vendor/switchery/switchery.min.js';
        $data['js'][] = 'js/tramites_online/base.js';
        $this->load_template('tramites_online/tramites/tramites_listar_bandeja_publico', $data);
    }

    public function bandeja_entrada_publico_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramites_todos = $this->input->post('tramites_todos');

        $this->load->helper('tramites_online/datatables_functions_helper');
        $dt = $this->datatables
            ->select("to2_tramites.id, to2_procesos.nombre as proceso, to2_tramites.fecha_inicio, (SELECT GROUP_CONCAT(CAST(to2_padrones.padron AS UNSIGNED) SEPARATOR ', ') FROM to2_tramites_padrones JOIN to2_padrones ON to2_padrones.id = to2_tramites_padrones.padron_id WHERE to2_tramites_padrones.pase_id = to2_pases.id ORDER BY to2_padrones.padron) AS padron, COALESCE(to2_oficinas.nombre, 'SOLICITANTE') as oficina, to2_estados.nombre as estado, to2_pases.fecha_inicio as ultimo_mov, to2_estados.id as ult_estado_id, to2_estados.imprimible as estado_imprimible")
            ->from('to2_tramites')
            ->join('to2_procesos', 'to2_procesos.id = to2_tramites.proceso_id', 'left')
            ->join('to2_pases', 'to2_pases.tramite_id = to2_tramites.id', 'left')
            ->join('to2_pases P', 'P.tramite_id = to2_tramites.id AND to2_pases.fecha_inicio < P.fecha_inicio', 'left outer')
            ->join('to2_estados', 'to2_estados.id = to2_pases.estado_destino_id', 'left')
            ->join('to2_oficinas', 'to2_oficinas.id = to2_estados.oficina_id', 'left')
            ->join('to2_iniciadores', 'to2_iniciadores.id = to2_tramites.iniciador_id', 'left')
            ->join('personas', 'personas.id = to2_iniciadores.persona_id', 'left')
            ->join('users', 'users.persona_id = personas.id', 'left')
            ->where('P.id IS NULL')
            ->where('users.id', $this->session->userdata('user_id'));


        if ($tramites_todos === 'false') {
            $dt->where('to2_estados.oficina_id IS NULL');
            $dt->add_column('ver', '<a href="tramites_online/tramites/ver/$1/bandeja_entrada_publico" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_tramites_editar(ult_estado_id, id)')
                ->add_column('imprimir', '$1', 'dt_column_tramites_imprimir(estado_imprimible, id)');

                $dt->where('to2_tramites.fecha_fin IS NULL');
        } else {
            $dt->add_column('ver', '<a href="tramites_online/tramites/ver/$1/bandeja_entrada_publico" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '', '')
                ->add_column('imprimir', '$1', 'dt_column_tramites_imprimir(estado_imprimible, id)');

        }
      
        echo $dt->generate();
    }

    public function modal_iniciar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            return $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        // BUSCA EL INICIADOR (PERSONA) ASOCIADA AL USUARIO ACTUAL
        $persona = $this->Iniciadores_model->get(array(
            'select' => array('to2_iniciadores.tipo_id'),
            'join' => array(
                array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array('users.id = ' . $this->session->userdata('user_id'))
        ));
        if (empty($persona)) {
            show_error('No se encontró el Iniciador', 500, 'Registro no encontrado');
        }

        // BUSCA LAS OFICINAS CON PROCESOS DISPONIBLES (QUE SEAN PUBLICOS Y CORRESPONDAN AL TIPO DE INICIADOR)
        $this->array_oficina_control = $array_oficina = $this->get_array('Oficinas', 'nombre', 'id',
            array(
                'join' => array(
                    array('to2_procesos', 'to2_procesos.oficina_id = to2_oficinas.id', 'left'),
                    array('to2_procesos_iniciadores', "to2_procesos_iniciadores.proceso_id = to2_procesos.id AND to2_procesos_iniciadores.iniciador_tipo_id = {$persona[0]->tipo_id}")
                ),
                'where' => array(
                    array('column' => 'to2_procesos.visibilidad', 'value' => 'Público')
                ),
                'group_by' => 'to2_oficinas.id'
            )
        );

        // BUSCA PROCESOS DISPONIBLES PARA LA OFICINA (QUE SEAN PUBLICOS Y CORRESPONDAN AL TIPO DE INICIADOR)
        if ($this->input->post('oficina')) {
            $this->array_proceso_control = $array_proceso = $this->get_array('Procesos', 'nombre', 'id',
                array(
                    'join' => array(
                        array('to2_procesos_iniciadores', "to2_procesos_iniciadores.proceso_id = to2_procesos.id AND to2_procesos_iniciadores.iniciador_tipo_id = {$persona[0]->tipo_id}")
                    ),
                    'where' => array(
                        array('column' => 'to2_procesos.visibilidad', 'value' => 'Público'),
                        array('column' => 'to2_procesos.oficina_id', 'value' => $this->input->post('oficina'))
                    )
                )
            );
        } else {
            $this->array_proceso_control = $array_proceso = array();
        }

        $this->set_model_validation_rules($this->Tramites_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE) {
            $proceso = $this->input->post('proceso');
            redirect("tramites_online/tramites/agregar/$proceso", 'refresh');
        } else {
            $this->session->set_flashdata('error', $this->Tramites_model->get_msg());
        }

        $this->Tramites_model->fields['oficina']['array'] = $array_oficina;
        $this->Tramites_model->fields['proceso']['label'] = 'Trámite';
        $this->Tramites_model->fields['proceso']['array'] = $array_proceso;
        $data['fields'] = $this->build_fields($this->Tramites_model->fields);
        $data['txt_btn'] = 'Iniciar';
        $data['title'] = 'Iniciar Trámite';
        $this->load->view('tramites_online/tramites/tramites_modal_iniciar', $data);
    }

    public function agregar($proceso_id)
    {
        if (!in_groups($this->grupos_publico, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('tramites_online/tramites/listar_publico', 'refresh');
        }

        // BUSCA EL INICIADOR (PERSONA) ASOCIADA AL USUARIO ACTUAL
        $persona = $this->Iniciadores_model->get(array(
            'select' => array('to2_iniciadores.id as iniciador', 'to2_iniciadores.tipo_id', 'personas.id', 'personas.cuil', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email'),
            'join' => array(
                array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT'),
                array('domicilios', 'domicilios.id = personas.domicilio_id', 'LEFT'),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array('users.id = ' . $this->session->userdata('user_id'))
        ));

        if (empty($persona)) {
            show_error('No se encontró el Iniciador', 500, 'Registro no encontrado');
        }

        // BUSCA EL PROCESO PARA VERIFICAR SI EL INICIADOR TIENE PERMISOS
        $proceso = $this->get_array('Procesos', 'nombre', 'id',
            array(
                'join' => array(
                    array('to2_procesos_iniciadores', "to2_procesos_iniciadores.proceso_id = to2_procesos.id AND to2_procesos_iniciadores.iniciador_tipo_id = {$persona[0]->tipo_id}")
                ),
                'where' => array(
                    array('column' => 'to2_procesos.visibilidad', 'value' => 'Público')
                )
            )
        );

        if (empty($proceso)) {
            show_error('No tiene permisos para iniciar este Trámite', 500, 'Registro no encontrado');
        }

        // BUSCA ESTADO INICIAL PARA EL PROCESO
        $estados = $this->Estados_model->get(
            array(
                'select' => array('to2_estados.id', 'to2_estados_secuencias.estado_posterior_id as estado_posterior'),
                'join' => array(
                    array('to2_estados_secuencias', 'to2_estados_secuencias.estado_id = to2_estados.id')
                ),
                'proceso_id' => $proceso_id,
                'inicial' => 'SI'
            )
        );
        if (empty($estados)) {
            show_error('No se encontró el Estado Inicial', 500, 'Registro no encontrado');
        }
        $estado_inicial = $estados[0];

        // BUSCA LOS PASOS Y FORMULARIOS NECESARIOS PARA EL ESTADO INICIAL
        $pasos = $this->Pasos_model->get(array(
                'select' => array('to2_formularios.nombre', 'to2_formularios.descripcion', 'to2_pasos.orden', 'to2_pasos.modo', 'to2_pasos.regla', 'to2_pasos.padron', 'to2_pasos.formulario_id', 'to2_pasos.mensaje'),
                'join' => array(
                    array('to2_formularios', 'to2_formularios.id = to2_pasos.formulario_id', 'left')
                ),
                'estado_id' => $estado_inicial->id,
                'sort_by' => 'orden')
        );
        if (empty($pasos)) {
            show_error('No se encontraron Pasos', 500, 'Registro no encontrado');
        }

        foreach ($pasos as $Paso) {
            // DUPLICACION DE LOS FIELDS DE UN FORM
            if ($Paso->regla === 'Multiple') {
                if (!empty($this->input->post("cant_{$Paso->orden}"))) {
                    $cant_[$Paso->orden] = $this->input->post("cant_{$Paso->orden}");
                } else {
                    $cant_[$Paso->orden] = 1;
                }
            } else {
                $cant_[$Paso->orden] = 1;
            }

            // TODO: Manejar distintas posiblidades para el enum padron
            if ($Paso->padron === 'Obligatorio') {
                // CREA MODELO PARA EL PADRON
                $fake_models[$Paso->orden] = new stdClass();
                $fake_models[$Paso->orden]->nombre = 'Inmueble';
                $fake_models[$Paso->orden]->subtitulo = 'Datos del inmueble';
                $fake_models[$Paso->orden]->regla = $Paso->regla;
                $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                $fake_models[$Paso->orden]->allFields = new stdClass();
                for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                    $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                    $fake_models[$Paso->orden]->allFields->{$i}->fields = array(
                        "{$Paso->orden}_nomenclatura_{$i}" => array('label' => 'Nomenclatura', 'type' => 'natural', 'maxlength' => '20', 'minlength' => '20', 'extra_button' => 'Buscar', 'extra_button_click' => "buscar_inmueble(this);", 'required' => TRUE),
                        "{$Paso->orden}_padron_{$i}" => array('label' => 'Padrón Municipal', 'maxlength' => '20', 'readonly' => TRUE, 'required' => TRUE),
                        "{$Paso->orden}_tit_dni_{$i}" => array('label' => 'Documento Titular', 'maxlength' => '20', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_apellido_{$i}" => array('label' => 'Apellido Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_nombre_{$i}" => array('label' => 'Nombre Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_sup_terreno_{$i}" => array('label' => 'Superficie Terreno', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_calle_{$i}" => array('label' => 'Calle', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_distrito_{$i}" => array('label' => 'Distrito', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_zona_urb_{$i}" => array('label' => 'Zona Urbanística', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_ordenanza_{$i}" => array('label' => 'Ordenanza', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_deuda_{$i}" => array('label' => 'Deuda', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_consulta_{$i}" => array('label' => 'Fecha Consulta', 'type' => 'date', 'readonly' => TRUE),
                        "{$Paso->orden}_comprobante_{$i}" => array('label' => 'Comprobante Pago', 'type' => 'file')
                    );
                    $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});
                }
            } else {
                $campos = $this->Campos_model->get(array('formulario_id' => $Paso->formulario_id, 'sort_by' => 'posicion'));
                if (!empty($campos)) {
                    // CREA MODELO PARA EL FORMULARIO
                    $fake_models[$Paso->orden] = new stdClass();
                    $fake_models[$Paso->orden]->nombre = $Paso->nombre;
                    $fake_models[$Paso->orden]->subtitulo = $Paso->descripcion;
                    $fake_models[$Paso->orden]->regla = $Paso->regla;
                    $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                    $fake_models[$Paso->orden]->allFields = new stdClass();
                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                        $fake_models[$Paso->orden]->allFields->{$i}->fields = [];
                    }

                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        foreach ($campos as $Campo) {
                            // TODO: Manejo de validaciones (required, maxlength, etc)
                            switch ($Campo->tipo) {
                                case 'combo':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'input_type' => $Campo->tipo, 'type' => 'bselect', 'required' => TRUE];
                                    if(!empty($Campo->funcion)){
                                        $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['onchange'] = $Campo->funcion;
                                    }
                                    $opciones_tmp = explode("|", $Campo->opciones);
                                    $opciones = array();
                                    if (!empty($opciones_tmp)) {
                                        foreach ($opciones_tmp as $Opcion) {
                                            $opciones[$Opcion] = $Opcion;
                                        }
                                    }
                                    $this->{"array_campo_{$Campo->id}_{$i}_control"} = ${"array_campo_{$Campo->id}_{$i}"} = $opciones;
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['array'] = ${"array_campo_{$Campo->id}_{$i}"};
                                    break;
                                case 'input':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'maxlength' => '50', 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    break;
                                case 'textarea':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    break;
                                case 'file':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'maxlength' => '50', 'required' => $Campo->obligatorio ? TRUE : FALSE];
                                    break;
                                case 'h3':
                                case 'h4':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'value' => $Campo->etiqueta];
                                    break;
                                default:
                                    break;
                            }
                        }
                        $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});
                    }
                }
            }
        }


        if (isset($_POST) && !empty($_POST)) {
            // Agregamos los nombres de los archivos al post para la validacion
            if (!empty($_FILES)) {
                foreach ($_FILES as $k => $v) {
                    $_POST[$k] = $v['name'];
                }
            }


            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE) {

                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;

                // CREA EL TRAMITE
                $trans_ok &= $this->Tramites_model->create(
                    array(
                        'proceso_id' => $proceso_id,
                        'fecha_inicio' => $fecha->format('Y-m-d H:i:s'),
                        'iniciador_id' => $persona[0]->iniciador
                    ), FALSE);

                $tramite_id = $this->Tramites_model->get_row_id();

                // CREA EL PASE
                $trans_ok &= $this->Pases_model->create(
                    array(
                        'tramite_id' => $tramite_id,
                        'estado_origen_id' => $estado_inicial->id,
                        'estado_destino_id' => $estado_inicial->estado_posterior,
                        'fecha_inicio' => $fecha->format('Y-m-d H:i:s'),
                        'usuario_origen' => $this->session->userdata('user_id')
                    ), FALSE);

                $pase_id = $this->Pases_model->get_row_id();

                // GUARDA TODA LA INFO INGRESADA EN LOS DISTINTOS PASOS
                foreach ($pasos as $Paso) {
                    // SI TIENE PADRON ES UN CASO ESPECIAL
                    if ($Paso->padron === 'Obligatorio') {
                        for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                            $padron_id = NULL;
                            $tramite_padron_id = NULL;
                            $padron = $this->Padrones_model->get(array('nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i")));
                            if (empty($padron)) {
                                $trans_ok &= $this->Padrones_model->create(
                                    array(
                                        'codigo' => 1,
                                        'padron' => $this->input->post("{$Paso->orden}_padron_$i"),
                                        'nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i"),
                                        'tit_dni' => $this->input->post("{$Paso->orden}_tit_dni_$i"),
                                        'tit_apellido' => $this->input->post("{$Paso->orden}_tit_apellido_$i"),
                                        'tit_nombre' => $this->input->post("{$Paso->orden}_tit_nombre_$i")
                                    ), FALSE);

                                $padron_id = $this->Padrones_model->get_row_id();
                            } else {
                                $padron_id = $padron[0]->id;
                            }

                            if (!empty($padron_id)) {
                                $trans_ok &= $this->Tramites_padrones_model->create(
                                    array(
                                        'pase_id' => $pase_id,
                                        'repeticion' => $i,
                                        'padron_id' => $padron_id,
                                        'consulta' => $this->get_datetime_sql("{$Paso->orden}_consulta_$i")
                                    ), FALSE);

                                $tramite_padron_id = $this->Tramites_padrones_model->get_row_id();
                            }

                            if (!empty($tramite_padron_id)) {
                                $uploads_padrones = array();
                                if (!empty($_FILES)) {
                                    $config_adjuntos['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($tramite_id, 6, "0", STR_PAD_LEFT) . "/";
                                    if (!file_exists($config_adjuntos['upload_path'])) {
                                        mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                                    }
                                    $config_adjuntos['encrypt_name'] = TRUE;
                                    $config_adjuntos['file_ext_tolower'] = TRUE;
                                    $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx|dwg|dxf|dwf|zip|rar|application/octet-stream';
                                    $config_adjuntos['max_size'] = 8192;
                                    $this->load->library('upload', $config_adjuntos);

                                    foreach ($_FILES as $id => $file) {
                                        // PASAN SOLO LOS Comprobantes de Padrones
                                        if ($id === "{$Paso->orden}_comprobante_$i") {
                                            if (!empty($file['name'])) {
                                                if (!$this->upload->do_upload($id)) {
                                                    $error_msg_file = $this->upload->display_errors();
                                                    $trans_ok = FALSE;
                                                } else {
                                                    $uploads_padrones[$id] = $this->upload->data();
                                                }
                                            }
                                        }
                                    }

                                    if ($trans_ok) {
                                        if (!empty($uploads_padrones)) {
                                            foreach ($uploads_padrones as $key => $Upload) {
                                                $trans_ok &= $this->Adjuntos_model->create(
                                                    array(
                                                        'tipo_id' => 1, // 1 = Adjunto generico (HC)
                                                        'nombre' => $Upload['file_name'],
                                                        'ruta' => $config_adjuntos['upload_path'],
                                                        'tamanio' => round($Upload['file_size'], 2),
                                                        'hash' => md5_file($config_adjuntos['upload_path'] . $Upload['file_name']),
                                                        'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                                        'usuario_subida' => $this->session->userdata('user_id')
                                                    ), FALSE);

                                                $adjunto_id = $this->Adjuntos_model->get_row_id();

                                                $trans_ok &= $this->Tramites_padrones_model->update(
                                                    array(
                                                        'id' => $tramite_padron_id,
                                                        'adjunto_id' => $adjunto_id
                                                    ), FALSE);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                foreach ($_POST as $id => $valor) {
                    $post_name = explode('_', $id, 3);
                    // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                    if ($post_name[0] === 'campo' && is_numeric($post_name[1])) {
                        $trans_ok &= $this->Datos_model->create(
                            array(
                                'pase_id' => $pase_id,
                                'campo_id' => $post_name[1],
                                'repeticion' => $post_name[2],
                                'valor' => $this->input->post($id)
                            ), FALSE);
                    }
                }

                $uploads = array();
                if (!empty($_FILES)) {
                    $config_adjuntos['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($tramite_id, 6, "0", STR_PAD_LEFT) . "/";
                    if (!file_exists($config_adjuntos['upload_path'])) {
                        mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                    }
                    $config_adjuntos['encrypt_name'] = TRUE;
                    $config_adjuntos['file_ext_tolower'] = TRUE;
                    $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx|dwg|dxf|dwf|zip|rar|application/octet-stream';

                    $config_adjuntos['max_size'] = 8192;
                    $this->load->library('upload', $config_adjuntos);

                    foreach ($_FILES as $id => $file) {
                        $file_name = explode('_', $id, 3);
                        // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                        if ($file_name[0] === 'campo' && is_numeric($file_name[1])) {

                            if (!empty($file['name'])) {
                                if (!$this->upload->do_upload($id)) {
                                    $error_msg_file = $this->upload->display_errors();
                                    $trans_ok = FALSE;
                                } else {
                                    $uploads[$id] = $this->upload->data();
                                }
                            }
                        }
                    }

                    if ($trans_ok) {
                        if (!empty($uploads)) {
                            foreach ($uploads as $key => $Upload) {
                                $campo = explode('_', $key, 3);
                                $trans_ok &= $this->Adjuntos_model->create(
                                    array(
                                        'tipo_id' => 1, // 1 = Adjunto generico (HC)
                                        'nombre' => $Upload['file_name'],
                                        'ruta' => $config_adjuntos['upload_path'],
                                        'tamanio' => round($Upload['file_size'], 2),
                                        'hash' => md5_file($config_adjuntos['upload_path'] . $Upload['file_name']),
                                        'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                        'usuario_subida' => $this->session->userdata('user_id')
                                    ), FALSE);

                                $adjunto_id = $this->Adjuntos_model->get_row_id();

                                $trans_ok &= $this->Datos_model->create(
                                    array(
                                        'pase_id' => $pase_id,
                                        'campo_id' => $campo[1],
                                        'repeticion' => $campo[2],
                                        'adjunto_id' => $adjunto_id
                                    ), FALSE);
                            }
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    // TODO: Hacer acciones genericas
                    //$this->send_email('tramites_online/email/tramites_iniciado', 'Trámite Iniciada', $tramite_tipo->email_responsable, array('tramite' => $tramite_id));
                    $this->session->set_flashdata('message', $this->Tramites_model->get_msg());
                    if (in_groups($this->grupos_area, $this->grupos)) {
                        redirect('tramites_online/tramites/bandeja_entrada', 'refresh');
                    } else {
                        redirect('tramites_online/tramites/bandeja_entrada_publico', 'refresh');
                    }
                } else {
                    $this->db->trans_rollback();
                    // BORRA ARCHIVOS SI FALLO ALGO
                    if (!empty($uploads)) {
                        foreach ($uploads as $Upload) {
                            unlink($config_adjuntos['upload_path'] . $Upload['file_name']);
                        }
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Padrones_model->get_error()) {
                        $error_msg .= $this->Padrones_model->get_error();
                    }
                    if ($this->Tramites_padrones_model->get_error()) {
                        $error_msg .= $this->Tramites_padrones_model->get_error();
                    }
                    if ($this->Tramites_model->get_error()) {
                        $error_msg .= $this->Tramites_model->get_error();
                    }
                    if ($this->Datos_model->get_error()) {
                        $error_msg .= $this->Datos_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error()) {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                    if ($this->Pases_model->get_error()) {
                        $error_msg .= $this->Pases_model->get_error();
                    }
                }
            }
        }
        if (!empty($error_msg_file)) {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        if (!empty($fake_models)) {
            foreach ($fake_models as $paso_id => $paso) {
                $data['fields_group'][$paso_id]['nombre'] = $paso->nombre;
                $data['fields_group'][$paso_id]['subtitulo'] = $paso->subtitulo;
                $data['fields_group'][$paso_id]['regla'] = $paso->regla;
                $data['fields_group'][$paso_id]['mensaje'] = $paso->mensaje;
                foreach ($paso->allFields as $array_fields) {
                    $data['fields_group'][$paso_id]['allFields'][] = $this->build_fields($array_fields->fields);
                }
            }
        }
        if (in_groups($this->grupos_publico, $this->grupos))
        {
            $data['tramites_frecuentes_data'] = $this->tramites_frecuentes_data(); 
        }

        $data['back_url'] = 'bandeja_entrada_publico';
        $data['txt_btn'] = 'Iniciar Trámite';
        $data['title_view'] = 'Iniciar Trámite';
        $data['title'] = TITLE . ' - Iniciar Trámite';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard.min.css';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard_theme_arrows.min.css';
        $data['js'][] = 'vendor/smart-wizard/js/jquery.smartWizard.min.js';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['js'][] = 'vendor/bootstrap-validator/validator.min.js';
        $data['js'][] = 'vendor/tinymce/tinymce.min.js';
        $data['js'][] = 'vendor/tinymce/langs/es_AR.js';
        $data['js'][] = 'js/tramites_online/base.js';
        $this->load_template('tramites_online/tramites/tramites_alta', $data);

        
        
    }

    public function revisar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite)) {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        // Tramite finalizado
        if (!empty($tramite->fecha_fin)) {
            $this->session->set_flashdata('message', "<br>El tramite finalizo el " . date_format(new DateTime($tramite->fecha_fin), 'd/m/Y'));
            redirect('tramites_online/tramites/ver/' . $tramite->id . '/bandeja_entrada', 'refresh');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Personas_model->get_user_id($tramite->persona_id) !== $this->session->userdata('user_id')) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos)) {
            $data['grupo'] = 'admin';
        } elseif (in_groups($this->grupos_area, $this->grupos)) {
            $data['grupo'] = 'area';
        } else {
            $data['grupo'] = 'publico';
        }

        $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), $data['grupo']);
        if (empty($ultimo_pase->p_id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        //$this->dump($ultimo_pase);

        if ($tramite->editable) {
            redirect('tramites_online/tramites/editar/' . $tramite->id, 'refresh');
        }


        $fake_model_pase = new stdClass();
        $fake_model_pase->fields = array(
            'destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );

        $estados_posteriores = $this->Estados_secuencias_model->get(array(
            'estado_id' => $ultimo_pase->ed_id,
            'join' => array(
                array('to2_estados', 'to2_estados.id = to2_estados_secuencias.estado_posterior_id', 'LEFT', 'to2_estados.nombre as estado_posterior, to2_estados.final as final')
            )
        ));

        if (empty($estados_posteriores)) {
            show_error('No se encontró el Estado', 500, 'Registro no encontrado');
        }
        $array_destino = array();
        foreach ($estados_posteriores as $Estado) {
            $array_destino[$Estado->estado_posterior_id] = array('opciones' => $Estado->estado_posterior, 'icono' => $Estado->icono, 'final' => $Estado->final);
            $array_destino_cont[$Estado->estado_posterior_id] = $Estado->estado_posterior;
        }
        $this->array_destino_control = $array_destino_cont;

        // BUSCA LOS PASOS Y FORMULARIOS NECESARIOS PARA EL ESTADO ACTUAl
        $pasos = $this->Pasos_model->get(array(
                'select' => array('to2_formularios.nombre', 'to2_formularios.descripcion', 'to2_pasos.orden', 'to2_pasos.modo', 'to2_pasos.regla', 'to2_pasos.padron', 'to2_pasos.formulario_id', 'to2_pasos.mensaje'),
                'join' => array(
                    array('to2_formularios', 'to2_formularios.id = to2_pasos.formulario_id', 'left')
                ),
                'estado_id' => $ultimo_pase->ed_id,
                'sort_by' => 'orden')
        );
        if (empty($pasos)) {
            show_error('No se encontraron Pasos', 500, 'Registro no encontrado');
        }

        foreach ($pasos as $Paso) {
            // DUPLICACION DE LOS FIELDS DE UN FORM
            if ($Paso->regla === 'Multiple') {
                if (!empty($this->input->post("cant_{$Paso->orden}"))) {
                    $cant_[$Paso->orden] = $this->input->post("cant_{$Paso->orden}");
                } else {
                    $cant_[$Paso->orden] = 1;
                }
            } else {
                $cant_[$Paso->orden] = 1;
            }

            // TODO: Manejar distintas posiblidades para el enum padron
            if ($Paso->padron === 'Obligatorio') {
                // CREA MODELO PARA EL PADRON
                $fake_models[$Paso->orden] = new stdClass();
                $fake_models[$Paso->orden]->nombre = 'Inmueble';
                $fake_models[$Paso->orden]->subtitulo = 'Datos del inmueble';
                $fake_models[$Paso->orden]->regla = $Paso->regla;
                $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                $fake_models[$Paso->orden]->allFields = new stdClass();
                for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                    $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                    $fake_models[$Paso->orden]->allFields->{$i}->fields = array(
                        "{$Paso->orden}_nomenclatura_{$i}" => array('label' => 'Nomenclatura', 'type' => 'natural', 'maxlength' => '20', 'minlength' => '20', 'extra_button' => 'Buscar', 'extra_button_click' => "buscar_inmueble(this);", 'required' => TRUE),
                        "{$Paso->orden}_padron_{$i}" => array('label' => 'Padrón Municipal', 'maxlength' => '20', 'readonly' => TRUE, 'required' => TRUE),
                        "{$Paso->orden}_tit_dni_{$i}" => array('label' => 'Documento Titular', 'maxlength' => '20', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_apellido_{$i}" => array('label' => 'Apellido Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_nombre_{$i}" => array('label' => 'Nombre Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_sup_terreno_{$i}" => array('label' => 'Superficie Terreno', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_calle_{$i}" => array('label' => 'Calle', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_distrito_{$i}" => array('label' => 'Distrito', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_zona_urb_{$i}" => array('label' => 'Zona Urbanística', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_ordenanza_{$i}" => array('label' => 'Ordenanza', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_deuda_{$i}" => array('label' => 'Deuda', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_consulta_{$i}" => array('label' => 'Fecha Consulta', 'type' => 'date', 'readonly' => TRUE)
                    );
                    $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});
                }
            } else {
                $campos = $this->Campos_model->get(array('formulario_id' => $Paso->formulario_id, 'sort_by' => 'posicion'));
                if (!empty($campos)) {
                    // CREA MODELO PARA EL FORMULARIO
                    $fake_models[$Paso->orden] = new stdClass();
                    $fake_models[$Paso->orden]->nombre = $Paso->nombre;
                    $fake_models[$Paso->orden]->subtitulo = $Paso->descripcion;
                    $fake_models[$Paso->orden]->regla = $Paso->regla;
                    $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                    $fake_models[$Paso->orden]->allFields = new stdClass();
                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                        $fake_models[$Paso->orden]->allFields->{$i}->fields = [];
                    }

                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        foreach ($campos as $Campo) {
                            // TODO: Manejo de validaciones (required, maxlength, etc)
                            switch ($Campo->tipo) {
                                case 'combo':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'input_type' => $Campo->tipo, 'type' => 'bselect', 'required' => $Campo->obligatorio ? TRUE : FALSE];
                                    if(!empty($Campo->funcion)){
                                        $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['onchange'] = $Campo->funcion;
                                    }
                                    $opciones_tmp = explode("|", $Campo->opciones);
                                    $opciones = array();
                                    if (!empty($opciones_tmp)) {
                                        foreach ($opciones_tmp as $Opcion) {
                                            $opciones[$Opcion] = $Opcion;
                                        }
                                    }
                                    $this->{"array_campo_{$Campo->id}_{$i}_control"} = ${"array_campo_{$Campo->id}_{$i}"} = $opciones;
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['array'] = ${"array_campo_{$Campo->id}_{$i}"};
                                    break;
                                case 'input':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'maxlength' => '50', 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    break;
                                case 'checkbox':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE];
                                    break;
                                case 'file':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'value'=>0, 'required' => $Campo->obligatorio ? TRUE : FALSE];
                                    break;
                                case 'textarea':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    break;
                                case 'h3':
                                case 'h4':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'value' => $Campo->etiqueta];
                                    break;
                                default:
                                    break;
                            }
                        }
                        $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});
                    }
                }
            }
        }


        $this->set_model_validation_rules($fake_model_pase);
        if (isset($_POST) && !empty($_POST)) {

            // Agregamos los nombres de los archivos al post para la validacion
            if (!empty($_FILES)) {
                foreach ($_FILES as $k => $v) {
                    $_POST[$k] = $v['name'];
                }
            }
            //$this->dump($_POST);
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = NULL;
            if ($this->form_validation->run() === TRUE && empty($error_msg)) {
                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;


                $trans_ok &= $this->Pases_model->update(
                    array(
                        'id' => $ultimo_pase->p_id,
                        'usuario_destino' => $this->session->userdata('user_id')
                    ), FALSE);

                $trans_ok &= $this->Pases_model->create(
                    array(
                        'tramite_id' => $this->input->post('id'),
                        'estado_origen_id' => $ultimo_pase->ed_id,
                        'estado_destino_id' => $this->input->post('destino'),
                        'fecha_inicio' => $fecha->format('Y-m-d H:i:s'),
                        'usuario_origen' => $this->session->userdata('user_id'),
                        'observaciones' => $this->input->post('observaciones')
                    ), FALSE);

                $pase_id = $this->Pases_model->get_row_id();

                // SI EL ESTADO DESTINO ESTA MARCADO COMO FINAL CERRAR EL TRAMITE
                if ($array_destino[$this->input->post('destino')]['final'] === 'SI') {
                    $trans_ok &= $this->Tramites_model->update(array(
                        'id' => $this->input->post('id'),
                        'fecha_fin' => $fecha->format('Y-m-d H:i:s')), FALSE);
                }

                // GUARDA TODA LA INFO INGRESADA EN LOS DISTINTOS PASOS
                foreach ($pasos as $Paso) {
                    // SI TIENE PADRON ES UN CASO ESPECIAL
                    if ($Paso->padron === 'Obligatorio') {
                        for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                            $padron_id = NULL;
                            $padron = $this->Padrones_model->get(array('nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i")));
                            if (empty($padron)) {
                                $trans_ok &= $this->Padrones_model->create(
                                    array(
                                        'codigo' => 1,
                                        'padron' => $this->input->post("{$Paso->orden}_padron_$i"),
                                        'nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i"),
                                        'tit_dni' => $this->input->post("{$Paso->orden}_tit_dni_$i"),
                                        'tit_apellido' => $this->input->post("{$Paso->orden}_tit_apellido_$i"),
                                        'tit_nombre' => $this->input->post("{$Paso->orden}_tit_nombre_$i")
                                    ), FALSE);

                                $padron_id = $this->Padrones_model->get_row_id();
                            } else {
                                $padron_id = $padron[0]->id;
                            }

                            if (!empty($padron_id)) {
                                $trans_ok &= $this->Tramites_padrones_model->create(
                                    array(
                                        'pase_id' => $pase_id,
                                        'repeticion' => $i,
                                        'padron_id' => $padron_id,
                                        'consulta' => $this->get_datetime_sql("{$Paso->orden}_consulta_$i")
                                    ), FALSE);
                            }
                        }
                    }
                }

                foreach ($_POST as $id => $valor) {
                    $post_name = explode('_', $id, 3);
                    // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                    if ($post_name[0] === 'campo' && is_numeric($post_name[1])) {
                        $trans_ok &= $this->Datos_model->create(
                            array(
                                'pase_id' => $pase_id,
                                'campo_id' => $post_name[1],
                                'repeticion' => $post_name[2],
                                'valor' => $this->input->post($id)
                            ), FALSE);
                    }
                }

                $uploads = array();
                if (!empty($_FILES)) {
                    $config_adjuntos['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($tramite->id, 6, "0", STR_PAD_LEFT) . "/";
                    if (!file_exists($config_adjuntos['upload_path'])) {
                        mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                    }
                    $config_adjuntos['encrypt_name'] = TRUE;
                    $config_adjuntos['file_ext_tolower'] = TRUE;
                    $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx|dwg|dxf|dwf|zip|rar|application/octet-stream';
                    $config_adjuntos['max_size'] = 8192;
                    $this->load->library('upload', $config_adjuntos);

                    foreach ($_FILES as $id => $file) {
                        $file_name = explode('_', $id, 3);
                        // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                        if ($file_name[0] === 'campo' && is_numeric($file_name[1])) {

                            if (!empty($file['name'])) {
                                if (!$this->upload->do_upload($id)) {
                                    $error_msg_file = $this->upload->display_errors();
                                    $trans_ok = FALSE;
                                } else {
                                    $uploads[$id] = $this->upload->data();
                                }
                            }
                        }
                    }

                    if ($trans_ok) {
                        if (!empty($uploads)) {
                            foreach ($uploads as $key => $Upload) {
                                $campo = explode('_', $key, 3);
                                $trans_ok &= $this->Adjuntos_model->create(
                                    array(
                                        'tipo_id' => 1, // 1 = Adjunto generico (HC)
                                        'nombre' => $Upload['file_name'],
                                        'ruta' => $config_adjuntos['upload_path'],
                                        'tamanio' => round($Upload['file_size'], 2),
                                        'hash' => md5_file($config_adjuntos['upload_path'] . $Upload['file_name']),
                                        'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                        'usuario_subida' => $this->session->userdata('user_id')
                                    ), FALSE);

                                $adjunto_id = $this->Adjuntos_model->get_row_id();

                                $trans_ok &= $this->Datos_model->create(
                                    array(
                                        'pase_id' => $pase_id,
                                        'campo_id' => $campo[1],
                                        'repeticion' => $campo[2],
                                        'adjunto_id' => $adjunto_id
                                    ), FALSE);
                            }
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Pases_model->get_msg());
                    if (in_groups($this->grupos_publico, $this->grupos)) {
                        redirect('tramites_online/tramites/bandeja_entrada_publico', 'refresh');
                    } else {
                        redirect('tramites_online/tramites/bandeja_entrada', 'refresh');
                    }
                } else {
                    $this->db->trans_rollback();
                    // BORRA ARCHIVOS SI FALLO ALGO
                    if (!empty($uploads)) {
                        foreach ($uploads as $Upload) {
                            unlink($config_adjuntos['upload_path'] . $Upload['file_name']);
                        }
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Pases_model->get_error()) {
                        $error_msg .= $this->Pases_model->get_error();
                    }
                    if ($this->Padrones_model->get_error()) {
                        $error_msg .= $this->Padrones_model->get_error();
                    }
                    if ($this->Tramites_padrones_model->get_error()) {
                        $error_msg .= $this->Tramites_padrones_model->get_error();
                    }
                    if ($this->Tramites_model->get_error()) {
                        $error_msg .= $this->Tramites_model->get_error();
                    }
                    if ($this->Datos_model->get_error()) {
                        $error_msg .= $this->Datos_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error()) {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                }
            }
        }
        if (!empty($error_msg_file)) {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model_pase->fields['destino']['array'] = $array_destino;
        $data['fields_pase'] = $this->build_fields($fake_model_pase->fields);

        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('to2_estados EO', 'EO.id = to2_pases.estado_origen_id', 'LEFT'),
                array('to2_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "CONCAT(EO.nombre, ' (', COALESCE(OO.nombre, ''), ')') as estado_origen"),
                array('to2_estados ED', 'ED.id = to2_pases.estado_destino_id', 'LEFT'),
                array('to2_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "CONCAT(ED.nombre, ' (', COALESCE(OD.nombre, ''), ')') as estado_destino")
            )
        ));


        if (!empty($fake_models)) {
            foreach ($fake_models as $paso_id => $paso) {
                $data['fields_group'][$paso_id]['nombre'] = $paso->nombre;
                $data['fields_group'][$paso_id]['subtitulo'] = $paso->subtitulo;
                $data['fields_group'][$paso_id]['regla'] = $paso->regla;
                $data['fields_group'][$paso_id]['mensaje'] = $paso->mensaje;
                foreach ($paso->allFields as $array_fields) {
                    $data['fields_group'][$paso_id]['allFields'][] = $this->build_fields($array_fields->fields);
                }
            }
        }

        $data['ultimo_pase'] = $ultimo_pase;
        $data['tramite'] = $tramite;
        if (in_groups($this->grupos_publico, $this->grupos)) {
            $data['back_url'] = 'bandeja_entrada_publico';
        } else {
            $data['back_url'] = 'bandeja_entrada';
        }

        $data['txt_btn'] = 'Enviar';
        $data['title_view'] = 'Revisar Trámite';
        $data['title'] = TITLE . ' - Revisar Trámite';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard.min.css';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard_theme_arrows.min.css';
        $data['js'][] = 'vendor/smart-wizard/js/jquery.smartWizard.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['js'][] = 'js/tramites_online/base.js';
        $this->load_template('tramites_online/tramites/tramites_revisar', $data);
    }

    public function ver($id = NULL, $back_url = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite)) {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Personas_model->get_user_id($tramite->persona_id) !== $this->session->userdata('user_id')) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $allow_edit_pases = FALSE;

        if (in_groups($this->grupos_admin, $this->grupos)) {
            $data['grupo'] = 'admin';
            $allow_edit_pases = TRUE;
        } elseif (in_groups($this->grupos_area, $this->grupos)) {
            $data['grupo'] = 'area';

            // TODO: agregar permisos para el dueño del proceso.
            $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), 'area');

            if (empty($ultimo_pase->p_id)) {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            }

            // El dueño del proceso puede habilitar el boton de edicion
            $this->load->model('tramites_online/Usuarios_oficinas_model');
            $usuario_oficina = $this->Usuarios_oficinas_model->get(array('oficina_id' => $tramite->oficina_id, 'user_id' => $this->session->userdata('user_id')));
            if (!empty($usuario_oficina)) {
                $allow_edit_pases = TRUE;
            }

        } else {
            $data['grupo'] = 'publico';
        }


        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('to2_estados EO', 'EO.id = to2_pases.estado_origen_id', 'LEFT', ['EO.editable AS estado_origen_editable', 'EO.oficina_id AS estado_origen_oficina']),
                array('to2_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "CONCAT(EO.nombre, ' (', COALESCE(OO.nombre, ''), ')') as estado_origen"),
                array('to2_estados ED', 'ED.id = to2_pases.estado_destino_id', 'LEFT'),
                array('to2_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "CONCAT(ED.nombre, ' (', COALESCE(OD.nombre, ''), ')') as estado_destino")
            )
        ));
        
        
        $data['estados'] = $this->Estados_model->get(array(
            'select' => array('to2_estados.nombre'),
            'where' => array('to2_estados.proceso_id = ' . $tramite->proceso_id)
        ));
        

        $data['tramite'] = $tramite;
        if (!empty($back_url)) {
            $data['back_url'] = $back_url;
        } else {
            if (in_groups($this->grupos_publico, $this->grupos)) {
                $data['back_url'] = 'listar_publico';
            } else {
                $data['back_url'] = 'listar';
            }
        }

        //$this->dump($data);
        $data['txt_btn'] = NULL;

        //$data['allow_edit_pases'] = (empty($tramite->fecha_fin)) ? $allow_edit_pases : FALSE;
        $data['allow_edit_pases'] = FALSE;

        $data['message'] = $this->session->flashdata('message');

        $data['title_view'] = 'Ver Trámite';
        $data['title'] = TITLE . ' - Ver Trámite';
        $data['js'][] = 'vendor/smart-wizard/js/jquery.smartWizard.min.js';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard.min.css';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard_theme_arrows.min.css';
        $data['js'][] = 'js/tramites_online/base.js';
        $this->load_template('tramites_online/tramites/tramites_ver', $data);
    }

    private function send_email($template, $title, $to, $data)
    {
        if (SIS_EMAIL_MODULO) {
            $this->email->initialize();
            $message = $this->load->view($template, $data, TRUE);
            $this->email->clear(TRUE);
            $this->email->set_mailtype("html");
            $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->email->to($to);
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $title);
            $this->email->message($message);

            if ($this->email->send()) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }

    /**
     * Impresion del tramite una vez que termina
     * @param $id
     * @throws \Mpdf\MpdfException
     */
    public function imprimir_detalle_tramite($id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos, $this->grupos_publico) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), 'admin');

        /*
        if (empty($ultimo_pase->p_id) || $ultimo_pase->final !== 'SI') //Finalizado (HC)
        {
            show_error('No tiene permisos para la acción solicitada.', 500, 'Acción no autorizada');
        }
*/
        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite)) {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        // if (in_groups($this->grupos_publico, $this->grupos)) { //} && $this->Escribanos_model->get_user_id($tramite->escribano_id) !== $this->session->userdata('user_id')) {
        //     show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        // }
        $oficina = $this->Oficinas_model->get_one($tramite->oficina_id);

        // buscar nombre del proceso
//        $proceso = $this->Procesos_model->get_one($tramite->proceso_id);

        $datos = $this->Datos_model->get(
            array(
                'select' => [
                    'to2_pases.id AS pase_id',
                    'to2_pases.fecha_inicio  AS pase_fecha_inicio',
                    'to2_campos.formulario_id AS formulario_id',
                    'to2_campos.posicion AS campo_posicion',
                    'to2_campos.etiqueta AS campo_etiqueta',
                    'to2_campos.tipo AS campo_tipo',
                    'to2_campos.imprimible AS campo_imprimible',
                    'to2_datos.valor',
                    'to2_datos.repeticion',
                    'to2_formularios.nombre AS formulario_nombre',
                    'to2_formularios.orden_impresion AS formulario_orden_impresion',
                ],

                'join' => [
                    [
                        'to2_pases', 'to2_pases.id = to2_datos.pase_id', 'LEFT',
                        [
                            'to2_pases.id AS pase_id',
                            'to2_pases.fecha_inicio AS pase_fecha_incio',
                        ]
                    ],
                    [
                        'to2_campos', 'to2_campos.id = to2_datos.campo_id', 'LEFT',
                        [
                            'to2_campos.id AS campo_id',
                            'to2_campos.posicion AS campo_posicion',
                            'to2_campos.formulario_id AS formulario_id',
                            'to2_campos.etiqueta AS campo_etiqueta',
                            'to2_campos.tipo AS campo_tipo',
                        ]
                    ],
                    [
                        'to2_formularios', 'to2_formularios.id = to2_campos.formulario_id', 'LEFT',
                        [
                            'to2_formularios.nombre AS formulario_nombre',
                            'to2_formularios.orden_impresion AS formulario_orden_impresion',
                        ]
                    ],
                ],

                'where' => [
                    [
                        'column' => 'to2_pases.tramite_id',
                        'value' => $id
                    ],
                    [
                        'column' => 'to2_datos.adjunto_id',
                        'value' => NULL
                    ],
                    [
                        'column' => 'to2_formularios.imprimible',
                        'value' => 1
                    ],
                    [
                        'column' => 'to2_campos.imprimible',
                        'value' => 1
                    ],

                ],
                'sort_by' => 'pase_id, campo_posicion'
            )
        );


        // $this->dump($datos);

        $salida = array();
        $formularios = array();

        //      [formu_orden]
        //          formu_nombre
        //          [repeticion]
        //            [posicion]
        //              label
        //              value
        foreach ($datos as $item) {

            // verifica si el formulario_id esta en el array
            // si no esta lo agrega junto con el pase
            if (empty($formularios[$item->formulario_id][$item->repeticion][$item->campo_posicion])) {
                //if(!in_array($item->formulario_id, $formularios)){
                $formularios[$item->formulario_id][$item->repeticion][$item->campo_posicion] = $item->pase_id;
                $agregar_campo = TRUE;
            } else {
                // si esta, controla que el id del pases sea mas grande
                $pase_antiguo = $formularios[$item->formulario_id][$item->repeticion][$item->campo_posicion];

                // el pase es mas nuevo
                if ($pase_antiguo < $item->pase_id) {

                    $agregar_campo = TRUE;
                    $formularios[$item->formulario_id][$item->repeticion][$item->campo_posicion] = $item->pase_id;
                } else {
                    // el pase es mas antiguo
                    $agregar_campo = FALSE;
                }
            }

            if ($agregar_campo) {
                $obj = new stdClass();
                $obj->label = $item->campo_etiqueta;
                $obj->tipo = $item->campo_tipo;
                $obj->value = $item->valor;

                $salida[$item->formulario_orden_impresion]['formulario_nombre'] = $item->formulario_nombre;
                $salida[$item->formulario_orden_impresion][$item->repeticion][$item->campo_posicion] = $obj;
            }

        }

        ksort($salida);
//        $this->dump($salida);


        $padrones = $this->Padrones_model->get(array(
            'join' => [
                [
                    'to2_tramites_padrones', 'to2_tramites_padrones.padron_id = to2_padrones.id', 'LEFT',
                    [
                        'to2_tramites_padrones.pase_id',
                    ]
                ],
                [
                    'to2_pases', 'to2_pases.id = to2_tramites_padrones.pase_id', 'LEFT',
                ],
                [
                    'to2_tramites', 'to2_tramites.id = to2_pases.tramite_id', 'LEFT',
                    [
                        'to2_tramites.id as tramite_id',
                    ]
                ],
            ],
            'where' => [
                [
                    'column' => 'to2_tramites.id',
                    'value' => $id
                ],
            ],
            //   'sort_by' => 'fecha_inicio'
        ));


        // BUSCA EL INICIADOR (PERSONA) ASOCIADA AL USUARIO ACTUAL
        $iniciador = $this->Iniciadores_model->get(array(
            'select' => array('to2_iniciadores.id as iniciador', 'to2_iniciadores.tipo_id', 'personas.id', 'personas.cuil', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email'),
            'join' => array(
                array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT'),
                array('domicilios', 'domicilios.id = personas.domicilio_id', 'LEFT'),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'),
            ),
            'where' => array('to2_iniciadores.id = ' . $tramite->iniciador_id)
        ))[0];

        if (empty($iniciador)) {
            show_error('No se encontró el Iniciador', 500, 'Registro no encontrado');
        }

        //$this->dump($iniciador);

        $data = array(
            'oficina' => $oficina,
            'padrones' => $padrones,
            'tramite' => $tramite,
            'iniciador' => $iniciador,
            'formularios' => $salida
        );

//        $this->dump($data);


        $html = $this->load->view('tramites_online/tramites/tramites_resumen_pdf', $data, TRUE);
        $header = $this->load->view('tramites_online/tramites/tramites_header_pdf', $data, TRUE);
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'format' => 'A4',
            'margin_left' => 6,
            'margin_right' => 6,
            'margin_top' => 50,
            'margin_bottom' => 25,
            'margin_header' => 9,
            'margin_footer' => 9
        ]);
        $mpdf->SetDisplayMode('fullwidth');
        $mpdf->pagenumPrefix = 'Página ';
        $mpdf->SetTitle("Trámite " . $tramite->id);

        $mpdf->setHeader($header);

        $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
        $mpdf->SetFooter("{PAGENO} de {nb}");
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output('tramite_' . $tramite->id . '.pdf', 'I');
    }


    public function enable_edit($id)
    {

        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_publico, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite)) {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_area, $this->grupos)) {
            $this->load->model('tramites_online/Usuarios_oficinas_model');
            $usuario_oficina = $this->Usuarios_oficinas_model->get(array('oficina_id' => $tramite->oficina_id, 'user_id' => $this->session->userdata('user_id')));
            if (empty($usuario_oficina)) {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            }
        }

        $this->db->trans_begin();
        $trans_ok = TRUE;
        $editable = (string)($tramite->editable ? 0 : 1);

        $trans_ok &= $this->Tramites_model->update(
            array(
                'id' => $id,
                'editable' => $editable,

            ), FALSE);

        if ($this->db->trans_status() && $trans_ok) {
            $this->db->trans_commit();
            $this->session->set_flashdata('message', $this->Tramites_model->get_msg());
            redirect('tramites_online/tramites/ver/' . $tramite->id . '/bandeja_entrada', 'refresh');
        } else {
            $this->db->trans_rollback();

            $error_msg = '<br />Se ha producido un error con la base de datos.';
            if ($this->Tramites_model->get_error()) {
                $error_msg .= $this->Tramites_model->get_error();
            }
        }

        $data['error'] = (!empty($error_msg)) ? $error_msg : $this->session->flashdata('error');
        redirect('tramites_online/tramites/ver/' . $tramite->id . '/bandeja_entrada', 'refresh');
    }


    private function dump($var, bool $show_query = FALSE)
    {
        echo "<pre>";
        if ($show_query) {
            print_r($this->db->last_query());
            echo "<br>";
        }
        print_r(($var));
        echo "</pre>";
        exit;
    }


    /**
     * @param $id
     */

    public function editar($id = NULL)
    {

        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite)) {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        // Tramite finalizado
        if (!empty($tramite->fecha_fin)) {
            $this->session->set_flashdata('message', "<br>El tramite finalizo el " . date_format(new DateTime($tramite->fecha_fin), 'd/m/Y'));
            redirect('tramites_online/tramites/ver/' . $tramite->id . '/bandeja_entrada', 'refresh');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Personas_model->get_user_id($tramite->persona_id) !== $this->session->userdata('user_id')) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos)) {
            $data['grupo'] = 'admin';
        } elseif (in_groups($this->grupos_area, $this->grupos)) {
            $data['grupo'] = 'area';
        } else {
            $data['grupo'] = 'publico';
        }

        $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), $data['grupo']);
        if (empty($ultimo_pase->p_id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $pase_id = $ultimo_pase->ed_id;

        $pase = $this->Pases_model->get_one($pase_id);
        if (empty($pase)) {
            return $this->modal_error('No se encontró el Pase', 'Registro no encontrado');
        }

        // El pase es editable?
        if ($pase->estado_origen_editable !== 'SI') {
            return $this->modal_error('El pase no se puede editar ', 'Pase no editable');
        }


        /**
         * Control de edicion para publico y areas
         * si es publico [ oficina no publica || no es suyo el tramite ]
         */
        if (in_groups($this->grupos_publico, $this->grupos)) {
            // Si el usuario es del grupo publico: la oficina no es la publica y el tramite no es de él
            if (!is_null($pase->estado_origen_oficina) || $this->Personas_model->get_user_id($tramite->persona_id) !== $this->session->userdata('user_id')) {
                return $this->modal_error('No puede editar este Pase', 'Pase no publico');
            }
        } else if (in_groups($this->grupos_area, $this->grupos)) {
            // El usuario es del grupo areas, el pase es de su area

            $usuario_oficinas = $this->Usuarios_oficinas_model->get_oficina_id($this->session->userdata('user_id'));
            $este_pase = $this->Pases_model->get(array(
                    'id' => $pase_id,
                    'select' => array('oficina_id'),
                    'join' => array(
                        array('to2_estados', 'to2_estados.id = to2_pases.estado_origen_id', 'left'
                        ),
                        array('to2_oficinas', 'to2_oficinas.id = to2_estados.oficina_id', 'left',
                            [
                                'to2_oficinas.oficina_id'
                            ]
                        ),
                    ),
                )
            );

            if (!in_array($este_pase->oficina_id, $usuario_oficinas)) {
                return $this->modal_error('No puede editar este Pase', 'Pase no es de su area');
            }
        }

        // BUSCA LOS PASOS Y FORMULARIOS NECESARIOS PARA EL ESTADO ACTUAl
        $pasos = $this->Pasos_model->get(array(
                'select' => array('to2_formularios.nombre', 'to2_formularios.descripcion', 'to2_pasos.orden', 'to2_pasos.modo', 'to2_pasos.regla', 'to2_pasos.padron', 'to2_pasos.formulario_id', 'to2_pasos.mensaje'),
                'join' => array(
                    array('to2_formularios', 'to2_formularios.id = to2_pasos.formulario_id', 'left'),
                ),
                'estado_id' => $pase->eo_id,
                'sort_by' => 'orden')
        );
        if (empty($pasos)) {
            show_error('No se encontraron Pasos', 500, 'Registro no encontrado');
        }


        foreach ($pasos as $Paso) {
            // TODO: Manejar distintas posiblidades para el enum padron
            if ($Paso->padron === 'Obligatorio') {
                $datos_padron = $this->Tramites_padrones_model->get(
                    array(
                        'select' => array('to2_tramites_padrones.repeticion', 'to2_padrones.nomenclatura', 'to2_padrones.tit_dni', 'to2_padrones.tit_apellido', 'to2_padrones.tit_nombre', 'to2_tramites_padrones.adjunto_id', 'to2_adjuntos.ruta', 'to2_adjuntos.nombre'),
                        'join' => array(
                            array('to2_padrones', 'to2_tramites_padrones.padron_id = to2_padrones.id', 'left'),
                            array('to2_adjuntos', 'to2_tramites_padrones.adjunto_id = to2_adjuntos.id', 'left')
                        ),
                        'pase_id' => $pase->id
                    )
                );

                $cant_[$Paso->orden] = sizeof($datos_padron);

                $array_datos_padron = array();
                if (!empty($datos_padron)) {
                    foreach ($datos_padron as $Dato) {
                        $array_datos_padron["nomenclatura"][$Dato->repeticion] = !empty($Dato->nomenclatura) ? $Dato->nomenclatura : "";
                        $array_datos_padron["padron"][$Dato->repeticion] = !empty($Dato->padron) ? $Dato->padron : "";
                        $array_datos_padron["tit_dni"][$Dato->repeticion] = !empty($Dato->tit_dni) ? $Dato->tit_dni : "";
                        $array_datos_padron["tit_apellido"][$Dato->repeticion] = !empty($Dato->tit_apellido) ? $Dato->tit_apellido : "";
                        $array_datos_padron["tit_nombre"][$Dato->repeticion] = !empty($Dato->tit_nombre) ? $Dato->tit_nombre : "";
                        $array_datos_padron["comprobante"][$Dato->repeticion] = !empty($Dato->adjunto_id) ? $Dato->ruta . $Dato->nombre : "";
                    }
                }
                //     $this->dump($datos_padron);

                // CREA MODELO PARA EL PADRON
                $fake_models[$Paso->orden] = new stdClass();
                $fake_models[$Paso->orden]->nombre = 'Inmueble';
                $fake_models[$Paso->orden]->subtitulo = 'Datos del inmueble';
                $fake_models[$Paso->orden]->regla = $Paso->regla;
                $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                $fake_models[$Paso->orden]->allFields = new stdClass();

                for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                    $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                    $fake_models[$Paso->orden]->allFields->{$i}->fields = array(
                        "{$Paso->orden}_nomenclatura_{$i}" => array('label' => 'Nomenclatura', 'type' => 'natural', 'maxlength' => '20', 'minlength' => '20', 'extra_button' => 'Buscar', 'extra_button_click' => "buscar_inmueble(this);", 'required' => TRUE),
                        "{$Paso->orden}_padron_{$i}" => array('label' => 'Padrón Municipal', 'maxlength' => '20', 'readonly' => TRUE, 'required' => TRUE),
                        "{$Paso->orden}_tit_dni_{$i}" => array('label' => 'Documento Titular', 'maxlength' => '20', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_apellido_{$i}" => array('label' => 'Apellido Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_nombre_{$i}" => array('label' => 'Nombre Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_sup_terreno_{$i}" => array('label' => 'Superficie Terreno', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_calle_{$i}" => array('label' => 'Calle', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_distrito_{$i}" => array('label' => 'Distrito', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_zona_urb_{$i}" => array('label' => 'Zona Urbanística', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_ordenanza_{$i}" => array('label' => 'Ordenanza', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_deuda_{$i}" => array('label' => 'Deuda', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_consulta_{$i}" => array('label' => 'Fecha Consulta', 'type' => 'date', 'readonly' => TRUE),
                        "{$Paso->orden}_comprobante_{$i}" => array('label' => 'Comprobante Pago', 'type' => 'file', 'form_type' => 'file')
                    );
                    $fake_models[$Paso->orden]->allFields->{$i}->valores = new stdClass();
                    //$this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});

                    // VALORES
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_nomenclatura_{$i}"} = !empty($array_datos_padron["nomenclatura"][$i]) ? $array_datos_padron["nomenclatura"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_padron_{$i}"} = !empty($array_datos_padron["padron"][$i]) ? $array_datos_padron["padron"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_dni_{$i}"} = !empty($array_datos_padron["tit_dni"][$i]) ? $array_datos_padron["tit_dni"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_apellido_{$i}"} = !empty($array_datos_padron["tit_apellido"][$i]) ? $array_datos_padron["tit_apellido"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_nombre_{$i}"} = !empty($array_datos_padron["tit_nombre"][$i]) ? $array_datos_padron["tit_nombre"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_sup_terreno_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_calle_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_distrito_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_zona_urb_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_ordenanza_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_deuda_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_consulta_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_comprobante_{$i}"} = !empty($array_datos_padron["comprobante"][$i]) ? $array_datos_padron["comprobante"][$i] : NULL;
                }
            } else {
                // DUPLICACION DE LOS FIELDS DE UN FORM
                if ($Paso->regla === 'Multiple') {
                    $cantidad = $this->db->query("SELECT COUNT(to2_datos.id) as cantidad "
                        . 'FROM to2_campos '
                        . 'JOIN to2_datos ON to2_datos.campo_id = to2_campos.id AND to2_datos.pase_id = ? '
                        . 'WHERE formulario_id = ? '
                        . 'GROUP BY to2_campos.id ', array($pase->id, $Paso->formulario_id))->row();

                    if (!empty($cantidad->cantidad)) {
                        $cant_[$Paso->orden] = $cantidad->cantidad;
                    } else {
                        $cant_[$Paso->orden] = 1;
                    }
                } else {
                    $cant_[$Paso->orden] = 1;
                }

                $campos = $this->Campos_model->get(
                    array(
                        'select' => array('to2_campos.id', 'to2_campos.etiqueta', 'to2_campos.opciones', 'to2_campos.tipo', 'to2_campos.obligatorio'),
                        'formulario_id' => $Paso->formulario_id,
                        'sort_by' => 'posicion'
                    )
                );

                $datos = $this->Campos_model->get(
                    array(
                        'select' => array('to2_campos.id', 'to2_datos.repeticion', 'to2_datos.valor', 'to2_datos.adjunto_id', 'to2_adjuntos.ruta', 'to2_adjuntos.nombre'),
                        'join' => array(
                            array('to2_datos', "to2_datos.campo_id = to2_campos.id AND to2_datos.pase_id = $pase->id", 'left'),
                            array('to2_adjuntos', "to2_adjuntos.id = to2_datos.adjunto_id", 'left')
                        ),
                        'formulario_id' => $Paso->formulario_id,
                        'sort_by' => 'posicion'
                    )
                );

                $array_datos = array();
                if (!empty($datos)) {
                    foreach ($datos as $Dato) {
                        $array_datos[$Dato->id][$Dato->repeticion] = !empty($Dato->valor) ? $Dato->valor : (!empty($Dato->adjunto_id) ? $Dato->ruta . $Dato->nombre : "");
                    }
                }

                if (!empty($campos)) {
                    // CREA MODELO PARA EL FORMULARIO
                    $fake_models[$Paso->orden] = new stdClass();
                    $fake_models[$Paso->orden]->nombre = $Paso->nombre;
                    $fake_models[$Paso->orden]->subtitulo = $Paso->descripcion;
                    $fake_models[$Paso->orden]->regla = $Paso->regla;
                    $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                    $fake_models[$Paso->orden]->allFields = new stdClass();
                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                        $fake_models[$Paso->orden]->allFields->{$i}->fields = [];
                        $fake_models[$Paso->orden]->allFields->{$i}->valores = new stdClass();
                    }

                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        foreach ($campos as $Campo) {
                            // TODO: Manejo de validaciones (required, maxlength, etc)
                            switch ($Campo->tipo) {
                                case 'combo':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'input_type' => $Campo->tipo, 'type' => 'bselect', 'required' => TRUE];
                                    if(!empty($Campo->funcion)){
                                        $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['onchange'] = $Campo->funcion;
                                    }
                                    $opciones_tmp = explode("|", $Campo->opciones);
                                    $opciones = array();
                                    if (!empty($opciones_tmp)) {
                                        foreach ($opciones_tmp as $Opcion) {
                                            $opciones[$Opcion] = $Opcion;
                                        }
                                    }
                                    $this->{"array_campo_{$Campo->id}_{$i}_control"} = ${"array_campo_{$Campo->id}_{$i}"} = $opciones;
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['array'] = ${"array_campo_{$Campo->id}_{$i}"};
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}_id"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'input':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'maxlength' => '50', 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'file':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'form_type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'textarea':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'h3':
                                case 'h4':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'value' => $Campo->etiqueta];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = $Campo->etiqueta;
                                    break;
                                default:
                                    break;
                            }
                        }
                        $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});
                    }
                }
            }
        }


        $data['fields'] = $this->build_fields($this->Pases_model->fields, $pase, TRUE);
        $data['pase'] = $pase;


        if (!empty($fake_models)) {
            foreach ($fake_models as $paso_id => $paso) {
                $data['fields_group'][$paso_id]['nombre'] = $paso->nombre;
                $data['fields_group'][$paso_id]['subtitulo'] = $paso->subtitulo;
                $data['fields_group'][$paso_id]['regla'] = $paso->regla;
                $data['fields_group'][$paso_id]['mensaje'] = $paso->mensaje;
                foreach ($paso->allFields as $array_fields) {
                    $data['fields_group'][$paso_id]['allFields'][] = $this->build_fields($array_fields->fields, $array_fields->valores, FALSE);
                }
            }
        }


        if (isset($_POST) && !empty($_POST)) {

            //       $this->dump($_POST);

            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }


            // Agregamos los nombres de los archivos al post para la validacion
            if (!empty($_FILES)) {
                foreach ($_FILES as $k => $v) {
                    $_POST[$k] = $v['name'];
                }
            }


            $error_msg = NULL;
            if ($this->form_validation->run() === TRUE && empty($error_msg)) {
                ////////////////////////////////////////////////////////////////////////////////////////////////////////
                ///  CONFIGURACION DEL LIB UPLOAD

                $uploads = array();
                if (!empty($_FILES)) {
                    $config_adjuntos['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($pase->tramite_id, 6, "0", STR_PAD_LEFT) . "/";
                    if (!file_exists($config_adjuntos['upload_path'])) {
                        mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                    }
                    $config_adjuntos['encrypt_name'] = TRUE;
                    $config_adjuntos['file_ext_tolower'] = TRUE;
                    $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx|dwg|dxf|dwf|zip|rar|application/octet-stream';
                    $config_adjuntos['max_size'] = 8192;
                    $this->load->library('upload', $config_adjuntos);
                }
                /////////////////////////////////////////////////////////////////////////////////////////////////////////

                //$pase_id = $id;

                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;

                // GUARDA TODA LA INFO INGRESADA EN LOS DISTINTOS PASOS
                foreach ($pasos as $Paso) {
                    // SI TIENE PADRON ES UN CASO ESPECIAL
                    if ($Paso->padron === 'Obligatorio') {

                        // Actualizar cada padron y subir el comprabante si es que tiene
                        for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {


                            $tramites_padrones = $this->Tramites_padrones_model->get(array(
                                    'pase_id' => $pase_id,
                                    'repeticion' => $i,
                                )
                            )[0];

                            $padron_id = NULL;
                            $padron = $this->Padrones_model->get(array('nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i")));
                            if (empty($padron)) {
                                $trans_ok &= $this->Padrones_model->create(
                                    array(
                                        'codigo' => 1,
                                        'padron' => $this->input->post("{$Paso->orden}_padron_$i"),
                                        'nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i")
                                    ), FALSE);

                                $padron_id = $this->Padrones_model->get_row_id();
                            } else {
                                $padron_id = $padron[0]->id;
                            }

                            if (!empty($padron_id)) {
                                // actualiza el tramite_padron
                                $trans_ok &= $this->Tramites_padrones_model->update(
                                    array(
                                        'id' => $tramites_padrones->id,
                                        'padron_id' => $padron_id
                                    ), FALSE);
                            }


                            // subir comprobante de los inmuebles
                            $uploads = array();
                            if (!empty($_FILES)) {
                                foreach ($_FILES as $id => $file) {
                                    $file_name = explode('_', $id, 3);
                                    // PASAN SOLO LOS CAMPOS DEL Comprobante (ID_comprobante_REPETICION)
                                    if ($file_name[1] === 'comprobante' && is_numeric($file_name[0])) {

                                        if (!empty($file['name'])) {
                                            if (!$this->upload->do_upload($id)) {
                                                $error_msg_file = $this->upload->display_errors();
                                                $trans_ok &= FALSE;
                                            } else {
                                                $uploads[$id] = $this->upload->data();
                                            }
                                        }
                                    }
                                }

                                if ($trans_ok) {
                                    if (!empty($uploads)) {
                                        foreach ($uploads as $key => $Upload) {
                                            $campo = explode('_', $key, 3);
                                            $trans_ok &= $this->Adjuntos_model->create(
                                                array(
                                                    'tipo_id' => 1, // 1 = Adjunto generico (HC)
                                                    'nombre' => $Upload['file_name'],
                                                    'ruta' => $config_adjuntos['upload_path'],
                                                    'tamanio' => round($Upload['file_size'], 2),
                                                    'hash' => md5_file($config_adjuntos['upload_path'] . $Upload['file_name']),
                                                    'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                                    'usuario_subida' => $this->session->userdata('user_id')
                                                ), FALSE);


                                            // Eliminar archivo viejo y registro de la db
                                            $adjunto_viejo = $this->db
                                                ->select(['adjunto_id', 'nombre', 'ruta'])
                                                ->join('to2_adjuntos', "to2_adjuntos.id = to2_tramites_padrones.adjunto_id", 'LEFT')
                                                ->get_where('to2_tramites_padrones', array(
                                                        'pase_id' => $pase_id,
                                                        'padron_id', $campo[0],
                                                        'repeticion' => $campo[2])
                                                )->row();


                                            $adjunto_id = $this->Adjuntos_model->get_row_id();
                                            // inserta el nuevo registro
                                            $this->db
                                                ->set('adjunto_id', $adjunto_id)
                                                ->where('pase_id', $pase->id)
                                                ->where('padron_id', $campo[0])
                                                ->where('repeticion', $campo[2]);
                                            $this->db->update('to2_tramites_padrones');

                                            if ($adjunto_viejo) {
                                                $this->Adjuntos_model->delete(array('id' => $adjunto_viejo->adjunto_id), FALSE);

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }


                foreach ($_POST as $id => $valor) {
                    // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                    $post_name = explode('_', $id, 3);
                    if ($post_name[0] === 'campo' && is_numeric($post_name[1])) {

                        $this->db
                            ->set('valor', $this->input->post($id))
                            ->where('pase_id', $pase_id)
                            ->where('campo_id', $post_name[1])
                            ->where('repeticion', $post_name[2]);
                        $this->db->update('to2_datos');
                    }
                }

                $uploads = array();
                if (!empty($_FILES)) {

                    foreach ($_FILES as $id => $file) {
                        $file_name = explode('_', $id, 3);
                        // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                        if ($file_name[0] === 'campo' && is_numeric($file_name[1])) {

                            if (!empty($file['name'])) {
                                if (!$this->upload->do_upload($id)) {
                                    $error_msg_file = $this->upload->display_errors();
                                    $trans_ok = FALSE;
                                } else {
                                    $uploads[$id] = $this->upload->data();
                                }
                            }
                        }
                    }

                    if ($trans_ok) {
                        if (!empty($uploads)) {
                            foreach ($uploads as $key => $Upload) {
                                $campo = explode('_', $key, 3);
                                $trans_ok &= $this->Adjuntos_model->create(
                                    array(
                                        'tipo_id' => 1, // 1 = Adjunto generico (HC)
                                        'nombre' => $Upload['file_name'],
                                        'ruta' => $config_adjuntos['upload_path'],
                                        'tamanio' => round($Upload['file_size'], 2),
                                        'hash' => md5_file($config_adjuntos['upload_path'] . $Upload['file_name']),
                                        'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                        'usuario_subida' => $this->session->userdata('user_id')
                                    ), FALSE);

                                $adjunto_id = $this->Adjuntos_model->get_row_id();

                                // Eliminar archivo viejo y registro de la db
                                $adjunto_viejo = $this->db
                                    ->select(['adjunto_id', 'nombre', 'ruta'])
                                    ->join('to2_adjuntos', "to2_adjuntos.id = to2_datos.adjunto_id", 'LEFT')
                                    ->get_where('to2_datos', array(
                                            'pase_id' => $pase_id,
                                            'campo_id' => $campo[1],
                                            'repeticion' => $campo[2])
                                    )->row();

                                // inserta el nuevo registro
                                $this->db
                                    ->set('adjunto_id', $adjunto_id)
                                    ->where('pase_id', $pase_id)
                                    ->where('campo_id', $campo[1])
                                    ->where('repeticion', $campo[2]);
                                $this->db->update('to2_datos');


                                if (isset($adjunto_viejo) && $adjunto_viejo) {
                                    $this->Adjuntos_model->delete(array('id' => $adjunto_viejo->adjunto_id), FALSE);
                                }
                            }
                        }
                    }
                }


                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();

                    //borrar archivo viejo
                    if (isset($adjunto_viejo) && $adjunto_viejo) {
                        unlink($config_adjuntos['upload_path'] . $adjunto_viejo->nombre);
                    }
                    $this->session->set_flashdata('message', $this->Pases_model->get_msg());
                    redirect('tramites_online/tramites/ver/' . $pase->tramite_id . '/bandeja_entrada', 'refresh');

                } else {
                    $this->db->trans_rollback();
                    // BORRA ARCHIVOS SI FALLO ALGO
                    if (!empty($uploads)) {
                        foreach ($uploads as $Upload) {
                            unlink($config_adjuntos['upload_path'] . $Upload['file_name']);
                        }
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Pases_model->get_error()) {
                        $error_msg .= $this->Pases_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error()) {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                    if (isset($lleva_padron)) {
                        if ($this->Padrones_model->get_error()) {
                            $error_msg .= $this->Padrones_model->get_error();
                        }

                        if ($this->Tramites_padrones_model->get_error()) {
                            $error_msg .= $this->Tramites_padrones_model->get_error();
                        }
                    }
                    if ($this->Datos_model->get_error()) {
                        $error_msg .= $this->Datos_model->get_error();
                    }
                }
            }
        }


        if (!empty($error_msg_file)) {
            $error_msg .= $error_msg_file;
        }

        $fake_model_pase = new stdClass();
        $fake_model_pase->fields = array(
            'destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );

        $estados_posteriores = $this->Estados_secuencias_model->get(array(
            'estado_id' => $ultimo_pase->ed_id,
            'join' => array(
                array('to2_estados', 'to2_estados.id = to2_estados_secuencias.estado_posterior_id', 'LEFT', 'to2_estados.nombre as estado_posterior, to2_estados.final as final')
            )
        ));

        if (empty($estados_posteriores)) {
            show_error('No se encontró el Estado', 500, 'Registro no encontrado');
        }
        $array_destino = array();
        foreach ($estados_posteriores as $Estado) {
            $array_destino[$Estado->estado_posterior_id] = array('opciones' => $Estado->estado_posterior, 'icono' => $Estado->icono, 'final' => $Estado->final);
            $array_destino_cont[$Estado->estado_posterior_id] = $Estado->estado_posterior;
        }
        $this->array_destino_control = $array_destino_cont;


        $fake_model_pase->fields['destino']['array'] = $array_destino;
        $data['fields_pase'] = $this->build_fields($fake_model_pase->fields);

        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('to2_estados EO', 'EO.id = to2_pases.estado_origen_id', 'LEFT'),
                array('to2_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "CONCAT(EO.nombre, ' (', COALESCE(OO.nombre, ''), ')') as estado_origen"),
                array('to2_estados ED', 'ED.id = to2_pases.estado_destino_id', 'LEFT'),
                array('to2_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "CONCAT(ED.nombre, ' (', COALESCE(OD.nombre, ''), ')') as estado_destino")
            )
        ));


        $data['ultimo_pase'] = $ultimo_pase;
        $data['tramite'] = $tramite;
        if (in_groups($this->grupos_publico, $this->grupos)) {
            $data['back_url'] = 'bandeja_entrada_publico';
        } else {
            $data['back_url'] = 'bandeja_entrada';
        }
        $data['tramite'] = $tramite;
        if (in_groups($this->grupos_publico, $this->grupos)) {
            $data['back_url'] = 'bandeja_entrada_publico';
        } else {
            $data['back_url'] = 'bandeja_entrada';
        }

        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['txt_btn'] = "Editar";
        $data['title_view'] = 'Editar Pase';
        $data['title'] = TITLE . ' - Editar Pase';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard.min.css';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard_theme_arrows.min.css';
        $data['js'][] = 'vendor/smart-wizard/js/jquery.smartWizard.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['js'][] = 'js/tramites_online/base.js';

        $this->load_template('tramites_online/tramites/tramites_editar', $data);
        //$this->load->view('tramites_online/pases/pases_modal_abm', $data);
    }

    private function tramites_frecuentes_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('tramites_online/Iniciadores_model');
        $this->load->model('tramites_online/Procesos_model');

        $tramites_frecuentes = array();

        // BUSCA EL INICIADOR (PERSONA) ASOCIADA AL USUARIO ACTUAL
        $persona = $this->Iniciadores_model->get(array(
            'select' => array('to2_iniciadores.tipo_id'),
            'join' => array(
                array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array('users.id = ' . $this->session->userdata('user_id'))
        ));
        if (!empty($persona))
        {
            // BUSCA TODOS LOS PROCESOS PUBLICOS DISPONIBLES PARA LA PERSONA
            $procesos = $this->Procesos_model->get(
                    array(
                        'join' => array(
                            array('to2_procesos_iniciadores', "to2_procesos_iniciadores.proceso_id = to2_procesos.id AND (to2_procesos_iniciadores.iniciador_tipo_id = {$persona[0]->tipo_id} OR to2_procesos_iniciadores.iniciador_tipo_id = 1)")
                        ),
                        'visibilidad' => 'Público'
                    )
            );

            if (!empty($procesos))
            {
                $indice = 0;
                foreach ($procesos as $Proceso)
                {
                    $tramites_frecuentes[$indice]['href'] = 'tramites_online/tramites/agregar/' . $Proceso->id;
                    $tramites_frecuentes[$indice]['title'] = $Proceso->nombre;
                    switch ($Proceso->tipo)
                    {
                        case 'Consulta':
                            $tramites_frecuentes[$indice]['span'] = '<span class="label label-danger">Consulta</span>';
                            break;
                        case 'Trámite':
                            $tramites_frecuentes[$indice]['span'] = '<span class="label label-warning">Trámite</span>';
                    }
                    $indice++;
                }
            }
        }

        return $tramites_frecuentes;
    }
    
}