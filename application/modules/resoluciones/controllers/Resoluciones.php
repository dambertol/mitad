<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Resoluciones extends MY_Controller
{

	/**
	 * Controlador de Resoluciones
	 * Autor: Leandro
	 * Creado: 29/11/2017
	 * Modificado: 07/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('resoluciones/Resoluciones_model');
		$this->load->model('resoluciones/Tipos_resoluciones_model');
		$this->load->model('resoluciones/Adjuntos_model');
		$this->grupos_permitidos = array('admin', 'resoluciones_user', 'resoluciones_consulta_general');
		$this->grupos_solo_consulta = array('resoluciones_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar($resolucion_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tableData = array(
				'columns' => array(
						array('label' => 'Tipo', 'data' => 'tipo_resolucion', 'width' => 7),
						array('label' => 'Numero', 'data' => 'numero', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Ejercicio', 'data' => 'ejercicio', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Título', 'data' => 'titulo', 'width' => 30),
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 9, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Exp N°', 'data' => 'expt_numero', 'width' => 7, 'class' => 'dt-body-right'),
						array('label' => 'Exp Ej', 'data' => 'expt_ejercicio', 'width' => 7, 'class' => 'dt-body-right'),
						array('label' => 'Exp Mat', 'data' => 'expt_matricula', 'width' => 7, 'class' => 'dt-body-right'),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
						array('label' => '', 'data' => 'imprimir', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'resoluciones_table',
				'source_url' => 'resoluciones/resoluciones/listar_data',
				'order' => array(array(0, 'asc'), array(2, 'desc'), array(1, 'desc')),
				'reuse_var' => TRUE,
				'initComplete' => "complete_resoluciones_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['resolucion_id'] = $resolucion_id;
		$data['array_tipos'] = $this->get_array('Tipos_resoluciones', 'codigo', 'codigo', array(), array('' => 'Todos'));
		$data['array_estados'] = array('' => 'Todas', 'Activa' => 'Activa', 'Anulada' => 'Anulada');
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Resoluciones';
		$data['title'] = TITLE . ' - Resoluciones';
		$this->load_template('resoluciones/resoluciones/resoluciones_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('resoluciones/datatables_functions_helper');
		$this->datatables
				->select("re_resoluciones.id, re_tipos_resoluciones.codigo as tipo_resolucion, re_resoluciones.numero, re_resoluciones.ejercicio, re_resoluciones.titulo, re_resoluciones.fecha, re_resoluciones.expt_numero, re_resoluciones.expt_ejercicio, re_resoluciones.expt_matricula, re_resoluciones.estado, re_resoluciones.estado as estadore")
				->custom_sort('tipo_resolucion', 're_tipos_resoluciones.nombre')
				->custom_sort('estado_re', 're_resoluciones.estado')
				->from('re_resoluciones')
				->join('re_tipos_resoluciones', 're_tipos_resoluciones.id = re_resoluciones.tipo_resolucion_id', 'left')
				->add_column('estado', '$1', 'dt_column_resoluciones_estado(estado, id)', TRUE)
				->add_column('imprimir', '$1', 'dt_column_resoluciones_imprimir(estadore, id)')
				->add_column('ver', '<a href="resoluciones/resoluciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '$1', 'dt_column_resoluciones_editar(estadore, id)');

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
			$this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
			redirect("resoluciones/resoluciones/listar", 'refresh');
		}

		$this->array_tipo_resolucion_control = $array_tipo_resolucion = $this->get_array('Tipos_resoluciones', 'codigo');
		$this->array_formato_control = $array_formato = array('T' => 'Texto', 'A' => 'Archivo');
		$this->set_model_validation_rules($this->Resoluciones_model);
		if ($this->input->post('formato') === 'T')
		{
			$this->form_validation->set_rules('texto', 'Texto', 'required');
		}
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
			$numero = $this->Resoluciones_model->get_ultima_resolucion($this->input->post('tipo_resolucion'), $this->input->post('ejercicio'));

			if ($this->input->post('formato') === 'A')
			{
				$texto = NULL;
			}
			else
			{
				$encabezado = '<p style="text-align: center;">
				<img src="img/resoluciones/escudo_001.jpg" alt="Luján de Cuyo" width="80" height="87" /></p>
				<h4 style="text-align: right;">Luján de Cuyo, ' . strftime('%d de %B de %Y', date_timestamp_get($fecha)) . '</h4>';
				$encabezado .= '<p><b>RESOLUCIÓN ' . $array_tipo_resolucion[$this->input->post('tipo_resolucion')] . ' N°: ' . $numero . '/' . $this->input->post('ejercicio') . '</b></p>';
				$encabezado .= '<span id="finencabezado"></span>';
				$texto = $encabezado . $this->input->post('texto');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Resoluciones_model->create(array(
					'tipo_resolucion_id' => $this->input->post('tipo_resolucion'),
					'numero' => $numero,
					'ejercicio' => $this->input->post('ejercicio'),
					'titulo' => $this->input->post('titulo'),
					'fecha' => $fecha->format('Y-m-d'),
					'expt_numero' => $this->input->post('expt_numero'),
					'expt_ejercicio' => $this->input->post('expt_ejercicio'),
					'expt_matricula' => $this->input->post('expt_matricula'),
					'texto' => $texto,
					'usuario_carga' => $this->session->userdata('user_id'),
					'fecha_carga' => (new DateTime())->format('Y-m-d'),
					'estado' => 'Activa'), FALSE);
			$resolucion_id = $this->Resoluciones_model->get_row_id();
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Resoluciones_model->get_msg());
				if ($this->input->post('formato') === 'A')
				{
					redirect("resoluciones/resoluciones/editar/$resolucion_id", 'refresh');
				}
				else
				{
					redirect("resoluciones/resoluciones/listar/$resolucion_id", 'refresh');
				}
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Resoluciones_model->get_error())
				{
					$error_msg .= $this->Resoluciones_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Resoluciones_model->fields['tipo_resolucion']['array'] = $array_tipo_resolucion;
		$this->Resoluciones_model->fields['formato']['array'] = $array_formato;
		$data['fields'] = $this->build_fields($this->Resoluciones_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Resolución';
		$data['title'] = TITLE . ' - Agregar Resolución';
		$data['js'][] = 'vendor/tinymce/tinymce.min.js';
		$data['js'][] = 'vendor/tinymce/langs/es_AR.js';
		$this->load_template('resoluciones/resoluciones/resoluciones_abm', $data);
	}

	public function editar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
			redirect("resoluciones/resoluciones/ver/$id", 'refresh');
		}

		$resolucion = $this->Resoluciones_model->get(array('id' => $id));
		if (empty($resolucion) || $resolucion->estado === 'Anulada')
		{
			show_error('No se encontró la Resolución', 500, 'Registro no encontrado');
		}

		if (empty($resolucion->texto))
		{
			$archivos = $this->Adjuntos_model->get(array(
					'resolucion_id' => $id,
					'sort_by' => 'id DESC'
			));
			if (!empty($archivos))
			{
				$data['archivo'] = $archivos[0];
			}
		}

		$this->array_tipo_resolucion_control = $array_tipo_resolucion = $this->get_array('Tipos_resoluciones', 'codigo', 'id', array(), array('' => ''));
		$this->array_formato_control = $array_formato = array('T' => 'Texto', 'A' => 'Archivo');

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'tipo_resolucion' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
				'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'titulo' => array('label' => 'Título', 'maxlength' => '255', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5'),
				'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4'),
				'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1'),
				'formato' => array('label' => 'Formato', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'texto' => array('label' => 'Texto *', 'form_type' => 'textarea', 'rows' => 5),
				'path' => array('label' => 'Archivo *', 'type' => 'file')
		);

		$this->set_model_validation_rules($fake_model);
		if ($this->input->post('formato') === 'T')
		{
			$this->form_validation->set_rules('texto', 'Texto', 'required');
		}
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
				if ($this->input->post('formato') === 'A')
				{
					if (!empty($_FILES['path']['name']))
					{
						$config['upload_path'] = "uploads/resoluciones/resoluciones/" . $resolucion->ejercicio . "/";
						if (!file_exists($config['upload_path']))
						{
							mkdir($config['upload_path'], 0755, TRUE);
						}
						$config['file_name'] = str_pad($resolucion->tipo_resolucion_id, 2, "0", STR_PAD_LEFT) . '_' . str_pad($resolucion->numero, 4, "0", STR_PAD_LEFT) . '.pdf';
						$config['file_ext_tolower'] = TRUE;
						$config['allowed_types'] = 'pdf';
						$config['max_size'] = 4096;
						$this->load->library('upload', $config);
						if (!$this->upload->do_upload('path'))
						{
							$error_msg = $this->upload->display_errors();
						}
						else
						{
							$upload_data = $this->upload->data();
						}
					}
					$texto = 'NULL';
				}
				else
				{
					$encabezado = '<p style="text-align: center;">
				<img src="img/resoluciones/escudo_001.jpg" alt="Luján de Cuyo" width="80" height="87" /></p>
				<h4 style="text-align: right;">Luján de Cuyo, ' . strftime('%d de %B de %Y', date_timestamp_get($fecha)) . '</h4>';
					$encabezado .= '<p><b>RESOLUCIÓN ' . $array_tipo_resolucion[$resolucion->tipo_resolucion_id] . ' N°: ' . $resolucion->numero . '/' . $resolucion->ejercicio . '</b></p>';
					$encabezado .= '<span id="finencabezado"></span>';
					$texto = $encabezado . $this->input->post('texto');
				}

				if (empty($error_msg))
				{
					$this->db->trans_begin();
					$trans_ok = TRUE;
					$trans_ok &= $this->Resoluciones_model->update(array(
							'id' => $this->input->post('id'),
							'tipo_resolucion_id' => $resolucion->tipo_resolucion_id,
							'numero' => $resolucion->numero,
							'ejercicio' => $resolucion->ejercicio,
							'titulo' => $this->input->post('titulo'),
							'fecha' => $fecha->format('Y-m-d'),
							'expt_ejercicio' => $this->input->post('expt_ejercicio'),
							'expt_numero' => $this->input->post('expt_numero'),
							'expt_matricula' => $this->input->post('expt_matricula'),
							'texto' => $texto,
							'estado' => $resolucion->estado), FALSE);

					if ($this->input->post('formato') === 'A' && !empty($_FILES['path']['name']))
					{
						$trans_ok &= $this->Adjuntos_model->create(array(
								'nombre' => $upload_data['file_name'],
								'ruta' => $config['upload_path'],
								'tamanio' => round($upload_data['file_size'], 2),
								'hash' => md5_file($config['upload_path'] . $upload_data['file_name']),
								'fecha_subida' => (new DateTime())->format('Y-m-d H:i'),
								'usuario_subida' => $this->session->userdata('user_id'),
								'resolucion_id' => $this->input->post('id')), FALSE);
					}

					if ($this->db->trans_status() && $trans_ok)
					{
						$this->db->trans_commit();
						$this->session->set_flashdata('message', $this->Resoluciones_model->get_msg());
						redirect('resoluciones/resoluciones/listar', 'refresh');
					}
					else
					{
						if ($this->input->post('formato') === 'A' && !empty($_FILES['path']['name']))
						{
							unlink($config['upload_path'] . $upload_data['file_name']);
						}
						$this->db->trans_rollback();
						$error_msg = '<br />Se ha producido un error con la base de datos.';
						if ($this->Resoluciones_model->get_error())
						{
							$error_msg .= $this->Resoluciones_model->get_error();
						}
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$resolucion->formato_id = empty($resolucion->texto) ? 'A' : 'T';
		$resolucion->texto = substr($resolucion->texto, strpos($resolucion->texto, '<span id="finencabezado"></span>') + 32);
		$resolucion->path = NULL;
		$fake_model->fields['tipo_resolucion']['array'] = $array_tipo_resolucion;
		$fake_model->fields['formato']['array'] = $array_formato;
		$data['fields'] = $this->build_fields($fake_model->fields, $resolucion);
		$data['resolucion'] = $resolucion;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Resolución';
		$data['title'] = TITLE . ' - Editar Resolución';
		$data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
		$data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
		$data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
		$data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
		$data['js'][] = 'vendor/tinymce/tinymce.min.js';
		$data['js'][] = 'vendor/tinymce/langs/es_AR.js';
		$this->load_template('resoluciones/resoluciones/resoluciones_abm', $data);
	}

	public function anular($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
			redirect("resoluciones/resoluciones/ver/$id", 'refresh');
		}

		$resolucion = $this->Resoluciones_model->get_one($id);
		if (empty($resolucion))
		{
			show_error('No se encontró la Resolución', 500, 'Registro no encontrado');
		}

		if (empty($resolucion->texto))
		{
			$archivos = $this->Adjuntos_model->get(array(
					'resolucion_id' => $id,
					'sort_by' => 'id DESC'
			));
			if (!empty($archivos))
			{
				$data['archivo'] = $archivos[0];
			}
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'tipo_resolucion' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
				'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'titulo' => array('label' => 'Título', 'maxlength' => '255', 'disabled' => 'disabled'),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
				'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5', 'disabled' => 'disabled'),
				'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1', 'disabled' => 'disabled'),
				'estado' => array('label' => 'Estado', 'maxlength' => '255', 'disabled' => 'disabled'),
				'motivo' => array('label' => 'Motivo Baja', 'maxlength' => '255', 'required' => TRUE),
				'formato' => array('label' => 'Formato', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
				'texto' => array('label' => 'Texto', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled')
		);

		$array_tipo_resolucion = $this->get_array('Tipos_resoluciones', 'codigo');
		$array_formato = array('T' => 'Texto', 'A' => 'Archivo');

		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Resoluciones_model->update(array(
					'id' => $this->input->post('id'),
					'tipo_resolucion_id' => $resolucion->tipo_resolucion_id,
					'numero' => $resolucion->numero,
					'ejercicio' => $resolucion->ejercicio,
					'estado' => 'Anulada',
					'motivo' => $this->input->post('motivo')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Resoluciones_model->get_msg());
				redirect('resoluciones/resoluciones/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Resoluciones_model->get_error())
				{
					$error_msg .= $this->Resoluciones_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$resolucion->formato_id = empty($resolucion->texto) ? 'A' : 'T';
		$resolucion->texto = substr($resolucion->texto, strpos($resolucion->texto, '<span id="finencabezado"></span>') + 32);
		$fake_model->fields['tipo_resolucion']['array'] = $array_tipo_resolucion;
		$fake_model->fields['formato']['array'] = $array_formato;
		$data['fields'] = $this->build_fields($fake_model->fields, $resolucion);
		$data['resolucion'] = $resolucion;
		$data['txt_btn'] = 'Anular';
		$data['title_view'] = 'Anular Resolución';
		$data['title'] = TITLE . ' - Anular Resolución';
		$data['js'][] = 'vendor/tinymce/tinymce.min.js';
		$data['js'][] = 'vendor/tinymce/langs/es_AR.js';
		$this->load_template('resoluciones/resoluciones/resoluciones_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$resolucion = $this->Resoluciones_model->get_one($id);
		if (empty($resolucion))
		{
			show_error('No se encontró el Resolución', 500, 'Registro no encontrado');
		}

		if (empty($resolucion->texto))
		{
			$archivos = $this->Adjuntos_model->get(array(
					'resolucion_id' => $id,
					'sort_by' => 'id DESC'
			));
			if (!empty($archivos))
			{
				$data['archivo'] = $archivos[0];
			}
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'tipo_resolucion' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
				'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'titulo' => array('label' => 'Título', 'maxlength' => '255', 'disabled' => 'disabled'),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'disabled' => 'disabled'),
				'expt_numero' => array('label' => 'Expt Número', 'type' => 'integer', 'maxlength' => '5', 'disabled' => 'disabled'),
				'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'disabled' => 'disabled'),
				'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1', 'disabled' => 'disabled'),
				'estado' => array('label' => 'Estado', 'maxlength' => '255', 'disabled' => 'disabled'),
				'motivo' => array('label' => 'Motivo Baja', 'maxlength' => '255', 'disabled' => 'disabled'),
				'formato' => array('label' => 'Formato', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
				'texto' => array('label' => 'Texto', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled')
		);

		$resolucion->formato = empty($resolucion->texto) ? 'Archivo' : 'Texto';
		$resolucion->texto = substr($resolucion->texto, strpos($resolucion->texto, '<span id="finencabezado"></span>') + 32);
		$data['fields'] = $this->build_fields($fake_model->fields, $resolucion, TRUE);
		$data['resolucion'] = $resolucion;

		$data['error'] = $this->session->flashdata('error');
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Resolución';
		$data['title'] = TITLE . ' - Ver Resolución';
		$data['js'][] = 'vendor/tinymce/tinymce.min.js';
		$data['js'][] = 'vendor/tinymce/langs/es_AR.js';
		$this->load_template('resoluciones/resoluciones/resoluciones_abm', $data);
	}

	public function imprimir($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$resolucion = $this->Resoluciones_model->get(array(
				'id' => $id,
				'join' => array(
						array(
								'type' => 'LEFT',
								'table' => 're_tipos_resoluciones',
								'where' => 're_tipos_resoluciones.id = re_resoluciones.tipo_resolucion_id',
								'columnas' => 'codigo as tipo'
						)
				)
		));
		if (empty($resolucion) || $resolucion->estado === 'Anulada')
		{
			show_error('No se encontró el Resolución', 500, 'Registro no encontrado');
		}

		if (empty($resolucion->texto))
		{
			$archivos = $this->Adjuntos_model->get(array(
					'resolucion_id' => $id,
					'sort_by' => 'id DESC'
			));

			if (!empty($archivos))
			{
				$this->load->helper('file');
				if (file_exists($archivos[0]->ruta . $archivos[0]->nombre)) // check the file is existing 
				{
					if (md5_file($archivos[0]->ruta . $archivos[0]->nombre) === $archivos[0]->hash)
					{
						$extension = strtolower(substr(strrchr($archivos[0]->nombre, '.'), 1));
						header('Content-Type: ' . get_mime_by_extension($archivos[0]->ruta . $archivos[0]->nombre));
						header('Content-Length: ' . filesize($archivos[0]->ruta . $archivos[0]->nombre));
						header('Content-Disposition: attachment; filename=' . str_replace(' ', '_', $archivos[0]->nombre) . '.' . $extension);
						readfile($archivos[0]->ruta . $archivos[0]->nombre);
						return;
					}
					else
					{
						show_error('Archivo inválido', 500, 'Registro no encontrado');
					}
				}
				else
				{
					show_error('No se encontró el archivo', 500, 'Registro no encontrado');
				}
			}
			else
			{
				$this->session->set_flashdata('error', '<br />No hay archivo adjunto');
				redirect('resoluciones/resoluciones/listar', 'refresh');
			}
		}
		else
		{
			$mpdf = new \Mpdf\Mpdf([
					'mode' => 'c',
					'format' => 'A4',
					'margin_left' => 6,
					'margin_right' => 6,
					'margin_top' => 6,
					'margin_bottom' => 6,
					'margin_header' => 9,
					'margin_footer' => 9
			]);
			$mpdf->SetDisplayMode('fullwidth');
			$mpdf->simpleTables = true;
			$mpdf->SetTitle("Resolución $resolucion->tipo $resolucion->numero/$resolucion->ejercicio");
			$mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
			$mpdf->SetFooter("{PAGENO} de {nb}");
			$mpdf->WriteHTML($resolucion->texto, 2);
			$mpdf->Output("Resolución $resolucion->tipo $resolucion->numero $resolucion->ejercicio" . '.pdf', 'D');
		}
	}
}