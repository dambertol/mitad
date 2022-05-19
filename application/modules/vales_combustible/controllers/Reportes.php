<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

    /**
     * Controlador de Reportes
     * Autor: Leandro
     * Creado: 15/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_consulta_general');
        $this->grupos_contaduria = array('admin', 'vales_combustible_contaduria', 'vales_combustible_consulta_general');
        $this->grupos_hacienda = array('vales_combustible_hacienda');
        $this->grupos_solo_consulta = array('vales_combustible_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_contaduria, $this->grupos))
        {
            $data['contaduria'] = TRUE;
        }

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de reportes';
        $data['title'] = TITLE . ' - Reportes';
        $this->load_template('vales_combustible/reportes/reportes_listar', $data);
    }

    public function facturas()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Facturas_model');
        $this->array_factura_control = $array_factura = $this->get_array('Facturas', 'factura');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'factura' => array('label' => 'Factura', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $options['select'] = array(
                'vc_facturas.factura',
                'vc_facturas.fecha as fecha_factura',
                'vc_facturas.total_litros',
                'vc_facturas.total_costo',
                'vc_remitos.remito',
                'vc_remitos.fecha as fecha_remito',
                'TCR.nombre as tipo_combustible',
                '(vc_remitos.litros / CASE WHEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) <> 0 THEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) ELSE 1 END) as litros',
                '(vc_remitos.costo / CASE WHEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) <> 0 THEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) ELSE 1 END) as costo',
                'vc_remitos.persona_id',
                "CASE 
					 WHEN vc_remitos.persona_id IS NOT NULL THEN CONCAT(personal.Apellido, ', ', personal.Nombre)
					 WHEN vc_remitos.persona_id IS NULL THEN vc_remitos.persona_nombre
					 END AS 'persona_nombre'",
                'vc_remitos.patente_maquinaria',
                'vc_remitos.observaciones as obs_remito',
                "CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as vale",
                'vc_vales.fecha as fecha_vale',
                'vc_vales.vencimiento as vencimiento_vale',
                'TCV.nombre as v_tipo_combustible',
                'vc_vales.metros_cubicos',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'vc_vales.observaciones as obs_vales'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_remitos', 'where' => 'vc_facturas.id = vc_remitos.factura_id'),
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCR', 'where' => 'TCR.id = vc_remitos.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'vc_vales', 'where' => 'vc_remitos.id = vc_vales.remito_id'),
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCV', 'where' => 'TCV.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'personal', 'where' => 'personal.Legajo = vc_remitos.persona_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id')
            );
            if ($this->input->post('factura') !== 'Todas')
            {
                $where['column'] = 'vc_facturas.id';
                $where['value'] = $this->input->post('factura');
                $options['where'] = array($where);
            }
            $options['sort_by'] = 'vc_facturas.factura, vc_remitos.remito';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Facturas_model->get($options);

            if (!empty($print_data))
            {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Facturas")
                        ->setDescription("Informe de Facturas (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Facturas");

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A2:T2')->applyFromArray($BStyle1);

                $nro_factura = '';
                $value_factura = '';
                $nro_remito = '';
                $value_remito = '';
                $linea = 0;
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha_factura'] = !empty($value['fecha_factura']) ? date_format(new DateTime($value['fecha_factura']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_remito'] = !empty($value['fecha_remito']) ? date_format(new DateTime($value['fecha_remito']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_vale'] = !empty($value['fecha_vale']) ? date_format(new DateTime($value['fecha_vale']), 'd-m-Y') : '';
                    $print_data[$key]['vencimiento_vale'] = !empty($value['vencimiento_vale']) ? date_format(new DateTime($value['vencimiento_vale']), 'd-m-Y') : '';

                    $value_factura = $value['factura'];
                    $value_remito = $value['remito'];

                    if ($nro_remito !== '')
                    {
                        $BStyle1 = array(
                            'borders' => array(
                                'bottom' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                                )
                            )
                        );
                        $sheet->getStyle('E' . ($linea + 2) . ':T' . ($linea + 2))->applyFromArray($BStyle1);
                    }

                    if ($nro_factura === $value['factura'])
                    {
                        $print_data[$key]['factura'] = NULL;
                        $print_data[$key]['fecha_factura'] = NULL;
                        $print_data[$key]['total_litros'] = NULL;
                        $print_data[$key]['total_costo'] = NULL;
                    }
                    elseif ($nro_factura !== '')
                    {
                        $BStyle1 = array(
                            'borders' => array(
                                'bottom' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                                )
                            )
                        );
                        $sheet->getStyle('A' . ($linea + 2) . ':T' . ($linea + 2))->applyFromArray($BStyle1);
                    }

                    $nro_factura = $value_factura;
                    $nro_remito = $value_remito;
                    $linea++;
                }

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A' . ($linea + 2) . ':T' . ($linea + 2))->applyFromArray($BStyle1);

                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(14);
                $sheet->getColumnDimension('K')->setWidth(30);
                $sheet->getColumnDimension('L')->setWidth(14);
                $sheet->getColumnDimension('M')->setWidth(25);
                $sheet->getColumnDimension('N')->setWidth(14);
                $sheet->getColumnDimension('O')->setWidth(14);
                $sheet->getColumnDimension('P')->setWidth(14);
                $sheet->getColumnDimension('Q')->setWidth(14);
                $sheet->getColumnDimension('R')->setWidth(14);
                $sheet->getColumnDimension('S')->setWidth(45);
                $sheet->getColumnDimension('T')->setWidth(25);
                $sheet->getStyle('A1:T2')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Datos de Factura', '', '', '', 'Datos de Remito', '', '', '', '', '', '', '', '', 'Datos de Vale')), NULL, 'A1');
                $sheet->fromArray(array(array('Factura', 'Fecha', 'Total M³/Litros', 'Total Costo', 'Remito', 'Fecha', 'Tipo', 'M³/Litros', 'Costo', 'DNI', 'Nombre', 'Patente', 'Observaciones', 'Vale', 'Fecha', 'Vencimiento', 'Tipo', 'M³/Litros', 'Área', 'Justificación')), NULL, 'A2');
                $sheet->fromArray($print_data, NULL, 'A3');

                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('E1:M1');
                $sheet->getStyle('E1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('N1:T1');
                $sheet->getStyle('N1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));

                $sheet->getStyle('D3:D' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");
                $sheet->getStyle('I3:I' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('E1:E' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('N1:N' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('U1:U' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A1:T1')->applyFromArray($BStyle1);

                $sheet->setAutoFilter('A2:T2');

                $nombreArchivo = 'InformeFacturas_' . date('Ymd');
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

        $fake_model->fields['factura']['array'] = $array_factura;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Facturas';
        $data['title'] = TITLE . ' - Informe de Facturas';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function areas()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('Areas_model');
        $this->load->model('vales_combustible/Vales_model');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Fecha Vale Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Fecha Vale Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

            $options['select'] = array(
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                "CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as vale",
                'vc_vales.fecha as fecha_vale',
                'vc_vales.vencimiento as vencimiento_vale',
                'TCV.nombre as v_tipo_combustible',
                'vc_vales.metros_cubicos',
                'vc_vales.estado',
                'vc_remitos.remito',
                'vc_remitos.fecha as fecha_remito',
                'TCR.nombre as tipo_combustible',
                'vc_remitos.litros',
                'vc_remitos.costo',
                'vc_facturas.factura',
                'vc_facturas.fecha as fecha_factura',
                'vc_facturas.total_litros',
                'vc_facturas.total_costo'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCV', 'where' => 'TCV.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id'),
                array('type' => 'left', 'table' => 'vc_remitos', 'where' => 'vc_remitos.id = vc_vales.remito_id'),
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCR', 'where' => 'TCR.id = vc_remitos.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'vc_facturas', 'where' => 'vc_facturas.id = vc_remitos.factura_id')
            );
            if ($this->input->post('area') !== 'Todas')
            {
                $where['column'] = 'vc_vales.area_id';
                $where['value'] = $this->input->post('area');
                $options['where'] = array($where);
            }

            $where['column'] = "vc_vales.estado NOT IN ('Anulado', 'Pendiente')";
            $where['value'] = '';
            $where['override'] = TRUE;
            $options['where'][] = $where;

            $options['fecha >='] = $desde->format('Y-m-d');
            $hasta->add(new DateInterval('P1D'));
            $options['fecha <'] = $hasta->format('Y-m-d');

            $options['sort_by'] = 'areas.codigo, vc_vales.id';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Vales_model->get($options);

            if (!empty($print_data))
            {
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha_factura'] = !empty($value['fecha_factura']) ? date_format(new DateTime($value['fecha_factura']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_remito'] = !empty($value['fecha_remito']) ? date_format(new DateTime($value['fecha_remito']), 'd-m-Y') : '';
                    $print_data[$key]['vencimiento_vale'] = !empty($value['vencimiento_vale']) ? date_format(new DateTime($value['vencimiento_vale']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_vale'] = !empty($value['fecha_vale']) ? date_format(new DateTime($value['fecha_vale']), 'd-m-Y') : '';
                }
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Áreas")
                        ->setDescription("Informe de Áreas (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Áreas");
                $sheet->getColumnDimension('A')->setWidth(45);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(14);
                $sheet->getColumnDimension('K')->setWidth(14);
                $sheet->getColumnDimension('L')->setWidth(14);
                $sheet->getColumnDimension('M')->setWidth(14);
                $sheet->getColumnDimension('N')->setWidth(14);
                $sheet->getColumnDimension('O')->setWidth(14);
                $sheet->getColumnDimension('P')->setWidth(14);
                $sheet->getStyle('A1:P2')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Área', 'Datos de Vale', '', '', '', '', '', 'Datos de Remito', '', '', '', '', 'Datos de Factura')), NULL, 'A1');
                $sheet->fromArray(array(array('Nombre', 'Vale', 'Fecha', 'Vencimiento', 'Tipo', 'M³/Litros', 'Estado', 'Remito', 'Fecha', 'Tipo', 'M³/Litros', 'Costo', 'Factura', 'Fecha', 'Total M³/Litros', 'Total Costo')), NULL, 'A2');
                $sheet->fromArray($print_data, NULL, 'A3');

                $sheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('B1:G1');
                $sheet->getStyle('B1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('H1:L1');
                $sheet->getStyle('H1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('M1:P1');
                $sheet->getStyle('M1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));

                $sheet->getStyle('L3:L' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");
                $sheet->getStyle('P3:P' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('B1:B' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('H1:H' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('M1:M' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('Q1:Q' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);

                $BStyle2 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A1:P1')->applyFromArray($BStyle2);
                $sheet->getStyle('A' . (sizeof($print_data) + 2) . ':P' . (sizeof($print_data) + 2))->applyFromArray($BStyle2);

                $sheet->setAutoFilter('A2:P2');

                $nombreArchivo = 'InformeAreas_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos para el periodo seleccionado';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Consumo por Área';
        $data['title'] = TITLE . ' - Informe de Consumo por Área';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function ordenes_compra()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Ordenes_compra_model');

        $print_data = $this->Ordenes_compra_model->get(array(
            'select' => array(
                "CONCAT(vc_ordenes_compra.numero, '/', vc_ordenes_compra.ejercicio) as orden_compra",
                'vc_ordenes_compra.fecha',
                'vc_ordenes_compra.total',
                'vc_tipos_combustible.nombre as tipo_combustible',
                'vc_ordenes_compra_detalles.litros',
                'vc_ordenes_compra_detalles.costo_unitario',
                '(vc_ordenes_compra_detalles.litros * vc_ordenes_compra_detalles.costo_unitario) as costo_total',
                '(SELECT SUM(R.litros) FROM vc_facturas F LEFT JOIN vc_remitos R ON R.factura_id = F.id WHERE R.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND F.orden_compra_id = vc_ordenes_compra_detalles.orden_compra_id) as asignado_facturas',
                '0 as restante_facturas',
                '(SELECT SUM(V.metros_cubicos) FROM vc_vales V WHERE V.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND V.orden_compra_id = vc_ordenes_compra_detalles.orden_compra_id) as asignado_vales',
                '0 as restante_vales'
            ),
            'join' => array(
                array(
                    'table' => 'vc_ordenes_compra_detalles',
                    'where' => 'vc_ordenes_compra_detalles.orden_compra_id = vc_ordenes_compra.id',
                    'type' => 'LEFT'),
                array(
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_ordenes_compra_detalles.tipo_combustible_id',
                    'type' => 'LEFT')
            ),
            'sort_by' => 'vc_ordenes_compra_detalles.id',
            'return_array' => TRUE
        ));

        if (!empty($print_data))
        {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getProperties()
                    ->setCreator("SistemaMLC")
                    ->setLastModifiedBy("SistemaMLC")
                    ->setTitle("Informe de Órdenes de Compra")
                    ->setDescription("Informe de Órdenes de Compra (Módulo Vales de Combustible)");
            $spreadsheet->setActiveSheetIndex(0);

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
            $sheet->setTitle("Informe de Órdenes de Compra");

            $BStyle1 = array(
                'borders' => array(
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                    )
                )
            );
            $sheet->getStyle('A1:K1')->applyFromArray($BStyle1);

            $nro_oc = '';
            $value_oc = '';
            $linea = 0;
            foreach ($print_data as $key => $value)
            {
                $print_data[$key]['fecha'] = !empty($value['fecha']) ? date_format(new DateTime($value['fecha']), 'd-m-Y') : '';
                $print_data[$key]['restante_facturas'] = $value['litros'] - $value['asignado_facturas'];
                $print_data[$key]['restante_vales'] = $value['litros'] - $value['asignado_vales'];
                $value_oc = $value['orden_compra'];
                $BStyle2 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );
                $sheet->getStyle('D' . ($linea + 2) . ':K' . ($linea + 2))->applyFromArray($BStyle2);
                if ($nro_oc === $value['orden_compra'])
                {
                    $print_data[$key]['orden_compra'] = NULL;
                    $print_data[$key]['fecha'] = NULL;
                    $print_data[$key]['total'] = NULL;
                }
                elseif ($nro_oc !== '')
                {
                    $sheet->getStyle('A' . ($linea + 2) . ':K' . ($linea + 2))->applyFromArray($BStyle1);
                }
                $nro_oc = $value_oc;
                $linea++;
            }
            $sheet->getStyle('A' . ($linea + 2) . ':K' . ($linea + 2))->applyFromArray($BStyle1);

            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(15);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(15);
            $sheet->getColumnDimension('K')->setWidth(15);

            $sheet->getStyle('A1:K2')->getFont()->setBold(TRUE);
            $sheet->fromArray(array(array('Datos Orden de Compra', '', '', 'Detalles Orden de Compra', '', '', '', '', '')), NULL, 'A1');
            $sheet->fromArray(array(array('Orden Compra', 'Fecha', 'Total', 'Tipo Combustible', 'M³/Litros', 'Costo Unitario', 'Costo Total', 'Asignado Facturas', 'Restante Facturas', 'Asignado Vales', 'Restante Vales')), NULL, 'A2');
            $sheet->fromArray($print_data, NULL, 'A3');

            $sheet->mergeCells('A1:C1');
            $sheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
            $sheet->mergeCells('D1:K1');
            $sheet->getStyle('D1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));

            $sheet->getStyle('C2:C' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");
            $sheet->getStyle('F2:F' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");
            $sheet->getStyle('G2:G' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");

            $BStyle3 = array(
                'borders' => array(
                    'left' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                    )
                )
            );
            $sheet->getStyle('D1:D' . (sizeof($print_data) + 2))->applyFromArray($BStyle3);
            $sheet->getStyle('L1:L' . (sizeof($print_data) + 2))->applyFromArray($BStyle3);

            $sheet->getStyle('A2:K2')->applyFromArray($BStyle1);
            $sheet->setAutoFilter('A2:K2');

            $nombreArchivo = 'InformeOrdenesCompra_' . date('Ymd');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
            header("Cache-Control: max-age=0");

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Sin datos');
            redirect('vales_combustible/reportes/listar', 'refresh');
        }
    }

    public function ordenes_compra_detalle()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Ordenes_compra_model');
        $this->array_orden_compra_control = $array_orden_compra = $this->get_array('Ordenes_compra', 'orden', 'id', array('select' => array('id',
                'CONCAT(vc_ordenes_compra.numero, \'/\', vc_ordenes_compra.ejercicio) as orden'), 'sort_by' => 'numero'), array('Todas' => 'Todas'));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'orden_compra' => array('label' => 'Órdenes de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $options['select'] = array(
                "CONCAT(vc_ordenes_compra.numero, '/', vc_ordenes_compra.ejercicio) as orden",
                'vc_ordenes_compra.fecha as fecha_orden',
                'vc_tipos_combustible.nombre as tipo_combustible',
                'vc_ordenes_compra_detalles.litros as cantidad_litros',
                'vc_ordenes_compra.total',
                'vc_facturas.factura',
                'vc_facturas.fecha as fecha_factura',
                'vc_facturas.total_litros',
                'vc_facturas.total_costo'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_facturas', 'where' => 'vc_facturas.orden_compra_id = vc_ordenes_compra.id'),
                array('type' => 'left', 'table' => 'vc_ordenes_compra_detalles', 'where' => 'vc_ordenes_compra_detalles.orden_compra_id = vc_ordenes_compra.id'),
                array('type' => 'left', 'table' => 'vc_tipos_combustible', 'where' => 'vc_tipos_combustible.id = vc_ordenes_compra_detalles.tipo_combustible_id')
            );
            if ($this->input->post('orden_compra') !== 'Todas')
            {
                $where['column'] = 'vc_ordenes_compra.id';
                $where['value'] = $this->input->post('orden_compra');
                $options['where'] = array($where);
            }
            $options['sort_by'] = 'vc_ordenes_compra.ejercicio, vc_ordenes_compra.numero';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Ordenes_compra_model->get($options);

            if (!empty($print_data))
            {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de OC Detallado")
                        ->setDescription("Informe de Ordenes de Compra Detallado (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de OC Detallado");

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A2:I2')->applyFromArray($BStyle1);

                $nro_orden = '';
                $value_orden = '';
                $nro_factura = '';
                $value_factura = '';
                $linea = 0;
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha_orden'] = !empty($value['fecha_orden']) ? date_format(new DateTime($value['fecha_orden']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_factura'] = !empty($value['fecha_factura']) ? date_format(new DateTime($value['fecha_factura']), 'd-m-Y') : '';

                    $value_orden = $value['orden'];
                    $value_factura = $value['factura'];

                    if ($nro_factura !== '')
                    {
                        $BStyle1 = array(
                            'borders' => array(
                                'bottom' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                                )
                            )
                        );
                        $sheet->getStyle('F' . ($linea + 2) . ':I' . ($linea + 2))->applyFromArray($BStyle1);
                    }

                    if ($nro_orden === $value['orden'])
                    {
                        $print_data[$key]['orden'] = NULL;
                        $print_data[$key]['fecha_orden'] = NULL;
                        $print_data[$key]['tipo_combustible'] = NULL;
                        $print_data[$key]['cantidad_litros'] = NULL;
                        $print_data[$key]['total'] = NULL;
                    }
                    elseif ($nro_orden !== '')
                    {
                        $BStyle1 = array(
                            'borders' => array(
                                'bottom' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                                )
                            )
                        );
                        $sheet->getStyle('A' . ($linea + 2) . ':I' . ($linea + 2))->applyFromArray($BStyle1);
                    }

                    $nro_orden = $value_orden;
                    $nro_factura = $value_factura;
                    $linea++;
                }

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A' . ($linea + 2) . ':I' . ($linea + 2))->applyFromArray($BStyle1);

                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getStyle('A1:I2')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Datos de Orden de Compra', '', '', '', '', 'Datos de Factura', '', '', '')), NULL, 'A1');
                $sheet->fromArray(array(array('Número', 'Fecha', 'Tipo', 'M³/Litros', 'Costo', 'Número', 'Fecha', 'M³/Litros', 'Costo')), NULL, 'A2');
                $sheet->fromArray($print_data, NULL, 'A3');

                $sheet->mergeCells('A1:E1');
                $sheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('F1:I1');
                $sheet->getStyle('F1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));

                $sheet->getStyle('E3:E' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");
                $sheet->getStyle('I3:I' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('F1:F' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('J1:J' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A1:I1')->applyFromArray($BStyle1);

                $sheet->setAutoFilter('A2:I2');

                $nombreArchivo = 'InformeOCDetallado_' . date('Ymd');
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

        $fake_model->fields['orden_compra']['array'] = $array_orden_compra;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Ordenes de Compra Detallado';
        $data['title'] = TITLE . ' - Informe de Ordenes de Compra Detallado';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function tipos_combustible()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Facturas_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Fecha Vale Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Fecha Vale Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

            $options['select'] = array(
                'vc_facturas.factura',
                'vc_facturas.fecha as fecha_factura',
                'vc_facturas.total_litros',
                'vc_facturas.total_costo',
                'vc_remitos.remito',
                'vc_remitos.fecha as fecha_remito',
                'TCR.nombre as tipo_combustible',
                '(vc_remitos.litros / CASE WHEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) <> 0 THEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) ELSE 1 END) as litros',
                '(vc_remitos.costo / CASE WHEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) <> 0 THEN (SELECT COUNT(1) FROM vc_vales VR WHERE VR.remito_id = vc_remitos.id) ELSE 1 END) as costo',
                'vc_remitos.persona_id',
                "CASE 
					 WHEN vc_remitos.persona_id IS NOT NULL THEN CONCAT(personal.Apellido, ', ', personal.Nombre)
					 WHEN vc_remitos.persona_id IS NULL THEN vc_remitos.persona_nombre
					 END AS 'persona_nombre'",
                'vc_remitos.patente_maquinaria',
                'vc_remitos.observaciones as obs_remito',
                "CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as vale",
                'vc_vales.fecha as fecha_vale',
                'vc_vales.vencimiento as vencimiento_vale',
                'TCV.nombre as v_tipo_combustible',
                'vc_vales.metros_cubicos',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'vc_vales.observaciones as obs_vales'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_remitos', 'where' => 'vc_facturas.id = vc_remitos.factura_id'),
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCR', 'where' => 'TCR.id = vc_remitos.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'vc_vales', 'where' => 'vc_remitos.id = vc_vales.remito_id'),
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCV', 'where' => 'TCV.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'personal', 'where' => 'personal.Legajo = vc_remitos.persona_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id')
            );
            if ($this->input->post('tipo_combustible') !== 'Todos')
            {
                $where['column'] = 'vc_vales.tipo_combustible_id';
                $where['value'] = $this->input->post('tipo_combustible');
                $options['where'] = array($where);
            }

            $options['fecha >='] = $desde->format('Y-m-d');
            $hasta->add(new DateInterval('P1D'));
            $options['fecha <'] = $hasta->format('Y-m-d');

            $options['sort_by'] = 'vc_facturas.factura, vc_remitos.remito';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Facturas_model->get($options);

            if (!empty($print_data))
            {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Facturas")
                        ->setDescription("Informe de Facturas (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Facturas");

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A2:T2')->applyFromArray($BStyle1);

                $nro_factura = '';
                $value_factura = '';
                $nro_remito = '';
                $value_remito = '';
                $linea = 0;
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha_factura'] = !empty($value['fecha_factura']) ? date_format(new DateTime($value['fecha_factura']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_remito'] = !empty($value['fecha_remito']) ? date_format(new DateTime($value['fecha_remito']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_vale'] = !empty($value['fecha_vale']) ? date_format(new DateTime($value['fecha_vale']), 'd-m-Y') : '';
                    $print_data[$key]['vencimiento_vale'] = !empty($value['vencimiento_vale']) ? date_format(new DateTime($value['vencimiento_vale']), 'd-m-Y') : '';

                    $value_factura = $value['factura'];
                    $value_remito = $value['remito'];

                    if ($nro_remito !== '')
                    {
                        $BStyle1 = array(
                            'borders' => array(
                                'bottom' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                                )
                            )
                        );
                        $sheet->getStyle('E' . ($linea + 2) . ':T' . ($linea + 2))->applyFromArray($BStyle1);
                    }

                    if ($nro_factura === $value['factura'])
                    {
                        $print_data[$key]['factura'] = NULL;
                        $print_data[$key]['fecha_factura'] = NULL;
                        $print_data[$key]['total_litros'] = NULL;
                        $print_data[$key]['total_costo'] = NULL;
                    }
                    elseif ($nro_factura !== '')
                    {
                        $BStyle1 = array(
                            'borders' => array(
                                'bottom' => array(
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                                )
                            )
                        );
                        $sheet->getStyle('A' . ($linea + 2) . ':T' . ($linea + 2))->applyFromArray($BStyle1);
                    }

                    $nro_factura = $value_factura;
                    $nro_remito = $value_remito;
                    $linea++;
                }

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A' . ($linea + 2) . ':T' . ($linea + 2))->applyFromArray($BStyle1);

                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(14);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(14);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(14);
                $sheet->getColumnDimension('K')->setWidth(30);
                $sheet->getColumnDimension('L')->setWidth(14);
                $sheet->getColumnDimension('M')->setWidth(25);
                $sheet->getColumnDimension('N')->setWidth(14);
                $sheet->getColumnDimension('O')->setWidth(14);
                $sheet->getColumnDimension('P')->setWidth(14);
                $sheet->getColumnDimension('Q')->setWidth(14);
                $sheet->getColumnDimension('R')->setWidth(14);
                $sheet->getColumnDimension('S')->setWidth(45);
                $sheet->getColumnDimension('T')->setWidth(25);
                $sheet->getStyle('A1:T2')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Datos de Factura', '', '', '', 'Datos de Remito', '', '', '', '', '', '', '', '', 'Datos de Vale')), NULL, 'A1');
                $sheet->fromArray(array(array('Factura', 'Fecha', 'Total M³/Litros', 'Total Costo', 'Remito', 'Fecha', 'Tipo', 'M³/Litros', 'Costo', 'DNI', 'Nombre', 'Patente', 'Observaciones', 'Vale', 'Fecha', 'Vencimiento', 'Tipo', 'M³/Litros', 'Área', 'Justificación')), NULL, 'A2');
                $sheet->fromArray($print_data, NULL, 'A3');

                $sheet->mergeCells('A1:D1');
                $sheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('E1:M1');
                $sheet->getStyle('E1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('N1:T1');
                $sheet->getStyle('N1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));

                $sheet->getStyle('D3:D' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");
                $sheet->getStyle('I3:I' . (sizeof($print_data) + 2))->getNumberFormat()->setFormatCode("$ #,##0.00");

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('E1:E' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('N1:N' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('U1:U' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);

                $BStyle1 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A1:T1')->applyFromArray($BStyle1);

                $sheet->setAutoFilter('A2:T2');

                $nombreArchivo = 'InformeFacturas_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos para el periodo seleccionado';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Consumo por Combustible';
        $data['title'] = TITLE . ' - Informe de Consumo por Combustible';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function emitidos()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Vales_model');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Fecha Vale Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Fecha Vale Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

            $options['select'] = array(
                "CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as vale",
                'vc_vales.fecha',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                "CASE 
                    WHEN vc_vales.persona_id IS NOT NULL THEN CONCAT(personal.Apellido, ', ', personal.Nombre)
                    WHEN vc_vales.persona_id IS NULL THEN vc_vales.persona_nombre
		END AS 'persona_nombre'",
		'vc_vales.persona_id',
                'V.nombre',
                "COALESCE(V.dominio, 'SIN DOMINIO')",
                'V.propiedad',
                'vc_vales.vencimiento',
                'TCV.nombre as v_tipo_combustible',
                'vc_vales.metros_cubicos',
                'vc_vales.estado',
                'vc_vales.observaciones'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCV', 'where' => 'TCV.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'vc_vehiculos V', 'where' => 'V.id = vc_vales.vehiculo_id'),
                array('type' => 'left', 'table' => 'personal', 'where' => 'personal.Legajo = vc_vales.persona_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id')
            );

            $options['fecha >='] = $desde->format('Y-m-d');
            $hasta->add(new DateInterval('P1D'));
            $options['fecha <'] = $hasta->format('Y-m-d');

            $options['sort_by'] = 'vc_vales.id';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Vales_model->get($options);

            if (!empty($print_data))
            {
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha'] = date_format(new DateTime($value['fecha']), 'd-m-Y');
                    $print_data[$key]['vencimiento'] = date_format(new DateTime($value['vencimiento']), 'd-m-Y');
                }
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Vales emitidos")
                        ->setDescription("Informe de Vales emitidos (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Vales emitidos");
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(45);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(18);
                $sheet->getColumnDimension('H')->setWidth(18);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(14);
                $sheet->getColumnDimension('K')->setWidth(14);
                $sheet->getColumnDimension('L')->setWidth(14);
                $sheet->getColumnDimension('M')->setWidth(30);
                $sheet->getStyle('A1:M1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Vale', 'Fecha', 'Área', 'Beneficiario', 'DNI Beneficiario', 'Vehículo Nombre', 'Vehículo Dominio', 'Vehículo Propiedad', 'Vencimiento', 'Tipo', 'M³/Litros', 'Estado', 'Justificación')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('M1:M' . (sizeof($print_data) + 1))->applyFromArray($BStyle1);

                $BStyle2 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A' . (sizeof($print_data) + 1) . ':L' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

                $sheet->setAutoFilter('A1:L1');

                $nombreArchivo = 'InformeEmitidos_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin datos para el periodo seleccionado';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Vales emitidos';
        $data['title'] = TITLE . ' - Informe de Vales emitidos';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function fuera_termino()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Vales_model');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Fecha Vale Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Fecha Vale Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

            $options['select'] = array(
                "CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as vale",
                'vc_vales.fecha',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'vc_vales.vencimiento',
                'TCV.nombre as v_tipo_combustible',
                'vc_vales.metros_cubicos',
                'vc_vales.estado',
                'vc_vales.observaciones',
                'vc_remitos.remito',
                'vc_remitos.fecha as fecha_remito'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCV', 'where' => 'TCV.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id'),
                array('table' => 'vc_remitos', 'where' => 'vc_remitos.id = vc_vales.remito_id AND vc_vales.vencimiento < vc_remitos.fecha')
            );

            $options['fecha >='] = $desde->format('Y-m-d');
            $hasta->add(new DateInterval('P1D'));
            $options['fecha <'] = $hasta->format('Y-m-d');

            $options['sort_by'] = 'vc_vales.id';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Vales_model->get($options);

            if (!empty($print_data))
            {
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha'] = !empty($value['fecha']) ? date_format(new DateTime($value['fecha']), 'd-m-Y') : '';
                    $print_data[$key]['vencimiento'] = !empty($value['vencimiento']) ? date_format(new DateTime($value['vencimiento']), 'd-m-Y') : '';
                    $print_data[$key]['fecha_remito'] = !empty($value['fecha_remito']) ? date_format(new DateTime($value['fecha_remito']), 'd-m-Y') : '';
                }
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Vales cargados fuera de término")
                        ->setDescription("Informe de Vales cargados fuera de término (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Vales");
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(45);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(30);
                $sheet->getColumnDimension('I')->setWidth(14);
                $sheet->getColumnDimension('J')->setWidth(14);
                $sheet->getStyle('A1:J1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Vale', 'Fecha', 'Área', 'Vencimiento', 'Tipo', 'M³/Litros', 'Estado', 'Justificación', 'Remito', 'Fecha Remito')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('K1:K' . (sizeof($print_data) + 1))->applyFromArray($BStyle1);

                $BStyle2 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A' . (sizeof($print_data) + 1) . ':J' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

                $sheet->setAutoFilter('A1:J1');

                $nombreArchivo = 'InformeFueraTermino_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin datos para el periodo seleccionado';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Vales cargados fuera de término';
        $data['title'] = TITLE . ' - Informe de Vales cargados fuera de término';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function resumen_vales()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('Areas_model');
        $this->load->model('vales_combustible/Cupos_combustible_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('vales_combustible/Vales_model');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));
        $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Semana Desde', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = clone $desde;
            $hasta->add(new DateInterval('P7D'));

            $cupos = $this->Cupos_combustible_model->get(
                    array(
                        'select' => array(
                            'vc_tipos_combustible.nombre as tipo_combustible',
                            "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                            'vc_cupos_combustible.fecha_inicio',
                            'vc_cupos_combustible.metros_cubicos'
                        ),
                        'join' => array(
                            array(
                                'type' => 'left',
                                'table' => '(SELECT tipo_combustible_id, area_id, MAX(fecha_inicio) as fecha_inicio FROM vc_cupos_combustible GROUP BY tipo_combustible_id, area_id) t2',
                                'where' => 'vc_cupos_combustible.tipo_combustible_id = t2.tipo_combustible_id and vc_cupos_combustible.area_id = t2.area_id AND vc_cupos_combustible.fecha_inicio = t2.fecha_inicio'),
                            array('type' => 'left', 'table' => 'vc_tipos_combustible', 'where' => 'vc_tipos_combustible.id = vc_cupos_combustible.tipo_combustible_id'),
                            array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_cupos_combustible.area_id')
                        ),
                        'where' => array(
                            array('column' => 'vc_cupos_combustible.fecha_inicio <=', 'value' => $hasta->format('Y-m-d'))
                        )
                    )
            );
            $resumen_cupos = array();
            if (!empty($cupos))
            {
                foreach ($cupos as $value)
                {
                    $resumen_cupos[$value->area][$value->tipo_combustible] = $value->metros_cubicos;
                }
            }
            $data['resumen_cupos'] = $resumen_cupos;

            $options['select'] = array(
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'vc_tipos_combustible.nombre as tipo_combustible',
                "CASE WHEN vc_vales.estado <> 'Pendiente' THEN 'Aprobado' ELSE vc_vales.estado END as estado",
                'SUM(vc_vales.metros_cubicos) as litros'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_tipos_combustible', 'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id')
            );

            if ($this->input->post('area') !== 'Todas')
            {
                $where['column'] = 'vc_vales.area_id';
                $where['value'] = $this->input->post('area');
                $options['where'] = array($where);
            }

            $where['column'] = "vc_vales.estado IN ('Asignado', 'Creado', 'Impreso', 'Pendiente')";
            $where['value'] = '';
            $where['override'] = TRUE;
            $options['where'][] = $where;

            $options['fecha >='] = $desde->format('Y-m-d');
            $hasta->add(new DateInterval('P1D'));
            $options['fecha <'] = $hasta->format('Y-m-d');

            $options['group_by'] = 'area, tipo_combustible, estado';
            $options['sort_by'] = 'areas.codigo, tipo_combustible, estado';
            $print_data = $this->Vales_model->get($options);

            if (!empty($print_data))
            {
                $resumen = array();

                $last_area = NULL;
                $last_tipo = NULL;
                $total_litros = 0;
                foreach ($print_data as $value)
                {
                    if ($last_area !== $value->area || $last_tipo !== $value->tipo_combustible)
                    {
                        if (!empty($last_area) && !empty($last_tipo))
                        {
                            $resumen[$last_area][$last_tipo]['TOTAL'] = $total_litros;
                        }
                        $total_litros = 0;
                        $last_area = $value->area;
                        $last_tipo = $value->tipo_combustible;
                    }
                    $resumen[$value->area][$value->tipo_combustible][$value->estado] = $value->litros;
                    $total_litros += $value->litros;
                }
                if (!empty($last_area) && !empty($last_tipo))
                {
                    $resumen[$last_area][$last_tipo]['TOTAL'] = $total_litros;
                }
                $data['resumen'] = $resumen;
            }
            else
            {
                $error_msg = '<br />Sin datos para el periodo seleccionado';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $data['tipos_combustible'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Resumen de Vales';
        $data['title'] = TITLE . ' - Resumen de Vales';
        $this->load_template('vales_combustible/reportes/reportes_resumen_content', $data);
    }

    public function sin_uso()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Vales_model');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Fecha Vale Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Fecha Vale Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

            $options['select'] = array(
                "CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as vale",
                'vc_vales.fecha',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'vc_vales.vencimiento',
                'TCV.nombre as v_tipo_combustible',
                'vc_vales.metros_cubicos',
                'vc_vales.estado',
                'vc_vales.observaciones'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCV', 'where' => 'TCV.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id')
            );
            $where['column'] = 'vc_vales.remito_id IS NULL';
            $where['value'] = '';
            $where['override'] = TRUE;
            $options['where'] = array($where);

            $options['fecha >='] = $desde->format('Y-m-d');
            $hasta->add(new DateInterval('P1D'));
            $options['fecha <'] = $hasta->format('Y-m-d');

            $options['sort_by'] = 'vc_vales.id';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Vales_model->get($options);

            if (!empty($print_data))
            {
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha'] = date_format(new DateTime($value['fecha']), 'd-m-Y');
                    $print_data[$key]['vencimiento'] = date_format(new DateTime($value['vencimiento']), 'd-m-Y');
                }
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Vales sin uso")
                        ->setDescription("Informe de Vales sin uso (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Vales sin uso");
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(45);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(30);
                $sheet->getStyle('A1:H1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Vale', 'Fecha', 'Área', 'Vencimiento', 'Tipo', 'M³/Litros', 'Estado', 'Justificación')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('I1:I' . (sizeof($print_data) + 1))->applyFromArray($BStyle1);

                $BStyle2 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A' . (sizeof($print_data) + 1) . ':H' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

                $sheet->setAutoFilter('A1:I1');

                $nombreArchivo = 'InformeSinUso_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin datos para el periodo seleccionado';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Vales sin uso';
        $data['title'] = TITLE . ' - Informe de Vales sin uso';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function vencidos()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Vales_model');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Fecha Vale Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Fecha Vale Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

            $options['select'] = array(
                "CONCAT('VC', LPAD(vc_vales.id, 6, '0')) as vale",
                'vc_vales.fecha',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'vc_vales.vencimiento',
                'TCV.nombre as v_tipo_combustible',
                'vc_vales.metros_cubicos',
                'vc_vales.estado',
                'vc_vales.observaciones'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'vc_tipos_combustible TCV', 'where' => 'TCV.id = vc_vales.tipo_combustible_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = vc_vales.area_id')
            );
            $where['column'] = 'vc_vales.vencimiento <';
            $where['value'] = "'" . date_format(new DateTime(), 'Y/m/d') . "'";
            $where['override'] = TRUE;
            $options['where'] = array($where);

            $where['column'] = "vc_vales.estado NOT IN ('Anulado', 'Asignado', 'Pendiente')";
            $where['value'] = '';
            $options['where'][] = $where;

            $options['fecha >='] = $desde->format('Y-m-d');
            $hasta->add(new DateInterval('P1D'));
            $options['fecha <'] = $hasta->format('Y-m-d');

            $options['sort_by'] = 'vc_vales.id';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Vales_model->get($options);

            if (!empty($print_data))
            {
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha'] = date_format(new DateTime($value['fecha']), 'd-m-Y');
                    $print_data[$key]['vencimiento'] = date_format(new DateTime($value['vencimiento']), 'd-m-Y');
                }
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Vales vencidos")
                        ->setDescription("Informe de Vales vencidos (Módulo Vales de Combustible)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Vales vencidos");
                $sheet->getColumnDimension('A')->setWidth(14);
                $sheet->getColumnDimension('B')->setWidth(14);
                $sheet->getColumnDimension('C')->setWidth(45);
                $sheet->getColumnDimension('D')->setWidth(14);
                $sheet->getColumnDimension('E')->setWidth(14);
                $sheet->getColumnDimension('F')->setWidth(14);
                $sheet->getColumnDimension('G')->setWidth(14);
                $sheet->getColumnDimension('H')->setWidth(30);
                $sheet->getStyle('A1:H1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Vale', 'Fecha', 'Área', 'Vencimiento', 'Tipo', 'M³/Litros', 'Estado', 'Justificación')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('I1:I' . (sizeof($print_data) + 1))->applyFromArray($BStyle1);

                $BStyle2 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A' . (sizeof($print_data) + 1) . ':H' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

                $sheet->setAutoFilter('A1:H1');

                $nombreArchivo = 'InformeVencidos_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin datos para el periodo seleccionado';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Vales vencidos';
        $data['title'] = TITLE . ' - Informe de Vales vencidos';
        $this->load_template('vales_combustible/reportes/reportes_content', $data);
    }

    public function vehiculos()
    {
        if (!in_groups($this->grupos_contaduria, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Vehiculos_model');

        $print_data = $this->Vehiculos_model->get(array(
            'select' => array(
                'vc_vehiculos.nombre',
                'vc_vehiculos.propiedad',
                'vc_vehiculos.propietario',
                'vc_tipos_vehiculo.nombre as tipo_vehiculo',
                'vc_vehiculos.dominio',
                "(SELECT GROUP_CONCAT(vc_tipos_combustible.nombre SEPARATOR \", \") FROM vc_vehiculos_combustible JOIN vc_tipos_combustible ON vc_tipos_combustible.id = vc_vehiculos_combustible.tipo_combustible_id WHERE vc_vehiculos_combustible.vehiculo_id = vc_vehiculos.id) AS tipo_combustible",
                'vc_vehiculos.consumo',
                'vc_vehiculos.capacidad_tanque',
                'vc_vehiculos.consumo_semanal',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'vc_vehiculos.vencimiento_seguro',
                'vc_vehiculos.observaciones'
            ),
            'join' => array(
                array(
                    'table' => 'vc_tipos_vehiculo',
                    'where' => 'vc_tipos_vehiculo.id = vc_vehiculos.tipo_vehiculo_id',
                    'type' => 'LEFT'),
                array(
                    'table' => 'areas',
                    'where' => 'areas.id = vc_vehiculos.area_id',
                    'type' => 'LEFT')
            ),
            'sort_by' => 'vc_vehiculos.id',
            'return_array' => TRUE
        ));

        if (!empty($print_data))
        {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getProperties()
                    ->setCreator("SistemaMLC")
                    ->setLastModifiedBy("SistemaMLC")
                    ->setTitle("Informe de Vehículos")
                    ->setDescription("Informe de Vehículos (Módulo Vales de Combustible)");
            $spreadsheet->setActiveSheetIndex(0);

            $sheet = $spreadsheet->getActiveSheet();
            $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
            $sheet->setTitle("Informe de Vehículos");

            $BStyle1 = array(
                'borders' => array(
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                    )
                )
            );
            $sheet->getStyle('A1:L1')->applyFromArray($BStyle1);

            foreach ($print_data as $key => $value)
            {
                $print_data[$key]['vencimiento_seguro'] = !empty($value['vencimiento_seguro']) ? date_format(new DateTime($value['vencimiento_seguro']), 'd-m-Y') : '';
            }
            $sheet->getStyle('A1:L1')->applyFromArray($BStyle1);

            $sheet->getColumnDimension('A')->setWidth(50);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(40);
            $sheet->getColumnDimension('D')->setWidth(15);
            $sheet->getColumnDimension('E')->setWidth(15);
            $sheet->getColumnDimension('F')->setWidth(20);
            $sheet->getColumnDimension('G')->setWidth(15);
            $sheet->getColumnDimension('H')->setWidth(15);
            $sheet->getColumnDimension('I')->setWidth(15);
            $sheet->getColumnDimension('J')->setWidth(40);
            $sheet->getColumnDimension('K')->setWidth(15);
            $sheet->getColumnDimension('L')->setWidth(40);

            $sheet->getStyle('A1:L1')->getFont()->setBold(TRUE);
            $sheet->fromArray(array(array('Nombre Vehículo', 'Propiedad', 'Propietario', 'Tipo Vehículo', 'Dominio/Serie', 'Tipo Combustible', 'Consumo c/100 KM', 'Capacidad Tanque', 'Consumo Semanal', 'Área', 'Venc Seguro', 'Observaciones')), NULL, 'A1');
            $sheet->fromArray($print_data, NULL, 'A2');

            $sheet->setAutoFilter('A1:L1');

            $nombreArchivo = 'InformeVehiculos_' . date('Ymd');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
            header("Cache-Control: max-age=0");

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Sin datos');
            redirect('vales_combustible/reportes/listar', 'refresh');
        }
    }
}
