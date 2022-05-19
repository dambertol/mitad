<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Alertas_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        // Inicializaciones necesarias colocar acá.
    }

    public function get($grupos)
    {
        $alertas = array();

        $in_incidencias_asignadas = array('admin', 'incidencias_admin', 'incidencias_user');
        if (in_groups($in_incidencias_asignadas, $grupos))
        {
            if (in_groups(array('admin', 'incidencias_admin'), $grupos))
            {
                $url = 'incidencias/incidencias/listar';
            }
            else
            {
                $url = 'incidencias/incidencias/listar_tecnico';
            }
            $incidencias_asignadas = $this->in_incidencias_asignadas();
            if ($incidencias_asignadas->cantidad > 0)
            {
                if ($incidencias_asignadas->cantidad > 1)
                {
                    $alertas[] = new Alerta('Incidencias asignadas', $incidencias_asignadas->cantidad, $url, 'fa fa-warning');
                }
                else
                {
                    $alertas[] = new Alerta('Incidencias asignadas', $incidencias_asignadas->cantidad, $url, 'fa fa-warning');
                }
            }
        }

        $tr_tramites_pendientes_areas = array('admin', 'transferencias_municipal'); //PARA ADMIN NO FUNCIONA SI NO TIENE OFICINA ASOCIADA
        if (in_groups($tr_tramites_pendientes_areas, $grupos))
        {
            $tramites_pendientes = $this->tr_tramites_pendientes_areas();
            if ($tramites_pendientes->cantidad > 0)
            {
                if ($tramites_pendientes->cantidad > 1)
                {
                    $alertas[] = new Alerta('Trámites pendientes', $tramites_pendientes->cantidad, 'transferencias/tramites/bandeja_entrada', 'fa fa-exchange');
                }
                else
                {
                    $alertas[] = new Alerta('Trámite pendiente', $tramites_pendientes->cantidad, 'transferencias/tramites/bandeja_entrada', 'fa fa-exchange');
                }
            }
        }

        $tr_tramites_pendientes_publico = array('admin', 'transferencias_publico');
        if (in_groups($tr_tramites_pendientes_publico, $grupos))
        {
            $tramites_pendientes = $this->tr_tramites_pendientes_publico();
            if ($tramites_pendientes->cantidad > 0)
            {
                if ($tramites_pendientes->cantidad > 1)
                {
                    $alertas[] = new Alerta('Trámites pendientes', $tramites_pendientes->cantidad, 'transferencias/tramites/bandeja_entrada_publico', 'fa fa-exchange');
                }
                else
                {
                    $alertas[] = new Alerta('Trámite pendiente', $tramites_pendientes->cantidad, 'transferencias/tramites/bandeja_entrada_publico', 'fa fa-exchange');
                }
            }
        }

        $vc_vales_pendientes = array('admin', 'vales_combustible_hacienda', 'vales_combustible_areas');
        if (in_groups($vc_vales_pendientes, $grupos))
        {
            $vc_vales_pendientes_areas = array('vales_combustible_areas');
            if (in_groups($vc_vales_pendientes_areas, $grupos))
            {
                $url = 'vales_combustible/vales/listar_areas';
                $vales_pendientes = $this->vc_vales_pendientes_areas();
            }
            else
            {
                $url = 'vales_combustible/vales/listar_pendientes';
                $vales_pendientes = $this->vc_vales_pendientes();
            }
            if ($vales_pendientes->cantidad > 0)
            {
                if ($vales_pendientes->cantidad > 1)
                {
                    $alertas[] = new Alerta('Vales pendientes de aprobación', $vales_pendientes->cantidad, $url, 'fa fa-truck');
                }
                else
                {
                    $alertas[] = new Alerta('Vale pendiente de aprobación', $vales_pendientes->cantidad, $url, 'fa fa-truck');
                }
            }
        }

        $vc_vehiculos_pendientes = array('admin', 'vales_combustible_contaduria', 'vales_combustible_areas');
        if (in_groups($vc_vehiculos_pendientes, $grupos))
        {
            $vc_vehiculos_pendientes_areas = array('vales_combustible_areas');
            if (in_groups($vc_vehiculos_pendientes_areas, $grupos))
            {
                $vehiculos_pendientes = $this->vc_vehiculos_pendientes_areas();
            }
            else
            {
                $vehiculos_pendientes = $this->vc_vehiculos_pendientes();
            }
            if ($vehiculos_pendientes->cantidad > 0)
            {
                if ($vehiculos_pendientes->cantidad > 1)
                {
                    $alertas[] = new Alerta('Vehículos pendientes de aprobación', $vehiculos_pendientes->cantidad, 'vales_combustible/vehiculos/listar', 'fa fa-truck');
                }
                else
                {
                    $alertas[] = new Alerta('Vehículo pendiente de aprobación', $vehiculos_pendientes->cantidad, 'vales_combustible/vehiculos/listar', 'fa fa-truck');
                }
            }
        }

        $vc_vehiculos_seguro_vencido = array('admin', 'vales_combustible_contaduria', 'vales_combustible_areas');
        if (in_groups($vc_vehiculos_seguro_vencido, $grupos))
        {
            $vc_vehiculos_seguro_vencido_areas = array('vales_combustible_areas');
            if (in_groups($vc_vehiculos_seguro_vencido_areas, $grupos))
            {
                $vehiculos_seguro_vencido = $this->vc_vehiculos_seguro_vencido_areas();
            }
            else
            {
                $vehiculos_seguro_vencido = $this->vc_vehiculos_seguro_vencido();
            }
            if ($vehiculos_seguro_vencido->cantidad > 0)
            {
                if ($vehiculos_seguro_vencido->cantidad > 1)
                {
                    $alertas[] = new Alerta('Vehículos con seguro vencido', $vehiculos_seguro_vencido->cantidad, 'vales_combustible/vehiculos/listar', 'fa fa-truck');
                }
                else
                {
                    $alertas[] = new Alerta('Vehículo con seguro vencido', $vehiculos_seguro_vencido->cantidad, 'vales_combustible/vehiculos/listar', 'fa fa-truck');
                }
            }
        }

        return $alertas;
    }

    // CONSULTAS
    private function tr_tramites_pendientes_areas()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('tr_tramites')
                        ->join('tr_pases', 'tr_pases.tramite_id = tr_tramites.id ', 'left')
                        ->join('tr_pases P', 'P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha', 'left outer')
                        ->join('tr_estados', 'tr_estados.id = tr_pases.estado_destino_id ', 'left')
                        ->join('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id ', 'left')
                        ->join('tr_usuarios_oficinas', 'tr_usuarios_oficinas.oficina_id = tr_oficinas.id ', 'left')
                        ->where('P.id IS NULL')
                        ->where('tr_usuarios_oficinas.user_id', $this->session->userdata('user_id'))
                        ->get()->row();
    }

    private function in_incidencias_asignadas()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('in_incidencias')
                        ->join('in_categorias', 'in_categorias.id = in_incidencias.categoria_id', 'left')
                        ->where('in_incidencias.tecnico_id', $this->session->userdata('user_id'))
                        //->where('(in_categorias.sector_id = (SELECT sector_id FROM in_usuarios_sectores WHERE user_id = ' . $this->session->userdata('user_id') . ') OR in_incidencias.tecnico_id = ' . $this->session->userdata('user_id') . ')')
                        ->where_in('in_incidencias.estado', array('Pendiente', 'En Proceso'))
                        ->get()->row();
    }

    private function tr_tramites_pendientes_publico()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('tr_tramites')
                        ->join('tr_pases', 'tr_pases.tramite_id = tr_tramites.id ', 'left')
                        ->join('tr_pases P', 'P.tramite_id = tr_tramites.id AND tr_pases.fecha < P.fecha', 'left outer')
                        ->join('tr_estados', 'tr_estados.id = tr_pases.estado_destino_id ', 'left')
                        ->join('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id ', 'left')
                        ->join('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'left')
                        ->join('personas', 'personas.id = tr_escribanos.persona_id', 'left')
                        ->join('users', 'users.persona_id = personas.id', 'left')
                        ->where('P.id IS NULL')
                        ->where('tr_oficinas.id', 1) //Escribano
                        ->where('users.id', $this->session->userdata('user_id'))
                        ->get()->row();
    }

    private function vc_vales_pendientes()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('vc_vales')
                        ->where('estado', 'Pendiente')
                        ->get()->row();
    }

    private function vc_vales_pendientes_areas()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('vc_vales')
                        ->join('areas', 'areas.id = vc_vales.area_id', 'left')
                        ->join('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id ', 'left')
                        ->where('estado', 'Pendiente')
                        ->where('vc_usuarios_areas.user_id', $this->session->userdata('user_id'))
                        ->get()->row();
    }

    private function vc_vehiculos_pendientes()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('vc_vehiculos')
                        ->where('estado', 'Pendiente')
                        ->get()->row();
    }

    private function vc_vehiculos_pendientes_areas()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('vc_vehiculos')
                        ->join('areas', 'areas.id = vc_vehiculos.area_id', 'left')
                        ->join('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id ', 'left')
                        ->where('estado', 'Pendiente')
                        ->where('vc_usuarios_areas.user_id', $this->session->userdata('user_id'))
                        ->get()->row();
    }

    private function vc_vehiculos_seguro_vencido()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('vc_vehiculos')
                        ->where('estado', 'Aprobado')
                        ->where('vencimiento_seguro <', 'NOW()', FALSE)
                        ->get()->row();
    }

    private function vc_vehiculos_seguro_vencido_areas()
    {
        return $this->db
                        ->select('COUNT(1) as cantidad')
                        ->from('vc_vehiculos')
                        ->join('areas', 'areas.id = vc_vehiculos.area_id', 'left')
                        ->join('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id ', 'left')
                        ->where('estado', 'Aprobado')
                        ->where('vencimiento_seguro <', 'NOW()', FALSE)
                        ->where('vc_usuarios_areas.user_id', $this->session->userdata('user_id'))
                        ->get()->row();
    }
}

class Alerta
{

    public $label;
    public $value;
    public $url;
    public $iclass;

    public function __construct($label, $value, $url, $iclass)
    {
        $this->label = $label;
        $this->value = $value;
        $this->url = $url;
        $this->iclass = $iclass;
    }
}
