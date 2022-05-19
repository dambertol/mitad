<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hobbies extends MY_Controller
{

    /**
     * Controlador de Hobbies
     * Autor: Leandro
     * Creado: 12/08/2019
     * Modificado: 07/05/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('recursos_humanos/Hobbies_model');
        $this->grupos_permitidos = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_consulta_general');
        $this->grupos_solo_consulta = array('recursos_humanos_consulta_general');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 88),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'hobbies_table',
            'source_url' => 'recursos_humanos/hobbies/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_hobbies_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Hobbies';
        $data['title'] = TITLE . ' - Hobbies';
        $this->load_template('recursos_humanos/hobbies/hobbies_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre')
                ->from('rh_hobbies')
                ->add_column('ver', '<a href="recursos_humanos/hobbies/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="recursos_humanos/hobbies/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="recursos_humanos/hobbies/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('recursos_humanos/hobbies/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Hobbies_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Hobbies_model->create(array(
                'nombre' => $this->input->post('nombre')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Hobbies_model->get_msg());
                redirect('recursos_humanos/hobbies/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Hobbies_model->get_error())
                {
                    $error_msg .= $this->Hobbies_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Hobbies_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Hobby';
        $data['title'] = TITLE . ' - Agregar Hobby';
        $this->load_template('recursos_humanos/hobbies/hobbies_abm', $data);
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
            redirect("recursos_humanos/hobbies/ver/$id", 'refresh');
        }

        $hobbi = $this->Hobbies_model->get(array('id' => $id));
        if (empty($hobbi))
        {
            show_error('No se encontró el Hobby', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Hobbies_model);
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
                $trans_ok &= $this->Hobbies_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Hobbies_model->get_msg());
                    redirect('recursos_humanos/hobbies/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Hobbies_model->get_error())
                    {
                        $error_msg .= $this->Hobbies_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Hobbies_model->fields, $hobbi);
        $data['hobbi'] = $hobbi;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Hobby';
        $data['title'] = TITLE . ' - Editar Hobby';
        $this->load_template('recursos_humanos/hobbies/hobbies_abm', $data);
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
            redirect("recursos_humanos/hobbies/ver/$id", 'refresh');
        }

        $hobbi = $this->Hobbies_model->get_one($id);
        if (empty($hobbi))
        {
            show_error('No se encontró el Hobby', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Hobbies_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Hobbies_model->get_msg());
                redirect('recursos_humanos/hobbies/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Hobbies_model->get_error())
                {
                    $error_msg .= $this->Hobbies_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Hobbies_model->fields, $hobbi, TRUE);
        $data['hobbi'] = $hobbi;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Hobby';
        $data['title'] = TITLE . ' - Eliminar Hobby';
        $this->load_template('recursos_humanos/hobbies/hobbies_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $hobbi = $this->Hobbies_model->get_one($id);
        if (empty($hobbi))
        {
            show_error('No se encontró el Hobby', 500, 'Registro no encontrado');
        }
        
        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Hobbies_model->fields, $hobbi, TRUE);
        $data['hobbi'] = $hobbi;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Hobby';
        $data['title'] = TITLE . ' - Ver Hobby';
        $this->load_template('recursos_humanos/hobbies/hobbies_abm', $data);
    }
}
