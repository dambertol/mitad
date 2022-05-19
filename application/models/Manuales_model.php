<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manuales_model extends MY_Model
{

    /**
     * Modelo de Manuales
     * Autor: Leandro
     * Creado: 02/06/2020
     * Modificado: 02/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'manuales';
        $this->full_log = TRUE;
        $this->msg_name = 'Manual';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'link', 'categoria_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
            'link' => array('label' => 'Link', 'maxlength' => '255', 'required' => TRUE),
            'categoria' => array('label' => 'Categoria', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('nombre', 'link', 'categoria_id');
        //$this->unicos = array();
        $this->default_join = array(
            array('manuales_categorias', 'manuales_categorias.id = manuales.categoria_id', 'LEFT', array('manuales_categorias.nombre as categoria'))
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
        return TRUE;
    }
}
