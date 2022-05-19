<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estados_model extends MY_Model
{

    /**
     * Modelo de Estados
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 12/06/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_estados';
        $this->full_log = TRUE;
        $this->msg_name = 'Estado';
        $this->id_name = 'id';
        $this->columnas = array('id', 'proceso_id', 'nombre', 'inicial', 'final', 'editable', 'imprimible', 'oficina_id', 'mensaje', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'proceso' => array('label' => 'Proceso', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'inicial' => array('label' => 'Inicial', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'inicial', 'required' => TRUE),
            'final' => array('label' => 'Final', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'final', 'required' => TRUE),
            'editable' => array('label' => 'Editable', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'editable', 'required' => TRUE),
            'imprimible' => array('label' => 'Imprimible', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'imprimible', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'bselect'),
            'mensaje' => array('label' => 'Mensaje', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );
        $this->requeridos = array('proceso_id', 'nombre', 'inicial');
        $this->unicos = array(array('proceso_id', 'nombre'));
        $this->default_join = array(
            array('to2_procesos', 'to2_procesos.id = to2_estados.proceso_id', 'LEFT', array('to2_procesos.nombre as proceso')),
            array('to2_oficinas', 'to2_oficinas.id = to2_estados.oficina_id', 'LEFT', array('to2_oficinas.nombre as oficina'))
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
        if ($this->db->where('estado_id', $delete_id)->count_all_results('to2_pasos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Paso.');
            return FALSE;
        }
        if ($this->db->where('estado_origen_id', $delete_id)->count_all_results('to2_pases') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pase.');
            return FALSE;
        }
        if ($this->db->where('estado_destino_id', $delete_id)->count_all_results('to2_pases') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pase.');
            return FALSE;
        }
        if ($this->db->where('estado_id', $delete_id)->count_all_results('to2_estados_secuencias') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Secuencia.');
            return FALSE;
        }
        if ($this->db->where('estado_posterior_id', $delete_id)->count_all_results('to2_estados_secuencias') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Secuencia.');
            return FALSE;
        }
        return TRUE;
    }
}
