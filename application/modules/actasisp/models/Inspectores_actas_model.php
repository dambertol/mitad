<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inspectores_actas_model extends MY_Model
{

	/**
	 * Modelo de Inspectores Actas
	 * Autor: Leandro
	 * Creado: 20/01/2020
	 * Modificado: 20/01/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'act_inspectores_actas';
		$this->full_log = TRUE;
		$this->msg_name = 'Inspector Acta';
		$this->id_name = 'id';
		$this->columnas = array('id', 'inspector_id', 'acta_id', 'posicion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'inspector' => array('label' => 'Inspector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'acta' => array('label' => 'Acta', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('inspector_id', 'acta_id');
		$this->unicos = array(array('inspector_id', 'acta_id'), array('acta_id', 'posicion'));
		$this->default_join = array(
				/* array('personas', 'personas.id = act_inspectores.persona_id', 'LEFT',
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
				  ) */
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