<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_tipos extends MY_Controller
{

    /**
     * Controlador de Tipos de Trámites
     * Autor: Leandro
     * Creado: 19/06/2018
     * Modificado: 20/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('transferencias/Tramites_tipos_model');
        $this->grupos_permitidos = array('admin', 'transferencias_consulta_general');
        $this->grupos_solo_consulta = array('transferencias_consulta_general');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 94),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_tipos_table',
            'source_url' => 'transferencias/tramites_tipos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_tipos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Tipos de Trámites';
        $data['title'] = TITLE . ' - Tipos de Trámites';
        $this->load_template('transferencias/tramites_tipos/tramites_tipos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre')
                ->from('tr_tramites_tipos')
                ->add_column('ver', '<a href="transferencias/tramites_tipos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="transferencias/tramites_tipos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="transferencias/tramites_tipos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('transferencias/tramites_tipos/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Tramites_tipos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Tramites_tipos_model->create(array(
                'nombre' => $this->input->post('nombre')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tramites_tipos_model->get_msg());
                redirect('transferencias/tramites_tipos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tramites_tipos_model->get_error())
                {
                    $error_msg .= $this->Tramites_tipos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tramites_tipos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Tipo de Trámite';
        $data['title'] = TITLE . ' - Agregar Tipo de Trámite';
        $this->load_template('transferencias/tramites_tipos/tramites_tipos_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("transferencias/tramites_tipos/ver/$id", 'refresh');
        }

        $tramites_tipo = $this->Tramites_tipos_model->get(array('id' => $id));
        if (empty($tramites_tipo))
        {
            show_error('No se encontró el Tipo de Trámite', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Tramites_tipos_model);
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
                $trans_ok &= $this->Tramites_tipos_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Tramites_tipos_model->get_msg());
                    redirect('transferencias/tramites_tipos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Tramites_tipos_model->get_error())
                    {
                        $error_msg .= $this->Tramites_tipos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tramites_tipos_model->fields, $tramites_tipo);
        $data['tramites_tipo'] = $tramites_tipo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Tipo de Trámite';
        $data['title'] = TITLE . ' - Editar Tipo de Trámite';
        $this->load_template('transferencias/tramites_tipos/tramites_tipos_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("transferencias/tramites_tipos/ver/$id", 'refresh');
        }

        $tramites_tipo = $this->Tramites_tipos_model->get_one($id);
        if (empty($tramites_tipo))
        {
            show_error('No se encontró el Tipo de Trámite', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Tramites_tipos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tramites_tipos_model->get_msg());
                redirect('transferencias/tramites_tipos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tramites_tipos_model->get_error())
                {
                    $error_msg .= $this->Tramites_tipos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tramites_tipos_model->fields, $tramites_tipo, TRUE);
        $data['tramites_tipo'] = $tramites_tipo;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Tipo de Trámite';
        $data['title'] = TITLE . ' - Eliminar Tipo de Trámite';
        $this->load_template('transferencias/tramites_tipos/tramites_tipos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramites_tipo = $this->Tramites_tipos_model->get_one($id);
        if (empty($tramites_tipo))
        {
            show_error('No se encontró el Tipo de Trámite', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Tramites_tipos_model->fields, $tramites_tipo, TRUE);
        $data['tramites_tipo'] = $tramites_tipo;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Tipo de Trámite';
        $data['title'] = TITLE . ' - Ver Tipo de Trámite';
        $this->load_template('transferencias/tramites_tipos/tramites_tipos_abm', $data);
    }
}
