<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos_model extends MY_Model
{

	/**
	 * Modelo de Artículos
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 11/03/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'si_articulos';
		$this->full_log = TRUE;
		$this->msg_name = 'Artículo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'modelo', 'marca_id', 'subcategoria_id', 'numero_serie', 'numero_inventario', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'subcategoria' => array('label' => 'Subcategoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'marca' => array('label' => 'Marca', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'modelo' => array('label' => 'Modelo', 'maxlength' => '100', 'required' => TRUE),
				'numero_serie' => array('label' => 'N° Serie', 'maxlength' => '50'),
				'numero_inventario' => array('label' => 'N° Inventario', 'maxlength' => '50'),
		);
		$this->requeridos = array('modelo', 'marca_id', 'subcategoria_id');
		$this->unicos = array('numero_serie', 'numero_inventario');
		$this->default_join = array(
				array('si_subcategorias', 'si_subcategorias.id = si_articulos.subcategoria_id', 'LEFT', array("si_subcategorias.descripcion as subcategoria")),
				array('si_categorias', 'si_categorias.id = si_subcategorias.categoria_id', 'LEFT', array("si_categorias.id as categoria_id", "si_categorias.descripcion as categoria")),
				array('si_marcas', 'si_marcas.id = si_articulos.marca_id', 'LEFT', array("si_marcas.nombre as marca"))
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
		if ($this->db->where('articulo_id', $delete_id)->count_all_results('si_atributos_articulos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Atributo.');
			return FALSE;
		}
		if ($this->db->where('articulo_id', $delete_id)->count_all_results('si_movimientos_detalle') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Movimiento.');
			return FALSE;
		}
		return TRUE;
	}
}