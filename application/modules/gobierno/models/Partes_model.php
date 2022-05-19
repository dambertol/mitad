<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Partes_model extends MY_Model
{

	/**
	 * Modelo de Partes
	 * Autor: Leandro
	 * Creado: 08/01/2020
	 * Modificado: 08/01/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'go_partes';
		$this->full_log = TRUE;
		$this->msg_name = 'Parte';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'persona_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre_parte' => array('label' => 'Nombre Parte', 'maxlength' => '100', 'required' => TRUE),
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('nombre');
		//$this->unicos = array();
		$this->default_join = array(
				array('personas', 'personas.id = go_partes.persona_id', 'LEFT',
						array(
								'personas.sexo',
								'personas.cuil',
								'personas.dni',
								'personas.nombre as nombre_persona',
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
		if ($this->db->where('parte_id', $delete_id)->count_all_results('go_documentos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Documento.');
			return FALSE;
		}
		return TRUE;
	}
}