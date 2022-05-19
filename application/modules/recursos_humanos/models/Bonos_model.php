<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bonos_model extends MY_Model
{

	/**
	 * Modelo de Bonos
	 * Autor: Leandro
	 * Creado: 26/02/2020
	 * Modificado: 26/02/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_bonos';
		$this->full_log = TRUE;
		$this->msg_name = 'Bono';
		$this->id_name = 'id';
		$this->columnas = array('id', 'legajo', 'liqu_Anio', 'liqu_Mes', 'liqu_Numero',	'cate_Codigo', 'cate_Descripcion', 'ofi_Oficina', 'ofi_Descripcion', 'codigo', 'fecha', 'envio_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'legajo' => array('label' => 'Legajo', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'liqu_Anio' => array('label' => 'Año', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'liqu_Mes' => array('label' => 'Mes', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'liqu_Numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'cate_Codigo' => array('label' => 'Cod. Categoría', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'cate_Descripcion' => array('label' => 'Categoría', 'maxlength' => '100', 'required' => TRUE),
				'ofi_Oficina' => array('label' => 'Nro. Oficina', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'ofi_Descripcion' => array('label' => 'Oficina', 'maxlength' => '100', 'required' => TRUE),
				'codigo' => array('label' => 'Código', 'maxlength' => '10', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'envio' => array('label' => 'Envio', 'input_type' => 'combo')
		);
		$this->requeridos = array('legajo', 'liqu_Anio', 'liqu_Mes', 'liqu_Numero',	'cate_Codigo', 'cate_Descripcion', 'ofi_Oficina', 'ofi_Descripcion', 'codigo', 'fecha');
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