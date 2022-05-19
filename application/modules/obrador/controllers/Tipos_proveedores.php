<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_proveedores extends MY_Controller
{

	/**
	 * Controlador de Tipos de Proveedores
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 21/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('obrador/Tipos_proveedores_model');
		$this->grupos_permitidos = array('admin', 'obrador_user', 'obrador_consulta_general');
		$this->grupos_solo_consulta = array('obrador_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tableData = array(
				'columns' => array(//@todo arreglar anchos de columnas
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 91),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'tipos_proveedores_table',
				'source_url' => 'obrador/tipos_proveedores/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_tipos_proveedores_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Tipos de Proveedores';
		$data['title'] = TITLE . ' - Tipos de Proveedores';
		$this->load_template('obrador/tipos_proveedores/tipos_proveedores_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('id, descripcion')
				->from('ob_tipos_proveedores')
				->add_column('ver', '<a href="obrador/tipos_proveedores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="obrador/tipos_proveedores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="obrador/tipos_proveedores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('obrador/tipos_proveedores/listar', 'refresh');
		}

		$this->set_model_validation_rules($this->Tipos_proveedores_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Tipos_proveedores_model->create(array(
					'descripcion' => $this->input->post('descripcion')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Tipos_proveedores_model->get_msg());
				redirect('obrador/tipos_proveedores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Tipos_proveedores_model->get_error())
				{
					$error_msg .= $this->Tipos_proveedores_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Tipos_proveedores_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Tipo de Proveedor';
		$data['title'] = TITLE . ' - Agregar Tipo de Proveedor';
		$this->load_template('obrador/tipos_proveedores/tipos_proveedores_abm', $data);
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
			redirect("obrador/tipos_proveedores/ver/$id", 'refresh');
		}

		$tipos_proveedor = $this->Tipos_proveedores_model->get(array('id' => $id));
		if (empty($tipos_proveedor))
		{
			show_error('No se encontró el Tipo de Proveedor', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Tipos_proveedores_model);
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
				$trans_ok &= $this->Tipos_proveedores_model->update(array(
						'id' => $this->input->post('id'),
						'descripcion' => $this->input->post('descripcion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Tipos_proveedores_model->get_msg());
					redirect('obrador/tipos_proveedores/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Tipos_proveedores_model->get_error())
					{
						$error_msg .= $this->Tipos_proveedores_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Tipos_proveedores_model->fields, $tipos_proveedor);
		$data['tipos_proveedor'] = $tipos_proveedor;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Tipo de Proveedor';
		$data['title'] = TITLE . ' - Editar Tipo de Proveedor';
		$this->load_template('obrador/tipos_proveedores/tipos_proveedores_abm', $data);
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
			redirect("obrador/tipos_proveedores/ver/$id", 'refresh');
		}

		$tipos_proveedor = $this->Tipos_proveedores_model->get_one($id);
		if (empty($tipos_proveedor))
		{
			show_error('No se encontró el Tipo de Proveedor', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Tipos_proveedores_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Tipos_proveedores_model->get_msg());
				redirect('obrador/tipos_proveedores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Tipos_proveedores_model->get_error())
				{
					$error_msg .= $this->Tipos_proveedores_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Tipos_proveedores_model->fields, $tipos_proveedor, TRUE);
		$data['tipos_proveedor'] = $tipos_proveedor;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Tipo de Proveedor';
		$data['title'] = TITLE . ' - Eliminar Tipo de Proveedor';
		$this->load_template('obrador/tipos_proveedores/tipos_proveedores_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tipos_proveedor = $this->Tipos_proveedores_model->get_one($id);
		if (empty($tipos_proveedor))
		{
			show_error('No se encontró el Tipo de Proveedor', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Tipos_proveedores_model->fields, $tipos_proveedor, TRUE);
		$data['tipos_proveedor'] = $tipos_proveedor;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Tipo de Proveedor';
		$data['title'] = TITLE . ' - Ver Tipo de Proveedor';
		$this->load_template('obrador/tipos_proveedores/tipos_proveedores_abm', $data);
	}
}