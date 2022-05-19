<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Personas_model extends MY_Model
{

    /**
     * Modelo de Personas
     * Autor: Leandro
     * Creado: 01/06/2018
     * Modificado: 28/09/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'personas';
        $this->full_log = TRUE;
        $this->msg_name = 'Persona';
        $this->id_name = 'id';
        $this->columnas = array('id', 'dni', 'sexo', 'cuil', 'nombre', 'apellido', 'telefono', 'celular', 'email', 'domicilio_id', 'fecha_nacimiento', 'nacionalidad_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'dni' => array('label' => 'DNI', 'type' => 'natural', 'minlength' => '7', 'maxlength' => '8', 'required' => TRUE),
            'sexo' => array('label' => 'Sexo', 'input_type' => 'combo', 'id_name' => 'sexo', 'type' => 'bselect', 'required' => TRUE),
            'cuil' => array('label' => 'CUIL', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13', 'required' => TRUE),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
            'telefono' => array('label' => 'Teléfono', 'type' => 'telefono', 'maxlength' => '12', 'minlength' => '10'),
            'celular' => array('label' => 'Celular', 'type' => 'telefono', 'maxlength' => '12', 'minlength' => '10'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
            'fecha_nacimiento' => array('label' => 'Fecha Nacimiento', 'type' => 'date'),
            'nacionalidad' => array('label' => 'Nacionalidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('dni', 'sexo', 'cuil', 'nombre', 'apellido', 'email');
        $this->unicos = array('dni', 'email');
        $this->default_join = array(
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
            array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
            array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
            array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
                array(
                    "CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
                )
            ),
            array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad'))
        );
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * set_unique_dni: Setea como único solo al campo DNI.
     *
     * @return void
     */
    public function set_unique_dni()
    {
        $this->unicos = array('dni');
    }

    /**
     * get_user_id: Devuelve el user_id de la persona.
     *
     * @param int $persona_id
     * @return int $user_id
     */
    public function get_user_id($persona_id)
    {
        $persona = $this->get(array(
            'select' => 'users.id as user_id',
            'join' => array(
                array('users', 'users.persona_id = personas.id', 'LEFT'),
            ),
            'id' => $persona_id
        ));

        if (!empty($persona->user_id))
        {
            return $persona->user_id;
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
        if ($this->db->where('persona_id', $delete_id)->count_all_results('act_inspectores') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Inspector.');
            return FALSE;
        }
        if ($this->db->where('persona_id', $delete_id)->count_all_results('go_partes') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Parte.');
            return FALSE;
        }
        if ($this->db->where('persona_id', $delete_id)->count_all_results('na_menores') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Menor.');
            return FALSE;
        }
        if ($this->db->where('persona_id', $delete_id)->count_all_results('na_adultos_responsables') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Adulto Responsable.');
            return FALSE;
        }
        if ($this->db->where('persona_id', $delete_id)->count_all_results('na_parentezcos_personas') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Parentezco.');
            return FALSE;
        }
        if ($this->db->where('pariente_id', $delete_id)->count_all_results('na_parentezcos_personas') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Parentezco.');
            return FALSE;
        }
        if ($this->db->where('persona_id', $delete_id)->count_all_results('tr_escribanos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Escribano.');
            return FALSE;
        }
        if ($this->db->where('persona_id', $delete_id)->count_all_results('users') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Usuario.');
            return FALSE;
        }
        return TRUE;
    }
}
