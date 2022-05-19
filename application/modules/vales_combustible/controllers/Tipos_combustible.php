<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_combustible extends MY_Controller
{

    /**
     * Controlador de Tipos Combustible
     * Autor: Leandro
     * Creado: 08/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Estaciones_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->grupos_permitidos = array('admin', 'vales_combustible_consulta_general');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 60),
                array('label' => 'Estación', 'data' => 'estacion', 'width' => 34),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tipos_combustible_table',
            'source_url' => 'vales_combustible/tipos_combustible/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_tipos_combustible_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de tipos de combustible';
        $data['title'] = TITLE . ' - Tipos de combustible';
        $this->load_template('vales_combustible/tipos_combustible/tipos_combustible_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('vc_tipos_combustible.id, vc_tipos_combustible.nombre, vc_estaciones.nombre as estacion')
                ->from('vc_tipos_combustible')
                ->join('vc_estaciones', 'vc_estaciones.id = vc_tipos_combustible.estacion_id', 'left')
                ->add_column('ver', '<a href="vales_combustible/tipos_combustible/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/tipos_combustible/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/tipos_combustible/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect("vales_combustible/tipos_combustible/listar", 'refresh');
        }

        $this->array_estacion_control = $array_estacion = $this->get_array('Estaciones', 'nombre');

        $this->set_model_validation_rules($this->Tipos_combustible_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Tipos_combustible_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'estacion_id' => $this->input->post('estacion')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tipos_combustible_model->get_msg());
                redirect('vales_combustible/tipos_combustible/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tipos_combustible_model->get_error())
                {
                    $error_msg .= $this->Tipos_combustible_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Tipos_combustible_model->fields['estacion']['array'] = $array_estacion;
        $data['fields'] = $this->build_fields($this->Tipos_combustible_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar tipo combustible';
        $data['title'] = TITLE . ' - Agregar tipo combustible';
        $this->load_template('vales_combustible/tipos_combustible/tipos_combustible_abm', $data);
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
            redirect("vales_combustible/tipos_combustible/ver/$id", 'refresh');
        }

        $tipo_combustible = $this->Tipos_combustible_model->get(array('id' => $id));
        if (empty($tipo_combustible))
        {
            show_error('No se encontró el Tipo Combustible', 500, 'Registro no encontrado');
        }

        $this->array_estacion_control = $array_estacion = $this->get_array('Estaciones', 'nombre');

        $this->set_model_validation_rules($this->Tipos_combustible_model);
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
                $trans_ok &= $this->Tipos_combustible_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'estacion_id' => $this->input->post('estacion')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Tipos_combustible_model->get_msg());
                    redirect('vales_combustible/tipos_combustible/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Tipos_combustible_model->get_error())
                    {
                        $error_msg .= $this->tipos_combustible_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Tipos_combustible_model->fields['estacion']['array'] = $array_estacion;
        $data['fields'] = $this->build_fields($this->Tipos_combustible_model->fields, $tipo_combustible);
        $data['tipo_combustible'] = $tipo_combustible;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar tipo combustible';
        $data['title'] = TITLE . ' - Editar tipo combustible';
        $this->load_template('vales_combustible/tipos_combustible/tipos_combustible_abm', $data);
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
            redirect("vales_combustible/tipos_combustible/ver/$id", 'refresh');
        }

        $tipo_combustible = $this->Tipos_combustible_model->get_one($id);
        if (empty($tipo_combustible))
        {
            show_error('No se encontró el Tipo Combustible', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Tipos_combustible_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tipos_combustible_model->get_msg());
                redirect('vales_combustible/tipos_combustible/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tipos_combustible_model->get_error())
                {
                    $error_msg .= $this->Tipos_combustible_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tipos_combustible_model->fields, $tipo_combustible, TRUE);
        $data['tipo_combustible'] = $tipo_combustible;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar tipo combustible';
        $data['title'] = TITLE . ' - Eliminar tipo combustible';
        $this->load_template('vales_combustible/tipos_combustible/tipos_combustible_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tipo_combustible = $this->Tipos_combustible_model->get_one($id);
        if (empty($tipo_combustible))
        {
            show_error('No se encontró el Tipo Combustible', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Tipos_combustible_model->fields, $tipo_combustible, TRUE);
        $data['tipo_combustible'] = $tipo_combustible;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver tipo combustible';
        $data['title'] = TITLE . ' - Ver tipo combustible';
        $this->load_template('vales_combustible/tipos_combustible/tipos_combustible_abm', $data);
    }
}
