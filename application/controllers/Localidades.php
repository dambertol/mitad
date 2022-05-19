<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Localidades extends MY_Controller
{

    /**
     * Controlador de Localidades
     * Autor: Leandro
     * Creado: 23/05/2018
     * Modificado: 01/10/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Localidades_model');
        $this->load->model('Departamentos_model');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 31),
                array('label' => 'Código', 'data' => 'codigo', 'width' => 10),
                array('label' => 'Departamento', 'data' => 'departamento', 'width' => 25),
                array('label' => 'Provincia', 'data' => 'provincia', 'width' => 25),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'localidades_table',
            'source_url' => 'localidades/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_localidades_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Localidades';
        $data['title'] = TITLE . ' - Localidades';
        $this->load_template('localidades/localidades_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('localidades.id, localidades.nombre, localidades.codigo, departamentos.nombre as departamento, provincias.nombre as provincia')
                ->unset_column('id')
                ->from('localidades')
                ->join('departamentos', 'departamentos.id = localidades.departamento_id', 'left')
                ->join('provincias', 'provincias.id = departamentos.provincia_id', 'left')
                ->add_column('ver', '<a href="localidades/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="localidades/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="localidades/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('localidades/listar', 'refresh');
        }

        $this->array_departamento_control = $array_departamento = $this->get_array('Departamentos', 'departamento', 'id', array('select' => "departamentos.id, CONCAT(departamentos.nombre, ' - ', provincias.nombre) as departamento", 'join' => array(array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'))));
        $this->set_model_validation_rules($this->Localidades_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Localidades_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'codigo' => $this->input->post('codigo'),
                'departamento_id' => $this->input->post('departamento')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Localidades_model->get_msg());
                redirect('localidades/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Localidades_model->get_error())
                {
                    $error_msg .= $this->Localidades_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Localidades_model->fields['departamento']['array'] = $array_departamento;
        $data['fields'] = $this->build_fields($this->Localidades_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Localidad';
        $data['title'] = TITLE . ' - Agregar Localidad';
        $this->load_template('localidades/localidades_abm', $data);
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
            redirect("localidades/ver/$id", 'refresh');
        }

        $this->array_departamento_control = $array_departamento = $this->get_array('Departamentos', 'departamento', 'id', array('select' => "departamentos.id, CONCAT(departamentos.nombre, ' - ', provincias.nombre) as departamento", 'join' => array(array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT'))));
        $localidad = $this->Localidades_model->get(array('id' => $id));
        if (empty($localidad))
        {
            show_error('No se encontró el Localidad', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Localidades_model);
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
                $trans_ok &= $this->Localidades_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'codigo' => $this->input->post('codigo'),
                    'departamento_id' => $this->input->post('departamento')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Localidades_model->get_msg());
                    redirect('localidades/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Localidades_model->get_error())
                    {
                        $error_msg .= $this->Localidades_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Localidades_model->fields['departamento']['array'] = $array_departamento;
        $data['fields'] = $this->build_fields($this->Localidades_model->fields, $localidad);
        $data['localidad'] = $localidad;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Localidad';
        $data['title'] = TITLE . ' - Editar Localidad';
        $this->load_template('localidades/localidades_abm', $data);
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
            redirect("localidades/ver/$id", 'refresh');
        }

        $localidad = $this->Localidades_model->get_one($id);
        if (empty($localidad))
        {
            show_error('No se encontró el Localidad', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Localidades_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Localidades_model->get_msg());
                redirect('localidades/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Localidades_model->get_error())
                {
                    $error_msg .= $this->Localidades_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Localidades_model->fields, $localidad, TRUE);
        $data['localidad'] = $localidad;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Localidad';
        $data['title'] = TITLE . ' - Eliminar Localidad';
        $this->load_template('localidades/localidades_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $localidad = $this->Localidades_model->get_one($id);
        if (empty($localidad))
        {
            show_error('No se encontró el Localidad', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Localidades_model->fields, $localidad, TRUE);
        $data['localidad'] = $localidad;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Localidad';
        $data['title'] = TITLE . ' - Ver Localidad';
        $this->load_template('localidades/localidades_abm', $data);
    }
}
