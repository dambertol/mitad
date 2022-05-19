<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Categorias extends MY_Controller
{

    /**
     * Controlador de Categorías
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 05/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('lujan_pass/Agrupamientos_model');
        $this->load->model('lujan_pass/Categorias_model');
        $this->grupos_permitidos = array('admin', 'lujan_pass_consulta_general');
        $this->grupos_solo_consulta = array('lujan_pass_consulta_general');
        $this->agrupamiento_id_turismo = '2';
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 50),
                array('label' => 'Estilo', 'data' => 'estilo', 'width' => 16),
                array('label' => 'Orden', 'data' => 'orden', 'class' => 'dt-body-right', 'width' => 8),
                array('label' => 'Agrupamiento', 'data' => 'agrupamiento', 'width' => 20),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'categorias_table',
            'source_url' => 'lujan_pass/categorias/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_categorias_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Categorías';
        $data['title'] = TITLE . ' - Categorías';
        $this->load_template('lujan_pass/categorias/categorias_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('ta_categorias.id, ta_categorias.nombre, ta_categorias.estilo, ta_categorias.orden, ta_agrupamientos.nombre as agrupamiento')
                ->unset_column('id')
                ->from('ta_categorias')
                ->join('ta_agrupamientos', 'ta_agrupamientos.id = ta_categorias.agrupamiento_id', 'left')
                ->where('ta_agrupamientos.id', $this->agrupamiento_id_turismo)
                ->add_column('ver', '<a href="lujan_pass/categorias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="lujan_pass/categorias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="lujan_pass/categorias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('lujan_pass/categorias/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Categorias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Categorias_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'estilo' => $this->input->post('estilo'),
                'orden' => $this->input->post('orden'),
                'agrupamiento_id' => $this->agrupamiento_id_turismo), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Categorias_model->get_msg());
                redirect('lujan_pass/categorias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Categorias_model->get_error())
                {
                    $error_msg .= $this->Categorias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Categorias_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Categoría';
        $data['title'] = TITLE . ' - Agregar Categoría';
        $this->load_template('lujan_pass/categorias/categorias_abm', $data);
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
            redirect("lujan_pass/categorias/ver/$id", 'refresh');
        }

        $categoria = $this->Categorias_model->get(array('id' => $id));
        if (empty($categoria || $categoria->agrupamiento_id !== $this->agrupamiento_id_turismo))
        {
            show_error('No se encontró el Categoría', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Categorias_model);
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
                $trans_ok &= $this->Categorias_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'estilo' => $this->input->post('estilo'),
                    'orden' => $this->input->post('orden'),
                    'agrupamiento_id' => $this->agrupamiento_id_turismo), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Categorias_model->get_msg());
                    redirect('lujan_pass/categorias/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Categorias_model->get_error())
                    {
                        $error_msg .= $this->Categorias_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Categorias_model->fields, $categoria);
        $data['categoria'] = $categoria;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Categoría';
        $data['title'] = TITLE . ' - Editar Categoría';
        $this->load_template('lujan_pass/categorias/categorias_abm', $data);
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
            redirect("lujan_pass/categorias/ver/$id", 'refresh');
        }

        $categoria = $this->Categorias_model->get_one($id);
        if (empty($categoria || $categoria->agrupamiento_id !== $this->agrupamiento_id_turismo))
        {
            show_error('No se encontró el Categoría', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Categorias_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Categorias_model->get_msg());
                redirect('lujan_pass/categorias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Categorias_model->get_error())
                {
                    $error_msg .= $this->Categorias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Categorias_model->fields, $categoria, TRUE);
        $data['categoria'] = $categoria;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Categoría';
        $data['title'] = TITLE . ' - Eliminar Categoría';
        $this->load_template('lujan_pass/categorias/categorias_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $categoria = $this->Categorias_model->get_one($id);
        if (empty($categoria || $categoria->agrupamiento_id !== $this->agrupamiento_id_turismo))
        {
            show_error('No se encontró el Categoría', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Categorias_model->fields, $categoria, TRUE);
        $data['categoria'] = $categoria;
        $data['txt_btn'] = NULL;
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Ver Categoría';
        $data['title'] = TITLE . ' - Ver Categoría';
        $this->load_template('lujan_pass/categorias/categorias_abm', $data);
    }
}
