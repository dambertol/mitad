<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Departamentos extends MY_Controller
{

    /**
     * Controlador de Departamentos
     * Autor: Leandro
     * Creado: 23/05/2018
     * Modificado: 29/09/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Departamentos_model');
        $this->load->model('Provincias_model');
        $this->grupos_permitidos = array('admin', 'consulta_general');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 41),
                array('label' => 'Código', 'data' => 'codigo', 'width' => 10),
                array('label' => 'Provincia', 'data' => 'provincia', 'width' => 40),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'departamentos_table',
            'source_url' => 'departamentos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_departamentos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Departamentos';
        $data['title'] = TITLE . ' - Departamentos';
        $this->load_template('departamentos/departamentos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('departamentos.id, departamentos.nombre, departamentos.codigo, provincias.nombre as provincia')
                ->unset_column('id')
                ->from('departamentos')
                ->join('provincias', 'provincias.id = departamentos.provincia_id', 'left')
                ->add_column('ver', '<a href="departamentos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="departamentos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="departamentos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('departamentos/listar', 'refresh');
        }

        $this->array_provincia_control = $array_provincia = $this->get_array('Provincias', 'nombre');
        $this->set_model_validation_rules($this->Departamentos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Departamentos_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'codigo' => $this->input->post('codigo'),
                'provincia_id' => $this->input->post('provincia')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Departamentos_model->get_msg());
                redirect('departamentos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Departamentos_model->get_error())
                {
                    $error_msg .= $this->Departamentos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Departamentos_model->fields['provincia']['array'] = $array_provincia;
        $data['fields'] = $this->build_fields($this->Departamentos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Departamento';
        $data['title'] = TITLE . ' - Agregar Departamento';
        $this->load_template('departamentos/departamentos_abm', $data);
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
            redirect("departamentos/ver/$id", 'refresh');
        }

        $this->array_provincia_control = $array_provincia = $this->get_array('Provincias', 'nombre');
        $departamento = $this->Departamentos_model->get(array('id' => $id));
        if (empty($departamento))
        {
            show_error('No se encontró el Departamento', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Departamentos_model);
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
                $trans_ok &= $this->Departamentos_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'codigo' => $this->input->post('codigo'),
                    'provincia_id' => $this->input->post('provincia')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Departamentos_model->get_msg());
                    redirect('departamentos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Departamentos_model->get_error())
                    {
                        $error_msg .= $this->Departamentos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Departamentos_model->fields['provincia']['array'] = $array_provincia;
        $data['fields'] = $this->build_fields($this->Departamentos_model->fields, $departamento);
        $data['departamento'] = $departamento;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Departamento';
        $data['title'] = TITLE . ' - Editar Departamento';
        $this->load_template('departamentos/departamentos_abm', $data);
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
            redirect("departamentos/ver/$id", 'refresh');
        }

        $departamento = $this->Departamentos_model->get_one($id);
        if (empty($departamento))
        {
            show_error('No se encontró el Departamento', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Departamentos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Departamentos_model->get_msg());
                redirect('departamentos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Departamentos_model->get_error())
                {
                    $error_msg .= $this->Departamentos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Departamentos_model->fields, $departamento, TRUE);
        $data['departamento'] = $departamento;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Departamento';
        $data['title'] = TITLE . ' - Eliminar Departamento';
        $this->load_template('departamentos/departamentos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $departamento = $this->Departamentos_model->get_one($id);
        if (empty($departamento))
        {
            show_error('No se encontró el Departamento', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Departamentos_model->fields, $departamento, TRUE);
        $data['departamento'] = $departamento;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Departamento';
        $data['title'] = TITLE . ' - Ver Departamento';
        $this->load_template('departamentos/departamentos_abm', $data);
    }
}
