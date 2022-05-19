<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lugares_model extends MY_Model
{

	/**
	 * Modelo de Lugares
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_lugares';
		$this->full_log = TRUE;
		$this->msg_name = 'Lugar';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_lugar_id', 'descripcion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_lugar' => array('label' => 'Tipo de Lugar', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '100', 'required' => TRUE)
		);
		$this->requeridos = array('tipo_lugar_id', 'descripcion');
		//$this->unicos = array();
		$this->default_join = array(
				array('ds_tipos_lugares', 'ds_tipos_lugares.id = ds_lugares.tipo_lugar_id', 'LEFT', array('ds_tipos_lugares.descripcion as tipo_lugar'))
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
		if ($this->db->where('lugar_id', $delete_id)->count_all_results('ds_beneficiarios') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Beneficiario.');
			return FALSE;
		}
		return TRUE;
	}
}