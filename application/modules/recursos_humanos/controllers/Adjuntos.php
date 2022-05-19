<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 07/06/2019
	 * Modificado: 11/12/2019 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_director', 'recursos_humanos_publico', 'recursos_humanos_consulta_general');
		$this->modulo = 'recursos_humanos';
		// Inicializaciones necesarias colocar acá.
	}

	public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		return;
	}

	public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $sub_directorio_nombre = NULL, $archivo_id = NULL)
	{
		if ($entidad_nombre === 'manuales')
		{
			$this->entidad = $entidad_nombre;
			$this->directorio = '';
			$this->archivo = $directorio_nombre;
			$this->verificar_archivo = FALSE;
		}
		else
		{
			$this->entidad = $entidad_nombre;
			$this->directorio = "$directorio_nombre/$sub_directorio_nombre";
			$this->archivo = $archivo_id;
			$this->entidad_id_nombre = 'legajo_id';
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