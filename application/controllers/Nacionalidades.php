<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Nacionalidades extends MY_Controller
{

	/**
	 * Controlador de Nacionalidades
	 * Autor: Leandro
	 * Creado: 09/09/2019
	 * Modificado: 09/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Nacionalidades_model');
		$this->grupos_permitidos = array('admin', 'consulta_general');
		$this->grupos_solo_consulta = array('consulta_general');
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
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 91),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'nacionalidades_table',
				'source_url' => 'nacionalidades/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_nacionalidades_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Nacionalidades';
		$data['title'] = TITLE . ' - Nacionalidades';
		$this->load_template('nacionalidades/nacionalidades_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('id, nombre')
				->from('nacionalidades')
				->add_column('ver', '<a href="nacionalidades/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="nacionalidades/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="nacionalidades/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('nacionalidades/listar', 'refresh');
		}

		$this->set_model_validation_rules($this->Nacionalidades_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Nacionalidades_model->create(array(
					'nombre' => $this->input->post('nombre')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Nacionalidades_model->get_msg());
				redirect('nacionalidades/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Nacionalidades_model->get_error())
				{
					$error_msg .= $this->Nacionalidades_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Nacionalidades_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Nacionalidad';
		$data['title'] = TITLE . ' - Agregar Nacionalidad';
		$this->load_template('nacionalidades/nacionalidades_abm', $data);
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
			redirect("nacionalidades/ver/$id", 'refresh');
		}

		$nacionalidad = $this->Nacionalidades_model->get(array('id' => $id));
		if (empty($nacionalidad))
		{
			show_error('No se encontró la Nacionalidad', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Nacionalidades_model);
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
				$trans_ok &= $this->Nacionalidades_model->update(array(
						'id' => $this->input->post('id'),
						'nombre' => $this->input->post('nombre')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Nacionalidades_model->get_msg());
					redirect('nacionalidades/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Nacionalidades_model->get_error())
					{
						$error_msg .= $this->Nacionalidades_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Nacionalidades_model->fields, $nacionalidad);
		$data['nacionalidad'] = $nacionalidad;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Nacionalidad';
		$data['title'] = TITLE . ' - Editar Nacionalidad';
		$this->load_template('nacionalidades/nacionalidades_abm', $data);
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
			redirect("nacionalidades/ver/$id", 'refresh');
		}

		$nacionalidad = $this->Nacionalidades_model->get_one($id);
		if (empty($nacionalidad))
		{
			show_error('No se encontró la Nacionalidad', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Nacionalidades_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Nacionalidades_model->get_msg());
				redirect('nacionalidades/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Nacionalidades_model->get_error())
				{
					$error_msg .= $this->Nacionalidades_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Nacionalidades_model->fields, $nacionalidad, TRUE);
		$data['nacionalidad'] = $nacionalidad;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Nacionalidad';
		$data['title'] = TITLE . ' - Eliminar Nacionalidad';
		$this->load_template('nacionalidades/nacionalidades_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$nacionalidad = $this->Nacionalidades_model->get_one($id);
		if (empty($nacionalidad))
		{
			show_error('No se encontró la Nacionalidad', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Nacionalidades_model->fields, $nacionalidad, TRUE);
		$data['nacionalidad'] = $nacionalidad;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Nacionalidad';
		$data['title'] = TITLE . ' - Ver Nacionalidad';
		$this->load_template('nacionalidades/nacionalidades_abm', $data);
	}
}