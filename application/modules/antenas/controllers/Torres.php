<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Torres extends MY_Controller
{

	/**
	 * Controlador de Torres
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 05/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('antenas/Torres_model');
		$this->load->model('antenas/Proveedores_model');
		$this->load->model('Localidades_model');
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
						array('label' => 'ID', 'data' => 'id', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Sitio', 'data' => 'sitio', 'width' => 12),
						array('label' => 'Exp N°', 'data' => 'expediente_numero', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Exp Ejer', 'data' => 'expediente_ejercicio', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Servicio', 'data' => 'servicio', 'width' => 12),
						array('label' => 'Proveedor', 'data' => 'proveedor', 'width' => 12),
						array('label' => 'Calle', 'data' => 'calle', 'width' => 11),
						array('label' => 'Distrito', 'data' => 'distrito', 'width' => 10),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 10),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'torres_table',
				'source_url' => 'antenas/torres/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_torres_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Torres';
		$data['title'] = TITLE . ' - Torres';
		$this->load_template('antenas/torres/torres_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('an_torres.id, sitio, an_torres.expediente_numero, an_torres.expediente_ejercicio, an_torres.servicio, an_proveedores.nombre as proveedor, an_torres.calle, localidades.nombre as distrito, an_torres.estado')
				->from('an_torres')
				->join('an_proveedores', 'an_proveedores.id = an_torres.proveedor_id', 'left')
				->join('localidades', 'localidades.id = an_torres.distrito_id', 'left')
				->add_column('ver', '<a href="antenas/torres/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="antenas/torres/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="antenas/torres/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('antenas/torres/listar', 'refresh');
		}

		$this->array_estado_control = $array_estado = array('No Declarada' => 'No Declarada', 'Pendiente' => 'Pendiente', 'En trámite' => 'En trámite', 'Habilitado' => 'Habilitado', 'Clandestina' => 'Clandestina');
		$this->array_proveedor_control = $array_proveedor = $this->get_array('Proveedores', 'nombre');
		$this->array_distrito_control = $array_distrito = $this->get_array('Localidades', 'nombre', 'id', array('select' => "localidades.id, localidades.nombre", 'where' => array(array('column' => 'departamento_id', 'value' => 345))));
		$this->set_model_validation_rules($this->Torres_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Torres_model->create(array(
					'servicio' => $this->input->post('servicio'),
					'caracteristicas' => $this->input->post('caracteristicas'),
					'observaciones' => $this->input->post('observaciones'),
					'padron' => $this->input->post('padron'),
					'calle' => $this->input->post('calle'),
					'latitud' => $this->input->post('latitud'),
					'longitud' => $this->input->post('longitud'),
					'zonificacion' => $this->input->post('zonificacion'),
					'entorno' => $this->input->post('entorno'),
					'proveedor_id' => $this->input->post('proveedor'),
					'distrito_id' => $this->input->post('distrito'),
					'estado' => $this->input->post('estado'),
					'expediente_ejercicio' => $this->input->post('expediente_ejercicio'),
					'expediente_numero' => $this->input->post('expediente_numero'),
					'ordenanza_1' => $this->input->post('ordenanza_1'),
					'ordenanza_2' => $this->input->post('ordenanza_2'),
					'ordenanza_3' => $this->input->post('ordenanza_3'),
					'nomenclatura' => $this->input->post('nomenclatura'),
					'sitio' => $this->input->post('sitio')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Torres_model->get_msg());
				redirect('antenas/torres/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Torres_model->get_error())
				{
					$error_msg .= $this->Torres_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Torres_model->fields['estado']['array'] = $array_estado;
		$this->Torres_model->fields['proveedor']['array'] = $array_proveedor;
		$this->Torres_model->fields['distrito']['array'] = $array_distrito;
		$data['fields'] = $this->build_fields($this->Torres_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Torre';
		$data['title'] = TITLE . ' - Agregar Torre';
		$data['js'][] = 'https://maps.google.com/maps/api/js?language=es&amp;key=AIzaSyALTbWXMGGenqreTH6wRAZaDHJj6PkLOqw';
		$data['js'][] = 'js/antenas/map.js';
		$this->load_template('antenas/torres/torres_abm', $data);
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
			redirect("antenas/torres/ver/$id", 'refresh');
		}

		$this->array_estado_control = $array_estado = array('No Declarada' => 'No Declarada', 'Pendiente' => 'Pendiente', 'En trámite' => 'En trámite', 'Habilitado' => 'Habilitado', 'Clandestina' => 'Clandestina');
		$this->array_proveedor_control = $array_proveedor = $this->get_array('Proveedores', 'nombre');
		$this->array_distrito_control = $array_distrito = $this->get_array('Localidades', 'nombre', 'id', array('select' => "localidades.id, localidades.nombre", 'where' => array(array('column' => 'departamento_id', 'value' => 345))));

		$torr = $this->Torres_model->get(array('id' => $id));
		if (empty($torr))
		{
			show_error('No se encontró el Torre', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Torres_model);
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
				$trans_ok &= $this->Torres_model->update(array(
						'id' => $this->input->post('id'),
						'servicio' => $this->input->post('servicio'),
						'caracteristicas' => $this->input->post('caracteristicas'),
						'observaciones' => $this->input->post('observaciones'),
						'padron' => $this->input->post('padron'),
						'calle' => $this->input->post('calle'),
						'latitud' => $this->input->post('latitud'),
						'longitud' => $this->input->post('longitud'),
						'zonificacion' => $this->input->post('zonificacion'),
						'entorno' => $this->input->post('entorno'),
						'proveedor_id' => $this->input->post('proveedor'),
						'distrito_id' => $this->input->post('distrito'),
						'estado' => $this->input->post('estado'),
						'expediente_ejercicio' => $this->input->post('expediente_ejercicio'),
						'expediente_numero' => $this->input->post('expediente_numero'),
						'ordenanza_1' => $this->input->post('ordenanza_1'),
						'ordenanza_2' => $this->input->post('ordenanza_2'),
						'ordenanza_3' => $this->input->post('ordenanza_3'),
						'nomenclatura' => $this->input->post('nomenclatura'),
						'sitio' => $this->input->post('sitio')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Torres_model->get_msg());
					redirect('antenas/torres/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Torres_model->get_error())
					{
						$error_msg .= $this->Torres_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Torres_model->fields['estado']['array'] = $array_estado;
		$this->Torres_model->fields['proveedor']['array'] = $array_proveedor;
		$this->Torres_model->fields['distrito']['array'] = $array_distrito;
		$data['fields'] = $this->build_fields($this->Torres_model->fields, $torr);
		$data['torr'] = $torr;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Torre';
		$data['title'] = TITLE . ' - Editar Torre';
		$data['js'][] = 'https://maps.google.com/maps/api/js?language=es&amp;key=AIzaSyALTbWXMGGenqreTH6wRAZaDHJj6PkLOqw';
		$data['js'][] = 'js/antenas/map.js';
		$this->load_template('antenas/torres/torres_abm', $data);
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
			redirect("antenas/torres/ver/$id", 'refresh');
		}

		$torr = $this->Torres_model->get_one($id);
		if (empty($torr))
		{
			show_error('No se encontró el Torre', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Torres_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Torres_model->get_msg());
				redirect('antenas/torres/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Torres_model->get_error())
				{
					$error_msg .= $this->Torres_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Torres_model->fields, $torr, TRUE);
		$data['torr'] = $torr;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Torre';
		$data['title'] = TITLE . ' - Eliminar Torre';
		$data['js'][] = 'https://maps.google.com/maps/api/js?language=es&amp;key=AIzaSyALTbWXMGGenqreTH6wRAZaDHJj6PkLOqw';
		$data['js'][] = 'js/antenas/map.js';
		$this->load_template('antenas/torres/torres_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$torr = $this->Torres_model->get_one($id);
		if (empty($torr))
		{
			show_error('No se encontró la Torre', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Torres_model->fields, $torr, TRUE);
		$data['torr'] = $torr;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Torre';
		$data['title'] = TITLE . ' - Ver Torre';
		$data['js'][] = 'https://maps.google.com/maps/api/js?language=es&amp;key=AIzaSyALTbWXMGGenqreTH6wRAZaDHJj6PkLOqw';
		$data['js'][] = 'js/antenas/map.js';
		$this->load_template('antenas/torres/torres_abm', $data);
	}
}