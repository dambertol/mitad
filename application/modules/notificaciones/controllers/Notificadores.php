<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notificadores extends MY_Controller
{

    /**
     * Controlador de Notificaciones
     * Autor: GENERATOR_MLC
     * Creado: 02/07/2019
     * Modificado: 02/07/2019 (GENERATOR_MLC)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notificaciones/Notificadores_model');
        $this->load->model('notificaciones/Zonas_model');
        $this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_consulta_general');
        $this->grupos_solo_consulta = array('notificaciones_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(//@todo arreglar anchos de columnas
                array('label' => 'Id', 'data' => 'id', 'width' => 10),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 10),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 10),
                array('label' => 'Documento', 'data' => 'documento', 'width' => 10),
                array('label' => 'Telefono', 'data' => 'telefono', 'width' => 10),
                array('label' => 'Email', 'data' => 'email', 'width' => 10),
                array('label' => 'Zona', 'data' => 'zona', 'width' => 10),
//				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
//				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
				array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
				array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'notificadores_table',
            'source_url' => 'notificaciones/notificadores/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_notificadores_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Notificadores';
        $data['title'] = TITLE . ' - Notificadores';
        $this->load_template('notificaciones/notificadores/notificadores_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
            ->select('nv_notificadores.id, nombre, apellido, documento, telefono, email, nv_zonas.descripcion as zona')//, audi_usuario, audi_fecha, audi_accion')
            ->from('nv_notificadores')
            ->join('nv_zonas', 'nv_notificadores.zona_id = nv_zonas.id', 'left')
            ->add_column('ver', '<a href="notificaciones/notificadores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            ->add_column('editar', '<a href="notificaciones/notificadores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            ->add_column('eliminar', '<a href="notificaciones/notificadores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');


        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('notificaciones/notificadores/listar', 'refresh');
        }

        // $this->array_zona_control = $this->Zonas_model->get();

        $this->set_model_validation_rules($this->Notificadores_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE) {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Notificadores_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'apellido' => $this->input->post('apellido'),
                'documento' => $this->input->post('documento'),
                'telefono' => $this->input->post('telefono'),
                'email' => $this->input->post('email'),
                'zona_id' => $this->input->post('zona'),
//				'audi_usuario' => $this->input->post('audi_usuario'),
//				'audi_fecha' => $this->input->post('audi_fecha'),
//				'audi_accion' => $this->input->post('audi_accion')
            ), FALSE);
            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Notificadores_model->get_msg());
                redirect('notificaciones/notificadores/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Notificadores_model->get_error()) {
                    $error_msg .= $this->Notificadores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['zonas'] = $this->Zonas_model->get();
        $data['fields'] = $this->build_fields($this->Notificadores_model->fields);

        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Notificador';
        $data['title'] = TITLE . ' - Agregar Notificador';
        $this->load_template('notificaciones/notificadores/notificadores_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/notificadores/ver/$id", 'refresh');
        }

        $notificador = $this->Notificadores_model->get(array('id' => $id));
        if (empty($notificador)) {
            show_error('No se encontró el Notificación', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Notificadores_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE) {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Notificadores_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'apellido' => $this->input->post('apellido'),
                    'documento' => $this->input->post('documento'),
                    'telefono' => $this->input->post('telefono'),
                    'email' => $this->input->post('email'),
                    'zona_id' => $this->input->post('zona'),
//					'audi_usuario' => $this->input->post('audi_usuario'),
//					'audi_fecha' => $this->input->post('audi_fecha'),
//					'audi_accion' => $this->input->post('audi_accion')
                ), FALSE);
                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Notificadores_model->get_msg());
                    redirect('notificaciones/notificadores/listar', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Notificadores_model->get_error()) {
                        $error_msg .= $this->Notificadores_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));


        $data['zonas'] = $this->Zonas_model->get();
        $data['fields'] = $this->build_fields($this->Notificadores_model->fields, $notificador);
        $data['notificador'] = $notificador;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Notificación';
        $data['title'] = TITLE . ' - Editar Notificación';
        $this->load_template('notificaciones/notificadores/notificadores_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/notificadores/ver/$id", 'refresh');
        }

        $notificador = $this->Notificadores_model->get_one($id);
        if (empty($notificador)) {
            show_error('No se encontró el Notificación', 500, 'Registro no encontrado');
        }

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Notificadores_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Notificadores_model->get_msg());
                redirect('notificaciones/notificadores/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Notificadores_model->get_error()) {
                    $error_msg .= $this->Notificadores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['zonas'] = $this->Zonas_model->get();
        $data['fields'] = $this->build_fields($this->Notificadores_model->fields, $notificador, TRUE);
        $data['notificador'] = $notificador;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Notificación';
        $data['title'] = TITLE . ' - Eliminar Notificación';
        $this->load_template('notificaciones/notificadores/notificadores_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $notificador = $this->Notificadores_model->get_one($id);

        if (empty($notificador)) {
            show_error('No se encontró el Notificación', 500, 'Registro no encontrado');
        }
        $data['zonas'] = $this->Zonas_model->get();
        $data['fields'] = $this->build_fields($this->Notificadores_model->fields, $notificador, TRUE);
        $data['notificador'] = $notificador;

        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Notificador';
        $data['title'] = TITLE . ' - Ver Notificador';
        $this->load_template('notificaciones/notificadores/notificadores_abm', $data);
    }
}