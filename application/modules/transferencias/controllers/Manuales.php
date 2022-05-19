<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manuales extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 29/10/2018
	 * Modificado: 10/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'transferencias_municipal', 'transferencias_area', 'transferencias_publico', 'transferencias_consulta_general');
		$this->grupos_admin = array('admin', 'transferencias_consulta_general');
		$this->grupos_publico = array('transferencias_publico');
		$this->grupos_municipal = array('transferencias_municipal', 'transferencias_area');
		// Inicializaciones necesarias colocar acá.
	}

	public function index()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_municipal, $this->grupos))
		{
			$data['municipal'] = 'uploads/transferencias/manuales/manual_municipal.pdf';
		}
		else
		{
			$data['municipal'] = 'uploads/transferencias/manuales/manual_escribano.pdf';
		}

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Manual de Usuario';
		$data['title'] = TITLE . ' - Manuales';
		$data['accesos_esc'] = load_permisos_transferencias_escritorio($this->grupos);
		$this->load_template('transferencias/manuales/content', $data);
	}
}