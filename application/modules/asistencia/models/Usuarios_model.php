<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends MY_Model
{

	/**
	 * Modelo de Usuarios
	 * Autor: Leandro
	 * Creado: 19/09/2016
	 * Modificado: 10/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'users';
		$this->full_log = TRUE;
		$this->msg_name = 'Usuario';
		$this->id_name = 'id';
		$this->columnas = array('id', 'username', 'password', 'active', 'last_login');
		$this->fields = array(
				'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'password' => array('label' => 'Contraseña', 'minlength' => '8', 'maxlength' => '32', 'type' => 'password'),
				'password_confirm' => array('label' => 'Confirmar contraseña', 'type' => 'password'),
				'active' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'active', 'required' => TRUE),
				'last_login' => array('label' => 'Último ingreso'),
				'groups' => array('label' => 'Grupo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'groups', 'required' => TRUE)
		);
		$this->requeridos = array('username', 'password');
		$this->unicos = array('username');
		$this->default_join = array(
				array('personas', 'personas.id = users.persona_id', 'LEFT',
						array(
								'personas.dni',
								'personas.sexo',
								'personas.cuil',
								'personas.nombre',
								'personas.apellido',
								'personas.telefono',
								'personas.celular',
								'personas.email',
								'personas.fecha_nacimiento',
								'personas.nacionalidad_id'
						)
				),
				array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad'))
		);
	}

	/**
	 * _can_delete: Devuelve true si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		return FALSE;
	}
}
/* End of file Usuarios_model.php */
/* Location: ./application/models/Usuarios_model.php */