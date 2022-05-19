<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 01/06/2018
     * Modificado: 20/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'transferencias_municipal', 'transferencias_area', 'transferencias_publico', 'transferencias_consulta_general');
        $this->grupos_admin = array('admin', 'transferencias_consulta_general');
        $this->grupos_publico = array('transferencias_publico');
        $this->grupos_municipal = array('transferencias_municipal', 'transferencias_area');
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
        $data['title_view'] = 'Módulo Transferencias';
        $data['title'] = TITLE . ' - Escritorio';
        $data['accesos_esc'] = load_permisos_transferencias_escritorio($this->grupos);
        $data['css'][] = 'vendor/c3/c3.min.css';
        $data['js'][] = 'vendor/d3/d3.min.js';
        $data['js'][] = 'vendor/c3/c3.min.js';
        $this->load_template('transferencias/escritorio/content', $data);
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
        if (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_municipal, $this->grupos))
        {
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(tr_tramites.id) as cantidad "
                            . 'FROM tr_tramites '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_fin, '%m/%Y') as mes, COUNT(tr_tramites.id) as cantidad "
                            . 'FROM tr_tramites '
                            . 'WHERE DATE(fecha_fin) BETWEEN ? AND ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_fin ASC', $db_param)->result();
        }
        else
        {
            $db_param[] = $this->session->userdata('user_id');
            $iniciados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(tr_tramites.id) as cantidad "
                            . 'FROM tr_tramites '
                            . 'LEFT JOIN tr_escribanos ON tr_escribanos.id = tr_tramites.escribano_id '
                            . 'LEFT JOIN personas ON personas.id = tr_escribanos.persona_id '
                            . 'LEFT JOIN users ON users.persona_id = personas.id '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND users.id = ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $finalizados_mes = $this->db->query("SELECT DATE_FORMAT(fecha_fin, '%m/%Y') as mes, COUNT(tr_tramites.id) as cantidad "
                            . 'FROM tr_tramites '
                            . 'LEFT JOIN tr_escribanos ON tr_escribanos.id = tr_tramites.escribano_id '
                            . 'LEFT JOIN personas ON personas.id = tr_escribanos.persona_id '
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

        //Promedios
        $iniciados = 0;
        $finalizados = 0;
        $meses = 0;

        while ($ini <= $fin)
        {
            $grafico_iniciados[0][] = $ini->format('m/Y');
            $grafico_iniciados[1][] = !empty($iniciados_array[$ini->format('m/Y')]) ? $iniciados_array[$ini->format('m/Y')] : 0;
            $grafico_iniciados[2][] = !empty($finalizados_array[$ini->format('m/Y')]) ? $finalizados_array[$ini->format('m/Y')] : 0;
            $ini->add(new DateInterval('P1M'));

            //Promedios
            $iniciados += !empty($iniciados_array[$ini->format('m/Y')]) ? $iniciados_array[$ini->format('m/Y')] : 0;
            $finalizados += !empty($finalizados_array[$ini->format('m/Y')]) ? $finalizados_array[$ini->format('m/Y')] : 0;
            $meses++;
        }

        //Promedios
        $promedios['iniciados'] = 0;
        $promedios['finalizados'] = 0;
        if ($meses > 0)
        {
            $promedios['iniciados'] = $iniciados / $meses;
            $promedios['finalizados'] = $finalizados / $meses;
        }

        $db_param_pendientes = array(array(1, 2, 3, 5, 6));
        if (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_municipal, $this->grupos))
        {
            $pendientes = $this->db->query("
			SELECT tr_oficinas.nombre, COALESCE(COUNT(tr_tramites.id),0) as cantidad
			FROM tr_tramites
			LEFT JOIN tr_pases ON tr_pases.tramite_id = tr_tramites.id
			LEFT OUTER JOIN tr_pases P ON P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha
			LEFT JOIN tr_estados ON tr_estados.id = tr_pases.estado_destino_id
			LEFT JOIN tr_oficinas ON tr_oficinas.id = tr_estados.oficina_id
			WHERE P.id IS NULL AND tr_oficinas.id IN ? AND tr_estados.id <> 12
			GROUP BY tr_oficinas.nombre", $db_param_pendientes)->result(); // (HC) 1=Escribano 2=Catastro 3=Aguas 5=Obras Privadas 6=Obras y Consorcios
        }
        else
        {
            $db_param_pendientes[] = $this->session->userdata('user_id');
            $pendientes = $this->db->query("
			SELECT tr_oficinas.nombre, COALESCE(COUNT(tr_tramites.id),0) as cantidad
			FROM tr_tramites
			LEFT JOIN tr_pases ON tr_pases.tramite_id = tr_tramites.id
			LEFT OUTER JOIN tr_pases P ON P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha
			LEFT JOIN tr_estados ON tr_estados.id = tr_pases.estado_destino_id
			LEFT JOIN tr_oficinas ON tr_oficinas.id = tr_estados.oficina_id
			LEFT JOIN tr_escribanos ON tr_escribanos.id = tr_tramites.escribano_id
			LEFT JOIN personas ON personas.id = tr_escribanos.persona_id
			LEFT JOIN users ON users.persona_id = personas.id
			WHERE P.id IS NULL AND tr_oficinas.id IN ? AND users.id = ? AND tr_estados.id <> 12
			GROUP BY tr_oficinas.nombre", $db_param_pendientes)->result(); // (HC) 1=Escribano 2=Catastro 3=Aguas 5=Obras Privadas 6=Obras y Consorcios
        }

        $grafico_pendientes = array();
        if (!empty($pendientes))
        {
            foreach ($pendientes as $Pendiente)
            {
                $grafico_pendientes[] = array($Pendiente->nombre, $Pendiente->cantidad);
            }
        }

        return array('grafico_iniciados' => json_encode($grafico_iniciados), 'grafico_pendientes' => json_encode($grafico_pendientes), 'promedios_iniciados' => $promedios);
    }
}
