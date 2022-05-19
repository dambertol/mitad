<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_areas_model extends MY_Model
{

	/**
	 * Modelo de Usuarios por Area
	 * Autor: Leandro
	 * Creado: 16/03/2020
	 * Modificado: 17/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'to_usuarios_areas';
		$this->full_log = TRUE;
		$this->msg_name = 'Usuario por Area';
		$this->id_name = 'id';
		$this->columnas = array('id', 'user_id', 'area_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'user' => array('label' => 'Usuario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('user_id', 'area_id');
		$this->unicos = array();
		$this->default_join = array(
				array('users', 'users.id = to_usuarios_areas.user_id', 'LEFT'),
				array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as user")),
				array('areas', 'areas.id = to_usuarios_areas.area_id', 'LEFT', array('CONCAT(areas.codigo, \' - \', areas.nombre) as area'))
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
		return TRUE;
	}
}