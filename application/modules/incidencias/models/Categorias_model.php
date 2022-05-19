<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias_model extends MY_Model
{

	/**
	 * Modelo de Categorías
	 * Autor: Leandro
	 * Creado: 12/04/2019
	 * Modificado: 12/04/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'in_categorias';
		$this->full_log = TRUE;
		$this->msg_name = 'Categoría';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'sector_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '100', 'required' => TRUE),
				'sector' => array('label' => 'Sector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
		);
		$this->requeridos = array('descripcion');
		$this->unicos = array(array('descripcion', 'sector_id'));
		$this->default_join = array(
				array('in_sectores', 'in_sectores.id = in_categorias.sector_id', 'LEFT', array("in_sectores.descripcion as sector"))
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
		if ($this->db->where('categoria_id', $delete_id)->count_all_results('in_incidencias') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Incidencia.');
			return FALSE;
		}
		return TRUE;
	}
}