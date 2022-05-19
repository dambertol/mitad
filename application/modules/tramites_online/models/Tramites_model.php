<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_model extends MY_Model
{

    /**
     * Modelo de Trámites
     * Autor: Leandro
     * Creado: 17/03/2020
     * Modificado: 19/06/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_tramites';
        $this->full_log = TRUE;
        $this->msg_name = 'Trámite';
        $this->id_name = 'id';
        $this->columnas = array('id', 'proceso_id', 'fecha_inicio', 'iniciador_id', 'editable', 'fecha_fin', 'observaciones', 'relacionado_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'proceso' => array('label' => 'Proceso', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('proceso_id', 'fecha_inicio', 'iniciador_id');
        //$this->unicos = array();
        $this->default_join = array(
            array('to2_procesos', 'to2_procesos.id = to2_tramites.proceso_id', 'LEFT', array('to2_procesos.oficina_id as oficina_id', 'to2_procesos.nombre as proceso')),
            array('to2_iniciadores', 'to2_iniciadores.id = to2_tramites.iniciador_id', 'LEFT'),
            array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT',
                array(
                    'personas.id as persona_id',
                    'personas.cuil',
                    'personas.dni',
                    'personas.nombre',
                    'personas.apellido',
                    'personas.telefono',
                    'personas.celular',
                    'personas.email',
                    'personas.domicilio_id'
                )
            )
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
        if ($this->db->where('tramite_id', $delete_id)->count_all_results('to2_pases') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pase.');
            return FALSE;
        }
        return TRUE;
    }
}
