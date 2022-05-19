<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vales extends MY_Controller
{

    /**
     * Controlador de Vales
     * Autor: Leandro
     * Creado: 14/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Cupos_combustible_model');
        $this->load->model('vales_combustible/Vales_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('Areas_model');
        $this->load->model('vales_combustible/Remitos_model');
        $this->load->model('vales_combustible/Ordenes_compra_model');
        $this->load->model('vales_combustible/Estaciones_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('vales_combustible/Usuarios_areas_model');
        $this->load->model('vales_combustible/Vehiculos_model');
        $this->load->model('Personal_model');
        $this->grupos_permitidos = array('admin', 'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_areas', 'vales_combustible_consulta_general');
        $this->grupos_admin_vales = array('admin', 'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_consulta_general');
        $this->grupos_admin = array('admin', 'vales_combustible_consulta_general');
        $this->grupos_contaduria = array('vales_combustible_contaduria');
        $this->grupos_hacienda = array('vales_combustible_hacienda');
        $this->grupos_areas = array('vales_combustible_areas');
        $this->grupos_solo_consulta = array('vales_combustible_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_admin_vales, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'numero', 'class' => 'dt-body-right', 'width' => 6),
                array('label' => 'Fecha', 'data' => 'fecha_vale', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 7),
                array('label' => 'Área', 'data' => 'area', 'width' => 19),
                array('label' => 'Vencimiento', 'data' => 'vencimiento', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 7),
                array('label' => 'Tipo', 'data' => 'tipo_combustible', 'width' => 8),
                array('label' => 'M³', 'data' => 'metros_cubicos', 'class' => 'dt-body-right', 'width' => 5),
                array('label' => 'Beneficiario', 'data' => 'beneficiario', 'width' => 15),
                array('label' => 'Vehículo', 'data' => 'vehiculo', 'width' => 12),
                array('label' => 'Remito', 'data' => 'remito', 'width' => 9),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 7),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'vales_table',
            'source_url' => 'vales_combustible/vales/listar_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_vales_table",
            'fnDrawCallback' => "drawCallback_vales_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
            'pageLength' => 100
        );
        if (in_groups($this->grupos_contaduria, $this->grupos))
        {
            $data['contaduria'] = TRUE;
        }
        else
        {
            $tableData['columns'][] = array('label' => '', 'data' => 'marcar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false');
            $data['contaduria'] = FALSE;
        }
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        $data['array_estados'] = array('' => 'Todos', 'Anulado' => 'Anulado', 'Asignado' => 'Asignado', 'Creado' => 'Creado', 'Impreso' => 'Impreso');

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Vales';
        $data['title'] = TITLE . ' - Vales';
        $this->load_template('vales_combustible/vales/vales_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_admin_vales, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_hacienda, $this->grupos) || in_groups($this->grupos_admin, $this->grupos))
        {
            $permisos_grupo = 'HAC';
        }
        else
        {
            $permisos_grupo = 'CON';
        }

        $this->load->helper('vales_combustible/datatables_functions_helper');
        $this->datatables
                ->select("vc_vales.id, CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as numero, vc_vales.fecha as fecha_vale, CONCAT(areas.codigo, ' - ', areas.nombre) as area, vc_vales.vencimiento, vc_tipos_combustible.nombre as tipo_combustible, vc_vales.metros_cubicos, CONCAT(vc_vales.persona_id, COALESCE(CONCAT(' - ', personal.Apellido, ', ', personal.Nombre), CONCAT(' - EXT: ', vc_vales.persona_nombre), '')) as beneficiario, CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(vc_vehiculos.dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo, vc_remitos.remito as remito, vc_vales.estado")
                ->custom_sort('numero', 'vc_vales.id')
                ->custom_sort('area', 'areas.codigo')
                ->from('vc_vales')
                ->join('vc_vehiculos', 'vc_vehiculos.id = vc_vales.vehiculo_id', 'left')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id', 'left')
                ->join('areas', 'areas.id = vc_vales.area_id', 'left')
                ->join('vc_remitos', 'vc_remitos.id = vc_vales.remito_id', 'left')
                ->join('personal', 'personal.Legajo = vc_vales.persona_id', 'left')
                ->where('vc_vales.estado <>', 'Pendiente')
                ->edit_column('estado', '$1', 'dt_column_vales_estado(estado)', TRUE)
                ->add_column('ver', '<a href="vales_combustible/vales/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_vales_editar(estado, id, "' . $permisos_grupo . '")')
                ->add_column('anular', '$1', 'dt_column_vales_anular(estado, id)')
                ->add_column('marcar', '<input type="checkbox" name="vale[]" value="$1">', 'id');

        echo $this->datatables->generate();
    }

    public function listar_pendientes()
    {
        if (!in_groups($this->grupos_admin_vales, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'numero', 'class' => 'dt-body-right', 'width' => 6),
                array('label' => 'Fecha', 'data' => 'fecha_vale', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 7),
                array('label' => 'Área', 'data' => 'area', 'width' => 21),
                array('label' => 'Vencimiento', 'data' => 'vencimiento', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 7),
                array('label' => 'Tipo', 'data' => 'tipo_combustible', 'width' => 8),
                array('label' => 'M³', 'data' => 'metros_cubicos', 'class' => 'dt-body-right', 'width' => 5),
                array('label' => 'Beneficiario', 'data' => 'beneficiario', 'width' => 19),
                array('label' => 'Vehículo', 'data' => 'vehiculo', 'width' => 17),
                array('label' => '', 'data' => 'aprobar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'marcar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'vales_pendientes_table',
            'source_url' => 'vales_combustible/vales/listar_pendientes_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_vales_pendientes_table",
            'fnDrawCallback' => "drawCallback_vales_pendientes_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        if (in_groups($this->grupos_contaduria, $this->grupos))
        {
            $data['nuevo_vale'] = FALSE;
        }
        else
        {
            $data['nuevo_vale'] = TRUE;
        }
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Vales Pendientes';
        $data['title'] = TITLE . ' - Vales pendientes';
        $this->load_template('vales_combustible/vales/vales_listar_pendientes', $data);
    }

    public function listar_pendientes_data()
    {
        if (!in_groups($this->grupos_admin_vales, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_hacienda, $this->grupos) || in_groups($this->grupos_admin, $this->grupos))
        {
            $permisos_grupo = 'HAC';
        }
        else
        {
            $permisos_grupo = 'CON';
        }

        $this->load->helper('vales_combustible/datatables_functions_helper');
        $this->datatables
                ->select("vc_vales.id, CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as numero, vc_vales.fecha as fecha_vale, CONCAT(areas.codigo, ' - ', areas.nombre) as area, vc_vales.vencimiento, vc_tipos_combustible.nombre as tipo_combustible, vc_vales.metros_cubicos, CONCAT(vc_vales.persona_id, COALESCE(CONCAT(' - ', personal.Apellido, ', ', personal.Nombre), CONCAT(' - EXT: ', vc_vales.persona_nombre), '')) as beneficiario, CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(vc_vehiculos.dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo")
                ->custom_sort('numero', 'vc_vales.id')
                ->custom_sort('area', 'areas.codigo')
                ->from('vc_vales')
                ->join('vc_vehiculos', 'vc_vehiculos.id = vc_vales.vehiculo_id', 'left')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id', 'left')
                ->join('areas', 'areas.id = vc_vales.area_id', 'left')
                ->join('personal', 'personal.Legajo = vc_vales.persona_id', 'left')
                ->where('vc_vales.estado', 'Pendiente')
                ->add_column('aprobar', '<a href="#" onclick="aprobar_vale($1);return false;" title="Aprobar" class="btn btn-success btn-xs"><i class="fa fa-check-circle"></i></a>', 'id')
                ->add_column('ver', '<a href="vales_combustible/vales/ver/$1/listar_pendientes" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_vales_pendientes_editar(estado, id, "' . $permisos_grupo . '")')
                ->add_column('anular', '$1', 'dt_column_vales_pendientes_anular(estado, id)')
                ->add_column('desanular', '$1', 'dt_column_vales_pendientes_desanular(estado, id)')
                ->add_column('marcar', '<input type="checkbox" name="vale[]" value="$1">', 'id');

        echo $this->datatables->generate();
    }

    public function listar_areas()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'numero', 'class' => 'dt-body-right', 'width' => 6),
                array('label' => 'Fecha', 'data' => 'fecha_vale', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 7),
                array('label' => 'Área', 'data' => 'area', 'width' => 19),
                array('label' => 'Vencimiento', 'data' => 'vencimiento', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 7),
                array('label' => 'Tipo', 'data' => 'tipo_combustible', 'width' => 8),
                array('label' => 'M³', 'data' => 'metros_cubicos', 'class' => 'dt-body-right', 'width' => 5),
                array('label' => 'Beneficiario', 'data' => 'persona', 'width' => 16),
                array('label' => 'Vehículo', 'data' => 'vehiculo', 'width' => 14),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'repetir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'marcar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'vales_areas_table',
            'source_url' => 'vales_combustible/vales/listar_areas_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_vales_areas_table",
            'fnDrawCallback' => "drawCallback_vales_areas_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);

        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        $data['array_estados'] = array('' => 'Todos', 'Anulado' => 'Anulado', 'Asignado' => 'Asignado', 'Creado' => 'Creado', 'Impreso' => 'Impreso', 'Pendiente' => 'Pendiente');
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Vales';
        $data['title'] = TITLE . ' - Vales';
        $this->load_template('vales_combustible/vales/vales_listar_areas', $data);
    }

    public function listar_areas_data()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_hacienda, $this->grupos) || in_groups($this->grupos_admin, $this->grupos))
        {
            $permisos_grupo = 'HAC';
        }
        else
        {
            $permisos_grupo = 'CON';
        }

        $this->load->helper('vales_combustible/datatables_functions_helper');
        $this->datatables
                ->select("vc_vales.id, CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as numero, vc_vales.fecha as fecha_vale, CONCAT(areas.codigo, ' - ', areas.nombre) as area, vc_vales.vencimiento, vc_tipos_combustible.nombre as tipo_combustible, vc_vales.metros_cubicos, CONCAT(vc_vales.persona_id, COALESCE(CONCAT(' - ', personal.Apellido, ', ', personal.Nombre), CONCAT(' - EXT: ', vc_vales.persona_nombre), '')) as persona, CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(vc_vehiculos.dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo, vc_vales.estado")
                ->custom_sort('numero', 'vc_vales.id')
                ->custom_sort('area', 'areas.codigo')
                ->from('vc_vales')
                ->join('vc_vehiculos', 'vc_vehiculos.id = vc_vales.vehiculo_id', 'left')
                ->join('personal', 'personal.Legajo = vc_vales.persona_id', 'left')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id', 'left')
                ->join('areas', 'areas.id = vc_vales.area_id', 'left')
                ->join('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id ', 'left')
                ->where('vc_usuarios_areas.user_id', $this->session->userdata('user_id'))
                ->edit_column('estado', '$1', 'dt_column_vales_estado(estado)', TRUE)
                ->add_column('ver', '<a href="vales_combustible/vales/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_vales_areas_editar(estado, id)', TRUE)
                ->add_column('anular', '$1', 'dt_column_vales_areas_anular(estado, id)', TRUE)
                ->add_column('repetir', '<a href="#" onclick="duplicar_vale($1);return false;" title="Repetir" class="btn btn-primary btn-xs"><i class="fa fa-repeat"></i></a>', 'id')
                ->add_column('marcar', '<input type="checkbox" name="vale[]" value="$1">', 'id');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_admin_vales, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array('select' => "id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo", 'where' => array(array('column' => 'estado', 'value' => 'Aprobado'), array('column' => 'vencimiento_seguro >', 'value' => 'NOW()', 'override' => TRUE))), array('NULL' => '-- Sin Especificar --'));
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->array_forma_carga_control = $array_forma_carga = array('Vehículo' => 'Vehículo', 'Bidón' => 'Bidón');
        $this->array_orden_compra_control = $array_orden_compra = $this->get_array('Ordenes_compra', 'orden', 'id', array('select' => array('id',
                'CONCAT(numero, \'/\', ejercicio) as orden'), 'sort_by' => 'numero'), array('NULL' => '-- Sin Orden de Compra --'));
        $this->array_estacion_control = $array_estacion = $this->get_array('Estaciones', 'nombre');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'required' => TRUE),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'persona' => array('label' => 'Legajo Beneficiario', 'type' => 'integer', 'maxlength' => '8'),
            'persona_major' => array('label' => 'Beneficiario', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Beneficiario Externo', 'maxlength' => '50'),
            'vehiculo' => array('label' => 'Vehiculo', 'input_type' => 'combo', 'type' => 'bselect'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
            'forma_carga' => array('label' => 'Forma de Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'forma_carga', 'required' => TRUE),
            'nota' => array('label' => 'Nota', 'maxlength' => '50'),
            'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
            'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
            $vencimiento = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Vales_model->create(array(
                'fecha' => $fecha->format('Y-m-d'),
                'vencimiento' => $vencimiento->format('Y-m-d'),
                'area_id' => $this->input->post('area'),
                'persona_id' => $this->input->post('persona'),
                'persona_nombre' => $this->input->post('persona_nombre'),
                'metros_cubicos' => $this->input->post('metros_cubicos'),
                'vehiculo_id' => $this->input->post('vehiculo'),
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'forma_carga' => $this->input->post('forma_carga'),
                'periodicidad' => 'Única Vez',
                'nota' => $this->input->post('nota'),
                'observaciones' => $this->input->post('observaciones'),
                'orden_compra_id' => $this->input->post('orden_compra'),
                'estacion_id' => $this->input->post('estacion'),
                'estado' => 'Creado',
                'user_id' => $this->session->userdata('user_id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Vales_model->get_msg());
                redirect('vales_combustible/vales/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= $this->Vales_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['vehiculo']['array'] = $array_vehiculo;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $fake_model->fields['forma_carga']['array'] = $array_forma_carga;
        $fake_model->fields['orden_compra']['array'] = $array_orden_compra;
        $fake_model->fields['estacion']['array'] = $array_estacion;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar vale';
        $data['title'] = TITLE . ' - Agregar vale';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function solicitar()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('areas.id', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'join' => array(array('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id', 'LEFT')),
            'where' => array("nombre <> '-'", "vc_usuarios_areas.user_id = " . $this->session->userdata('user_id')),
            'sort_by' => 'codigo')
        );
        if (in_groups($this->grupos_areas, $this->grupos))
        {
            $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
                'select' => "vc_vehiculos.id, CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo",
                'join' => array(
                    array('areas', 'areas.id = vc_vehiculos.area_id', 'left'),
                    array('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id', 'left')
                ),
                'where' => array(
                    "(estado = 'Aprobado' AND vencimiento_seguro > NOW() AND (propiedad = 'Oficial' OR vc_usuarios_areas.user_id = " . $this->session->userdata('user_id') . "))"
                ))
            );
        }
        else
        {
            $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
                'select' => "id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo",
                'where' => array(
                    "(estado = 'Aprobado' AND vencimiento_seguro > NOW())"
                ))
            );
        }

        if ($this->input->post('vehiculo'))
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre', 'id', array(
                'join' => array(array('vc_vehiculos_combustible', 'vc_vehiculos_combustible.tipo_combustible_id = vc_tipos_combustible.id', 'LEFT')),
                'where' => array(
                    array('column' => 'vc_vehiculos_combustible.vehiculo_id', 'value' => $this->input->post('vehiculo')))
                    )
            );
        }
        else
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = array();
        }

        $this->array_forma_carga_control = $array_forma_carga = array('Vehículo' => 'Vehículo', 'Bidón' => 'Bidón');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'persona' => array('label' => 'Legajo Beneficiario', 'type' => 'integer', 'maxlength' => '8', 'required' => TRUE),
            'persona_major' => array('label' => 'Beneficiario', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Beneficiario Externo', 'maxlength' => '50'),
            'vehiculo' => array('label' => 'Vehículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
            'forma_carga' => array('label' => 'Forma de Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'forma_carga', 'required' => TRUE),
            'nota' => array('label' => 'Nota', 'maxlength' => '50'),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        $cupo_msg = '';
        if ($this->form_validation->run() === TRUE)
        {
            $tipo_combustible = $this->Tipos_combustible_model->get(array('id' => $this->input->post('tipo_combustible')));
            if (empty($tipo_combustible))
            {
                show_error('No se encontró el Tipo de Combustible', 500, 'Registro no encontrado');
            }

            $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
            $vencimiento = clone $fecha;
            $vencimiento->add(new DateInterval('P7D'));

            $cupos_combustible = $this->Cupos_combustible_model->get(array(
                'select' => array("(metros_cubicos + (CASE WHEN ampliacion_vencimiento >= '" . $fecha->format('Y-m-d') . "' THEN ampliacion ELSE 0 END)) as total"),
                'area_id' => $this->input->post('area'),
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'fecha_inicio <=' => $fecha->format('Y-m-d'),
                'sort_by' => 'fecha_inicio DESC'
            ));
            if (empty($cupos_combustible))
            {
                $error_msg = '<br>Sin cupo asignado. Por favor contacte a la oficina de Auditoría';
            }
            else
            {
                //SEMANAL
                $cupo_semanal = $cupos_combustible[0]->total;
                $ini_sem = clone $fecha;
                $ini_sem->modify('this week');
                $fin_sem = clone $fecha;
                $fin_sem->modify('this week +6 days');
                $ini_sem_sql = $ini_sem->format('Y-m-d');
                $fin_sem_sql = $fin_sem->format('Y-m-d');
                $vales_oficina_semanal = $this->Vales_model->get(array(
                    'area_id' => $this->input->post('area'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'fecha >=' => $ini_sem_sql,
                    'fecha <=' => $fin_sem_sql,
                    'estado !=' => 'Anulado'
                ));
                $cupo_semanal_usado = 0;
                if (!empty($vales_oficina_semanal))
                {
                    foreach ($vales_oficina_semanal as $Vale)
                    {
                        $cupo_semanal_usado += $Vale->metros_cubicos;
                    }
                }
                if ($cupo_semanal_usado + $this->input->post('metros_cubicos') > $cupo_semanal)
                {
                    $cupo_msg = '<br>Su pedido supera el cupo semanal autorizado. Se realizó el vale pero se descontará de su cupo mensual';
                }

                //MENSUAL
                $cupo_mensual = $cupos_combustible[0]->total * 4;
                $ini_mes = clone $fecha;
                $ini_mes->modify('first day of this month');
                $fin_mes = clone $fecha;
                $fin_mes->modify('last day of this month');
                $ini_mes_sql = $ini_mes->format('Y-m-d');
                $fin_mes_sql = $fin_mes->format('Y-m-d');
                $vales_oficina = $this->Vales_model->get(array(
                    'area_id' => $this->input->post('area'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'fecha >=' => $ini_mes_sql,
                    'fecha <=' => $fin_mes_sql,
                    'estado !=' => 'Anulado'
                ));
                $cupo_mensual_usado = 0;
                if (!empty($vales_oficina))
                {
                    foreach ($vales_oficina as $Vale)
                    {
                        $cupo_mensual_usado += $Vale->metros_cubicos;
                    }
                }
                if ($cupo_mensual_usado + $this->input->post('metros_cubicos') > $cupo_mensual)
                {
                    $error_msg = '<br>Su pedido supera el cupo mensual autorizado. Por favor contacte a la oficina de Auditoría';
                }
            }

            if (empty($error_msg))
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Vales_model->create(array(
                    'fecha' => $fecha->format('Y-m-d'),
                    'vencimiento' => $vencimiento->format('Y-m-d'),
                    'area_id' => $this->input->post('area'),
                    'persona_id' => $this->input->post('persona'),
                    'persona_nombre' => $this->input->post('persona_nombre'),
                    'metros_cubicos' => $this->input->post('metros_cubicos'),
                    'vehiculo_id' => $this->input->post('vehiculo'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'forma_carga' => $this->input->post('forma_carga'),
                    'periodicidad' => 'Única Vez',
                    'nota' => $this->input->post('nota'),
                    'observaciones' => $this->input->post('observaciones'),
                    'estacion_id' => $tipo_combustible->estacion_id,
                    'estado' => 'Creado',
                    'user_id' => $this->session->userdata('user_id')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Vales_model->get_msg() . $cupo_msg);
                    redirect('vales_combustible/vales/listar_areas', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Vales_model->get_error())
                    {
                        $error_msg .= $this->Vales_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['vehiculo']['array'] = $array_vehiculo;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $fake_model->fields['forma_carga']['array'] = $array_forma_carga;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['back_url'] = 'listar_areas';
        $data['txt_btn'] = 'Solicitar';
        $data['title_view'] = 'Solicitar vale';
        $data['title'] = TITLE . ' - Solicitar vale';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function repetir()
    {
        if ((!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos)))
        {
            $this->output->set_status_header('403');
            $return_data['message'] = 'No tiene permisos para la acción solicitada';
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->output->set_status_header('403');
            $return_data['message'] = 'Usuario sin permisos de edición';
        }

        $this->form_validation->set_rules('vale_id', 'Vale', 'integer|required');
        $this->form_validation->set_rules('fecha', 'Fecha', 'date|required');
        $cupo_msg = '';
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $vale = $this->Vales_model->get(array('id' => $this->input->post('vale_id')));
            if (empty($vale))
            {
                $this->output->set_status_header('500');
                $return_data['message'] = 'No se encontró el Vale';
            }

            if (in_groups($this->grupos_areas, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $vale->area_id))
            {
                $this->output->set_status_header('403');
                $return_data['message'] = 'No tiene permisos para la acción solicitada';
            }

            if (!empty($vale) && !empty($vale->vehiculo_id) && (!empty($vale->persona_id) || !empty($vale->persona_nombre) ) && !empty($vale->observaciones))
            {
                $vehiculo = $this->Vehiculos_model->get(array('id' => $vale->vehiculo_id));
                if (!empty($vehiculo))
                {
                    $hoy = new DateTime();
                    $venc_seguro = new DateTime($vehiculo->vencimiento_seguro);
                    if ($venc_seguro > $hoy && $vehiculo->estado === 'Aprobado')
                    {
                        $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
                        $vencimiento = clone $fecha;
                        $vencimiento->add(new DateInterval('P7D'));

                        $cupos_combustible = $this->Cupos_combustible_model->get(array(
                            'select' => array("(metros_cubicos + (CASE WHEN ampliacion_vencimiento >= '" . $fecha->format('Y-m-d') . "' THEN ampliacion ELSE 0 END)) as total"),
                            'area_id' => $vale->area_id,
                            'tipo_combustible_id' => $vale->tipo_combustible_id,
                            'fecha_inicio <=' => $fecha->format('Y-m-d'),
                            'sort_by' => 'fecha_inicio DESC'
                        ));
                        if (empty($cupos_combustible))
                        {
                            $error_msg = 'Sin cupo asignado. Por favor contacte a la oficina de Auditoría';
                        }
                        else
                        {
                            //SEMANAL
                            $cupo_semanal = $cupos_combustible[0]->total;
                            $ini_sem = clone $fecha;
                            $ini_sem->modify('this week');
                            $fin_sem = clone $fecha;
                            $fin_sem->modify('this week +6 days');
                            $ini_sem_sql = $ini_sem->format('Y-m-d');
                            $fin_sem_sql = $fin_sem->format('Y-m-d');
                            $vales_oficina_semanal = $this->Vales_model->get(array(
                                'area_id' => $vale->area_id,
                                'tipo_combustible_id' => $vale->tipo_combustible_id,
                                'fecha >=' => $ini_sem_sql,
                                'fecha <=' => $fin_sem_sql,
                                'estado !=' => 'Anulado'
                            ));
                            $cupo_semanal_usado = 0;
                            if (!empty($vales_oficina_semanal))
                            {
                                foreach ($vales_oficina_semanal as $Vale)
                                {
                                    $cupo_semanal_usado += $Vale->metros_cubicos;
                                }
                            }
                            if ($cupo_semanal_usado + $this->input->post('metros_cubicos') > $cupo_semanal)
                            {
                                $cupo_msg = '. Su pedido supera el cupo semanal autorizado. Se realizó el vale pero se descontará de su cupo mensual';
                            }

                            //MENSUAL
                            $cupo_mensual = $cupos_combustible[0]->total * 4;
                            $ini_mes = clone $fecha;
                            $ini_mes->modify('first day of this month');
                            $fin_mes = clone $fecha;
                            $fin_mes->modify('last day of this month');
                            $ini_mes_sql = $ini_mes->format('Y-m-d');
                            $fin_mes_sql = $fin_mes->format('Y-m-d');
                            $vales_oficina = $this->Vales_model->get(array(
                                'area_id' => $vale->area_id,
                                'tipo_combustible_id' => $vale->tipo_combustible_id,
                                'fecha >=' => $ini_mes_sql,
                                'fecha <=' => $fin_mes_sql,
                                'estado !=' => 'Anulado'
                            ));
                            $cupo_mensual_usado = 0;
                            if (!empty($vales_oficina))
                            {
                                foreach ($vales_oficina as $Vale)
                                {
                                    $cupo_mensual_usado += $Vale->metros_cubicos;
                                }
                            }
                            if ($cupo_mensual_usado + $this->input->post('metros_cubicos') > $cupo_mensual)
                            {
                                $error_msg = 'Su pedido supera el cupo mensual autorizado. Por favor contacte a la oficina de Auditoría';
                            }
                        }

                        if (empty($error_msg))
                        {
                            $this->db->trans_begin();
                            $trans_ok = TRUE;
                            $trans_ok &= $this->Vales_model->create(array(
                                'fecha' => $fecha->format('Y-m-d'),
                                'vencimiento' => $vencimiento->format('Y-m-d'),
                                'area_id' => $vale->area_id,
                                'persona_id' => $vale->persona_id,
                                'persona_nombre' => $vale->persona_nombre,
                                'metros_cubicos' => $vale->metros_cubicos,
                                'vehiculo_id' => $vale->vehiculo_id,
                                'tipo_combustible_id' => $vale->tipo_combustible_id,
                                'forma_carga' => $vale->forma_carga,
                                'periodicidad' => $vale->periodicidad,
                                'nota' => $vale->nota,
                                'observaciones' => $vale->observaciones,
                                'estacion_id' => $vale->estacion_id,
                                'estado' => 'Creado',
                                'user_id' => $this->session->userdata('user_id')), FALSE);
                            if ($this->db->trans_status() && $trans_ok)
                            {
                                $this->db->trans_commit();
                                $this->output->set_status_header('200');
                                $return_data['message'] = 'Vale duplicado correctamente' . $cupo_msg;
                            }
                            else
                            {
                                $this->db->trans_rollback();
                                $error_msg = 'Se ha producido un error con la base de datos.';
                                if ($this->Vales_model->get_error())
                                {
                                    $error_msg .= $this->Vales_model->get_error();
                                }
                                $this->output->set_status_header('500');
                                $return_data['message'] = 'ERROR: ' . $error_msg;
                            }
                        }
                        else
                        {
                            $this->output->set_status_header('500');
                            $return_data['message'] = $error_msg;
                        }
                    }
                    else
                    {
                        $this->output->set_status_header('500');
                        $return_data['message'] = 'No se puede duplicar este Vale. Verifique que el vehículo esté "Aprobado" y con seguro vigente';
                    }
                }
                else
                {
                    $this->output->set_status_header('500');
                    $return_data['message'] = 'No se puede duplicar este Vale. Verifique que contenga vehículo asignado';
                }
            }
            else
            {
                $this->output->set_status_header('500');
                $return_data['message'] = 'No se puede duplicar este Vale';
            }
        }
        else
        {
            $this->output->set_status_header('400');
            $return_data['message'] = validation_errors();
        }

        echo json_encode($return_data);
    }

    public function agregar_masivo()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->array_orden_compra_control = $array_orden_compra = $this->get_array('Ordenes_compra', 'orden', 'id', array('select' => array('id',
                'CONCAT(numero, \'/\', ejercicio) as orden'), 'sort_by' => 'numero'), array('NULL' => '-- Sin Orden de Compra --'));
        $this->array_estacion_control = $array_estacion = $this->get_array('Estaciones', 'nombre');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'cantidad' => array('label' => 'Cantidad', 'type' => 'integer', 'required' => TRUE),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'required' => TRUE),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'persona' => array('label' => 'Legajo Beneficiario', 'type' => 'integer', 'maxlength' => '8'),
            'persona_major' => array('label' => 'Beneficiario', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Beneficiario Externo', 'maxlength' => '50'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
            'nota' => array('label' => 'Nota', 'maxlength' => '50'),
            'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
            'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
            $vencimiento = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento'));

            $this->db->trans_begin();
            $cantidad = $this->input->post('cantidad');
            $trans_ok = TRUE;
            $datos_vale = array(
                'fecha' => $fecha->format('Y-m-d'),
                'vencimiento' => $vencimiento->format('Y-m-d'),
                'area_id' => $this->input->post('area'),
                'persona_id' => $this->input->post('persona'),
                'persona_nombre' => $this->input->post('persona_nombre'),
                'metros_cubicos' => $this->input->post('metros_cubicos'),
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'periodicidad' => 'Única Vez',
                'nota' => $this->input->post('nota'),
                'orden_compra_id' => $this->input->post('orden_compra'),
                'estacion_id' => $this->input->post('estacion'),
                'observaciones' => $this->input->post('observaciones'),
                'estado' => 'Creado',
                'user_id' => $this->session->userdata('user_id')
            );
            $trans_ok &= $this->Vales_model->create($datos_vale, FALSE);
            $vale_1 = $this->Vales_model->get_row_id();
            for ($i = 1; $i < $cantidad; $i++)
            {
                $trans_ok &= $this->Vales_model->create($datos_vale, FALSE);
            }
            $vale_n = $this->Vales_model->get_row_id();
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Vales_model->get_msg());
                redirect("vales_combustible/vales/imprimir/No/$vale_1/$vale_n", 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= $this->Vales_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $fake_model->fields['orden_compra']['array'] = $array_orden_compra;
        $fake_model->fields['estacion']['array'] = $array_estacion;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar vales masivos';
        $data['title'] = TITLE . ' - Agregar vales masivos';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function editar($id = NULL, $back_url = NULL)
    {
        if (!in_groups($this->grupos_admin_vales, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        if (in_groups($this->grupos_contaduria, $this->grupos))
        {
            redirect("vales_combustible/vales/editar_con/$id/$back_url", 'refresh');
        }
        else if (in_groups($this->grupos_hacienda, $this->grupos) || in_groups($this->grupos_admin, $this->grupos))
        {
            redirect("vales_combustible/vales/editar_hac/$id/$back_url", 'refresh');
        }
    }

    public function editar_hac($id = NULL, $back_url = NULL)
    {
        if ((!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos)) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        $vale = $this->Vales_model->get(array('id' => $id));
        if (empty($vale) || ($vale->estado !== 'Pendiente' && $vale->estado !== 'Creado'))
        {
            show_error('No se encontró el Vale', 500, 'Registro no encontrado');
        }

        $vehiculo_where = '';
        if (!empty($vale->vehiculo_id))
        {
            $vehiculo_where = "OR id = $vale->vehiculo_id";
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'where' => array("nombre<>'-'"),
            'sort_by' => 'codigo'
        ));
        $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
            'select' => "id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo",
            'where' => array("(estado = 'Aprobado' $vehiculo_where)")
                ), array('NULL' => '-- Sin Especificar --')
        );
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->array_forma_carga_control = $array_forma_carga = array('Vehículo' => 'Vehículo', 'Bidón' => 'Bidón');
        $this->array_orden_compra_control = $array_orden_compra = $this->get_array('Ordenes_compra', 'orden', 'id', array('select' => array('id',
                'CONCAT(numero, \'/\', ejercicio) as orden'), 'sort_by' => 'numero'), array('NULL' => '-- Sin Orden de Compra --'));
        $this->array_estacion_control = $array_estacion = $this->get_array('Estaciones', 'nombre');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'required' => TRUE),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'persona' => array('label' => 'Legajo Beneficiario', 'type' => 'integer', 'maxlength' => '8'),
            'persona_major' => array('label' => 'Beneficiario', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Beneficiario Externo', 'maxlength' => '50'),
            'vehiculo' => array('label' => 'Vehiculo', 'input_type' => 'combo', 'type' => 'bselect'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
            'forma_carga' => array('label' => 'Forma de Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'forma_carga', 'required' => TRUE),
            'nota' => array('label' => 'Nota', 'maxlength' => '50'),
            'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
            'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5)
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
                $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
                $vencimiento = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Vales_model->update(array(
                    'id' => $this->input->post('id'),
                    'fecha' => $fecha->format('Y-m-d'),
                    'vencimiento' => $vencimiento->format('Y-m-d'),
                    'area_id' => $this->input->post('area'),
                    'persona_id' => $this->input->post('persona'),
                    'persona_nombre' => $this->input->post('persona_nombre'),
                    'metros_cubicos' => $this->input->post('metros_cubicos'),
                    'forma_carga' => $this->input->post('forma_carga'),
                    'vehiculo_id' => $this->input->post('vehiculo'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'periodicidad' => $vale->periodicidad,
                    'nota' => $this->input->post('nota'),
                    'estacion_id' => $this->input->post('estacion'),
                    'orden_compra_id' => $this->input->post('orden_compra'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Vales_model->get_msg());
                    if (!empty($back_url))
                    {
                        redirect("vales_combustible/vales/$back_url", 'refresh');
                    }
                    else
                    {
                        redirect('vales_combustible/vales/listar', 'refresh');
                    }
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Vales_model->get_error())
                    {
                        $error_msg .= $this->Vales_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['vehiculo']['array'] = $array_vehiculo;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $fake_model->fields['forma_carga']['array'] = $array_forma_carga;
        $fake_model->fields['orden_compra']['array'] = $array_orden_compra;
        $fake_model->fields['estacion']['array'] = $array_estacion;
        $vale->numero = 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT);
        $vale->persona = $vale->persona_id;
        $vale->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $vale);
        $data['vale'] = $vale;
        if (!empty($back_url))
        {
            $data['back_url'] = $back_url;
        }
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar vale';
        $data['title'] = TITLE . ' - Editar vale';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function editar_con($id = NULL, $back_url = NULL)
    {
        if ((!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_contaduria, $this->grupos)) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        $vale = $this->Vales_model->get(array('id' => $id));
        if (empty($vale) || $vale->estado === 'Anulado' || $vale->estado === 'Creado')
        {
            show_error('No se encontró el Vale', 500, 'Registro no encontrado');
        }

        $vehiculo_where = '';
        if (!empty($vale->vehiculo_id))
        {
            $vehiculo_where = "OR id = $vale->vehiculo_id";
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'where' => array("nombre<>'-'"),
            'sort_by' => 'codigo'
                ), array('' => ''));
        $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
            'select' => "id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo",
            'where' => array("(estado = 'Aprobado' $vehiculo_where)")
                ), array('NULL' => '-- Sin Especificar --'));
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre', 'id', array(), array('' => ''));
        $this->array_forma_carga_control = $array_forma_carga = array('Vehículo' => 'Vehículo', 'Bidón' => 'Bidón');
        $this->array_orden_compra_control = $array_orden_compra = $this->get_array('Ordenes_compra', 'orden', 'id', array(
            'select' => array('id', 'CONCAT(numero, \'/\', ejercicio) as orden'),
            'sort_by' => 'numero'
                ), array('' => '-- Sin Orden de Compra --'));
        $this->array_estacion_control = $array_estacion = $this->get_array('Estaciones', 'nombre', 'id', array(), array('' => ''));
        $this->array_remito_control = $array_remito = $this->get_array('Remitos', 'remito', 'id', array(), array('NULL' => '-- Sin Remito --'));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'persona' => array('label' => 'Legajo Beneficiario', 'type' => 'integer', 'maxlength' => '8', 'required' => TRUE),
            'persona_major' => array('label' => 'Beneficiario', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Beneficiario Externo', 'maxlength' => '50'),
            'vehiculo' => array('label' => 'Vehículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
            'forma_carga' => array('label' => 'Forma de Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'forma_carga', 'required' => TRUE),
            'remito' => array('label' => 'Remito', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
            'nota' => array('label' => 'Nota', 'maxlength' => '50', 'disabled' => 'disabled'),
            'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'disabled' => 'disabled'),
            'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled')
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
                $trans_ok &= $this->Vales_model->update(array(
                    'id' => $this->input->post('id'),
                    'persona_id' => $this->input->post('persona'),
                    'persona_nombre' => $this->input->post('persona_nombre'),
                    'metros_cubicos' => $this->input->post('metros_cubicos'),
                    'forma_carga' => $this->input->post('forma_carga'),
                    'vehiculo_id' => $this->input->post('vehiculo'),
                    'remito_id' => $this->input->post('remito'),
                    'estado' => $this->input->post('remito') === 'NULL' ? 'Impreso' : 'Asignado'), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Vales_model->get_msg());
                    redirect('vales_combustible/vales/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Vales_model->get_error())
                    {
                        $error_msg .= $this->Vales_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['vehiculo']['array'] = $array_vehiculo;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $fake_model->fields['forma_carga']['array'] = $array_forma_carga;
        $fake_model->fields['orden_compra']['array'] = $array_orden_compra;
        $fake_model->fields['estacion']['array'] = $array_estacion;
        $fake_model->fields['remito']['array'] = $array_remito;
        $vale->numero = 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT);
        $vale->persona = $vale->persona_id;
        $vale->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $vale);
        $data['vale'] = $vale;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar vale';
        $data['title'] = TITLE . ' - Editar vale';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function editar_area($id = NULL, $back_url = 'listar_areas')
    {
        if ((!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos)) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        $vale = $this->Vales_model->get(array('id' => $id));
        if (empty($vale) || $vale->estado !== 'Pendiente')
        {
            show_error('No se encontró el Vale', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_areas, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $vale->area_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $vehiculo_where = '';
        if (!empty($vale->vehiculo_id))
        {
            $vehiculo_where = "OR vc_vehiculos.id = $vale->vehiculo_id";
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('areas.id', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'join' => array(array('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id', 'LEFT')),
            'where' => array("nombre <> '-'", "vc_usuarios_areas.user_id = " . $this->session->userdata('user_id')),
            'sort_by' => 'codigo')
        );
        $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
            'select' => "id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo",
            'where' => array("(estado = 'Aprobado' $vehiculo_where)")
                )
        );
        if (in_groups($this->grupos_areas, $this->grupos))
        {
            $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
                'select' => "vc_vehiculos.id, CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo",
                'join' => array(
                    array('areas', 'areas.id = vc_vehiculos.area_id', 'left'),
                    array('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id', 'left')
                ),
                'where' => array(
                    "((estado = 'Aprobado' AND vencimiento_seguro > NOW() AND (propiedad = 'Oficial' OR vc_usuarios_areas.user_id = " . $this->session->userdata('user_id') . ")) $vehiculo_where)"
                ))
            );
        }
        else
        {
            $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
                'select' => "id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo",
                'where' => array(
                    "((estado = 'Aprobado' AND vencimiento_seguro > NOW()) $vehiculo_where)"
                ))
            );
        }
        if ($this->input->post('vehiculo'))
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre', 'id', array(
                'join' => array(array('vc_vehiculos_combustible', 'vc_vehiculos_combustible.tipo_combustible_id = vc_tipos_combustible.id', 'LEFT')),
                'where' => array(
                    array('column' => 'vc_vehiculos_combustible.vehiculo_id', 'value' => $this->input->post('vehiculo')))
                    )
            );
        }
        else
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre', 'id', array(
                'join' => array(array('vc_vehiculos_combustible', 'vc_vehiculos_combustible.tipo_combustible_id = vc_tipos_combustible.id', 'LEFT')),
                'where' => array(
                    array('column' => 'vc_vehiculos_combustible.vehiculo_id', 'value' => $vale->vehiculo_id))
                    )
            );
        }

        $this->array_forma_carga_control = $array_forma_carga = array('Vehículo' => 'Vehículo', 'Bidón' => 'Bidón');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'persona' => array('label' => 'Legajo Beneficiario', 'type' => 'integer', 'maxlength' => '8'),
            'persona_major' => array('label' => 'Beneficiario', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Beneficiario Externo', 'maxlength' => '50'),
            'vehiculo' => array('label' => 'Vehiculo', 'input_type' => 'combo', 'type' => 'bselect'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
            'forma_carga' => array('label' => 'Forma de Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'forma_carga', 'required' => TRUE),
            'nota' => array('label' => 'Nota', 'maxlength' => '50'),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5)
        );

        $this->set_model_validation_rules($fake_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            $cupo_msg = '';
            if ($this->form_validation->run() === TRUE)
            {
                $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
                $vencimiento = clone $fecha;
                $vencimiento->add(new DateInterval('P7D'));

                $tipo_combustible = $this->Tipos_combustible_model->get(array('id' => $this->input->post('tipo_combustible')));
                if (empty($tipo_combustible))
                {
                    show_error('No se encontró el Tipo de Combustible', 500, 'Registro no encontrado');
                }

                $cupos_combustible = $this->Cupos_combustible_model->get(array(
                    'select' => array("(metros_cubicos + (CASE WHEN ampliacion_vencimiento >= '" . $fecha->format('Y-m-d') . "' THEN ampliacion ELSE 0 END)) as total"),
                    'area_id' => $this->input->post('area'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'fecha_inicio <=' => $fecha->format('Y-m-d'),
                    'sort_by' => 'fecha_inicio DESC'
                ));
                if (empty($cupos_combustible))
                {
                    $error_msg = '<br>Sin cupo asignado. Por favor contacte a la oficina de Auditoría';
                }
                else
                {
                    //SEMANAL
                    $cupo_semanal = $cupos_combustible[0]->total;
                    $ini_sem = clone $fecha;
                    $ini_sem->modify('this week');
                    $fin_sem = clone $fecha;
                    $fin_sem->modify('this week +6 days');
                    $ini_sem_sql = $ini_sem->format('Y-m-d');
                    $fin_sem_sql = $fin_sem->format('Y-m-d');
                    $vales_oficina_semanal = $this->Vales_model->get(array(
                        'id !=' => $this->input->post('id'),
                        'area_id' => $this->input->post('area'),
                        'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                        'fecha >=' => $ini_sem_sql,
                        'fecha <=' => $fin_sem_sql,
                        'estado !=' => 'Anulado'
                    ));
                    $cupo_semanal_usado = 0;
                    if (!empty($vales_oficina_semanal))
                    {
                        foreach ($vales_oficina_semanal as $Vale)
                        {
                            $cupo_semanal_usado += $Vale->metros_cubicos;
                        }
                    }
                    if ($cupo_semanal_usado + $this->input->post('metros_cubicos') > $cupo_semanal)
                    {
                        $cupo_msg = '<br>Su pedido supera el cupo semanal autorizado. Se realizó el vale pero se descontará de su cupo mensual';
                    }

                    //MENSUAL
                    $cupo_mensual = $cupos_combustible[0]->total * 4;
                    $ini_mes = clone $fecha;
                    $ini_mes->modify('first day of this month');
                    $fin_mes = clone $fecha;
                    $fin_mes->modify('last day of this month');
                    $ini_mes_sql = $ini_mes->format('Y-m-d');
                    $fin_mes_sql = $fin_mes->format('Y-m-d');
                    $vales_oficina = $this->Vales_model->get(array(
                        'id !=' => $this->input->post('id'),
                        'area_id' => $this->input->post('area'),
                        'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                        'fecha >=' => $ini_mes_sql,
                        'fecha <=' => $fin_mes_sql,
                        'estado !=' => 'Anulado'
                    ));
                    $cupo_mensual_usado = 0;
                    if (!empty($vales_oficina))
                    {
                        foreach ($vales_oficina as $Vale)
                        {
                            $cupo_mensual_usado += $Vale->metros_cubicos;
                        }
                    }
                    if ($cupo_mensual_usado + $this->input->post('metros_cubicos') > $cupo_mensual)
                    {
                        $error_msg = '<br>Su pedido supera el cupo mensual autorizado. Por favor contacte a la oficina de Auditoría';
                    }
                }

                if (empty($error_msg))
                {
                    $this->db->trans_begin();
                    $trans_ok = TRUE;
                    $trans_ok &= $this->Vales_model->update(array(
                        'id' => $this->input->post('id'),
                        'fecha' => $fecha->format('Y-m-d'),
                        'area_id' => $this->input->post('area'),
                        'persona_id' => $this->input->post('persona'),
                        'persona_nombre' => $this->input->post('persona_nombre'),
                        'metros_cubicos' => $this->input->post('metros_cubicos'),
                        'vehiculo_id' => $this->input->post('vehiculo'),
                        'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                        'forma_carga' => $this->input->post('forma_carga'),
                        'nota' => $this->input->post('nota'),
                        'estacion_id' => $tipo_combustible->estacion_id,
                        'observaciones' => $this->input->post('observaciones')), FALSE);
                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('message', $this->Vales_model->get_msg() . $cupo_msg);
                        if (!empty($back_url))
                        {
                            redirect("vales_combustible/vales/$back_url", 'refresh');
                        }
                        else
                        {
                            redirect('vales_combustible/vales/listar', 'refresh');
                        }
                    }
                    else
                    {
                        $this->db->trans_rollback();
                        $error_msg = '<br />Se ha producido un error con la base de datos.';
                        if ($this->Vales_model->get_error())
                        {
                            $error_msg .= $this->Vales_model->get_error();
                        }
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['vehiculo']['array'] = $array_vehiculo;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $fake_model->fields['forma_carga']['array'] = $array_forma_carga;
        $vale->numero = 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT);
        $vale->persona = $vale->persona_id;
        $vale->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $vale);
        $data['vale'] = $vale;
        if (!empty($back_url))
        {
            $data['back_url'] = $back_url;
        }
        $data['txt_btn'] = 'Editar vale';
        $data['title_view'] = 'Editar vale';
        $data['title'] = TITLE . ' - Editar vale';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function ver($id = NULL, $back_url = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $vale = $this->Vales_model->get_one($id);
        if (empty($vale))
        {
            show_error('No se encontró el Vale', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_areas, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $vale->area_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'persona' => array('label' => 'Legajo Beneficiario', 'type' => 'integer', 'maxlength' => '8', 'disabled' => 'disabled'),
            'persona_major' => array('label' => 'Beneficiario', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Beneficiario Externo', 'maxlength' => '50', 'disabled' => 'disabled'),
            'vehiculo' => array('label' => 'Vehículo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'forma_carga' => array('label' => 'Forma de Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'forma_carga', 'disabled' => 'disabled'),
            'remito' => array('label' => 'Remito', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
            'nota' => array('label' => 'Nota', 'maxlength' => '50', 'disabled' => 'disabled'),
            'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'disabled' => 'disabled'),
            'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled'),
            'usuario' => array('label' => 'Usuario Creación', 'disabled' => 'disabled'),
            'fecha_creacion' => array('label' => 'Fecha Creación', 'type' => 'datetime', 'disabled' => 'disabled'),
        );

        $vale_auditoria = $this->db->query(
                        "SELECT audi_usuario, audi_fecha, audi_accion FROM (" .
                        "SELECT audi_usuario, audi_fecha, audi_accion FROM vc_vales WHERE id = ? AND audi_accion = 'I' " .
                        "UNION " .
                        "SELECT audi_usuario, audi_fecha, audi_accion FROM " . SIS_AUD_DB . ".vc_vales WHERE id = ? AND audi_accion = 'I' " .
                        ") a", array($id, $id))->result();
        $vale->fecha_creacion = $vale_auditoria[0]->audi_fecha;

        $data['error'] = $this->session->flashdata('error');
        $vale->numero = 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT);
        $vale->persona = $vale->persona_id;
        $vale->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $vale, TRUE);
        $data['vale'] = $vale;
        if (in_groups($this->grupos_areas, $this->grupos))
        {
            $data['back_url'] = 'listar_areas';
        }
        if (!empty($back_url))
        {
            $data['back_url'] = $back_url;
        }
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver vale';
        $data['title'] = TITLE . ' - Ver vale';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function imprimir($reimprimir = 'No', $desde = NULL, $hasta = NULL, $area_id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        if ($desde !== NULL && ctype_digit($desde) && $hasta !== NULL && ctype_digit($hasta))
        {
            $vales_options = array(
                'join' => array(
                    array(
                        'type' => 'left',
                        'table' => 'vc_vehiculos',
                        'where' => 'vc_vehiculos.id = vc_vales.vehiculo_id',
                        'columnas' => array('vc_vehiculos.dominio as dominio', 'vc_vehiculos.nombre as vehiculo')),
                    array(
                        'type' => 'left',
                        'table' => 'vc_tipos_combustible',
                        'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                        'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                    array(
                        'table' => 'areas',
                        'where' => 'areas.id=vc_vales.area_id',
                        'columnas' => array('areas.codigo as area_codigo', 'areas.nombre as area_nombre')),
                    array(
                        'type' => 'left',
                        'table' => 'vc_estaciones',
                        'where' => 'vc_estaciones.id = vc_vales.estacion_id',
                        'columnas' => array('vc_estaciones.nombre as estacion'))
                )
            );
            $vales_options['where'] = array("vc_vales.estado <> 'Pendiente' AND vc_vales.estado <> 'Anulado'");
            if (!empty($desde))
            {
                $vales_options['id >='] = $desde;
            }

            if (!empty($hasta))
            {
                $vales_options['id <='] = $hasta;
            }

            if ($reimprimir === 'No')
            {
                $vales_options['estado'] = 'Creado';
            }

            if (!empty($area_id))
            {
                $vales_options['area_id'] = $area_id;
            }

            $vales_options['sort_by'] = 'areas.codigo, id';
            $vales = $this->Vales_model->get($vales_options);
            if (empty($vales))
            {
                $data['error'] = '<br />No hay vales a imprimir';
            }
            else
            {
                foreach ($vales as $key => $Vale)
                {
                    if (!empty($Vale->persona_id))
                    {
                        $persona = $this->Personal_model->get(array('Legajo' => $Vale->persona_id));
                        if (!empty($persona))
                        {
                            $vales[$key]->persona_major = $persona->Apellido . ', ' . $persona->Nombre;
                        }
                    }
                }
                $data['vales'] = $vales;
            }
        }
        else
        {
            $data['vales'] = array();
        }
        $data['desde'] = $desde;
        $data['hasta'] = $hasta;

        $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'where' => array("nombre<>'-'"),
            'sort_by' => 'codigo'), array('' => 'Todas las Áreas')
        );
        $array_reimprimir = array('No' => 'No', 'Si' => 'Si');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Desde', 'type' => 'integer', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'integer', 'required' => TRUE),
            'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'reimprimir' => array('label' => 'Reimprimir', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'id_name' => 'reimprimir', 'required' => TRUE)
        );

        $busqueda = new stdClass();
        $busqueda->reimprimir = $reimprimir;
        $busqueda->area_id = $area_id;
        $busqueda->desde = $desde;
        $busqueda->hasta = $hasta;
        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['reimprimir']['array'] = $array_reimprimir;
        $data['fields'] = $this->build_fields($fake_model->fields, $busqueda);
        $data['txt_btn'] = 'Buscar';
        $data['title_view'] = 'Imprimir vales';
        $data['title'] = TITLE . ' - Imprimir vales';
        $data['css'] = 'css/vales_combustible/imprimir.css';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/vales/vales_imprimir', $data);
    }

    public function imprimir_pdf($reimprimir = 'No', $desde = 1, $hasta = NULL, $area_id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        $vales_options = array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_vehiculos',
                    'where' => 'vc_vehiculos.id = vc_vales.vehiculo_id',
                    'columnas' => array('vc_vehiculos.dominio as dominio', 'vc_vehiculos.nombre as vehiculo')),
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                array(
                    'table' => 'areas',
                    'where' => 'areas.id=vc_vales.area_id',
                    'columnas' => array('areas.codigo as area_codigo', 'areas.nombre as area_nombre')),
                array(
                    'type' => 'left',
                    'table' => 'vc_estaciones',
                    'where' => 'vc_estaciones.id = vc_vales.estacion_id',
                    'columnas' => array('vc_estaciones.nombre as estacion'))
            )
        );
        $vales_options['where'] = array("vc_vales.estado <> 'Pendiente' AND vc_vales.estado <> 'Anulado'");
        if (!empty($desde))
        {
            $vales_options['id >='] = $desde;
        }

        if (!empty($hasta))
        {
            $vales_options['id <='] = $hasta;
        }

        if ($reimprimir === 'No')
        {
            $vales_options['estado'] = 'Creado';
        }

        if (!empty($area_id))
        {
            $vales_options['area_id'] = $area_id;
        }

        $vales_options['sort_by'] = 'areas.codigo, id';
        $vales = $this->Vales_model->get($vales_options);
        if (empty($vales))
        {
            $this->session->set_flashdata('error', '<br />No hay vales a imprimir');
            redirect("vales_combustible/vales/imprimir/$reimprimir/$desde/$hasta", 'refresh');
        }
        else
        {
            foreach ($vales as $key => $Vale)
            {
                if (!empty($Vale->persona_id))
                {
                    $persona = $this->Personal_model->get(array('Legajo' => $Vale->persona_id));
                    if (!empty($persona))
                    {
                        $vales[$key]->persona_major = $persona->Apellido . ', ' . $persona->Nombre;
                    }
                }
            }
            $data['desde'] = $desde;
            $data['hasta'] = $hasta;
            $data['vales'] = $vales;
            $html = $this->load->view('vales_combustible/vales/vales_imprimir_pdf', $data, TRUE);
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'c',
                'format' => 'A4',
                'margin_left' => 5,
                'margin_right' => 5,
                'margin_top' => 6,
                'margin_bottom' => 6,
                'margin_header' => 9,
                'margin_footer' => 9
            ]);
            $mpdf->SetDisplayMode('fullwidth');
            $mpdf->pagenumPrefix = 'Página ';
            $mpdf->SetTitle('Vales Combustible');
            $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
            $mpdf->WriteHTML($html, 2);
            $mpdf->Output('vales_combustible.pdf', 'I');
        }
    }

    public function imprimir_planilla()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'where' => array("nombre<>'-'"),
            'sort_by' => 'codigo'), array('' => 'Todas las Áreas')
        );

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Desde Vale', 'type' => 'integer', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta Vale', 'type' => 'integer', 'required' => TRUE),
            'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        if (isset($_POST) && !empty($_POST))
        {
            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $desde = $this->input->post('desde');
                $hasta = $this->input->post('hasta');
                $area_id = $this->input->post('area_id');

                $vales_options = array(
                    'id >=' => $desde,
                    'id <=' => $hasta,
                    'join' => array(
                        array(
                            'type' => 'left',
                            'table' => 'vc_tipos_combustible',
                            'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                            'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                        array(
                            'table' => 'areas',
                            'where' => 'areas.id=vc_vales.area_id',
                            'columnas' => array("CONCAT(areas.codigo, '-', areas.nombre) as area")),
                        array(
                            'type' => 'left',
                            'table' => 'vc_estaciones',
                            'where' => 'vc_estaciones.id = vc_vales.estacion_id',
                            'columnas' => array('vc_estaciones.nombre as estacion'))
                    )
                );

                if (!empty($area_id))
                {
                    $vales_options['area_id'] = $area_id;
                }

                $vales_options['sort_by'] = 'areas.codigo, id';
                $vales = $this->Vales_model->get($vales_options);
                if (empty($vales))
                {
                    $this->session->set_flashdata('error', '<br />No hay vales a imprimir');
                    redirect("vales_combustible/vales/imprimir_planilla", 'refresh');
                }
                else
                {
                    $data['desde'] = $desde;
                    $data['hasta'] = $hasta;
                    $data['vales'] = $vales;
                    $html = $this->load->view('vales_combustible/vales/vales_imprimir_planilla_pdf', $data, TRUE);
                    $mpdf = new \Mpdf\Mpdf([
                        'mode' => 'c',
                        'format' => 'A4',
                        'margin_left' => 6,
                        'margin_right' => 6,
                        'margin_top' => 6,
                        'margin_bottom' => 6,
                        'margin_header' => 9,
                        'margin_footer' => 9
                    ]);
                    $mpdf->SetDisplayMode('fullwidth');
                    $mpdf->pagenumPrefix = 'Página ';
                    $mpdf->SetTitle('Planilla de Vales Combustible');
                    $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
                    $mpdf->WriteHTML($html, 2);
                    $mpdf->Output('planilla_vales_combustible.pdf', 'I');
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['vale'] = NULL;
        $data['txt_btn'] = 'Imprimir Planilla';
        $data['title_view'] = 'Imprimir Planilla';
        $data['title'] = TITLE . ' - Imprimir Planilla';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function anular($id = NULL, $back_url = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        $vale = $this->Vales_model->get_one($id);
        if (in_groups($this->grupos_areas, $this->grupos))
        {
            if (empty($vale) || $vale->estado !== 'Pendiente')
            {
                show_error('No se encontró el Vale', 500, 'Registro no encontrado');
            }
        }
        else
        {
            if (empty($vale) || $vale->estado === 'Anulado' || $vale->estado === 'Asignado')
            {
                show_error('No se encontró el Vale', 500, 'Registro no encontrado');
            }

            if (!empty($vale->remito_id))
            {
                $this->session->set_flashdata('error', '<br />El vale ya esta asignado a un remito. No es posible anularlo');
                if (!empty($back_url))
                {
                    redirect("vales_combustible/vales/$back_url", 'refresh');
                }
                else
                {
                    redirect('vales_combustible/vales/listar', 'refresh');
                }
            }
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'forma_carga' => array('label' => 'Forma de Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'forma_carga', 'disabled' => 'disabled'),
            'remito' => array('label' => 'Remito', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
            'nota' => array('label' => 'Nota', 'maxlength' => '50', 'disabled' => 'disabled'),
            'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'disabled' => 'disabled'),
            'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled'),
            'usuario' => array('label' => 'Usuario Creación', 'disabled' => 'disabled')
        );

        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Vales_model->update(array('id' => $this->input->post('id'), 'estado' => 'Anulado'), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Vales_model->get_msg());
                if (!empty($back_url))
                {
                    redirect("vales_combustible/vales/$back_url", 'refresh');
                }
                else
                {
                    redirect('vales_combustible/vales/listar', 'refresh');
                }
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= '<br>' . $this->Vales_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $vale->numero = 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT);
        $data['fields'] = $this->build_fields($fake_model->fields, $vale, TRUE);
        $data['vale'] = $vale;
        if (!empty($back_url))
        {
            $data['back_url'] = $back_url;
        }
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular vale';
        $data['title'] = TITLE . ' - Anular vale';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function anular_masivo()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Desde', 'type' => 'integer', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'integer', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->input->post('desde') > $this->input->post('hasta'))
        {
            $error_msg = '<br />El campo Hasta debe contener un número mayor o igual a Desde';
        }

        if (($this->input->post('desde') + 10) <= $this->input->post('hasta'))
        {
            $error_msg = '<br />Máximo de vales a anular masivamente: 10';
        }

        if ($this->form_validation->run() === TRUE && !$error_msg)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            for ($i = $this->input->post('desde'); $i <= $this->input->post('hasta'); $i++)
            {
                $vale = $this->Vales_model->get(array('id' => $i));
                if (!empty($vale) && $vale->estado !== 'Anulado' && $vale->estado !== 'Asignado')
                {
                    $trans_ok &= $this->Vales_model->update(array(
                        'id' => $i,
                        'estado' => 'Anulado'), FALSE);
                }
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Vales_model->get_msg());
                redirect('vales_combustible/vales/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= '<br>' . $this->Vales_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($fake_model->fields, NULL);
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular vales';
        $data['title'] = TITLE . ' - Anular vales';
        $this->load_template('vales_combustible/vales/vales_anular_masivo', $data);
    }

    public function desanular($id = NULL, $back_url = NULL)
    {
        if (!in_groups($this->grupos_admin_vales, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/ver/$id", 'refresh');
        }

        $vale = $this->Vales_model->get_one($id);
        if (empty($vale) || $vale->estado !== 'Anulado')
        {
            show_error('No se encontró el Vale', 500, 'Registro no encontrado');
        }

        $columna = '';
        if (in_groups($this->grupos_contaduria, $this->grupos))
        {
            $columna = 'desanula_con';
        }
        elseif (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_hacienda, $this->grupos))
        {
            $columna = 'desanula_hac';
        }

        if (empty($columna))
        {
            show_404();
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'numero' => array('label' => 'Número', 'disabled' => 'disabled'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
            'remito' => array('label' => 'Remito', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
            'nota' => array('label' => 'Nota', 'maxlength' => '50', 'disabled' => 'disabled'),
            'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'disabled' => 'disabled'),
            'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled'),
            'usuario' => array('label' => 'Usuario Creación', 'disabled' => 'disabled')
        );

        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.', 500);
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            if ((in_groups($this->grupos_contaduria, $this->grupos) && $vale->desanula_hac != 0) || ((in_groups($this->grupos_hacienda, $this->grupos) || in_groups($this->grupos_admin, $this->grupos)) && $vale->desanula_con != 0))
            {
                $trans_ok &= $this->Vales_model->update(array('id' => $this->input->post('id'), 'desanula_con' => '0', 'desanula_hac' => '0', 'estado' => 'Creado'), FALSE);
            }
            else
            {
                $trans_ok &= $this->Vales_model->update(array('id' => $this->input->post('id'), $columna => 1), FALSE);
            }
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Vales_model->get_msg());
                if (!empty($back_url))
                {
                    redirect("vales_combustible/vales/$back_url", 'refresh');
                }
                else
                {
                    redirect('vales_combustible/vales/listar', 'refresh');
                }
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= $this->Vales_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $vale->numero = 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT);
        $data['fields'] = $this->build_fields($fake_model->fields, $vale, TRUE);
        $data['vale'] = $vale;
        if (!empty($back_url))
        {
            $data['back_url'] = $back_url;
        }
        $data['txt_btn'] = 'Desanular';
        $data['title_view'] = 'Desanular vale';
        $data['title'] = TITLE . ' - Desanular vale';
        $this->load_template('vales_combustible/vales/vales_abm', $data);
    }

    public function marcar_impresos()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('desde', 'Desde', 'required|integer');
        $this->form_validation->set_rules('hasta', 'Hasta', 'required|integer');
        $this->form_validation->set_rules('area', 'Área', 'integer');
        if ($this->form_validation->run() === TRUE)
        {
            $this->Vales_model->marcar_impreso($this->input->post('desde'), $this->input->post('hasta'), $this->input->post('area'));
            echo json_encode(array('msg' => 'OK'));
        }
    }

    public function aprobar()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            $this->output->set_status_header('403');
            $return_data['message'] = 'No tiene permisos para la acción solicitada';
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->output->set_status_header('403');
            $return_data['message'] = 'Usuario sin permisos de edición';
        }

        $this->form_validation->set_rules('vale_id', 'Vale', 'integer|required');
        $this->form_validation->set_rules('vencimiento', 'Vencimiento', 'date|required');
        if ($this->form_validation->run() === TRUE)
        {
            $vale = $this->Vales_model->get(array('id' => $this->input->post('vale_id')));
            if (empty($vale) || $vale->estado !== 'Pendiente')
            {
                $this->output->set_status_header('500');
                $return_data['message'] = 'No se encontró el Vale';
            }
            $vale->numero = 'VC' . str_pad($vale->id, 6, '0', STR_PAD_LEFT);

            $vencimiento = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Vales_model->update(array(
                'id' => $vale->id,
                'vencimiento' => $vencimiento->format('Y-m-d'),
                'area_id' => $vale->area_id,
                'persona_id' => $vale->persona_id,
                'persona_nombre' => $vale->persona_nombre,
                'metros_cubicos' => $vale->metros_cubicos,
                'vehiculo_id' => $vale->vehiculo_id,
                'tipo_combustible_id' => $vale->tipo_combustible_id,
                'nota' => $vale->nota,
                'estacion_id' => $vale->estacion_id,
                'estado' => 'Creado',
                'observaciones' => $vale->observaciones), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->output->set_status_header('200');
                $return_data['message'] = "Vale $vale->numero aprobado";
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = 'Se ha producido un error con la base de datos.';
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= $this->Vales_model->get_error();
                }
                $this->output->set_status_header('500');
                $return_data['message'] = 'ERROR: ' . $error_msg;
            }
        }
        else
        {
            $this->output->set_status_header('400');
            $return_data['message'] = validation_errors();
        }

        echo json_encode($return_data);
    }

    public function acciones_masivas_Anular()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_tipo_control = array('Anular' => 'Anular');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'required' => TRUE),
            'vale[]' => array('label' => 'Vales', 'required' => TRUE),
            'back_url' => array('label' => 'URL', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $vales = $this->input->post('vale');
            $this->db->trans_begin();
            $trans_ok = TRUE;
            foreach ($vales as $Vale_id)
            {
                $vale = $this->Vales_model->get(array('id' => $Vale_id));
                if (in_groups($this->grupos_areas, $this->grupos))
                {
                    if (!empty($vale) && $vale->estado === 'Pendiente')
                    {
                        $trans_ok &= $this->Vales_model->update(array('id' => $Vale_id, 'estado' => 'Anulado'), FALSE);
                    }
                    else
                    {
                        $error_msg = '<br />No se pueden anular los vales. Verifique que todos esten en estado "Pendiente"';
                        $trans_ok = FALSE;
                        break;
                    }
                }
                else
                {
                    if (!empty($vale) && $vale->estado !== 'Anulado' && $vale->estado !== 'Asignado')
                    {
                        $trans_ok &= $this->Vales_model->update(array('id' => $Vale_id, 'estado' => 'Anulado'), FALSE);
                    }
                    else
                    {
                        $error_msg = '<br />No se pueden anular los vales. Verifique que no esten en estado "Asignado"';
                        $trans_ok = FALSE;
                        break;
                    }
                }
            }
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', '<br />Vales anulados correctamente');
            }
            else
            {
                $this->db->trans_rollback();
                if (empty($error_msg))
                {
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                }
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= '<br>' . $this->Vales_model->get_error();
                }
                $this->session->set_flashdata('error', !empty($error_msg) ? $error_msg : '');
            }
        }
        else
        {
            $this->session->set_flashdata('error', validation_errors() ? validation_errors() : '');
        }
        redirect('vales_combustible/vales/' . $this->input->post('back_url'), 'refresh');
    }

    //TODOOO
    public function acciones_masivas_Aprobar()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_tipo_control = array('Aprobar' => 'Aprobar');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'required' => TRUE),
            'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'required' => TRUE),
            'vale[]' => array('label' => 'Vales', 'required' => TRUE),
            'back_url' => array('label' => 'URL', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $vales = $this->input->post('vale');
            $vencimiento = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento'));
            $this->db->trans_begin();
            $trans_ok = TRUE;
            foreach ($vales as $Vale_id)
            {
                $vale = $this->Vales_model->get(array('id' => $Vale_id));
                if (!empty($vale) && $vale->estado === 'Pendiente')
                {
                    $trans_ok &= $this->Vales_model->update(array(
                        'id' => $vale->id,
                        'vencimiento' => $vencimiento->format('Y-m-d'),
                        'area_id' => $vale->area_id,
                        'persona_id' => $vale->persona_id,
                        'persona_nombre' => $vale->persona_nombre,
                        'metros_cubicos' => $vale->metros_cubicos,
                        'vehiculo_id' => $vale->vehiculo_id,
                        'tipo_combustible_id' => $vale->tipo_combustible_id,
                        'nota' => $vale->nota,
                        'estacion_id' => $vale->estacion_id,
                        'estado' => 'Creado',
                        'observaciones' => $vale->observaciones), FALSE);
                }
            }
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', '<br />Vales aprobados correctamente');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= '<br>' . $this->Vales_model->get_error();
                }
                $this->session->set_flashdata('error', !empty($error_msg) ? $error_msg : '');
            }
        }
        else
        {
            $this->session->set_flashdata('error', validation_errors() ? validation_errors() : '');
        }
        redirect('vales_combustible/vales/' . $this->input->post('back_url'), 'refresh');
    }

    public function acciones_masivas_Imprimir()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_tipo_control = array('Imprimir' => 'Imprimir');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'required' => TRUE),
            'vale[]' => array('label' => 'Vales', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        if ($this->form_validation->run() === TRUE)
        {
            $vales_id = $this->input->post('vale');
            $vales_options = array(
                'join' => array(
                    array(
                        'type' => 'left',
                        'table' => 'vc_vehiculos',
                        'where' => 'vc_vehiculos.id = vc_vales.vehiculo_id',
                        'columnas' => array('vc_vehiculos.dominio as dominio', 'vc_vehiculos.nombre as vehiculo')),
                    array(
                        'type' => 'left',
                        'table' => 'vc_tipos_combustible',
                        'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                        'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                    array(
                        'table' => 'areas',
                        'where' => 'areas.id=vc_vales.area_id',
                        'columnas' => array('areas.codigo as area_codigo', 'areas.nombre as area_nombre')),
                    array(
                        'type' => 'left',
                        'table' => 'vc_estaciones',
                        'where' => 'vc_estaciones.id = vc_vales.estacion_id',
                        'columnas' => array('vc_estaciones.nombre as estacion'))
                )
            );
            $vales_options['where_in'] = array(array('column' => 'vc_vales.id', 'value' => $vales_id));
            $vales_options['where'] = array("vc_vales.estado <> 'Pendiente' AND vc_vales.estado <> 'Anulado'");
            $vales_options['sort_by'] = 'id';
            $vales = $this->Vales_model->get($vales_options);
            if (empty($vales))
            {
                $this->session->set_flashdata('error', '<br />No hay vales a imprimir');
            }
            else
            {
                foreach ($vales as $key => $Vale)
                {
                    if (!empty($Vale->persona_id))
                    {
                        $persona = $this->Personal_model->get(array('Legajo' => $Vale->persona_id));
                        if (!empty($persona))
                        {
                            $vales[$key]->persona_major = $persona->Apellido . ', ' . $persona->Nombre;
                        }
                    }
                    $this->Vales_model->marcar_impreso($Vale->id, $Vale->id, $Vale->area_id);
                }
                $data['vales'] = $vales;
                $this->session->set_flashdata('message', '<br />Vales impresos correctamente');

                $html = $this->load->view('vales_combustible/vales/vales_imprimir_pdf', $data, TRUE);
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'c',
                    'format' => 'A4',
                    'margin_left' => 5,
                    'margin_right' => 5,
                    'margin_top' => 12,
                    'margin_bottom' => 12,
                    'margin_header' => 9,
                    'margin_footer' => 9
                ]);
                $mpdf->SetDisplayMode('fullwidth');
                $mpdf->pagenumPrefix = 'Página ';
                $mpdf->SetTitle('Vales Combustible');
                $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
                $mpdf->WriteHTML($html, 2);
                $mpdf->Output('vales_combustible.pdf', 'I');
            }
        }
        else
        {
            $this->session->set_flashdata('error', validation_errors() ? validation_errors() : '');
        }
        redirect('vales_combustible/vales/listar', 'refresh');
    }

    public function acciones_masivas_Repetir()
    {
        if ((!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_hacienda, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos)))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_tipo_control = array('Repetir' => 'Repetir');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'required' => TRUE),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
            'vale[]' => array('label' => 'Vales', 'required' => TRUE),
            'back_url' => array('label' => 'URL', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        $cupo_msg = '';
        if ($this->form_validation->run() === TRUE)
        {
            $vales = $this->input->post('vale');
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $temp_metros_area = array();
            foreach ($vales as $Vale_id)
            {
                $vale = $this->Vales_model->get(array('id' => $Vale_id));
                if (in_groups($this->grupos_areas, $this->grupos))
                {
                    if (!empty($vale) && !empty($vale->vehiculo_id) && !empty($vale->persona_id) && !empty($vale->observaciones))
                    {
                        $vehiculo = $this->Vehiculos_model->get(array('id' => $vale->vehiculo_id));
                        if (!empty($vehiculo))
                        {
                            $hoy = new DateTime();
                            $venc_seguro = new DateTime($vehiculo->vencimiento_seguro);
                            if ($venc_seguro > $hoy && $vehiculo->estado === 'Aprobado')
                            {
                                $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
                                $vencimiento = clone $fecha;
                                $vencimiento->add(new DateInterval('P7D'));

                                if (empty($temp_metros_area[$vale->area_id]))
                                {
                                    $temp_metros_area[$vale->area_id] = $vale->metros_cubicos;
                                }
                                else
                                {
                                    $temp_metros_area[$vale->area_id] += $vale->metros_cubicos;
                                }

                                $cupos_combustible = $this->Cupos_combustible_model->get(array(
                                    'select' => array("(metros_cubicos + (CASE WHEN ampliacion_vencimiento >= '" . $fecha->format('Y-m-d') . "' THEN ampliacion ELSE 0 END)) as total"),
                                    'area_id' => $vale->area_id,
                                    'tipo_combustible_id' => $vale->tipo_combustible_id,
                                    'fecha_inicio <=' => $fecha->format('Y-m-d'),
                                    'sort_by' => 'fecha_inicio DESC'
                                ));
                                if (empty($cupos_combustible))
                                {
                                    $error_msg = '<br>Sin cupo asignado. Por favor contacte a la oficina de Auditoría';
                                }
                                else
                                {
                                    //SEMANAL
                                    $cupo_semanal = $cupos_combustible[0]->total;
                                    $ini_sem = clone $fecha;
                                    $ini_sem->modify('this week');
                                    $fin_sem = clone $fecha;
                                    $fin_sem->modify('this week +6 days');
                                    $ini_sem_sql = $ini_sem->format('Y-m-d');
                                    $fin_sem_sql = $fin_sem->format('Y-m-d');
                                    $vales_oficina_semanal = $this->Vales_model->get(array(
                                        'area_id' => $vale->area_id,
                                        'tipo_combustible_id' => $vale->tipo_combustible_id,
                                        'fecha >=' => $ini_sem_sql,
                                        'fecha <=' => $fin_sem_sql,
                                        'estado !=' => 'Anulado'
                                    ));
                                    $cupo_semanal_usado = 0;
                                    if (!empty($vales_oficina_semanal))
                                    {
                                        foreach ($vales_oficina_semanal as $Vale)
                                        {
                                            $cupo_semanal_usado += $Vale->metros_cubicos;
                                        }
                                    }
                                    if ($cupo_semanal_usado + $this->input->post('metros_cubicos') > $cupo_semanal)
                                    {
                                        $cupo_msg = '<br>Su pedido supera el cupo semanal autorizado. Se realizó el vale pero se descontará de su cupo mensual';
                                    }

                                    //MENSUAL
                                    $cupo_mensual = $cupos_combustible[0]->total * 4;
                                    $ini_mes = clone $fecha;
                                    $ini_mes->modify('first day of this month');
                                    $fin_mes = clone $fecha;
                                    $fin_mes->modify('last day of this month');
                                    $ini_mes_sql = $ini_mes->format('Y-m-d');
                                    $fin_mes_sql = $fin_mes->format('Y-m-d');
                                    $vales_oficina = $this->Vales_model->get(array(
                                        'area_id' => $vale->area_id,
                                        'tipo_combustible_id' => $vale->tipo_combustible_id,
                                        'fecha >=' => $ini_mes_sql,
                                        'fecha <=' => $fin_mes_sql,
                                        'estado !=' => 'Anulado'
                                    ));
                                    $cupo_mensual_usado = 0;
                                    if (!empty($vales_oficina))
                                    {
                                        foreach ($vales_oficina as $Vale)
                                        {
                                            $cupo_mensual_usado += $Vale->metros_cubicos;
                                        }
                                    }
                                    if ($cupo_mensual_usado + $this->input->post('metros_cubicos') > $cupo_mensual)
                                    {
                                        $error_msg = '<br>Su pedido supera el cupo mensual autorizado. Por favor contacte a la oficina de Auditoría';
                                    }
                                }

                                if (empty($error_msg))
                                {
                                    $trans_ok &= $this->Vales_model->create(array(
                                        'fecha' => $fecha->format('Y-m-d'),
                                        'vencimiento' => $vencimiento->format('Y-m-d'),
                                        'area_id' => $vale->area_id,
                                        'persona_id' => $vale->persona_id,
                                        'persona_nombre' => $vale->persona_nombre,
                                        'metros_cubicos' => $vale->metros_cubicos,
                                        'vehiculo_id' => $vale->vehiculo_id,
                                        'tipo_combustible_id' => $vale->tipo_combustible_id,
                                        'forma_carga' => $vale->forma_carga,
                                        'periodicidad' => $vale->periodicidad,
                                        'nota' => $vale->nota,
                                        'observaciones' => $vale->observaciones,
                                        'estacion_id' => $vale->estacion_id,
                                        'estado' => 'Creado',
                                        'user_id' => $this->session->userdata('user_id')), FALSE);
                                }
                                else
                                {
                                    $trans_ok = FALSE;
                                    break;
                                }
                            }
                            else
                            {
                                $error_msg = '<br />No se pueden duplicar los vales. Verifique que todos los vehículos estén "Aprobados" y con seguro vigente';
                                $trans_ok = FALSE;
                                break;
                            }
                        }
                        else
                        {
                            $error_msg = '<br />No se pueden duplicar los vales. Verifique que todos contenga vehículo asignado';
                            $trans_ok = FALSE;
                            break;
                        }
                    }
                    else
                    {
                        $error_msg = '<br />No se pueden duplicar los vales. Verifique que todos contenga vehículo y beneficiario asignados';
                        $trans_ok = FALSE;
                        break;
                    }
                }
                else
                {
                    if (!empty($vale))
                    {
                        if (!empty($vale->vehiculo_id))
                        {
                            $vehiculo = $this->Vehiculos_model->get(array('id' => $vale->vehiculo_id));
                            if (!empty($vehiculo))
                            {
                                $hoy = new DateTime();
                                $venc_seguro = new DateTime($vehiculo->vencimiento_seguro);
                                if ($venc_seguro > $hoy && $vehiculo->estado === 'Aprobado')
                                {
                                    $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
                                    $vencimiento = clone $fecha;
                                    $vencimiento->add(new DateInterval('P7D'));
                                    $trans_ok &= $this->Vales_model->create(array(
                                        'fecha' => $fecha->format('Y-m-d'),
                                        'vencimiento' => $vencimiento->format('Y-m-d'),
                                        'area_id' => $vale->area_id,
                                        'persona_id' => $vale->persona_id,
                                        'persona_nombre' => $vale->persona_nombre,
                                        'metros_cubicos' => $vale->metros_cubicos,
                                        'vehiculo_id' => $vale->vehiculo_id,
                                        'tipo_combustible_id' => $vale->tipo_combustible_id,
                                        'forma_carga' => $vale->forma_carga,
                                        'periodicidad' => $vale->periodicidad,
                                        'nota' => $vale->nota,
                                        'observaciones' => $vale->observaciones,
                                        'estacion_id' => $vale->estacion_id,
                                        'estado' => 'Pendiente',
                                        'user_id' => $this->session->userdata('user_id')), FALSE);
                                }
                                else
                                {
                                    $error_msg = '<br />No se pueden duplicar los vales. Verifique que todos los vehículos estén "Aprobados" y con seguro vigente';
                                    $trans_ok = FALSE;
                                    break;
                                }
                            }
                            else
                            {
                                $error_msg = '<br />No se pueden duplicar los vales. Verifique que todos contenga vehículo asignado';
                                $trans_ok = FALSE;
                                break;
                            }
                        }
                        else
                        {
                            $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
                            $vencimiento = clone $fecha;
                            $vencimiento->add(new DateInterval('P7D'));
                            $trans_ok &= $this->Vales_model->create(array(
                                'fecha' => $fecha->format('Y-m-d'),
                                'vencimiento' => $vencimiento->format('Y-m-d'),
                                'area_id' => $vale->area_id,
                                'persona_id' => $vale->persona_id,
                                'persona_nombre' => $vale->persona_nombre,
                                'metros_cubicos' => $vale->metros_cubicos,
                                'vehiculo_id' => $vale->vehiculo_id,
                                'tipo_combustible_id' => $vale->tipo_combustible_id,
                                'forma_carga' => $vale->forma_carga,
                                'periodicidad' => $vale->periodicidad,
                                'nota' => $vale->nota,
                                'observaciones' => $vale->observaciones,
                                'estacion_id' => $vale->estacion_id,
                                'estado' => 'Pendiente',
                                'user_id' => $this->session->userdata('user_id')), FALSE);
                        }
                    }
                    else
                    {
                        $error_msg = '<br />No se pueden duplicar los vales.';
                        $trans_ok = FALSE;
                        break;
                    }
                }
            }
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', '<br />Vales duplicados correctamente' . $cupo_msg);
            }
            else
            {
                $this->db->trans_rollback();
                if (empty($error_msg))
                {
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                }
                if ($this->Vales_model->get_error())
                {
                    $error_msg .= '<br>' . $this->Vales_model->get_error();
                }
                $this->session->set_flashdata('error', !empty($error_msg) ? $error_msg : '');
            }
        }
        else
        {
            $this->session->set_flashdata('error', validation_errors() ? validation_errors() : '');
        }
        redirect('vales_combustible/vales/' . $this->input->post('back_url'), 'refresh');
    }
}
