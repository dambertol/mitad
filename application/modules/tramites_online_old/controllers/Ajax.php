<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{

	/**
	 * Controlador de Ajax
	 * Autor: Leandro
	 * Creado: 18/03/2020
	 * Modificado: 17/04/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_ajax = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
		$this->grupos_ajax_publico = array('tramites_online_publico');
		// Inicializaciones necesarias colocar acá.
	}

	public function buscar_tipo_tramite()
	{
		if (!in_groups($this->grupos_ajax, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->form_validation->set_rules('categoria_id', 'Categoría', 'required|integer');
		if ($this->form_validation->run() === TRUE)
		{
			$this->load->model('tramites_online/Tramites_tipos_model');
			if (in_groups($this->grupos_ajax_publico, $this->grupos))
			{
				$tipo_tramite = $this->Tramites_tipos_model->get(array(
						'categoria_id' => $this->input->post('categoria_id'),
						'visibilidad' => 'Público'
				));
			}
			else
			{
				$tipo_tramite = $this->Tramites_tipos_model->get(array(
						'categoria_id' => $this->input->post('categoria_id')
				));
			}
			if (empty($tipo_tramite))
			{
				$datos['no_data'] = TRUE;
			}
			else
			{
				$datos['tipo_tramite'] = $tipo_tramite;
			}
		}
		else
		{
			$datos['no_data'] = TRUE;
		}

		echo json_encode($datos);
	}
}