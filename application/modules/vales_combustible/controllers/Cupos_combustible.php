<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cupos_combustible extends MY_Controller
{

    /**
     * Controlador de Cupos Combustible
     * Autor: Leandro
     * Creado: 25/01/2019
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Cupos_combustible_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('Areas_model');
        $this->grupos_permitidos = array('admin', 'vales_combustible_contaduria', 'vales_combustible_hacienda', 'vales_combustible_consulta_general');
        $this->grupos_solo_consulta = array('vales_combustible_consulta_general');
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
                array('label' => 'Fecha Inicio', 'data' => 'fecha_inicio', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Tipo Combustible', 'data' => 'tipo_combustible', 'width' => 10),
                array('label' => 'Secretaría', 'data' => 'secretaria', 'width' => 21),
                array('label' => 'Area', 'data' => 'area', 'width' => 22),
                array('label' => 'M³/Litros Semanal', 'data' => 'metros_cubicos', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'M³/Litros Ampliación', 'data' => 'ampliacion', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Vencimiento Ampliación', 'data' => 'ampliacion_vencimiento', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Consumo Mes Actual', 'data' => 'consumo', 'width' => 9, 'class' => 'dt-body-center'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'cupos_combustible_table',
            'source_url' => 'vales_combustible/cupos_combustible/listar_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_cupos_combustible_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Cupos de Combustible';
        $data['title'] = TITLE . ' - Cupos de Combustible';
        $this->load_template('vales_combustible/cupos_combustible/cupos_combustible_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fecha = new DateTime();
        $ini_mes = clone $fecha;
        $ini_mes->modify('first day of this month');
        $fin_mes = clone $fecha;
        $fin_mes->modify('last day of this month');
        $ini_mes_sql = $ini_mes->format('Y-m-d');
        $fin_mes_sql = $fin_mes->format('Y-m-d');

        $this->load->helper('vales_combustible/datatables_functions_helper');
        $this->datatables
                ->select("vc_cupos_combustible.id, vc_cupos_combustible.fecha_inicio, vc_tipos_combustible.nombre as tipo_combustible, CONCAT(areas.codigo, ' - ', areas.nombre) as area, vc_cupos_combustible.metros_cubicos, vc_cupos_combustible.ampliacion, vc_cupos_combustible.ampliacion_vencimiento, (SELECT CONCAT(SEC.codigo, ' - ', SEC.nombre) FROM areas SEC WHERE SUBSTRING(SEC.agrupamiento, 1, 5) = SUBSTRING(areas.agrupamiento, 1, 5) AND (SUBSTRING(SEC.agrupamiento, 6, 23) = '.00.00.00.00.00.01' OR SEC.agrupamiento = '01.00.00.00.01.00.00.00') AND SEC.agrupamiento <> '01.00.00.00.00.00.00.01' GROUP BY SEC.agrupamiento) as secretaria, (SELECT SUM(metros_cubicos) FROM vc_vales WHERE vc_vales.fecha >= '$ini_mes_sql' AND vc_vales.fecha <= '$fin_mes_sql' AND vc_vales.area_id = vc_cupos_combustible.area_id AND vc_vales.tipo_combustible_id = vc_cupos_combustible.tipo_combustible_id AND vc_vales.estado != 'Anulado') as consumo, (SELECT (metros_cubicos + (CASE WHEN ampliacion_vencimiento >= '2020-01-06' THEN ampliacion ELSE 0 END)) * 4 as total FROM vc_cupos_combustible C WHERE C.id = vc_cupos_combustible.id) as cupo")
                // Oficina 103 - INTENDENCIA Agregada por agrupamiento distinto a las demás secretarías (01.00.00.00.01.00.00.00)
                // Oficina 361 - ADSCRIPTOS BOMBEROS VOLUNTARIOS DE LUJAN Quitada por agrupamiento similar a secretarias (01.01.00.00.00.00.00.01)
                ->from('vc_cupos_combustible')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_cupos_combustible.tipo_combustible_id', 'left')
                ->join('areas', 'areas.id = vc_cupos_combustible.area_id', 'left')
                ->edit_column('consumo', '$1', 'dt_column_cupos_combustible_cupos_consumo(consumo, cupo)', TRUE)
                ->add_column('ver', '<a href="vales_combustible/cupos_combustible/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/cupos_combustible/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/cupos_combustible/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('vales_combustible/cupos_combustible/listar', 'refresh');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $this->set_model_validation_rules($this->Cupos_combustible_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Cupos_combustible_model->create(array(
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'fecha_inicio' => $this->get_date_sql('fecha_inicio'),
                'area_id' => $this->input->post('area'),
                'metros_cubicos' => $this->input->post('metros_cubicos'),
                'ampliacion' => $this->input->post('ampliacion'),
                'ampliacion_vencimiento' => $this->get_date_sql('ampliacion_vencimiento')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cupos_combustible_model->get_msg());
                redirect('vales_combustible/cupos_combustible/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cupos_combustible_model->get_error())
                {
                    $error_msg .= $this->Cupos_combustible_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Cupos_combustible_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $this->Cupos_combustible_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($this->Cupos_combustible_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Cupo de Combustible';
        $data['title'] = TITLE . ' - Agregar Cupo de Combustible';
        $this->load_template('vales_combustible/cupos_combustible/cupos_combustible_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/cupos_combustible/ver/$id", 'refresh');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $cupos_combustible = $this->Cupos_combustible_model->get(array('id' => $id));
        if (empty($cupos_combustible))
        {
            show_error('No se encontró el Cupo de Combustible', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Cupos_combustible_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Cupos_combustible_model->update(array(
                    'id' => $this->input->post('id'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'fecha_inicio' => $this->get_date_sql('fecha_inicio'),
                    'area_id' => $this->input->post('area'),
                    'metros_cubicos' => $this->input->post('metros_cubicos'),
                    'ampliacion' => $this->input->post('ampliacion'),
                    'ampliacion_vencimiento' => $this->get_date_sql('ampliacion_vencimiento')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Cupos_combustible_model->get_msg());
                    redirect('vales_combustible/cupos_combustible/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Cupos_combustible_model->get_error())
                    {
                        $error_msg .= $this->Cupos_combustible_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Cupos_combustible_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $this->Cupos_combustible_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($this->Cupos_combustible_model->fields, $cupos_combustible);
        $data['cupos_combustible'] = $cupos_combustible;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Cupo de Combustible';
        $data['title'] = TITLE . ' - Editar Cupo de Combustible';
        $this->load_template('vales_combustible/cupos_combustible/cupos_combustible_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/cupos_combustible/ver/$id", 'refresh');
        }

        $cupos_combustible = $this->Cupos_combustible_model->get_one($id);
        if (empty($cupos_combustible))
        {
            show_error('No se encontró el Cupo de Combustible', 500, 'Registro no encontrado');
        }

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Cupos_combustible_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cupos_combustible_model->get_msg());
                redirect('vales_combustible/cupos_combustible/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cupos_combustible_model->get_error())
                {
                    $error_msg .= $this->Cupos_combustible_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Cupos_combustible_model->fields, $cupos_combustible, TRUE);
        $data['cupos_combustible'] = $cupos_combustible;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Cupo de Combustible';
        $data['title'] = TITLE . ' - Eliminar Cupo de Combustible';
        $this->load_template('vales_combustible/cupos_combustible/cupos_combustible_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $cupos_combustible = $this->Cupos_combustible_model->get_one($id);
        if (empty($cupos_combustible))
        {
            show_error('No se encontró el Cupo de Combustible', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Cupos_combustible_model->fields, $cupos_combustible, TRUE);
        $data['cupos_combustible'] = $cupos_combustible;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Cupo de Combustible';
        $data['title'] = TITLE . ' - Ver Cupo de Combustible';
        $this->load_template('vales_combustible/cupos_combustible/cupos_combustible_abm', $data);
    }
}
