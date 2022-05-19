<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Valores_combustible_model extends MY_Model
{

	/**
	 * Modelo de Valores Combustible
	 * Autor: Leandro
	 * Creado: 06/11/2017
	 * Modificado: 25/01/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_valores_combustible';
		$this->full_log = TRUE;
		$this->msg_name = 'Valor de Combustible';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha_inicio', 'tipo_combustible_id', 'costo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_combustible' => array('label' => 'Tipo combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_inicio' => array('label' => 'Fecha inicio', 'type' => 'date', 'required' => TRUE),
				'costo' => array('label' => 'Costo', 'type' => 'money', 'required' => TRUE)
		);
		$this->requeridos = array('fecha_inicio', 'tipo_combustible_id', 'costo');
		//$this->unicos = array();
		$this->default_join = array(
				array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_valores_combustible.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible"))
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