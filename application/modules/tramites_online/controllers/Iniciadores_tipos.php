<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Iniciadores_tipos extends MY_Controller
{

    /**
     * Controlador de Tipos de Iniciadores
     * Autor: Leandro
     * Creado: 14/05/2021
     * Modificado: 14/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Iniciadores_tipos_model');
        $this->load->model('tramites_online/Procesos_model');
        $this->grupos_permitidos = array('admin', 'tramites_online_consulta_general');
        $this->grupos_solo_consulta = array('tramites_online_consulta_general');
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
            'table_id' => 'iniciadores_tipos_table',
            'source_url' => 'tramites_online/iniciadores_tipos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_iniciadores_tipos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Tipos de Iniciadores';
        $data['title'] = TITLE . ' - Tipos de Iniciadores';
        $this->load_template('tramites_online/iniciadores_tipos/iniciadores_tipos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre')
                ->from('to2_iniciadores_tipos')
                ->add_column('ver', '<a href="tramites_online/iniciadores_tipos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="tramites_online/iniciadores_tipos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="tramites_online/iniciadores_tipos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('tramites_online/iniciadores_tipos/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Iniciadores_tipos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Iniciadores_tipos_model->create(
                    array(
                        'nombre' => $this->input->post('nombre')
                    ), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Iniciadores_tipos_model->get_msg());
                redirect('tramites_online/iniciadores_tipos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Iniciadores_tipos_model->get_error())
                {
                    $error_msg .= $this->Iniciadores_tipos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Iniciadores_tipos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Tipo de Iniciador';
        $data['title'] = TITLE . ' - Agregar Tipo de Iniciador';
        $this->load_template('tramites_online/iniciadores_tipos/iniciadores_tipos_abm', $data);
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
            redirect("tramites_online/iniciadores_tipos/ver/$id", 'refresh');
        }

        $iniciadores_tipo = $this->Iniciadores_tipos_model->get(array('id' => $id));
        if (empty($iniciadores_tipo))
        {
            show_error('No se encontró el Tipo de Iniciador', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Iniciadores_tipos_model);
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
                $trans_ok &= $this->Iniciadores_tipos_model->update(
                        array(
                            'id' => $this->input->post('id'),
                            'nombre' => $this->input->post('nombre')
                        ), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Iniciadores_tipos_model->get_msg());
                    redirect('tramites_online/iniciadores_tipos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Iniciadores_tipos_model->get_error())
                    {
                        $error_msg .= $this->Iniciadores_tipos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Iniciadores_tipos_model->fields, $iniciadores_tipo);
        $data['iniciadores_tipo'] = $iniciadores_tipo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Tipo de Iniciador';
        $data['title'] = TITLE . ' - Editar Tipo de Iniciador';
        $this->load_template('tramites_online/iniciadores_tipos/iniciadores_tipos_abm', $data);
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
            redirect("tramites_online/iniciadores_tipos/ver/$id", 'refresh');
        }

        $iniciadores_tipo = $this->Iniciadores_tipos_model->get_one($id);
        if (empty($iniciadores_tipo))
        {
            show_error('No se encontró el Tipo de Iniciador', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Iniciadores_tipos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Iniciadores_tipos_model->get_msg());
                redirect('tramites_online/iniciadores_tipos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Iniciadores_tipos_model->get_error())
                {
                    $error_msg .= $this->Iniciadores_tipos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Iniciadores_tipos_model->fields, $iniciadores_tipo, TRUE);
        $data['iniciadores_tipo'] = $iniciadores_tipo;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Tipo de Iniciador';
        $data['title'] = TITLE . ' - Eliminar Tipo de Iniciador';
        $this->load_template('tramites_online/iniciadores_tipos/iniciadores_tipos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $iniciadores_tipo = $this->Iniciadores_tipos_model->get_one($id);
        if (empty($iniciadores_tipo))
        {
            show_error('No se encontró el Tipo de Iniciador', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Iniciadores_tipos_model->fields, $iniciadores_tipo, TRUE);
        $data['iniciadores_tipo'] = $iniciadores_tipo;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Tipo de Iniciador';
        $data['title'] = TITLE . ' - Ver Tipo de Iniciador';
        $this->load_template('tramites_online/iniciadores_tipos/iniciadores_tipos_abm', $data);
    }
}
