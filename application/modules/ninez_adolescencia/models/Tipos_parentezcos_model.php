<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_parentezcos_model extends MY_Model
{

	/**
	 * Modelo de Tipos de Parentezco
	 * Autor: Leandro
	 * Creado: 09/09/2019
	 * Modificado: 09/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_tipos_parentezcos';
		$this->full_log = TRUE;
		$this->msg_name = 'Tipo de Parentezco';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('nombre');
		$this->unicos = array('nombre');
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
		if ($this->db->where('tipo_parentezco_id', $delete_id)->count_all_results('na_parentezcos_personas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Persona.');
			return FALSE;
		}
		return TRUE;
	}
}