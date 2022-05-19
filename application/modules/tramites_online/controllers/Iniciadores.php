<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Iniciadores extends MY_Controller
{

    /**
     * Controlador de Iniciadores
     * Autor: Leandro
     * Creado: 13/06/2021
     * Modificado: 13/06/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Iniciadores_model');
        $this->load->model('tramites_online/Iniciadores_tipos_model');
        $this->load->model('Personas_model');
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
                array('label' => 'CUIL', 'data' => 'cuil', 'width' => 14, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 30),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 30),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 20),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'iniciadores_table',
            'source_url' => 'tramites_online/iniciadores/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_iniciadores_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Iniciadores';
        $data['title'] = TITLE . ' - Iniciadores';
        $this->load_template('tramites_online/iniciadores/iniciadores_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $dt = $this->datatables
                ->select('to2_iniciadores.id, personas.cuil as cuil, personas.nombre as nombre, personas.apellido as apellido, to2_iniciadores_tipos.nombre as tipo')
                ->from('to2_iniciadores')
                ->join('personas', 'personas.id = to2_iniciadores.persona_id', 'left')
                ->join('to2_iniciadores_tipos', 'to2_iniciadores_tipos.id = to2_iniciadores.tipo_id', 'left')
                ->add_column('ver', '<a href="tramites_online/iniciadores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="tramites_online/iniciadores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="tramites_online/iniciadores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $dt->generate();
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
            redirect('tramites_online/iniciadores/listar', 'refresh');
        }

        $this->array_persona_control = $array_persona = $this->get_array('Personas', 'persona', 'id', array(
            'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as persona",
            'where' => array(
                "id NOT IN (SELECT to2_iniciadores.persona_id FROM to2_iniciadores) AND " //Personas sin iniciador
                . "(id NOT IN (SELECT persona_id FROM users WHERE persona_id IS NOT NULL) OR id IN (SELECT persona_id FROM users LEFT JOIN users_groups ON users.id = users_groups.user_id LEFT JOIN `groups` ON users_groups.group_id = `groups`.id WHERE `groups`.name = 'tramites_online_publico'))"), //Personas sin usuario o con usuario "tramites_online_publico"
            'sort_by' => 'personas.apellido, personas.nombre'));
        $this->array_tipo_control = $array_tipo = $this->get_array('Iniciadores_tipos', 'nombre');

        $this->set_model_validation_rules($this->Iniciadores_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Iniciadores_model->create(
                    array(
                        'persona_id' => $this->input->post('persona'),
                        'tipo_id' => $this->input->post('tipo')
                    ), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Iniciadores_model->get_msg());
                redirect('tramites_online/iniciadores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Iniciadores_model->get_error())
                {
                    $error_msg .= $this->Iniciadores_model->get_error();
                }
                if ($this->ion_auth->errors())
                {
                    $error_msg .= $this->ion_auth->errors();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Iniciadores_model->fields['tipo']['array'] = $array_tipo;
        $this->Iniciadores_model->fields['persona']['array'] = $array_persona;
        $data['fields'] = $this->build_fields($this->Iniciadores_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Iniciador';
        $data['title'] = TITLE . ' - Agregar Iniciador';
        $this->load_template('tramites_online/iniciadores/iniciadores_abm', $data);
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
            redirect("tramites_online/iniciadores/ver/$id", 'refresh');
        }

        $iniciador = $this->Iniciadores_model->get_one($id);
        if (empty($iniciador))
        {
            show_error('No se encontró el Iniciador', 500, 'Registro no encontrado');
        }

        $iniciadores_model = $this->Iniciadores_model;
        unset($iniciadores_model->fields['persona']['input_type']);
        unset($iniciadores_model->fields['persona']['required']);
        $iniciadores_model->fields['persona']['readonly'] = 'readonly';
        $this->array_tipo_control = $array_tipo = $this->get_array('Iniciadores_tipos', 'nombre');

        $this->set_model_validation_rules($this->Iniciadores_model);
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

                $trans_ok &= $this->Iniciadores_model->update(
                        array(
                            'id' => $this->input->post('id'),
                            'tipo_id' => $this->input->post('tipo'),
                        ), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Iniciadores_model->get_msg());
                    redirect('tramites_online/iniciadores/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Iniciadores_model->get_error())
                    {
                        $error_msg .= $this->Iniciadores_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Iniciadores_model->fields['tipo']['array'] = $array_tipo;
        $data['fields'] = $this->build_fields($this->Iniciadores_model->fields, $iniciador);
        $data['iniciador'] = $iniciador;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Iniciador';
        $data['title'] = TITLE . ' - Editar Iniciador';
        $this->load_template('tramites_online/iniciadores/iniciadores_abm', $data);
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
            redirect("tramites_online/iniciadores/ver/$id", 'refresh');
        }

        $iniciador = $this->Iniciadores_model->get_one($id);
        if (empty($iniciador))
        {
            show_error('No se encontró el Iniciador', 500, 'Registro no encontrado');
        }

        $iniciadores_model = $this->Iniciadores_model;
        unset($iniciadores_model->fields['persona']['input_type']);
        unset($iniciadores_model->fields['persona']['required']);
        $iniciadores_model->fields['persona']['readonly'] = 'readonly';

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Iniciadores_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Iniciadores_model->get_msg());
                redirect('tramites_online/iniciadores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Iniciadores_model->get_error())
                {
                    $error_msg .= $this->Iniciadores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Iniciadores_model->fields, $iniciador, TRUE);
        $data['iniciador'] = $iniciador;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Iniciador';
        $data['title'] = TITLE . ' - Eliminar Iniciador';
        $this->load_template('tramites_online/iniciadores/iniciadores_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $iniciador = $this->Iniciadores_model->get_one($id);
        if (empty($iniciador))
        {
            show_error('No se encontró el Iniciador', 500, 'Registro no encontrado');
        }

        $iniciadores_model = $this->Iniciadores_model;
        unset($iniciadores_model->fields['persona']['input_type']);
        unset($iniciadores_model->fields['persona']['required']);
        $iniciadores_model->fields['persona']['readonly'] = 'readonly';

        $data['error'] = $this->session->flashdata('error');

        $data['fields'] = $this->build_fields($this->Iniciadores_model->fields, $iniciador, TRUE);
        $data['iniciador'] = $iniciador;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Iniciador';
        $data['title'] = TITLE . ' - Ver Iniciador';
        $this->load_template('tramites_online/iniciadores/iniciadores_abm', $data);
    }
}
