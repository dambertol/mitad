<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subcategorias extends MY_Controller
{

	/**
	 * Controlador de Subcategorías
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 18/02/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('stock_informatica/Subcategorias_model');
		$this->load->model('stock_informatica/Categorias_model');
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
						array('label' => 'Categoría', 'data' => 'categoria', 'width' => 31,),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 60),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'subcategorias_table',
				'source_url' => 'stock_informatica/subcategorias/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_subcategorias_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Subcategorías';
		$data['title'] = TITLE . ' - Subcategorías';
		$this->load_template('stock_informatica/subcategorias/subcategorias_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('si_subcategorias.id, si_subcategorias.descripcion, si_categorias.descripcion as categoria')
				->from('si_subcategorias')
				->join('si_categorias', 'si_categorias.id = si_subcategorias.categoria_id', 'left')
				->add_column('ver', '<a href="stock_informatica/subcategorias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="stock_informatica/subcategorias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="stock_informatica/subcategorias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('stock_informatica/subcategorias/listar', 'refresh');
		}

		$this->array_categoria_control = $array_categoria = $this->get_array('Categorias');
		$this->set_model_validation_rules($this->Subcategorias_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Subcategorias_model->create(array(
					'descripcion' => $this->input->post('descripcion'),
					'categoria_id' => $this->input->post('categoria')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Subcategorias_model->get_msg());
				redirect('stock_informatica/subcategorias/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Subcategorias_model->get_error())
				{
					$error_msg .= $this->Subcategorias_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Subcategorias_model->fields['categoria']['array'] = $array_categoria;
		$data['fields'] = $this->build_fields($this->Subcategorias_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Subcategoría';
		$data['title'] = TITLE . ' - Agregar Subcategoría';
		$this->load_template('stock_informatica/subcategorias/subcategorias_abm', $data);
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
			redirect("stock_informatica/subcategorias/ver/$id", 'refresh');
		}

		$this->array_categoria_control = $array_categoria = $this->get_array('Categorias');
		$subcategoria = $this->Subcategorias_model->get(array('id' => $id));
		if (empty($subcategoria))
		{
			show_error('No se encontró la Subcategoría', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Subcategorias_model);
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
				$trans_ok &= $this->Subcategorias_model->update(array(
						'id' => $this->input->post('id'),
						'descripcion' => $this->input->post('descripcion'),
						'categoria_id' => $this->input->post('categoria')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Subcategorias_model->get_msg());
					redirect('stock_informatica/subcategorias/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Subcategorias_model->get_error())
					{
						$error_msg .= $this->Subcategorias_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Subcategorias_model->fields['categoria']['array'] = $array_categoria;
		$data['fields'] = $this->build_fields($this->Subcategorias_model->fields, $subcategoria);
		$data['subcategoria'] = $subcategoria;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Subcategoría';
		$data['title'] = TITLE . ' - Editar Subcategoría';
		$this->load_template('stock_informatica/subcategorias/subcategorias_abm', $data);
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
			redirect("stock_informatica/subcategorias/ver/$id", 'refresh');
		}

		$subcategoria = $this->Subcategorias_model->get_one($id);
		if (empty($subcategoria))
		{
			show_error('No se encontró la Subcategoría', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Subcategorias_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Subcategorias_model->get_msg());
				redirect('stock_informatica/subcategorias/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Subcategorias_model->get_error())
				{
					$error_msg .= $this->Subcategorias_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Subcategorias_model->fields, $subcategoria, TRUE);
		$data['subcategoria'] = $subcategoria;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Subcategoría';
		$data['title'] = TITLE . ' - Eliminar Subcategoría';
		$this->load_template('stock_informatica/subcategorias/subcategorias_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$subcategoria = $this->Subcategorias_model->get_one($id);
		if (empty($subcategoria))
		{
			show_error('No se encontró la Subcategoría', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Subcategorias_model->fields, $subcategoria, TRUE);
		$data['subcategoria'] = $subcategoria;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Subcategoría';
		$data['title'] = TITLE . ' - Ver Subcategoría';
		$this->load_template('stock_informatica/subcategorias/subcategorias_abm', $data);
	}
}