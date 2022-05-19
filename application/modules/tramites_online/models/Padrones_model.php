<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Padrones_model extends MY_Model
{

    /**
     * Modelo de Padrones
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 07/07/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_padrones';
        $this->full_log = TRUE;
        $this->msg_name = 'Padrón';
        $this->id_name = 'id';
        $this->columnas = array('id', 'codigo', 'padron', 'nomenclatura', 'tit_dni', 'tit_apellido', 'tit_nombre', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'codigo' => array('label' => 'Código', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'codigo', 'required' => TRUE),
            'padron' => array('label' => 'Padrón', 'maxlength' => '20', 'required' => TRUE),
            'nomenclatura' => array('label' => 'Nomenclatura', 'minlength' => '20', 'maxlength' => '20', 'required' => TRUE),
            'tit_dni' => array('label' => 'Documento Titular'),
            'tit_apellido' => array('label' => 'Apellido Titular'),
            'tit_nombre' => array('label' => 'Nombre Titular')
        );
        $this->requeridos = array('codigo', 'padron', 'nomenclatura');
        $this->unicos = array(array('codigo', 'padron'), 'nomenclatura');
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
        if ($this->db->where('padron_id', $delete_id)->count_all_results('to2_tramites_padrones') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Trámite.');
            return FALSE;
        }
        return TRUE;
    }
}
