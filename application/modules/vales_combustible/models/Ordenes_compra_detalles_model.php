<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ordenes_compra_detalles_model extends MY_Model
{

	/**
	 * Modelo de Detalles de Órdenes de Compra
	 * Autor: Leandro
	 * Creado: 13/11/2017
	 * Modificado: 30/01/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_ordenes_compra_detalles';
		$this->full_log = TRUE;
		$this->msg_name = 'Detalle de Orden de Compra';
		$this->id_name = 'id';
		$this->columnas = array('id', 'orden_compra_id', 'tipo_combustible_id', 'litros', 'costo_unitario', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_combustible_1' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'litros_1' => array('label' => 'M³/Litros', 'type' => 'numeric', 'required' => TRUE, 'class' => 'costo_total_calculo'),
				'costo_unitario_1' => array('label' => 'Costo Unitario', 'type' => 'money', 'required' => TRUE, 'class' => 'costo_total_calculo'),
				'costo_total_1' => array('label' => 'Costo Total', 'type' => 'money', 'required' => TRUE, 'readonly' => TRUE)
		);
		$this->requeridos = array('orden_compra_id', 'tipo_combustible_id', 'litros', 'costo_unitario');
		$this->unicos = array(array('orden_compra_id'));
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

	public function delete_detalles($orden_compra_id)
	{
		$this->db->where('orden_compra_id', $orden_compra_id);

		if ($this->db->delete($this->table_name))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
}