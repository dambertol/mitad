<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Motivos extends MY_Controller
{

    /**
     * Controlador de Motivos
     * Autor: Leandro
     * Creado: 24/10/2019
     * Modificado: 24/02/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
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

        $tableData = array(
            'columns' => array(
                array('label' => 'Código', 'data' => 'codigo', 'width' => 14, 'class' => 'dt-body-right'),
                array('label' => 'Motivo', 'data' => 'motivo', 'width' => 80),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'motivos_table',
            'source_url' => 'actasisp/motivos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_motivos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Motivos';
        $data['title'] = TITLE . ' - Motivos';
        $this->load_template('actasisp/motivos/motivos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, codigo, motivo')
                ->from('act_motivos')
                ->add_column('ver', '<a href="actasisp/motivos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="actasisp/motivos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="actasisp/motivos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('actasisp/motivos/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Motivos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Motivos_model->create(array(
                'codigo' => $this->input->post('codigo'),
                'motivo' => $this->input->post('motivo')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Motivos_model->get_msg());
                redirect('actasisp/motivos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Motivos_model->get_error())
                {
                    $error_msg .= $this->Motivos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Motivos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Motivo';
        $data['title'] = TITLE . ' - Agregar Motivo';
        $this->load_template('actasisp/motivos/motivos_abm', $data);
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
            redirect("actasisp/motivos/ver/$id", 'refresh');
        }

        $motivo = $this->Motivos_model->get(array('id' => $id));
        if (empty($motivo))
        {
            show_error('No se encontró el Motivo', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Motivos_model);
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
                $trans_ok &= $this->Motivos_model->update(array(
                    'id' => $this->input->post('id'),
                    'codigo' => $this->input->post('codigo'),
                    'motivo' => $this->input->post('motivo')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Motivos_model->get_msg());
                    redirect('actasisp/motivos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Motivos_model->get_error())
                    {
                        $error_msg .= $this->Motivos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Motivos_model->fields, $motivo);
        $data['motivo'] = $motivo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Motivo';
        $data['title'] = TITLE . ' - Editar Motivo';
        $this->load_template('actasisp/motivos/motivos_abm', $data);
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
            redirect("actasisp/motivos/ver/$id", 'refresh');
        }

        $motivo = $this->Motivos_model->get_one($id);
        if (empty($motivo))
        {
            show_error('No se encontró el Motivo', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Motivos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Motivos_model->get_msg());
                redirect('actasisp/motivos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Motivos_model->get_error())
                {
                    $error_msg .= $this->Motivos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Motivos_model->fields, $motivo, TRUE);
        $data['motivo'] = $motivo;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Motivo';
        $data['title'] = TITLE . ' - Eliminar Motivo';
        $this->load_template('actasisp/motivos/motivos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $motivo = $this->Motivos_model->get_one($id);
        if (empty($motivo))
        {
            show_error('No se encontró el Motivo', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Motivos_model->fields, $motivo, TRUE);
        $data['motivo'] = $motivo;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Motivo';
        $data['title'] = TITLE . ' - Ver Motivo';
        $this->load_template('actasisp/motivos/motivos_abm', $data);
    }
}
