<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hojas_rutas_model extends MY_Model
{

	/**
	 * Modelo de Hojas de Ruta
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'nv_hojas_rutas';
		$this->full_log = TRUE;
		$this->msg_name = 'Hoja de Ruta';
		$this->id_name = 'id';
		$this->columnas = array('id', 'notificador_id', 'estado_id', 'fecha_creacion', 'fecha_limite', 'usuario_id');
		$this->fields = array(
//			'notificador' => array('label' => 'Notificador', 'input_type' => 'combo'),
//			'estado' => array('label' => 'Estado', 'input_type' => 'combo'),
//			'fecha_creacion' => array('label' => 'Fecha de Creacion', 'type' => 'date'),
			'fecha_limite' => array('label' => 'Fecha de Limite', 'type' => 'date'),
//			'usuario' => array('label' => 'Usuario', 'input_type' => 'combo'),
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
		if ($this->db->where('hoja_ruta_id', $delete_id)->count_all_results('nv_cedulas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a cedulas.');
			return FALSE;
		}
		return TRUE;
	}

}