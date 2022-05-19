<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Marcas extends MY_Controller
{

	/**
	 * Controlador de Marcas
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 18/02/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('stock_informatica/Marcas_model');
		$this->grupos_permitidos = array('admin', 'stock_informatica_user', 'stock_informatica_consulta_general');
		$this->grupos_solo_consulta = array('stock_informatica_consulta_general');	
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
			'table_id' => 'marcas_table',
			'source_url' => 'stock_informatica/marcas/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_marcas_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Marcas';
		$data['title'] = TITLE . ' - Marcas';
		$this->load_template('stock_informatica/marcas/marcas_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
				->select('id, nombre')
			->from('si_marcas')
			->add_column('ver', '<a href="stock_informatica/marcas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="stock_informatica/marcas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="stock_informatica/marcas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('stock_informatica/marcas/listar', 'refresh');
		}
		
		$this->set_model_validation_rules($this->Marcas_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Marcas_model->create(array(
					'nombre' => $this->input->post('nombre')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Marcas_model->get_msg());
				redirect('stock_informatica/marcas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Marcas_model->get_error())
				{
					$error_msg .= $this->Marcas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Marcas_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Marca';
		$data['title'] = TITLE . ' - Agregar Marca';
		$this->load_template('stock_informatica/marcas/marcas_abm', $data);
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
			redirect("stock_informatica/marcas/ver/$id", 'refresh');
		}
		
		$marca = $this->Marcas_model->get(array('id' => $id));
		if (empty($marca))
		{
			show_error('No se encontró la Marca', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Marcas_model);
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
				$trans_ok &= $this->Marcas_model->update(array(
					'id' => $this->input->post('id'),
						'nombre' => $this->input->post('nombre')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Marcas_model->get_msg());
					redirect('stock_informatica/marcas/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Marcas_model->get_error())
					{
						$error_msg .= $this->Marcas_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Marcas_model->fields, $marca);
		$data['marca'] = $marca;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Marca';
		$data['title'] = TITLE . ' - Editar Marca';
		$this->load_template('stock_informatica/marcas/marcas_abm', $data);
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
			redirect("stock_informatica/marcas/ver/$id", 'refresh');
		}

		$marca = $this->Marcas_model->get_one($id);
		if (empty($marca))
		{
			show_error('No se encontró la Marca', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Marcas_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Marcas_model->get_msg());
				redirect('stock_informatica/marcas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Marcas_model->get_error())
				{
					$error_msg .= $this->Marcas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Marcas_model->fields, $marca, TRUE);
		$data['marca'] = $marca;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Marca';
		$data['title'] = TITLE . ' - Eliminar Marca';
		$this->load_template('stock_informatica/marcas/marcas_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$marca = $this->Marcas_model->get_one($id);
		if (empty($marca))
		{
			show_error('No se encontró la Marca', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Marcas_model->fields, $marca, TRUE);
		$data['marca'] = $marca;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Marca';
		$data['title'] = TITLE . ' - Ver Marca';
		$this->load_template('stock_informatica/marcas/marcas_abm', $data);
	}
}