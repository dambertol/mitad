<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Promociones_model extends MY_Model
{

    /**
     * Modelo de Promociones
     * Autor: Leandro
     * Creado: 20/07/2020
     * Modificado: 07/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'ta_promociones';
        $this->full_log = TRUE;
        $this->msg_name = 'Promoción';
        $this->id_name = 'id';
        $this->columnas = array('id', 'comercio_id', 'campania_id', 'descripcion', 'imagen_url', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'comercio' => array('label' => 'Comercio', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'campania' => array('label' => 'Campaña', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'imagen_url' => array('label' => "Imagen <br /><span class='red' style='font-size:70%;'>Recomendado 400px x 320px</span>", 'type' => 'file', 'maxlength' => '255'),
            'descripcion' => array('label' => 'Descripción', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );
        $this->requeridos = array('comercio_id', 'campania_id');
        //$this->unicos = array();
        $this->default_join = array(
            array('ta_campanias', 'ta_campanias.id = ta_promociones.campania_id', 'left', 'ta_campanias.nombre as campania, ta_campanias.agrupamiento_id as agrupamiento_id'),
            array('ta_comercios', 'ta_comercios.id = ta_promociones.comercio_id', 'left', 'ta_comercios.nombre as comercio, ta_comercios.encargado_id as encargado_id, ta_comercios.calle as calle, ta_comercios.altura as altura, ta_comercios.telefono as telefono, ta_comercios.email as email, ta_comercios.web as web, ta_comercios.facebook as facebook, ta_comercios.instagram as instagram, ta_comercios.twitter as twitter'),
            array('localidades', 'localidades.id = ta_comercios.localidad_id', 'LEFT', 'localidades.nombre as localidad')
        );
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
