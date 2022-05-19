<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Autorizaciones_model extends MY_Model
{

	/**
	 * Modelo de Autorizaciones
	 * Autor: Leandro
	 * Creado: 17/11/2017
	 * Modificado: 27/09/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_autorizaciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Autorización';
		$this->id_name = 'id';
		$this->columnas = array('id', 'vehiculo_id', 'tipo_combustible_id', 'lleno', 'litros_autorizados', 'fecha_autorizacion', 'autoriza_id', 'persona_id', 'persona_nombre', 'fecha_carga', 'litros_cargados', 'carga_id', 'observaciones', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'vehiculo' => array('label' => 'Vehiculo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'lleno' => array('label' => 'Tanque Lleno', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE, 'id_name' => 'lleno'),
				'litros_autorizados' => array('label' => 'Litros Autorizados', 'type' => 'numeric', 'required' => TRUE),
				'fecha_autorizacion' => array('label' => 'Fecha Autorización', 'type' => 'date', 'required' => TRUE),
				'persona' => array('label' => 'Legajo Chofer', 'type' => 'integer', 'required' => TRUE),
				'persona_major' => array('label' => 'Chofer', 'disabled' => TRUE),
				'persona_nombre' => array('label' => 'Chofer Externo', 'maxlength' => '50'),
				'observaciones' => array('label' => 'Observaciones', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5)
		);
		$this->requeridos = array('vehiculo_id', 'litros_autorizados', 'lleno', 'fecha_autorizacion', 'persona_id', 'autoriza_id', 'estado');
		//$this->unicos = array();
		$this->default_join = array(
				array('vc_vehiculos', 'vc_vehiculos.id = vc_autorizaciones.vehiculo_id', 'LEFT', array("CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(vc_vehiculos.dominio, 'SIN DOMINIO/SERIE') , ' - ', vc_vehiculos.propiedad) as vehiculo")),
				array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_autorizaciones.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible")),
				array('users UA', 'UA.id = vc_autorizaciones.autoriza_id', 'LEFT'),
				array('personas PA', 'PA.id = UA.persona_id', 'LEFT', array("CONCAT(PA.apellido, ', ', PA.nombre) as usuario")),
				array('users UC', 'UC.id = vc_autorizaciones.carga_id', 'LEFT'),
				array('personas PC', 'PC.id = UA.persona_id', 'LEFT', array("CONCAT(PC.apellido, ', ', PC.nombre) as usuario_carga"))
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
		return TRUE;
	}
}