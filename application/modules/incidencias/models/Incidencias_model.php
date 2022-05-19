<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Incidencias_model extends MY_Model
{

    /**
     * Modelo de Incidencias
     * Autor: Leandro
     * Creado: 12/04/2019
     * Modificado: 25/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'in_incidencias';
        $this->full_log = TRUE;
        $this->msg_name = 'Incidencia';
        $this->id_name = 'id';
        $this->columnas = array('id', 'fecha_inicio', 'area_id', 'contacto', 'telefono', 'categoria_id', 'detalle', 'tecnico_id', 'estado', 'fecha_finalizacion', 'resolucion', 'user_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'fecha_inicio' => array('label' => 'Fecha Inicio', 'type' => 'datetime', 'required' => TRUE),
            'contacto' => array('label' => 'Contacto', 'maxlength' => '100', 'required' => TRUE),
            'telefono' => array('label' => 'Teléfono', 'maxlength' => '50'),
            'sector' => array('label' => 'Sector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'detalle' => array('label' => 'Detalle', 'form_type' => 'textarea', 'rows' => 5),
            'tecnico' => array('label' => 'Técnico', 'input_type' => 'combo', 'type' => 'bselect')
        );
        $this->requeridos = array('area_id', 'fecha_inicio', 'contacto', 'categoria_id');
        //$this->unicos = array();
        $this->default_join = array(
            array('areas', 'areas.id = in_incidencias.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area")),
            array('in_categorias', 'in_categorias.id = in_incidencias.categoria_id', 'LEFT', array('in_categorias.sector_id as sector_id', 'in_categorias.descripcion as categoria')),
            array('in_sectores', 'in_sectores.id = in_categorias.sector_id', 'LEFT', array('in_sectores.descripcion as sector')),
            array('users UT', 'UT.id = in_incidencias.tecnico_id', 'LEFT'),
            array('personas PT', 'PT.id = UT.persona_id', 'LEFT', array("CONCAT(PT.apellido, ', ', PT.nombre, ' (', UT.username, ')') as tecnico")),
            array('users UC', 'UC.id = in_incidencias.user_id', 'LEFT'),
            array('personas PC', 'PC.id = UC.persona_id', 'LEFT', array("CONCAT(PC.apellido, ', ', PC.nombre, ' (', UC.username, ')') as user"))
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
        if ($this->db->where('incidencia_id', $delete_id)->count_all_results('in_observaciones_incidencias') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Observación.');
            return FALSE;
        }
        return TRUE;
    }
}
