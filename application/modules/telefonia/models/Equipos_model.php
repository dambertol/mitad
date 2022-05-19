<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Equipos_model extends MY_Model
{

	/**
	 * Modelo de Equipos
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 04/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_equipos';
		$this->full_log = TRUE;
		$this->msg_name = 'Equipo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'modelo_id', 'imei', 'estado', 'observaciones', 'area_id', 'labo_Codigo', 'persona', 'accesorios', 'fecha_compra', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'modelo' => array('label' => 'Modelo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'imei' => array('label' => 'IMEI', 'maxlength' => '50', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'required' => TRUE),
				'accesorios' => array('label' => 'Accesorios', 'maxlength' => '50'),
				'fecha_compra' => array('label' => 'Fecha de Compra', 'type' => 'date'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('modelo_id', 'imei', 'estado');
		//$this->unicos = array();
		$this->default_join = array(
				array('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'LEFT', array("tm_modelos.nombre as modelo")),
				array('tm_marcas', 'tm_marcas.id = tm_modelos.marca_id', 'LEFT', array("tm_marcas.nombre as marca")),
				array('personal', 'personal.Legajo = tm_equipos.labo_Codigo', 'LEFT', array("personal.Legajo as dni_personal, CONCAT(personal.Nombre, ' ', personal.Apellido) as nombre_personal")),
				array('areas', 'areas.id = tm_equipos.area_id', 'LEFT', array("areas.nombre as nombre_area")),
				array('tm_lineas', 'tm_lineas.equipo_id = tm_equipos.id', 'LEFT', array("tm_lineas.id as linea_id"))
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
		if ($this->db->where('equipo_id', $delete_id)->count_all_results('tm_lineas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Línea.');
			return FALSE;
		}
		if ($this->db->where('equipo_id', $delete_id)->count_all_results('tm_movimientos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Movimiento.');
			return FALSE;
		}
		return TRUE;
	}
}