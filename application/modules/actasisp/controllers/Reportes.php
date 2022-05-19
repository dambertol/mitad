<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportes extends MY_Controller
{

    /**
     * Controlador de Reportes
     * Autor: Leandro
     * Creado: 19/08/2020
     * Modificado: 19/08/2020 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->load->model('actasisp/Actas_model');
        $this->load->model('actasisp/Motivos_model');
        $this->grupos_permitidos = array('admin', 'actasisp_user', 'actasisp_consulta_general');
        $this->grupos_solo_consulta = array('actasisp_consulta_general');
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
        $this->load_template('actasisp/reportes/reportes_listar', $data);
    }

    public function actas()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'motivo' => array('label' => 'Motivo', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE)
        );

        $this->array_motivo_control = $array_motivo = $this->get_array('Motivos', 'motivo', 'id', array(
            'select' => "id, CONCAT(act_motivos.codigo, ' - ', act_motivos.motivo) as motivo",
            'sort_by' => 'act_motivos.codigo'
                )
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = NULL;
        if ($this->form_validation->run() === TRUE)
        {
            $options['select'] = array(
                "act_actas.numero",
                "act_actas.tipo",
                "act_actas.fecha",
                "act_actas.estado",
                "act_actas.padron_municipal",
                "domicilios.calle",
                "domicilios.altura",
                "domicilios.piso",
                "domicilios.dpto",
                "domicilios.manzana",
                "domicilios.casa",
                "localidades.nombre as localidad",
                "CONCAT(P1.apellido, ', ', P1.nombre, ' (', P1.dni,  ')') as inspector_1",
                "CONCAT(P2.apellido, ', ', P2.nombre, ' (', P2.dni,  ')') as inspector_2",
                "CONCAT(act_motivos.codigo, ' - ', act_motivos.motivo) as motivo",
                "act_actas.observaciones",
            );
            $options['join'] = array(
                array('act_motivos', 'act_motivos.id = act_actas.motivo_id', 'left'),
                array('act_inspectores_actas IA1', 'IA1.acta_id = act_actas.id AND IA1.posicion = 1', 'left'),
                array('act_inspectores I1', 'IA1.inspector_id = I1.id', 'left'),
                array('personas P1', 'P1.id = I1.persona_id', 'left'),
                array('act_inspectores_actas IA2', 'IA2.acta_id = act_actas.id AND IA2.posicion = 2', 'left'),
                array('act_inspectores I2', 'IA2.inspector_id = I2.id', 'left'),
                array('personas P2', 'P2.id = I2.persona_id', 'left'),
                array('domicilios', 'domicilios.id = act_actas.domicilio_id', 'left'),
                array('localidades', 'localidades.id = domicilios.localidad_id', 'left')
            );
            $options['where_in'] = array(array('column' => 'act_motivos.id', 'value' => $this->input->post('motivo')));
            $options['sort_by'] = 'act_actas.numero';
            $options['return_array'] = TRUE;

            $print_data = $this->Actas_model->get($options);
            if (!empty($print_data))
            {
                foreach ($print_data as $key => $value)
                {
                    $print_data[$key]['fecha'] = !empty($value['fecha']) ? date_format(new DateTime($value['fecha']), 'd-m-Y') : '';
                }

                $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                $spreadsheet->getProperties()
                        ->setCreator("SistemaMLC")
                        ->setLastModifiedBy("SistemaMLC")
                        ->setTitle("Informe de Actas")
                        ->setDescription("Informe de Actas (Módulo Stock Actas ISP)");
                $spreadsheet->setActiveSheetIndex(0);

                $sheet = $spreadsheet->getActiveSheet();
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->setTitle("Informe de Actas");
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(20);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(10);
                $sheet->getColumnDimension('F')->setWidth(40);
                $sheet->getColumnDimension('G')->setWidth(10);
                $sheet->getColumnDimension('H')->setWidth(10);
                $sheet->getColumnDimension('I')->setWidth(10);
                $sheet->getColumnDimension('J')->setWidth(10);
                $sheet->getColumnDimension('K')->setWidth(10);
                $sheet->getColumnDimension('L')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(40);
                $sheet->getColumnDimension('N')->setWidth(40);
                $sheet->getColumnDimension('O')->setWidth(40);
                $sheet->getColumnDimension('P')->setWidth(80);
                $sheet->getStyle('A1:P1')->getFont()->setBold(TRUE);
                $sheet->fromArray(array('N°', 'Tipo', 'Fecha', 'Estado', 'Padrón', 'Calle', 'Altura', 'Piso', 'Dpto', 'Mzana', 'Casa', 'Localidad', 'Inspector 1', 'Inspector 2', 'Motivo', 'Observaciones'), NULL, 'A1');
                $sheet->fromArray($print_data, NULL, 'A2');
                $nombreArchivo = 'InformeActas_' . date('Ymd');

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

        $fake_model->fields['motivo']['array'] = $array_motivo;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Generar';
        $data['title_view'] = 'Informe de Actas';
        $data['title'] = TITLE . ' - Informe de Actas';
        $this->load_template('actasisp/reportes/reportes_content', $data);
    }
}
