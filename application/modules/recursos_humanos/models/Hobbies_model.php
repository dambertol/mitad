<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hobbies_model extends MY_Model
{

	/**
	 * Modelo de Hobbies
	 * Autor: Leandro
	 * Creado: 12/08/2019
	 * Modificado: 12/08/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_hobbies';
		$this->full_log = TRUE;
		$this->msg_name = 'Hobby';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50')
		);
		$this->requeridos = array('nombre');
		$this->unicos = array('nombre');
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
		if ($this->db->where('hobby_id', $delete_id)->count_all_results('rh_datos_extra_hobbies') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Legajo.');
			return FALSE;
		}
		return TRUE;
	}
}