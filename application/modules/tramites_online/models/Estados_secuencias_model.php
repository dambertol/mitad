<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estados_secuencias_model extends MY_Model
{

    /**
     * Modelo de Secuencias de Estados
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 21/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_estados_secuencias';
        $this->full_log = TRUE;
        $this->msg_name = 'Secuencia de Estados';
        $this->id_name = 'id';
        $this->columnas = array('id', 'estado_id', 'estado_posterior_id', 'tipo', 'regla', 'icono', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'estado_posterior' => array('label' => 'Estado Posterior', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'tipo', 'required' => TRUE),
            'regla' => array('label' => 'Regla', 'maxlength' => '9999'),
            'icono' => array('label' => 'Ícono', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            //'icono' => array('label' => 'Ícono', 'maxlength' => '50', 'required' => TRUE)
        );
        $this->requeridos = array('estado_id', 'estado_posterior_id', 'tipo', 'icono');
        $this->unicos = array(array('estado_id', 'estado_posterior_id'));
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
