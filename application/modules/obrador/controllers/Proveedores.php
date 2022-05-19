<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores extends MY_Controller
{

	/**
	 * Controlador de Proveedores
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 21/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('obrador/Proveedores_model');
		$this->load->model('obrador/Tipos_proveedores_model');
		$this->load->model('obrador/Situaciones_iva_model');
		$this->load->model('obrador/Ganancias_model');
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
				'columns' => array(
						array('label' => 'Razon Social', 'data' => 'razon_social', 'width' => 20),
						array('label' => 'Tipo de Proveedor', 'data' => 'tipo_proveedor', 'width' => 12),
						array('label' => 'CUIT', 'data' => 'cuit', 'width' => 12, 'class' => 'dt-body-right'),
						array('label' => 'Beneficiario', 'data' => 'beneficiario', 'width' => 18),
						array('label' => 'IVA', 'data' => 'iva', 'width' => 10),
						array('label' => 'Ganancias', 'data' => 'ganancia', 'width' => 10),
						array('label' => 'Fecha Inscripción', 'data' => 'fecha_inscripcion', 'width' => 9, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'proveedores_table',
				'source_url' => 'obrador/proveedores/listar_data',
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
		$this->load_template('obrador/proveedores/proveedores_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('ob_proveedores.id, ob_proveedores.razon_social, ob_tipos_proveedores.descripcion as tipo_proveedor, ob_proveedores.cuit, ob_proveedores.beneficiario, ob_situaciones_iva.descripcion as iva, ob_ganancias.descripcion as ganancia, ob_proveedores.fecha_inscripcion')
				->from('ob_proveedores')
				->join('ob_tipos_proveedores', 'ob_tipos_proveedores.id = ob_proveedores.tipo_proveedor_id', 'left')
				->join('ob_situaciones_iva', 'ob_situaciones_iva.id = ob_proveedores.iva_id', 'left')
				->join('ob_ganancias', 'ob_ganancias.id = ob_proveedores.ganancia_id', 'left')
				->add_column('ver', '<a href="obrador/proveedores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="obrador/proveedores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="obrador/proveedores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('obrador/proveedores/listar', 'refresh');
		}

		$this->array_tipo_proveedor_control = $array_tipo_proveedor = $this->get_array('Tipos_proveedores');
		$this->array_iva_control = $array_iva = $this->get_array('Situaciones_iva');
		$this->array_ganancia_control = $array_ganancia = $this->get_array('Ganancias');
		$this->set_model_validation_rules($this->Proveedores_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Proveedores_model->create(array(
					'tipo_proveedor_id' => $this->input->post('tipo_proveedor'),
					'razon_social' => $this->input->post('razon_social'),
					'cuit' => $this->input->post('cuit'),
					'beneficiario' => $this->input->post('beneficiario'),
					'domicilio' => $this->input->post('domicilio'),
					'localidad' => $this->input->post('localidad'),
					'codigo_postal' => $this->input->post('codigo_postal'),
					'tipo_sociedad' => $this->input->post('tipo_sociedad'),
					'iva_id' => $this->input->post('iva'),
					'ganancia_id' => $this->input->post('ganancia'),
					'fecha_inscripcion' => $this->get_date_sql('fecha_inscripcion'),
					'ingresos_brutos' => $this->input->post('ingresos_brutos'),
					'observaciones' => $this->input->post('observaciones')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Proveedores_model->get_msg());
				redirect('obrador/proveedores/listar', 'refresh');
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
		$this->Proveedores_model->fields['iva']['array'] = $array_iva;
		$this->Proveedores_model->fields['ganancia']['array'] = $array_ganancia;
		$data['fields'] = $this->build_fields($this->Proveedores_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Proveedor';
		$data['title'] = TITLE . ' - Agregar Proveedor';
		$this->load_template('obrador/proveedores/proveedores_abm', $data);
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
			redirect("obrador/proveedores/ver/$id", 'refresh');
		}

		$this->array_tipo_proveedor_control = $array_tipo_proveedor = $this->get_array('Tipos_proveedores');
		$this->array_iva_control = $array_iva = $this->get_array('Situaciones_iva');
		$this->array_ganancia_control = $array_ganancia = $this->get_array('Ganancias');
		$proveedor = $this->Proveedores_model->get(array('id' => $id));
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
						'cuit' => $this->input->post('cuit'),
						'beneficiario' => $this->input->post('beneficiario'),
						'domicilio' => $this->input->post('domicilio'),
						'localidad' => $this->input->post('localidad'),
						'codigo_postal' => $this->input->post('codigo_postal'),
						'tipo_sociedad' => $this->input->post('tipo_sociedad'),
						'iva_id' => $this->input->post('iva'),
						'ganancia_id' => $this->input->post('ganancia'),
						'fecha_inscripcion' => $this->get_date_sql('fecha_inscripcion'),
						'ingresos_brutos' => $this->input->post('ingresos_brutos'),
						'observaciones' => $this->input->post('observaciones')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Proveedores_model->get_msg());
					redirect('obrador/proveedores/listar', 'refresh');
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
		$this->Proveedores_model->fields['iva']['array'] = $array_iva;
		$this->Proveedores_model->fields['ganancia']['array'] = $array_ganancia;
		$data['fields'] = $this->build_fields($this->Proveedores_model->fields, $proveedor);
		$data['proveedor'] = $proveedor;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Proveedor';
		$data['title'] = TITLE . ' - Editar Proveedor';
		$this->load_template('obrador/proveedores/proveedores_abm', $data);
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
			redirect("obrador/proveedores/ver/$id", 'refresh');
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
				redirect('obrador/proveedores/listar', 'refresh');
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
		$this->load_template('obrador/proveedores/proveedores_abm', $data);
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
		$this->load_template('obrador/proveedores/proveedores_abm', $data);
	}
}