<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Stock extends MY_Controller
{

	/**
	 * Controlador de Stock
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 11/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('stock_informatica/Articulos_model');
		$this->load->model('stock_informatica/Atributos_articulos_model');
		$this->load->model('stock_informatica/Movimientos_detalle_model');
		$this->load->model('stock_informatica/Movimientos_model');
		$this->load->model('Personas_model');
		$this->load->model('stock_informatica/Subcategorias_model');
		$this->load->model('Areas_model');
		$this->grupos_permitidos = array('admin', 'stock_informatica_user', 'stock_informatica_consulta_general');
		$this->grupos_solo_consulta = array('stock_informatica_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar($area_id = 'Todas')
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tableData = array(
				'columns' => array(
						array('label' => 'Área', 'data' => 'area', 'width' => 20),
						array('label' => 'Categoría', 'data' => 'categoria', 'width' => 10),
						array('label' => 'SubCategoría', 'data' => 'subcategoria', 'width' => 10),
						array('label' => 'Artículo', 'data' => 'articulo', 'width' => 22),
						array('label' => 'N° Serie', 'data' => 'numero_serie', 'width' => 10),
						array('label' => 'N° Inventario', 'data' => 'numero_inventario', 'width' => 10),
						array('label' => 'Ala', 'data' => 'ala', 'width' => 5),
						array('label' => 'Oficina', 'data' => 'oficina', 'width' => 4),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'transferir', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'baja', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'stock_table',
				'source_url' => "stock_informatica/stock/listar_data/$area_id",
				'reuse_var' => TRUE,
				'initComplete' => 'complete_stock_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);

		$data['area_opt'] = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', "CONCAT(areas.codigo, ' - ', areas.nombre) as area"), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));
		$data['area_id'] = $area_id;

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Stock';
		$data['title'] = TITLE . ' - Stock';
		$this->load_template('stock_informatica/stock/stock_listar', $data);
	}

	public function listar_data($area_id = 'Todas')
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select("MAX(si_movimientos_detalle.id) as id, CONCAT(areas.codigo, ' - ', areas.nombre) as area, si_categorias.descripcion as categoria, si_subcategorias.descripcion as subcategoria, CONCAT(si_marcas.nombre, ' - ', si_articulos.modelo) as articulo, si_articulos.numero_serie, si_articulos.numero_inventario, SUM(si_movimientos_detalle.cantidad) as cantidad, si_movimientos_detalle.ala, si_movimientos_detalle.oficina")
				->from('si_movimientos_detalle')
				->join('si_movimientos', 'si_movimientos.id = si_movimientos_detalle.movimiento_id', 'left')
				->join('areas', 'areas.id = si_movimientos.area_id', 'left')
				->join('si_articulos', 'si_articulos.id = si_movimientos_detalle.articulo_id', 'left')
				->join('si_marcas', 'si_marcas.id = si_articulos.marca_id', 'left')
				->join('si_subcategorias', 'si_subcategorias.id = si_articulos.subcategoria_id', 'left')
				->join('si_categorias', 'si_categorias.id = si_subcategorias.categoria_id', 'left')
				->having('SUM(si_movimientos_detalle.cantidad) >', '0')
				->group_by('areas.codigo, areas.nombre, si_categorias.descripcion, si_subcategorias.descripcion, si_marcas.nombre, si_articulos.modelo, si_articulos.numero_serie, si_articulos.numero_inventario, si_articulos.id')
				->add_column('ver', '<a href="stock_informatica/stock/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('transferir', '<a href="stock_informatica/stock/transferencia_articulo/$1" title="Transferir" class="btn btn-primary btn-xs"><i class="fa fa-exchange"></i></a>', 'id')
				->add_column('baja', '<a href="stock_informatica/stock/transferencia_articulo/$1/TRUE" title="Baja" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		if ($area_id !== 'Todas')
		{
			$this->datatables->where('si_movimientos.area_id', $area_id);
		}

		echo $this->datatables->generate();
	}

	public function ingreso()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("stock_informatica/stock/listar", 'refresh');
		}

		$this->array_subcategoria_control = $array_subcategoria = $this->get_array('Subcategorias', 'subcategoria', 'id', array('select' => array('si_subcategorias.id', "CONCAT(si_categorias.descripcion, ' - ', si_subcategorias.descripcion) as subcategoria"), 'join' => array(array('table' => 'si_categorias', 'where' => 'si_categorias.id=si_subcategorias.categoria_id')), 'sort_by' => 'si_categorias.descripcion, si_subcategorias.descripcion'), array(NULL => '-- Todas --'));
		$this->array_articulo_control = $array_articulo = $this->get_array('Articulos', 'articulo', 'id', array('select' => array('si_articulos.id', "CONCAT(si_marcas.nombre, ' - ', si_articulos.modelo, ' - ', COALESCE(si_articulos.numero_serie, ''), ' - ', COALESCE(si_articulos.numero_inventario, '')) as articulo"), 'join' => array(array('table' => 'si_marcas', 'where' => 'si_marcas.id=si_articulos.marca_id')), 'sort_by' => 'si_marcas.nombre, si_articulos.modelo'));
		$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', "CONCAT(areas.codigo, ' - ', areas.nombre) as area"), 'where' => array("nombre<>'-'")));
		$this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array('select' => array('id', "CONCAT(apellido, ', ', nombre) as persona"), 'sort_by' => 'apellido, nombre'), array(NULL => '-- Sin Persona --'));

		unset($this->Movimientos_model->fields['tipo']);
		$this->set_model_validation_rules($this->Movimientos_model);
		$this->form_validation->set_rules('cant_rows', 'Cantidad de Artículos', 'required|integer');
		if ($this->input->post('cant_rows'))
		{
			$cant_rows = $this->input->post('cant_rows');
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$this->form_validation->set_rules('subcategoria_' . $i, 'Subcategoría ' . $i, 'callback_control_combo[subcategoria]');
				$this->form_validation->set_rules('articulo_' . $i, 'Artículo ' . $i, 'required|callback_control_combo[articulo]');
				$this->form_validation->set_rules('ala_' . $i, 'Ala ' . $i, 'max_length[50]');
				$this->form_validation->set_rules('oficina_' . $i, 'Oficina ' . $i, 'integer[10]');
				$this->form_validation->set_rules('ip_' . $i, 'IP ' . $i, 'max_length[50]');
			}
		}

		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Movimientos_model->create(array(
					'fecha' => $this->get_datetime_sql('fecha'),
					'tipo' => 'Ingreso',
					'area_id' => $this->input->post('area'),
					'persona_id' => $this->input->post('persona'),
					'descripcion' => $this->input->post('descripcion')), FALSE);
			$movimiento_id = $this->Movimientos_model->get_row_id();

			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$trans_ok &= $this->Movimientos_detalle_model->create(array(
						'movimiento_id' => $movimiento_id,
						'articulo_id' => $this->input->post('articulo_' . $i),
						'cantidad' => 1,
						'ala' => $this->input->post('ala_' . $i),
						'oficina' => $this->input->post('oficina_' . $i),
						'ip' => $this->input->post('ip_' . $i)), FALSE);
			}
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Movimientos_model->get_msg());
				redirect('stock_informatica/stock/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br /> Se ha producido un error con la base de datos.';
				if ($this->Movimientos_model->get_error())
				{
					$error_msg .= $this->Movimientos_model->get_error();
				}
				if ($this->Movimientos_detalle_model->get_error())
				{
					$error_msg .= $this->Movimientos_detalle_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$rows = $this->form_validation->set_value('cant_rows', 1);
		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"subcategoria_$i" => array('label' => 'Subcategoría', 'input_type' => 'combo', 'type' => 'bselect', 'class' => 'select_subcategoria', 'required' => TRUE),
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'class' => 'select_articulo', 'required' => TRUE),
					"ala_$i" => array('label' => 'Ala', 'maxlength' => '50'),
					"oficina_$i" => array('label' => 'Oficina', 'type' => 'integer', 'maxlength' => '10'),
					"ip_$i" => array('label' => 'IP', 'maxlength' => '50')
			);

			$fake_model_fields["subcategoria_$i"]['array'] = $array_subcategoria;
			$fake_model_fields["articulo_$i"]['array'] = $array_articulo;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, NULL, FALSE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$this->Movimientos_model->fields['area']['array'] = $array_area;
		$this->Movimientos_model->fields['persona']['array'] = $array_persona;
		$data['fields'] = $this->build_fields($this->Movimientos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Ingreso Stock';
		$data['title'] = TITLE . ' - Ingreso Stock';
		$data['js'] = 'js/stock_informatica/base.js';
		$this->load_template('stock_informatica/stock/stock_ingreso', $data);
	}

	public function transferencia_articulo($movimiento_det_id, $baja = FALSE)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("stock_informatica/stock/listar", 'refresh');
		}

		$articulo = $this->Movimientos_detalle_model->get(array(
				'id' => $movimiento_det_id,
				'join' => array(
						array('table' => 'si_movimientos', 'where' => 'si_movimientos.id=si_movimientos_detalle.movimiento_id', 'columnas' => array('si_movimientos.area_id as area_id', 'si_movimientos.persona_id as persona_id')),
						array('table' => 'si_articulos', 'where' => 'si_articulos.id=si_movimientos_detalle.articulo_id', 'columnas' => array()),
						array('table' => 'areas', 'where' => 'areas.id=si_movimientos.area_id', 'columnas' => array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
				)
		));
		$articulos_area = $this->Movimientos_detalle_model->get(array(
				'select' => array('si_movimientos.area_id', 'si_articulos.subcategoria_id', 'si_movimientos_detalle.articulo_id',
						'si_articulos.numero_serie', 'si_articulos.numero_inventario', 'SUM(si_movimientos_detalle.cantidad) AS cantidad',
						'MAX(si_movimientos_detalle.id) AS fila_id'
				),
				'join' => array(
						array('type' => 'left', 'table' => 'si_movimientos', 'where' => 'si_movimientos.id = si_movimientos_detalle.movimiento_id'),
						array('type' => 'left', 'table' => 'si_articulos', 'where' => 'si_articulos.id = si_movimientos_detalle.articulo_id')
				),
				'group_by' => array('si_movimientos.area_id', 'si_articulos.subcategoria_id', 'si_movimientos_detalle.articulo_id', 'si_articulos.numero_serie', 'si_articulos.numero_inventario'),
				'having' => array(
						array('column' => 'SUM(si_movimientos_detalle.cantidad) >', 'value' => '0'),
						array('column' => 'MAX(si_movimientos_detalle.id)', 'value' => $movimiento_det_id)
				),
				'sort_by' => 'MAX(si_movimientos_detalle.id)'
		));
		if (empty($articulos_area))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}

		$this->array_subcategoria_control = $array_subcategoria = $this->get_array('Subcategorias', 'subcategoria', 'id', array('select' => array('si_subcategorias.id', "CONCAT(si_categorias.descripcion, ' - ', si_subcategorias.descripcion) as subcategoria"), 'join' => array(array('table' => 'si_categorias', 'where' => 'si_categorias.id=si_subcategorias.categoria_id')), 'sort_by' => 'si_categorias.descripcion, si_subcategorias.descripcion'), array(NULL => '-- Todas --'));
		$this->array_articulo_control = $array_articulo = $this->get_array('Articulos', 'articulo', 'id', array('select' => array('si_articulos.id', "CONCAT(si_marcas.nombre, ' - ', si_articulos.modelo) as articulo"), 'join' => array(array('table' => 'si_marcas', 'where' => 'si_marcas.id=si_articulos.marca_id')), 'sort_by' => 'si_marcas.nombre, si_articulos.modelo'));
		if ($baja)
		{
			$this->array_area_control = $array_area = array('NULL' => 'Baja');
		}
		else
		{
			$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', "CONCAT(areas.codigo, ' - ', areas.nombre) as area"), 'where' => array("nombre<>'-'")));
		}
		$this->array_origen_control = $array_origen = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', "CONCAT(areas.codigo, ' - ', areas.nombre) as area"), 'where' => array("nombre<>'-'")));


		$this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array('select' => array('id', "CONCAT(apellido, ', ', nombre) as persona"), 'sort_by' => 'apellido, nombre'), array(NULL => '-- Sin Persona --'));

		unset($this->Movimientos_model->fields['tipo']);
		$this->Movimientos_model->fields['origen'] = array('label' => 'Origen', 'disabled' => TRUE);
		$this->Movimientos_model->fields['area']['label'] = 'Destino';
		$this->set_model_validation_rules($this->Movimientos_model);

		for ($i = 1; $i <= sizeof($articulos_area); $i++)
		{
			${"array_cantidad_$i"} = array();
			for ($j = 1; $j <= $articulos_area[$i - 1]->cantidad; $j++)
			{
				${"array_cantidad_$i"}[$j] = $j;
			}
			$this->{"array_cantidad_{$i}_control"} = ${"array_cantidad_$i"};
			$this->form_validation->set_rules("cantidad_$i", "Cantidad $i", "callback_control_combo[cantidad_$i]");
			$this->form_validation->set_rules("ala_$i", "Ala $i", 'max_length[50]');
			$this->form_validation->set_rules("oficina_$i", "Oficina $i", 'integer');
			$this->form_validation->set_rules("ip_$i", "IP $i", 'max_length[50]');
		}

		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Movimientos_model->create(array(
					'fecha' => $this->get_datetime_sql('fecha'),
					'tipo' => $baja ? 'Baja' : 'Transferencia Origen',
					'area_id' => $articulo->area_id,
					'persona_id' => $articulo->persona_id,
					'descripcion' => $this->input->post('descripcion')), FALSE);
			$origen_id = $this->Movimientos_model->get_row_id();

			for ($i = 1; $i <= sizeof($articulos_area); $i++)
			{
				$trans_ok &= $this->Movimientos_detalle_model->create(array(
						'movimiento_id' => $origen_id,
						'articulo_id' => $articulo->articulo_id,
						'numero_serie' => $articulo->numero_serie,
						'numero_inventario' => $articulo->numero_inventario,
						'cantidad' => -1 * ($this->input->post('cantidad_' . $i)),
						'ala' => $articulo->ala,
						'oficina' => $articulo->oficina,
						'ip' => $articulo->ip), FALSE);
			}

			if (!$baja)
			{
				$trans_ok &= $this->Movimientos_model->create(array(
						'fecha' => $this->get_datetime_sql('fecha'),
						'tipo' => 'Transferencia Destino',
						'area_id' => $this->input->post('area'),
						'persona_id' => $this->input->post('persona'),
						'descripcion' => $this->input->post('descripcion')), FALSE);
				$destino_id = $this->Movimientos_model->get_row_id();

				for ($i = 1; $i <= sizeof($articulos_area); $i++)
				{
					$trans_ok &= $this->Movimientos_detalle_model->create(array(
							'movimiento_id' => $destino_id,
							'articulo_id' => $articulo->articulo_id,
							'numero_serie' => $articulo->numero_serie,
							'numero_inventario' => $articulo->numero_inventario,
							'cantidad' => 1 * ($this->input->post('cantidad_' . $i)),
							'ala' => $this->input->post('ala_' . $i),
							'oficina' => $this->input->post('oficina_' . $i),
							'ip' => $this->input->post('ip_' . $i)), FALSE);
				}
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Movimientos_model->get_msg());
				redirect('stock_informatica/stock/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br /> Se ha producido un error con la base de datos.';
				if ($this->Movimientos_model->get_error())
				{
					$error_msg .= $this->Movimientos_model->get_error();
				}
				if ($this->Movimientos_detalle_model->get_error())
				{
					$error_msg .= $this->Movimientos_detalle_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= sizeof($articulos_area); $i++)
		{
			$fake_model_fields = array(
					"subcategoria_$i" => array('label' => 'Subcategoría', 'input_type' => 'combo', 'type' => 'bselect', 'class' => 'select_subcategoria', 'disabled' => TRUE),
					"articulo_$i" => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'class' => 'select_articulo', 'disabled' => TRUE),
					"numero_serie_$i" => array('label' => 'N° de Serie', 'maxlength' => '50', 'disabled' => TRUE),
					"numero_inventario_$i" => array('label' => 'N° de Inventario', 'maxlength' => '50', 'disabled' => TRUE),
					"ala_$i" => array('label' => 'Ala', 'maxlength' => '50'),
					"oficina_$i" => array('label' => 'Oficina', 'type' => 'integer', 'maxlength' => '10'),
					"ip_$i" => array('label' => 'IP', 'maxlength' => '50'),
					"cantidad_$i" => array('label' => 'Cantidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
			);

			if (empty($_POST))
			{
				$temp_detalle = new stdClass();
				$temp_detalle->{"id_detalle_{$i}"} = $articulos_area[$i - 1]->fila_id;
				$temp_detalle->{"subcategoria_{$i}_id"} = $articulos_area[$i - 1]->subcategoria_id;
				$temp_detalle->{"articulo_{$i}_id"} = $articulos_area[$i - 1]->articulo_id;
				$temp_detalle->{"numero_serie_{$i}"} = $articulos_area[$i - 1]->numero_serie;
				$temp_detalle->{"numero_inventario_{$i}"} = $articulos_area[$i - 1]->numero_inventario;
				$temp_detalle->{"ala_{$i}"} = NULL;
				$temp_detalle->{"oficina_{$i}"} = NULL;
				$temp_detalle->{"ip_{$i}"} = NULL;
				$temp_detalle->{"cantidad_{$i}_id"} = $articulos_area[$i - 1]->cantidad;
			}
			else
			{
				$temp_detalle = new stdClass();
				$temp_detalle->{"id_detalle_{$i}"} = $articulos_area[$i - 1]->fila_id;
				$temp_detalle->{"subcategoria_{$i}_id"} = $articulos_area[$i - 1]->subcategoria_id;
				$temp_detalle->{"articulo_{$i}_id"} = $articulos_area[$i - 1]->articulo_id;
				$temp_detalle->{"numero_serie_{$i}"} = $articulos_area[$i - 1]->numero_serie;
				$temp_detalle->{"numero_inventario_{$i}"} = $articulos_area[$i - 1]->numero_inventario;
				$temp_detalle->{"cantidad_{$i}_id"} = $this->form_validation->set_value("cantidad_{$i}_id");
				$temp_detalle->{"ala_{$i}"} = $this->form_validation->set_value("ala_{$i}");
				$temp_detalle->{"oficina_{$i}"} = $this->form_validation->set_value("oficina_{$i}");
				$temp_detalle->{"ip_{$i}"} = $this->form_validation->set_value("ip_{$i}");
			}

			$fake_model_fields["subcategoria_$i"]['array'] = $array_subcategoria;
			$fake_model_fields["articulo_$i"]['array'] = $array_articulo;
			$fake_model_fields["cantidad_$i"]['array'] = ${"array_cantidad_$i"};
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, FALSE, 'table');
		}

		$temp_movimiento = new stdClass();
		$temp_movimiento->origen = $articulo->area;
		$temp_movimiento->area_id = NULL;
		$temp_movimiento->persona_id = NULL;
		$temp_movimiento->fecha = NULL;
		$temp_movimiento->descripcion = NULL;

		$this->Movimientos_model->fields['area']['array'] = $array_area;
		$this->Movimientos_model->fields['persona']['array'] = $array_persona;
		$data['fields'] = $this->build_fields($this->Movimientos_model->fields, $temp_movimiento);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Transferencia Stock';
		$data['title'] = TITLE . ' - Transferencia Stock';
		$this->load_template('stock_informatica/stock/stock_transferencia', $data);
	}

	public function ver($movimiento_det_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $movimiento_det_id == NULL || !ctype_digit($movimiento_det_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$articulo = $this->Movimientos_detalle_model->get(array('id' => $movimiento_det_id,
				'join' => array(
						array('si_movimientos', 'si_movimientos.id = si_movimientos_detalle.movimiento_id', 'left'),
						array('si_articulos', 'si_articulos.id = si_movimientos_detalle.articulo_id', 'left', array('si_articulos.modelo as modelo', 'si_articulos.numero_serie as numero_serie', 'si_articulos.numero_inventario as numero_inventario')),
						array('si_marcas', 'si_marcas.id = si_articulos.marca_id', 'left', array('si_marcas.nombre as marca')),
						array('areas', 'areas.id = si_movimientos.area_id', 'left', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area")),
						array('personas', 'personas.id = si_movimientos.persona_id', 'left', array("CONCAT(personas.apellido, ', ', personas.nombre) as persona")),
						array('si_subcategorias', 'si_subcategorias.id = si_articulos.subcategoria_id', 'left', array('si_subcategorias.descripcion as subcategoria')),
						array('si_categorias', 'si_categorias.id = si_subcategorias.categoria_id', 'left', array('si_categorias.descripcion as categoria'))
				),
		));
		if (empty($articulo))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}

		$atributos = $this->Atributos_articulos_model->get(array(
				'articulo_id' => $articulo->articulo_id,
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

		$articulos_area = $this->Movimientos_detalle_model->get(array(
				'select' => array(
						'si_movimientos_detalle.id as id',
						"CONCAT(areas.codigo, ' - ', areas.nombre) as area", "CONCAT(personas.apellido, ', ', personas.nombre) as persona",
						'si_movimientos.fecha', 'si_movimientos.tipo', 'si_movimientos.descripcion',
						'si_movimientos_detalle.id AS fila_id', 'si_movimientos_detalle.articulo_id as articulo_id',
						'si_articulos.numero_serie', 'si_articulos.numero_inventario', 'si_movimientos_detalle.cantidad',
						'si_movimientos_detalle.ala', 'si_movimientos_detalle.oficina', 'si_movimientos_detalle.ip'
				),
				'join' => array(
						array('type' => 'left', 'table' => 'si_articulos', 'where' => 'si_articulos.id = si_movimientos_detalle.articulo_id'),
						array('type' => 'left', 'table' => 'si_movimientos', 'where' => 'si_movimientos.id = si_movimientos_detalle.movimiento_id'),
						array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = si_movimientos.area_id'),
						array('type' => 'left', 'table' => 'personas', 'where' => 'personas.id = si_movimientos.persona_id'),
				),
				'where' => array(
						array('column' => 'si_movimientos_detalle.articulo_id', 'value' => $articulo->articulo_id),
						'si_movimientos.area_id IS NOT NULL'
				),
				'sort_by' => 'si_movimientos_detalle.id',
				'sort_direction' => 'desc'
		));
		if (empty($articulos_area))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}

		$data['fields_detalle_movimiento_array'] = array();
		if (!empty($articulos_area))
		{
			foreach ($articulos_area as $Detalle)
			{
				$fake_model_movimiento_fields = array(
						"fecha_{$Detalle->id}" => array('label' => 'Fecha', 'type' => 'datetime', 'readonly' => TRUE),
						"area_{$Detalle->id}" => array('label' => 'Área', 'readonly' => TRUE),
						"persona_{$Detalle->id}" => array('label' => 'Persona', 'readonly' => TRUE),
						"tipo_{$Detalle->id}" => array('label' => 'Tipo', 'readonly' => TRUE),
						"ala_{$Detalle->id}" => array('label' => 'Ala', 'readonly' => TRUE),
						"oficina_{$Detalle->id}" => array('label' => 'Oficina', 'readonly' => TRUE),
						"ip_{$Detalle->id}" => array('label' => 'IP', 'readonly' => TRUE),
						"cantidad_{$Detalle->id}" => array('label' => 'Cantidad', 'readonly' => TRUE)
				);

				$temp_detalle = new stdClass();
				$temp_detalle->{"fecha_{$Detalle->id}"} = $Detalle->fecha;
				$temp_detalle->{"area_{$Detalle->id}"} = $Detalle->area;
				$temp_detalle->{"persona_{$Detalle->id}"} = $Detalle->persona;
				$temp_detalle->{"tipo_{$Detalle->id}"} = $Detalle->tipo;
				$temp_detalle->{"ala_{$Detalle->id}"} = $Detalle->ala;
				$temp_detalle->{"oficina_{$Detalle->id}"} = $Detalle->oficina;
				$temp_detalle->{"ip_{$Detalle->id}"} = $Detalle->ip;
				$temp_detalle->{"cantidad_{$Detalle->id}"} = $Detalle->cantidad;

				$data['fields_detalle_movimiento_array'][] = $this->build_fields($fake_model_movimiento_fields, $temp_detalle, TRUE, 'table');
			}
		}

		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo, TRUE);
		$data['fields_movimiento'] = $this->build_fields($this->Movimientos_detalle_model->fields, $articulo, TRUE);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Stock';
		$data['title'] = TITLE . ' - Ver Stock';
		$this->load_template('stock_informatica/stock/stock_abm', $data);
	}
}