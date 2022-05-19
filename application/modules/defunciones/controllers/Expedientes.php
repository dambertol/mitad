<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expedientes extends MY_Controller
{

    /**
     * Controlador de Expedientes
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Expedientes_model');
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
                array('label' => 'Matrícula', 'data' => 'matricula', 'width' => 22, 'class' => 'dt-body-right'),
                array('label' => 'Ejercicio', 'data' => 'ejercicio', 'width' => 25, 'class' => 'dt-body-right'),
                array('label' => 'Numero', 'data' => 'numero', 'width' => 25, 'class' => 'dt-body-right'),
                array('label' => 'Letra', 'data' => 'letra', 'width' => 22),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'expedientes_table',
            'source_url' => 'defunciones/expedientes/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_expedientes_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Expedientes';
        $data['title'] = TITLE . ' - Expedientes';
        $this->load_template('defunciones/expedientes/expedientes_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, matricula, ejercicio, numero, letra')
                ->from('df_expedientes')
                ->add_column('ver', '<a href="defunciones/expedientes/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/expedientes/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/expedientes/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/expedientes/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Expedientes_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Expedientes_model->create(array(
                'matricula' => $this->input->post('matricula'),
                'ejercicio' => $this->input->post('ejercicio'),
                'numero' => $this->input->post('numero'),
                'letra' => $this->input->post('letra')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Expedientes_model->get_msg());
                redirect('defunciones/expedientes/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Expedientes_model->get_error())
                {
                    $error_msg .= $this->Expedientes_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Expedientes_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Expediente';
        $data['title'] = TITLE . ' - Agregar Expediente';
        $this->load_template('defunciones/expedientes/expedientes_abm', $data);
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
            redirect("defunciones/expedientes/ver/$id", 'refresh');
        }

        $expedient = $this->Expedientes_model->get(array('id' => $id));
        if (empty($expedient))
        {
            show_error('No se encontró el Expediente', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Expedientes_model);
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
                $trans_ok &= $this->Expedientes_model->update(array(
                    'id' => $this->input->post('id'),
                    'matricula' => $this->input->post('matricula'),
                    'ejercicio' => $this->input->post('ejercicio'),
                    'numero' => $this->input->post('numero'),
                    'letra' => $this->input->post('letra')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Expedientes_model->get_msg());
                    redirect('defunciones/expedientes/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Expedientes_model->get_error())
                    {
                        $error_msg .= $this->Expedientes_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Expedientes_model->fields, $expedient);
        $data['expedient'] = $expedient;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Expediente';
        $data['title'] = TITLE . ' - Editar Expediente';
        $this->load_template('defunciones/expedientes/expedientes_abm', $data);
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
            redirect("defunciones/expedientes/ver/$id", 'refresh');
        }

        $expedient = $this->Expedientes_model->get_one($id);
        if (empty($expedient))
        {
            show_error('No se encontró el Expediente', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Expedientes_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Expedientes_model->get_msg());
                redirect('defunciones/expedientes/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Expedientes_model->get_error())
                {
                    $error_msg .= $this->Expedientes_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Expedientes_model->fields, $expedient, TRUE);
        $data['expedient'] = $expedient;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Expediente';
        $data['title'] = TITLE . ' - Eliminar Expediente';
        $this->load_template('defunciones/expedientes/expedientes_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $expedient = $this->Expedientes_model->get_one($id);
        if (empty($expedient))
        {
            show_error('No se encontró el Expediente', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Expedientes_model->fields, $expedient, TRUE);
        $data['expedient'] = $expedient;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Expediente';
        $data['title'] = TITLE . ' - Ver Expediente';
        $this->load_template('defunciones/expedientes/expedientes_abm', $data);
    }
}
