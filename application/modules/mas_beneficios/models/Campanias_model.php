<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Campanias_model extends MY_Model
{

    /**
     * Modelo de Campañas
     * Autor: Leandro
     * Creado: 20/07/2020
     * Modificado: 04/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'ta_campanias';
        $this->full_log = TRUE;
        $this->msg_name = 'Campaña';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'activo', 'visible', 'estilo', 'orden', 'agrupamiento_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'activo' => array('label' => 'Activo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'activo', 'required' => TRUE),
            'visible' => array('label' => 'Visible', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'visible', 'required' => TRUE),
            'estilo' => array('label' => 'Estilo', 'maxlength' => '50', 'required' => TRUE),
            'orden' => array('label' => 'Orden', 'type' => 'integer', 'required' => TRUE)
        );
        $this->requeridos = array('nombre', 'activo', 'visible', 'estilo', 'orden', 'agrupamiento_id');
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
        if ($this->db->where('campania_id', $delete_id)->count_all_results('ta_promociones') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Promoción.');
            return FALSE;
        }
        return TRUE;
    }
}
