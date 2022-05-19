<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_oficinas_model extends MY_Model
{

	/**
	 * Modelo de Usuarios por Oficina
	 * Autor: Leandro
	 * Creado: 25/06/2018
	 * Modificado: 12/04/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_usuarios_oficinas';
		$this->full_log = TRUE;
		$this->msg_name = 'Usuario por Oficina';
		$this->id_name = 'id';
		$this->columnas = array('id', 'user_id', 'oficina_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'user' => array('label' => 'Usuario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('user_id', 'oficina_id');
		$this->unicos = array('user_id');
		$this->default_join = array(
				array('users', 'users.id = tr_usuarios_oficinas.user_id', 'LEFT'),
				array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as user")),
				array('tr_oficinas', 'tr_oficinas.id = tr_usuarios_oficinas.oficina_id', 'LEFT', array('tr_oficinas.nombre as oficina'))
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