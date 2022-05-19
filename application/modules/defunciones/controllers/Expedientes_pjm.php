<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expedientes_pjm extends MY_Controller
{

    /**
     * Controlador de Expedientes PJM
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Expedientes_pjm_model');
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

        $tableData = array(
            'columns' => array(
                array('label' => 'ID', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Fecha Pago', 'data' => 'fecha_pago', 'width' => 9, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Año', 'data' => 'anio', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Mes', 'data' => 'mes', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Expediente', 'data' => 'expediente', 'width' => 9, 'class' => 'dt-body-right'),
                array('label' => 'Monto UT', 'data' => 'monto_ut', 'width' => 9, 'render' => 'numeric', 'class' => 'dt-body-right'),
                array('label' => 'Monto $', 'data' => 'monto_pesos', 'width' => 9, 'render' => 'money', 'class' => 'dt-body-right'),
                array('label' => 'Licencias', 'data' => 'licencias', 'width' => 10, 'render' => 'numeric', 'class' => 'dt-body-right'),
                array('label' => 'Parcelas 5.3%', 'data' => 'parcelas', 'width' => 10, 'render' => 'numeric', 'class' => 'dt-body-right'),
                array('label' => 'Cheque Nro', 'data' => 'cheque', 'width' => 13, 'class' => 'dt-body-right'),
                array('label' => 'Boleta Pago', 'data' => 'boleta_pago', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'expedientes_pjm_table',
            'source_url' => 'defunciones/expedientes_pjm/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_expedientes_pjm_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Expedientes PJM';
        $data['title'] = TITLE . ' - Expedientes PJM';
        $this->load_template('defunciones/expedientes_pjm/expedientes_pjm_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, anio, mes, licencias, parcelas, monto_ut, monto_pesos, fecha_pago, cheque, boleta_pago, expediente')
                ->from('df_expedientes_pjm')
                ->add_column('ver', '<a href="defunciones/expedientes_pjm/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/expedientes_pjm/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/expedientes_pjm/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/expedientes_pjm/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Expedientes_pjm_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Expedientes_pjm_model->create(array(
                'anio' => $this->input->post('anio'),
                'mes' => $this->input->post('mes'),
                'licencias' => $this->input->post('licencias'),
                'parcelas' => $this->input->post('parcelas'),
                'monto_ut' => $this->input->post('monto_ut'),
                'monto_pesos' => $this->input->post('monto_pesos'),
                'fecha_pago' => $this->get_datetime_sql('fecha_pago'),
                'cheque' => $this->input->post('cheque'),
                'boleta_pago' => $this->input->post('boleta_pago'),
                'expediente' => $this->input->post('expediente')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Expedientes_pjm_model->get_msg());
                redirect('defunciones/expedientes_pjm/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Expedientes_pjm_model->get_error())
                {
                    $error_msg .= $this->Expedientes_pjm_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Expedientes_pjm_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Expediente PJM';
        $data['title'] = TITLE . ' - Agregar Expediente PJM';
        $this->load_template('defunciones/expedientes_pjm/expedientes_pjm_abm', $data);
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
            redirect("defunciones/expedientes_pjm/ver/$id", 'refresh');
        }

        $expedientes_pj = $this->Expedientes_pjm_model->get(array('id' => $id));
        if (empty($expedientes_pj))
        {
            show_error('No se encontró el Expediente PJM', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Expedientes_pjm_model);
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
                $trans_ok &= $this->Expedientes_pjm_model->update(array(
                    'id' => $this->input->post('id'),
                    'anio' => $this->input->post('anio'),
                    'mes' => $this->input->post('mes'),
                    'licencias' => $this->input->post('licencias'),
                    'parcelas' => $this->input->post('parcelas'),
                    'monto_ut' => $this->input->post('monto_ut'),
                    'monto_pesos' => $this->input->post('monto_pesos'),
                    'fecha_pago' => $this->get_datetime_sql('fecha_pago'),
                    'cheque' => $this->input->post('cheque'),
                    'boleta_pago' => $this->input->post('boleta_pago'),
                    'expediente' => $this->input->post('expediente')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Expedientes_pjm_model->get_msg());
                    redirect('defunciones/expedientes_pjm/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Expedientes_pjm_model->get_error())
                    {
                        $error_msg .= $this->Expedientes_pjm_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Expedientes_pjm_model->fields, $expedientes_pj);
        $data['expedientes_pj'] = $expedientes_pj;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Expediente PJM';
        $data['title'] = TITLE . ' - Editar Expediente PJM';
        $this->load_template('defunciones/expedientes_pjm/expedientes_pjm_abm', $data);
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
            redirect("defunciones/expedientes_pjm/ver/$id", 'refresh');
        }

        $expedientes_pj = $this->Expedientes_pjm_model->get_one($id);
        if (empty($expedientes_pj))
        {
            show_error('No se encontró el Expediente PJM', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Expedientes_pjm_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Expedientes_pjm_model->get_msg());
                redirect('defunciones/expedientes_pjm/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Expedientes_pjm_model->get_error())
                {
                    $error_msg .= $this->Expedientes_pjm_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Expedientes_pjm_model->fields, $expedientes_pj, TRUE);
        $data['expedientes_pj'] = $expedientes_pj;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Expediente PJM';
        $data['title'] = TITLE . ' - Eliminar Expediente PJM';
        $this->load_template('defunciones/expedientes_pjm/expedientes_pjm_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $expedientes_pj = $this->Expedientes_pjm_model->get_one($id);
        if (empty($expedientes_pj))
        {
            show_error('No se encontró el Expediente PJM', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Expedientes_pjm_model->fields, $expedientes_pj, TRUE);
        $data['expedientes_pj'] = $expedientes_pj;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Expediente PJM';
        $data['title'] = TITLE . ' - Ver Expediente PJM';
        $this->load_template('defunciones/expedientes_pjm/expedientes_pjm_abm', $data);
    }
}
