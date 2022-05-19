<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Comercios_model extends MY_Model
{

    /**
     * Modelo de Comercios
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 03/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'ta_comercios';
        $this->full_log = TRUE;
        $this->msg_name = 'Comercio';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'razon_social', 'padron', 'padron_c', 'cuit', 'calle', 'altura', 'localidad_id', 'telefono', 'email', 'web', 'facebook', 'instagram', 'twitter', 'envio_domicilio', 'encargado_id', 'imagen_url', 'latitud', 'longitud', 'comentarios', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre de Fantasía', 'maxlength' => '100', 'required' => TRUE),
            'razon_social' => array('label' => 'Razón Social / Titular', 'maxlength' => '100', 'required' => TRUE),
            'padron' => array('label' => 'Padrón Municipal', 'type' => 'integer', 'maxlength' => '6'),
            'padron_c' => array('label' => 'Padrón Comercial', 'type' => 'integer', 'maxlength' => '6'),
            'cuit' => array('label' => 'CUIL / CUIT', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13', 'required' => TRUE),
            'calle' => array('label' => 'Calle', 'maxlength' => '100', 'required' => TRUE),
            'altura' => array('label' => 'Altura', 'maxlength' => '50', 'required' => TRUE),
            'localidad' => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13', 'required' => TRUE),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
            'web' => array('label' => "Web <br /><span class='red' style='font-size:70%;'>Ej: lujandecuyo.gob.ar</span>", 'maxlength' => '100'),
            'facebook' => array('label' => "Facebook <br /><span class='red' style='font-size:70%;'>Ej: facebook.com/munilujandecuyo</span>", 'maxlength' => '100'),
            'instagram' => array('label' => "Instagram <br /><span class='red' style='font-size:70%;'>Ej: instagram.com/lujandecuyomza</span>", 'maxlength' => '100'),
            'twitter' => array('label' => "Twitter <br /><span class='red' style='font-size:70%;'>Ej: twitter.com/MuniLujanDeCuyo</span>", 'maxlength' => '100'),
            'envio_domicilio' => array('label' => 'Envío a domicilio', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'envio_domicilio', 'required' => TRUE),
            'encargado' => array('label' => 'Encargado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'required' => TRUE),
            'imagen_url' => array('label' => "Imagen <br /><span class='red' style='font-size:70%;'>Recomendado 400px x 320px</span>", 'type' => 'file', 'maxlength' => '255'),
            'comentarios' => array('label' => 'Comentarios', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );
        $this->requeridos = array('nombre', 'razon_social', 'cuit', 'calle', 'altura', 'localidad_id', 'telefono', 'email', 'encargado_id');
        // $this->unicos = array('nombre');
        $this->default_join = array(
            array('localidades', 'localidades.id = ta_comercios.localidad_id', 'LEFT', array("localidades.nombre as localidad")),
            array('personas', 'personas.id = ta_comercios.encargado_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil, ')') as encargado"))
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
        if ($this->db->where('comercio_id', $delete_id)->count_all_results('ta_comercios_categorias') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Categoria.');
            return FALSE;
        }
        if ($this->db->where('comercio_id', $delete_id)->count_all_results('ta_promociones') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Promoción.');
            return FALSE;
        }
        return TRUE;
    }
}
