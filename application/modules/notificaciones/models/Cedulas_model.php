<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas_model extends MY_Model
{

    /**
     * Modelo de Cédulas
     * Autor: GENERATOR_MLC
     * Creado: 02/07/2019
     * Modificado: 02/07/2019 (GENERATOR_MLC)
     */
    public function __construct()
    {
        parent::__construct();
        $this->table_name = 'nv_cedulas';
        $this->full_log = FALSE;
        $this->msg_name = 'Cédula';
        $this->id_name = 'id';
        $this->columnas = array(
            'id', 'n_documento', 'anio', 'n_cedula', 'prioridad', 'texto',
            'rotacion_insp', 'observaciones', 'oficina_id', 'tipo_doc_id',
            'destinatario_id', 'domicilio_id', 'hoja_ruta_id',
            'notificador_id', 'notificador_suplente_id', 'zona_id', 'estado_id',
            'fecha_creacion', 'fecha_update', 'fecha_probable_entrega', 'fecha_delete');//, 'audi_usuario', 'audi_fecha', 'audi_accion', 'audi_usuario', 'audi_fecha', 'audi_accion');
        $this->fields = array(
            'n_documento' => array('label' => 'Nro de Expediente o Nota', 'maxlength' => '10'),
            'anio' => array('label' => 'Año del Expediente o Nota', 'type' => 'integer', 'maxlength' => '4'),
            'n_cedula' => array('label' => 'Nro de Cedula', 'type' => 'integer', 'maxlength' => '8', 'readonly' => ''),
            'texto' => array('label' => 'Cuerpo de la Cédula', 'form_type' => 'textarea', 'rows' => 15, 'maxlength' => '2500'),
//			'rotacion_insp' => array('label' => 'Rotacion de Insp', 'type' => 'integer', 'maxlength' => '1'),
            'observaciones' => array('label' => 'Observaciones y Aclaraciones <small>(Referencias para llegar al lugar)</small>', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '2500'),
//			'oficina_id' => array('label' => 'Oficina', 'input_type' => 'combo'),
//			'tipo_doc' => array('label' => 'Tipo de Doc', 'input_type' => 'combo'),
//			'destinatario' => array('label' => 'Destinatario', 'input_type' => 'combo'),
//			'domicilio' => array('label' => 'Domicilio', 'input_type' => 'combo'),
//			'hoja_ruta' => array('label' => 'Hoja de Ruta', 'input_type' => 'combo'),
//			'notificador' => array('label' => 'Notificador', 'input_type' => 'combo'),
//			'zona' => array('label' => 'Zona', 'input_type' => 'combo'),
//			'estado' => array('label' => 'Estado', 'input_type' => 'combo'),
//			'fecha_creacion' => array('label' => 'Fecha de Creacion', 'type' => 'date'),
            'fecha_probable_entrega' => array('label' => 'Fecha Probable de Entrega', 'type' => 'date', 'disabled' => 'yes'),
//			'fecha_update' => array('label' => 'Fecha de Update', 'type' => 'date'),
//			'fecha_notificacion' => array('label' => 'Fecha de Notificacion', 'type' => 'date'),
            'fecha_delete' => array('label' => 'Fecha de Delete', 'type' => 'date'),
//			'audi_usuario' => array('label' => 'Audi de Usuario', 'type' => 'integer', 'maxlength' => '11'),
//			'audi_fecha' => array('label' => 'Audi de Fecha', 'type' => 'date'),
//			'audi_accion' => array('label' => 'Audi de Accion', 'maxlength' => '1')
        );
        $this->requeridos = array('n_documento', 'anio', 'n_cedula', 'texto', 'oficina_id', 'tipo_doc_id', 'destinatario_id', 'domicilio_id');
        //$this->unicos = array();
        $this->default_join = array();
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
        if ($this->db->where('cedula_id', $delete_id)->count_all_results('nv_adjuntos') > 0) {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a adjuntos.');
            return FALSE;
        }
        if ($this->db->where('cedula_id', $delete_id)->count_all_results('nv_cedulas_devoluciones') > 0) {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a cedulas de devoluciones.');
            return FALSE;
        }
        if ($this->db->where('cedula_id', $delete_id)->count_all_results('nv_cedulas_movimientos') > 0) {
            $this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a cedulas de movimientos.');
            return FALSE;
        }
        return TRUE;
    }


    /**
     * Consulta si la cedula se encuentra en ese estado
     * @param $cedula_id
     * @param $estados
     * @return bool
     */
    public function tiene_estado($cedula_id, $estados)
    {
        $var = FALSE;
        if (is_array($estados)) {
            foreach ($estados as $estado_a_buscar) {
                $var |= $this->Cedulas_movimientos_model->get_last_movimiento($cedula_id)->tipo_movimiento_id == $estado_a_buscar ? TRUE : FALSE;
            }
        } else {
            $var |= $this->Cedulas_movimientos_model->get_last_movimiento($cedula_id)->tipo_movimiento_id == $estados ? TRUE : FALSE;
        }
        return $var;
    }


    /**
     * Obtiene el notificador indicado
     * @param null $id
     * @return null
     */
    public function get_notificador($id = NULL)
    {
        $notificador = NULL;
        if (!is_null($id)) {

            $this->db->select("users.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as usuario");
            $this->db->from("users");

            $this->db->join('personas', 'personas.id = users.persona_id');
            $this->db->join('users_groups', 'users_groups.user_id = users.id');
            $this->db->join('groups', 'users_groups.group_id = groups.id');


            $this->db->where('groups.name', 'notificaciones_notificadores');
            $this->db->where('users.active', '1');
            $this->db->where('users.id', $id);

            $this->db->order_by("personas.apellido, personas.nombre, username", "desc");

            $query = $this->db->get();

            $notificador = $query->row(); // first row

        }
        return $notificador;
    }


    /**
     * Obtiene la lista de todos los notificadores
     * @return mixed
     */
    public function list_notificadores()
    {
        $this->db->select("users.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as usuario");
        $this->db->from("users");

        $this->db->join('personas', 'personas.id = users.persona_id');
        $this->db->join('users_groups', 'users_groups.user_id = users.id');
        $this->db->join('groups', 'users_groups.group_id = groups.id');

        $this->db->where('groups.name', 'notificaciones_notificadores');
        $this->db->where('users.active', '1');

        $this->db->order_by("personas.apellido, personas.nombre, username", "desc");

        $query = $this->db->get();

        return $query->result();
    }

    /**
     * Busca las cedulas que tienen asociado el notificador y hojas de ruta
     * @param $notificador_id
     * @param null $hoja_ruta_id
     * @return array
     */
    public function get_cedulas_hojas_ruta($notificador_id, $hoja_ruta_id = NULL)
    {
        /*
        $this->db->select("*");
        $this->db->from("nv_cedulas");
        $this->db->where('notificador_id', $notificador_id);
        $this->db->where('hoja_ruta_id', $hoja_ruta_id);

        $query = $this->db->get();
        return $query->result();
        */

        return $this->get(
            [
                'where' => [
                    [
                        'column' => 'notificador_id',
                        'value' => $notificador_id
                    ],
                    [
                        'column' => 'hoja_ruta_id',
                        'value' => $hoja_ruta_id
                    ],
                ],
                'join' => [
                    [
                        'nv_destinatarios', 'nv_destinatarios.id = nv_cedulas.destinatario_id', 'LEFT',
                        [
                            'nv_destinatarios.nombre', 'nv_destinatarios.apellido', 'nv_destinatarios.tipo_identificacion', 'nv_destinatarios.n_identificacion',
                        ]
                    ],
                    [
                        'nv_domicilios', 'nv_domicilios.id = nv_cedulas.domicilio_id', 'LEFT',
                        [
                            'nv_domicilios.direccion', 'nv_domicilios.num', 'nv_domicilios.localidad', 'nv_domicilios.codigo_postal', 'nv_domicilios.alternativo as domicilio_alternativo',
                        ]
                    ],
                ],
            ]
        );

    }

    /**
     * Busca una sola cedula y los datos relacionados
     * @param $cedula_id
     * @return mixed
     */
    public function load_cedula($cedula_id)
    {
        return $this->get(
            [
                'where' => [
                    [
                        'column' => 'nv_cedulas.id',
                        'value' => $cedula_id
                    ]
                ],
                'join' => [
                    [
                        'nv_destinatarios', 'nv_destinatarios.id = nv_cedulas.destinatario_id', 'LEFT',
                        [
                            'nv_destinatarios.nombre', 'nv_destinatarios.apellido', 'nv_destinatarios.tipo_identificacion', 'nv_destinatarios.n_identificacion',
                        ]
                    ],
                    [
                        'nv_domicilios', 'nv_domicilios.id = nv_cedulas.domicilio_id', 'LEFT',
                        [
                            'nv_domicilios.direccion', 'nv_domicilios.num', 'nv_domicilios.localidad', 'nv_domicilios.codigo_postal', 'nv_domicilios.alternativo as domicilio_alternativo',
                        ]
                    ],
                ],
            ]
        )[0];
    }

    /**
     * Obtiene todas las cedulas y los datos relacionados
     * @return array
     */
    public function load_cedulas()
    {
        return $this->get(
            [
                'join' => [
                    [
                        'nv_destinatarios', 'nv_destinatarios.id = nv_cedulas.destinatario_id', 'LEFT',
                        [
                            'nv_destinatarios.nombre', 'nv_destinatarios.apellido', 'nv_destinatarios.tipo_identificacion', 'nv_destinatarios.n_identificacion',
                        ]
                    ],
                    [
                        'nv_domicilios', 'nv_domicilios.id = nv_cedulas.domicilio_id', 'LEFT',
                        [
                            'nv_domicilios.direccion', 'nv_domicilios.num', 'nv_domicilios.localidad', 'nv_domicilios.codigo_postal', 'nv_domicilios.alternativo as domicilio_alternativo',
                        ]
                    ],
                    [
                        'nv_cedulas_movimientos', 'nv_cedulas_movimientos.cedula_id = nv_cedulas.id', 'LEFT',
                        [

                        ]
                    ],
                    [
                        'nv_cedulas_movimientos P', 'P.cedula_id = nv_cedulas.id AND nv_cedulas_movimientos.fecha < P.fecha', 'LEFT',
                        [

                        ]
                    ],
                    [
                        'nv_cedulas_movimientos_tipos', 'nv_cedulas_movimientos.tipo_movimiento_id = nv_cedulas_movimientos_tipos.id', 'LEFT',
                        [
                            'nv_cedulas_movimientos.tipo_movimiento_id as estado_id', 'nv_cedulas_movimientos_tipos.descripcion as estado_desc'
                        ]
                    ],
                ],
                'where' => [
                    'P.id IS NULL'
                ],
            ]
        );
    }

}