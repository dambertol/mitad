<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Antenas extends MY_Controller
{

	/**
	 * Controlador de Antenas
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 05/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('antenas/Antenas_model');
		$this->load->model('antenas/Proveedores_model');
		$this->load->model('antenas/Torres_model');
		$this->grupos_permitidos = array('admin', 'antenas_admin', 'antenas_consulta_general');
		$this->grupos_solo_consulta = array('antenas_consulta_general');
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
						array('label' => 'ID', 'data' => 'id', 'width' => 7, 'class' => 'dt-body-right'),
						array('label' => 'Torre', 'data' => 'torre', 'width' => 18),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 24),
						array('label' => 'Proveedor', 'data' => 'proveedor', 'width' => 18),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 24),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'antenas_table',
				'source_url' => 'antenas/antenas/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_antenas_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Antenas';
		$data['title'] = TITLE . ' - Antenas';
		$this->load_template('antenas/antenas/antenas_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('an_antenas.id, an_torres.servicio as torre, an_antenas.descripcion, an_proveedores.nombre as proveedor, an_antenas.observaciones')
				->from('an_antenas')
				->join('an_torres', 'an_torres.id = an_antenas.torre_id', 'left')
				->join('an_proveedores', 'an_proveedores.id = an_antenas.proveedor_id', 'left')
				->add_column('ver', '<a href="antenas/antenas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="antenas/antenas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="antenas/antenas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('antenas/antenas/listar', 'refresh');
		}

		$this->array_proveedor_control = $array_proveedor = $this->get_array('Proveedores', 'nombre');
		$this->array_torre_control = $array_torre = $this->get_array('Torres', 'servicio');
		$this->set_model_validation_rules($this->Antenas_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Antenas_model->create(array(
					'descripcion' => $this->input->post('descripcion'),
					'proveedor_id' => $this->input->post('proveedor'),
					'observaciones' => $this->input->post('observaciones'),
					'torre_id' => $this->input->post('torre')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Antenas_model->get_msg());
				redirect('antenas/antenas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Antenas_model->get_error())
				{
					$error_msg .= $this->Antenas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Antenas_model->fields['proveedor']['array'] = $array_proveedor;
		$this->Antenas_model->fields['torre']['array'] = $array_torre;
		$data['fields'] = $this->build_fields($this->Antenas_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Antena';
		$data['title'] = TITLE . ' - Agregar Antena';
		$this->load_template('antenas/antenas/antenas_abm', $data);
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
			redirect("antenas/antenas/ver/$id", 'refresh');
		}

		$this->array_proveedor_control = $array_proveedor = $this->get_array('Proveedores', 'nombre');
		$this->array_torre_control = $array_torre = $this->get_array('Torres', 'servicio');
		$antena = $this->Antenas_model->get(array('id' => $id));
		if (empty($antena))
		{
			show_error('No se encontró la Antena', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Antenas_model);
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
				$trans_ok &= $this->Antenas_model->update(array(
						'id' => $this->input->post('id'),
						'descripcion' => $this->input->post('descripcion'),
						'proveedor_id' => $this->input->post('proveedor'),
						'observaciones' => $this->input->post('observaciones'),
						'torre_id' => $this->input->post('torre')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Antenas_model->get_msg());
					redirect('antenas/antenas/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Antenas_model->get_error())
					{
						$error_msg .= $this->Antenas_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Antenas_model->fields['proveedor']['array'] = $array_proveedor;
		$this->Antenas_model->fields['torre']['array'] = $array_torre;
		$data['fields'] = $this->build_fields($this->Antenas_model->fields, $antena);
		$data['antena'] = $antena;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Antena';
		$data['title'] = TITLE . ' - Editar Antena';
		$this->load_template('antenas/antenas/antenas_abm', $data);
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
			redirect("antenas/antenas/ver/$id", 'refresh');
		}

		$antena = $this->Antenas_model->get_one($id);
		if (empty($antena))
		{
			show_error('No se encontró la Antena', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Antenas_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Antenas_model->get_msg());
				redirect('antenas/antenas/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Antenas_model->get_error())
				{
					$error_msg .= $this->Antenas_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Antenas_model->fields, $antena, TRUE);
		$data['antena'] = $antena;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Antena';
		$data['title'] = TITLE . ' - Eliminar Antena';
		$this->load_template('antenas/antenas/antenas_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$antena = $this->Antenas_model->get_one($id);
		if (empty($antena))
		{
			show_error('No se encontró la Antena', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Antenas_model->fields, $antena, TRUE);
		$data['antena'] = $antena;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Antena';
		$data['title'] = TITLE . ' - Ver Antena';
		$this->load_template('antenas/antenas/antenas_abm', $data);
	}
}