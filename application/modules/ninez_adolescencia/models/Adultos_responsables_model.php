<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adultos_responsables_model extends MY_Model
{

	/**
	 * Modelo de Adultos Responsables
	 * Autor: Leandro
	 * Creado: 12/09/2019
	 * Modificado: 17/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_adultos_responsables';
		$this->full_log = TRUE;
		$this->msg_name = 'Adulto Responsable';
		$this->id_name = 'id';
		$this->columnas = array('id', 'persona_id', 'expediente_id', 'hasta', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
		);
		$this->requeridos = array('persona_id', 'expediente_id', 'hasta');
		$this->unicos = array();
		$this->default_join = array(
				array('personas', 'personas.id = na_adultos_responsables.persona_id', 'LEFT',
						array(
								'personas.sexo',
								'personas.cuil',
								'personas.dni',
								'personas.nombre',
								'personas.apellido',
								'personas.telefono',
								'personas.celular',
								'personas.email',
								'personas.fecha_nacimiento',
								'personas.nacionalidad_id',
								'personas.domicilio_id'
						)
				),
				array('domicilios', 'domicilios.id = personas.domicilio_id', 'LEFT',
						array(
								'domicilios.calle',
								'domicilios.barrio',
								'domicilios.altura',
								'domicilios.piso',
								'domicilios.dpto',
								'domicilios.manzana',
								'domicilios.casa',
								'domicilios.localidad_id'
						)
				),
				array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad')),
				array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
				array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
				array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
						array(
								"CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
						)
				)
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