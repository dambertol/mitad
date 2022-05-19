<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tablero extends MY_Controller
{

    /**
     * Controlador de Cementerios
     * Autor: Leandro
     * Creado: 28/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Concesiones_model');
        $this->load->model('defunciones/Constructores_model');
        $this->grupos_permitidos = array('admin', 'defunciones_user', 'defunciones_consulta_general');
        $this->grupos_solo_consulta = array('defunciones_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableDataC = array(
            'columns' => array(
                array('label' => 'Difunto', 'data' => 'difunto', 'width' => 34),
                array('label' => 'Cementerio', 'data' => 'cementerio', 'width' => 20),
                array('label' => 'Tipo', 'data' => 'ubicacion_tipo', 'width' => 14),
                array('label' => 'Inicio', 'data' => 'inicio_concesion', 'width' => 12, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Fin', 'data' => 'fin_concesion', 'width' => 12, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'imprimir', 'width' => 4, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 4, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            ),
            'table_id' => 'concesiones_table',
            'source_url' => 'defunciones/tablero/listar_data_concesiones',
            'order' => array(array(4, 'asc')),
            'reuse_var' => TRUE,
            'initComplete' => 'complete_concesiones_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_tableC'] = buildHTML($tableDataC);
        $data['js_tableC'] = buildJS($tableDataC);

        $tableDataP = array(
            'columns' => array(
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 28),
                array('label' => 'DNI', 'data' => 'dni', 'width' => 14, 'class' => 'dt-body-right'),
                array('label' => 'Domicilio', 'data' => 'domicilio', 'width' => 26),
                array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 14, 'class' => 'dt-body-right'),
                array('label' => 'Vencimiento', 'data' => 'vencimiento', 'width' => 14, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 4, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            ),
            'table_id' => 'permisos_table',
            'source_url' => 'defunciones/tablero/listar_data_permisos',
            'order' => array(array(4, 'asc')),
            'reuse_var' => TRUE,
            'initComplete' => 'complete_permisos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_tableP'] = buildHTML($tableDataP);
        $data['js_tableP'] = buildJS($tableDataP);

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_viewC'] = 'Concesiones';
        $data['title_viewP'] = 'Permisos Constructores';
        $data['title'] = TITLE . ' - Vencimientos';
        $this->load_template('defunciones/tablero/tablero_listar', $data);
    }

    public function listar_data_concesiones()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("df_concesiones.id, CONCAT(df_difuntos.apellido, ', ', df_difuntos.nombre) as difunto, df_cementerios.nombre as cementerio, df_ubicaciones.tipo as ubicacion_tipo, df_concesiones.inicio as inicio_concesion, df_concesiones.fin as fin_concesion")
                ->from('df_operaciones')
                ->join('df_difuntos', 'df_difuntos.id = df_operaciones.difunto_id', 'left')
                ->join('df_concesiones', 'df_concesiones.operacion_id = df_operaciones.id AND df_difuntos.ultima_concesion_id = df_concesiones.id', 'left')
                ->join('df_ubicaciones', 'df_ubicaciones.id = df_concesiones.ubicacion_id', 'left')
                ->join('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'left')
                ->where('tipo_concesion', 'Alquiler')
                ->add_column('imprimir', '<a href="defunciones/concesiones/imprimir/$1" target="_blank" title="Imprimir" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>', 'id')
                ->add_column('ver', '<a href="defunciones/concesiones/ver/$1" target="_blank" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function listar_data_permisos()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("df_constructores.id, df_constructores.nombre, df_constructores.dni, df_constructores.domicilio, df_constructores.telefono, df_permisos_constructores.vencimiento as vencimiento")
                ->from('df_constructores')
                ->join('df_permisos_constructores', 'df_permisos_constructores.id = df_constructores.ultimo_permiso_id', 'left')
                ->add_column('ver', '<a href="defunciones/constructores/ver/$1" target="_blank" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id');

        echo $this->datatables->generate();
    }
}
