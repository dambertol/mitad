<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 13/01/2020
	 * Modificado: 13/01/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'gobierno_user', 'gobierno_consulta_general');
		$this->modulo = 'gobierno';
		// Inicializaciones necesarias colocar acá.
	}

	public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->archivo_id = $archivo_id;
		$this->entidad_id_nombre = 'documento_id';

		$this->load->model("$this->modulo/Adjuntos_model");
		$adjunto = $this->Adjuntos_model->get_one($this->archivo_id);
		if (empty($adjunto))
		{
			show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
		}

		parent::descargar();
	}

	public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->directorio = $directorio_nombre;
		$this->archivo = $archivo_id;
		$this->entidad_id_nombre = 'documento_id';

		$this->load->model("$this->modulo/Adjuntos_model");
		$path = "uploads/$this->modulo/$this->entidad/$this->directorio/";
		$adjunto = $this->Adjuntos_model->get(array('ruta' => $path, 'nombre' => $this->archivo));
		if (empty($adjunto[0]))
		{
			show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
		}

		parent::ver();
	}

	public function modal_agregar($entidad_nombre = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->extensiones = '["jpg", "png", "jpeg", "pdf", "tiff"]';
		parent::modal_agregar();
	}

	public function agregar($entidad_nombre = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->extensiones = 'jpg|jpeg|png|pdf|tiff';
		parent::agregar();
	}
}