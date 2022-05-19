<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas_movimientos_model extends MY_Model
{

    /**
     * Modelo de Movimientos de Cédula
     * Autor: GENERATOR_MLC
     * Creado: 02/07/2019
     * Modificado: 02/07/2019 (GENERATOR_MLC)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'nv_cedulas_movimientos';
        $this->full_log = TRUE;
        $this->msg_name = 'Movimiento de Cédula';
        $this->id_name = 'id';
        $this->columnas = array('id', 'observaciones', 'fecha', 'tipo_movimiento_id', 'cedula_id', 'usuario_id');//, 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'observaciones' => array('label' => 'Observaciones'),
            'fecha' => array('label' => 'Fecha', 'type' => 'date'),
            'tipo_movimiento' => array('label' => 'Tipo de Movimiento', 'input_type' => 'combo'),
            'cedula' => array('label' => 'Cedula', 'input_type' => 'combo'),
            'usuario' => array('label' => 'Usuario', 'input_type' => 'combo'),
//            'audi_usuario' => array('label' => 'Audi de Usuario', 'type' => 'integer', 'maxlength' => '11'),
//            'audi_fecha' => array('label' => 'Audi de Fecha', 'type' => 'date'),
//            'audi_accion' => array('label' => 'Audi de Accion', 'maxlength' => '1')
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
        return TRUE;
    }

    public function get_movimientos($cedula_id)
    {
        return $this->get(
            [
                'cedula_id' => $cedula_id,
                'join' => [
                    [
                        'nv_cedulas_movimientos_tipos', 'nv_cedulas_movimientos_tipos.id = nv_cedulas_movimientos.tipo_movimiento_id', 'LEFT',
                        [
                            'nv_cedulas_movimientos_tipos.descripcion as tipo_movimiento'
                        ]
                    ],
                    [
                        'users', 'users.id = nv_cedulas_movimientos.usuario_id', 'LEFT',
                        [
                            "users.username"
                        ]
                    ],
                    [
                        'personas', 'personas.id = users.persona_id', 'LEFT',
                        [
                            "CONCAT(personas.apellido, ', ', personas.nombre, ' (', username ,')') as usuario"
                        ]
                    ],

                ],
            ]
        );
    }

    public function get_last_movimiento($cedula_id)
    {
        return $this->get(
            [
                'cedula_id' => $cedula_id,
                'join' => [
                    [
                        'nv_cedulas_movimientos_tipos', 'nv_cedulas_movimientos_tipos.id = nv_cedulas_movimientos.tipo_movimiento_id', 'LEFT',
                        [
                            'nv_cedulas_movimientos_tipos.descripcion as tipo_movimiento',
                        ]
                    ]
                ],
                "limit" => 1,
                "sort_by" => "fecha DESC",
            ]
        )[0];
    }


    public function add_movimiento($cedula_id, $tipo, $obs = "")
    {
        return $this->create(array(
            'cedula_id' => $cedula_id,
            'observaciones' => $obs,
            'tipo_movimiento_id' => $tipo, // Creacion
            'fecha' => (new DateTime())->format('Y-m-d H:i:s'),
            'usuario_id' => $this->session->userdata('user_id')), FALSE);
    }
}