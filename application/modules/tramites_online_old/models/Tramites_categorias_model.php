<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_categorias_model extends MY_Model
{

    /**
     * Modelo de Categorías de Trámites
     * Autor: Leandro
     * Creado: 18/03/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to_tramites_categorias';
        $this->full_log = TRUE;
        $this->msg_name = 'Categoría de Consulta';
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
        if ($this->db->where('categoria_id', $delete_id)->count_all_results('to_tramites_tipos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Consulta.');
            return FALSE;
        }
        return TRUE;
    }
}
