<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Detalle_compras_model extends MY_Model
{

	/**
	 * Modelo de Detalles de Compras
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 21/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ob_detalle_compras';
		$this->full_log = TRUE;
		$this->msg_name = 'Detalle de Compra';
		$this->id_name = 'id';
		$this->columnas = array('id', 'compra_id', 'articulo_id', 'cantidad', 'valor', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'compra' => array('label' => 'Compra', 'input_type' => 'combo', 'required' => TRUE),
				'articulo' => array('label' => 'Artículo', 'input_type' => 'combo', 'required' => TRUE),
				'cantidad' => array('label' => 'Cantidad', 'required' => TRUE),
				'valor' => array('label' => 'Valor', 'required' => TRUE)
		);
		$this->requeridos = array('compra_id', 'articulo_id', 'cantidad', 'valor');
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