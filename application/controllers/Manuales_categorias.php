<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manuales_categorias extends MY_Controller
{

    /**
     * Controlador de Categorías Manuales
     * Autor: Leandro
     * Creado: 02/06/2020
     * Modificado: 05/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Manuales_categorias_model');
        $this->grupos_permitidos = array('admin', 'admin_manuales', 'consulta_general');
        $this->grupos_solo_consulta = array('consulta_general');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 91),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'manuales_categorias_table',
            'source_url' => 'manuales_categorias/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_manuales_categorias_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Categorías Manuales';
        $data['title'] = TITLE . ' - Categorías Manuales';
        $this->load_template('manuales_categorias/manuales_categorias_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre')
                ->from('manuales_categorias')
                ->add_column('ver', '<a href="manuales_categorias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="manuales_categorias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="manuales_categorias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('manuales_categorias/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Manuales_categorias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Manuales_categorias_model->create(array(
                'nombre' => $this->input->post('nombre')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Manuales_categorias_model->get_msg());
                redirect('manuales_categorias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Manuales_categorias_model->get_error())
                {
                    $error_msg .= $this->Manuales_categorias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Manuales_categorias_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Categoría Manuales';
        $data['title'] = TITLE . ' - Agregar Categoría Manuales';
        $this->load_template('manuales_categorias/manuales_categorias_abm', $data);
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
            redirect("manuales_categorias/ver/$id", 'refresh');
        }

        $manuales_categoria = $this->Manuales_categorias_model->get(array('id' => $id));
        if (empty($manuales_categoria))
        {
            show_error('No se encontró el Categoría Manuales', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Manuales_categorias_model);
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
                $trans_ok &= $this->Manuales_categorias_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Manuales_categorias_model->get_msg());
                    redirect('manuales_categorias/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Manuales_categorias_model->get_error())
                    {
                        $error_msg .= $this->Manuales_categorias_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Manuales_categorias_model->fields, $manuales_categoria);
        $data['manuales_categoria'] = $manuales_categoria;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Categoría Manuales';
        $data['title'] = TITLE . ' - Editar Categoría Manuales';
        $this->load_template('manuales_categorias/manuales_categorias_abm', $data);
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
            redirect("manuales_categorias/ver/$id", 'refresh');
        }

        $manuales_categoria = $this->Manuales_categorias_model->get_one($id);
        if (empty($manuales_categoria))
        {
            show_error('No se encontró el Categoría Manuales', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Manuales_categorias_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Manuales_categorias_model->get_msg());
                redirect('manuales_categorias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Manuales_categorias_model->get_error())
                {
                    $error_msg .= $this->Manuales_categorias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Manuales_categorias_model->fields, $manuales_categoria, TRUE);
        $data['manuales_categoria'] = $manuales_categoria;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Categoría Manuales';
        $data['title'] = TITLE . ' - Eliminar Categoría Manuales';
        $this->load_template('manuales_categorias/manuales_categorias_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $manuales_categoria = $this->Manuales_categorias_model->get_one($id);
        if (empty($manuales_categoria))
        {
            show_error('No se encontró el Categoría Manuales', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Manuales_categorias_model->fields, $manuales_categoria, TRUE);
        $data['manuales_categoria'] = $manuales_categoria;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Categoría Manuales';
        $data['title'] = TITLE . ' - Ver Categoría Manuales';
        $this->load_template('manuales_categorias/manuales_categorias_abm', $data);
    }
}
