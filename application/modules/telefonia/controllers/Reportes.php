<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

	/**
	 * Controlador de Reportes
	 * Autor: Leandro
	 * Creado: 04/09/2019
	 * Modificado: 06/01/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('Areas_model');
		$this->grupos_permitidos = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
		$this->grupos_solo_consulta = array('telefonia_consulta_general');
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
		$this->load_template('telefonia/reportes/reportes_listar', $data);
	}

	public function consumo_lineas_fijas()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'periodo_ini' => array('label' => 'Periodo desde', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
				'periodo_fin' => array('label' => 'Periodo hasta', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
				'linea' => array('label' => 'Línea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE)
		);

		$this->load->model('telefonia/Lineas_fijas_consumos_model');
		$this->load->model('telefonia/Lineas_fijas_model');
		$this->array_linea_control = $array_linea = $this->get_array('Lineas_fijas', 'linea', 'id', array(), array('Todas' => 'Todas'));

		$periodos = array();
		$inicio = '201403';
		$fin = date_format(new DateTime(), 'Ym');
		$periodos[$fin] = $fin;
		$periodo = date_format(new DateTime($fin . '01 -1 month'), 'Ym');
		while ($inicio <= $periodo)
		{
			$periodos[$periodo] = $periodo;
			$periodo = date_format(new DateTime($periodo . '01 -1 month'), 'Ym');
		}
		$periodos_fin = $periodos;
		$periodos_fin['NULL'] = 'Activo';
		$this->array_periodo_ini_control = $array_periodo_ini = $periodos;
		$this->array_periodo_fin_control = $array_periodo_fin = $periodos_fin;


		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$options['periodo >='] = $this->input->post('periodo_ini');
			$options['periodo <='] = $this->input->post('periodo_fin');
			if ($this->input->post('linea') != 'Todas')
			{
				$options['telefono_id'] = $this->input->post('linea');
			}
			$options['sort_by'] = 'telefono_id, periodo';
			$options['select'] = array(
					'tm_lineas_fijas.linea',
					'tipo_linea',
					'tm_lineas_fijas.domicilio',
					'areas.nombre',
					'tm_lineas_fijas.observaciones',
					'periodo_ini',
					'periodo_fin',
					'periodo',
					'monto',
					'tm_lineas_fijas_consumos.estado'
			);
			$options['join'] = array(
					array('table' => 'tm_lineas_fijas', 'where' => 'tm_lineas_fijas.id=tm_lineas_fijas_consumos.telefono_id'),
					array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id=tm_lineas_fijas.area_id')
			);
			$options['return_array'] = TRUE;

			$print_data = $this->Lineas_fijas_consumos_model->get($options);
			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()->setTitle("Informe de Consumo")->setDescription("");
				$spreadsheet->setActiveSheetIndex(0);
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setTitle("Informe de Consumo");
				$sheet->getColumnDimension('A')->setWidth(15);
				$sheet->getColumnDimension('B')->setWidth(10);
				$sheet->getColumnDimension('C')->setWidth(40);
				$sheet->getColumnDimension('D')->setWidth(50);
				$sheet->getColumnDimension('E')->setWidth(30);
				$sheet->getColumnDimension('F')->setWidth(10);
				$sheet->getColumnDimension('G')->setWidth(10);
				$sheet->getColumnDimension('H')->setWidth(10);
				$sheet->getColumnDimension('I')->setWidth(10);
				$sheet->getColumnDimension('J')->setWidth(10);
				$sheet->getStyle('A1:J1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array('Línea', 'Tipo', 'Domicilio', 'Área', 'Observaciones', 'Inicio', 'Fin', 'Periodo', 'Monto', 'Estado')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter($sheet->calculateWorksheetDimension());
				$nombreArchivo = 'InformeConsumo_' . date('Ymd');

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

		$fake_model->fields['periodo_ini']['array'] = $array_periodo_ini;
		$fake_model->fields['periodo_fin']['array'] = $array_periodo_fin;
		$fake_model->fields['linea']['array'] = $array_linea;
		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Consumo de Líneas Fijas';
		$data['title'] = TITLE . ' - Informe de Consumo de Líneas Fijas';
		$this->load_template('telefonia/reportes/reportes_content', $data);
	}

	public function lineas()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'prestador' => array('label' => 'Prestador', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
		);

		$this->load->model('telefonia/Prestadores_model');
		$this->load->model('telefonia/Lineas_model');
		$this->array_prestador_control = $array_prestador = $this->get_array('Prestadores', 'nombre', 'id', array(), array('Todos' => 'Todos'));
		$this->array_estado_control = $array_estado = array('Todos' => 'Todos', 'Baja' => 'Baja', 'Denunciada' => 'Denunciada', 'Disponible' => 'Disponible', 'En Uso' => 'En Uso', 'No Disponible' => 'No Disponible');

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$options['select'] = array(
					'tm_prestadores.nombre as prestador',
					'tm_lineas.numero',
					'tm_lineas.numero_corto',
					'tm_lineas.numero_sim',
					'tm_lineas.min_internacional',
					'tm_lineas.min_nacional',
					'tm_lineas.min_interno',
					'tm_lineas.datos',
					"CONCAT(personal.Nombre, ' ', personal.Apellido, '(', personal.Legajo, ')') as nombre_personal ",
					'areas.codigo as codigo_area',
					'areas.nombre as area',
					'tm_lineas.persona',
					'tm_lineas.estado',
					'tm_lineas.observaciones',
					'tm_marcas.nombre as marca',
					'tm_modelos.nombre as modelo',
					'tm_equipos.imei'
			);
			$options['join'] = array(
					array('type' => 'left', 'table' => 'tm_prestadores', 'where' => 'tm_prestadores.id = tm_lineas.prestador_id'),
					array('type' => 'left', 'table' => 'tm_equipos', 'where' => 'tm_lineas.equipo_id = tm_equipos.id'),
					array('type' => 'left', 'table' => 'tm_modelos', 'where' => 'tm_modelos.id = tm_equipos.modelo_id'),
					array('type' => 'left', 'table' => 'tm_marcas', 'where' => 'tm_marcas.id = tm_modelos.marca_id'),
					array('type' => 'left', 'table' => 'personal', 'where' => 'personal.Legajo = tm_lineas.labo_Codigo'),
					array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = tm_lineas.area_id')
			);

			$options['where'] = array();
			if ($this->input->post('prestador') !== 'Todos')
			{
				$where['column'] = 'tm_prestadores.id';
				$where['value'] = $this->input->post('prestador');
				$options['where'][] = $where;
			}
			if ($this->input->post('estado') !== 'Todos')
			{
				$where['column'] = 'tm_lineas.estado';
				$where['value'] = $this->input->post('estado');
				$options['where'][] = $where;
			}

			$options['sort_by'] = 'tm_lineas.numero';
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->Lineas_model->get($options);

			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()->setTitle("Informe de Líneas")->setDescription("");
				$spreadsheet->setActiveSheetIndex(0);
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setTitle("Informe de Líneas");
				$sheet->getColumnDimension('A')->setWidth(25);
				$sheet->getColumnDimension('B')->setWidth(15);
				$sheet->getColumnDimension('C')->setWidth(15);
				$sheet->getColumnDimension('D')->setWidth(20);
				$sheet->getColumnDimension('E')->setWidth(15);
				$sheet->getColumnDimension('F')->setWidth(15);
				$sheet->getColumnDimension('G')->setWidth(15);
				$sheet->getColumnDimension('H')->setWidth(15);
				$sheet->getColumnDimension('I')->setWidth(40);
				$sheet->getColumnDimension('J')->setWidth(15);
				$sheet->getColumnDimension('K')->setWidth(40);
				$sheet->getColumnDimension('L')->setWidth(15);
				$sheet->getColumnDimension('M')->setWidth(15);
				$sheet->getColumnDimension('N')->setWidth(40);
				$sheet->getColumnDimension('O')->setWidth(20);
				$sheet->getColumnDimension('P')->setWidth(20);
				$sheet->getColumnDimension('Q')->setWidth(20);
				$sheet->getStyle('A1:Q1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array('Proveedor', 'Número', 'Núm. Corto', 'Sim', 'Min. Internac', 'Min. Nacional.', 'Min. Interno', 'Datos', 'Persona', 'Código Área', 'Área', 'Persona Ext.', 'Estado', 'Observaciones', 'Marca', 'Modelo', 'IMEI')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');

				$sheet->setAutoFilter('A1:Q1');
				$nombreArchivo = 'InformeLineas_' . date('Ymd');
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

		$fake_model->fields['prestador']['array'] = $array_prestador;
		$fake_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Líneas';
		$data['title'] = TITLE . ' - Informe de Líneas';
		$this->load_template('telefonia/reportes/reportes_content', $data);
	}

	public function lineas_listado()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'prestador' => array('label' => 'Prestador', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
		);

		$this->load->model('telefonia/Prestadores_model');
		$this->load->model('telefonia/Lineas_model');
		$this->array_prestador_control = $array_prestador = $this->get_array('Prestadores', 'nombre', 'id', array(), array('Todos' => 'Todos'));
		$this->array_estado_control = $array_estado = array('Todos' => 'Todos', 'Baja' => 'Baja', 'Denunciada' => 'Denunciada', 'Disponible' => 'Disponible', 'En Uso' => 'En Uso', 'No Disponible' => 'No Disponible');

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$options['select'] = array(
					'tm_prestadores.nombre as prestador',
					'tm_lineas.numero',
					"CASE WHEN tm_lineas.labo_Codigo IS NULL THEN tm_lineas.persona ELSE CONCAT(personal.Apellido, ', ', personal.Nombre) END as nombre_personal",
					'areas.nombre as area'
			);

			$options['join'] = array(
					array('type' => 'left', 'table' => 'tm_prestadores', 'where' => 'tm_prestadores.id = tm_lineas.prestador_id'),
					array('type' => 'left', 'table' => 'personal', 'where' => 'personal.Legajo = tm_lineas.labo_Codigo'),
					array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = tm_lineas.area_id')
			);

			$options['where'] = array();
			if ($this->input->post('prestador') !== 'Todos')
			{
				$where['column'] = 'tm_prestadores.id';
				$where['value'] = $this->input->post('prestador');
				$options['where'][] = $where;
			}
			if ($this->input->post('estado') !== 'Todos')
			{
				$where['column'] = 'tm_lineas.estado';
				$where['value'] = $this->input->post('estado');
				$options['where'][] = $where;
			}

			$options['sort_by'] = 'areas.nombre, personal.Apellido';
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->Lineas_model->get($options);

			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()->setTitle("Listado de Líneas")->setDescription("");
				$spreadsheet->setActiveSheetIndex(0);
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setTitle("Informe de Líneas");
				$sheet->getColumnDimension('A')->setWidth(20);
				$sheet->getColumnDimension('B')->setWidth(15);
				$sheet->getColumnDimension('C')->setWidth(45);
				$sheet->getColumnDimension('D')->setWidth(45);
				$sheet->getStyle('A1:D1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array('Proveedor', 'Número', 'Persona', 'Área')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');

				$sheet->setAutoFilter('A1:D1');
				$nombreArchivo = 'ListadoLineas_' . date('Ymd');
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

		$fake_model->fields['prestador']['array'] = $array_prestador;
		$fake_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Listado de Líneas';
		$data['title'] = TITLE . ' - Listado de Líneas';
		$this->load_template('telefonia/reportes/reportes_content', $data);
	}

	public function equipos()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'marca' => array('label' => 'Marca', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
				'modelo' => array('label' => 'Modelo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'required' => TRUE),
		);

		$this->load->model('telefonia/Marcas_model');
		$this->load->model('telefonia/Modelos_model');
		$this->load->model('telefonia/Equipos_model');
		$this->array_marca_control = $array_marca = $this->get_array('Marcas', 'nombre', 'id', array(), array('Todas' => 'Todas'));
		$this->array_modelo_control = $array_modelo = $this->get_array('Modelos', 'nombre', 'id', array(), array('Todos' => 'Todos'));
		$this->array_estado_control = $array_estado = array('Todos' => 'Todos', 'Disponible' => 'Disponible', 'En Uso' => 'En Uso', 'No Disponible' => 'No Disponible', 'Robado' => 'Robado');

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$options['select'] = array(
					'tm_marcas.nombre as marca',
					'tm_modelos.nombre as modelo',
					'tm_equipos.imei',
					'tm_equipos.estado',
					'tm_equipos.observaciones',
					"CONCAT(personal.Nombre, ' ', personal.Apellido, '(', personal.Legajo, ')') as nombre_personal ",
					'areas.nombre as area',
					'tm_equipos.persona',
					'tm_lineas.numero',
					'tm_lineas.numero_corto',
					'tm_lineas.numero_sim',
					'tm_lineas.min_internacional',
					'tm_lineas.min_nacional',
					'tm_lineas.min_interno',
					'tm_lineas.datos'
			);
			$options['join'] = array(
					array('type' => 'left', 'table' => 'tm_lineas', 'where' => 'tm_lineas.equipo_id = tm_equipos.id'),
					array('type' => 'left', 'table' => 'tm_modelos', 'where' => 'tm_modelos.id = tm_equipos.modelo_id'),
					array('type' => 'left', 'table' => 'tm_marcas', 'where' => 'tm_marcas.id = tm_modelos.marca_id'),
					array('type' => 'left', 'table' => 'personal', 'where' => 'personal.Legajo = tm_equipos.labo_Codigo'),
					array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = tm_equipos.area_id')
			);

			if ($this->input->post('marca') !== 'Todas')
			{
				$where['column'] = 'tm_modelos.marca_id';
				$where['value'] = $this->input->post('marca');
				$options['where'] = array($where);
			}
			if ($this->input->post('modelo') !== 'Todos')
			{
				$where['column'] = 'tm_equipos.modelo_id';
				$where['value'] = $this->input->post('modelo');
				$options['where'] = array($where);
			}
			if ($this->input->post('estado') !== 'Todos')
			{
				$where['column'] = 'tm_equipos.estado';
				$where['value'] = $this->input->post('estado');
				$options['where'] = array($where);
			}

			$options['sort_by'] = 'tm_equipos.imei';
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->Equipos_model->get($options);

			if (!empty($print_data))
			{
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()->setTitle("Informe de Equipos")->setDescription("");
				$spreadsheet->setActiveSheetIndex(0);
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setTitle("Informe de Equipos");
				$sheet->getColumnDimension('A')->setWidth(20);
				$sheet->getColumnDimension('B')->setWidth(30);
				$sheet->getColumnDimension('C')->setWidth(20);
				$sheet->getColumnDimension('D')->setWidth(15);
				$sheet->getColumnDimension('E')->setWidth(40);
				$sheet->getColumnDimension('F')->setWidth(40);
				$sheet->getColumnDimension('G')->setWidth(40);
				$sheet->getColumnDimension('H')->setWidth(40);
				$sheet->getColumnDimension('I')->setWidth(15);
				$sheet->getColumnDimension('J')->setWidth(15);
				$sheet->getColumnDimension('K')->setWidth(20);
				$sheet->getColumnDimension('L')->setWidth(15);
				$sheet->getColumnDimension('M')->setWidth(15);
				$sheet->getColumnDimension('N')->setWidth(15);
				$sheet->getColumnDimension('O')->setWidth(15);
				$sheet->getStyle('A1:O1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array('Marca', 'Modelo', 'IMEI', 'Estado', 'Observaciones', 'Persona', 'Área', 'Persona Ext.', 'Número', 'Núm. Corto', 'Sim', 'Min. Internac', 'Min. Nacional.', 'Min. Interno', 'Datos')), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');

				$sheet->setAutoFilter('A1:O1');
				$nombreArchivo = 'InformeEquipos_' . date('Ymd');
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

		$fake_model->fields['marca']['array'] = $array_marca;
		$fake_model->fields['modelo']['array'] = $array_modelo;
		$fake_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Equipos';
		$data['title'] = TITLE . ' - Informe de Equipos';
		$this->load_template('telefonia/reportes/reportes_content', $data);
	}
}