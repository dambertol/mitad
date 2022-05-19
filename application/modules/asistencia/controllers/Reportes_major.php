<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes_major extends MY_Controller
{

    /**
     * Controlador de Reportes Major
     * Autor: Leandro
     * Creado: 17/01/2018
     * Modificado: 11/02/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('asistencia/Usuarios_oficinas_model');
        $this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_consulta_general');
        $this->grupos_contralor = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_consulta_general');
        $this->grupos_rrhh = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_consulta_general');
        $this->grupos_solo_consulta = array('asistencia_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function parte_novedades()
    {
//		$this->benchmark->mark('all_start');
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_secretaria_control = $array_secretaria;

        $array_oficina = array();
        $array_oficina_temp = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficina_temp[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_oficina_control = $array_oficina_temp;

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'secretaria' => array('label' => 'Secretaría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
//			$this->benchmark->mark('post_start');
            $ofi_Secretaria = $this->input->post('secretaria');
            $ofi_Oficina = $this->input->post('oficina');
            $desde = $this->input->post('desde');
            $hasta = $this->input->post('hasta');
            $desde_sql = DateTime::createFromFormat("d/m/Y", $desde)->format('Ymd');
            $hasta_sql = DateTime::createFromFormat("d/m/Y", $hasta)->format('Ymd');

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
            } catch (Exception $e)
            {
                $feriados = NULL;
            }

            $tmp_feriados = array();
            if (!empty($feriados))
            {
                foreach ($feriados as $Feriado)
                {
                    $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                }
            }

            //BUSCA TODOS LOS EMPLEADOS DE LAS OFICINAS SELECCIONADAS Y DATOS ASOCIADOS(NOVEDADES, FICHADAS, NOVEDADES DE FICHADAS Y AUSENCIAS)
            try
            {
                $http_response_personal = $guzzleHttp->request('GET', "personas/reporte_novedades", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde, 'hasta' => $hasta]]);
                $personal = json_decode($http_response_personal->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal = NULL;
            }

            if (!empty($personal))
            {
                //RANGO DE FECHAS A TRABAJAR
                $inicio = DateTime::createFromFormat("d/m/Y H:i:s", $desde . ' 00:00:00');
                $fin = DateTime::createFromFormat("d/m/Y H:i:s", $hasta . ' 00:00:00');
                $fin->add(new DateInterval('P1D'));
                $cant_dias = $inicio->diff($fin)->format("%a");

                //FILA DONDE COMIENZAN LOS DATOS
                $fila_excel = 7;

                //CARGA LOS DATOS NECESARIOS PARA EXCEL
                $planilla_excel = array();
                $ausentes_excel = array();
                $noficha_excel = array();
                $rellenos_excel = array();
                $oficinas_titulo = array();

                foreach ($personal as $Emp)
                {
                    //EVITA ERRORES SI EL EMPLEADO ESTA 2 VECES (POR HORARIOS REPETIDOS GENERALMENTE)
                    if (!empty($planilla_excel[$Emp->datos->labo_Codigo]))
                    {
                        continue;
                    }

                    //COLUMNA DONDE COMIENZAN LOS DATOS
                    $columna_excel = 'F';

                    //LISTADO DE PERSONAL PARA EXCEL
                    $planilla_excel[$Emp->datos->labo_Codigo] = array('labo_Codigo' => $Emp->datos->labo_Codigo, 'nombre' => $Emp->datos->nombre, 'oficina' => $Emp->datos->oficina, 'novedades' => ' ', 'horario' => $Emp->datos->horario);

                    //FILAS EN ROJO PARA PERSONAL QUE NO FICHA
                    if ($Emp->datos->hoca_Ficha === 'N')
                    {
                        $noficha_excel[$fila_excel] = 'FF0000';
                    }

                    //GUARDA NOMBRE DE OFICINA PARA TITULO
                    $oficinas_titulo[$Emp->datos->oficina] = $Emp->datos->oficina;

                    //NOVEDADES
                    if (!empty($Emp->novedades))
                    {
                        foreach ($Emp->novedades as $Nov)
                        {
                            if (empty($planilla_excel[$Emp->datos->labo_Codigo]['novedades']) || $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] === ' ')
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= $Nov->vava_Descripcion;
                            }
                            else
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= " | $Nov->vava_Descripcion";
                            }
                        }
                    }

                    //FICHADAS
                    $tmp_fich = array();
                    if (!empty($Emp->fichadas))
                    {
                        foreach ($Emp->fichadas as $Fich)
                        {
                            //PONER SALIDA EN EL DÍA ANTERIOR
//							if (trim($Fich->fich_Codigo) === 'S')
//							{
//								$fecha = new DateTime($Fich->fich_FechaHora);
//								$hora = $fecha->format("H");
//								if (!isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . 'E']) && $hora < 1) //NO HAY ENTRADA ANTERIOR Y SON MENOS DE LA 1 AM
//								{
//									$fecha->sub(new DateInterval('P1D'));
//									if (isset($tmp_fich[date_format($fecha, 'mj') . 'S'])) //YA HAY SALIDA EL DÍA ANTERIOR
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									else
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									continue; //SALTANDO SALIDA QUE SUPUESTAMENTE CORRESPONDE AL DIA ANTERIOR HASTA DEFINIR COMO LA MANEJA MAJOR
//								}
//							}

                            if (isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)]))
                            {
                                if (trim($Fich->fich_Codigo) === 'E')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') >= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA ENTRADA DESPUES DE 5 MINUTOS DE LA PRIMERA
                                    }
                                }
                                elseif (trim($Fich->fich_Codigo) === 'S')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') <= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                                    }
                                    else
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA SALIDA DESPUES DE 5 MINUTOS DE LA SEGUNDA
                                    }
                                }
                            }
                            else
                            {
                                $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                            }
                        }
                    }

                    //NOVEDADES DE FICHADAS
                    if (!empty($Emp->novedades_fichadas))
                    {
                        foreach ($Emp->novedades_fichadas as $Nov_fich)
                        {
                            $fecha_nov = new DateTime($Nov_fich->nofi_Fecha);
                            if (empty($tmp_fich[date_format($fecha_nov, 'mj') . 'N']))
                            {
                                $tmp_fich[date_format($fecha_nov, 'mj') . 'N'] = $Nov_fich->tnof_Descripcion . ' (' . $this->_convertTime($Nov_fich->nofi_Valor) . ')';
                            }
                            else
                            {
                                $tmp_fich[date_format($fecha_nov, 'mj') . 'N'] .= ' | ' . $Nov_fich->tnof_Descripcion . ' (' . $this->_convertTime($Nov_fich->nofi_Valor) . ')';
                            }
                            $tmp_fich[date_format($fecha_nov, 'mj') . 'NT'][] = "$Nov_fich->tnof_Codigo";
                        }
                    }

                    //AUSENCIAS
                    if (!empty($Emp->ausencias))
                    {
                        foreach ($Emp->ausencias as $Aus)
                        {
                            $fecha_ini_aus = new DateTime($Aus->ause_FechaInicio);
                            if ($fecha_ini_aus < $inicio)
                            {
                                $fecha_ini_aus = clone $inicio;
                            }
                            $fecha_fin_aus = new DateTime($Aus->ause_FechaFin);
                            if ($fecha_fin_aus > $fin)
                            {
                                $fecha_fin_aus = clone $fin;
                            }

                            do
                            {
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'E'] = $Aus->moau_Descripcion;
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'S'] = '\'';
                                $fecha_ini_aus->add(new DateInterval('P1D'));
                            } while ($fecha_ini_aus <= $fecha_fin_aus);
                        }
                    }

                    //RECORRE TODO EL RANGO DE FECHAS PARA ARMAR $planilla_arr
                    $inicio_inc = clone $inicio;
                    while ($inicio_inc < $fin)
                    {
                        $str_fecha_ini = date_format($inicio_inc, 'mj');

                        //CARGA ENTRADAS
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'E'] = empty($tmp_fich[$str_fecha_ini . 'E']) ? ' ' : $tmp_fich[$str_fecha_ini . 'E'];

                        //CARGA AUSENCIAS
                        if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
                        {
                            $ausentes_excel[] = array('columna' => $columna_excel, 'fila' => $fila_excel);
                        }
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            foreach ($tmp_fich[$str_fecha_ini . 'NT'] as $Tipo_nov)
                            {
                                switch ($Tipo_nov)
                                {
                                    case 1: //LLEGADA TARDE
//									case 5: //OMITIO FICHAR ENTRADA
                                    case 16: //LLEGADA TARDE LICENCIA ESPECIAL
                                    case 22: //LLEGADA TARDE JUSTIFICADA
                                    case 27: //LLEGADA TARDE EN COMISION
                                    case 30: //LLEGADA TARDE GREMIAL
//									case 34: //OMITIO FICHAR ENTRADA JUSTIFICADA
//									case 35: //OMITIO FICHAR ENTRADA COMISION
//									case 36: //OMITIO FICHAR ENTRADA GREMIAL
                                    case 41: //LLEGADA TARDE FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        //CARGA SALIDAS
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'S'] = empty($tmp_fich[$str_fecha_ini . 'S']) ? ' ' : $tmp_fich[$str_fecha_ini . 'S'];
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            foreach ($tmp_fich[$str_fecha_ini . 'NT'] as $Tipo_nov)
                            {
                                switch ($Tipo_nov)
                                {
                                    case 2: //SALIDA ANTICIPADA
//									case 6:  //OMITIO FICHAR SALIDA
                                    case 17: //SALIDA ANTICIPADA LICENCIA ESPECIAL
                                    case 20: //SALIDA ANTICIPADA ENFERMO
                                    case 23: //SALIDA ANTICIPADA JUSTIFICAD
                                    case 26: //SALIDA ANTICIPADA COMPENSADA
                                    case 28: //SALIDA ANTICIPADA EN COMISION
                                    case 31: //SALIDA ANTICIPADA GREMIAL
//									case 37: //OMITIO FICHAR SALIDA JUSTIFICADA
//									case 38: //OMITIO FICHAR SALIDA COMISION
//									case 39: //OMITIO FICHAR SALIDA GREMIAL
                                    case 42: //SALIDA ANTICIPADA FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        //CARGA NOVEDADES
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'N'] = empty($tmp_fich[$str_fecha_ini . 'N']) ? ' ' : $tmp_fich[$str_fecha_ini . 'N'];
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            if (count($tmp_fich[$str_fecha_ini . 'NT']) < 2)
                            {
                                switch ($tmp_fich[$str_fecha_ini . 'NT'][0])
                                {
                                    case 10: //HS.EXTRAS NORMALES
                                    case 11: //HS.EXTRAS AL 50%
                                    case 12: //HS.EXTRAS AL 100%
                                    case 13: //HS.ANTICIPADAS NORMALES
                                    case 14: //HS.ANTICIPADAS AL 50%
                                    case 15: //HS.ANTICIPADAS AL 100%
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'C4D79B'; //VERDE PARA HORAS EXTRAS
                                        break;
                                    case 1: //LLEGADA TARDE
                                    case 2: //SALIDA ANTICIPADA
                                    case 16: //LLEGADA TARDE LICENCIA ESPECIAL
                                    case 17: //SALIDA ANTICIPADA LICENCIA ESPECIAL
                                    case 20: //SALIDA ANTICIPADA ENFERMO
                                    case 22: //LLEGADA TARDE JUSTIFICADA
                                    case 23: //SALIDA ANTICIPADA JUSTIFICAD
                                    case 26: //SALIDA ANTICIPADA COMPENSADA
                                    case 27: //LLEGADA TARDE EN COMISION
                                    case 28: //SALIDA ANTICIPADA EN COMISION
                                    case 30: //LLEGADA TARDE GREMIAL
                                    case 31: //SALIDA ANTICIPADA GREMIAL
                                    case 41: //LLEGADA TARDE FRANCO COMPENSATORIO
                                    case 42: //SALIDA ANTICIPADA FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO PARA LLEGADAS TARDE/SALIDAS ANTICIPADAS
                                        break;
                                    default:
                                        //case 3:  //HORAS NO TRABAJADAS
                                        //case 5:  //OMITIO FICHAR ENTRADA
                                        //case 6:  //OMITIO FICHAR SALIDA
                                        //case 7:  //REVISAR MANUALMENTE
                                        //case 8:  //HORAS NO PERMITIDAS
                                        //case 9:  //DIA NO TRABAJADO
                                        //case 21: //INCONVENIENTE DE TRANSPORTE
                                        //case 24: //HORAS NO TRABAJADAS JUSTIFICADAS
                                        //case 29: //HORAS EN COMISION
                                        //case 32: //HORAS GREMIALES
                                        //case 34: //OMITIO FICHAR ENTRADA JUSTIFICADA
                                        //case 35: //OMITIO FICHAR ENTRADA COMISION
                                        //case 36: //OMITIO FICHAR ENTRADA GREMIAL
                                        //case 37: //OMITIO FICHAR SALIDA JUSTIFICADA
                                        //case 38: //OMITIO FICHAR SALIDA COMISION
                                        //case 39: //OMITIO FICHAR SALIDA GREMIAL
                                        //case 40: //DIA JUSTIFICADO
                                        //case 43: //HORAS FRANCO COMPENSATORIO
                                        //case 44: //RELOJ NO LEE TIMBRADO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        $inicio_inc->add(new DateInterval('P1D'));
                    }
                    $fila_excel++;
                }

                //ARMADO TITULO PLANILLA
                $tit_fecha = array();
                $tit_desc = array();
                $inicio_inc = clone $inicio;
                while ($inicio_inc < $fin)
                {
                    $str_fecha = $this->_getDayName($inicio_inc);
                    $str_fecha .= ' ' . date_format($inicio_inc, 'j');
                    $str_fecha .= ' ' . $this->_getMonthName($inicio_inc);
                    if (array_key_exists($inicio_inc->format('dmY'), $tmp_feriados))
                    {
                        $str_fecha .= ' (FERIADO)';
                    }
                    $tit_fecha[] = $str_fecha;
                    $tit_fecha[] = '';
                    $tit_fecha[] = '';
                    $tit_desc[] = 'E';
                    $tit_desc[] = 'S';
                    $tit_desc[] = 'NOVEDAD';
                    $inicio_inc->add(new DateInterval('P1D'));
                }
                $tit_planilla = array($tit_fecha, $tit_desc);

                //INICIO GENERACION EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $cant_filas = sizeof($planilla_excel) + 6;
                $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
                if (!$validLocale)
                {
                    lm('Unable to set locale to es - reverting to en_us');
                }
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Parte de Novedades")
                        ->setDescription("Parte de Novedades del Personal");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Parte de Novedades");

                //OPCIONES DE IMPRESIÓN
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(1);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(1);
                $sheet->getPageSetup()->setFitToWidth(1);

                $sheet->getStyle("A5:{$columna_excel}6")->getFont()->setSize(10);
                $sheet->getStyle("A7:$columna_excel$cant_filas")->getFont()->setSize(8);
                $sheet->getStyle("F4:$columna_excel$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);

                $border_left_thin = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                $border_allborders_thin = array(
                    'borders' => array(
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                //TITULO TABLA
                $col = 'F';
                $col_anterior = 'E';
                for ($i = 1; $i <= sizeof($tit_fecha); $i++)
                {
                    if ($i % 3 === 1)
                    {
                        $sheet->getColumnDimension($col)->setWidth(13);
                        $sheet->getStyle("{$col}5:$col$cant_filas")->applyFromArray($border_left_thin);
                        $col_merge = $col;
                        $col_merge++;
                        $col_merge++;
                        $sheet->mergeCells("{$col}5:{$col_merge}5");
                    }
                    else if ($i % 3 === 0)
                    {
                        $sheet->getColumnDimension($col)->setWidth(22);
                        $sheet->getStyle("{$col}7:$col$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT));
                    }
                    else
                    {
                        $sheet->getColumnDimension($col)->setWidth(13);
                    }

                    $col_anterior = $col;
                    $col++;
                }
                $sheet->mergeCells("A5:A6"); //LEGAJO
                $sheet->mergeCells("B5:B6"); //APELLIDO Y NOMBRE
                $sheet->mergeCells("C5:C6"); //OFICINA
                $sheet->mergeCells("D5:D6"); //ADICIONALES
                $sheet->mergeCells("E5:E6"); //HORARIOS
                //COLUMNA PARA JUSTIFICACIÓN
                if ($cant_dias === '1')
                {
                    $sheet->fromArray(array(array("JUSTIFICACIÓN")), NULL, "{$columna_excel}5");
                    $sheet->mergeCells("{$columna_excel}5:{$columna_excel}6");
                    $sheet->getStyle("{$columna_excel}5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                    $sheet->getColumnDimension($columna_excel)->setWidth(64);
                    $sheet->getStyle("A5:$col$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                    $sheet->getStyle("A5:{$col}6")->getFont()->setBold(TRUE); //NEGRITA TITULO
                }
                else
                {
                    $sheet->getStyle("A5:$col_anterior$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                    $sheet->getStyle("A5:{$col_anterior}6")->getFont()->setBold(TRUE); //NEGRITA TITULO
                }
                $sheet->fromArray(array(array('LEGAJO', 'APELLIDO Y NOMBRE', 'OFICINA', 'ADICIONALES', 'HORARIO')), NULL, 'A5');
                $sheet->getStyle("A5:E5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->fromArray($tit_planilla, NULL, 'F5');
                $sheet->freezePane('F7');

                //TITULO PRINCIPAL
                $sheet->fromArray(array(array('PARTE DE NOVEDADES DEL PERSONAL')), NULL, 'A1');
                $sheet->mergeCells("A1:E1");
                $sheet->mergeCells("A2:E2");
                $sheet->getStyle('A1')->getFont()->setSize(18);
                $sheet->fromArray(array(array("SECRETARÍA: $array_secretaria[$ofi_Secretaria]")), NULL, 'A2');
                $sheet->fromArray(array(array("OFICINA: " . implode(', ', $oficinas_titulo))), NULL, 'A3');
                $sheet->mergeCells("A3:E3");
                $sheet->mergeCells("A4:E4");
                $sheet->getStyle('A2')->getFont()->setSize(14);
                $sheet->getStyle('A3:A4')->getFont()->setSize(10);

                //DATOS TABLA
                $sheet->fromArray($planilla_excel, NULL, 'A7');

                //RELLENOS A NOVEDADES
                if (!empty($rellenos_excel))
                {
                    foreach ($rellenos_excel as $Celda => $Relleno)
                    {
                        $sheet->getStyle($Celda)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => $Relleno)
                                    )
                                )
                        );
                    }
                }

                //LETRA ROJA A GENTE QUE NO DEBE FICHAR
                if (!empty($noficha_excel))
                {
                    foreach ($noficha_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:$columna_excel$Fila")->applyFromArray(
                                array(
                                    'font' => array(
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //RELLENOS A AUSENCIAS
                if (!empty($ausentes_excel))
                {
                    foreach ($ausentes_excel as $Ausente)
                    {
                        $inicio_ausente_merge = $Ausente['columna'] . $Ausente['fila'];
                        $Ausente['columna']++;
                        $Ausente['columna']++;
                        $fin_ausente_merge = $Ausente['columna'] . $Ausente['fila'];
                        $sheet->mergeCells("$inicio_ausente_merge:$fin_ausente_merge");
                        $sheet->getStyle($inicio_ausente_merge)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'E6B4B8') //ROJO PARA AUSENCIAS
                                    )
                                )
                        );
                    }
                }

                //REFERENCIAS
                $sheet->setCellValue('A' . (string) ($cant_filas + 2), 'REFERENCIAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 3))->applyFromArray(
                        array(
                            'font' => array(
                                'color' => array('rgb' => 'FF0000')
                            )
                        )
                );
                $sheet->setCellValue('A' . (string) ($cant_filas + 3), 'ROJO');
                $sheet->setCellValue('B' . (string) ($cant_filas + 3), 'PERSONAL QUE NO ES NECESARIO QUE FICHE');

                $sheet->getStyle('A' . (string) ($cant_filas + 4))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'C4D79B')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 4), 'NOVEDADES: HS EXTRAS Y HS ANTICIPADAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 5))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF99')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 5), 'NOVEDADES: LLEGADAS TARDE Y SALIDAS ANTICIPADAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 6))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FABF8F')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 6), 'NOVEDADES: OMISIÓN DE FICHADAS Y HS/DÍAS NO TRABAJADOS');

                $sheet->getStyle('A' . (string) ($cant_filas + 7))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'E6B4B8')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 7), 'AUSENCIAS');

                //NOMBRES RELOJES
                $sheet->setCellValue('A' . (string) ($cant_filas + 9), 'RELOJES');
                $sheet->fromArray(array(array('(1) TABOADA 1', '(2) CENTRO CÍVICO 2', '(3) HACIENDA')), NULL, 'B' . (string) ($cant_filas + 10));
                $sheet->fromArray(array(array('(4) OBRADOR', '(5) POLICIA VIAL', '(6) POLIDEPORTIVO')), NULL, 'B' . (string) ($cant_filas + 11));
                $sheet->fromArray(array(array('(7) DELEG CHACRAS', '(8) DELEG CARRODILLA', '(9) SANTA ELENA')), NULL, 'B' . (string) ($cant_filas + 12));
                $sheet->fromArray(array(array('(10) DESARROLLO SOCIAL', '(11) PLANTA POTABILIZADORA', '(12) CENTRO CÍVICO 1')), NULL, 'B' . (string) ($cant_filas + 13));
                $sheet->fromArray(array(array('(13) ESTACION FERRI', '(14) CEMENTERIO', '(15) DELEG PERDRIEL')), NULL, 'B' . (string) ($cant_filas + 14));
                $sheet->fromArray(array(array('(16) DELEG AGRELO', '(17) DELEG UGARTECHE', '(18) DELEG CARRIZAL')), NULL, 'B' . (string) ($cant_filas + 15));
                $sheet->fromArray(array(array('(19) BIBLIOTECA', '(20) DELEG COMPUERTAS', '(21) DELEG PEDEMONTE')), NULL, 'B' . (string) ($cant_filas + 16));
                $sheet->fromArray(array(array('(22) DELEG DRUMMOND', '(23) CENTRO CÍVICO', '(24) PLAYÓN ESTE', '(25) POLID CARRODILLA')), NULL, 'B' . (string) ($cant_filas + 17));

                $nombreArchivo = 'parte_novedades_' . date('YmdHi');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $this->session->set_flashdata('error', '<br />No se encontraron personas en la oficina seleccionada');
                redirect('asistencia/reportes_major/parte_novedades', 'refresh');
            }
//			$this->benchmark->mark('post_end');
//			lm('POST: ' . $this->benchmark->elapsed_time('post_start', 'post_end'));
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['secretaria_sel'] = $this->form_validation->set_value('secretaria');

        $fake_model->fields['oficina']['array'] = $array_oficina;
        $fake_model->fields['secretaria']['array'] = $array_secretaria;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Parte de Novedades del Personal';
        $data['title'] = TITLE . ' - Parte de Novedades';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/reportes_major/reportes_parte_novedades', $data);
//		$this->benchmark->mark('all_end');
//		lm('ALL: ' . $this->benchmark->elapsed_time('all_start', 'all_end'));
    }

    public function parte_diario()
    {
//		$this->benchmark->mark('all_start');
        if (!in_groups($this->grupos_contralor, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_secretaria_control = $array_secretaria;

        $array_oficina = array();
        $array_oficina_temp = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficina_temp[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_oficina_control = $array_oficina_temp;

        $this->array_particion_control = $array_particion = array(
            '01' => '01 - MUNICIPALES',
            '02' => '02 - H.C.D.',
            '05' => '05 - JARDINES MATERNALES',
            '07' => '07 - LOCACIONES',
            '10' => '10 - PLAN CONSTRUYENDO MI FUTURO'
        );

        $this->array_mostrar_control = $array_mostrar = array(
            '1' => 'Todo el personal',
            '2' => 'Todo el personal que debe fichar',
            '3' => 'Sólo el personal ausente',
            '4' => 'Sólo el personal ausente (sin motivo de ausencia)'
        );

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'secretaria' => array('label' => 'Secretaría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'particion' => array('label' => 'Partición', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'mostrar' => array('label' => 'Mostrar', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE),
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
//			$this->benchmark->mark('post_start');
            $ofi_Secretaria = $this->input->post('secretaria');
            $ofi_Oficina = $this->input->post('oficina');
            $prtn_Codigo = $this->input->post('particion');
            $mostrar = $this->input->post('mostrar');
            $desde = $this->input->post('desde');
            $hasta = $this->input->post('hasta');
            $desde_sql = DateTime::createFromFormat("d/m/Y", $desde)->format('Ymd');
            $hasta_sql = DateTime::createFromFormat("d/m/Y", $hasta)->format('Ymd');

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
            } catch (Exception $e)
            {
                $feriados = NULL;
            }

            $tmp_feriados = array();
            if (!empty($feriados))
            {
                foreach ($feriados as $Feriado)
                {
                    $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                }
            }

            //BUSCO HORARIOS DIARIOS
            try
            {
                $http_response_horarios = $guzzleHttp->request('GET', "personas/horarios_diarios_oficina", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $horarios_diarios = json_decode($http_response_horarios->getBody()->getContents());
            } catch (Exception $e)
            {
                $horarios_diarios = NULL;
            }

            $tmp_horarios_diarios = array();
            if (!empty($horarios_diarios))
            {
                foreach ($horarios_diarios as $Diario)
                {
                    $tmp_horarios_diarios[$Diario->labo_Codigo][(new DateTime($Diario->hodi_Fecha))->format('dmY')] = array('E' => $Diario->hodi_Entrada, 'S' => $Diario->hodi_Salida);
                }
            }

            //BUSCA TODOS LOS EMPLEADOS DE LAS OFICINAS SELECCIONADAS Y DATOS ASOCIADOS(NOVEDADES, FICHADAS, NOVEDADES DE FICHADAS Y AUSENCIAS)
            try
            {
                $http_response_personal = $guzzleHttp->request('GET', "personas/parte_diario", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'prtn_Codigo' => $prtn_Codigo, 'mostrar' => $mostrar, 'desde' => $desde, 'hasta' => $hasta, 'feriados' => $tmp_feriados]]);
                $personal = json_decode($http_response_personal->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal = NULL;
            }

            if (!empty($personal))
            {
                //RANGO DE FECHAS A TRABAJAR
                $inicio = DateTime::createFromFormat("d/m/Y H:i:s", $desde . ' 00:00:00');
                $fin = DateTime::createFromFormat("d/m/Y H:i:s", $hasta . ' 00:00:00');
                $fin->add(new DateInterval('P1D'));
//				$cant_dias = $inicio->diff($fin)->format("%a");
                //FILA DONDE COMIENZAN LOS DATOS
                $fila_excel = 7;

                //CARGA LOS DATOS NECESARIOS PARA EXCEL
                $planilla_excel = array();
                $ausentes_excel = array();
                $noficha_excel = array();
                $rellenos_excel = array();
                $oficinas_titulo = array();

                //MUESTRA TODAS LAS OFICINAS SELECCIONADAS
                foreach ($ofi_Oficina as $Oficina_sel)
                {
                    $oficinas_titulo[$array_oficina_temp[$Oficina_sel]] = $array_oficina_temp[$Oficina_sel];
                }

                foreach ($personal as $Emp)
                {
                    //EVITA ERRORES SI EL EMPLEADO ESTA 2 VECES (POR HORARIOS REPETIDOS GENERALMENTE)
                    if (!empty($planilla_excel[$Emp->datos->labo_Codigo]))
                    {
                        continue;
                    }

                    //TEXTO PARA CELDA RESUMEN FINAL
                    $resumen_empleado = '';

                    //COLUMNA DONDE COMIENZAN LOS DATOS
                    $columna_excel = 'H';

                    //LISTADO DE PERSONAL PARA EXCEL
                    $planilla_excel[$Emp->datos->labo_Codigo] = array('labo_Codigo' => $Emp->datos->labo_Codigo, 'nombre' => $Emp->datos->nombre, 'oficina' => $Emp->datos->oficina, 'novedades' => ' ', 'horario' => $Emp->datos->horario, 'e' => 0, 's' => 0);

                    //FILAS EN ROJO PARA PERSONAL QUE NO FICHA
                    if ($Emp->datos->hoca_Ficha === 'N')
                    {
                        $noficha_excel[$fila_excel] = 'FF0000';
                    }
                    //GUARDA NOMBRE DE OFICINA PARA TITULO
//					$oficinas_titulo[$Emp->datos->oficina] = $Emp->datos->oficina;
                    //NOVEDADES
                    if (!empty($Emp->novedades))
                    {
                        foreach ($Emp->novedades as $Nov)
                        {
                            if (empty($planilla_excel[$Emp->datos->labo_Codigo]['novedades']) || $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] === ' ')
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= $Nov->vava_Descripcion;
                            }
                            else
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= " | $Nov->vava_Descripcion";
                            }
                        }
                    }

                    //FICHADAS
                    $tmp_fich = array();
                    if (!empty($Emp->fichadas))
                    {
                        foreach ($Emp->fichadas as $Fich)
                        {
                            //PONER SALIDA EN EL DÍA ANTERIOR
//							if (trim($Fich->fich_Codigo) === 'S')
//							{
//								$fecha = new DateTime($Fich->fich_FechaHora);
//								$hora = $fecha->format("H");
//								if (!isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . 'E']) && $hora < 1) //NO HAY ENTRADA ANTERIOR Y SON MENOS DE LA 1 AM
//								{
//									$fecha->sub(new DateInterval('P1D'));
//									if (isset($tmp_fich[date_format($fecha, 'mj') . 'S'])) //YA HAY SALIDA EL DÍA ANTERIOR
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									else
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									continue; //SALTANDO SALIDA QUE SUPUESTAMENTE CORRESPONDE AL DIA ANTERIOR HASTA DEFINIR COMO LA MANEJA MAJOR
//								}
//							}

                            if (isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)]))
                            {
                                if (trim($Fich->fich_Codigo) === 'E')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') >= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA ENTRADA DESPUES DE 5 MINUTOS DE LA PRIMERA
                                    }
                                }
                                elseif (trim($Fich->fich_Codigo) === 'S')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') <= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                                    }
                                    else
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA SALIDA DESPUES DE 5 MINUTOS DE LA SEGUNDA
                                    }
                                }
                            }
                            else
                            {
                                $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                            }
                        }
                    }

                    //NOVEDADES DE FICHADAS
                    if (!empty($Emp->novedades_fichadas))
                    {
                        foreach ($Emp->novedades_fichadas as $Nov_fich)
                        {
                            $fecha_nov = new DateTime($Nov_fich->nofi_Fecha);
                            if (empty($tmp_fich[date_format($fecha_nov, 'mj') . 'N']))
                            {
                                $tmp_fich[date_format($fecha_nov, 'mj') . 'N'] = $Nov_fich->tnof_Descripcion . ' (' . $this->_convertTime($Nov_fich->nofi_Valor) . ')';
                            }
                            else
                            {
                                $tmp_fich[date_format($fecha_nov, 'mj') . 'N'] .= ' | ' . $Nov_fich->tnof_Descripcion . ' (' . $this->_convertTime($Nov_fich->nofi_Valor) . ')';
                            }
                            $tmp_fich[date_format($fecha_nov, 'mj') . 'NT'][] = "$Nov_fich->tnof_Codigo";
                        }
                    }

                    //AUSENCIAS
                    if (!empty($Emp->ausencias))
                    {
                        foreach ($Emp->ausencias as $Aus)
                        {
                            $fecha_ini_aus = new DateTime($Aus->ause_FechaInicio);
                            if ($fecha_ini_aus < $inicio)
                            {
                                $fecha_ini_aus = clone $inicio;
                            }
                            $fecha_fin_aus = new DateTime($Aus->ause_FechaFin);
                            if ($fecha_fin_aus > $fin)
                            {
                                $fecha_fin_aus = clone $fin;
                            }

                            do
                            {
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'E'] = $Aus->moau_Descripcion;
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'S'] = '\'';
                                $fecha_ini_aus->add(new DateInterval('P1D'));
                            } while ($fecha_ini_aus <= $fecha_fin_aus);
                        }
                    }

                    //RECORRE TODO EL RANGO DE FECHAS PARA ARMAR $planilla_arr
                    $inicio_inc = clone $inicio;
                    while ($inicio_inc < $fin)
                    {
                        $str_fecha_ini = date_format($inicio_inc, 'mj');

                        //CARGA ENTRADAS
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'E'] = empty($tmp_fich[$str_fecha_ini . 'E']) ? ' ' : $tmp_fich[$str_fecha_ini . 'E'];

                        if (!empty($tmp_horarios_diarios[$Emp->datos->labo_Codigo]) && array_key_exists($inicio_inc->format('dmY'), $tmp_horarios_diarios[$Emp->datos->labo_Codigo]))
                        {
                            $horario_diario = $tmp_horarios_diarios[$Emp->datos->labo_Codigo][$inicio_inc->format('dmY')];

                            if ($horario_diario['E'] !== '00:00' || $horario_diario['S'] !== '00:00')
                            {
                                if (empty($tmp_fich[$str_fecha_ini . 'E']))
                                {
                                    $planilla_excel[$Emp->datos->labo_Codigo]['e']++;
                                    $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                    $resumen_empleado .= "|" . $inicio_inc->format('d/m/Y') . ": OMITIO FICHAR ENTRADA| ";
                                }
                            }
                        }
                        else
                        {
                            //NÚMERO DE DÍA
                            if (array_key_exists($inicio_inc->format('dmY'), $tmp_feriados))
                            {
                                $numero_dia = 8; //FERIADO ES 8 EN SECUENCIA DE MAJOR ??
                            }
                            else
                            {
                                $numero_dia = $inicio_inc->format('w');
                                if ($numero_dia === '0')
                                {
                                    $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                }
                            }

                            if ($Emp->datos->{"hora_DiaSec" . $numero_dia . "Ent"} !== '00:00' || $Emp->datos->{"hora_DiaSec" . $numero_dia . "Sal"} !== '00:00')
                            {
                                if (empty($tmp_fich[$str_fecha_ini . 'E']))
                                {
                                    $planilla_excel[$Emp->datos->labo_Codigo]['e']++;
                                    $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                    $resumen_empleado .= "|" . $inicio_inc->format('d/m/Y') . ": OMITIO FICHAR ENTRADA| ";
                                }
                            }
                        }

                        //CARGA AUSENCIAS
                        if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
                        {
                            $ausentes_excel[] = array('columna' => $columna_excel, 'fila' => $fila_excel);
                        }
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            foreach ($tmp_fich[$str_fecha_ini . 'NT'] as $Tipo_nov)
                            {
                                switch ($Tipo_nov)
                                {
                                    case 1: //LLEGADA TARDE
//									case 5: //OMITIO FICHAR ENTRADA
                                    case 16: //LLEGADA TARDE LICENCIA ESPECIAL
                                    case 22: //LLEGADA TARDE JUSTIFICADA
                                    case 27: //LLEGADA TARDE EN COMISION
                                    case 30: //LLEGADA TARDE GREMIAL
//									case 34: //OMITIO FICHAR ENTRADA JUSTIFICADA
//									case 35: //OMITIO FICHAR ENTRADA COMISION
//									case 36: //OMITIO FICHAR ENTRADA GREMIAL
                                    case 41: //LLEGADA TARDE FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        //CARGA SALIDAS
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'S'] = empty($tmp_fich[$str_fecha_ini . 'S']) ? ' ' : $tmp_fich[$str_fecha_ini . 'S'];
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            foreach ($tmp_fich[$str_fecha_ini . 'NT'] as $Tipo_nov)
                            {
                                switch ($Tipo_nov)
                                {
                                    case 2: //SALIDA ANTICIPADA
//									case 6:  //OMITIO FICHAR SALIDA
                                    case 17: //SALIDA ANTICIPADA LICENCIA ESPECIAL
                                    case 20: //SALIDA ANTICIPADA ENFERMO
                                    case 23: //SALIDA ANTICIPADA JUSTIFICAD
                                    case 26: //SALIDA ANTICIPADA COMPENSADA
                                    case 28: //SALIDA ANTICIPADA EN COMISION
                                    case 31: //SALIDA ANTICIPADA GREMIAL
//									case 37: //OMITIO FICHAR SALIDA JUSTIFICADA
//									case 38: //OMITIO FICHAR SALIDA COMISION
//									case 39: //OMITIO FICHAR SALIDA GREMIAL
                                    case 42: //SALIDA ANTICIPADA FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO
                                        break;
                                }
                            }
                        }

                        if (!empty($tmp_horarios_diarios[$Emp->datos->labo_Codigo]) && array_key_exists($inicio_inc->format('dmY'), $tmp_horarios_diarios[$Emp->datos->labo_Codigo]))
                        {
                            $horario_diario = $tmp_horarios_diarios[$Emp->datos->labo_Codigo][$inicio_inc->format('dmY')];

                            if ($horario_diario['E'] !== '00:00' || $horario_diario['S'] !== '00:00')
                            {
                                if (empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
                                    $planilla_excel[$Emp->datos->labo_Codigo]['s']++;
                                    $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                    $resumen_empleado .= "|" . $inicio_inc->format('d/m/Y') . ": OMITIO FICHAR SALIDA| ";
                                }
                            }
                        }
                        else
                        {
                            if ($Emp->datos->{"hora_DiaSec" . $numero_dia . "Ent"} !== '00:00' || $Emp->datos->{"hora_DiaSec" . $numero_dia . "Sal"} !== '00:00')
                            {
                                if (empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
                                    $planilla_excel[$Emp->datos->labo_Codigo]['s']++;
                                    $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                    $resumen_empleado .= "|" . $inicio_inc->format('d/m/Y') . ": OMITIO FICHAR SALIDA| ";
                                }
                            }
                        }
                        $columna_excel++;

                        //CARGA NOVEDADES
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'N'] = empty($tmp_fich[$str_fecha_ini . 'N']) ? ' ' : $tmp_fich[$str_fecha_ini . 'N'];
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            if (count($tmp_fich[$str_fecha_ini . 'NT']) < 2)
                            {
                                switch ($tmp_fich[$str_fecha_ini . 'NT'][0])
                                {
                                    case 10: //HS.EXTRAS NORMALES
                                    case 11: //HS.EXTRAS AL 50%
                                    case 12: //HS.EXTRAS AL 100%
                                    case 13: //HS.ANTICIPADAS NORMALES
                                    case 14: //HS.ANTICIPADAS AL 50%
                                    case 15: //HS.ANTICIPADAS AL 100%
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'C4D79B'; //VERDE PARA HORAS EXTRAS
                                        break;
                                    case 1: //LLEGADA TARDE
                                    case 2: //SALIDA ANTICIPADA
                                    case 16: //LLEGADA TARDE LICENCIA ESPECIAL
                                    case 17: //SALIDA ANTICIPADA LICENCIA ESPECIAL
                                    case 20: //SALIDA ANTICIPADA ENFERMO
                                    case 22: //LLEGADA TARDE JUSTIFICADA
                                    case 23: //SALIDA ANTICIPADA JUSTIFICAD
                                    case 26: //SALIDA ANTICIPADA COMPENSADA
                                    case 27: //LLEGADA TARDE EN COMISION
                                    case 28: //SALIDA ANTICIPADA EN COMISION
                                    case 30: //LLEGADA TARDE GREMIAL
                                    case 31: //SALIDA ANTICIPADA GREMIAL
                                    case 41: //LLEGADA TARDE FRANCO COMPENSATORIO
                                    case 42: //SALIDA ANTICIPADA FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO PARA LLEGADAS TARDE/SALIDAS ANTICIPADAS
                                        break;
                                    default:
                                        //case 3:  //HORAS NO TRABAJADAS
                                        //case 5:  //OMITIO FICHAR ENTRADA
                                        //case 6:  //OMITIO FICHAR SALIDA
                                        //case 7:  //REVISAR MANUALMENTE
                                        //case 8:  //HORAS NO PERMITIDAS
                                        //case 9:  //DIA NO TRABAJADO
                                        //case 21: //INCONVENIENTE DE TRANSPORTE
                                        //case 24: //HORAS NO TRABAJADAS JUSTIFICADAS
                                        //case 29: //HORAS EN COMISION
                                        //case 32: //HORAS GREMIALES
                                        //case 34: //OMITIO FICHAR ENTRADA JUSTIFICADA
                                        //case 35: //OMITIO FICHAR ENTRADA COMISION
                                        //case 36: //OMITIO FICHAR ENTRADA GREMIAL
                                        //case 37: //OMITIO FICHAR SALIDA JUSTIFICADA
                                        //case 38: //OMITIO FICHAR SALIDA COMISION
                                        //case 39: //OMITIO FICHAR SALIDA GREMIAL
                                        //case 40: //DIA JUSTIFICADO
                                        //case 43: //HORAS FRANCO COMPENSATORIO
                                        //case 44: //RELOJ NO LEE TIMBRADO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        $inicio_inc->add(new DateInterval('P1D'));
                    }
                    $planilla_excel[$Emp->datos->labo_Codigo]['resumen'] = $resumen_empleado;
                    $fila_excel++;
//					}
                }
                //ARMADO TITULO PLANILLA
                $tit_fecha = array();
                $tit_desc = array();
                $inicio_inc = clone $inicio;
                while ($inicio_inc < $fin)
                {
                    $str_fecha = $this->_getDayName($inicio_inc);
                    $str_fecha .= ' ' . date_format($inicio_inc, 'j');
                    $str_fecha .= ' ' . $this->_getMonthName($inicio_inc);
                    if (array_key_exists($inicio_inc->format('dmY'), $tmp_feriados))
                    {
                        $str_fecha .= ' (FERIADO)';
                    }
                    $tit_fecha[] = $str_fecha;
                    $tit_fecha[] = '';
                    $tit_fecha[] = '';
                    $tit_desc[] = 'E';
                    $tit_desc[] = 'S';
                    $tit_desc[] = 'NOVEDAD';
                    $inicio_inc->add(new DateInterval('P1D'));
                }
                $tit_fecha[] = 'RESUMEN';
                $tit_planilla = array($tit_fecha, $tit_desc);

                //INICIO GENERACION EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $cant_filas = sizeof($planilla_excel) + 6;
                $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
                if (!$validLocale)
                {
                    lm('Unable to set locale to es - reverting to en_us');
                }
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Parte Diario")
                        ->setDescription("Parte Diario del Personal");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Parte Diario");

                //OPCIONES DE IMPRESIÓN
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(1);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(1);
                $sheet->getPageSetup()->setFitToWidth(1);

                $sheet->getStyle("A5:{$columna_excel}6")->getFont()->setSize(10);
                $sheet->getStyle("A7:$columna_excel$cant_filas")->getFont()->setSize(8);
                $sheet->getStyle("H4:$columna_excel$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getStyle("{$columna_excel}4:$columna_excel$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)); //RESUMEN
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(6);
                $sheet->getColumnDimension('G')->setWidth(6);
                $sheet->getColumnDimension($columna_excel)->setWidth(30);

                $border_left_thin = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                $border_allborders_thin = array(
                    'borders' => array(
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                //TITULO TABLA
                $col = 'H';
                $col_anterior = 'G';
                for ($i = 1; $i <= sizeof($tit_fecha) - 1; $i++)
                {
                    if ($i % 3 === 1)
                    {
                        $sheet->getColumnDimension($col)->setWidth(13);
                        $sheet->getStyle("{$col}5:$col$cant_filas")->applyFromArray($border_left_thin);
                        $col_merge = $col;
                        $col_merge++;
                        $col_merge++;
                        $sheet->mergeCells("{$col}5:{$col_merge}5");
                    }
                    else if ($i % 3 === 0)
                    {
                        $sheet->getColumnDimension($col)->setWidth(22);
                        $sheet->getStyle("{$col}7:$col$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT));
                    }
                    else
                    {
                        $sheet->getColumnDimension($col)->setWidth(13);
                    }

                    $col_anterior = $col;
                    $col++;
                }
                $sheet->mergeCells("A5:A6"); //LEGAJO
                $sheet->mergeCells("B5:B6"); //APELLIDO Y NOMBRE
                $sheet->mergeCells("C5:C6"); //OFICINA
                $sheet->mergeCells("D5:D6"); //ADICIONALES
                $sheet->mergeCells("E5:E6"); //HORARIOS
                $sheet->mergeCells("F5:F6"); //ENTRADAS
                $sheet->mergeCells("G5:G6"); //SALIDAS
                $sheet->mergeCells("{$columna_excel}5:{$columna_excel}6"); //RESUMEN
                $sheet->getStyle("A5:$col_anterior$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                $sheet->getStyle("{$columna_excel}5:{$columna_excel}$cant_filas")->applyFromArray($border_allborders_thin); //BORDES RESUMEN
                $sheet->getStyle("A5:{$col_anterior}6")->getFont()->setBold(TRUE); //NEGRITA TITULO
                $sheet->getStyle("{$columna_excel}5:{$columna_excel}6")->getFont()->setBold(TRUE); //NEGRITA RESUMEN
                $sheet->fromArray(array(array('LEGAJO', 'APELLIDO Y NOMBRE', 'OFICINA', 'ADICIONALES', 'HORARIO', 'E**', 'S**')), NULL, 'A5');
                $sheet->getStyle("A5:G5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("{$columna_excel}5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->fromArray($tit_planilla, NULL, 'H5');
                $sheet->freezePane('H7');

                //TITULO PRINCIPAL
                $sheet->fromArray(array(array('PARTE DIARIO DEL PERSONAL')), NULL, 'A1');
                $sheet->mergeCells("A1:G1");
                $sheet->mergeCells("A2:G2");
                $sheet->getStyle('A1')->getFont()->setSize(18);
                $sheet->fromArray(array(array("SECRETARÍA: $array_secretaria[$ofi_Secretaria]")), NULL, 'A2');
                $sheet->fromArray(array(array("OFICINA: " . implode(', ', $oficinas_titulo))), NULL, 'A3');
                $sheet->fromArray(array(array("MOSTRAR: " . $array_mostrar[$mostrar])), NULL, 'A4');
                $sheet->mergeCells("A3:G3");
                $sheet->mergeCells("A4:G4");
                $sheet->mergeCells("A4:G4");
                $sheet->getStyle('A2')->getFont()->setSize(14);
                $sheet->getStyle('A3:A4')->getFont()->setSize(10);

                //DATOS TABLA
                $sheet->fromArray($planilla_excel, NULL, 'A7');

                if ($mostrar >= 3)
                {
                    //ACLARACIONES HORARIOS
                    $sheet->setCellValue('I1', 'ACLARACIONES FUNCIONAMIENTO ACTUAL');
                    $sheet->setCellValue('I2', '* PERSONAL: Temporalmente muestra sólo el personal con horarios normales (N), no incluyendo horarios rotativos (R) ni flexibles (F).');
                    $sheet->setCellValue('I3', '** AUSENCIAS: Cuenta la cantidad de días sin entradas (E) o salidas (S).');
                    $sheet->getStyle('I1:I4')->applyFromArray(array(
                        'font' => array(
                            'bold' => true,
                            'color' => array('rgb' => 'FF0000'),
                    )));
                    $sheet->getStyle('I1:I2')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT));
                }

                //RELLENOS A NOVEDADES
                if (!empty($rellenos_excel))
                {
                    foreach ($rellenos_excel as $Celda => $Relleno)
                    {
                        $sheet->getStyle($Celda)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => $Relleno)
                                    )
                                )
                        );
                    }
                }

                //LETRA ROJA A GENTE QUE NO DEBE FICHAR
                if (!empty($noficha_excel))
                {
                    foreach ($noficha_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:$columna_excel$Fila")->applyFromArray(
                                array(
                                    'font' => array(
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //RELLENOS A AUSENCIAS
                if (!empty($ausentes_excel))
                {
                    foreach ($ausentes_excel as $Ausente)
                    {
                        $inicio_ausente_merge = $Ausente['columna'] . $Ausente['fila'];
                        $Ausente['columna']++;
                        $Ausente['columna']++;
                        $fin_ausente_merge = $Ausente['columna'] . $Ausente['fila'];
                        $sheet->mergeCells("$inicio_ausente_merge:$fin_ausente_merge");
                        $sheet->getStyle($inicio_ausente_merge)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'E6B4B8') //ROJO PARA AUSENCIAS
                                    )
                                )
                        );
                    }
                }

                //REFERENCIAS
                $sheet->setCellValue('A' . (string) ($cant_filas + 2), 'REFERENCIAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 3))->applyFromArray(
                        array(
                            'font' => array(
                                'color' => array('rgb' => 'FF0000')
                            )
                        )
                );
                $sheet->setCellValue('A' . (string) ($cant_filas + 3), 'ROJO');
                $sheet->setCellValue('B' . (string) ($cant_filas + 3), 'PERSONAL QUE NO ES NECESARIO QUE FICHE');

                $sheet->getStyle('A' . (string) ($cant_filas + 4))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'C4D79B')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 4), 'NOVEDADES: HS EXTRAS Y HS ANTICIPADAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 5))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF99')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 5), 'NOVEDADES: LLEGADAS TARDE Y SALIDAS ANTICIPADAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 6))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FABF8F')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 6), 'NOVEDADES: OMISIÓN DE FICHADAS Y HS/DÍAS NO TRABAJADOS');

                $sheet->getStyle('A' . (string) ($cant_filas + 7))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'E6B4B8')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 7), 'AUSENCIAS');

                //NOMBRES RELOJES
                $sheet->setCellValue('A' . (string) ($cant_filas + 9), 'RELOJES');
                $sheet->fromArray(array(array('(1) TABOADA 1', '(2) CENTRO CÍVICO 2', '(3) HACIENDA')), NULL, 'B' . (string) ($cant_filas + 10));
                $sheet->fromArray(array(array('(4) OBRADOR', '(5) POLICIA VIAL', '(6) POLIDEPORTIVO')), NULL, 'B' . (string) ($cant_filas + 11));
                $sheet->fromArray(array(array('(7) DELEG CHACRAS', '(8) DELEG CARRODILLA', '(9) SANTA ELENA')), NULL, 'B' . (string) ($cant_filas + 12));
                $sheet->fromArray(array(array('(10) DESARROLLO SOCIAL', '(11) PLANTA POTABILIZADORA', '(12) CENTRO CÍVICO 1')), NULL, 'B' . (string) ($cant_filas + 13));
                $sheet->fromArray(array(array('(13) ESTACION FERRI', '(14) CEMENTERIO', '(15) DELEG PERDRIEL')), NULL, 'B' . (string) ($cant_filas + 14));
                $sheet->fromArray(array(array('(16) DELEG AGRELO', '(17) DELEG UGARTECHE', '(18) DELEG CARRIZAL')), NULL, 'B' . (string) ($cant_filas + 15));
                $sheet->fromArray(array(array('(19) BIBLIOTECA', '(20) DELEG COMPUERTAS', '(21) DELEG PEDEMONTE')), NULL, 'B' . (string) ($cant_filas + 16));
                $sheet->fromArray(array(array('(22) DELEG DRUMMOND', '(23) CENTRO CÍVICO', '(24) PLAYÓN ESTE', '(25) POLID CARRODILLA')), NULL, 'B' . (string) ($cant_filas + 17));

                $nombreArchivo = 'parte_diario_' . date('YmdHi');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            elseif (isset($personal['data']->error))
            {
                $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                redirect('asistencia/escritorio', 'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', '<br />No se encontraron personas en la oficina seleccionada');
                redirect('asistencia/reportes_major/parte_diario', 'refresh');
            }
//			$this->benchmark->mark('post_end');
//			lm('POST: ' . $this->benchmark->elapsed_time('post_start', 'post_end'));
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['secretaria_sel'] = $this->form_validation->set_value('secretaria');

        $fake_model->fields['mostrar']['array'] = $array_mostrar;
        $fake_model->fields['oficina']['array'] = $array_oficina;
        $fake_model->fields['particion']['array'] = $array_particion;
        $fake_model->fields['secretaria']['array'] = $array_secretaria;

        //OPCIONES POR DEFECTO
        $default = new stdClass();
        $default->secretaria_id = NULL;
        $default->oficina_id = NULL;
        $default->particion_id = NULL;
        $default->mostrar_id = '4';
        $default->fecha = 'now';
        $default->desde = NULL;
        $default->hasta = NULL;


        $data['fields'] = $this->build_fields($fake_model->fields, $default);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Parte Diario del Personal';
        $data['title'] = TITLE . ' - Parte Diario';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/reportes_major/reportes_parte_diario', $data);
//		$this->benchmark->mark('all_end');
//		lm('ALL: ' . $this->benchmark->elapsed_time('all_start', 'all_end'));
    }

    public function parte_diario_impresion()
    {
        if (!in_groups($this->grupos_contralor, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);
        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_secretaria_control = $array_secretaria;

        $array_oficina = array();
        $array_oficina_temp = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficina_temp[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_oficina_control = $array_oficina_temp;

        $this->array_particion_control = $array_particion = array(
            '01' => '01 - MUNICIPALES',
            '02' => '02 - H.C.D.',
            '05' => '05 - JARDINES MATERNALES',
            '07' => '07 - LOCACIONES',
            '10' => '10 - PLAN CONSTRUYENDO MI FUTURO'
        );

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'secretaria' => array('label' => 'Secretaría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'particion' => array('label' => 'Partición', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE),
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $ofi_Secretaria = $this->input->post('secretaria');
            $ofi_Oficina = $this->input->post('oficina');
            $prtn_Codigo = $this->input->post('particion');
            $desde = $this->input->post('desde');
            $hasta = $this->input->post('hasta');
            $desde_sql = DateTime::createFromFormat("d/m/Y", $desde)->format('Ymd');
            $hasta_sql = DateTime::createFromFormat("d/m/Y", $hasta)->format('Ymd');

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
            } catch (Exception $e)
            {
                $feriados = NULL;
            }

            $tmp_feriados = array();
            if (!empty($feriados))
            {
                foreach ($feriados as $Feriado)
                {
                    $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                }
            }

            //BUSCO HORARIOS DIARIOS
            try
            {
                $http_response_horarios = $guzzleHttp->request('GET', "personas/horarios_diarios_oficina", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $horarios_diarios = json_decode($http_response_horarios->getBody()->getContents());
            } catch (Exception $e)
            {
                $horarios_diarios = NULL;
            }

            $tmp_horarios_diarios = array();
            if (!empty($horarios_diarios))
            {
                foreach ($horarios_diarios as $Diario)
                {
                    $tmp_horarios_diarios[$Diario->labo_Codigo][(new DateTime($Diario->hodi_Fecha))->format('dmY')] = array('E' => $Diario->hodi_Entrada, 'S' => $Diario->hodi_Salida);
                }
            }

            //BUSCA TODOS LOS EMPLEADOS DE LAS OFICINAS SELECCIONADAS Y DATOS ASOCIADOS(NOVEDADES, FICHADAS, NOVEDADES DE FICHADAS Y AUSENCIAS)
            try
            {
                $http_response_personal = $guzzleHttp->request('GET', "personas/parte_diario_impresion", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'prtn_Codigo' => $prtn_Codigo, 'desde' => $desde, 'hasta' => $hasta, 'feriados' => $tmp_feriados]]);
                $personal = json_decode($http_response_personal->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal = NULL;
            }

            if (!empty($personal))
            {
                //RANGO DE FECHAS A TRABAJAR
                $inicio = DateTime::createFromFormat("d/m/Y H:i:s", $desde . ' 00:00:00');
                $fin = DateTime::createFromFormat("d/m/Y H:i:s", $hasta . ' 00:00:00');

                //FILA DONDE COMIENZAN LOS DATOS
                $fila_excel = 6;

                //CARGA LOS DATOS NECESARIOS PARA EXCEL
                $planilla_excel = array();
                $fines_excel = array();
                $noficha_excel = array();
                $oficinas_titulo = array();

                //MUESTRA TODAS LAS OFICINAS SELECCIONADAS
                foreach ($ofi_Oficina as $Oficina_sel)
                {
                    $oficinas_titulo[$array_oficina_temp[$Oficina_sel]] = $array_oficina_temp[$Oficina_sel];
                }

                foreach ($personal as $Emp)
                {
                    //EVITA ERRORES SI EL EMPLEADO ESTA 2 VECES (POR HORARIOS REPETIDOS GENERALMENTE)
                    if (!empty($planilla_excel[$Emp->datos[0]->labo_Codigo]))
                    {
                        continue;
                    }

                    //LISTADO DE PERSONAL PARA EXCEL (CON DATOS DEL PRIMER HORARIO)
                    $planilla_excel[$Emp->datos[0]->labo_Codigo] = array(
                        'labo_Codigo' => $Emp->datos[0]->labo_Codigo,
                        'nombre' => $Emp->datos[0]->nombre,
                        'oficina' => '',
                        'horario' => ''
                    );

                    //FICHADAS
                    $tmp_fich = array();
                    if (!empty($Emp->fichadas))
                    {
                        foreach ($Emp->fichadas as $Fich)
                        {
                            //PONER SALIDA EN EL DÍA ANTERIOR
//							if (trim($Fich->fich_Codigo) === 'S')
//							{
//								$fecha = new DateTime($Fich->fich_FechaHora);
//								$hora = $fecha->format("H");
//								if (!isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . 'E']) && $hora < 1) //NO HAY ENTRADA ANTERIOR Y SON MENOS DE LA 1 AM
//								{
//									$fecha->sub(new DateInterval('P1D'));
//									if (isset($tmp_fich[date_format($fecha, 'mj') . 'S'])) //YA HAY SALIDA EL DÍA ANTERIOR
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									else
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									continue; //SALTANDO SALIDA QUE SUPUESTAMENTE CORRESPONDE AL DIA ANTERIOR HASTA DEFINIR COMO LA MANEJA MAJOR
//								}
//							}

                            if (isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)]))
                            {
                                if (trim($Fich->fich_Codigo) === 'E')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') >= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA ENTRADA DESPUES DE 5 MINUTOS DE LA PRIMERA
                                    }
                                }
                                elseif (trim($Fich->fich_Codigo) === 'S')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') <= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                                    }
                                    else
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA SALIDA DESPUES DE 5 MINUTOS DE LA SEGUNDA
                                    }
                                }
                            }
                            else
                            {
                                $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                            }
                        }
                    }

                    //AUSENCIAS
                    if (!empty($Emp->ausencias))
                    {
                        foreach ($Emp->ausencias as $Aus)
                        {
                            $fecha_ini_aus = new DateTime($Aus->ause_FechaInicio);
                            if ($fecha_ini_aus < $inicio)
                            {
                                $fecha_ini_aus = clone $inicio;
                            }
                            $fecha_fin_aus = new DateTime($Aus->ause_FechaFin);
                            if ($fecha_fin_aus > $fin)
                            {
                                $fecha_fin_aus = clone $fin;
                            }

                            do
                            {
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'E'] = $Aus->moau_Descripcion;
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'S'] = $Aus->moau_Codigo;
                                $fecha_ini_aus->add(new DateInterval('P1D'));
                            } while ($fecha_ini_aus <= $fecha_fin_aus);
                        }
                    }

                    foreach ($Emp->datos as $Horario)
                    {
                        //INICIALIZO FECHAS (HORARIO)
                        $inicio_horario = new Datetime($Horario->hoca_FechaDesde);
                        $fin_horario = new Datetime($Horario->hoca_FechaHasta);

                        //CARGA DATOS DE OFICINAS Y HORARIOS
                        if (empty($planilla_excel[$Horario->labo_Codigo]['oficina']))
                        {
                            $planilla_excel[$Horario->labo_Codigo]['oficina'] = $Horario->oficina;
                        }
                        else if ($planilla_excel[$Horario->labo_Codigo]['oficina'] !== $Horario->oficina)
                        {
                            $planilla_excel[$Horario->labo_Codigo]['oficina'] .= " | $Horario->oficina";
                        }

                        if (!empty($Horario->horario))
                        {
                            if (empty($planilla_excel[$Horario->labo_Codigo]['horario']))
                            {
                                $planilla_excel[$Horario->labo_Codigo]['horario'] = $Horario->horario . ' (' . date_format(new DateTime($Horario->hoca_FechaDesde), 'd-m-Y') . ' a ' . date_format(new DateTime($Horario->hoca_FechaHasta), 'd-m-Y') . ')';
                            }
                            else
                            {
                                $planilla_excel[$Horario->labo_Codigo]['horario'] .= " | $Horario->horario" . ' (' . date_format(new DateTime($Horario->hoca_FechaDesde), 'd-m-Y') . ' a ' . date_format(new DateTime($Horario->hoca_FechaHasta), 'd-m-Y') . ')';
                            }
                        }

                        //FILAS EN ROJO PARA PERSONAL QUE NO FICHA
                        if ($Horario->hoca_Ficha === 'N')
                        {
                            $noficha_excel[$fila_excel] = 'FF0000';
                        }

                        //RECORRE TODO EL RANGO DE FECHAS PARA ARMAR $planilla_arr
                        $fecha_inicial_while = clone $inicio;
                        $fecha_final_while = clone $fin;
                        //COLUMNA DONDE COMIENZAN LOS DATOS
                        $columna_excel = 'E';
                        while ($fecha_inicial_while <= $fecha_final_while)
                        {
                            $str_fecha_ini = date_format($fecha_inicial_while, 'mj');

                            if (!empty($tmp_horarios_diarios[$Horario->labo_Codigo]) && array_key_exists($fecha_inicial_while->format('dmY'), $tmp_horarios_diarios[$Horario->labo_Codigo]))
                            {
                                $horario_diario = $tmp_horarios_diarios[$Horario->labo_Codigo][$fecha_inicial_while->format('dmY')];
                                if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
                                    if (!ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
                                    {
                                        switch ($tmp_fich[$str_fecha_ini . 'S'])
                                        {
                                            case 1:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'VAC';
                                                break;
                                            case 4:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ENF';
                                                break;
                                            case 6:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ACC';
                                                break;
                                            case 8:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'MAT';
                                                break;
                                            case 9:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'LAC';
                                                break;
                                            case 14:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAT';
                                                break;
                                            case 19:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAR';
                                                break;
                                            case 23:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'JUN';
                                                break;
                                            default:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AUS';
                                                break;
                                        }
                                    }
                                    else
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'P';
                                    }
                                }
                                else if (!empty($tmp_fich[$str_fecha_ini . 'E']))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PA';
                                }
                                else if (!empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AP';
                                }
                                else
                                {
                                    if ($horario_diario['E'] !== '00:00' || $horario_diario['S'] !== '00:00') //HORARIO DIARIO CON E O S PARA EL EMPLEADO
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'A';
                                    }
                                    else
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                    }
                                }
                            }
                            else
                            {
                                if ($fecha_inicial_while >= $inicio_horario && $fecha_inicial_while <= $fin_horario)
                                {
                                    //VERIFICA NUMERO DE DIA
                                    $error_secuencia = FALSE;
                                    $error_horario = FALSE;
                                    if (!empty($Horario->hora_Codigo))
                                    {
                                        switch ($Horario->hora_Tipo)
                                        {
                                            case 'N':
                                            case 'F':
                                                if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_feriados))
                                                {
                                                    $numero_dia = 8; //FERIADO ES 8 EN MAJOR
                                                }
                                                else
                                                {
                                                    $numero_dia = $fecha_inicial_while->format('w');
                                                    if ($numero_dia === '0')
                                                    {
                                                        $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                                    }
                                                }
                                                break;
                                            case 'R':
                                                $inicio_sec = new DateTime($Horario->hoca_FechaSecuencia1);
                                                if ($fecha_inicial_while < $inicio_sec)
                                                {
                                                    $numero_dia = 1;
                                                    $error_secuencia = TRUE;
                                                }
                                                else
                                                {
                                                    $dias_inicio_sec = $fecha_inicial_while->diff($inicio_sec)->format("%a");
                                                    $dias_inicio_sec++; //SI ES EL PRIMER DÍA DEVUELVE 0 Y DEBERIA SER 1
                                                    $sum_dias_sec = 0;
                                                    for ($i = 1; $i <= 20; $i++)
                                                    {
                                                        $sum_dias_sec += (int) $Horario->{"hora_DiaSec" . $i . "Cant"};
                                                    }

                                                    $resto_sec = $dias_inicio_sec % $sum_dias_sec;
                                                    if ($resto_sec === 0)
                                                    {
                                                        $resto_sec = $sum_dias_sec;
                                                    }
                                                    for ($i = 1; $i <= 20; $i++)
                                                    {
                                                        if ($resto_sec <= (int) $Horario->{"hora_DiaSec" . $i . "Cant"})
                                                        {
                                                            $numero_dia = $i;
                                                            break;
                                                        }
                                                        else
                                                        {
                                                            $resto_sec -= (int) $Horario->{"hora_DiaSec" . $i . "Cant"};
                                                        }
                                                    }
                                                }
                                                break;
                                        }
                                    }
                                    else
                                    {
                                        $error_horario = TRUE;
                                        $numero_dia = 1;
                                    }

                                    if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !empty($tmp_fich[$str_fecha_ini . 'S']))
                                    {
                                        if (!ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
                                        {
                                            switch ($tmp_fich[$str_fecha_ini . 'S'])
                                            {
                                                case 1:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'VAC';
                                                    break;
                                                case 4:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ENF';
                                                    break;
                                                case 6:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ACC';
                                                    break;
                                                case 8:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'MAT';
                                                    break;
                                                case 9:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'LAC';
                                                    break;
                                                case 14:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAT';
                                                    break;
                                                case 19:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAR';
                                                    break;
                                                case 23:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'JUN';
                                                    break;
                                                default:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AUS';
                                                    break;
                                            }
                                        }
                                        else
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'P';
                                        }
                                    }
                                    else if (!empty($tmp_fich[$str_fecha_ini . 'E']))
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PA';
                                    }
                                    else if (!empty($tmp_fich[$str_fecha_ini . 'S']))
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AP';
                                    }
                                    else
                                    {
                                        if ($Horario->{'hora_DiaSec' . $numero_dia . 'Ent'} !== '00:00' || $Horario->{'hora_DiaSec' . $numero_dia . 'Sal'} !== '00:00') //DIA CON E O S PARA EL EMPLEADO
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'A';
                                        }
                                        else
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                        }
                                    }
                                }
                                elseif (empty($planilla_excel[$Horario->labo_Codigo][$str_fecha_ini]))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                }
                            }

                            if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_feriados))
                            {
                                $col_dia = 8; //FERIADO ES 8 EN MAJOR
                            }
                            else
                            {
                                $col_dia = $fecha_inicial_while->format('w');
                                if ($col_dia === '0')
                                {
                                    $col_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                }
                            }
                            if ($col_dia == 6 || $col_dia == 7 || $col_dia == 8) //NO USAR ===
                            {
                                $fines_excel[$columna_excel] = array('columna' => $columna_excel);
                            }
                            $columna_excel++;

                            $fecha_inicial_while->add(new DateInterval('P1D'));
                        }
                    }
                    $fila_excel++;
                }

                //ARMADO TITULO PLANILLA
                $tit_fecha = array();
                $inicio_inc = clone $inicio;
                while ($inicio_inc <= $fin)
                {
                    $str_fecha = date_format($inicio_inc, 'j');
                    $tit_fecha[] = $str_fecha;
                    $inicio_inc->add(new DateInterval('P1D'));
                }
                $tit_planilla = array($tit_fecha);

                //INICIO GENERACION EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $cant_filas = sizeof($planilla_excel) + 5;
                $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
                if (!$validLocale)
                {
                    lm('Unable to set locale to es - reverting to en_us');
                }
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Parte Diario Impresión")
                        ->setDescription("Parte Diario del Personal Impresión");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Parte Diario Impresión");

                //OPCIONES DE IMPRESIÓN
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(1);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(1);
                $sheet->getPageSetup()->setFitToWidth(1);

                $sheet->getStyle("A5:{$columna_excel}5")->getFont()->setSize(10);
                $sheet->getStyle("A6:$columna_excel$cant_filas")->getFont()->setSize(8);
                $sheet->getStyle("E4:$columna_excel$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(25);

                $border_allborders_thin = array(
                    'borders' => array(
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                //TITULO TABLA
                $col = 'E';
                $col_anterior = 'D';
                for ($i = 1; $i <= sizeof($tit_fecha); $i++)
                {
                    $sheet->getColumnDimension($col)->setWidth(4);

                    $col_anterior = $col;
                    $col++;
                }
                $sheet->getStyle("A5:$col_anterior$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                $sheet->getStyle("A5:{$col_anterior}5")->getFont()->setBold(TRUE); //NEGRITA TITULO
                $sheet->getStyle("{$columna_excel}5:{$columna_excel}5")->getFont()->setBold(TRUE); //NEGRITA RESUMEN
                $sheet->fromArray(array(array('LEGAJO', 'APELLIDO Y NOMBRE', 'OFICINA', 'HORARIO')), NULL, 'A5');
                $sheet->getStyle("A5:D5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("{$columna_excel}5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->fromArray($tit_planilla, NULL, 'E5');
                $sheet->freezePane('E6');

                //TITULO PRINCIPAL
                $sheet->fromArray(array(array('PARTE DIARIO DEL PERSONAL')), NULL, 'A1');
                $sheet->mergeCells("A1:D1");
                $sheet->mergeCells("A2:D2");
                $sheet->getStyle('A1')->getFont()->setSize(18);
                $sheet->fromArray(array(array("SECRETARÍA: $array_secretaria[$ofi_Secretaria]")), NULL, 'A2');
                $sheet->fromArray(array(array("OFICINA: " . implode(', ', $oficinas_titulo))), NULL, 'A3');
                $sheet->fromArray(array(array("DESDE: $desde - HASTA: $hasta")), NULL, 'A4');
                $sheet->mergeCells("A3:D3");
                $sheet->mergeCells("A4:D4");
                $sheet->mergeCells("A4:D4");
                $sheet->getStyle('A2')->getFont()->setSize(14);
                $sheet->getStyle('A3:A4')->getFont()->setSize(10);

                //DATOS TABLA
                $sheet->fromArray($planilla_excel, NULL, 'A6');

                //LETRA ROJA A GENTE QUE NO DEBE FICHAR
                if (!empty($noficha_excel))
                {
                    foreach ($noficha_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:$columna_excel$Fila")->applyFromArray(
                                array(
                                    'font' => array(
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //RELLENOS A FINES DE SEMANA Y FERIADOS
                if (!empty($fines_excel))
                {
                    foreach ($fines_excel as $Finde)
                    {
                        $sheet->getStyle($Finde['columna'] . "5:" . $Finde['columna'] . $cant_filas)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'D9D9D9') //ROJO PARA AUSENCIAS
                                    )
                                )
                        );
                    }
                }

                //NOMBRES RELOJES
                $sheet->setCellValue('A' . (string) ($cant_filas + 2), 'REFERENCIAS');
                $sheet->fromArray(array(array('A', 'SIN ENTRADA Y SIN SALIDA')), NULL, 'A' . (string) ($cant_filas + 3));
                $sheet->fromArray(array(array('P', 'CON ENTRADA Y SALIDA')), NULL, 'A' . (string) ($cant_filas + 4));
                $sheet->fromArray(array(array('PA', 'CON ENTRADA Y SIN SALIDA')), NULL, 'A' . (string) ($cant_filas + 5));
                $sheet->fromArray(array(array('AP', 'SIN ENTRADA Y CON SALIDA')), NULL, 'A' . (string) ($cant_filas + 6));
                $sheet->fromArray(array(array('VAC', 'LICENCIA ANUAL ORDINARIA-LEY 5811-CAP II')), NULL, 'A' . (string) ($cant_filas + 7));
                $sheet->fromArray(array(array('ENF', 'PARTE DE ENFERMO-LEY 5811-CAP III ART 40')), NULL, 'A' . (string) ($cant_filas + 8));
                $sheet->fromArray(array(array('ACC', 'ACC. DE TRABAJO/ENF.PROF-LEY5811 ART 37')), NULL, 'A' . (string) ($cant_filas + 9));
                $sheet->fromArray(array(array('MAT', 'LICENCIA POR MATERNIDAD-LEY 5811 ART54')), NULL, 'A' . (string) ($cant_filas + 10));
                $sheet->fromArray(array(array('LAC', 'LIC. LACTANCIA EXCLUSIVA-ORD. 8450-2008')), NULL, 'A' . (string) ($cant_filas + 11));
                $sheet->fromArray(array(array('PAT', 'LIC. NACIMIENTO HIJO-LEY 8687 ART54 BIS')), NULL, 'A' . (string) ($cant_filas + 12));
                $sheet->fromArray(array(array('PAR', 'TRAMITES PARTICULARES  50.C.9')), NULL, 'A' . (string) ($cant_filas + 13));
                $sheet->fromArray(array(array('JUN', 'JUNTA MEDICA')), NULL, 'A' . (string) ($cant_filas + 14));
                $sheet->fromArray(array(array('AUS', 'OTROS MOTIVOS DE AUSENCIA')), NULL, 'A' . (string) ($cant_filas + 15));

                $nombreArchivo = 'parte_diario_impresion_' . date('YmdHi');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            elseif (isset($personal['data']->error))
            {
                $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                redirect('asistencia/escritorio', 'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', '<br />No se encontraron personas en la oficina seleccionada');
                redirect('asistencia/reportes_major/parte_diario_impresion', 'refresh');
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['secretaria_sel'] = $this->form_validation->set_value('secretaria');

        $fake_model->fields['secretaria']['array'] = $array_secretaria;
        $fake_model->fields['oficina']['array'] = $array_oficina;
        $fake_model->fields['particion']['array'] = $array_particion;

        //OPCIONES POR DEFECTO
        $default = new stdClass();
        $default->secretaria_id = NULL;
        $default->oficina_id = NULL;
        $default->particion_id = NULL;
        $default->mostrar_id = '4';
        $default->fecha = 'now';
        $default->desde = NULL;
        $default->hasta = NULL;

        $data['fields'] = $this->build_fields($fake_model->fields, $default);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Parte Diario del Personal Impresión';
        $data['title'] = TITLE . ' - Parte Diario Impresión';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/reportes_major/reportes_parte_diario', $data);
    }

    public function parte_diario_horas()
    {
        if (!in_groups($this->grupos_contralor, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);
        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_secretaria_control = $array_secretaria;

        $array_oficina = array();
        $array_oficina_temp = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficina_temp[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_oficina_control = $array_oficina_temp;

        $this->array_particion_control = $array_particion = array(
            '01' => '01 - MUNICIPALES',
            '02' => '02 - H.C.D.',
            '05' => '05 - JARDINES MATERNALES',
            '07' => '07 - LOCACIONES',
            '10' => '10 - PLAN CONSTRUYENDO MI FUTURO'
        );

        $this->array_mostrar_control = $array_mostrar = array(
            '1' => 'Todo el personal',
            '2' => 'Sólo el personal con ausencias o con menos de 6 HS',
            '3' => 'Sólo el personal con menos de 6 HS'
        );

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'secretaria' => array('label' => 'Secretaría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'particion' => array('label' => 'Partición', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'mostrar' => array('label' => 'Mostrar', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE),
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $ofi_Secretaria = $this->input->post('secretaria');
            $ofi_Oficina = $this->input->post('oficina');
            $prtn_Codigo = $this->input->post('particion');
            $mostrar = $this->input->post('mostrar');
            $desde = $this->input->post('desde');
            $hasta = $this->input->post('hasta');
            $desde_sql = DateTime::createFromFormat("d/m/Y", $desde)->format('Ymd');
            $hasta_sql = DateTime::createFromFormat("d/m/Y", $hasta)->format('Ymd');

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
            } catch (Exception $e)
            {
                $feriados = NULL;
            }

            $tmp_feriados = array();
            if (!empty($feriados))
            {
                foreach ($feriados as $Feriado)
                {
                    $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                }
            }

            //BUSCO HORARIOS DIARIOS
            try
            {
                $http_response_horarios = $guzzleHttp->request('GET', "personas/horarios_diarios_oficina", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $horarios_diarios = json_decode($http_response_horarios->getBody()->getContents());
            } catch (Exception $e)
            {
                $horarios_diarios = NULL;
            }

            $tmp_horarios_diarios = array();
            if (!empty($horarios_diarios))
            {
                foreach ($horarios_diarios as $Diario)
                {
                    $tmp_horarios_diarios[$Diario->labo_Codigo][(new DateTime($Diario->hodi_Fecha))->format('dmY')] = array('E' => $Diario->hodi_Entrada, 'S' => $Diario->hodi_Salida);
                }
            }

            //BUSCA TODOS LOS EMPLEADOS DE LAS OFICINAS SELECCIONADAS Y DATOS ASOCIADOS(NOVEDADES, FICHADAS, NOVEDADES DE FICHADAS Y AUSENCIAS)
            try
            {
                $http_response_personal = $guzzleHttp->request('GET', "personas/parte_diario_impresion", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'prtn_Codigo' => $prtn_Codigo, 'desde' => $desde, 'hasta' => $hasta, 'feriados' => $tmp_feriados]]);
                $personal = json_decode($http_response_personal->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal = NULL;
            }

            if (!empty($personal))
            {
                //RANGO DE FECHAS A TRABAJAR
                $inicio = DateTime::createFromFormat("d/m/Y H:i:s", $desde . ' 00:00:00');
                $fin = DateTime::createFromFormat("d/m/Y H:i:s", $hasta . ' 00:00:00');

                //FILA DONDE COMIENZAN LOS DATOS
                $fila_excel = 6;

                //CARGA LOS DATOS NECESARIOS PARA EXCEL
                $planilla_excel = array();
                $fines_excel = array();
                $noficha_excel = array();
                $rellenos_excel = array();
                $oficinas_titulo = array();

                //MUESTRA TODAS LAS OFICINAS SELECCIONADAS
                foreach ($ofi_Oficina as $Oficina_sel)
                {
                    $oficinas_titulo[$array_oficina_temp[$Oficina_sel]] = $array_oficina_temp[$Oficina_sel];
                }

                foreach ($personal as $Emp)
                {
                    //EVITA ERRORES SI EL EMPLEADO ESTA 2 VECES (POR HORARIOS REPETIDOS GENERALMENTE)
                    if (!empty($planilla_excel[$Emp->datos[0]->labo_Codigo]))
                    {
                        continue;
                    }

                    //LISTADO DE PERSONAL PARA EXCEL (CON DATOS DEL PRIMER HORARIO)
                    $planilla_excel[$Emp->datos[0]->labo_Codigo] = array(
                        'labo_Codigo' => $Emp->datos[0]->labo_Codigo,
                        'nombre' => $Emp->datos[0]->nombre,
                        'oficina' => '',
                        'horario' => ''
                    );

                    //FICHADAS
                    $tmp_fich = array();
                    if (!empty($Emp->fichadas))
                    {
                        foreach ($Emp->fichadas as $Fich)
                        {
                            //PONER SALIDA EN EL DÍA ANTERIOR
//							if (trim($Fich->fich_Codigo) === 'S')
//							{
//								$fecha = new DateTime($Fich->fich_FechaHora);
//								$hora = $fecha->format("H");
//								if (!isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . 'E']) && $hora < 1) //NO HAY ENTRADA ANTERIOR Y SON MENOS DE LA 1 AM
//								{
//									$fecha->sub(new DateInterval('P1D'));
//									if (isset($tmp_fich[date_format($fecha, 'mj') . 'S'])) //YA HAY SALIDA EL DÍA ANTERIOR
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									else
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									continue; //SALTANDO SALIDA QUE SUPUESTAMENTE CORRESPONDE AL DIA ANTERIOR HASTA DEFINIR COMO LA MANEJA MAJOR
//								}
//							}

                            if (isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)]))
                            {
                                if (trim($Fich->fich_Codigo) === 'E')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') >= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA ENTRADA DESPUES DE 5 MINUTOS DE LA PRIMERA
                                    }
                                }
                                elseif (trim($Fich->fich_Codigo) === 'S')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') <= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                                    }
                                    else
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA SALIDA DESPUES DE 5 MINUTOS DE LA SEGUNDA
                                    }
                                }
                            }
                            else
                            {
                                $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                            }
                        }
                    }

                    //AUSENCIAS
                    if (!empty($Emp->ausencias))
                    {
                        foreach ($Emp->ausencias as $Aus)
                        {
                            $fecha_ini_aus = new DateTime($Aus->ause_FechaInicio);
                            if ($fecha_ini_aus < $inicio)
                            {
                                $fecha_ini_aus = clone $inicio;
                            }
                            $fecha_fin_aus = new DateTime($Aus->ause_FechaFin);
                            if ($fecha_fin_aus > $fin)
                            {
                                $fecha_fin_aus = clone $fin;
                            }

                            do
                            {
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'E'] = $Aus->moau_Descripcion;
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'S'] = $Aus->moau_Codigo;
                                $fecha_ini_aus->add(new DateInterval('P1D'));
                            } while ($fecha_ini_aus <= $fecha_fin_aus);
                        }
                    }

                    foreach ($Emp->datos as $Horario)
                    {
                        //INICIALIZO FECHAS (HORARIO)
                        $inicio_horario = new Datetime($Horario->hoca_FechaDesde);
                        $fin_horario = new Datetime($Horario->hoca_FechaHasta);

                        //CARGA DATOS DE OFICINAS Y HORARIOS
                        if (empty($planilla_excel[$Horario->labo_Codigo]['oficina']))
                        {
                            $planilla_excel[$Horario->labo_Codigo]['oficina'] = $Horario->oficina;
                        }
                        else if ($planilla_excel[$Horario->labo_Codigo]['oficina'] !== $Horario->oficina)
                        {
                            $planilla_excel[$Horario->labo_Codigo]['oficina'] .= " | $Horario->oficina";
                        }

                        if (!empty($Horario->horario))
                        {
                            if (empty($planilla_excel[$Horario->labo_Codigo]['horario']))
                            {
                                $planilla_excel[$Horario->labo_Codigo]['horario'] = $Horario->horario . ' (' . date_format(new DateTime($Horario->hoca_FechaDesde), 'd-m-Y') . ' a ' . date_format(new DateTime($Horario->hoca_FechaHasta), 'd-m-Y') . ')';
                            }
                            else
                            {
                                $planilla_excel[$Horario->labo_Codigo]['horario'] .= " | $Horario->horario" . ' (' . date_format(new DateTime($Horario->hoca_FechaDesde), 'd-m-Y') . ' a ' . date_format(new DateTime($Horario->hoca_FechaHasta), 'd-m-Y') . ')';
                            }
                        }

                        //QUITAR PERSONAL QUE NO FICHA O PONER FILA EN ROJO
                        if ($Horario->hoca_Ficha === 'N')
                        {
                            if ($mostrar !== '1')
                            {
                                continue;
                            }
                            else
                            {
                                $noficha_excel[$fila_excel] = 'FF0000';
                            }
                        }

                        //RECORRE TODO EL RANGO DE FECHAS PARA ARMAR $planilla_arr
                        $fecha_inicial_while = clone $inicio;
                        $fecha_final_while = clone $fin;
                        //COLUMNA DONDE COMIENZAN LOS DATOS
                        $columna_excel = 'E';
                        while ($fecha_inicial_while <= $fecha_final_while)
                        {
                            $str_fecha_ini = date_format($fecha_inicial_while, 'mj');

                            if (!empty($tmp_horarios_diarios[$Horario->labo_Codigo]) && array_key_exists($fecha_inicial_while->format('dmY'), $tmp_horarios_diarios[$Horario->labo_Codigo]))
                            {
                                $horario_diario = $tmp_horarios_diarios[$Horario->labo_Codigo][$fecha_inicial_while->format('dmY')];
                                if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
                                    if (!ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
                                    {
                                        switch ($tmp_fich[$str_fecha_ini . 'S'])
                                        {
                                            case 1:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'VAC';
                                                break;
                                            case 4:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ENF';
                                                break;
                                            case 6:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ACC';
                                                break;
                                            case 8:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'MAT';
                                                break;
                                            case 9:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'LAC';
                                                break;
                                            case 14:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAT';
                                                break;
                                            case 19:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAR';
                                                break;
                                            case 23:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'JUN';
                                                break;
                                            default:
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AUS';
                                                break;
                                        }
                                        if ($mostrar === '2')
                                        {
                                            $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                        }
                                    }
                                    else
                                    {
                                        if ($tmp_fich[$str_fecha_ini . 'E'] < $tmp_fich[$str_fecha_ini . 'S'])
                                        {
                                            $ent = DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial_while->format('d/m/Y') . ' ' . substr($tmp_fich[$str_fecha_ini . 'E'], 0, 5) . ':00');
                                            $sal = DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial_while->format('d/m/Y') . ' ' . substr($tmp_fich[$str_fecha_ini . 'S'], 0, 5) . ':00');
                                            $diferencia_es = $ent->diff($sal);
                                            if ($mostrar !== '1' && $diferencia_es->h < 6)
                                            {
                                                $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                            }
                                            if ($diferencia_es->h < 6)
                                            {
                                                $rellenos_excel[$columna_excel . $fila_excel] = 'E6B4B8'; //ROJO
                                            }
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = str_pad($diferencia_es->h, 2, 0, STR_PAD_LEFT) . ':' . str_pad($diferencia_es->i, 2, 0, STR_PAD_LEFT);
                                        }
                                        else
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = "VER";
                                            if ($mostrar !== '1')
                                            {
                                                $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                            }
                                            $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                        }
                                    }
                                }
                                else if (!empty($tmp_fich[$str_fecha_ini . 'E']))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PA';
                                    if ($mostrar === '2')
                                    {
                                        $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                    }
                                }
                                else if (!empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AP';
                                    if ($mostrar === '2')
                                    {
                                        $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                    }
                                }
                                else
                                {
                                    if ($horario_diario['E'] !== '00:00' || $horario_diario['S'] !== '00:00') //HORARIO DIARIO CON E O S PARA EL EMPLEADO
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'A';
                                        if ($mostrar === '2')
                                        {
                                            $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                        }
                                    }
                                    else
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                    }
                                }
                            }
                            else
                            {
                                if ($fecha_inicial_while >= $inicio_horario && $fecha_inicial_while <= $fin_horario)
                                {
                                    //VERIFICA NUMERO DE DIA
                                    $error_secuencia = FALSE;
                                    $error_horario = FALSE;
                                    if (!empty($Horario->hora_Codigo))
                                    {
                                        switch ($Horario->hora_Tipo)
                                        {
                                            case 'N':
                                            case 'F':
                                                if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_feriados))
                                                {
                                                    $numero_dia = 8; //FERIADO ES 8 EN MAJOR
                                                }
                                                else
                                                {
                                                    $numero_dia = $fecha_inicial_while->format('w');
                                                    if ($numero_dia === '0')
                                                    {
                                                        $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                                    }
                                                }
                                                break;
                                            case 'R':
                                                $inicio_sec = new DateTime($Horario->hoca_FechaSecuencia1);
                                                if ($fecha_inicial_while < $inicio_sec)
                                                {
                                                    $numero_dia = 1;
                                                    $error_secuencia = TRUE;
                                                }
                                                else
                                                {
                                                    $dias_inicio_sec = $fecha_inicial_while->diff($inicio_sec)->format("%a");
                                                    $dias_inicio_sec++; //SI ES EL PRIMER DÍA DEVUELVE 0 Y DEBERIA SER 1
                                                    $sum_dias_sec = 0;
                                                    for ($i = 1; $i <= 20; $i++)
                                                    {
                                                        $sum_dias_sec += (int) $Horario->{"hora_DiaSec" . $i . "Cant"};
                                                    }

                                                    $resto_sec = $dias_inicio_sec % $sum_dias_sec;
                                                    if ($resto_sec === 0)
                                                    {
                                                        $resto_sec = $sum_dias_sec;
                                                    }
                                                    for ($i = 1; $i <= 20; $i++)
                                                    {
                                                        if ($resto_sec <= (int) $Horario->{"hora_DiaSec" . $i . "Cant"})
                                                        {
                                                            $numero_dia = $i;
                                                            break;
                                                        }
                                                        else
                                                        {
                                                            $resto_sec -= (int) $Horario->{"hora_DiaSec" . $i . "Cant"};
                                                        }
                                                    }
                                                }
                                                break;
                                        }
                                    }
                                    else
                                    {
                                        $error_horario = TRUE;
                                        $numero_dia = 1;
                                    }

                                    if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !empty($tmp_fich[$str_fecha_ini . 'S']))
                                    {
                                        if (!ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
                                        {
                                            switch ($tmp_fich[$str_fecha_ini . 'S'])
                                            {
                                                case 1:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'VAC';
                                                    break;
                                                case 4:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ENF';
                                                    break;
                                                case 6:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ACC';
                                                    break;
                                                case 8:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'MAT';
                                                    break;
                                                case 9:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'LAC';
                                                    break;
                                                case 14:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAT';
                                                    break;
                                                case 19:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAR';
                                                    break;
                                                case 23:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'JUN';
                                                    break;
                                                default:
                                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AUS';
                                                    break;
                                            }
                                            if ($mostrar === '2')
                                            {
                                                $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                            }
                                        }
                                        else
                                        {
                                            if ($tmp_fich[$str_fecha_ini . 'E'] < $tmp_fich[$str_fecha_ini . 'S'])
                                            {
                                                $ent = DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial_while->format('d/m/Y') . ' ' . substr($tmp_fich[$str_fecha_ini . 'E'], 0, 5) . ':00');
                                                $sal = DateTime::createFromFormat("d/m/Y H:i:s", $fecha_inicial_while->format('d/m/Y') . ' ' . substr($tmp_fich[$str_fecha_ini . 'S'], 0, 5) . ':00');
                                                $diferencia_es = $ent->diff($sal);
                                                if ($mostrar !== '1' && $diferencia_es->h < 6)
                                                {
                                                    $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                                }
                                                if ($diferencia_es->h < 6)
                                                {
                                                    $rellenos_excel[$columna_excel . $fila_excel] = 'E6B4B8'; //ROJO
                                                }
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = str_pad($diferencia_es->h, 2, 0, STR_PAD_LEFT) . ':' . str_pad($diferencia_es->i, 2, 0, STR_PAD_LEFT);
                                            }
                                            else
                                            {
                                                if ($mostrar !== '1')
                                                {
                                                    $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                                }
                                                $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = "VER";
                                                $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                            }
                                        }
                                    }
                                    else if (!empty($tmp_fich[$str_fecha_ini . 'E']))
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PA';
                                        if ($mostrar === '2')
                                        {
                                            $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                        }
                                    }
                                    else if (!empty($tmp_fich[$str_fecha_ini . 'S']))
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AP';
                                        if ($mostrar === '2')
                                        {
                                            $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                        }
                                    }
                                    else
                                    {
                                        if ($Horario->{'hora_DiaSec' . $numero_dia . 'Ent'} !== '00:00' || $Horario->{'hora_DiaSec' . $numero_dia . 'Sal'} !== '00:00') //DIA CON E O S PARA EL EMPLEADO
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'A';
                                            if ($mostrar === '2')
                                            {
                                                $planilla_excel[$Horario->labo_Codigo]['dejar'] = TRUE;
                                            }
                                        }
                                        else
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                        }
                                    }
                                }
                                elseif (empty($planilla_excel[$Horario->labo_Codigo][$str_fecha_ini]))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                }
                            }

                            if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_feriados))
                            {
                                $col_dia = 8; //FERIADO ES 8 EN MAJOR
                            }
                            else
                            {
                                $col_dia = $fecha_inicial_while->format('w');
                                if ($col_dia === '0')
                                {
                                    $col_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                }
                            }
                            if ($col_dia == 6 || $col_dia == 7 || $col_dia == 8) //NO USAR ===
                            {
                                $fines_excel[$columna_excel] = array('columna' => $columna_excel);
                            }
                            $columna_excel++;

                            $fecha_inicial_while->add(new DateInterval('P1D'));
                        }
                    }

                    //FILTRAR CUALES EMPLEADOS MOSTRAR
                    if ($mostrar !== '1')
                    {
                        if (!empty($planilla_excel[$Horario->labo_Codigo]['dejar']) && $planilla_excel[$Horario->labo_Codigo]['dejar'])
                        {
                            $fila_excel++;
                            unset($planilla_excel[$Horario->labo_Codigo]['dejar']);
                        }
                        else
                        {
                            unset($planilla_excel[$Horario->labo_Codigo]);
                        }
                    }
                    else
                    {
                        $fila_excel++;
                    }
                }

                //ARMADO TITULO PLANILLA
                $tit_fecha = array();
                $inicio_inc = clone $inicio;
                while ($inicio_inc <= $fin)
                {
                    $str_fecha = date_format($inicio_inc, 'j');
                    $tit_fecha[] = $str_fecha;
                    $inicio_inc->add(new DateInterval('P1D'));
                }
                $tit_planilla = array($tit_fecha);

                //INICIO GENERACION EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $cant_filas = sizeof($planilla_excel) + 5;
                $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
                if (!$validLocale)
                {
                    lm('Unable to set locale to es - reverting to en_us');
                }
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Parte Diario Horas")
                        ->setDescription("Parte Diario de Horas del Personal ");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Parte Diario Horas");

                //OPCIONES DE IMPRESIÓN
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(1);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(1);
                $sheet->getPageSetup()->setFitToWidth(1);

                $sheet->getStyle("A5:{$columna_excel}5")->getFont()->setSize(10);
                $sheet->getStyle("A6:$columna_excel$cant_filas")->getFont()->setSize(8);
                $sheet->getStyle("E4:$columna_excel$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(25);

                $border_allborders_thin = array(
                    'borders' => array(
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                //TITULO TABLA
                $col = 'E';
                $col_anterior = 'D';
                for ($i = 1; $i <= sizeof($tit_fecha); $i++)
                {
                    $sheet->getColumnDimension($col)->setWidth(6);

                    $col_anterior = $col;
                    $col++;
                }
                $sheet->getStyle("A5:$col_anterior$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                $sheet->getStyle("A5:{$col_anterior}5")->getFont()->setBold(TRUE); //NEGRITA TITULO
                $sheet->getStyle("{$columna_excel}5:{$columna_excel}5")->getFont()->setBold(TRUE); //NEGRITA RESUMEN
                $sheet->fromArray(array(array('LEGAJO', 'APELLIDO Y NOMBRE', 'OFICINA', 'HORARIO')), NULL, 'A5');
                $sheet->getStyle("A5:D5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("{$columna_excel}5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->fromArray($tit_planilla, NULL, 'E5');
                $sheet->freezePane('E6');

                //TITULO PRINCIPAL
                $sheet->fromArray(array(array('PARTE DIARIO DE HORAS DEL PERSONAL')), NULL, 'A1');
                $sheet->mergeCells("A1:D1");
                $sheet->mergeCells("A2:D2");
                $sheet->getStyle('A1')->getFont()->setSize(18);
                $sheet->fromArray(array(array("SECRETARÍA: $array_secretaria[$ofi_Secretaria]")), NULL, 'A2');
                $sheet->fromArray(array(array("OFICINA: " . implode(', ', $oficinas_titulo))), NULL, 'A3');
                //$sheet->fromArray(array(array("MOSTRAR: " . $array_mostrar[$mostrar])), NULL, 'A4');
                $sheet->fromArray(array(array("DESDE: $desde - HASTA: $hasta")), NULL, 'A4');
                $sheet->mergeCells("A3:D3");
                $sheet->mergeCells("A4:D4");
                $sheet->mergeCells("A4:D4");
                $sheet->getStyle('A2')->getFont()->setSize(14);
                $sheet->getStyle('A3:A4')->getFont()->setSize(10);

                //DATOS TABLA
                $sheet->fromArray($planilla_excel, NULL, 'A6');

                //LETRA ROJA A GENTE QUE NO DEBE FICHAR
                if (!empty($noficha_excel))
                {
                    foreach ($noficha_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:$columna_excel$Fila")->applyFromArray(
                                array(
                                    'font' => array(
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //RELLENOS A FINES DE SEMANA Y FERIADOS
                if (!empty($fines_excel))
                {
                    foreach ($fines_excel as $Finde)
                    {
                        $sheet->getStyle($Finde['columna'] . "5:" . $Finde['columna'] . $cant_filas)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'D9D9D9') //ROJO PARA AUSENCIAS
                                    )
                                )
                        );
                    }
                }

                //RELLENOS A NOVEDADES
                if (!empty($rellenos_excel))
                {
                    foreach ($rellenos_excel as $Celda => $Relleno)
                    {
                        $sheet->getStyle($Celda)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => $Relleno)
                                    )
                                )
                        );
                    }
                }

                //NOMBRES RELOJES
                $sheet->setCellValue('A' . (string) ($cant_filas + 2), 'REFERENCIAS');
                $sheet->fromArray(array(array('A', 'SIN ENTRADA Y SIN SALIDA')), NULL, 'A' . (string) ($cant_filas + 3));
                $sheet->fromArray(array(array('P', 'CON ENTRADA Y SALIDA')), NULL, 'A' . (string) ($cant_filas + 4));
                $sheet->fromArray(array(array('PA', 'CON ENTRADA Y SIN SALIDA')), NULL, 'A' . (string) ($cant_filas + 5));
                $sheet->fromArray(array(array('AP', 'SIN ENTRADA Y CON SALIDA')), NULL, 'A' . (string) ($cant_filas + 6));
                $sheet->fromArray(array(array('VAC', 'LICENCIA ANUAL ORDINARIA-LEY 5811-CAP II')), NULL, 'A' . (string) ($cant_filas + 7));
                $sheet->fromArray(array(array('ENF', 'PARTE DE ENFERMO-LEY 5811-CAP III ART 40')), NULL, 'A' . (string) ($cant_filas + 8));
                $sheet->fromArray(array(array('ACC', 'ACC. DE TRABAJO/ENF.PROF-LEY5811 ART 37')), NULL, 'A' . (string) ($cant_filas + 9));
                $sheet->fromArray(array(array('MAT', 'LICENCIA POR MATERNIDAD-LEY 5811 ART54')), NULL, 'A' . (string) ($cant_filas + 10));
                $sheet->fromArray(array(array('LAC', 'LIC. LACTANCIA EXCLUSIVA-ORD. 8450-2008')), NULL, 'A' . (string) ($cant_filas + 11));
                $sheet->fromArray(array(array('PAT', 'LIC. NACIMIENTO HIJO-LEY 8687 ART54 BIS')), NULL, 'A' . (string) ($cant_filas + 12));
                $sheet->fromArray(array(array('PAR', 'TRAMITES PARTICULARES  50.C.9')), NULL, 'A' . (string) ($cant_filas + 13));
                $sheet->fromArray(array(array('JUN', 'JUNTA MEDICA')), NULL, 'A' . (string) ($cant_filas + 14));
                $sheet->fromArray(array(array('AUS', 'OTROS MOTIVOS DE AUSENCIA')), NULL, 'A' . (string) ($cant_filas + 15));
                $sheet->fromArray(array(array('VER', 'REVISAR MANUALMENTE (SALIDA ES MENOR A ENTRADA)')), NULL, 'A' . (string) ($cant_filas + 16));

                $nombreArchivo = 'parte_diario_horas_' . date('YmdHi');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            elseif (isset($personal['data']->error))
            {
                $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                redirect('asistencia/escritorio', 'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', '<br />No se encontraron personas en la oficina seleccionada');
                redirect('asistencia/reportes_major/parte_diario_impresion', 'refresh');
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['secretaria_sel'] = $this->form_validation->set_value('secretaria');

        $fake_model->fields['secretaria']['array'] = $array_secretaria;
        $fake_model->fields['oficina']['array'] = $array_oficina;
        $fake_model->fields['particion']['array'] = $array_particion;
        $fake_model->fields['mostrar']['array'] = $array_mostrar;

        //OPCIONES POR DEFECTO
        $default = new stdClass();
        $default->secretaria_id = NULL;
        $default->oficina_id = NULL;
        $default->particion_id = NULL;
        $default->mostrar_id = '2';
        $default->fecha = 'now';
        $default->desde = NULL;
        $default->hasta = NULL;

        $data['fields'] = $this->build_fields($fake_model->fields, $default);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Parte Diario de Horas del Personal';
        $data['title'] = TITLE . ' - Parte Diario de Horas';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/reportes_major/reportes_parte_diario', $data);
    }

    public function parte_estadistico()
    {
//		$this->benchmark->mark('all_start');
        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);
        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_secretaria_control = $array_secretaria;

        $array_oficina = array();
        $array_oficina_temp = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficina_temp[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_oficina_control = $array_oficina_temp;

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'secretaria' => array('label' => 'Secretaría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
//			$this->benchmark->mark('post_start');
            $ofi_Secretaria = $this->input->post('secretaria');
            $ofi_Oficina = $this->input->post('oficina');
            $desde = $this->input->post('desde');
            $hasta = $this->input->post('hasta');
            $desde_sql = DateTime::createFromFormat("d/m/Y", $desde)->format('Ymd');
            $hasta_sql = DateTime::createFromFormat("d/m/Y", $hasta)->format('Ymd');

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $desde_Sql, 'hasta' => $hasta_sql]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
            } catch (Exception $e)
            {
                $feriados = NULL;
            }

            $tmp_feriados = array();
            if (!empty($feriados))
            {
                foreach ($feriados as $Feriado)
                {
                    $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                }
            }

            //BUSCO HORARIOS DIARIOS
            try
            {
                $http_response_horarios = $guzzleHttp->request('GET', "personas/horarios_diarios_oficina", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $horarios_diarios = json_decode($http_response_horarios->getBody()->getContents());
            } catch (Exception $e)
            {
                $horarios_diarios = NULL;
            }

            $tmp_horarios_diarios = array();
            if (!empty($horarios_diarios))
            {
                foreach ($horarios_diarios as $Diario)
                {
                    $tmp_horarios_diarios[$Diario->labo_Codigo][(new DateTime($Diario->hodi_Fecha))->format('dmY')] = array('E' => $Diario->hodi_Entrada, 'S' => $Diario->hodi_Salida);
                }
            }

            //BUSCA TODOS LOS EMPLEADOS DE LAS OFICINAS SELECCIONADAS Y DATOS ASOCIADOS(NOVEDADES, FICHADAS, NOVEDADES DE FICHADAS Y AUSENCIAS)
            try
            {
                $http_response_personal = $guzzleHttp->request('GET', "personas/parte_estadistico", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde, 'hasta' => $hasta]]);
                $personal = json_decode($http_response_personal->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal = NULL;
            }

            if (!empty($personal))
            {
                //RANGO DE FECHAS A TRABAJAR
                $inicio = DateTime::createFromFormat("d/m/Y H:i:s", $desde . ' 00:00:00');
                $fin = DateTime::createFromFormat("d/m/Y H:i:s", $hasta . ' 00:00:00');
                $fin->add(new DateInterval('P1D'));

                //FILA DONDE COMIENZAN LOS DATOS
                $fila_excel = 7;

                //CARGA LOS DATOS NECESARIOS PARA EXCEL
                $planilla_excel = array();
                $oficinas_titulo = array();
                $noficha_excel = array();
                $error_horario_excel = array();
                $error_secuencia_excel = array();

                foreach ($personal as $Emp)
                {
                    //EVITA ERRORES SI EL EMPLEADO ESTA 2 VECES (POR HORARIOS REPETIDOS GENERALMENTE)
                    if (!empty($planilla_excel[$Emp->datos->labo_Codigo]))
                    {
                        continue;
                    }

                    //LISTADO DE PERSONAL PARA EXCEL
                    $planilla_excel[$Emp->datos->labo_Codigo] = array('labo_Codigo' => $Emp->datos->labo_Codigo, 'nombre' => $Emp->datos->nombre, 'oficina' => $Emp->datos->oficina, 'novedades' => ' ', 'horario' => $Emp->datos->horario, 'ficha' => $Emp->datos->hoca_Ficha, 'D' => 0, 'DL' => 0, 'E' => 0, 'EP' => 0, 'S' => 0, 'SP' => 0, 'F' => 0, 'FP' => 0, 'J' => 0, 'I' => 0, 'A' => 0, 'AP' => 0, 'FA' => 0, 'FAP' => 0);

                    //FILAS EN ROJO PARA PERSONAL QUE NO FICHA
                    if ($Emp->datos->hoca_Ficha === 'N')
                    {
                        $noficha_excel[$fila_excel] = 'FF0000';
                    }

                    //GUARDA NOMBRE DE OFICINA PARA TITULO
                    $oficinas_titulo[$Emp->datos->oficina] = $Emp->datos->oficina;

                    //NOVEDADES
                    if (!empty($Emp->novedades))
                    {
                        foreach ($Emp->novedades as $Nov)
                        {
                            if (empty($planilla_excel[$Emp->datos->labo_Codigo]['novedades']) || $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] === ' ')
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= $Nov->vava_Descripcion;
                            }
                            else
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= " | $Nov->vava_Descripcion";
                            }
                        }
                    }

                    //FICHADAS
                    $tmp_fich = array();
                    if (!empty($Emp->fichadas))
                    {
                        foreach ($Emp->fichadas as $Fich)
                        {
                            if (!isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)]))
                            {
                                $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                                $planilla_excel[$Emp->datos->labo_Codigo][trim($Fich->fich_Codigo)]++;
                                $planilla_excel[$Emp->datos->labo_Codigo]['F']++;
                            }
                        }
                    }

                    //AUSENCIAS
                    if (!empty($Emp->ausencias))
                    {
                        foreach ($Emp->ausencias as $Aus)
                        {
                            $fecha_ini_aus = new DateTime($Aus->ause_FechaInicio);
                            if ($fecha_ini_aus < $inicio)
                            {
                                $fecha_ini_aus = clone $inicio;
                            }
                            $fecha_fin_aus = new DateTime($Aus->ause_FechaFin);
                            if ($fecha_fin_aus > $fin)
                            {
                                $fecha_fin_aus = clone $fin;
                            }
                            do
                            {
                                //VERIFICA NUMERO DE DIA
                                $error_secuencia = FALSE;
                                switch ($Emp->datos->hora_Tipo)
                                {
                                    case 'N':
                                    case 'F':
                                        if (array_key_exists($fecha_ini_aus->format('dmY'), $tmp_feriados))
                                        {
                                            $numero_dia = 8; //FERIADO ES 8 EN MAJOR
                                        }
                                        else
                                        {
                                            $numero_dia = $fecha_ini_aus->format('w');
                                            if ($numero_dia === '0')
                                            {
                                                $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                            }
                                        }
                                        break;
                                    case 'R':
                                        $inicio_sec = new DateTime($Emp->datos->hoca_FechaSecuencia1);
                                        if ($fecha_ini_aus < $inicio_sec)
                                        {
                                            $error_secuencia = TRUE;
                                        }
                                        else
                                        {
                                            $dias_inicio_sec = $fecha_ini_aus->diff($inicio_sec)->format("%a");
                                            $dias_inicio_sec++; //SI ES EL PRIMER DÍA DEVUELVE 0 Y DEBERIA SER 1
                                            $sum_dias_sec = 0;
                                            for ($i = 1; $i <= 20; $i++)
                                            {
                                                $sum_dias_sec += (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"};
                                            }

                                            $resto_sec = $dias_inicio_sec % $sum_dias_sec;
                                            if ($resto_sec === 0)
                                            {
                                                $resto_sec = $sum_dias_sec;
                                            }
                                            for ($i = 1; $i <= 20; $i++)
                                            {
                                                if ($resto_sec <= (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"})
                                                {
                                                    $numero_dia = $i;
                                                    break;
                                                }
                                                else
                                                {
                                                    $resto_sec -= (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"};
                                                }
                                            }
                                        }
                                        break;
                                }
                                if ($Emp->datos->{'hora_DiaSec' . $numero_dia . 'Ent'} !== '00:00' || $Emp->datos->{'hora_DiaSec' . $numero_dia . 'Sal'} !== '00:00') //DIA CON E O S PARA EL EMPLEADO
                                {
                                    $planilla_excel[$Emp->datos->labo_Codigo]['J']++; //TODO SEPARAR INJUSTIFICADAS
                                    $planilla_excel[$Emp->datos->labo_Codigo]['A']++;
                                }
                                $fecha_ini_aus->add(new DateInterval('P1D'));
                            } while ($fecha_ini_aus < $fecha_fin_aus);
                        }
                    }

                    //RECORRE TODO EL RANGO DE FECHAS PARA ARMAR $planilla_arr
                    $inicio_inc = clone $inicio;
                    while ($inicio_inc < $fin)
                    {
                        if (!empty($tmp_horarios_diarios[$Emp->datos->labo_Codigo]) && array_key_exists($inicio_inc->format('dmY'), $tmp_horarios_diarios[$Emp->datos->labo_Codigo]))
                        {
                            $horario_diario = $tmp_horarios_diarios[$Emp->datos->labo_Codigo][$inicio_inc->format('dmY')];

                            if ($horario_diario['E'] !== '00:00' || $horario_diario['S'] !== '00:00')
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['DL']++;
                            }
                        }
                        else
                        {
                            //VERIFICA NUMERO DE DIA
                            $error_secuencia = FALSE;
                            $error_horario = FALSE;
                            if (!empty($Emp->datos->hora_Codigo))
                            {
                                switch ($Emp->datos->hora_Tipo)
                                {
                                    case 'N':
                                    case 'F':
                                        if (array_key_exists($inicio_inc->format('dmY'), $tmp_feriados))
                                        {
                                            $numero_dia = 8; //FERIADO ES 8 EN MAJOR
                                        }
                                        else
                                        {
                                            $numero_dia = $inicio_inc->format('w');
                                            if ($numero_dia === '0')
                                            {
                                                $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                            }
                                        }
                                        break;
                                    case 'R':
                                        $inicio_sec = new DateTime($Emp->datos->hoca_FechaSecuencia1);
                                        if ($inicio_inc < $inicio_sec)
                                        {
                                            $numero_dia = 1;
                                            $error_secuencia = TRUE;
                                        }
                                        else
                                        {
                                            $dias_inicio_sec = $inicio_inc->diff($inicio_sec)->format("%a");
                                            $dias_inicio_sec++; //SI ES EL PRIMER DÍA DEVUELVE 0 Y DEBERIA SER 1
                                            $sum_dias_sec = 0;
                                            for ($i = 1; $i <= 20; $i++)
                                            {
                                                $sum_dias_sec += (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"};
                                            }

                                            $resto_sec = $dias_inicio_sec % $sum_dias_sec;
                                            if ($resto_sec === 0)
                                            {
                                                $resto_sec = $sum_dias_sec;
                                            }
                                            for ($i = 1; $i <= 20; $i++)
                                            {
                                                if ($resto_sec <= (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"})
                                                {
                                                    $numero_dia = $i;
                                                    break;
                                                }
                                                else
                                                {
                                                    $resto_sec -= (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"};
                                                }
                                            }
                                        }
                                        break;
                                }
                            }
                            else
                            {
                                $error_horario = TRUE;
                                $numero_dia = 1;
                            }

                            if ($Emp->datos->{'hora_DiaSec' . $numero_dia . 'Ent'} !== '00:00' || $Emp->datos->{'hora_DiaSec' . $numero_dia . 'Sal'} !== '00:00') //DIA CON E O S PARA EL EMPLEADO
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['DL']++;
                            }

                            if ($error_horario) //FILAS EN NARANJA PARA ERRORES DE HORARIO
                            {
                                $error_horario_excel[$fila_excel] = 'E26B0A';
                            }

                            if ($error_secuencia) //FILAS EN AMARILLO PARA ERRORES DE SECUENCIA
                            {
                                $error_secuencia_excel[$fila_excel] = 'E7E20A';
                            }
                        }
                        $planilla_excel[$Emp->datos->labo_Codigo]['D']++; // CONTADOR DÍAS CORRIDOS
                        $inicio_inc->add(new DateInterval('P1D'));
                    }

                    //CALCULO PORCENTAJES
                    $planilla_excel[$Emp->datos->labo_Codigo]['EP'] = "=I$fila_excel/H$fila_excel";
                    $planilla_excel[$Emp->datos->labo_Codigo]['SP'] = "=K$fila_excel/H$fila_excel";
                    $planilla_excel[$Emp->datos->labo_Codigo]['FP'] = "=M$fila_excel/(H$fila_excel*2)";
                    $planilla_excel[$Emp->datos->labo_Codigo]['AP'] = "=Q$fila_excel/H$fila_excel";
                    $planilla_excel[$Emp->datos->labo_Codigo]['FA'] = "=(M$fila_excel/2)+O$fila_excel";
                    $planilla_excel[$Emp->datos->labo_Codigo]['FAP'] = "=S$fila_excel/H$fila_excel";

                    $fila_excel++;
                }

                //INICIO GENERACION EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $cant_filas = sizeof($planilla_excel) + 9; //6 DE ENCABEZADOS Y 3 DE TOTALES
                $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
                if (!$validLocale)
                {
                    lm('Unable to set locale to es - reverting to en_us');
                }
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Parte Estadístico")
                        ->setDescription("Parte Estadístico del Personal");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Parte Estadístico");

                //OPCIONES DE IMPRESIÓN
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(1);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(1);
                $sheet->getPageSetup()->setFitToWidth(1);

                $sheet->getStyle("A5:R6")->getFont()->setSize(10);
                $sheet->getStyle("A7:T$cant_filas")->getFont()->setSize(8);
                $sheet->getStyle("F4:T$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);
                $sheet->getColumnDimension('F')->setWidth(11);
                $sheet->getColumnDimension('G')->setWidth(7);
                $sheet->getColumnDimension('H')->setWidth(7);
                $sheet->getColumnDimension('I')->setWidth(7);
                $sheet->getColumnDimension('J')->setWidth(7);
                $sheet->getColumnDimension('K')->setWidth(7);
                $sheet->getColumnDimension('L')->setWidth(7);
                $sheet->getColumnDimension('M')->setWidth(7);
                $sheet->getColumnDimension('N')->setWidth(7);
                $sheet->getColumnDimension('O')->setWidth(7);
                $sheet->getColumnDimension('P')->setWidth(7);
                $sheet->getColumnDimension('Q')->setWidth(7);
                $sheet->getColumnDimension('R')->setWidth(7);
                $sheet->getColumnDimension('S')->setWidth(7);
                $sheet->getColumnDimension('T')->setWidth(7);

                $border_allborders_thin = array(
                    'borders' => array(
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                $sheet->getStyle("A5:T$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                $sheet->getStyle("A5:T6")->getFont()->setBold(TRUE); //NEGRITA TITULO
                //TITULOS COLUMNAS
                $sheet->fromArray(array(array('DÍAS', '', 'FICHADAS', '', '', '', '', '', 'AUSENCIAS*', '', '', '', 'FICH + AUS J')), NULL, 'G5');
                $sheet->mergeCells("G5:H5"); //DÍAS
                $sheet->mergeCells("I5:N5"); //FICHADAS
                $sheet->mergeCells("O5:R5"); //AUSENCIAS
                $sheet->mergeCells("S5:T5"); //TOTAL
                $sheet->getStyle("A5:F5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->freezePane('F7');
                $sheet->fromArray(array(array('LEGAJO', 'APELLIDO Y NOMBRE', 'OFICINA', 'ADICIONALES', 'HORARIO', 'DEBE FICHAR', 'CORR', 'LABOR', 'E*', 'E %', 'S*', 'S %', 'TOT', 'TOT %', 'J**', 'I', 'TOT', 'TOT %', 'TOT', 'TOT %')), NULL, 'A6');
                $sheet->setAutoFilter('A6:T6');

                //TITULO PRINCIPAL
                $sheet->fromArray(array(array('PARTE ESTADÍSTICO DEL PERSONAL')), NULL, 'A1');
                $sheet->mergeCells("A1:E1");
                $sheet->mergeCells("A2:E2");
                $sheet->getStyle('A1')->getFont()->setSize(18);
                $sheet->fromArray(array(array("SECRETARÍA: $array_secretaria[$ofi_Secretaria]")), NULL, 'A2');
                $sheet->fromArray(array(array("OFICINA: " . implode(', ', $oficinas_titulo))), NULL, 'A3');
                $inicio_excel = DateTime::createFromFormat("d/m/Y H:i:s", $desde . ' 00:00:00');
                $fin_excel = DateTime::createFromFormat("d/m/Y H:i:s", $hasta . ' 00:00:00');
                $sheet->fromArray(array(array("DESDE: " . $this->_getDayName($inicio_excel) . " $desde HASTA: " . $this->_getDayName($fin_excel) . " $hasta")), NULL, 'A4');
                $sheet->mergeCells("A3:E3");
                $sheet->mergeCells("A4:E4");
                $sheet->getStyle('A2')->getFont()->setSize(14);
                $sheet->getStyle('A3:A4')->getFont()->setSize(10);

                //DATOS TABLA
                $sheet->fromArray($planilla_excel, NULL, 'A7', TRUE);

                //ACLARACIONES
                $sheet->setCellValue('G1', 'ACLARACIONES FUNCIONAMIENTO ACTUAL');
                $sheet->setCellValue('G2', '* FICHADAS E y S: Trae sólo una E y una S por día, no tiene en cuenta si hace horario partido, siempre limita a 1 por día.');
                $sheet->setCellValue('G3', '** AUSENCIAS J: Actualmente todas las ausencias cargadas están siendo tomadas como justificadas.');
                $sheet->getStyle('G1:G4')->applyFromArray(array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('rgb' => 'FF0000'),
                )));
                $sheet->getStyle('G1:G4')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT));

                //TOTALES
                $sheet->setCellValue("C" . ($cant_filas - 1), "SUBTOTAL DEBE FICHAR = S");
                $sheet->getStyle("C" . ($cant_filas - 1) . ":T" . ($cant_filas - 1))->getFont()->setSize(12);
                $sheet->getStyle("C" . ($cant_filas - 1))->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getStyle("C" . ($cant_filas - 1) . ":T" . ($cant_filas - 1))->getFont()->setBold(TRUE);
                $sheet->setCellValue("G" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",G7:G' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("H" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",H7:H' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("I" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",I7:I' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("J" . ($cant_filas - 1), '=I' . ($cant_filas - 1) . '/H' . ($cant_filas - 1));
                $sheet->setCellValue("K" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",K7:K' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("L" . ($cant_filas - 1), '=K' . ($cant_filas - 1) . '/H' . ($cant_filas - 1));
                $sheet->setCellValue("M" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",M7:M' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("N" . ($cant_filas - 1), '=M' . ($cant_filas - 1) . '/(H' . ($cant_filas - 1) . '*2)');
                $sheet->setCellValue("O" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",O7:O' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("P" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",P7:P' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("Q" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",Q7:Q' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("R" . ($cant_filas - 1), '=Q' . ($cant_filas - 1) . '/H' . ($cant_filas - 1));
                $sheet->setCellValue("S" . ($cant_filas - 1), '=SUMIF(F7:F' . ($cant_filas - 2) . ',"S",S7:S' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("T" . ($cant_filas - 1), '=S' . ($cant_filas - 1) . '/H' . ($cant_filas - 1));

                $sheet->setCellValue("C" . ($cant_filas), "TOTAL GENERAL");
                $sheet->getStyle("C" . ($cant_filas) . ":T" . ($cant_filas))->getFont()->setSize(12);
                $sheet->getStyle("C" . ($cant_filas))->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getStyle("C" . ($cant_filas) . ":T" . ($cant_filas))->getFont()->setBold(TRUE);
                $sheet->setCellValue("G" . ($cant_filas), '=SUM(G7:G' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("H" . ($cant_filas), '=SUM(H7:H' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("I" . ($cant_filas), '=SUM(I7:I' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("J" . ($cant_filas), '=I' . ($cant_filas) . '/H' . ($cant_filas));
                $sheet->setCellValue("K" . ($cant_filas), '=SUM(K7:K' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("L" . ($cant_filas), '=K' . ($cant_filas) . '/H' . ($cant_filas));
                $sheet->setCellValue("M" . ($cant_filas), '=SUM(M7:M' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("N" . ($cant_filas), '=M' . ($cant_filas) . '/(H' . ($cant_filas) . '*2)');
                $sheet->setCellValue("O" . ($cant_filas), '=SUM(O7:O' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("P" . ($cant_filas), '=SUM(P7:P' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("Q" . ($cant_filas), '=SUM(Q7:Q' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("R" . ($cant_filas), '=Q' . ($cant_filas) . '/H' . ($cant_filas));
                $sheet->setCellValue("S" . ($cant_filas), '=SUM(S7:S' . ($cant_filas - 2) . ')');
                $sheet->setCellValue("T" . ($cant_filas), '=S' . ($cant_filas) . '/H' . ($cant_filas));

                //FORMATOS COLUMNAS
                $sheet->getStyle("J5:J$cant_filas")->getNumberFormat()->applyFromArray(
                        array(
                            'code' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                        )
                );
                $sheet->getStyle("L5:L$cant_filas")->getNumberFormat()->applyFromArray(
                        array(
                            'code' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                        )
                );
                $sheet->getStyle("N5:N$cant_filas")->getNumberFormat()->applyFromArray(
                        array(
                            'code' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                        )
                );
                $sheet->getStyle("R5:R$cant_filas")->getNumberFormat()->applyFromArray(
                        array(
                            'code' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                        )
                );
                $sheet->getStyle("T5:T$cant_filas")->getNumberFormat()->applyFromArray(
                        array(
                            'code' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE
                        )
                );
//				
                //RELLENOS
//				$objConditionalStyle = new \PhpOffice\PhpSpreadsheet\Style\Conditional();
//				$objConditionalStyle->setConditionType(\PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_CELLIS)
//					->setOperatorType(\PhpOffice\PhpSpreadsheet\Style\Conditional::OPERATOR_BETWEEN)
//					->setConditions(array('0', '50'));
//				$objConditionalStyle->getStyle()->applyFromArray(
//					array(
//						'font' => array(
//							'bold'  => true,
//							'color' => array('rgb' => 'FF0000'),
//							'size' => 8,
//						)
//				));
//				$conditionalStyles = $sheet->getStyle("J7")->getConditionalStyles();
//				array_push($conditionalStyles, $objConditionalStyle);
//				$sheet->duplicateConditionalStyle(array($objConditionalStyle), "J7:J$cant_filas");
                //LETRA ROJA A GENTE QUE NO DEBE FICHAR
                if (!empty($noficha_excel))
                {
                    foreach ($noficha_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:T$Fila")->applyFromArray(
                                array(
                                    'font' => array(
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //RELLENO A GENTE CON ERROR DE HORARIO
                if (!empty($error_horario_excel))
                {
                    foreach ($error_horario_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:T$Fila")->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //RELLENO A GENTE CON ERROR DE SECUENCIA
                if (!empty($error_secuencia_excel))
                {
                    foreach ($error_secuencia_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:T$Fila")->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //REFERENCIAS
                $sheet->setCellValue('A' . (string) ($cant_filas + 2), 'REFERENCIAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 3))->applyFromArray(
                        array(
                            'font' => array(
                                'color' => array('rgb' => 'FF0000')
                            )
                        )
                );
                $sheet->setCellValue('A' . (string) ($cant_filas + 3), 'ROJO');
                $sheet->setCellValue('B' . (string) ($cant_filas + 3), 'PERSONAL QUE NO ES NECESARIO QUE FICHE');
                $sheet->getStyle('A' . (string) ($cant_filas + 4))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'E26B0A')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 4), 'PERSONAL CON ERROR EN EL HORARIO');
                $sheet->getStyle('A' . (string) ($cant_filas + 5))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'E7E20A')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 5), 'PERSONAL CON ERROR EN EL INICIO DE SECUENCIA O COMIENZO DE SECUENCIA POSTERIOR');

                $nombreArchivo = 'parte_estadistico_' . date('YmdHi');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            elseif (isset($personal['data']->error))
            {
                $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                redirect('asistencia/escritorio', 'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', '<br />No se encontraron personas en la oficina seleccionada');
                redirect('asistencia/reportes_major/parte_estadistico', 'refresh');
            }
//			$this->benchmark->mark('post_end');
//			lm('POST: ' . $this->benchmark->elapsed_time('post_start', 'post_end'));
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['secretaria_sel'] = $this->form_validation->set_value('secretaria');

        $fake_model->fields['oficina']['array'] = $array_oficina;
        $fake_model->fields['secretaria']['array'] = $array_secretaria;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Parte de Estadístico del Personal';
        $data['title'] = TITLE . ' - Parte de Estadístico del Personal';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/reportes_major/reportes_parte_novedades', $data);
//		$this->benchmark->mark('all_end');
//		lm('ALL: ' . $this->benchmark->elapsed_time('all_start', 'all_end'));
    }

    public function reporte_diario()
    {
//		$this->benchmark->mark('all_start');
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);
        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_secretaria_control = $array_secretaria;

        $array_oficina = array();
        $array_oficina_temp = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficina_temp[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_oficina_control = $array_oficina_temp;

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'secretaria' => array('label' => 'Secretaría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
//			$this->benchmark->mark('post_start');
            $ofi_Secretaria = $this->input->post('secretaria');
            $ofi_Oficina = $this->input->post('oficina');
            $fecha = $this->input->post('fecha');

            //BUSCA TODOS LOS EMPLEADOS DE LAS OFICINAS SELECCIONADAS Y DATOS ASOCIADOS(NOVEDADES, FICHADAS, NOVEDADES DE FICHADAS Y AUSENCIAS)
            try
            {
                $http_response_personal = $guzzleHttp->request('GET', "personas/reporte_diario", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'fecha' => $fecha]]);
                $personal = json_decode($http_response_personal->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal = NULL;
            }

            if (!empty($personal))
            {
                //RANGO DE FECHAS A TRABAJAR
                $inicio = DateTime::createFromFormat("d/m/Y H:i:s", $fecha . ' 00:00:00');
                $fin = DateTime::createFromFormat("d/m/Y H:i:s", $fecha . ' 00:00:00');
                $fin->add(new DateInterval('P1D'));
                $desde_sql = DateTime::createFromFormat("d/m/Y", $fecha)->format('Ymd');
                $hasta_sql = DateTime::createFromFormat("d/m/Y", $fecha)->format('Ymd');

                //BUSCO FERIADOS
                try
                {
                    $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                    $feriados = json_decode($http_response_feriados->getBody()->getContents());
                } catch (Exception $e)
                {
                    $feriados = NULL;
                }

                $tmp_feriados = array();
                $es_feriado = FALSE;
                if (!empty($feriados))
                {
                    $es_feriado = TRUE; //NO HACE FALTA CONTROLAR QUE DÍA PORQUE ES UNO SOLO
                    foreach ($feriados as $Feriado)
                    {
                        $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                    }
                }

                //BUSCO HORARIOS DIARIOS
                try
                {
                    $http_response_horarios = $guzzleHttp->request('GET', "personas/horarios_diarios_oficina", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                    $horarios_diarios = json_decode($http_response_horarios->getBody()->getContents());
                } catch (Exception $e)
                {
                    $horarios_diarios = NULL;
                }

                $tmp_horarios_diarios = array();
                if (!empty($horarios_diarios))
                {
                    foreach ($horarios_diarios as $Diario)
                    {
                        $tmp_horarios_diarios[$Diario->labo_Codigo][(new DateTime($Diario->hodi_Fecha))->format('dmY')] = array('E' => $Diario->hodi_Entrada, 'S' => $Diario->hodi_Salida);
                    }
                }

                //CONTADORES PARA RESUMEN AUSENCIAS
                $cont_aus = array();

                //FILA DONDE COMIENZAN LOS DATOS
                $fila_excel = 7;

                //CARGA LOS DATOS NECESARIOS PARA EXCEL
                $planilla_excel = array();
                $ausentes_excel = array();
                $rellenos_excel = array();
                $oficinas_titulo = array();

                //MUESTRA TODAS LAS OFICINAS SELECCIONADAS
                foreach ($ofi_Oficina as $Oficina_sel)
                {
                    $oficinas_titulo[$array_oficina_temp[$Oficina_sel]] = $array_oficina_temp[$Oficina_sel];
                }

                foreach ($personal as $Emp)
                {
                    //EVITA ERRORES SI EL EMPLEADO ESTA 2 VECES (POR HORARIOS REPETIDOS GENERALMENTE)
                    if (!empty($planilla_excel[$Emp->datos->labo_Codigo]))
                    {
                        continue;
                    }

                    //COLUMNA DONDE COMIENZAN LOS DATOS
                    $columna_excel = 'F';

                    if (!empty($tmp_horarios_diarios[$Emp->datos->labo_Codigo]) && array_key_exists($inicio->format('dmY'), $tmp_horarios_diarios[$Emp->datos->labo_Codigo]))
                    {
                        $horario_diario = $tmp_horarios_diarios[$Emp->datos->labo_Codigo][$inicio->format('dmY')];

                        if ($horario_diario['E'] === '00:00' && $horario_diario['S'] === '00:00')
                        {
                            continue; //NO CORRESPONDE TRABAJAR ESE DIA (NO AGREGO EL EMPLEADO AL REPORTE)
                        }
                    }
                    else
                    {
                        //VERIFICA NUMERO DE DIA
                        $error_secuencia = FALSE;
                        switch ($Emp->datos->hora_Tipo)
                        {
                            case 'N':
                            case 'F':
                                if ($es_feriado)
                                {
                                    $numero_dia = 8; //FERIADO ES 8 EN MAJOR
                                }
                                else
                                {
                                    $numero_dia = $inicio->format('w');
                                    if ($numero_dia === '0')
                                    {
                                        $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                    }
                                }
                                break;
                            case 'R':
                                $inicio_sec = new DateTime($Emp->datos->hoca_FechaSecuencia1);
                                if ($inicio < $inicio_sec)
                                {
                                    $error_secuencia = TRUE;
                                }
                                else
                                {
                                    $dias_inicio_sec = $inicio->diff($inicio_sec)->format("%a");
                                    $dias_inicio_sec++; //SI ES EL PRIMER DÍA DEVUELVE 0 Y DEBERIA SER 1
                                    $sum_dias_sec = 0;
                                    for ($i = 1; $i <= 20; $i++)
                                    {
                                        $sum_dias_sec += (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"};
                                    }

                                    $resto_sec = $dias_inicio_sec % $sum_dias_sec;
                                    if ($resto_sec === 0)
                                    {
                                        $resto_sec = $sum_dias_sec;
                                    }
                                    for ($i = 1; $i <= 20; $i++)
                                    {
                                        if ($resto_sec <= (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"})
                                        {
                                            $numero_dia = $i;
                                            break;
                                        }
                                        else
                                        {
                                            $resto_sec -= (int) $Emp->datos->{"hora_DiaSec" . $i . "Cant"};
                                        }
                                    }
                                }
                                break;
                        }

                        //DIA SIN ENTRADA O SALIDA PARA EL EMPLEADO
                        if ($Emp->datos->{'hora_DiaSec' . $numero_dia . 'Ent'} === '00:00' && $Emp->datos->{'hora_DiaSec' . $numero_dia . 'Sal'} === '00:00')
                        {
                            continue; //NO CORRESPONDE TRABAJAR ESE DIA (NO AGREGO EL EMPLEADO AL REPORTE)
                        }
                    }

                    //CONTROLA ENTRADA Y SALIDA
                    $entrada = 'N';
                    $salida = 'N';
                    $cumple_horas = 'N';
                    $novedades_calculadas = array();
                    $horario_entrada = DateTime::createFromFormat("d/m/Y H:i:s", $fecha . ' ' . $Emp->datos->{'hora_DiaSec' . $numero_dia . 'Ent'} . ':00');
                    $horario_salida = DateTime::createFromFormat("d/m/Y H:i:s", $fecha . ' ' . $Emp->datos->{'hora_DiaSec' . $numero_dia . 'Sal'} . ':00');
                    $fichada_entrada = NULL;
                    $fichada_salida = NULL;

                    //FICHADAS
                    $tmp_fich = array();
                    $novedad_temp = new stdClass(); // PARA SEGUNDAS SALIDAS
                    if (!empty($Emp->fichadas))
                    {
                        foreach ($Emp->fichadas as $Fich)
                        {
                            if (isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)]))
                            {
                                if (trim($Fich->fich_Codigo) === 'E')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') >= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA ENTRADA DESPUES DE 5 MINUTOS DE LA PRIMERA
                                    }
                                }
                                elseif (trim($Fich->fich_Codigo) === 'S')
                                {
                                    if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') <= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
                                    }
                                    else
                                    {
                                        $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA SALIDA DESPUES DE 5 MINUTOS DE LA SEGUNDA
                                    }

                                    // PARA SEGUNDAS SALIDAS
                                    $hora_fichada = new DateTime($Fich->fich_FechaHora);
                                    if ($Emp->datos->hora_Tipo !== 'F')
                                    {
                                        $diferencia_fichada = $horario_salida->diff($hora_fichada);
                                        $minutos_diferencia = $diferencia_fichada->format("%r%i") + ($diferencia_fichada->format("%r%h") * 60);
                                        if ($minutos_diferencia < -$Emp->datos->hora_ToleranciaSal)
                                        {
                                            $salida = 'A';
                                            $novedad_temp = new stdClass();
                                            $novedad_temp->labo_Codigo = $Emp->datos->labo_Codigo;
                                            $novedad_temp->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                                            $novedad_temp->nofi_Valor = abs($minutos_diferencia) / 60;
                                            $novedad_temp->tnof_Codigo = 2;
                                            $novedad_temp->tnof_Descripcion = 'SALIDA ANTICIPADA';
                                        }
                                        else
                                        {
                                            $salida = 'S';
                                        }
                                    }
                                    else
                                    {
                                        //TODO: CONTROLAR SI LA SALIDA ESTA DENTRO DEL RANGO ?
                                        $salida = 'S';
                                        if (empty($fichada_salida))
                                        {
                                            $fichada_salida = $hora_fichada;
                                        }
                                    }
                                }
                            }
                            else
                            {
                                $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';

                                $hora_fichada = new DateTime($Fich->fich_FechaHora);
                                switch (trim($Fich->fich_Codigo))
                                {
                                    case 'E':
                                        if ($Emp->datos->hora_Tipo !== 'F')
                                        {
                                            $diferencia_fichada = $horario_entrada->diff($hora_fichada);
                                            $minutos_diferencia = $diferencia_fichada->format("%r%i") + ($diferencia_fichada->format("%r%h") * 60);
                                            if ($minutos_diferencia > $Emp->datos->hora_ToleranciaEnt)
                                            {
                                                if ($entrada !== 'T' && $entrada !== 'S') //PARA EVITAR MULTIPLES NOVEDADES DE TARDANZA
                                                {
                                                    $entrada = 'T';
                                                    $novedad = new stdClass();
                                                    $novedad->labo_Codigo = $Emp->datos->labo_Codigo;
                                                    $novedad->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                                                    $novedad->nofi_Valor = abs($minutos_diferencia) / 60;
                                                    $novedad->tnof_Codigo = 1;
                                                    $novedad->tnof_Descripcion = 'LLEGADA TARDE';
                                                    $novedades_calculadas[] = $novedad;
                                                }
                                            }
                                            else
                                            {
                                                $entrada = 'S';
                                            }
                                        }
                                        else
                                        {
                                            //TODO: CONTROLAR SI LA ENTRADA ESTA DENTRO DEL RANGO ?
                                            $entrada = 'S';
                                            if (empty($fichada_entrada))
                                            {
                                                $fichada_entrada = $hora_fichada;
                                            }
                                        }
                                        break;
                                    case 'S':
                                        if ($Emp->datos->hora_Tipo !== 'F')
                                        {
                                            $diferencia_fichada = $horario_salida->diff($hora_fichada);
                                            $minutos_diferencia = $diferencia_fichada->format("%r%i") + ($diferencia_fichada->format("%r%h") * 60);
                                            if ($minutos_diferencia < -$Emp->datos->hora_ToleranciaSal)
                                            {
                                                if ($salida !== 'A') //PARA EVITAR MULTIPLES NOVEDADES DE ANTICIPADA
                                                {
                                                    $salida = 'A';
                                                    $novedad_temp = new stdClass();
                                                    $novedad_temp->labo_Codigo = $Emp->datos->labo_Codigo;
                                                    $novedad_temp->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                                                    $novedad_temp->nofi_Valor = abs($minutos_diferencia) / 60;
                                                    $novedad_temp->tnof_Codigo = 2;
                                                    $novedad_temp->tnof_Descripcion = 'SALIDA ANTICIPADA';
                                                }
                                            }
                                            else
                                            {
                                                $salida = 'S';
                                            }
                                        }
                                        else
                                        {
                                            //TODO: CONTROLAR SI LA SALIDA ESTA DENTRO DEL RANGO ?
                                            $salida = 'S';
                                            if (empty($fichada_salida))
                                            {
                                                $fichada_salida = $hora_fichada;
                                            }
                                        }
                                        break;
                                }
                            }
                        }
                        if ($salida === 'A')
                        {
                            $novedades_calculadas[] = $novedad_temp;
                        }
                    }

                    //AUSENCIAS
                    if (!empty($Emp->ausencias))
                    {
                        foreach ($Emp->ausencias as $Aus)
                        {
                            $fecha_ini_aus = new DateTime($Aus->ause_FechaInicio);
                            if ($fecha_ini_aus < $inicio)
                            {
                                $fecha_ini_aus = clone $inicio;
                            }
                            $fecha_fin_aus = new DateTime($Aus->ause_FechaFin);
                            if ($fecha_fin_aus > $fin)
                            {
                                $fecha_fin_aus = clone $fin;
                            }

                            do
                            {
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'E'] = $Aus->moau_Descripcion;
                                $tmp_fich[date_format($fecha_ini_aus, 'mj') . 'S'] = '\'';
                                $fecha_ini_aus->add(new DateInterval('P1D'));
                            } while ($fecha_ini_aus <= $fecha_fin_aus);
                        }
                    }

                    if (!$error_secuencia)
                    {
                        if ($Emp->datos->hora_Tipo !== 'F')
                        {
                            $horas_realizadas = $horas_dia = $horario_entrada->diff($horario_salida)->format("%r%h") + ($horario_entrada->diff($horario_salida)->format("%r%i") / 60);
                        }
                        else
                        {
                            $horas_dia = (int) $Emp->datos->{'hora_DiaSec' . $numero_dia . 'Cant'};
                            if (!empty($fichada_entrada) && !empty($fichada_salida))
                            {
                                $horas_realizadas = $fichada_entrada->diff($fichada_salida)->format("%r%h") + ($fichada_entrada->diff($fichada_salida)->format("%r%i") / 60);
                            }
                            else
                            {
                                $horas_realizadas = 0;
                            }
                        }

                        if ($horas_realizadas >= $horas_dia)
                        {
                            $cumple_horas = 'S';
                        }
                        else
                        {
                            $horas_faltantes = $horas_dia - $horas_realizadas;
                        }

                        if ($entrada === 'S' && $salida === 'S' && $cumple_horas === 'S')
                        {
                            continue; //ENTRADA, SALIDA Y HORAS OK (NO AGREGO EL EMPLEADO AL REPORTE)
                        }

                        if ($entrada === 'N' && $salida === 'N')
                        {
                            if (empty($Emp->ausencias))
                            {
                                $novedad = new stdClass();
                                $novedad->labo_Codigo = $Emp->datos->labo_Codigo;
                                $novedad->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                                $novedad->nofi_Valor = abs($horas_dia);
                                $novedad->tnof_Codigo = 9;
                                $novedad->tnof_Descripcion = 'DIA NO TRABAJADO';
                                $novedades_calculadas[] = $novedad;
                                isset($cont_aus[$novedad->tnof_Descripcion]) ? $cont_aus[$novedad->tnof_Descripcion]++ : $cont_aus[$novedad->tnof_Descripcion] = 1;
                            }
                            else
                            {
                                isset($cont_aus[$Emp->ausencias[0]->moau_Descripcion]) ? $cont_aus[$Emp->ausencias[0]->moau_Descripcion]++ : $cont_aus[$Emp->ausencias[0]->moau_Descripcion] = 1;
                            }
                        }
                        elseif ($entrada === 'N')
                        {
                            $novedad = new stdClass();
                            $novedad->labo_Codigo = $Emp->datos->labo_Codigo;
                            $novedad->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                            $novedad->nofi_Valor = abs($horas_dia);
                            $novedad->tnof_Codigo = 5;
                            $novedad->tnof_Descripcion = 'OMITIO FICHAR ENTRADA';
                            $novedades_calculadas[] = $novedad;
                            isset($cont_aus[$novedad->tnof_Descripcion]) ? $cont_aus[$novedad->tnof_Descripcion]++ : $cont_aus[$novedad->tnof_Descripcion] = 1;
                        }
                        elseif ($salida === 'N')
                        {
                            $novedad = new stdClass();
                            $novedad->labo_Codigo = $Emp->datos->labo_Codigo;
                            $novedad->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                            $novedad->nofi_Valor = abs($horas_dia);
                            $novedad->tnof_Codigo = 6;
                            $novedad->tnof_Descripcion = 'OMITIO FICHAR SALIDA';
                            $novedades_calculadas[] = $novedad;
                            isset($cont_aus[$novedad->tnof_Descripcion]) ? $cont_aus[$novedad->tnof_Descripcion]++ : $cont_aus[$novedad->tnof_Descripcion] = 1;
                        }
                        elseif ($cumple_horas === 'N')
                        {
                            $novedad = new stdClass();
                            $novedad->labo_Codigo = $Emp->datos->labo_Codigo;
                            $novedad->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                            $novedad->nofi_Valor = abs($horas_faltantes);
                            $novedad->tnof_Codigo = 3;
                            $novedad->tnof_Descripcion = 'HORAS NO TRABAJADAS';
                            $novedades_calculadas[] = $novedad;
                            isset($cont_aus[$novedad->tnof_Descripcion]) ? $cont_aus[$novedad->tnof_Descripcion]++ : $cont_aus[$novedad->tnof_Descripcion] = 1;
                        }
                    }
                    else
                    {
                        $novedad = new stdClass();
                        $novedad->labo_Codigo = $Emp->datos->labo_Codigo;
                        $novedad->nofi_Fecha = $inicio->format('Y-m-d H:i:s');
                        $novedad->nofi_Valor = 0;
                        $novedad->tnof_Codigo = 7;
                        $novedad->tnof_Descripcion = 'REVISAR MANUALMENTE';
                        $novedades_calculadas[] = $novedad;
                        isset($cont_aus[$novedad->tnof_Descripcion]) ? $cont_aus[$novedad->tnof_Descripcion]++ : $cont_aus[$novedad->tnof_Descripcion] = 1;
                    }

                    //NOVEDADES DE FICHADAS (CALCULADAS POR SISTEMA MLC2)
                    if (!empty($novedades_calculadas))
                    {
                        foreach ($novedades_calculadas as $Nov_fich)
                        {
                            $fecha_nov = new DateTime($Nov_fich->nofi_Fecha);
                            if (empty($tmp_fich[date_format($fecha_nov, 'mj') . 'N']))
                            {
                                $tmp_fich[date_format($fecha_nov, 'mj') . 'N'] = $Nov_fich->tnof_Descripcion . ' (' . $this->_convertTime($Nov_fich->nofi_Valor) . ')';
                            }
                            else
                            {
                                $tmp_fich[date_format($fecha_nov, 'mj') . 'N'] .= ' | ' . $Nov_fich->tnof_Descripcion . ' (' . $this->_convertTime($Nov_fich->nofi_Valor) . ')';
                            }
                            $tmp_fich[date_format($fecha_nov, 'mj') . 'NT'][] = "$Nov_fich->tnof_Codigo";
                        }
                    }

                    //LISTADO DE PERSONAL PARA EXCEL
                    $planilla_excel[$Emp->datos->labo_Codigo] = array('labo_Codigo' => $Emp->datos->labo_Codigo, 'nombre' => $Emp->datos->nombre, 'oficina' => $Emp->datos->oficina, 'novedades' => ' ', 'horario' => $Emp->datos->horario);

                    //NOVEDADES
                    if (!empty($Emp->novedades))
                    {
                        foreach ($Emp->novedades as $Nov)
                        {
                            if (empty($planilla_excel[$Emp->datos->labo_Codigo]['novedades']) || $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] === ' ')
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= $Nov->vava_Descripcion;
                            }
                            else
                            {
                                $planilla_excel[$Emp->datos->labo_Codigo]['novedades'] .= " | $Nov->vava_Descripcion";
                            }
                        }
                    }

                    //RECORRE TODO EL RANGO DE FECHAS PARA ARMAR $planilla_arr
                    $inicio_inc = clone $inicio;
                    while ($inicio_inc < $fin)
                    {
                        $str_fecha_ini = date_format($inicio_inc, 'mj');

                        //CARGA AUSENCIAS
                        if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
                        {
                            $ausentes_excel[] = array('columna' => $columna_excel, 'fila' => $fila_excel);
                        }

                        //CARGA ENTRADAS
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'E'] = empty($tmp_fich[$str_fecha_ini . 'E']) ? ' ' : $tmp_fich[$str_fecha_ini . 'E'];
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            foreach ($tmp_fich[$str_fecha_ini . 'NT'] as $Tipo_nov)
                            {
                                switch ($Tipo_nov)
                                {
                                    case 1: //LLEGADA TARDE
//									case 5: //OMITIO FICHAR ENTRADA
                                    case 16: //LLEGADA TARDE LICENCIA ESPECIAL
                                    case 22: //LLEGADA TARDE JUSTIFICADA
                                    case 27: //LLEGADA TARDE EN COMISION
                                    case 30: //LLEGADA TARDE GREMIAL
//									case 34: //OMITIO FICHAR ENTRADA JUSTIFICADA
//									case 35: //OMITIO FICHAR ENTRADA COMISION
//									case 36: //OMITIO FICHAR ENTRADA GREMIAL
                                    case 41: //LLEGADA TARDE FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        //CARGA SALIDAS
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'S'] = empty($tmp_fich[$str_fecha_ini . 'S']) ? ' ' : $tmp_fich[$str_fecha_ini . 'S'];
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            foreach ($tmp_fich[$str_fecha_ini . 'NT'] as $Tipo_nov)
                            {
                                switch ($Tipo_nov)
                                {
                                    case 2: //SALIDA ANTICIPADA
//									case 6:  //OMITIO FICHAR SALIDA
                                    case 17: //SALIDA ANTICIPADA LICENCIA ESPECIAL
                                    case 20: //SALIDA ANTICIPADA ENFERMO
                                    case 23: //SALIDA ANTICIPADA JUSTIFICAD
                                    case 26: //SALIDA ANTICIPADA COMPENSADA
                                    case 28: //SALIDA ANTICIPADA EN COMISION
                                    case 31: //SALIDA ANTICIPADA GREMIAL
//									case 37: //OMITIO FICHAR SALIDA JUSTIFICADA
//									case 38: //OMITIO FICHAR SALIDA COMISION
//									case 39: //OMITIO FICHAR SALIDA GREMIAL
                                    case 42: //SALIDA ANTICIPADA FRANCO COMPENSATORIO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        //CARGA NOVEDADES
                        $planilla_excel[$Emp->datos->labo_Codigo][$str_fecha_ini . 'N'] = empty($tmp_fich[$str_fecha_ini . 'N']) ? ' ' : $tmp_fich[$str_fecha_ini . 'N'];
                        if (!empty($tmp_fich[$str_fecha_ini . 'NT']))
                        {
                            foreach ($tmp_fich[$str_fecha_ini . 'NT'] as $Tipo_nov)
                            {
                                //COLORES DE RELLENO SEGUN IMPORTANCIA
                                switch ($Tipo_nov)
                                {
                                    case 10: //HS.EXTRAS NORMALES
                                    case 11: //HS.EXTRAS AL 50%
                                    case 12: //HS.EXTRAS AL 100%
                                    case 13: //HS.ANTICIPADAS NORMALES
                                    case 14: //HS.ANTICIPADAS AL 50%
                                    case 15: //HS.ANTICIPADAS AL 100%
                                        if (empty($rellenos_excel[$columna_excel . $fila_excel])) //SOLO SI ESTA VACIO
                                        {
                                            $rellenos_excel[$columna_excel . $fila_excel] = 'C4D79B'; //VERDE PARA HORAS EXTRAS
                                        }
                                        break;
                                    case 1: //LLEGADA TARDE
                                    case 2: //SALIDA ANTICIPADA
                                    case 16: //LLEGADA TARDE LICENCIA ESPECIAL
                                    case 17: //SALIDA ANTICIPADA LICENCIA ESPECIAL
                                    case 20: //SALIDA ANTICIPADA ENFERMO
                                    case 22: //LLEGADA TARDE JUSTIFICADA
                                    case 23: //SALIDA ANTICIPADA JUSTIFICAD
                                    case 26: //SALIDA ANTICIPADA COMPENSADA
                                    case 27: //LLEGADA TARDE EN COMISION
                                    case 28: //SALIDA ANTICIPADA EN COMISION
                                    case 30: //LLEGADA TARDE GREMIAL
                                    case 31: //SALIDA ANTICIPADA GREMIAL
                                    case 41: //LLEGADA TARDE FRANCO COMPENSATORIO
                                    case 42: //SALIDA ANTICIPADA FRANCO COMPENSATORIO
                                        if (empty($rellenos_excel[$columna_excel . $fila_excel]) || $rellenos_excel[$columna_excel . $fila_excel] === 'C4D79B') //SOLO SI ESTA VACIO O ES VERDE
                                        {
                                            $rellenos_excel[$columna_excel . $fila_excel] = 'FFFF99'; //AMARILLO PARA LLEGADAS TARDE/SALIDAS ANTICIPADAS
                                        }
                                        break;
                                    default:
                                        //case 3:  //HORAS NO TRABAJADAS
                                        //case 5:  //OMITIO FICHAR ENTRADA
                                        //case 6:  //OMITIO FICHAR SALIDA
                                        //case 7:  //REVISAR MANUALMENTE
                                        //case 8:  //HORAS NO PERMITIDAS
                                        //case 9:  //DIA NO TRABAJADO
                                        //case 21: //INCONVENIENTE DE TRANSPORTE
                                        //case 24: //HORAS NO TRABAJADAS JUSTIFICADAS
                                        //case 29: //HORAS EN COMISION
                                        //case 32: //HORAS GREMIALES
                                        //case 34: //OMITIO FICHAR ENTRADA JUSTIFICADA
                                        //case 35: //OMITIO FICHAR ENTRADA COMISION
                                        //case 36: //OMITIO FICHAR ENTRADA GREMIAL
                                        //case 37: //OMITIO FICHAR SALIDA JUSTIFICADA
                                        //case 38: //OMITIO FICHAR SALIDA COMISION
                                        //case 39: //OMITIO FICHAR SALIDA GREMIAL
                                        //case 40: //DIA JUSTIFICADO
                                        //case 43: //HORAS FRANCO COMPENSATORIO
                                        //case 44: //RELOJ NO LEE TIMBRADO
                                        $rellenos_excel[$columna_excel . $fila_excel] = 'FABF8F'; //NARANJA
                                        break;
                                }
                            }
                        }
                        $columna_excel++;

                        $inicio_inc->add(new DateInterval('P1D'));
                    }
                    $fila_excel++;
//					}
                }
                //RESTABLECIENDO ULTIMA COLUMNA USADA
                $columna_excel = 'K';

                //ARMADO TITULO PLANILLA
                $tit_fecha = array();
                $tit_desc = array();
                $inicio_inc = clone $inicio;
                while ($inicio_inc < $fin)
                {
                    $str_fecha = $this->_getDayName($inicio_inc);
                    $str_fecha .= ' ' . date_format($inicio_inc, 'j');
                    $str_fecha .= ' ' . $this->_getMonthName($inicio_inc);
                    if (array_key_exists($inicio_inc->format('dmY'), $tmp_feriados))
                    {
                        $str_fecha .= ' (FERIADO)';
                    }
                    $tit_fecha[] = $str_fecha;
                    $tit_fecha[] = '';
                    $tit_fecha[] = '';
                    $tit_fecha[] = '';
                    $tit_fecha[] = '';
                    $tit_fecha[] = '';
                    $tit_desc[] = 'E';
                    $tit_desc[] = 'S';
                    $tit_desc[] = 'AVISO';
                    $tit_desc[] = 'NVO CONTROL';
                    $tit_desc[] = 'FECHA ALTA';
                    $tit_desc[] = 'OBSERVACIONES';
                    $inicio_inc->add(new DateInterval('P1D'));
                }
                $tit_planilla = array($tit_fecha, $tit_desc);

                //INICIO GENERACION EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $cant_filas = sizeof($planilla_excel) + 6;
                $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
                if (!$validLocale)
                {
                    lm('Unable to set locale to es - reverting to en_us');
                }
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Reporte Diario")
                        ->setDescription("Reporte Diario del Personal");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Reporte Diario");

                //OPCIONES DE IMPRESIÓN
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(1);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(1);
                $sheet->getPageSetup()->setFitToWidth(1);

                $sheet->getStyle("A5:{$columna_excel}6")->getFont()->setSize(10);
                $sheet->getStyle("A7:$columna_excel$cant_filas")->getFont()->setSize(8);
                $sheet->getStyle("F4:$columna_excel$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(25);

                $border_left_thin = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                $border_allborders_thin = array(
                    'borders' => array(
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                //TITULO TABLA
                $col = 'F';
                $col_anterior = 'E';
                for ($i = 1; $i <= sizeof($tit_fecha); $i++)
                {
                    switch ($i % 6)
                    {
                        case 1:
                            $sheet->getColumnDimension($col)->setWidth(13);
                            $sheet->getStyle("{$col}5:$col$cant_filas")->applyFromArray($border_left_thin);
                            $col_merge = $col;
                            $col_merge++;
                            $col_merge++;
                            $col_merge++;
                            $col_merge++;
                            $col_merge++;
                            $sheet->mergeCells("{$col}5:{$col_merge}5");
                            break;
                        case 2:
                            $sheet->getColumnDimension($col)->setWidth(13);
                            break;
                        case 3:
                            $sheet->getColumnDimension($col)->setWidth(22);
                            break;
                        case 4:
                        case 5:
                            $sheet->getColumnDimension($col)->setWidth(13);
                            break;
                        default:
                            $sheet->getColumnDimension($col)->setWidth(38);
                            $sheet->getStyle("{$col}7:$col$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT));
                            break;
                    }
                    $col_anterior = $col;
                    $col++;
                }
                $sheet->mergeCells("A5:A6"); //LEGAJO
                $sheet->mergeCells("B5:B6"); //APELLIDO Y NOMBRE
                $sheet->mergeCells("C5:C6"); //OFICINA
                $sheet->mergeCells("D5:D6"); //ADICIONALES
                $sheet->mergeCells("E5:E6"); //HORARIOS
                $sheet->getStyle("A5:$col_anterior$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                $sheet->getStyle("A5:{$col_anterior}6")->getFont()->setBold(TRUE); //NEGRITA TITULO
                $sheet->fromArray(array(array('LEGAJO', 'APELLIDO Y NOMBRE', 'OFICINA', 'ADICIONALES', 'HORARIO')), NULL, 'A5');
                $sheet->getStyle("A5:E5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->fromArray($tit_planilla, NULL, 'F5');
                $sheet->freezePane('F7');

                //TITULO PRINCIPAL
                $sheet->fromArray(array(array('REPORTE DIARIO DEL PERSONAL')), NULL, 'A1');
                $sheet->mergeCells("A1:E1");
                $sheet->mergeCells("A2:E2");
                $sheet->getStyle('A1')->getFont()->setSize(18);
                $sheet->fromArray(array(array("SECRETARÍA: $array_secretaria[$ofi_Secretaria]")), NULL, 'A2');
                $sheet->fromArray(array(array("OFICINA: " . implode(', ', $oficinas_titulo))), NULL, 'A3');
                $sheet->mergeCells("A3:E3");
                $sheet->mergeCells("A4:E4");
                $sheet->mergeCells("A4:E4");
                $sheet->getStyle('A2')->getFont()->setSize(14);
                $sheet->getStyle('A3:A4')->getFont()->setSize(10);

                //DATOS TABLA
                $sheet->fromArray($planilla_excel, NULL, 'A7');

                //RELLENOS A NOVEDADES
                if (!empty($rellenos_excel))
                {
                    foreach ($rellenos_excel as $Celda => $Relleno)
                    {
                        $sheet->getStyle($Celda)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => $Relleno)
                                    )
                                )
                        );
                    }
                }

                //RELLENOS A AUSENCIAS
                if (!empty($ausentes_excel))
                {
                    foreach ($ausentes_excel as $Ausente)
                    {
                        $inicio_ausente_merge = $Ausente['columna'] . $Ausente['fila'];
                        $Ausente['columna']++;
                        $Ausente['columna']++;
                        $fin_ausente_merge = $Ausente['columna'] . $Ausente['fila'];
                        $sheet->mergeCells("$inicio_ausente_merge:$fin_ausente_merge");
                        $sheet->getStyle($inicio_ausente_merge)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'E6B4B8') //ROJO PARA AUSENCIAS
                                    )
                                )
                        );
                    }
                }

                //REFERENCIAS
                $sheet->setCellValue('A' . (string) ($cant_filas + 2), 'REFERENCIAS');
                $sheet->getStyle('A' . (string) ($cant_filas + 2))->getFont()->setBold(TRUE); //NEGRITA

                $sheet->getStyle('A' . (string) ($cant_filas + 3))->applyFromArray(
                        array(
                            'font' => array(
                                'color' => array('rgb' => 'FF0000')
                            )
                        )
                );
                $sheet->setCellValue('A' . (string) ($cant_filas + 3), 'ROJO');
                $sheet->setCellValue('B' . (string) ($cant_filas + 3), 'PERSONAL QUE NO ES NECESARIO QUE FICHE');

                $sheet->getStyle('A' . (string) ($cant_filas + 4))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'C4D79B')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 4), 'NOVEDADES: HS EXTRAS Y HS ANTICIPADAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 5))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FFFF99')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 5), 'NOVEDADES: LLEGADAS TARDE Y SALIDAS ANTICIPADAS');

                $sheet->getStyle('A' . (string) ($cant_filas + 6))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'FABF8F')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 6), 'NOVEDADES: OMISIÓN DE FICHADAS Y HS/DÍAS NO TRABAJADOS');

                $sheet->getStyle('A' . (string) ($cant_filas + 7))->applyFromArray(
                        array(
                            'fill' => array(
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'color' => array('rgb' => 'E6B4B8')
                            )
                        )
                );
                $sheet->setCellValue('B' . (string) ($cant_filas + 7), 'AUSENCIAS');

                //NOMBRES RELOJES
                $sheet->setCellValue('A' . (string) ($cant_filas + 9), 'RELOJES');
                $sheet->getStyle('A' . (string) ($cant_filas + 9))->getFont()->setBold(TRUE); //NEGRITA
                $sheet->fromArray(array(array('(1) TABOADA 1', '(2) CENTRO CÍVICO 2', '(3) HACIENDA')), NULL, 'B' . (string) ($cant_filas + 10));
                $sheet->fromArray(array(array('(4) OBRADOR', '(5) POLICIA VIAL', '(6) POLIDEPORTIVO')), NULL, 'B' . (string) ($cant_filas + 11));
                $sheet->fromArray(array(array('(7) DELEG CHACRAS', '(8) DELEG CARRODILLA', '(9) SANTA ELENA')), NULL, 'B' . (string) ($cant_filas + 12));
                $sheet->fromArray(array(array('(10) DESARROLLO SOCIAL', '(11) PLANTA POTABILIZADORA', '(12) CENTRO CÍVICO 1')), NULL, 'B' . (string) ($cant_filas + 13));
                $sheet->fromArray(array(array('(13) ESTACION FERRI', '(14) CEMENTERIO', '(15) DELEG PERDRIEL')), NULL, 'B' . (string) ($cant_filas + 14));
                $sheet->fromArray(array(array('(16) DELEG AGRELO', '(17) DELEG UGARTECHE', '(18) DELEG CARRIZAL')), NULL, 'B' . (string) ($cant_filas + 15));
                $sheet->fromArray(array(array('(19) BIBLIOTECA', '(20) DELEG COMPUERTAS', '(21) DELEG PEDEMONTE')), NULL, 'B' . (string) ($cant_filas + 16));
                $sheet->fromArray(array(array('(22) DELEG DRUMMOND', '(23) CENTRO CÍVICO', '(24) PLAYÓN ESTE', '(25) POLID CARRODILLA')), NULL, 'B' . (string) ($cant_filas + 17));

                //RESUMEN
                $sheet->setCellValue('F' . (string) ($cant_filas + 2), 'RESUMEN AUSENCIAS');
                $sheet->getStyle('F' . (string) ($cant_filas + 2))->getFont()->setBold(TRUE); //NEGRITA
                $tmp_fila_res = 3;
                $cont_total_aus = 0;
                foreach ($cont_aus as $nombre => $valor)
                {
                    $sheet->setCellValue('F' . (string) ($cant_filas + $tmp_fila_res), $nombre);
                    $sheet->setCellValue('H' . (string) ($cant_filas + $tmp_fila_res), $valor);
                    $cont_total_aus += $valor;
                    $tmp_fila_res++;
                }
                $sheet->setCellValue('F' . (string) ($cant_filas + $tmp_fila_res), 'TOTAL AUSENTES');
                $sheet->setCellValue('H' . (string) ($cant_filas + $tmp_fila_res), $cont_total_aus);
                $sheet->getStyle('F' . (string) ($cant_filas + $tmp_fila_res))->getFont()->setBold(TRUE); //NEGRITA
                $sheet->getStyle('H' . (string) ($cant_filas + $tmp_fila_res))->getFont()->setBold(TRUE); //NEGRITA

                $nombreArchivo = 'reporte_diario_' . date('YmdHi');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            elseif (isset($personal['data']->error))
            {
                $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                redirect('asistencia/escritorio', 'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', '<br />No se encontraron personas en la oficina seleccionada');
                redirect('asistencia/reportes_major/reporte_diario', 'refresh');
            }
//			$this->benchmark->mark('post_end');
//			lm('POST: ' . $this->benchmark->elapsed_time('post_start', 'post_end'));
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['secretaria_sel'] = $this->form_validation->set_value('secretaria');

        $fake_model->fields['oficina']['array'] = $array_oficina;
        $fake_model->fields['secretaria']['array'] = $array_secretaria;

        //OPCIONES POR DEFECTO
        $default = new stdClass();
        $default->secretaria_id = NULL;
        $default->oficina_id = NULL;
        $default->mostrar_id = '4';
        $default->fecha = 'now';


        $data['fields'] = $this->build_fields($fake_model->fields, $default);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Reporte Diario del Personal';
        $data['title'] = TITLE . ' - Parte de Diario';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/reportes_major/reportes_reporte_diario', $data);
//		$this->benchmark->mark('all_end');
//		lm('ALL: ' . $this->benchmark->elapsed_time('all_start', 'all_end'));
    }

    public function parte_relojes()
    {
        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);
        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            try
            {
                $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'con_Personal' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna'
                ]]);
                $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
            } catch (Exception $e)
            {
                $oficinas_major = NULL;
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_secretaria_control = $array_secretaria;

        $array_oficina = array();
        $array_oficina_temp = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficina_temp[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $this->array_oficina_control = $array_oficina_temp;

        $this->array_particion_control = $array_particion = array(
            '01' => '01 - MUNICIPALES',
            '02' => '02 - H.C.D.',
            '05' => '05 - JARDINES MATERNALES',
            '07' => '07 - LOCACIONES',
            '10' => '10 - PLAN CONSTRUYENDO MI FUTURO'
        );

        $this->array_reloj_control = $array_reloj = array(
            '1' => '(1) TABOADA 1',
            '2' => '(2) CENTRO CÍVICO 2',
            '3' => '(3) HACIENDA',
            '4' => '(4) OBRADOR',
            '5' => '(5) POLICIA VIAL',
            '6' => '(6) POLIDEPORTIVO',
            '7' => '(7) DELEG CHACRAS',
            '8' => '(8) DELEG CARRODILLA',
            '9' => '(9) SANTA ELENA',
            '10' => '(10) DESARROLLO SOCIAL',
            '11' => '(11) PLANTA POTABILIZADORA',
            '12' => '(12) CENTRO CÍVICO 1',
            '13' => '(13) ESTACION FERRI',
            '14' => '(14) CEMENTERIO',
            '15' => '(15) DELEG PERDRIEL',
            '16' => '(16) DELEG AGRELO',
            '17' => '(17) DELEG UGARTECHE',
            '18' => '(18) DELEG CARRIZAL',
            '19' => '(19) BIBLIOTECA',
            '20' => '(20) DELEG COMPUERTAS',
            '21' => '(21) DELEG PEDEMONTE',
            '22' => '(22) DELEG DRUMMOND',
            '23' => '(23) CENTRO CÍVICO',
            '24' => '(24) PLAYÓN ESTE',
            '25' => '(25) POLID CARRODILLA'
        );

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'secretaria' => array('label' => 'Secretaría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'particion' => array('label' => 'Partición', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'reloj' => array('label' => 'Reloj', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $ofi_Secretaria = $this->input->post('secretaria');
            $ofi_Oficina = $this->input->post('oficina');
            $prtn_Codigo = $this->input->post('particion');
            $fich_NroReloj = $this->input->post('reloj');
            $desde = $this->input->post('desde');
            $hasta = $this->input->post('hasta');
            $desde_sql = DateTime::createFromFormat("d/m/Y", $desde)->format('Ymd');
            $hasta_sql = DateTime::createFromFormat("d/m/Y", $hasta)->format('Ymd');

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
            } catch (Exception $e)
            {
                $feriados = NULL;
            }

            $tmp_feriados = array();
            if (!empty($feriados))
            {
                foreach ($feriados as $Feriado)
                {
                    $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                }
            }

            //BUSCO HORARIOS DIARIOS
            try
            {
                $http_response_horarios = $guzzleHttp->request('GET', "personas/horarios_diarios_oficina", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'desde' => $desde_sql, 'hasta' => $hasta_sql]]);
                $horarios_diarios = json_decode($http_response_horarios->getBody()->getContents());
            } catch (Exception $e)
            {
                $horarios_diarios = NULL;
            }

            $tmp_horarios_diarios = array();
            if (!empty($horarios_diarios))
            {
                foreach ($horarios_diarios as $Diario)
                {
                    $tmp_horarios_diarios[$Diario->labo_Codigo][(new DateTime($Diario->hodi_Fecha))->format('dmY')] = array('E' => $Diario->hodi_Entrada, 'S' => $Diario->hodi_Salida);
                }
            }

            //BUSCA TODOS LOS EMPLEADOS DE LAS OFICINAS SELECCIONADAS Y DATOS ASOCIADOS(NOVEDADES, FICHADAS, NOVEDADES DE FICHADAS Y AUSENCIAS)
            try
            {
                $http_response_personal = $guzzleHttp->request('GET', "personas/parte_reloj_impresion", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'prtn_Codigo' => $prtn_Codigo, 'fich_NroReloj' => $fich_NroReloj, 'desde' => $desde, 'hasta' => $hasta, 'feriados' => $tmp_feriados]]);
                $personal = json_decode($http_response_personal->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal = NULL;
            }

            if (!empty($personal))
            {
                //RANGO DE FECHAS A TRABAJAR
                $inicio = DateTime::createFromFormat("d/m/Y H:i:s", $desde . ' 00:00:00');
                $fin = DateTime::createFromFormat("d/m/Y H:i:s", $hasta . ' 00:00:00');

                //FILA DONDE COMIENZAN LOS DATOS
                $fila_excel = 6;

                //CARGA LOS DATOS NECESARIOS PARA EXCEL
                $planilla_excel = array();
                $fines_excel = array();
                $noficha_excel = array();
                $oficinas_titulo = array();

                //MUESTRA TODAS LAS OFICINAS SELECCIONADAS
                foreach ($ofi_Oficina as $Oficina_sel)
                {
                    $oficinas_titulo[$array_oficina_temp[$Oficina_sel]] = $array_oficina_temp[$Oficina_sel];
                }

                foreach ($personal as $Emp)
                {
                    //EVITA ERRORES SI EL EMPLEADO ESTA 2 VECES (POR HORARIOS REPETIDOS GENERALMENTE)
                    if (!empty($planilla_excel[$Emp->datos[0]->labo_Codigo]))
                    {
                        continue;
                    }

                    //LISTADO DE PERSONAL PARA EXCEL (CON DATOS DEL PRIMER HORARIO)
                    $planilla_excel[$Emp->datos[0]->labo_Codigo] = array(
                        'labo_Codigo' => $Emp->datos[0]->labo_Codigo,
                        'nombre' => $Emp->datos[0]->nombre,
                        'oficina' => '',
                        'horario' => ''
                    );

                    //FICHADAS
                    $tmp_fich = array();
                    if (!empty($Emp->fichadas))
                    {
                        foreach ($Emp->fichadas as $Fich)
                        {
                            //PONER SALIDA EN EL DÍA ANTERIOR
//							if (trim($Fich->fich_Codigo) === 'S')
//							{
//								$fecha = new DateTime($Fich->fich_FechaHora);
//								$hora = $fecha->format("H");
//								if (!isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . 'E']) && $hora < 1) //NO HAY ENTRADA ANTERIOR Y SON MENOS DE LA 1 AM
//								{
//									$fecha->sub(new DateInterval('P1D'));
//									if (isset($tmp_fich[date_format($fecha, 'mj') . 'S'])) //YA HAY SALIDA EL DÍA ANTERIOR
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									else
//									{
//										$tmp_fich[date_format($fecha, 'mj') . 'S'] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									continue; //SALTANDO SALIDA QUE SUPUESTAMENTE CORRESPONDE AL DIA ANTERIOR HASTA DEFINIR COMO LA MANEJA MAJOR
//								}
//							}

                            if (isset($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)]))
                            {
//								if (trim($Fich->fich_Codigo) === 'E')
//								{
//									if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') >= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
//									{
//										$tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA ENTRADA DESPUES DE 5 MINUTOS DE LA PRIMERA
//									}
//								}
//								elseif (trim($Fich->fich_Codigo) === 'S')
//								{
//									if (date_format(new DateTime($Fich->fich_FechaHora), 'H:i') <= $this->_agregarMinutos($tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)], 5))
//									{
//										$tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')';
//									}
//									else
//									{
//										$tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] .= '|' . date_format(new DateTime($Fich->fich_FechaHora), 'H:i') . ' (' . $Fich->fich_NroReloj . ')'; // SEGUNDA SALIDA DESPUES DE 5 MINUTOS DE LA SEGUNDA
//									}
//								}
                            }
                            else
                            {
                                $tmp_fich[date_format(new DateTime($Fich->fich_FechaHora), 'mj') . trim($Fich->fich_Codigo)] = $Fich->fich_NroReloj;
                            }
                        }
                    }

//					//AUSENCIAS
//					if (!empty($Emp->ausencias))
//					{
//						foreach ($Emp->ausencias as $Aus)
//						{
//							$fecha_ini_aus = new DateTime($Aus->ause_FechaInicio);
//							if ($fecha_ini_aus < $inicio)
//							{
//								$fecha_ini_aus = clone $inicio;
//							}
//							$fecha_fin_aus = new DateTime($Aus->ause_FechaFin);
//							if ($fecha_fin_aus > $fin)
//							{
//								$fecha_fin_aus = clone $fin;
//							}
//
//							do
//							{
//								$tmp_fich[date_format($fecha_ini_aus, 'mj') . 'E'] = $Aus->moau_Descripcion;
//								$tmp_fich[date_format($fecha_ini_aus, 'mj') . 'S'] = $Aus->moau_Codigo;
//								$fecha_ini_aus->add(new DateInterval('P1D'));
//							} while ($fecha_ini_aus <= $fecha_fin_aus);
//						}
//					}

                    foreach ($Emp->datos as $Horario)
                    {
                        //INICIALIZO FECHAS (HORARIO)
                        $inicio_horario = new Datetime($Horario->hoca_FechaDesde);
                        $fin_horario = new Datetime($Horario->hoca_FechaHasta);

                        //CARGA DATOS DE OFICINAS Y HORARIOS
                        if (empty($planilla_excel[$Horario->labo_Codigo]['oficina']))
                        {
                            $planilla_excel[$Horario->labo_Codigo]['oficina'] = $Horario->oficina;
                        }
                        else if ($planilla_excel[$Horario->labo_Codigo]['oficina'] !== $Horario->oficina)
                        {
                            $planilla_excel[$Horario->labo_Codigo]['oficina'] .= " | $Horario->oficina";
                        }

                        if (!empty($Horario->horario))
                        {
                            if (empty($planilla_excel[$Horario->labo_Codigo]['horario']))
                            {
                                $planilla_excel[$Horario->labo_Codigo]['horario'] = $Horario->horario . ' (' . date_format(new DateTime($Horario->hoca_FechaDesde), 'd-m-Y') . ' a ' . date_format(new DateTime($Horario->hoca_FechaHasta), 'd-m-Y') . ')';
                            }
                            else
                            {
                                $planilla_excel[$Horario->labo_Codigo]['horario'] .= " | $Horario->horario" . ' (' . date_format(new DateTime($Horario->hoca_FechaDesde), 'd-m-Y') . ' a ' . date_format(new DateTime($Horario->hoca_FechaHasta), 'd-m-Y') . ')';
                            }
                        }

                        //FILAS EN ROJO PARA PERSONAL QUE NO FICHA
                        if ($Horario->hoca_Ficha === 'N')
                        {
                            $noficha_excel[$fila_excel] = 'FF0000';
                        }

                        //RECORRE TODO EL RANGO DE FECHAS PARA ARMAR $planilla_arr
                        $fecha_inicial_while = clone $inicio;
                        $fecha_final_while = clone $fin;
                        //COLUMNA DONDE COMIENZAN LOS DATOS
                        $columna_excel = 'E';
                        while ($fecha_inicial_while <= $fecha_final_while)
                        {
                            $str_fecha_ini = date_format($fecha_inicial_while, 'mj');

                            if (!empty($tmp_horarios_diarios[$Horario->labo_Codigo]) && array_key_exists($fecha_inicial_while->format('dmY'), $tmp_horarios_diarios[$Horario->labo_Codigo]))
                            {
                                $horario_diario = $tmp_horarios_diarios[$Horario->labo_Codigo][$fecha_inicial_while->format('dmY')];
                                if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
//									if (!ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
//									{
//										switch ($tmp_fich[$str_fecha_ini . 'S'])
//										{
//											case 1:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'VAC';
//												break;
//											case 4:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ENF';
//												break;
//											case 6:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ACC';
//												break;
//											case 8:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'MAT';
//												break;
//											case 9:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'LAC';
//												break;
//											case 14:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAT';
//												break;
//											case 19:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAR';
//												break;
//											case 23:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'JUN';
//												break;
//											default:
//												$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AUS';
//												break;
//										}
//									}
//									else
//									{
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(' . $tmp_fich[$str_fecha_ini . 'E'] . ') ' . '(' . $tmp_fich[$str_fecha_ini . 'S'] . ')';
//									}
                                }
                                else if (!empty($tmp_fich[$str_fecha_ini . 'E']))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(' . $tmp_fich[$str_fecha_ini . 'E'] . ') ' . '(-)';
                                }
                                else if (!empty($tmp_fich[$str_fecha_ini . 'S']))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(-) ' . '(' . $tmp_fich[$str_fecha_ini . 'S'] . ')';
                                }
                                else
                                {
                                    if ($horario_diario['E'] !== '00:00' || $horario_diario['S'] !== '00:00') //HORARIO DIARIO CON E O S PARA EL EMPLEADO
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(-) (-)';
                                    }
                                    else
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                    }
                                }
                            }
                            else
                            {
                                if ($fecha_inicial_while >= $inicio_horario && $fecha_inicial_while <= $fin_horario)
                                {
                                    //VERIFICA NUMERO DE DIA
                                    $error_secuencia = FALSE;
                                    $error_horario = FALSE;
                                    if (!empty($Horario->hora_Codigo))
                                    {
                                        switch ($Horario->hora_Tipo)
                                        {
                                            case 'N':
                                            case 'F':
                                                if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_feriados))
                                                {
                                                    $numero_dia = 8; //FERIADO ES 8 EN MAJOR
                                                }
                                                else
                                                {
                                                    $numero_dia = $fecha_inicial_while->format('w');
                                                    if ($numero_dia === '0')
                                                    {
                                                        $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                                    }
                                                }
                                                break;
                                            case 'R':
                                                $inicio_sec = new DateTime($Horario->hoca_FechaSecuencia1);
                                                if ($fecha_inicial_while < $inicio_sec)
                                                {
                                                    $numero_dia = 1;
                                                    $error_secuencia = TRUE;
                                                }
                                                else
                                                {
                                                    $dias_inicio_sec = $fecha_inicial_while->diff($inicio_sec)->format("%a");
                                                    $dias_inicio_sec++; //SI ES EL PRIMER DÍA DEVUELVE 0 Y DEBERIA SER 1
                                                    $sum_dias_sec = 0;
                                                    for ($i = 1; $i <= 20; $i++)
                                                    {
                                                        $sum_dias_sec += (int) $Horario->{"hora_DiaSec" . $i . "Cant"};
                                                    }

                                                    $resto_sec = $dias_inicio_sec % $sum_dias_sec;
                                                    if ($resto_sec === 0)
                                                    {
                                                        $resto_sec = $sum_dias_sec;
                                                    }
                                                    for ($i = 1; $i <= 20; $i++)
                                                    {
                                                        if ($resto_sec <= (int) $Horario->{"hora_DiaSec" . $i . "Cant"})
                                                        {
                                                            $numero_dia = $i;
                                                            break;
                                                        }
                                                        else
                                                        {
                                                            $resto_sec -= (int) $Horario->{"hora_DiaSec" . $i . "Cant"};
                                                        }
                                                    }
                                                }
                                                break;
                                        }
                                    }
                                    else
                                    {
                                        $error_horario = TRUE;
                                        $numero_dia = 1;
                                    }

                                    if (!empty($tmp_fich[$str_fecha_ini . 'E']) && !empty($tmp_fich[$str_fecha_ini . 'S']))
                                    {
//										if (!ctype_digit(substr($tmp_fich[$str_fecha_ini . 'E'], 0, 2)))
//										{
//											switch ($tmp_fich[$str_fecha_ini . 'S'])
//											{
//												case 1:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'VAC';
//													break;
//												case 4:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ENF';
//													break;
//												case 6:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'ACC';
//													break;
//												case 8:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'MAT';
//													break;
//												case 9:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'LAC';
//													break;
//												case 14:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAT';
//													break;
//												case 19:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'PAR';
//													break;
//												case 23:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'JUN';
//													break;
//												default:
//													$planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = 'AUS';
//													break;
//											}
//										}
//										else
//										{
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(' . $tmp_fich[$str_fecha_ini . 'E'] . ') ' . '(' . $tmp_fich[$str_fecha_ini . 'S'] . ')';
//										}
                                    }
                                    else if (!empty($tmp_fich[$str_fecha_ini . 'E']))
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(' . $tmp_fich[$str_fecha_ini . 'E'] . ') ' . '(-)';
                                    }
                                    else if (!empty($tmp_fich[$str_fecha_ini . 'S']))
                                    {
                                        $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(-) ' . '(' . $tmp_fich[$str_fecha_ini . 'S'] . ')';
                                    }
                                    else
                                    {
                                        if ($Horario->{'hora_DiaSec' . $numero_dia . 'Ent'} !== '00:00' || $Horario->{'hora_DiaSec' . $numero_dia . 'Sal'} !== '00:00') //DIA CON E O S PARA EL EMPLEADO
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = '(-) (-)';
                                        }
                                        else
                                        {
                                            $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                        }
                                    }
                                }
                                elseif (empty($planilla_excel[$Horario->labo_Codigo][$str_fecha_ini]))
                                {
                                    $planilla_excel[$Horario->labo_Codigo][$str_fecha_ini] = ' ';
                                }
                            }

                            if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_feriados))
                            {
                                $col_dia = 8; //FERIADO ES 8 EN MAJOR
                            }
                            else
                            {
                                $col_dia = $fecha_inicial_while->format('w');
                                if ($col_dia === '0')
                                {
                                    $col_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                }
                            }
                            if ($col_dia == 6 || $col_dia == 7 || $col_dia == 8) //NO USAR ===
                            {
                                $fines_excel[$columna_excel] = array('columna' => $columna_excel);
                            }
                            $columna_excel++;

                            $fecha_inicial_while->add(new DateInterval('P1D'));
                        }
                    }
                    $fila_excel++;
                }

                //ARMADO TITULO PLANILLA
                $tit_fecha = array();
                $inicio_inc = clone $inicio;
                while ($inicio_inc <= $fin)
                {
                    $str_fecha = date_format($inicio_inc, 'j');
                    $tit_fecha[] = $str_fecha;
                    $inicio_inc->add(new DateInterval('P1D'));
                }
                $tit_planilla = array($tit_fecha);

                //INICIO GENERACION EXCEL
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $cant_filas = sizeof($planilla_excel) + 5;
                $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
                if (!$validLocale)
                {
                    lm('Unable to set locale to es - reverting to en_us');
                }
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Parte Relojes")
                        ->setDescription("Parte Relojes");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Parte Relojes");

                //OPCIONES DE IMPRESIÓN
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(1);
                $sheet->getPageMargins()->setRight(0.5);
                $sheet->getPageMargins()->setLeft(0.4);
                $sheet->getPageMargins()->setBottom(1);
                $sheet->getPageSetup()->setFitToWidth(1);

                $sheet->getStyle("A5:{$columna_excel}5")->getFont()->setSize(10);
                $sheet->getStyle("A6:$columna_excel$cant_filas")->getFont()->setSize(8);
                $sheet->getStyle("E4:$columna_excel$cant_filas")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(25);

                $border_allborders_thin = array(
                    'borders' => array(
                        'allborders' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                        )
                    )
                );

                //TITULO TABLA
                $col = 'E';
                $col_anterior = 'D';
                for ($i = 1; $i <= sizeof($tit_fecha); $i++)
                {
                    $sheet->getColumnDimension($col)->setWidth(5);

                    $col_anterior = $col;
                    $col++;
                }
                $sheet->getStyle("A5:$col_anterior$cant_filas")->applyFromArray($border_allborders_thin); //BORDES PLANILLA
                $sheet->getStyle("A5:{$col_anterior}5")->getFont()->setBold(TRUE); //NEGRITA TITULO
                $sheet->getStyle("{$columna_excel}5:{$columna_excel}5")->getFont()->setBold(TRUE); //NEGRITA RESUMEN
                $sheet->fromArray(array(array('LEGAJO', 'APELLIDO Y NOMBRE', 'OFICINA', 'HORARIO')), NULL, 'A5');
                $sheet->getStyle("A5:D5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle("{$columna_excel}5")->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->fromArray($tit_planilla, NULL, 'E5');
                $sheet->freezePane('E6');

                //TITULO PRINCIPAL
                $sheet->fromArray(array(array('PARTE RELOJES')), NULL, 'A1');
                $sheet->mergeCells("A1:D1");
                $sheet->mergeCells("A2:D2");
                $sheet->getStyle('A1')->getFont()->setSize(18);
                $sheet->fromArray(array(array("SECRETARÍA: $array_secretaria[$ofi_Secretaria]")), NULL, 'A2');
                $sheet->fromArray(array(array("OFICINA: " . implode(', ', $oficinas_titulo))), NULL, 'A3');
                $sheet->fromArray(array(array("DESDE: $desde - HASTA: $hasta")), NULL, 'A4');
                $sheet->mergeCells("A3:D3");
                $sheet->mergeCells("A4:D4");
                $sheet->mergeCells("A4:D4");
                $sheet->getStyle('A2')->getFont()->setSize(14);
                $sheet->getStyle('A3:A4')->getFont()->setSize(10);

                //DATOS TABLA
                $sheet->fromArray($planilla_excel, NULL, 'A6');

                //LETRA ROJA A GENTE QUE NO DEBE FICHAR
                if (!empty($noficha_excel))
                {
                    foreach ($noficha_excel as $Fila => $Color)
                    {
                        $sheet->getStyle("A$Fila:$columna_excel$Fila")->applyFromArray(
                                array(
                                    'font' => array(
                                        'color' => array('rgb' => $Color)
                                    )
                                )
                        );
                    }
                }

                //RELLENOS A FINES DE SEMANA Y FERIADOS
                if (!empty($fines_excel))
                {
                    foreach ($fines_excel as $Finde)
                    {
                        $sheet->getStyle($Finde['columna'] . "5:" . $Finde['columna'] . $cant_filas)->applyFromArray(
                                array(
                                    'fill' => array(
                                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                        'color' => array('rgb' => 'D9D9D9') //ROJO PARA AUSENCIAS
                                    )
                                )
                        );
                    }
                }

                //NOMBRES RELOJES
                $sheet->setCellValue('A' . (string) ($cant_filas + 2), 'RELOJES');
                $sheet->fromArray(array(array('(1) TABOADA 1', '(2) CENTRO CÍVICO 2', '(3) HACIENDA')), NULL, 'A' . (string) ($cant_filas + 3));
                $sheet->fromArray(array(array('(4) OBRADOR', '(5) POLICIA VIAL', '(6) POLIDEPORTIVO')), NULL, 'A' . (string) ($cant_filas + 4));
                $sheet->fromArray(array(array('(7) DELEG CHACRAS', '(8) DELEG CARRODILLA', '(9) SANTA ELENA')), NULL, 'A' . (string) ($cant_filas + 5));
                $sheet->fromArray(array(array('(10) DESARROLLO SOCIAL', '(11) PLANTA POTABILIZADORA', '(12) CENTRO CÍVICO 1')), NULL, 'A' . (string) ($cant_filas + 6));
                $sheet->fromArray(array(array('(13) ESTACION FERRI', '(14) CEMENTERIO', '(15) DELEG PERDRIEL')), NULL, 'A' . (string) ($cant_filas + 7));
                $sheet->fromArray(array(array('(16) DELEG AGRELO', '(17) DELEG UGARTECHE', '(18) DELEG CARRIZAL')), NULL, 'A' . (string) ($cant_filas + 8));
                $sheet->fromArray(array(array('(19) BIBLIOTECA', '(20) DELEG COMPUERTAS', '(21) DELEG PEDEMONTE')), NULL, 'A' . (string) ($cant_filas + 9));
                $sheet->fromArray(array(array('(22) DELEG DRUMMOND', '(23) CENTRO CÍVICO', '(24) PLAYÓN ESTE', '(25) POLID CARRODILLA')), NULL, 'A' . (string) ($cant_filas + 10));

                $nombreArchivo = 'parte_relojes_' . date('YmdHi');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            elseif (isset($personal['data']->error))
            {
                $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                redirect('asistencia/escritorio', 'refresh');
            }
            else
            {
                $this->session->set_flashdata('error', '<br />No se encontraron personas en la oficina seleccionada');
                redirect('asistencia/reportes_major/parte_relojes', 'refresh');
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['secretaria_sel'] = $this->form_validation->set_value('secretaria');

        $fake_model->fields['secretaria']['array'] = $array_secretaria;
        $fake_model->fields['oficina']['array'] = $array_oficina;
        $fake_model->fields['particion']['array'] = $array_particion;
        $fake_model->fields['reloj']['array'] = $array_reloj;

        //OPCIONES POR DEFECTO
        $default = new stdClass();
        $default->secretaria_id = NULL;
        $default->oficina_id = NULL;
        $default->particion_id = NULL;
        $default->mostrar_id = '4';
        $default->reloj_id = NULL;
        $default->fecha = 'now';
        $default->desde = NULL;
        $default->hasta = NULL;

        $data['fields'] = $this->build_fields($fake_model->fields, $default);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Parte Relojes';
        $data['title'] = TITLE . ' - Parte Relojes';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/reportes_major/reportes_parte_diario', $data);
    }

    private function _agregarMinutos($actual, $minutos_extra)
    {
        list($hora_actual, $minuto_actual) = explode(':', substr($actual, 0, 5));
        $minuto_nuevo = $hora_actual * 60;
        $minuto_nuevo += $minuto_actual;
        $minuto_nuevo += $minutos_extra;

        $hora_nuevo = floor($minuto_nuevo / 60);
        $minuto_nuevo -= $hora_nuevo * 60;

        return sprintf('%02d:%02d', $hora_nuevo, $minuto_nuevo);
    }

    private function _convertTime($dec)
    {
        // start by converting to seconds
        $seconds = ($dec * 3600);
        // we're given hours, so let's get those the easy way
        $hours = floor($dec);
        // since we've "calculated" hours, let's remove them from the seconds variable
        $seconds -= $hours * 3600;
        // calculate minutes left
        $minutes = floor($seconds / 60);
        // remove those from seconds as well
        $seconds -= $minutes * 60;
        // return the time formatted HH:MM
        return $this->_lz($hours) . ":" . $this->_lz($minutes);
    }

    private function _lz($num)
    {
        return (strlen($num) < 2) ? "0{$num}" : $num;
    }

    private function _getDayName($date)
    {
        switch (date_format($date, 'w'))
        {
            case 0: $day_name = 'DOMINGO';
                break;
            case 1: $day_name = 'LUNES';
                break;
            case 2: $day_name = 'MARTES';
                break;
            case 3: $day_name = 'MIÉRCOLES';
                break;
            case 4: $day_name = 'JUEVES';
                break;
            case 5: $day_name = 'VIERNES';
                break;
            case 6: $day_name = 'SÁBADO';
                break;
        }
        return $day_name;
    }

    private function _getMonthName($date)
    {
        switch (date_format($date, 'm'))
        {
            case 1: $month_name = 'ENERO';
                break;
            case 2: $month_name = 'FEBRERO';
                break;
            case 3: $month_name = 'MARZO';
                break;
            case 4: $month_name = 'ABRIL';
                break;
            case 5: $month_name = 'MAYO';
                break;
            case 6: $month_name = 'JUNIO';
                break;
            case 7: $month_name = 'JULIO';
                break;
            case 8: $month_name = 'AGOSTO';
                break;
            case 9: $month_name = 'SETIEMBRE';
                break;
            case 10: $month_name = 'OCTUBRE';
                break;
            case 11: $month_name = 'NOVIEMBRE';
                break;
            case 12: $month_name = 'DICIEMBRE';
                break;
        }
        return $month_name;
    }
}
