<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Procesos_model extends MY_Model
{

    /**
     * Modelo de Procesos
     * Autor: Leandro
     * Creado: 22/04/2021
     * Modificado: 10/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_procesos';
        $this->full_log = TRUE;
        $this->msg_name = 'Proceso';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'oficina_id', 'email_responsable', 'link_guia', 'tipo', 'visibilidad', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'email_responsable' => array('label' => 'Email Responsable', 'type' => 'email', 'maxlength' => '50'),
            'link_guia' => array('label' => 'Link Guía', 'maxlength' => '255'),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'tipo', 'required' => TRUE),
            'visibilidad' => array('label' => 'Visibilidad', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'visibilidad', 'required' => TRUE),
            'iniciadores' => array('label' => 'Iniciadores', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'id_name' => 'iniciadores', 'required' => TRUE)
        );
        $this->requeridos = array('nombre', 'oficina_id', 'tipo', 'visibilidad');
        $this->unicos = array(array('nombre', 'oficina_id'));
        $this->default_join = array(
            array('to2_oficinas', 'to2_oficinas.id = to2_procesos.oficina_id', 'LEFT', array('to2_oficinas.nombre as oficina')),
            array('to2_procesos_iniciadores', 'to2_procesos_iniciadores.proceso_id = to2_procesos.id', 'LEFT'),
            array('to2_iniciadores_tipos', 'to2_iniciadores_tipos.id = to2_procesos_iniciadores.iniciador_tipo_id', 'LEFT', array('to2_iniciadores_tipos.nombre as oficina'))
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
        if ($this->db->where('proceso_id', $delete_id)->count_all_results('to2_estados') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Estado.');
            return FALSE;
        }
        if ($this->db->where('proceso_id', $delete_id)->count_all_results('to2_formularios') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Formulario.');
            return FALSE;
        }
        if ($this->db->where('proceso_id', $delete_id)->count_all_results('to2_tramites') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Trámite.');
            return FALSE;
        }
        if ($this->db->where('proceso_id', $delete_id)->count_all_results('to2_procesos_iniciadores') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Trámite.');
            return FALSE;
        }
        return TRUE;
    }
}
