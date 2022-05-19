<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Intervenciones_detalles_model extends MY_Model
{

	/**
	 * Modelo de Detalles de Intervención
	 * Autor: Leandro
	 * Creado: 13/09/2019
	 * Modificado: 16/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_intervenciones_detalles';
		$this->full_log = TRUE;
		$this->msg_name = 'Detalle de Intervención';
		$this->id_name = 'id';
		$this->columnas = array('id', 'intervencion_id', 'efector_id', 'motivo_id', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'efector' => array('label' => 'Efector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'motivo' => array('label' => 'Motivo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('intervencion_id', 'efector_id', 'motivo_id');
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
		return TRUE;
	}
}