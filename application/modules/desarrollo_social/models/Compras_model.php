<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compras_model extends MY_Model
{

	/**
	 * Modelo de Compras
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_compras';
		$this->full_log = TRUE;
		$this->msg_name = 'Compra';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha_recepcion', 'proveedor_id', 'lugar_fisico', 'nro_orden', 'recepcionista', 'ubicacion', 'nro_remito', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'proveedor' => array('label' => 'Proveedor', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_recepcion' => array('label' => 'Fecha Recepción', 'type' => 'datetime', 'required' => TRUE),
				'recepcionista' => array('label' => 'Recepcionista', 'maxlength' => '50', 'required' => TRUE),
				'lugar_fisico' => array('label' => 'Lugar Físico', 'maxlength' => '50'),
				'nro_remito' => array('label' => 'Nro Remito', 'maxlength' => '10'),
				'nro_orden' => array('label' => 'Nro Orden', 'maxlength' => '50')
		);
		$this->requeridos = array('fecha_recepcion', 'proveedor_id', 'recepcionista', 'estado');
		//$this->unicos = array();
		$this->default_join = array(
				array('ds_proveedores', 'ds_proveedores.id = ds_compras.proveedor_id', 'LEFT', array('ds_proveedores.razon_social as proveedor'))
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
		if ($this->db->where('compra_id', $delete_id)->count_all_results('ds_detalle_compras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}