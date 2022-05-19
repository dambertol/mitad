<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

	/**
	 * Modelo de Adjuntos
	 * Autor: Leandro
	 * Creado: 15/08/2018
	 * Modificado: 17/122018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'nv_adjuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Adjunto';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_id', 'nombre', 'descripcion', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida', 'cedula_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_adjunto' => array('label' => 'Tipo', 'id_name' => 'tipo_id', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'descripcion' => array('label' => 'Nombre', 'maxlength' => '100'),
				'ruta' => array('label' => 'Ruta', 'maxlength' => '100', 'required' => TRUE),
				'tamanio' => array('label' => 'Tamaño', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'hash' => array('label' => 'Hash', 'required' => TRUE),
				'fecha_subida' => array('label' => 'Fecha Subida', 'type' => 'date', 'required' => TRUE),
				'usuario_subida' => array('label' => 'Usuario Subida', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
				'cedula' => array('label' => 'Cedula', 'input_type' => 'combo', 'type' => 'bselect')
		);
		$this->requeridos = array('tipo_id', 'nombre', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida');
		$this->unicos = array(array('nombre', 'ruta'));
		$this->default_join = array(
				array('nv_tipos_adjuntos', 'nv_tipos_adjuntos.id = nv_adjuntos.tipo_id', 'LEFT',
						array(
								'nv_tipos_adjuntos.nombre as tipo_adjunto'
						)
		));
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * delete_adjuntos: Elimina adjuntos de un vehículo.
	 *
	 * @param int $cedula_id
	 * @return bool
	 */
	public function delete_adjuntos($cedula_id)
	{
		$this->db->where('cedula_id', $cedula_id);

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