<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_model extends MY_Model
{

    /**
     * Modelo de Usuarios
     * Autor: Leandro
     * Creado: 26/01/2017
     * Modificado: 05/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'users';
        $this->full_log = TRUE;
        $this->msg_name = 'Usuario';
        $this->id_name = 'id';
        $this->columnas = array('id', 'username', 'password', 'active', 'last_login', 'password_change', 'persona_id');
        $this->fields = array(
            'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'password' => array('label' => 'Contraseña', 'minlength' => '8', 'maxlength' => '32', 'type' => 'password'),
            'password_confirm' => array('label' => 'Confirmar contraseña', 'type' => 'password'),
            'password_change' => array('label' => 'Debe cambiar contraseña', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'password_change', 'required' => TRUE),
            'active' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'active', 'required' => TRUE),
            'last_login' => array('label' => 'Último ingreso'),
            'groups' => array('label' => 'Grupos', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'id_name' => 'groups', 'required' => TRUE)
        );
        $this->requeridos = array('username', 'password', 'password_change');
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
                    'personas.nacionalidad_id',
                    'personas.domicilio_id'
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
