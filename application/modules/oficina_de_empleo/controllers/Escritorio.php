<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 //editado por yoel grosso
	 * Creado: 10/10/2018
	 * Modificado: 10/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'oficina_empleo','oficina_empleo_general','user'); 
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
		$data['title_view'] = 'Módulo Oficina de empleo';  
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_oficina_de_empleo_escritorio($this->grupos);        //****esto redirecciona a application/helpers/permisos_helper.php
		$this->load_template('oficina_de_empleo/escritorio/content', $data); 
	}
}