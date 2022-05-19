<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Formularios extends MY_Controller
{

	/**
	 * Controlador Formularios
	 * Autor: Leandro
	 * Creado: 15/11/2019
	 * Modificado: 15/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user', 'asistencia_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('asistencia/Adjuntos_model');
		$adjuntos = $this->Adjuntos_model->get();
		if (!empty($adjuntos))
		{
			foreach ($adjuntos as $Adjunto)
			{
				if (($pos = strpos($Adjunto->ruta, ".")) !== FALSE)
				{
					$Adjunto->formato = strtoupper(substr($Adjunto->ruta, $pos + 1));
				}
				switch ($Adjunto->formato)
				{
					case 'PDF':
						$Adjunto->icono = 'fa-file-pdf-o';
						break;
					case 'XLS':
					case 'XLSX':
						$Adjunto->icono = 'fa-file-excel-o';
						break;
					case 'DOC':
					case 'DOCX':
						$Adjunto->icono = 'fa-file-word-o';
						break;
					default:
						$Adjunto->icono = 'fa-file-o';
						break;
				}
			}
			$data['formularios'] = $adjuntos;
		}

		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Formularios';
		$data['title'] = TITLE . ' - Formularios';
		$data['css'] = 'css/asistencia/asistencia.css';
		$data['title'] = TITLE . ' - Formularios';
		$this->load_template('asistencia/formularios/content', $data);
	}
}