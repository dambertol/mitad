<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cos_model extends MY_Model
{

    /**
     * Modelo de COS
     * Autor: Leandro
     * Creado: 07/08/2021
     * Modificado: 07/08/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'cos';
        $this->full_log = TRUE;
        $this->msg_name = 'COS';
        $this->id_name = 'id';
        $this->columnas = array('id', 'zona', 'tamanio_lote', 'frente_minimo', 'fos', 'retiro_frontal', 'retiro_bilateral', 'retiro_posterior', 'h_max', 'ordenanza_usos', 'ordenanza_limites', 'aplica_nue', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'zona' => array('label' => 'Zona', 'type' => 'bselect', 'required' => TRUE),
            'tamanio_lote' => array('label' => 'Tamanio de Lote', 'maxlength' => '100'),
            'frente_minimo' => array('label' => 'Frente de Minimo', 'maxlength' => '100'),
            'fos' => array('label' => 'Fos', 'maxlength' => '100'),
            'retiro_frontal' => array('label' => 'Retiro de Frontal', 'maxlength' => '100'),
            'retiro_bilateral' => array('label' => 'Retiro de Bilateral', 'maxlength' => '100'),
            'retiro_posterior' => array('label' => 'Retiro de Posterior', 'maxlength' => '100'),
            'h_max' => array('label' => 'H de Max', 'maxlength' => '100'),
            'ordenanza_usos' => array('label' => 'Ordenanza de Usos', 'maxlength' => '100'),
            'ordenanza_limites' => array('label' => 'Ordenanza de Limites', 'maxlength' => '100'),
            'aplica_nue' => array('label' => 'Aplica de Nue', 'maxlength' => '100'),
            'observaciones' => array('label' => 'Observaciones')
        );
        $this->requeridos = array('zona');
        $this->unicos = array('zona');
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
