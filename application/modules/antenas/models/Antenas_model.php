<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Antenas_model extends MY_Model
{

	/**
	 * Modelo de Antenas
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'an_antenas';
		$this->full_log = TRUE;
		$this->msg_name = 'Antena';
		$this->id_name = 'id';
		$this->columnas = array('id', 'torre_id', 'descripcion', 'proveedor_id', 'observaciones', 'torre_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'torre' => array('label' => 'Torre', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50', 'required' => TRUE),
				'proveedor' => array('label' => 'Proveedor', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('torre_id', 'descripcion', 'proveedor_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('an_torres', 'an_torres.id = an_antenas.torre_id', 'LEFT', array("an_torres.servicio as torre")),
				array('an_proveedores', 'an_proveedores.id = an_antenas.proveedor_id', 'LEFT', array("an_proveedores.nombre as proveedor"))
		);
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