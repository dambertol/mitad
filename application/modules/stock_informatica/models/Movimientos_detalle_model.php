<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos_detalle_model extends MY_Model
{

	/**
	 * Modelo de Detalles de Movimiento
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 11/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'si_movimientos_detalle';
		$this->full_log = TRUE;
		$this->msg_name = 'Detalle de Movimiento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'movimiento_id', 'articulo_id', 'cantidad', 'ala', 'oficina', 'ip', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'ala' => array('label' => 'Ala', 'maxlength' => '50'),
				'oficina' => array('label' => 'Oficina', 'type' => 'integer', 'maxlength' => '10'),
				'ip' => array('label' => 'IP', 'maxlength' => '50'),
				'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect'),
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect'),
		);
		$this->requeridos = array('movimiento_id', 'articulo_id');
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