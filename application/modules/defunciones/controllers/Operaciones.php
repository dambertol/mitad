<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Operaciones extends MY_Controller
{

    /**
     * Controlador de Operaciones
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Operaciones_model');
        $this->load->model('defunciones/Difuntos_model');
        $this->load->model('defunciones/Solicitantes_model');
        $this->load->model('defunciones/Expedientes_model');
        $this->load->model('Usuarios_model');
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

        $tableData = array(
            'columns' => array(
                array('label' => 'ID', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Solicitante', 'data' => 'solicitante', 'width' => 16),
                array('label' => 'Difunto', 'data' => 'difunto', 'width' => 16),
                array('label' => 'Fecha Trámite', 'data' => 'fecha_tramite', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Tipo Operación', 'data' => 'tipo_operacion', 'width' => 9),
                array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 22),
                array('label' => 'Fecha Pago', 'data' => 'fecha_pago', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Boleta Pago', 'data' => 'boleta_pago', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'operaciones_table',
            'source_url' => 'defunciones/operaciones/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_operaciones_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = array('' => 'Todos', '1' => 'Concesión', '2' => 'Ornato', '3' => 'Reducción', '4' => 'Traslado');
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Operaciones';
        $data['title'] = TITLE . ' - Operaciones';
        $this->load_template('defunciones/operaciones/operaciones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('defunciones/datatables_functions_helper');
        $this->datatables
                ->select("df_operaciones.id, df_solicitantes.nombre as solicitante, CONCAT(df_difuntos.apellido, ', ', df_difuntos.nombre) as difunto, df_operaciones.fecha_tramite, df_operaciones.tipo_operacion, df_operaciones.observaciones, df_operaciones.fecha_pago, df_operaciones.boleta_pago")
                ->from('df_operaciones')
                ->join('df_solicitantes', 'df_solicitantes.id = df_operaciones.solicitante_id', 'left')
                ->join('df_difuntos', 'df_difuntos.id = df_operaciones.difunto_id', 'left')
                ->edit_column('tipo_operacion', '$1', 'dt_column_operaciones_tipo(tipo_operacion)', TRUE)
                ->add_column('imprimir', '<a href="defunciones/operaciones/imprimir/$1" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>', 'id')
                ->add_column('ver', '<a href="defunciones/operaciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/operaciones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/operaciones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
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
            redirect("defunciones/operaciones/ver/$id", 'refresh');
        }

        $operacion = $this->Operaciones_model->get(array('id' => $id));
        if (empty($operacion))
        {
            show_error('No se encontró la Operación', 500, 'Registro no encontrado');
        }

        switch ($operacion->tipo_operacion)
        {
            case '1':
                $tipo_operacion = "concesiones";
                break;
            case '2':
                $tipo_operacion = "ornatos";
                break;
            case '3':
                $tipo_operacion = "reducciones";
                break;
            case '4':
                $tipo_operacion = "traslados";
                break;
            case '5':
                $tipo_operacion = "compras_terrenos";
                break;
        }
        if (!empty($tipo_operacion))
        {
            $this->load->model("defunciones/{$tipo_operacion}_model");
            $operaciones = $this->{"{$tipo_operacion}_model"}->get(array('operacion_id' => $operacion->id));
            if (!empty($operaciones))
            {
                $operacion->operacion_id = $operaciones[0]->id;
            }
            if (!empty($operacion->operacion_id))
            {
                redirect("defunciones/$tipo_operacion/editar/$operacion->operacion_id");
            }
        }
        else
        {
            redirect("defunciones/operaciones/listar");
        }
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
            redirect("defunciones/operaciones/ver/$id", 'refresh');
        }

        $operacion = $this->Operaciones_model->get_one($id);
        if (empty($operacion))
        {
            show_error('No se encontró la Operación', 500, 'Registro no encontrado');
        }

        switch ($operacion->tipo_operacion)
        {
            case '1':
                $tipo_operacion = "concesiones";
                break;
            case '2':
                $tipo_operacion = "ornatos";
                break;
            case '3':
                $tipo_operacion = "reducciones";
                break;
            case '4':
                $tipo_operacion = "traslados";
                break;
            case '5':
                $tipo_operacion = "compras_terrenos";
                break;
        }
        if (!empty($tipo_operacion))
        {
            $this->load->model("defunciones/{$tipo_operacion}_model");
            $operaciones = $this->{"{$tipo_operacion}_model"}->get(array('operacion_id' => $operacion->id));
            if (!empty($operaciones))
            {
                $operacion->operacion_id = $operaciones[0]->id;
            }
            if (!empty($operacion->operacion_id))
            {
                redirect("defunciones/$tipo_operacion/eliminar/$operacion->operacion_id");
            }
        }
        else
        {
            redirect("defunciones/operaciones/listar");
        }
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $operacion = $this->Operaciones_model->get_one($id);
        if (empty($operacion))
        {
            show_error('No se encontró la Operación', 500, 'Registro no encontrado');
        }

        switch ($operacion->tipo_operacion)
        {
            case '1':
                $tipo_operacion = "concesiones";
                break;
            case '2':
                $tipo_operacion = "ornatos";
                break;
            case '3':
                $tipo_operacion = "reducciones";
                break;
            case '4':
                $tipo_operacion = "traslados";
                break;
            case '5':
                $tipo_operacion = "compras_terrenos";
                break;
        }
        if (!empty($tipo_operacion))
        {
            $this->load->model("defunciones/{$tipo_operacion}_model");
            $operaciones = $this->{"{$tipo_operacion}_model"}->get(array('operacion_id' => $operacion->id));
            if (!empty($operaciones))
            {
                $operacion->operacion_id = $operaciones[0]->id;
            }
            if (!empty($operacion->operacion_id))
            {
                redirect("defunciones/$tipo_operacion/ver/$operacion->operacion_id");
            }
        }
        else
        {
            redirect("defunciones/operaciones/listar");
        }
    }

    public function imprimir($id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $operacion = $this->Operaciones_model->get(array(
            'id' => $id,
            'join' => array(
                array(
                    'table' => 'df_solicitantes',
                    'where' => 'df_solicitantes.id = df_operaciones.solicitante_id',
                    'columnas' => array('df_solicitantes.nombre as s_nombre', 'df_solicitantes.dni as s_dni', 'df_solicitantes.domicilio as s_domicilio', 'df_solicitantes.telefono as s_telefono', 'df_solicitantes.domicilio_alt as s_domicilio_alt', 'df_solicitantes.telefono_alt as s_telefono_alt')),
                array(
                    'type' => 'LEFT',
                    'table' => 'df_difuntos',
                    'where' => 'df_difuntos.id = df_operaciones.difunto_id',
                    'columnas' => array('df_difuntos.nombre as d_nombre', 'df_difuntos.apellido as d_apellido', 'df_difuntos.dni as d_dni', 'df_difuntos.causa_muerte as d_causa', 'df_difuntos.defuncion as d_defuncion', 'df_difuntos.edad as d_edad', 'df_difuntos.ficha as d_ficha', 'df_difuntos.nacimiento as d_nacimiento', 'df_difuntos.observaciones as d_observaciones')),
                array(
                    'type' => 'LEFT',
                    'table' => 'df_cocherias',
                    'where' => 'df_cocherias.id = df_difuntos.cocheria_id',
                    'columnas' => array('df_cocherias.nombre as cocheria_difunto')),
                array(
                    'type' => 'LEFT',
                    'table' => 'users',
                    'where' => 'users.id = df_operaciones.user_id'),
                array(
                    'type' => 'LEFT',
                    'table' => 'personas',
                    'where' => 'personas.id = users.persona_id',
                    'columnas' => array("CONCAT(personas.nombre, ' ', personas.apellido) as agente"))
            )
        ));
        if (empty($operacion))
        {
            show_error('No se encontró la Operación', 500, 'Registro no encontrado');
        }

        switch ($operacion->tipo_operacion)
        {
            case '1':
                $tipo_operacion = "concesiones";
                break;
            case '2':
                $tipo_operacion = "ornatos";
                break;
            case '3':
                $tipo_operacion = "reducciones";
                break;
            case '4':
                $tipo_operacion = "traslados";
                break;
            case '5':
                $tipo_operacion = "compras_terrenos";
                break;
        }
        if (!empty($tipo_operacion))
        {
            $this->load->model("defunciones/{$tipo_operacion}_model");
            $join_array = array();
            if ($tipo_operacion === "ornatos" || $tipo_operacion === "compras_terrenos")
                $join_array[] = array(
                    'table' => 'df_constructores',
                    'where' => "df_constructores.id = df_{$tipo_operacion}.constructor_id",
                    'columnas' => array('df_constructores.nombre as c_nombre', 'df_constructores.dni as c_dni')
                );

            if ($tipo_operacion !== "traslados")
            {
                $join_array[] = array(
                    'table' => 'df_ubicaciones',
                    'where' => 'df_ubicaciones.id = df_' . $tipo_operacion . '.ubicacion_id',
                    'columnas' => array('df_ubicaciones.tipo as u_tipo', 'df_ubicaciones.nicho as u_nicho', 'df_ubicaciones.fila as u_fila', 'df_ubicaciones.cuadro as u_cuadro', 'df_ubicaciones.sector as u_sector', 'df_ubicaciones.denominacion as u_denominacion')
                );
            }
            else
            {
                $join_array[] = array(
                    'table' => 'df_ubicaciones UD',
                    'where' => 'UD.id = df_traslados.ubicacion_destino_id',
                    'columnas' => array('UD.tipo as tipo_d', 'UD.sector as sector_d', 'UD.cuadro as cuadro_d', 'UD.fila as fila_d', 'UD.nicho as nicho_d', 'UO.denominacion as denominacion_d')
                );
                $join_array[] = array(
                    'table' => 'df_cementerios CD',
                    'where' => 'CD.id = UD.cementerio_id',
                    'columnas' => array('CD.nombre as cementerio_d')
                );
                $join_array[] = array(
                    'table' => 'df_ubicaciones UO',
                    'where' => 'UO.id = df_traslados.ubicacion_origen_id',
                    'columnas' => array('UO.tipo as tipo_o', 'UO.sector as sector_o', 'UO.cuadro as cuadro_o', 'UO.fila as fila_o', 'UO.nicho as nicho_o', 'UO.denominacion as denominacion_o')
                );
                $join_array[] = array(
                    'table' => 'df_cementerios CO',
                    'where' => 'CO.id = UO.cementerio_id',
                    'columnas' => array('CO.nombre as cementerio_o')
                );
                $join_array[] = array(
                    'type' => 'LEFT',
                    'table' => 'df_cocherias CH',
                    'where' => 'CH.id = df_traslados.cocheria_traslado_id',
                    'columnas' => array('CH.nombre as cocheria')
                );
            }

            $detalle_operacion = $this->{"{$tipo_operacion}_model"}->get(array(
                'operacion_id' => $operacion->id,
                'join' => $join_array
            ));
            $data['detalle_operacion'] = $detalle_operacion[0];
        }
        $data['operacion'] = $operacion;

        if ($operacion->tipo_operacion === 5)
        {
            $html = $this->load->view('defunciones/operaciones/operaciones_compra_pdf', $data, TRUE);
        }
        else
        {
            $html = $this->load->view('defunciones/operaciones/operaciones_pdf', $data, TRUE);
        }

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
        $mpdf->simpleTables = true;
        $mpdf->SetTitle("Solicitud Defunciones");
        $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output("Operacion_$id.pdf", 'I');
    }
}
