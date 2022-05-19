<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 15/11/2019
	 * Modificado: 15/11/2019 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user', 'asistencia_consulta_general');
		$this->modulo = 'asistencia';
		// Inicializaciones necesarias colocar acá.
	}

	public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		return;
	}

	public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $sub_directorio_nombre = NULL, $archivo_id = NULL)
	{
		if ($entidad_nombre === 'formularios')
		{
			$this->entidad = $entidad_nombre;
			$this->directorio = '';
			$this->archivo = $directorio_nombre;
			$this->verificar_archivo = FALSE;
		}
		parent::ver();
	}

	public function modal_agregar($entidad_nombre = NULL)
	{
		return;
	}

	public function agregar($entidad_nombre = NULL)
	{
		return;
	}
}