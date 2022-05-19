<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos extends MY_Controller
{

	/**
	 * Controlador de Artículos
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 11/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('stock_informatica/Articulos_model');
		$this->load->model('stock_informatica/Atributos_model');
		$this->load->model('stock_informatica/Atributos_articulos_model');
		$this->load->model('stock_informatica/Marcas_model');
		$this->load->model('stock_informatica/Categorias_model');
		$this->load->model('stock_informatica/Subcategorias_model');
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
						array('label' => 'Categoría', 'data' => 'categoria', 'width' => 10),
						array('label' => 'Subcategoría', 'data' => 'subcategoria', 'width' => 10),
						array('label' => 'Marca', 'data' => 'marca', 'width' => 10),
						array('label' => 'Modelo', 'data' => 'modelo', 'width' => 41),
						array('label' => 'N° Serie', 'data' => 'numero_serie', 'width' => 10),
						array('label' => 'N° Inventario', 'data' => 'numero_inventario', 'width' => 10),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'articulos_table',
				'source_url' => 'stock_informatica/articulos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_articulos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Artículos';
		$data['title'] = TITLE . ' - Artículos';
		$this->load_template('stock_informatica/articulos/articulos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('si_articulos.id, si_categorias.descripcion as categoria, si_subcategorias.descripcion as subcategoria, si_marcas.nombre as marca, si_articulos.modelo, si_articulos.numero_serie, si_articulos.numero_inventario')
				->from('si_articulos')
				->join('si_marcas', 'si_marcas.id = si_articulos.marca_id', 'left')
				->join('si_subcategorias', 'si_subcategorias.id = si_articulos.subcategoria_id', 'left')
				->join('si_categorias', 'si_categorias.id = si_subcategorias.categoria_id', 'left')
				->add_column('ver', '<a href="stock_informatica/articulos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="stock_informatica/articulos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="stock_informatica/articulos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function agregar($categoria_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect('stock_informatica/articulos/listar', 'refresh');
		}

		$this->array_marca_control = $array_marca = $this->get_array('Marcas', 'nombre');
		$this->array_categoria_control = $array_categoria = $this->get_array('Categorias');
		$this->array_subcategoria_control = $array_subcategoria = $this->get_array('Subcategorias', 'descripcion', 'id', array('categoria_id' => $categoria_id));

		$this->set_model_validation_rules($this->Articulos_model);
		$error_msg = FALSE;
		if (!empty($categoria_id))
		{
			$atributos = $this->Atributos_model->get(array('categoria_id' => $categoria_id));
			if (!empty($atributos))
			{
				foreach ($atributos as $Atributo)
				{
					$regla = '';
					switch ($Atributo->tipo)
					{
						case 'Cadena':
							$regla = 'max_length[254]';
							break;
						case 'Decimal':
							$regla = 'numeric';
							break;
						case 'Entero':
							$regla = 'integer';
							break;
					}
					$this->form_validation->set_rules("atributo_$Atributo->id", $Atributo->nombre, $regla);
				}
			}
		}
		if ($this->form_validation->run() === TRUE)
		{
			if (empty($this->input->post('numero_serie')))
			{
				$nro_serie = uniqid('MLC');
			}
			else
			{
				$nro_serie = $this->input->post('numero_serie');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Articulos_model->create(array(
					'modelo' => $this->input->post('modelo'),
					'marca_id' => $this->input->post('marca'),
					'subcategoria_id' => $this->input->post('subcategoria'),
					'numero_serie' => $nro_serie,
					'numero_inventario' => $this->input->post('numero_inventario')), FALSE);

			$articulo_id = $this->Articulos_model->get_row_id();
			if (!empty($atributos))
			{
				foreach ($atributos as $Atributo)
				{
					$trans_ok &= $this->Atributos_articulos_model->create(array(
							'articulo_id' => $articulo_id,
							'atributo_id' => $Atributo->id,
							'valor' => $this->input->post("atributo_$Atributo->id")), FALSE);
				}
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
				redirect('stock_informatica/articulos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Articulos_model->get_error())
				{
					$error_msg .= $this->Articulos_model->get_error();
				}
				if ($this->Atributos_articulos_model->get_error())
				{
					$error_msg .= $this->Atributos_articulos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Articulos_model->fields['marca']['array'] = $array_marca;
		$this->Articulos_model->fields['categoria']['array'] = $array_categoria;
		$this->Articulos_model->fields['subcategoria']['array'] = $array_subcategoria;
		$data['fields'] = $this->build_fields($this->Articulos_model->fields);

		$data['fields_detalle_array'] = array();
		if (!empty($atributos))
		{
			foreach ($atributos as $Atributo)
			{
				$fake_model_fields = array(
						"nombre_{$Atributo->id}" => array('label' => 'Atributo', 'readonly' => TRUE),
						"atributo_{$Atributo->id}" => array('label' => 'Valor', 'maxlength' => '255', 'required' => TRUE)
				);

				$Atributo->{"nombre_{$Atributo->id}"} = $Atributo->nombre;
				$Atributo->{"atributo_{$Atributo->id}"} = '';

				$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $Atributo, FALSE, 'table');
			}
		}

		$data['categoria_id'] = $categoria_id;
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Artículo';
		$data['title'] = TITLE . ' - Agregar Artículo';
		$this->load_template('stock_informatica/articulos/articulos_abm', $data);
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
			redirect("stock_informatica/articulos/ver/$id", 'refresh');
		}

		$articulo = $this->Articulos_model->get_one($id);
		if (empty($articulo))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}
		$atributos = $this->Atributos_articulos_model->get(array(
				'articulo_id' => $id,
				'join' => array(
						array(
								'table' => 'si_atributos',
								'where' => 'si_atributos.id = si_atributos_articulos.atributo_id',
								'columnas' => array('si_atributos.nombre as nombre', 'si_atributos.tipo as tipo')))
		));

		$this->array_marca_control = $array_marca = $this->get_array('Marcas', 'nombre');
		$this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(), array(NULL => 'SIN MODIFICAR'));
		$this->array_subcategoria_control = $array_subcategoria = $this->get_array('Subcategorias', 'descripcion', 'id', array('categoria_id' => $articulo->categoria_id));

		$this->Articulos_model->fields['categoria']['disabled'] = TRUE;

		$this->set_model_validation_rules($this->Articulos_model);
		if (!empty($atributos))
		{
			foreach ($atributos as $Atributo)
			{
				$regla = '';
				switch ($Atributo->tipo)
				{
					case 'Cadena':
						$regla = 'max_length[254]';
						break;
					case 'Decimal':
						$regla = 'numeric';
						break;
					case 'Entero':
						$regla = 'integer';
						break;
				}
				$this->form_validation->set_rules("atributo_$Atributo->id", $Atributo->nombre, $regla);
			}
		}
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				if (empty($this->input->post('numero_serie')))
				{
					$nro_serie = $articulo->numero_serie;
				}
				else
				{
					$nro_serie = $this->input->post('numero_serie');
				}

				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Articulos_model->update(array(
						'id' => $this->input->post('id'),
						'modelo' => $this->input->post('modelo'),
						'marca_id' => $this->input->post('marca'),
						'subcategoria_id' => $this->input->post('subcategoria'),
						'numero_serie' => $nro_serie,
						'numero_inventario' => $this->input->post('numero_inventario')), FALSE);

				if (!empty($atributos))
				{
					foreach ($atributos as $Atributo)
					{
						$trans_ok &= $this->Atributos_articulos_model->update(array(
								'id' => $Atributo->id,
								'articulo_id' => $this->input->post('id'),
								'atributo_id' => $Atributo->atributo_id,
								'valor' => $this->input->post("atributo_$Atributo->id")), FALSE);
					}
				}

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
					redirect('stock_informatica/articulos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Articulos_model->get_error())
					{
						$error_msg .= $this->Articulos_model->get_error();
					}
					if ($this->Atributos_articulos_model->get_error())
					{
						$error_msg .= $this->Atributos_articulos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields_detalle_array'] = array();
		if (!empty($atributos))
		{
			foreach ($atributos as $Atributo)
			{
				$fake_model_fields = array(
						"nombre_{$Atributo->id}" => array('label' => 'Atributo', 'readonly' => TRUE),
						"atributo_{$Atributo->id}" => array('label' => 'Valor', 'maxlength' => '255', 'required' => TRUE)
				);

				$Atributo->{"nombre_{$Atributo->id}"} = $Atributo->nombre;
				$Atributo->{"atributo_{$Atributo->id}"} = $Atributo->valor;

				$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $Atributo, FALSE, 'table');
			}
		}

		$this->Articulos_model->fields['marca']['array'] = $array_marca;
		$this->Articulos_model->fields['categoria']['array'] = $array_categoria;
		$this->Articulos_model->fields['subcategoria']['array'] = $array_subcategoria;
		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Artículo';
		$data['title'] = TITLE . ' - Editar Artículo';
		$this->load_template('stock_informatica/articulos/articulos_abm', $data);
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
			redirect("stock_informatica/articulos/ver/$id", 'refresh');
		}

		$articulo = $this->Articulos_model->get_one($id);
		if (empty($articulo))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}

		$atributos = $this->Atributos_articulos_model->get(array(
				'articulo_id' => $id,
				'join' => array(array(
								'table' => 'si_atributos',
								'where' => 'si_atributos.id = si_atributos_articulos.atributo_id',
								'columnas' => array('si_atributos.nombre as nombre')))
		));

		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			if (!empty($atributos))
			{
				foreach ($atributos as $Atributo)
				{
					$trans_ok &= $this->Atributos_articulos_model->delete(array('id' => $Atributo->id), FALSE);
				}
			}
			$trans_ok &= $this->Articulos_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
				redirect('stock_informatica/articulos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Atributos_articulos_model->get_error())
				{
					$error_msg .= $this->Atributos_articulos_model->get_error();
				}
				if ($this->Articulos_model->get_error())
				{
					$error_msg .= $this->Articulos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields_detalle_array'] = array();
		if (!empty($atributos))
		{
			foreach ($atributos as $Atributo)
			{
				$fake_model_fields = array(
						"nombre_{$Atributo->id}" => array('label' => 'Atributo', 'readonly' => TRUE),
						"atributo_{$Atributo->id}" => array('label' => 'Valor', 'maxlength' => '255', 'required' => TRUE)
				);

				$Atributo->{"nombre_{$Atributo->id}"} = $Atributo->nombre;
				$Atributo->{"atributo_{$Atributo->id}"} = $Atributo->valor;

				$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $Atributo, TRUE, 'table');
			}
		}

		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo, TRUE);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Artículo';
		$data['title'] = TITLE . ' - Eliminar Artículo';
		$this->load_template('stock_informatica/articulos/articulos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$articulo = $this->Articulos_model->get_one($id);
		if (empty($articulo))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}

		$atributos = $this->Atributos_articulos_model->get(array(
				'articulo_id' => $id,
				'join' => array(array(
								'table' => 'si_atributos',
								'where' => 'si_atributos.id = si_atributos_articulos.atributo_id',
								'columnas' => array('si_atributos.nombre as nombre')))
		));

		$data['fields_detalle_array'] = array();
		if (!empty($atributos))
		{
			foreach ($atributos as $Atributo)
			{
				$fake_model_fields = array(
						"nombre_{$Atributo->id}" => array('label' => 'Atributo', 'readonly' => TRUE),
						"atributo_{$Atributo->id}" => array('label' => 'Valor', 'maxlength' => '255', 'required' => TRUE)
				);

				$Atributo->{"nombre_{$Atributo->id}"} = $Atributo->nombre;
				$Atributo->{"atributo_{$Atributo->id}"} = $Atributo->valor;

				$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $Atributo, TRUE, 'table');
			}
		}

		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo, TRUE);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Artículo';
		$data['title'] = TITLE . ' - Ver Artículo';
		$this->load_template('stock_informatica/articulos/articulos_abm', $data);
	}

	public function get_articulos_subcategoria()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('subcategoria_id', 'ID Categoría', 'required|integer|max_length[8]');
		if ($this->form_validation->run() === TRUE)
		{
			$subcategoria_id = $this->input->post('subcategoria_id');
			$articulos = $this->Articulos_model->get(array(
					'subcategoria_id' => $subcategoria_id,
					'join' => array(array('si_marcas', 'si_marcas.id = si_articulos.marca_id', 'LEFT', array("si_marcas.nombre as marca")))
			));
			$articulos_tmp = array();
			if (!empty($articulos))
			{
				foreach ($articulos as $articulos)
				{
					$cons = array();
					$cons['nombre'] = "$articulos->marca - $articulos->modelo";
					$cons['id'] = $articulos->id;
					$articulos_tmp[] = $cons;
				}
				$data['articulos'] = $articulos_tmp;
			}
			else
			{
				$data['error'] = 'Artículos no encontrados';
			}
		}
		else
		{
			$data['error'] = 'Debe ingresar un ID válido';
		}

		echo json_encode($data);
	}
}