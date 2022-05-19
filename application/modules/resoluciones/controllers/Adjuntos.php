<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 07/06/2019
	 * Modificado: 07/06/2019 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'resoluciones_user', 'resoluciones_consulta_general');
		$this->modulo = 'resoluciones';
		// Inicializaciones necesarias colocar acá.
	}

	public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		return;
	}

	public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->directorio = $directorio_nombre;
		$this->archivo = $archivo_id;
		$this->entidad_id_nombre = 'resolucion_id';
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