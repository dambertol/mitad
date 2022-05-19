<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expedientes_model extends MY_Model
{

	/**
	 * Modelo de Expedientes
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_expedientes';
		$this->full_log = TRUE;
		$this->msg_name = 'Expediente';
		$this->id_name = 'id';
		$this->columnas = array('id', 'matricula', 'ejercicio', 'numero', 'letra', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'matricula' => array('label' => 'Matricula', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'numero' => array('label' => 'Numero', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'letra' => array('label' => 'Letra', 'maxlength' => '1')
		);
		$this->requeridos = array('matricula', 'ejercicio', 'numero');
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
		if ($this->db->where('expediente_id', $delete_id)->count_all_results('df_operaciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Operación.');
			return FALSE;
		}
		if ($this->db->where('expediente_compra_id', $delete_id)->count_all_results('df_propietarios') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Propietario.');
			return FALSE;
		}
		if ($this->db->where('expediente_construccion_id', $delete_id)->count_all_results('df_propietarios') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Propietario.');
			return FALSE;
		}
		return TRUE;
	}
}