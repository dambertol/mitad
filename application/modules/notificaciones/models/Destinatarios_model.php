<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Destinatarios_model extends MY_Model
{

    /**
     * Modelo de Destinatarios
     * Autor: GENERATOR_MLC
     * Creado: 02/07/2019
     * Modificado: 02/07/2019 (GENERATOR_MLC)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'nv_destinatarios';
        $this->full_log = TRUE;
        $this->msg_name = 'Destinatario';
        $this->id_name = 'id';
        $this->columnas = array('id', 'tipo_identificacion', 'n_identificacion', 'nombre', 'apellido');//, 'audi_usuario', 'audi_fecha', 'audi_accion', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '100'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '100'),
            'tipo_identificacion' => array('label' => 'Tipo Identificacion', 'maxlength' => '2'),
            'n_identificacion' => array('label' => 'Nº Identificacion', 'maxlength' => '15'),
//			'audi_usuario' => array('label' => 'Audi de Usuario', 'type' => 'integer', 'maxlength' => '11'),
//			'audi_fecha' => array('label' => 'Audi de Fecha', 'type' => 'date'),
//			'audi_accion' => array('label' => 'Audi de Accion', 'maxlength' => '1')
        );
        //$this->requeridos = array('tipo_identificacion', 'n_identificacion', 'nombre', 'apellido');
        $this->requeridos = array('tipo_identificacion', 'n_identificacion');
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
        if ($this->db->where('destinatario_id', $delete_id)->count_all_results('nv_cedulas') > 0) {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a cedulas.');
            return FALSE;
        }
        return TRUE;
    }

    /**
     * @param $tipo_identificacion
     * @param $n_identificacion
     * @return mixed
     *
     * Busca un destinatario, si es null no existe
     */
    public function buscar_destinatario($tipo_identificacion, $n_identificacion)
    {
        return $this->get(
            [
                'where' => [
                    ['column' => 'tipo_identificacion', 'value' => $tipo_identificacion],
                    ['column' => 'n_identificacion', 'value' => $n_identificacion],
                ],
                'limit' => 1,
            ]
        )[0];

    }
}