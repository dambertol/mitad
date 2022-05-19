<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitudes extends MY_Controller
{

	/**
	 * Controlador de Solicitudes
	 * Autor: Leandro
	 * Creado: 13/08/2019
	 * Modificado: 14/08/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'major_solicitudes', 'major_consulta_general');
		$this->grupos_solo_consulta = array('major_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar($ejercicio = NULL, $oficina = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (empty($ejercicio))
		{
			$ejercicio = date("Y");
		}
		if (empty($oficina))
		{
			$oficina = '17';
		}
		if (!ctype_digit($ejercicio) || !ctype_digit($oficina))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tableData = array(
				'columns' => array(
						array('label' => 'Fecha', 'data' => 'Comp_Ingreso', 'width' => 7, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Of Ej', 'data' => 'Comp_OficinaEjercicio', 'width' => 5, 'class' => 'dt-body-right'),
						array('label' => 'Of N°', 'data' => 'Comp_Oficina', 'width' => 5, 'class' => 'dt-body-right'),
						array('label' => 'N°', 'data' => 'Comp_Numero', 'width' => 4, 'class' => 'dt-body-right'),
						array('label' => 'Detalle', 'data' => 'Reco_Detalle', 'width' => 20),
						array('label' => 'Ú° Avance', 'data' => 'Estado', 'width' => 12),
						array('label' => 'Exp Ej', 'data' => 'expe_Ejercicio', 'width' => 5),
						array('label' => 'Exp N°', 'data' => 'expe_Numero', 'width' => 5),
						array('label' => 'Exp Descripción', 'data' => 'expe_Descripcion', 'width' => 30),
						array('label' => 'Items', 'data' => 'items', 'width' => 4),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'solicitudes_table',
				'server_side' => FALSE,
				'source_url' => "major/solicitudes/listar_data/$ejercicio/$oficina",
				'reuse_var' => TRUE,
				'initComplete' => "complete_solicitudes_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);

		$guzzleHttp = new GuzzleHttp\Client([
				'base_uri' => $this->config->item('rest_server2'),
				'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
				'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
		]);

		try
		{
			$http_response_oficinas = $guzzleHttp->request('GET', "comprobantes/oficinas", ['query' => ['ofi_OficinaEjercicio' => $ejercicio]]);
			$oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
		} catch (Exception $e)
		{
			$oficinas_major = NULL;
		}

		$array_oficinas = array();
		$array_ejercicios = array_combine(range(date("Y"), 2000), range(date("Y"), 2000));
		if (!empty($oficinas_major))
		{
			foreach ($oficinas_major as $Oficina)
			{
				$array_oficinas[$Oficina->ofi_Oficina] = "$Oficina->ofi_Oficina - $Oficina->ofi_Descripcion";
			}
		}

		$data['oficina_opt'] = $array_oficinas;
		$data['oficina_id'] = $oficina;
		$data['ejercicio_opt'] = $array_ejercicios;
		$data['ejercicio_id'] = $ejercicio;

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Solicitudes';
		$data['title'] = TITLE . ' - Solicitudes';
		$this->load_template('major/solicitudes/solicitudes_listar', $data);
	}

	public function listar_data($ejercicio = NULL, $oficina = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $ejercicio === NULL || !ctype_digit($ejercicio) || $oficina === NULL || !ctype_digit($oficina))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$guzzleHttp = new GuzzleHttp\Client([
				'base_uri' => $this->config->item('rest_server2'),
				'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
				'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
		]);

		try
		{
			$http_response_comprobante = $guzzleHttp->request('GET', "comprobantes/listar", ['query' => ['comp_OficinaEjercicio' => $ejercicio, 'comp_Oficina' => $oficina]]);
			$comprobantes = json_decode($http_response_comprobante->getBody()->getContents());
		} catch (Exception $e)
		{
			$comprobantes = NULL;
		}

		if (!empty($comprobantes))
		{
			$comp['data'] = $comprobantes;
			foreach ($comp['data'] as $i => $Comprobante)
			{
				$Comprobante->items = "";
				if (!empty($Comprobante->items_data))
				{
					foreach ($Comprobante->items_data as $item)
					{
						if (!empty($item->IPed_Cantidad) && $item->IPed_Cantidad > 0)
						{
							$Comprobante->items .= number_format($item->IPed_Cantidad) . " ";
						}
						$Comprobante->items .= $item->IPed_Descripcion . " ";
						if (!empty($item->IPed_Importe) && $item->IPed_Importe > 0)
						{
							$Comprobante->items .= "$ " . number_format($item->IPed_Importe);
						}
						$Comprobante->items .= "\n";
					}
				}
				$comp['data'][$i]->items = "<div class='btn btn-primary btn-xs' title='$Comprobante->items'><i class='fa fa-list'></i></div>";
				$comp['data'][$i]->ver = "<a href='major/solicitudes/ver/$Comprobante->Comp_OficinaEjercicio/$Comprobante->Comp_Oficina/$Comprobante->Comp_ComprobanteTipo/$Comprobante->Comp_Numero' title='Ver solicitud' class='btn btn-primary btn-xs'><i class='fa fa-search'></i></a>";
			}
			echo json_encode($comp);
		}
		else
		{
			echo json_encode(array('data' => array()));
		}
	}

	public function ver($ejercicio, $oficina, $tipo, $numero)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $ejercicio === NULL || !ctype_digit($ejercicio) || $oficina === NULL || !ctype_digit($oficina) || $tipo === NULL || !ctype_digit($tipo) || $numero === NULL || !ctype_digit($numero))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$guzzleHttp = new GuzzleHttp\Client([
				'base_uri' => $this->config->item('rest_server2'),
				'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
				'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
		]);

		try
		{
			$http_response_comprobante = $guzzleHttp->request('GET', "comprobantes/datos", ['query' => ['comp_ComprobanteTipo' => 3, 'comp_OficinaEjercicio' => $ejercicio, 'comp_Oficina' => $oficina, 'comp_Numero' => $numero]]);
			$comprobante = json_decode($http_response_comprobante->getBody()->getContents());
		} catch (Exception $e)
		{
			$comprobante = NULL;
		}

		$data['comprobante_desc'] = "n°$numero - Oficina $oficina/$ejercicio";
		$data['comprobante'] = $comprobante;
		$data['txt_btn'] = NULL;
		$data['title_view'] = "Ver Solicitud N°$numero - Oficina $oficina/$ejercicio";
		$data['title'] = TITLE . ' - Ver Solicitud';
		$this->load_template('major/solicitudes/solicitudes_abm', $data);
	}

	public function orden($ejercicio, $oficina, $tipo, $numero)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $ejercicio === NULL || !ctype_digit($ejercicio) || $oficina === NULL || !ctype_digit($oficina) || $tipo === NULL || !ctype_digit($tipo) || $numero === NULL || !ctype_digit($numero))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$guzzleHttp = new GuzzleHttp\Client([
				'base_uri' => $this->config->item('rest_server2'),
				'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
				'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
		]);

		try
		{
			$http_response_orden = $guzzleHttp->request('GET', "comprobantes/orden_compra", ['query' => ['ocom_ComprobanteTipo' => $tipo, 'ocom_OficinaEjercicio' => $ejercicio, 'ocom_Oficina' => $oficina, 'ocom_Numero' => $numero]]);
			$orden_compra = json_decode($http_response_orden->getBody()->getContents());
		} catch (Exception $e)
		{
			$orden_compra = NULL;
		}

		$data['orden_compra_desc'] = "N°$numero - Oficina $oficina/$ejercicio";
		$data['orden_compra'] = $orden_compra;
		$data['txt_btn'] = NULL;
		$data['title_view'] = "Ver Orden de Compra N°$numero - Oficina $oficina/$ejercicio";
		$data['title'] = TITLE . ' - Ver Orden de Compra';
		$this->load_template('major/solicitudes/orden_abm', $data);
	}
}