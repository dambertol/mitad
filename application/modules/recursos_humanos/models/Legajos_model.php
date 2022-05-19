<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Legajos_model extends MY_Model
{

	/**
	 * Modelo de Legajos
	 * Autor: Leandro
	 * Creado: 02/02/2017
	 * Modificado: 09/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_legajos';
		$this->full_log = TRUE;
		$this->msg_name = 'Legajo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'legajo', 'nombre', 'apellido', 'publico');
		$this->fields = array(
				'legajo' => array('label' => 'Legajo', 'type' => 'integer', 'maxlength' => '8', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
				'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
				'publico' => array('label' => 'Público', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'publico', 'array' => array('SI' => 'SI', 'NO' => 'NO'))
		);
		$this->requeridos = array('legajo', 'publico');
		$this->unicos = array('legajo');
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
		if ($this->db->where('legajo_id', $delete_id)->count_all_results('rh_adjuntos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Documento.');
			return FALSE;
		}
		return TRUE;
	}
}