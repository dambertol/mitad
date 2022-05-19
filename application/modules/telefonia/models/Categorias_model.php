<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias_model extends MY_Model
{

	/**
	 * Modelo de Categorías
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 02/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_categorias';
		$this->full_log = TRUE;
		$this->msg_name = 'Categoría';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'tipo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '100', 'required' => TRUE),
				'tipo' => array('label' => 'Tipo', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('descripcion', 'tipo');
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
		if ($this->db->where('categoria_id', $delete_id)->count_all_results('tm_modelos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Modelo.');
			return FALSE;
		}
		return TRUE;
	}
}