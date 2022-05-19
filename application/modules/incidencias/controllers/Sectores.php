<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sectores extends MY_Controller
{

    /**
     * Controlador de Sectores
     * Autor: Leandro
     * Creado: 12/04/2019
     * Modificado: 14/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('incidencias/Sectores_model');
        $this->grupos_permitidos = array('admin', 'incidencias_admin', 'incidencias_consulta_general');
        $this->grupos_solo_consulta = array('incidencias_consulta_general');
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
                array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 94),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'sectores_table',
            'source_url' => 'incidencias/sectores/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_sectores_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Sectores';
        $data['title'] = TITLE . ' - Sectores';
        $this->load_template('incidencias/sectores/sectores_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, descripcion')
                ->from('in_sectores')
                ->add_column('ver', '<a href="incidencias/sectores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="incidencias/sectores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="incidencias/sectores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('incidencias/sectores/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Sectores_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Sectores_model->create(array(
                'descripcion' => $this->input->post('descripcion')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Sectores_model->get_msg());
                redirect('incidencias/sectores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Sectores_model->get_error())
                {
                    $error_msg .= $this->Sectores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Sectores_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Sector';
        $data['title'] = TITLE . ' - Agregar Sector';
        $this->load_template('incidencias/sectores/sectores_abm', $data);
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
            redirect("incidencias/sectores/ver/$id", 'refresh');
        }

        $sector = $this->Sectores_model->get(array('id' => $id));
        if (empty($sector))
        {
            show_error('No se encontró el Sector', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Sectores_model);
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
                $trans_ok &= $this->Sectores_model->update(array(
                    'id' => $this->input->post('id'),
                    'descripcion' => $this->input->post('descripcion')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Sectores_model->get_msg());
                    redirect('incidencias/sectores/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Sectores_model->get_error())
                    {
                        $error_msg .= $this->Sectores_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Sectores_model->fields, $sector);
        $data['sector'] = $sector;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Sector';
        $data['title'] = TITLE . ' - Editar Sector';
        $this->load_template('incidencias/sectores/sectores_abm', $data);
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
            redirect("incidencias/sectores/ver/$id", 'refresh');
        }

        $sector = $this->Sectores_model->get_one($id);
        if (empty($sector))
        {
            show_error('No se encontró el Sector', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Sectores_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Sectores_model->get_msg());
                redirect('incidencias/sectores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Sectores_model->get_error())
                {
                    $error_msg .= $this->Sectores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Sectores_model->fields, $sector, TRUE);
        $data['sector'] = $sector;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Sector';
        $data['title'] = TITLE . ' - Eliminar Sector';
        $this->load_template('incidencias/sectores/sectores_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $sector = $this->Sectores_model->get_one($id);
        if (empty($sector))
        {
            show_error('No se encontró el Sector', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Sectores_model->fields, $sector, TRUE);
        $data['sector'] = $sector;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Sector';
        $data['title'] = TITLE . ' - Ver Sector';
        $this->load_template('incidencias/sectores/sectores_abm', $data);
    }
}
