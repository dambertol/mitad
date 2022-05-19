<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Deudas extends MY_Controller
{

    /**
     * Controlador de Deudas
     * Autor: Leandro
     * Creado: 29/01/2019
     * Modificado: 08/04/2021 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'major_deudas', 'major_consulta_general');
        $this->grupos_solo_consulta = array('major_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->array_dtri_Codigo_control = $array_dtri_Codigo = array(1 => 'Inmueble', 2 => 'Comercio', 7 => 'Cuenta Especial');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'dtri_Codigo' => array('label' => 'Tipo', 'input_type' => 'combo', 'id_name' => 'dtri_Codigo', 'type' => 'bselect', 'required' => TRUE),
            'trib_Cuenta' => array('label' => 'Padrón', 'maxlength' => '20', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $dtri_Codigo = (int) $this->input->post('dtri_Codigo');
            if ($dtri_Codigo === 7)
            {
                $trib_Cuenta = $this->input->post('trib_Cuenta');
            }
            else
            {
                $trib_Cuenta = (int) $this->input->post('trib_Cuenta');
            }

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

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

                $padron = json_decode($http_response_datos->getBody()->getContents());
                if (!empty($padron) && $dtri_Codigo === 1)
                {
                    $sup['TOTAL'][0] = 0;
                    $sup['CUBIERTA'][0] = 0;
                    foreach ($padron->superficies as $superficie)
                    {
                        $sup[$superficie->tips_Descripcion][$superficie->supe_Numero] = $superficie->supe_Superficie;
                    }

                    $sup_total = 0;
                    foreach ($sup['TOTAL'] as $sup_t)
                    {
                        $sup_total += $sup_t;
                    }
                    $sup_cubierta = 0;
                    foreach ($sup['CUBIERTA'] as $sup_c)
                    {
                        $sup_cubierta += $sup_c;
                    }
                    $padron->sup_total = $sup_total;
                    $padron->sup_cubierta = $sup_cubierta;
                }
                $data['padron'] = array($padron);
                $data['fecha'] = date_format(new DateTime(), 'd/m/y');
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

            if (!empty($padron))
            {
                try
                {
                    if ($dtri_Codigo === 1)
                    {
                        $http_response_deuda = $guzzleHttp->request('GET', "inmuebles/deudas", ['query' => ['id' => $trib_Cuenta]]);
                    }
                    else if ($dtri_Codigo === 2)
                    {
                        $http_response_deuda = $guzzleHttp->request('GET', "comercios/deudas", ['query' => ['id' => $trib_Cuenta]]);
                    }
                    else if ($dtri_Codigo === 7)
                    {
                        $http_response_deuda = $guzzleHttp->request('GET', "cuentas_especiales/deudas", ['query' => ['id' => $trib_Cuenta]]);
                    }

                    $deuda = json_decode($http_response_deuda->getBody()->getContents());
                    $data['deudas'] = $deuda->deudas;
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
            else
            {
                $error_msg = '<br>No se encontro el Padrón solicitado';
            }

            if (!empty($deuda) && !empty($padron))
            {
                if ($dtri_Codigo === 1)
                {
                    $data['title_view'] = 'Informe de deuda inmueble';
                    $data['title'] = TITLE . ' - Informe de deuda';
                    $data['css'] = 'css/major/major_deudas.css';
                    $this->load_template('major/deudas/deudas_informe_view', $data);
                }
                else if ($dtri_Codigo === 2)
                {
                    $data['title_view'] = 'Informe de deuda comercio';
                    $data['title'] = TITLE . ' - Informe de deuda comercio';
                    $data['css'] = 'css/major/major_deudas.css';
                    $this->load_template('major/deudas/deudas_informe_view_comercio', $data);
                }
                else
                {
                    $data['title_view'] = 'Informe de deuda cuenta especial';
                    $data['title'] = TITLE . ' - Informe de deuda cuenta especial';
                    $data['css'] = 'css/major/major_deudas.css';
                    $this->load_template('major/deudas/deudas_informe_view_especial', $data);
                }
                return;
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['dtri_Codigo']['array'] = $array_dtri_Codigo;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Consultar';
        $data['title_view'] = 'Consultar Deuda';
        $data['title'] = TITLE . ' - Consultar Deuda';
        $this->load_template('major/deudas/deudas_consultar', $data);
    }
}
