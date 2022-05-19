<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 13/03/2017
	 * Modificado: 04/07/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user', 'asistencia_consulta_general');
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
		$data['title_view'] = 'Módulo Asistencia';
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_asistencia_escritorio($this->grupos);
		$this->load_template('asistencia/escritorio/content', $data);
	}
}