<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos_model extends MY_Model
{

	/**
	 * Modelo de Artículos
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 01/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_articulos';
		$this->full_log = TRUE;
		$this->msg_name = 'Artículo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'marca', 'tipo_unidad_id', 'tipo_articulo_id', 'cantidad_real', 'nombre', 'ubicacion', 'cantidad_minima', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_articulo' => array('label' => 'Tipo de Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'tipo_unidad' => array('label' => 'Tipo de Unidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '200', 'required' => TRUE),
				'marca' => array('label' => 'Marca', 'maxlength' => '200'),
				'cantidad_minima' => array('label' => 'Cantidad Minima'),
				'ubicacion' => array('label' => 'Ubicación', 'maxlength' => '50'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('tipo_unidad_id', 'tipo_articulo_id', 'nombre');
		//$this->unicos = array();
		$this->default_join = array(
				array('ds_tipos_unidades', 'ds_tipos_unidades.id = ds_articulos.tipo_unidad_id', 'LEFT', array('ds_tipos_unidades.descripcion as tipo_unidad')),
				array('ds_tipos_articulos', 'ds_tipos_articulos.id = ds_articulos.tipo_articulo_id', 'LEFT', array('ds_tipos_articulos.descripcion as tipo_articulo'))
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
		if ($this->db->where('articulo_id', $delete_id)->count_all_results('ds_detalle_compras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Compra.');
			return FALSE;
		}
		if ($this->db->where('articulo_id', $delete_id)->count_all_results('ds_detalle_entregas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Entrega.');
			return FALSE;
		}
		return TRUE;
	}
}