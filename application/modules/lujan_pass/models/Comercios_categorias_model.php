<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Comercios_categorias_model extends MY_Model
{

    /**
     * Modelo de Categorías de Comercios
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 27/04/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'ta_comercios_categorias';
        $this->full_log = TRUE;
        $this->msg_name = 'Categoría de Comercio';
        $this->id_name = 'id';
        $this->columnas = array('id', 'comercio_id', 'categoria_id', 'principal', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'comercio' => array('label' => 'Comercio', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'categoria' => array('label' => 'Categoria', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'principal' => array('label' => 'Principal', 'id_name' => 'principal', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
        );
        $this->requeridos = array('comercio_id', 'categoria_id', 'principal');
        $this->unicos = array(array('comercio_id', 'categoria_id'));
        $this->default_join = array();
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * intersect_asignaciones: Actualiza asignaciones de comercio a categorias.
     *
     * @param int $comercio_id
     * @param array $new_asignaciones
     * @param bool $trans_enabled
     * @return bool
     */
    public function intersect_asignaciones($comercio_id, $new_asignaciones, $trans_enabled = false)
    {
        if ($trans_enabled)
        {
            $this->db->trans_begin();
        }
        $trans_ok = TRUE;
        $old_asignaciones = $this->get(array('comercio_id' => $comercio_id));
        if (!empty($old_asignaciones))
        {
            foreach ($old_asignaciones as $Old)
            {
                $old_asignaciones_array[$Old->id] = $Old->categoria_id;
            }

            //Delete asignaciones
            $asignaciones_to_delete = $this->array_diff_no_cast($old_asignaciones_array, $new_asignaciones);
            foreach ($asignaciones_to_delete as $To_delete_key => $To_delete_value)
            {
                $trans_ok &= $this->delete(array('id' => $To_delete_key), FALSE);
            }

            $asignaciones_to_add = $this->array_diff_no_cast($new_asignaciones, $old_asignaciones_array);
        }
        else
        {
            $asignaciones_to_add = $new_asignaciones;
        }

        if (!empty($asignaciones_to_add))
        {
            //Add asignaciones
            foreach ($asignaciones_to_add as $To_add)
            {
                $trans_ok &= $this->create(array(
                    'comercio_id' => $comercio_id,
                    'categoria_id' => $To_add,
                    'principal' => 'SI'
                        ), FALSE);
            }
        }

        if ($trans_enabled)
        {
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                return true;
            }
            else
            {
                $this->db->trans_rollback();
                return false;
            }
        }
        else
        {
            return $trans_ok;
        }
    }

    function array_diff_no_cast(&$ar1, &$ar2)
    {
        $diff = Array();
        foreach ($ar1 as $key => $val1)
        {
            if (array_search($val1, $ar2) === false)
            {
                $diff[$key] = $val1;
            }
        }

        return $diff;
    }

    /**
     * delete_asignaciones: Elimina asignaciones de comercios a categorias.
     *
     * @param int $comercio_id
     * @return bool
     */
    public function delete_asignaciones($comercio_id)
    {
        $this->db->where('comercio_id', $comercio_id);

        if ($this->db->delete($this->table_name))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
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
        return TRUE;
    }
}
