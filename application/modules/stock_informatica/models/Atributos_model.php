<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Atributos_model extends MY_Model
{

	/**
	 * Modelo de Atributos
	 * Autor: Leandro
	 * Creado: 18/02/2020
	 * Modificado: 18/02/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'si_atributos';
		$this->full_log = TRUE;
		$this->msg_name = 'Atributo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'categoria_id', 'nombre', 'tipo', 'valor_defecto', 'orden', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'categoria' => array('label' => 'Categoria', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'maxlength' => '50', 'id_name' => 'tipo', 'required' => TRUE),
				'valor_defecto' => array('label' => 'Valor por Defecto', 'maxlength' => '255')
		);
		$this->requeridos = array('categoria_id', 'nombre', 'tipo');
		//$this->unicos = array();
		$this->default_join = array(
				array('si_categorias', 'si_categorias.id = si_atributos.categoria_id', 'LEFT', array("si_categorias.descripcion as categoria"))
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
		if ($this->db->where('atributo_id', $delete_id)->count_all_results('si_atributos_articulos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a atributos de un Artículo.');
			return FALSE;
		}
		return TRUE;
	}
}