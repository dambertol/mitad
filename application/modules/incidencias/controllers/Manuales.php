<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manuales extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 28/04/2020
     * Modificado: 28/04/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_admin = array('admin', 'incidencias_admin', 'incidencias_consulta_general');
        $this->grupos_tecnico = array('incidencias_user');
        $this->grupos_permitidos = array('admin', 'incidencias_admin', 'incidencias_user', 'incidencias_area', 'incidencias_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data['municipal'] = 'uploads/incidencias/manuales/manual_municipal.pdf';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Manual de Usuario';
        $data['title'] = TITLE . ' - Manuales';
        $data['accesos_esc'] = load_permisos_transferencias_escritorio($this->grupos);
        $this->load_template('incidencias/manuales/content', $data);
    }
}
