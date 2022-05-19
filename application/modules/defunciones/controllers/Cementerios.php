<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cementerios extends MY_Controller
{

    /**
     * Controlador de Cementerios
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Cementerios_model');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 40),
                array('label' => 'Domicilio', 'data' => 'domicilio', 'width' => 34),
                array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 20, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'cementerios_table',
            'source_url' => 'defunciones/cementerios/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_cementerios_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Cementerios';
        $data['title'] = TITLE . ' - Cementerios';
        $this->load_template('defunciones/cementerios/cementerios_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre, domicilio, telefono')
                ->from('df_cementerios')
                ->add_column('ver', '<a href="defunciones/cementerios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/cementerios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/cementerios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/cementerios/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Cementerios_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Cementerios_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'domicilio' => $this->input->post('domicilio'),
                'telefono' => $this->input->post('telefono')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cementerios_model->get_msg());
                redirect('defunciones/cementerios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cementerios_model->get_error())
                {
                    $error_msg .= $this->Cementerios_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Cementerios_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Cementerio';
        $data['title'] = TITLE . ' - Agregar Cementerio';
        $this->load_template('defunciones/cementerios/cementerios_abm', $data);
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
            redirect("defunciones/cementerios/ver/$id", 'refresh');
        }

        $cementerio = $this->Cementerios_model->get(array('id' => $id));
        if (empty($cementerio))
        {
            show_error('No se encontró el Cementerio', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Cementerios_model);
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
                $trans_ok &= $this->Cementerios_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'domicilio' => $this->input->post('domicilio'),
                    'telefono' => $this->input->post('telefono')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Cementerios_model->get_msg());
                    redirect('defunciones/cementerios/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Cementerios_model->get_error())
                    {
                        $error_msg .= $this->Cementerios_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Cementerios_model->fields, $cementerio);
        $data['cementerio'] = $cementerio;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Cementerio';
        $data['title'] = TITLE . ' - Editar Cementerio';
        $this->load_template('defunciones/cementerios/cementerios_abm', $data);
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
            redirect("defunciones/cementerios/ver/$id", 'refresh');
        }

        $cementerio = $this->Cementerios_model->get_one($id);
        if (empty($cementerio))
        {
            show_error('No se encontró el Cementerio', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Cementerios_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cementerios_model->get_msg());
                redirect('defunciones/cementerios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cementerios_model->get_error())
                {
                    $error_msg .= $this->Cementerios_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Cementerios_model->fields, $cementerio, TRUE);
        $data['cementerio'] = $cementerio;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Cementerio';
        $data['title'] = TITLE . ' - Eliminar Cementerio';
        $this->load_template('defunciones/cementerios/cementerios_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $cementerio = $this->Cementerios_model->get_one($id);
        if (empty($cementerio))
        {
            show_error('No se encontró el Cementerio', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Cementerios_model->fields, $cementerio, TRUE);
        $data['cementerio'] = $cementerio;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Cementerio';
        $data['title'] = TITLE . ' - Ver Cementerio';
        $this->load_template('defunciones/cementerios/cementerios_abm', $data);
    }
}
