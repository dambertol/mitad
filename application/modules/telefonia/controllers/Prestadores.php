<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Prestadores extends MY_Controller
{

	/**
	 * Controlador de Prestadores
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 02/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('telefonia/Prestadores_model');
		$this->grupos_permitidos = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
		$this->grupos_solo_consulta = array('telefonia_consulta_general');	
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
				array('label' => 'Nombre', 'data' => 'nombre', 'width' => 99),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
			'table_id' => 'prestadores_table',
			'source_url' => 'telefonia/prestadores/listar_data',
			'reuse_var' => TRUE,
			'initComplete' => 'complete_prestadores_table',
			'footer' => TRUE,
			'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Prestadores';
		$data['title'] = TITLE . ' - Prestadores';
		$this->load_template('telefonia/prestadores/prestadores_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}	
		
		$this->datatables
			->select('id, nombre')
			->from('tm_prestadores')
			->add_column('ver', '<a href="telefonia/prestadores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
			->add_column('editar', '<a href="telefonia/prestadores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
			->add_column('eliminar', '<a href="telefonia/prestadores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
			redirect('telefonia/prestadores/listar', 'refresh');
		}
		
		$this->set_model_validation_rules($this->Prestadores_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Prestadores_model->create(array(
				'nombre' => $this->input->post('nombre')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Prestadores_model->get_msg());
				redirect('telefonia/prestadores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Prestadores_model->get_error())
				{
					$error_msg .= $this->Prestadores_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Prestadores_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Prestador';
		$data['title'] = TITLE . ' - Agregar Prestador';
		$this->load_template('telefonia/prestadores/prestadores_abm', $data);
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
			redirect("telefonia/prestadores/ver/$id", 'refresh');
		}
		
		$prestador = $this->Prestadores_model->get(array('id' => $id));
		if (empty($prestador))
		{
			show_error('No se encontró el Prestador', 500, 'Registro no encontrado');
		}
		
		$this->set_model_validation_rules($this->Prestadores_model);
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
				$trans_ok &= $this->Prestadores_model->update(array(
					'id' => $this->input->post('id'),
					'nombre' => $this->input->post('nombre')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Prestadores_model->get_msg());
					redirect('telefonia/prestadores/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Prestadores_model->get_error())
					{
						$error_msg .= $this->Prestadores_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		
		$data['fields'] = $this->build_fields($this->Prestadores_model->fields, $prestador);
		$data['prestador'] = $prestador;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Prestador';
		$data['title'] = TITLE . ' - Editar Prestador';
		$this->load_template('telefonia/prestadores/prestadores_abm', $data);
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
			redirect("telefonia/prestadores/ver/$id", 'refresh');
		}

		$prestador = $this->Prestadores_model->get_one($id);
		if (empty($prestador))
		{
			show_error('No se encontró el Prestador', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Prestadores_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Prestadores_model->get_msg());
				redirect('telefonia/prestadores/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Prestadores_model->get_error())
				{
					$error_msg .= $this->Prestadores_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($this->Prestadores_model->fields, $prestador, TRUE);
		$data['prestador'] = $prestador;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Prestador';
		$data['title'] = TITLE . ' - Eliminar Prestador';
		$this->load_template('telefonia/prestadores/prestadores_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		
		$prestador = $this->Prestadores_model->get_one($id);
		if (empty($prestador))
		{
			show_error('No se encontró el Prestador', 500, 'Registro no encontrado');
		}
		$data['fields'] = $this->build_fields($this->Prestadores_model->fields, $prestador, TRUE);
		$data['prestador'] = $prestador;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Prestador';
		$data['title'] = TITLE . ' - Ver Prestador';
		$this->load_template('telefonia/prestadores/prestadores_abm', $data);
	}
}