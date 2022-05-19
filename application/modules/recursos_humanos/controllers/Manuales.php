<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manuales extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 29/10/2018
	 * Modificado: 07/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function index()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$data['user'] = 'uploads/recursos_humanos/manuales/manual_user.pdf';
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Manual de Usuario';
		$data['title'] = TITLE . ' - Manuales';
		$data['accesos_esc'] = load_permisos_transferencias_escritorio($this->grupos);
		$this->load_template('recursos_humanos/manuales/content', $data);
	}
}