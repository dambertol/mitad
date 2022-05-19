<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_areas_model extends MY_Model
{

    /**
     * Modelo de Usuarios por Área
     * Autor: Leandro
     * Creado: 10/07/2018
     * Modificado: 17/07/2018 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'nv_usuarios_areas';
        $this->full_log = TRUE;
        $this->msg_name = 'Usuario por Área';
        $this->id_name = 'id';
        $this->columnas = array('id', 'user_id', 'area_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'user' => array('label' => 'Usuario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array();
        $this->unicos = array(array('user_id', 'area_id'));
        $this->default_join = array(
            array('users', 'users.id = nv_usuarios_areas.user_id', 'LEFT'),
            array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as user")),
            array('areas', 'areas.id = nv_usuarios_areas.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
        );
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * in_area: Devuelve TRUE si el Usuario está asignado al Área.
     *
     * @param int $user_id
     * @param int $area_id
     * @return boolean
     */
    public function in_area($user_id, $area_id)
    {
        $asignacion = $this->get(array(
            'user_id' => $user_id,
            'area_id' => $area_id
        ));
        if (!empty($asignacion)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function get_areas($user_id)
    {
        $result = $this->get(
            [
                'select' => 'area_id',
                'user_id' => $user_id,
                'return_array' => TRUE
            ]
        );
        $array = array_map(function ($value) {
            return $value['area_id'];
        }, $result);

        return $array;
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