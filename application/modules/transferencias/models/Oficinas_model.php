<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Oficinas_model extends MY_Model
{

	/**
	 * Modelo de Oficinas
	 * Autor: Leandro
	 * Creado: 21/06/2018
	 * Modificado: 21/06/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_oficinas';
		$this->full_log = TRUE;
		$this->msg_name = 'Oficina';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50')
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
		if ($this->db->where('oficina_id', $delete_id)->count_all_results('tr_estados') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Estado.');
			return FALSE;
		}
		if ($this->db->where('oficina_id', $delete_id)->count_all_results('tr_usuarios_oficinas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Usuarios.');
			return FALSE;
		}
		return TRUE;
	}
}