<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitantes_model extends MY_Model
{

	/**
	 * Modelo de Solicitantes
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_solicitantes';
		$this->full_log = TRUE;
		$this->msg_name = 'Solicitante';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'dni', 'domicilio', 'telefono', 'email', 'domicilio_alt', 'telefono_alt', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'dni' => array('label' => 'DNI', 'type' => 'natural', 'maxlength' => '11', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '100', 'required' => TRUE),
				'telefono' => array('label' => 'Teléfono', 'maxlength' => '50'),
				'email' => array('label' => 'Email', 'maxlength' => '50'),
				'domicilio_alt' => array('label' => 'Domicilio Alt', 'maxlength' => '100'),
				'telefono_alt' => array('label' => 'Teléfono Alt', 'maxlength' => '50')
		);
		$this->requeridos = array('nombre', 'dni', 'domicilio');
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
		if ($this->db->where('solicitante_id', $delete_id)->count_all_results('df_construcciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Construcción.');
			return FALSE;
		}
		if ($this->db->where('solicitante_id', $delete_id)->count_all_results('df_operaciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Operación.');
			return FALSE;
		}
		if ($this->db->where('solicitante_id', $delete_id)->count_all_results('df_propietarios') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Propietario.');
			return FALSE;
		}
		return TRUE;
	}
}