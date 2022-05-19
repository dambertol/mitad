<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lugares extends MY_Controller
{

	/**
	 * Controlador de Lugares
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('desarrollo_social/Lugares_model');
		$this->load->model('desarrollo_social/Tipos_lugares_model');
		$this->grupos_permitidos = array('admin', 'desarrollo_social_user', 'desarrollo_social_consulta_general');
		$this->grupos_solo_consulta = array('desarrollo_social_consulta_general');
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
						array('label' => 'Tipo de Lugar', 'data' => 'tipo_lugar', 'width' => 31),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 60),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'lugares_table',
				'source_url' => 'desarrollo_social/lugares/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_lugares_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Lugares';
		$data['title'] = TITLE . ' - Lugares';
		$this->load_template('desarrollo_social/lugares/lugares_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('ds_lugares.id, ds_tipos_lugares.descripcion as tipo_lugar, ds_lugares.descripcion')
				->from('ds_lugares')
				->join('ds_tipos_lugares', 'ds_tipos_lugares.id = ds_lugares.tipo_lugar_id', 'left')
				->add_column('ver', '<a href="desarrollo_social/lugares/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="desarrollo_social/lugares/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="desarrollo_social/lugares/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('desarrollo_social/lugares/listar', 'refresh');
		}

		$this->array_tipo_lugar_control = $array_tipo_lugar = $this->get_array('Tipos_lugares');
		$this->set_model_validation_rules($this->Lugares_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Lugares_model->create(array(
					'tipo_lugar_id' => $this->input->post('tipo_lugar'),
					'descripcion' => $this->input->post('descripcion')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Lugares_model->get_msg());
				redirect('desarrollo_social/lugares/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Lugares_model->get_error())
				{
					$error_msg .= $this->Lugares_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Lugares_model->fields['tipo_lugar']['array'] = $array_tipo_lugar;
		$data['fields'] = $this->build_fields($this->Lugares_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Lugar';
		$data['title'] = TITLE . ' - Agregar Lugar';
		$this->load_template('desarrollo_social/lugares/lugares_abm', $data);
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
			redirect("desarrollo_social/lugares/ver/$id", 'refresh');
		}

		$this->array_tipo_lugar_control = $array_tipo_lugar = $this->get_array('Tipos_lugares');
		$lugar = $this->Lugares_model->get(array('id' => $id));
		if (empty($lugar))
		{
			show_error('No se encontró el Lugar', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Lugares_model);
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
				$trans_ok &= $this->Lugares_model->update(array(
						'id' => $this->input->post('id'),
						'tipo_lugar_id' => $this->input->post('tipo_lugar'),
						'descripcion' => $this->input->post('descripcion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Lugares_model->get_msg());
					redirect('desarrollo_social/lugares/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Lugares_model->get_error())
					{
						$error_msg .= $this->Lugares_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Lugares_model->fields['tipo_lugar']['array'] = $array_tipo_lugar;
		$data['fields'] = $this->build_fields($this->Lugares_model->fields, $lugar);
		$data['lugar'] = $lugar;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Lugar';
		$data['title'] = TITLE . ' - Editar Lugar';
		$this->load_template('desarrollo_social/lugares/lugares_abm', $data);
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
			redirect("desarrollo_social/lugares/ver/$id", 'refresh');
		}

		$lugar = $this->Lugares_model->get_one($id);
		if (empty($lugar))
		{
			show_error('No se encontró el Lugar', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Lugares_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Lugares_model->get_msg());
				redirect('desarrollo_social/lugares/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Lugares_model->get_error())
				{
					$error_msg .= $this->Lugares_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Lugares_model->fields, $lugar, TRUE);
		$data['lugar'] = $lugar;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Lugar';
		$data['title'] = TITLE . ' - Eliminar Lugar';
		$this->load_template('desarrollo_social/lugares/lugares_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$lugar = $this->Lugares_model->get_one($id);
		if (empty($lugar))
		{
			show_error('No se encontró el Lugar', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Lugares_model->fields, $lugar, TRUE);
		$data['lugar'] = $lugar;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Lugar';
		$data['title'] = TITLE . ' - Ver Lugar';
		$this->load_template('desarrollo_social/lugares/lugares_abm', $data);
	}
}