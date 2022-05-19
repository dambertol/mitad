<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_padrones_model extends MY_Model
{

    /**
     * Modelo de Trámites Padrones
     * Autor: Leandro
     * Creado: 26/05/2021
     * Modificado: 27/07/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_tramites_padrones';
        $this->full_log = TRUE;
        $this->msg_name = 'Trámite Padrón';
        $this->id_name = 'id';
        $this->columnas = array('id', 'pase_id', 'repeticion', 'padron_id', 'adjunto_id', 'consulta', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'pase' => array('label' => 'Pase', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'repeticion' => array('label' => 'Repetición', 'type' => 'integer', 'required' => TRUE),
            'padron' => array('label' => 'Padrón', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('pase_id', 'repeticion', 'padron_id');
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
