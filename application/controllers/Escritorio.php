<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 26/01/2017
	 * Modificado: 26/11/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		// Inicializaciones necesarias colocar acá.
	}

	public function index()
	{
		if (sizeof($this->grupos) == 1 && $this->session->userdata('escritorio_general_acceso') !== TRUE)
		{
			$this->session->set_userdata('escritorio_general_acceso', TRUE);
			redirecciones_general_escritorio($this->grupos[0]);
		}

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Bienvenido al ' . TITLE;
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_escritorio($this->grupos);
		$this->load_template('template/content', $data);
	}
}