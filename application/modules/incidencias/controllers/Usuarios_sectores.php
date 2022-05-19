<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_sectores extends MY_Controller
{

    /**
     * Controlador de Usuarios Sector
     * Autor: Leandro
     * Creado: 12/04/2019
     * Modificado: 14/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('incidencias/Usuarios_sectores_model');
        $this->load->model('Usuarios_model');
        $this->load->model('incidencias/Sectores_model');
        $this->grupos_permitidos = array('admin', 'incidencias_admin', 'incidencias_consulta_general');
        $this->grupos_solo_consulta = array('incidencias_consulta_general');
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
                array('label' => 'Sector', 'data' => 'sector', 'width' => 44),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'usuarios_sectores_table',
            'source_url' => 'incidencias/usuarios_sectores/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_usuarios_sectores_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Usuarios por Sector';
        $data['title'] = TITLE . ' - Usuarios por Sector';
        $this->load_template('incidencias/usuarios_sectores/usuarios_sectores_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('in_usuarios_sectores.id, users.username, personas.apellido as apellido, personas.nombre as nombre, in_sectores.descripcion as sector')
                ->from('in_usuarios_sectores')
                ->join('users', 'users.id = in_usuarios_sectores.user_id', 'left')
                ->join('personas', 'personas.id = users.persona_id', 'left')
                ->join('in_sectores', 'in_sectores.id = in_usuarios_sectores.sector_id', 'left')
                ->add_column('ver', '<a href="incidencias/usuarios_sectores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="incidencias/usuarios_sectores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="incidencias/usuarios_sectores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('incidencias/usuarios_sectores/listar', 'refresh');
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
                        array('column' => 'groups.name IN', 'value' => "('admin', 'incidencias_user', 'incidencias_admin')", 'override' => TRUE),
                        array('column' => 'users.active', 'value' => '1'),
                    ),
                    'sort_by' => 'personas.apellido, personas.nombre'
                )
        );
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        $this->set_model_validation_rules($this->Usuarios_sectores_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Usuarios_sectores_model->create(array(
                'user_id' => $this->input->post('user'),
                'sector_id' => $this->input->post('sector')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Usuarios_sectores_model->get_msg());
                redirect('incidencias/usuarios_sectores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Usuarios_sectores_model->get_error())
                {
                    $error_msg .= $this->Usuarios_sectores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Usuarios_sectores_model->fields['user']['array'] = $array_user;
        $this->Usuarios_sectores_model->fields['sector']['array'] = $array_sector;
        $data['fields'] = $this->build_fields($this->Usuarios_sectores_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Usuario por Sector';
        $data['title'] = TITLE . ' - Agregar Usuario por Sector';
        $this->load_template('incidencias/usuarios_sectores/usuarios_sectores_abm', $data);
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
            redirect("incidencias/usuarios_sectores/ver/$id", 'refresh');
        }

        unset($this->Usuarios_sectores_model->fields['user']['input_type']);
        unset($this->Usuarios_sectores_model->fields['user']['required']);
        $this->Usuarios_sectores_model->fields['user']['disabled'] = TRUE;
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        $usuarios_sector = $this->Usuarios_sectores_model->get_one($id);
        if (empty($usuarios_sector))
        {
            show_error('No se encontró el Usuario por Sector', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Usuarios_sectores_model);
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
                $trans_ok &= $this->Usuarios_sectores_model->update(array(
                    'id' => $this->input->post('id'),
                    'sector_id' => $this->input->post('sector')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Usuarios_sectores_model->get_msg());
                    redirect('incidencias/usuarios_sectores/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Usuarios_sectores_model->get_error())
                    {
                        $error_msg .= $this->Usuarios_sectores_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Usuarios_sectores_model->fields['sector']['array'] = $array_sector;
        $data['fields'] = $this->build_fields($this->Usuarios_sectores_model->fields, $usuarios_sector);
        $data['usuarios_sector'] = $usuarios_sector;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Usuario por Sector';
        $data['title'] = TITLE . ' - Editar Usuario por Sector';
        $this->load_template('incidencias/usuarios_sectores/usuarios_sectores_abm', $data);
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
            redirect("incidencias/usuarios_sectores/ver/$id", 'refresh');
        }

        $usuarios_sector = $this->Usuarios_sectores_model->get_one($id);
        if (empty($usuarios_sector))
        {
            show_error('No se encontró el Usuario por Sector', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Usuarios_sectores_model->delete(array('id' => $this->input->post('id')));
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Usuarios_sectores_model->get_msg());
                redirect('incidencias/usuarios_sectores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Usuarios_sectores_model->get_error())
                {
                    $error_msg .= $this->Usuarios_sectores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Usuarios_sectores_model->fields, $usuarios_sector, TRUE);
        $data['usuarios_sector'] = $usuarios_sector;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Usuario por Sector';
        $data['title'] = TITLE . ' - Eliminar Usuario por Sector';
        $this->load_template('incidencias/usuarios_sectores/usuarios_sectores_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuarios_sector = $this->Usuarios_sectores_model->get_one($id);
        if (empty($usuarios_sector))
        {
            show_error('No se encontró el Usuario por Sector', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Usuarios_sectores_model->fields, $usuarios_sector, TRUE);
        $data['usuarios_sector'] = $usuarios_sector;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Usuario por Sector';
        $data['title'] = TITLE . ' - Ver Usuario por Sector';
        $this->load_template('incidencias/usuarios_sectores/usuarios_sectores_abm', $data);
    }
}
