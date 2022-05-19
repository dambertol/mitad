<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Habilitaciones extends MY_Controller
{

	/**
	 * Controlador de Habilitaciones
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 13/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('antenas/Habilitaciones_model');
		$this->load->model('antenas/Torres_model');
		$this->grupos_permitidos = array('admin', 'antenas_admin', 'antenas_consulta_general');
		$this->grupos_solo_consulta = array('antenas_consulta_general');
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
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Expediente', 'data' => 'expediente', 'width' => 20, 'class' => 'dt-body-right'),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 20),
						array('label' => 'Torre', 'data' => 'torre', 'width' => 41),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'habilitaciones_table',
				'source_url' => 'antenas/habilitaciones/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_habilitaciones_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Habilitaciones';
		$data['title'] = TITLE . ' - Habilitaciones';
		$this->load_template('antenas/habilitaciones/habilitaciones_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('an_habilitaciones.id, an_habilitaciones.fecha, an_habilitaciones.expediente, an_habilitaciones.estado, an_torres.servicio as torre')
				->from('an_habilitaciones')
				->join('an_torres', 'an_torres.id = an_habilitaciones.torre_id', 'left')
				->add_column('ver', '<a href="antenas/habilitaciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="antenas/habilitaciones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="antenas/habilitaciones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('antenas/habilitaciones/listar', 'refresh');
		}

		$this->array_torre_control = $array_torre = $this->get_array('Torres', 'servicio');
		$this->set_model_validation_rules($this->Habilitaciones_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Habilitaciones_model->create(array(
					'fecha' => $fecha->format('Y-m-d'),
					'expediente' => $this->input->post('expediente'),
					'estado' => $this->input->post('estado'),
					'torre_id' => $this->input->post('torre')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Habilitaciones_model->get_msg());
				redirect('antenas/habilitaciones/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Habilitaciones_model->get_error())
				{
					$error_msg .= $this->Habilitaciones_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Habilitaciones_model->fields['torre']['array'] = $array_torre;
		$data['fields'] = $this->build_fields($this->Habilitaciones_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Habilitación';
		$data['title'] = TITLE . ' - Agregar Habilitación';
		$this->load_template('antenas/habilitaciones/habilitaciones_abm', $data);
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
			redirect("antenas/habilitaciones/ver/$id", 'refresh');
		}

		$this->array_torre_control = $array_torre = $this->get_array('Torres', 'servicio');
		$habilitacion = $this->Habilitaciones_model->get(array('id' => $id));
		if (empty($habilitacion))
		{
			show_error('No se encontró la Habilitación', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Habilitaciones_model);
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

				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Habilitaciones_model->update(array(
						'id' => $this->input->post('id'),
						'fecha' => $fecha->format('Y-m-d'),
						'expediente' => $this->input->post('expediente'),
						'estado' => $this->input->post('estado'),
						'torre_id' => $this->input->post('torre')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Habilitaciones_model->get_msg());
					redirect('antenas/habilitaciones/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Habilitaciones_model->get_error())
					{
						$error_msg .= $this->Habilitaciones_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Habilitaciones_model->fields['torre']['array'] = $array_torre;
		$data['fields'] = $this->build_fields($this->Habilitaciones_model->fields, $habilitacion);
		$data['habilitacion'] = $habilitacion;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Habilitación';
		$data['title'] = TITLE . ' - Editar Habilitación';
		$this->load_template('antenas/habilitaciones/habilitaciones_abm', $data);
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
			redirect("antenas/habilitaciones/ver/$id", 'refresh');
		}

		$habilitacion = $this->Habilitaciones_model->get_one($id);
		if (empty($habilitacion))
		{
			show_error('No se encontró la Habilitación', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Habilitaciones_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Habilitaciones_model->get_msg());
				redirect('antenas/habilitaciones/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Habilitaciones_model->get_error())
				{
					$error_msg .= $this->Habilitaciones_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Habilitaciones_model->fields, $habilitacion, TRUE);
		$data['habilitacion'] = $habilitacion;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Habilitación';
		$data['title'] = TITLE . ' - Eliminar Habilitación';
		$this->load_template('antenas/habilitaciones/habilitaciones_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$habilitacion = $this->Habilitaciones_model->get_one($id);
		if (empty($habilitacion))
		{
			show_error('No se encontró la Habilitación', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Habilitaciones_model->fields, $habilitacion, TRUE);
		$data['habilitacion'] = $habilitacion;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Habilitación';
		$data['title'] = TITLE . ' - Ver Habilitación';
		$this->load_template('antenas/habilitaciones/habilitaciones_abm', $data);
	}
}