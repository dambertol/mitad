<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias_model extends MY_Model
{

	/**
	 * Modelo de Categorías
	 * Autor: Leandro
	 * Creado: 17/12/2019
	 * Modificado: 17/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rm_categorias';
		$this->full_log = TRUE;
		$this->msg_name = 'Categoría';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '100', 'required' => TRUE)
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
		if ($this->db->where('categoria_id', $delete_id)->count_all_results('rm_incidencias') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Incidencia.');
			return FALSE;
		}
		return TRUE;
	}
}