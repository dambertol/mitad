<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_tipos extends MY_Controller
{

    /**
     * Controlador de Tipos de Trámites
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Areas_model');
        $this->load->model('tramites_online/Tramites_categorias_model');
        $this->load->model('tramites_online/Tramites_tipos_model');
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
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 18),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 26),
                array('label' => 'Visibilidad', 'data' => 'visibilidad', 'width' => 9),
                array('label' => 'Área', 'data' => 'area', 'width' => 23),
                array('label' => 'Email Responsable', 'data' => 'email_responsable', 'width' => 18),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tramites_tipos_table',
            'source_url' => 'tramites_online/tramites_tipos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_tramites_tipos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Tipos de Consultas';
        $data['title'] = TITLE . ' - Tipos de Consultas';
        $this->load_template('tramites_online/tramites_tipos/tramites_tipos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("to_tramites_tipos.id, to_tramites_categorias.nombre as categoria, to_tramites_tipos.nombre, to_tramites_tipos.visibilidad, CONCAT(areas.codigo, ' - ', areas.nombre) as area, to_tramites_tipos.email_responsable")
                ->from('to_tramites_tipos')
                ->join('to_tramites_categorias', 'to_tramites_categorias.id = to_tramites_tipos.categoria_id', 'left')
                ->join('areas', 'areas.id = to_tramites_tipos.area_id', 'left')
                ->add_column('ver', '<a href="tramites_online/tramites_tipos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="tramites_online/tramites_tipos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="tramites_online/tramites_tipos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('tramites_online/tramites_tipos/listar', 'refresh');
        }

        $this->array_categoria_control = $array_categoria = $this->get_array('Tramites_categorias', 'nombre');
        $this->array_visibilidad_control = $array_visibilidad = array('Público' => 'Público', 'Privado' => 'Privado');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $this->set_model_validation_rules($this->Tramites_tipos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Tramites_tipos_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'visibilidad' => $this->input->post('visibilidad'),
                'categoria_id' => $this->input->post('categoria'),
                'area_id' => $this->input->post('area'),
                'email_responsable' => $this->input->post('email_responsable')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tramites_tipos_model->get_msg());
                redirect('tramites_online/tramites_tipos/listar', 'refresh');
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

        $this->Tramites_tipos_model->fields['categoria']['array'] = $array_categoria;
        $this->Tramites_tipos_model->fields['visibilidad']['array'] = $array_visibilidad;
        $this->Tramites_tipos_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($this->Tramites_tipos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Tipo de Consulta';
        $data['title'] = TITLE . ' - Agregar Tipo de Consulta';
        $this->load_template('tramites_online/tramites_tipos/tramites_tipos_abm', $data);
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
            redirect("tramites_online/tramites_tipos/ver/$id", 'refresh');
        }

        $tramites_tipo = $this->Tramites_tipos_model->get(array('id' => $id));
        if (empty($tramites_tipo))
        {
            show_error('No se encontró el Tipo de Consulta', 500, 'Registro no encontrado');
        }

        $this->array_categoria_control = $array_categoria = $this->get_array('Tramites_categorias', 'nombre');
        $this->array_visibilidad_control = $array_visibilidad = array('Público' => 'Público', 'Privado' => 'Privado');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
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
                    'nombre' => $this->input->post('nombre'),
                    'visibilidad' => $this->input->post('visibilidad'),
                    'categoria_id' => $this->input->post('categoria'),
                    'area_id' => $this->input->post('area'),
                    'email_responsable' => $this->input->post('email_responsable')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Tramites_tipos_model->get_msg());
                    redirect('tramites_online/tramites_tipos/listar', 'refresh');
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

        $this->Tramites_tipos_model->fields['categoria']['array'] = $array_categoria;
        $this->Tramites_tipos_model->fields['visibilidad']['array'] = $array_visibilidad;
        $this->Tramites_tipos_model->fields['area']['array'] = $array_area;
        $data['fields'] = $this->build_fields($this->Tramites_tipos_model->fields, $tramites_tipo);
        $data['tramites_tipo'] = $tramites_tipo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Tipo de Consulta';
        $data['title'] = TITLE . ' - Editar Tipo de Consulta';
        $this->load_template('tramites_online/tramites_tipos/tramites_tipos_abm', $data);
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
            redirect("tramites_online/tramites_tipos/ver/$id", 'refresh');
        }

        $tramites_tipo = $this->Tramites_tipos_model->get_one($id);
        if (empty($tramites_tipo))
        {
            show_error('No se encontró el Tipo de Consulta', 500, 'Registro no encontrado');
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
                redirect('tramites_online/tramites_tipos/listar', 'refresh');
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
        $data['title_view'] = 'Eliminar Tipo de Consulta';
        $data['title'] = TITLE . ' - Eliminar Tipo de Consulta';
        $this->load_template('tramites_online/tramites_tipos/tramites_tipos_abm', $data);
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
            show_error('No se encontró el Tipo de Consulta', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Tramites_tipos_model->fields, $tramites_tipo, TRUE);
        $data['tramites_tipo'] = $tramites_tipo;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Tipo de Consulta';
        $data['title'] = TITLE . ' - Ver Tipo de Consulta';
        $this->load_template('tramites_online/tramites_tipos/tramites_tipos_abm', $data);
    }
}
