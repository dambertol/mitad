<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 09/05/2019
	 * Modificado: 09/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'toner_admin', 'toner_consulta_general');
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
		$data['title_view'] = 'Módulo Toner';
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_toner_escritorio($this->grupos);
		$data['css'][] = 'vendor/c3/c3.min.css';
		$data['js'][] = 'vendor/d3/d3.min.js';
		$data['js'][] = 'vendor/c3/c3.min.js';
		$this->load_template('toner/escritorio/content', $data);
	}

	private function graficos_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		//INICIALIZO FECHAS
		$ini = new DateTime('first day of this month');
		$fin = clone $ini;
		$ini->sub(new DateInterval('P11M'));
		$fin->add(new DateInterval('P1M'));
		$fin->sub(new DateInterval('P1D'));
		$ini_sql = $ini->format('Y-m-d');
		$fin_sql = $fin->format('Y-m-d');

		$db_param = array($ini_sql, $fin_sql);
		$pedidos_mes = $this->db->query("SELECT DATE_FORMAT(fecha_solicitud, '%m/%Y') as mes, COUNT(gt_pedidos_consumibles.id) as cantidad "
						. 'FROM gt_pedidos_consumibles '
						. 'WHERE DATE(fecha_solicitud) BETWEEN ? AND ? '
						. 'GROUP BY mes '
						. 'ORDER BY fecha_solicitud ASC', $db_param)->result();

		$grafico_pedidos = array(array('x'), array('pedidos'));

		$pedidos_array = array();
		if (!empty($pedidos_mes))
		{
			foreach ($pedidos_mes as $Mes)
			{
				$pedidos_array[$Mes->mes] = $Mes->cantidad;
			}
		}

		$temp_ini = clone $ini;
		while ($temp_ini <= $fin)
		{
			$grafico_pedidos[0][] = $temp_ini->format('m/Y');
			$grafico_pedidos[1][] = !empty($pedidos_array[$temp_ini->format('m/Y')]) ? $pedidos_array[$temp_ini->format('m/Y')] : 0;
			$temp_ini->add(new DateInterval('P1M'));
		}

		$estados = $this->db->query("
			SELECT gt_pedidos_consumibles.estado, COALESCE(COUNT(gt_pedidos_consumibles.id),0) as cantidad
			FROM gt_pedidos_consumibles
			GROUP BY gt_pedidos_consumibles.estado")->result();

		$grafico_estados = array();
		if (!empty($estados))
		{
			foreach ($estados as $Estado)
			{
				$grafico_estados[] = array($Estado->estado, $Estado->cantidad);
			}
		}

		return array('grafico_pedidos' => json_encode($grafico_pedidos), 'grafico_estados' => json_encode($grafico_estados));
	}
}