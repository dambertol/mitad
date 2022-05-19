<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Motivos_model extends MY_Model
{

	/**
	 * Modelo de Motivos
	 * Autor: Leandro
	 * Creado: 24/10/2019
	 * Modificado: 24/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'act_motivos';
		$this->full_log = TRUE;
		$this->msg_name = 'Motivo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'codigo', 'motivo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'codigo' => array('label' => 'Código', 'type' => 'integer', 'maxlength' => '3', 'required' => TRUE),
				'motivo' => array('label' => 'Motivo', 'maxlength' => '45', 'required' => TRUE)
		);
		$this->requeridos = array('codigo', 'motivo');
		$this->unicos = array('codigo', 'motivo');
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
		if ($this->db->where('motivo_id', $delete_id)->count_all_results('act_actas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Acta.');
			return FALSE;
		}
		return TRUE;
	}
}