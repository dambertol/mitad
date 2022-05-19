<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 17/12/2019
	 * Modificado: 17/12/2019 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'reclamos_major_admin', 'reclamos_major_consulta_general');
		$this->modulo = 'reclamos_major';
		// Inicializaciones necesarias colocar acá.
	}

	public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->archivo_id = $archivo_id;
		$this->entidad_id_nombre = 'incidencia_id';
		parent::descargar();
	}

	public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->directorio = $directorio_nombre;
		$this->archivo = $archivo_id;
		$this->entidad_id_nombre = 'incidencia_id';
		parent::ver();
	}

	public function modal_agregar($entidad_nombre = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->extensiones = '["jpg", "png", "jpeg", "pdf", "doc", "docx", "xls", "xlsx"]';
		parent::modal_agregar();
	}

	public function agregar($entidad_nombre = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->extensiones = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
		parent::agregar();
	}
}