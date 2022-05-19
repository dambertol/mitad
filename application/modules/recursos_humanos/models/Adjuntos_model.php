<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

	/**
	 * Modelo de Documentos
	 * Autor: Leandro
	 * Creado: 20/02/2017
	 * Modificado: 09/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_adjuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Documento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'legajo_id', 'categoria_id', 'fecha_presentacion', 'nombre', 'descripcion', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida');
		$this->fields = array(
				'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_presentacion' => array('label' => 'Presentación', 'type' => 'date', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'descripcion' => array('label' => 'Descripción', 'required' => TRUE),
				'ruta' => array('label' => 'Archivo *', 'type' => 'file'),
				'tamanio' => array('label' => 'Tamaño', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'fecha_subida' => array('label' => 'Fecha Carga', 'type' => 'datetime', 'required' => TRUE),
				'usuario_subida' => array('label' => 'Usuario Carga', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'usuario_subida', 'required' => TRUE)
		);
		$this->requeridos = array('legajo_id', 'categoria_id', 'fecha_presentacion', 'nombre', 'descripcion', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida');
		$this->unicos = array(array('nombre', 'ruta'));
		$this->default_join = array(
				array('rh_categorias', 'rh_categorias.id = rh_adjuntos.categoria_id', 'LEFT', array("rh_categorias.nombre as categoria")),
				array('rh_legajos', 'rh_legajos.id = rh_adjuntos.legajo_id', 'LEFT', array("rh_legajos.publico as publico")),
				array('users', 'users.id = rh_adjuntos.usuario_subida', 'LEFT'),
				array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario_subida"))
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