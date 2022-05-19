<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 19/07/2018
     * Modificado 16/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'mas_beneficios_control', 'mas_beneficios_publico', 'mas_beneficios_beneficiario', 'mas_beneficios_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Módulo Más Beneficios';
        $data['title'] = TITLE . ' - Escritorio';
        $data['accesos_esc'] = load_permisos_mas_beneficios_escritorio($this->grupos);
        $this->load_template('mas_beneficios/escritorio/content', $data);
    }
}
