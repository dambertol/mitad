<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{

	/**
	 * Controlador de Ajax
	 * Autor: Leandro
	 * Creado: 30/01/2018
	 * Modificado: 24/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();

		$this->grupos_ajax = array('admin', 'vales_combustible_autorizaciones', 'vales_combustible_contaduria', 'vales_combustible_obrador', 'vales_combustible_estacion', 'vales_combustible_hacienda', 'vales_combustible_areas', 'vales_combustible_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function buscar_combustible_vehiculo()
	{
		if (!in_groups($this->grupos_ajax, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('vehiculo_id', 'Vehículo', 'required|integer');
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('vales_combustible/Vehiculos_combustible_model');
			$combustible_vehiculo = $this->Vehiculos_combustible_model->get(array(
					'vehiculo_id' => $this->input->post('vehiculo_id'),
					'join' => array(
							array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_vehiculos_combustible.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible"))
					)
			));
			if (empty($combustible_vehiculo))
			{
				$datos['no_data'] = TRUE;
			}
			else
			{
				$datos['combustible_vehiculo'] = $combustible_vehiculo;
			}
		}
		else
		{
			$datos['no_data'] = TRUE;
		}

		echo json_encode($datos);
	}

	public function buscar_persona()
	{
		if (!in_groups($this->grupos_ajax, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('dni', 'DNI', 'required|integer|max_length[11]');
		$this->form_validation->set_rules('call', 'Call', 'required|integer|max_length[9]');
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('Personal_model');
			$dni = $this->input->post('dni');
			$call = $this->input->post('call');
			if (!empty($dni) && ctype_digit($dni))
			{
				$datos['persona'] = $this->Personal_model->get(array('Legajo' => $this->input->post('dni')));
				if (empty($datos['persona']))
				{
					$datos['no_data'] = TRUE;
				}
				$datos['call'] = $call;
				echo json_encode($datos);
			}
			else
			{
				$datos['no_data'] = TRUE;
				$datos['call'] = $call;
				echo json_encode($datos);
			}
		}
	}
}