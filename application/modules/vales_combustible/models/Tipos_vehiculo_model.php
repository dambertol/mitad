<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_vehiculo_model extends MY_Model
{

	/**
	 * Modelo de Tipos Vehículo
	 * Autor: Leandro
	 * Creado: 10/07/2018
	 * Modificado: 10/07/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_tipos_vehiculo';
		$this->full_log = TRUE;
		$this->msg_name = 'Tipo de Vehículo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'required' => TRUE)
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
		if ($this->db->where('tipo_vehiculo_id', $delete_id)->count_all_results('vc_vehiculos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vehículo.');
			return FALSE;
		}
		return TRUE;
	}
}