<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hojas_rutas extends MY_Controller
{

    /**
     * Controlador de Hojas de Ruta
     * Autor: GENERATOR_MLC
     * Creado: 02/07/2019
     * Modificado: 02/07/2019 (GENERATOR_MLC)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Usuarios_model');
        $this->load->model('notificaciones/Cedulas_model');
        $this->load->model('notificaciones/Cedulas_estados_model');
        $this->load->model('notificaciones/Hojas_rutas_model');
        $this->load->helper('notificaciones/notificaciones_functions_helper');

        /**
         * Grupos: admin,
         *      - notificaciones_user: usuario de notificaciones
         *      - notificaciones_areas: usuario de oficina externas a notificaciones
         *      - notificaciones_notificadores: usuario Notificador
         *      - notificaciones_control: usuario de Control y Estadisticas
         */


        $this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_notificadores');
        $this->grupos_notificador = array('notificaciones_notificadores');
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
                array('label' => 'Notificador', 'data' => 'notificador_id', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Estado', 'data' => 'estado_id', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Fecha de Creacion', 'data' => 'fecha_creacion', 'width' => 10, 'render' => 'datetime'),
                array('label' => 'Fecha de Limite', 'data' => 'fecha_limite', 'width' => 10, 'render' => 'datetime'),
//				array('label' => 'Usuario', 'data' => 'usuario_id', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
//				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'hojas_rutas_table',
            'source_url' => 'notificaciones/hojas_rutas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_hojas_rutas_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Hojas de Ruta';
        $data['title'] = TITLE . ' - Hojas de Ruta';
        $data['is_notificador'] = (in_groups($this->grupos_notificador, $this->grupos)) ? $this->session->userdata('user_id') : false;
        $this->load_template('notificaciones/hojas_rutas/hojas_rutas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
            ->select('id, notificador_id, estado_id, fecha_creacion, fecha_limite')
            ->from('nv_hojas_rutas')
            ->add_column('ver', '<a href="notificaciones/hojas_rutas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            ->add_column('editar', '<a href="notificaciones/hojas_rutas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            ->add_column('eliminar', '<a href="notificaciones/hojas_rutas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->set_model_validation_rules($this->Hojas_rutas_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE) {

            $cedulas = $this->input->post('cedulas');
            $fecha = new DateTime();

            if (in_groups($this->grupos_notificador, $this->grupos)) {
                $notificador_id = $this->session->userdata('user_id');
            } else {
                $notificador_id = $this->input->post('notificador_id');
            }


            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Hojas_rutas_model->create(array(
                'notificador_id' => $notificador_id,
                'estado_id' => 1,
                'fecha_creacion' => $fecha->format('Y-m-d H:i:s'),
                'fecha_limite' => $fecha->format('Y-m-d H:i:s'),
                // 'fecha_limite' => $this->input->post('fecha_limite'),
                'usuario_id' => $this->session->userdata('user_id'),
            ), FALSE);

            $hoja_ruta_id = $this->Hojas_rutas_model->get_row_id();
            foreach ($cedulas as $cedula) {
                $trans_ok &= $this->Cedulas_model->update(array(
                    'id' => $cedula,
                    'hoja_ruta_id' => $hoja_ruta_id,
                    'estado_id' => Cedulas_estados_model::EN_RUTA,
                ), FALSE);

            }


            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Hojas_rutas_model->get_msg());
                redirect('notificaciones/hojas_rutas/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Hojas_rutas_model->get_error()) {
                    $error_msg .= $this->Hojas_rutas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Hojas_rutas_model->fields);

        $data['notificador'] = $this->Cedulas_model->get_notificador($this->session->userdata('user_id'));

        $data['notificadores'] = $this->Cedulas_model->list_notificadores();

        $data['estado_hoja_ruta'] = false;


        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Hoja de Ruta';
        $data['title'] = TITLE . ' - Agregar Hoja de Ruta';
        $this->load_template('notificaciones/hojas_rutas/hojas_rutas_agregar', $data);
    }

    public function cargar_cedulas()
    {
        //ajax
        $notificador_id = $this->input->post('n_id');
        $data['cedulas'] = $this->Cedulas_model->get_cedulas_hojas_ruta($notificador_id);

        echo json_encode($data);

    }


    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/hojas_rutas/ver/$id", 'refresh');
        }

        $this->array_notificador_control = $array_notificador = $this->get_array('Notificadores');
        $this->array_estado_control = $array_estado = $this->get_array('Estados');
        $this->array_usuario_control = $array_usuario = $this->get_array('Usuarios');
        $hojas_ruta = $this->Hojas_rutas_model->get(array('id' => $id));
        if (empty($hojas_ruta)) {
            show_error('No se encontró el Hoja de Ruta', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Hojas_rutas_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE) {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Hojas_rutas_model->update(array(
                    'id' => $this->input->post('id'),
                    'notificador_id' => $this->input->post('notificador'),
                    'estado_id' => $this->input->post('estado'),
                    'fecha_creacion' => $this->input->post('fecha_creacion'),
                    'fecha_limite' => $this->input->post('fecha_limite'),
                    'usuario_id' => $this->input->post('usuario'),
                    'audi_usuario' => $this->input->post('audi_usuario'),
                    'audi_fecha' => $this->input->post('audi_fecha'),
                    'audi_accion' => $this->input->post('audi_accion')), FALSE);
                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Hojas_rutas_model->get_msg());
                    redirect('notificaciones/hojas_rutas/listar', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Hojas_rutas_model->get_error()) {
                        $error_msg .= $this->Hojas_rutas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Hojas_rutas_model->fields['notificador']['array'] = $array_notificador;
        $this->Hojas_rutas_model->fields['estado']['array'] = $array_estado;
        $this->Hojas_rutas_model->fields['usuario']['array'] = $array_usuario;
        $data['fields'] = $this->build_fields($this->Hojas_rutas_model->fields, $hojas_ruta);
        $data['hojas_ruta'] = $hojas_ruta;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Hoja de Ruta';
        $data['title'] = TITLE . ' - Editar Hoja de Ruta';
        $this->load_template('notificaciones/hojas_rutas/hojas_rutas_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/hojas_rutas/ver/$id", 'refresh');
        }

        $hojas_ruta = $this->Hojas_rutas_model->get_one($id);
        if (empty($hojas_ruta)) {
            show_error('No se encontró el Hoja de Ruta', 500, 'Registro no encontrado');
        }

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Hojas_rutas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Hojas_rutas_model->get_msg());
                redirect('notificaciones/hojas_rutas/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Hojas_rutas_model->get_error()) {
                    $error_msg .= $this->Hojas_rutas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Hojas_rutas_model->fields, $hojas_ruta, TRUE);
        $data['hojas_ruta'] = $hojas_ruta;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Hoja de Ruta';
        $data['title'] = TITLE . ' - Eliminar Hoja de Ruta';
        $this->load_template('notificaciones/hojas_rutas/hojas_rutas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $hojas_ruta = $this->Hojas_rutas_model->get_one($id);
        if (empty($hojas_ruta)) {
            show_error('No se encontró el Hoja de Ruta', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Hojas_rutas_model->fields, $hojas_ruta, TRUE);
        $data['hojas_ruta'] = $hojas_ruta;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Hoja de Ruta';
        $data['title'] = TITLE . ' - Ver Hoja de Ruta';
        $this->load_template('notificaciones/hojas_rutas/hojas_rutas_abm', $data);
    }
}