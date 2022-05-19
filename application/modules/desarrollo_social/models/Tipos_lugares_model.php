<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_lugares_model extends MY_Model
{

	/**
	 * Modelo de Tipos de Lugares
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_tipos_lugares';
		$this->full_log = TRUE;
		$this->msg_name = 'Tipo de Lugar';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('descripcion');
		//$this->unicos = array('descripcion');
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
		if ($this->db->where('tipo_lugar_id', $delete_id)->count_all_results('ds_lugares') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Lugar.');
			return FALSE;
		}
		return TRUE;
	}
}