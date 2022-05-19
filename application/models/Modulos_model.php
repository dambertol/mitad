<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Modulos_model extends MY_Model
{

	/**
	 * Modelo de Modulos
	 * Autor: Leandro
	 * Creado: 17/03/2017
	 * Modificado: 17/03/2017 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'modulos';
		$this->full_log = TRUE;
		$this->msg_name = 'Módulo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'limite_seleccion', 'icono');
		$this->fields = array(
			'nombre' => array('label' => 'Nombre', 'maxlength' => '40', 'required' => TRUE),
			'limite_seleccion' => array('label' => 'Límite selección', 'type' => 'integer', 'required' => TRUE),
			'icono' => array('label' => 'Icono', 'maxlength' => '40', 'required' => TRUE)
		);
		$this->requeridos = array('nombre', 'limite_seleccion', 'icono');
		$this->unicos = array('nombre');
	}

	/**
	 * _can_delete: Devuelve true si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('modulo_id', $delete_id)->count_all_results('groups') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Grupo.');
			return FALSE;
		}
		return TRUE;
	}
}