<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Menores_model extends MY_Model
{

	/**
	 * Modelo de Menores
	 * Autor: Leandro
	 * Creado: 12/09/2019
	 * Modificado: 13/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_menores';
		$this->full_log = TRUE;
		$this->msg_name = 'Menor';
		$this->id_name = 'id';
		$this->columnas = array('id', 'persona_id', 'expediente_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('persona_id', 'expediente_id');
		$this->unicos = array(array('persona_id', 'expediente_id'));
		$this->default_join = array(
				array('personas', 'personas.id = na_menores.persona_id', 'LEFT',
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