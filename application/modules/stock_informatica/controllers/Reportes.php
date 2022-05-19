<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

	/**
	 * Controlador de Reportes
	 * Autor: Leandro
	 * Creado: 30/03/2020
	 * Modificado: 08/04/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('Areas_model');
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

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de reportes';
		$data['title'] = TITLE . ' - Reportes';
		$this->load_template('stock_informatica/reportes/reportes_listar', $data);
	}

	public function stock_area()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE)
		);

		$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('stock_informatica/Atributos_model');
			$this->load->model('stock_informatica/Movimientos_detalle_model');
                        
                        $titulos = array('Área', 'Categoría', 'Subcategoría', 'Marca', 'Modelo', 'N° Serie', 'N° Inventario', 'IP', 'Ala', 'Oficina');
			$options['select'] = array(
                                        "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                                        'si_categorias.descripcion as categoria',
                                        'si_subcategorias.descripcion as subcategoria',
                                        'si_marcas.nombre as marca',
                                        'si_articulos.modelo', 
                                        'si_articulos.numero_serie', 
                                        'si_articulos.numero_inventario', 
                                        'si_movimientos_detalle.ip',
                                        'si_movimientos_detalle.ala', 
                                        'si_movimientos_detalle.oficina'
                        );
                        $options['join'] = array(
					array('si_movimientos', 'si_movimientos.id = si_movimientos_detalle.movimiento_id', 'left'),
                                        array('areas', 'areas.id = si_movimientos.area_id', 'left'),
                                        array('si_articulos', 'si_articulos.id = si_movimientos_detalle.articulo_id', 'left'),
                                        array('si_marcas', 'si_marcas.id = si_articulos.marca_id', 'left'),
                                        array('si_subcategorias', 'si_subcategorias.id = si_articulos.subcategoria_id', 'left'),
                                        array('si_categorias', 'si_categorias.id = si_subcategorias.categoria_id', 'left')
			);
                        $options['where_in'] = array(array('column' => 'areas.id', 'value' => $this->input->post('area')));
			$options['having'] = array('SUM(si_movimientos_detalle.cantidad) > 0');
			$options['group_by'] = 'areas.codigo, areas.nombre, si_categorias.descripcion, si_subcategorias.descripcion, si_marcas.nombre, si_articulos.modelo, si_articulos.numero_serie, si_articulos.numero_inventario, si_articulos.id';
			$options['return_array'] = TRUE;
                        
                        $atributos = $this->Atributos_model->get();
                        if (!empty($atributos))
                        {
                                foreach ($atributos as $Atributo) 
                                {
                                        $titulos[] = $Atributo->nombre;
                                        $options['select'][] = "A{$Atributo->id}.valor A{$Atributo->id}";
                                        $options['join'][] = array("si_atributos_articulos A{$Atributo->id}", "A{$Atributo->id}.articulo_id=si_articulos.id and A{$Atributo->id}.atributo_id = {$Atributo->id}", 'left');
                                }
                        }
                        
			$print_data = $this->Movimientos_detalle_model->get($options);
			if (!empty($print_data))
			{                                    
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()
						->setCreator("SistemaMLC")
						->setLastModifiedBy("SistemaMLC")
						->setTitle("Informe de Stock por Área")
						->setDescription("Informe de Stock por Área (Módulo Stock Informática)");
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
				$sheet->setTitle("Informe de Stock por Área");
				$sheet->getColumnDimension('A')->setWidth(40);
				$sheet->getColumnDimension('B')->setWidth(20);
				$sheet->getColumnDimension('C')->setWidth(20);
				$sheet->getColumnDimension('D')->setWidth(20);
				$sheet->getColumnDimension('E')->setWidth(40);
				$sheet->getColumnDimension('F')->setWidth(20);
				$sheet->getColumnDimension('G')->setWidth(20);
				$sheet->getColumnDimension('H')->setWidth(20);
				$sheet->getColumnDimension('I')->setWidth(10);
				$sheet->getColumnDimension('J')->setWidth(10);
				$sheet->getStyle('A1:Q1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array($titulos), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$nombreArchivo = 'InformeStockArea_' . date('Ymd');

				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
				header("Cache-Control: max-age=0");
				
				$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			}
			else
			{
				$error_msg = '<br />Sin Datos';
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$fake_model->fields['area']['array'] = $array_area;
		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Stock por Área';
		$data['title'] = TITLE . ' - Informe de Stock por Área';
		$this->load_template('stock_informatica/reportes/reportes_content', $data);
	}
}