<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pedidos_consumibles_model extends MY_Model
{

	/**
	 * Modelo de Pedidos
	 * Autor: Leandro
	 * Creado: 09/05/2019
	 * Modificado: 09/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_pedidos_consumibles';
		$this->full_log = TRUE;
		$this->msg_name = 'Pedido';
		$this->id_name = 'id';
		$this->columnas = array('id', 'area_id', 'fecha_solicitud', 'resp_solicitud', 'observacion', 'estado', 'user_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_solicitud' => array('label' => 'Fecha Solicitud', 'type' => 'datetime', 'required' => TRUE),
				'resp_solicitud' => array('label' => 'Resp Solicitud', 'maxlength' => '50', 'required' => TRUE),
				'observacion' => array('label' => 'Observación', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('area_id', 'fecha_solicitud', 'resp_solicitud', 'estado');
		//$this->unicos = array();
		$this->default_join = array(
				array('areas', 'areas.id = gt_pedidos_consumibles.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
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
		if ($this->db->where('pedido_consumible_id', $delete_id)->count_all_results('gt_pedidos_consumibles_detalles') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}