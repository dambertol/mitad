<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Procesos_iniciadores_model extends MY_Model
{

    /**
     * Modelo de Iniciadores de Procesos
     * Autor: Leandro
     * Creado: 14/05/2021
     * Modificado: 14/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_procesos_iniciadores';
        $this->full_log = TRUE;
        $this->msg_name = 'Iniciador de Proceso';
        $this->id_name = 'id';
        $this->columnas = array('id', 'proceso_id', 'iniciador_tipo_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'proceso' => array('label' => 'Proceso', 'input_type' => 'combo', 'required' => TRUE),
            'iniciador_tipo' => array('label' => 'Iniciador de Tipo', 'input_type' => 'combo', 'required' => TRUE)
        );
        $this->requeridos = array('proceso_id', 'iniciador_tipo_id');
        $this->unicos = array(array('proceso_id', 'iniciador_tipo_id'));
        $this->default_join = array();
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * intersect_asignaciones: Actualiza asignaciones de tipos de iniciadores a proceos.
     *
     * @param int $proceso_id
     * @param array $new_asignaciones
     * @param bool $trans_enabled
     * @return bool
     */
    public function intersect_asignaciones($proceso_id, $new_asignaciones, $trans_enabled = false)
    {
        if ($trans_enabled)
        {
            $this->db->trans_begin();
        }
        $trans_ok = TRUE;
        $old_asignaciones = $this->get(array('proceso_id' => $proceso_id));
        if (!empty($old_asignaciones))
        {
            foreach ($old_asignaciones as $Old)
            {
                $old_asignaciones_array[$Old->id] = $Old->iniciador_tipo_id;
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
                    'proceso_id' => $proceso_id,
                    'iniciador_tipo_id' => $To_add
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
     * delete_detalles: Elimina los iniciadores asignados al proceso.
     *
     * @param int $proceso_id
     * @return bool
     */
    public function delete_detalles($proceso_id)
    {
        $this->db->where('proceso_id', $proceso_id);

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
