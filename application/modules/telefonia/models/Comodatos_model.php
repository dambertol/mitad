<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Comodatos_model extends MY_Model
{

	/**
	 * Modelo de Comodatos
	 * Autor: Leandro
	 * Creado: 04/09/2019
	 * Modificado: 05/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_comodatos';
		$this->full_log = TRUE;
		$this->msg_name = 'Comodato';
		$this->id_name = 'id';
		$this->columnas = array('id', 'movimiento_id', 'marca', 'modelo', 'imei', 'accesorios', 'persona_equipo', 'area_equipo', 'prestador', 'numero', 'sim', 'min_internacional', 'min_nacional', 'min_interno', 'datos', 'persona_linea', 'area_linea', 'user_id', 'fecha_generacion', 'tipo', 'estado_equipo', 'dni_persona_equipo', 'dni_persona_linea', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array();
		$this->requeridos = array('movimiento_id', 'user_id', 'fecha_generacion', 'tipo');
		//$this->unicos = array();
		$this->default_join = array();
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * delete_comodato_movimiento: Elimina comodato perteneciente al movimiento.
	 *
	 * @param int $movimiento_id
	 * @return bool
	 */
	public function delete_comodato_movimiento($movimiento_id)
	{
		$this->db->where('movimiento_id', $movimiento_id);

		if ($this->db->delete($this->table_name))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
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