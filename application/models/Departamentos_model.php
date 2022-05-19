<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Departamentos_model extends MY_Model
{

    /**
     * Modelo de Departamentos
     * Autor: Leandro
     * Creado: 23/05/2018
     * Modificado: 29/09/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'departamentos';
        $this->full_log = TRUE;
        $this->msg_name = 'Departamento';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'codigo', 'provincia_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'codigo' => array('label' => 'Código', 'maxlength' => '5'),
            'provincia' => array('label' => 'Provincia', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('nombre', 'provincia_id');
        $this->unicos = array(array('nombre', 'provincia_id'), 'codigo');
        $this->default_join = array(
            array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT', array("provincias.nombre as provincia"))
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
        if ($this->db->where('departamento_id', $delete_id)->count_all_results('localidades') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Localidad.');
            return FALSE;
        }
        return TRUE;
    }
}
