<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Observaciones_incidencias_model extends MY_Model
{

	/**
	 * Modelo de Observaciones
	 * Autor: Leandro
	 * Creado: 12/04/2019
	 * Modificado: 12/04/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'in_observaciones_incidencias';
		$this->full_log = TRUE;
		$this->msg_name = 'Observación';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'incidencia_id', 'observacion', 'user_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'incidencia' => array('label' => 'Incidencia', 'input_type' => 'combo', 'required' => TRUE),
				'observacion' => array('label' => 'Observación', 'required' => TRUE),
				'user' => array('label' => 'Usuario', 'input_type' => 'combo', 'required' => TRUE)
		);
		$this->requeridos = array('fecha', 'incidencia_id', 'observacion', 'user_id');
		//$this->unicos = array();
		$this->default_join = array();
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		return TRUE;
	}
}