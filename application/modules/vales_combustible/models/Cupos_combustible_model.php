<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cupos_combustible_model extends MY_Model
{

	/**
	 * Modelo de Cupos Combustible
	 * Autor: Leandro
	 * Creado: 25/01/2019
	 * Modificado: 13/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_cupos_combustible';
		$this->full_log = TRUE;
		$this->msg_name = 'Cupo de Combustible';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_combustible_id', 'fecha_inicio', 'metros_cubicos', 'area_id', 'ampliacion', 'ampliacion_vencimiento', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_inicio' => array('label' => 'Fecha Inicio', 'type' => 'date', 'required' => TRUE),
				'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'metros_cubicos' => array('label' => 'M³/Litros Semanal', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'ampliacion' => array('label' => 'M³/Litros Ampliación', 'type' => 'integer', 'maxlength' => '4'),
				'ampliacion_vencimiento' => array('label' => 'Vencimiento Ampliación', 'type' => 'date')
		);
		$this->requeridos = array('tipo_combustible_id', 'fecha_inicio', 'area_id', 'metros_cubicos');
		//$this->unicos = array();
		$this->default_join = array(
				array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_cupos_combustible.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible")),
				array('areas', 'areas.id = vc_cupos_combustible.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
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
		return TRUE;
	}
}