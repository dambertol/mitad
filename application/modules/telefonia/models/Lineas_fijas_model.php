<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lineas_fijas_model extends MY_Model
{

	/**
	 * Modelo de Líneas Fijas
	 * Autor: Leandro
	 * Creado: 05/09/2019
	 * Modificado: 05/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_lineas_fijas';
		$this->full_log = TRUE;
		$this->msg_name = 'Línea Fija';
		$this->id_name = 'id';
		$this->columnas = array('id', 'linea', 'domicilio', 'observaciones', 'periodo_ini', 'periodo_fin', 'area_id', 'latitud', 'longitud', 'tipo_linea', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'linea' => array('label' => 'Linea', 'type' => 'integer', 'required' => TRUE),
				'tipo_linea' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'tipo_linea', 'required' => TRUE),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '255'),
				'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
				'periodo_ini' => array('label' => 'Inicio', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'periodo_ini', 'required' => TRUE),
				'periodo_fin' => array('label' => 'Fin', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null', 'id_name' => 'periodo_fin')
		);
		$this->requeridos = array('linea', 'periodo_ini', 'tipo_linea');
		//$this->unicos = array();
		$this->default_join = array(
				array('areas', 'areas.id = tm_lineas_fijas.area_id', 'LEFT', array("areas.nombre as area")),
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
		if ($this->db->where('telefono_id', $delete_id)->count_all_results('tm_lineas_fijas_consumos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Consumo.');
			return FALSE;
		}
		return TRUE;
	}
}