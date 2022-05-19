<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Iniciadores_model extends MY_Model
{

    /**
     * Modelo de Iniciadores
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 13/06/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_iniciadores';
        $this->full_log = TRUE;
        $this->msg_name = 'Iniciador';
        $this->id_name = 'id';
        $this->columnas = array('id', 'tipo_id', 'persona_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('tipo_id', 'persona_id');
        //$this->unicos = array();
        $this->default_join = array(
            array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil, ')') as persona")),
            array('to2_iniciadores_tipos', 'to2_iniciadores_tipos.id = to2_iniciadores.tipo_id', 'LEFT', array('to2_iniciadores_tipos.nombre as tipo'))
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
        if ($this->db->where('iniciador_id', $delete_id)->count_all_results('to2_tramites') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Trámite.');
            return FALSE;
        }
        return TRUE;
    }
}
