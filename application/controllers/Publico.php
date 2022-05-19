<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Publico extends MY_Controller
{

    /**
     * Controlador Público
     * Autor: Leandro
     * Creado: 13/08/2019
     * Modificado: 15/03/2021 (Leandro)
     */
    public function __construct()
    {
        $this->auth = FALSE;
        parent::__construct();
        $this->load->database();
        $this->load->library(array('form_validation', 'recaptcha'));
        $this->load->helper(array('url', 'language'));
    }

    public function boletos()
    {
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

        $this->form_validation->set_rules('padron', 'Padrón', 'required|max_length[6]|integer');
        $this->form_validation->set_rules('servicio', 'Servicio', 'required|max_length[1]');
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $recaptcha = $this->input->post('g-recaptcha-response');
            if (!empty($recaptcha))
            {
                $response = $this->recaptcha->verifyResponse($recaptcha);
                if (isset($response['success']) and $response['success'] === TRUE)
                {
                    $trib_Cuenta = (int) $this->input->post('padron');
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
                else
                {
                    $this->session->set_flashdata('error', '<br />Captcha inválido');
                    redirect("publico/boletos", 'refresh');
                }
            }
            else
            {
                $this->session->set_flashdata('error', '<br />Debe completar el captcha');
                redirect("publico/boletos", 'refresh');
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['recaptcha_widget'] = $this->recaptcha->getWidgetInvisible(array('data-callback' => 'submitForm'));
        $data['recaptcha_script'] = $this->recaptcha->getScriptTag();
        $data['servicio_opt'] = $array_servicio;
        $data['servicio_opt_selected'] = $this->form_validation->set_value('servicio');
        $data['padron'] = array(
            'id' => 'padron',
            'name' => 'padron',
            'type' => 'text',
            'value' => $this->form_validation->set_value('padron'),
            'class' => 'form-control',
            'placeholder' => 'Padrón',
            'required' => 'required',
            'pattern' => '^(0|[1-9][0-9]*)$',
            'title' => 'Debe ingresar sólo números (sin guiones, espacios ni 0 adelante)'
        );

        $this->load->view('publico/boletos', $data);
    }

    public function formularios()
    {
        $this->load->model('Localidades_model');
        $this->load->model('Formularios_model');
        $this->load->model('Formularios_registros_model');

        $formulario = $this->Formularios_model->get_one(1);

        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'nombre', 'id', array('select' => 'localidades.id, localidades.nombre', 'where' => array(array('column' => 'departamento_id', 'value' => 345))));
        $this->array_firmas_control = $array_firmas = array('bombero' => 'Bomberos', 'policia' => 'Policía', 'reina' => 'Reina', 'veterano' => 'Veteranos');

        $this->set_model_validation_rules($this->Formularios_registros_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $recaptcha = $this->input->post('g-recaptcha-response');
            if (!empty($recaptcha))
            {
                $response = $this->recaptcha->verifyResponse($recaptcha);
                if (isset($response['success']) and $response['success'] === TRUE)
                {
                    $this->db->trans_begin();
                    $trans_ok = TRUE;
                    $trans_ok &= $this->Formularios_registros_model->create(array(
                        'nombre' => $this->input->post('nombre'),
                        'apellido' => $this->input->post('apellido'),
                        'dni' => $this->input->post('dni'),
                        'telefono' => $this->input->post('telefono'),
                        'email' => $this->input->post('email'),
                        'calle' => $this->input->post('calle'),
                        'altura' => $this->input->post('altura'),
                        'localidad_id' => $this->input->post('localidad'),
                        'nombre_ninio' => $this->input->post('nombre_ninio'),
                        'apellido_ninio' => $this->input->post('apellido_ninio'),
                        'formulario_id' => 1
                            ), FALSE);

                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $registro_id = $this->Formularios_registros_model->get_row_id();
                        $attachment = $this->generar_certificado($this->input->post('nombre_ninio'), $this->input->post('apellido_ninio'), $this->input->post('firmas'));
                    }

                    if ($this->db->trans_status() && $trans_ok && !empty($attachment))
                    {

                        $this->db->trans_commit();
                        $this->session->set_flashdata('message', $this->Formularios_registros_model->get_msg());
                        $data['image'] = $attachment;
                        $data['titulo'] = "¡Muchas gracias!";
                        $this->load->view('publico/diplomas', $data);
                        exit();
                    }
                    else
                    {
                        $this->db->trans_rollback();
                        $error_msg = '<br />Se ha producido un error con la base de datos.';
                        if ($this->Formularios_registros_model->get_error())
                        {
                            $error_msg .= $this->Formularios_registros_model->get_error();
                        }
                    }
                }
                else
                {
                    $this->session->set_flashdata('error', '<br />Captcha inválido');
                }
            }
            else
            {
                $this->session->set_flashdata('error', '<br />Debe completar el captcha');
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['recaptcha_widget'] = $this->recaptcha->getWidgetInvisible(array('data-callback' => 'submitForm'));
        $data['recaptcha_script'] = $this->recaptcha->getScriptTag();
        $this->Formularios_registros_model->fields['localidad']['array'] = $array_localidad;
        $this->Formularios_registros_model->fields['firmas']['array'] = $array_firmas;
        $fake_registro = new stdClass();
        $fake_registro->nombre = '';
        $fake_registro->apellido = '';
        $fake_registro->dni = '';
        $fake_registro->telefono = '';
        $fake_registro->email = '';
        $fake_registro->calle = '';
        $fake_registro->altura = '';
        $fake_registro->localidad_id = '';
        $fake_registro->nombre_ninio = '';
        $fake_registro->apellido_ninio = '';
        $fake_registro->firmas = array('bombero', 'policia', 'reina', 'veterano');
        $data['fields'] = $this->build_fields($this->Formularios_registros_model->fields, $fake_registro);
        $data['titulo'] = $formulario->nombre;
        $this->load->view('publico/formularios', $data);
    }

    private function generar_certificado($nombre, $apellido, $firmas)
    {
        $image = imagecreatefrompng("img/generales/publico/formularios/diploma.png");
        $black = imagecolorallocate($image, 0, 0, 0);
        $font_path = realpath('css/fonts/Panton-Regular.ttf');
        $text = strtoupper("$nombre $apellido");

        // Get image Width and Height
        $image_width = imagesx($image);
        $image_height = imagesy($image);

        // Get Bounding Box Size
        $text_box = imagettfbbox(32, 0, $font_path, $text);

        // Get your Text Width and Height
        $text_width = $text_box[2] - $text_box[0];
        $text_height = $text_box[7] - $text_box[1];

        // Calculate coordinates of the text
        $start_x = ($image_width / 2) - ($text_width / 2);
        $start_y = 275;

        imagettftext($image, 32, 0, $start_x, $start_y, 0, $font_path, $text);

        if (!empty($firmas))
        {
            foreach ($firmas as $Firma)
            {
                if ($Firma === 'veterano')
                {
                    $firma = imagecreatefrompng("img/generales/publico/formularios/veterano.png");
                    imagecopy($image, $firma, 330, 500, 0, 0, 156, 145);
                }
                if ($Firma === 'policia')
                {
                    $firma = imagecreatefrompng("img/generales/publico/formularios/policia.png");
                    imagecopy($image, $firma, 475, 500, 0, 0, 156, 145);
                }
                if ($Firma === 'bombero')
                {
                    $firma = imagecreatefrompng("img/generales/publico/formularios/bombero.png");
                    imagecopy($image, $firma, 730, 500, 0, 0, 138, 145);
                }
                if ($Firma === 'reina')
                {
                    $firma = imagecreatefrompng("img/generales/publico/formularios/reina.png");
                    imagecopy($image, $firma, 860, 500, 0, 0, 138, 145);
                }
            }
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);
        ob_start();
        imagepng($image, NULL, 9, PNG_NO_FILTER);
        $i = ob_get_contents();
        $attachment = chunk_split(base64_encode($i));
        ob_clean();
        imagedestroy($image);

        return $attachment;
    }
}
