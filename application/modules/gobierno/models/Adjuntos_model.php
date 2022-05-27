<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

	/**
	 * Modelo de Adjuntos
	 * Autor: Leandro
	 * Creado: 13/01/2020
	 * Modificado: 13/01/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'go_adjuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Adjunto';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_id', 'nombre', 'descripcion', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida', 'documento_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_adjunto' => array('label' => 'Tipo', 'id_name' => 'tipo_id', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'descripcion' => array('label' => 'Nombre', 'maxlength' => '100'),
				'ruta' => array('label' => 'Ruta', 'maxlength' => '100', 'required' => TRUE),
				'tamanio' => array('label' => 'Tamaño', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'hash' => array('label' => 'Hash', 'required' => TRUE),
				'fecha_subida' => array('label' => 'Fecha Subida', 'type' => 'date', 'required' => TRUE),
				'usuario_subida' => array('label' => 'Usuario Subida', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
				'documento' => array('label' => 'Documento', 'input_type' => 'combo', 'type' => 'bselect')
		);
		$this->requeridos = array('tipo_id', 'nombre', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida');
		$this->unicos = array(array('nombre', 'ruta'));
		$this->default_join = array();
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * delete_adjuntos: Elimina adjuntos de un documento.
	 *
	 * @param int $documento_id
	 * @return bool
	 */
	public function delete_adjuntos($documento_id)
	{
		$this->db->where('documento_id', $documento_id);

		if ($this->db->delete($this->table_name))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
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