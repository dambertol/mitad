<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites extends MY_Controller
{

    /**
     * Controlador de Trámites
     * Autor: Leandro
     * Creado: 17/03/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
        $this->load->model('tramites_online/Adjuntos_model');
        $this->load->model('tramites_online/Adjuntos_tipos_model');
        $this->load->model('tramites_online/Tramites_model');
        $this->load->model('tramites_online/Tramites_categorias_model');
        $this->load->model('tramites_online/Tramites_tipos_model');
        $this->load->model('tramites_online/Estados_secuencias_model');
        $this->load->model('Areas_model');
        $this->load->model('Personas_model');
        $this->load->model('Nacionalidades_model');
        $this->load->model('tramites_online/Pases_model');
        $this->load->model('Oro_model');
        $this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
        $this->grupos_admin = array('admin', 'tramites_online_admin', 'tramites_online_consulta_general');
        $this->grupos_publico = array('tramites_online_publico');
        $this->grupos_area = array('tramites_online_area');
        $this->grupos_solo_consulta = array('tramites_online_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 17),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Persona', 'data' => 'persona', 'width' => 14),
                array('label' => 'Ubicación', 'data' => 'area', 'width' => 18),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'tramites_online/tramites/listar_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['crear'] = 'agregar_admin';
        }
        else
        {
            $data['crear'] = 'agregar';
        }
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Consultas';
        $data['title'] = TITLE . ' - Consultas';
        $this->load_template('tramites_online/tramites/tramites_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('tramites_online/datatables_functions_helper');
        $dt = $this->datatables
                ->select("to_tramites.id, to_tramites.fecha_inicio, CONCAT(to_tramites_categorias.nombre, ' - ', to_tramites_tipos.nombre) as tipo, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil,  ')') as persona, to_tramites.padron as padron, CONCAT(areas.codigo, ' - ', areas.nombre) as area, to_pases.estado_destino_id as estado_id, to_estados.nombre as estado, to_pases.fecha as ultimo_mov, to_estados.id as ult_estado_id")
                ->from('to_tramites')
                ->join('to_tramites_tipos', 'to_tramites_tipos.id = to_tramites.tipo_id', 'left')
                ->join('to_tramites_categorias', 'to_tramites_categorias.id = to_tramites_tipos.categoria_id', 'left')
                ->join('to_pases', 'to_pases.tramite_id = to_tramites.id', 'left')
                ->join('to_pases P', 'P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha', 'left outer')
                ->join('to_estados', 'to_estados.id = to_pases.estado_destino_id', 'left')
                ->join('areas', 'areas.id = to_pases.area_destino_id', 'left')
                ->join('personas', 'personas.id = to_tramites.persona_id', 'left')
                ->where('P.id IS NULL')
                ->add_column('ver', '<a href="tramites_online/tramites/ver/$1/listar" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_tramites_listar_editar(ult_estado_id, id)');

        if (in_groups($this->grupos_area, $this->grupos))
        {
            $dt->join('to_usuarios_areas', 'to_usuarios_areas.area_id = to_tramites_tipos.area_id ', 'left')
                    ->where('to_usuarios_areas.user_id', $this->session->userdata('user_id'));
        }

        echo $this->datatables->generate();
    }

    public function bandeja_entrada()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 14),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Persona', 'data' => 'persona', 'width' => 14),
                array('label' => 'Ubicación', 'data' => 'area', 'width' => 18),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'tramites_online/tramites/bandeja_entrada_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['crear'] = 'agregar_admin';
        }
        else
        {
            $data['crear'] = 'agregar';
        }
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Bandeja de Entrada';
        $data['title'] = TITLE . ' - Bandeja de Entrada';
        $this->load_template('tramites_online/tramites/tramites_listar_bandeja', $data);
    }

    public function bandeja_entrada_data()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('tramites_online/datatables_functions_helper');
        $dt = $this->datatables
                ->select("to_tramites.id, to_tramites.fecha_inicio, CONCAT(to_tramites_categorias.nombre, ' - ', to_tramites_tipos.nombre) as tipo, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil,  ')') as persona, to_tramites.padron as padron, CONCAT(areas.codigo, ' - ', areas.nombre) as area, to_estados.nombre as estado, to_pases.fecha as ultimo_mov, to_estados.id as ult_estado_id")
                ->from('to_tramites')
                ->join('to_tramites_tipos', 'to_tramites_tipos.id = to_tramites.tipo_id', 'left')
                ->join('to_tramites_categorias', 'to_tramites_categorias.id = to_tramites_tipos.categoria_id', 'left')
                ->join('to_pases', 'to_pases.tramite_id = to_tramites.id', 'left')
                ->join('to_pases P', 'P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha', 'left outer')
                ->join('to_estados', 'to_estados.id = to_pases.estado_destino_id', 'left')
                ->join('areas', 'areas.id = to_pases.area_destino_id', 'left')
                ->join('personas', 'personas.id = to_tramites.persona_id', 'left')
                ->where('P.id IS NULL')
                ->where('to_pases.estado_destino_id NOT IN (2,3)'); // HC: Finalizado || Cancelado

        if (in_groups($this->grupos_area, $this->grupos))
        {
            $dt->join('to_usuarios_areas', 'to_usuarios_areas.area_id = areas.id ', 'left')
                    ->where('to_usuarios_areas.user_id', $this->session->userdata('user_id'));
        }

        $dt->add_column('ver', '<a href="tramites_online/tramites/ver/$1/bandeja_entrada" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
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
                array('label' => 'N°', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 17),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Ubicación', 'data' => 'area', 'width' => 19),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 19),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'tramites_online/tramites/listar_publico_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['crear'] = 'agregar';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Consultas';
        $data['title'] = TITLE . ' - Consultas';
        $this->load_template('tramites_online/tramites/tramites_listar_publico', $data);
    }

    public function listar_publico_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('tramites_online/datatables_functions_helper');
        $this->datatables
                ->select("to_tramites.id, to_tramites.fecha_inicio, CONCAT(to_tramites_categorias.nombre, ' - ', to_tramites_tipos.nombre) as tipo, to_tramites.padron as padron, CONCAT(areas.codigo, ' - ', areas.nombre) as area, to_pases.estado_destino_id as estado_id, to_estados.nombre as estado, to_pases.fecha as ultimo_mov")
                ->from('to_tramites')
                ->join('to_tramites_tipos', 'to_tramites_tipos.id = to_tramites.tipo_id', 'left')
                ->join('to_tramites_categorias', 'to_tramites_categorias.id = to_tramites_tipos.categoria_id', 'left')
                ->join('to_pases', 'to_pases.tramite_id = to_tramites.id', 'left')
                ->join('to_pases P', 'P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha', 'left outer')
                ->join('to_estados', 'to_estados.id = to_pases.estado_destino_id', 'left')
                ->join('areas', 'areas.id = to_pases.area_destino_id', 'left')
                ->join('personas', 'personas.id = to_tramites.persona_id', 'left')
                ->join('users', 'users.persona_id = personas.id', 'left')
                ->where('P.id IS NULL')
                ->where('users.id', $this->session->userdata('user_id'))
                ->add_column('ver', '<a href="tramites_online/tramites/ver/$1/listar_publico" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '', 'id');

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
                array('label' => 'N°', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 14),
                array('label' => 'Padrón', 'data' => 'padron', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Ubicación', 'data' => 'area', 'width' => 20),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 18),
                array('label' => 'Últ. Movim', 'data' => 'ultimo_mov', 'width' => 12, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_table',
            'source_url' => 'tramites_online/tramites/bandeja_entrada_publico_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['crear'] = 'agregar';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Bandeja de Entrada';
        $data['title'] = TITLE . ' - Bandeja de Entrada';
        $this->load_template('tramites_online/tramites/tramites_listar_bandeja_publico', $data);
    }

    public function bandeja_entrada_publico_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('tramites_online/datatables_functions_helper');
        $dt = $this->datatables
                ->select("to_tramites.id, to_tramites.fecha_inicio, CONCAT(to_tramites_categorias.nombre, ' - ', to_tramites_tipos.nombre) as tipo, to_tramites.padron as padron, areas.nombre as area, to_estados.nombre as estado, to_pases.fecha as ultimo_mov, to_estados.id as ult_estado_id")
                ->from('to_tramites')
                ->join('to_tramites_tipos', 'to_tramites_tipos.id = to_tramites.tipo_id', 'left')
                ->join('to_tramites_categorias', 'to_tramites_categorias.id = to_tramites_tipos.categoria_id', 'left')
                ->join('to_pases', 'to_pases.tramite_id = to_tramites.id', 'left')
                ->join('to_pases P', 'P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha', 'left outer')
                ->join('to_estados', 'to_estados.id = to_pases.estado_destino_id', 'left')
                ->join('areas', 'areas.id = to_pases.area_destino_id', 'left')
                ->join('personas', 'personas.id = to_tramites.persona_id', 'left')
                ->join('users', 'users.persona_id = personas.id', 'left')
                ->where('P.id IS NULL')
                ->where('areas.id IS NULL'); //Persona (HC)

        if (in_groups($this->grupos_publico, $this->grupos))
        {
            $dt->where('users.id', $this->session->userdata('user_id'));
        }

        $dt->add_column('ver', '<a href="tramites_online/tramites/ver/$1/bandeja_entrada_publico" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_tramites_editar(ult_estado_id, id)');

        echo $dt->generate();
    }

    public function agregar()
    {
//		if (!in_groups($this->grupos_publico, $this->grupos))
//		{
//			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
//		}

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('tramites_online/tramites/listar_publico', 'refresh');
        }

        $persona = $this->Personas_model->get(array(
            'select' => array('personas.id', 'personas.cuil', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email'),
            'join' => array(
                array('domicilios', 'domicilios.id = personas.domicilio_id', 'LEFT'),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array('users.id = ' . $this->session->userdata('user_id'))
        ));
        if (empty($persona))
        {
            show_error('No se encontró la Persona', 500, 'Registro no encontrado');
        }

        $fake_model_persona = new stdClass();
        $fake_model_persona->fields = array(
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13'),
            'dni' => array('label' => 'DNI', 'type' => 'cuil', 'maxlength' => '8'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
        );

        $fake_model_inmueble = new stdClass();
        $fake_model_inmueble->fields = array(
            'padron' => array('label' => 'Padrón', 'type' => 'integer', 'maxlength' => '6')
        );

        $fake_model_adjuntos = new stdClass();
        $fake_model_adjuntos->fields = array(
            'otros[]' => array('label' => 'Otros adjuntos', 'type' => 'file', 'id_name' => 'otros', 'is_multiple' => TRUE)
        );

        $this->array_categoria_control = $array_categoria = $this->get_array('Tramites_categorias', 'nombre', 'id', array(
            'join' => array(
                array('to_tramites_tipos', 'to_tramites_tipos.categoria_id = to_tramites_categorias.id', 'left')
            ),
            'where' => array(
                array('column' => 'to_tramites_tipos.visibilidad', 'value' => 'Público')
            ),
            'group_by' => 'to_tramites_categorias.id, to_tramites_categorias.nombre'
                )
        );
        if ($this->input->post('categoria'))
        {
            $this->array_tipo_control = $array_tipo = $this->get_array('Tramites_tipos', 'nombre', 'id', array(
                'where' => array(
                    array('column' => 'to_tramites_tipos.visibilidad', 'value' => 'Público'),
                    array('column' => 'to_tramites_tipos.categoria_id', 'value' => $this->input->post('categoria'))
                )
                    )
            );
        }
        else
        {
            $this->array_tipo_control = $array_tipo = array();
        }
        $this->set_model_validation_rules($this->Tramites_model);
        $this->set_model_validation_rules($fake_model_inmueble);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha = new DateTime();
            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Tramites_model->create(array(
                'fecha_inicio' => $fecha->format('Y-m-d H:i:s'),
                'tipo_id' => $this->input->post('tipo'),
                'persona_id' => $persona[0]->id,
                'padron' => $this->input->post('padron'),
                'observaciones' => $this->input->post('observaciones')), FALSE);

            $tramite_id = $this->Tramites_model->get_row_id();

            $config_adjuntos['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($tramite_id, 6, "0", STR_PAD_LEFT) . "/";
            if (!file_exists($config_adjuntos['upload_path']))
            {
                mkdir($config_adjuntos['upload_path'], 0755, TRUE);
            }
            $config_adjuntos['encrypt_name'] = TRUE;
            $config_adjuntos['file_ext_tolower'] = TRUE;
            $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
            $config_adjuntos['max_size'] = 8192;
            $this->load->library('upload', $config_adjuntos);

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

                $tramite_tipo = $this->Tramites_tipos_model->get(array('id' => $this->input->post('tipo')));
                $trans_ok &= $this->Pases_model->create(array(
                    'tramite_id' => $tramite_id,
                    'estado_origen_id' => 4, //Iniciado (HC)
                    'estado_destino_id' => 1, //Pendiente (HC)
                    'area_origen_id' => NULL, //Vecino
                    'area_destino_id' => $tramite_tipo->area_id,
                    'fecha' => $fecha->format('Y-m-d H:i:s'),
                    'usuario_origen' => $this->session->userdata('user_id')), FALSE);
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->send_email('tramites_online/email/tramites_iniciado', 'Consulta Iniciada', $tramite_tipo->email_responsable, array('tramite' => $tramite_id));
                $this->session->set_flashdata('message', $this->Tramites_model->get_msg());
                if (in_groups($this->grupos_area, $this->grupos))
                {
                    redirect('tramites_online/tramites/listar', 'refresh');
                }
                else
                {
                    redirect('tramites_online/tramites/listar_publico', 'refresh');
                }
            }
            else
            {
                $this->db->trans_rollback();
                if (!empty($upload_otros))
                {
                    foreach ($upload_otros as $Upload_otro)
                    {
                        unlink($config_adjuntos['upload_path'] . $Upload_otro['file_name']);
                    }
                }
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tramites_model->get_error())
                {
                    $error_msg .= $this->Tramites_model->get_error();
                }
                if ($this->Adjuntos_model->get_error())
                {
                    $error_msg .= $this->Adjuntos_model->get_error();
                }
                if ($this->Pases_model->get_error())
                {
                    $error_msg .= $this->Pases_model->get_error();
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Tramites_model->fields['tipo']['array'] = $array_tipo;
        $this->Tramites_model->fields['categoria']['array'] = $array_categoria;
        $data['fields_tramite'] = $this->build_fields($this->Tramites_model->fields);
        $data['fields_persona'] = $this->build_fields($fake_model_persona->fields, $persona[0], TRUE);
        $data['fields_inmueble'] = $this->build_fields($fake_model_inmueble->fields);
        $data['fields_adjunto'] = $this->build_fields($fake_model_adjuntos->fields);

        $data['back_url'] = 'listar_publico';
        $data['txt_btn'] = 'Iniciar Consulta';
        $data['title_view'] = 'Iniciar Consulta';
        $data['title'] = TITLE . ' - Iniciar Consulta';
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

    public function agregar_admin()
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('tramites_online/tramites/listar_publico', 'refresh');
        }

        $fake_model_pers_existente = new stdClass();
        $fake_model_pers_existente->fields = array(
            'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        unset($this->Personas_model->fields['cuil']['required']);
        unset($this->Personas_model->fields['email']['required']);
        unset($this->Personas_model->fields['nacionalidad']['required']);
        $this->Personas_model->fields['nacionalidad']['bselect_title'] = 'null';
        $this->Personas_model->set_unique_dni();
        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array('select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona", 'sort_by' => 'personas.apellido, personas.nombre'), array('agregar' => '-- Agregar Persona --'));
        $this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
        $this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre', 'id', NULL, array(NULL => '-- Desconocida --'));

        $fake_model_inmueble = new stdClass();
        $fake_model_inmueble->fields = array(
            'padron' => array('label' => 'Padrón', 'type' => 'integer', 'maxlength' => '6')
        );

        $fake_model_adjuntos = new stdClass();
        $fake_model_adjuntos->fields = array(
            'otros[]' => array('label' => 'Otros adjuntos', 'type' => 'file', 'id_name' => 'otros', 'is_multiple' => TRUE)
        );

        $this->array_categoria_control = $array_categoria = $this->get_array('Tramites_categorias', 'nombre');
        if ($this->input->post('categoria'))
        {
            $this->array_tipo_control = $array_tipo = $this->get_array('Tramites_tipos', 'nombre', 'id', array(
                'where' => array(
                    array('column' => 'to_tramites_tipos.categoria_id', 'value' => $this->input->post('categoria')))
                    )
            );
        }
        else
        {
            $this->array_tipo_control = $array_tipo = array();
        }
        $this->set_model_validation_rules($this->Tramites_model);
        if (!empty($_POST) && $_POST['persona'] === 'agregar')
        {
            $this->set_model_validation_rules($this->Personas_model);
        }
        $this->set_model_validation_rules($fake_model_inmueble);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha = new DateTime();
            $this->db->trans_begin();
            $trans_ok = TRUE;
            if (!empty($_POST) && $_POST['persona'] === 'agregar')
            {
                $trans_ok &= $this->Personas_model->create(array(
                    'dni' => $this->input->post('dni'),
                    'sexo' => $this->input->post('sexo'),
                    'cuil' => $this->input->post('cuil'),
                    'nombre' => $this->input->post('nombre'),
                    'apellido' => $this->input->post('apellido'),
                    'telefono' => $this->input->post('telefono'),
                    'celular' => $this->input->post('celular'),
                    'email' => strtolower($this->input->post('email')),
                    'fecha_nacimiento' => $this->get_date_sql('fecha_nacimiento'),
                    'nacionalidad_id' => $this->input->post('nacionalidad')), FALSE);

                if ($this->Personas_model->get_error())
                {
                    $trans_ok = FALSE;
                }
                $persona_id = $this->Personas_model->get_row_id();
                $dni = $this->input->post('dni');
            }
            else
            {
                $persona_id = $this->input->post('persona');
                $persona = $this->Personas_model->get_one($persona_id);
                if (empty($persona))
                {
                    show_error('No se encontró la Persona', 500, 'Registro no encontrado');
                }
                $dni = $persona->dni;
            }

            if ($trans_ok)
            {
                $trans_ok &= $this->Tramites_model->create(array(
                    'fecha_inicio' => $fecha->format('Y-m-d H:i:s'),
                    'tipo_id' => $this->input->post('tipo'),
                    'persona_id' => $persona_id,
                    'padron' => $this->input->post('padron'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $tramite_id = $this->Tramites_model->get_row_id();

                $config_adjuntos['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($tramite_id, 6, "0", STR_PAD_LEFT) . "/";
                if (!file_exists($config_adjuntos['upload_path']))
                {
                    mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                }
                $config_adjuntos['encrypt_name'] = TRUE;
                $config_adjuntos['file_ext_tolower'] = TRUE;
                $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config_adjuntos['max_size'] = 8192;
                $this->load->library('upload', $config_adjuntos);

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
            }

            if ($trans_ok)
            {
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

                $tramite_tipo = $this->Tramites_tipos_model->get(array('id' => $this->input->post('tipo')));
                $trans_ok &= $this->Pases_model->create(array(
                    'tramite_id' => $tramite_id,
                    'estado_origen_id' => 4, //Iniciado (HC)
                    'estado_destino_id' => 1, //Pendiente (HC)
                    'area_origen_id' => NULL, //Vecino
                    'area_destino_id' => $tramite_tipo->area_id,
                    'fecha' => $fecha->format('Y-m-d H:i:s'),
                    'usuario_origen' => $this->session->userdata('user_id')), FALSE);
            }

            if (SIS_ORO_ACTIVE)
            {
                // ORO CRM
                if (!empty($_POST) && $_POST['persona'] === 'agregar')
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
                        $datos['tags'] = 'Consultas Online';
                        $this->Oro_model->send_data($datos);
                    }
                }
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->send_email('tramites_online/email/tramites_iniciado', 'Consulta Iniciada', $tramite_tipo->email_responsable, array('tramite' => $tramite_id));
                $this->session->set_flashdata('message', $this->Tramites_model->get_msg());
                redirect('tramites_online/tramites/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                if (!empty($upload_otros))
                {
                    foreach ($upload_otros as $Upload_otro)
                    {
                        unlink($config_adjuntos['upload_path'] . $Upload_otro['file_name']);
                    }
                }
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Personas_model->get_error())
                {
                    $error_msg .= $this->Personas_model->get_error();
                }
                if ($this->Tramites_model->get_error())
                {
                    $error_msg .= $this->Tramites_model->get_error();
                }
                if ($this->Adjuntos_model->get_error())
                {
                    $error_msg .= $this->Adjuntos_model->get_error();
                }
                if ($this->Pases_model->get_error())
                {
                    $error_msg .= $this->Pases_model->get_error();
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Tramites_model->fields['tipo']['array'] = $array_tipo;
        $this->Tramites_model->fields['categoria']['array'] = $array_categoria;
        $data['fields_tramite'] = $this->build_fields($this->Tramites_model->fields);

        $fake_model_pers_existente->fields['persona']['array'] = $array_persona;
        $data['fields_persona_existente'] = $this->build_fields($fake_model_pers_existente->fields);

        $this->Personas_model->fields['sexo']['array'] = $array_sexo;
        $this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
        $data['fields_persona'] = $this->build_fields($this->Personas_model->fields);

        $data['fields_inmueble'] = $this->build_fields($fake_model_inmueble->fields);
        $data['fields_adjunto'] = $this->build_fields($fake_model_adjuntos->fields);

        $data['back_url'] = 'listar_publico';
        $data['txt_btn'] = 'Iniciar Consulta';
        $data['title_view'] = 'Iniciar Consulta';
        $data['title'] = TITLE . ' - Iniciar Consulta';
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
        $this->load_template('tramites_online/tramites/tramites_alta_admin', $data);
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
            show_error('No se encontró la Consulta', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Personas_model->get_user_id($tramite->persona_id) !== $this->session->userdata('user_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['grupo'] = 'admin';
            $area_destino = NULL;
        }
        elseif (in_groups($this->grupos_area, $this->grupos))
        {
            $data['grupo'] = 'area';
            $area_destino = NULL;
        }
        else
        {
            $data['grupo'] = 'publico';
            $tramite_tipo = $this->Tramites_tipos_model->get(array('id' => $tramite->tipo_id));
            $area_destino = $tramite_tipo->area_id;
        }

        $ultimo_pase = $this->Pases_model->get_acceso_ultimo($id, $this->session->userdata('user_id'), $data['grupo']);
        if (empty($ultimo_pase->p_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model_tramites = new stdClass();
        $fake_model_tramites->fields = array(
            'fecha_inicio' => array('label' => 'Inicio', 'type' => 'datetime', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
            'fecha_fin' => array('label' => 'Fin', 'type' => 'datetime')
        );

        $fake_model_persona = new stdClass();
        $fake_model_persona->fields = array(
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13'),
            'dni' => array('label' => 'DNI', 'type' => 'cuil', 'maxlength' => '8'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
        );

        $fake_model_inmueble = new stdClass();
        $fake_model_inmueble->fields = array(
            'padron' => array('label' => 'Padrón', 'type' => 'integer', 'maxlength' => '6', 'required' => TRUE)
        );

        $fake_model_pase = new stdClass();
        if ($this->input->post('destino') === '3') //Cancelado (HC)
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

        $fake_model_adjuntos_pase = new stdClass();
        $fake_model_adjuntos_pase->fields = array(
            'adjuntos[]' => array('label' => 'Adjuntos', 'type' => 'file', 'id_name' => 'adjuntos', 'is_multiple' => TRUE)
        );

        if (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_area, $this->grupos))
        {
            $estados_posteriores = $this->Estados_secuencias_model->get(array(
                'estado_id' => $ultimo_pase->ed_id,
                'join' => array(
                    array('to_estados', 'to_estados.id = to_estados_secuencias.estado_posterior_id', 'LEFT', 'to_estados.nombre as estado_posterior')
                )
            ));
        }
        else
        {
            $estados_posteriores = $this->Estados_secuencias_model->get(array(
                'estado_id' => $ultimo_pase->ed_id,
                'join' => array(
                    array('to_estados', 'to_estados.id = to_estados_secuencias.estado_posterior_id', 'LEFT', 'to_estados.nombre as estado_posterior')
                ),
                'where' => array('estado_posterior_id NOT IN (2,3)') //HC: Finalizado || Cancelado
            ));
        }

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
                    'area_origen_id' => $ultimo_pase->ad_id,
                    'area_destino_id' => $area_destino,
                    'fecha' => $fecha->format('Y-m-d H:i:s'),
                    'usuario_origen' => $this->session->userdata('user_id'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $pase_id = $this->Pases_model->get_row_id();

                if ($this->input->post('destino') === '2' || $this->input->post('destino') === '3') //Finalizado (HC) Cancelado (HC)
                {
                    $trans_ok &= $this->Tramites_model->update(array(
                        'id' => $this->input->post('id'),
                        'fecha_fin' => $fecha->format('Y-m-d H:i:s')), FALSE);
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

                        $config_pase['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/" . str_pad($pase_id, 6, "0", STR_PAD_LEFT) . "/";
                        if (!file_exists($config_pase['upload_path']))
                        {
                            mkdir($config_pase['upload_path'], 0755, TRUE);
                        }
                        $config_pase['encrypt_name'] = TRUE;
                        $config_pase['file_ext_tolower'] = TRUE;
                        $config_pase['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
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
                        redirect('tramites_online/tramites/bandeja_entrada_publico', 'refresh');
                    }
                    else
                    {
                        redirect('tramites_online/tramites/bandeja_entrada', 'refresh');
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
        $data['fields_persona'] = $this->build_fields($fake_model_persona->fields, $tramite, TRUE);
        $data['fields_inmueble'] = $this->build_fields($fake_model_inmueble->fields, $tramite, TRUE);

        $fake_model_pase->fields['destino']['array'] = $array_destino;
        $data['fields_pase'] = $this->build_fields($fake_model_pase->fields);
        $data['fields_adjunto_pase'] = $this->build_fields($fake_model_adjuntos_pase->fields);

        $adjuntos_tramite = $this->Adjuntos_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('to_adjuntos_tipos', 'to_adjuntos_tipos.id = to_adjuntos.tipo_id', 'LEFT', array("to_adjuntos_tipos.nombre as tipo"))
            )
        ));
        $data['adjuntos_tramite'] = $adjuntos_tramite;

        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('to_estados EO', 'EO.id = to_pases.estado_origen_id', 'LEFT'),
                array('areas OO', 'OO.id = to_pases.area_origen_id', 'LEFT', "CONCAT(EO.nombre, ' (', COALESCE(OO.nombre, ''), ')') as estado_origen"),
                array('to_estados ED', 'ED.id = to_pases.estado_destino_id', 'LEFT'),
                array('areas OD', 'OD.id = to_pases.area_destino_id', 'LEFT', "CONCAT(ED.nombre, ' (', COALESCE(OD.nombre, ''), ')') as estado_destino")
            )
        ));

        $data['ultimo_pase'] = $ultimo_pase;
        $data['tramite'] = $tramite;
        if (in_groups($this->grupos_publico, $this->grupos))
        {
            $data['back_url'] = 'bandeja_entrada_publico';
        }
        else
        {
            $data['back_url'] = 'bandeja_entrada';
        }
        $data['txt_btn'] = 'Enviar';
        $data['title_view'] = 'Revisar Consulta';
        $data['title'] = TITLE . ' - Revisar Consulta';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $this->load_template('tramites_online/tramites/tramites_revisar', $data);
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
            show_error('No se encontró la Consulta', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $this->Personas_model->get_user_id($tramite->persona_id) !== $this->session->userdata('user_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_area, $this->grupos))
        {
            $this->load->model('tramites_online/Usuarios_areas_model');
            $usuario_area = $this->Usuarios_areas_model->get(array('area_id' => $tramite->area, 'user_id' => $this->session->userdata('user_id')));
            if (empty($usuario_area))
            {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            }
        }

        $fake_model_tramites = new stdClass();
        $fake_model_tramites->fields = array(
            'fecha_inicio' => array('label' => 'Inicio', 'type' => 'datetime', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
            'fecha_fin' => array('label' => 'Fin', 'type' => 'datetime')
        );

        $fake_model_persona = new stdClass();
        $fake_model_persona->fields = array(
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13'),
            'dni' => array('label' => 'DNI', 'type' => 'dni', 'maxlength' => '8'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13'),
            'celular' => array('label' => 'Celular', 'type' => 'integer', 'maxlength' => '13'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
        );

        $fake_model_inmueble = new stdClass();
        $fake_model_inmueble->fields = array(
            'padron' => array('label' => 'Padrón', 'type' => 'integer', 'maxlength' => '6', 'required' => TRUE)
        );

        $data['error'] = $this->session->flashdata('error');

        $data['fields_tramite'] = $this->build_fields($fake_model_tramites->fields, $tramite, TRUE);
        $data['fields_persona'] = $this->build_fields($fake_model_persona->fields, $tramite, TRUE);
        $data['fields_inmueble'] = $this->build_fields($fake_model_inmueble->fields, $tramite, TRUE);

        $adjuntos_tramite = $this->Adjuntos_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('to_adjuntos_tipos', 'to_adjuntos_tipos.id = to_adjuntos.tipo_id', 'LEFT', array("to_adjuntos_tipos.nombre as tipo"))
            )
        ));
        $data['adjuntos_tramite'] = $adjuntos_tramite;

        $data['pases'] = $this->Pases_model->get(array(
            'tramite_id' => $id,
            'join' => array(
                array('to_estados EO', 'EO.id = to_pases.estado_origen_id', 'LEFT'),
                array('areas OO', 'OO.id = to_pases.area_origen_id', 'LEFT', "CONCAT(EO.nombre, ' (', COALESCE(OO.nombre, ''), ')') as estado_origen"),
                array('to_estados ED', 'ED.id = to_pases.estado_destino_id', 'LEFT'),
                array('areas OD', 'OD.id = to_pases.area_destino_id', 'LEFT', "CONCAT(ED.nombre, ' (', COALESCE(OD.nombre, ''), ')') as estado_destino")
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
        $data['title_view'] = 'Ver Consulta';
        $data['title'] = TITLE . ' - Ver Consulta';
        $this->load_template('tramites_online/tramites/tramites_revisar', $data);
    }

    private function informar_pase($tramite_id)
    {
        $ultimo_pase = $this->Pases_model->get_ultimo($tramite_id);
        if (!empty($ultimo_pase))
        {
            if ($ultimo_pase->estado_destino_id === '1') //Activo (HC)
            {
                $data['nombre'] = $ultimo_pase->persona;
                $data['tramite'] = $ultimo_pase->tramite_id;
                $data['estado'] = $ultimo_pase->estado_destino;
                $data['observaciones'] = $ultimo_pase->observaciones;
                $data['persona'] = TRUE;
                $this->send_email('tramites_online/email/tramites_activo', 'Consulta Activa', $ultimo_pase->email_persona, $data);
            }
            else if ($ultimo_pase->estado_destino_id === '2') //Finalizado (HC)
            {
                $data['nombre'] = $ultimo_pase->persona;
                $data['tramite'] = $ultimo_pase->tramite_id;
                $data['estado'] = $ultimo_pase->estado_destino;
                $data['observaciones'] = $ultimo_pase->observaciones;
                $data['persona'] = TRUE;
                $this->send_email('tramites_online/email/tramites_finalizado', 'Consulta Finalizada', $ultimo_pase->email_persona, $data);
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
}
