<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Areas_model extends MY_Model
{

	/**
	 * Modelo de Áreas
	 * Autor: Leandro 
	 * Creado: 13/11/2017
	 * Modificado: 13/11/2017 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'areas';
		$this->full_log = FALSE;
		$this->msg_name = 'Área';
		$this->id_name = 'id';
		$this->columnas = array('id', 'codigo', 'nombre');
		$this->requeridos = array('codigo', 'nombre');
		$this->unicos = array('codigo', 'nombre');
		$this->default_join = array();
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * _can_delete: Retorna true si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('area_id', $delete_id)->count_all_results('gt_estimaciones_consumo') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no este asociado a una Estimación de consumo.');
			return FALSE;
		}
		if ($this->db->where('area_id', $delete_id)->count_all_results('gt_impresoras_areas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no este asociado a una Impresora.');
			return FALSE;
		}
		if ($this->db->where('area_id', $delete_id)->count_all_results('tm_lineas_fijas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no este asociado a un Línea.');
			return FALSE;
		}
		if ($this->db->where('area_id', $delete_id)->count_all_results('si_movimientos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no este asociado a un Movimiento.');
			return FALSE;
		}
		if ($this->db->where('area_id', $delete_id)->count_all_results('users_areas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Usuario.');
			return FALSE;
		}
		return TRUE;
	}
}