<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 05/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
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
		$data['title_view'] = 'Módulo Telefonía';
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_telefonia_escritorio($this->grupos);
		$data['css'][] = 'vendor/c3/c3.min.css';
		$data['js'][] = 'vendor/d3/d3.min.js';
		$data['js'][] = 'vendor/c3/c3.min.js';
		$this->load_template('telefonia/escritorio/content', $data);
	}

	private function graficos_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$db_param_equipos = array();
		$equipos = $this->db->query("
			SELECT tm_equipos.estado, COALESCE(COUNT(tm_equipos.id),0) as cantidad
			FROM tm_equipos
			GROUP BY tm_equipos.estado", $db_param_equipos)->result();

		$grafico_equipos = array();
		if (!empty($equipos))
		{
			foreach ($equipos as $Estado)
			{
				$grafico_equipos[] = array($Estado->estado, $Estado->cantidad);
			}
		}

		$db_param_lineas = array();
		$lineas = $this->db->query("
			SELECT tm_lineas.estado, COALESCE(COUNT(tm_lineas.id),0) as cantidad
			FROM tm_lineas
			GROUP BY tm_lineas.estado", $db_param_lineas)->result();

		$grafico_lineas = array();
		if (!empty($lineas))
		{
			foreach ($lineas as $Linea)
			{
				$grafico_lineas[] = array($Linea->estado, $Linea->cantidad);
			}
		}

		return array('grafico_equipos' => json_encode($grafico_equipos), 'grafico_lineas' => json_encode($grafico_lineas));
	}
}