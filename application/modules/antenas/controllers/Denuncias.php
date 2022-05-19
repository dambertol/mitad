<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Denuncias extends MY_Controller
{

	/**
	 * Controlador de Denuncias
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 06/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('antenas/Denuncias_model');
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
						array('label' => 'Fecha Denuncia', 'data' => 'fecha_denuncia', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Motivo Denuncia', 'data' => 'motivo_denuncia', 'width' => 20),
						array('label' => 'Fecha Solución', 'data' => 'fecha_solucion', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
						array('label' => 'Solución', 'data' => 'solucion', 'width' => 20),
						array('label' => 'Estado', 'data' => 'estado', 'width' => 10),
						array('label' => 'Torre', 'data' => 'torre', 'width' => 21),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'denuncias_table',
				'source_url' => 'antenas/denuncias/listar_data',
				'reuse_var' => TRUE,
				'initComplete' => "complete_denuncias_table",
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Denuncias';
		$data['title'] = TITLE . ' - Denuncias';
		$this->load_template('antenas/denuncias/denuncias_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('an_denuncias.id, an_denuncias.fecha_denuncia, an_denuncias.motivo_denuncia, an_denuncias.fecha_solucion, an_denuncias.solucion, an_denuncias.estado, an_torres.servicio as torre')
				->from('an_denuncias')
				->join('an_torres', 'an_torres.id = an_denuncias.torre_id', 'left')
				->add_column('ver', '<a href="antenas/denuncias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="antenas/denuncias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="antenas/denuncias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('antenas/denuncias/listar', 'refresh');
		}

		$this->array_torre_control = $array_torre = $this->get_array('Torres', 'servicio');
		$this->set_model_validation_rules($this->Denuncias_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$fecha_denuncia = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_denuncia'));
			$fecha_solucion = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_solucion'));

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Denuncias_model->create(array(
					'fecha_denuncia' => $fecha_denuncia->format('Y-m-d'),
					'motivo_denuncia' => $this->input->post('motivo_denuncia'),
					'fecha_solucion' => $fecha_solucion->format('Y-m-d'),
					'solucion' => $this->input->post('solucion'),
					'estado' => $this->input->post('estado'),
					'torre_id' => $this->input->post('torre')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Denuncias_model->get_msg());
				redirect('antenas/denuncias/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Denuncias_model->get_error())
				{
					$error_msg .= $this->Denuncias_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Denuncias_model->fields['torre']['array'] = $array_torre;
		$data['fields'] = $this->build_fields($this->Denuncias_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Denuncia';
		$data['title'] = TITLE . ' - Agregar Denuncia';
		$this->load_template('antenas/denuncias/denuncias_abm', $data);
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
			redirect("antenas/denuncias/ver/$id", 'refresh');
		}

		$this->array_torre_control = $array_torre = $this->get_array('Torres', 'servicio');
		$denuncia = $this->Denuncias_model->get(array('id' => $id));
		if (empty($denuncia))
		{
			show_error('No se encontró la Denuncia', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Denuncias_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$fecha_denuncia = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_denuncia'));
				$fecha_solucion = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_solucion'));

				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Denuncias_model->update(array(
						'id' => $this->input->post('id'),
						'fecha_denuncia' => $fecha_denuncia->format('Y-m-d'),
						'motivo_denuncia' => $this->input->post('motivo_denuncia'),
						'fecha_solucion' => $fecha_solucion->format('Y-m-d'),
						'solucion' => $this->input->post('solucion'),
						'estado' => $this->input->post('estado'),
						'torre_id' => $this->input->post('torre')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Denuncias_model->get_msg());
					redirect('antenas/denuncias/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Denuncias_model->get_error())
					{
						$error_msg .= $this->Denuncias_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Denuncias_model->fields['torre']['array'] = $array_torre;
		$data['fields'] = $this->build_fields($this->Denuncias_model->fields, $denuncia);
		$data['denuncia'] = $denuncia;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Denuncia';
		$data['title'] = TITLE . ' - Editar Denuncia';
		$this->load_template('antenas/denuncias/denuncias_abm', $data);
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
			redirect("antenas/denuncias/ver/$id", 'refresh');
		}

		$denuncia = $this->Denuncias_model->get_one($id);
		if (empty($denuncia))
		{
			show_error('No se encontró la Denuncia', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Denuncias_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Denuncias_model->get_msg());
				redirect('antenas/denuncias/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Denuncias_model->get_error())
				{
					$error_msg .= $this->Denuncias_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->Denuncias_model->fields, $denuncia, TRUE);
		$data['denuncia'] = $denuncia;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Denuncia';
		$data['title'] = TITLE . ' - Eliminar Denuncia';
		$this->load_template('antenas/denuncias/denuncias_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$denuncia = $this->Denuncias_model->get_one($id);
		if (empty($denuncia))
		{
			show_error('No se encontró la Denuncia', 500, 'Registro no encontrado');
		}

		$data['fields'] = $this->build_fields($this->Denuncias_model->fields, $denuncia, TRUE);
		$data['denuncia'] = $denuncia;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Denuncia';
		$data['title'] = TITLE . ' - Ver Denuncia';
		$this->load_template('antenas/denuncias/denuncias_abm', $data);
	}
}