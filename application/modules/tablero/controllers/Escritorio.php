<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

	/**
	 * Controlador Escritorio
	 * Autor: Leandro
	 * Creado: 02/12/2019
	 * Modificado: 02/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'tablero_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function index()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

//		$this->output->enable_profiler(TRUE);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Módulo Tablero de Control';
		$data['title'] = TITLE . ' - Escritorio';
		$data['css'][] = 'vendor/c3/c3.min.css';
		$data['js'][] = 'vendor/d3/d3.min.js';
		$data['js'][] = 'vendor/c3/c3.min.js';
		$this->load_template('tablero/escritorio/content', $data);
	}

	public function liquidaciones_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		//INICIALIZO FECHAS
		$hoy = new DateTime();
		$ano_sql = $hoy->format('Y');
		$mes_sql = $hoy->format('m') - 1;

		//CONEXION REST_WEBSERVER
		$guzzleHttp = new GuzzleHttp\Client([
				'base_uri' => $this->config->item('rest_server2'),
				'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
				'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
		]);

		//LIQUIDACIONES
		try
		{
			$http_response_historico = $guzzleHttp->request('GET', "liquidaciones/resumen", ['query' => ['liqu_Anio' => $ano_sql, 'liqu_Mes' => $mes_sql]]);
			$resumen = json_decode($http_response_historico->getBody()->getContents());
		} catch (GuzzleHttp\Exception\ClientException $e)
		{
			$response_content = $e->getResponse()->getBody()->getContents();
			$response_content_json = json_decode($response_content);
			if (!empty($response_content_json->message))
			{
				$error_msg = '<br>' . $response_content_json->message;
			}
			else
			{
				$error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
			}
		} catch (Exception $e)
		{
			$error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
		}

		$grafico_cantidades = array();
		$grafico_importes = array();
		if (!empty($resumen))
		{
			foreach ($resumen as $R)
			{
				$grafico_cantidades[] = array($R->particion, $R->cantidad);
				$grafico_importes[] = array($R->particion, $R->importe);
			}
		}
		echo json_encode(array('grafico_cantidades' => $grafico_cantidades, 'grafico_importes' => $grafico_importes, 'fecha' => "$mes_sql/$ano_sql"));
		return;
	}

	public function recaudaciones_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		//INICIALIZO FECHAS
		$ini = new DateTime('first day of this month');
		$fin = clone $ini;
		$fin->add(new DateInterval('P1M'));
		$ini_sql = $ini->format('Ymd');
		$fin_sql = $fin->format('Ymd');

		//CONEXION REST_WEBSERVER
		$guzzleHttp = new GuzzleHttp\Client([
				'base_uri' => $this->config->item('rest_server2'),
				'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
				'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
		]);

		//RECAUDACIONES
		try
		{
			$http_response_general = $guzzleHttp->request('GET', "recaudaciones/resumen", ['query' => ['desde' => $ini_sql, 'hasta' => $fin_sql]]);
			$general = json_decode($http_response_general->getBody()->getContents());
		} catch (GuzzleHttp\Exception\ClientException $e)
		{
			$response_content = $e->getResponse()->getBody()->getContents();
			$response_content_json = json_decode($response_content);
			if (!empty($response_content_json->message))
			{
				$error_msg = '<br>' . $response_content_json->message;
			}
			else
			{
				$error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
			}
		} catch (Exception $e)
		{
			$error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
		}

		$grafico_recaudaciones = array();
		$fecha = new DateTime();
		$cant = 0;
		$max = 10;
		if (!empty($general))
		{
			foreach ($general as $G)
			{
				$fecha = new DateTime($G->fecha);
				if ($cant < $max)
				{
					$grafico_recaudaciones[$cant] = array($G->cuenta, $G->monto);
				}
				else
				{
					if (empty($grafico_recaudaciones[$max]))
					{
						$grafico_recaudaciones[$max] = array('OTRAS CUENTAS', $G->monto);
					}
					else
					{
						$grafico_recaudaciones[$max][1] += $G->monto;
					}
				}
				$cant++;
			}
		}
		echo json_encode(array('grafico_recaudaciones' => $grafico_recaudaciones, 'fecha' => $fecha->format('d/m/Y')));
		return;
	}

	public function transferencias_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		//INICIALIZO FECHAS
		$ini = new DateTime('first day of this month');
		$fin = clone $ini;
		$ini->sub(new DateInterval('P1M'));
		$fin->sub(new DateInterval('P1D'));
		$ini_sql = $ini->format('Y-m-d');
		$fin_sql = $fin->format('Y-m-d');

		$db_param = array($ini_sql, $fin_sql);
		$iniciadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(tr_tramites.id) as cantidad "
						. 'FROM tr_tramites '
						. 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? '
						. 'GROUP BY mes '
						. 'ORDER BY fecha_inicio ASC', $db_param)->result();

		if (empty($iniciadas_mes))
		{
			$iniciadas = 0;
		}
		else
		{
			$iniciadas = $iniciadas_mes[0]->cantidad;
		}

		$finalizadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_fin, '%m/%Y') as mes, COUNT(tr_tramites.id) as cantidad "
						. 'FROM tr_tramites '
						. 'WHERE DATE(fecha_fin) BETWEEN ? AND ? '
						. 'GROUP BY mes '
						. 'ORDER BY fecha_fin ASC', $db_param)->result();

		if (empty($finalizadas_mes))
		{
			$finalizadas = 0;
		}
		else
		{
			$finalizadas = $finalizadas_mes[0]->cantidad;
		}

		echo json_encode(array('iniciadas' => $iniciadas, 'finalizadas' => $finalizadas, 'fecha' => $ini->format('m/Y')));
		return;
	}

	public function turnero_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		//INICIALIZO FECHAS
		$hoy = new DateTime();
		$mañana = clone $hoy;
		$mañana->add(new DateInterval('P1D'));
		$hoy_sql = $hoy->format('Y-m-d');
		$mañana_sql = $mañana->format('Y-m-d');

		//CONEXION REST_WEBSERVER
		$guzzleHttp = new GuzzleHttp\Client([
				'base_uri' => $this->config->item('rest_server2'),
				'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
				'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
		]);

		//TURNERO
		try
		{
			$http_response_turnos = $guzzleHttp->request('GET', "turnos/resumen", ['query' => ['desde' => $hoy_sql, 'hasta' => $mañana_sql]]);
			$turnos = json_decode($http_response_turnos->getBody()->getContents());
		} catch (GuzzleHttp\Exception\ClientException $e)
		{
			$response_content = $e->getResponse()->getBody()->getContents();
			$response_content_json = json_decode($response_content);
			if (!empty($response_content_json->message))
			{
				$error_msg = '<br>' . $response_content_json->message;
			}
			else
			{
				$error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
			}
		} catch (Exception $e)
		{
			$error_msg = '<br>Error al conectar el servidor. Intente nuevamente más tarde';
		}
		
		$turnero_data = array('fecha' => $hoy->format('d/m/Y'));
		if (!empty($turnos))
		{
			foreach ($turnos as $Turno)
			{
				$turnero_data[$Turno->unidad][$Turno->estado] = array(
						'cantidad' => $Turno->cantidad,
						'tma' => $this->_convertTime($Turno->tma, 'second'),
						'tme' => $this->_convertTime($Turno->tme, 'second')
				);
			}
		}
		echo json_encode($turnero_data);
		return;
	}

	private function _convertTime($dec, $type = 'hour')
	{
		switch ($type)
		{
			case 'hour':
				$seconds = ($dec * 3600);
				$hours = floor($dec);
				break;
			case 'minute':
				$seconds = ($dec * 60);
				$hours = floor($dec / 60);
				break;
			case 'second':
				$seconds = $dec;
				$hours = floor($dec / 3600);
				break;
		}
		$seconds -= $hours * 3600;
		$minutes = floor($seconds / 60);
		$seconds -= $minutes * 60;
		return $this->_lz($hours) . ":" . $this->_lz($minutes) . ":" . $this->_lz(floor($seconds));
	}

	private function _lz($num)
	{
		return (strlen($num) < 2) ? "0{$num}" : $num;
	}

	public function vales_combustible_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('vales_combustible/Tipos_combustible_model');
		$tipos_combustible = $this->Tipos_combustible_model->get();
		if (empty($tipos_combustible))
		{
			show_error('No se encontró el Tipo de Combustible', 500, 'Registro no encontrado');
		}

		//INICIALIZO FECHAS
		$ini = new DateTime('first day of this month');
		$fin = clone $ini;
		$ini->sub(new DateInterval('P1M'));
		$fin->sub(new DateInterval('P1D'));
		$ini_sql = $ini->format('Y-m-d');
		$fin_sql = $fin->format('Y-m-d');

		//GRAFICO VALES POR TIPO COMBUSTIBLE
		$db_param_tipos = array($ini_sql, $fin_sql);
		$tipos = $this->db->query("
			SELECT vc_tipos_combustible.nombre as tipo, COALESCE(SUM(vc_vales.metros_cubicos),0) as cantidad
			FROM vc_vales
			LEFT JOIN vc_tipos_combustible ON vc_tipos_combustible.id = vc_vales.tipo_combustible_id 
			WHERE DATE(fecha) BETWEEN ? AND ? AND estado IN ('Creado', 'Impreso', 'Asignado')
			GROUP BY vc_tipos_combustible.nombre
			ORDER BY vc_tipos_combustible.id ", $db_param_tipos)->result();

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

		echo json_encode(array('grafico_tipos_combustible' => $grafico_tipos, 'fecha' => $ini->format('m/Y')));
		return;
	}
}