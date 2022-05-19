<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Areas extends MY_Controller
{

	/**
	 * Controlador de Areas
	 * Autor: Leandro
	 * Creado: 13/12/2019
	 * Modificado: 13/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Areas_model');
		$this->grupos_permitidos = array('admin', 'consulta_general');
		$this->grupos_solo_consulta = array('consulta_general');
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
						array('label' => 'Código', 'data' => 'codigo', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 34),
						array('label' => 'Agrupamiento', 'data' => 'agrupamiento', 'width' => 19),
						array('label' => 'Secretaría', 'data' => 'secretaria', 'width' => 34),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'areas_table',
				'source_url' => 'areas/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_areas_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Areas';
		$data['title'] = TITLE . ' - Areas';
		$this->load_template('areas/areas_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('areas.id, areas.codigo, areas.nombre, areas.agrupamiento, SEC.nombre as secretaria')
				->unset_column('id')
				->from('areas')
				->join('areas SEC', "SUBSTRING(SEC.agrupamiento, 1, 5) = SUBSTRING(areas.agrupamiento, 1, 5) AND (SUBSTRING(SEC.agrupamiento, 6, 23) = '.00.00.00.00.00.01' OR SEC.agrupamiento = '01.00.00.00.01.00.00.00') AND SEC.agrupamiento <> '01.00.00.00.00.00.00.01'", 'left')
				->add_column('ver', '', 'id');
		// Oficina 103 - INTENDENCIA Agregada por agrupamiento distinto a las demás secretarías (01.00.00.00.01.00.00.00)
		// Oficina 361 - ADSCRIPTOS BOMBEROS VOLUNTARIOS DE LUJAN Quitada por agrupamiento similar a secretarias (01.01.00.00.00.00.00.01)

		echo $this->datatables->generate();
	}
}