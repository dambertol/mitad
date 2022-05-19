<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

	/**
	 * Controlador de Reportes
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 06/01/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'obrador_user', 'obrador_consulta_general');
		$this->grupos_solo_consulta = array('obrador_consulta_general');
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
		$this->load_template('obrador/reportes/reportes_listar', $data);
	}

	public function stock()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$error_msg = NULL;
		$this->load->model('obrador/Articulos_model');
		$options['return_array'] = TRUE;
		$options['select'] = array(
				'A.id AS IdArticulo',
				'A.nombre AS Nombre',
				'A.marca AS Marca',
				'T.descripcion AS DescripTipo',
				'U.descripcion AS DescripUnidad',
				'A.medida AS Medida',
				'A.cant_real AS CantReal',
		);
		$options['from'] = 'ob_articulos A';
		$options['join'] = array(
				array('table' => 'ob_tipos_articulos T', 'where' => 'A.tipo_articulo_id=T.id'),
				array('table' => 'ob_tipos_unidades U', 'where' => 'A.tipo_unidad_id=U.id'),
		);
		$options['where'][] = 'A.cant_real>0';
		$options['sort_by'] = 'A.id';
		$print_data = $this->Articulos_model->get($options);

		if (!empty($print_data))
		{
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$spreadsheet->getProperties()->setTitle("Informe de Articulos-Stock")->setDescription("");
			$spreadsheet->setActiveSheetIndex(0);
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Informe de Artículos-Stock");
			$sheet->getColumnDimension('A')->setWidth(10);
			$sheet->getColumnDimension('B')->setWidth(30);
			$sheet->getColumnDimension('C')->setWidth(20);
			$sheet->getColumnDimension('D')->setWidth(20);
			$sheet->getColumnDimension('E')->setWidth(20);
			$sheet->getColumnDimension('F')->setWidth(20);
			$sheet->getColumnDimension('G')->setWidth(20);
			$sheet->getStyle('A1:G1')->getFont()->setBold(true);
			$sheet->fromArray(array(array('IdArticulo', 'Nombre', 'Marca', 'Descrip Tipo Art', 'Descrip Unidad', 'Medida', 'Cant Real')), NULL, 'A1');
			$sheet->fromArray($print_data, NULL, 'A2');
			$sheet->setAutoFilter('A1:G' . $sheet->getHighestRow());
			$nombreArchivo = 'InformeArticuloStock_' . date('Ymd');

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
			header("Cache-Control: max-age=0");

			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save('php://output');
			exit();
		}
		else
		{
			$error_msg = '<br />Sin Datos';
		}
	}

	public function stock_critico()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('obrador/Articulos_model');
		$options['return_array'] = TRUE;
		$options['select'] = array(
				'A.id AS IdArticulo',
				'A.nombre AS Nombre',
				'A.marca AS Marca',
				'T.descripcion AS DescripTipo',
				'U.descripcion AS DescripUnidad',
				'A.medida AS Medida',
				'A.cant_minima AS cantminima',
				'A.cant_real AS CantReal',
		);
		$options['from'] = 'ob_articulos A';
		$options['join'] = array(
				array('table' => 'ob_tipos_articulos T', 'where' => 'A.tipo_articulo_id=T.id'),
				array('table' => 'ob_tipos_unidades U', 'where' => 'A.tipo_unidad_id=U.id'),
		);
		$options['where'][] = 'A.cant_real< A.cant_minima';
		$options['sort_by'] = 'A.id';
		$print_data = $this->Articulos_model->get($options);

		if (!empty($print_data))
		{
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$spreadsheet->getProperties()->setTitle("Informe de Articulos-Stock Critico")->setDescription("");
			$spreadsheet->setActiveSheetIndex(0);
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle("Informe de Articulos-Stock Crit");
			$sheet->getColumnDimension('A')->setWidth(10);
			$sheet->getColumnDimension('B')->setWidth(30);
			$sheet->getColumnDimension('C')->setWidth(20);
			$sheet->getColumnDimension('D')->setWidth(20);
			$sheet->getColumnDimension('E')->setWidth(20);
			$sheet->getColumnDimension('F')->setWidth(10);
			$sheet->getColumnDimension('G')->setWidth(20);
			$sheet->getColumnDimension('H')->setWidth(20);
			$sheet->getStyle('A1:H1')->getFont()->setBold(true);
			$sheet->fromArray(array(array('IdArticulo', 'Nombre', 'Marca', 'Descrip Tipo Art', 'Descrip Unidad', 'Medida', 'Cant_Minima', 'Cant Real')), NULL, 'A1');
			$sheet->fromArray($print_data, NULL, 'A2');
			$sheet->setAutoFilter('A1:H' . $sheet->getHighestRow());
			$nombreArchivo = 'InformeArticuloStockCritico_' . date('Ymd');

			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
			header("Cache-Control: max-age=0");

			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save('php://output');
			exit();
		}
		else
		{
			$error_msg = '<br />Sin Datos';
		}
	}

	public function entregas()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
				'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
		);

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('obrador/Entregas_model');

			$hasta_date = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
			$hasta_date->add(new DateInterval('P1D'));

			$from_date = date_format(DateTime::createFromFormat('d/m/Y', $this->input->post('desde')), 'Y-m-d');
			$to_date = date_format($hasta_date, 'Y-m-d');

			$options['select'] = array(
					'E.id AS Codigo_entrega',
					'E.fecha AS Fecha_Entrega',
					'E.responsable AS Responsable',
					'A.nombre AS Nombre_Articulo',
					'DE.cantidad AS Cantidad_Entregada',
					'E.expediente AS Expediente',
					'E.destino AS Destino'
			);
			$options['from'] = 'ob_entregas E';
			$options['join'] = array(
					array('table' => 'ob_detalle_entregas DE', 'where' => 'E.id=DE.entrega_id'),
					array('table' => 'ob_articulos A', 'where' => 'DE.articulo_id=A.id')
			);
			$options['where'] = array("E.fecha BETWEEN '$from_date' AND '$to_date'");
			$options['sort_by'] = 'E.id';
			$options['return_array'] = TRUE;
			$print_data = $this->Entregas_model->get($options);

			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()->setTitle("Informe de Entregas")->setDescription("");
				$spreadsheet->setActiveSheetIndex(0);
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setTitle("Informe de Entregas");
				$sheet->getColumnDimension('A')->setWidth(20);
				$sheet->getColumnDimension('B')->setWidth(30);
				$sheet->getColumnDimension('C')->setWidth(25);
				$sheet->getColumnDimension('D')->setWidth(30);
				$sheet->getColumnDimension('E')->setWidth(25);
				$sheet->getColumnDimension('F')->setWidth(20);
				$sheet->getColumnDimension('G')->setWidth(30);
				$sheet->getStyle('A1:G1')->getFont()->setBold(true);
				$sheet->fromArray(array(array('Cod Entrega', 'Fecha Entrega', 'Responsable', 'Articulo', 'Cant Entregada', 'Expediente', 'Destino')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter('A1:G' . $sheet->getHighestRow());
				$nombreArchivo = 'InformeEntregas_' . date('Ymd');

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
				header("Cache-Control: max-age=0");

				$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			}
			else
			{
				$error_msg = '<br />Sin Datos';
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Entregas';
		$data['title'] = TITLE . ' - Informe de Entregas';
		$this->load_template('obrador/reportes/reportes_content', $data);
	}

	public function compras()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
				'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
		);

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('obrador/Compras_model');

			$hasta_date = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
			$hasta_date->add(new DateInterval('P1D'));

			$from_date = date_format(DateTime::createFromFormat('d/m/Y', $this->input->post('desde')), 'Y-m-d');
			$to_date = date_format($hasta_date, 'Y-m-d');

			$options['select'] = array(
					'C.id AS Codigo_Compra',
					'C.fecha_recepcion AS Fecha_Compra',
					'A.nombre AS Nombre_Articulo',
					'DC.cantidad AS Cantidad_Comprada',
					'DC.valor AS Valor',
					'(DC.cantidad*COALESCE(DC.valor,0)) as Total_Compra',
					'C.expediente AS Expediente',
			);
			$options['from'] = 'ob_compras C';
			$options['join'] = array(
					array('table' => 'ob_detalle_compras DC', 'where' => 'C.id=DC.compra_id'),
					array('table' => 'ob_articulos A', 'where' => 'DC.articulo_id=A.id'),
			);
			$options['where'] = array("C.fecha_recepcion BETWEEN '$from_date' AND '$to_date'");
			$options['sort_by'] = 'C.id';
			$options['return_array'] = TRUE;
			$print_data = $this->Compras_model->get($options);

			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()->setTitle("Informe de Compras")->setDescription("");
				$spreadsheet->setActiveSheetIndex(0);
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setTitle("Informe de Compras");
				$sheet->getColumnDimension('A')->setWidth(20);
				$sheet->getColumnDimension('B')->setWidth(30);
				$sheet->getColumnDimension('C')->setWidth(25);
				$sheet->getColumnDimension('D')->setWidth(25);
				$sheet->getColumnDimension('E')->setWidth(25);
				$sheet->getColumnDimension('F')->setWidth(20);
				$sheet->getColumnDimension('G')->setWidth(20);
				$sheet->getStyle('A1:G1')->getFont()->setBold(true);
				$sheet->fromArray(array(array('Cod Compra', 'Fecha Compra', 'Articulo', 'Cant comprada', 'Valor', 'Total Compra', ' Expediente')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter('A1:G' . $sheet->getHighestRow());
				$nombreArchivo = 'Informe_Compras_' . date('Ymd');

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
				header("Cache-Control: max-age=0");

				$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			}
			else
			{
				$error_msg = '<br />Sin Datos';
			}
		}

		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Compras';
		$data['title'] = TITLE . ' - Informe de Compras';
		$this->load_template('obrador/reportes/reportes_content', $data);
	}
}