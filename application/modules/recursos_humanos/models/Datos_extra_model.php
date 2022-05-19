<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Datos_extra_model extends MY_Model
{

	/**
	 * Modelo de Datos Extra
	 * Autor: Leandro
	 * Creado: 12/08/2019
	 * Modificado: 12/08/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_datos_extra';
		$this->full_log = TRUE;
		$this->msg_name = 'Datos Extra';
		$this->id_name = 'id';
		$this->columnas = array('id', 'legajo_id', 'experiencias', 'conformidad_oficina');
		$this->fields = array(
				'hobbies' => array('label' => 'Hobbies', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'id_name' => 'hobbies'),
				'experiencias' => array('label' => 'Experiencias Laborales', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
				'conformidad_oficina' => array('label' => 'Conformidad Oficina Actual', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'conformidad_oficina'),
				'posibles_oficinas' => array('label' => 'Posibles Oficinas', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'id_name' => 'posibles_oficinas')
		);
		$this->requeridos = array('legajo_id');
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
		return FALSE;
	}
}