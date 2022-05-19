<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vehiculos_model extends MY_Model
{

	/**
	 * Modelo de Vehículos
	 * Autor: Leandro
	 * Creado: 17/11/2017
	 * Modificado: 26/11/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_vehiculos';
		$this->full_log = TRUE;
		$this->msg_name = 'Vehículo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'propietario', 'propiedad', 'tipo_vehiculo_id', 'dominio', 'consumo', 'capacidad_tanque', 'consumo_semanal', 'vencimiento_seguro', 'estado', 'observaciones', 'user_id', 'area_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre Vehículo', 'maxlength' => '50', 'required' => TRUE),
				'propiedad' => array('label' => 'Propiedad', 'input_type' => 'combo', 'id_name' => 'propiedad', 'type' => 'bselect', 'required' => TRUE),
				'propietario' => array('label' => 'Propietario', 'maxlength' => '50', 'required' => TRUE),
				'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'tipo_vehiculo' => array('label' => 'Tipo vehículo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'dominio' => array('label' => 'Dominio/Serie', 'maxlength' => '10'),
				'tipo_combustible' => array('label' => 'Tipo combustible', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'required' => TRUE),
				'consumo' => array('label' => 'Consumo c/100 KM', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'capacidad_tanque' => array('label' => 'Capacidad tanque', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'consumo_semanal' => array('label' => 'Consumo semanal', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'vencimiento_seguro' => array('label' => 'Venc. Seguro', 'type' => 'date', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'id_name' => 'estado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('nombre', 'propiedad', 'propietario', 'tipo_vehiculo_id', 'consumo', 'vencimiento_seguro', 'estado', 'user_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('vc_tipos_vehiculo', 'vc_tipos_vehiculo.id = vc_vehiculos.tipo_vehiculo_id', 'LEFT', array("vc_tipos_vehiculo.nombre as tipo_vehiculo")),
				array('areas A', 'A.id = vc_vehiculos.area_id', 'LEFT', array("CONCAT(A.codigo, ' - ', A.nombre) as area")),
				array('users U', 'U.id = vc_vehiculos.user_id', 'LEFT'),
				array('personas P', 'P.id = U.persona_id', 'LEFT', array("CONCAT(P.apellido, ', ', P.nombre) as usuario"))
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
		if ($this->db->where('vehiculo_id', $delete_id)->count_all_results('vc_autorizaciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Autorizacion.');
			return FALSE;
		}
		return TRUE;
	}
}