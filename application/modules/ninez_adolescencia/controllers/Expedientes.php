<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expedientes extends MY_Controller
{

	/**
	 * Controlador de Expedientes
	 * Autor: Leandro
	 * Creado: 12/09/2019
	 * Modificado: 23/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('ninez_adolescencia/Expedientes_model');
		$this->load->model('Personas_model');
		$this->grupos_permitidos = array('admin', 'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general');
		$this->grupos_solo_consulta = array('ninez_adolescencia_consulta_general');
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
						array('label' => 'N°', 'data' => 'nro_expediente', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Adulto Responsable', 'data' => 'adulto_responsable', 'width' => 36),
						array('label' => 'Desde Exp', 'data' => 'fecha_desde_exp', 'width' => 15, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Hasta Exp', 'data' => 'fecha_hasta_exp', 'width' => 15, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'U° Movimiento', 'data' => 'fecha_movimiento', 'width' => 15, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'expedientes_table',
				'source_url' => 'ninez_adolescencia/expedientes/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_expedientes_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Expedientes';
		$data['title'] = TITLE . ' - Expedientes';
		$this->load_template('ninez_adolescencia/expedientes/expedientes_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select("na_expedientes.id, na_expedientes.nro_expediente, CONCAT(personas.apellido, ', ', personas.nombre,  ' (', personas.dni, ')') as adulto_responsable, na_expedientes.fecha_desde_exp, na_expedientes.fecha_hasta_exp, na_expedientes.fecha_movimiento")
				->from('na_expedientes')
				->join('na_adultos_responsables', 'na_adultos_responsables.expediente_id = na_expedientes.id', 'left')
				->join('na_adultos_responsables R', 'R.expediente_id = na_expedientes.id AND na_adultos_responsables.hasta < R.hasta', 'left outer')
				->join('personas', 'personas.id = na_adultos_responsables.persona_id', 'left')
				->where('R.id IS NULL')
				->add_column('ver', '<a href="ninez_adolescencia/expedientes/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="ninez_adolescencia/expedientes/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="ninez_adolescencia/expedientes/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function buscador()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->array_tipo_control = $array_tipo = array('adultos' => 'Adultos Responsables', 'menores' => 'Menores');
		$fake_model = new stdClass();
		$fake_model->fields = array(
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE, 'array' => $array_tipo),
				'dni' => array('label' => 'DNI', 'type' => 'integer', 'maxlength' => '8'),
				'apellido' => array('label' => 'Apellido', 'maxlength' => '50')
		);
		$this->set_model_validation_rules($fake_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$tipo = $this->input->post('tipo');
			$dni = $this->input->post('dni');
			$apellido = $this->input->post('apellido');

			if (empty($dni) && empty($apellido))
			{
				$error_msg = '<br />Debe ingresar DNI o Apellido';
			}
			else
			{
				if ($tipo === 'menores')
				{
					if (!empty($dni))
					{
						$personas_temp = $this->Personas_model->get(array(
								'dni' => $dni,
								'select' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre as nacionalidad', 'na_expedientes.id as id_expediente'),
								'join' => array(
										array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad')),
										array('na_menores', 'na_menores.persona_id = personas.id', 'RIGHT'),
										array('na_expedientes', 'na_expedientes.id = na_menores.expediente_id', 'LEFT',
												array(
														'na_expedientes.id as id_expediente',
														'na_expedientes.nro_expediente',
												)
										)
								),
								'group_by' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre', 'na_expedientes.id')
						));
					}
					else
					{
						$personas_temp = $this->Personas_model->get(array(
								'apellido like both' => $apellido,
								'select' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre as nacionalidad', 'na_expedientes.id as id_expediente'),
								'join' => array(
										array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad')),
										array('na_menores', 'na_menores.persona_id = personas.id', 'RIGHT'),
										array('na_expedientes', 'na_expedientes.id = na_menores.expediente_id', 'LEFT',
												array(
														'na_expedientes.id as id_expediente',
														'na_expedientes.nro_expediente',
												)
										)
								),
								'group_by' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre', 'na_expedientes.id')
						));
					}
				}
				else
				{
					if (!empty($dni))
					{
						$personas_temp = $this->Personas_model->get(array(
								'dni' => $dni,
								'select' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre as nacionalidad', 'na_expedientes.id as id_expediente', 'na_expedientes.nro_expediente as nro_expediente'),
								'join' => array(
										array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT'),
										array('na_adultos_responsables', 'na_adultos_responsables.persona_id = personas.id', 'RIGHT'),
										array('na_expedientes', 'na_expedientes.id = na_adultos_responsables.expediente_id', 'LEFT')
								),
								'group_by' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre', 'na_expedientes.id', 'na_expedientes.nro_expediente')
						));
					}
					else
					{
						$personas_temp = $this->Personas_model->get(array(
								'apellido like both' => $apellido,
								'select' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre as nacionalidad', 'na_expedientes.id as id_expediente', 'na_expedientes.nro_expediente as nro_expediente'),
								'join' => array(
										array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT'),
										array('na_adultos_responsables', 'na_adultos_responsables.persona_id = personas.id', 'RIGHT'),
										array('na_expedientes', 'na_expedientes.id = na_adultos_responsables.expediente_id', 'LEFT')
								),
								'group_by' => array('personas.id', 'personas.dni', 'personas.nombre', 'personas.apellido', 'personas.telefono', 'personas.celular', 'personas.email', 'personas.fecha_nacimiento', 'nacionalidades.nombre', 'na_expedientes.id', 'na_expedientes.nro_expediente')
						));
					}
				}

				$personas = array();
				if (!empty($personas_temp))
				{
					foreach ($personas_temp as $Pers)
					{
						if (isset($personas[$Pers->id]))
						{
							$personas[$Pers->id]->exp_count++;
							$personas[$Pers->id]->expedientes[$Pers->id_expediente] = $Pers->nro_expediente;
						}
						else
						{
							$personas[$Pers->id] = $Pers;
							$personas[$Pers->id]->exp_count = 1;
							$personas[$Pers->id]->expedientes = array($Pers->id_expediente => $Pers->nro_expediente);
						}
					}
				}
				$data['personas'] = $personas;

				if (empty($personas))
				{
					$error_msg = '<br />No se encontraron personas';
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['message'] = $this->session->flashdata('message');
		$data['txt_btn'] = 'Buscar';
		$data['title_view'] = 'Buscador de personas por DNI o apellido';
		$data['title'] = TITLE . ' - Buscador';
		$this->load_template('ninez_adolescencia/expedientes/expedientes_buscador', $data);
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
			redirect('ninez_adolescencia/expedientes/listar', 'refresh');
		}

		$this->set_model_validation_rules($this->Expedientes_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$fecha = new DateTime();
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Expedientes_model->create(array(
					'nro_expediente' => $this->input->post('nro_expediente'),
					'fecha_desde_exp' => $this->get_date_sql('fecha_desde_exp'),
					'fecha_hasta_exp' => $this->get_date_sql('fecha_hasta_exp'),
					'fecha_movimiento' => $fecha->format('Y-m-d H:i:s')), FALSE);

			$expediente_id = $this->Expedientes_model->get_row_id();

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Expedientes_model->get_msg());
				redirect("ninez_adolescencia/expedientes/ver/$expediente_id", 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Expedientes_model->get_error())
				{
					$error_msg .= $this->Expedientes_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Expedientes_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Expediente';
		$data['title'] = TITLE . ' - Agregar Expediente';
		$this->load_template('ninez_adolescencia/expedientes/expedientes_abm', $data);
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
			redirect("ninez_adolescencia/expedientes/ver/$id", 'refresh');
		}

		$expedient = $this->Expedientes_model->get(array('id' => $id));
		if (empty($expedient))
		{
			show_error('No se encontró el Expediente', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Expedientes_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$fecha = new DateTime();
				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Expedientes_model->update(array(
						'id' => $this->input->post('id'),
						'nro_expediente' => $this->input->post('nro_expediente'),
						'fecha_desde_exp' => $this->get_date_sql('fecha_desde_exp'),
						'fecha_hasta_exp' => $this->get_date_sql('fecha_hasta_exp'),
						'fecha_movimiento' => $fecha->format('Y-m-d H:i:s')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Expedientes_model->get_msg());
					redirect('ninez_adolescencia/expedientes/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Expedientes_model->get_error())
					{
						$error_msg .= $this->Expedientes_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Expedientes_model->fields, $expedient);
		$data['expedient'] = $expedient;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Expediente';
		$data['title'] = TITLE . ' - Editar Expediente';
		$this->load_template('ninez_adolescencia/expedientes/expedientes_abm', $data);
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
			redirect("ninez_adolescencia/expedientes/ver/$id", 'refresh');
		}

		$expedient = $this->Expedientes_model->get_one($id);
		if (empty($expedient))
		{
			show_error('No se encontró el Expediente', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Expedientes_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Expedientes_model->get_msg());
				redirect('ninez_adolescencia/expedientes/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Expedientes_model->get_error())
				{
					$error_msg .= $this->Expedientes_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Expedientes_model->fields, $expedient, TRUE);
		$data['expedient'] = $expedient;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Expediente';
		$data['title'] = TITLE . ' - Eliminar Expediente';
		$this->load_template('ninez_adolescencia/expedientes/expedientes_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$expedient = $this->Expedientes_model->get_one($id);
		if (empty($expedient))
		{
			show_error('No se encontró el Expediente', 500, 'Registro no encontrado');
		}

		$tableData_adultos = array(
				'columns' => array(
						array('label' => 'Persona', 'data' => 'persona', 'width' => 70),
						array('label' => 'Hasta', 'data' => 'hasta', 'width' => 21, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'adultos_responsables_table',
				'source_url' => "ninez_adolescencia/adultos_responsables/listar_data/$id",
				'reuse_var' => TRUE,
				'initComplete' => 'complete_adultos_responsables_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table_adultos'] = buildHTML($tableData_adultos);
		$data['js_table_adultos'] = buildJS($tableData_adultos);

		$tableData_menores = array(
				'columns' => array(
						array('label' => 'Persona', 'data' => 'persona', 'width' => 91),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'menores_table',
				'source_url' => "ninez_adolescencia/menores/listar_data/$id",
				'reuse_var' => TRUE,
				'initComplete' => 'complete_menores_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table_menores'] = buildHTML($tableData_menores);
		$data['js_table_menores'] = buildJS($tableData_menores);

		$tableData_intervenciones = array(
				'columns' => array(
						array('label' => 'Fecha', 'data' => 'fecha_intervencion', 'width' => 20, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Tipo', 'data' => 'tipo_intervencion', 'width' => 71),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'intervenciones_table',
				'source_url' => "ninez_adolescencia/intervenciones/listar_data/$id",
				'reuse_var' => TRUE,
				'initComplete' => 'complete_intervenciones_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table_intervenciones'] = buildHTML($tableData_intervenciones);
		$data['js_table_intervenciones'] = buildJS($tableData_intervenciones);

		$tableData_adjuntos = array(
				'columns' => array(
						array('label' => 'Fecha', 'data' => 'fecha_subida', 'width' => 20, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Tipo', 'data' => 'tipo', 'width' => 30),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 44),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'adjuntos_table',
				'source_url' => "ninez_adolescencia/adjuntos/listar_data/$id",
				'reuse_var' => TRUE,
				'initComplete' => 'complete_adjuntos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table_adjuntos'] = buildHTML($tableData_adjuntos);
		$data['js_table_adjuntos'] = buildJS($tableData_adjuntos);

		$data['fields'] = $this->build_fields($this->Expedientes_model->fields, $expedient, TRUE);
		$data['expedient'] = $expedient;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Expediente';
		$data['title'] = TITLE . ' - Ver Expediente';
		$this->load_template('ninez_adolescencia/expedientes/expedientes_abm', $data);
	}

	public function visualizar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('ninez_adolescencia/Adjuntos_model');
		$adjuntos = $this->Adjuntos_model->get(array('expediente_id' => $id));
		$urls = array();
		$descripciones = array();
		if (!empty($adjuntos))
		{
			foreach ($adjuntos as $Adjunto)
			{
				$urls[] = base_url() . $Adjunto->ruta . $Adjunto->nombre;
				$descripciones[] = $Adjunto->descripcion;
			}
		}

		$data['expediente_id'] = $id;
		$data['urls'] = json_encode($urls);
		$data['descripciones'] = json_encode($descripciones);
		$this->load->view('ninez_adolescencia/expedientes/expedientes_visualizar', $data);
	}
}