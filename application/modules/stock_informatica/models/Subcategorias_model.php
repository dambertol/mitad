<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Subcategorias_model extends MY_Model
{

	/**
	 * Modelo de Subcategorías
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 18/02/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'si_subcategorias';
		$this->full_log = TRUE;
		$this->msg_name = 'Subcategoría';
		$this->id_name = 'id';
		$this->columnas = array('id', 'descripcion', 'categoria_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50', 'required' => TRUE),
				'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
		);
		$this->requeridos = array('descripcion', 'categoria_id');
		$this->unicos = array(array('descripcion', 'categoria_id'));
		$this->default_join = array(
				array('si_categorias', 'si_categorias.id = si_subcategorias.categoria_id', 'LEFT', array("si_categorias.descripcion as categoria"))
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
		if ($this->db->where('subcategoria_id', $delete_id)->count_all_results('si_articulos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Artículo.');
			return FALSE;
		}
		return TRUE;
	}
}