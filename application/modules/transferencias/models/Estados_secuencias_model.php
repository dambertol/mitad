<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estados_secuencias_model extends MY_Model
{

	/**
	 * Modelo de Secuencias de Estados
	 * Autor: Leandro
	 * Creado: 27/06/2018
	 * Modificado: 17/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_estados_secuencias';
		$this->full_log = TRUE;
		$this->msg_name = 'Secuencia de Estados';
		$this->id_name = 'id';
		$this->columnas = array('id', 'estado_id', 'estado_posterior_id', 'icono', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'estado_posterior' => array('label' => 'Estado de Posterior', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('estado_id', 'estado_posterior_id');
		$this->unicos = array(array('estado_id', 'estado_posterior_id'));
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