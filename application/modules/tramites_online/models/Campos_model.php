<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Campos_model extends MY_Model
{

    /**
     * Modelo de Campos
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 08/08/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_campos';
        $this->full_log = TRUE;
        $this->msg_name = 'Campo';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'readonly', 'valor_default', 'posicion', 'tipo', 'opciones', 'formulario_id', 'iniciador_tipo_id', 'etiqueta', 'validacion', 'editable', 'imprimible', 'obligatorio', 'funcion', 'ayuda', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'readonly' => array('label' => 'Readonly', 'type' => 'integer', 'maxlength' => '1', 'required' => TRUE),
            'valor_default' => array('label' => 'Valor', 'maxlength' => '9999'),
            'posicion' => array('label' => 'Posición', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'maxlength' => '50', 'required' => TRUE),
            'opciones' => array('label' => 'Opciones', 'maxlength' => '9999'),
            'etiqueta' => array('label' => 'Etiqueta', 'maxlength' => '50', 'required' => TRUE),
            'validacion' => array('label' => 'Validación', 'maxlength' => '50'),
            'editable' => array('label' => 'Editable', 'type' => 'integer', 'maxlength' => '1', 'required' => TRUE),
            'imprimible' => array('label' => 'imprimible', 'type' => 'integer', 'maxlength' => '1', 'required' => TRUE),
            'obligatorio' => array('label' => 'obligatorio', 'type' => 'integer', 'maxlength' => '1', 'required' => TRUE),
            'funcion' => array('label' => 'Función', 'maxlength' => '255'),
            'ayuda' => array('label' => 'Ayuda', 'maxlength' => '9999')
        );
        $this->requeridos = array('nombre', 'readonly', 'posicion', 'tipo', 'etiqueta');
        //$this ->unicos = array();
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
        if ($this->db->where('campo_id', $delete_id)->count_all_results('to2_datos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Dato.');
            return FALSE;
        }
        return TRUE;
    }
}
