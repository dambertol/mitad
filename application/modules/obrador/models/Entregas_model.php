<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entregas_model extends MY_Model
{

	/**
	 * Modelo de Entregas
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 28/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ob_entregas';
		$this->full_log = TRUE;
		$this->msg_name = 'Entrega';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'descripcion', 'destino', 'responsable', 'expediente', 'transportista', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'responsable' => array('label' => 'Responsable', 'maxlength' => '50', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'destino' => array('label' => 'Destino', 'maxlength' => '50', 'required' => TRUE),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50'),
				'expediente' => array('label' => 'Expediente', 'maxlength' => '50')
		);
		$this->requeridos = array('fecha', 'responsable', 'destino');
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
		if ($this->db->where('entrega_id', $delete_id)->count_all_results('ob_detalle_entregas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}