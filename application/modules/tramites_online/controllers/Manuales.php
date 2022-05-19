<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manuales extends MY_Controller {

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 27/04/2020
     * Modificado: 27/04/2020 (Leandro)
     */
    public function __construct() {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
        $this->grupos_admin = array('admin', 'tramites_online_admin', 'tramites_online_consulta_general');
        $this->grupos_publico = array('tramites_online_publico');
        $this->grupos_area = array('tramites_online_area');
        // Inicializaciones necesarias colocar acá.
    }

    public function index() {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_area, $this->grupos)) {
            $data['municipal'] = 'uploads/tramites_online/manuales/manual_municipal.pdf';
        } else {
            $data['publico'] = 'uploads/tramites_online/manuales/manual_tad.pdf';
        }

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Manual de Usuario';
        $data['title'] = TITLE . ' - Manuales';
        $data['accesos_esc'] = load_permisos_transferencias_escritorio($this->grupos);
        $this->load_template('tramites_online/manuales/content', $data);
    }

}
