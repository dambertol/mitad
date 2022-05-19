<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 16/03/2020
	 * Modificado: 22/03/2020 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
		$this->grupos_publico = array('tramites_online_publico');
		$this->modulo = 'tramites_online';
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
			if (!empty($archivo_id)) //Pases usa subdirectorio
			{
				$this->entidad = $entidad_nombre;
				$this->directorio = "$directorio_nombre/$sub_directorio_nombre";
				$this->archivo = $archivo_id;
				$this->entidad_id_nombre = 'pase_id';
			}
			else
			{
				$this->entidad = $entidad_nombre;
				$this->directorio = $directorio_nombre;
				$this->archivo = $sub_directorio_nombre;
				$this->entidad_id_nombre = 'tramite_id';
			}

			$this->load->model("$this->modulo/Adjuntos_model");
			$path = "uploads/$this->modulo/$this->entidad/$this->directorio/";
			$persona_id = $this->Adjuntos_model->persona_adjunto($path, $this->archivo);
			if (empty($persona_id))
			{
				show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
			}
			$this->load->model("Personas_model");
			if (in_groups($this->grupos_publico, $this->grupos) && $this->Personas_model->get_user_id($persona_id) !== $this->session->userdata('user_id'))
			{
				show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
			}
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