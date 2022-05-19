<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Impresoras_areas extends MY_Controller
{

	/**
	 * Controlador de Impresoras Áreas
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 07/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toner/Impresoras_areas_model');
		$this->load->model('toner/Impresoras_model');
		$this->load->model('Areas_model');
		$this->grupos_permitidos = array('admin', 'toner_admin', 'toner_consulta_general');
		$this->grupos_solo_consulta = array('toner_consulta_general');
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
						array('label' => 'Área', 'data' => 'area', 'width' => 45),
						array('label' => 'Impresora', 'data' => 'impresora', 'width' => 46),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'impresoras_areas_table',
				'source_url' => 'toner/impresoras_areas/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_impresoras_areas_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Impresoras Áreas';
		$data['title'] = TITLE . ' - Impresoras Áreas';
		$this->load_template('toner/impresoras_areas/impresoras_areas_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select("gt_impresoras_areas.id, CONCAT(areas.codigo, ' - ', areas.nombre) as area, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora")
				->from('gt_impresoras_areas')
				->join('areas', 'areas.id = gt_impresoras_areas.area_id', 'left')
				->join('gt_impresoras', 'gt_impresoras.id = gt_impresoras_areas.impresora_id', 'left')
				->join('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'left')
				->add_column('ver', '<a href="toner/impresoras_areas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="toner/impresoras_areas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="toner/impresoras_areas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('toner/impresoras_areas/listar', 'refresh');
		}

		$this->array_impresora_control = $array_impresora = $this->get_array('Impresoras', 'impresora', 'id', array('select' => array("gt_impresoras.id, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"), 'join' => array(array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT'))));
		$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
		$this->set_model_validation_rules($this->Impresoras_areas_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Impresoras_areas_model->create(array(
					'impresora_id' => $this->input->post('impresora'),
					'area_id' => $this->input->post('area')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Impresoras_areas_model->get_msg());
				redirect('toner/impresoras_areas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Impresoras_areas_model->get_error())
				{
					$error_msg .= $this->Impresoras_areas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Impresoras_areas_model->fields['impresora']['array'] = $array_impresora;
		$this->Impresoras_areas_model->fields['area']['array'] = $array_area;
		$data['fields'] = $this->build_fields($this->Impresoras_areas_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Impresora Área';
		$data['title'] = TITLE . ' - Agregar Impresora Área';
		$this->load_template('toner/impresoras_areas/impresoras_areas_abm', $data);
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
			redirect("toner/impresoras_areas/ver/$id", 'refresh');
		}

		$this->array_impresora_control = $array_impresora = $this->get_array('Impresoras', 'impresora', 'id', array('select' => array("gt_impresoras.id, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"), 'join' => array(array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT'))));
		$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
		$impresoras_area = $this->Impresoras_areas_model->get(array('id' => $id));
		if (empty($impresoras_area))
		{
			show_error('No se encontró el Impresora Área', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Impresoras_areas_model);
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
				$trans_ok &= $this->Impresoras_areas_model->update(array(
						'id' => $this->input->post('id'),
						'impresora_id' => $this->input->post('impresora'),
						'area_id' => $this->input->post('area')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Impresoras_areas_model->get_msg());
					redirect('toner/impresoras_areas/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Impresoras_areas_model->get_error())
					{
						$error_msg .= $this->Impresoras_areas_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Impresoras_areas_model->fields['impresora']['array'] = $array_impresora;
		$this->Impresoras_areas_model->fields['area']['array'] = $array_area;
		$data['fields'] = $this->build_fields($this->Impresoras_areas_model->fields, $impresoras_area);
		$data['impresoras_area'] = $impresoras_area;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Impresora Área';
		$data['title'] = TITLE . ' - Editar Impresora Área';
		$this->load_template('toner/impresoras_areas/impresoras_areas_abm', $data);
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
			redirect("toner/impresoras_areas/ver/$id", 'refresh');
		}

		$impresoras_area = $this->Impresoras_areas_model->get_one($id);
		if (empty($impresoras_area))
		{
			show_error('No se encontró el Impresora Área', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Impresoras_areas_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Impresoras_areas_model->get_msg());
				redirect('toner/impresoras_areas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Impresoras_areas_model->get_error())
				{
					$error_msg .= $this->Impresoras_areas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Impresoras_areas_model->fields, $impresoras_area, TRUE);
		$data['impresoras_area'] = $impresoras_area;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Impresora Área';
		$data['title'] = TITLE . ' - Eliminar Impresora Área';
		$this->load_template('toner/impresoras_areas/impresoras_areas_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$impresoras_area = $this->Impresoras_areas_model->get_one($id);
		if (empty($impresoras_area))
		{
			show_error('No se encontró el Impresora Área', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Impresoras_areas_model->fields, $impresoras_area, TRUE);
		$data['impresoras_area'] = $impresoras_area;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Impresora Área';
		$data['title'] = TITLE . ' - Ver Impresora Área';
		$this->load_template('toner/impresoras_areas/impresoras_areas_abm', $data);
	}

	public function get_impresoras()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('area_id', 'ID Area', 'required|integer|max_length[8]');
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('toner/Consumibles_impresoras_model');
			$area_id = $this->input->post('area_id');
			$impresoras = $this->Impresoras_areas_model->get_impresoras($area_id);
			$impresoras_tmp = array();
			if (!empty($impresoras))
			{
				foreach ($impresoras as $Impresora)
				{
					$imp = array();
					$imp['nombre'] = "$Impresora->nombre - $Impresora->modelo";
					$imp['id'] = $Impresora->id;
					$impresoras_tmp[] = $imp;
				}
				$data['impresoras'] = $impresoras_tmp;
			}
			else
			{
				$data['error'] = 'Impresora no encontrada';
			}
		}
		else
		{
			$data['error'] = 'Debe ingresar un ID válido';
		}

		echo json_encode($data);
	}
}