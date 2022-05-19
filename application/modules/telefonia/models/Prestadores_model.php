<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Prestadores_model extends MY_Model
{

	/**
	 * Modelo de Prestadores
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 02/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_prestadores';
		$this->full_log = TRUE;
		$this->msg_name = 'Prestador';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('nombre');
		$this->unicos = array('nombre');
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
		if ($this->db->where('prestador_id', $delete_id)->count_all_results('tm_lineas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Línea.');
			return FALSE;
		}
		return TRUE;
	}
}