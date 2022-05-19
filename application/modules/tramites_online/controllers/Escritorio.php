<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 19/08/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
        $this->grupos_admin = array('admin', 'tramites_online_admin', 'tramites_online_consulta_general');
        $this->grupos_publico = array('tramites_online_publico');
        $this->grupos_area = array('tramites_online_area');
        // Inicializaciones necesarias colocar acá.

    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data['dashboard'] = FALSE;
        $data['graficos_data'] = $this->graficos_data();
        if (in_groups($this->grupos_publico, $this->grupos))
        {
            $data['agregar'] = TRUE;
            $data['tramites_frecuentes_data'] = $this->tramites_frecuentes_data();
        }

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Módulo Trámites A Distancia';
        $data['title'] = TITLE . ' - Escritorio';
        $data['accesos_esc'] = load_permisos_tramites_online_escritorio($this->grupos);
        $data['css'][] = 'vendor/c3/c3.min.css';
        $data['js'][] = 'vendor/d3/d3.min.js';
        $data['js'][] = 'vendor/c3/c3.min.js';
        $data['js'][] = 'js/tramites_online/base.js';
        $this->load_template('tramites_online/escritorio/content', $data);
    }

    private function graficos_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        //INICIALIZO FECHAS
        $ini = new DateTime('first day of this month');
        $fin = clone $ini;
        $ini->sub(new DateInterval('P11M'));
        $fin->add(new DateInterval('P1M'));
        $fin->sub(new DateInterval('P1D'));
        $ini_sql = $ini->format('Y-m-d');
        $fin_sql = $fin->format('Y-m-d');

        $db_param = array($ini_sql, $fin_sql);
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(to2_tramites.id) as cantidad "
                            . 'FROM to2_tramites '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_fin, '%m/%Y') as mes, COUNT(to2_tramites.id) as cantidad "
                            . 'FROM to2_tramites '
                            . 'WHERE DATE(fecha_fin) BETWEEN ? AND ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_fin ASC', $db_param)->result();
        }
        elseif (in_groups($this->grupos_area, $this->grupos))
        {
            $db_param[] = $this->session->userdata('user_id');
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(to2_tramites.fecha_inicio, '%m/%Y') as mes, COUNT(to2_tramites.id) as cantidad "
                            . 'FROM to2_tramites '
                            . 'LEFT JOIN to2_procesos ON to2_procesos.id = to2_tramites.proceso_id '
                            . 'LEFT JOIN to2_usuarios_oficinas ON to2_usuarios_oficinas.oficina_id = to2_procesos.oficina_id '
                            . 'WHERE DATE(to2_tramites.fecha_inicio) BETWEEN ? AND ? '
                            . 'AND to2_usuarios_oficinas.user_id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY to2_tramites.fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(to2_tramites.fecha_fin, '%m/%Y') as mes, COUNT(to2_tramites.id) as cantidad "
                            . 'FROM to2_tramites '
                            . 'LEFT JOIN to2_procesos ON to2_procesos.id = to2_tramites.proceso_id '
                            . 'LEFT JOIN to2_usuarios_oficinas ON to2_usuarios_oficinas.oficina_id = to2_procesos.oficina_id '
                            . 'WHERE DATE(to2_tramites.fecha_fin) BETWEEN ? AND ? '
                            . 'AND to2_usuarios_oficinas.user_id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY to2_tramites.fecha_fin ASC', $db_param)->result();
        }
        else
        {
            $db_param[] = $this->session->userdata('user_id');
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(to2_tramites.fecha_inicio, '%m/%Y') as mes, COUNT(to2_tramites.id) as cantidad "
                            . 'FROM to2_tramites '
                            . 'LEFT JOIN to2_iniciadores ON to2_iniciadores.id = to2_tramites.iniciador_id '
                            . 'LEFT JOIN personas ON personas.id = to2_iniciadores.persona_id '
                            . 'LEFT JOIN users ON users.persona_id = personas.id '
                            . 'WHERE DATE(to2_tramites.fecha_inicio) BETWEEN ? AND ? AND users.id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY to2_tramites.fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(to2_tramites.fecha_fin, '%m/%Y') as mes, COUNT(to2_tramites.id) as cantidad "
                            . 'FROM to2_tramites '
                            . 'LEFT JOIN to2_iniciadores ON to2_iniciadores.id = to2_tramites.iniciador_id '
                            . 'LEFT JOIN personas ON personas.id = to2_iniciadores.persona_id '
                            . 'LEFT JOIN users ON users.persona_id = personas.id '
                            . 'WHERE DATE(to2_tramites.fecha_fin) BETWEEN ? AND ? AND users.id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY to2_tramites.fecha_fin ASC', $db_param)->result();
        }
        $grafico_iniciados = array(array('x'), array('iniciados'), array('finalizados'));

        $iniciados_array = array();
        if (!empty($iniciados_mes))
        {
            foreach ($iniciados_mes as $Mes)
            {
                $iniciados_array[$Mes->mes] = $Mes->cantidad;
            }
        }

        $finalizados_array = array();
        if (!empty($finalizados_mes))
        {
            foreach ($finalizados_mes as $Mes)
            {
                $finalizados_array[$Mes->mes] = $Mes->cantidad;
            }
        }

        while ($ini <= $fin)
        {
            $grafico_iniciados[0][] = $ini->format('m/Y');
            $grafico_iniciados[1][] = !empty($iniciados_array[$ini->format('m/Y')]) ? $iniciados_array[$ini->format('m/Y')] : 0;
            $grafico_iniciados[2][] = !empty($finalizados_array[$ini->format('m/Y')]) ? $finalizados_array[$ini->format('m/Y')] : 0;
            $ini->add(new DateInterval('P1M'));
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $pendientes = $this->db->query("
			SELECT COALESCE(to2_oficinas.nombre, 'SOLICITANTE') as nombre, COUNT(1) as cantidad
			FROM to2_tramites
			LEFT JOIN to2_pases ON to2_pases.tramite_id = to2_tramites.id
			LEFT OUTER JOIN to2_pases P ON P.tramite_id = to2_tramites.id AND to2_pases.fecha_inicio < P.fecha_inicio
			LEFT JOIN to2_estados ON to2_estados.id = to2_pases.estado_destino_id
			LEFT JOIN to2_oficinas ON to2_oficinas.id = to2_estados.oficina_id
			WHERE P.id IS NULL AND to2_tramites.fecha_fin IS NULL
			GROUP BY nombre")->result();
        }
        else if (in_groups($this->grupos_area, $this->grupos))
        {
            $db_param_pendientes = $this->session->userdata('user_id');
            $pendientes = $this->db->query("
			SELECT COALESCE(to2_oficinas.nombre, 'SOLICITANTE') as nombre, COUNT(1) as cantidad
			FROM to2_tramites
			LEFT JOIN to2_pases ON to2_pases.tramite_id = to2_tramites.id
			LEFT OUTER JOIN to2_pases P ON P.tramite_id = to2_tramites.id AND to2_pases.fecha_inicio < P.fecha_inicio
			LEFT JOIN to2_estados ON to2_estados.id = to2_pases.estado_destino_id
			LEFT JOIN to2_oficinas ON to2_oficinas.id = to2_estados.oficina_id
                        LEFT JOIN to2_procesos ON to2_procesos.id = to2_tramites.proceso_id
                        LEFT JOIN to2_usuarios_oficinas ON to2_usuarios_oficinas.oficina_id = to2_procesos.oficina_id
			WHERE P.id IS NULL AND to2_usuarios_oficinas.user_id = ? AND to2_tramites.fecha_fin IS NULL
			GROUP BY nombre", $db_param_pendientes)->result();
        }
        else
        {
            $db_param_pendientes = $this->session->userdata('user_id');
            $pendientes = $this->db->query("
			SELECT COALESCE(to2_oficinas.nombre, 'SOLICITANTE') as nombre, COUNT(1) as cantidad
			FROM to2_tramites
			LEFT JOIN to2_pases ON to2_pases.tramite_id = to2_tramites.id
			LEFT OUTER JOIN to2_pases P ON P.tramite_id = to2_tramites.id AND to2_pases.fecha_inicio < P.fecha_inicio
			LEFT JOIN to2_estados ON to2_estados.id = to2_pases.estado_destino_id
			LEFT JOIN to2_oficinas ON to2_oficinas.id = to2_estados.oficina_id
			LEFT JOIN to2_iniciadores ON to2_iniciadores.id = to2_tramites.iniciador_id
			LEFT JOIN personas ON personas.id = to2_iniciadores.persona_id
			LEFT JOIN users ON users.persona_id = personas.id
			WHERE P.id IS NULL AND users.id = ? AND to2_tramites.fecha_fin IS NULL
			GROUP BY nombre", $db_param_pendientes)->result();
        }

        $grafico_pendientes = array();
        if (!empty($pendientes))
        {
            foreach ($pendientes as $Pendiente)
            {
                $grafico_pendientes[] = array($Pendiente->nombre, $Pendiente->cantidad);
            }
        }

        return array('grafico_iniciados' => json_encode($grafico_iniciados), 'grafico_pendientes' => json_encode($grafico_pendientes));
    }

    private function tramites_frecuentes_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        
        $this->load->model('tramites_online/Iniciadores_model');
        $this->load->model('tramites_online/Procesos_model');
        $this->load->model('tramites_online/Procesos_iniciadores_model');

        $tramites_frecuentes = array();

        // BUSCA EL INICIADOR (PERSONA) ASOCIADA AL USUARIO ACTUAL
        $persona = $this->Iniciadores_model->get(array(
            'select' => array('to2_iniciadores.tipo_id'),
            'join' => array(
                array('personas', 'personas.id = to2_iniciadores.persona_id', 'LEFT'),
                array('users', 'users.persona_id = personas.id')
            ),
            'where' => array('users.id = ' . $this->session->userdata('user_id'))
        ));

        if (!empty($persona))
        {
            // BUSCA TODOS LOS PROCESOS PUBLICOS DISPONIBLES PARA LA PERSONA
            $procesos = $this->Procesos_model->get(
                array(
                    'join' => array(
                        array('to2_procesos_iniciadores', "to2_procesos_iniciadores.proceso_id = to2_procesos.id AND (to2_procesos_iniciadores.iniciador_tipo_id = {$persona[0]->tipo_id} OR to2_procesos_iniciadores.iniciador_tipo_id = 1)"),            
                    ),
                    'visibilidad' => 'Público'
                    )
                );
                
                if (!empty($procesos))
                {
                    $indice = 0;
                    foreach ($procesos as $Proceso)
                    {
                        
                    $Iniciador = $this->Procesos_iniciadores_model->get(
                        array(
                            'select' => array('to2_procesos_iniciadores.iniciador_tipo_id'),
                             'join' => array(
                                 array('to2_iniciadores', 'to2_procesos_iniciadores.iniciador_tipo_id = to2_iniciadores.tipo_id'),
                                 array('to2_procesos', "{$Proceso->id} = to2_procesos_iniciadores.proceso_id")
                             )
                            ),
                   
                        );

                    $tramites_frecuentes[$indice]['href'] = 'tramites_online/tramites/agregar/' . $Proceso->id;
                    $tramites_frecuentes[$indice]['title'] = $Proceso->nombre;
                    $tramites_frecuentes[$indice]['iniciador'] = $Iniciador[0]->iniciador_tipo_id;
                    switch ($Proceso->tipo)
                    {
                        case 'Consulta':
                            $tramites_frecuentes[$indice]['span'] = '<span class="label label-danger">Consulta</span>';
                            break;
                        case 'Trámite':
                            $tramites_frecuentes[$indice]['span'] = '<span class="label label-warning">Trámite</span>';
                    }
                    $indice++;
                }
            }
        }

        // MOSTRANDO TRANSFERENCIAS SI TIENE PERMISOS EN EL MODULO CORRESPONDIENTE
        $grupos_transferencias_publico = array('transferencias_publico');
        if (in_groups($grupos_transferencias_publico, $this->grupos))
        {
            $tramites_frecuentes[$indice]['href'] = 'transferencias/tramites/agregar';
            $tramites_frecuentes[$indice]['title'] = 'Transferencias';
            $tramites_frecuentes[$indice]['span'] = '<span class="label label-warning">Trámite</span>';
            $indice++;
        }

        return $tramites_frecuentes;
    }
}
