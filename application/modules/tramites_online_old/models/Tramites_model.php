<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_model extends MY_Model
{

    /**
     * Modelo de Trámites
     * Autor: Leandro
     * Creado: 17/03/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to_tramites';
        $this->full_log = TRUE;
        $this->msg_name = 'Consulta';
        $this->id_name = 'id';
        $this->columnas = array('id', 'fecha_inicio', 'tipo_id', 'persona_id', 'padron', 'observaciones', 'fecha_fin', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999', 'required' => TRUE)
        );
        $this->requeridos = array('fecha_inicio', 'tipo_id', 'persona_id', 'observaciones');
        //$this->unicos = array();
        $this->default_join = array(
            array('to_tramites_tipos', 'to_tramites_tipos.id = to_tramites.tipo_id', 'LEFT',
                array(
                    'to_tramites_tipos.area_id as area'
                )
            ),
            array('to_tramites_categorias', 'to_tramites_categorias.id = to_tramites_tipos.categoria_id', 'LEFT',
                array(
                    "CONCAT(to_tramites_categorias.nombre, ' - ', to_tramites_tipos.nombre) as tipo",
                )
            ),
            array('personas', 'personas.id = to_tramites.persona_id', 'LEFT',
                array(
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
        if ($this->db->where('tramite_id', $delete_id)->count_all_results('to_adjuntos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Adjunto.');
            return FALSE;
        }
        if ($this->db->where('tramite_id', $delete_id)->count_all_results('to_pases') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pases.');
            return FALSE;
        }
        return TRUE;
    }
}
