<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos_model extends MY_Model
{

	/**
	 * Modelo de Movimientos
	 * Autor: Leandro
	 * Creado: 03/09/2019
	 * Modificado: 05/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_movimientos';
		$this->full_log = TRUE;
		$this->msg_name = 'Movimiento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'equipo_id', 'fecha', 'tipo', 'labo_Codigo', 'area_id', 'linea_id', 'min_internacional', 'min_nacional', 'min_interno', 'datos', 'observaciones', 'persona', 'estado_equipo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'linea' => array('label' => 'Linea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'equipo' => array('label' => 'Equipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'persona_externa' => array('label' => 'Persona Externa', 'maxlength' => '50'),
				'min_internacional' => array('label' => 'Minutos Internacional', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'min_nacional' => array('label' => 'Minutos Nacional', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'min_interno' => array('label' => 'Minutos Interno', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'datos' => array('label' => 'Datos', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('fecha', 'tipo');
		//$this->unicos = array();
		$this->default_join = array(
				array('tm_lineas', 'tm_lineas.id = tm_movimientos.linea_id', 'LEFT', array("tm_lineas.numero as linea", "tm_lineas.persona as persona_externa_linea")),
				array('tm_equipos', 'tm_equipos.id = tm_movimientos.equipo_id', 'LEFT', array("tm_equipos.persona as persona_externa_equipo")),
				array('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'LEFT', array("CONCAT(tm_modelos.nombre, ' - ', tm_equipos.imei) as equipo")),
				array('personal PL', 'PL.Legajo = tm_lineas.labo_Codigo', 'LEFT', array("PL.Legajo as dni_personal, CONCAT(PL.Nombre, ' ', PL.Apellido) as persona_linea")),
				array('areas AL', 'AL.id = tm_lineas.area_id', 'LEFT', array("AL.nombre as area_linea")),
				array('personal PE', 'PE.Legajo = tm_equipos.labo_Codigo', 'LEFT', array("PE.Legajo as dni_personal, CONCAT(PE.Nombre, ' ', PE.Apellido) as persona_equipo")),
				array('areas AE', 'AE.id = tm_equipos.area_id', 'LEFT', array("AE.nombre as area_equipo"))
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
		if ($this->db->where('movimiento_id', $delete_id)->count_all_results('tm_comodatos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Comodato.');
			return FALSE;
		}
		return TRUE;
	}
}