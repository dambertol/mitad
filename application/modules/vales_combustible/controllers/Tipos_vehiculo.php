<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_vehiculo extends MY_Controller
{

    /**
     * Controlador de Tipos Vehículo
     * Autor: Leandro
     * Creado: 10/07/2018
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Tipos_vehiculo_model');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 94),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'tipos_vehiculo_table',
            'source_url' => 'vales_combustible/tipos_vehiculo/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_tipos_vehiculo_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de tipos de vehículo';
        $data['title'] = TITLE . ' - Tipos de vehículo';
        $this->load_template('vales_combustible/tipos_vehiculo/tipos_vehiculo_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre')
                ->from('vc_tipos_vehiculo')
                ->add_column('ver', '<a href="vales_combustible/tipos_vehiculo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/tipos_vehiculo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/tipos_vehiculo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect("vales_combustible/tipos_vehiculo/listar", 'refresh');
        }

        $this->set_model_validation_rules($this->Tipos_vehiculo_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Tipos_vehiculo_model->create(array(
                'nombre' => $this->input->post('nombre')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tipos_vehiculo_model->get_msg());
                redirect('vales_combustible/tipos_vehiculo/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tipos_vehiculo_model->get_error())
                {
                    $error_msg .= $this->Tipos_vehiculo_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tipos_vehiculo_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar tipo vehículo';
        $data['title'] = TITLE . ' - Agregar tipo vehículo';
        $this->load_template('vales_combustible/tipos_vehiculo/tipos_vehiculo_abm', $data);
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
            redirect("vales_combustible/tipos_vehiculo/ver/$id", 'refresh');
        }

        $tipo_vehiculo = $this->Tipos_vehiculo_model->get(array('id' => $id));
        if (empty($tipo_vehiculo))
        {
            show_error('No se encontró el Tipo Vehículo', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Tipos_vehiculo_model);
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
                $trans_ok &= $this->Tipos_vehiculo_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Tipos_vehiculo_model->get_msg());
                    redirect('vales_combustible/tipos_vehiculo/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Tipos_vehiculo_model->get_error())
                    {
                        $error_msg .= $this->tipos_vehiculo_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tipos_vehiculo_model->fields, $tipo_vehiculo);
        $data['tipo_vehiculo'] = $tipo_vehiculo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar tipo vehículo';
        $data['title'] = TITLE . ' - Editar tipo vehículo';
        $this->load_template('vales_combustible/tipos_vehiculo/tipos_vehiculo_abm', $data);
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
            redirect("vales_combustible/tipos_vehiculo/ver/$id", 'refresh');
        }

        $tipo_vehiculo = $this->Tipos_vehiculo_model->get_one($id);
        if (empty($tipo_vehiculo))
        {
            show_error('No se encontró el Tipo Vehículo', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Tipos_vehiculo_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Tipos_vehiculo_model->get_msg());
                redirect('vales_combustible/tipos_vehiculo/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Tipos_vehiculo_model->get_error())
                {
                    $error_msg .= $this->Tipos_vehiculo_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Tipos_vehiculo_model->fields, $tipo_vehiculo, TRUE);
        $data['tipo_vehiculo'] = $tipo_vehiculo;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar tipo vehículo';
        $data['title'] = TITLE . ' - Eliminar tipo vehículo';
        $this->load_template('vales_combustible/tipos_vehiculo/tipos_vehiculo_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tipo_vehiculo = $this->Tipos_vehiculo_model->get_one($id);
        if (empty($tipo_vehiculo))
        {
            show_error('No se encontró el Tipo Vehículo', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Tipos_vehiculo_model->fields, $tipo_vehiculo, TRUE);
        $data['tipo_vehiculo'] = $tipo_vehiculo;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver tipo vehículo';
        $data['title'] = TITLE . ' - Ver tipo vehículo';
        $this->load_template('vales_combustible/tipos_vehiculo/tipos_vehiculo_abm', $data);
    }
}
