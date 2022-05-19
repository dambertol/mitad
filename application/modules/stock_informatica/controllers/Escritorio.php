<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 18/02/2019
	 * Modificado: 18/02/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'stock_informatica_user', 'stock_informatica_consulta_general');
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
		$data['title_view'] = 'Módulo Stock Informática';
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_stock_informatica_escritorio($this->grupos);
		$this->load_template('stock_informatica/escritorio/content', $data);
	}
}