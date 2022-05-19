<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{

	/**
	 * Controlador Ajax
	 * Autor: Leandro
	 * Creado: 13/03/2017
	 * Modificado: 14/11/2018 (Leandro)
	 */



    public function __construct()
    {
        parent::__construct();
        $this->grupos_ajax = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
        $this->grupos_ajax_publico = array('tramites_online_publico');
        // Inicializaciones necesarias colocar acá.
    }

    public function buscar_aptitud_urbanistica()
    {
        if (!in_groups($this->grupos_ajax, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('zona', 'Zona', 'required');
        if ($this->form_validation->run() === TRUE)
        {
            $zona = $this->input->post('zona');

            $this->load->model('Cos_model');
            // BUSCA INFO DE APTITUD URBANISTICA
            $info = $this->Cos_model->get(array('zona' => $zona));

            if (empty($info))
            {
                $datos['no_data'] = TRUE;
            }
            else
            {
                $datos['info'] = $info[0];
            }
        }
        else
        {
            $datos['error'] = 'Debe ingresar una zona válida';
        }
        echo json_encode($datos);
    }


    public function buscar_inmueble()
    {
        if (!in_groups($this->grupos_ajax, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('nomenclatura', 'Nomenclatura', 'required|is_natural|min_length[20]|max_length[20]');
        if ($this->form_validation->run() === TRUE)
        {
            $nomenclatura = $this->input->post('nomenclatura');

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);
            $data = [];
            try
            {
                $http_response_inmueble = $guzzleHttp->request('GET', "inmuebles/datos_nomenclatura", ['query' => ['nomenclatura' => $nomenclatura]]);
                $inmueble = json_decode($http_response_inmueble->getBody()->getContents());
            } catch (Exception $e)
            {
                $inmueble = NULL;
            }

            if (!empty($inmueble))
            {
                $http_response_deuda = $guzzleHttp->request('GET', "inmuebles/deudas_procesadas", ['query' => ['id_desde' => $inmueble->trib_Cuenta, 'id_hasta' => $inmueble->trib_Cuenta, 'hasta' => date_format(new DateTime(), ('Y-m-d'))]]);
                $deuda = json_decode($http_response_deuda->getBody()->getContents());
                if (!empty($deuda))
                {
                    $inmueble->deuda = $deuda->{$inmueble->trib_Cuenta}->total;
                }
                else
                {
                    $inmueble->deuda = 0;
                }
                $inmueble->consulta = (new DateTime())->format('d/m/Y');
                $data['inmueble'] = $inmueble;
            }
            else
            {
                $data['error'] = 'Inmueble no encontrado';
            }
        }
        else
        {
            $data['error'] = 'Debe ingresar una nomenclatura válida';
        }
        echo json_encode($data);
    }

    public function buscar_procesos_oficina()
    {
        if (!in_groups($this->grupos_ajax, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('oficina_id', 'Oficina', 'required|integer');
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('tramites_online/Iniciadores_model');
            $this->load->model('tramites_online/Procesos_model');
            if (in_groups($this->grupos_ajax_publico, $this->grupos))
            {
                // BUSCA EL INICIADOR (PERSONA) ASOCIADA AL USUARIO ACTUAL
                $persona = $this->Iniciadores_model->get(array(
                    'select' => array('to2_iniciadores.tipo_id'),
                    'join' => array(
                        array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT'),
                        array('users', 'users.persona_id = personas.id')
                    ),
                    'where' => array('users.id = ' . $this->session->userdata('user_id'))
                ));
                if (!empty($persona))
                {
                    $procesos = $this->Procesos_model->get(
                            array(
                                'join' => array(
                                    array('to2_procesos_iniciadores', "to2_procesos_iniciadores.proceso_id = to2_procesos.id AND to2_procesos_iniciadores.iniciador_tipo_id = {$persona[0]->tipo_id}")
                                ),
                                'oficina_id' => $this->input->post('oficina_id'),
                                'visibilidad' => 'Público'
                            )
                    );
                }
            }
            else
            {
                $procesos = $this->Procesos_model->get(
                        array(
                            'oficina_id' => $this->input->post('oficina_id')
                        )
                );
            }
            if (empty($procesos))
            {
                $datos['no_data'] = TRUE;
            }
            else
            {
                $datos['procesos'] = $procesos;
            }
        }
        else
        {
            $datos['no_data'] = TRUE;
        }

        echo json_encode($datos);
    }

    /**
     * Buscador
     */
    public function search() {

        $this->load->model('tramites_online/Procesos_model');
        $query = $this->input->get('q');
        $data = array();

        $this->db->select('*');
        $this->db->from('to2_procesos');
        $this->db->like('nombre', $query);
        $ds = $this->db->get()->result_array();

        foreach ($ds as $d) {
            $data[] = array(
                "id" => $d['id'],
                "text" => $d['nombre'],
            );
        }

        echo json_encode($data);
    }

}
