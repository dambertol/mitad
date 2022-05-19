<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Impresoras_model extends MY_Model
{

	/**
	 * Modelo de Impresoras
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 07/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_impresoras';
		$this->full_log = TRUE;
		$this->msg_name = 'Impresora';
		$this->id_name = 'id';
		$this->columnas = array('id', 'marca_id', 'modelo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'marca' => array('label' => 'Marca', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'modelo' => array('label' => 'Modelo', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('marca_id', 'modelo');
		$this->unicos = array('modelo');
		$this->default_join = array(
				array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT', array("gt_marcas.nombre as marca"))
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
		if ($this->db->where('impresora_id', $delete_id)->count_all_results('gt_consumibles_impresoras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Consumible.');
			return FALSE;
		}
		if ($this->db->where('impresora_id', $delete_id)->count_all_results('gt_impresoras_areas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Área.');
			return FALSE;
		}
		return TRUE;
	}
}