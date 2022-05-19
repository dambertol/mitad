<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Horarios_major extends MY_Controller
{

    /**
     * Controlador de Horarios Major
     * Autor: Leandro
     * Creado: 24/02/2017
     * Modificado: 11/05/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('asistencia/Usuarios_oficinas_model');
        $this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_consulta_general');
        $this->grupos_solo_consulta = array('asistencia_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Cód. Horario', 'data' => 'hora_Codigo', 'width' => 10, 'class' => 'dt-body-right', 'responsive_class' => 'all'),
                array('label' => 'Horario', 'data' => 'hora_Descripcion', 'width' => 35),
                array('label' => 'Tipo', 'data' => 'hora_Tipo', 'width' => 10),
                array('label' => 'Tolerancia Entrada', 'data' => 'hora_ToleranciaEnt', 'class' => 'dt-body-right', 'width' => 13),
                array('label' => 'Tolerancia Salida', 'data' => 'hora_ToleranciaSal', 'class' => 'dt-body-right', 'width' => 13),
                array('label' => 'Cant. Personal', 'data' => 'cantidad', 'class' => 'dt-body-right', 'width' => 13),
                array('label' => '', 'data' => 'detalle', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'horarios_major_table',
            'server_side' => FALSE,
            'source_url' => 'asistencia/horarios_major/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_horarios_major_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );

        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = !empty($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de horarios Major';
        $data['title'] = TITLE . ' - Horarios Major';
        $this->load_template('asistencia/horarios_major/horarios_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
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
            $http_response_horarios = $guzzleHttp->request('GET', "personas/horarios", ['query' => ['fecha' => date_format(new Datetime(), 'Ymd')]]);
            $horarios = json_decode($http_response_horarios->getBody()->getContents());
        } catch (Exception $e)
        {
            $horarios = NULL;
        }

        if (!empty($horarios))
        {
            $horarios['data'] = $horarios;
            foreach ($horarios['data'] as $i => $Horario)
            {
                if ($horarios['data'][$i]->cantidad > 0)
                {
                    $horarios['data'][$i]->cantidad = '<span class="label label-success" style="float:right;">' . $horarios['data'][$i]->cantidad . '</span>';
                }
                else
                {
                    $horarios['data'][$i]->cantidad = '<span class="label label-danger" style="float:right;">' . $horarios['data'][$i]->cantidad . '</span>';
                }
                if ($horarios['data'][$i]->hora_Tipo === 'N' || $horarios['data'][$i]->hora_Tipo === 'F' || $horarios['data'][$i]->hora_Tipo === 'R')
                {
                    $horarios['data'][$i]->detalle = '<a href="asistencia/horarios_major/ver_detalle/' . $Horario->hora_Codigo . '" title="Ver detalle" class="btn btn-primary btn-xs"><i class="fa fa-calendar"></i></a>';
                }
                else
                {
                    $horarios['data'][$i]->detalle = '';
                }
                $horarios['data'][$i]->ver = '<a href="asistencia/horarios_major/listar_personal/' . $Horario->hora_Codigo . '" title="Ver personal" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>';
            }
            echo json_encode($horarios);
        }
        else
        {
            echo json_encode(array('data' => array()));
        }
    }

    public function reporte()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
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
            $http_response_empleados = $guzzleHttp->request('GET', "personas/horarios_listado", ['query' => ['fecha' => date_format(new Datetime(), 'Ymd')]]);
            $empleados = json_decode($http_response_empleados->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleados = NULL;
        }

        if (!empty($empleados))
        {
            $planilla_excel = array();
            $personal['data'] = $empleados;
            foreach ($personal['data'] as $i => $Personal)
            {
                $planilla_excel[] = array(
                    'codigo' => $Personal->hora_Codigo,
                    'descripcion' => $Personal->hora_Descripcion,
                    'tipo' => $Personal->hora_Tipo,
                    'oficina_cod' => $Personal->ofi_Oficina,
                    'oficina_desc' => $Personal->ofi_Descripcion,
                    'legajo' => $Personal->labo_Codigo,
                    'apellido' => $Personal->pers_Apellido,
                    'nombre' => $Personal->pers_Nombre,
                    'ficha' => $Personal->hoca_ficha
                );
            }

            //INICIO GENERACION EXCEL
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $validLocale = \PhpOffice\PhpSpreadsheet\Settings::setLocale('es');
            if (!$validLocale)
            {
                lm('Unable to set locale to es - reverting to en_us');
            }
            $spreadsheet->getProperties()
                    ->setCreator("SistemaMLC")
                    ->setLastModifiedBy("SistemaMLC")
                    ->setTitle("Informe de Horarios Major")
                    ->setDescription("Informe de Horarios Major");
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Informe de Horarios Major");

            $cant_filas = sizeof($planilla_excel) + 2; //2 DE ENCABEZADOS
            $sheet->getColumnDimension('A')->setWidth(8); //COD HORARIO
            $sheet->getColumnDimension('B')->setWidth(55); //DESC HORARIO
            $sheet->getColumnDimension('C')->setWidth(8); //TIPO
            $sheet->getColumnDimension('D')->setWidth(8); //COD OFICINA
            $sheet->getColumnDimension('E')->setWidth(50); //DESC OFICINA
            $sheet->getColumnDimension('F')->setWidth(10); //LEGAJO
            $sheet->getColumnDimension('G')->setWidth(25); //APELLIDO
            $sheet->getColumnDimension('H')->setWidth(25); //NOMBRE
            $sheet->getColumnDimension('I')->setWidth(8); //FICHA
            //TITULOS
            $sheet->fromArray(array(array('HORARIO', '', '', 'OFICINA', '', 'PERSONAL')), NULL, 'A1');
            $sheet->getStyle("A1:I1")->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
            $sheet->mergeCells("A1:C1"); //HORARIO
            $sheet->mergeCells("D1:E1"); //OFICINA
            $sheet->mergeCells("F1:I1"); //PERSONAL
            $sheet->fromArray(array(array('CÓD', 'DESCRIPCIÓN', 'TIPO', 'CÓD', 'DESCRIPCIÓN', 'LEGAJO', 'APELLIDO', 'NOMBRE', 'FICHA')), NULL, 'A2');
            $sheet->getStyle("A1:I2")->getFont()->setBold(TRUE);
            $sheet->setAutoFilter('A2:I2');
            $border_allborders_thin = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
            $sheet->getStyle("A1:I$cant_filas")->applyFromArray($border_allborders_thin);

            //DATOS
            $sheet->fromArray($planilla_excel, NULL, 'A3');

            $nombreArchivo = 'informe_horarios_' . date('YmdHi');
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
            $this->session->set_flashdata('error', '<br />No se encontraron horarios');
            redirect('asistencia/horarios_major/listar', 'refresh');
        }
    }

    public function listar_personal($hora_Codigo = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $hora_Codigo == NULL || !ctype_digit($hora_Codigo))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Legajo', 'data' => 'labo_Codigo', 'width' => 12, 'responsive_class' => 'all'),
                array('label' => 'Apellido', 'data' => 'pers_Apellido', 'width' => 16),
                array('label' => 'Nombre', 'data' => 'pers_Nombre', 'width' => 16),
                array('label' => 'Ficha', 'data' => 'hoca_ficha', 'width' => 6),
                array('label' => 'Inicio Sec', 'data' => 'hoca_FechaSecuencia1', 'render' => 'date', 'width' => 9, 'class' => 'dt-body-right'),
                array('label' => 'Cód. Oficina', 'data' => 'ofi_Oficina', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Oficina', 'data' => 'ofi_Descripcion', 'width' => 27),
                array('label' => '', 'data' => 'calendario', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'personal_table',
            'server_side' => FALSE,
            'source_url' => "asistencia/horarios_major/listar_personal_data/$hora_Codigo",
            'reuse_var' => TRUE,
            'initComplete' => "complete_personal_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );

        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);

        $data['error'] = !empty($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = "Listado de personal por horario (Cod. Horario: $hora_Codigo)";
        $data['title'] = TITLE . ' - Personal por Horario';
        $this->load_template('asistencia/horarios_major/horarios_listar_personal', $data);
    }

    public function listar_personal_data($hora_Codigo = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
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
            $http_response_empleados = $guzzleHttp->request('GET', "personas/horario_listado", ['query' => ['hora_Codigo' => $hora_Codigo, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
            $empleados = json_decode($http_response_empleados->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleados = NULL;
        }

        if (!empty($empleados))
        {
            $personal['data'] = $empleados;
            foreach ($personal['data'] as $i => $Personal)
            {
                $personal['data'][$i]->calendario = '<a href="asistencia/personal_major/calendario/' . $Personal->labo_Codigo . '" title="Ver calendario" class="btn btn-primary btn-xs"><i class="fa fa-calendar"></i></a>';
                $personal['data'][$i]->ver = '<a href="asistencia/fichadas/ver/' . $Personal->labo_Codigo . '" title="Ver fichadas" class="btn btn-primary btn-xs"><i class="fa fa-clock-o"></i></a>';
            }
            echo json_encode($personal);
        }
        else
        {
            echo json_encode(array('data' => array()));
        }
    }

    public function ver_detalle($hora_Codigo = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $hora_Codigo == NULL || !ctype_digit($hora_Codigo))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'codigo' => array('label' => 'Codigo', 'type' => 'integer'),
            'descripcion' => array('label' => 'Descripción'),
            'tipo' => array('label' => 'Tipo'),
            'toleranciaE' => array('label' => 'Tolerancia Entrada'),
            'toleranciaS' => array('label' => 'Tolerancia Salida')
        );

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        try
        {
            $http_response_horario = $guzzleHttp->request('GET', "personas/horario", ['query' => ['hora_Codigo' => $hora_Codigo, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
            $detalle = json_decode($http_response_horario->getBody()->getContents());
        } catch (Exception $e)
        {
            $detalle = NULL;
        }

        if (!empty($detalle))
        {
            $detalle['data'] = $detalle;
            $horario = new stdClass();
            $horario->codigo = $detalle['data'][0]->hora_Codigo;
            $horario->descripcion = $detalle['data'][0]->hora_Descripcion;
            $horario->tipo = $detalle['data'][0]->hora_Tipo;
            $horario->toleranciaE = $detalle['data'][0]->hora_ToleranciaEnt;
            $horario->toleranciaS = $detalle['data'][0]->hora_ToleranciaSal;
        }
        else
        {
            $this->session->set_flashdata('error', '<br />No se encontro el detalle para el horario especificado');
            redirect('asistencia/horarios_major/listar', 'refresh');
        }

        $data['fields'] = $this->build_fields($fake_model->fields, $horario, TRUE);
        $data['detalle'] = $detalle['data'];
        $data['txt_btn'] = NULL;
        $data['title_view'] = "Detalle de horario Major";
        $data['css'] = 'css/asistencia/asistencia.css';
        $data['title'] = TITLE . ' - Detalle de horario Major';
        $this->load_template('asistencia/horarios_major/horarios_detalle', $data);
    }
}
