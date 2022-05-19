<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Constructores_model extends MY_Model
{

	/**
	 * Modelo de Constructores
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_constructores';
		$this->full_log = TRUE;
		$this->msg_name = 'Constructor';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'dni', 'domicilio', 'telefono', 'ultimo_permiso_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'dni' => array('label' => 'DNI', 'type' => 'natural', 'maxlength' => '11'),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '100'),
				'telefono' => array('label' => 'Teléfono', 'maxlength' => '50')
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
		if ($this->db->where('constructor_id', $delete_id)->count_all_results('df_compras_terrenos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Compra de terreno.');
			return FALSE;
		}
		if ($this->db->where('constructor_id', $delete_id)->count_all_results('df_ornatos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Ornato.');
			return FALSE;
		}
		if ($this->db->where('constructor_id', $delete_id)->count_all_results('df_permisos_constructores') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Permiso.');
			return FALSE;
		}
		return TRUE;
	}
}