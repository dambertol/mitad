<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Torres_model extends MY_Model
{

	/**
	 * Modelo de Torres
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'an_torres';
		$this->full_log = TRUE;
		$this->msg_name = 'Torre';
		$this->id_name = 'id';
		$this->columnas = array('id', 'servicio', 'caracteristicas', 'observaciones', 'padron', 'calle', 'latitud', 'longitud', 'zonificacion', 'entorno', 'proveedor_id', 'distrito_id', 'estado', 'expediente_ejercicio', 'expediente_numero', 'ordenanza_1', 'ordenanza_2', 'ordenanza_3', 'nomenclatura', 'sitio', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'sitio' => array('label' => 'Sitio', 'maxlength' => '20'),
				'expediente_numero' => array('label' => 'Expediente Número', 'type' => 'integer', 'maxlength' => '11'),
				'expediente_ejercicio' => array('label' => 'Expediente Ejercicio', 'type' => 'integer', 'maxlength' => '11'),
				'servicio' => array('label' => 'Servicio', 'maxlength' => '50', 'required' => TRUE),
				'proveedor' => array('label' => 'Proveedor', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'calle' => array('label' => 'Calle', 'maxlength' => '100'),
				'distrito' => array('label' => 'Distrito', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'padron' => array('label' => 'Padrón', 'maxlength' => '50'),
				'nomenclatura' => array('label' => 'Nomenclatura', 'maxlength' => '40'),
				'latitud' => array('label' => 'Latitud', 'maxlength' => '25', 'readonly' => TRUE),
				'longitud' => array('label' => 'Longitud', 'maxlength' => '25', 'readonly' => TRUE),
				'zonificacion' => array('label' => 'Zonificación', 'maxlength' => '50'),
				'entorno' => array('label' => 'Entorno', 'maxlength' => '50'),
				'estado' => array('label' => 'Estado', 'maxlength' => '20', 'input_type' => 'combo', 'id_name' => 'estado', 'type' => 'bselect', 'required' => TRUE),
				'caracteristicas' => array('label' => 'Características', 'maxlength' => '255', 'required' => TRUE),
				'ordenanza_1' => array('label' => 'Ordenanza 1704/00', 'maxlength' => '255'),
				'ordenanza_2' => array('label' => 'Ordenanza 3935/04', 'maxlength' => '255'),
				'ordenanza_3' => array('label' => 'Ordenanza 12971/12', 'maxlength' => '255'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('servicio', 'caracteristicas', 'calle', 'distrito_id', 'proveedor_id', 'estado');
		//$this->unicos = array();
		$this->default_join = array(
				array('an_proveedores', 'an_proveedores.id = an_torres.proveedor_id', 'LEFT', array("an_proveedores.nombre as proveedor")),
				array('localidades', 'localidades.id = an_torres.distrito_id', 'LEFT', array("localidades.nombre as distrito"))
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
		if ($this->db->where('torre_id', $delete_id)->count_all_results('an_antenas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Antena.');
			return FALSE;
		}
		if ($this->db->where('torre_id', $delete_id)->count_all_results('an_denuncias') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Denuncia.');
			return FALSE;
		}
		if ($this->db->where('torre_id', $delete_id)->count_all_results('an_habilitaciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Habilitación.');
			return FALSE;
		}
		return TRUE;
	}
}