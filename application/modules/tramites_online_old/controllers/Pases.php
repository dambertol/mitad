<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pases extends MY_Controller
{

	/**
	 * Controlador de Pases
	 * Autor: Leandro
	 * Creado: 16/03/2020
	 * Modificado: 22/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('tramites_online/Adjuntos_model');
		$this->load->model('tramites_online/Pases_model');
		$this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
		$this->grupos_solo_consulta = array('tramites_online_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function modal_ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			return $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
		}

		$pase = $this->Pases_model->get_one($id);
		if (empty($pase))
		{
			return $this->modal_error('No se encontró el Pase', 'Registro no encontrado');
		}

		$adjuntos = $this->Adjuntos_model->get(array('pase_id' => $id));

		$data['fields'] = $this->build_fields($this->Pases_model->fields, $pase, TRUE);
		$data['pase'] = $pase;
		$data['adjuntos'] = $adjuntos;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Pase';
		$data['title'] = TITLE . ' - Ver Pase';
		$this->load->view('tramites_online/pases/pases_modal_abm', $data);
	}
}