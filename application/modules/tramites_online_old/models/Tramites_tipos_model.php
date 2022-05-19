<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_tipos_model extends MY_Model
{

    /**
     * Modelo de Tipos de Trámites
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to_tramites_tipos';
        $this->full_log = TRUE;
        $this->msg_name = 'Tipo de Consulta';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'categoria_id', 'visibilidad', 'area_id', 'email_responsable', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'visibilidad' => array('label' => 'Visibilidad', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'visibilidad', 'required' => TRUE),
            'area' => array('label' => 'Area', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'email_responsable' => array('label' => 'Email Responsable', 'type' => 'email', 'maxlength' => '50')
        );
        $this->requeridos = array('nombre');
        $this->unicos = array(array('nombre', 'categoria_id', 'visibilidad', 'area_id'));
        $this->default_join = array(
            array('to_tramites_categorias', 'to_tramites_categorias.id = to_tramites_tipos.categoria_id', 'LEFT', array('to_tramites_categorias.nombre as categoria')),
            array('areas', 'areas.id = to_tramites_tipos.area_id', 'LEFT', array('areas.nombre as area'))
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
        if ($this->db->where('tipo_id', $delete_id)->count_all_results('to_tramites') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Consulta.');
            return FALSE;
        }
        return TRUE;
    }
}
