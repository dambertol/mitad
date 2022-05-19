<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Agrupamientos_model extends MY_Model
{

    /**
     * Modelo de Agrupamientos
     * Autor: Leandro
     * Creado: 15/07/2020
     * Modificado: 29/12/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'ta_agrupamientos';
        $this->full_log = TRUE;
        $this->msg_name = 'Agrupamiento';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '100')
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
        if ($this->db->where('agrupamiento_id', $delete_id)->count_all_results('ta_campanias') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a campañas.');
            return FALSE;
        }
        if ($this->db->where('agrupamiento_id', $delete_id)->count_all_results('ta_categorias') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a categorias.');
            return FALSE;
        }
        return TRUE;
    }
}
