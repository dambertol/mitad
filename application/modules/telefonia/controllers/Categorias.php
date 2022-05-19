<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends MY_Controller
{

	/**
	 * Controlador de Categorías
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 02/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('telefonia/Categorias_model');
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
				array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 51),
				array('label' => 'Tipo', 'data' => 'tipo', 'width' => 40),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'categorias_table',
			'source_url' => 'telefonia/categorias/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_categorias_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Categorías';
		$data['title'] = TITLE . ' - Categorías';
		$this->load_template('telefonia/categorias/categorias_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
			->select('id, descripcion, tipo')
			->from('tm_categorias')
			->add_column('ver', '<a href="telefonia/categorias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="telefonia/categorias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="telefonia/categorias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('telefonia/categorias/listar', 'refresh');
		}
		
		$this->set_model_validation_rules($this->Categorias_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Categorias_model->create(array(
				'descripcion' => $this->input->post('descripcion'),
				'tipo' => $this->input->post('tipo')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Categorias_model->get_msg());
				redirect('telefonia/categorias/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Categorias_model->get_error())
				{
					$error_msg .= $this->Categorias_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Categorias_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Categoría';
		$data['title'] = TITLE . ' - Agregar Categoría';
		$this->load_template('telefonia/categorias/categorias_abm', $data);
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
			redirect("telefonia/categorias/ver/$id", 'refresh');
		}
		
		$categoria = $this->Categorias_model->get(array('id' => $id));
		if (empty($categoria))
		{
			show_error('No se encontró la Categoría', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Categorias_model);
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
				$trans_ok &= $this->Categorias_model->update(array(
					'id' => $this->input->post('id'),
					'descripcion' => $this->input->post('descripcion'),
					'tipo' => $this->input->post('tipo')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Categorias_model->get_msg());
					redirect('telefonia/categorias/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Categorias_model->get_error())
					{
						$error_msg .= $this->Categorias_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Categorias_model->fields, $categoria);
		$data['categoria'] = $categoria;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Categoría';
		$data['title'] = TITLE . ' - Editar Categoría';
		$this->load_template('telefonia/categorias/categorias_abm', $data);
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
			redirect("telefonia/categorias/ver/$id", 'refresh');
		}

		$categoria = $this->Categorias_model->get_one($id);
		if (empty($categoria))
		{
			show_error('No se encontró la Categoría', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Categorias_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Categorias_model->get_msg());
				redirect('telefonia/categorias/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Categorias_model->get_error())
				{
					$error_msg .= $this->Categorias_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Categorias_model->fields, $categoria, TRUE);
		$data['categoria'] = $categoria;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Categoría';
		$data['title'] = TITLE . ' - Eliminar Categoría';
		$this->load_template('telefonia/categorias/categorias_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$categoria = $this->Categorias_model->get_one($id);
		if (empty($categoria))
		{
			show_error('No se encontró la Categoría', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Categorias_model->fields, $categoria, TRUE);
		$data['categoria'] = $categoria;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Categoría';
		$data['title'] = TITLE . ' - Ver Categoría';
		$this->load_template('telefonia/categorias/categorias_abm', $data);
	}
}