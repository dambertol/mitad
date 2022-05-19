<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Domicilios extends MY_Controller
{

	/**
	 * Controlador de Domicilios
	 * Autor: Leandro
	 * Creado: 23/05/2018
	 * Modificado: 16/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Domicilios_model');
		$this->load->model('Localidades_model');
		$this->grupos_permitidos = array('admin', 'consulta_general');
		$this->grupos_solo_consulta = array('consulta_general');
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
						array('label' => 'Calle', 'data' => 'calle', 'width' => 12),
						array('label' => 'Barrio', 'data' => 'barrio', 'width' => 12),
						array('label' => 'Altura', 'data' => 'altura', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Piso', 'data' => 'piso', 'width' => 8),
						array('label' => 'Dpto', 'data' => 'dpto', 'width' => 8),
						array('label' => 'Manzana', 'data' => 'manzana', 'width' => 8),
						array('label' => 'Casa', 'data' => 'casa', 'width' => 8),
						array('label' => 'Localidad', 'data' => 'localidad', 'width' => 9),
						array('label' => 'Departamento', 'data' => 'departamento', 'width' => 9),
						array('label' => 'Provincia', 'data' => 'provincia', 'width' => 9),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'domicilios_table',
				'source_url' => 'domicilios/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_domicilios_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Domicilios';
		$data['title'] = TITLE . ' - Domicilios';
		$this->load_template('domicilios/domicilios_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('domicilios.id, domicilios.calle, domicilios.barrio, domicilios.altura, domicilios.piso, domicilios.dpto, domicilios.manzana, domicilios.casa, localidades.nombre as localidad, departamentos.nombre as departamento, provincias.nombre as provincia')
				->unset_column('id')
				->from('domicilios')
				->join('localidades', 'localidades.id = domicilios.localidad_id', 'left')
				->join('departamentos', 'departamentos.id = localidades.departamento_id', 'left')
				->join('provincias', 'provincias.id = departamentos.provincia_id', 'left')
				->add_column('ver', '<a href="domicilios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="domicilios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="domicilios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('domicilios/listar', 'refresh');
		}

		$this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'))));
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
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Domicilios_model->get_msg());
				redirect('domicilios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Domicilios_model->get_error())
				{
					$error_msg .= $this->Domicilios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
		$data['fields'] = $this->build_fields($this->Domicilios_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Domicilio';
		$data['title'] = TITLE . ' - Agregar Domicilio';
		$this->load_template('domicilios/domicilios_abm', $data);
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
			redirect("domicilios/ver/$id", 'refresh');
		}

		$this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'))));
		$domicilio = $this->Domicilios_model->get(array('id' => $id));
		if (empty($domicilio))
		{
			show_error('No se encontró el Domicilio', 500, 'Registro no encontrado');
		}

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
						'id' => $this->input->post('id'),
						'calle' => $this->input->post('calle'),
						'barrio' => $this->input->post('barrio'),
						'altura' => $this->input->post('altura'),
						'piso' => $this->input->post('piso'),
						'dpto' => $this->input->post('dpto'),
						'manzana' => $this->input->post('manzana'),
						'casa' => $this->input->post('casa'),
						'localidad_id' => $this->input->post('localidad')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Domicilios_model->get_msg());
					redirect('domicilios/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Domicilios_model->get_error())
					{
						$error_msg .= $this->Domicilios_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
		$data['fields'] = $this->build_fields($this->Domicilios_model->fields, $domicilio);
		$data['domicilio'] = $domicilio;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Domicilio';
		$data['title'] = TITLE . ' - Editar Domicilio';
		$this->load_template('domicilios/domicilios_abm', $data);
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
			redirect("domicilios/ver/$id", 'refresh');
		}

		$domicilio = $this->Domicilios_model->get_one($id);
		if (empty($domicilio))
		{
			show_error('No se encontró el Domicilio', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Domicilios_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Domicilios_model->get_msg());
				redirect('domicilios/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Domicilios_model->get_error())
				{
					$error_msg .= $this->Domicilios_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Domicilios_model->fields, $domicilio, TRUE);
		$data['domicilio'] = $domicilio;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Domicilio';
		$data['title'] = TITLE . ' - Eliminar Domicilio';
		$this->load_template('domicilios/domicilios_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$domicilio = $this->Domicilios_model->get_one($id);
		if (empty($domicilio))
		{
			show_error('No se encontró el Domicilio', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Domicilios_model->fields, $domicilio, TRUE);
		$data['domicilio'] = $domicilio;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Domicilio';
		$data['title'] = TITLE . ' - Ver Domicilio';
		$this->load_template('domicilios/domicilios_abm', $data);
	}
}