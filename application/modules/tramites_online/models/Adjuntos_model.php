<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

    /**
     * Modelo de Adjuntos
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 28/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'to2_adjuntos';
        $this->full_log = TRUE;
        $this->msg_name = 'Adjunto';
        $this->id_name = 'id';
        $this->columnas = array('id', 'tipo_id', 'nombre', 'descripcion', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'tipo_adjunto' => array('label' => 'Tipo', 'id_name' => 'tipo_id', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
            'descripcion' => array('label' => 'Descripción', 'maxlength' => '100'),
            'ruta' => array('label' => 'Ruta', 'maxlength' => '255', 'required' => TRUE),
            'tamanio' => array('label' => 'Tamaño', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
            'hash' => array('label' => 'Hash', 'required' => TRUE),
            'fecha_subida' => array('label' => 'Fecha Subida', 'type' => 'date', 'required' => TRUE),
            'usuario_subida' => array('label' => 'Usuario Subida', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE)
        );
        $this->requeridos = array('tipo_id', 'nombre', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida');
        $this->unicos = array(array('nombre', 'ruta'));
        $this->default_join = array(
            array('to2_adjuntos_tipos', 'to2_adjuntos_tipos.id = to2_adjuntos.tipo_id', 'LEFT', array('to2_adjuntos_tipos.nombre as tipo_adjunto')),
            array('to2_iniciadores', 'to2_iniciadores.id = to2_tramites.iniciador_id', 'LEFT'),
            array('to2_datos', 'to2_adjuntos.id = to2_datos.adjunto_id', 'LEFT'),
            array('to2_pases', 'to2_datos.pase_id = to2_pases.id', 'LEFT'),
            array('to2_tramites', 'to2_tramites.id = to2_pases.tramite_id', 'LEFT', array('to2_tramites.persona_id as persona_id')),
        );
        // Inicializaciones necesarias colocar acá.
    }

    /**
     * persona_adjunto: Devuelve el id de la persona al que pertenece el adjunto.
     *
     * @param string $ruta
     * @param string $nombre
     * @return int $persona_id
     */
    public function persona_adjunto($ruta, $nombre)
    {
        $this->db->select('persona_id');
        $this->db->where('ruta', $ruta);
        $this->db->where('nombre', $nombre);
        $this->db->join('to2_datos', 'to2_adjuntos.id = to2_datos.adjunto_id', 'LEFT');
        $this->db->join('to2_pases', 'to2_datos.pase_id = to2_pases.id', 'LEFT');
        $this->db->join('to2_tramites', 'to2_tramites.id = to2_pases.tramite_id', 'LEFT');
        $this->db->join('to2_iniciadores', 'to2_iniciadores.id = to2_tramites.iniciador_id', 'LEFT');
        $adjunto = $this->db->get($this->table_name)->row(0);

        if (!empty($adjunto))
        {
            return $adjunto->persona_id;
        }

        return NULL;

/*
        if (!empty($adjunto))
        {
            if (!empty($adjunto->tramite_id))
            {
                $tramite_id = $adjunto->tramite_id;
            }
            elseif (!empty($adjunto->pase_id))
            {
                $this->db->where('id', $adjunto->pase_id);
                $pase = $this->db->get('to2_pases')->row(0);
                if (!empty($pase->tramite_id))
                {
                    $tramite_id = $pase->tramite_id;
                }
            }
        }

        if (!empty($tramite_id))
        {
            $this->db->where('id', $tramite_id);
            $tramite = $this->db->get('to2_tramites')->row(0);
            ;
            if (!empty($tramite))
            {
                return $tramite->persona_id;
            }
        }

        return NULL;
*/
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
