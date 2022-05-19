<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hojas_rutas_estados_model extends MY_Model
{

	/**
	 * Modelo de Estados Hoja de Ruta
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'nv_hojas_rutas_estados';
		$this->full_log = TRUE;
		$this->msg_name = 'Estado Hoja de Ruta';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'audi_usuario', 'audi_fecha', 'audi_accion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
			'descripcion' => array('label' => 'Descripcion', 'maxlength' => '50'),
			'audi_usuario' => array('label' => 'Audi de Usuario', 'type' => 'integer', 'maxlength' => '11'),
			'audi_fecha' => array('label' => 'Audi de Fecha', 'type' => 'date'),
			'audi_accion' => array('label' => 'Audi de Accion', 'maxlength' => '1')
		);
		$this->requeridos = array();
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
		if ($this->db->where('estado_id', $delete_id)->count_all_results('nv_hojas_rutas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a hojas de rutas.');
			return FALSE;
		}
		return TRUE;
	}
}