<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_oficinas_model extends MY_Model
{

    /**
     * Modelo de Usuarios por Oficina
     * Autor: Leandro
     * Creado: 21/04/2021
     * Modificado: 23/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_usuarios_oficinas';
        $this->full_log = TRUE;
        $this->msg_name = 'Usuario por Oficina';
        $this->id_name = 'id';
        $this->columnas = array('id', 'user_id', 'oficina_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'user' => array('label' => 'Usuario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('user_id', 'oficina_id');
        $this->unicos = array(array('user_id', 'oficina_id'));
        $this->default_join = array(
            array('users', 'users.id = to2_usuarios_oficinas.user_id', 'LEFT'),
            array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as user")),
            array('to2_oficinas', 'to2_oficinas.id = to2_usuarios_oficinas.oficina_id', 'LEFT', array('to2_oficinas.nombre as oficina'))
        );
        // Inicializaciones necesarias colocar acá.
    }


    public function get_oficina_id($user_id)
    {
        $oficinas = $this->db->query("
				SELECT UT.oficina_id 
				FROM to2_usuarios_oficinas UT
				WHERE UT.user_id = $user_id"
        )->result_array();
        return array_column($oficinas, "oficina_id");

    }

    /**
     * _can_delete: Devuelve TRUE si puede eliminarse el registro.
     *
     * @param int $delete_id
     * @return bool
     */
    protected
    function _can_delete($delete_id)
    {
        return TRUE;
    }
}
