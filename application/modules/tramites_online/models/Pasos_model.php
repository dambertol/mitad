<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pasos_model extends MY_Model
{

    /**
     * Modelo de Pasos
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 19/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_pasos';
        $this->full_log = TRUE;
        $this->msg_name = 'Paso';
        $this->id_name = 'id';
        $this->columnas = array('id', 'orden', 'modo', 'regla', 'padron', 'formulario_id', 'estado_id', 'mensaje', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'orden' => array('label' => 'Orden', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
            'modo' => array('label' => 'Modo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'modo', 'required' => TRUE),
            'regla' => array('label' => 'Regla', 'maxlength' => '50'),
            'padron' => array('label' => 'Padrón', 'input_type' => 'combo', 'type' => 'bselect'),
            'formulario' => array('label' => 'Formulario', 'input_type' => 'combo', 'type' => 'bselect'),
            'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'mensaje' => array('label' => 'Mensaje', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );
        $this->requeridos = array('orden', 'modo', 'estado_id');
        //$this->unicos = array();
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
        return TRUE;
    }
}
