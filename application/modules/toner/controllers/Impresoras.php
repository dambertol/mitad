<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Impresoras extends MY_Controller
{

	/**
	 * Controlador de Impresoras
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 07/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toner/Impresoras_model');
		$this->load->model('toner/Marcas_model');
		$this->grupos_permitidos = array('admin', 'toner_admin', 'toner_consulta_general');
		$this->grupos_solo_consulta = array('toner_consulta_general');
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
						array('label' => 'Modelo', 'data' => 'modelo', 'width' => 60),
						array('label' => 'Marca', 'data' => 'marca', 'width' => 31),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'impresoras_table',
				'source_url' => 'toner/impresoras/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_impresoras_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Impresoras';
		$data['title'] = TITLE . ' - Impresoras';
		$this->load_template('toner/impresoras/impresoras_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('gt_impresoras.id, gt_impresoras.modelo, gt_marcas.nombre as marca')
				->from('gt_impresoras')
				->join('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'left')
				->add_column('ver', '<a href="toner/impresoras/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="toner/impresoras/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="toner/impresoras/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('toner/impresoras/listar', 'refresh');
		}

		$this->array_marca_control = $array_marca = $this->get_array('Marcas', 'nombre');
		$this->set_model_validation_rules($this->Impresoras_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Impresoras_model->create(array(
					'marca_id' => $this->input->post('marca'),
					'modelo' => $this->input->post('modelo')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Impresoras_model->get_msg());
				redirect('toner/impresoras/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Impresoras_model->get_error())
				{
					$error_msg .= $this->Impresoras_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Impresoras_model->fields['marca']['array'] = $array_marca;
		$data['fields'] = $this->build_fields($this->Impresoras_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Impresora';
		$data['title'] = TITLE . ' - Agregar Impresora';
		$this->load_template('toner/impresoras/impresoras_abm', $data);
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
			redirect("toner/impresoras/ver/$id", 'refresh');
		}

		$this->array_marca_control = $array_marca = $this->get_array('Marcas', 'nombre');
		$impresora = $this->Impresoras_model->get(array('id' => $id));
		if (empty($impresora))
		{
			show_error('No se encontró la Impresora', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Impresoras_model);
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
				$trans_ok &= $this->Impresoras_model->update(array(
						'id' => $this->input->post('id'),
						'marca_id' => $this->input->post('marca'),
						'modelo' => $this->input->post('modelo')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Impresoras_model->get_msg());
					redirect('toner/impresoras/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Impresoras_model->get_error())
					{
						$error_msg .= $this->Impresoras_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Impresoras_model->fields['marca']['array'] = $array_marca;
		$data['fields'] = $this->build_fields($this->Impresoras_model->fields, $impresora);
		$data['impresora'] = $impresora;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Impresora';
		$data['title'] = TITLE . ' - Editar Impresora';
		$this->load_template('toner/impresoras/impresoras_abm', $data);
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
			redirect("toner/impresoras/ver/$id", 'refresh');
		}

		$impresora = $this->Impresoras_model->get_one($id);
		if (empty($impresora))
		{
			show_error('No se encontró la Impresora', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Impresoras_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Impresoras_model->get_msg());
				redirect('toner/impresoras/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Impresoras_model->get_error())
				{
					$error_msg .= $this->Impresoras_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Impresoras_model->fields, $impresora, TRUE);
		$data['impresora'] = $impresora;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Impresora';
		$data['title'] = TITLE . ' - Eliminar Impresora';
		$this->load_template('toner/impresoras/impresoras_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$impresora = $this->Impresoras_model->get_one($id);
		if (empty($impresora))
		{
			show_error('No se encontró la Impresora', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Impresoras_model->fields, $impresora, TRUE);
		$data['impresora'] = $impresora;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Impresora';
		$data['title'] = TITLE . ' - Ver Impresora';
		$this->load_template('toner/impresoras/impresoras_abm', $data);
	}
}