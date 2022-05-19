<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Denuncias_model extends MY_Model
{

	/**
	 * Modelo de Denuncias
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'an_denuncias';
		$this->full_log = TRUE;
		$this->msg_name = 'Denuncia';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha_denuncia', 'motivo_denuncia', 'fecha_solucion', 'solucion', 'estado', 'torre_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'torre' => array('label' => 'Torre', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_denuncia' => array('label' => 'Fecha Denuncia', 'type' => 'date', 'required' => TRUE),
				'motivo_denuncia' => array('label' => 'Motivo Denuncia', 'required' => TRUE),
				'fecha_solucion' => array('label' => 'Fecha Solución', 'type' => 'date'),
				'solucion' => array('label' => 'Solución'),
				'estado' => array('label' => 'Estado', 'maxlength' => '20', 'required' => TRUE),
		);
		$this->requeridos = array('fecha_denuncia', 'motivo_denuncia', 'estado', 'torre_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('an_torres', 'an_torres.id = an_denuncias.torre_id', 'LEFT', array("an_torres.servicio as torre"))
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