<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Equipos extends MY_Controller
{

	/**
	 * Controlador de Equipos
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 03/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('telefonia/Equipos_model');
		$this->load->model('telefonia/Modelos_model');
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
						array('label' => 'Modelo', 'data' => 'modelo', 'width' => 12),
						array('label' => 'IMEI', 'data' => 'imei', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Prestador', 'data' => 'prestador', 'width' => 8),
						array('label' => 'Línea', 'data' => 'linea', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Área', 'data' => 'area', 'width' => 12),
						array('label' => 'Personal', 'data' => 'persona_equipo', 'width' => 12),
						array('label' => 'Compra', 'data' => 'fecha_compra', 'width' => 9, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 12),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'equipos_table',
				'source_url' => 'telefonia/equipos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_equipos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['array_estados'] = array('' => 'Todos', 'Disponible' => 'Disponible', 'En Uso' => 'En Uso', 'No Disponible' => 'No Disponible', 'Robado' => 'Robado');
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Equipos';
		$data['title'] = TITLE . ' - Equipos';
		$this->load_template('telefonia/equipos/equipos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('telefonia/datatables_functions_helper');
		$this->datatables
				->select("tm_equipos.id, tm_modelos.nombre as modelo, tm_equipos.imei, tm_equipos.estado, tm_prestadores.nombre as prestador, tm_lineas.numero as linea, CONCAT(areas.codigo, ' - ', areas.nombre) as area, COALESCE(CONCAT(tm_equipos.labo_Codigo, COALESCE(CONCAT(' - ', personal.Apellido, ', ', personal.Nombre), '')), CONCAT('EXT: ', tm_equipos.persona), '') as persona_equipo, tm_equipos.fecha_compra, tm_equipos.observaciones")
				->from('tm_equipos')
				->join('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'left')
				->join('tm_lineas', 'tm_lineas.equipo_id = tm_equipos.id', 'left')
				->join('tm_prestadores', 'tm_prestadores.id = tm_lineas.prestador_id', 'left')
				->join('areas', 'areas.id = tm_equipos.area_id', 'left')
				->join('personal', 'personal.Legajo = tm_equipos.labo_Codigo', 'left')
				->edit_column('estado', '$1', 'dt_column_equipos_estado(estado)', TRUE)
				->add_column('ver', '<a href="telefonia/equipos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="telefonia/equipos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="telefonia/equipos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('telefonia/equipos/listar', 'refresh');
		}

		$this->array_modelo_control = $array_modelo = $this->get_array('Modelos', 'nombre');
		$this->array_estado_control = $array_estado = array('Disponible' => 'Disponible', 'No Disponible' => 'No Disponible', 'Robado' => 'Robado');
		$this->set_model_validation_rules($this->Equipos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Equipos_model->create(array(
					'modelo_id' => $this->input->post('modelo'),
					'imei' => $this->input->post('imei'),
					'estado' => $this->input->post('estado'),
					'observaciones' => $this->input->post('observaciones'),
					'accesorios' => $this->input->post('accesorios'),
					'fecha_compra' => $this->get_date_sql('fecha_compra')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Equipos_model->get_msg());
				redirect('telefonia/equipos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Equipos_model->get_error())
				{
					$error_msg .= $this->Equipos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Equipos_model->fields['modelo']['array'] = $array_modelo;
		$this->Equipos_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($this->Equipos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Equipo';
		$data['title'] = TITLE . ' - Agregar Equipo';
		$this->load_template('telefonia/equipos/equipos_abm', $data);
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
			redirect("telefonia/equipos/ver/$id", 'refresh');
		}

		$this->array_modelo_control = $array_modelo = $this->get_array('Modelos', 'nombre');
		$this->array_estado_control = $array_estado = array('Disponible' => 'Disponible', 'En Uso' => 'En Uso', 'No Disponible' => 'No Disponible', 'Robado' => 'Robado');
		$equipo = $this->Equipos_model->get(array('id' => $id));
		if (empty($equipo))
		{
			show_error('No se encontró el Equipo', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Equipos_model);
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
				$trans_ok &= $this->Equipos_model->update(array(
						'id' => $this->input->post('id'),
						'modelo_id' => $this->input->post('modelo'),
						'imei' => $this->input->post('imei'),
						'estado' => $this->input->post('estado'),
						'observaciones' => $this->input->post('observaciones'),
						'accesorios' => $this->input->post('accesorios'),
						'fecha_compra' => $this->get_date_sql('fecha_compra')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Equipos_model->get_msg());
					redirect('telefonia/equipos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Equipos_model->get_error())
					{
						$error_msg .= $this->Equipos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Equipos_model->fields['modelo']['array'] = $array_modelo;
		$this->Equipos_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($this->Equipos_model->fields, $equipo);
		$data['equipo'] = $equipo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Equipo';
		$data['title'] = TITLE . ' - Editar Equipo';
		$this->load_template('telefonia/equipos/equipos_abm', $data);
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
			redirect("telefonia/equipos/ver/$id", 'refresh');
		}

		$equipo = $this->Equipos_model->get_one($id);
		if (empty($equipo))
		{
			show_error('No se encontró el Equipo', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Equipos_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Equipos_model->get_msg());
				redirect('telefonia/equipos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Equipos_model->get_error())
				{
					$error_msg .= $this->Equipos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Equipos_model->fields, $equipo, TRUE);
		$data['equipo'] = $equipo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Equipo';
		$data['title'] = TITLE . ' - Eliminar Equipo';
		$this->load_template('telefonia/equipos/equipos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$equipo = $this->Equipos_model->get_one($id);
		if (empty($equipo))
		{
			show_error('No se encontró el Equipo', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Equipos_model->fields, $equipo, TRUE);
		$data['equipo'] = $equipo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Equipo';
		$data['title'] = TITLE . ' - Ver Equipo';
		$this->load_template('telefonia/equipos/equipos_abm', $data);
	}
}