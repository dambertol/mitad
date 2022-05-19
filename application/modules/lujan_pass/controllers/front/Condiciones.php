<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Condiciones extends MY_Controller
{

    /**
     * Controlador de Condiciones
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 05/01/2021 (Leandro)
     */
    public function __construct()
    {
        $this->auth = FALSE;
        parent::__construct();
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        $data['image'] = 'img/lujan_pass/condiciones.jpg';
        $data['title'] = 'Términos y Condiciones';
        $data['usuario_logueado'] = $this->ion_auth->logged_in();
        $this->load_template('lujan_pass/front/condiciones/condiciones_content', $data);
    }

    protected function load_template($contenido = 'general', $datos = NULL)
    {
        $data['menu'] = $this->load->view('lujan_pass/front/template/menu', $datos, TRUE);
        $data['content'] = $this->load->view($contenido, $datos, TRUE);
        $data['footer'] = $this->load->view('lujan_pass/front/template/footer', $datos, TRUE);
        $this->load->view('lujan_pass/front/template/template', $data);
    }
}
