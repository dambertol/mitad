<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas_estados_model extends MY_Model
{

    const SOLICITUD_REALIZADA = 1;
    const SOLICITUD_ACEPTADA = 2;
    const NOTIFICADOR_ASIGNADO = 3;
    const ENTREGA_POSITIVA_MANO = 4;
    const ENTREGA_POSITIVA_BAJO_PUERTA = 5;
    const ENTREGA_NEGATIVA = 6;
    const DATOS_INCORRECTOS = 7;
    const SOLICITUD_ANULADA = 8;
    const CEDULA_IMPRESA = 9;
    const EN_RUTA = 9;

    /**
     * Modelo de Estados de Cédula
     * Autor: GENERATOR_MLC
     * Creado: 02/07/2019
     * Modificado: 02/07/2019 (GENERATOR_MLC)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'nv_cedulas_estados';
        $this->full_log = TRUE;
        $this->msg_name = 'Estado de Cédula';
        $this->id_name = 'id';
        $this->columnas = array('id', 'descripcion');//, 'audi_usuario', 'audi_fecha', 'audi_accion', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'descripcion' => array('label' => 'Descripcion', 'maxlength' => '50'),
//			'audi_usuario' => array('label' => 'Audi de Usuario', 'type' => 'integer', 'maxlength' => '11'),
//			'audi_fecha' => array('label' => 'Audi de Fecha', 'type' => 'date'),
//			'audi_accion' => array('label' => 'Audi de Accion', 'maxlength' => '1')
        );
        $this->requeridos = array();
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
        if ($this->db->where('estado_id', $delete_id)->count_all_results('nv_cedulas') > 0) {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a cedulas.');
            return FALSE;
        }
        return TRUE;
    }
}