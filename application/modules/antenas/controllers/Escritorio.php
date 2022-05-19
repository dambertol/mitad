<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 06/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'antenas_admin', 'antenas_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function index()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$data['graficos_data'] = $this->graficos_data();

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Módulo Antenas';
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_antenas_escritorio($this->grupos);
		$data['css'][] = 'vendor/c3/c3.min.css';
		$data['js'][] = 'vendor/d3/d3.min.js';
		$data['js'][] = 'vendor/c3/c3.min.js';
		$this->load_template('antenas/escritorio/content', $data);
	}

	private function graficos_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$db_param_estados = array();
		$estados = $this->db->query("
			SELECT an_torres.estado, COALESCE(COUNT(an_torres.id),0) as cantidad
			FROM an_torres
			GROUP BY an_torres.estado", $db_param_estados)->result();

		$grafico_estados = array();
		if (!empty($estados))
		{
			foreach ($estados as $Estado)
			{
				$grafico_estados[] = array($Estado->estado, $Estado->cantidad);
			}
		}

		return array('grafico_estados' => json_encode($grafico_estados));
	}
}