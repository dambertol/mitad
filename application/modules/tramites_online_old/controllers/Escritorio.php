<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 10/03/2021 (Leandro)
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

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Módulo Consultas OnLine';
        $data['title'] = TITLE . ' - Escritorio';
        $data['accesos_esc'] = load_permisos_tramites_online_escritorio($this->grupos);
        $data['css'][] = 'vendor/c3/c3.min.css';
        $data['js'][] = 'vendor/d3/d3.min.js';
        $data['js'][] = 'vendor/c3/c3.min.js';
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
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(to_tramites.id) as cantidad "
                            . 'FROM to_tramites '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_fin, '%m/%Y') as mes, COUNT(to_tramites.id) as cantidad "
                            . 'FROM to_tramites '
                            . 'WHERE DATE(fecha_fin) BETWEEN ? AND ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_fin ASC', $db_param)->result();
        }
        elseif (in_groups($this->grupos_area, $this->grupos))
        {
            $db_param[] = $this->session->userdata('user_id');
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(to_tramites.id) as cantidad "
                            . 'FROM to_tramites '
                            . 'LEFT JOIN to_tramites_tipos ON to_tramites.tipo_id = to_tramites_tipos.id '
                            . 'LEFT JOIN to_usuarios_areas ON to_usuarios_areas.area_id = to_tramites_tipos.area_id '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? '
                            . 'AND to_usuarios_areas.user_id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_fin, '%m/%Y') as mes, COUNT(to_tramites.id) as cantidad "
                            . 'FROM to_tramites '
                            . 'LEFT JOIN to_tramites_tipos ON to_tramites.tipo_id = to_tramites_tipos.id '
                            . 'LEFT JOIN to_usuarios_areas ON to_usuarios_areas.area_id = to_tramites_tipos.area_id '
                            . 'WHERE DATE(fecha_fin) BETWEEN ? AND ? '
                            . 'AND to_usuarios_areas.user_id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_fin ASC', $db_param)->result();
        }
        else
        {
            $db_param[] = $this->session->userdata('user_id');
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(to_tramites.id) as cantidad "
                            . 'FROM to_tramites '
                            . 'LEFT JOIN personas ON personas.id = to_tramites.persona_id '
                            . 'LEFT JOIN users ON users.persona_id = personas.id '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND users.id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_fin, '%m/%Y') as mes, COUNT(to_tramites.id) as cantidad "
                            . 'FROM to_tramites '
                            . 'LEFT JOIN personas ON personas.id = to_tramites.persona_id '
                            . 'LEFT JOIN users ON users.persona_id = personas.id '
                            . 'WHERE DATE(fecha_fin) BETWEEN ? AND ? AND users.id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_fin ASC', $db_param)->result();
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
			SELECT COALESCE(areas.nombre, 'SOLICITANTE') as nombre, COUNT(1) as cantidad
			FROM to_tramites
			LEFT JOIN to_pases ON to_pases.tramite_id = to_tramites.id
			LEFT OUTER JOIN to_pases P ON P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha
			LEFT JOIN to_estados ON to_estados.id = to_pases.estado_destino_id
			LEFT JOIN areas ON areas.id = to_pases.area_destino_id
			WHERE P.id IS NULL AND to_estados.id NOT IN (2,3)
			GROUP BY areas.nombre")->result(); // (HC) 2=Finalizado || 3=Cancelado
        }
        else if (in_groups($this->grupos_area, $this->grupos))
        {
            $db_param_pendientes = $this->session->userdata('user_id');
            $pendientes = $this->db->query("
			SELECT COALESCE(areas.nombre, 'SOLICITANTE') as nombre, COUNT(1) as cantidad
			FROM to_tramites
			LEFT JOIN to_pases ON to_pases.tramite_id = to_tramites.id
			LEFT OUTER JOIN to_pases P ON P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha
			LEFT JOIN to_estados ON to_estados.id = to_pases.estado_destino_id
			LEFT JOIN areas ON areas.id = to_pases.area_destino_id
                        LEFT JOIN to_tramites_tipos ON to_tramites.tipo_id = to_tramites_tipos.id
                        LEFT JOIN to_usuarios_areas ON to_usuarios_areas.area_id = to_tramites_tipos.area_id
			WHERE P.id IS NULL AND to_usuarios_areas.user_id = ? AND to_estados.id NOT IN (2,3)
			GROUP BY areas.nombre", $db_param_pendientes)->result(); // (HC) 2=Finalizado || 3=Cancelado
        }
        else
        {
            $db_param_pendientes = $this->session->userdata('user_id');
            $pendientes = $this->db->query("
			SELECT COALESCE(areas.nombre, 'SOLICITANTE') as nombre, COUNT(1) as cantidad
			FROM to_tramites
			LEFT JOIN to_pases ON to_pases.tramite_id = to_tramites.id
			LEFT OUTER JOIN to_pases P ON P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha
			LEFT JOIN to_estados ON to_estados.id = to_pases.estado_destino_id
			LEFT JOIN areas ON areas.id = to_pases.area_destino_id
			LEFT JOIN personas ON personas.id = to_tramites.persona_id
			LEFT JOIN users ON users.persona_id = personas.id
			WHERE P.id IS NULL AND users.id = ? AND to_estados.id NOT IN (2,3)
			GROUP BY areas.nombre", $db_param_pendientes)->result(); // (HC) 2=Finalizado || 3=Cancelado
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
}
