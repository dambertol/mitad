<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos_model extends MY_Model
{

	/**
	 * Modelo de Artículos
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 28/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ob_articulos';
		$this->full_log = TRUE;
		$this->msg_name = 'Artículo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'marca', 'descripcion', 'tipo_unidad_id', 'tipo_articulo_id', 'medida', 'modelo', 'destino', 'cant_real', 'nombre', 'caracteristica', 'estado', 'ubicacion', 'medida_alto', 'medida_frente', 'medida_costado', 'fecha', 'cant_minima', 'valor', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_unidad' => array('label' => 'Tipo de Unidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'tipo_articulo' => array('label' => 'Tipo de Artículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date'),
				'cant_minima' => array('label' => 'Cant Mínima'),
				'marca' => array('label' => 'Marca', 'maxlength' => '50'),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50'),
				'valor' => array('label' => 'Valor'),
				'caracteristica' => array('label' => 'Característica', 'maxlength' => '50'),
				'destino' => array('label' => 'Destino', 'maxlength' => '50', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'maxlength' => '50'),
				'ubicacion' => array('label' => 'Ubicacion', 'maxlength' => '50'),
				'modelo' => array('label' => 'Modelo', 'maxlength' => '50'),
				'medida' => array('label' => 'Medida', 'maxlength' => '50'),
				'medida_costado' => array('label' => 'Medida de Costado', 'maxlength' => '50'),
				'medida_alto' => array('label' => 'Medida de Alto', 'maxlength' => '50'),
				'medida_frente' => array('label' => 'Medida de Frente', 'maxlength' => '50')
		);
		$this->requeridos = array('tipo_unidad_id', 'tipo_articulo_id', 'nombre', 'destino');
		//$this->unicos = array();
		$this->default_join = array(
				array('ob_tipos_unidades', 'ob_tipos_unidades.id = ob_articulos.tipo_unidad_id', 'LEFT', array('ob_tipos_unidades.descripcion as tipo_unidad')),
				array('ob_tipos_articulos', 'ob_tipos_articulos.id = ob_articulos.tipo_articulo_id', 'LEFT', array('ob_tipos_articulos.descripcion as tipo_articulo'))
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
		if ($this->db->where('articulo_id', $delete_id)->count_all_results('ob_detalle_compras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Compra.');
			return FALSE;
		}
		if ($this->db->where('articulo_id', $delete_id)->count_all_results('ob_detalle_entregas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Entrega.');
			return FALSE;
		}
		return TRUE;
	}
}