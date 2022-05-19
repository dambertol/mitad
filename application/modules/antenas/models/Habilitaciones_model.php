<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Habilitaciones_model extends MY_Model
{

	/**
	 * Modelo de Habilitaciones
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'an_habilitaciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Habilitación';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'expediente', 'estado', 'torre_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'torre' => array('label' => 'Torre', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'expediente' => array('label' => 'Expediente', 'maxlength' => '50', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'maxlength' => '150', 'required' => TRUE),
		);
		$this->requeridos = array('fecha', 'expediente', 'estado', 'torre_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('an_torres', 'an_torres.id = an_habilitaciones.torre_id', 'LEFT', array("an_torres.servicio as torre"))
		);
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