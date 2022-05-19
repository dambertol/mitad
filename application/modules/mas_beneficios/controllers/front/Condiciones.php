<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Condiciones extends MY_Controller {

    /**
     * Controlador de Condiciones
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 23/04/2020 (Leandro)
     */
    public function __construct() {
        $this->auth = FALSE;
        parent::__construct();
        // Inicializaciones necesarias colocar acá.
    }

    public function index() {
        $data['image'] = 'img/mas_beneficios/condiciones.jpg';
        $data['title'] = 'Términos y Condiciones';
        $this->load_template('mas_beneficios/front/condiciones/condiciones_content', $data);
    }

    protected function load_template($contenido = 'general', $datos = NULL) {
        $data['menu'] = $this->load->view('mas_beneficios/front/template/menu', $datos, TRUE);
        $data['content'] = $this->load->view($contenido, $datos, TRUE);
        $data['footer'] = $this->load->view('mas_beneficios/front/template/footer', $datos, TRUE);
        $this->load->view('mas_beneficios/front/template/template', $data);
    }

}
