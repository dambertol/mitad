<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos_model extends MY_Model
{

	/**
	 * Modelo de Movimientos
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 03/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'si_movimientos';
		$this->full_log = TRUE;
		$this->msg_name = 'Movimiento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'tipo', 'descripcion', 'area_id', 'persona_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect'),
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '255')
		);
		$this->requeridos = array('fecha', 'tipo', 'area_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('areas', 'areas.id = si_movimientos.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area")),
				array('personas', 'personas.id = si_movimientos.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as persona"))
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
		if ($this->db->where('movimiento_id', $delete_id)->count_all_results('si_movimientos_detalle') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}