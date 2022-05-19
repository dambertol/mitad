<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pases_model extends MY_Model
{

	/**
	 * Modelo de Pases
	 * Autor: Leandro
	 * Creado: 17/03/2020
	 * Modificado: 17/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'to_pases';
		$this->full_log = TRUE;
		$this->msg_name = 'Pase';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tramite_id', 'estado_origen_id', 'estado_destino_id', 'fecha', 'usuario_origen', 'usuario_destino', 'area_origen_id', 'area_destino_id', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'estado_origen' => array('label' => 'Origen', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'estado_destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('tramite_id', 'estado_origen_id', 'estado_destino_id', 'fecha', 'usuario_origen');
		//$this->unicos = array();
		$this->default_join = array(
				array('to_estados EO', 'EO.id = to_pases.estado_origen_id', 'LEFT'),
				array('areas AO', 'AO.id = to_pases.area_origen_id', 'LEFT', "CONCAT(EO.nombre, ' (', COALESCE(AO.nombre, ''), ')') as estado_origen"),
				array('to_estados ED', 'ED.id = to_pases.estado_destino_id', 'LEFT'),
				array('areas AD', 'AD.id = to_pases.area_destino_id', 'LEFT', "CONCAT(ED.nombre, ' (', COALESCE(AD.nombre, ''), ')') as estado_destino")
		);
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * get_acceso_ultimo: Devuelve el último pase si el usuario pertenece a la misma oficina.
	 *
	 * @param int $tramite_id
	 * @param int $user_id
	 * @return array $acceso_ultimo_pase
	 */
	public function get_acceso_ultimo($tramite_id, $user_id, $grupo)
	{

		switch ($grupo)
		{
			case 'admin':
				$acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha, P.observaciones, EO.id as eo_id, EO.nombre as estado_origen, ED.id as ed_id, ED.nombre as estado_destino, ED.mensaje as mensaje_destino, AD.id as ad_id, AD.nombre as area_destino
				FROM to_tramites T 
				LEFT JOIN to_pases P ON P.tramite_id = T.id 
				LEFT JOIN to_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN to_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN areas AD ON AD.id = P.area_destino_id 
				WHERE T.id = $tramite_id 
				ORDER BY P.fecha DESC LIMIT 1")->row();
				break;
			case 'area':
				$acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha, P.observaciones, EO.id as eo_id, EO.nombre as estado_origen, ED.id as ed_id, ED.nombre as estado_destino, ED.mensaje as mensaje_destino, AD.id as ad_id, AD.nombre as area_destino 
				FROM to_tramites T 
				LEFT JOIN to_pases P ON P.tramite_id = T.id 
				LEFT OUTER JOIN to_pases PP ON PP.tramite_id = T.id AND P.fecha < PP.fecha
				LEFT JOIN to_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN to_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN areas AD ON AD.id = P.area_destino_id 
				LEFT JOIN to_usuarios_areas UO ON UO.area_id = AD.id
				WHERE T.id = $tramite_id AND UO.user_id = $user_id AND PP.id IS NULL
				ORDER BY P.fecha DESC LIMIT 1")->row();
				break;
			case 'publico':
				$acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha, P.observaciones, EO.id as eo_id, EO.nombre as estado_origen, ED.id as ed_id, ED.nombre as estado_destino, ED.mensaje as mensaje_destino, AD.id as ad_id, AD.nombre as area_destino
				FROM to_tramites T
				LEFT JOIN to_pases P ON P.tramite_id = T.id 
				LEFT OUTER JOIN to_pases PP ON PP.tramite_id = T.id AND P.fecha < PP.fecha 
				LEFT JOIN to_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN to_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN areas AD ON AD.id = P.area_destino_id 
				WHERE T.id = $tramite_id AND P.area_destino_id IS NULL AND PP.id IS NULL
				ORDER BY P.fecha DESC LIMIT 1")->row();
				break;
		}

		return $acceso_ultimo_pase;
	}

	/**
	 * get_ultimo: Devuelve el último pase.
	 *
	 * @param int $tramite_id
	 * @return array $ultimo_pase
	 */
	public function get_ultimo($tramite_id)
	{
		$ultimo_pase = $this->db->query("
				SELECT to_pases.*, ED.nombre as estado_destino, AD.id as area_destino_id, AD.nombre as area_destino, CONCAT(personas.apellido, ', ', personas.nombre) as persona,  personas.email as email_persona
				FROM to_tramites 
				LEFT JOIN to_pases ON to_pases.tramite_id = to_tramites.id 
				LEFT JOIN to_estados ED ON ED.id = to_pases.estado_destino_id 
				LEFT JOIN areas AD ON AD.id = to_pases.area_destino_id 
				LEFT JOIN personas ON personas.id = to_tramites.persona_id
				WHERE to_tramites.id = $tramite_id 
				ORDER BY to_pases.fecha DESC LIMIT 1")->row();

		return $ultimo_pase;
	}

	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('pase_id', $delete_id)->count_all_results('to_adjuntos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Adjunto.');
			return FALSE;
		}
		return TRUE;
	}
}