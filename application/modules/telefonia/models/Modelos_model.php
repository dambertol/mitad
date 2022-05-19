<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Modelos_model extends MY_Model
{

	/**
	 * Modelo de Modelos
	 * Autor: Leandro
	 * Creado: 02/09/2019
	 * Modificado: 02/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_modelos';
		$this->full_log = TRUE;
		$this->msg_name = 'Modelo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'marca_id', 'categoria_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'marca' => array('label' => 'Marca', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('nombre', 'marca_id', 'categoria_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('tm_marcas', 'tm_marcas.id = tm_modelos.marca_id', 'LEFT', array("tm_marcas.nombre as marca")),
				array('tm_categorias', 'tm_categorias.id = tm_modelos.categoria_id', 'LEFT', array("tm_categorias.descripcion as categoria"))
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
		if ($this->db->where('modelo_id', $delete_id)->count_all_results('tm_equipos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Equipo.');
			return FALSE;
		}
		return TRUE;
	}
}