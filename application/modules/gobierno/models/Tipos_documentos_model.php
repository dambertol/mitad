<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_documentos_model extends MY_Model
{

	/**
	 * Modelo de Tipos de Documentos
	 * Autor: Leandro
	 * Creado: 08/01/2020
	 * Modificado: 08/01/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'go_tipos_documentos';
		$this->full_log = TRUE;
		$this->msg_name = 'Tipo de Documento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'codigo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'codigo' => array('label' => 'Código', 'maxlength' => '10', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE)
		);
		$this->requeridos = array('nombre', 'codigo');
		$this->unicos = array('nombre', 'codigo');
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
		if ($this->db->where('tipo_documento_id', $delete_id)->count_all_results('go_documentos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Documento.');
			return FALSE;
		}
		if ($this->db->where('tipo_documento_id', $delete_id)->count_all_results('go_numeraciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Numeración.');
			return FALSE;
		}
		return TRUE;
	}
}