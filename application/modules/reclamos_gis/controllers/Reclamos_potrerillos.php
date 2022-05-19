<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador de Reclamos Potrerillos
 * Autor: Leandro
 * Creado: 14/03/2019
 * Modificado: 06/01/2020 (Leandro)
 */
class Reclamos_potrerillos extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('reclamos_gis/Reclamos_potrerillos_model');
		$this->grupos_permitidos = array('admin', 'reclamos_gis_user', 'reclamos_gis_consulta_general');
		$this->grupos_solo_consulta = array('reclamos_gis_consulta_general');
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
						array('label' => 'N° Orden', 'data' => 'n_orden', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Padrón', 'data' => 'padron', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Nomenclatura', 'data' => 'nomenclatura', 'width' => 11, 'class' => 'dt-body-right'),
						array('label' => 'Agente', 'data' => 'agente', 'width' => 14),
						array('label' => 'Tipo', 'data' => 'tipo', 'width' => 12),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 16),
						array('label' => 'N° Nota', 'data' => 'n_nota', 'width' => 8),
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'reclamos_table',
				'source_url' => 'reclamos_gis/reclamos_potrerillos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_reclamos_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Reclamos Potrerillos';
		$data['title'] = TITLE . ' - Reclamos Potrerillos';
		$this->load_template('reclamos_gis/reclamos_potrerillos/reclamos_potrerillos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		$this->load->helper('reclamos_gis/datatables_functions_helper');
		$this->datatables
				->select('id, padron, nomenclatura, agente, tipo, estado, n_nota, telefono_contacto, fecha, n_orden')
				->from('gis_reclamos_potrerillos')
				->edit_column('estado', '$1', 'dt_column_reclamos_gis_estado(estado)', TRUE)
				->add_column('ver', '<a href="reclamos_gis/reclamos_potrerillos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="reclamos_gis/reclamos_potrerillos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="reclamos_gis/reclamos_potrerillos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('reclamos_gis/reclamos_potrerillos/listar', 'refresh');
		}

		$this->array_estado_control = $this->Reclamos_potrerillos_model->get_estados();
		$this->array_tipo_control = $this->Reclamos_potrerillos_model->get_tipos();
		$this->array_inspeccion_control = $this->Reclamos_potrerillos_model->get_inspeccion();
		$this->array_correccion_capa_control = $this->Reclamos_potrerillos_model->get_correccion_capa();

		$this->set_model_validation_rules($this->Reclamos_potrerillos_model);
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
			$trans_ok &= $this->Reclamos_potrerillos_model->create(array(
					'padron' => $this->input->post('padron'),
					'nomenclatura' => $this->input->post('nomenclatura'),
					'agente' => $agente,
					'tipo' => $this->input->post('tipo'),
					'estado' => $this->input->post('estado'),
					'cubierta_existente' => $this->input->post('cubierta_existente'),
					'pileta_existente' => $this->input->post('pileta_existente'),
					'cubierta_gis_existente' => $this->input->post('cubierta_gis_existente'),
					'pileta_gis_existente' => $this->input->post('pileta_gis_existente'),
					'cubierta_gis_nueva' => $this->input->post('cubierta_gis_nueva'),
					'pileta_gis_nueva' => $this->input->post('pileta_gis_nueva'),
					'cubierta_declarada' => $this->input->post('cubierta_declarada'),
					'pileta_declarada' => $this->input->post('pileta_declarada'),
					'observaciones' => $this->input->post('observaciones'),
					'n_nota' => $this->input->post('n_nota'),
					'telefono_contacto' => $this->input->post('telefono_contacto'),
					'correccion_capa' => $this->input->post('correccion_capa'),
					'inspeccion' => $this->input->post('inspeccion'),
					'fecha' => $fecha,
					'n_orden' => $n_orden), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Reclamos_potrerillos_model->get_msg());
				redirect('reclamos_gis/reclamos_potrerillos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Reclamos_potrerillos_model->get_error())
				{
					$error_msg .= $this->Reclamos_potrerillos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Reclamos_potrerillos_model->fields['estado']['array'] = $this->Reclamos_potrerillos_model->get_estados();
		$this->Reclamos_potrerillos_model->fields['tipo']['array'] = $this->Reclamos_potrerillos_model->get_tipos();
		$this->Reclamos_potrerillos_model->fields['inspeccion']['array'] = $this->Reclamos_potrerillos_model->get_inspeccion();
		$this->Reclamos_potrerillos_model->fields['correccion_capa']['array'] = $this->Reclamos_potrerillos_model->get_correccion_capa();
		$this->Reclamos_potrerillos_model->fields['agente']['value'] = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');

		$data['fields'] = $this->build_fields($this->Reclamos_potrerillos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Reclamo Potrerillos';
		$data['title'] = TITLE . ' - Agregar Reclamo Potrerillos';
		$this->load_template('reclamos_gis/reclamos_potrerillos/reclamos_potrerillos_abm', $data);
	}

	private function get_last_id_n_orden()
	{
		$maxid = 1;
		$row = $this->db->query('SELECT MAX(n_orden) AS `maxid` FROM `gis_reclamos_potrerillos`')->row();
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
			redirect("reclamos_gis/reclamos_potrerillos/ver/$id", 'refresh');
		}

		$reclamo = $this->Reclamos_potrerillos_model->get(array('id' => $id));
		if (empty($reclamo))
		{
			show_error('No se encontró el Reclamo', 500, 'Registro no encontrado');
		}

		$this->array_estado_control = $this->Reclamos_potrerillos_model->get_estados();
		$this->array_tipo_control = $this->Reclamos_potrerillos_model->get_tipos();
		$this->array_inspeccion_control = $this->Reclamos_potrerillos_model->get_inspeccion();
		$this->array_correccion_capa_control = $this->Reclamos_potrerillos_model->get_correccion_capa();

		$this->set_model_validation_rules($this->Reclamos_potrerillos_model);
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
				$trans_ok &= $this->Reclamos_potrerillos_model->update(array(
						'id' => $this->input->post('id'),
						'padron' => $this->input->post('padron'),
						'nomenclatura' => $this->input->post('nomenclatura'),
						'tipo' => $this->input->post('tipo'),
						'estado' => $this->input->post('estado'),
						'cubierta_existente' => $this->input->post('cubierta_existente'),
						'pileta_existente' => $this->input->post('pileta_existente'),
						'cubierta_gis_existente' => $this->input->post('cubierta_gis_existente'),
						'pileta_gis_existente' => $this->input->post('pileta_gis_existente'),
						'cubierta_gis_nueva' => $this->input->post('cubierta_gis_nueva'),
						'pileta_gis_nueva' => $this->input->post('pileta_gis_nueva'),
						'cubierta_declarada' => $this->input->post('cubierta_declarada'),
						'pileta_declarada' => $this->input->post('pileta_declarada'),
						'observaciones' => $this->input->post('observaciones'),
						'inspeccion' => $this->input->post('inspeccion'),
						'n_nota' => $this->input->post('n_nota'),
						'telefono_contacto' => $this->input->post('telefono_contacto'),
						'correccion_capa' => $this->input->post('correccion_capa'),
						'fecha' => $fecha
						), FALSE);

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Reclamos_potrerillos_model->get_msg());
					redirect('reclamos_gis/reclamos_potrerillos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Reclamos_potrerillos_model->get_error())
					{
						$error_msg .= $this->Reclamos_potrerillos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Reclamos_potrerillos_model->fields['estado']['array'] = $this->Reclamos_potrerillos_model->get_estados();
		$this->Reclamos_potrerillos_model->fields['tipo']['array'] = $this->Reclamos_potrerillos_model->get_tipos();
		$this->Reclamos_potrerillos_model->fields['inspeccion']['array'] = $this->Reclamos_potrerillos_model->get_inspeccion();
		$this->Reclamos_potrerillos_model->fields['correccion_capa']['array'] = $this->Reclamos_potrerillos_model->get_correccion_capa();

		$data['fields'] = $this->build_fields($this->Reclamos_potrerillos_model->fields, $reclamo);
		$data['reclamo'] = $reclamo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Reclamo Potrerillos';
		$data['title'] = TITLE . ' - Editar Reclamo Potrerillos';
		$this->load_template('reclamos_gis/reclamos_potrerillos/reclamos_potrerillos_abm', $data);
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
			redirect("reclamos_gis/reclamos_potrerillos/ver/$id", 'refresh');
		}

		$reclamo = $this->Reclamos_potrerillos_model->get(array('id' => $id));
		if (empty($reclamo))
		{
			show_error('No se encontró el Reclamo', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Reclamos_potrerillos_model->delete(array('id' => $this->input->post('id')));
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Reclamos_potrerillos_model->get_msg());
				redirect('reclamos_gis/reclamos_potrerillos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Reclamos_potrerillos_model->get_error())
				{
					$error_msg .= $this->Reclamos_potrerillos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Reclamos_potrerillos_model->fields, $reclamo, TRUE);
		$data['reclamo'] = $reclamo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Reclamo Potrerillos';
		$data['title'] = TITLE . ' - Eliminar Reclamo Potrerillos';
		$this->load_template('reclamos_gis/reclamos_potrerillos/reclamos_potrerillos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$reclamo = $this->Reclamos_potrerillos_model->get(array(
				'id' => $id,
				'join' => array(
						array(
								'type' => 'LEFT',
								'table' => 'users',
								'where' => 'users.id = gis_reclamos_potrerillos.audi_usuario'
						),
						array(
								'type' => 'LEFT',
								'table' => 'personas',
								'where' => 'personas.id = users.persona_id',
								'columnas' => "CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as audi_usuario",
						)
				)
		));
		if (empty($reclamo))
		{
			show_error('No se encontró el Reclamo', 500, 'Registro no encontrado');
		}

		$this->load->helper('audi_helper');
		$data['audi_modal'] = audi_modal($reclamo);

		$data['fields'] = $this->build_fields($this->Reclamos_potrerillos_model->fields, $reclamo, TRUE);
		$data['reclamo'] = $reclamo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Reclamo Potrerillos';
		$data['title'] = TITLE . ' - Ver Reclamo Potrerillos';
		$this->load_template('reclamos_gis/reclamos_potrerillos/reclamos_potrerillos_abm', $data);
	}

	public function exportar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('reclamos_gis/Reclamos_potrerillos_model');

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
					"gis_reclamos_potrerillos.id as id",
					'gis_reclamos_potrerillos.n_orden',
					'gis_reclamos_potrerillos.padron',
					'gis_reclamos_potrerillos.nomenclatura',
					'gis_reclamos_potrerillos.agente',
					'gis_reclamos_potrerillos.n_nota',
					'gis_reclamos_potrerillos.fecha',
					'gis_reclamos_potrerillos.tipo',
					'gis_reclamos_potrerillos.estado',
					'gis_reclamos_potrerillos.inspeccion',
					'gis_reclamos_potrerillos.correccion_capa',
					'gis_reclamos_potrerillos.cubierta_existente',
					'gis_reclamos_potrerillos.pileta_existente',
					'gis_reclamos_potrerillos.cubierta_gis_existente',
					'gis_reclamos_potrerillos.pileta_gis_existente',
					'gis_reclamos_potrerillos.cubierta_gis_nueva',
					'gis_reclamos_potrerillos.pileta_gis_nueva',
					'gis_reclamos_potrerillos.cubierta_declarada',
					'gis_reclamos_potrerillos.pileta_declarada',
					'gis_reclamos_potrerillos.telefono_contacto',
					'gis_reclamos_potrerillos.observaciones',
			);

			$options['fecha >='] = $desde->format('Y-m-d');
			$hasta->add(new DateInterval('P1D'));
			$options['fecha <'] = $hasta->format('Y-m-d');

			$options['sort_by'] = 'gis_reclamos_potrerillos.id';
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->Reclamos_potrerillos_model->get($options);

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
						->setTitle("Reclamos Potrerillos Gis")
						->setDescription("Reclamos Potrerillos Gis");
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
				$sheet->setTitle("Reclamos Potrerillos Gis");
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
								'ID', 'N_Orden', 'Padron', 'Nomenclatura', 'Agente', 'N_Nota', 'Fecha',
								'Tipo', 'Estado', 'Inspeccion', 'Correcion Capa',
								'Cubierta Existente', 'Pileta Existente', 'Cubierta Gis Existente', 'Pileta Gis Existente',
								'Cubierta Gis Nueva', 'Pileta Gis Nueva', 'Cubierta Declarada', 'Pileta Declarada',
								'Telefono de Contacto', 'Observaciones'
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

				$nombreArchivo = 'InformeReclamosPotrerillos_' . date('Ymd');
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
		$data['title_view'] = 'Informe de Reclamos Potrerillos';
		$data['title'] = TITLE . ' - Informe de Reclamos Potrerillos';
		$this->load_template('reclamos_gis/reclamos_potrerillos/reclamos_potrerillos_exportar', $data);
	}
}