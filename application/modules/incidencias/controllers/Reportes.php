<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

	/**
	 * Controlador de Reportes
	 * Autor: Leandro
	 * Creado: 17/12/2019
	 * Modificado: 06/01/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('Areas_model');
		$this->grupos_permitidos = array('admin', 'incidencias_admin', 'incidencias_consulta_general');
		$this->grupos_solo_consulta = array('incidencias_consulta_general');
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
		$this->load_template('incidencias/reportes/reportes_listar', $data);
	}

	public function incidencias()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'area' => array('label' => 'Área', 'type' => 'date', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
				'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
		);

		$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
			$hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
			$hasta->add(new DateInterval('P1D'));
			$desde_sql = $desde->format('Y/m/d');
			$hasta_sql = $hasta->format('Y/m/d');
			$area = $this->input->post('area');

			$this->load->model('incidencias/Incidencias_model');
			$options['select'] = array('in_incidencias.id', 'in_incidencias.fecha_inicio', "CONCAT(areas.codigo, ' - ', areas.nombre) AS area", 'in_incidencias.contacto',
					'in_incidencias.telefono', 'in_categorias.descripcion AS categoria', 'in_sectores.descripcion AS sector', 'in_incidencias.detalle', "CONCAT(personas.apellido, ', ', personas.nombre) as usuario", 'in_incidencias.estado', 'in_incidencias.fecha_finalizacion', 'in_incidencias.resolucion');
			$options['join'] = array(
					array('type' => 'left', 'table' => 'areas', 'where' => 'in_incidencias.area_id=areas.id'),
					array('type' => 'left', 'table' => 'in_categorias', 'where' => 'in_incidencias.categoria_id=in_categorias.id'),
					array('type' => 'left', 'table' => 'in_sectores', 'where' => 'in_categorias.sector_id=in_sectores.id'),
					array('type' => 'left', 'table' => 'users', 'where' => 'in_incidencias.tecnico_id=users.id'),
					array('type' => 'left', 'table' => 'personas', 'where' => 'personas.id = users.persona_id')
			);
			if ($area !== 'Todas')
			{
				$options['where'][] = array('column' => 'in_incidencias.area_id', 'value' => $area);
			}
			if (!empty($desde))
			{
				$options['where'][] = array('column' => 'in_incidencias.fecha_inicio >=', 'value' => $desde_sql);
			}
			if (!empty($hasta))
			{
				$options['where'][] = array('column' => 'in_incidencias.fecha_inicio <', 'value' => $hasta_sql);
			}
			$options['return_array'] = TRUE;

			$print_data = $this->Incidencias_model->get($options);
			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()
						->setCreator("SistemaMLC")
						->setLastModifiedBy("SistemaMLC")
						->setTitle("Informe de Incidencias")
						->setDescription("Informe de Incidencias (Módulo Incidencias)");
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getColumnDimension('A')->setWidth(8);
				$sheet->getColumnDimension('B')->setWidth(20);
				$sheet->getColumnDimension('C')->setWidth(40);
				$sheet->getColumnDimension('D')->setWidth(20);
				$sheet->getColumnDimension('E')->setWidth(15);
				$sheet->getColumnDimension('F')->setWidth(35);
				$sheet->getColumnDimension('G')->setWidth(15);
				$sheet->getColumnDimension('H')->setWidth(60);
				$sheet->getColumnDimension('I')->setWidth(30);
				$sheet->getColumnDimension('J')->setWidth(15);
				$sheet->getColumnDimension('K')->setWidth(20);
				$sheet->getColumnDimension('L')->setWidth(60);
				$sheet->getStyle('A1:L1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array('N°', 'Fecha Inicio', 'Área', 'Contacto', 'Teléfono', 'Categoría', 'Sector', 'Detalle', 'Técnico', 'Estado', 'Fecha Finalización', 'Resolución')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2', TRUE);
				$sheet->setAutoFilter($sheet->calculateWorksheetDimension());
				$nombreArchivo = 'InformeIncidentes_' . date('Ymd');

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

		$fake_model->fields['area']['array'] = $array_area;
		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Incidencias';
		$data['title'] = TITLE . ' - Informe de Incidencias';
		$this->load_template('incidencias/reportes/reportes_content', $data);
	}
}