<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tablero extends MY_Controller
{

    /**
     * Controlador Tablero
     * Autor: Leandro
     * Creado: 01/04/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_consulta_general');
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
        $data['title_view'] = 'Tablero de Consultas';
        $data['title'] = TITLE . ' - Consultas';
        $this->load_template('tramites_online/tablero/content', $data);
    }
}
