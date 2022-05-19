<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

    /**
     * Controlador de Reportes
     * Autor: Leandro
     * Creado: 10/05/2019
     * Modificado: 14/01/2021 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('Areas_model');
        $this->grupos_permitidos = array('admin', 'toner_admin', 'toner_consulta_general');
        $this->grupos_solo_consulta = array('toner_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de reportes';
        $data['title'] = TITLE . ' - Reportes';
        $this->load_template('toner/reportes/reportes_listar', $data);
    }

    public function consumo()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));
        $this->array_estado_control = $array_estado = array('Todos' => 'Todos', 'Activo' => 'Activo', 'Baja' => 'Baja');

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('toner/Consumibles_model');
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
            $hasta->add(new DateInterval('P1D'));
            $desde_sql = $desde->format('Y/m/d');
            $hasta_sql = $hasta->format('Y/m/d');
            $area_where = '';
            if ($this->input->post('area') != 'Todas')
            {
                $area_where = " AND area_id= " . $this->input->post('area');
            }
            $options['select'] = array(
                'modelo AS Modelo',
                'descripcion AS Descripcion',
                'tipo AS Tipo',
                'stock_llenos AS StockActual',
                "(SELECT COALESCE(SUM(cantidad_llenos),0)" .
                " FROM gt_movimientos M" .
                " JOIN gt_movimientos_detalles MD" .
                " ON (M.id=MD.movimiento_id)" .
                " WHERE MD.consumible_id=gt_consumibles.id" .
                " AND estado = 'Activo' AND M.fecha_movimiento BETWEEN '$desde_sql' AND '$hasta_sql') AS Ingresos",
                "(SELECT COUNT(1)" .
                " FROM gt_pedidos_consumibles P" .
                " JOIN gt_pedidos_consumibles_detalles PD" .
                " ON (P.id=PD.pedido_consumibles_id)" .
                " WHERE PD.consumible_id=gt_consumibles.id" .
                $area_where .
                " AND estado != 'Anulado' AND PD.fecha_entrega BETWEEN '$desde_sql' AND '$hasta_sql') AS Egresos"
            );
            if ($this->input->post('estado') != 'Todos')
            {
                $options['estado'] = $this->input->post('estado');
            }
            $options['sort_by'] = 'modelo';
            $options['return_array'] = TRUE;

            $print_data = $this->Consumibles_model->get($options);
            if (!empty($print_data))
            {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Consumo")
                        ->setDescription("Informe de Consumo (Módulo Toner)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Consumo");
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(57);
                $sheet->getColumnDimension('C')->setWidth(9);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(10);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getStyle('A1:F1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Modelo', 'Descripcion', 'Tipo', 'Stock Actual', 'Ingresos', 'Egresos')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');
                $nombreArchivo = 'InformeConsumo_' . date('Ymd');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['estado']['array'] = $array_estado;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Consumo';
        $data['title'] = TITLE . ' - Informe de Consumo';
        $this->load_template('toner/reportes/reportes_content', $data);
    }

    public function consumo_area()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));
        $this->array_estado_control = $array_estado = array('Todos' => 'Todos', 'Activo' => 'Activo', 'Baja' => 'Baja');

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('toner/Pedidos_consumibles_detalles_model');
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
            $hasta->add(new DateInterval('P1D'));
            $desde_sql = $desde->format('Y/m/d');
            $hasta_sql = $hasta->format('Y/m/d');
            if ($this->input->post('area') != 'Todas')
            {
                $options['where'][] = 'area_id = ' . $this->input->post('area');
            }

            $options['select'] = array(
                'modelo AS Modelo',
                'descripcion AS Descripcion',
                'tipo AS Tipo',
                'stock_llenos AS Stock',
                'areas.nombre AS Area',
                'COUNT(gt_pedidos_consumibles_detalles.id) AS Egresos');
            $options['join'][] = array('table' => 'gt_pedidos_consumibles', 'where' => 'gt_pedidos_consumibles.id=gt_pedidos_consumibles_detalles.pedido_consumibles_id');
            $options['join'][] = array('table' => 'gt_consumibles', 'where' => 'gt_pedidos_consumibles_detalles.consumible_id=gt_consumibles.id');
            $options['join'][] = array('table' => 'areas', 'where' => 'gt_pedidos_consumibles.area_id=areas.id');
            $options['where'][] = "gt_pedidos_consumibles.estado != 'Anulado'";
            $options['where'][] = 'gt_pedidos_consumibles_detalles.fecha_entrega IS NOT NULL';
            $options['where'][] = "gt_pedidos_consumibles_detalles.fecha_entrega BETWEEN '$desde_sql' AND '$hasta_sql'";
            if ($this->input->post('estado') != 'Todos')
            {
                $options['where'][] = "gt_consumibles.estado = '" . $this->input->post('estado') . "'";
            }
            $options['group_by'] = 'modelo, descripcion, tipo, stock_llenos, areas.nombre';
            $options['sort_by'] = 'modelo, areas.nombre';
            $options['return_array'] = TRUE;
            $print_data = $this->Pedidos_consumibles_detalles_model->get($options);
            if (!empty($print_data))
            {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Consumo por Área")
                        ->setDescription("Informe de Consumo por Área (Módulo Toner)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(50);
                $sheet->getColumnDimension('C')->setWidth(9);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(50);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getStyle('A1:F1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Modelo', 'Descripcion', 'Tipo', 'Stock', 'Área', 'Egresos')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');
                $nombreArchivo = 'InformeConsumoArea_' . date('Ymd');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $fake_model->fields['estado']['array'] = $array_estado;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Consumo por Área';
        $data['title'] = TITLE . ' - Informe de Consumo por Área';
        $this->load_template('toner/reportes/reportes_content', $data);
    }

    public function impresoras()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'type' => 'date', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('toner/Impresoras_model');
            if ($this->input->post('area') != 'Todas')
            {
                $options['where'][] = 'area_id = ' . $this->input->post('area');
            }
            $options['select'] = array(
                'areas.nombre as Área',
                "gt_marcas.nombre as Marca",
                "gt_impresoras.modelo as Impresora");
            $options['join'][] = array('table' => 'gt_impresoras_areas', 'where' => 'gt_impresoras_areas.impresora_id=gt_impresoras.id');
            $options['join'][] = array('table' => 'gt_marcas', 'where' => 'gt_marcas.id=gt_impresoras.marca_id');
            $options['join'][] = array('table' => 'areas', 'where' => 'gt_impresoras_areas.area_id=areas.id');
            $options['sort_by'] = 'areas.nombre, gt_marcas.nombre, gt_impresoras.modelo';
            $options['return_array'] = TRUE;
            $print_data = $this->Impresoras_model->get($options);

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getProperties()
                    ->setCreator("SistemaMLC")
                    ->setLastModifiedBy("SistemaMLC")
                    ->setTitle("Informe de Impresoras por Área")
                    ->setDescription("Informe de Impresoras por Área (Módulo Toner)");
            $spreadsheet->setActiveSheetIndex(0);

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getColumnDimension('A')->setWidth(40);
            $sheet->getColumnDimension('B')->setWidth(10);
            $sheet->getColumnDimension('C')->setWidth(40);
            $sheet->getStyle('A1:C1')->getFont()->setBold(TRUE);
            $sheet->fromArray(array(array('Área', 'Marca', 'Impresora')), NULL, 'A1');
            $sheet->fromArray($print_data, NULL, 'A2');
            $nombreArchivo = 'InformeImpresorasArea_' . date('Ymd');

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
            header("Cache-Control: max-age=0");

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Impresoras por Área';
        $data['title'] = TITLE . ' - Informe de Impresoras por Área';
        $this->load_template('toner/reportes/reportes_content', $data);
    }

    public function historico_consumible()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'consumible' => array('label' => 'Consumible', 'type' => 'date', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->load->model('toner/Consumibles_model');
        $this->array_consumible_control = $array_consumible = $this->get_array('Consumibles', 'consumible', 'id', array('select' => array("gt_consumibles.id, CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible")));

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('toner/Pedidos_consumibles_detalles_model');

            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
            $hasta->add(new DateInterval('P1D'));
            $desde_sql = $desde->format('Y/m/d');
            $hasta_sql = $hasta->format('Y/m/d');
            $consumible_id = $this->input->post('consumible');
            $consumible = $this->Consumibles_model->get(array('id' => $consumible_id));

            $query_saldo = "SELECT ID, SUM(Movimiento) as Movimiento, SUM(Pedido) as Pedido, SUM(Movimiento-Pedido) as Saldo
FROM (
SELECT 1 as ID, 0 as Movimiento, COUNT(gt_pedidos_consumibles_detalles.id) as Pedido
FROM gt_pedidos_consumibles_detalles
JOIN gt_pedidos_consumibles ON gt_pedidos_consumibles.id = gt_pedidos_consumibles_detalles.pedido_consumibles_id
WHERE consumible_id = ?
AND gt_pedidos_consumibles.estado != 'Anulado'
AND gt_pedidos_consumibles_detalles.fecha_entrega IS NOT NULL
AND gt_pedidos_consumibles_detalles.fecha_entrega < ?
UNION
SELECT 1 as ID, SUM(cantidad_llenos) as Movimiento, 0 as Pedido
FROM gt_movimientos_detalles
JOIN gt_movimientos ON gt_movimientos.id = gt_movimientos_detalles.movimiento_id
WHERE consumible_id = ?
AND gt_movimientos.estado = 'Activo'
AND gt_movimientos.fecha_movimiento IS NOT NULL
AND gt_movimientos.fecha_movimiento < ?
) a
GROUP BY ID";
            $saldo_anterior = $this->db->query($query_saldo, array($consumible_id, $desde_sql, $consumible_id, $desde_sql))->row();

            $query = "SELECT Entrega, Area, Movimiento, Pedido FROM
(SELECT CAST(fecha_entrega AS DATE) AS Entrega, areas.nombre AS Area, 0 as Movimiento, COUNT(gt_pedidos_consumibles_detalles.id) as Pedido
FROM gt_pedidos_consumibles_detalles
JOIN gt_pedidos_consumibles ON gt_pedidos_consumibles.id = gt_pedidos_consumibles_detalles.pedido_consumibles_id
JOIN gt_consumibles ON gt_pedidos_consumibles_detalles.consumible_id = gt_consumibles.id
JOIN areas ON gt_pedidos_consumibles.area_id=areas.id
WHERE consumible_id = ?
AND gt_pedidos_consumibles.estado != 'Anulado'
AND gt_pedidos_consumibles_detalles.fecha_entrega IS NOT NULL
AND gt_pedidos_consumibles_detalles.fecha_entrega BETWEEN ? AND ?
GROUP BY CAST(fecha_entrega AS DATE), areas.nombre
) a
UNION
(SELECT CAST(fecha_movimiento AS DATE) AS Entrega, observaciones AS Area, cantidad_llenos as Movimiento, 0 as Pedido
FROM gt_movimientos_detalles
JOIN gt_movimientos ON gt_movimientos.id = gt_movimientos_detalles.movimiento_id
WHERE consumible_id = ?
AND gt_movimientos.estado = 'Activo'
AND gt_movimientos.fecha_movimiento IS NOT NULL
AND gt_movimientos.fecha_movimiento BETWEEN ? AND ?
)
ORDER BY Entrega asc, Movimiento desc";
            $print_data = $this->db->query($query, array($consumible_id, $desde_sql, $hasta_sql, $consumible_id, $desde_sql, $hasta_sql))->result_array();

            if (!empty($print_data) && !empty($consumible))
            {
                $saldo_inicial = $saldo_anterior->Saldo;
                foreach ($print_data as $key => $Detalle)
                {
                    $print_data[$key]['Entrega'] = date_format(new DateTime($Detalle['Entrega']), 'd/m/Y');
                    $saldo_inicial += $Detalle['Movimiento'] - $Detalle['Pedido'];
                    $print_data[$key]['Saldo'] = $saldo_inicial;
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Histórico consumible")
                        ->setDescription("Histórico consumible (Módulo Toner)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(50);
                $sheet->getColumnDimension('C')->setWidth(12);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getStyle('E:E')->getFont()->setBold(TRUE);
                $sheet->getStyle('A2:A5')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Histórico Consumible')), NULL, 'A1');
                $sheet->getStyle('A1')->getFont()->setSize(16);
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('B2:E2');
                $sheet->mergeCells('B3:E3');
                $sheet->mergeCells('B4:E4');
                $sheet->mergeCells('B5:E5');
                $sheet->mergeCells('B6:E6');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->fromArray(array(array('Modelo', $consumible->modelo)), NULL, 'A2');
                $sheet->fromArray(array(array('Descripción', $consumible->descripcion)), NULL, 'A3');
                $sheet->fromArray(array(array('Tipo', $consumible->tipo)), NULL, 'A4');
                $sheet->fromArray(array(array('Stock Actual', $consumible->stock_llenos)), NULL, 'A5', TRUE);
                $sheet->getStyle('B5')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('A7:E8')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Fecha Entrega', 'Detalle', 'Movimiento', 'Pedido', 'Saldo')), NULL, 'A7', TRUE);
                $sheet->fromArray(array(array('', 'SALDO ANTERIOR AL ' . date_format($desde, 'd/m/Y'), $saldo_anterior->Movimiento, $saldo_anterior->Pedido, $saldo_anterior->Saldo)), NULL, 'A8', TRUE);
                $sheet->fromArray($print_data, NULL, 'A9', TRUE);
                $sheet->getStyle('A1')->getFont()->setBold(TRUE);
                $nombreArchivo = 'HistoricoConsumible_' . date('Ymd');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['consumible']['array'] = $array_consumible;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Histórico Consumible';
        $data['title'] = TITLE . ' - Histórico Consumible';
        $this->load_template('toner/reportes/reportes_content', $data);
    }

    public function historico_consumo()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'consumible' => array('label' => 'Consumible', 'type' => 'date', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->load->model('toner/Consumibles_model');
        $this->array_consumible_control = $array_consumible = $this->get_array('Consumibles', 'consumible', 'id', array('select' => array("gt_consumibles.id, CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible")));

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
            $hasta->add(new DateInterval('P1D'));
            $desde_sql = $desde->format('Y/m/d');
            $hasta_sql = $hasta->format('Y/m/d');
            $consumible_id = $this->input->post('consumible');
            $consumibles = $this->Consumibles_model->get(array('id IN' => $consumible_id, 'sort_by' => 'id ASC'));
            if (!empty($consumibles))
            {
                $consumibles_array = [];
                foreach ($consumibles as $Consumible)
                {
                    $consumibles_array[$Consumible->id] = $Consumible;
                }

                $query = "SELECT consumible_id as Consumible, CAST(fecha_entrega AS DATE) AS Entrega, areas.nombre AS Area, COUNT(gt_pedidos_consumibles_detalles.id) as Pedido
                        FROM gt_pedidos_consumibles_detalles
                        JOIN gt_pedidos_consumibles ON gt_pedidos_consumibles.id = gt_pedidos_consumibles_detalles.pedido_consumibles_id
                        JOIN gt_consumibles ON gt_pedidos_consumibles_detalles.consumible_id = gt_consumibles.id
                        JOIN areas ON gt_pedidos_consumibles.area_id=areas.id
                        WHERE consumible_id IN ?
                        AND gt_pedidos_consumibles.estado != 'Anulado'
                        AND gt_pedidos_consumibles_detalles.fecha_entrega IS NOT NULL
                        AND gt_pedidos_consumibles_detalles.fecha_entrega BETWEEN ? AND ?
                        GROUP BY consumible_id, CAST(fecha_entrega AS DATE), areas.nombre 
                        ORDER BY consumible_id ASC, Entrega ASC";
                $data = $this->db->query($query, array($consumible_id, $desde_sql, $hasta_sql))->result_array();

                $num_row = 2;
                if (!empty($data))
                {
                    $array_merge = array();
                    $array_bold = array();
                    $array_subtotal = array();
                    $array_total = array();
                    $total_consumible = 0;
                    $total_general = 0;
                    $last_consumible = NULL;
                    foreach ($data as $key => $value)
                    {
                        //TRANSFORMA FECHAS
                        $data[$key]['Entrega'] = date_format(new DateTime($value['Entrega']), 'd/m/Y');

                        //CAMBIA DE CONSUMIBLE
                        if ($last_consumible !== $value['Consumible'])
                        {
                            //TOTAL PARA CONSUMIBLE ANTERIOR
                            if (!empty($last_consumible))
                            {
                                $print_data[] = array('', 'TOTAL', $total_consumible);
                                $array_subtotal[] = "A$num_row:C$num_row";
                                $array_bold[] = "A$num_row:C$num_row";
                                $num_row++;
                                $total_consumible = 0;
                                $print_data[] = array('', '', '');
                                $num_row++;
                            }

                            //INICIALIZA CONSUMIBLE ACTUAL
                            $print_data[] = array('', $consumibles_array[$value['Consumible']]->modelo);
                            $array_subtotal[] = "A$num_row:C$num_row";
                            $array_bold[] = "A$num_row:C$num_row";
                            $array_merge[] = "B$num_row:C$num_row";
                            $num_row++;
                            $print_data[] = array('Entrega', 'Detalle', 'Cantidad');
                            $array_bold[] = "A$num_row:C$num_row";
                            $num_row++;
                            $last_consumible = $value['Consumible'];
                        }

                        $print_data[] = array($data[$key]['Entrega'], $data[$key]['Area'], $data[$key]['Pedido']);
                        $num_row++;
                        $total_consumible += $value['Pedido'];
                        $total_general += $value['Pedido'];
                    }

                    //TOTALES ULTIMA FILA
                    if (!empty($last_consumible))
                    {
                        $print_data[] = array('', 'Total ' . $consumibles_array[$value['Consumible']]->modelo, $total_consumible);
                        $array_subtotal[] = "A$num_row:C$num_row";
                        $array_bold[] = "A$num_row:C$num_row";
                        $num_row++;
                        $total_consumible = 0;
                    }
                    $print_data[] = array('', 'Total GENERAL', $total_general);
                    $array_total[] = "A$num_row:C$num_row";
                    $array_bold[] = "A$num_row:C$num_row";
                    $num_row++;
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Histórico consumo")
                        ->setDescription("Histórico consumo (Módulo Toner)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(46);
                $sheet->getColumnDimension('C')->setWidth(12);

                $sheet->getStyle('C:C')->getFont()->setBold(TRUE);
                $sheet->getStyle('A2:B2')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Histórico Consumo')), NULL, 'A1');
                $sheet->getStyle('A1')->getFont()->setSize(16);
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setBold(TRUE);

                //DATOS
                $sheet->fromArray($print_data, NULL, 'A2');

                //APLICANDO MERGE
                foreach ($array_merge as $Merge)
                {
                    $sheet->mergeCells($Merge);
                }

                //APLICANDO BOLD
                foreach ($array_bold as $Bold)
                {
                    $sheet->getStyle($Bold)->getFont()->setBold(TRUE);
                }

                //APLICANDO COLOR
                foreach ($array_subtotal as $Subtotal)
                {
                    $sheet->getStyle($Subtotal)->applyFromArray(
                            array(
                                'fill' => array(
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'D9D9D9')
                                )
                            )
                    );
                }
                foreach ($array_total as $Total)
                {
                    $sheet->getStyle($Total)->applyFromArray(
                            array(
                                'fill' => array(
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'BFBFBF')
                                )
                            )
                    );
                }

                $nombreArchivo = 'HistoricoConsumo_' . date('Ymd');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['consumible']['array'] = $array_consumible;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Histórico Consumo';
        $data['title'] = TITLE . ' - Histórico Consumo';
        $this->load_template('toner/reportes/reportes_content', $data);
    }

    public function pedidos()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'type' => 'date', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('toner/Pedidos_consumibles_detalles_model');
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
            $hasta->add(new DateInterval('P1D'));
            $desde_sql = $desde->format('Y/m/d');
            $hasta_sql = $hasta->format('Y/m/d');
            if ($this->input->post('area') != 'Todas')
            {
                $options['where'][] = 'area_id = ' . $this->input->post('area');
            }

            $options['select'] = array(
                'areas.nombre AS Area',
                'descripcion AS Descripcion',
                'fecha_solicitud AS Fecha',
                'fecha_entrega AS Entrega',
                '1 as Total');
            $options['join'][] = array('table' => 'gt_pedidos_consumibles',
                'where' => 'gt_pedidos_consumibles.id=gt_pedidos_consumibles_detalles.pedido_consumibles_id');
            $options['join'][] = array('table' => 'gt_consumibles',
                'where' => 'gt_pedidos_consumibles_detalles.consumible_id=gt_consumibles.id');
            $options['join'][] = array('table' => 'areas', 'where' => 'gt_pedidos_consumibles.area_id=areas.id');
            $options['where'][] = "gt_pedidos_consumibles.estado != 'Anulado'";
            $options['where'][] = "gt_pedidos_consumibles.fecha_solicitud BETWEEN '$desde_sql' AND '$hasta_sql'";
            $options['sort_by'] = 'areas.nombre, descripcion, fecha_solicitud';
            $options['return_array'] = TRUE;
            $data = $this->Pedidos_consumibles_detalles_model->get($options);
            $print_data = array();
            $num_row = 1;
            if (!empty($data))
            {
                $array_merge = array();
                $array_subtotal = array();
                $array_total = array();
                $area_anterior = '';
                $total_area = 0;
                $num_row_area = 1;
                $toner_anterior = '';
                $total_toner = 0;
                $num_row_toner = 1;
                $total_general = 0;
                foreach ($data as $key => $value)
                {
                    //TRANSFORMA FECHAS
                    $data[$key]['Fecha'] = !empty($value['Fecha']) ? date_format(new DateTime($value['Fecha']), 'd-m-Y') : '';
                    $data[$key]['Entrega'] = !empty($value['Entrega']) ? date_format(new DateTime($value['Entrega']), 'd-m-Y') : '';

                    //CAMBIA DE TONER (O DE AREA)
                    if ($toner_anterior !== $value['Descripcion'] || $area_anterior !== $value['Area'])
                    {
                        //MERGE CELDAS DE TONER ANTERIOR
                        if ($num_row_toner < $num_row)
                        {
                            $array_merge[] = "B$num_row_toner:B$num_row";
                        }
                        //TOTAL PARA TONER ANTERIOR
                        if (!empty($toner_anterior))
                        {
                            $print_data[] = array('Area' => '', 'Descripcion' => 'Total ' . $toner_anterior, 'Fecha' => '', 'Entrega' => '', 'Total' => $total_toner);
                            $total_toner = 0;
                            $num_row++;
                            $array_merge[] = "B$num_row:D$num_row";
                            $array_subtotal[] = "B$num_row:E$num_row";
                        }
                        //INICIALIZA TONER ACTUAL
                        $toner_anterior = $value['Descripcion'];
                        $num_row_toner = $num_row + 1;
                    }

                    //CAMBIA DE AREA
                    if ($area_anterior !== $value['Area'])
                    {
                        //MERGE CELDAS DE AREA ANTERIOR
                        if ($num_row_area < $num_row)
                        {
                            $array_merge[] = "A$num_row_area:A$num_row";
                        }
                        //TOTAL PARA AREA ANTERIOR
                        if (!empty($area_anterior))
                        {
                            $print_data[] = array('Area' => 'Total ' . $area_anterior, 'Descripcion' => '', 'Fecha' => '', 'Entrega' => '', 'Total' => $total_area);
                            $total_area = 0;
                            $num_row++;
                            $num_row_toner++;
                            $array_merge[] = "A$num_row:D$num_row";
                            $array_total[] = "A$num_row:E$num_row";
                        }
                        //INICIALIZA AREA ACTUAL
                        $area_anterior = $value['Area'];
                        $num_row_area = $num_row + 1;
                    }
                    $print_data[] = $data[$key];
                    $num_row++;
                    $total_toner++;
                    $total_area++;
                    $total_general++;
                }

                //TOTALES ULTIMA FILA
                if ($num_row_toner < $num_row)
                {
                    $array_merge[] = "B$num_row_toner:B$num_row";
                }
                if (!empty($toner_anterior))
                {
                    $print_data[] = array('Area' => '', 'Descripcion' => 'Total ' . $toner_anterior, 'Fecha' => '', 'Entrega' => '', 'Total' => $total_toner);
                    $total_toner = 0;
                    $num_row++;
                    $array_merge[] = "B$num_row:D$num_row";
                    $array_subtotal[] = "B$num_row:E$num_row";
                }
                if ($num_row_area < $num_row)
                {
                    $array_merge[] = "A$num_row_area:A$num_row";
                }
                if (!empty($area_anterior))
                {
                    $print_data[] = array('Area' => 'Total ' . $area_anterior, 'Descripcion' => '', 'Fecha' => '', 'Entrega' => '', 'Total' => $total_area);
                    $total_area = 0;
                    $num_row++;
                    $num_row_toner++;
                    $array_merge[] = "A$num_row:D$num_row";
                    $array_total[] = "A$num_row:E$num_row";
                }
                $print_data[] = array('Area' => 'Total GENERAL', 'Descripcion' => '', 'Fecha' => '', 'Entrega' => '', 'Total' => $total_general);
                $num_row++;
                $array_merge[] = "A$num_row:D$num_row";

                //INICIANDO EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Pedidos")
                        ->setDescription("Informe de Pedidos (Módulo Toner)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getColumnDimension('A')->setWidth(46);
                $sheet->getColumnDimension('B')->setWidth(52);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(18);
                $sheet->getColumnDimension('E')->setWidth(10);
                $sheet->getStyle('A1:E1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Área', 'Descripción', 'Fecha Solicitud', 'Fecha Entrega', 'Total')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');

                //APLICANDO MERGE
                $sheet->getStyle("A1:B$num_row")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
                foreach ($array_merge as $Merge)
                {
                    $sheet->mergeCells($Merge);
                }

                //APLICANDO BORDE
                $border_all = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle("A1:E$num_row")->applyFromArray($border_all);

                //APLICANDO COLOR
                foreach ($array_subtotal as $Subtotal)
                {
                    $sheet->getStyle($Subtotal)->applyFromArray(
                            array(
                                'fill' => array(
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'F2F2F2')
                                )
                            )
                    );
                }
                foreach ($array_total as $Total)
                {
                    $sheet->getStyle($Total)->applyFromArray(
                            array(
                                'fill' => array(
                                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                    'color' => array('rgb' => 'D9D9D9')
                                )
                            )
                    );
                }
                $sheet->getStyle("A$num_row:E$num_row")->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'BFBFBF')
                            )
                        )
                );

                $nombreArchivo = 'InformePedidos_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Pedidos';
        $data['title'] = TITLE . ' - Informe de Pedidos';
        $this->load_template('toner/reportes/reportes_content', $data);
    }
}
