<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Beneficiarios extends MY_Controller
{

	/**
	 * Controlador de Beneficiarios
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 08/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('desarrollo_social/Beneficiarios_model');
		$this->load->model('desarrollo_social/Lugares_model');
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
						array('label' => 'DNI', 'data' => 'dni', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 15),
						array('label' => 'Apellido', 'data' => 'apellido', 'width' => 15),
						array('label' => 'Lugar', 'data' => 'lugar', 'width' => 11),
						array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Nro Apros', 'data' => 'nro_apros', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 20),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'beneficiarios_table',
				'source_url' => 'desarrollo_social/beneficiarios/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_beneficiarios_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Beneficiarios';
		$data['title'] = TITLE . ' - Beneficiarios';
		$this->load_template('desarrollo_social/beneficiarios/beneficiarios_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('ds_beneficiarios.id, ds_beneficiarios.dni, ds_beneficiarios.nombre, ds_beneficiarios.apellido, ds_lugares.descripcion as lugar, ds_beneficiarios.telefono, ds_beneficiarios.nro_apros, ds_beneficiarios.observaciones')
				->from('ds_beneficiarios')
				->join('ds_lugares', 'ds_lugares.id = ds_beneficiarios.lugar_id', 'left')
				->add_column('ver', '<a href="desarrollo_social/beneficiarios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="desarrollo_social/beneficiarios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="desarrollo_social/beneficiarios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('desarrollo_social/beneficiarios/listar', 'refresh');
		}

		$this->array_lugar_control = $array_lugar = $this->get_array('Lugares');
		$this->set_model_validation_rules($this->Beneficiarios_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Beneficiarios_model->create(array(
					'dni' => $this->input->post('dni'),
					'nombre' => $this->input->post('nombre'),
					'apellido' => $this->input->post('apellido'),
					'telefono' => $this->input->post('telefono'),
					'domicilio' => $this->input->post('domicilio'),
					'localidad' => $this->input->post('localidad'),
					'lugar_id' => $this->input->post('lugar'),
					'nro_apros' => $this->input->post('nro_apros'),
					'observaciones' => $this->input->post('observaciones')), FALSE);

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Beneficiarios_model->get_msg());
				redirect('desarrollo_social/beneficiarios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Beneficiarios_model->get_error())
				{
					$error_msg .= $this->Beneficiarios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Beneficiarios_model->fields['lugar']['array'] = $array_lugar;
		$data['fields'] = $this->build_fields($this->Beneficiarios_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Beneficiario';
		$data['title'] = TITLE . ' - Agregar Beneficiario';
		$data['js'] = 'js/desarrollo_social/base.js';
		$this->load_template('desarrollo_social/beneficiarios/beneficiarios_abm', $data);
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
			redirect("desarrollo_social/beneficiarios/ver/$id", 'refresh');
		}

		$this->array_lugar_control = $array_lugar = $this->get_array('Lugares');
		$beneficiario = $this->Beneficiarios_model->get_one($id);
		if (empty($beneficiario))
		{
			show_error('No se encontró el Beneficiario', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Beneficiarios_model);
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
				$trans_ok &= $this->Beneficiarios_model->update(array(
						'id' => $this->input->post('id'),
						'dni' => $this->input->post('dni'),
						'nombre' => $this->input->post('nombre'),
						'apellido' => $this->input->post('apellido'),
						'telefono' => $this->input->post('telefono'),
						'domicilio' => $this->input->post('domicilio'),
						'localidad' => $this->input->post('localidad'),
						'lugar_id' => $this->input->post('lugar'),
						'nro_apros' => $this->input->post('nro_apros'),
						'observaciones' => $this->input->post('observaciones')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Beneficiarios_model->get_msg());
					redirect('desarrollo_social/beneficiarios/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Beneficiarios_model->get_error())
					{
						$error_msg .= $this->Beneficiarios_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Beneficiarios_model->fields['lugar']['array'] = $array_lugar;
		$data['fields'] = $this->build_fields($this->Beneficiarios_model->fields, $beneficiario);
		$data['beneficiario'] = $beneficiario;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Beneficiario';
		$data['title'] = TITLE . ' - Editar Beneficiario';
		$data['js'] = 'js/desarrollo_social/base.js';
		$this->load_template('desarrollo_social/beneficiarios/beneficiarios_abm', $data);
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
			redirect("desarrollo_social/beneficiarios/ver/$id", 'refresh');
		}

		$beneficiario = $this->Beneficiarios_model->get_one($id);
		if (empty($beneficiario))
		{
			show_error('No se encontró el Beneficiario', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Beneficiarios_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Beneficiarios_model->get_msg());
				redirect('desarrollo_social/beneficiarios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Beneficiarios_model->get_error())
				{
					$error_msg .= $this->Beneficiarios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Beneficiarios_model->fields, $beneficiario, TRUE);
		$data['beneficiario'] = $beneficiario;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Beneficiario';
		$data['title'] = TITLE . ' - Eliminar Beneficiario';
		$data['js'] = 'js/desarrollo_social/base.js';
		$this->load_template('desarrollo_social/beneficiarios/beneficiarios_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$beneficiario = $this->Beneficiarios_model->get_one($id);
		if (empty($beneficiario))
		{
			show_error('No se encontró el Beneficiario', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Beneficiarios_model->fields, $beneficiario, TRUE);
		$data['beneficiario'] = $beneficiario;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Beneficiario';
		$data['title'] = TITLE . ' - Ver Beneficiario';
		$data['js'] = 'js/desarrollo_social/base.js';
		$this->load_template('desarrollo_social/beneficiarios/beneficiarios_abm', $data);
	}
}