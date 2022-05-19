<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Fichadas extends MY_Controller
{

    /**
     * Controlador de Fichadas
     * Autor: Leandro
     * Creado: 04/07/2016
     * Modificado: 11/04/2018 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user', 'asistencia_consulta_general');
        $this->grupos_rrhh = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_consulta_general');
        $this->grupos_user = array('asistencia_user');
        $this->grupos_solo_consulta = array('asistencia_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        redirect('asistencia/fichadas/ver', 'refresh');
    }

    public function ver($labo_Codigo = NULL, $anio = NULL, $mes = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        if (empty($labo_Codigo) || !ctype_digit($labo_Codigo))
        {
            redirect("asistencia/fichadas/ver/" . $this->session->userdata('username'), 'refresh');
        }
        if (in_groups($this->grupos_user, $this->grupos) && $labo_Codigo !== $this->session->userdata('username'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $ahora = new DateTime();
        if (empty($anio) || !ctype_digit($anio))
        {
            $anio = $ahora->format('Y');
        }
        if (empty($mes) || !ctype_digit($mes) || $mes < 1 || $mes > 12)
        {
            $mes = $ahora->format('m');
        }
        $fecha_ini = "$anio-$mes-01";
        $fecha_fin = "$anio-" . ($mes + 1) . "-01";
        $inicio = DateTime::createFromFormat("Y-m-d", $fecha_ini);
        $fin = DateTime::createFromFormat("Y-m-d", $fecha_fin);
        $data = $this->get_data_fichadas($labo_Codigo, $inicio, $fin);
        $data['labo_Codigo'] = $labo_Codigo;
        $data['anio'] = $anio;
        $data['mes'] = $mes;
        $data['mes_planilla'] = ucfirst(strftime("%B %Y", strtotime($fecha_ini)));
        $data['error'] = !empty($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $data['css'] = 'css/asistencia/fichadas.css';
        $data['title'] = TITLE . ' - Ver Fichada';
        $this->load_template('asistencia/fichadas/fichadas_ver', $data);
    }

    private function get_data_fichadas($labo_Codigo, $fecha_ini, $fecha_fin)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        if (in_groups($this->grupos_user, $this->grupos) && $labo_Codigo !== $this->session->userdata('username'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        try
        {
            $http_response_empleado = $guzzleHttp->request('GET', "personas/datos", ['query' => ['labo_Codigo' => $labo_Codigo, 'fecha' => date_format($fecha_ini, 'Ymd'), 'fecha_fin' => date_format($fecha_fin, 'Ymd')]]);
            $empleado = json_decode($http_response_empleado->getBody()->getContents());
        } catch (Exception $e)
	{
            $empleado = NULL;
        }

        if (!empty($empleado))
        {
            try
            {
                $http_response_novedades = $guzzleHttp->request('GET', "personas/novedades", ['query' => ['labo_Codigo' => $labo_Codigo, 'fecha_inicio' => date_format($fecha_ini, 'Ymd'), 'fecha_fin' => date_format($fecha_fin, 'Ymd'), 'vari_Nombres' => array('@EXTMAYCUA', '@TURNOROTA')]]);
                $novedades = json_decode($http_response_novedades->getBody()->getContents());
            } catch (Exception $e)
            {
                $novedades = NULL;
            }

            if (!in_groups($this->grupos_rrhh, $this->grupos) && $labo_Codigo !== $this->session->userdata('username'))
            {
                $this->load->model('asistencia/Usuarios_oficinas_model');
                $ofi_Oficina = array();
                $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id')));
                if (!empty($oficinas))
                {
                    foreach ($oficinas as $Oficina)
                    {
                        $ofi_Oficina[] = $Oficina->ofi_Oficina;
                    }
                }
                if (!in_array($empleado->ofi_Oficina, $ofi_Oficina))
                {
                    show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
                }
            }
            $data['empleado'] = $empleado;
            $data['novedades'] = $novedades;
        }
        else
        {
            $data['error'] = '<br />Error al conectarse a Major<br />Intente nuevamente más tarde';
            return $data;
        }

        try
        {
            $http_response_ausencias = $guzzleHttp->request('GET', "personas/ausencias", ['query' => ['labo_Codigo' => $labo_Codigo, 'fecha_inicio' => date_format($fecha_ini, 'Ymd'), 'fecha_fin' => date_format($fecha_fin, 'Ymd')]]);
            $ausencias_list = json_decode($http_response_ausencias->getBody()->getContents());
        } catch (Exception $e)
        {
            $ausencias_list = NULL;
        }

        $array_temp_ausencias = array();
        if (!empty($ausencias_list))
        {
            foreach ($ausencias_list as $Ausencia)
            {
                $fecha_ini_aus = new DateTime($Ausencia->ause_FechaInicio);
                $fecha_fin_aus = new DateTime($Ausencia->ause_FechaFin);
                do
                {
                    $array_temp_ausencias[date_format($fecha_ini_aus, 'd-m-Y')]['A'] = $Ausencia->moau_Descripcion;
                    $fecha_ini_aus->add(new DateInterval('P1D'));
                } while ($fecha_ini_aus <= $fecha_fin_aus);
            }
        }

        try
        {
            $fecha_ini_fichadas = clone $fecha_ini;
            $fecha_ini_fichadas->sub(new DateInterval('P1D')); //QUITO UN DIA PARA ENTRADAS EN EL DÍA ANTERIOR
            $http_response_fichadas = $guzzleHttp->request('GET', "personas/fichadas", ['query' => ['labo_Codigo' => $labo_Codigo, 'fecha_inicio' => date_format($fecha_ini_fichadas, 'Ymd'), 'fecha_fin' => date_format($fecha_fin, 'Ymd')]]);
            $fichadas_list = json_decode($http_response_fichadas->getBody()->getContents());
        } catch (Exception $e)
        {
            $fichadas_list = NULL;
        }
        $temp_fichadas = array();
        if (!empty($fichadas_list))
        {
            foreach ($fichadas_list as $Fichada)
            {
//				if (trim($Fichada->fich_Codigo) === 'S')
//				{
//					$fecha = new DateTime($Fichada->fich_FechaHora);
//					$hora = $fecha->format("H");
//					if (!isset($temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')]['E']) && $hora < 1)
//					{
//						continue; //SALTANDO SALIDA QUE SUPUESTAMENTE CORRESPONDE AL DIA ANTERIOR HASTA DEFINIR COMO LA MANEJA MAJOR
//					}
//				}
                if (isset($temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)]))
                {
                    if (trim($Fichada->fich_Codigo) === 'E')
                    {
                        if (date_format(new DateTime($Fichada->fich_FechaHora), 'H:i') > $this->agregarMinutos(end($temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)]), 10))
                        {
                            $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)][] = date_format(new DateTime($Fichada->fich_FechaHora), 'H:i');
                            $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')]['R_' . trim($Fichada->fich_Codigo)][] = $Fichada->fich_NroReloj;
                        }
                    }
                    elseif (trim($Fichada->fich_Codigo) === 'S')
                    {
                        if (date_format(new DateTime($Fichada->fich_FechaHora), 'H:i') < $this->agregarMinutos(end($temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)]), 10))
                        {
                            end($temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)]);
                            $key = key($temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)]);
                            $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)][$key] = date_format(new DateTime($Fichada->fich_FechaHora), 'H:i');
                            $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')]['R_' . trim($Fichada->fich_Codigo)][$key] = $Fichada->fich_NroReloj;
                        }
                        else
                        {
                            $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)][] = date_format(new DateTime($Fichada->fich_FechaHora), 'H:i');
                            $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')]['R_' . trim($Fichada->fich_Codigo)][] = $Fichada->fich_NroReloj;
                        }
                    }
                }
                else
                {
                    $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')][trim($Fichada->fich_Codigo)][] = date_format(new DateTime($Fichada->fich_FechaHora), 'H:i');
                    $temp_fichadas[date_format(new DateTime($Fichada->fich_FechaHora), 'd-m-Y')]['R_' . trim($Fichada->fich_Codigo)][] = $Fichada->fich_NroReloj;
                }
            }

            $ent_anterior = NULL;
            foreach ($temp_fichadas as $fecha => $fichada_arr)
            {
                $total = 0;
                for ($i = 0; $i <= 2; $i++)
                {
                    if (!empty($fichada_arr['E'][$i]) && !empty($fichada_arr['S'][$i]) && $fichada_arr['S'][$i] > $fichada_arr['E'][$i])
                    {
                        $total += strtotime($fichada_arr['S'][$i]) - strtotime($fichada_arr['E'][$i]);
                    }
                }

                //1er ENTRADA DIA 1 (DESPUES DE LAS 16:00) CON 1er SALIDA DIA 2 (ANTES DE LAS 08:00)
                if (!empty($ent_anterior) && !empty($fichada_arr['S'][0]) && (empty($fichada_arr['E'][0]) || strtotime($fichada_arr['E'][0]) > strtotime($fichada_arr['S'][0])) && empty($fichada_arr['E'][1]) && empty($fichada_arr['S'][1]) && strtotime($fichada_arr['S'][0]) <= strtotime("08:00"))
                {
                    $total += strtotime("23:59") - strtotime($ent_anterior) + 60;
                    $total += strtotime($fichada_arr['S'][0]) - strtotime("00:00");
                }

                if (!empty($fichada_arr['E'][0]) && (empty($fichada_arr['S'][0]) || strtotime($fichada_arr['S'][0]) < strtotime($fichada_arr['E'][0])) && empty($fichada_arr['E'][1]) && empty($fichada_arr['S'][1]) && strtotime($fichada_arr['E'][0]) >= strtotime("16:00"))
                {
                    $ent_anterior = $fichada_arr['E'][0];
                }
                else
                {
                    $ent_anterior = NULL;
                }

                $total = $total / 60;
                $temp_fichadas[$fecha]['total'] = intval($total / 60) . ':' . str_pad(intval(($total % 60)), 2, '0', STR_PAD_LEFT);
                if ($temp_fichadas[$fecha]['total'] === '0:00')
                {
                    $temp_fichadas[$fecha]['total'] = '';
                }
            }
        }

        //BUSCO FERIADOS
        try
        {
            $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => date_format($fecha_ini, 'Ymd'), 'hasta' => date_format($fecha_fin, 'Ymd')]]);
            $feriados = json_decode($http_response_feriados->getBody()->getContents());
        } catch (Exception $e)
        {
            $feriados = NULL;
        }
        $tmp_feriados = array();
        if (!empty($feriados))
        {
            $feriados['data'] = $feriados;
            foreach ($feriados['data'] as $Feriado)
            {
                $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
            }
        }

        while ($fecha_ini < $fecha_fin)
        {
            $str_fecha_ini = date_format($fecha_ini, 'd-m-Y');
            $planilla_arr[$str_fecha_ini] = empty($array_temp_ausencias[$str_fecha_ini]) ? (empty($temp_fichadas[$str_fecha_ini]) ? '' : $temp_fichadas[$str_fecha_ini]) : $array_temp_ausencias[$str_fecha_ini];
            $fecha_ini->add(new DateInterval('P1D'));
        }
        $data['planilla'] = $planilla_arr;
        $data['feriados'] = $tmp_feriados;

        return $data;
    }

    public function descargar($labo_Codigo = NULL, $anio = NULL, $mes = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $labo_Codigo == NULL || !ctype_digit($labo_Codigo) || $anio == NULL || !ctype_digit($anio) || $mes == NULL || !ctype_digit($mes))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        if (in_groups($this->grupos_user, $this->grupos) && $labo_Codigo !== $this->session->userdata('username'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fecha = "$anio-$mes-01";
        $inicio = DateTime::createFromFormat("Y-m-d", date("Y-m-01", strtotime($fecha)));
        $fin = DateTime::createFromFormat("Y-m-d", date("Y-m-t", strtotime($fecha)));

        $data = $this->get_data_fichadas($labo_Codigo, $inicio, $fin);
        if (!empty($data['error']))
        {
            redirect("asistencia/fichadas/ver/$labo_Codigo/$anio/$mes", 'refresh');
        }
        $data['mes_planilla'] = ucfirst(strftime("%B %Y", strtotime($fecha)));

        $html = $this->load->view('asistencia/fichadas/fichadas_pdf', $data, TRUE);
        $stylesheet = file_get_contents('vendor/bootstrap/css/bootstrap.min.css');
        $stylesheet .= file_get_contents('css/asistencia/fichadas.css');

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'format' => 'A4',
            'margin_left' => 6,
            'margin_right' => 6,
            'margin_top' => 6,
            'margin_bottom' => 6,
            'margin_header' => 9,
            'margin_footer' => 9
        ]);
        $mpdf->SetDisplayMode('fullwidth');
        $mpdf->simpleTables = true;
        $mpdf->SetTitle("Asistencia $labo_Codigo");
        $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output("Asistencia_" . $data['mes_planilla'] . "_" . $labo_Codigo . '.pdf', 'D');
    }

    private function agregarMinutos($actual, $minutos_extra)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        list($hora_actual, $minuto_actual) = explode(':', $actual);
        $minuto_nuevo = $hora_actual * 60;
        $minuto_nuevo += $minuto_actual;
        $minuto_nuevo += $minutos_extra;

        $hora_nuevo = floor($minuto_nuevo / 60);
        $minuto_nuevo -= $hora_nuevo * 60;

        return sprintf('%02d:%02d', $hora_nuevo, $minuto_nuevo);
    }
}
