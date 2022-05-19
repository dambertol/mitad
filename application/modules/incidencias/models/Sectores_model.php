<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sectores_model extends MY_Model
{

	/**
	 * Modelo de Sectores
	 * Autor: Leandro
	 * Creado: 12/04/2019
	 * Modificado: 12/04/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'in_sectores';
		$this->full_log = TRUE;
		$this->msg_name = 'Sector';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('descripcion');
		$this->unicos = array('descripcion');
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
		if ($this->db->where('sector_id', $delete_id)->count_all_results('in_categorias') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Categoría.');
			return FALSE;
		}
		if ($this->db->where('sector_id', $delete_id)->count_all_results('in_usuarios_sectores') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Usuario.');
			return FALSE;
		}
		return TRUE;
	}
}