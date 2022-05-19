<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ubicaciones extends MY_Controller
{

    /**
     * Controlador de Ubicaciones
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Ubicaciones_model');
        $this->load->model('defunciones/Cementerios_model');
        $this->load->model('defunciones/Difuntos_model');
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
                array('label' => 'ID', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Cementerio', 'data' => 'cementerio', 'width' => 21),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 13),
                array('label' => 'Sector', 'data' => 'sector', 'width' => 10),
                array('label' => 'Cuadro', 'data' => 'cuadro', 'width' => 8),
                array('label' => 'Fila', 'data' => 'fila', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Nicho', 'data' => 'nicho', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Denominación', 'data' => 'denominacion', 'width' => 13),
                array('label' => 'Nomenclatura', 'data' => 'nomenclatura', 'width' => 8),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'ubicaciones_table',
            'source_url' => 'defunciones/ubicaciones/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_ubicaciones_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Ubicaciones';
        $data['title'] = TITLE . ' - Ubicaciones';
        $this->load_template('defunciones/ubicaciones/ubicaciones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('df_ubicaciones.id, df_cementerios.nombre as cementerio, df_ubicaciones.tipo, df_ubicaciones.sector, df_ubicaciones.cuadro, df_ubicaciones.fila, df_ubicaciones.nicho, df_ubicaciones.denominacion, df_ubicaciones.nomenclatura')
                ->from('df_ubicaciones')
                ->join('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'left')
                ->add_column('ver', '<a href="defunciones/ubicaciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/ubicaciones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/ubicaciones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/ubicaciones/listar', 'refresh');
        }

        $this->array_cementerio_control = $array_cementerio = $this->get_array('Cementerios', 'nombre');
        $this->array_tipo_control = $this->Ubicaciones_model->fields['tipo']['array'];
        $this->set_model_validation_rules($this->Ubicaciones_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Ubicaciones_model->create(array(
                'cementerio_id' => $this->input->post('cementerio'),
                'tipo' => $this->input->post('tipo'),
                'sector' => $this->input->post('sector'),
                'cuadro' => $this->input->post('cuadro'),
                'fila' => $this->input->post('fila'),
                'nicho' => $this->input->post('nicho'),
                'denominacion' => $this->input->post('denominacion'),
                'nomenclatura' => $this->input->post('nomenclatura'),
                'observaciones' => $this->input->post('observaciones')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Ubicaciones_model->get_msg());
                redirect('defunciones/ubicaciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Ubicaciones_model->get_error())
                {
                    $error_msg .= $this->Ubicaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Ubicaciones_model->fields['cementerio']['array'] = $array_cementerio;
        $data['fields'] = $this->build_fields($this->Ubicaciones_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Ubicación';
        $data['title'] = TITLE . ' - Agregar Ubicación';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/ubicaciones/ubicaciones_abm', $data);
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
            redirect("defunciones/ubicaciones/ver/$id", 'refresh');
        }

        $this->array_cementerio_control = $array_cementerio = $this->get_array('Cementerios', 'nombre');
        $this->array_tipo_control = $this->Ubicaciones_model->fields['tipo']['array'];
        $ubicacion = $this->Ubicaciones_model->get(array('id' => $id));
        if (empty($ubicacion))
        {
            show_error('No se encontró la Ubicación', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Ubicaciones_model);
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
                $trans_ok &= $this->Ubicaciones_model->update(array(
                    'id' => $this->input->post('id'),
                    'cementerio_id' => $this->input->post('cementerio'),
                    'tipo' => $this->input->post('tipo'),
                    'sector' => $this->input->post('sector'),
                    'cuadro' => $this->input->post('cuadro'),
                    'fila' => $this->input->post('fila'),
                    'nicho' => $this->input->post('nicho'),
                    'denominacion' => $this->input->post('denominacion'),
                    'nomenclatura' => $this->input->post('nomenclatura'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Ubicaciones_model->get_msg());
                    redirect('defunciones/ubicaciones/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Ubicaciones_model->get_error())
                    {
                        $error_msg .= $this->Ubicaciones_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Ubicaciones_model->fields['cementerio']['array'] = $array_cementerio;
        $data['fields'] = $this->build_fields($this->Ubicaciones_model->fields, $ubicacion);
        $data['ubicacion'] = $ubicacion;
        $data['difuntos'] = $this->Difuntos_model->get(array('ubicacion_id' => $ubicacion->id));
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Ubicación';
        $data['title'] = TITLE . ' - Editar Ubicación';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/ubicaciones/ubicaciones_abm', $data);
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
            redirect("defunciones/ubicaciones/ver/$id", 'refresh');
        }

        $ubicacion = $this->Ubicaciones_model->get_one($id);
        if (empty($ubicacion))
        {
            show_error('No se encontró la Ubicación', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Ubicaciones_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Ubicaciones_model->get_msg());
                redirect('defunciones/ubicaciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Ubicaciones_model->get_error())
                {
                    $error_msg .= $this->Ubicaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Ubicaciones_model->fields, $ubicacion, TRUE);
        $data['ubicacion'] = $ubicacion;
        $data['difuntos'] = $this->Difuntos_model->get(array('ubicacion_id' => $ubicacion->id));
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Ubicación';
        $data['title'] = TITLE . ' - Eliminar Ubicación';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/ubicaciones/ubicaciones_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $ubicacion = $this->Ubicaciones_model->get_one($id);
        if (empty($ubicacion))
        {
            show_error('No se encontró la Ubicación', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Ubicaciones_model->fields, $ubicacion, TRUE);
        $data['ubicacion'] = $ubicacion;
        $data['difuntos'] = $this->Difuntos_model->get(array('ubicacion_id' => $ubicacion->id));
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Ubicación';
        $data['title'] = TITLE . ' - Ver Ubicación';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/ubicaciones/ubicaciones_abm', $data);
    }
}
