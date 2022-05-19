<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Escritorio extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 12/04/2019
     * Modificado: 25/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_admin = array('admin', 'incidencias_admin', 'incidencias_consulta_general');
        $this->grupos_tecnico = array('incidencias_user');
        $this->grupos_permitidos = array('admin', 'incidencias_admin', 'incidencias_user', 'incidencias_area', 'incidencias_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['graficos_data'] = $this->graficos_data();
            $data['leyenda'] = 'Todos los sectores';
        }
        else if (in_groups($this->grupos_tecnico, $this->grupos))
        {
            $data['graficos_data'] = $this->graficos_data();
            $usuario_sectores = $this->Usuarios_sectores_model->get(array(
                'user_id' => $this->session->userdata('user_id'),
                'join' => array(
                    array('in_sectores', 'in_sectores.id = in_usuarios_sectores.sector_id', 'LEFT', array('in_sectores.descripcion as sector'))
                )
            ));
            $data['leyenda'] = 'Sectores: ';
            if (!empty($usuario_sectores))
            {
                foreach ($usuario_sectores as $Usuario_sector)
                {
                    $data['leyenda'] .= "$Usuario_sector->sector - ";
                }
            }
            $data['leyenda'] = trim($data['leyenda'], " - ");
        }
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Módulo Incidencias';
        $data['title'] = TITLE . ' - Escritorio';
        $data['accesos_esc'] = load_permisos_incidencias_escritorio($this->grupos);
        $data['css'][] = 'vendor/c3/c3.min.css';
        $data['js'][] = 'vendor/d3/d3.min.js';
        $data['js'][] = 'vendor/c3/c3.min.js';
        $this->load_template('incidencias/escritorio/content', $data);
    }

    private function graficos_data()
    {
        if (!in_groups($this->grupos_admin, $this->grupos) && !in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('incidencias/Usuarios_sectores_model');

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
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            $sectores_id = array();
            $usuario_sectores = $this->Usuarios_sectores_model->get(array('user_id' => $this->session->userdata('user_id')));
            if (!empty($usuario_sectores))
            {
                foreach ($usuario_sectores as $Usuario_sector)
                {
                    $sectores_id[] = $Usuario_sector->sector_id;
                }
            }
        }

        $db_param = array($ini_sql, $fin_sql);
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $iniciadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(in_incidencias.id) as cantidad "
                            . 'FROM in_incidencias '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $solucionadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_finalizacion, '%m/%Y') as mes, COUNT(in_incidencias.id) as cantidad "
                            . 'FROM in_incidencias '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND estado = "Solucionada"'
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $cerradas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_finalizacion, '%m/%Y') as mes, COUNT(in_incidencias.id) as cantidad "
                            . 'FROM in_incidencias '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND estado = "Cerrada"'
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();
        }
        else
        {
            $db_param[] = $sectores_id;
            $iniciadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_inicio, '%m/%Y') as mes, COUNT(in_incidencias.id) as cantidad "
                            . 'FROM in_incidencias '
                            . 'LEFT JOIN in_categorias ON in_categorias.id = in_incidencias.categoria_id '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND in_categorias.sector_id IN ? '
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $solucionadas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_finalizacion, '%m/%Y') as mes, COUNT(in_incidencias.id) as cantidad "
                            . 'FROM in_incidencias '
                            . 'LEFT JOIN in_categorias ON in_categorias.id = in_incidencias.categoria_id '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND in_categorias.sector_id IN ? AND estado = "Solucionada"'
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();

            $cerradas_mes = $this->db->query("SELECT DATE_FORMAT(fecha_finalizacion, '%m/%Y') as mes, COUNT(in_incidencias.id) as cantidad "
                            . 'FROM in_incidencias '
                            . 'LEFT JOIN in_categorias ON in_categorias.id = in_incidencias.categoria_id '
                            . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND in_categorias.sector_id IN ? AND estado = "Cerrada"'
                            . 'GROUP BY mes '
                            . 'ORDER BY fecha_inicio ASC', $db_param)->result();
        }
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
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $areas = $this->db->query("
			SELECT areas.nombre, COALESCE(COUNT(in_incidencias.id),0) as cantidad
			FROM in_incidencias
			LEFT JOIN areas ON areas.id = in_incidencias.area_id
			WHERE DATE(fecha_inicio) BETWEEN ? AND ?
			GROUP BY areas.nombre", $db_param_areas)->result();
        }
        else
        {
            $db_param_areas[] = $sectores_id;
            $areas = $this->db->query("
			SELECT areas.nombre, COALESCE(COUNT(in_incidencias.id),0) as cantidad
			FROM in_incidencias
			LEFT JOIN in_categorias ON in_categorias.id = in_incidencias.categoria_id 
			LEFT JOIN areas ON areas.id = in_incidencias.area_id
			WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND in_categorias.sector_id IN ?
			GROUP BY areas.nombre", $db_param_areas)->result();
        }

        $grafico_areas = array();
        if (!empty($areas))
        {
            foreach ($areas as $Categoria)
            {
                $grafico_areas[] = array($Categoria->nombre, $Categoria->cantidad);
            }
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $tecnicos = $this->db->query("
			SELECT users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario
			FROM users
			LEFT JOIN personas ON personas.id = users.persona_id 
			WHERE users.id IN (
				SELECT DISTINCT tecnico_id 
				FROM in_incidencias 
				WHERE tecnico_id IS NOT NULL AND estado = 'Solucionada'
			)
			GROUP BY id, usuario
			ORDER BY personas.apellido, personas.nombre")->result();
        }
        else
        {
            $db_param_tecnicos = array($sectores_id);
            $tecnicos = $this->db->query("
			SELECT users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario
			FROM users
			LEFT JOIN personas ON personas.id = users.persona_id 
			WHERE users.id IN (
				SELECT DISTINCT tecnico_id 
				FROM in_incidencias 
				JOIN in_categorias ON in_categorias.id = in_incidencias.categoria_id 
				WHERE tecnico_id IS NOT NULL AND estado = 'Solucionada' AND in_categorias.sector_id IN ?
			)
			GROUP BY id, usuario
			ORDER BY personas.apellido, personas.nombre", $db_param_tecnicos)->result();
        }
        $grafico_tecnicos = array(array('x'));
        if (!empty($tecnicos))
        {
            foreach ($tecnicos as $Tecnico)
            {
                $tecnicos_mes[$Tecnico->id] = $this->db->query("SELECT DATE_FORMAT(fecha_finalizacion, '%m/%Y') as mes, COUNT(in_incidencias.id) as cantidad "
                                . 'FROM in_incidencias '
                                . 'WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND tecnico_id = ? '
                                . 'GROUP BY mes '
                                . 'ORDER BY fecha_inicio ASC', array($ini_sql, $fin_sql, $Tecnico->id))->result();

                $grafico_tecnicos[] = array($Tecnico->usuario);
            }
        }

        $tecnicos_array = array();
        if (!empty($tecnicos_mes))
        {
            foreach ($tecnicos_mes as $Tecnico_id => $Tecnico)
            {
                foreach ($Tecnico as $Mes)
                {
                    $tecnicos_array[$Tecnico_id][$Mes->mes] = $Mes->cantidad;
                }
            }
        }

        $temp_ini = clone $ini;
        while ($temp_ini <= $fin)
        {
            $grafico_tecnicos[0][] = $temp_ini->format('m/Y');
            $cont = 1;
            if (!empty($tecnicos_mes))
            {
                foreach ($tecnicos_mes as $Tecnico_id => $Tecnico)
                {
                    $grafico_tecnicos[$cont][] = !empty($tecnicos_array[$Tecnico_id][$temp_ini->format('m/Y')]) ? $tecnicos_array[$Tecnico_id][$temp_ini->format('m/Y')] : 0;
                    $cont++;
                }
            }
            $temp_ini->add(new DateInterval('P1M'));
        }

        $db_param_categorias = array($ini_mes_sql, $fin_mes_sql);
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $categorias = $this->db->query("
			SELECT in_categorias.descripcion, COALESCE(COUNT(in_incidencias.id),0) as cantidad
			FROM in_incidencias
			LEFT JOIN in_categorias ON in_categorias.id = in_incidencias.categoria_id
			WHERE DATE(fecha_inicio) BETWEEN ? AND ?
			GROUP BY in_categorias.descripcion", $db_param_categorias)->result();
        }
        else
        {
            $db_param_categorias[] = $sectores_id;
            $categorias = $this->db->query("
			SELECT in_categorias.descripcion, COALESCE(COUNT(in_incidencias.id),0) as cantidad
			FROM in_incidencias
			LEFT JOIN in_categorias ON in_categorias.id = in_incidencias.categoria_id 
			WHERE DATE(fecha_inicio) BETWEEN ? AND ? AND in_categorias.sector_id IN ?
			GROUP BY in_categorias.descripcion", $db_param_categorias)->result();
        }

        $grafico_categorias = array();
        if (!empty($categorias))
        {
            foreach ($categorias as $Categoria)
            {
                $grafico_categorias[] = array($Categoria->descripcion, $Categoria->cantidad);
            }
        }

        return array('grafico_iniciados' => json_encode($grafico_iniciados), 'grafico_areas' => json_encode($grafico_areas), 'grafico_tecnicos' => json_encode($grafico_tecnicos), 'grafico_categorias' => json_encode($grafico_categorias));
    }
}
