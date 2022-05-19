<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escribanos_model extends MY_Model
{

	/**
	 * Modelo de Escribanos
	 * Autor: Leandro
	 * Creado: 04/06/2018
	 * Modificado: 09/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_escribanos';
		$this->full_log = TRUE;
		$this->msg_name = 'Escribano';
		$this->id_name = 'id';
		$this->columnas = array('id', 'persona_id', 'matricula_nro', 'registro_nro', 'registro_tipo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'matricula_nro' => array('label' => 'Matrícula N°', 'type' => 'integer', 'maxlength' => '11'),
				'registro_nro' => array('label' => 'Registro N°', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'registro_tipo' => array('label' => 'Registro Tipo', 'input_type' => 'combo', 'id_name' => 'registro_tipo', 'type' => 'bselect', 'required' => TRUE),
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('persona_id', 'registro_nro', 'registro_tipo');
		$this->unicos = array('persona_id');
		$this->default_join = array(
				array('personas', 'personas.id = tr_escribanos.persona_id', 'LEFT',
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
	 * get_user_id: Devuelve el user_id del escribano.
	 *
	 * @param int $escribano_id
	 * @return int $user_id
	 */
	public function get_user_id($escribano_id)
	{
		$escribano = $this->get(array(
				'select' => 'users.id as user_id',
				'join' => array(
						array('personas', 'personas.id = tr_escribanos.persona_id', 'LEFT'),
						array('users', 'users.persona_id = personas.id', 'LEFT'),
				),
				'id' => $escribano_id
		));

		if (!empty($escribano->user_id))
		{
			return $escribano->user_id;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('escribano_id', $delete_id)->count_all_results('tr_tramites') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Trámite.');
			return FALSE;
		}
		return TRUE;
	}
}