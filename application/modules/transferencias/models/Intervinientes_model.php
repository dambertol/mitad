<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Intervinientes_model extends MY_Model
{

	/**
	 * Modelo de Intervinientes
	 * Autor: Leandro
	 * Creado: 05/06/2018
	 * Modificado: 12/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_intervinientes';
		$this->full_log = TRUE;
		$this->msg_name = 'Interviniente';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tramite_id', 'tipo', 'porcentaje', 'cuil', 'nombre', 'apellido', 'email', 'domicilio_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'porcentaje' => array('label' => 'Porcentaje', 'type' => 'numeric', 'required' => TRUE),
				'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'maxlength' => '13', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
				'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100')
		);
		$this->requeridos = array('tramite_id', 'porcentaje', 'tipo', 'cuil', 'nombre', 'apellido');
		//$this->unicos = array();
		$this->default_join = array();
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * delete_intervinientes: Elimina intervinientes de un trámite.
	 *
	 * @param int $tramite_id
	 * @param string $tipo
	 * @return bool
	 */
	public function delete_intervinientes($tramite_id, $tipo)
	{
		$this->db->where('tramite_id', $tramite_id);
		$this->db->where('tipo', $tipo);

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