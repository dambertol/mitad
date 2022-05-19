<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_entregas_model extends MY_Model
{

	/**
	 * Modelo de Detalles de Entregas
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_detalle_entregas';
		$this->full_log = TRUE;
		$this->msg_name = 'Detalles de Entregas';
		$this->id_name = 'id';
		$this->columnas = array('id', 'entrega_id', 'articulo_id', 'cantidad', 'expediente', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'entrega' => array('label' => 'Entrega', 'input_type' => 'combo', 'required' => TRUE),
				'articulo' => array('label' => 'Articulo', 'input_type' => 'combo', 'required' => TRUE),
				'cantidad' => array('label' => 'Cantidad', 'required' => TRUE),
				'expediente' => array('label' => 'Expediente', 'maxlength' => '50')
		);
		$this->requeridos = array('entrega_id', 'articulo_id', 'cantidad');
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