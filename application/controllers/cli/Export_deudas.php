<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Export_deudas extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->input->is_cli_request())
        {
            show_404();
        }
    }

    public function index()
    {
        show_404();
    }

    public function export($dtri_Codigo = 1, $hasta = '20201231', $trib_Cuenta_desde = 1, $trib_Cuenta_hasta = 1000, $detalle = 'Ninguno')
    {
        $this->benchmark->mark('inicio');

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        $error_msg = '';
        if (empty($error_msg))
        {
            $trib_Cuenta_desde = str_pad($trib_Cuenta_desde, 20, "0", STR_PAD_LEFT);
            $trib_Cuenta_hasta = str_pad($trib_Cuenta_hasta, 20, "0", STR_PAD_LEFT);
            try
            {
                if ($dtri_Codigo === 1)
                {
                    $http_response_deuda = $guzzleHttp->request('GET', "inmuebles/deudas_procesadas", ['query' => ['id_desde' => $trib_Cuenta_desde, 'id_hasta' => $trib_Cuenta_hasta, 'hasta' => $hasta]]);
                }
                else if ($dtri_Codigo === 2)
                {
                    $http_response_deuda = $guzzleHttp->request('GET', "comercios/deudas_procesadas", ['query' => ['id_desde' => $trib_Cuenta_desde, 'id_hasta' => $trib_Cuenta_hasta, 'hasta' => $hasta]]);
                }
                $deudas = json_decode($http_response_deuda->getBody()->getContents(), TRUE);
            } catch (GuzzleHttp\Exception\ClientException $e)
            {
                $response_content = $e->getResponse()->getBody()->getContents();
                $response_content_json = json_decode($response_content);
                if (!empty($response_content_json->message))
                {
                    $error_msg = '<br>' . $response_content_json->message;
                }
                else
                {
                    $error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
                }
            } catch (Exception $e)
            {
                $error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
            }
        }

        $this->benchmark->mark('excel');

        if (!empty($deudas))
        {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getProperties()
                    ->setCreator("SistemaMLC")
                    ->setLastModifiedBy("SistemaMLC")
                    ->setTitle("Deudas Masivas")
                    ->setDescription("Informe de Deudas Masivas (Módulo Major)");
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Deudas");
            if ($detalle === 'SubTasa')
            {
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(25);
                $sheet->getStyle('A1:F1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Padron', 'Tasa', 'SubTasa', 'Deuda Base', 'Intereses y Recargos', 'Deuda Total')), NULL, 'A1');
                $sheet->fromArray($deudas, NULL, 'A2');

                $cant_deudas = count($deudas) + 1;
                $sheet->getStyle("D2:D$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                $sheet->getStyle("E2:E$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                $sheet->getStyle("F2:F$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
            }
            elseif ($detalle === 'Tasa')
            {
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(40);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getStyle('A1:E1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Padron', 'Tasa', 'Deuda Base', 'Intereses y Recargos', 'Deuda Total')), NULL, 'A1');
                $sheet->fromArray($deudas, NULL, 'A2');

                $cant_deudas = count($deudas) + 1;
                $sheet->getStyle("C2:C$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                $sheet->getStyle("D2:D$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                $sheet->getStyle("E2:E$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
            }
            else
            {
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getStyle('A1:D1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Padron', 'Deuda Base', 'Intereses y Recargos', 'Deuda Total')), NULL, 'A1');
                $sheet->fromArray($deudas, NULL, 'A2');

                $cant_deudas = count($deudas) + 1;
                $sheet->getStyle("B2:B$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                $sheet->getStyle("C2:C$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                $sheet->getStyle("D2:D$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
            }
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save("deudas_masivas_al_$hasta.xlsx");
        }
        $this->benchmark->mark('fin');

        echo $this->benchmark->elapsed_time('inicio', 'excel') . " | ";
        echo $this->benchmark->elapsed_time('excel', 'fin') . " | ";
        echo $this->benchmark->elapsed_time('inicio', 'fin');
    }
}
