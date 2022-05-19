<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 10/10/2018
	 * Modificado: 10/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_areas', 'notificaciones_notificadores', 'notificaciones_control');
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
		$data['title_view'] = 'Módulo Notificaciones';
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_notificaciones_escritorio($this->grupos);
		$this->load_template('notificaciones/escritorio/content', $data);
	}
}