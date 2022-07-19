<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador de Intermediacion
 * Autor: Leandro
 * Creado: 14/03/2019
 * Modificado: 06/01/2020 (Leandro)
 */
class Busquedas extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('oficina_de_empleo/Intermediacion_model');
		$this->grupos_permitidos = array('admin','oficina_empleo_general','oficina_empleo');
		$this->grupos_solo_consulta = array('oficina_de_empleo');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tableData = array(
				'columns' => array(
						array('label' => 'CUIT', 'data' => 'cuit', 'width' => 11, 'class' => 'dt-body-right'),
						array('label' => 'Razon Social', 'data' => 'razon_social', 'width' => 35, 'class' => 'dt-body-right'),
						//array('label' => 'Domicilio', 'data' => 'domicilio', 'width' => 50, 'class' => 'dt-body-right'),
						array('label' => 'Distrito', 'data' => 'agente', 'width' => 20),
						array('label' => 'Teléfono Empresa', 'data' => 'telefono_empresa', 'width' => 15),
						//array('label' => 'Email', 'data' => 'email', 'width' => 50),
						array('label' => 'Puesto Requerido', 'data' => 'puesto_requerido', 'width' => 25),
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 15, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'intermediacion_table',
				'source_url' => 'oficina_de_empleo/intermediacion/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_intermediacion_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Intermediacion';
		$data['title'] = TITLE . ' - Intermediacion';
		$this->load_template('oficina_de_empleo/intermediacion/intermediacion_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		$this->load->helper('oficina_de_empleo/datatables_functions_helper');
		$this->datatables
				->select('id, razon_social, distrito, telefono_empresa, puesto_requerido, fecha, cuit')
				->from('intermediacion')
				->edit_column('estado', '$1', 'dt_column_oficina_de_empleo_email(email)', TRUE)
				->add_column('ver', '<a href="oficina_de_empleo/intermediacion/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="oficina_de_empleo/intermediacion/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="oficina_de_empleo/intermediacion/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function agregar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect('oficina_de_empleo/intermediacion/listar', 'refresh');
		}

		//$this->array_email_control = $this->Intermediacion_model->get_email();
		//$this->array_telefono_alternativo_control = $this->Intermediacion_model->get_telefono_alternativo();
		//$this->array_inspeccion_control = $this->Intermediacion_model->get_inspeccion();
		//$this->array_correccion_capa_control = $this->Intermediacion_model->get_correccion_capa();
		$this->array_distrito = $this->Intermediacion_model->get_distrito();
		$this->array_estudios = $this->Intermediacion_model->get_estudios();
		$this->array_nivel_estudios = $this->Intermediacion_model->get_nivel_estudios();
		$this->array_genero = $this->Intermediacion_model->get_genero();
		$this->array_tipo_solicitud = $this->Intermediacion_model->get_tipo_solicitud();
		$this->array_carrera = $this->Intermediacion_model->get_carrera();

		$this->set_model_validation_rules($this->Intermediacion_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{

			$n_orden = $this->get_last_id_n_orden();
			if (!empty($this->input->post('fecha')))
			{
				$fecha = DateTime::createFromFormat('d/m/Y H:i', $this->input->post('fecha'));
				$fecha->format('Y-m-d H:i:s');
			}
			else
			{
				$fecha = NULL;
			}
			$agente = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Intermediacion_model->create(array(
					'domicilio' => $this->input->post('domicilio'),
					'razon_social' => $this->input->post('razon_social'),
					'distrito' => $this->input->post('distrito'),
					'telefono_empresa' => $this->input->post('telefono_empresa'),
					'email' => $this->input->post('email'),
					'cantidad_personas' => $this->input->post('cantidad_personas'),
					'genero' => $this->input->post('genero'),
					'rango_edad' => $this->input->post('rango_edad'),
					'estudios' => $this->input->post('estudios'),
					'nivel_estudios' => $this->input->post('nivel_estudios'),
					'estado' => $this->input->post('estado'),
					'carrera' => $this->input->post('carrera'),
					'experiencia_requerida' => $this->input->post('experiencia_requerida'),
					'tareas_realizar' => $this->input->post('tareas_realizar'),
					'datos_adicionales' => $this->input->post('datos_adicionales'),
					'tipo_solicitud' => $this->input->post('tipo_solicitud'),
					'fecha' => $fecha,
					'cuit' => $cuit), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Intermediacion_model->get_msg());
				redirect('oficina_de_empleo/intermediacion/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Intermediacion_model->get_error())
				{
					$error_msg .= $this->Intermediacion_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		//$this->Intermediacion_model->fields['email']['array'] = $this->Intermediacion_model->get_email();
		//$this->Intermediacion_model->fields['telefono_alternativo']['array'] = $this->Intermediacion_model->get_telefono_alternativo();
		//$this->Intermediacion_model->fields['inspeccion']['array'] = $this->Intermediacion_model->get_inspeccion();
		//$this->Intermediacion_model->fields['correccion_capa']['array'] = $this->Intermediacion_model->get_correccion_capa();
		$this->Intermediacion_model->fields['distrito']['array'] = $this->Intermediacion_model->get_distrito();
		$this->Intermediacion_model->fields['estudios']['array'] = $this->Intermediacion_model->get_estudios();
		$this->Intermediacion_model->fields['nivel_estudios']['array'] = $this->Intermediacion_model->get_nivel_estudios();
		$this->Intermediacion_model->fields['genero']['array'] = $this->Intermediacion_model->get_genero();
		$this->Intermediacion_model->fields['tipo_solicitud']['array'] = $this->Intermediacion_model->get_tipo_solicitud();
		$this->Intermediacion_model->fields['carrera']['array'] = $this->Intermediacion_model->get_carrera();
		$this->Intermediacion_model->fields['agente']['value'] = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');
	
		$data['fields'] = $this->build_fields($this->Intermediacion_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Intermediacion';
		$data['title'] = TITLE . ' - Agregar Intermediacion';
		$this->load_template('oficina_de_empleo/intermediacion/intermediacion_abm', $data);
	}

	private function get_last_id_n_orden()
	{
		$maxid = 1;
		$row = $this->db->query('SELECT MAX(cuit) AS `maxid` FROM `intermediacion`')->row();
		if ($row)
		{
			$maxid = $row->maxid + 1;
		}
		return $maxid;
	}

	public function editar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("oficina_de_empleo/intermediacion/ver/$id", 'refresh');
		}

		$empleo = $this->Intermediacion_model->get(array('id' => $id));
		if (empty($empleo))
		{
			show_error('No se encontró el Reclamo', 500, 'Registro no encontrado');
		}

		//$this->array_email_control = $this->Intermediacion_model->get_email();
		//$this->array_telefono_alternativo_control = $this->Intermediacion_model->get_telefono_alternativo();
		//$this->array_inspeccion_control = $this->Intermediacion_model->get_inspeccion();
		//$this->array_correccion_capa_control = $this->Intermediacion_model->get_correccion_capa();
		$this->array_distrito = $this->Intermediacion_model->get_distrito();
		$this->array_estudios = $this->Intermediacion_model->get_estudios();
		$this->array_nivel_estudios = $this->Intermediacion_model->get_nivel_estudios();
		$this->array_genero = $this->Intermediacion_model->get_genero();
		$this->array_tipo_solicitud = $this->Intermediacion_model->get_tipo_solicitud();
		$this->array_carrera = $this->Intermediacion_model->get_tipo_carrera();

		$this->set_model_validation_rules($this->Intermediacion_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				if (!empty($this->input->post('fecha')))
				{
					$fecha = DateTime::createFromFormat('d/m/Y H:i', $this->input->post('fecha'));
					$fecha->format('Y-m-d H:i:s');
				}
				else
				{
					$fecha = NULL;
				}

				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Intermediacion_model->update(array(
						'id' => $this->input->post('id'),
						'domicilio' => $this->input->post('domicilio'),
						'razon_social' => $this->input->post('razon_social'),
						'telefono_empresa' => $this->input->post('telefono_empresa'),
						'email' => $this->input->post('email'),
						'cantidad_personas' => $this->input->post('cantidad_personas'),
						'genero' => $this->input->post('genero'),
						'rango_edad' => $this->input->post('rango_edad'),
						'estudios' => $this->input->post('estudios'),
						'nivel_estudios' => $this->input->post('nivel_estudios'),
						'carrera' => $this->input->post('carrera'),
						'experiencia_requerida' => $this->input->post('experiencia_requerida'),
						'tareas_realizar' => $this->input->post('tareas_realizar'),
						'datos_adicionale' => $this->input->post('datos_adicionales'),
						'tipo_solicitud' => $this->input->post('tipo_solicitud'),
						'fecha' => $fecha
						), FALSE);

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Intermediacion_model->get_msg());
					redirect('oficina_de_empleo/intermediacion/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Intermediacion_model->get_error())
					{
						$error_msg .= $this->Intermediacion_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		//$this->Intermediacion_model->fields['email']['array'] = $this->Intermediacion_model->get_email();
		//$this->Intermediacion_model->fields['telefono_alternativo']['array'] = $this->Intermediacion_model->get_telefono_alternativo();
		//$this->Intermediacion_model->fields['inspeccion']['array'] = $this->Intermediacion_model->get_inspeccion();
		//$this->Intermediacion_model->fields['correccion_capa']['array'] = $this->Intermediacion_model->get_correccion_capa();
		$this->Intermediacion_model->fields['distrito']['array'] = $this->Intermediacion_model->get_distrito();
		$this->Intermediacion_model->fields['estudios']['array'] = $this->Intermediacion_model->get_estudios();
		$this->Intermediacion_model->fields['nivel_estudios']['array'] = $this->Intermediacion_model->get_nivel_estudios();
		$this->Intermediacion_model->fields['genero']['array'] = $this->Intermediacion_model->get_genero();
		$this->Intermediacion_model->fields['tipo_solicitud']['array'] = $this->Intermediacion_model->get_tipo_solicitud();
		$this->Intermediacion_model->fields['carrera']['array'] = $this->Intermediacion_model->get_carrera();

		$data['fields'] = $this->build_fields($this->Intermediacion_model->fields, $empleo);
		$data['empleo'] = $empleo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Intermediacion';
		$data['title'] = TITLE . ' - Editar Intermediacion';
		$this->load_template('oficina_de_empleo/intermediacion/intermediacion_abm', $data);
	}

	public function eliminar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("oficina_de_empleo/intermediacion/ver/$id", 'refresh');
		}

		$empleo = $this->Intermediacion_model->get(array('id' => $id));
		if (empty($empleo))
		{
			show_error('No se encontró la Intermediacion', 500, 'Registro no encontrado');
		}

		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Intermediacion_model->delete(array('id' => $this->input->post('id')));
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Intermediacion_model->get_msg());
				redirect('oficina_de_empleo/intermediacion/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Intermediacion_model->get_error())
				{
					$error_msg .= $this->Intermediacion_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Intermediacion_model->fields, $empleo, TRUE);
		$data['empleo'] = $empleo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Intermediacion';
		$data['title'] = TITLE . ' - Eliminar Intermediacion';
		$this->load_template('oficina_de_empleo/intermediacion/intermediacion_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$empleo = $this->Intermediacion_model->get(array(
				'id' => $id,
				'join' => array(
						array(
								'type' => 'LEFT',
								'table' => 'users',
								'where' => 'users.id = intermediacion.audi_usuario'
						),
						array(
								'type' => 'LEFT',
								'table' => 'personas',
								'where' => 'personas.id = users.persona_id',
								'columnas' => "CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as audi_usuario",
						)
				)
		));
		if (empty($empleo))
		{
			show_error('No se encontró el Reclamo', 500, 'Registro no encontrado');
		}

		$this->load->helper('audi_helper');
		$data['audi_modal'] = audi_modal($empleo);

		$data['fields'] = $this->build_fields($this->Intermediacion_model->fields, $empleo, TRUE);
		$data['empleo'] = $empleo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Intermediacion';
		$data['title'] = TITLE . ' - Ver Intermediacion';
		$this->load_template('oficina_de_empleo/intermediacion/intermediacion_abm', $data);
	}

	public function exportar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('oficina_de_empleo/Intermediacion_model');

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'desde' => array('label' => 'Fecha Desde', 'type' => 'date', 'required' => TRUE),
				'hasta' => array('label' => 'Fecha Hasta', 'type' => 'date', 'required' => TRUE)
		);

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
			$hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

			$options['select'] = array(
					"intermediacion.id as id",
					'intermediacion.cuit',
					'intermediacion.domicilio',
					'intermediacion.razon_social',
					'intermediacion.telefono_empresa',
					'intermediacion.emial',
					'intermediacion.cantidad_personas',
					'intermediacion.genero',
					'intermediacion.rango_edad',
					'intermediacion.estudios',
					'intermediacion.nivel_estudios',
					'intermediacion.estado',
					'intermediacion.carrera',
					'intermediacion.experiencia_requerida',
					'intermediacion.tareas_realizar',
					'intermediacion.datos_adicionales',
					'intermediacion.tipo_solicitud',
			);

			$options['fecha >='] = $desde->format('Y-m-d');
			$hasta->add(new DateInterval('P1D'));
			$options['fecha <'] = $hasta->format('Y-m-d');

			$options['sort_by'] = 'intermediacion.id';
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->Intermediacion_model->get($options);

			if (!empty($print_data))
			{
				foreach ($print_data as $key => $value)
				{
					$print_data[$key]['fecha'] = date_format(new DateTime($value['fecha']), 'd-m-Y');
				}

				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()
						->setCreator("SistemaMLC")
						->setLastModifiedBy("SistemaMLC")
						->setTitle("Intermediacion")
						->setDescription("Intermediacion");
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
				$sheet->setTitle("Intermediacion");
				$sheet->getColumnDimension('A')->setWidth(14);
				$sheet->getColumnDimension('B')->setWidth(14);
				$sheet->getColumnDimension('C')->setWidth(14);
				$sheet->getColumnDimension('D')->setWidth(14);
				$sheet->getColumnDimension('E')->setWidth(14);
				$sheet->getColumnDimension('F')->setWidth(14);
				$sheet->getColumnDimension('G')->setWidth(18);
				$sheet->getColumnDimension('H')->setWidth(14);
				$sheet->getColumnDimension('I')->setWidth(14);
				$sheet->getColumnDimension('J')->setWidth(14);
				$sheet->getColumnDimension('K')->setWidth(14);
				$sheet->getColumnDimension('L')->setWidth(14);
				$sheet->getColumnDimension('M')->setWidth(14);
				$sheet->getColumnDimension('N')->setWidth(14);
				$sheet->getColumnDimension('O')->setWidth(14);
				$sheet->getColumnDimension('P')->setWidth(14);
				$sheet->getColumnDimension('Q')->setWidth(14);
				$sheet->getColumnDimension('R')->setWidth(14);
				$sheet->getColumnDimension('S')->setWidth(14);
				$sheet->getColumnDimension('T')->setWidth(14);
				$sheet->getColumnDimension('U')->setWidth(100);

				$sheet->getStyle('A1:T1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array(
								'ID', 'CUIT', 'Domicilio', 'Razon_social', 'Distrito', 'telefono_empresa', 'Email',
								'puesto_requerido', 'cantidad_personas', 'genero', 'rango_edad',
								'estudios', 'nivel_estudio', 'estado', 'carrera',
								'experiencia_requerida', 'tareas_realizar', 'datos_adionales', 'tipo_solicutd',
						)), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter('A1:U' . $sheet->getHighestRow());

				$BStyle1 = array(
						'borders' => array(
								'left' => array(
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
								)
						)
				);
				$sheet->getStyle('U1:U' . (sizeof($print_data) + 1))->applyFromArray($BStyle1);

				$BStyle2 = array(
						'borders' => array(
								'bottom' => array(
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
								)
						)
				);
				$sheet->getStyle('A' . (sizeof($print_data) + 1) . ':U' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

				$nombreArchivo = 'InformeIntermediacion_' . date('Ymd');
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
				header("Cache-Control: max-age=0");

				$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			}
			else
			{
				$error_msg = '<br />Sin datos para el periodo seleccionado';
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Intermediacion';
		$data['title'] = TITLE . ' - Informe de Intermediacion';
		$this->load_template('oficina_de_empleo/intermediacion/intermediacion_exportar', $data);
	}
}