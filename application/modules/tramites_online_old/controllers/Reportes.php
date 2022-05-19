<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

    /**
     * Controlador de Reportes
     * Autor: Leandro
     * Creado: 02/04/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('Areas_model');
        $this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_consulta_general');
        $this->grupos_admin = array('admin', 'tramites_online_admin', 'tramites_online_consulta_general');
        $this->grupos_solo_consulta = array('tramites_online_consulta_general');
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
        $this->load_template('tramites_online/reportes/reportes_listar', $data);
    }

    public function tramites()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'area' => array('label' => 'Área', 'type' => 'date', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'desde' => array('label' => 'Desde', 'type' => 'date', 'required' => TRUE),
            'hasta' => array('label' => 'Hasta', 'type' => 'date', 'required' => TRUE)
        );

        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
                'select' => array('areas.id', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
                'join' => array(array('to_usuarios_areas', 'to_usuarios_areas.area_id = areas.id', 'LEFT')),
                'where' => array("nombre <> '-'", "to_usuarios_areas.user_id = " . $this->session->userdata('user_id')),
                'sort_by' => 'codigo'), array('Todas' => 'Todas')
            );
        }
        else
        {
            $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('Todas' => 'Todas'));
        }

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
            $hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));
            $hasta->add(new DateInterval('P1D'));
            $desde_sql = $desde->format('Y/m/d');
            $hasta_sql = $hasta->format('Y/m/d');
            $area = $this->input->post('area');

            $this->load->model('tramites_online/Tramites_model');
            $options['select'] = array(
                'to_tramites.id',
                'to_tramites.fecha_inicio',
                "to_tramites_categorias.nombre as categoria",
                "to_tramites_tipos.nombre as tipo",
                "CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil,  ')') as persona",
                'personas.dni as dni',
                'personas.telefono as telefono',
                'personas.celular as celular',
                'personas.email as email',
                'to_tramites.padron as padron',
                "CONCAT(areas.codigo, ' - ', areas.nombre) as area",
                'to_estados.nombre as estado',
                'to_pases.fecha as ultimo_mov',
                'to_tramites.fecha_fin'
            );
            $options['join'] = array(
                array('type' => 'left', 'table' => 'to_tramites_tipos', 'where' => 'to_tramites_tipos.id = to_tramites.tipo_id'),
                array('type' => 'left', 'table' => 'to_tramites_categorias', 'where' => 'to_tramites_categorias.id = to_tramites_tipos.categoria_id'),
                array('type' => 'left', 'table' => 'to_pases', 'where' => 'to_pases.tramite_id = to_tramites.id'),
                array('type' => 'left outer', 'table' => 'to_pases P', 'where' => 'P.tramite_id = to_tramites.id AND to_pases.fecha < P.fecha'),
                array('type' => 'left', 'table' => 'to_estados', 'where' => 'to_estados.id = to_pases.estado_destino_id'),
                array('type' => 'left', 'table' => 'areas', 'where' => 'areas.id = to_pases.area_destino_id'),
                array('type' => 'left', 'table' => 'personas', 'where' => 'personas.id = to_tramites.persona_id')
            );
            if (!in_groups($this->grupos_admin, $this->grupos))
            {
                $options['join'][] = array('type' => 'left', 'table' => 'to_usuarios_areas', 'where' => 'to_usuarios_areas.area_id = to_tramites_tipos.area_id');
                $options['where'][] = array('column' => 'to_usuarios_areas.user_id', 'value' => $this->session->userdata('user_id'));
            }
            if ($area !== 'Todas')
            {
                $options['where'][] = array('column' => 'to_tramites_tipos.area_id', 'value' => $area);
            }
            if (!empty($desde))
            {
                $options['where'][] = array('column' => 'to_tramites.fecha_inicio >=', 'value' => $desde_sql);
            }
            if (!empty($hasta))
            {
                $options['where'][] = array('column' => 'to_tramites.fecha_inicio <', 'value' => $hasta_sql);
            }
            $options['return_array'] = TRUE;

            $print_data = $this->Tramites_model->get($options);
            if (!empty($print_data))
            {
                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Consultas")
                        ->setDescription("Informe de Consultas (Módulo Consultas OnLine)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(30);
                $sheet->getColumnDimension('E')->setWidth(40);
                $sheet->getColumnDimension('F')->setWidth(10);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(15);
                $sheet->getColumnDimension('I')->setWidth(30);
                $sheet->getColumnDimension('J')->setWidth(10);
                $sheet->getColumnDimension('K')->setWidth(40);
                $sheet->getColumnDimension('L')->setWidth(10);
                $sheet->getColumnDimension('M')->setWidth(20);
                $sheet->getColumnDimension('N')->setWidth(20);
                $sheet->getStyle('A1:N1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array(array('N°', 'Fecha Inicio', 'Categoría', 'Tipo', 'Persona', 'DNI', 'Teléfono', 'Celular', 'Mail', 'Padrón', 'Area', 'Estado', 'Ult. Movimiento', 'Fecha Finalización')), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2', TRUE);
                $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
                $nombreArchivo = 'InformeTramites_' . date('Ymd');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
                header("Cache-Control: max-age=0");

                $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                $writer->save('php://output');
                exit();
            }
            else
            {
                $error_msg = '<br />Sin Datos';
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Consultas';
        $data['title'] = TITLE . ' - Informe de Consultas';
        $this->load_template('tramites_online/reportes/reportes_content', $data);
    }
}
