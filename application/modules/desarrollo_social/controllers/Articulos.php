<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos extends MY_Controller
{

	/**
	 * Controlador de Artículos
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('desarrollo_social/Articulos_model');
		$this->load->model('desarrollo_social/Tipos_unidades_model');
		$this->load->model('desarrollo_social/Tipos_articulos_model');
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
						array('label' => 'Tipo de Artículo', 'data' => 'tipo_articulo', 'width' => 10),
						array('label' => 'Tipo de Unidad', 'data' => 'tipo_unidad', 'width' => 10),
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 20),
						array('label' => 'Marca', 'data' => 'marca', 'width' => 10),
						array('label' => 'Cant. Real', 'data' => 'cantidad_real', 'width' => 6, 'render' => 'numeric', 'class' => 'dt-body-right'),
						array('label' => 'Cant.Minima', 'data' => 'cantidad_minima', 'width' => 6, 'render' => 'numeric', 'class' => 'dt-body-right'),
						array('label' => 'Ubicación', 'data' => 'ubicacion', 'width' => 10),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 13),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'articulos_table',
				'source_url' => 'desarrollo_social/articulos/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => 'complete_articulos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Artículos';
		$data['title'] = TITLE . ' - Artículos';
		$this->load_template('desarrollo_social/articulos/articulos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('ds_articulos.id, ds_articulos.marca, ds_tipos_unidades.descripcion as tipo_unidad, ds_tipos_articulos.descripcion as tipo_articulo, ds_articulos.cantidad_real, ds_articulos.nombre, ds_articulos.ubicacion, ds_articulos.cantidad_minima, ds_articulos.observaciones')
				->from('ds_articulos')
				->join('ds_tipos_unidades', 'ds_tipos_unidades.id = ds_articulos.tipo_unidad_id', 'left')
				->join('ds_tipos_articulos', 'ds_tipos_articulos.id = ds_articulos.tipo_articulo_id', 'left')
				->add_column('ver', '<a href="desarrollo_social/articulos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="desarrollo_social/articulos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="desarrollo_social/articulos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('desarrollo_social/articulos/listar', 'refresh');
		}

		$this->array_tipo_unidad_control = $array_tipo_unidad = $this->get_array('Tipos_unidades');
		$this->array_tipo_articulo_control = $array_tipo_articulo = $this->get_array('Tipos_articulos');
		$this->set_model_validation_rules($this->Articulos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Articulos_model->create(array(
					'marca' => $this->input->post('marca'),
					'tipo_unidad_id' => $this->input->post('tipo_unidad'),
					'tipo_articulo_id' => $this->input->post('tipo_articulo'),
					'nombre' => $this->input->post('nombre'),
					'ubicacion' => $this->input->post('ubicacion'),
					'cantidad_minima' => $this->input->post('cantidad_minima'),
					'observaciones' => $this->input->post('observaciones')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
				redirect('desarrollo_social/articulos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Articulos_model->get_error())
				{
					$error_msg .= $this->Articulos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Articulos_model->fields['tipo_unidad']['array'] = $array_tipo_unidad;
		$this->Articulos_model->fields['tipo_articulo']['array'] = $array_tipo_articulo;
		$data['fields'] = $this->build_fields($this->Articulos_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Artículo';
		$data['title'] = TITLE . ' - Agregar Artículo';
		$this->load_template('desarrollo_social/articulos/articulos_abm', $data);
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
			redirect("desarrollo_social/articulos/ver/$id", 'refresh');
		}

		$this->array_tipo_unidad_control = $array_tipo_unidad = $this->get_array('Tipos_unidades');
		$this->array_tipo_articulo_control = $array_tipo_articulo = $this->get_array('Tipos_articulos');
		$articulo = $this->Articulos_model->get(array('id' => $id));
		if (empty($articulo))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Articulos_model);
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
				$trans_ok &= $this->Articulos_model->update(array(
						'id' => $this->input->post('id'),
						'marca' => $this->input->post('marca'),
						'tipo_unidad_id' => $this->input->post('tipo_unidad'),
						'tipo_articulo_id' => $this->input->post('tipo_articulo'),
						'nombre' => $this->input->post('nombre'),
						'ubicacion' => $this->input->post('ubicacion'),
						'cantidad_minima' => $this->input->post('cantidad_minima'),
						'observaciones' => $this->input->post('observaciones')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
					redirect('desarrollo_social/articulos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Articulos_model->get_error())
					{
						$error_msg .= $this->Articulos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Articulos_model->fields['tipo_unidad']['array'] = $array_tipo_unidad;
		$this->Articulos_model->fields['tipo_articulo']['array'] = $array_tipo_articulo;
		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Artículo';
		$data['title'] = TITLE . ' - Editar Artículo';
		$this->load_template('desarrollo_social/articulos/articulos_abm', $data);
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
			redirect("desarrollo_social/articulos/ver/$id", 'refresh');
		}

		$articulo = $this->Articulos_model->get_one($id);
		if (empty($articulo))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Articulos_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
				redirect('desarrollo_social/articulos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Articulos_model->get_error())
				{
					$error_msg .= $this->Articulos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo, TRUE);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Artículo';
		$data['title'] = TITLE . ' - Eliminar Artículo';
		$this->load_template('desarrollo_social/articulos/articulos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$articulo = $this->Articulos_model->get_one($id);
		if (empty($articulo))
		{
			show_error('No se encontró el Artículo', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo, TRUE);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Artículo';
		$data['title'] = TITLE . ' - Ver Artículo';
		$this->load_template('desarrollo_social/articulos/articulos_abm', $data);
	}
}