<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lineas extends MY_Controller
{

	/**
	 * Controlador de Líneas
	 * Autor: Leandro
	 * Creado: 03/09/2019
	 * Modificado: 03/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('telefonia/Lineas_model');
		$this->load->model('telefonia/Equipos_model');
		$this->load->model('telefonia/Prestadores_model');
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

		$tableData = array(
				'columns' => array(
						array('label' => 'Prestador', 'data' => 'prestador', 'width' => 8),
						array('label' => 'Número', 'data' => 'numero', 'width' => 6, 'class' => 'dt-body-right'),
						array('label' => 'SIM', 'data' => 'numero_sim', 'width' => 11, 'class' => 'dt-body-right'),
						array('label' => 'Modelo', 'data' => 'modelo', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'IMEI', 'data' => 'imei', 'width' => 10, 'class' => 'dt-body-right', 'class' => 'dt-body-right'),
						array('label' => 'Area', 'data' => 'area', 'width' => 10),
						array('label' => 'Personal', 'data' => 'persona_linea', 'width' => 10),
						array('label' => 'INac', 'data' => 'min_internacional', 'width' => 3, 'class' => 'dt-body-right'),
						array('label' => 'Nac', 'data' => 'min_nacional', 'width' => 3, 'class' => 'dt-body-right'),
						array('label' => 'Int', 'data' => 'min_interno', 'width' => 3, 'class' => 'dt-body-right'),
						array('label' => 'Dat', 'data' => 'datos', 'width' => 3, 'class' => 'dt-body-right'),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 8),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 6),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'lineas_table',
				'source_url' => 'telefonia/lineas/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_lineas_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		
		
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['array_estados'] = array('' => 'Todos', 'Baja' => 'Baja', 'Denunciada' => 'Denunciada', 'Disponible' => 'Disponible', 'En Uso' => 'En Uso', 'No Disponible' => 'No Disponible');
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Líneas';
		$data['title'] = TITLE . ' - Líneas';
		$this->load_template('telefonia/lineas/lineas_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('telefonia/datatables_functions_helper');
		$this->datatables
				->select("tm_lineas.id, tm_prestadores.nombre as prestador, tm_lineas.numero, tm_lineas.numero_sim, tm_lineas.estado, tm_modelos.nombre as modelo, tm_equipos.imei as imei, CONCAT(areas.codigo, ' - ', areas.nombre) as area, COALESCE(CONCAT(tm_equipos.labo_Codigo, COALESCE(CONCAT(' - ', personal.Apellido, ', ', personal.Nombre), '')), CONCAT('EXT: ', tm_equipos.persona), '') as persona_linea, tm_lineas.min_internacional, tm_lineas.min_nacional, tm_lineas.min_interno, tm_lineas.datos, tm_lineas.observaciones")
				->from('tm_lineas')
				->join('tm_prestadores', 'tm_prestadores.id = tm_lineas.prestador_id', 'left')
				->join('tm_equipos', 'tm_equipos.id = tm_lineas.equipo_id', 'left')
				->join('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'left')
				->join('areas', 'areas.id = tm_lineas.area_id', 'left')
				->join('personal', 'personal.Legajo = tm_lineas.labo_Codigo', 'left')
				->edit_column('estado', '$1', 'dt_column_lineas_estado(estado)', TRUE)
				->add_column('ver', '<a href="telefonia/lineas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="telefonia/lineas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="telefonia/lineas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('telefonia/lineas/listar', 'refresh');
		}

		$this->array_prestador_control = $array_prestador = $this->get_array('Prestadores', 'nombre');
		$this->array_estado_control = $array_estado = array('Baja' => 'Baja', 'Denunciada' => 'Denunciada', 'Disponible' => 'Disponible', 'No Disponible' => 'No Disponible');
		$this->set_model_validation_rules($this->Lineas_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Lineas_model->create(array(
					'prestador_id' => $this->input->post('prestador'),
					'numero' => $this->input->post('numero'),
					'numero_corto' => $this->input->post('numero_corto'),
					'numero_sim' => $this->input->post('numero_sim'),
					'min_internacional' => $this->input->post('min_internacional'),
					'min_nacional' => $this->input->post('min_nacional'),
					'min_interno' => $this->input->post('min_interno'),
					'datos' => $this->input->post('datos'),
					'estado' => $this->input->post('estado'),
					'observaciones' => $this->input->post('observaciones')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Lineas_model->get_msg());
				redirect('telefonia/lineas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Lineas_model->get_error())
				{
					$error_msg .= $this->Lineas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Lineas_model->fields['prestador']['array'] = $array_prestador;
		$this->Lineas_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($this->Lineas_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Línea';
		$data['title'] = TITLE . ' - Agregar Línea';
		$this->load_template('telefonia/lineas/lineas_abm', $data);
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
			redirect("telefonia/lineas/ver/$id", 'refresh');
		}

		$this->array_prestador_control = $array_prestador = $this->get_array('Prestadores', 'nombre');
		$this->array_estado_control = $array_estado = array('Baja' => 'Baja', 'Denunciada' => 'Denunciada', 'Disponible' => 'Disponible', 'En Uso' => 'En Uso', 'No Disponible' => 'No Disponible');
		$linea = $this->Lineas_model->get(array('id' => $id));
		if (empty($linea))
		{
			show_error('No se encontró la Línea', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Lineas_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Lineas_model->update(array(
						'id' => $this->input->post('id'),
						'prestador_id' => $this->input->post('prestador'),
						'numero' => $this->input->post('numero'),
						'numero_corto' => $this->input->post('numero_corto'),
						'numero_sim' => $this->input->post('numero_sim'),
						'min_internacional' => $this->input->post('min_internacional'),
						'min_nacional' => $this->input->post('min_nacional'),
						'min_interno' => $this->input->post('min_interno'),
						'datos' => $this->input->post('datos'),
						'estado' => $this->input->post('estado'),
						'observaciones' => $this->input->post('observaciones')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Lineas_model->get_msg());
					redirect('telefonia/lineas/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Lineas_model->get_error())
					{
						$error_msg .= $this->Lineas_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Lineas_model->fields['prestador']['array'] = $array_prestador;
		$this->Lineas_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($this->Lineas_model->fields, $linea);
		$data['linea'] = $linea;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Línea';
		$data['title'] = TITLE . ' - Editar Línea';
		$this->load_template('telefonia/lineas/lineas_abm', $data);
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
			redirect("telefonia/lineas/ver/$id", 'refresh');
		}

		$linea = $this->Lineas_model->get_one($id);
		if (empty($linea))
		{
			show_error('No se encontró la Línea', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Lineas_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Lineas_model->get_msg());
				redirect('telefonia/lineas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Lineas_model->get_error())
				{
					$error_msg .= $this->Lineas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Lineas_model->fields, $linea, TRUE);
		$data['linea'] = $linea;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Línea';
		$data['title'] = TITLE . ' - Eliminar Línea';
		$this->load_template('telefonia/lineas/lineas_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$linea = $this->Lineas_model->get_one($id);
		if (empty($linea))
		{
			show_error('No se encontró la Línea', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Lineas_model->fields, $linea, TRUE);
		$data['linea'] = $linea;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Línea';
		$data['title'] = TITLE . ' - Ver Línea';
		$this->load_template('telefonia/lineas/lineas_abm', $data);
	}
}