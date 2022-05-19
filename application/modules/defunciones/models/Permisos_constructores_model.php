<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos_constructores_model extends MY_Model
{

	/**
	 * Modelo de Permisos de Constructores
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_permisos_constructores';
		$this->full_log = TRUE;
		$this->msg_name = 'Permiso de Constructor';
		$this->id_name = 'id';
		$this->columnas = array('id', 'constructor_id', 'fecha_pago', 'boleta_pago', 'vencimiento', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'fecha_pago' => array('label' => 'Fecha de Pago', 'type' => 'datetime', 'required' => TRUE),
				'boleta_pago' => array('label' => 'Boleta de Pago', 'maxlength' => '50', 'required' => TRUE),
				'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'required' => TRUE)
		);
		$this->requeridos = array('constructor_id', 'fecha_pago', 'boleta_pago', 'vencimiento');
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
		if ($this->db->where('ultimo_permiso_id', $delete_id)->count_all_results('df_constructores') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Constructor.');
			return FALSE;
		}
		return TRUE;
	}
}