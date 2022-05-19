<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

	/**
	 * Modelo de Adjuntos
	 * Autor: Leandro
	 * Creado: 10/09/2019
	 * Modificado: 20/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_adjuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Adjunto';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_id', 'nombre', 'descripcion', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida', 'expediente_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo' => array('label' => 'Tipo', 'id_name' => 'tipo_id', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50', 'required' => TRUE),
				'path' => array('label' => 'Archivo *', 'type' => 'file')
		);
		$this->requeridos = array('tipo_id', 'nombre', 'descripcion', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida');
		$this->unicos = array(array('nombre', 'ruta'));
		$this->default_join = array(array('na_tipos_adjuntos', 'na_tipos_adjuntos.id = na_adjuntos.tipo_id', 'LEFT', array('na_tipos_adjuntos.nombre as tipo')));
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * delete_adjuntos: Elimina adjuntos de un expediente.
	 *
	 * @param int $expediente_id
	 * @return bool
	 */
	public function delete_adjuntos($expediente_id)
	{
		$this->db->where('expediente_id', $expediente_id);

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