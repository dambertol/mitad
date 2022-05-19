<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador de Reclamos
 * Autor: Leandro
 * Creado: 10/10/2018
 * Modificado: 06/01/2020 (Leandro)
 */
class Reclamos extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('reclamos_gis/Reclamos_model');
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
						array('label' => 'N° Orden', 'data' => 'n_orden', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Padrón', 'data' => 'padron', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Agente', 'data' => 'agente', 'width' => 16),
						array('label' => 'Tipo', 'data' => 'tipo', 'width' => 15),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 16),
						array('label' => 'N° Nota', 'data' => 'n_nota', 'width' => 10),
						//         array('label' => 'Teléfono', 'data' => 'telefono_contacto', 'width' => 10),
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'reclamos_table',
				'source_url' => 'reclamos_gis/reclamos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_reclamos_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Reclamos';
		$data['title'] = TITLE . ' - Reclamos';
		$this->load_template('reclamos_gis/reclamos/reclamos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		$this->load->helper('reclamos_gis/datatables_functions_helper');
		$this->datatables
				->select('id, padron, agente, tipo, estado, n_nota, telefono_contacto, fecha, n_orden')
				->from('gis_reclamos')
				->edit_column('estado', '$1', 'dt_column_reclamos_gis_estado(estado)', TRUE)
				->add_column('ver', '<a href="reclamos_gis/reclamos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="reclamos_gis/reclamos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="reclamos_gis/reclamos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('reclamos_gis/reclamos/listar', 'refresh');
		}

		$this->array_estado_control = $this->Reclamos_model->get_estados();
		$this->array_tipo_control = $this->Reclamos_model->get_tipos();
		$this->array_inspeccion_control = $this->Reclamos_model->get_inspeccion();
		$this->array_correccion_capa_control = $this->Reclamos_model->get_correccion_capa();

		$this->set_model_validation_rules($this->Reclamos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{

			$n_orden = $this->get_last_id_n_orden();
			$fecha = DateTime::createFromFormat('d/m/Y H:i', $this->input->post('fecha'));
			$agente = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Reclamos_model->create(array(
					'padron' => $this->input->post('padron'),
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
					'fecha' => $fecha->format('Y-m-d H:i:s'),
					'n_orden' => $n_orden), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Reclamos_model->get_msg());
				redirect('reclamos_gis/reclamos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Reclamos_model->get_error())
				{
					$error_msg .= $this->Reclamos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Reclamos_model->fields['estado']['array'] = $this->Reclamos_model->get_estados();
		$this->Reclamos_model->fields['tipo']['array'] = $this->Reclamos_model->get_tipos();
		$this->Reclamos_model->fields['inspeccion']['array'] = $this->Reclamos_model->get_inspeccion();
		$this->Reclamos_model->fields['correccion_capa']['array'] = $this->Reclamos_model->get_correccion_capa();
		$this->Reclamos_model->fields['agente']['value'] = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');

		$data['fields'] = $this->build_fields($this->Reclamos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Reclamo';
		$data['title'] = TITLE . ' - Agregar Reclamo';
		$this->load_template('reclamos_gis/reclamos/reclamos_abm', $data);
	}

	private function get_last_id_n_orden()
	{
		$maxid = 1;
		$row = $this->db->query('SELECT MAX(n_orden) AS `maxid` FROM `gis_reclamos`')->row();
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
			redirect("reclamos_gis/reclamos/ver/$id", 'refresh');
		}

		$reclamo = $this->Reclamos_model->get(array('id' => $id));
		if (empty($reclamo))
		{
			show_error('No se encontró el Reclamo', 500, 'Registro no encontrado');
		}

		$this->array_estado_control = $this->Reclamos_model->get_estados();
		$this->array_tipo_control = $this->Reclamos_model->get_tipos();
		$this->array_inspeccion_control = $this->Reclamos_model->get_inspeccion();
		$this->array_correccion_capa_control = $this->Reclamos_model->get_correccion_capa();

		$this->set_model_validation_rules($this->Reclamos_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$fecha = DateTime::createFromFormat('d/m/Y H:i', $this->input->post('fecha'));

				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Reclamos_model->update(array(
						'id' => $this->input->post('id'),
						'padron' => $this->input->post('padron'),
						//'agente' => $this->input->post('agente'),
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
						'fecha' => $fecha->format('Y-m-d H:i:s'),
						//'n_orden' => $this->get_last_id_n_orden()
						), FALSE);

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Reclamos_model->get_msg());
					redirect('reclamos_gis/reclamos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Reclamos_model->get_error())
					{
						$error_msg .= $this->Reclamos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Reclamos_model->fields['estado']['array'] = $this->Reclamos_model->get_estados();
		$this->Reclamos_model->fields['tipo']['array'] = $this->Reclamos_model->get_tipos();
		$this->Reclamos_model->fields['inspeccion']['array'] = $this->Reclamos_model->get_inspeccion();
		$this->Reclamos_model->fields['correccion_capa']['array'] = $this->Reclamos_model->get_correccion_capa();

		$data['fields'] = $this->build_fields($this->Reclamos_model->fields, $reclamo);
		$data['reclamo'] = $reclamo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Reclamo';
		$data['title'] = TITLE . ' - Editar Reclamo';
		$this->load_template('reclamos_gis/reclamos/reclamos_abm', $data);
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
			redirect("reclamos_gis/reclamos/ver/$id", 'refresh');
		}

		$reclamo = $this->Reclamos_model->get(array('id' => $id));
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
			$trans_ok &= $this->Reclamos_model->delete(array('id' => $this->input->post('id')));
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Reclamos_model->get_msg());
				redirect('reclamos_gis/reclamos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Reclamos_model->get_error())
				{
					$error_msg .= $this->Reclamos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Reclamos_model->fields, $reclamo, TRUE);
		$data['reclamo'] = $reclamo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Reclamo';
		$data['title'] = TITLE . ' - Eliminar Reclamo';
		$this->load_template('reclamos_gis/reclamos/reclamos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$reclamo = $this->Reclamos_model->get(array(
				'id' => $id,
				'join' => array(
						array(
								'type' => 'LEFT',
								'table' => 'users',
								'where' => 'users.id = gis_reclamos.audi_usuario'
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

		$data['fields'] = $this->build_fields($this->Reclamos_model->fields, $reclamo, TRUE);
		$data['reclamo'] = $reclamo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Reclamo';
		$data['title'] = TITLE . ' - Ver Reclamo';
		$this->load_template('reclamos_gis/reclamos/reclamos_abm', $data);
	}

	public function exportar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('reclamos_gis/Reclamos_model');

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
					"gis_reclamos.id as id",
					'gis_reclamos.n_orden',
					'gis_reclamos.padron',
					'gis_reclamos.agente',
					'gis_reclamos.n_nota',
					'gis_reclamos.fecha',
					'gis_reclamos.tipo',
					'gis_reclamos.estado',
					'gis_reclamos.inspeccion',
					'gis_reclamos.correccion_capa',
					'gis_reclamos.cubierta_existente',
					'gis_reclamos.pileta_existente',
					'gis_reclamos.cubierta_gis_existente',
					'gis_reclamos.pileta_gis_existente',
					'gis_reclamos.cubierta_gis_nueva',
					'gis_reclamos.pileta_gis_nueva',
					'gis_reclamos.cubierta_declarada',
					'gis_reclamos.pileta_declarada',
					'gis_reclamos.telefono_contacto',
					'gis_reclamos.observaciones',
			);
			/*
			  $where['column'] = 'gis_reclamos.vencimiento <';
			  $where['value'] = "'" . date_format(new DateTime(), 'Y/m/d') . "'";
			  $where['override'] = TRUE;
			 */
			/*
			  $where['column'] = "gis_reclamos.estado NOT IN ('Anulado', 'Asignado', 'Pendiente')";
			  $where['value'] = '';
			  $options['where'] = array($where);
			  //$options['where'][] = $where;
			 */
			$options['fecha >='] = $desde->format('Y-m-d');
			$hasta->add(new DateInterval('P1D'));
			$options['fecha <'] = $hasta->format('Y-m-d');

			$options['sort_by'] = 'gis_reclamos.id';
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->Reclamos_model->get($options);

			if (!empty($print_data))
			{
				foreach ($print_data as $key => $value)
				{
					$print_data[$key]['fecha'] = date_format(new DateTime($value['fecha']), 'd-m-Y');
					//  $print_data[$key]['vencimiento'] = date_format(new DateTime($value['vencimiento']), 'd-m-Y');
				}

				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()
						->setCreator("SistemaMLC")
						->setLastModifiedBy("SistemaMLC")
						->setTitle("Informe de Reclamos Gis")
						->setDescription("Informe de Reclamos Gis");
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
				$sheet->setTitle("Informe de Reclamos Gis");
				$sheet->getColumnDimension('A')->setWidth(14);
				$sheet->getColumnDimension('B')->setWidth(14);
				$sheet->getColumnDimension('C')->setWidth(14);
				$sheet->getColumnDimension('D')->setWidth(14);
				$sheet->getColumnDimension('E')->setWidth(14);
				$sheet->getColumnDimension('F')->setWidth(18);
				$sheet->getColumnDimension('G')->setWidth(14);
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
				$sheet->getColumnDimension('T')->setWidth(100);

				$sheet->getStyle('A1:T1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array(
								'ID', 'N_Orden', 'Padron', 'Agente', 'N_Nota', 'Fecha',
								'Tipo', 'Estado', 'Inspeccion', 'Correcion Capa',
								'Cubierta Existente', 'Pileta Existente', 'Cubierta Gis Existente', 'Pileta Gis Existente',
								'Cubierta Gis Nueva', 'Pileta Gis Nueva', 'Cubierta Declarada', 'Pileta Declarada',
								'Telefono de Contacto', 'Observaciones'
						)), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter('A1:T' . $sheet->getHighestRow());

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
				$sheet->getStyle('A' . (sizeof($print_data) + 1) . ':T' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

				$nombreArchivo = 'InformeReclamos_' . date('Ymd');
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
		$data['title_view'] = 'Informe de Turnos';
		$data['title'] = TITLE . ' - Informe de Turnos';
		$this->load_template('reclamos_gis/reclamos/reclamos_exportar', $data);
	}
}