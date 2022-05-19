<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Modelos extends MY_Controller
{

	/**
	 * Controlador de Modelos
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 02/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('telefonia/Modelos_model');
		$this->load->model('telefonia/Marcas_model');
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
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 41),
						array('label' => 'Marca', 'data' => 'marca', 'width' => 25),
						array('label' => 'Categoría', 'data' => 'categoria', 'width' => 25),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'modelos_table',
				'source_url' => 'telefonia/modelos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_modelos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Modelos';
		$data['title'] = TITLE . ' - Modelos';
		$this->load_template('telefonia/modelos/modelos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('tm_modelos.id, tm_modelos.nombre, tm_marcas.nombre as marca, tm_categorias.descripcion as categoria')
				->from('tm_modelos')
				->join('tm_marcas', 'tm_marcas.id = tm_modelos.marca_id', 'left')
				->join('tm_categorias', 'tm_categorias.id = tm_modelos.categoria_id', 'left')
				->add_column('ver', '<a href="telefonia/modelos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="telefonia/modelos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="telefonia/modelos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('telefonia/modelos/listar', 'refresh');
		}

		$this->array_marca_control = $array_marca = $this->get_array('Marcas', 'nombre');
		$this->array_categoria_control = $array_categoria = $this->get_array('Categorias');
		$this->set_model_validation_rules($this->Modelos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Modelos_model->create(array(
					'nombre' => $this->input->post('nombre'),
					'marca_id' => $this->input->post('marca'),
					'categoria_id' => $this->input->post('categoria')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Modelos_model->get_msg());
				redirect('telefonia/modelos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Modelos_model->get_error())
				{
					$error_msg .= $this->Modelos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Modelos_model->fields['marca']['array'] = $array_marca;
		$this->Modelos_model->fields['categoria']['array'] = $array_categoria;
		$data['fields'] = $this->build_fields($this->Modelos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Modelo';
		$data['title'] = TITLE . ' - Agregar Modelo';
		$this->load_template('telefonia/modelos/modelos_abm', $data);
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
			redirect("telefonia/modelos/ver/$id", 'refresh');
		}

		$this->array_marca_control = $array_marca = $this->get_array('Marcas', 'nombre');
		$this->array_categoria_control = $array_categoria = $this->get_array('Categorias');
		$modelo = $this->Modelos_model->get(array('id' => $id));
		if (empty($modelo))
		{
			show_error('No se encontró el Modelo', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Modelos_model);
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
				$trans_ok &= $this->Modelos_model->update(array(
						'id' => $this->input->post('id'),
						'nombre' => $this->input->post('nombre'),
						'marca_id' => $this->input->post('marca'),
						'categoria_id' => $this->input->post('categoria')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Modelos_model->get_msg());
					redirect('telefonia/modelos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Modelos_model->get_error())
					{
						$error_msg .= $this->Modelos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Modelos_model->fields['marca']['array'] = $array_marca;
		$this->Modelos_model->fields['categoria']['array'] = $array_categoria;
		$data['fields'] = $this->build_fields($this->Modelos_model->fields, $modelo);
		$data['modelo'] = $modelo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Modelo';
		$data['title'] = TITLE . ' - Editar Modelo';
		$this->load_template('telefonia/modelos/modelos_abm', $data);
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
			redirect("telefonia/modelos/ver/$id", 'refresh');
		}

		$modelo = $this->Modelos_model->get_one($id);
		if (empty($modelo))
		{
			show_error('No se encontró el Modelo', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Modelos_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Modelos_model->get_msg());
				redirect('telefonia/modelos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Modelos_model->get_error())
				{
					$error_msg .= $this->Modelos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Modelos_model->fields, $modelo, TRUE);
		$data['modelo'] = $modelo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Modelo';
		$data['title'] = TITLE . ' - Eliminar Modelo';
		$this->load_template('telefonia/modelos/modelos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$modelo = $this->Modelos_model->get_one($id);
		if (empty($modelo))
		{
			show_error('No se encontró el Modelo', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Modelos_model->fields, $modelo, TRUE);
		$data['modelo'] = $modelo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Modelo';
		$data['title'] = TITLE . ' - Ver Modelo';
		$this->load_template('telefonia/modelos/modelos_abm', $data);
	}
}