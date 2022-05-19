<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

    /**
     * Controlador de Reportes
     * Autor: Leandro
     * Creado: 28/11/2019
     * Modificado: 29/10/2020 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'defunciones_user', 'defunciones_consulta_general');
        $this->grupos_solo_consulta = array('defunciones_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de reportes';
        $data['title'] = TITLE . ' - Reportes';
        $this->load_template('defunciones/reportes/reportes_listar', $data);
    }

    public function grafico_operaciones()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = $this->get_date_sql('desde');
            $hasta = $this->get_date_sql('hasta');
        }
        else
        {
            $desde = date_format(new DateTime('- 1 month'), 'd-m-Y');
            $hasta = date_format(new DateTime(), 'd-m-Y');
        }
        $data['graficos_data'] = $this->graficos_operaciones_data($desde, $hasta);

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');

        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Operaciones por Usuario';
        $data['title'] = TITLE . ' - Operaciones por Usuario';
        $data['css'][] = 'vendor/c3/c3.min.css';
        $data['js'][] = 'vendor/d3/d3.min.js';
        $data['js'][] = 'vendor/c3/c3.min.js';
        $this->load_template('defunciones/reportes/reportes_content', $data);
    }

    private function graficos_operaciones_data($ini_sql, $fin_sql)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('Usuarios_model');
        $this->load->model('defunciones/Operaciones_model');
        $ini = new DateTime($ini_sql);
        $fin = new DateTime($fin_sql);

        $usuarios = $this->db->query("
			SELECT users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario
			FROM users
			LEFT JOIN personas ON personas.id = users.persona_id 
			LEFT JOIN users_groups ON users_groups.user_id = users.id 
			LEFT JOIN `groups` ON `groups`.id = users_groups.group_id 
			WHERE `groups`.name = 'defunciones_user' AND users.active = 1
			ORDER BY personas.apellido, personas.nombre")->result();

        //GRAFICO LINEAS
        $grafico_usuarios = array(array('x'));
        if (!empty($usuarios))
        {
            foreach ($usuarios as $Usuario)
            {
                $usuarios_mes[$Usuario->id] = $this->db->query("SELECT DATE_FORMAT(fecha, '%d/%m/%Y') as dia, COUNT(1) as cantidad "
                                . 'FROM df_operaciones '
                                . 'WHERE DATE(fecha) BETWEEN ? AND ? AND user_id = ? '
                                . 'GROUP BY dia '
                                . 'ORDER BY fecha ASC', array($ini_sql, $fin_sql, $Usuario->id))->result();

                $grafico_usuarios[] = array($Usuario->usuario);
            }
        }

        $usuarios_array = array();
        if (!empty($usuarios_mes))
        {
            foreach ($usuarios_mes as $Usuario_id => $Usuario)
            {
                foreach ($Usuario as $Dia)
                {
                    $usuarios_array[$Usuario_id][$Dia->dia] = $Dia->cantidad;
                }
            }
        }

        $temp_ini = clone $ini;
        while ($temp_ini <= $fin)
        {
            $grafico_usuarios[0][] = $temp_ini->format('d/m');
            $cont = 1;
            if (!empty($usuarios_mes))
            {
                foreach ($usuarios_mes as $Usuario_id => $Usuario)
                {
                    $grafico_usuarios[$cont][] = !empty($usuarios_array[$Usuario_id][$temp_ini->format('d/m/Y')]) ? $usuarios_array[$Usuario_id][$temp_ini->format('d/m/Y')] : 0;
                    $cont++;
                }
            }
            $temp_ini->add(new DateInterval('P1D'));
        }

        //GRAFICO TORTA
        $usuarios_t = $this->db->query("SELECT CONCAT(personas.apellido, ', ', personas.nombre) as usuario, COUNT(1) as cantidad "
                        . 'FROM df_operaciones '
                        . 'JOIN users ON df_operaciones.user_id = users.id '
                        . 'LEFT JOIN personas ON personas.id = users.persona_id '
                        . 'WHERE DATE(fecha) BETWEEN ? AND ? '
                        . 'GROUP BY usuario '
                        . 'ORDER BY usuario ASC', array($ini_sql, $fin_sql))->result();

        $grafico_usuario_t = array();

        $usuarios_t_array = array();
        if (!empty($usuarios_t))
        {
            foreach ($usuarios_t as $Usuario_t)
            {
                $usuarios_t_array[$Usuario_t->usuario] = array($Usuario_t->usuario, $Usuario_t->cantidad);
            }
        }

        foreach ($usuarios as $Usuario)
        {
            $grafico_usuario_t[] = !empty($usuarios_t_array[$Usuario->usuario]) ? $usuarios_t_array[$Usuario->usuario] : array($Usuario->usuario, 0);
        }

        return array('grafico_usuarios' => json_encode($grafico_usuarios), 'grafico_usuarios_t' => json_encode($grafico_usuario_t));
    }

    public function sin_boleta()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('defunciones/Operaciones_model');

        $options['select'] = array(
            'df_operaciones.fecha',
            'df_operaciones.tipo_operacion',
            'df_difuntos.dni as difunto_dni',
            'df_difuntos.apellido as difunto_apellido',
            'df_difuntos.nombre as difunto_nombre',
            'df_difuntos.nacimiento',
            'df_difuntos.defuncion',
            'df_ubicaciones.tipo as ubicacion_tipo',
            'df_ubicaciones.sector as ubicacion_sector',
            'df_ubicaciones.cuadro as ubicacion_cuadro',
            'df_ubicaciones.fila as ubicacion_fila',
            'df_ubicaciones.nicho as ubicacion_nicho',
            'df_ubicaciones.denominacion as ubicacion_denominacion',
            'df_solicitantes.dni as solicitante_dni',
            'df_solicitantes.nombre as solicitante_nombre',
            'df_solicitantes.domicilio as solicitante_domicilio',
            'df_solicitantes.telefono as solicitante_telefono',
            'df_solicitantes.domicilio_alt as solicitante_domicilio_alt',
            'df_solicitantes.telefono_alt as solicitante_telefono_alt'
        );

        $options['join'] = array(
            array('type' => 'left', 'table' => 'df_difuntos', 'where' => 'df_operaciones.difunto_id = df_difuntos.id'),
            array('type' => 'left', 'table' => 'df_solicitantes', 'where' => 'df_operaciones.solicitante_id = df_solicitantes.id'),
            array('type' => 'left', 'table' => 'df_ubicaciones', 'where' => 'df_difuntos.ubicacion_id = df_ubicaciones.id')
        );

        $where['column'] = "(df_operaciones.boleta_pago IS NULL OR df_operaciones.boleta_pago = '')";
        $where['value'] = '';
        $where['override'] = TRUE;
        $options['where'] = array($where);

        $options['sort_by'] = 'df_operaciones.fecha';
        $options['sort_direction'] = 'asc';
        $options['return_array'] = TRUE;
        $print_data = $this->Operaciones_model->get($options);

        if (!empty($print_data))
        {
            foreach ($print_data as $key => $value)
            {
                $print_data[$key]['fecha'] = date_format(new DateTime($value['fecha']), 'd-m-Y');
                $print_data[$key]['defuncion'] = date_format(new DateTime($value['defuncion']), 'd-m-Y');
                $print_data[$key]['nacimiento'] = !empty($value['nacimiento']) ? date_format(new DateTime($value['nacimiento']), 'd-m-Y') : NULL;
                switch ($value['tipo_operacion'])
                {
                    case '1':
                        $print_data[$key]['tipo_operacion'] = "Concesión";
                        break;
                    case '2':
                        $print_data[$key]['tipo_operacion'] = "Ornato";
                        break;
                    case '3':
                        $print_data[$key]['tipo_operacion'] = "Reducción";
                        break;
                    case '4':
                        $print_data[$key]['tipo_operacion'] = "Traslado";
                        break;
                }
                switch ($value['ubicacion_tipo'])
                {
                    case 'Nicho':
                        $print_data[$key]['ubicacion_tipo'] = "S: {$value['ubicacion_sector']} - F: {$value['ubicacion_fila']} - N: {$value['ubicacion_nicho']}";
                        break;
                    case 'Tierra':
                        $print_data[$key]['ubicacion_tipo'] = "S: {$value['ubicacion_sector']} - C: {$value['ubicacion_cuadro']} - F: {$value['ubicacion_fila']} - P: {$value['ubicacion_nicho']}";
                        break;
                    case 'Mausoleo':
                        $print_data[$key]['ubicacion_tipo'] = "C: {$value['ubicacion_cuadro']} - D: {$value['ubicacion_denominacion']}";
                        break;
                    case 'Pileta':
                        $print_data[$key]['ubicacion_tipo'] = "C: {$value['ubicacion_cuadro']} - D: {$value['ubicacion_denominacion']}";
                        break;
                    case 'Nicho Urna':
                        $print_data[$key]['ubicacion_tipo'] = "S: {$value['ubicacion_sector']} - F: {$value['ubicacion_fila']} - N: {$value['ubicacion_nicho']}";
                        break;
                }
                unset($print_data[$key]['ubicacion_sector']);
                unset($print_data[$key]['ubicacion_cuadro']);
                unset($print_data[$key]['ubicacion_fila']);
                unset($print_data[$key]['ubicacion_nicho']);
                unset($print_data[$key]['ubicacion_denominacion']);
            }
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $spreadsheet->getProperties()->setTitle("Trámites sin Boleta")->setDescription("");
            $spreadsheet->setActiveSheetIndex(0);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle("Trámites sin Boleta");
            $sheet->getColumnDimension('A')->setWidth(12);
            $sheet->getColumnDimension('B')->setWidth(12);
            $sheet->getColumnDimension('C')->setWidth(10);
            $sheet->getColumnDimension('D')->setWidth(22);
            $sheet->getColumnDimension('E')->setWidth(22);
            $sheet->getColumnDimension('F')->setWidth(12);
            $sheet->getColumnDimension('G')->setWidth(12);
            $sheet->getColumnDimension('H')->setWidth(45);
            $sheet->getColumnDimension('I')->setWidth(10);
            $sheet->getColumnDimension('J')->setWidth(30);
            $sheet->getColumnDimension('K')->setWidth(30);
            $sheet->getColumnDimension('L')->setWidth(20);
            $sheet->getColumnDimension('M')->setWidth(30);
            $sheet->getColumnDimension('N')->setWidth(20);
            $sheet->getStyle('A1:N2')->getFont()->setBold(TRUE);
            $sheet->fromArray(array(array('Operación', '', 'Difunto', '', '', '', '', '', 'Solicitante')), NULL, 'A1');
            $sheet->fromArray(array(array('Fecha', 'Tipo', 'DNI', 'Apellido', 'Nombre', 'Nacimiento', 'Defunción', 'Ubicación', 'DNI', 'Nombre', 'Domicilio', 'Teléfono', 'Domicilio Alt.', 'Teléfono Alt.')), NULL, 'A2');
            $sheet->fromArray($print_data, NULL, 'A3');
            $sheet->setAutoFilter('A2:N' . $sheet->getHighestRow());

            $sheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
            $sheet->mergeCells('A1:B1');
            $sheet->getStyle('C1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
            $sheet->mergeCells('C1:H1');
            $sheet->getStyle('I1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
            $sheet->mergeCells('I1:N1');

            $BStyle1 = array(
                'borders' => array(
                    'left' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                    )
                )
            );
            $sheet->getStyle('C1:C' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
            $sheet->getStyle('I1:I' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
            $sheet->getStyle('O1:O' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);

            $BStyle2 = array(
                'borders' => array(
                    'bottom' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                    )
                )
            );
            $sheet->getStyle('A1:N1')->applyFromArray($BStyle2);
            $sheet->getStyle('A' . (sizeof($print_data) + 2) . ':N' . (sizeof($print_data) + 2))->applyFromArray($BStyle2);

            $nombreArchivo = 'tramites_sin_boleta_' . date('Ymd');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
            header("Cache-Control: max-age=0");

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        }
        else
        {
            $this->session->set_flashdata('error', 'Sin datos');
            redirect('defunciones/reportes/listar', 'refresh');
        }
    }

    public function sin_licencia()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('defunciones/Difuntos_model');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'anio' => array('label' => 'Año', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $anios_array = array('Todos' => 'Todos');
        $anio_actual = (int) date_format(new DateTime(), 'Y');
        for ($i = 1920; $i <= $anio_actual; $i++)
        {
            $anios_array[$i] = $i;
        }
        $this->array_anio_control = $data['anio_opt'] = $anios_array;

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $options['select'] = array(
                'df_difuntos.dni as difunto_dni',
                'df_difuntos.apellido as difunto_apellido',
                'df_difuntos.nombre as difunto_nombre',
                'df_difuntos.nacimiento',
                'df_difuntos.defuncion',
                'df_difuntos.edad',
                'df_cocherias.nombre as cocheria',
                'df_ubicaciones.tipo as ubicacion_tipo',
                'df_ubicaciones.sector as ubicacion_sector',
                'df_ubicaciones.cuadro as ubicacion_cuadro',
                'df_ubicaciones.fila as ubicacion_fila',
                'df_ubicaciones.nicho as ubicacion_nicho',
                'df_ubicaciones.denominacion as ubicacion_denominacion',
                'df_solicitantes.dni as solicitante_dni',
                'df_solicitantes.nombre as solicitante_nombre',
                'df_solicitantes.domicilio as solicitante_domicilio',
                'df_solicitantes.telefono as solicitante_telefono',
                'df_solicitantes.domicilio_alt as solicitante_domicilio_alt',
                'df_solicitantes.telefono_alt as solicitante_telefono_alt'
            );

            $options['join'] = array(
                array('type' => 'left', 'table' => 'df_cocherias', 'where' => 'df_difuntos.cocheria_id = df_cocherias.id'),
                array('type' => 'left', 'table' => 'df_operaciones', 'where' => 'df_operaciones.id = (SELECT id FROM df_operaciones WHERE df_operaciones.difunto_id = df_difuntos.id AND df_operaciones.tipo_operacion = 1 ORDER BY df_operaciones.fecha ASC LIMIT 1)'),
                array('type' => 'left', 'table' => 'df_solicitantes', 'where' => 'df_operaciones.solicitante_id = df_solicitantes.id'),
                array('type' => 'left', 'table' => 'df_ubicaciones', 'where' => 'df_difuntos.ubicacion_id = df_ubicaciones.id')
            );

            $where['column'] = "(df_difuntos.licencia_inhumacion IS NULL OR df_difuntos.licencia_inhumacion = '')";
            $where['value'] = '';
            $where['override'] = TRUE;
            $options['where'] = array($where);

            if ($this->input->post('anio') !== 'Todos')
            {
                $where['column'] = 'year(df_difuntos.defuncion)';
                $where['value'] = $this->input->post('anio');
                $options['where'][] = $where;
            }

            $options['sort_by'] = 'df_difuntos.dni';
            $options['sort_direction'] = 'asc';
            $options['return_array'] = TRUE;
            $print_data = $this->Difuntos_model->get($options);

            if (!empty($print_data))
            {
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['defuncion'] = date_format(new DateTime($value['defuncion']), 'd-m-Y');
                    $print_data[$key]['nacimiento'] = !empty($value['nacimiento']) ? date_format(new DateTime($value['nacimiento']), 'd-m-Y') : NULL;
                    switch ($value['ubicacion_tipo'])
                    {
                        case 'Nicho':
                            $print_data[$key]['ubicacion_tipo'] = "S: {$value['ubicacion_sector']} - F: {$value['ubicacion_fila']} - N: {$value['ubicacion_nicho']}";
                            break;
                        case 'Tierra':
                            $print_data[$key]['ubicacion_tipo'] = "S: {$value['ubicacion_sector']} - C: {$value['ubicacion_cuadro']} - F: {$value['ubicacion_fila']} - P: {$value['ubicacion_nicho']}";
                            break;
                        case 'Mausoleo':
                            $print_data[$key]['ubicacion_tipo'] = "C: {$value['ubicacion_cuadro']} - D: {$value['ubicacion_denominacion']}";
                            break;
                        case 'Pileta':
                            $print_data[$key]['ubicacion_tipo'] = "C: {$value['ubicacion_cuadro']} - D: {$value['ubicacion_denominacion']}";
                            break;
                        case 'Nicho Urna':
                            $print_data[$key]['ubicacion_tipo'] = "S: {$value['ubicacion_sector']} - F: {$value['ubicacion_fila']} - N: {$value['ubicacion_nicho']}";
                            break;
                    }
                    unset($print_data[$key]['ubicacion_sector']);
                    unset($print_data[$key]['ubicacion_cuadro']);
                    unset($print_data[$key]['ubicacion_fila']);
                    unset($print_data[$key]['ubicacion_nicho']);
                    unset($print_data[$key]['ubicacion_denominacion']);
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()->setTitle("Difuntos sin Licencia")->setDescription("");
                $spreadsheet->setActiveSheetIndex(0);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle("Difuntos sin Licencia");
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(22);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(12);
                $sheet->getColumnDimension('G')->setWidth(30);
                $sheet->getColumnDimension('H')->setWidth(45);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(30);
                $sheet->getColumnDimension('K')->setWidth(30);
                $sheet->getColumnDimension('L')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(30);
                $sheet->getColumnDimension('N')->setWidth(20);
                $sheet->getStyle('A1:N2')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('Difunto', '', '', '', '', '', '', '', 'Solicitante')), NULL, 'A1');
                $sheet->fromArray(array(array('DNI', 'Apellido', 'Nombre', 'Nacimiento', 'Defunción', 'Edad', 'Cochería', 'Ubicación', 'DNI', 'Nombre', 'Domicilio', 'Teléfono', 'Domicilio Alt.', 'Teléfono Alt.')), NULL, 'A2');
                $sheet->fromArray($print_data, NULL, 'A3');
                $sheet->setAutoFilter('A2:N' . $sheet->getHighestRow());

                $sheet->getStyle('A1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('A1:H1');
                $sheet->getStyle('I1')->getAlignment()->applyFromArray(array('horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER));
                $sheet->mergeCells('I1:N1');

                $BStyle1 = array(
                    'borders' => array(
                        'left' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('I1:I' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);
                $sheet->getStyle('O1:O' . (sizeof($print_data) + 2))->applyFromArray($BStyle1);

                $BStyle2 = array(
                    'borders' => array(
                        'bottom' => array(
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
                        )
                    )
                );
                $sheet->getStyle('A1:N1')->applyFromArray($BStyle2);
                $sheet->getStyle('A' . (sizeof($print_data) + 2) . ':N' . (sizeof($print_data) + 2))->applyFromArray($BStyle2);

                $nombreArchivo = 'difuntos_sin_licencia_' . date('Ymd');
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = 'Sin Datos';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['anio']['array'] = $anios_array;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Difuntos sin Licencia de Inhumación';
        $data['title'] = TITLE . ' - Informe de Difuntos sin Licencia de Inhumación';
        $this->load_template('defunciones/reportes/reportes_content', $data);
    }
}
