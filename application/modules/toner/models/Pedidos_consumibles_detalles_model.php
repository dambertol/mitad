<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos_consumibles_detalles_model extends MY_Model
{

	/**
	 * Modelo de Detalles de Pedido
	 * Autor: Leandro
	 * Creado: 09/05/2019
	 * Modificado: 09/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_pedidos_consumibles_detalles';
		$this->full_log = TRUE;
		$this->msg_name = 'Detalle de Pedido';
		$this->id_name = 'id';
		$this->columnas = array('id', 'pedido_consumibles_id', 'consumible_id', 'fecha_entrega', 'resp_entrega', 'orden_compra', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'pedido_consumibles' => array('label' => 'Pedido', 'input_type' => 'combo', 'required' => TRUE),
				'consumible' => array('label' => 'Consumible', 'input_type' => 'combo', 'required' => TRUE),
				'fecha_entrega' => array('label' => 'Fecha Entrega', 'type' => 'date'),
				'resp_entrega' => array('label' => 'Resp Entrega', 'maxlength' => '50'),
				'orden_compra' => array('label' => 'Orden de Compra', 'maxlength' => '50')
		);
		$this->requeridos = array('pedido_consumibles_id', 'consumible_id');
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