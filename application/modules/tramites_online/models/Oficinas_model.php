<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Oficinas_model extends MY_Model
{

    /**
     * Modelo de Oficinas
     * Autor: Leandro
     * Creado: 21/04/2021
     * Modificado: 23/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_oficinas';
        $this->full_log = TRUE;
        $this->msg_name = 'Oficina';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE)
        );
        $this->requeridos = array('nombre');
        $this->unicos = array('nombre');
        $this->default_join = array();
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
        if ($this->db->where('oficina_id', $delete_id)->count_all_results('to2_estados') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Estado.');
            return FALSE;
        }
        if ($this->db->where('oficina_id', $delete_id)->count_all_results('to2_procesos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Proceso.');
            return FALSE;
        }
        if ($this->db->where('oficina_id', $delete_id)->count_all_results('to2_usuarios_oficinas') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Usuario.');
            return FALSE;
        }
        return TRUE;
    }
}
