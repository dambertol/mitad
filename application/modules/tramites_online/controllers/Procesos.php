<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Procesos extends MY_Controller
{

    /**
     * Controlador de Procesos
     * Autor: Leandro
     * Creado: 23/04/2021
     * Modificado: 14/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Iniciadores_tipos_model');
        $this->load->model('tramites_online/Oficinas_model');
        $this->load->model('tramites_online/Procesos_model');
        $this->load->model('tramites_online/Procesos_iniciadores_model');
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
                array('label' => 'Oficina', 'data' => 'oficina', 'width' => 20),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 20),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
                array('label' => 'Visibilidad', 'data' => 'visibilidad', 'width' => 10),
                array('label' => 'Email Responsable', 'data' => 'email_responsable', 'width' => 20),
                array('label' => 'Iniciadores', 'data' => 'iniciadores', 'width' => 14),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'procesos_table',
            'source_url' => 'tramites_online/procesos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_procesos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Procesos';
        $data['title'] = TITLE . ' - Procesos';
        $this->load_template('tramites_online/procesos/procesos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('to2_procesos.id, to2_oficinas.nombre as oficina, to2_procesos.nombre, to2_procesos.tipo, to2_procesos.visibilidad, to2_procesos.email_responsable, (SELECT GROUP_CONCAT(to2_iniciadores_tipos.nombre SEPARATOR ", ") FROM to2_procesos_iniciadores JOIN to2_iniciadores_tipos ON to2_procesos_iniciadores.iniciador_tipo_id = to2_iniciadores_tipos.id WHERE to2_procesos_iniciadores.proceso_id = to2_procesos.id) AS iniciadores')
                ->from('to2_procesos')
                ->join('to2_oficinas', 'to2_oficinas.id = to2_procesos.oficina_id', 'left')
                ->add_column('ver', '<a href="tramites_online/procesos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="tramites_online/procesos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="tramites_online/procesos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('tramites_online/procesos/listar', 'refresh');
        }

        $this->array_oficina_control = $array_oficina = $this->get_array('Oficinas', 'nombre');
        $this->array_tipo_control = $array_tipo = array('Consulta' => 'Consulta', 'Trámite' => 'Trámite');
        $this->array_visibilidad_control = $array_visibilidad = array('Público' => 'Público', 'Privado' => 'Privado');
        $this->array_iniciadores_control = $array_iniciadores = $this->get_array('Iniciadores_tipos', 'nombre');
        $this->set_model_validation_rules($this->Procesos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Procesos_model->create(
                    array(
                        'nombre' => $this->input->post('nombre'),
                        'tipo' => $this->input->post('tipo'),
                        'visibilidad' => $this->input->post('visibilidad'),
                        'oficina_id' => $this->input->post('oficina'),
                        'email_responsable' => $this->input->post('email_responsable')
                    ), FALSE);

            $proceso_id = $this->Procesos_model->get_row_id();
            $iniciadores = $this->input->post('iniciadores');
            foreach ($iniciadores as $Iniciador)
            {
                $trans_ok &= $this->Procesos_iniciadores_model->create(
                        array(
                            'proceso_id' => $proceso_id,
                            'iniciador_tipo_id' => $Iniciador
                        ), FALSE);
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Procesos_model->get_msg());
                redirect('tramites_online/procesos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Procesos_model->get_error())
                {
                    $error_msg .= $this->Procesos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Procesos_model->fields['oficina']['array'] = $array_oficina;
        $this->Procesos_model->fields['tipo']['array'] = $array_tipo;
        $this->Procesos_model->fields['visibilidad']['array'] = $array_visibilidad;
        $this->Procesos_model->fields['iniciadores']['array'] = $array_iniciadores;
        $data['fields'] = $this->build_fields($this->Procesos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Proceso';
        $data['title'] = TITLE . ' - Agregar Proceso';
        $this->load_template('tramites_online/procesos/procesos_abm', $data);
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
            redirect("tramites_online/procesos/ver/$id", 'refresh');
        }

        $proceso = $this->Procesos_model->get(array('id' => $id));
        if (empty($proceso))
        {
            show_error('No se encontró el Proceso', 500, 'Registro no encontrado');
        }

        $proceso->iniciadores = array();
        $procesos_iniciadores = $this->Procesos_iniciadores_model->get(array('proceso_id' => $id));
        if (!empty($procesos_iniciadores))
        {
            foreach ($procesos_iniciadores as $Iniciador)
            {
                $proceso->iniciadores[] = $Iniciador->iniciador_tipo_id;
            }
        }

        $this->array_oficina_control = $array_oficina = $this->get_array('Oficinas', 'nombre');
        $this->array_tipo_control = $array_tipo = array('Consulta' => 'Consulta', 'Trámite' => 'Trámite');
        $this->array_visibilidad_control = $array_visibilidad = array('Público' => 'Público', 'Privado' => 'Privado');
        $this->array_iniciadores_control = $array_iniciadores = $this->get_array('Iniciadores_tipos', 'nombre');
        $this->set_model_validation_rules($this->Procesos_model);
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
                $trans_ok &= $this->Procesos_model->update(
                        array(
                            'id' => $this->input->post('id'),
                            'nombre' => $this->input->post('nombre'),
                            'tipo' => $this->input->post('tipo'),
                            'visibilidad' => $this->input->post('visibilidad'),
                            'oficina_id' => $this->input->post('oficina'),
                            'email_responsable' => $this->input->post('email_responsable')
                        ), FALSE);

                $iniciadores_post = $this->input->post('iniciadores');
                if (empty($iniciadores_post))
                {
                    $iniciadores_post = array();
                }
                $trans_ok &= $this->Procesos_iniciadores_model->intersect_asignaciones($id, $iniciadores_post, FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Procesos_model->get_msg());
                    redirect('tramites_online/procesos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Procesos_model->get_error())
                    {
                        $error_msg .= $this->Procesos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Procesos_model->fields['oficina']['array'] = $array_oficina;
        $this->Procesos_model->fields['tipo']['array'] = $array_tipo;
        $this->Procesos_model->fields['visibilidad']['array'] = $array_visibilidad;
        $this->Procesos_model->fields['iniciadores']['array'] = $array_iniciadores;
        $data['fields'] = $this->build_fields($this->Procesos_model->fields, $proceso);
        $data['proceso'] = $proceso;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Proceso';
        $data['title'] = TITLE . ' - Editar Proceso';
        $this->load_template('tramites_online/procesos/procesos_abm', $data);
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
            redirect("tramites_online/procesos/ver/$id", 'refresh');
        }

        $proceso = $this->Procesos_model->get_one($id);
        if (empty($proceso))
        {
            show_error('No se encontró el Proceso', 500, 'Registro no encontrado');
        }

        $tipos_iniciadores = array();
        $procesos_iniciadores = $this->Procesos_iniciadores_model->get(array(
            'proceso_id' => $id,
            'join' => array(
                array('to2_iniciadores_tipos', 'to2_iniciadores_tipos.id = to2_procesos_iniciadores.iniciador_tipo_id', 'left', 'to2_iniciadores_tipos.nombre as tipo_iniciador')
            )
        ));
        if (!empty($procesos_iniciadores))
        {
            foreach ($procesos_iniciadores as $Iniciador)
            {
                $tipos_iniciadores[] = $Iniciador->tipo_iniciador;
            }
        }
        $proceso->iniciadores = implode(', ', $tipos_iniciadores);

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Procesos_iniciadores_model->delete_detalles($this->input->post('id'), FALSE);
            $trans_ok &= $this->Procesos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Procesos_model->get_msg());
                redirect('tramites_online/procesos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Procesos_model->get_error())
                {
                    $error_msg .= $this->Procesos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Procesos_model->fields, $proceso, TRUE);
        $data['proceso'] = $proceso;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Proceso';
        $data['title'] = TITLE . ' - Eliminar Proceso';
        $this->load_template('tramites_online/procesos/procesos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $proceso = $this->Procesos_model->get_one($id);
        if (empty($proceso))
        {
            show_error('No se encontró el Proceso', 500, 'Registro no encontrado');
        }

        $tipos_iniciadores = array();
        $procesos_iniciadores = $this->Procesos_iniciadores_model->get(array(
            'proceso_id' => $id,
            'join' => array(
                array('to2_iniciadores_tipos', 'to2_iniciadores_tipos.id = to2_procesos_iniciadores.iniciador_tipo_id', 'left', 'to2_iniciadores_tipos.nombre as tipo_iniciador')
            )
        ));
        if (!empty($procesos_iniciadores))
        {
            foreach ($procesos_iniciadores as $Iniciador)
            {
                $tipos_iniciadores[] = $Iniciador->tipo_iniciador;
            }
        }
        $proceso->iniciadores = implode(', ', $tipos_iniciadores);

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Procesos_model->fields, $proceso, TRUE);
        $data['proceso'] = $proceso;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Proceso';
        $data['title'] = TITLE . ' - Ver Proceso';
        $this->load_template('tramites_online/procesos/procesos_abm', $data);
    }
}
