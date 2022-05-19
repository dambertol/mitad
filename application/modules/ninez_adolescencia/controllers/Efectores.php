<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Efectores extends MY_Controller
{

	/**
	 * Controlador de Efectores
	 * Autor: Leandro
	 * Creado: 09/09/2019
	 * Modificado: 30/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('ninez_adolescencia/Efectores_model');
		$this->load->model('Domicilios_model');
		$this->load->model('Localidades_model');
		$this->grupos_permitidos = array('admin', 'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general');
		$this->grupos_solo_consulta = array('ninez_adolescencia_consulta_general');
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
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 31),
						array('label' => 'Contacto', 'data' => 'contacto', 'width' => 12),
						array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 12),
						array('label' => 'Celular', 'data' => 'celular', 'width' => 12),
						array('label' => 'Email', 'data' => 'email', 'width' => 24),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'efectores_table',
				'source_url' => 'ninez_adolescencia/efectores/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_efectores_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Efectores';
		$data['title'] = TITLE . ' - Efectores';
		$this->load_template('ninez_adolescencia/efectores/efectores_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('id, nombre, contacto, telefono, celular, email')
				->from('na_efectores')
				->add_column('ver', '<a href="ninez_adolescencia/efectores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="ninez_adolescencia/efectores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="ninez_adolescencia/efectores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('ninez_adolescencia/efectores/listar', 'refresh');
		}

		$this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

		$this->set_model_validation_rules($this->Efectores_model);
		$this->set_model_validation_rules($this->Domicilios_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Domicilios_model->create(array(
					'calle' => $this->input->post('calle'),
					'barrio' => $this->input->post('barrio'),
					'altura' => $this->input->post('altura'),
					'piso' => $this->input->post('piso'),
					'dpto' => $this->input->post('dpto'),
					'manzana' => $this->input->post('manzana'),
					'casa' => $this->input->post('casa'),
					'localidad_id' => $this->input->post('localidad')), FALSE);

			$domicilio_id = $this->Domicilios_model->get_row_id();

			$trans_ok &= $this->Efectores_model->create(array(
					'nombre' => $this->input->post('nombre'),
					'contacto' => $this->input->post('contacto'),
					'telefono' => $this->input->post('telefono'),
					'celular' => $this->input->post('celular'),
					'email' => $this->input->post('email'),
					'domicilio_id' => $domicilio_id), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Efectores_model->get_msg());
				redirect('ninez_adolescencia/efectores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Efectores_model->get_error())
				{
					$error_msg .= $this->Efectores_model->get_error();
				}
				if ($this->Domicilios_model->get_error())
				{
					$error_msg .= $this->Domicilios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Efectores_model->fields);
		$this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
		$data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Efector';
		$data['title'] = TITLE . ' - Agregar Efector';
		$this->load_template('ninez_adolescencia/efectores/efectores_abm', $data);
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
			redirect("ninez_adolescencia/efectores/ver/$id", 'refresh');
		}

		$this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

		$efector = $this->Efectores_model->get_one($id);
		if (empty($efector))
		{
			show_error('No se encontró el Efector', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Efectores_model);
		$this->set_model_validation_rules($this->Domicilios_model);
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
				$trans_ok &= $this->Domicilios_model->update(array(
						'id' => $efector->domicilio_id,
						'calle' => $this->input->post('calle'),
						'barrio' => $this->input->post('barrio'),
						'altura' => $this->input->post('altura'),
						'piso' => $this->input->post('piso'),
						'dpto' => $this->input->post('dpto'),
						'manzana' => $this->input->post('manzana'),
						'casa' => $this->input->post('casa'),
						'localidad_id' => $this->input->post('localidad')), FALSE);

				$trans_ok &= $this->Efectores_model->update(array(
						'id' => $this->input->post('id'),
						'nombre' => $this->input->post('nombre'),
						'contacto' => $this->input->post('contacto'),
						'telefono' => $this->input->post('telefono'),
						'celular' => $this->input->post('celular'),
						'email' => $this->input->post('email'),
						'domicilio_id' => $efector->domicilio_id), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Efectores_model->get_msg());
					redirect('ninez_adolescencia/efectores/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Efectores_model->get_error())
					{
						$error_msg .= $this->Efectores_model->get_error();
					}
					if ($this->Domicilios_model->get_error())
					{
						$error_msg .= $this->Domicilios_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Efectores_model->fields, $efector);
		$this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
		$data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $efector);
		$data['efector'] = $efector;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Efector';
		$data['title'] = TITLE . ' - Editar Efector';
		$this->load_template('ninez_adolescencia/efectores/efectores_abm', $data);
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
			redirect("ninez_adolescencia/efectores/ver/$id", 'refresh');
		}

		$efector = $this->Efectores_model->get_one($id);
		if (empty($efector))
		{
			show_error('No se encontró el Efector', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Efectores_model->delete(array('id' => $this->input->post('id')), FALSE);
			$trans_ok &= $this->Domicilios_model->delete(array('id' => $efector->domicilio_id), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Efectores_model->get_msg());
				redirect('ninez_adolescencia/efectores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Efectores_model->get_error())
				{
					$error_msg .= $this->Efectores_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Efectores_model->fields, $efector, TRUE);
		$data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $efector, TRUE);
		$data['efector'] = $efector;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Efector';
		$data['title'] = TITLE . ' - Eliminar Efector';
		$this->load_template('ninez_adolescencia/efectores/efectores_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$efector = $this->Efectores_model->get_one($id);
		if (empty($efector))
		{
			show_error('No se encontró el Efector', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Efectores_model->fields, $efector, TRUE);
		$data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $efector, TRUE);
		$data['efector'] = $efector;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Efector';
		$data['title'] = TITLE . ' - Ver Efector';
		$this->load_template('ninez_adolescencia/efectores/efectores_abm', $data);
	}
}