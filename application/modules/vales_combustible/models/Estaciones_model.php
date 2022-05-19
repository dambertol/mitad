<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estaciones_model extends MY_Model
{

	/**
	 * Modelo de Estaciones
	 * Autor: Leandro
	 * Creado: 03/11/2017
	 * Modificado: 13/08/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_estaciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Estación';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE)
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
		if ($this->db->where('estacion_id', $delete_id)->count_all_results('vc_vales') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vale.');
			return FALSE;
		}
		if ($this->db->where('estacion_id', $delete_id)->count_all_results('vc_vales_semanales') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vale Semanal.');
			return FALSE;
		}
		if ($this->db->where('estacion_id', $delete_id)->count_all_results('vc_tipos_combustible') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a Tipo de Combustible.');
			return FALSE;
		}
		return TRUE;
	}
}