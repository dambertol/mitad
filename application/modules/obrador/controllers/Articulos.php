<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos extends MY_Controller
{

	/**
	 * Controlador de Artículos
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 21/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('obrador/Articulos_model');
		$this->load->model('obrador/Tipos_unidades_model');
		$this->load->model('obrador/Tipos_articulos_model');
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
						array('label' => 'ID', 'data' => 'id', 'width' => 6, 'class' => 'dt-body-right'),
						array('label' => 'Tipo Artículo', 'data' => 'tipo_articulo', 'width' => 10),
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Nombre', 'data' => 'nombre', 'width' => 15),
						array('label' => 'Marca', 'data' => 'marca', 'width' => 10),
						array('label' => 'Característica', 'data' => 'caracteristica', 'width' => 10),
						array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 10),
						array('label' => 'Destino', 'data' => 'destino', 'width' => 10),
						array('label' => 'Cantidad', 'data' => 'cant_real', 'width' => 6, 'class' => 'dt-body-right'),
						array('label' => 'C. Mínima', 'data' => 'cant_minima', 'width' => 6, 'class' => 'dt-body-right'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'articulos_table',
				'source_url' => 'obrador/articulos/listar_data',
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
		$this->load_template('obrador/articulos/articulos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('ob_articulos.id, ob_tipos_articulos.descripcion as tipo_articulo, ob_articulos.fecha, ob_articulos.nombre, ob_articulos.marca, ob_articulos.caracteristica, ob_articulos.descripcion, ob_articulos.destino, ob_articulos.cant_real, ob_articulos.cant_minima')
				->from('ob_articulos')
				->join('ob_tipos_articulos', 'ob_tipos_articulos.id = ob_articulos.tipo_articulo_id', 'left')
				->add_column('ver', '<a href="obrador/articulos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="obrador/articulos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="obrador/articulos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('obrador/articulos/listar', 'refresh');
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
					'descripcion' => $this->input->post('descripcion'),
					'tipo_unidad_id' => $this->input->post('tipo_unidad'),
					'tipo_articulo_id' => $this->input->post('tipo_articulo'),
					'medida' => $this->input->post('medida'),
					'modelo' => $this->input->post('modelo'),
					'destino' => $this->input->post('destino'),
					'nombre' => $this->input->post('nombre'),
					'caracteristica' => $this->input->post('caracteristica'),
					'estado' => $this->input->post('estado'),
					'ubicacion' => $this->input->post('ubicacion'),
					'medida_alto' => $this->input->post('medida_alto'),
					'medida_frente' => $this->input->post('medida_frente'),
					'medida_costado' => $this->input->post('medida_costado'),
					'fecha' => $this->get_date_sql('fecha'),
					'cant_minima' => $this->input->post('cant_minima'),
					'valor' => $this->input->post('valor')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
				redirect('obrador/articulos/listar', 'refresh');
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
		$this->load_template('obrador/articulos/articulos_abm', $data);
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
			redirect("obrador/articulos/ver/$id", 'refresh');
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
						'descripcion' => $this->input->post('descripcion'),
						'tipo_unidad_id' => $this->input->post('tipo_unidad'),
						'tipo_articulo_id' => $this->input->post('tipo_articulo'),
						'medida' => $this->input->post('medida'),
						'modelo' => $this->input->post('modelo'),
						'destino' => $this->input->post('destino'),
						'nombre' => $this->input->post('nombre'),
						'caracteristica' => $this->input->post('caracteristica'),
						'estado' => $this->input->post('estado'),
						'ubicacion' => $this->input->post('ubicacion'),
						'medida_alto' => $this->input->post('medida_alto'),
						'medida_frente' => $this->input->post('medida_frente'),
						'medida_costado' => $this->input->post('medida_costado'),
						'fecha' => $this->get_date_sql('fecha'),
						'cant_minima' => $this->input->post('cant_minima'),
						'valor' => $this->input->post('valor')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Articulos_model->get_msg());
					redirect('obrador/articulos/listar', 'refresh');
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
		$this->load_template('obrador/articulos/articulos_abm', $data);
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
			redirect("obrador/articulos/ver/$id", 'refresh');
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
				redirect('obrador/articulos/listar', 'refresh');
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
		$this->load_template('obrador/articulos/articulos_abm', $data);
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

		$this->Articulos_model->fields['cant_real'] = array('label' => 'Cant Real');
		$data['fields'] = $this->build_fields($this->Articulos_model->fields, $articulo, TRUE);
		$data['articulo'] = $articulo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Artículo';
		$data['title'] = TITLE . ' - Ver Artículo';
		$this->load_template('obrador/articulos/articulos_abm', $data);
	}
}