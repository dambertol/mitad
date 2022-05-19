<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Numeraciones extends MY_Controller
{

    /**
     * Controlador de Numeraciones
     * Autor: Leandro
     * Creado: 24/10/2018
     * Modificado: 20/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('transferencias/Numeraciones_model');
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
                array('label' => 'Ejercicio', 'data' => 'ejercicio', 'width' => 47, 'class' => 'dt-body-right'),
                array('label' => 'N° Inicial', 'data' => 'numero_inicial', 'width' => 47, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'numeraciones_table',
            'source_url' => 'transferencias/numeraciones/listar_data',
            'order' => array(array(0, 'asc'), array(1, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_numeraciones_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Numeraciones';
        $data['title'] = TITLE . ' - Numeraciones';
        $this->load_template('transferencias/numeraciones/numeraciones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('tr_numeraciones.id, tr_numeraciones.ejercicio, tr_numeraciones.numero_inicial')
                ->from('tr_numeraciones')
                ->add_column('ver', '<a href="transferencias/numeraciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="transferencias/numeraciones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="transferencias/numeraciones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('transferencias/numeraciones/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Numeraciones_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Numeraciones_model->create(array(
                'ejercicio' => $this->input->post('ejercicio'),
                'numero_inicial' => $this->input->post('numero_inicial')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Numeraciones_model->get_msg());
                redirect('transferencias/numeraciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Numeraciones_model->get_error())
                {
                    $error_msg .= $this->Numeraciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Numeraciones_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Numeración';
        $data['title'] = TITLE . ' - Agregar Numeración';
        $this->load_template('transferencias/numeraciones/numeraciones_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("transferencias/numeraciones/ver/$id", 'refresh');
        }

        $numeracion = $this->Numeraciones_model->get(array('id' => $id));
        if (empty($numeracion))
        {
            show_error('No se encontró la Numeración', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Numeraciones_model);
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
                $trans_ok &= $this->Numeraciones_model->update(array(
                    'id' => $this->input->post('id'),
                    'ejercicio' => $this->input->post('ejercicio'),
                    'numero_inicial' => $this->input->post('numero_inicial')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Numeraciones_model->get_msg());
                    redirect('transferencias/numeraciones/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Numeraciones_model->get_error())
                    {
                        $error_msg .= $this->Numeraciones_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Numeraciones_model->fields, $numeracion);
        $data['numeracion'] = $numeracion;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Numeración';
        $data['title'] = TITLE . ' - Editar Numeración';
        $this->load_template('transferencias/numeraciones/numeraciones_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("transferencias/numeraciones/ver/$id", 'refresh');
        }

        $numeracion = $this->Numeraciones_model->get_one($id);
        if (empty($numeracion))
        {
            show_error('No se encontró la Numeración', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Numeraciones_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Numeraciones_model->get_msg());
                redirect('transferencias/numeraciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Numeraciones_model->get_error())
                {
                    $error_msg .= $this->Numeraciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Numeraciones_model->fields, $numeracion, TRUE);
        $data['numeracion'] = $numeracion;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Numeración';
        $data['title'] = TITLE . ' - Eliminar Numeración';
        $this->load_template('transferencias/numeraciones/numeraciones_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $numeracion = $this->Numeraciones_model->get_one($id);
        if (empty($numeracion))
        {
            show_error('No se encontró la Numeración', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Numeraciones_model->fields, $numeracion, TRUE);
        $data['numeracion'] = $numeracion;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Numeración';
        $data['title'] = TITLE . ' - Ver Numeración';
        $this->load_template('transferencias/numeraciones/numeraciones_abm', $data);
    }
}
