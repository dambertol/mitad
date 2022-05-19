<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lineas_fijas_consumos_model extends MY_Model
{

	/**
	 * Modelo de Consumos Líneas Fijas
	 * Autor: Leandro
	 * Creado: 05/09/2019
	 * Modificado: 05/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_lineas_fijas_consumos';
		$this->full_log = TRUE;
		$this->msg_name = 'Consumo Línea Fija';
		$this->id_name = 'id';
		$this->columnas = array('id', 'periodo', 'telefono_id', 'monto', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array();
		$this->requeridos = array('periodo', 'telefono_id', 'estado');
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
		return TRUE;
	}
}