<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Datos_model extends MY_Model
{

    /**
     * Modelo de Datos
     * Autor: Leandro
     * Creado: 26/05/2021
     * Modificado: 12/06/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_datos';
        $this->full_log = TRUE;
        $this->msg_name = 'Dato';
        $this->id_name = 'id';
        $this->columnas = array('id', 'pase_id', 'campo_id', 'repeticion', 'valor', 'adjunto_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'pase' => array('label' => 'Pase', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'campo' => array('label' => 'Campo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'repeticion' => array('label' => 'Repetición', 'type' => 'integer', 'required' => TRUE),
            'valor' => array('label' => 'Valor')
        );
        $this->requeridos = array('pase_id', 'campo_id', 'repeticion');
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
