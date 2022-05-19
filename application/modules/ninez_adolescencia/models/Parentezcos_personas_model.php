<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Parentezcos_personas_model extends MY_Model
{

	/**
	 * Modelo de Parentezcos
	 * Autor: Leandro
	 * Creado: 09/09/2019
	 * Modificado: 09/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_parentezcos_personas';
		$this->full_log = TRUE;
		$this->msg_name = 'Parentezco';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_parentezco_id', 'persona_id', 'pariente_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'tipo_parentezco' => array('label' => 'Tipo de Parentezco', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'pariente' => array('label' => 'Pariente', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('tipo_parentezco_id', 'persona_id', 'pariente_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('na_tipos_parentezcos', 'na_tipos_parentezcos.id = na_parentezcos_personas.tipo_parentezco_id', 'LEFT', array('na_tipos_parentezcos.nombre as tipo_parentezco')),
				array('personas PE', 'PE.id = na_parentezcos_personas.persona_id', 'LEFT',
						array(
								'PE.sexo',
								'PE.cuil',
								'PE.dni',
								'PE.nombre',
								'PE.apellido',
								'PE.telefono',
								'PE.celular',
								'PE.email',
								'PE.fecha_nacimiento',
								'PE.nacionalidad_id',
								'PE.domicilio_id'
						)
				),
				array('domicilios DPE', 'DPE.id = PE.domicilio_id', 'LEFT',
						array(
								'DPE.calle',
								'DPE.barrio',
								'DPE.altura',
								'DPE.piso',
								'DPE.dpto',
								'DPE.manzana',
								'DPE.casa',
								'DPE.localidad_id'
						)
				),
				array('nacionalidades NPE', 'NPE.id = PE.nacionalidad_id', 'LEFT', array('NPE.nombre as nacionalidad')),
				array('localidades LPE', 'LPE.id = DPE.localidad_id', 'LEFT'),
				array('departamentos DEPE', 'DEPE.id = LPE.departamento_id', 'LEFT'),
				array('provincias PPE', 'PPE.id = DEPE.provincia_id', 'LEFT',
						array(
								"CONCAT(LPE.nombre, ' - ', DEPE.nombre, ' - ', PPE.nombre) as localidad"
						)
				),
				array('personas PA', 'PA.id = na_parentezcos_personas.pariente_id', 'LEFT',
						array(
								'PA.sexo as pa_sexo',
								'PA.cuil as pa_cuil',
								'PA.dni as pa_dni',
								'PA.nombre as pa_nombre',
								'PA.apellido as pa_apellido',
								'PA.telefono as pa_telefono',
								'PA.celular as pa_celular',
								'PA.email as pa_email',
								'PA.fecha_nacimiento as pa_fecha_nacimiento',
								'PA.nacionalidad_id as pa_nacionalidad_id',
								'PA.domicilio_id as pa_domicilio_id'
						)
				),
				array('domicilios DPA', 'DPA.id = PA.domicilio_id', 'LEFT',
						array(
								'DPA.calle as pa_calle',
								'DPA.barrio as pa_barrio',
								'DPA.altura as pa_altura',
								'DPA.piso as pa_piso',
								'DPA.dpto as pa_dpto',
								'DPA.manzana as pa_manzana',
								'DPA.casa as pa_casa',
								'DPA.localidad_id as pa_localidad_id'
						)
				),
				array('nacionalidades NPA', 'NPA.id = PA.nacionalidad_id', 'LEFT', array('NPA.nombre as pa_nacionalidad')),
				array('localidades LPA', 'LPA.id = DPA.localidad_id', 'LEFT'),
				array('departamentos DEPA', 'DEPA.id = LPA.departamento_id', 'LEFT'),
				array('provincias PPA', 'PPA.id = DEPA.provincia_id', 'LEFT',
						array(
								"CONCAT(LPA.nombre, ' - ', DEPA.nombre, ' - ', PPA.nombre) as pa_localidad"
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