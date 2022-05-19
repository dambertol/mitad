<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Localidades_model extends MY_Model
{

    /**
     * Modelo de Localidades
     * Autor: Leandro
     * Creado: 23/05/2018
     * Modificado: 01/10/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'localidades';
        $this->full_log = TRUE;
        $this->msg_name = 'Localidad';
        $this->id_name = 'id';
        $this->columnas = array('id', 'nombre', 'codigo', 'departamento_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
            'codigo' => array('label' => 'Código', 'maxlength' => '11'),
            'departamento' => array('label' => 'Departamento', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->requeridos = array('nombre', 'departamento_id');
        $this->unicos = array(array('nombre', 'departamento_id'), 'codigo');
        $this->default_join = array(
            array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
            array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT', array("CONCAT(departamentos.nombre, ' - ', provincias.nombre) as departamento"))
        );
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * get_provincia: Devuelve la provincia y departamento a la que pertenece la localidad.
     *
     * @param int $localidad_id
     * @return array $provincia
     */
    public function get_provincia($localidad_id)
    {
        $provincia = $this->get(array(
            'select' => 'provincias.id as id_p, provincias.nombre as nombre_p, provincias.codigo as codigo_p, departamentos.id as id_d, departamentos.nombre as nombre_d, departamentos.codigo as codigo_d, localidades.id as id_l, localidades.nombre as nombre_l, localidades.codigo as codigo_l',
            'join' => array(
                array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
                array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'),
            ),
            'id' => $localidad_id
        ));

        if (!empty($provincia))
        {
            return $provincia;
        }
        else
        {
            return NULL;
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
        if ($this->db->where('localidad_id', $delete_id)->count_all_results('domicilios') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Domicilio.');
            return FALSE;
        }
        return TRUE;
    }
}
