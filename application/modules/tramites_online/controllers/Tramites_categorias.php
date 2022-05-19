<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_categorias extends MY_Controller
{

    /**
     * Controlador de Categorías de Trámites
     * Autor: Leandro
     * Creado: 18/03/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Tramites_categorias_model');
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
            'table_id' => 'tramites_categorias_table',
            'source_url' => 'tramites_online/tramites_categorias/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_categorias_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Categorías de Consultas';
        $data['title'] = TITLE . ' - Categorías de Consultas';
        $this->load_template('tramites_online/tramites_categorias/tramites_categorias_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("to_tramites_categorias.id, to_tramites_categorias.nombre")
                ->from('to_tramites_categorias')
                ->add_column('ver', '<a href="tramites_online/tramites_categorias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="tramites_online/tramites_categorias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="tramites_online/tramites_categorias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('tramites_online/tramites_categorias/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Tramites_categorias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Tramites_categorias_model->create(array(
                'nombre' => $this->input->post('nombre')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tramites_categorias_model->get_msg());
                redirect('tramites_online/tramites_categorias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tramites_categorias_model->get_error())
                {
                    $error_msg .= $this->Tramites_categorias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tramites_categorias_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Categoría de Consulta';
        $data['title'] = TITLE . ' - Agregar Categoría de Consulta';
        $this->load_template('tramites_online/tramites_categorias/tramites_categorias_abm', $data);
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
            redirect("tramites_online/tramites_categorias/ver/$id", 'refresh');
        }

        $tramites_categoria = $this->Tramites_categorias_model->get(array('id' => $id));
        if (empty($tramites_categoria))
        {
            show_error('No se encontró la Categoría de Consulta', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Tramites_categorias_model);
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
                $trans_ok &= $this->Tramites_categorias_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Tramites_categorias_model->get_msg());
                    redirect('tramites_online/tramites_categorias/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Tramites_categorias_model->get_error())
                    {
                        $error_msg .= $this->Tramites_categorias_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tramites_categorias_model->fields, $tramites_categoria);
        $data['tramites_categoria'] = $tramites_categoria;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Categoría de Consulta';
        $data['title'] = TITLE . ' - Editar Categoría de Consulta';
        $this->load_template('tramites_online/tramites_categorias/tramites_categorias_abm', $data);
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
            redirect("tramites_online/tramites_categorias/ver/$id", 'refresh');
        }

        $tramites_categoria = $this->Tramites_categorias_model->get_one($id);
        if (empty($tramites_categoria))
        {
            show_error('No se encontró la Categoría de Consulta', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Tramites_categorias_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tramites_categorias_model->get_msg());
                redirect('tramites_online/tramites_categorias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tramites_categorias_model->get_error())
                {
                    $error_msg .= $this->Tramites_categorias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tramites_categorias_model->fields, $tramites_categoria, TRUE);
        $data['tramites_categoria'] = $tramites_categoria;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Categoría de Consulta';
        $data['title'] = TITLE . ' - Eliminar Categoría de Consulta';
        $this->load_template('tramites_online/tramites_categorias/tramites_categorias_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tramites_categoria = $this->Tramites_categorias_model->get_one($id);
        if (empty($tramites_categoria))
        {
            show_error('No se encontró la Categoría de Consulta', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Tramites_categorias_model->fields, $tramites_categoria, TRUE);
        $data['tramites_categoria'] = $tramites_categoria;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Categoría de Consulta';
        $data['title'] = TITLE . ' - Ver Categoría de Consulta';
        $this->load_template('tramites_online/tramites_categorias/tramites_categorias_abm', $data);
    }
}
