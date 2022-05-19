<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Iniciadores_tipos_model extends MY_Model
{

    /**
     * Modelo de Tipos de Iniciadores
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 14/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_iniciadores_tipos';
        $this->full_log = TRUE;
        $this->msg_name = 'Tipo de Iniciador';
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
        if ($this->db->where('iniciador_tipo_id', $delete_id)->count_all_results('to2_campos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Campo.');
            return FALSE;
        }
        if ($this->db->where('tipo_id', $delete_id)->count_all_results('to2_iniciadores') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Iniciador.');
            return FALSE;
        }
        if ($this->db->where('iniciador_tipo_id', $delete_id)->count_all_results('to2_procesos_iniciadores') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Proceso.');
            return FALSE;
        }
        return TRUE;
    }
}
