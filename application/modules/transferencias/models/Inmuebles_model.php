<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inmuebles_model extends MY_Model
{

	/**
	 * Modelo de Inmuebles
	 * Autor: Leandro
	 * Creado: 05/06/2018
	 * Modificado: 10/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_inmuebles';
		$this->full_log = TRUE;
		$this->msg_name = 'Inmueble';
		$this->id_name = 'id';
		$this->columnas = array('id', 'padron', 'nomenclatura', 'sup_titulo', 'sup_mensura', 'sup_afectada', 'sup_cubierta', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'padron' => array('label' => 'Padrón municipal', 'type' => 'integer', 'maxlength' => '6', 'required' => TRUE),
				'nomenclatura' => array('label' => 'Nomenclatura', 'type' => 'natural', 'maxlength' => '25', 'required' => TRUE),
				'sup_titulo' => array('label' => 'Superficie Título', 'type' => 'numeric'),
				'sup_mensura' => array('label' => 'Superficie Mensura', 'type' => 'numeric'),
				'sup_afectada' => array('label' => 'Superficie Afectada', 'type' => 'numeric'),
				'sup_cubierta' => array('label' => 'Superficie Cubierta', 'type' => 'numeric')
		);
		$this->requeridos = array('padron', 'nomenclatura');
		//$this->unicos = array();
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
		if ($this->db->where('inmueble_id', $delete_id)->count_all_results('tr_tramites') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Trámite.');
			return FALSE;
		}
		return TRUE;
	}
}