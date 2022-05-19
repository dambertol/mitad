<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Grupos_model extends MY_Model
{

	/**
	 * Modelo de Grupos
	 * Autor: Leandro
	 * Creado: 27/01/2017
	 * Modificado: 17/03/2017 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'groups';
		$this->full_log = TRUE;
		$this->msg_name = 'Grupo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'name', 'description', 'modulo_id');
		$this->fields = array(
			'name' => array('label' => 'Nombre', 'maxlength' => '40', 'required' => TRUE),
			'description' => array('label' => 'Descripción', 'maxlength' => '100', 'required' => TRUE),
			'modulo' => array('label' => 'Módulo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('name', 'description', 'modulo_id');
		$this->unicos = array('name');
	}

	/**
	 * _can_delete: Devuelve true si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('group_id', $delete_id)->count_all_results('users_groups') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Usuario.');
			return FALSE;
		}
		return TRUE;
	}
}