<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas_devoluciones_tipo_model extends MY_Model
{

	/**
	 * Modelo de Tipos de Devolución
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'nv_cedulas_devoluciones_tipo';
		$this->full_log = TRUE;
		$this->msg_name = 'Tipos de Devolución';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion');//, 'audi_usuario', 'audi_fecha', 'audi_accion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
			'descripcion' => array('label' => 'Descripcion', 'maxlength' => '50'),
//			'audi_usuario' => array('label' => 'Audi de Usuario', 'type' => 'integer', 'maxlength' => '11'),
//			'audi_fecha' => array('label' => 'Audi de Fecha', 'type' => 'date'),
//			'audi_accion' => array('label' => 'Audi de Accion', 'maxlength' => '1')
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
		if ($this->db->where('tipo_devolucion_id', $delete_id)->count_all_results('nv_cedulas_devoluciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a cedulas de devoluciones.');
			return FALSE;
		}
		return TRUE;
	}
}