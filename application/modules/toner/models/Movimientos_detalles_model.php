<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos_detalles_model extends MY_Model
{

	/**
	 * Modelo de Detalles de Movimiento
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 26/08/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_movimientos_detalles';
		$this->full_log = TRUE;
		$this->msg_name = 'Detalle de Movimiento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'movimiento_id', 'consumible_id', 'cantidad_llenos', 'cantidad_vacios', 'cantidad_fuera_servicio', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'movimiento' => array('label' => 'Movimiento', 'input_type' => 'combo', 'required' => TRUE),
				'consumible' => array('label' => 'Consumible', 'input_type' => 'combo', 'required' => TRUE),
				'cantidad_llenos' => array('label' => 'Cantidad de Llenos', 'type' => 'integer_with_neg', 'maxlength' => '11', 'required' => TRUE),
				'cantidad_vacios' => array('label' => 'Cantidad de Vacios', 'type' => 'integer_with_neg', 'maxlength' => '11', 'required' => TRUE),
				'cantidad_fuera_servicio' => array('label' => 'Cantidad de Fuera', 'type' => 'integer_with_neg', 'maxlength' => '11', 'required' => TRUE)
		);
		$this->requeridos = array('movimiento_id', 'consumible_id', 'cantidad_llenos', 'cantidad_vacios', 'cantidad_fuera_servicio');
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