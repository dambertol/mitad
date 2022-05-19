<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pases_model extends MY_Model
{

    /**
     * Modelo de Pases
     * Autor: Leandro
     * Creado: 05/06/2018
     * Modificado: 11/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'tr_pases';
        $this->full_log = TRUE;
        $this->msg_name = 'Pase';
        $this->id_name = 'id';
        $this->columnas = array('id', 'tramite_id', 'estado_origen_id', 'estado_destino_id', 'fecha', 'usuario_origen', 'usuario_destino', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
            'estado_origen' => array('label' => 'Origen', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'estado_destino' => array('label' => 'Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );
        $this->requeridos = array('tramite_id', 'estado_origen_id', 'estado_destino_id', 'fecha', 'usuario_origen');
        //$this->unicos = array();
        $this->default_join = array(
            array('tr_estados EO', 'EO.id = tr_pases.estado_origen_id', 'LEFT'),
            array('tr_oficinas OO', 'OO.id = EO.oficina_id', 'LEFT', "CONCAT(EO.nombre, ' (', OO.nombre, ')') as estado_origen"),
            array('tr_estados ED', 'ED.id = tr_pases.estado_destino_id', 'LEFT'),
            array('tr_oficinas OD', 'OD.id = ED.oficina_id', 'LEFT', "CONCAT(ED.nombre, ' (', OD.nombre, ')') as estado_destino"),
            array('users UO', 'UO.id = tr_pases.usuario_origen', 'LEFT', ''),
            array('personas PO', 'PO.id = UO.persona_id', 'LEFT', "CONCAT(PO.apellido, ', ', PO.nombre, ' (', UO.username, ')') as usuario_origen")
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
				SELECT T.id as t_id, P.id as p_id, P.fecha, P.observaciones, EO.id as eo_id, CONCAT(EO.nombre, ' (', OO.nombre, ')') as estado_origen, ED.id as ed_id, CONCAT(ED.nombre, ' (', OD.nombre, ')') as estado_destino, ED.mensaje as mensaje_destino
				FROM tr_tramites T 
				LEFT JOIN tr_pases P ON P.tramite_id = T.id 
				LEFT JOIN tr_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN tr_oficinas OO ON OO.id = EO.oficina_id 
				LEFT JOIN tr_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN tr_oficinas OD ON OD.id = ED.oficina_id 
				WHERE T.id = $tramite_id 
				ORDER BY P.fecha DESC LIMIT 1")->row();
                break;
            case 'municipal':
                $acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha, P.observaciones, EO.id as eo_id, CONCAT(EO.nombre, ' (', OO.nombre, ')') as estado_origen, ED.id as ed_id, CONCAT(ED.nombre, ' (', OD.nombre, ')') as estado_destino, ED.mensaje as mensaje_destino 
				FROM tr_tramites T 
				LEFT JOIN tr_pases P ON P.tramite_id = T.id 
				LEFT OUTER JOIN tr_pases PP ON PP.tramite_id = T.id AND P.fecha < PP.fecha
				LEFT JOIN tr_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN tr_oficinas OO ON OO.id = EO.oficina_id 
				LEFT JOIN tr_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN tr_oficinas OD ON OD.id = ED.oficina_id 
				LEFT JOIN tr_usuarios_oficinas UO ON UO.oficina_id = OD.id
				WHERE T.id = $tramite_id AND UO.user_id = $user_id AND PP.id IS NULL
				ORDER BY P.fecha DESC LIMIT 1")->row();
                break;
            case 'publico':
                $acceso_ultimo_pase = $this->db->query("
				SELECT T.id as t_id, P.id as p_id, P.fecha, P.observaciones, EO.id as eo_id, CONCAT(EO.nombre, ' (', OO.nombre, ')') as estado_origen, ED.id as ed_id, CONCAT(ED.nombre, ' (', OD.nombre, ')') as estado_destino, ED.mensaje as mensaje_destino
				FROM tr_tramites T
				LEFT JOIN tr_pases P ON P.tramite_id = T.id 
				LEFT OUTER JOIN tr_pases PP ON PP.tramite_id = T.id AND P.fecha < PP.fecha 
				LEFT JOIN tr_estados EO ON EO.id = P.estado_origen_id 
				LEFT JOIN tr_oficinas OO ON OO.id = EO.oficina_id 
				LEFT JOIN tr_estados ED ON ED.id = P.estado_destino_id 
				LEFT JOIN tr_oficinas OD ON OD.id = ED.oficina_id 
				WHERE T.id = $tramite_id AND ED.oficina_id = 1 AND PP.id IS NULL
				ORDER BY P.fecha DESC LIMIT 1")->row(); // ED.oficina_id = 1 Escribano
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
				SELECT tr_pases.*, ED.nombre as estado_destino, OD.id as oficina_destino_id, OD.nombre as oficina_destino, CONCAT(personas.apellido, ', ', personas.nombre) as escribano,  personas.email as email_escribano
				FROM tr_tramites 
				LEFT JOIN tr_pases ON tr_pases.tramite_id = tr_tramites.id 
				LEFT JOIN tr_estados ED ON ED.id = tr_pases.estado_destino_id 
				LEFT JOIN tr_oficinas OD ON OD.id = ED.oficina_id 
				LEFT JOIN tr_escribanos ON tr_escribanos.id = tr_tramites.escribano_id LEFT JOIN personas ON personas.id = tr_escribanos.persona_id
				WHERE tr_tramites.id = $tramite_id 
				ORDER BY tr_pases.fecha DESC LIMIT 1")->row();

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
        if ($this->db->where('pase_id', $delete_id)->count_all_results('tr_adjuntos') > 0)
        {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Adjunto.');
            return FALSE;
        }
        return TRUE;
    }
}
