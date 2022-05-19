<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Oficinas extends MY_Controller
{

    /**
     * Controlador de Oficinas
     * Autor: Leandro
     * Creado: 23/04/2021
     * Modificado: 23/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Oficinas_model');
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
            'table_id' => 'oficinas_table',
            'source_url' => 'tramites_online/oficinas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_oficinas_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Oficinas';
        $data['title'] = TITLE . ' - Oficinas';
        $this->load_template('tramites_online/oficinas/oficinas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre, audi_usuario, audi_fecha, audi_accion')
                ->from('to2_oficinas')
                ->add_column('ver', '<a href="tramites_online/oficinas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="tramites_online/oficinas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="tramites_online/oficinas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('tramites_online/oficinas/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Oficinas_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Oficinas_model->create(
                    array(
                        'nombre' => $this->input->post('nombre')
                    ), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Oficinas_model->get_msg());
                redirect('tramites_online/oficinas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Oficinas_model->get_error())
                {
                    $error_msg .= $this->Oficinas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Oficinas_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Oficina';
        $data['title'] = TITLE . ' - Agregar Oficina';
        $this->load_template('tramites_online/oficinas/oficinas_abm', $data);
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
            redirect("tramites_online/oficinas/ver/$id", 'refresh');
        }

        $oficina = $this->Oficinas_model->get(array('id' => $id));
        if (empty($oficina))
        {
            show_error('No se encontró la Oficina', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Oficinas_model);
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
                $trans_ok &= $this->Oficinas_model->update(
                        array(
                            'id' => $this->input->post('id'),
                            'nombre' => $this->input->post('nombre')
                        ), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Oficinas_model->get_msg());
                    redirect('tramites_online/oficinas/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Oficinas_model->get_error())
                    {
                        $error_msg .= $this->Oficinas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Oficinas_model->fields, $oficina);
        $data['oficina'] = $oficina;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Oficina';
        $data['title'] = TITLE . ' - Editar Oficina';
        $this->load_template('tramites_online/oficinas/oficinas_abm', $data);
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
            redirect("tramites_online/oficinas/ver/$id", 'refresh');
        }

        $oficina = $this->Oficinas_model->get_one($id);
        if (empty($oficina))
        {
            show_error('No se encontró la Oficina', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Oficinas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Oficinas_model->get_msg());
                redirect('tramites_online/oficinas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Oficinas_model->get_error())
                {
                    $error_msg .= $this->Oficinas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Oficinas_model->fields, $oficina, TRUE);
        $data['oficina'] = $oficina;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Oficina';
        $data['title'] = TITLE . ' - Eliminar Oficina';
        $this->load_template('tramites_online/oficinas/oficinas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $oficina = $this->Oficinas_model->get_one($id);
        if (empty($oficina))
        {
            show_error('No se encontró la Oficina', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Oficinas_model->fields, $oficina, TRUE);
        $data['oficina'] = $oficina;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Oficina';
        $data['title'] = TITLE . ' - Ver Oficina';
        $this->load_template('tramites_online/oficinas/oficinas_abm', $data);
    }
}
