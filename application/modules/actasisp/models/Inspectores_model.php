<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inspectores_model extends MY_Model
{

    /**
     * Modelo de Inspectores
     * Autor: Leandro
     * Creado: 24/10/2019
     * Modificado: 22/02/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'act_inspectores';
        $this->full_log = TRUE;
        $this->msg_name = 'Inspector';
        $this->id_name = 'id';
        $this->columnas = array('id', 'persona_id', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'persona' => array('label' => 'Persona', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'required' => TRUE)
        );
        $this->requeridos = array('persona_id');
        $this->unicos = array('persona_id');
        $this->default_join = array(
            array('personas', 'personas.id = act_inspectores.persona_id', 'LEFT',
                array(
                    'personas.sexo',
                    'personas.cuil',
                    'personas.dni',
                    'personas.nombre',
                    'personas.apellido',
                    'personas.telefono',
                    'personas.celular',
                    'personas.email',
                    'personas.fecha_nacimiento',
                    'personas.nacionalidad_id',
                    'personas.domicilio_id'
                )
            ),
            array('domicilios', 'domicilios.id = personas.domicilio_id', 'LEFT',
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
            array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad')),
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
     * get_user_id: Devuelve el user_id del inspector.
     *
     * @param int $inspector_id
     * @return int $user_id
     */
    public function get_user_id($inspector_id)
    {
        $inspector = $this->get(array(
            'select' => 'users.id as user_id',
            'join' => array(
                array('personas', 'personas.id = act_inspectores.persona_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id', 'LEFT'),
            ),
            'id' => $inspector_id
        ));

        if (!empty($inspector->user_id))
        {
            return $inspector->user_id;
        }
        else
        {
            return 0;
        }
    }

    /**
     * _can_delete: Devuelve TRUE si puede eliminarse el registro.
     *
     * @param int $delete_id
     * @return bool
     */
    protected function _can_delete($delete_id)
    {
        if ($this->db->where('inspector_id', $delete_id)->count_all_results('act_inspectores_actas') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Acta.');
            return FALSE;
        }
        return TRUE;
    }
}
