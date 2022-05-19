<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Formularios_registros_model extends MY_Model
{

    /**
     * Modelo de Registros
     * Autor: Leandro
     * Creado: 13/08/2020
     * Modificado: 15/08/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'formularios_registros';
        $this->full_log = TRUE;
        $this->msg_name = 'Registro';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'apellido', 'dni', 'telefono', 'email', 'calle', 'altura', 'localidad_id', 'nombre_ninio', 'apellido_ninio', 'formulario_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
            'dni' => array('label' => 'DNI (sin puntos)', 'type' => 'integer', 'maxlength' => '8', 'required' => TRUE),
            'telefono' => array('label' => 'Teléfono (sin el 0 ni el 15)', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
            'email' => array('label' => 'Correo electrónico', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
            'calle' => array('label' => 'Calle', 'maxlength' => '100', 'required' => TRUE),
            'altura' => array('label' => 'Altura', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
            'localidad' => array('label' => 'Distrito', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'nombre_ninio' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'apellido_ninio' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
            'firmas' => array('label' => 'Firmas', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'id_name' => 'firmas', 'required' => TRUE),
        );
        $this->requeridos = array('nombre', 'apellido', 'dni', 'telefono', 'email', 'calle', 'altura', 'localidad_id', 'nombre_ninio', 'apellido_ninio', 'formulario_id');
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
