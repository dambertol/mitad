<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 03/11/2017
	 * Modificado: 19/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'vales_combustible_autorizaciones', 'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_areas', 'vales_combustible_obrador', 'vales_combustible_estacion', 'vales_combustible_consulta_general');
		$this->grupos_admin = array('admin', 'vales_combustible_contaduria', 'vales_combustible_hacienda', 'transferencias_consulta_general');
		$this->grupos_areas = array('vales_combustible_areas');
		$this->grupos_autorizaciones = array('vales_combustible_autorizaciones', 'vales_combustible_obrador', 'vales_combustible_estacion');
		// Inicializaciones necesarias colocar acá.
	}

	public function index()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$data['dashboard'] = FALSE;
		if (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_areas, $this->grupos))
		{
			$data['graficos_data'] = $this->graficos_data();
			$data['graficos_areas_data'] = $this->graficos_areas_data();
		}
		else if (in_groups($this->grupos_autorizaciones, $this->grupos))
		{
			$data['graficos_autorizaciones_data'] = $this->graficos_autorizaciones_data();
		}

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Módulo Vales Combustible';
		$data['title'] = TITLE . ' - Escritorio';
		$data['accesos_esc'] = load_permisos_vales_combustible_escritorio($this->grupos);
		$data['css'][] = 'vendor/c3/c3.min.css';
		$data['js'][] = 'vendor/d3/d3.min.js';
		$data['js'][] = 'vendor/c3/c3.min.js';
		$this->load_template('vales_combustible/escritorio/content', $data);
	}

	private function graficos_data()
	{
		if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('vales_combustible/Tipos_combustible_model');
		$tipos_combustible = $this->Tipos_combustible_model->get();
		if (empty($tipos_combustible))
		{
			show_error('No se encontró el Tipo de Combustible', 500, 'Registro no encontrado');
		}

		$tipos_estados = array('Anulado', 'Asignado', 'Creado', 'Impreso', 'Pendiente');
		if (empty($tipos_estados))
		{
			show_error('No se encontró el Tipo de Estado', 500, 'Registro no encontrado');
		}

		//INICIALIZO FECHAS
		$ini = new DateTime('first day of this month');
		$fin = clone $ini;
		$ini->sub(new DateInterval('P11M'));
		$fin->add(new DateInterval('P1M'));
		$fin->sub(new DateInterval('P1D'));
		$ini_sql = $ini->format('Y-m-d');
		$fin_sql = $fin->format('Y-m-d');

		//GRAFICO VALES POR MES
		$db_param_vales = array($ini_sql, $fin_sql);
		if (in_groups($this->grupos_admin, $this->grupos))
		{
			$vales_mes = $this->db->query("SELECT DATE_FORMAT(fecha, '%m/%Y') as mes, COUNT(vc_vales.id) as cantidad "
							. 'FROM vc_vales '
							. "WHERE DATE(fecha) BETWEEN ? AND ? AND estado IN ('Creado', 'Impreso', 'Asignado') "
							. 'GROUP BY mes '
							. 'ORDER BY fecha ASC', $db_param_vales)->result();
		}
		else
		{
			$db_param_vales[] = $this->session->userdata('user_id');
			$vales_mes = $this->db->query("SELECT DATE_FORMAT(fecha, '%m/%Y') as mes, COUNT(vc_vales.id) as cantidad "
							. 'FROM vc_vales '
							. 'LEFT JOIN vc_usuarios_areas ON vc_usuarios_areas.area_id = vc_vales.area_id '
							. "WHERE DATE(fecha) BETWEEN ? AND ? AND estado IN ('Creado', 'Impreso', 'Asignado') AND vc_usuarios_areas.user_id = ? "
							. 'GROUP BY mes '
							. 'ORDER BY fecha ASC', $db_param_vales)->result();
		}
		$grafico_vales = array(array('x'), array('vales'));

		$vales_array = array();
		if (!empty($vales_mes))
		{
			foreach ($vales_mes as $Mes)
			{
				$vales_array[$Mes->mes] = $Mes->cantidad;
			}
		}

		$ini_while = clone $ini;
		while ($ini_while <= $fin)
		{
			$grafico_vales[0][] = $ini_while->format('m/Y');
			$grafico_vales[1][] = !empty($vales_array[$ini_while->format('m/Y')]) ? $vales_array[$ini_while->format('m/Y')] : 0;
			$ini_while->add(new DateInterval('P1M'));
		}

		//GRAFICO VALES POR ESTADO
		$db_param_estados = array($ini_sql, $fin_sql);
		if (in_groups($this->grupos_admin, $this->grupos))
		{
			$estados = $this->db->query("
			SELECT vc_vales.estado, COALESCE(COUNT(vc_vales.id),0) as cantidad
			FROM vc_vales
			WHERE DATE(fecha) BETWEEN ? AND ?  
			GROUP BY vc_vales.estado
			ORDER BY vc_vales.estado", $db_param_estados)->result();
		}
		else
		{
			$db_param_estados[] = $this->session->userdata('user_id');
			$estados = $this->db->query("
			SELECT vc_vales.estado, COALESCE(COUNT(vc_vales.id),0) as cantidad
			FROM vc_vales
			LEFT JOIN vc_usuarios_areas ON vc_usuarios_areas.area_id = vc_vales.area_id
			WHERE DATE(fecha) BETWEEN ? AND ? AND vc_usuarios_areas.user_id = ?
			GROUP BY vc_vales.estado
			ORDER BY vc_vales.estado", $db_param_estados)->result();
		}
		$grafico_estados = array();

		$estados_array = array();
		if (!empty($estados))
		{
			foreach ($estados as $Area)
			{
				$estados_array[$Area->estado] = array($Area->estado, $Area->cantidad);
			}
		}

		foreach ($tipos_estados as $Estado)
		{
			$grafico_estados[] = !empty($estados_array[$Estado]) ? $estados_array[$Estado] : array($Estado, 0);
		}

		//GRAFICO VALES POR COMBUSTIBLE
		$db_param_combustibles = array($ini_sql, $fin_sql);
		if (in_groups($this->grupos_admin, $this->grupos))
		{
			$combustibles = $this->db->query("SELECT DATE_FORMAT(fecha, '%m/%Y') as mes, SUM(vc_vales.metros_cubicos) as cantidad, vc_tipos_combustible.nombre as combustible "
							. 'FROM vc_vales '
							. 'LEFT JOIN vc_tipos_combustible ON vc_tipos_combustible.id = vc_vales.tipo_combustible_id '
							. "WHERE DATE(fecha) BETWEEN ? AND ? AND estado IN ('Creado', 'Impreso', 'Asignado') "
							. 'GROUP BY mes, combustible '
							. 'ORDER BY fecha ASC', $db_param_combustibles)->result();
		}
		else
		{
			$db_param_combustibles[] = $this->session->userdata('user_id');
			$combustibles = $this->db->query("SELECT DATE_FORMAT(fecha, '%m/%Y') as mes, SUM(vc_vales.metros_cubicos) as cantidad, vc_tipos_combustible.nombre as combustible "
							. 'FROM vc_vales '
							. 'LEFT JOIN vc_tipos_combustible ON vc_tipos_combustible.id = vc_vales.tipo_combustible_id '
							. 'LEFT JOIN vc_usuarios_areas ON vc_usuarios_areas.area_id = vc_vales.area_id '
							. "WHERE DATE(fecha) BETWEEN ? AND ? AND estado IN ('Creado', 'Impreso', 'Asignado') AND vc_usuarios_areas.user_id = ? "
							. 'GROUP BY mes, combustible '
							. 'ORDER BY fecha ASC', $db_param_combustibles)->result();
		}

		$grafico_combustible = array(array('x'));
		foreach ($tipos_combustible as $Tipo)
		{
			$grafico_combustible[] = array($Tipo->nombre);
		}

		$combustible_array = array();
		if (!empty($combustibles))
		{
			foreach ($combustibles as $Mes)
			{
				$combustible_array[$Mes->mes][$Mes->combustible] = $Mes->cantidad;
			}
		}

		$ini_while = clone $ini;
		while ($ini_while <= $fin)
		{
			$grafico_combustible[0][] = $ini_while->format('m/Y');
			$i = 1;
			foreach ($tipos_combustible as $Tipo)
			{
				$grafico_combustible[$i][] = !empty($combustible_array[$ini_while->format('m/Y')][$Tipo->nombre]) ? $combustible_array[$ini_while->format('m/Y')][$Tipo->nombre] : 0;
				$i++;
			}
			$ini_while->add(new DateInterval('P1M'));
		}

		//GRAFICO VALES POR TIPO COMBUSTIBLE
		$db_param_tipos = array($ini_sql, $fin_sql);
		if (in_groups($this->grupos_admin, $this->grupos))
		{
			$tipos = $this->db->query("
			SELECT vc_tipos_combustible.nombre as tipo, COALESCE(SUM(vc_vales.metros_cubicos),0) as cantidad
			FROM vc_vales
			LEFT JOIN vc_tipos_combustible ON vc_tipos_combustible.id = vc_vales.tipo_combustible_id 
			WHERE DATE(fecha) BETWEEN ? AND ? AND estado IN ('Creado', 'Impreso', 'Asignado')
			GROUP BY vc_tipos_combustible.nombre
			ORDER BY vc_tipos_combustible.id ", $db_param_tipos)->result();
		}
		else
		{
			$db_param_tipos[] = $this->session->userdata('user_id');
			$tipos = $this->db->query("
			SELECT vc_tipos_combustible.nombre as tipo, COALESCE(SUM(vc_vales.metros_cubicos),0) as cantidad
			FROM vc_vales
			LEFT JOIN vc_tipos_combustible ON vc_tipos_combustible.id = vc_vales.tipo_combustible_id 
			LEFT JOIN vc_usuarios_areas ON vc_usuarios_areas.area_id = vc_vales.area_id
			WHERE DATE(fecha) BETWEEN ? AND ? AND estado IN ('Creado', 'Impreso', 'Asignado') AND vc_usuarios_areas.user_id = ? 
			GROUP BY vc_tipos_combustible.nombre
			ORDER BY vc_tipos_combustible.id ", $db_param_tipos)->result();
		}
		$grafico_tipos = array();

		$tipos_array = array();
		if (!empty($tipos))
		{
			foreach ($tipos as $Tipo)
			{
				$tipos_array[$Tipo->tipo] = array($Tipo->tipo, $Tipo->cantidad);
			}
		}

		foreach ($tipos_combustible as $Tipo)
		{
			$grafico_tipos[] = !empty($tipos_array[$Tipo->nombre]) ? $tipos_array[$Tipo->nombre] : array($Tipo->nombre, 0);
		}

		return array(
				'grafico_vales' => json_encode($grafico_vales),
				'grafico_estados' => json_encode($grafico_estados),
				'grafico_combustible' => json_encode($grafico_combustible),
				'grafico_tipos' => json_encode($grafico_tipos),
				'tipos_combustible' => $tipos_combustible
		);
	}

	private function graficos_autorizaciones_data()
	{
		if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_autorizaciones, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('vales_combustible/Tipos_combustible_model');
		$tipos_combustible = $this->Tipos_combustible_model->get();
		if (empty($tipos_combustible))
		{
			show_error('No se encontró el Tipo de Combustible', 500, 'Registro no encontrado');
		}

		$tipos_estados = array('Anulado', 'Asignado', 'Creado', 'Impreso', 'Pendiente');
		if (empty($tipos_estados))
		{
			show_error('No se encontró el Tipo de Estado', 500, 'Registro no encontrado');
		}

		//INICIALIZO FECHAS
		$ini = new DateTime('first day of this month');
		$fin = clone $ini;
		$ini->sub(new DateInterval('P11M'));
		$fin->add(new DateInterval('P1M'));
		$fin->sub(new DateInterval('P1D'));
		$ini_sql = $ini->format('Y-m-d');
		$fin_sql = $fin->format('Y-m-d');

		//GRAFICO AUTORIZACIONES POR MES
		$db_param_autorizaciones = array($ini_sql, $fin_sql);

		$autorizaciones_mes = $this->db->query("SELECT DATE_FORMAT(fecha_autorizacion, '%m/%Y') as mes, COUNT(id) as cantidad "
						. 'FROM vc_autorizaciones '
						. "WHERE DATE(fecha_autorizacion) BETWEEN ? AND ? AND estado IN ('Autorizada', 'Cargada') "
						. 'GROUP BY mes '
						. 'ORDER BY fecha_autorizacion ASC', $db_param_autorizaciones)->result();

		$cargas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_autorizacion, '%m/%Y') as mes, COUNT(id) as cantidad "
						. 'FROM vc_autorizaciones '
						. "WHERE DATE(fecha_autorizacion) BETWEEN ? AND ? AND estado IN ('Cargada') "
						. 'GROUP BY mes '
						. 'ORDER BY fecha_autorizacion ASC', $db_param_autorizaciones)->result();

		$grafico_autorizaciones = array(array('x'), array('autorizaciones'), array('cargas'));

		$autorizaciones_array = array();
		if (!empty($autorizaciones_mes))
		{
			foreach ($autorizaciones_mes as $Mes)
			{
				$autorizaciones_array[$Mes->mes] = $Mes->cantidad;
			}
		}

		$cargas_array = array();
		if (!empty($cargas_mes))
		{
			foreach ($cargas_mes as $Mes)
			{
				$cargas_array[$Mes->mes] = $Mes->cantidad;
			}
		}

		$ini_while = clone $ini;
		while ($ini_while <= $fin)
		{
			$grafico_autorizaciones[0][] = $ini_while->format('m/Y');
			$grafico_autorizaciones[1][] = !empty($autorizaciones_array[$ini_while->format('m/Y')]) ? $autorizaciones_array[$ini_while->format('m/Y')] : 0;
			$grafico_autorizaciones[2][] = !empty($cargas_array[$ini_while->format('m/Y')]) ? $cargas_array[$ini_while->format('m/Y')] : 0;
			$ini_while->add(new DateInterval('P1M'));
		}

		//GRAFICO AUTORIZACIONES LITROS POR MES
		$db_param_autorizaciones_litros = array($ini_sql, $fin_sql);
		$cargas_litros_mes = $this->db->query("SELECT DATE_FORMAT(fecha_autorizacion, '%m/%Y') as mes, SUM(COALESCE(litros_cargados,0)) as cantidad "
						. 'FROM vc_autorizaciones '
						. "WHERE DATE(fecha_autorizacion) BETWEEN ? AND ? AND estado IN ('Autorizada', 'Cargada') "
						. 'GROUP BY mes '
						. 'ORDER BY fecha_autorizacion ASC', $db_param_autorizaciones_litros)->result();

		$grafico_autorizaciones_litros = array(array('x'), array('cargas'));


		$cargas_litros_array = array();
		if (!empty($cargas_litros_mes))
		{
			foreach ($cargas_litros_mes as $Mes)
			{
				$cargas_litros_array[$Mes->mes] = $Mes->cantidad;
			}
		}


		$ini_while = clone $ini;
		while ($ini_while <= $fin)
		{
			$grafico_autorizaciones_litros[0][] = $ini_while->format('m/Y');
			$grafico_autorizaciones_litros[1][] = !empty($cargas_litros_array[$ini_while->format('m/Y')]) ? $cargas_litros_array[$ini_while->format('m/Y')] : 0;
			$ini_while->add(new DateInterval('P1M'));
		}

		return array(
				'grafico_autorizaciones' => json_encode($grafico_autorizaciones),
				'grafico_autorizaciones_litros' => json_encode($grafico_autorizaciones_litros)
		);
	}

	private function graficos_areas_data()
	{
		if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_areas, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('vales_combustible/Cupos_combustible_model');
		$this->load->model('vales_combustible/Tipos_combustible_model');
		$this->load->model('vales_combustible/Usuarios_areas_model');
		$this->load->model('vales_combustible/Vales_model');

		$tipos_combustible = $this->Tipos_combustible_model->get();
		if (empty($tipos_combustible))
		{
			return NULL;
		}

		$usuarios_area = $this->Usuarios_areas_model->get(array(
				'user_id' => $this->session->userdata('user_id'),
				'join' => array(
						array('areas', 'areas.id = vc_usuarios_areas.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
				)
		));
		if (empty($usuarios_area))
		{
			return NULL;
		}

		$fecha = new DateTime();
		$vencimiento = clone $fecha;
		$vencimiento->add(new DateInterval('P7D'));

		$tmp_cupos = array();
		foreach ($usuarios_area as $Area)
		{
			foreach ($tipos_combustible as $Tipo)
			{
				$cupos_combustible = $this->Cupos_combustible_model->get(array(
						'select' => array("(metros_cubicos + (CASE WHEN ampliacion_vencimiento >= '" . $fecha->format('Y-m-d') . "' THEN ampliacion ELSE 0 END)) as total"),
						'area_id' => $Area->area_id,
						'tipo_combustible_id' => $Tipo->id,
						'fecha_inicio <=' => $fecha->format('Y-m-d'),
						'sort_by' => 'fecha_inicio DESC'
				));
				if (!empty($cupos_combustible))
				{
					//SEMANAL
					$cupo_semanal = $cupos_combustible[0]->total;
					$ini_sem = clone $fecha;
					$ini_sem->modify('this week');
					$fin_sem = clone $fecha;
					$fin_sem->modify('this week +6 days');
					$ini_sem_sql = $ini_sem->format('Y-m-d');
					$fin_sem_sql = $fin_sem->format('Y-m-d');
					$vales_oficina_semanal = $this->Vales_model->get(array(
							'area_id' => $Area->area_id,
							'tipo_combustible_id' => $Tipo->id,
							'fecha >=' => $ini_sem_sql,
							'fecha <=' => $fin_sem_sql,
							'estado !=' => 'Anulado'
					));
					$cupo_semanal_usado = 0;
					if (!empty($vales_oficina_semanal))
					{
						foreach ($vales_oficina_semanal as $Vale)
						{
							$cupo_semanal_usado += $Vale->metros_cubicos;
						}
					}

					//MENSUAL
					$cupo_mensual = $cupos_combustible[0]->total * 4;
					$ini_mes = clone $fecha;
					$ini_mes->modify('first day of this month');
					$fin_mes = clone $fecha;
					$fin_mes->modify('last day of this month');
					$ini_mes_sql = $ini_mes->format('Y-m-d');
					$fin_mes_sql = $fin_mes->format('Y-m-d');
					$vales_oficina = $this->Vales_model->get(array(
							'area_id' => $Area->area_id,
							'tipo_combustible_id' => $Tipo->id,
							'fecha >=' => $ini_mes_sql,
							'fecha <=' => $fin_mes_sql,
							'estado !=' => 'Anulado'
					));
					$cupo_mensual_usado = 0;
					if (!empty($vales_oficina))
					{
						foreach ($vales_oficina as $Vale)
						{
							$cupo_mensual_usado += $Vale->metros_cubicos;
						}
					}

					$tmp_cupos[$Area->area_id][$Tipo->id] = array(
							'area_id' => $Area->area_id,
							'area_nombre' => $Area->area,
							'tipo_id' => $Tipo->id,
							'tipo_nombre' => $Tipo->nombre,
							'cupo_semanal' => $cupo_semanal,
							'cupo_semanal_usado' => $cupo_semanal_usado,
							'cupo_mensual' => $cupo_mensual,
							'cupo_mensual_usado' => $cupo_mensual_usado
					);
				}
			}
		}

		return $tmp_cupos;
	}
}