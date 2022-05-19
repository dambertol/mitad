<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_resoluciones_model extends MY_Model
{

	/**
	 * Modelo de Tipos de Resolución
	 * Autor: Leandro
	 * Creado: 29/11/2017
	 * Modificado: 12/06/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 're_tipos_resoluciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Tipo de Resolución';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'codigo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'codigo' => array('label' => 'Código', 'maxlength' => '10', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE)
		);
		$this->requeridos = array('nombre', 'codigo');
		$this->unicos = array('nombre', 'codigo');
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
		if ($this->db->where('tipo_resolucion_id', $delete_id)->count_all_results('re_numeraciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Numeración.');
			return FALSE;
		}
		if ($this->db->where('tipo_resolucion_id', $delete_id)->count_all_results('re_resoluciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Resolución.');
			return FALSE;
		}
		return TRUE;
	}
}