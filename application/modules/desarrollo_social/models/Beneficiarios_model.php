<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Beneficiarios_model extends MY_Model
{

	/**
	 * Modelo de Beneficiarios
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 28/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_beneficiarios';
		$this->full_log = TRUE;
		$this->msg_name = 'Beneficiario';
		$this->id_name = 'id';
		$this->columnas = array('id', 'dni', 'apellido', 'nombre', 'telefono', 'domicilio', 'localidad', 'lugar_id', 'nro_apros', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'dni' => array('label' => 'DNI', 'type' => 'natural', 'minlength' => '7', 'maxlength' => '8', 'required' => TRUE),
				'apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'telefono' => array('label' => 'Teléfono', 'maxlength' => '15'),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '50', 'required' => TRUE),
				'localidad' => array('label' => 'Localidad', 'maxlength' => '50', 'required' => TRUE),
				'lugar' => array('label' => 'Lugar', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nro_apros' => array('label' => 'Nro Apros', 'maxlength' => '10'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
		);
		$this->requeridos = array('dni', 'apellido', 'nombre', 'domicilio', 'localidad', 'lugar_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('ds_lugares', 'ds_lugares.id = ds_beneficiarios.lugar_id', 'LEFT', array('ds_lugares.descripcion as lugar'))
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
		if ($this->db->where('beneficiario_id', $delete_id)->count_all_results('ds_entregas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Entrega.');
			return FALSE;
		}
		return TRUE;
	}
}