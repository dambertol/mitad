<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

	/**
	 * Modelo de Adjuntos
	 * Autor: Leandro
	 * Creado: 15/11/2019
	 * Modificado: 15/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'as_adjuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Adjunto';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'ruta' => array('label' => 'Ruta', 'maxlength' => '100', 'required' => TRUE),
				'tamanio' => array('label' => 'Tamaño', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'hash' => array('label' => 'Hash'),
				'fecha_subida' => array('label' => 'Fecha Subida', 'type' => 'date'),
				'usuario_subida' => array('label' => 'Usuario Subida', 'type' => 'integer', 'maxlength' => '10')
		);
		$this->requeridos = array('nombre', 'ruta', 'tamanio');
		$this->unicos = array(array('nombre', 'ruta'));
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