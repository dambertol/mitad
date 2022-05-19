<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends MY_Controller
{

	/**
	 * Controlador de Categorías
	 * Autor: Leandro
	 * Creado: 02/02/2017
	 * Modificado: 07/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('recursos_humanos/Categorias_model');
		$this->grupos_permitidos = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_consulta_general');
		$this->grupos_solo_consulta = array('recursos_humanos_consulta_general');
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
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 94),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'categorias_table',
				'source_url' => 'recursos_humanos/categorias/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_categorias_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de categorías';
		$data['title'] = TITLE . ' - Categorías';
		$this->load_template('recursos_humanos/categorias/categorias_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('id, nombre')
				->from('rh_categorias')
				->add_column('ver', '<a href="recursos_humanos/categorias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('eliminar', '<a href="recursos_humanos/categorias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			$this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
			redirect("recursos_humanos/categorias/listar", 'refresh');
		}

		$modelo_categorias = $this->Categorias_model;
		unset($modelo_categorias->fields['ruta']);
		$this->set_model_validation_rules($modelo_categorias);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$unwanted_array = array('Š' => 'S', 'š' => 's', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
					'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U',
					'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c',
					'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o',
					'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y');
			$ruta = strtr($this->input->post('nombre'), $unwanted_array);
			$ruta = mb_strtolower(str_replace(str_split(preg_replace("/([[:alnum:]_.-]*)/", "_", $ruta)), "_", $ruta), 'UTF-8');

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Categorias_model->create(array(
					'nombre' => $this->input->post('nombre'),
					'ruta' => $ruta), FALSE);

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Categorias_model->get_msg());
				redirect('recursos_humanos/categorias/listar', 'refresh');
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

		$data['fields'] = $this->build_fields($modelo_categorias->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar categoría';
		$data['title'] = TITLE . ' - Agregar categoría';
		$this->load_template('recursos_humanos/categorias/categorias_abm', $data);
	}

	public function eliminar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
			redirect("recursos_humanos/categorias/ver/$id", 'refresh');
		}

		$categoria = $this->Categorias_model->get(array('id' => $id));
		if (empty($categoria))
		{
			show_error('No se encontró el Categoría', 500, 'Registro no encontrado');
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
				redirect('recursos_humanos/categorias/listar', 'refresh');
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
		$data['title_view'] = 'Eliminar categoría';
		$data['title'] = TITLE . ' - Eliminar categoría';
		$this->load_template('recursos_humanos/categorias/categorias_abm', $data);
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
			show_error('No se encontró el Categoría', 500, 'Registro no encontrado');
		}

		$data['error'] = $this->session->flashdata('error');
		$data['fields'] = $this->build_fields($this->Categorias_model->fields, $categoria, TRUE);
		$data['categoria'] = $categoria;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver categoría';
		$data['title'] = TITLE . ' - Ver categoría';
		$this->load_template('recursos_humanos/categorias/categorias_abm', $data);
	}
}