<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Domicilios_model extends MY_Model
{

	/**
	 * Modelo de Domicilios
	 * Autor: Leandro
	 * Creado: 23/05/2018
	 * Modificado: 30/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'domicilios';
		$this->full_log = TRUE;
		$this->msg_name = 'Domicilio';
		$this->id_name = 'id';
		$this->columnas = array('id', 'calle', 'barrio', 'altura', 'piso', 'dpto', 'manzana', 'casa', 'localidad_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'calle' => array('label' => 'Calle', 'maxlength' => '50', 'required' => TRUE),
				'barrio' => array('label' => 'Barrio', 'maxlength' => '50'),
				'altura' => array('label' => 'Altura', 'maxlength' => '10', 'required' => TRUE),
				'piso' => array('label' => 'Piso', 'maxlength' => '10'),
				'dpto' => array('label' => 'Dpto', 'maxlength' => '10'),
				'manzana' => array('label' => 'Manzana', 'maxlength' => '10'),
				'casa' => array('label' => 'Casa', 'maxlength' => '10'),
				'localidad' => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('localidad_id', 'calle', 'altura');
		//$this->unicos = array();
		$this->default_join = array(
				array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
				array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
				array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT', array("CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"))
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
		if ($this->db->where('domicilio_id', $delete_id)->count_all_results('personas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Persona.');
			return FALSE;
		}
		if ($this->db->where('domicilio_id', $delete_id)->count_all_results('tr_intervinientes') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Comprador/Vendedor.');
			return FALSE;
		}
		if ($this->db->where('domicilio_id', $delete_id)->count_all_results('na_efectores') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Efector.');
			return FALSE;
		}
		if ($this->db->where('domicilio_id', $delete_id)->count_all_results('act_actas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Acta de ISP.');
			return FALSE;
		}
		return TRUE;
	}
}