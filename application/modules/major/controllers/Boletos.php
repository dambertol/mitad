<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Boletos extends MY_Controller
{

    /**
     * Controlador de Boletos
     * Autor: Leandro
     * Creado: 29/01/2019
     * Modificado: 15/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'major_boletos', 'major_consulta_general');
        $this->grupos_solo_consulta = array('major_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $u_trib = 13.80;
        $pago_Periodo = 2021;
        $this->array_servicio_control = $array_servicio = array(
            'I' => 'Inmueble',
            'C' => 'Comercio'
        );

        $array_servicio_impresion = array(
            'A' => 'Agua Potable y Tratamiento de Efluentes Cloacales',
            'B' => 'Servicios Generales a la Propiedad Raíz',
            'C' => 'Derechos de Comercio, Industria y Actividades Civiles'
        );

        $this->form_validation->set_rules('trib_Cuenta', 'Padrón', 'required|max_length[6]|integer');
        $this->form_validation->set_rules('servicio', 'Servicio', 'required|callback_control_combo[servicio]');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'servicio' => array('label' => 'Servicio', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'trib_Cuenta' => array('label' => 'Padrón', 'type' => 'integer', 'maxlength' => '20', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $trib_Cuenta = (int) $this->input->post('trib_Cuenta');
            $servicio = $this->input->post('servicio');
            switch ($servicio)
            {
                case 'I':
                    $dtri_Codigo = 1;
                    break;
                default:
                    $dtri_Codigo = 2;
                    break;
            }

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            try
            {
                $http_response_boletos = $guzzleHttp->request('GET', "boletos/boletos", ['query' => ['id' => $trib_Cuenta, 'dtri_Codigo' => $dtri_Codigo, 'pago_Periodo' => $pago_Periodo]]);
                $boletos = json_decode($http_response_boletos->getBody()->getContents());
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

            if (!empty($boletos))
            {
                $cuotas_servicio = array(
                    'A' => array('00A4', '00A5', '00A6'),
                    'B' => array('00B4', '00B5', '00B6'),
                    'C' => array('0004', '0005', '0006')
                );
                $cuota_anual = array(
                    'A' => array('00A4'),
                    'B' => array('00B4'),
                    'C' => array('0004')
                );
                $boletas_arr = array();
                $detalle = NULL;
                $detalle_anual = NULL;
                $descuento_en_termino = 0;

                foreach ($boletos as $Boleto)
                {
                    try
                    {
                        if ($dtri_Codigo === 1)
                        {
                            //INMUEBLES A
                            if ($Boleto->pago_Cuota === $cuotas_servicio['A'][0] && $Boleto->pago_CodigoDelegacion === 30)
                            {
                                $http_response_detalle = $guzzleHttp->request('GET', "boletos/detalle", ['query' => ['id' => $trib_Cuenta, 'dtri_Codigo' => $dtri_Codigo, 'pago_CodigoDelegacion' => $Boleto->pago_CodigoDelegacion, 'pago_Numero' => $Boleto->pago_Numero]]);
                                $detalle['A'] = json_decode($http_response_detalle->getBody()->getContents());
                            }
                            if ($Boleto->pago_Cuota === $cuota_anual['A'][0] && $Boleto->pago_CodigoDelegacion === 85)
                            {
                                $http_response_detalle = $guzzleHttp->request('GET', "boletos/detalle", ['query' => ['id' => $trib_Cuenta, 'dtri_Codigo' => $dtri_Codigo, 'pago_CodigoDelegacion' => $Boleto->pago_CodigoDelegacion, 'pago_Numero' => $Boleto->pago_Numero]]);
                                $detalle_anual['A'] = json_decode($http_response_detalle->getBody()->getContents());
                            }
                            //INMUEBLES B
                            if ($Boleto->pago_Cuota === $cuotas_servicio['B'][0] && $Boleto->pago_CodigoDelegacion === 30)
                            {
                                $http_response_detalle = $guzzleHttp->request('GET', "boletos/detalle", ['query' => ['id' => $trib_Cuenta, 'dtri_Codigo' => $dtri_Codigo, 'pago_CodigoDelegacion' => $Boleto->pago_CodigoDelegacion, 'pago_Numero' => $Boleto->pago_Numero]]);
                                $detalle['B'] = json_decode($http_response_detalle->getBody()->getContents());
                            }
                            if ($Boleto->pago_Cuota === $cuota_anual['B'][0] && $Boleto->pago_CodigoDelegacion === 85)
                            {
                                $http_response_detalle = $guzzleHttp->request('GET', "boletos/detalle", ['query' => ['id' => $trib_Cuenta, 'dtri_Codigo' => $dtri_Codigo, 'pago_CodigoDelegacion' => $Boleto->pago_CodigoDelegacion, 'pago_Numero' => $Boleto->pago_Numero]]);
                                $detalle_anual['B'] = json_decode($http_response_detalle->getBody()->getContents());
                            }
                        }
                        else if ($dtri_Codigo === 2)
                        {
                            //COMERCIOS
                            if ($Boleto->pago_Cuota === $cuotas_servicio[$servicio][0] && $Boleto->pago_CodigoDelegacion === 30)
                            {
                                $http_response_detalle = $guzzleHttp->request('GET', "boletos/detalle", ['query' => ['id' => $trib_Cuenta, 'dtri_Codigo' => $dtri_Codigo, 'pago_CodigoDelegacion' => $Boleto->pago_CodigoDelegacion, 'pago_Numero' => $Boleto->pago_Numero]]);
                                $detalle = json_decode($http_response_detalle->getBody()->getContents());
                            }
                            if ($Boleto->pago_Cuota === $cuota_anual[$servicio][0] && $Boleto->pago_CodigoDelegacion === 85)
                            {
                                $http_response_detalle = $guzzleHttp->request('GET', "boletos/detalle", ['query' => ['id' => $trib_Cuenta, 'dtri_Codigo' => $dtri_Codigo, 'pago_CodigoDelegacion' => $Boleto->pago_CodigoDelegacion, 'pago_Numero' => $Boleto->pago_Numero]]);
                                $detalle_anual = json_decode($http_response_detalle->getBody()->getContents());
                            }
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

                    if ($dtri_Codigo === 1)
                    {
                        if (in_array($Boleto->pago_Cuota, $cuotas_servicio['A']))
                        {
                            $boletas_arr['A'][] = $Boleto;
                        }
                        if (in_array($Boleto->pago_Cuota, $cuotas_servicio['B']))
                        {
                            $boletas_arr['B'][] = $Boleto;
                        }
                    }
                    else if ($dtri_Codigo === 2)
                    {
                        if (in_array($Boleto->pago_Cuota, $cuotas_servicio[$servicio]))
                        {
                            $boletas_arr[] = $Boleto;
                        }
                    }
                }
            }

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
                $padron = json_decode($http_response_datos->getBody()->getContents());
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

            if (!empty($padron) && !empty($boletas_arr))
            {
                if ($dtri_Codigo == 1)
                {
                    $sup = array();
                    $sup[1][0] = 0; //TOTAL
                    $sup[2][0] = 0; //CUBIERTA
                    $sup[4][0] = 0; //PILETA/PISCINA DECLARADA
                    $sup[5][0] = 0; //CUBIERTA GIS
                    $sup[6][0] = 0; //PILETA/PISCINA GIS
                    foreach ($padron->superficies as $superficie)
                    {
                        $sup[$superficie->tips_Codigo][$superficie->supe_Numero] = $superficie->supe_Superficie;
                    }

                    $sup_total = 0;
                    foreach ($sup[1] as $sup_t)
                    {
                        $sup_total += $sup_t;
                    }

                    $sup_cubierta = 0;
                    foreach ($sup[2] as $sup_c)
                    {
                        $sup_cubierta += $sup_c;
                    }

                    $sup_pileta = 0;
                    foreach ($sup[4] as $sup_p)
                    {
                        $sup_pileta += $sup_p;
                    }

                    $sup_cubierta_gis = 0;
                    foreach ($sup[5] as $sup_cg)
                    {
                        $sup_cubierta_gis += $sup_cg;
                    }

                    $sup_pileta_gis = 0;
                    foreach ($sup[6] as $sup_pg)
                    {
                        $sup_pileta_gis += $sup_pg;
                    }

                    $padron->sup_total = $sup_total;
                    $padron->sup_cubierta = $sup_cubierta;
                    $padron->sup_pileta = $sup_pileta;
                    $padron->sup_cubierta_gis = $sup_cubierta_gis;
                    $padron->sup_pileta_gis = $sup_pileta_gis;
                }
                $data['boletas'] = $boletas_arr;
                $data['padron'] = array($padron);

                if ($dtri_Codigo === 1)
                {
                    $txt_tmp = '';
                    if (!empty($boletas_arr['A']))
                    {
                        $txt_tmp .= $array_servicio_impresion['A'] . ' ';
                    }
                    if (!empty($boletas_arr['B']))
                    {
                        $txt_tmp .= $array_servicio_impresion['B'];
                    }
                    $data['texto_servicio'] = $txt_tmp;
                }
                else if ($dtri_Codigo === 2)
                {
                    $data['texto_servicio'] = $array_servicio_impresion[$servicio];
                }

                $facturacion_tasa = array();
                if (!empty($detalle))
                {
                    if ($dtri_Codigo === 1)
                    {
                        if (!empty($detalle['A']))
                        {
                            foreach ($detalle['A'] as $Det)
                            {
                                if ($Det->tcuo_Codigo === 0)
                                {
                                    $facturacion_tasa['A'][$Det->ttas_Tasa . '-' . $Det->ttas_SubTasa] = $Det->pago_Importe;
                                }
                            }
                        }
                        if (!empty($detalle['B']))
                        {
                            foreach ($detalle['B'] as $Det)
                            {
                                if ($Det->tcuo_Codigo === 0)
                                {
                                    $facturacion_tasa['B'][$Det->ttas_Tasa . '-' . $Det->ttas_SubTasa] = $Det->pago_Importe;
                                }
                            }
                        }
                    }
                    else if ($dtri_Codigo === 2)
                    {
                        foreach ($detalle as $Det)
                        {
                            if ($Det->tcuo_Codigo === 0)
                            {
                                $facturacion_tasa[$Det->ttas_Tasa . '-' . $Det->ttas_SubTasa] = $Det->pago_Importe;
                            }
                        }
                    }
                }

                if (!empty($detalle_anual))
                {
                    if ($dtri_Codigo === 1)
                    {
                        $tmp_descuento = 0;
                        if (!empty($detalle_anual['A']))
                        {
                            foreach ($detalle_anual['A'] as $DetA)
                            {
                                if ($DetA->tcuo_Codigo === 0)
                                {
                                    $facturacion_tasa_anual['A'][$DetA->ttas_Tasa . '-' . $DetA->ttas_SubTasa] = $DetA->pago_Importe;
                                }
                                else if ($DetA->tcuo_Codigo === 21)
                                {
                                    $tmp_descuento += $DetA->pago_aCancelar;
                                }
                            }
                        }
                        if (!empty($detalle_anual['B']))
                        {
                            foreach ($detalle_anual['B'] as $DetA)
                            {
                                if ($DetA->tcuo_Codigo === 0)
                                {
                                    $facturacion_tasa_anual['B'][$DetA->ttas_Tasa . '-' . $DetA->ttas_SubTasa] = $DetA->pago_Importe;
                                }
                                else if ($DetA->tcuo_Codigo === 21)
                                {
                                    $tmp_descuento += $DetA->pago_aCancelar;
                                }
                            }
                        }
                        $descuento_en_termino = number_format($tmp_descuento, 2, ',', '.');
                    }
                    else if ($dtri_Codigo === 2)
                    {
                        foreach ($detalle_anual as $DetA)
                        {
                            if ($DetA->tcuo_Codigo === 0)
                            {
                                $facturacion_tasa_anual[$DetA->ttas_Tasa . '-' . $DetA->ttas_SubTasa] = $DetA->pago_Importe;
                            }
                            else if ($DetA->tcuo_Codigo === 21)
                            {
                                $descuento_en_termino = number_format($DetA->pago_aCancelar, 2, ',', '.');
                            }
                        }
                    }
                }

                switch ($servicio)
                {
                    case 'I':
                        //AGUA
                        $totalA = (isset($facturacion_tasa['A']['1000-3']) ? $facturacion_tasa['A']['1000-3'] : 0) + (isset($facturacion_tasa['A']['1000-4']) ? $facturacion_tasa['A']['1000-4'] : 0) + (isset($facturacion_tasa['A']['99999-0']) ? $facturacion_tasa['A']['99999-0'] : 0) + (isset($facturacion_tasa['A']['1300-0']) ? $facturacion_tasa['A']['1300-0'] : 0) + (isset($facturacion_tasa['A']['1000-12']) ? $facturacion_tasa['A']['1000-12'] : 0);

                        $facturacion['A'] = array(
                            (isset($facturacion_tasa['A']['1000-3']) ? '$' . number_format($facturacion_tasa['A']['1000-3'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['A']['1000-4']) ? '$' . number_format($facturacion_tasa['A']['1000-4'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['A']['99999-10']) ? '$' . number_format($facturacion_tasa['A']['99999-10'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['A']['99999-0']) ? '$' . number_format($facturacion_tasa['A']['99999-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['A']['1300-0']) ? '$' . number_format($facturacion_tasa['A']['1300-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['A']['1000-12']) ? '$' . number_format($facturacion_tasa['A']['1000-12'], 2, ',', '.') : ''),
                            (isset($totalA) ? '$' . number_format($totalA, 2, ',', '.') : '')
                        );

                        $total_anualA = (isset($facturacion_tasa_anual['A']['1000-3']) ? $facturacion_tasa_anual['A']['1000-3'] : 0) + (isset($facturacion_tasa_anual['A']['1000-4']) ? $facturacion_tasa_anual['A']['1000-4'] : 0) + (isset($facturacion_tasa_anual['A']['99999-0']) ? $facturacion_tasa_anual['A']['99999-0'] : 0) + (isset($facturacion_tasa_anual['A']['1300-0']) ? $facturacion_tasa_anual['A']['1300-0'] : 0) + (isset($facturacion_tasa_anual['A']['1000-12']) ? $facturacion_tasa_anual['A']['1000-12'] : 0);

                        $facturacion_anual['A'] = array(
                            (isset($facturacion_tasa_anual['A']['1000-3']) ? '$' . number_format($facturacion_tasa_anual['A']['1000-3'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['A']['1000-4']) ? '$' . number_format($facturacion_tasa_anual['A']['1000-4'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['A']['99999-10']) ? '$' . number_format($facturacion_tasa_anual['A']['99999-10'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['A']['99999-0']) ? '$' . number_format($facturacion_tasa_anual['A']['99999-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['A']['1300-0']) ? '$' . number_format($facturacion_tasa_anual['A']['1300-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['A']['1000-12']) ? '$' . number_format($facturacion_tasa_anual['A']['1000-12'], 2, ',', '.') : ''),
                            (isset($total_anualA) ? '$' . number_format($total_anualA, 2, ',', '.') : '')
                        );

                        //SERV GENERALES + ALUMBRADO
                        $totalB = (isset($facturacion_tasa['B']['1000-0']) ? $facturacion_tasa['B']['1000-0'] : 0) + (isset($facturacion_tasa['B']['1000-1']) ? $facturacion_tasa['B']['1000-1'] : 0) + (isset($facturacion_tasa['B']['1000-7']) ? $facturacion_tasa['B']['1000-7'] : 0) + (isset($facturacion_tasa['B']['99999-0']) ? $facturacion_tasa['B']['99999-0'] : 0) + (isset($facturacion_tasa['B']['99999-4']) ? $facturacion_tasa['B']['99999-4'] : 0) + (isset($facturacion_tasa['B']['1000-9']) ? $facturacion_tasa['B']['1000-9'] : 0) + (isset($facturacion_tasa['B']['1000-11']) ? $facturacion_tasa['B']['1000-11'] : 0);

                        $facturacion_tasa['B']['1000-0'] = 0;
                        if (isset($facturacion_tasa['B']['1000-1']))
                        {
                            $facturacion_tasa['B']['1000-0'] += $facturacion_tasa['B']['1000-1'];
                        }
                        if (isset($facturacion_tasa['B']['1000-2']))
                        {
                            $facturacion_tasa['B']['1000-0'] += $facturacion_tasa['B']['1000-2'];
                        }
                        $facturacion['B'] = array(
                            (isset($facturacion_tasa['B']['1000-0']) ? '$' . number_format($facturacion_tasa['B']['1000-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['B']['1000-7']) ? '$' . number_format($facturacion_tasa['B']['1000-7'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['B']['99999-0']) ? '$' . number_format($facturacion_tasa['B']['99999-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['B']['99999-4']) ? '$' . number_format($facturacion_tasa['B']['99999-4'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['B']['1000-9']) ? '$' . number_format($facturacion_tasa['B']['1000-9'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['B']['1000-11']) ? '$' . number_format($facturacion_tasa['B']['1000-11'], 2, ',', '.') : ''),
                            (isset($totalB) ? '$' . number_format($totalB, 2, ',', '.') : '')
                        );

                        $total_anualB = (isset($facturacion_tasa_anual['B']['1000-0']) ? $facturacion_tasa_anual['B']['1000-0'] : 0) + (isset($facturacion_tasa_anual['B']['1000-1']) ? $facturacion_tasa_anual['B']['1000-1'] : 0) + (isset($facturacion_tasa_anual['B']['1000-7']) ? $facturacion_tasa_anual['B']['1000-7'] : 0) + (isset($facturacion_tasa_anual['B']['99999-0']) ? $facturacion_tasa_anual['B']['99999-0'] : 0) + (isset($facturacion_tasa_anual['B']['99999-4']) ? $facturacion_tasa_anual['B']['99999-4'] : 0) + (isset($facturacion_tasa_anual['B']['1000-9']) ? $facturacion_tasa_anual['B']['1000-9'] : 0) + (isset($facturacion_tasa_anual['B']['1000-11']) ? $facturacion_tasa_anual['B']['1000-11'] : 0);

                        $facturacion_tasa_anual['B']['1000-0'] = 0;
                        if (isset($facturacion_tasa_anual['B']['1000-1']))
                        {
                            $facturacion_tasa_anual['B']['1000-0'] += $facturacion_tasa_anual['B']['1000-1'];
                        }
                        if (isset($facturacion_tasa_anual['B']['1000-2']))
                        {
                            $facturacion_tasa_anual['B']['1000-0'] += $facturacion_tasa_anual['B']['1000-2'];
                        }
                        $facturacion_anual['B'] = array(
                            (isset($facturacion_tasa_anual['B']['1000-0']) ? '$' . number_format($facturacion_tasa_anual['B']['1000-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['B']['1000-7']) ? '$' . number_format($facturacion_tasa_anual['B']['1000-7'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['B']['99999-0']) ? '$' . number_format($facturacion_tasa_anual['B']['99999-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['B']['99999-4']) ? '$' . number_format($facturacion_tasa_anual['B']['99999-4'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['B']['1000-9']) ? '$' . number_format($facturacion_tasa_anual['B']['1000-9'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['B']['1000-11']) ? '$' . number_format($facturacion_tasa_anual['B']['1000-11'], 2, ',', '.') : ''),
                            (isset($total_anualB) ? '$' . number_format($total_anualB, 2, ',', '.') : '')
                        );
                        break;
                    case 'C':
                        try
                        {
                            $http_response_tributo = $guzzleHttp->request('GET', "comercios/tributo_anual", ['query' => ['id' => $trib_Cuenta, 'ddjj_FechaBaja' => '20190101']]);
                            $tributo_anual_result = json_decode($http_response_tributo->getBody()->getContents());
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

                        $tributo_anual = $tributo_anual_result[0]->ddjj_Valor;
                        $total = (isset($facturacion_tasa['99999-0']) ? $facturacion_tasa['99999-0'] : 0) + (isset($facturacion_tasa['2000-0']) ? $facturacion_tasa['2000-0'] : 0) + (isset($facturacion_tasa['99999-4']) ? $facturacion_tasa['99999-4'] : 0);
                        $facturacion = array(
                            (isset($facturacion_tasa['99999-0']) ? '$' . number_format($facturacion_tasa['99999-0'], 2, ',', '.') : ''),
                            (isset($tributo_anual) ? number_format($tributo_anual, 2, ',', '.') : ''),
                            (isset($tributo_anual) ? number_format($tributo_anual / 6, 2, ',', '.') : ''),
                            number_format($u_trib, 2, ',', '.'),
                            (isset($facturacion_tasa['2000-0']) ? '$' . number_format($facturacion_tasa['2000-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa['99999-4']) ? '$' . number_format($facturacion_tasa['99999-4'], 2, ',', '.') : ''),
                            (isset($total) ? '$' . number_format($total, 2, ',', '.') : '')
                        );

                        $total_anual = (isset($facturacion_tasa_anual['99999-0']) ? $facturacion_tasa_anual['99999-0'] : 0) + (isset($facturacion_tasa_anual['2000-0']) ? $facturacion_tasa_anual['2000-0'] : 0) + (isset($facturacion_tasa_anual['99999-4']) ? $facturacion_tasa_anual['99999-4'] : 0);
                        $facturacion_anual = array(
                            (isset($facturacion_tasa_anual['99999-0']) ? '$' . number_format($facturacion_tasa_anual['99999-0'], 2, ',', '.') : ''),
                            (isset($tributo_anual) ? number_format($tributo_anual, 2, ',', '.') : ''),
                            (isset($tributo_anual) ? number_format($tributo_anual / 6, 2, ',', '.') : ''),
                            number_format($u_trib, 2, ',', '.'),
                            (isset($facturacion_tasa_anual['2000-0']) ? '$' . number_format($facturacion_tasa_anual['2000-0'], 2, ',', '.') : ''),
                            (isset($facturacion_tasa_anual['99999-4']) ? '$' . number_format($facturacion_tasa_anual['99999-4'], 2, ',', '.') : ''),
                            (isset($total_anual) ? '$' . number_format($total_anual, 2, ',', '.') : '')
                        );
                        break;
                }
                $deuda = $this->db->query("SELECT COALESCE(deuda, 0.00) as deuda FROM deudas_emision_2021 WHERE dtri_Codigo = ? AND trib_Cuenta = ?", [$dtri_Codigo, $trib_Cuenta])->row();
                if (empty($deuda))
                {
                    $deuda = new stdClass();
                    $deuda->deuda = 0.00;
                }

                $data['facturacion'] = $facturacion;
                $data['facturacion_anual'] = $facturacion_anual;
                $data['descuento_en_termino'] = $descuento_en_termino;
                $data['deuda'] = $deuda->deuda;

                $stylesheet = file_get_contents('css/major/major_boletas_pdf.css');
                if ($dtri_Codigo == 1)
                {
                    $html = $this->load->view('major/boletos/boletos_content_pdf', $data, TRUE);
                }
                else
                {
                    $html = $this->load->view('major/boletos/boletos_content_comercio_pdf', $data, TRUE);
                }

                $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 's',
                    'format' => 'A4',
                    'fontDir' => array_merge($fontDirs, [
                        FCPATH . 'css' . DIRECTORY_SEPARATOR . 'fonts',
                    ]),
                    'fontdata' => $fontData + [
                'panton' => [
                    'R' => 'Panton-Regular.ttf',
                    'B' => 'Panton-Bold.ttf',
                    'I' => 'Panton-Italic.ttf',
                    'BI' => 'Panton-BoldItalic.ttf'
                ],
                'pantonb' => [
                    'R' => 'Panton-Black.ttf'
                ]
                    ],
                    'default_font' => 'panton',
                    'margin_left' => 10,
                    'margin_right' => 10,
                    'margin_top' => 6,
                    'margin_bottom' => 3,
                    'margin_header' => 9,
                    'margin_footer' => 9
                ]);

                $mpdf->SetDisplayMode('fullwidth');
                $mpdf->SetTitle("Boleto");
                $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
                $mpdf->WriteHTML($stylesheet, 1);
                if ($dtri_Codigo === 1)
                {
                    $mpdf->SetDefaultBodyCSS('background', "url('img/major/frente_lujan_boleta.png')");
                }
                else
                {
                    $mpdf->SetDefaultBodyCSS('background', "url('img/major/frente_lujan_boleta_c.png')");
                }
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                $mpdf->WriteHTML($html, 2);
                $mpdf->AddPage();
                $mpdf->SetDefaultBodyCSS('background', "url('img/major/pie_lujan_boleta.png')");
                $mpdf->SetDefaultBodyCSS('background-image-resize', 6);
                $mpdf->WriteHTML("", 2);
                $mpdf->Output("$trib_Cuenta-boleta-$servicio.pdf", 'I');
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['servicio']['array'] = $array_servicio;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Impresión de Boletos';
        $data['title'] = TITLE . ' - Impresión de Boletos';
        $this->load_template('major/boletos/boletos_content', $data);
    }
}
