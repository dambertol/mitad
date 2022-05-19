<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Datos_extra extends MY_Controller
{

	/**
	 * Controlador de Datos Extra
	 * Autor: Leandro
	 * Creado: 08/08/2019
	 * Modificado: 12/08/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Areas_model');
		$this->load->model('recursos_humanos/Datos_extra_model');
		$this->load->model('recursos_humanos/Datos_extra_hobbies_model');
		$this->load->model('recursos_humanos/Datos_extra_oficinas_model');
		$this->load->model('recursos_humanos/Hobbies_model');
		$this->load->model('recursos_humanos/Legajos_model');
		$this->grupos_permitidos = array('admin', 'recursos_humanos_user', 'recursos_humanos_consulta_general');
		$this->grupos_solo_consulta = array('recursos_humanos_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function editar($legajo_id = NULL, $datos_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $legajo_id == NULL || !ctype_digit($legajo_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("recursos_humanos/legajo/ver/$legajo_id", 'refresh');
		}

		if ($datos_id !== NULL)
		{
			if (!ctype_digit($datos_id))
			{
				show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
			}

			$datos_extra = $this->Datos_extra_model->get(array('id' => $datos_id));
			if (empty($datos_extra) || $datos_extra->legajo_id !== $legajo_id)
			{
				show_error('No se encontraron los Datos Extra', 500, 'Registro no encontrado');
			}

			$hobbies = $this->Datos_extra_hobbies_model->get(array('dato_extra_id' => $datos_id));
			$datos_extra->hobbies = array();
			if (!empty($hobbies))
			{
				foreach ($hobbies as $Hobby)
				{
					$datos_extra->hobbies[] = $Hobby->hobby_id;
				}
			}

			$posibles_oficinas = $this->Datos_extra_oficinas_model->get(array('dato_extra_id' => $datos_id));
			$datos_extra->posibles_oficinas = array();
			if (!empty($posibles_oficinas))
			{
				foreach ($posibles_oficinas as $Oficina)
				{
					$datos_extra->posibles_oficinas[] = $Oficina->area_id;
				}
			}
		}
		else
		{
			$datos_extra = new stdClass();
			$datos_extra->id = NULL;
			$datos_extra->hobbies = NULL;
			$datos_extra->experiencias = NULL;
			$datos_extra->conformidad_oficina = NULL;
			$datos_extra->posibles_oficinas = NULL;
		}

		$this->array_hobbies_control = $array_hobbies = $this->get_array('Hobbies', 'nombre');
		$this->set_model_validation_rules($this->Datos_extra_hobbies_model);

		$this->array_posibles_oficinas_control = $array_posibles_oficinas = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
		$this->set_model_validation_rules($this->Datos_extra_oficinas_model);

		$this->array_conformidad_oficina_control = $array_conformidad_oficina = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10);
		$this->set_model_validation_rules($this->Datos_extra_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($datos_id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$hobbies_data = $this->input->post('hobbies[]');
				if (empty($hobbies_data))
				{
					$hobbies_data = array();
				}

				$posibles_oficinas_data = $this->input->post('posibles_oficinas[]');
				if (empty($posibles_oficinas_data))
				{
					$posibles_oficinas_data = array();
				}

				$this->db->trans_begin();
				$trans_ok = TRUE;
				if ($datos_id !== NULL)
				{
					$trans_ok &= $this->Datos_extra_model->update(array(
							'id' => $this->input->post('id'),
							'legajo_id' => $legajo_id,
							'experiencias' => $this->input->post('experiencias'),
							'conformidad_oficina' => $this->input->post('conformidad_oficina')
							), FALSE);
					$dato_extra_id = $this->input->post('id');
				}
				else
				{
					$trans_ok &= $this->Datos_extra_model->create(array(
							'legajo_id' => $legajo_id,
							'experiencias' => $this->input->post('experiencias'),
							'conformidad_oficina' => $this->input->post('conformidad_oficina')
							), FALSE);
					$dato_extra_id = $this->Datos_extra_model->get_row_id();
				}
				$trans_ok &= $this->Datos_extra_hobbies_model->intersect_asignaciones($dato_extra_id, $hobbies_data, FALSE);
				$trans_ok &= $this->Datos_extra_oficinas_model->intersect_asignaciones($dato_extra_id, $posibles_oficinas_data, FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Datos_extra_model->get_msg());
					redirect("recursos_humanos/legajos/ver/$legajo_id", 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Datos_extra_model->get_error())
					{
						$error_msg .= $this->Datos_extra_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Datos_extra_model->fields['hobbies']['array'] = $array_hobbies;
		$this->Datos_extra_model->fields['posibles_oficinas']['array'] = $array_posibles_oficinas;
		$this->Datos_extra_model->fields['conformidad_oficina']['array'] = $array_conformidad_oficina;
		$data['fields'] = $this->build_fields($this->Datos_extra_model->fields, $datos_extra);
		$data['legajo_id'] = $legajo_id;
		$data['datos_extra'] = $datos_extra;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Datos Extra';
		$data['title'] = TITLE . ' - Editar Datos Extra';
		$this->load_template('recursos_humanos/datos_extra/datos_extra_abm', $data);
	}
}