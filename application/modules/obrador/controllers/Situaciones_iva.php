<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Situaciones_iva extends MY_Controller
{

	/**
	 * Controlador de Situaciones IVA
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 21/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('obrador/Situaciones_iva_model');
		$this->grupos_permitidos = array('admin', 'obrador_user', 'obrador_consulta_general');
		$this->grupos_solo_consulta = array('obrador_consulta_general');
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
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 91),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'situaciones_iva_table',
				'source_url' => 'obrador/situaciones_iva/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_situaciones_iva_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Situaciones IVA';
		$data['title'] = TITLE . ' - Situaciones IVA';
		$this->load_template('obrador/situaciones_iva/situaciones_iva_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('id, descripcion')
				->from('ob_situaciones_iva')
				->add_column('ver', '<a href="obrador/situaciones_iva/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="obrador/situaciones_iva/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="obrador/situaciones_iva/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('obrador/situaciones_iva/listar', 'refresh');
		}

		$this->set_model_validation_rules($this->Situaciones_iva_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Situaciones_iva_model->create(array(
					'descripcion' => $this->input->post('descripcion')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Situaciones_iva_model->get_msg());
				redirect('obrador/situaciones_iva/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Situaciones_iva_model->get_error())
				{
					$error_msg .= $this->Situaciones_iva_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Situaciones_iva_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Situación IVA';
		$data['title'] = TITLE . ' - Agregar Situación IVA';
		$this->load_template('obrador/situaciones_iva/situaciones_iva_abm', $data);
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
			redirect("obrador/situaciones_iva/ver/$id", 'refresh');
		}

		$situaciones_iv = $this->Situaciones_iva_model->get(array('id' => $id));
		if (empty($situaciones_iv))
		{
			show_error('No se encontró el Situación IVA', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Situaciones_iva_model);
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
				$trans_ok &= $this->Situaciones_iva_model->update(array(
						'id' => $this->input->post('id'),
						'descripcion' => $this->input->post('descripcion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Situaciones_iva_model->get_msg());
					redirect('obrador/situaciones_iva/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Situaciones_iva_model->get_error())
					{
						$error_msg .= $this->Situaciones_iva_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Situaciones_iva_model->fields, $situaciones_iv);
		$data['situaciones_iv'] = $situaciones_iv;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Situación IVA';
		$data['title'] = TITLE . ' - Editar Situación IVA';
		$this->load_template('obrador/situaciones_iva/situaciones_iva_abm', $data);
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
			redirect("obrador/situaciones_iva/ver/$id", 'refresh');
		}

		$situaciones_iv = $this->Situaciones_iva_model->get_one($id);
		if (empty($situaciones_iv))
		{
			show_error('No se encontró el Situación IVA', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Situaciones_iva_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Situaciones_iva_model->get_msg());
				redirect('obrador/situaciones_iva/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Situaciones_iva_model->get_error())
				{
					$error_msg .= $this->Situaciones_iva_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Situaciones_iva_model->fields, $situaciones_iv, TRUE);
		$data['situaciones_iv'] = $situaciones_iv;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Situación IVA';
		$data['title'] = TITLE . ' - Eliminar Situación IVA';
		$this->load_template('obrador/situaciones_iva/situaciones_iva_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$situaciones_iv = $this->Situaciones_iva_model->get_one($id);
		if (empty($situaciones_iv))
		{
			show_error('No se encontró el Situación IVA', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Situaciones_iva_model->fields, $situaciones_iv, TRUE);
		$data['situaciones_iv'] = $situaciones_iv;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Situación IVA';
		$data['title'] = TITLE . ' - Ver Situación IVA';
		$this->load_template('obrador/situaciones_iva/situaciones_iva_abm', $data);
	}
}