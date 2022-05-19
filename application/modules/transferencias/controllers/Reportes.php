<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

	/**
	 * Controlador de Reportes
	 * Autor: Leandro
	 * Creado: 01/04/2019
	 * Modificado: 06/01/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'transferencias_municipal', 'transferencias_consulta_general');
		$this->grupos_solo_consulta = array('transferencias_consulta_general');
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
		$this->load_template('transferencias/reportes/reportes_listar', $data);
	}

	public function tramites()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('transferencias/Tramites_model');

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'desde' => array('label' => 'Inicio Trámite Desde', 'type' => 'date', 'required' => TRUE),
				'hasta' => array('label' => 'Inicio Trámite Hasta', 'type' => 'date', 'required' => TRUE)
		);

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
			$hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

			$options['select'] = array(
					'tr_tramites.id',
					'tr_tramites.fecha_inicio',
					'tr_tramites_tipos.nombre as tipo',
					"CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil,  ')') as escribano",
					'tr_inmuebles.padron',
					'tr_inmuebles.nomenclatura',
					'tr_oficinas.nombre as oficina',
					'tr_estados.nombre as estado',
					'tr_pases.fecha as ultimo_mov',
					'tr_tramites.fecha_fin',
					'tr_tramites.escritura_nro',
					'tr_tramites.escritura_foja',
					'tr_tramites.escritura_fecha',
					'tr_tramites.transferencia_nro',
					'tr_tramites.transferencia_eje',
					'tr_tramites.observaciones',
					'TR.id as relacionado'
			);
			$options['join'] = array(
					array('type' => 'left', 'table' => 'tr_tramites_tipos', 'where' => 'tr_tramites_tipos.id = tr_tramites.tipo_id'),
					array('type' => 'left', 'table' => 'tr_pases', 'where' => 'tr_pases.tramite_id = tr_tramites.id'),
					array('type' => 'left outer', 'table' => 'tr_pases P', 'where' => 'P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha'),
					array('type' => 'left', 'table' => 'tr_estados', 'where' => 'tr_estados.id = tr_pases.estado_destino_id'),
					array('type' => 'left', 'table' => 'tr_oficinas', 'where' => 'tr_oficinas.id = tr_estados.oficina_id'),
					array('type' => 'left', 'table' => 'tr_escribanos', 'where' => 'tr_escribanos.id = tr_tramites.escribano_id'),
					array('type' => 'left', 'table' => 'personas', 'where' => 'personas.id = tr_escribanos.persona_id'),
					array('type' => 'left', 'table' => 'tr_inmuebles', 'where' => 'tr_inmuebles.id = tr_tramites.inmueble_id'),
					array('type' => 'left', 'table' => 'tr_tramites TR', 'where' => 'TR.id = tr_tramites.relacionado_id'),
			);
			$options['where'] = array('P.id IS NULL');
			$options['fecha_inicio >='] = $desde->format('Y-m-d');
			$hasta->add(new DateInterval('P1D'));
			$options['fecha_inicio <'] = $hasta->format('Y-m-d');
			$options['sort_by'] = 'tr_tramites.id';
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->Tramites_model->get($options);

			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()
						->setCreator("SistemaMLC")
						->setLastModifiedBy("SistemaMLC")
						->setTitle("Informe de Trámites")
						->setDescription("Informe de Trámites (Módulo Transferencias)");
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
				$sheet->setTitle("Informe de Trámites");

				$BStyle1 = array(
						'borders' => array(
								'bottom' => array(
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
								)
						)
				);
				$sheet->getStyle('A1:Q1')->applyFromArray($BStyle1);

				foreach ($print_data as $key => $value)
				{
					$print_data[$key]['fecha_inicio'] = !empty($value['fecha_inicio']) ? date_format(new DateTime($value['fecha_inicio']), 'd-m-Y') : '';
					$print_data[$key]['fecha_fin'] = !empty($value['fecha_fin']) ? date_format(new DateTime($value['fecha_fin']), 'd-m-Y') : '';
					$print_data[$key]['ultimo_mov'] = !empty($value['ultimo_mov']) ? date_format(new DateTime($value['ultimo_mov']), 'd-m-Y') : '';
				}

				$sheet->getColumnDimension('A')->setWidth(10);
				$sheet->getColumnDimension('B')->setWidth(14);
				$sheet->getColumnDimension('C')->setWidth(14);
				$sheet->getColumnDimension('D')->setWidth(32);
				$sheet->getColumnDimension('E')->setWidth(10);
				$sheet->getColumnDimension('F')->setWidth(16);
				$sheet->getColumnDimension('G')->setWidth(16);
				$sheet->getColumnDimension('H')->setWidth(24);
				$sheet->getColumnDimension('I')->setWidth(14);
				$sheet->getColumnDimension('J')->setWidth(14);
				$sheet->getColumnDimension('K')->setWidth(14);
				$sheet->getColumnDimension('L')->setWidth(14);
				$sheet->getColumnDimension('M')->setWidth(14);
				$sheet->getColumnDimension('N')->setWidth(14);
				$sheet->getColumnDimension('O')->setWidth(14);
				$sheet->getColumnDimension('P')->setWidth(32);
				$sheet->getColumnDimension('Q')->setWidth(14);
				$sheet->getStyle('A1:Q1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array('N°', 'Inicio', 'Tipo', 'Escribano', 'Padrón', 'Nomenclatura', 'Ubicación', 'Estado', 'Ult. Movimiento', 'Fin', 'Esc. Número', 'Esc. Foja', 'Esc. Fecha', 'Transf. Número', 'Transf. Ejercicio', 'Observaciones', 'Trámite Relac.')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter('A1:Q' . $sheet->getHighestRow());
				$nombreArchivo = 'InformeTramites_' . date('Ymd');

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
		$data['title_view'] = 'Informe de Trámites';
		$data['title'] = TITLE . ' - Informe de Trámites';
		$this->load_template('transferencias/reportes/reportes_content', $data);
	}
}