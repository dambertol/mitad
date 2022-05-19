<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

	/**
	 * Controlador de Reportes
	 * Autor: Leandro
	 * Creado: 25/03/2019
	 * Modificado: 06/01/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'antenas_admin', 'antenas_consulta_general');
		$this->grupos_solo_consulta = array('antenas_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de reportes';
		$data['title'] = TITLE . ' - Reportes';
		$this->load_template('antenas/reportes/reportes_listar', $data);
	}

	public function antenas()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('antenas/Antenas_model');
		$options['return_array'] = TRUE;
		$options['select'] = array(
				'A.id AS IdAntena',
				'A.descripcion AS DescripcionAntena',
				'A.observaciones AS ObservacionesAntena',
				"AP.nombre + ' ('+AP.cuit+')' AS ProveedorAntena",
				'T.id AS IdTorre',
				'T.servicio AS ServicioTorre',
				'T.caracteristicas AS CaracteristicasTorre',
				'T.observaciones AS ObservacionesTorre',
				'T.padron AS PadronTorre',
				'T.calle AS CalleTorre',
				'T.latitud AS LatitudTorre',
				'T.longitud AS LongitudTorre',
				'T.zonificacion AS ZonificacionTorre',
				'T.entorno AS EntornoTorre',
				"TP.nombre + ' ('+TP.cuit+')' AS ProveedorTorre",
				'L.nombre AS DistritoTorre',
		);
		$options['from'] = 'an_antenas A';
		$options['join'] = array(
				array('table' => 'an_torres T', 'where' => 'A.torre_id = T.id', 'type' => 'RIGHT'),
				array('table' => 'an_proveedores AP', 'where' => 'A.proveedor_id = AP.id', 'type' => 'LEFT'),
				array('table' => 'an_proveedores TP', 'where' => 'T.proveedor_id = TP.id', 'type' => 'LEFT'),
				array('table' => 'localidades L', 'where' => 'T.distrito_id = L.id', 'type' => 'LEFT')
		);
		$options['sort_by'] = 'A.id, T.id';
		$print_data = $this->Antenas_model->get($options);

		if (!empty($print_data))
		{
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$spreadsheet->getProperties()
					->setCreator("SistemaMLC")
					->setLastModifiedBy("SistemaMLC")
					->setTitle("Informe de Antenas")
					->setDescription("Informe de Antenas (Módulo Antenas)");
			$spreadsheet->setActiveSheetIndex(0);

			$sheet = $spreadsheet->getActiveSheet();
			$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
			$sheet->setTitle("Informe de Antenas");

			$sheet->getColumnDimension('A')->setWidth(5);
			$sheet->getColumnDimension('E')->setWidth(5);
			$sheet->getStyle('A1:P2')->getFont()->setBold(true);
			$sheet->fromArray(array(
					array('Antena', '', '', '',
							'Torre', '', '', '', '', '', '', '', '', '', '', ''),
					array(
							'Id', 'Descripción', 'Observaciones', 'Proveedor',
							'Id', 'Servicio', 'Características', 'Observaciones', 'Padron',
							'Calle', 'Latitud', 'Longitud', 'Zonificación', 'Entorno',
							'Proveedor', 'Distrito')
					), NULL, 'A1');
			$sheet->fromArray($print_data, NULL, 'A3');
			$sheet->setAutoFilter('A2:P' . $sheet->getHighestRow());
			$sheet->mergeCells('A1:D1');
			$sheet->mergeCells('E1:P1');
			$sheet->getStyle('A1:P1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$nombreArchivo = 'InformeAntenas_' . date('Ymd');

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
			header("Cache-Control: max-age=0");

			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save('php://output');
			exit();
		}
		else
		{
			$this->session->set_flashdata('error', '<br />Sin Datos');
		}

		redirect('antenas/reportes/listar', 'refresh');
	}

	public function torres()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('antenas/Torres_model');
		$options['return_array'] = TRUE;
		$options['select'] = array(
				'T.id',
				'T.sitio',
				'T.expediente_numero',
				'T.expediente_ejercicio',
				'T.servicio',
				"P.nombre + ' ('+P.cuit+')' AS Proveedor",
				'T.latitud',
				'T.longitud',
				'T.calle',
				'L.nombre AS DistritoTorre',
				'T.padron',
				'T.nomenclatura',
				'T.estado',
				'T.caracteristicas',
				'T.observaciones'
		);
		$options['from'] = 'an_torres T';
		$options['join'] = array(
				array('table' => 'an_proveedores P', 'where' => 'T.proveedor_id  =P.id', 'type' => 'LEFT'),
				array('table' => 'localidades L', 'where' => 'T.distrito_id = L.id', 'type' => 'LEFT')
		);
		$options['sort_by'] = 'T.id';
		$print_data = $this->Torres_model->get($options);

		if (!empty($print_data))
		{
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$spreadsheet->getProperties()
					->setCreator("SistemaMLC")
					->setLastModifiedBy("SistemaMLC")
					->setTitle("Informe de Torres")
					->setDescription("Informe de Torres (Módulo Antenas)");
			$spreadsheet->setActiveSheetIndex(0);

			$sheet = $spreadsheet->getActiveSheet();
			$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
			$sheet->setTitle("Informe de Torres");

			$sheet->getColumnDimension('A')->setWidth(5);
			$sheet->getColumnDimension('B')->setWidth(10);
			$sheet->getColumnDimension('C')->setWidth(10);
			$sheet->getColumnDimension('D')->setWidth(10);
			$sheet->getColumnDimension('E')->setWidth(35);
			$sheet->getColumnDimension('F')->setWidth(40);
			$sheet->getColumnDimension('G')->setWidth(15);
			$sheet->getColumnDimension('H')->setWidth(15);
			$sheet->getColumnDimension('I')->setWidth(40);
			$sheet->getColumnDimension('J')->setWidth(25);
			$sheet->getColumnDimension('K')->setWidth(14);
			$sheet->getColumnDimension('L')->setWidth(28);
			$sheet->getColumnDimension('M')->setWidth(15);
			$sheet->getColumnDimension('N')->setWidth(40);
			$sheet->getColumnDimension('O')->setWidth(60);
			$sheet->getStyle('A1:O1')->getFont()->setBold(true);
			$sheet->fromArray(array(array('Id', 'Sitio', 'Exp N°', 'Exp Ejer.', 'Servicio', 'Proveedor', 'Latitud', 'Longitud', 'Calle', 'Distrito', 'Padrón', 'Nomenclatura', 'Estado', 'Características', 'Observaciones')), NULL, 'A1');
			$sheet->fromArray($print_data, NULL, 'A2');
			$sheet->setAutoFilter('A1:O' . $sheet->getHighestRow());
			$nombreArchivo = 'InformeTorres_' . date('Ymd');

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
			header("Cache-Control: max-age=0");

			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save('php://output');
			exit();
		}
		else
		{
			$this->session->set_flashdata('error', '<br />Sin Datos');
		}

		redirect('antenas/reportes/listar', 'refresh');
	}
}