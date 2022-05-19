<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Numeraciones_model extends MY_Model
{

	/**
	 * Modelo de Numeraciones
	 * Autor: Leandro
	 * Creado: 24/10/2018
	 * Modificado: 24/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_numeraciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Numeración';
		$this->id_name = 'id';
		$this->columnas = array('id', 'ejercicio', 'numero_inicial', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'numero_inicial' => array('label' => 'Número Inicial', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE)
		);
		$this->requeridos = array('ejercicio', 'numero_inicial');
		$this->unicos = array(array('ejercicio'));
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