<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Consumibles extends MY_Controller
{

	/**
	 * Controlador de Consumibles
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 07/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toner/Consumibles_model');
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
						array('label' => 'Modelo', 'data' => 'modelo', 'width' => 15),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 27),
						array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
						array('label' => 'Llenos', 'data' => 'stock_llenos', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Vacíos', 'data' => 'stock_vacios', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'F. Servicio', 'data' => 'stock_fuera_servicio', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 10),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'consumibles_table',
				'source_url' => 'toner/consumibles/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_consumibles_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Consumibles';
		$data['title'] = TITLE . ' - Consumibles';
		$this->load_template('toner/consumibles/consumibles_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('toner/datatables_functions_helper');
		$this->datatables
				->select('id, modelo, descripcion, tipo, stock_llenos, stock_vacios, stock_fuera_servicio, estado')
				->from('gt_consumibles')
				->edit_column('estado', '$1', 'dt_column_consumibles_estado(estado)', TRUE)
				->add_column('ver', '<a href="toner/consumibles/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="toner/consumibles/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="toner/consumibles/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('toner/consumibles/listar', 'refresh');
		}

		$this->array_tipo_control = $array_tipo = array('Toner' => 'Toner', 'Cartucho' => 'Cartucho', 'Cinta' => 'Cinta');
		$this->array_estado_control = $array_estado = array('Activo' => 'Activo', 'Baja' => 'Baja');
		$this->set_model_validation_rules($this->Consumibles_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Consumibles_model->create(array(
					'modelo' => $this->input->post('modelo'),
					'descripcion' => $this->input->post('descripcion'),
					'tipo' => $this->input->post('tipo'),
					'stock_vacios' => '0',
					'stock_llenos' => '0',
					'stock_fuera_servicio' => '0',
					'estado' => $this->input->post('estado')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Consumibles_model->get_msg());
				redirect('toner/consumibles/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Consumibles_model->get_error())
				{
					$error_msg .= $this->Consumibles_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Consumibles_model->fields['tipo']['array'] = $array_tipo;
		$this->Consumibles_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($this->Consumibles_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Consumible';
		$data['title'] = TITLE . ' - Agregar Consumible';
		$this->load_template('toner/consumibles/consumibles_abm', $data);
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
			redirect("toner/consumibles/ver/$id", 'refresh');
		}

		$consumible = $this->Consumibles_model->get(array('id' => $id));
		if (empty($consumible))
		{
			show_error('No se encontró el Consumible', 500, 'Registro no encontrado');
		}

		$this->array_tipo_control = $array_tipo = array('Toner' => 'Toner', 'Cartucho' => 'Cartucho', 'Cinta' => 'Cinta');
		$this->array_estado_control = $array_estado = array('Activo' => 'Activo', 'Baja' => 'Baja');
		$this->set_model_validation_rules($this->Consumibles_model);
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
				$trans_ok &= $this->Consumibles_model->update(array(
						'id' => $this->input->post('id'),
						'modelo' => $this->input->post('modelo'),
						'descripcion' => $this->input->post('descripcion'),
						'tipo' => $this->input->post('tipo'),
						'estado' => $this->input->post('estado')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Consumibles_model->get_msg());
					redirect('toner/consumibles/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Consumibles_model->get_error())
					{
						$error_msg .= $this->Consumibles_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Consumibles_model->fields['tipo']['array'] = $array_tipo;
		$this->Consumibles_model->fields['estado']['array'] = $array_estado;
		$data['fields'] = $this->build_fields($this->Consumibles_model->fields, $consumible);
		$data['consumibl'] = $consumible;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Consumible';
		$data['title'] = TITLE . ' - Editar Consumible';
		$this->load_template('toner/consumibles/consumibles_abm', $data);
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
			redirect("toner/consumibles/ver/$id", 'refresh');
		}

		$consumible = $this->Consumibles_model->get_one($id);
		if (empty($consumible))
		{
			show_error('No se encontró el Consumible', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Consumibles_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Consumibles_model->get_msg());
				redirect('toner/consumibles/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Consumibles_model->get_error())
				{
					$error_msg .= $this->Consumibles_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Consumibles_model->fields, $consumible, TRUE);
		$data['consumibl'] = $consumible;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Consumible';
		$data['title'] = TITLE . ' - Eliminar Consumible';
		$this->load_template('toner/consumibles/consumibles_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$consumible = $this->Consumibles_model->get_one($id);
		if (empty($consumible))
		{
			show_error('No se encontró el Consumible', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Consumibles_model->fields, $consumible, TRUE);
		$data['consumibl'] = $consumible;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Consumible';
		$data['title'] = TITLE . ' - Ver Consumible';
		$this->load_template('toner/consumibles/consumibles_abm', $data);
	}
}