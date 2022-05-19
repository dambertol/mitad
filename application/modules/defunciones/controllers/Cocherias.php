<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cocherias extends MY_Controller
{

    /**
     * Controlador de Cocherías
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Cocherias_model');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 20),
                array('label' => 'Domicilio', 'data' => 'domicilio', 'width' => 26),
                array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 16, 'class' => 'dt-body-right'),
                array('label' => 'Email', 'data' => 'email', 'width' => 16),
                array('label' => 'Responsable', 'data' => 'responsable', 'width' => 16),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'cocherias_table',
            'source_url' => 'defunciones/cocherias/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_cocherias_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Cocherías';
        $data['title'] = TITLE . ' - Cocherías';
        $this->load_template('defunciones/cocherias/cocherias_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre, domicilio, telefono, email, responsable')
                ->from('df_cocherias')
                ->add_column('ver', '<a href="defunciones/cocherias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/cocherias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/cocherias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/cocherias/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Cocherias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Cocherias_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'domicilio' => $this->input->post('domicilio'),
                'telefono' => $this->input->post('telefono'),
                'email' => $this->input->post('email'),
                'responsable' => $this->input->post('responsable'),
                'observaciones' => $this->input->post('observaciones')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cocherias_model->get_msg());
                redirect('defunciones/cocherias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cocherias_model->get_error())
                {
                    $error_msg .= $this->Cocherias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Cocherias_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Cochería';
        $data['title'] = TITLE . ' - Agregar Cochería';
        $this->load_template('defunciones/cocherias/cocherias_abm', $data);
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
            redirect("defunciones/cocherias/ver/$id", 'refresh');
        }

        $cocheria = $this->Cocherias_model->get(array('id' => $id));
        if (empty($cocheria))
        {
            show_error('No se encontró la Cochería', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Cocherias_model);
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
                $trans_ok &= $this->Cocherias_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'domicilio' => $this->input->post('domicilio'),
                    'telefono' => $this->input->post('telefono'),
                    'email' => $this->input->post('email'),
                    'responsable' => $this->input->post('responsable'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Cocherias_model->get_msg());
                    redirect('defunciones/cocherias/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Cocherias_model->get_error())
                    {
                        $error_msg .= $this->Cocherias_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Cocherias_model->fields, $cocheria);
        $data['cocheria'] = $cocheria;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Cochería';
        $data['title'] = TITLE . ' - Editar Cochería';
        $this->load_template('defunciones/cocherias/cocherias_abm', $data);
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
            redirect("defunciones/cocherias/ver/$id", 'refresh');
        }

        $cocheria = $this->Cocherias_model->get_one($id);
        if (empty($cocheria))
        {
            show_error('No se encontró la Cochería', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Cocherias_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cocherias_model->get_msg());
                redirect('defunciones/cocherias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cocherias_model->get_error())
                {
                    $error_msg .= $this->Cocherias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Cocherias_model->fields, $cocheria, TRUE);
        $data['cocheria'] = $cocheria;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Cochería';
        $data['title'] = TITLE . ' - Eliminar Cochería';
        $this->load_template('defunciones/cocherias/cocherias_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $cocheria = $this->Cocherias_model->get_one($id);
        if (empty($cocheria))
        {
            show_error('No se encontró la Cochería', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Cocherias_model->fields, $cocheria, TRUE);
        $data['cocheria'] = $cocheria;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Cochería';
        $data['title'] = TITLE . ' - Ver Cochería';
        $this->load_template('defunciones/cocherias/cocherias_abm', $data);
    }
}
