<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_sectores_model extends MY_Model
{

    /**
     * Modelo de Usuarios Sector
     * Autor: Leandro
     * Creado: 12/04/2019
     * Modificado: 25/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'in_usuarios_sectores';
        $this->full_log = TRUE;
        $this->msg_name = 'Usuario Sector';
        $this->id_name = 'id';
        $this->columnas = array('id', 'user_id', 'sector_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'user' => array('label' => 'Usuario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'sector' => array('label' => 'Sector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('user_id', 'sector_id');
        $this->unicos = array(array('user_id', 'sector_id'));
        $this->default_join = array(
            array('users', 'users.id = in_usuarios_sectores.user_id', 'LEFT'),
            array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as user")),
            array('in_sectores', 'in_sectores.id = in_usuarios_sectores.sector_id', 'LEFT', array('in_sectores.descripcion as sector'))
        );
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * in_sector: Devuelve TRUE si el Usuario está asignado al Sector.
     *
     * @param int $user_id
     * @param int $sector_id
     * @return boolean
     */
    public function in_sector($user_id, $sector_id)
    {
        $asignacion = $this->get(array(
            'user_id' => $user_id,
            'sector_id' => $sector_id
        ));
        if (!empty($asignacion))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
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
        return TRUE;
    }
}
