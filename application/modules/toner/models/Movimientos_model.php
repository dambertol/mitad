<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos_model extends MY_Model
{

	/**
	 * Modelo de Movimientos
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 07/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_movimientos';
		$this->full_log = TRUE;
		$this->msg_name = 'Movimiento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha_movimiento', 'observaciones', 'estado', 'orden_compra', 'fecha_oc', 'monto_oc', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'fecha_movimiento' => array('label' => 'Fecha Movimiento', 'type' => 'datetime', 'required' => TRUE),
				'orden_compra' => array('label' => 'Orden de Compra', 'maxlength' => '50'),
				'fecha_oc' => array('label' => 'Fecha OC', 'type' => 'date'),
				'monto_oc' => array('label' => 'Monto OC'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
		);
		$this->requeridos = array('fecha_movimiento', 'estado');
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
		if ($this->db->where('movimiento_id', $delete_id)->count_all_results('gt_movimientos_detalles') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}