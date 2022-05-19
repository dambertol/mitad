<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{

	/**
	 * Controlador de Ajax
	 * Autor: Leandro
	 * Creado: 04/09/2019
	 * Modificado: 04/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();

		$this->grupos_ajax = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function get_equipo()
	{
		if (!in_groups($this->grupos_ajax, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('equipo_id', 'Equipo', 'required|integer');
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('telefonia/Equipos_model');
			$equipo = $this->Equipos_model->get_one($this->input->post('equipo_id'));
			if (!empty($equipo))
			{
				$datos['equipo'] = $equipo;
			}
			else
			{
				$datos['no_data'] = TRUE;
			}
		}
		else
		{
			$datos['no_data'] = TRUE;
		}

		echo json_encode($datos);
	}

	public function get_linea()
	{
		if (!in_groups($this->grupos_ajax, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('linea_id', 'Linea', 'required|integer');
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('telefonia/Lineas_model');
			$linea = $this->Lineas_model->get_one($this->input->post('linea_id'));
			if (!empty($linea))
			{
				$datos['linea'] = $linea;
			}
			else
			{
				$datos['no_data'] = TRUE;
			}
		}
		else
		{
			$datos['no_data'] = TRUE;
		}

		echo json_encode($datos);
	}
}