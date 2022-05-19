<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

	/**
	 * Modelo de Adjuntos
	 * Autor: Leandro
	 * Creado: 29/11/2017
	 * Modificado: 07/06/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 're_adjuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Adjunto';
		$this->id_name = 'id';
		$this->columnas = array('fecha_subida', 'id', 'tamanio', 'usuario_subida', 'resolucion_id', 'nombre', 'ruta', 'hash');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'ruta' => array('label' => 'Archivo *', 'type' => 'file'),
				'tamanio' => array('label' => 'Tamanio', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
		);
		$this->requeridos = array('fecha_subida', 'tamanio', 'usuario_subida', 'resolucion_id', 'nombre', 'ruta', 'hash');
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