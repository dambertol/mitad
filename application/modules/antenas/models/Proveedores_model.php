<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores_model extends MY_Model
{

	/**
	 * Modelo de Proveedores
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'an_proveedores';
		$this->full_log = TRUE;
		$this->msg_name = 'Proveedor';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'cuit', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
				'cuit' => array('label' => 'CUIT', 'maxlength' => '20')
		);
		$this->requeridos = array('nombre', 'cuit');
		$this->unicos = array('nombre', 'cuit');
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
		if ($this->db->where('proveedor_id', $delete_id)->count_all_results('an_antenas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Antena.');
			return FALSE;
		}
		if ($this->db->where('proveedor_id', $delete_id)->count_all_results('an_torres') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Torre.');
			return FALSE;
		}
		return TRUE;
	}
}