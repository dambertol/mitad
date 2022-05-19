<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias_model extends MY_Model
{

    /**
     * Modelo de Categorías
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 29/12/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'ta_categorias';
        $this->full_log = TRUE;
        $this->msg_name = 'Categoría';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'estilo', 'orden', 'agrupamiento_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
            'estilo' => array('label' => 'Estilo', 'maxlength' => '50', 'required' => TRUE),
            'orden' => array('label' => 'Orden', 'type' => 'integer', 'required' => TRUE)
        );
        $this->requeridos = array('nombre', 'estilo', 'orden', 'agrupamiento_id');
        $this->unicos = array('nombre');
        $this->default_join = array(
            array('ta_agrupamientos', 'ta_agrupamientos.id = ta_categorias.agrupamiento_id', 'LEFT', array("ta_agrupamientos.nombre as agrupamiento"))
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
        if ($this->db->where('categoria_id', $delete_id)->count_all_results('ta_comercios_categorias') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Comercio.');
            return FALSE;
        }
        return TRUE;
    }
}
