<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ordenes_compra_model extends MY_Model
{

	/**
	 * Modelo de Órdenes de Compra
	 * Autor: Leandro
	 * Creado: 13/11/2017
	 * Modificado: 12/06/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_ordenes_compra';
		$this->full_log = TRUE;
		$this->msg_name = 'Orden de Compra';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'ejercicio', 'numero', 'total', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'required' => TRUE),
				'numero' => array('label' => 'Número', 'type' => 'integer', 'required' => TRUE),
				'total' => array('label' => 'Total', 'type' => 'numeric', 'required' => TRUE, 'readonly' => TRUE)
		);
		$this->requeridos = array('fecha', 'ejercicio', 'numero', 'total');
		$this->unicos = array(array('ejercicio', 'numero'));
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
		if ($this->db->where('orden_compra_id', $delete_id)->count_all_results('vc_facturas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Factura.');
			return FALSE;
		}
		if ($this->db->where('orden_compra_id', $delete_id)->count_all_results('vc_vales') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vale.');
			return FALSE;
		}
		if ($this->db->where('orden_compra_id', $delete_id)->count_all_results('vc_vales_semanales') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vale semanal.');
			return FALSE;
		}
		return TRUE;
	}
}