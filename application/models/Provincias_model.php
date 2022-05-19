<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Provincias_model extends MY_Model
{

    /**
     * Modelo de Provincias
     * Autor: Leandro
     * Creado: 23/05/2018
     * Modificado: 29/09/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'provincias';
        $this->full_log = TRUE;
        $this->msg_name = 'Provincia';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'codigo', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'codigo' => array('label' => 'Código', 'maxlength' => '5')
        );
        $this->requeridos = array('nombre');
        $this->unicos = array('nombre', 'codigo');
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
        if ($this->db->where('provincia_id', $delete_id)->count_all_results('departamentos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Departamento.');
            return FALSE;
        }
        return TRUE;
    }
}
