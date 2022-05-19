<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 19/07/2018
     * Modificado 22/12/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'lujan_pass_control', 'lujan_pass_publico', 'lujan_pass_beneficiario', 'lujan_pass_consulta_general');
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
        $data['title_view'] = 'Módulo Luján Pass';
        $data['title'] = TITLE . ' - Escritorio';
        $data['accesos_esc'] = load_permisos_lujan_pass_escritorio($this->grupos);
        $this->load_template('lujan_pass/escritorio/content', $data);
    }
}
