<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Consumibles_model extends MY_Model
{

	/**
	 * Modelo de Consumibles
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 07/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_consumibles';
		$this->full_log = TRUE;
		$this->msg_name = 'Consumible';
		$this->id_name = 'id';
		$this->columnas = array('id', 'modelo', 'descripcion', 'tipo', 'stock_vacios', 'stock_llenos', 'stock_fuera_servicio', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'modelo' => array('label' => 'Modelo', 'maxlength' => '50', 'required' => TRUE),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '100', 'required' => TRUE),
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'tipo', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'required' => TRUE)
		);
		$this->requeridos = array('modelo', 'descripcion', 'tipo', 'stock_vacios', 'stock_llenos', 'stock_fuera_servicio', 'estado');
		//$this->unicos = array();
		$this->default_join = array();
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * update_stock: Actualiza el stock del consumible.
	 *
	 * @param int $consumible_id
	 * @param int $cantidad_llenos
	 * @param int $cantidad_vacios
	 * @param int $cantidad_fuera_servicio
	 * @return array
	 */
	public function update_stock($consumible_id, $cantidad_llenos, $cantidad_vacios, $cantidad_fuera_servicio)
	{
		$this->db->set('stock_llenos', 'stock_llenos+' . $cantidad_llenos, FALSE);
		$this->db->set('stock_vacios', 'stock_vacios+' . $cantidad_vacios, FALSE);
		$this->db->set('stock_fuera_servicio', 'stock_fuera_servicio+' . $cantidad_fuera_servicio, FALSE);
		$this->db->where($this->id_name, $consumible_id);
		$this->db->update($this->table_name);
		$rows = $this->db->affected_rows();
		if ($rows > -1)
		{
			$this->_set_msg('Registro de ' . $this->msg_name . ' modificado');
			return TRUE;
		}
		else
		{
			$this->_set_error('No se ha podido modificar el registro de ' . $this->msg_name);
			return FALSE;
		}
	}

	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('consumible_id', $delete_id)->count_all_results('gt_consumibles_impresoras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Impresora.');
			return FALSE;
		}
		if ($this->db->where('consumible_id', $delete_id)->count_all_results('gt_movimientos_detalles') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Movimiento.');
			return FALSE;
		}
		if ($this->db->where('consumible_id', $delete_id)->count_all_results('gt_pedidos_consumibles_detalles') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pedido.');
			return FALSE;
		}
		return TRUE;
	}
}