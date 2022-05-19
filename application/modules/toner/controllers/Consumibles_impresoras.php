<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Consumibles_impresoras extends MY_Controller
{

	/**
	 * Controlador de Consumibles Impresora
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 27/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toner/Consumibles_impresoras_model');
		$this->load->model('toner/Consumibles_model');
		$this->load->model('toner/Impresoras_model');
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
						array('label' => 'Impresora', 'data' => 'impresora', 'width' => 25),
						array('label' => 'Consumible', 'data' => 'consumible', 'width' => 25),
						array('label' => 'Desc Consumible', 'data' => 'consumible_desc', 'width' => 41),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'consumibles_impresoras_table',
				'source_url' => 'toner/consumibles_impresoras/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_consumibles_impresoras_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Consumibles Impresora';
		$data['title'] = TITLE . ' - Consumibles Impresora';
		$this->load_template('toner/consumibles_impresoras/consumibles_impresoras_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select("gt_consumibles_impresoras.id, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora, gt_consumibles.modelo as consumible, gt_consumibles.descripcion as consumible_desc")
				->from('gt_consumibles_impresoras')
				->join('gt_consumibles', 'gt_consumibles.id = gt_consumibles_impresoras.consumible_id', 'left')
				->join('gt_impresoras', 'gt_impresoras.id = gt_consumibles_impresoras.impresora_id', 'left')
				->join('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'left')
				->add_column('ver', '<a href="toner/consumibles_impresoras/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="toner/consumibles_impresoras/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="toner/consumibles_impresoras/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('toner/consumibles_impresoras/listar', 'refresh');
		}

		$this->array_consumible_control = $array_consumible = $this->get_array('Consumibles', 'consumible', 'id', array('select' => array("gt_consumibles.id, CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"), 'where' => array(array('column' => 'estado', 'value' => 'Activo'))));
		$this->array_impresora_control = $array_impresora = $this->get_array('Impresoras', 'impresora', 'id', array('select' => array("gt_impresoras.id, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"), 'join' => array(array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT'))));
		$this->set_model_validation_rules($this->Consumibles_impresoras_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Consumibles_impresoras_model->create(array(
					'consumible_id' => $this->input->post('consumible'),
					'impresora_id' => $this->input->post('impresora')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Consumibles_impresoras_model->get_msg());
				redirect('toner/consumibles_impresoras/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Consumibles_impresoras_model->get_error())
				{
					$error_msg .= $this->Consumibles_impresoras_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Consumibles_impresoras_model->fields['consumible']['array'] = $array_consumible;
		$this->Consumibles_impresoras_model->fields['impresora']['array'] = $array_impresora;
		$data['fields'] = $this->build_fields($this->Consumibles_impresoras_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Consumible Impresora';
		$data['title'] = TITLE . ' - Agregar Consumible Impresora';
		$this->load_template('toner/consumibles_impresoras/consumibles_impresoras_abm', $data);
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
			redirect("toner/consumibles_impresoras/ver/$id", 'refresh');
		}

		$consumibles_impresora = $this->Consumibles_impresoras_model->get(array('id' => $id));
		if (empty($consumibles_impresora))
		{
			show_error('No se encontró el Consumible Impresora', 500, 'Registro no encontrado');
		}

		$this->array_consumible_control = $array_consumible = $this->get_array('Consumibles', 'consumible', 'id', array('select' => array("gt_consumibles.id, CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible"), 'where' => array(array('column' => "(estado = 'Activo' OR gt_consumibles.id = $consumibles_impresora->consumible_id)", 'value' => '', 'override' => TRUE))));
		$this->array_impresora_control = $array_impresora = $this->get_array('Impresoras', 'impresora', 'id', array('select' => array("gt_impresoras.id, CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"), 'join' => array(array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT'))));

		$this->set_model_validation_rules($this->Consumibles_impresoras_model);
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
				$trans_ok &= $this->Consumibles_impresoras_model->update(array(
						'id' => $this->input->post('id'),
						'consumible_id' => $this->input->post('consumible'),
						'impresora_id' => $this->input->post('impresora')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Consumibles_impresoras_model->get_msg());
					redirect('toner/consumibles_impresoras/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Consumibles_impresoras_model->get_error())
					{
						$error_msg .= $this->Consumibles_impresoras_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Consumibles_impresoras_model->fields['consumible']['array'] = $array_consumible;
		$this->Consumibles_impresoras_model->fields['impresora']['array'] = $array_impresora;
		$data['fields'] = $this->build_fields($this->Consumibles_impresoras_model->fields, $consumibles_impresora);
		$data['consumibles_impresora'] = $consumibles_impresora;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Consumible Impresora';
		$data['title'] = TITLE . ' - Editar Consumible Impresora';
		$this->load_template('toner/consumibles_impresoras/consumibles_impresoras_abm', $data);
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
			redirect("toner/consumibles_impresoras/ver/$id", 'refresh');
		}

		$consumibles_impresora = $this->Consumibles_impresoras_model->get_one($id);
		if (empty($consumibles_impresora))
		{
			show_error('No se encontró el Consumible Impresora', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Consumibles_impresoras_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Consumibles_impresoras_model->get_msg());
				redirect('toner/consumibles_impresoras/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Consumibles_impresoras_model->get_error())
				{
					$error_msg .= $this->Consumibles_impresoras_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Consumibles_impresoras_model->fields, $consumibles_impresora, TRUE);
		$data['consumibles_impresora'] = $consumibles_impresora;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Consumible Impresora';
		$data['title'] = TITLE . ' - Eliminar Consumible Impresora';
		$this->load_template('toner/consumibles_impresoras/consumibles_impresoras_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$consumibles_impresora = $this->Consumibles_impresoras_model->get_one($id);
		if (empty($consumibles_impresora))
		{
			show_error('No se encontró el Consumible Impresora', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Consumibles_impresoras_model->fields, $consumibles_impresora, TRUE);
		$data['consumibles_impresora'] = $consumibles_impresora;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Consumible Impresora';
		$data['title'] = TITLE . ' - Ver Consumible Impresora';
		$this->load_template('toner/consumibles_impresoras/consumibles_impresoras_abm', $data);
	}

	public function get_consumibles()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('impresora_id', 'ID Impresora', 'required|integer|max_length[8]');
		if ($this->form_validation->run() === TRUE)
		{
			$impresora_id = $this->input->post('impresora_id');
			$consumibles = $this->Consumibles_impresoras_model->get_consumibles($impresora_id);
			$consumibles_tmp = array();
			if (!empty($consumibles))
			{
				foreach ($consumibles as $Consumible)
				{
					$cons = array();
					$cons['nombre'] = "$Consumible->modelo - $Consumible->descripcion";
					$cons['id'] = $Consumible->id;
					$consumibles_tmp[] = $cons;
				}
				$data['consumibles'] = $consumibles_tmp;
			}
			else
			{
				$data['error'] = 'Consumible no encontrado';
			}
		}
		else
		{
			$data['error'] = 'Debe ingresar un ID válido';
		}

		echo json_encode($data);
	}
}