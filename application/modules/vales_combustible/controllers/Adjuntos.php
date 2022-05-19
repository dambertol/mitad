<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 10/06/2019
	 * Modificado: 24/06/2019 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'vales_combustible_autorizaciones', 'vales_combustible_contaduria', 'vales_combustible_areas', 'vales_combustible_obrador', 'vales_combustible_consulta_general');
		$this->modulo = 'vales_combustible';
		// Inicializaciones necesarias colocar acá.
	}

	public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->archivo_id = $archivo_id;
		$this->entidad_id_nombre = 'vehiculo_id';

		$this->load->model("$this->modulo/Adjuntos_model");
		$adjunto = $this->Adjuntos_model->get_one($this->archivo_id);
		if (empty($adjunto))
		{
			show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
		}
		$this->load->model("$this->modulo/{$this->entidad}_model");
		$entidad = $this->{"{$this->entidad}_model"}->get_one($adjunto->{$this->entidad_id_nombre});
		$this->load->model("$this->modulo/Usuarios_areas_model");
		$this->grupos_limitados = array('vales_combustible_areas');
		if (in_groups($this->grupos_limitados, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $entidad->area_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		parent::descargar();
	}

	public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->directorio = $directorio_nombre;
		$this->archivo = $archivo_id;
		$this->entidad_id_nombre = 'vehiculo_id';

		$this->load->model("$this->modulo/Adjuntos_model");
		$path = "uploads/$this->modulo/$this->entidad/$this->directorio/";
		$adjunto = $this->Adjuntos_model->get(array('ruta' => $path, 'nombre' => $this->archivo));
		if (empty($adjunto[0]))
		{
			show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
		}
		$this->load->model("$this->modulo/{$this->entidad}_model");
		$entidad = $this->{"{$this->entidad}_model"}->get_one($adjunto[0]->{$this->entidad_id_nombre});
		$this->load->model("$this->modulo/Usuarios_areas_model");
		$this->grupos_limitados = array('vales_combustible_areas');
		if (in_groups($this->grupos_limitados, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $entidad->area_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		parent::ver();
	}

	public function modal_agregar($entidad_nombre = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->extensiones = '["jpg", "png", "jpeg", "pdf"]';
		parent::modal_agregar();
	}

	public function agregar($entidad_nombre = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->extensiones = 'jpg|jpeg|png|pdf';
		parent::agregar();
	}
}