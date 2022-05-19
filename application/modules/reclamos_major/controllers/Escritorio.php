<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 17/12/2019
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'reclamos_major_admin', 'reclamos_major_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data['graficos_data'] = $this->graficos_data();
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Módulo Reclamos Major';
        $data['title'] = TITLE . ' - Escritorio';
        $data['accesos_esc'] = load_permisos_reclamos_major_escritorio($this->grupos);
        $data['css'][] = 'vendor/c3/c3.min.css';
        $data['js'][] = 'vendor/d3/d3.min.js';
        $data['js'][] = 'vendor/c3/c3.min.js';
        $this->load_template('reclamos_major/escritorio/content', $data);
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
        $ini_mes = new DateTime('first day of this month');
        $fin_mes = new DateTime('last day of this month');
        $ini->sub(new DateInterval('P11M'));
        $fin->add(new DateInterval('P1M'));
        $fin->sub(new DateInterval('P1D'));
        $ini_sql = $ini->format('Y-m-d');
        $fin_sql = $fin->format('Y-m-d');
        $ini_mes_sql = $ini_mes->format('Y-m-d');
        $fin_mes_sql = $fin_mes->format('Y-m-d');

        $db_param = array($ini_sql, $fin_sql);
        $iniciadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(rm_incidencias.id) as cantidad "
                        . 'FROM rm_incidencias '
                        . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? '
                        . 'GROUP BY mes '
                        . 'ORDER BY fecha_inicio ASC', $db_param)->result();

        $solucionadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_finalizacion, '%m/%Y') as mes, COUNT(rm_incidencias.id) as cantidad "
                        . 'FROM rm_incidencias '
                        . 'WHERE DATE(fecha_finalizacion) BETWEEN ? AND ? AND estado = "Solucionada"'
                        . 'GROUP BY mes '
                        . 'ORDER BY fecha_finalizacion ASC', $db_param)->result();

        $cerradas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_finalizacion, '%m/%Y') as mes, COUNT(rm_incidencias.id) as cantidad "
                        . 'FROM rm_incidencias '
                        . 'WHERE DATE(fecha_finalizacion) BETWEEN ? AND ? AND estado = "Cerrada"'
                        . 'GROUP BY mes '
                        . 'ORDER BY fecha_finalizacion ASC', $db_param)->result();

        $grafico_iniciados = array(array('x'), array('iniciadas'), array('solucionadas'), array('cerradas'));

        $iniciadas_array = array();
        if (!empty($iniciadas_mes))
        {
            foreach ($iniciadas_mes as $Mes)
            {
                $iniciadas_array[$Mes->mes] = $Mes->cantidad;
            }
        }

        $solucionadas_array = array();
        if (!empty($solucionadas_mes))
        {
            foreach ($solucionadas_mes as $Mes)
            {
                $solucionadas_array[$Mes->mes] = $Mes->cantidad;
            }
        }

        $cerradas_array = array();
        if (!empty($cerradas_mes))
        {
            foreach ($cerradas_mes as $Mes)
            {
                $cerradas_array[$Mes->mes] = $Mes->cantidad;
            }
        }

        $temp_ini = clone $ini;
        while ($temp_ini <= $fin)
        {
            $grafico_iniciados[0][] = $temp_ini->format('m/Y');
            $grafico_iniciados[1][] = !empty($iniciadas_array[$temp_ini->format('m/Y')]) ? $iniciadas_array[$temp_ini->format('m/Y')] : 0;
            $grafico_iniciados[2][] = !empty($solucionadas_array[$temp_ini->format('m/Y')]) ? $solucionadas_array[$temp_ini->format('m/Y')] : 0;
            $grafico_iniciados[3][] = !empty($cerradas_array[$temp_ini->format('m/Y')]) ? $cerradas_array[$temp_ini->format('m/Y')] : 0;
            $temp_ini->add(new DateInterval('P1M'));
        }

        $db_param_areas = array($ini_mes_sql, $fin_mes_sql);
        $areas = $this->db->query("
			SELECT areas.nombre, COALESCE(COUNT(rm_incidencias.id),0) as cantidad
			FROM rm_incidencias
			LEFT JOIN areas ON areas.id = rm_incidencias.area_id
			WHERE DATE(fecha_inicio) BETWEEN ? AND ?
			GROUP BY areas.nombre", $db_param_areas)->result();

        $grafico_areas = array();
        if (!empty($areas))
        {
            foreach ($areas as $Categoria)
            {
                $grafico_areas[] = array($Categoria->nombre, $Categoria->cantidad);
            }
        }

        return array('grafico_iniciados' => json_encode($grafico_iniciados), 'grafico_areas' => json_encode($grafico_areas));
    }
}
