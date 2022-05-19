<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_oficinas extends MY_Controller
{

    /**
     * Controlador de Usuarios por Oficina
     * Autor: Leandro
     * Creado: 23/04/2020
     * Modificado: 23/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Oficinas_model');
        $this->load->model('tramites_online/Usuarios_oficinas_model');
        $this->load->model('Usuarios_model');
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
                array('label' => 'Legajo', 'data' => 'username', 'width' => 14),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 18),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 18),
                array('label' => 'Oficina', 'data' => 'oficina', 'width' => 44),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'usuarios_oficinas_table',
            'source_url' => 'tramites_online/usuarios_oficinas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_usuarios_oficinas_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Usuarios por Oficina';
        $data['title'] = TITLE . ' - Usuarios por Oficina';
        $this->load_template('tramites_online/usuarios_oficinas/usuarios_oficinas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('to2_usuarios_oficinas.id, users.username, personas.apellido as apellido, personas.nombre as nombre, to2_oficinas.nombre as oficina')
                ->from('to2_usuarios_oficinas')
                ->join('users', 'users.id = to2_usuarios_oficinas.user_id', 'left')
                ->join('personas', 'personas.id = users.persona_id', 'left')
                ->join('to2_oficinas', 'to2_oficinas.id = to2_usuarios_oficinas.oficina_id', 'left')
                ->add_column('ver', '<a href="tramites_online/usuarios_oficinas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="tramites_online/usuarios_oficinas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="tramites_online/usuarios_oficinas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('tramites_online/usuarios_oficinas/listar', 'refresh');
        }

        $this->array_user_control = $array_user = $this->get_array('Usuarios', 'usuario', 'id',
                array(
                    'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as usuario",
                    'join' => array(
                        array('personas', 'personas.id = users.persona_id', 'LEFT'),
                        array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                        array('groups', 'users_groups.group_id = groups.id', 'LEFT')
                    ),
                    'where' => array(
                        array('column' => 'groups.name IN', 'value' => "('tramites_online_area')", 'override' => TRUE),
                        array('column' => 'users.active', 'value' => '1'),
                    ),
                    'sort_by' => 'personas.apellido, personas.nombre, username'
                )
        );
        $this->array_oficina_control = $array_oficina = $this->get_array('Oficinas', 'nombre');

        $this->set_model_validation_rules($this->Usuarios_oficinas_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Usuarios_oficinas_model->create(
                    array(
                        'user_id' => $this->input->post('user'),
                        'oficina_id' => $this->input->post('oficina')
                    ), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Usuarios_oficinas_model->get_msg());
                redirect('tramites_online/usuarios_oficinas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Usuarios_oficinas_model->get_error())
                {
                    $error_msg .= $this->Usuarios_oficinas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Usuarios_oficinas_model->fields['user']['array'] = $array_user;
        $this->Usuarios_oficinas_model->fields['oficina']['array'] = $array_oficina;
        $data['fields'] = $this->build_fields($this->Usuarios_oficinas_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Usuario por Oficina';
        $data['title'] = TITLE . ' - Agregar Usuario por Oficina';
        $this->load_template('tramites_online/usuarios_oficinas/usuarios_oficinas_abm', $data);
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
            redirect("tramites_online/usuarios_oficinas/ver/$id", 'refresh');
        }

        $usuarios_oficina = $this->Usuarios_oficinas_model->get_one($id);
        if (empty($usuarios_oficina))
        {
            show_error('No se encontró el Usuario por Oficina', 500, 'Registro no encontrado');
        }

        unset($this->Usuarios_oficinas_model->fields['user']['input_type']);
        unset($this->Usuarios_oficinas_model->fields['user']['required']);
        $this->Usuarios_oficinas_model->fields['user']['disabled'] = TRUE;
        $this->array_oficina_control = $array_oficina = $this->get_array('Oficinas', 'nombre');

        $this->set_model_validation_rules($this->Usuarios_oficinas_model);
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
                $trans_ok &= $this->Usuarios_oficinas_model->update(
                        array(
                            'id' => $this->input->post('id'),
                            'user_id' => $usuarios_oficina->user_id,
                            'oficina_id' => $this->input->post('oficina')
                        ), FALSE);
                
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Usuarios_oficinas_model->get_msg());
                    redirect('tramites_online/usuarios_oficinas/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Usuarios_oficinas_model->get_error())
                    {
                        $error_msg .= $this->Usuarios_oficinas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Usuarios_oficinas_model->fields['oficina']['array'] = $array_oficina;
        $data['fields'] = $this->build_fields($this->Usuarios_oficinas_model->fields, $usuarios_oficina);
        $data['usuarios_oficina'] = $usuarios_oficina;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Usuario por Oficina';
        $data['title'] = TITLE . ' - Editar Usuario por Oficina';
        $this->load_template('tramites_online/usuarios_oficinas/usuarios_oficinas_abm', $data);
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
            redirect("tramites_online/usuarios_oficinas/ver/$id", 'refresh');
        }

        $usuarios_oficina = $this->Usuarios_oficinas_model->get_one($id);
        if (empty($usuarios_oficina))
        {
            show_error('No se encontró el Usuario por Oficina', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Usuarios_oficinas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Usuarios_oficinas_model->get_msg());
                redirect('tramites_online/usuarios_oficinas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Usuarios_oficinas_model->get_error())
                {
                    $error_msg .= $this->Usuarios_oficinas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Usuarios_oficinas_model->fields, $usuarios_oficina, TRUE);
        $data['usuarios_oficina'] = $usuarios_oficina;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Usuario por Oficina';
        $data['title'] = TITLE . ' - Eliminar Usuario por Oficina';
        $this->load_template('tramites_online/usuarios_oficinas/usuarios_oficinas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuarios_oficina = $this->Usuarios_oficinas_model->get_one($id);
        if (empty($usuarios_oficina))
        {
            show_error('No se encontró el Usuario por Oficina', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Usuarios_oficinas_model->fields, $usuarios_oficina, TRUE);
        $data['usuarios_oficina'] = $usuarios_oficina;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Usuario por Oficina';
        $data['title'] = TITLE . ' - Ver Usuario por Oficina';
        $this->load_template('tramites_online/usuarios_oficinas/usuarios_oficinas_abm', $data);
    }
}
