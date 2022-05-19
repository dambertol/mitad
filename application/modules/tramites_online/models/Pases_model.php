<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pases_model extends MY_Model
{

    /**
     * Modelo de Pases
     * Autor: Leandro
     * Creado: 21/04/2021
     * Modificado: 19/06/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_pases';
        $this->full_log = TRUE;
        $this->msg_name = 'Pase';
        $this->id_name = 'id';
        $this->columnas = array('id', 'tramite_id', 'estado_origen_id', 'estado_destino_id', 'fecha_inicio', 'fecha_fin', 'usuario_origen', 'usuario_destino', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'fecha_inicio' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
            'estado_origen' => array('label' => 'Origen', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'estado_destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );
        $this->requeridos = array('tramite_id', 'estado_origen_id', 'estado_destino_id', 'fecha_inicio', 'usuario_origen');
        //$this->unicos = array();
        $this->default_join = array(
            array('to2_estados EO', 'EO.id = to2_pases.estado_origen_id', 'LEFT', ['EO.editable AS estado_origen_editable', 'EO.oficina_id AS estado_origen_oficina']),
            array('to2_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "EO.id as eo_id, CONCAT(EO.nombre, ' (', COALESCE(OO.nombre, ''), ')') as estado_origen"),
            array('to2_estados ED', 'ED.id = to2_pases.estado_destino_id', 'LEFT'),
            array('to2_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "ED.id as ed_id, CONCAT(ED.nombre, ' (', COALESCE(OD.nombre, ''), ')') as estado_destino")
        );
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * get_acceso_ultimo: Devuelve el último pase si el usuario pertenece a la misma oficina.
     *
     * @param int $tramite_id
     * @param int $user_id
     * @return array $acceso_ultimo_pase
     */
    public function get_acceso_ultimo($tramite_id, $user_id, $grupo)
    {

        switch ($grupo)
        {
            case 'admin':
                $acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha_inicio, P.observaciones, EO.id as eo_id, EO.nombre as estado_origen, ED.id as ed_id, ED.nombre as estado_destino, ED.final as final, ED.mensaje as mensaje_destino, OD.id as od_id, OD.nombre as oficina_destino
				FROM to2_tramites T 
				LEFT JOIN to2_pases P ON P.tramite_id = T.id 
				LEFT JOIN to2_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN to2_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN to2_oficinas OD ON OD.id = ED.oficina_id 
				WHERE T.id = $tramite_id 
				ORDER BY P.fecha_inicio DESC LIMIT 1")->row();
                break;
            case 'area':
                $acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha_inicio, P.observaciones, EO.id as eo_id, EO.nombre as estado_origen, ED.id as ed_id, ED.nombre as estado_destino, ED.mensaje as mensaje_destino, OD.id as od_id, OD.nombre as oficina_destino 
				FROM to2_tramites T 
				LEFT JOIN to2_pases P ON P.tramite_id = T.id 
				LEFT OUTER JOIN to2_pases PP ON PP.tramite_id = T.id AND P.fecha_inicio < PP.fecha_inicio
				LEFT JOIN to2_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN to2_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN to2_oficinas OD ON OD.id = ED.oficina_id  
				LEFT JOIN to2_usuarios_oficinas UO ON UO.oficina_id = OD.id
				WHERE T.id = $tramite_id AND UO.user_id = $user_id AND PP.id IS NULL
				ORDER BY P.fecha_inicio DESC LIMIT 1")->row();
                break;
            case 'publico':
                $acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha_inicio, P.observaciones, EO.id as eo_id, EO.nombre as estado_origen, ED.id as ed_id, ED.nombre as estado_destino, ED.mensaje as mensaje_destino, OD.id as od_id, OD.nombre as oficina_destino
				FROM to2_tramites T
				LEFT JOIN to2_pases P ON P.tramite_id = T.id 
				LEFT OUTER JOIN to2_pases PP ON PP.tramite_id = T.id AND P.fecha_inicio < PP.fecha_inicio 
				LEFT JOIN to2_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN to2_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN to2_oficinas OD ON OD.id = ED.oficina_id  
				WHERE T.id = $tramite_id AND ED.oficina_id IS NULL AND PP.id IS NULL
				ORDER BY P.fecha_inicio DESC LIMIT 1")->row();
                break;
        }

        return $acceso_ultimo_pase;
    }

    /**
     * get_ultimo: Devuelve el último pase.
     *
     * @param int $tramite_id
     * @return array $ultimo_pase
     */
    public function get_ultimo($tramite_id)
    {
        $ultimo_pase = $this->db->query("
				SELECT to2_pases.*, ED.nombre as estado_destino, AD.id as area_destino_id, AD.nombre as area_destino, CONCAT(personas.apellido, ', ', personas.nombre) as persona,  personas.email as email_persona
				FROM to2_tramites 
				LEFT JOIN to2_pases ON to2_pases.tramite_id = to2_tramites.id 
				LEFT JOIN to2_estados ED ON ED.id = to2_pases.estado_destino_id 
				LEFT JOIN areas AD ON AD.id = to2_pases.area_destino_id 
				LEFT JOIN personas ON personas.id = to2_tramites.persona_id
				WHERE to2_tramites.id = $tramite_id 
				ORDER BY to2_pases.fecha_inicio DESC LIMIT 1")->row();

        return $ultimo_pase;
    }

    /**
     * _can_delete: Devuelve TRUE si puede eliminarse el registro.
     *
     * @param int $delete_id
     * @return bool
     */
    protected function _can_delete($delete_id)
    {
        if ($this->db->where('pase_id', $delete_id)->count_all_results('to2_datos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Dato.');
            return FALSE;
        }
        if ($this->db->where('pase_id', $delete_id)->count_all_results('to2_tramites_padrones') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Padrón.');
            return FALSE;
        }
        return TRUE;
    }
}
