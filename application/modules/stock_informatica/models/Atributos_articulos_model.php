<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Atributos_articulos_model extends MY_Model
{

	/**
	 * Modelo de Atributos artículo
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 18/02/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'si_atributos_articulos';
		$this->full_log = TRUE;
		$this->msg_name = 'Atributo artículo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'articulo_id', 'atributo_id', 'valor', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'articulo' => array('label' => 'Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'atributo' => array('label' => 'Atributo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'valor' => array('label' => 'Valor', 'maxlength' => '255'),
		);
		$this->requeridos = array('articulo_id', 'atributo_id');
		$this->unicos = array(array('articulo_id', 'atributo_id'));
		$this->default_join = array(
				array('si_articulos', 'si_articulos.id = si_atributos_articulos.articulo_id', 'LEFT', array("si_articulos.modelo as articulo")),
				array('si_atributos', 'si_atributos.id = si_atributos_articulos.atributo_id', 'LEFT', array("si_atributos.nombre as atributo"))
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