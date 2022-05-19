<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos extends MY_Controller
{

	/**
	 * Controlador de Movimientos
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 11/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('stock_informatica/Movimientos_model');
		$this->load->model('stock_informatica/Movimientos_detalle_model');
		$this->load->model('Areas_model');
		$this->load->model('Personas_model');
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
						array('label' => 'ID', 'data' => 'id', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => 'Area', 'data' => 'area', 'width' => 20),
						array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 29),
						array('label' => 'Persona', 'data' => 'persona', 'width' => 20),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'movimientos_table',
				'source_url' => 'stock_informatica/movimientos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_movimientos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['array_tipos'] = array('' => 'Todos', 'Baja' => 'Baja', 'Ingreso' => 'Ingreso', 'Transferencia Destino' => 'Transferencia Destino', 'Transferencia Origen' => 'Transferencia Origen');
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Movimientos';
		$data['title'] = TITLE . ' - Movimientos';
		$this->load_template('stock_informatica/movimientos/movimientos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select("si_movimientos.id, si_movimientos.fecha, CONCAT(areas.codigo, ' - ', areas.nombre) as area, si_movimientos.tipo, si_movimientos.descripcion, CONCAT(personas.apellido, ', ', personas.nombre) as persona")
				->from('si_movimientos')
				->join('areas', 'areas.id = si_movimientos.area_id', 'left')
				->join('personas', 'personas.id = si_movimientos.persona_id', 'left')
				->add_column('ver', '<a href="stock_informatica/movimientos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$movimiento = $this->Movimientos_model->get_one($id);
		if (empty($movimiento))
		{
			show_error('No se encontró el Movimiento', 500, 'Registro no encontrado');
		}

		$movimiento_detalle = $this->Movimientos_detalle_model->get(array(
				'movimiento_id' => $id,
				'join' => array(
						array('type' => 'left', 'table' => 'si_articulos', 'where' => 'si_articulos.id = si_movimientos_detalle.articulo_id', 'columnas' => array('si_articulos.modelo as articulo', 'si_articulos.numero_serie as numero_serie', 'si_articulos.numero_inventario as numero_inventario')),
						array('type' => 'left', 'table' => 'si_marcas', 'where' => 'si_marcas.id = si_articulos.marca_id', 'columnas' => array('si_marcas.nombre as marca')),
						array('type' => 'left', 'table' => 'si_subcategorias', 'where' => 'si_subcategorias.id = si_articulos.subcategoria_id'),
						array('type' => 'left', 'table' => 'si_categorias', 'where' => 'si_categorias.id = si_subcategorias.categoria_id', 'columnas' => array("CONCAT(si_categorias.descripcion, ' - ', si_subcategorias.descripcion) as subcategoria")),
				)
		));

		$data['fields_detalle_array'] = array();
		if (!empty($movimiento_detalle))
		{
			foreach ($movimiento_detalle as $Detalle)
			{
				$fake_model_fields = array(
						"subcategoria_{$Detalle->id}" => array('label' => 'Subcategoría', 'readonly' => TRUE),
						"articulo_{$Detalle->id}" => array('label' => 'Artículo', 'readonly' => TRUE),
						"serie_{$Detalle->id}" => array('label' => 'N° Serie', 'readonly' => TRUE),
						"inventario_{$Detalle->id}" => array('label' => 'N° Inventario', 'readonly' => TRUE),
						"ala_{$Detalle->id}" => array('label' => 'Ala', 'readonly' => TRUE),
						"oficina_{$Detalle->id}" => array('label' => 'Oficina', 'readonly' => TRUE),
						"ip_{$Detalle->id}" => array('label' => 'IP', 'readonly' => TRUE),
						"cantidad_{$Detalle->id}" => array('label' => 'Cantidad', 'readonly' => TRUE)
				);

				$temp_detalle = new stdClass();
				$temp_detalle->{"subcategoria_{$Detalle->id}"} = $Detalle->subcategoria;
				$temp_detalle->{"articulo_{$Detalle->id}"} = "$Detalle->marca - $Detalle->articulo";
				$temp_detalle->{"serie_{$Detalle->id}"} = $Detalle->numero_serie;
				$temp_detalle->{"inventario_{$Detalle->id}"} = $Detalle->numero_inventario;
				$temp_detalle->{"ala_{$Detalle->id}"} = $Detalle->ala;
				$temp_detalle->{"oficina_{$Detalle->id}"} = $Detalle->oficina;
				$temp_detalle->{"ip_{$Detalle->id}"} = $Detalle->ip;
				$temp_detalle->{"cantidad_{$Detalle->id}"} = $Detalle->cantidad;

				$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
			}
		}

		$data['fields'] = $this->build_fields($this->Movimientos_model->fields, $movimiento, TRUE);
		$data['movimiento'] = $movimiento;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Movimiento';
		$data['title'] = TITLE . ' - Ver Movimiento';
		$this->load_template('stock_informatica/movimientos/movimientos_abm', $data);
	}
}