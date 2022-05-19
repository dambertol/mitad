<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Situaciones_iva_model extends MY_Model
{

	/**
	 * Modelo de Situaciones IVA
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 21/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ob_situaciones_iva';
		$this->full_log = TRUE;
		$this->msg_name = 'Situación IVA';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('descripcion');
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
		if ($this->db->where('iva_id', $delete_id)->count_all_results('ob_proveedores') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Proveedor.');
			return FALSE;
		}
		return TRUE;
	}
}