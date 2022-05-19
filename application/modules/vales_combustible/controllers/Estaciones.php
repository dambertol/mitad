<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estaciones extends MY_Controller
{

    /**
     * Controlador de Estaciones
     * Autor: Leandro
     * Creado: 03/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Estaciones_model');
        $this->grupos_permitidos = array('admin', 'vales_combustible_contaduria', 'vales_combustible_consulta_general');
        $this->grupos_solo_consulta = array('vales_combustible_consulta_general');
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
            'table_id' => 'estaciones_table',
            'source_url' => 'vales_combustible/estaciones/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_estaciones_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de estaciones';
        $data['title'] = TITLE . ' - Estaciones';
        $this->load_template('vales_combustible/estaciones/estaciones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre')
                ->from('vc_estaciones')
                ->add_column('ver', '<a href="vales_combustible/estaciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/estaciones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/estaciones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect("vales_combustible/estaciones/listar", 'refresh');
        }

        $this->set_model_validation_rules($this->Estaciones_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Estaciones_model->create(array(
                'nombre' => $this->input->post('nombre')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Estaciones_model->get_msg());
                redirect('vales_combustible/estaciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Estaciones_model->get_error())
                {
                    $error_msg .= $this->Estaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Estaciones_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar estación';
        $data['title'] = TITLE . ' - Agregar estación';
        $this->load_template('vales_combustible/estaciones/estaciones_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/estaciones/ver/$id", 'refresh');
        }

        $estacion = $this->Estaciones_model->get(array('id' => $id));
        if (empty($estacion))
        {
            show_error('No se encontró la Estación', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Estaciones_model);
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
                $trans_ok &= $this->Estaciones_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Estaciones_model->get_msg());
                    redirect('vales_combustible/estaciones/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Estaciones_model->get_error())
                    {
                        $error_msg .= $this->Estaciones_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Estaciones_model->fields, $estacion);
        $data['estacion'] = $estacion;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar estación';
        $data['title'] = TITLE . ' - Editar estación';
        $this->load_template('vales_combustible/estaciones/estaciones_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/estaciones/ver/$id", 'refresh');
        }

        $estacion = $this->Estaciones_model->get_one($id);
        if (empty($estacion))
        {
            show_error('No se encontró la Estación', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Estaciones_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Estaciones_model->get_msg());
                redirect('vales_combustible/estaciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Estaciones_model->get_error())
                {
                    $error_msg .= $this->Estaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Estaciones_model->fields, $estacion, TRUE);
        $data['estacion'] = $estacion;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar estación';
        $data['title'] = TITLE . ' - Eliminar estación';
        $this->load_template('vales_combustible/estaciones/estaciones_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $estacion = $this->Estaciones_model->get_one($id);
        if (empty($estacion))
        {
            show_error('No se encontró la Estación', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Estaciones_model->fields, $estacion, TRUE);
        $data['estacion'] = $estacion;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver estación';
        $data['title'] = TITLE . ' - Ver estación';
        $this->load_template('vales_combustible/estaciones/estaciones_abm', $data);
    }
}
