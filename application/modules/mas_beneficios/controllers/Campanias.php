<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Campanias extends MY_Controller
{

    /**
     * Controlador de Campañas
     * Autor: Leandro
     * Creado: 20/07/2020
     * Modificado: 07/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mas_beneficios/Agrupamientos_model');
        $this->load->model('mas_beneficios/Campanias_model');
        $this->grupos_permitidos = array('admin', 'mas_beneficios_consulta_general');
        $this->grupos_solo_consulta = array('mas_beneficios_consulta_general');
        $this->agrupamiento_id_comercio = '1';
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 34),
                array('label' => 'Activo', 'data' => 'activo', 'width' => 10),
                array('label' => 'Visible', 'data' => 'visible', 'width' => 10),
                array('label' => 'Estilo', 'data' => 'estilo', 'width' => 12),
                array('label' => 'Orden', 'data' => 'orden', 'class' => 'dt-body-right', 'width' => 8),
                array('label' => 'Agrupamiento', 'data' => 'agrupamiento', 'width' => 20),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'campanias_table',
            'source_url' => 'mas_beneficios/campanias/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_campanias_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Campañas';
        $data['title'] = TITLE . ' - Campañas';
        $this->load_template('mas_beneficios/campanias/campanias_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('ta_campanias.id, ta_campanias.nombre, ta_campanias.activo, ta_campanias.visible, ta_campanias.estilo, ta_campanias.orden, ta_agrupamientos.nombre as agrupamiento')
                ->from('ta_campanias')
                ->join('ta_agrupamientos', 'ta_agrupamientos.id = ta_campanias.agrupamiento_id', 'left')
                ->where('ta_agrupamientos.id', $this->agrupamiento_id_comercio)
                ->add_column('ver', '<a href="mas_beneficios/campanias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="mas_beneficios/campanias/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="mas_beneficios/campanias/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('mas_beneficios/campanias/listar', 'refresh');
        }

        $this->array_activo_control = $array_activo = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_visible_control = $array_visible = array('SI' => 'SI', 'NO' => 'NO');
        $this->set_model_validation_rules($this->Campanias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Campanias_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'activo' => $this->input->post('activo'),
                'visible' => $this->input->post('visible'),
                'estilo' => $this->input->post('estilo'),
                'orden' => $this->input->post('orden'),
                'agrupamiento_id' => $this->agrupamiento_id_comercio), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Campanias_model->get_msg());
                redirect('mas_beneficios/campanias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Campanias_model->get_error())
                {
                    $error_msg .= $this->Campanias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Campanias_model->fields['activo']['array'] = $array_activo;
        $this->Campanias_model->fields['visible']['array'] = $array_visible;
        $data['fields'] = $this->build_fields($this->Campanias_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Campaña';
        $data['title'] = TITLE . ' - Agregar Campaña';
        $this->load_template('mas_beneficios/campanias/campanias_abm', $data);
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
            redirect("mas_beneficios/campanias/ver/$id", 'refresh');
        }

        $campania = $this->Campanias_model->get(array('id' => $id));
        if (empty($campania) || $campania->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Campaña', 500, 'Registro no encontrado');
        }

        $this->array_activo_control = $array_activo = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_visible_control = $array_visible = array('SI' => 'SI', 'NO' => 'NO');
        $this->set_model_validation_rules($this->Campanias_model);
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
                $trans_ok &= $this->Campanias_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'activo' => $this->input->post('activo'),
                    'visible' => $this->input->post('visible'),
                    'estilo' => $this->input->post('estilo'),
                    'orden' => $this->input->post('orden'),
                    'agrupamiento_id' => $this->agrupamiento_id_comercio), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Campanias_model->get_msg());
                    redirect('mas_beneficios/campanias/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Campanias_model->get_error())
                    {
                        $error_msg .= $this->Campanias_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Campanias_model->fields['activo']['array'] = $array_activo;
        $this->Campanias_model->fields['visible']['array'] = $array_visible;
        $data['fields'] = $this->build_fields($this->Campanias_model->fields, $campania);
        $data['campania'] = $campania;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Campaña';
        $data['title'] = TITLE . ' - Editar Campaña';
        $this->load_template('mas_beneficios/campanias/campanias_abm', $data);
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
            redirect("mas_beneficios/campanias/ver/$id", 'refresh');
        }

        $campania = $this->Campanias_model->get_one($id);
        if (empty($campania) || $campania->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Campaña', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Campanias_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Campanias_model->get_msg());
                redirect('mas_beneficios/campanias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Campanias_model->get_error())
                {
                    $error_msg .= $this->Campanias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Campanias_model->fields, $campania, TRUE);
        $data['campania'] = $campania;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Campaña';
        $data['title'] = TITLE . ' - Eliminar Campaña';
        $this->load_template('mas_beneficios/campanias/campanias_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $campania = $this->Campanias_model->get_one($id);
        if (empty($campania) || $campania->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Campaña', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Campanias_model->fields, $campania, TRUE);
        $data['campania'] = $campania;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Campaña';
        $data['title'] = TITLE . ' - Ver Campaña';
        $this->load_template('mas_beneficios/campanias/campanias_abm', $data);
    }
}
