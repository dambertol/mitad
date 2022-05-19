<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias_model extends MY_Model
{

	/**
	 * Modelo de Categorías
	 * Autor: Leandro
	 * Creado: 02/02/2017
	 * Modificado: 12/06/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_categorias';
		$this->full_log = TRUE;
		$this->msg_name = 'Categoría';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'ruta');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'ruta' => array('label' => 'Ruta', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('nombre', 'ruta');
		$this->unicos = array('nombre', 'ruta');
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
		if ($this->db->where('categoria_id', $delete_id)->count_all_results('rh_documentos_legajo') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Documento.');
			return FALSE;
		}
		return TRUE;
	}
}