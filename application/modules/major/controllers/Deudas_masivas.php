<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Deudas_masivas extends MY_Controller
{

    /**
     * Controlador de Deudas Masivas
     * Autor: Leandro
     * Creado: 19/11/2019
     * Modificado: 16/04/2021 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'major_deudas_masivas', 'major_consulta_general');
        $this->grupos_solo_consulta = array('major_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->array_detalle_control = $array_detalle = array('Padrón' => 'Padrón', 'Tasa' => 'Tasa', 'SubTasa' => 'SubTasa');  // 'Documento' => 'Documento'
        $this->array_dtri_Codigo_control = $array_dtri_Codigo = array(1 => 'Inmueble', 2 => 'Comercio', 7 => 'Cuenta Especial', 99 => 'Persona');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'detalle' => array('label' => 'Detalle', 'input_type' => 'combo', 'id_name' => 'detalle', 'type' => 'bselect', 'required' => TRUE),
            'dtri_Codigo' => array('label' => 'Tipo', 'input_type' => 'combo', 'id_name' => 'dtri_Codigo', 'type' => 'bselect', 'required' => TRUE),
            'trib_Cuentas' => array('label' => 'Padrones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999', 'required' => TRUE),
            'hasta' => array('label' => 'Vencimiento Hasta', 'type' => 'date', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $detalle = $this->input->post('detalle');
            $dtri_Codigo = (int) $this->input->post('dtri_Codigo');
            $trib_Cuentas = $this->input->post('trib_Cuentas');
            $hasta = $this->get_date_sql('hasta', 'd/m/Y', 'Ymd');
            $array_Cuentas = explode(',', $trib_Cuentas);

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            $error_msg = '';
            if (empty($error_msg))
            {
                foreach ($array_Cuentas as $trib_Cuenta)
                {
                    $padrones = array();
                    try
                    {
                        if ($dtri_Codigo === 1)
                        {
                            $http_response_datos = $guzzleHttp->request('GET', "inmuebles/datos", ['query' => ['id' => $trib_Cuenta]]);
                        }
                        else if ($dtri_Codigo === 2)
                        {
                            $http_response_datos = $guzzleHttp->request('GET', "comercios/datos", ['query' => ['id' => $trib_Cuenta]]);
                        }
                        else if ($dtri_Codigo === 7)
                        {
                            $http_response_datos = $guzzleHttp->request('GET', "cuentas_especiales/datos", ['query' => ['id' => $trib_Cuenta]]);
                        }
                        else
                        {
                            $http_response_datos = $guzzleHttp->request('GET', "personas/datos_contribuyente", ['query' => ['id' => $trib_Cuenta]]);
                        }

                        $padrones = json_decode($http_response_datos->getBody()->getContents());
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

                    if (!empty($padrones))
                    {
                        if (!is_array($padrones))
                        {
                            $padrones = array($padrones);
                        }
                        $txt_documento = '';
                        $txt_padrones = '';
                        $total_deuda = 0;
                        $total_recargos = 0;
                        foreach ($padrones as $padron)
                        {
                            try
                            {
                                if ($padron->dtri_Codigo === 1)
                                {
                                    $http_response_deuda = $guzzleHttp->request('GET', "inmuebles/deudas", ['query' => ['id' => $padron->trib_Cuenta, 'hasta' => $hasta]]);
                                }
                                else if ($padron->dtri_Codigo === 2)
                                {
                                    $http_response_deuda = $guzzleHttp->request('GET', "comercios/deudas", ['query' => ['id' => $padron->trib_Cuenta, 'hasta' => $hasta]]);
                                }
                                else if ($padron->dtri_Codigo === 7)
                                {
                                    $http_response_deuda = $guzzleHttp->request('GET', "cuentas_especiales/deudas", ['query' => ['id' => $padron->trib_Cuenta, 'hasta' => $hasta]]);
                                }

                                $deuda = json_decode($http_response_deuda->getBody()->getContents());

                                if ($detalle !== 'Documento')   // Si es "Documento" acumula todo
                                {
                                    $total_deuda = 0;
                                    $total_recargos = 0;

                                    $deuda_padron_tasa = [];
                                    $deuda_padron_subtasa = [];
                                    foreach ($deuda->deudas as $Deuda)
                                    {
                                        $deuda_tmp = 0;
                                        $recargos_tmp = 0;
                                        $deuda_tmp += round($Deuda->Saldo * $Deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN);
                                        foreach ($Deuda->extras as $Extra)
                                        {
                                            $recargos_tmp += round($Extra * $Deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN);
                                        }

                                        if ($detalle === 'SubTasa')
                                        {
                                            if (isset($deuda_padron_subtasa[$Deuda->ttas_Tasa . $Deuda->ttas_SubTasa]))
                                            {
                                                $deuda_padron_subtasa[$Deuda->ttas_Tasa . $Deuda->ttas_SubTasa]['deuda'] += $deuda_tmp;
                                                $deuda_padron_subtasa[$Deuda->ttas_Tasa . $Deuda->ttas_SubTasa]['recargos'] += $recargos_tmp;
                                                $deuda_padron_subtasa[$Deuda->ttas_Tasa . $Deuda->ttas_SubTasa]['total'] += ($deuda_tmp + $recargos_tmp);
                                            }
                                            else
                                            {
                                                $deuda_padron_subtasa[$Deuda->ttas_Tasa . $Deuda->ttas_SubTasa] = array(
                                                    'documento' => $padron->pers_Numero,
                                                    'padron' => $padron->trib_Cuenta,
                                                    'tasa' => "$Deuda->ttas_Tasa - $Deuda->tasa_Descripcion",
                                                    'subtasa' => "$Deuda->ttas_SubTasa - $Deuda->ttas_Descripcion",
                                                    'deuda' => $deuda_tmp,
                                                    'recargos' => $recargos_tmp,
                                                    'total' => $deuda_tmp + $recargos_tmp
                                                );
                                            }
                                        }
                                        elseif ($detalle === 'Tasa')
                                        {
                                            if (isset($deuda_padron_tasa[$Deuda->ttas_Tasa]))
                                            {
                                                $deuda_padron_tasa[$Deuda->ttas_Tasa]['deuda'] += $deuda_tmp;
                                                $deuda_padron_tasa[$Deuda->ttas_Tasa]['recargos'] += $recargos_tmp;
                                                $deuda_padron_tasa[$Deuda->ttas_Tasa]['total'] += ($deuda_tmp + $recargos_tmp);
                                            }
                                            else
                                            {
                                                $deuda_padron_tasa[$Deuda->ttas_Tasa] = array(
                                                    'documento' => $padron->pers_Numero,
                                                    'padron' => $padron->trib_Cuenta,
                                                    'tasa' => "$Deuda->ttas_Tasa - $Deuda->tasa_Descripcion",
                                                    'deuda' => $deuda_tmp,
                                                    'recargos' => $recargos_tmp,
                                                    'total' => $deuda_tmp + $recargos_tmp
                                                );
                                            }
                                        }
                                        else
                                        {
                                            $total_deuda += $deuda_tmp;
                                            $total_recargos += $recargos_tmp;
                                        }
                                    }

                                    if ($detalle === 'SubTasa')
                                    {
                                        foreach ($deuda_padron_subtasa as $Dpst)
                                        {
                                            $deudas[] = $Dpst;
                                        }
                                    }
                                    elseif ($detalle === 'Tasa')
                                    {
                                        foreach ($deuda_padron_tasa as $Dpt)
                                        {
                                            $deudas[] = $Dpt;
                                        }
                                    }
                                    else
                                    {
                                        $deudas[] = array(
                                            'documento' => $padron->pers_Numero,
                                            'padron' => $padron->trib_Cuenta,
                                            'deuda' => $total_deuda,
                                            'recargos' => $total_recargos,
                                            'total' => $total_deuda + $total_recargos
                                        );
                                    }
                                }
                                else
                                {
                                    $deuda_tmp = 0;
                                    $recargos_tmp = 0;
                                    foreach ($deuda->deudas as $Deuda)
                                    {
                                        $deuda_tmp += round($Deuda->Saldo * $Deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN);
                                        foreach ($Deuda->extras as $Extra)
                                        {
                                            $recargos_tmp += round($Extra * $Deuda->ValorTipoCantidadSaldo, 2, PHP_ROUND_HALF_EVEN);
                                        }
                                    }

                                    $txt_documento = $padron->pers_Numero;  // Todos los padrones deberian ser del mismo Documento
                                    $txt_padrones .= "$padron->trib_Cuenta | ";
                                    $total_deuda += $deuda_tmp;
                                    $total_recargos += $recargos_tmp;
                                }
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

                        if ($detalle === 'Documento')
                        {
                            $deudas[] = array(
                                'documento' => $txt_documento,
                                'padron' => $txt_padrones,
                                'deuda' => $total_deuda,
                                'recargos' => $total_recargos,
                                'total' => $total_deuda + $total_recargos
                            );
                        }
                    }
                    else
                    {
                        $error_msg = '<br>No se encontro el Padrón solicitado';
                    }
                }
            }

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
                    $sheet->getColumnDimension('B')->setWidth(25);
                    $sheet->getColumnDimension('C')->setWidth(50);
                    $sheet->getColumnDimension('D')->setWidth(50);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getColumnDimension('G')->setWidth(20);
                    $sheet->getStyle('A1:G1')->getFont()->setBold(TRUE);
                    $sheet->fromArray(array(array('Documento', 'Padron', 'Tasa', 'SubTasa', 'Deuda Base', 'Intereses y Recargos', 'Deuda Total')), NULL, 'A1');
                    $sheet->fromArray($deudas, NULL, 'A2');
                    $nombreArchivo = 'deudas_masivas';

                    $cant_deudas = count($deudas) + 1;
                    $sheet->getStyle("E2:E$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                    $sheet->getStyle("F2:F$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                    $sheet->getStyle("G2:G$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                }
                elseif ($detalle === 'Tasa')
                {
                    $sheet->getColumnDimension('A')->setWidth(25);
                    $sheet->getColumnDimension('B')->setWidth(25);
                    $sheet->getColumnDimension('C')->setWidth(50);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getColumnDimension('F')->setWidth(20);
                    $sheet->getStyle('A1:F1')->getFont()->setBold(TRUE);
                    $sheet->fromArray(array(array('Documento', 'Padron', 'Tasa', 'Deuda Base', 'Intereses y Recargos', 'Deuda Total')), NULL, 'A1');
                    $sheet->fromArray($deudas, NULL, 'A2');
                    $nombreArchivo = 'deudas_masivas';

                    $cant_deudas = count($deudas) + 1;
                    $sheet->getStyle("D2:D$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                    $sheet->getStyle("E2:E$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                    $sheet->getStyle("F2:F$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                }
                else
                {
                    $sheet->getColumnDimension('A')->setWidth(25);
                    $sheet->getColumnDimension('B')->setWidth(25);
                    $sheet->getColumnDimension('C')->setWidth(20);
                    $sheet->getColumnDimension('D')->setWidth(20);
                    $sheet->getColumnDimension('E')->setWidth(20);
                    $sheet->getStyle('A1:E1')->getFont()->setBold(TRUE);
                    $sheet->fromArray(array(array('Documento', 'Padron', 'Deuda Base', 'Intereses y Recargos', 'Deuda Total')), NULL, 'A1');
                    $sheet->fromArray($deudas, NULL, 'A2');
                    $nombreArchivo = 'deudas_masivas';

                    $cant_deudas = count($deudas) + 1;
                    $sheet->getStyle("C2:C$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                    $sheet->getStyle("D2:D$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                    $sheet->getStyle("E2:E$cant_deudas")->getNumberFormat()->setFormatCode("$#,##0.00;$(-#,##0.00)");
                }
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['detalle']['array'] = $array_detalle;
        $fake_model->fields['dtri_Codigo']['array'] = $array_dtri_Codigo;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Consultar';
        $data['title_view'] = 'Consultar Deudas Masivas';
        $data['title'] = TITLE . ' - Consultar Deudas Masivas';
        $this->load_template('major/deudas_masivas/deudas_masivas_consultar', $data);
    }
}
