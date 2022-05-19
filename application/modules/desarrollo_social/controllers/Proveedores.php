<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends MY_Controller
{

	/**
	 * Controlador de Proveedores
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 08/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('desarrollo_social/Proveedores_model');
		$this->load->model('desarrollo_social/Tipos_proveedores_model');
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
						array('label' => 'ID', 'data' => 'id', 'width' => 6, 'class' => 'dt-body-right'),
						array('label' => 'Razon Social', 'data' => 'razon_social', 'width' => 34),
						array('label' => 'Tipo de Proveedor', 'data' => 'tipo_proveedor', 'width' => 10),
						array('label' => 'Fecha Inscripción', 'data' => 'fecha_inscripcion', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 31),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'proveedores_table',
				'source_url' => 'desarrollo_social/proveedores/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_proveedores_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Proveedores';
		$data['title'] = TITLE . ' - Proveedores';
		$this->load_template('desarrollo_social/proveedores/proveedores_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('ds_proveedores.id, ds_proveedores.razon_social, ds_tipos_proveedores.descripcion as tipo_proveedor, ds_proveedores.fecha_inscripcion, ds_proveedores.observaciones')
				->from('ds_proveedores')
				->join('ds_tipos_proveedores', 'ds_tipos_proveedores.id = ds_proveedores.tipo_proveedor_id', 'left')
				->add_column('ver', '<a href="desarrollo_social/proveedores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="desarrollo_social/proveedores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="desarrollo_social/proveedores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('desarrollo_social/proveedores/listar', 'refresh');
		}

		$this->array_tipo_proveedor_control = $array_tipo_proveedor = $this->get_array('Tipos_proveedores');

		$this->set_model_validation_rules($this->Proveedores_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Proveedores_model->create(array(
					'tipo_proveedor_id' => $this->input->post('tipo_proveedor'),
					'razon_social' => $this->input->post('razon_social'),
					'domicilio' => $this->input->post('domicilio'),
					'localidad' => $this->input->post('localidad'),
					'fecha_inscripcion' => $this->get_date_sql('fecha_inscripcion'),
					'cuit' => $this->input->post('cuit'),
					'telefono' => $this->input->post('telefono'),
					'observaciones' => $this->input->post('observaciones')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Proveedores_model->get_msg());
				redirect('desarrollo_social/proveedores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Proveedores_model->get_error())
				{
					$error_msg .= $this->Proveedores_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Proveedores_model->fields['tipo_proveedor']['array'] = $array_tipo_proveedor;
		$data['fields'] = $this->build_fields($this->Proveedores_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Proveedor';
		$data['title'] = TITLE . ' - Agregar Proveedor';
		$this->load_template('desarrollo_social/proveedores/proveedores_abm', $data);
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
			redirect("desarrollo_social/proveedores/ver/$id", 'refresh');
		}

		$this->array_tipo_proveedor_control = $array_tipo_proveedor = $this->get_array('Tipos_proveedores');
		$proveedor = $this->Proveedores_model->get_one($id);
		if (empty($proveedor))
		{
			show_error('No se encontró el Proveedor', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Proveedores_model);
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

				$trans_ok &= $this->Proveedores_model->update(array(
						'id' => $this->input->post('id'),
						'tipo_proveedor_id' => $this->input->post('tipo_proveedor'),
						'razon_social' => $this->input->post('razon_social'),
						'domicilio' => $this->input->post('domicilio'),
						'localidad' => $this->input->post('localidad'),
						'fecha_inscripcion' => $this->get_date_sql('fecha_inscripcion'),
						'cuit' => $this->input->post('cuit'),
						'telefono' => $this->input->post('telefono'),
						'observaciones' => $this->input->post('observaciones')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Proveedores_model->get_msg());
					redirect('desarrollo_social/proveedores/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Proveedores_model->get_error())
					{
						$error_msg .= $this->Proveedores_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Proveedores_model->fields['tipo_proveedor']['array'] = $array_tipo_proveedor;
		$data['fields'] = $this->build_fields($this->Proveedores_model->fields, $proveedor);
		$data['proveedor'] = $proveedor;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Proveedor';
		$data['title'] = TITLE . ' - Editar Proveedor';
		$this->load_template('desarrollo_social/proveedores/proveedores_abm', $data);
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
			redirect("desarrollo_social/proveedores/ver/$id", 'refresh');
		}

		$proveedor = $this->Proveedores_model->get_one($id);
		if (empty($proveedor))
		{
			show_error('No se encontró el Proveedor', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Proveedores_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Proveedores_model->get_msg());
				redirect('desarrollo_social/proveedores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Proveedores_model->get_error())
				{
					$error_msg .= $this->Proveedores_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Proveedores_model->fields, $proveedor, TRUE);
		$data['proveedor'] = $proveedor;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Proveedor';
		$data['title'] = TITLE . ' - Eliminar Proveedor';
		$this->load_template('desarrollo_social/proveedores/proveedores_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$proveedor = $this->Proveedores_model->get_one($id);
		if (empty($proveedor))
		{
			show_error('No se encontró el Proveedor', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Proveedores_model->fields, $proveedor, TRUE);
		$data['proveedor'] = $proveedor;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Proveedor';
		$data['title'] = TITLE . ' - Ver Proveedor';
		$this->load_template('desarrollo_social/proveedores/proveedores_abm', $data);
	}
}