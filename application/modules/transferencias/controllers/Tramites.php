<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites extends MY_Controller
{

    /**
     * Controlador de Trámites
     * Autor: Leandro
     * Creado: 21/05/2018
     * Modificado: 29/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
        $this->load->model('transferencias/Adjuntos_model');
        $this->load->model('transferencias/Adjuntos_tipos_model');
        $this->load->model('transferencias/Tramites_model');
        $this->load->model('transferencias/Tramites_tipos_model');
        $this->load->model('transferencias/Escribanos_model');
        $this->load->model('transferencias/Estados_secuencias_model');
        $this->load->model('transferencias/Inmuebles_model');
        $this->load->model('transferencias/Intervinientes_model');
        $this->load->model('transferencias/Oficinas_model');
        $this->load->model('transferencias/Pases_model');
        $this->grupos_permitidos = array('admin', 'transferencias_municipal', 'transferencias_area', 'transferencias_publico', 'transferencias_consulta_general');
        $this->grupos_admin = array('admin', 'transferencias_consulta_general');
        $this->grupos_publico = array('transferencias_publico');
        $this->grupos_municipal = array('transferencias_municipal', 'transferencias_area');
        $this->grupos_solo_consulta = array('transferencias_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_municipal, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
                array('label' => 'Escribano', 'data' => 'escribano', 'width' => 23),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Ubicación Trámite', 'data' => 'oficina', 'width' => 10),
                array('label' => 'Estado Trámite', 'data' => 'estado', 'width' => 10),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Transferencia', 'data' => 'transferencia', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'transferencias/tramites/listar_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Trámites';
        $data['title'] = TITLE . ' - Trámites';
        $this->load_template('transferencias/tramites/tramites_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_municipal, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('transferencias/datatables_functions_helper');
        $this->datatables
                ->select("tr_tramites.id, tr_tramites.fecha_inicio, tr_tramites_tipos.nombre as tipo, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil,  ')') as escribano, tr_inmuebles.padron as padron, tr_oficinas.nombre as oficina, tr_pases.estado_destino_id as estado_id, tr_estados.nombre as estado, tr_pases.fecha as ultimo_mov, CONCAT(tr_tramites.transferencia_nro, '/', tr_tramites.transferencia_eje) as transferencia, tr_estados.id as ult_estado_id")
                ->from('tr_tramites')
                ->join('tr_tramites_tipos', 'tr_tramites_tipos.id = tr_tramites.tipo_id', 'left')
                ->join('tr_pases', 'tr_pases.tramite_id = tr_tramites.id', 'left')
                ->join('tr_pases P', 'P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha', 'left outer')
                ->join('tr_estados', 'tr_estados.id = tr_pases.estado_destino_id', 'left')
                ->join('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id', 'left')
                ->join('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'left')
                ->join('personas', 'personas.id = tr_escribanos.persona_id', 'left')
                ->join('tr_inmuebles', 'tr_inmuebles.id = tr_tramites.inmueble_id', 'left')
                ->where('P.id IS NULL')
                ->add_column('ver', '<a href="transferencias/tramites/ver/$1/listar" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_tramites_listar_editar(ult_estado_id, id)')
                ->add_column('imprimir', '$1', 'dt_column_tramites_imprimir(estado_id, id)');

        echo $this->datatables->generate();
    }

    public function bandeja_entrada()
    {
        if (!in_groups($this->grupos_municipal, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
                array('label' => 'Escribano', 'data' => 'escribano', 'width' => 27),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Ubicación Trámite', 'data' => 'oficina', 'width' => 12),
                array('label' => 'Estado Trámite', 'data' => 'estado', 'width' => 13),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'transferencias/tramites/bandeja_entrada_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Bandeja de Entrada';
        $data['title'] = TITLE . ' - Bandeja de Entrada';
        $this->load_template('transferencias/tramites/tramites_listar_bandeja', $data);
    }

    public function bandeja_entrada_data()
    {
        if (!in_groups($this->grupos_municipal, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('transferencias/datatables_functions_helper');
        $dt = $this->datatables
                ->select("tr_tramites.id, tr_tramites.fecha_inicio, tr_tramites_tipos.nombre as tipo, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil,  ')') as escribano, tr_inmuebles.padron as padron, tr_oficinas.nombre as oficina, tr_estados.nombre as estado, tr_pases.fecha as ultimo_mov, tr_estados.id as ult_estado_id")
                ->from('tr_tramites')
                ->join('tr_tramites_tipos', 'tr_tramites_tipos.id = tr_tramites.tipo_id', 'left')
                ->join('tr_pases', 'tr_pases.tramite_id = tr_tramites.id ', 'left')
                ->join('tr_pases P', 'P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha', 'left outer')
                ->join('tr_estados', 'tr_estados.id = tr_pases.estado_destino_id ', 'left')
                ->join('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id ', 'left')
                ->join('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'left')
                ->join('personas', 'personas.id = tr_escribanos.persona_id', 'left')
                ->join('tr_inmuebles', 'tr_inmuebles.id = tr_tramites.inmueble_id', 'left')
                ->where('P.id IS NULL')
                ->where('tr_pases.estado_destino_id <> 12'); // HC: Finalizado

        if (in_groups($this->grupos_municipal, $this->grupos))
        {
            $dt->join('tr_usuarios_oficinas', 'tr_usuarios_oficinas.oficina_id = tr_oficinas.id ', 'left')
                    ->where('tr_usuarios_oficinas.user_id', $this->session->userdata('user_id'));
        }

        $dt->add_column('ver', '<a href="transferencias/tramites/ver/$1/bandeja_entrada" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_tramites_editar(ult_estado_id, id)');

        echo $this->datatables->generate();
    }

    public function listar_publico()
    {
        if (!in_groups($this->grupos_publico, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
                array('label' => 'Adquirente', 'data' => 'comprador', 'width' => 20),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Ubicación Trámite', 'data' => 'oficina', 'width' => 10),
                array('label' => 'Estado Trámite', 'data' => 'estado', 'width' => 10),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right', 'class' => 'dt-body-right'),
                array('label' => 'Transferencia', 'data' => 'transferencia', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'transferencias/tramites/listar_publico_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['crear'] = TRUE;
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Trámites';
        $data['title'] = TITLE . ' - Trámites';
        $this->load_template('transferencias/tramites/tramites_listar', $data);
    }

    public function listar_publico_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('transferencias/datatables_functions_helper');
        $this->datatables
                ->select("tr_tramites.id, tr_tramites.fecha_inicio, tr_tramites_tipos.nombre as tipo, (SELECT GROUP_CONCAT(CONCAT(tr_intervinientes.apellido, ', ', tr_intervinientes.nombre) SEPARATOR ' | ') FROM tr_intervinientes WHERE tr_intervinientes.tramite_id = tr_tramites.id AND tr_intervinientes.tipo = 'Comprador' ORDER BY tr_intervinientes.apellido) AS comprador, tr_inmuebles.padron as padron, tr_oficinas.nombre as oficina, tr_pases.estado_destino_id as estado_id, tr_estados.nombre as estado, tr_pases.fecha as ultimo_mov, CONCAT(tr_tramites.transferencia_nro, '/', tr_tramites.transferencia_eje) as transferencia")
                ->from('tr_tramites')
                ->join('tr_tramites_tipos', 'tr_tramites_tipos.id = tr_tramites.tipo_id', 'left')
                ->join('tr_pases', 'tr_pases.tramite_id = tr_tramites.id ', 'left')
                ->join('tr_pases P', 'P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha', 'left outer')
                ->join('tr_estados', 'tr_estados.id = tr_pases.estado_destino_id ', 'left')
                ->join('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id ', 'left')
                ->join('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'left')
                ->join('personas', 'personas.id = tr_escribanos.persona_id', 'left')
                ->join('users', 'users.persona_id = personas.id', 'left')
                ->join('tr_inmuebles', 'tr_inmuebles.id = tr_tramites.inmueble_id', 'left')
                ->where('P.id IS NULL')
                ->where('users.id', $this->session->userdata('user_id'))
                ->add_column('ver', '<a href="transferencias/tramites/ver/$1/listar_publico" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '', 'id')
                ->add_column('imprimir', '$1', 'dt_column_tramites_imprimir(estado_id, id)');

        echo $this->datatables->generate();
    }

    public function bandeja_entrada_publico()
    {
        if (!in_groups($this->grupos_publico, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
                array('label' => 'Adquirente', 'data' => 'comprador', 'width' => 28),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Ubicación Trámite', 'data' => 'oficina', 'width' => 12),
                array('label' => 'Estado Trámite', 'data' => 'estado', 'width' => 12),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'transferencias/tramites/bandeja_entrada_publico_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['crear'] = TRUE;
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Bandeja de Entrada';
        $data['title'] = TITLE . ' - Bandeja de Entrada';
        $this->load_template('transferencias/tramites/tramites_listar_bandeja', $data);
    }

    public function bandeja_entrada_publico_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('transferencias/datatables_functions_helper');
        $dt = $this->datatables
                ->select("tr_tramites.id, tr_tramites.fecha_inicio, tr_tramites_tipos.nombre as tipo, (SELECT GROUP_CONCAT(CONCAT(tr_intervinientes.apellido, ', ', tr_intervinientes.nombre) SEPARATOR ' | ') FROM tr_intervinientes WHERE tr_intervinientes.tramite_id = tr_tramites.id AND tr_intervinientes.tipo = 'Comprador' ORDER BY tr_intervinientes.apellido) AS comprador, tr_inmuebles.padron as padron, tr_oficinas.nombre as oficina, tr_estados.nombre as estado, tr_pases.fecha as ultimo_mov, tr_estados.id as ult_estado_id")
                ->from('tr_tramites')
                ->join('tr_tramites_tipos', 'tr_tramites_tipos.id = tr_tramites.tipo_id', 'left')
                ->join('tr_pases', 'tr_pases.tramite_id = tr_tramites.id ', 'left')
                ->join('tr_pases P', 'P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha', 'left outer')
                ->join('tr_estados', 'tr_estados.id = tr_pases.estado_destino_id ', 'left')
                ->join('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id ', 'left')
                ->join('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'left')
                ->join('personas', 'personas.id = tr_escribanos.persona_id', 'left')
                ->join('users', 'users.persona_id = personas.id', 'left')
                ->join('tr_inmuebles', 'tr_inmuebles.id = tr_tramites.inmueble_id', 'left')
                ->where('P.id IS NULL')
                ->where('tr_oficinas.id', 1); //Escribano (HC)

        if (in_groups($this->grupos_publico, $this->grupos))
        {
            $dt->where('users.id', $this->session->userdata('user_id'));
        }

        $dt->add_column('ver', '<a href="transferencias/tramites/ver/$1/bandeja_entrada_publico" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_tramites_editar(ult_estado_id, id)');

        echo $dt->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_publico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('transferencias/tramites/listar_publico', 'refresh');
        }

        $escribano = $this->Escribanos_model->get(array(
            'select' => array('tr_escribanos.id', 'personas.cuil', 'personas.nombre', 'personas.apellido', 'tr_escribanos.matricula_nro', 'tr_escribanos.registro_nro', 'tr_escribanos.registro_tipo', 'personas.telefono', 'personas.celular', 'personas.email'),
            'join' => array(
                array('personas', 'personas.id = tr_escribanos.persona_id', 'LEFT'),
                array('domicilios', 'domicilios.id = personas.domicilio_id', 'LEFT'),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array('users.id = ' . $this->session->userdata('user_id'))
        ));
        if (empty($escribano))
        {
            show_error('No se encontró el Escribano', 500, 'Registro no encontrado');
        }

        $fake_model_escribano = new stdClass();
        $fake_model_escribano->fields = array(
            'matricula_nro' => array('label' => 'Matrícula N°', 'maxlength' => '50'),
            'registro_nro' => array('label' => 'Registro N°', 'maxlength' => '50'),
            'registro_tipo' => array('label' => 'Registro Tipo', 'maxlength' => '50'),
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
        );

        if (!empty($this->input->post('cant_v')))
        {
            $cant_v = $this->input->post('cant_v');
        }
        else
        {
            $cant_v = 1;
        }

        for ($i = 1; $i <= $cant_v; $i++)
        {
            $fake_model_vendedor_{$i} = new stdClass();
            $fake_model_vendedor_{$i}->fields = array(
                "porcentaje_v_$i" => array('label' => "Porcentaje", 'type' => 'numeric', 'class' => 'porcentaje', 'required' => TRUE),
                "cuil_v_$i" => array('label' => "CUIL", 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_v_$i" => array('label' => "Nombre", 'maxlength' => '50', 'required' => TRUE),
                "apellido_v_$i" => array('label' => "Apellido", 'maxlength' => '50', 'required' => TRUE),
                "email_v_$i" => array('label' => "Email", 'type' => 'email', 'maxlength' => '100')
            );
            $this->set_model_validation_rules($fake_model_vendedor_{$i});
        }

        $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        if (!empty($this->input->post('cant_c')))
        {
            $cant_c = $this->input->post('cant_c');
        }
        else
        {
            $cant_c = 1;
        }

        for ($i = 1; $i <= $cant_c; $i++)
        {
            $fake_model_comprador_{$i} = new stdClass();
            $fake_model_comprador_{$i}->fields = array(
                "porcentaje_c_$i" => array('label' => "Porcentaje", 'type' => 'numeric', 'class' => 'porcentaje', 'required' => TRUE),
                "cuil_c_$i" => array('label' => "CUIL", 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_c_$i" => array('label' => "Nombre", 'maxlength' => '50', 'required' => TRUE),
                "apellido_c_$i" => array('label' => "Apellido", 'maxlength' => '50', 'required' => TRUE),
                "email_c_$i" => array('label' => "Email", 'type' => 'email', 'maxlength' => '100'),
                "calle_c_$i" => array('label' => "Calle", 'maxlength' => '50', 'required' => TRUE),
                "barrio_c_$i" => array('label' => "Barrio", 'maxlength' => '50'),
                "altura_c_$i" => array('label' => "Altura", 'maxlength' => '10', 'required' => TRUE),
                "piso_c_$i" => array('label' => "Piso", 'maxlength' => '10'),
                "dpto_c_$i" => array('label' => "Dpto", 'maxlength' => '10'),
                "manzana_c_$i" => array('label' => "Manzana", 'maxlength' => '10'),
                "casa_c_$i" => array('label' => "Casa", 'maxlength' => '10'),
                "localidad_c_$i" => array('label' => "Localidad", 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
            );
            $this->{'array_localidad_c_' . $i . '_control'} = $array_localidad;
            $this->set_model_validation_rules($fake_model_comprador_{$i});
        }

        $fake_model_adjuntos = new stdClass();
        $fake_model_adjuntos->fields = array(
            'plano_mensura' => array('label' => 'Plano de mensura', 'type' => 'file'),
            'certificado_catastral' => array('label' => 'Certificado catastral informado', 'type' => 'file', 'required' => TRUE),
            'otros[]' => array('label' => 'Otros adjuntos', 'type' => 'file', 'id_name' => 'otros', 'is_multiple' => TRUE)
        );

        $this->array_tipo_control = $array_tipo = $this->get_array('Tramites_tipos', 'nombre');
        $this->array_relacionado_control = $array_relacionado = $this->get_array('Tramites', 'relacionado', 'id', array(
            'select' => "tr_tramites.id, CONCAT('Trámite N° ', tr_tramites.id) as relacionado",
            'join' => array(
                array('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'LEFT'),
                array('personas', 'personas.id = tr_escribanos.persona_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array(
                array('column' => 'users.id', 'value' => $this->session->userdata('user_id'))
            )
                ), array(NULL => '-- Sin Trámite Relacionado --')
        );
        $this->set_model_validation_rules($this->Tramites_model);
        $this->set_model_validation_rules($this->Inmuebles_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            if (!empty($_FILES['certificado_catastral']['name']))
            {
                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;

                $trans_ok &= $this->Inmuebles_model->create(array(
                    'padron' => $this->input->post('padron'),
                    'nomenclatura' => $this->input->post('nomenclatura'),
                    'sup_titulo' => $this->input->post('sup_titulo'),
                    'sup_mensura' => $this->input->post('sup_mensura'),
                    'sup_afectada' => $this->input->post('sup_afectada'),
                    'sup_cubierta' => $this->input->post('sup_cubierta')), FALSE);

                $inmueble_id = $this->Inmuebles_model->get_row_id();

                $trans_ok &= $this->Tramites_model->create(array(
                    'fecha_inicio' => $fecha->format('Y-m-d H:i:s'),
                    'tipo_id' => $this->input->post('tipo'),
                    'escribano_id' => $escribano[0]->id,
                    'inmueble_id' => $inmueble_id,
                    'relacionado_id' => $this->input->post('relacionado'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $tramite_id = $this->Tramites_model->get_row_id();

                $config_adjuntos['upload_path'] = "uploads/transferencias/tramites/" . str_pad($tramite_id, 6, "0", STR_PAD_LEFT) . "/";
                if (!file_exists($config_adjuntos['upload_path']))
                {
                    mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                }
                $config_adjuntos['encrypt_name'] = TRUE;
                $config_adjuntos['file_ext_tolower'] = TRUE;
                $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf';
                $config_adjuntos['max_size'] = 8192;
                $this->load->library('upload', $config_adjuntos);

                if (!empty($_FILES['plano_mensura']['name']))
                {
                    if (!$this->upload->do_upload('plano_mensura'))
                    {
                        $error_msg_file = $this->upload->display_errors();
                        $trans_ok = FALSE;
                    }
                    else
                    {
                        $upload_plano_mensura = $this->upload->data();
                    }
                }

                if (!$this->upload->do_upload('certificado_catastral'))
                {
                    $error_msg_file = $this->upload->display_errors();
                    $trans_ok = FALSE;
                }
                else
                {
                    $upload_certificado_catastral = $this->upload->data();
                }

                if (!empty($_FILES['otros']['name'][0]))
                {
                    $files_otros = $_FILES['otros'];
                    $filecount = count($_FILES['otros']['name']);
                    for ($i = 0; $i < $filecount; $i++)
                    {
                        $_FILES['otros']['name'] = $files_otros['name'][$i];
                        $_FILES['otros']['type'] = $files_otros['type'][$i];
                        $_FILES['otros']['tmp_name'] = $files_otros['tmp_name'][$i];
                        $_FILES['otros']['error'] = $files_otros['error'][$i];
                        $_FILES['otros']['size'] = $files_otros['size'][$i];

                        if (!$this->upload->do_upload('otros'))
                        {
                            $error_msg_file = $this->upload->display_errors();
                            $trans_ok = FALSE;
                        }
                        else
                        {
                            $upload_otros[] = $this->upload->data();
                        }
                    }
                }

                if ($trans_ok)
                {
                    if (!empty($upload_plano_mensura))
                    {
                        $trans_ok &= $this->Adjuntos_model->create(array(
                            'tipo_id' => 1, // 1 = Plano de Mensura	(HC)
                            'nombre' => $upload_plano_mensura['file_name'],
                            'ruta' => $config_adjuntos['upload_path'],
                            'tamanio' => round($upload_plano_mensura['file_size'], 2),
                            'hash' => md5_file($config_adjuntos['upload_path'] . $upload_plano_mensura['file_name']),
                            'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                            'usuario_subida' => $this->session->userdata('user_id'),
                            'tramite_id' => $tramite_id), FALSE);
                    }

                    if (!empty($upload_certificado_catastral))
                    {
                        $trans_ok &= $this->Adjuntos_model->create(array(
                            'tipo_id' => 2, // 2 = Certificado Catastral (HC)
                            'nombre' => $upload_certificado_catastral['file_name'],
                            'ruta' => $config_adjuntos['upload_path'],
                            'tamanio' => round($upload_certificado_catastral['file_size'], 2),
                            'hash' => md5_file($config_adjuntos['upload_path'] . $upload_certificado_catastral['file_name']),
                            'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                            'usuario_subida' => $this->session->userdata('user_id'),
                            'tramite_id' => $tramite_id), FALSE);
                    }
                    else
                    {
                        $trans_ok = FALSE;
                    }

                    if (!empty($upload_otros))
                    {
                        foreach ($upload_otros as $Upload_otro)
                        {
                            $trans_ok &= $this->Adjuntos_model->create(array(
                                'tipo_id' => 3, // 3 = Adjunto generico trámite (HC)
                                'nombre' => $Upload_otro['file_name'],
                                'ruta' => $config_adjuntos['upload_path'],
                                'tamanio' => round($Upload_otro['file_size'], 2),
                                'hash' => md5_file($config_adjuntos['upload_path'] . $Upload_otro['file_name']),
                                'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                'usuario_subida' => $this->session->userdata('user_id'),
                                'tramite_id' => $tramite_id), FALSE);
                        }
                    }

                    for ($i = 1; $i <= $cant_v; $i++)
                    {
                        $trans_ok &= $this->Intervinientes_model->create(array(
                            'tramite_id' => $tramite_id,
                            'tipo' => 'Vendedor',
                            'porcentaje' => $this->input->post("porcentaje_v_$i"),
                            'cuil' => $this->input->post("cuil_v_$i"),
                            'nombre' => $this->input->post("nombre_v_$i"),
                            'apellido' => $this->input->post("apellido_v_$i"),
                            'email' => $this->input->post("email_v_$i")), FALSE);
                    }

                    for ($i = 1; $i <= $cant_c; $i++)
                    {
                        $trans_ok &= $this->Domicilios_model->create(array(
                            'calle' => $this->input->post("calle_c_$i"),
                            'barrio' => $this->input->post("barrio_c_$i"),
                            'altura' => $this->input->post("altura_c_$i"),
                            'piso' => $this->input->post("piso_c_$i"),
                            'dpto' => $this->input->post("dpto_c_$i"),
                            'manzana' => $this->input->post("manzana_c_$i"),
                            'casa' => $this->input->post("casa_c_$i"),
                            'localidad_id' => $this->input->post("localidad_c_$i")), FALSE);

                        $domicilio_c_id = $this->Domicilios_model->get_row_id();

                        $trans_ok &= $this->Intervinientes_model->create(array(
                            'tramite_id' => $tramite_id,
                            'tipo' => 'Comprador',
                            'porcentaje' => $this->input->post("porcentaje_c_$i"),
                            'cuil' => $this->input->post("cuil_c_$i"),
                            'nombre' => $this->input->post("nombre_c_$i"),
                            'apellido' => $this->input->post("apellido_c_$i"),
                            'email' => $this->input->post("email_c_$i"),
                            'domicilio_id' => $domicilio_c_id), FALSE);
                    }

                    $trans_ok &= $this->Pases_model->create(array(
                        'tramite_id' => $tramite_id,
                        'estado_origen_id' => 1, //Carga de Datos (Escribano) (HC)
                        'estado_destino_id' => 4, //Informe de Aportes Económicos (Aguas) (HC)
                        'fecha' => $fecha->format('Y-m-d H:i:s'),
                        'usuario_origen' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->informar_pase($tramite_id);
                    $this->session->set_flashdata('message', $this->Tramites_model->get_msg());
                    redirect('transferencias/tramites/listar_publico', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    if (!empty($upload_plano_mensura))
                    {
                        unlink($config_adjuntos['upload_path'] . $upload_plano_mensura['file_name']);
                    }
                    if (!empty($upload_certificado_catastral))
                    {
                        unlink($config_adjuntos['upload_path'] . $upload_certificado_catastral['file_name']);
                    }
                    if (!empty($upload_otros))
                    {
                        foreach ($upload_otros as $Upload_otro)
                        {
                            unlink($config_adjuntos['upload_path'] . $Upload_otro['file_name']);
                        }
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Inmuebles_model->get_error())
                    {
                        $error_msg .= $this->Inmuebles_model->get_error();
                    }
                    if ($this->Tramites_model->get_error())
                    {
                        $error_msg .= $this->Tramites_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                    if ($this->Domicilios_model->get_error())
                    {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Intervinientes_model->get_error())
                    {
                        $error_msg .= $this->Intervinientes_model->get_error();
                    }
                    if ($this->Pases_model->get_error())
                    {
                        $error_msg .= $this->Pases_model->get_error();
                    }
                }
            }
            else
            {
                $error_msg = '<br />Debe adjuntar Certificado catastral informado';
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Tramites_model->fields['tipo']['array'] = $array_tipo;
        $this->Tramites_model->fields['relacionado']['array'] = $array_relacionado;
        $data['fields_tramite'] = $this->build_fields($this->Tramites_model->fields);
        $data['fields_escribano'] = $this->build_fields($fake_model_escribano->fields, $escribano[0], TRUE);
        $data['fields_inmueble'] = $this->build_fields($this->Inmuebles_model->fields);
        for ($i = 1; $i <= $cant_v; $i++)
        {
            $data['fields_vendedores'][] = $this->build_fields($fake_model_vendedor_{$i}->fields);
        }
        for ($i = 1; $i <= $cant_c; $i++)
        {
            $fake_model_comprador_{$i}->fields["localidad_c_$i"]['array'] = $array_localidad;
            $data['fields_compradores'][] = $this->build_fields($fake_model_comprador_{$i}->fields);
        }
        $data['fields_adjunto'] = $this->build_fields($fake_model_adjuntos->fields);

        $data['back_url'] = 'listar_publico';
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
        $this->load_template('transferencias/tramites/tramites_alta', $data);
    }

    public function editar($id = NULL)
    {
        if ((!in_groups($this->grupos_publico, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos)) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("transferencias/tramites/ver/$id", 'refresh');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite))
        {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Escribanos_model->get_user_id($tramite->escribano_id) !== $this->session->userdata('user_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['grupo'] = 'admin';
        }
        else
        {
            $data['grupo'] = 'publico';
        }
        $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), $data['grupo']);
        if (empty($ultimo_pase) || ($ultimo_pase->ed_id !== '3' && $ultimo_pase->ed_id !== '6')) //Corrección de Datos o Corrección de acuerdo a Observaciones (Escribano) (HC)
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $vendedores = $this->Intervinientes_model->get(array('tramite_id' => $tramite->id, 'tipo' => 'Vendedor'));
        if (empty($vendedores))
        {
            show_error('No se encontró el Vendedor', 500, 'Registro no encontrado');
        }

        $compradores = $this->Intervinientes_model->get(array(
            'tramite_id' => $tramite->id,
            'tipo' => 'Comprador',
            'join' => array(
                array('domicilios', "domicilios.id = tr_intervinientes.domicilio_id", 'LEFT',
                    array(
                        'domicilios.calle',
                        'domicilios.barrio',
                        'domicilios.altura',
                        'domicilios.piso',
                        'domicilios.dpto',
                        'domicilios.manzana',
                        'domicilios.casa',
                        'domicilios.localidad_id'
                    )
                ),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
                    array(
                        "CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
                    )
                ),
            )
        ));
        if (empty($compradores))
        {
            show_error('No se encontró el Comprador', 500, 'Registro no encontrado');
        }

        $fake_model_escribano = new stdClass();
        $fake_model_escribano->fields = array(
            'matricula_nro' => array('label' => 'Matrícula N°', 'maxlength' => '50'),
            'registro_nro' => array('label' => 'Registro N°', 'maxlength' => '50'),
            'registro_tipo' => array('label' => 'Registro Tipo', 'maxlength' => '50'),
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
        );

        if (!empty($this->input->post('cant_v')))
        {
            $cant_v = $this->input->post('cant_v');
        }
        else
        {
            $cant_v = sizeof($vendedores);
        }
        for ($i = 1; $i <= $cant_v; $i++)
        {
            $fake_model_vendedor_{$i} = new stdClass();
            $fake_model_vendedor_{$i}->fields = array(
                "porcentaje_v_$i" => array('label' => "Porcentaje", 'type' => 'numeric', 'class' => 'porcentaje', 'required' => TRUE),
                "cuil_v_$i" => array('label' => "CUIL", 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_v_$i" => array('label' => "Nombre", 'maxlength' => '50', 'required' => TRUE),
                "apellido_v_$i" => array('label' => "Apellido", 'maxlength' => '50', 'required' => TRUE),
                "email_v_$i" => array('label' => "Email", 'type' => 'email', 'maxlength' => '100')
            );
            $this->set_model_validation_rules($fake_model_vendedor_{$i});
        }

        $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        if (!empty($this->input->post('cant_c')))
        {
            $cant_c = $this->input->post('cant_c');
        }
        else
        {
            $cant_c = sizeof($compradores);
        }
        for ($i = 1; $i <= $cant_c; $i++)
        {
            $fake_model_comprador_{$i} = new stdClass();
            $fake_model_comprador_{$i}->fields = array(
                "porcentaje_c_$i" => array('label' => "Porcentaje", 'type' => 'numeric', 'class' => 'porcentaje', 'required' => TRUE),
                "cuil_c_$i" => array('label' => "CUIL", 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_c_$i" => array('label' => "Nombre", 'maxlength' => '50', 'required' => TRUE),
                "apellido_c_$i" => array('label' => "Apellido", 'maxlength' => '50', 'required' => TRUE),
                "email_c_$i" => array('label' => "Email", 'type' => 'email', 'maxlength' => '100'),
                "calle_c_$i" => array('label' => "Calle", 'maxlength' => '50', 'required' => TRUE),
                "barrio_c_$i" => array('label' => "Barrio", 'maxlength' => '50'),
                "altura_c_$i" => array('label' => "Altura", 'maxlength' => '10', 'required' => TRUE),
                "piso_c_$i" => array('label' => "Piso", 'maxlength' => '10'),
                "dpto_c_$i" => array('label' => "Dpto", 'maxlength' => '10'),
                "manzana_c_$i" => array('label' => "Manzana", 'maxlength' => '10'),
                "casa_c_$i" => array('label' => "Casa", 'maxlength' => '10'),
                "localidad_c_$i" => array('label' => "Localidad", 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
            );
            $this->{'array_localidad_c_' . $i . '_control'} = $array_localidad;
            $this->set_model_validation_rules($fake_model_comprador_{$i});
        }

        $fake_model_adjuntos = new stdClass();
        $fake_model_adjuntos->fields = array(
            'plano_mensura' => array('label' => 'Plano de mensura', 'type' => 'file', 'form_type' => 'file'),
            'certificado_catastral' => array('label' => 'Certificado catastral informado', 'type' => 'file', 'form_type' => 'file'),
            'otros[]' => array('label' => 'Otros adjuntos', 'type' => 'file', 'form_type' => 'file', 'id_name' => 'otros', 'is_multiple' => TRUE)
        );

        $this->array_tipo_control = $array_tipo = $this->get_array('Tramites_tipos', 'nombre');
        $this->array_relacionado_control = $array_relacionado = $this->get_array('Tramites', 'relacionado', 'id', array(
            'select' => "tr_tramites.id, CONCAT('Trámite N° ', tr_tramites.id) as relacionado",
            'join' => array(
                array('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'LEFT'),
                array('personas', 'personas.id = tr_escribanos.persona_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array(
                array('column' => 'users.id', 'value' => $this->session->userdata('user_id')),
                array('column' => 'tr_tramites.id !=', 'value' => $id)
            )
                ), array(NULL => '-- Sin Trámite Relacionado --')
        );

        $this->set_model_validation_rules($this->Tramites_model);
        $this->set_model_validation_rules($this->Inmuebles_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $tramite_id = $this->input->post('id');

                $trans_ok &= $this->Inmuebles_model->update(array(
                    'id' => $tramite->inmueble_id,
                    'padron' => $this->input->post('padron'),
                    'nomenclatura' => $this->input->post('nomenclatura'),
                    'sup_titulo' => $this->input->post('sup_titulo'),
                    'sup_mensura' => $this->input->post('sup_mensura'),
                    'sup_afectada' => $this->input->post('sup_afectada'),
                    'sup_cubierta' => $this->input->post('sup_cubierta')), FALSE);

                $trans_ok &= $this->Tramites_model->update(array(
                    'id' => $tramite_id,
                    'tipo_id' => $this->input->post('tipo'),
                    'relacionado_id' => $this->input->post('relacionado'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $config_adjuntos['upload_path'] = "uploads/transferencias/tramites/" . str_pad($tramite_id, 6, "0", STR_PAD_LEFT) . "/";
                if (!file_exists($config_adjuntos['upload_path']))
                {
                    mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                }
                $config_adjuntos['encrypt_name'] = TRUE;
                $config_adjuntos['file_ext_tolower'] = TRUE;
                $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf';
                $config_adjuntos['max_size'] = 8192;
                $this->load->library('upload', $config_adjuntos);

                if (!empty($_FILES['plano_mensura']['name']))
                {
                    if (!$this->upload->do_upload('plano_mensura'))
                    {
                        $error_msg_file = $this->upload->display_errors();
                        $trans_ok = FALSE;
                    }
                    else
                    {
                        $upload_plano_mensura = $this->upload->data();
                    }
                }

                if (!empty($_FILES['certificado_catastral']['name']))
                {
                    if (!$this->upload->do_upload('certificado_catastral'))
                    {
                        $error_msg_file = $this->upload->display_errors();
                        $trans_ok = FALSE;
                    }
                    else
                    {
                        $upload_certificado_catastral = $this->upload->data();
                    }
                }

                if (!empty($_FILES['otros']['name'][0]))
                {
                    $files_otros = $_FILES['otros'];
                    $filecount = count($_FILES['otros']['name']);
                    for ($i = 0; $i < $filecount; $i++)
                    {
                        $_FILES['otros']['name'] = $files_otros['name'][$i];
                        $_FILES['otros']['type'] = $files_otros['type'][$i];
                        $_FILES['otros']['tmp_name'] = $files_otros['tmp_name'][$i];
                        $_FILES['otros']['error'] = $files_otros['error'][$i];
                        $_FILES['otros']['size'] = $files_otros['size'][$i];

                        if (!$this->upload->do_upload('otros'))
                        {
                            $error_msg_file = $this->upload->display_errors();
                            $trans_ok = FALSE;
                        }
                        else
                        {
                            $upload_otros[] = $this->upload->data();
                        }
                    }
                }

                if ($trans_ok)
                {
                    if (!empty($upload_plano_mensura))
                    {
                        $plano_mensura_anterior = $this->Adjuntos_model->get(array('tramite_id' => $id, 'tipo_id' => 1));
                        $trans_ok &= $this->Adjuntos_model->delete_adjuntos($tramite_id, 1);
                        $trans_ok &= $this->Adjuntos_model->create(array(
                            'tipo_id' => 1, // 1 = Plano de Mensura (HC)
                            'nombre' => $upload_plano_mensura['file_name'],
                            'ruta' => $config_adjuntos['upload_path'],
                            'tamanio' => round($upload_plano_mensura['file_size'], 2),
                            'hash' => md5_file($config_adjuntos['upload_path'] . $upload_plano_mensura['file_name']),
                            'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                            'usuario_subida' => $this->session->userdata('user_id'),
                            'tramite_id' => $tramite_id), FALSE);
                    }

                    if (!empty($upload_certificado_catastral))
                    {
                        $certificado_catastral_anterior = $this->Adjuntos_model->get(array('tramite_id' => $id, 'tipo_id' => 2));
                        $trans_ok &= $this->Adjuntos_model->delete_adjuntos($tramite_id, 2);
                        $trans_ok &= $this->Adjuntos_model->create(array(
                            'tipo_id' => 2, // 2 = Certificado Catastral (HC)
                            'nombre' => $upload_certificado_catastral['file_name'],
                            'ruta' => $config_adjuntos['upload_path'],
                            'tamanio' => round($upload_certificado_catastral['file_size'], 2),
                            'hash' => md5_file($config_adjuntos['upload_path'] . $upload_certificado_catastral['file_name']),
                            'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                            'usuario_subida' => $this->session->userdata('user_id'),
                            'tramite_id' => $tramite_id), FALSE);
                    }

                    if (!empty($upload_otros))
                    {
                        $otros_adjuntos_anterior = $this->Adjuntos_model->get(array('tramite_id' => $id, 'tipo_id' => 3));
                        $trans_ok &= $this->Adjuntos_model->delete_adjuntos($tramite_id, 3);
                        foreach ($upload_otros as $Upload_otro)
                        {
                            $trans_ok &= $this->Adjuntos_model->create(array(
                                'tipo_id' => 3, // 3 = Adjunto generico trámite (HC)
                                'nombre' => $Upload_otro['file_name'],
                                'ruta' => $config_adjuntos['upload_path'],
                                'tamanio' => round($Upload_otro['file_size'], 2),
                                'hash' => md5_file($config_adjuntos['upload_path'] . $Upload_otro['file_name']),
                                'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                'usuario_subida' => $this->session->userdata('user_id'),
                                'tramite_id' => $tramite_id), FALSE);
                        }
                    }

                    $trans_ok &= $this->Intervinientes_model->delete_intervinientes($tramite_id, 'Vendedor');
                    for ($i = 1; $i <= $cant_v; $i++)
                    {
                        $trans_ok &= $this->Intervinientes_model->create(array(
                            'tramite_id' => $tramite_id,
                            'tipo' => 'Vendedor',
                            'porcentaje' => $this->input->post("porcentaje_v_$i"),
                            'cuil' => $this->input->post("cuil_v_$i"),
                            'nombre' => $this->input->post("nombre_v_$i"),
                            'apellido' => $this->input->post("apellido_v_$i"),
                            'email' => $this->input->post("email_v_$i")), FALSE);
                    }

                    $trans_ok &= $this->Intervinientes_model->delete_intervinientes($tramite_id, 'Comprador');
                    foreach ($compradores as $Comp)
                    {
                        $trans_ok &= $this->Domicilios_model->delete(array('id' => $Comp->domicilio_id), FALSE);
                    }
                    for ($i = 1; $i <= $cant_c; $i++)
                    {
                        $trans_ok &= $this->Domicilios_model->create(array(
                            'calle' => $this->input->post("calle_c_$i"),
                            'barrio' => $this->input->post("barrio_c_$i"),
                            'altura' => $this->input->post("altura_c_$i"),
                            'piso' => $this->input->post("piso_c_$i"),
                            'dpto' => $this->input->post("dpto_c_$i"),
                            'manzana' => $this->input->post("manzana_c_$i"),
                            'casa' => $this->input->post("casa_c_$i"),
                            'localidad_id' => $this->input->post("localidad_c_$i")), FALSE);

                        $domicilio_c_id = $this->Domicilios_model->get_row_id();

                        $trans_ok &= $this->Intervinientes_model->create(array(
                            'tramite_id' => $tramite_id,
                            'tipo' => 'Comprador',
                            'porcentaje' => $this->input->post("porcentaje_c_$i"),
                            'cuil' => $this->input->post("cuil_c_$i"),
                            'nombre' => $this->input->post("nombre_c_$i"),
                            'apellido' => $this->input->post("apellido_c_$i"),
                            'email' => $this->input->post("email_c_$i"),
                            'domicilio_id' => $domicilio_c_id), FALSE);
                    }

                    /* if ($ultimo_pase->ed_id === '3') //Corrección de Datos (Escribano) (HC)
                      {
                      $destino_pase = 2; //Revisión de Datos (Catastro) (HC)
                      }
                      else // Corrección de acuerdo a Observaciones (Escribano) (HC)
                      {
                      $destino_pase = 13; //Generación de Informe de Deuda (Catastro) (HC)
                      } */
                    $destino_pase = 5; //Generación de Boletos de Deuda y Aforos (Catastro) (HC)

                    $trans_ok &= $this->Pases_model->create(array(
                        'tramite_id' => $tramite_id,
                        'estado_origen_id' => $ultimo_pase->ed_id,
                        'estado_destino_id' => $destino_pase,
                        'fecha' => $fecha->format('Y-m-d H:i:s'),
                        'usuario_origen' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->informar_pase($tramite_id);
                    if (!empty($plano_mensura_anterior))
                    {
                        unlink($plano_mensura_anterior[0]->ruta . $plano_mensura_anterior[0]->nombre);
                    }
                    if (!empty($certificado_catastral_anterior))
                    {
                        unlink($certificado_catastral_anterior[0]->ruta . $certificado_catastral_anterior[0]->nombre);
                    }
                    if (!empty($otros_adjuntos_anterior))
                    {
                        foreach ($otros_adjuntos_anterior as $Otro)
                        {
                            unlink($Otro->ruta . $Otro->nombre);
                        }
                    }
                    $this->session->set_flashdata('message', $this->Tramites_model->get_msg());
                    redirect('transferencias/tramites/listar_publico', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    if (!empty($upload_plano_mensura))
                    {
                        unlink($config_adjuntos['upload_path'] . $upload_plano_mensura['file_name']);
                    }
                    if (!empty($upload_certificado_catastral))
                    {
                        unlink($config_adjuntos['upload_path'] . $upload_certificado_catastral['file_name']);
                    }
                    if (!empty($upload_otros))
                    {
                        foreach ($upload_otros as $Upload_otro)
                        {
                            unlink($config_adjuntos['upload_path'] . $Upload_otro['file_name']);
                        }
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Inmuebles_model->get_error())
                    {
                        $error_msg .= $this->Inmuebles_model->get_error();
                    }
                    if ($this->Tramites_model->get_error())
                    {
                        $error_msg .= $this->Tramites_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                    if ($this->Domicilios_model->get_error())
                    {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Intervinientes_model->get_error())
                    {
                        $error_msg .= $this->Intervinientes_model->get_error();
                    }
                    if ($this->Pases_model->get_error())
                    {
                        $error_msg .= $this->Pases_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Tramites_model->fields['tipo']['array'] = $array_tipo;
        $this->Tramites_model->fields['relacionado']['array'] = $array_relacionado;
        $data['fields_tramite'] = $this->build_fields($this->Tramites_model->fields, $tramite);
        $data['fields_escribano'] = $this->build_fields($fake_model_escribano->fields, $tramite, TRUE);
        $data['fields_inmueble'] = $this->build_fields($this->Inmuebles_model->fields, $tramite);

        if (!empty($this->input->post('cant_v')))
        {
            for ($i = 1; $i <= $cant_v; $i++)
            {
                $data['fields_vendedores'][] = $this->build_fields($fake_model_vendedor_{$i}->fields);
            }
        }
        else
        {
            for ($i = 1; $i <= $cant_v; $i++)
            {
                $temp_vendedor = new stdClass();
                $temp_vendedor->{"porcentaje_v_$i"} = $vendedores[$i - 1]->porcentaje;
                $temp_vendedor->{"cuil_v_$i"} = $vendedores[$i - 1]->cuil;
                $temp_vendedor->{"nombre_v_$i"} = $vendedores[$i - 1]->nombre;
                $temp_vendedor->{"apellido_v_$i"} = $vendedores[$i - 1]->apellido;
                $temp_vendedor->{"email_v_$i"} = $vendedores[$i - 1]->email;
                $data['fields_vendedores'][] = $this->build_fields($fake_model_vendedor_{$i}->fields, $temp_vendedor);
            }
        }

        if (!empty($this->input->post('cant_c')))
        {
            for ($i = 1; $i <= $cant_c; $i++)
            {
                $fake_model_comprador_{$i}->fields["localidad_c_$i"]['array'] = $array_localidad;
                $data['fields_compradores'][] = $this->build_fields($fake_model_comprador_{$i}->fields);
            }
        }
        else
        {
            for ($i = 1; $i <= $cant_c; $i++)
            {
                $temp_comprador = new stdClass();
                $temp_comprador->{"porcentaje_c_$i"} = $compradores[$i - 1]->porcentaje;
                $temp_comprador->{"cuil_c_$i"} = $compradores[$i - 1]->cuil;
                $temp_comprador->{"nombre_c_$i"} = $compradores[$i - 1]->nombre;
                $temp_comprador->{"apellido_c_$i"} = $compradores[$i - 1]->apellido;
                $temp_comprador->{"email_c_$i"} = $compradores[$i - 1]->email;
                $temp_comprador->{"calle_c_$i"} = $compradores[$i - 1]->calle;
                $temp_comprador->{"barrio_c_$i"} = $compradores[$i - 1]->barrio;
                $temp_comprador->{"altura_c_$i"} = $compradores[$i - 1]->altura;
                $temp_comprador->{"piso_c_$i"} = $compradores[$i - 1]->piso;
                $temp_comprador->{"dpto_c_$i"} = $compradores[$i - 1]->dpto;
                $temp_comprador->{"manzana_c_$i"} = $compradores[$i - 1]->manzana;
                $temp_comprador->{"casa_c_$i"} = $compradores[$i - 1]->casa;
                $temp_comprador->{"localidad_c_" . $i . "_id"} = $compradores[$i - 1]->localidad_id;
                $fake_model_comprador_{$i}->fields["localidad_c_$i"]['array'] = $array_localidad;
                $data['fields_compradores'][] = $this->build_fields($fake_model_comprador_{$i}->fields, $temp_comprador);
            }
        }

        $adjuntos_tramite = $this->Adjuntos_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('tr_adjuntos_tipos', 'tr_adjuntos_tipos.id = tr_adjuntos.tipo_id', 'LEFT', array("tr_adjuntos_tipos.nombre as tipo"))
            )
        ));
        $data['adjuntos_tramite'] = $adjuntos_tramite;

        $tmp_adjuntos = new stdClass();
        $tmp_adjuntos->plano_mensura = NULL;
        $tmp_adjuntos->certificado_catastral = NULL;
        $tmp_adjuntos->{"otros[]"} = array();
        foreach ($adjuntos_tramite as $Adjunto)
        {
            switch ($Adjunto->tipo)
            {
                case 'Plano de Mensura':
                    $tmp_adjuntos->plano_mensura = $Adjunto->ruta . $Adjunto->nombre;
                    break;
                case 'Certificado Catastral':
                    $tmp_adjuntos->certificado_catastral = $Adjunto->ruta . $Adjunto->nombre;
                    break;
                default:
                    $tmp_adjuntos->{"otros[]"}[] = $Adjunto->ruta . $Adjunto->nombre;
                    break;
            }
        }
        $data['fields_adjunto'] = $this->build_fields($fake_model_adjuntos->fields, $tmp_adjuntos);

        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('tr_estados EO', 'EO.id = tr_pases.estado_origen_id', 'LEFT'),
                array('tr_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "CONCAT(EO.nombre, ' (', OO.nombre, ')') as estado_origen"),
                array('tr_estados ED', 'ED.id = tr_pases.estado_destino_id', 'LEFT'),
                array('tr_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "CONCAT(ED.nombre, ' (', OD.nombre, ')') as estado_destino")
            )
        ));

        $data['tramite'] = $tramite;
        $data['back_url'] = 'listar_publico';
        $data['txt_btn'] = 'Enviar Trámite';
        $data['title_view'] = "Editar Trámite $id";
        $data['title'] = TITLE . ' - Editar Trámite';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard.min.css';
        $data['css'][] = 'vendor/smart-wizard/css/smart_wizard_theme_arrows.min.css';
        $data['js'][] = 'vendor/smart-wizard/js/jquery.smartWizard.min.js';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['js'][] = 'vendor/bootstrap-validator/validator.min.js';
        $this->load_template('transferencias/tramites/tramites_modificacion', $data);
    }

    public function revisar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite))
        {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Escribanos_model->get_user_id($tramite->escribano_id) !== $this->session->userdata('user_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['grupo'] = 'admin';
        }
        elseif (in_groups($this->grupos_municipal, $this->grupos))
        {
            $data['grupo'] = 'municipal';
        }
        else
        {
            $data['grupo'] = 'publico';
        }

        $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), $data['grupo']);
        if (empty($ultimo_pase->p_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if ($ultimo_pase->ed_id === '3' || $ultimo_pase->ed_id === '6') //Corrección de Datos o Corrección de acuerdo a Observaciones (Escribano) (HC)
        {
            redirect("transferencias/tramites/editar/$id", 'refresh');
        }

        $vendedores = $this->Intervinientes_model->get(array('tramite_id' => $tramite->id, 'tipo' => 'Vendedor'));
        if (empty($vendedores))
        {
            show_error('No se encontró el Vendedor', 500, 'Registro no encontrado');
        }

        $compradores = $this->Intervinientes_model->get(array(
            'tramite_id' => $tramite->id,
            'tipo' => 'Comprador',
            'join' => array(
                array('domicilios', "domicilios.id = tr_intervinientes.domicilio_id", 'LEFT',
                    array(
                        'domicilios.calle',
                        'domicilios.barrio',
                        'domicilios.altura',
                        'domicilios.piso',
                        'domicilios.dpto',
                        'domicilios.manzana',
                        'domicilios.casa',
                        'domicilios.localidad_id'
                    )
                ),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
                    array(
                        "CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
                    )
                ),
            )
        ));
        if (empty($compradores))
        {
            show_error('No se encontró el Comprador', 500, 'Registro no encontrado');
        }

        $fake_model_tramites = new stdClass();
        $fake_model_tramites->fields = array(
            'fecha_inicio' => array('label' => 'Inicio', 'type' => 'datetime', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
            'relacionado' => array('label' => 'Trámite relacionado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'escritura_nro' => array('label' => 'Escritura N°', 'type' => 'integer', 'maxlength' => '9'),
            'escritura_foja' => array('label' => 'Escritura Foja', 'type' => 'integer', 'maxlength' => '9'),
            'escritura_fecha' => array('label' => 'Escritura Fecha', 'type' => 'date'),
            'transferencia_nro' => array('label' => 'Transferencia N°', 'type' => 'integer', 'maxlength' => '9'),
            'transferencia_eje' => array('label' => 'Transferencia Ejercicio', 'type' => 'integer', 'maxlength' => '9'),
            'fecha_fin' => array('label' => 'Fin', 'type' => 'datetime')
        );

        $fake_model_escribano = new stdClass();
        $fake_model_escribano->fields = array(
            'matricula_nro' => array('label' => 'Matrícula N°', 'maxlength' => '50'),
            'registro_nro' => array('label' => 'Registro N°', 'maxlength' => '50'),
            'registro_tipo' => array('label' => 'Registro Tipo', 'maxlength' => '50'),
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
        );

        $cant_v = sizeof($vendedores);
        for ($i = 1; $i <= $cant_v; $i++)
        {
            $fake_model_vendedor_{$i} = new stdClass();
            $fake_model_vendedor_{$i}->fields = array(
                "porcentaje_v_$i" => array('label' => 'Porcentaje', 'type' => 'numeric', 'required' => TRUE),
                "cuil_v_$i" => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_v_$i" => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
                "apellido_v_$i" => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
                "email_v_$i" => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
            );
        }

        $cant_c = sizeof($compradores);
        for ($i = 1; $i <= $cant_c; $i++)
        {
            $fake_model_comprador_{$i} = new stdClass();
            $fake_model_comprador_{$i}->fields = array(
                "porcentaje_c_$i" => array('label' => 'Porcentaje', 'type' => 'numeric', 'required' => TRUE),
                "cuil_c_$i" => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_c_$i" => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
                "apellido_c_$i" => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
                "email_c_$i" => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
                "calle_c_$i" => array('label' => 'Calle', 'maxlength' => '50', 'required' => TRUE),
                "barrio_c_$i" => array('label' => 'Barrio', 'maxlength' => '50'),
                "altura_c_$i" => array('label' => 'Altura', 'maxlength' => '10', 'required' => TRUE),
                "piso_c_$i" => array('label' => 'Piso', 'maxlength' => '10'),
                "dpto_c_$i" => array('label' => 'Dpto', 'maxlength' => '10'),
                "manzana_c_$i" => array('label' => 'Manzana', 'maxlength' => '10'),
                "casa_c_$i" => array('label' => 'Casa', 'maxlength' => '10'),
                "localidad_c_$i" => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
            );
        }
        $fake_model_pase = new stdClass();
        if ($ultimo_pase->ed_id === '8') //Pago de Deudas y Aforos (HC)
        {
            if ($this->input->post('destino') === '11') //Verifica Trámite (HC)
            {
                $fake_model_pase->fields = array(
                    'destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                    'escritura_nro_p' => array('label' => 'Escritura N°', 'type' => 'integer', 'maxlength' => '9', 'required' => TRUE),
                    'escritura_foja_p' => array('label' => 'Escritura Foja', 'type' => 'integer', 'maxlength' => '9', 'required' => TRUE),
                    'escritura_fecha_p' => array('label' => 'Escritura Fecha', 'type' => 'date', 'required' => TRUE),
                    'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
                );
            }
            else
            {
                $fake_model_pase->fields = array(
                    'destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                    'escritura_nro_p' => array('label' => 'Escritura N° *', 'type' => 'integer', 'maxlength' => '9'),
                    'escritura_foja_p' => array('label' => 'Escritura Foja *', 'type' => 'integer', 'maxlength' => '9'),
                    'escritura_fecha_p' => array('label' => 'Escritura Fecha *', 'type' => 'date'),
                    'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
                );
            }
        }
        else
        {
            if ($this->input->post('destino') === '15') //Cancelado (HC)
            {
                $fake_model_pase->fields = array(
                    'destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                    'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999', 'required' => TRUE)
                );
            }
            else
            {
                $fake_model_pase->fields = array(
                    'destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                    'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
                );
            }
        }

        $fake_model_adjuntos_pase = new stdClass();
        $fake_model_adjuntos_pase->fields = array(
            'adjuntos[]' => array('label' => 'Adjuntos', 'type' => 'file', 'id_name' => 'adjuntos', 'is_multiple' => TRUE)
        );

        $estados_posteriores = $this->Estados_secuencias_model->get(array(
            'estado_id' => $ultimo_pase->ed_id,
            'join' => array(
                array('tr_estados', 'tr_estados.id = tr_estados_secuencias.estado_posterior_id', 'LEFT'),
                array('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id', 'LEFT', "CONCAT(tr_estados.nombre, ' (', tr_oficinas.nombre, ')') as estado_posterior")
            )
        ));
        if (empty($estados_posteriores))
        {
            show_error('No se encontró el Estado', 500, 'Registro no encontrado');
        }
        $array_destino = array();
        foreach ($estados_posteriores as $Estado)
        {
            $array_destino[$Estado->estado_posterior_id] = array('opciones' => $Estado->estado_posterior, 'icono' => $Estado->icono);
            $array_destino_cont[$Estado->estado_posterior_id] = $Estado->estado_posterior;
        }
        $this->array_destino_control = $array_destino_cont;

        $this->set_model_validation_rules($fake_model_pase);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = NULL;
            if ($this->input->post('destino') === '8' && (empty($tramite->transferencia_nro) || empty($tramite->transferencia_eje))) //Pago de Deuda y Aforo (HC)
            {
                $error_msg = '<br> Debe generar el número de transferencia';
            }
            if ($this->form_validation->run() === TRUE && empty($error_msg))
            {
                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;

                $trans_ok &= $this->Pases_model->update(array(
                    'id' => $ultimo_pase->p_id,
                    'usuario_destino' => $this->session->userdata('user_id')), FALSE);

                $trans_ok &= $this->Pases_model->create(array(
                    'tramite_id' => $this->input->post('id'),
                    'estado_origen_id' => $ultimo_pase->ed_id,
                    'estado_destino_id' => $this->input->post('destino'),
                    'fecha' => $fecha->format('Y-m-d H:i:s'),
                    'usuario_origen' => $this->session->userdata('user_id'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $pase_id = $this->Pases_model->get_row_id();

                if ($this->input->post('destino') === '12' || $this->input->post('destino') === '15') //Finalizado (HC) Cancelado (HC)
                {
                    $trans_ok &= $this->Tramites_model->update(array(
                        'id' => $this->input->post('id'),
                        'fecha_fin' => $fecha->format('Y-m-d H:i:s')), FALSE);
                }

                if ($ultimo_pase->ed_id === '8' && $this->input->post('destino') === '11') //Pago de Deudas y Aforos (HC) y Verifica Trámite (HC)
                {
                    $escritura_fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('escritura_fecha_p'));

                    $trans_ok &= $this->Tramites_model->update(array(
                        'id' => $this->input->post('id'),
                        'escritura_nro' => $this->input->post('escritura_nro_p'),
                        'escritura_foja' => $this->input->post('escritura_foja_p'),
                        'escritura_fecha' => $escritura_fecha->format('Y-m-d H:i:s')), FALSE);
                }

                if (!empty($_FILES['adjuntos']['name'][0]))
                {
                    $this->load->library('upload');
                    $files = $_FILES;
                    $filecount = count($_FILES['adjuntos']['name']);
                    for ($i = 0; $i < $filecount; $i++)
                    {
                        $_FILES['adjuntos']['name'] = $files['adjuntos']['name'][$i];
                        $_FILES['adjuntos']['type'] = $files['adjuntos']['type'][$i];
                        $_FILES['adjuntos']['tmp_name'] = $files['adjuntos']['tmp_name'][$i];
                        $_FILES['adjuntos']['error'] = $files['adjuntos']['error'][$i];
                        $_FILES['adjuntos']['size'] = $files['adjuntos']['size'][$i];

                        $config_pase['upload_path'] = "uploads/transferencias/tramites/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/" . str_pad($pase_id, 6, "0", STR_PAD_LEFT) . "/";
                        if (!file_exists($config_pase['upload_path']))
                        {
                            mkdir($config_pase['upload_path'], 0755, TRUE);
                        }
                        $config_pase['encrypt_name'] = TRUE;
                        $config_pase['file_ext_tolower'] = TRUE;
                        $config_pase['allowed_types'] = 'jpg|jpeg|png|pdf';
                        $config_pase['max_size'] = 8192;

                        $this->upload->initialize($config_pase);
                        if (!$this->upload->do_upload('adjuntos'))
                        {
                            $error_msg_file = $this->upload->display_errors();
                            $trans_ok = FALSE;
                        }
                        else
                        {
                            $upload_pase[] = $this->upload->data();
                        }
                    }

                    if ($trans_ok)
                    {
                        foreach ($upload_pase as $Upload)
                        {
                            $trans_ok &= $this->Adjuntos_model->create(array(
                                'tipo_id' => 4, // 4 = Adjunto generico pase (HC)
                                'nombre' => $Upload['file_name'],
                                'ruta' => $config_pase['upload_path'],
                                'tamanio' => round($Upload['file_size'], 2),
                                'hash' => md5_file($config_pase['upload_path'] . $Upload['file_name']),
                                'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                'usuario_subida' => $this->session->userdata('user_id'),
                                'pase_id' => $pase_id), FALSE);
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->informar_pase($this->input->post('id'));
                    $this->session->set_flashdata('message', $this->Pases_model->get_msg());
                    if (in_groups($this->grupos_publico, $this->grupos))
                    {
                        redirect('transferencias/tramites/bandeja_entrada_publico', 'refresh');
                    }
                    else
                    {
                        redirect('transferencias/tramites/bandeja_entrada', 'refresh');
                    }
                }
                else
                {
                    $this->db->trans_rollback();
                    if (!empty($upload_pase))
                    {
                        foreach ($upload_pase as $Upload)
                        {
                            unlink($config_pase['upload_path'] . $Upload['file_name']);
                        }
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Pases_model->get_error())
                    {
                        $error_msg .= $this->Pases_model->get_error();
                    }
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields_tramite'] = $this->build_fields($fake_model_tramites->fields, $tramite, TRUE);
        $data['fields_escribano'] = $this->build_fields($fake_model_escribano->fields, $tramite, TRUE);
        $data['fields_inmueble'] = $this->build_fields($this->Inmuebles_model->fields, $tramite, TRUE);
        for ($i = 1; $i <= $cant_v; $i++)
        {
            $temp_vendedor = new stdClass();
            $temp_vendedor->{"porcentaje_v_$i"} = $vendedores[$i - 1]->porcentaje;
            $temp_vendedor->{"cuil_v_$i"} = $vendedores[$i - 1]->cuil;
            $temp_vendedor->{"nombre_v_$i"} = $vendedores[$i - 1]->nombre;
            $temp_vendedor->{"apellido_v_$i"} = $vendedores[$i - 1]->apellido;
            $temp_vendedor->{"email_v_$i"} = $vendedores[$i - 1]->email;
            $data['fields_vendedores'][] = $this->build_fields($fake_model_vendedor_{$i}->fields, $temp_vendedor, TRUE);
        }
        for ($i = 1; $i <= $cant_c; $i++)
        {
            $temp_comprador = new stdClass();
            $temp_comprador->{"porcentaje_c_$i"} = $compradores[$i - 1]->porcentaje;
            $temp_comprador->{"cuil_c_$i"} = $compradores[$i - 1]->cuil;
            $temp_comprador->{"nombre_c_$i"} = $compradores[$i - 1]->nombre;
            $temp_comprador->{"apellido_c_$i"} = $compradores[$i - 1]->apellido;
            $temp_comprador->{"email_c_$i"} = $compradores[$i - 1]->email;
            $temp_comprador->{"calle_c_$i"} = $compradores[$i - 1]->calle;
            $temp_comprador->{"barrio_c_$i"} = $compradores[$i - 1]->barrio;
            $temp_comprador->{"altura_c_$i"} = $compradores[$i - 1]->altura;
            $temp_comprador->{"piso_c_$i"} = $compradores[$i - 1]->piso;
            $temp_comprador->{"dpto_c_$i"} = $compradores[$i - 1]->dpto;
            $temp_comprador->{"manzana_c_$i"} = $compradores[$i - 1]->manzana;
            $temp_comprador->{"casa_c_$i"} = $compradores[$i - 1]->casa;
            $temp_comprador->{"localidad_c_$i"} = $compradores[$i - 1]->localidad;
            $data['fields_compradores'][] = $this->build_fields($fake_model_comprador_{$i}->fields, $temp_comprador, TRUE);
        }
        $fake_model_pase->fields['destino']['array'] = $array_destino;
        if ($ultimo_pase->ed_id === '8' && !empty($tramite->escritura_nro)) //Pago de Deudas y Aforos (HC)
        {
            $escritura = new stdClass();
            $escritura->escritura_nro_p = $tramite->escritura_nro;
            $escritura->escritura_foja_p = $tramite->escritura_foja;
            $escritura->escritura_fecha_p = $tramite->escritura_fecha;
            $escritura->destino_id = NULL;
            $escritura->observaciones = NULL;
            $data['fields_pase'] = $this->build_fields($fake_model_pase->fields, $escritura);
        }
        else if ($ultimo_pase->ed_id === '13' || $ultimo_pase->ed_id === '5') //Genera Informe de Deuda (HC) o Generación de Boletos de Deuda y Aforos (HC
        {
            $pase = new stdClass();
            $pase->destino_id = NULL;
            $pase->observaciones = '
INFORME AGUAS LUJAN: 
INFORME OBRAS Y CONSORCIOS: 
INFORME DE RENTAS: 
INFORME DE COMERCIO: 
MULTAS O ACTAS: 
INFORME SUPERFICIE CUBIERTA: 

Si a la fecha de finalización de este trámite ingresan nuevos períodos exigibles y/o multas o actas y otro tipo de aforo municipal tenga en cuenta que se le exigirá la cancelación de los mismos.
El presente informe está sujeto a Revisión al finalizarse la transferencia.';
            $data['fields_pase'] = $this->build_fields($fake_model_pase->fields, $pase);
        }
        else
        {
            $data['fields_pase'] = $this->build_fields($fake_model_pase->fields);
        }
        $data['fields_adjunto_pase'] = $this->build_fields($fake_model_adjuntos_pase->fields);

        $adjuntos_tramite = $this->Adjuntos_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('tr_adjuntos_tipos', 'tr_adjuntos_tipos.id = tr_adjuntos.tipo_id', 'LEFT', array("tr_adjuntos_tipos.nombre as tipo"))
            )
        ));
        $data['adjuntos_tramite'] = $adjuntos_tramite;

        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('tr_estados EO', 'EO.id = tr_pases.estado_origen_id', 'LEFT'),
                array('tr_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "CONCAT(EO.nombre, ' (', OO.nombre, ')') as estado_origen"),
                array('tr_estados ED', 'ED.id = tr_pases.estado_destino_id', 'LEFT'),
                array('tr_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "CONCAT(ED.nombre, ' (', OD.nombre, ')') as estado_destino")
            )
        ));

        $data['ultimo_pase'] = $ultimo_pase;
        $data['tramite'] = $tramite;
        if ($ultimo_pase->ed_id === '5' && empty($tramite->transferencia_nro)) //Generación de Boletos de Deuda y Aforo (HC)
        {
            $data['generar_numero'] = TRUE;
        }
        else
        {
            $data['generar_numero'] = FALSE;
        }
        if (in_groups($this->grupos_publico, $this->grupos))
        {
            $data['back_url'] = 'bandeja_entrada_publico';
        }
        else
        {
            $data['back_url'] = 'bandeja_entrada';
        }
        $data['txt_btn'] = 'Enviar';
        $data['title_view'] = 'Revisar Trámite';
        $data['title'] = TITLE . ' - Revisar Trámite';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $this->load_template('transferencias/tramites/tramites_revisar', $data);
    }

    public function ver($id = NULL, $back_url = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite))
        {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        $vendedores = $this->Intervinientes_model->get(array('tramite_id' => $tramite->id, 'tipo' => 'Vendedor'));
        if (empty($vendedores))
        {
            show_error('No se encontró el Vendedor', 500, 'Registro no encontrado');
        }

        $compradores = $this->Intervinientes_model->get(array(
            'tramite_id' => $tramite->id,
            'tipo' => 'Comprador',
            'join' => array(
                array('domicilios', "domicilios.id = tr_intervinientes.domicilio_id", 'LEFT',
                    array(
                        'domicilios.calle',
                        'domicilios.barrio',
                        'domicilios.altura',
                        'domicilios.piso',
                        'domicilios.dpto',
                        'domicilios.manzana',
                        'domicilios.casa',
                        'domicilios.localidad_id'
                    )
                ),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
                    array(
                        "CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
                    )
                ),
            )
        ));
        if (empty($compradores))
        {
            show_error('No se encontró el Comprador', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Escribanos_model->get_user_id($tramite->escribano_id) !== $this->session->userdata('user_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model_tramites = new stdClass();
        $fake_model_tramites->fields = array(
            'fecha_inicio' => array('label' => 'Inicio', 'type' => 'datetime', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
            'relacionado' => array('label' => 'Trámite relacionado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'escritura_nro' => array('label' => 'Escritura N°', 'type' => 'integer', 'maxlength' => '9'),
            'escritura_foja' => array('label' => 'Escritura Foja', 'type' => 'integer', 'maxlength' => '9'),
            'escritura_fecha' => array('label' => 'Escritura Fecha', 'type' => 'date'),
            'transferencia_nro' => array('label' => 'Transferencia N°', 'type' => 'integer', 'maxlength' => '9'),
            'transferencia_eje' => array('label' => 'Transferencia Ejercicio', 'type' => 'integer', 'maxlength' => '9'),
            'fecha_fin' => array('label' => 'Fin', 'type' => 'datetime')
        );

        $fake_model_escribano = new stdClass();
        $fake_model_escribano->fields = array(
            'matricula_nro' => array('label' => 'Matrícula N°', 'maxlength' => '50'),
            'registro_nro' => array('label' => 'Registro N°', 'maxlength' => '50'),
            'registro_tipo' => array('label' => 'Registro Tipo', 'maxlength' => '50'),
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
        );

        $cant_v = sizeof($vendedores);
        for ($i = 1; $i <= $cant_v; $i++)
        {
            $fake_model_vendedor_{$i} = new stdClass();
            $fake_model_vendedor_{$i}->fields = array(
                "porcentaje_v_$i" => array('label' => 'Porcentaje', 'type' => 'numeric', 'required' => TRUE),
                "cuil_v_$i" => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_v_$i" => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
                "apellido_v_$i" => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
                "email_v_$i" => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
            );
        }

        $cant_c = sizeof($compradores);
        for ($i = 1; $i <= $cant_c; $i++)
        {
            $fake_model_comprador_{$i} = new stdClass();
            $fake_model_comprador_{$i}->fields = array(
                "porcentaje_c_$i" => array('label' => 'Porcentaje', 'type' => 'numeric', 'required' => TRUE),
                "cuil_c_$i" => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
                "nombre_c_$i" => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
                "apellido_c_$i" => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
                "email_c_$i" => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
                "calle_c_$i" => array('label' => 'Calle', 'maxlength' => '50', 'required' => TRUE),
                "barrio_c_$i" => array('label' => 'Barrio', 'maxlength' => '50'),
                "altura_c_$i" => array('label' => 'Altura', 'maxlength' => '10', 'required' => TRUE),
                "piso_c_$i" => array('label' => 'Piso', 'maxlength' => '10'),
                "dpto_c_$i" => array('label' => 'Dpto', 'maxlength' => '10'),
                "manzana_c_$i" => array('label' => 'Manzana', 'maxlength' => '10'),
                "casa_c_$i" => array('label' => 'Casa', 'maxlength' => '10'),
                "localidad_c_$i" => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
            );
        }

        $data['error'] = $this->session->flashdata('error');

        $data['fields_tramite'] = $this->build_fields($fake_model_tramites->fields, $tramite, TRUE);
        $data['fields_escribano'] = $this->build_fields($fake_model_escribano->fields, $tramite, TRUE);
        $data['fields_inmueble'] = $this->build_fields($this->Inmuebles_model->fields, $tramite, TRUE);
        for ($i = 1; $i <= $cant_v; $i++)
        {
            $temp_vendedor = new stdClass();
            $temp_vendedor->{"porcentaje_v_$i"} = $vendedores[$i - 1]->porcentaje;
            $temp_vendedor->{"cuil_v_$i"} = $vendedores[$i - 1]->cuil;
            $temp_vendedor->{"nombre_v_$i"} = $vendedores[$i - 1]->nombre;
            $temp_vendedor->{"apellido_v_$i"} = $vendedores[$i - 1]->apellido;
            $temp_vendedor->{"email_v_$i"} = $vendedores[$i - 1]->email;
            $data['fields_vendedores'][] = $this->build_fields($fake_model_vendedor_{$i}->fields, $temp_vendedor, TRUE);
        }
        for ($i = 1; $i <= $cant_c; $i++)
        {
            $temp_comprador = new stdClass();
            $temp_comprador->{"porcentaje_c_$i"} = $compradores[$i - 1]->porcentaje;
            $temp_comprador->{"cuil_c_$i"} = $compradores[$i - 1]->cuil;
            $temp_comprador->{"nombre_c_$i"} = $compradores[$i - 1]->nombre;
            $temp_comprador->{"apellido_c_$i"} = $compradores[$i - 1]->apellido;
            $temp_comprador->{"email_c_$i"} = $compradores[$i - 1]->email;
            $temp_comprador->{"calle_c_$i"} = $compradores[$i - 1]->calle;
            $temp_comprador->{"barrio_c_$i"} = $compradores[$i - 1]->barrio;
            $temp_comprador->{"altura_c_$i"} = $compradores[$i - 1]->altura;
            $temp_comprador->{"piso_c_$i"} = $compradores[$i - 1]->piso;
            $temp_comprador->{"dpto_c_$i"} = $compradores[$i - 1]->dpto;
            $temp_comprador->{"manzana_c_$i"} = $compradores[$i - 1]->manzana;
            $temp_comprador->{"casa_c_$i"} = $compradores[$i - 1]->casa;
            $temp_comprador->{"localidad_c_$i"} = $compradores[$i - 1]->localidad;
            $data['fields_compradores'][] = $this->build_fields($fake_model_comprador_{$i}->fields, $temp_comprador, TRUE);
        }

        $adjuntos_tramite = $this->Adjuntos_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('tr_adjuntos_tipos', 'tr_adjuntos_tipos.id = tr_adjuntos.tipo_id', 'LEFT', array("tr_adjuntos_tipos.nombre as tipo"))
            )
        ));
        $data['adjuntos_tramite'] = $adjuntos_tramite;

        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('tr_estados EO', 'EO.id = tr_pases.estado_origen_id', 'LEFT'),
                array('tr_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "CONCAT(EO.nombre, ' (', OO.nombre, ')') as estado_origen"),
                array('tr_estados ED', 'ED.id = tr_pases.estado_destino_id', 'LEFT'),
                array('tr_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "CONCAT(ED.nombre, ' (', OD.nombre, ')') as estado_destino")
            )
        ));

        $data['tramite'] = $tramite;
        if (!empty($back_url))
        {
            $data['back_url'] = $back_url;
        }
        else
        {
            if (in_groups($this->grupos_publico, $this->grupos))
            {
                $data['back_url'] = 'listar_publico';
            }
            else
            {
                $data['back_url'] = 'listar';
            }
        }
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Trámite';
        $data['title'] = TITLE . ' - Ver Trámite';
        $this->load_template('transferencias/tramites/tramites_revisar', $data);
    }

    public function generar_numero_transferencia()
    {
        if (!in_groups($this->grupos_municipal, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['grupo'] = 'admin';
        }
        elseif (in_groups($this->grupos_municipal, $this->grupos))
        {
            $data['grupo'] = 'municipal';
        }

        $datos['no_data'] = TRUE;
        $this->form_validation->set_rules('tramite_id', 'Trámite', 'integer|required');
        if ($this->form_validation->run() === TRUE)
        {
            $ultimo_pase = $this->Pases_model->get_acceso_ultimo($this->input->post('tramite_id'), $this->session->userdata('user_id'), $data['grupo']);
            if (empty($ultimo_pase->p_id) || $ultimo_pase->ed_id !== '5') //Generación de Boletos de Deuda y Aforo (HC)
            {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            }

            $fecha = new DateTime();
            $ejercicio = $fecha->format('Y');
            $result = $this->Tramites_model->generar_transferencia($this->input->post('tramite_id'), $ejercicio);
            if ($result)
            {
                $tramite = $this->Tramites_model->get(array('id' => $this->input->post('tramite_id')));
                if (!empty($tramite))
                {
                    unset($datos['no_data']);
                    $datos['numero'] = $tramite->transferencia_nro;
                    $datos['ejercicio'] = $tramite->transferencia_eje;
                }
            }
        }

        echo json_encode($datos);
    }

    private function informar_pase($tramite_id)
    {
        $ultimo_pase = $this->Pases_model->get_ultimo($tramite_id);
        if (!empty($ultimo_pase))
        {
            if ($ultimo_pase->estado_destino_id === '12') //Finalizado (HC)
            {
                $data['nombre'] = $ultimo_pase->escribano;
                $data['tramite'] = $ultimo_pase->tramite_id;
                $data['estado'] = $ultimo_pase->estado_destino;
                $data['observaciones'] = $ultimo_pase->observaciones;
                $data['escribano'] = TRUE;
                $this->send_email('transferencias/email/tramites_finalizado', 'Trámite Finalizado', $ultimo_pase->email_escribano, $data);
            }
            else
            {
                return;
                if ($ultimo_pase->oficina_destino_id === '1') //Escribano (HC)
                {
                    $data['nombre'] = $ultimo_pase->escribano;
                    $data['tramite'] = $ultimo_pase->tramite_id;
                    $data['estado'] = $ultimo_pase->estado_destino;
                    $data['escribano'] = TRUE;
                    $this->send_email('transferencias/email/tramites_pase', 'Pase recibido', $ultimo_pase->email_escribano, $data);
                }
                else
                {
                    $this->load->model('transferencias/Usuarios_oficinas_model');
                    $usuarios_oficina = $this->Usuarios_oficinas_model->get(array(
                        'oficina_id' => $ultimo_pase->oficina_destino_id,
                        'join' => array(
                            array('users', 'users.id = tr_usuarios_oficinas.user_id', 'LEFT'),
                            array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                            array('groups', 'users_groups.group_id = groups.id', 'LEFT'),
                            array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as empleado, personas.email as email_empleado"))
                        ),
                        'where' => array(
                            array('column' => 'groups.name IN', 'value' => "('transferencias_municipal', 'transferencias_area')", 'override' => TRUE),
                            array('column' => 'users.active', 'value' => '1')
                        ))
                    );
                    if (!empty($usuarios_oficina))
                    {
                        foreach ($usuarios_oficina as $Usuario)
                        {
                            $data['nombre'] = $Usuario->empleado;
                            $data['tramite'] = $ultimo_pase->tramite_id;
                            $data['estado'] = $ultimo_pase->estado_destino;
                            $data['escribano'] = FALSE;
                            $this->send_email('transferencias/email/tramites_pase', 'Pase recibido', $Usuario->email_empleado, $data);
                        }
                    }
                }
            }
        }
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

    public function imprimir_detalle_tramite($id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), 'admin');
        if (empty($ultimo_pase->p_id) || $ultimo_pase->ed_id !== '12') //Finalizado (HC)
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramite = $this->Tramites_model->get_one($id);
        if (empty($tramite))
        {
            show_error('No se encontró el Trámite', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Escribanos_model->get_user_id($tramite->escribano_id) !== $this->session->userdata('user_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $pases = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'estado_destino_id' => 12, // (HC) FINALIZADO
            'sort_by' => 'fecha'
        ));
        if (empty($pases))
        {
            $pases = array();
        }

        $vendedores = $this->Intervinientes_model->get(array('tramite_id' => $tramite->id, 'tipo' => 'Vendedor'));
        if (empty($vendedores))
        {
            show_error('No se encontró el Vendedor', 500, 'Registro no encontrado');
        }

        $compradores = $this->Intervinientes_model->get(array(
            'tramite_id' => $tramite->id,
            'tipo' => 'Comprador',
            'join' => array(
                array('domicilios', "domicilios.id = tr_intervinientes.domicilio_id", 'LEFT',
                    array(
                        'domicilios.calle',
                        'domicilios.barrio',
                        'domicilios.altura',
                        'domicilios.piso',
                        'domicilios.dpto',
                        'domicilios.manzana',
                        'domicilios.casa',
                        'domicilios.localidad_id'
                    )
                ),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
                    array(
                        "CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
                    )
                ),
            )
        ));
        if (empty($compradores))
        {
            show_error('No se encontró el Comprador', 500, 'Registro no encontrado');
        }

        $data = array(
            'tramite' => $tramite,
            'vendedores' => $vendedores,
            'compradores' => $compradores,
            'pases' => $pases
        );
        $html = $this->load->view('transferencias/tramites/tramites_resumen_pdf', $data, TRUE);
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'format' => 'A4',
            'margin_left' => 6,
            'margin_right' => 6,
            'margin_top' => 6,
            'margin_bottom' => 15,
            'margin_header' => 9,
            'margin_footer' => 9
        ]);
        $mpdf->SetDisplayMode('fullwidth');
        $mpdf->pagenumPrefix = 'Página ';
        $mpdf->SetTitle("Trámite");
        $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
        $mpdf->SetFooter("{PAGENO} de {nb}");
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output('tramite.pdf', 'I');
    }
}
