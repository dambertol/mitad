<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Programa extends MY_Controller
{

    /**
     * Controlador de Programa
     * Autor: Leandro
     * Creado: 22/08/2018
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
        $data['error'] = json_encode((!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error')));
        $data['message'] = json_encode($this->session->flashdata('message'));

        $data['image'] = 'img/lujan_pass/home.jpg';
        $data['title'] = 'Programa';
        $data['usuario_logueado'] = $this->ion_auth->logged_in();
        $this->load_template('lujan_pass/front/programa/programa_content', $data);
    }

    protected function load_template($contenido = 'general', $datos = NULL)
    {
        $data['menu'] = $this->load->view('lujan_pass/front/template/menu', $datos, TRUE);
        $data['content'] = $this->load->view($contenido, $datos, TRUE);
        $data['footer'] = $this->load->view('lujan_pass/front/template/footer', $datos, TRUE);
        $this->load->view('lujan_pass/front/template/template', $data);
    }
}
