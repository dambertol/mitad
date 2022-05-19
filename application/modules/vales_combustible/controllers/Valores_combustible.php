<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Valores_combustible extends MY_Controller
{

    /**
     * Controlador de Valores Combustible
     * Autor: Leandro
     * Creado: 06/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Valores_combustible_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->grupos_permitidos = array('admin', 'vales_combustible_contaduria', 'vales_combustible_consulta_general');
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
                array('label' => 'Fecha Inicio', 'data' => 'fecha_inicio', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 15),
                array('label' => 'Tipo Combustible', 'data' => 'tipo_combustible', 'width' => 59),
                array('label' => 'Costo', 'data' => 'costo', 'render' => 'money', 'class' => 'dt-body-right', 'width' => 20),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'valores_combustible_table',
            'source_url' => 'vales_combustible/valores_combustible/listar_data',
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_valores_combustible_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de valores de combustible';
        $data['title'] = TITLE . ' - Valores de combustible';
        $this->load_template('vales_combustible/valores_combustible/valores_combustible_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('vc_valores_combustible.id, vc_valores_combustible.fecha_inicio, vc_tipos_combustible.nombre as tipo_combustible, vc_valores_combustible.costo')
                ->from('vc_valores_combustible')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_valores_combustible.tipo_combustible_id', 'left')
                ->add_column('ver', '<a href="vales_combustible/valores_combustible/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/valores_combustible/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/valores_combustible/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect("vales_combustible/valores_combustible/listar", 'refresh');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->set_model_validation_rules($this->Valores_combustible_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha_inicio = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_inicio'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Valores_combustible_model->create(array(
                'fecha_inicio' => $fecha_inicio->format('Y-m-d'),
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'costo' => $this->input->post('costo')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Valores_combustible_model->get_msg());
                redirect('vales_combustible/valores_combustible/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Valores_combustible_model->get_error())
                {
                    $error_msg .= $this->Valores_combustible_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Valores_combustible_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($this->Valores_combustible_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar valor combustible';
        $data['title'] = TITLE . ' - Agregar valor combustible';
        $this->load_template('vales_combustible/valores_combustible/valores_combustible_abm', $data);
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
            redirect("vales_combustible/valores_combustible/ver/$id", 'refresh');
        }

        $valor_combustible = $this->Valores_combustible_model->get(array('id' => $id));
        if (empty($valor_combustible))
        {
            show_error('No se encontró el Valor Combustible', 500, 'Registro no encontrado');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->set_model_validation_rules($this->Valores_combustible_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $fecha_inicio = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_inicio'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Valores_combustible_model->update(array(
                    'id' => $this->input->post('id'),
                    'fecha_inicio' => $fecha_inicio->format('Y-m-d'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'costo' => $this->input->post('costo')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Valores_combustible_model->get_msg());
                    redirect('vales_combustible/valores_combustible/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Valores_combustible_model->get_error())
                    {
                        $error_msg .= $this->Valores_combustible_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Valores_combustible_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($this->Valores_combustible_model->fields, $valor_combustible);
        $data['valor_combustible'] = $valor_combustible;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar valor combustible';
        $data['title'] = TITLE . ' - Editar valor combustible';
        $this->load_template('vales_combustible/valores_combustible/valores_combustible_abm', $data);
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
            redirect("vales_combustible/valores_combustible/ver/$id", 'refresh');
        }

        $valor_combustible = $this->Valores_combustible_model->get_one($id);
        if (empty($valor_combustible))
        {
            show_error('No se encontró el Valor Combustible', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Valores_combustible_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Valores_combustible_model->get_msg());
                redirect('vales_combustible/valores_combustible/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Valores_combustible_model->get_error())
                {
                    $error_msg .= $this->Valores_combustible_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Valores_combustible_model->fields, $valor_combustible, TRUE);
        $data['valor_combustible'] = $valor_combustible;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar valor combustible';
        $data['title'] = TITLE . ' - Eliminar valor combustible';
        $this->load_template('vales_combustible/valores_combustible/valores_combustible_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $valor_combustible = $this->Valores_combustible_model->get_one($id);
        if (empty($valor_combustible))
        {
            show_error('No se encontró el Valor Combustible', 500, 'Registro no encontrado');
        }

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Valores_combustible_model->fields, $valor_combustible, TRUE);
        $data['valor_combustible'] = $valor_combustible;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver valor combustible';
        $data['title'] = TITLE . ' - Ver valor combustible';
        $this->load_template('vales_combustible/valores_combustible/valores_combustible_abm', $data);
    }

    public function buscar_costo()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('tipo', 'Tipo', 'required|integer');
        $this->form_validation->set_rules('litros', 'M³/Litros', 'required|numeric|max_length[9]');
        $this->form_validation->set_rules('fecha', 'Fecha', 'required|validate_date');
        $this->form_validation->set_rules('call', 'Call', 'required|integer|max_length[9]');
        if ($this->form_validation->run() === TRUE)
        {
            $fecha_inicio = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));
            $litros = $this->input->post('litros');
            $valores_combustible = $this->Valores_combustible_model->get(array(
                'fecha_inicio <=' => $fecha_inicio->format('Y-m-d'),
                'tipo_combustible_id' => $this->input->post('tipo'),
                'sort_by' => 'fecha_inicio DESC, id',
                'sort_direction' => 'DESC'
            ));
            if (empty($valores_combustible))
            {
                $datos['no_data'] = TRUE;
            }
            else
            {
                $datos['valor_combustible'] = number_format($valores_combustible[0]->costo * $litros, 2, '.', '');
            }
        }
        else
        {
            $datos['no_data'] = TRUE;
        }

        $datos['call'] = $this->input->post('call');
        echo json_encode($datos);
    }
}
