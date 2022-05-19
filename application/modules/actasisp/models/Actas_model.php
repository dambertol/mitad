<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Actas_model extends MY_Model
{

    /**
     * Modelo de Actas
     * Autor: Leandro
     * Creado: 24/10/2019
     * Modificado: 20/05/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'act_actas';
        $this->full_log = TRUE;
        $this->msg_name = 'Acta';
        $this->id_name = 'id';
        $this->columnas = array('id', 'numero', 'tipo', 'fecha', 'estado', 'padron_municipal', 'domicilio_id', 'motivo_id', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'numero' => array('label' => 'Numero', 'maxlength' => '20', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE, 'id_name' => 'tipo'),
            'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
            'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE, 'id_name' => 'estado'),
            'padron_municipal' => array('label' => 'Padron', 'type' => 'integer', 'maxlength' => '10'),
            'inspector_1' => array('label' => 'Inspector 1', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'inspector_2' => array('label' => 'Inspector 2', 'input_type' => 'combo', 'type' => 'bselect'),
            'motivo' => array('label' => 'Motivo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );
        $this->requeridos = array('numero', 'tipo', 'fecha', 'estado', 'domicilio_id', 'motivo_id');
        $this->unicos = array('numero');
        $this->default_join = array(
            array('act_motivos', 'act_motivos.id = act_actas.motivo_id', 'LEFT', array("CONCAT(act_motivos.codigo, ' - ', act_motivos.motivo) as motivo")),
            array('act_inspectores_actas IA1', 'IA1.acta_id = act_actas.id AND IA1.posicion = 1', 'LEFT', array('IA1.id as inspector_acta_1_id', 'IA1.inspector_id as inspector_1_id')),
            array('act_inspectores I1', 'IA1.inspector_id = I1.id', 'LEFT'),
            array('personas P1', 'P1.id = I1.persona_id', 'LEFT', array("CONCAT(P1.apellido, ', ', P1.nombre, ' (', P1.dni,  ')') as inspector_1")),
            array('act_inspectores_actas IA2', 'IA2.acta_id = act_actas.id AND IA2.posicion = 2', 'LEFT', array('IA2.id as inspector_acta_2_id', 'IA2.inspector_id as inspector_2_id')),
            array('act_inspectores I2', 'IA2.inspector_id = I2.id', 'LEFT'),
            array('personas P2', 'P2.id = I2.persona_id', 'LEFT', array("CONCAT(P2.apellido, ', ', P2.nombre, ' (', P2.dni,  ')') as inspector_2")),
            array('domicilios', 'domicilios.id = act_actas.domicilio_id', 'LEFT',
                array(
                    'domicilios.calle',
                    'domicilios.barrio',
                    'domicilios.altura',
                    'domicilios.piso',
                    'domicilios.dpto',
                    'domicilios.manzana',
                    'domicilios.casa',
                    'domicilios.localidad_id'
                )
            ),
            array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
            array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
            array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
                array(
                    "CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
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
        if ($this->db->where('acta_id', $delete_id)->count_all_results('act_inspectores_actas') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Inspector.');
            return FALSE;
        }
        return TRUE;
    }
}
