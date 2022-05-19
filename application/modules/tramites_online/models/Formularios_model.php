<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Formularios_model extends MY_Model
{

    /**
     * Modelo de Formularios
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 28/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_formularios';
        $this->full_log = TRUE;
        $this->msg_name = 'Formulario';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'descripcion', 'proceso_id', 'imprimible', 'orden_impresion', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'proceso' => array('label' => 'Proceso', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'descripcion' => array('label' => 'Descripción', 'maxlength' => '100', 'required' => TRUE),
            'imprimible' => array('label' => 'Imprimible', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'imprimible', 'required' => TRUE),
            'orden_impresion' => array('label' => 'Orden Impresion', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
        );
        $this->requeridos = array('nombre', 'descripcion', 'proceso_id', 'imprimible', 'orden_impresion');
        $this->unicos = array(array('nombre', 'proceso_id'), array('orden_impresion', 'proceso_id'));
        $this->default_join = array(
            array('to2_procesos', 'to2_procesos.id = to2_formularios.proceso_id', 'LEFT', array('to2_procesos.nombre as proceso'))
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
        if ($this->db->where('formulario_id', $delete_id)->count_all_results('to2_campos') > 0) {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Campo.');
            return FALSE;
        }
        if ($this->db->where('formulario_id', $delete_id)->count_all_results('to2_pasos') > 0) {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Paso.');
            return FALSE;
        }
        return TRUE;
    }
}
