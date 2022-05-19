<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cementerios_model extends MY_Model
{

	/**
	 * Modelo de Cementerios
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_cementerios';
		$this->full_log = TRUE;
		$this->msg_name = 'Cementerio';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'domicilio', 'telefono', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
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
		if ($this->db->where('cementerio_id', $delete_id)->count_all_results('df_ubicaciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Ubicación.');
			return FALSE;
		}
		return TRUE;
	}
}