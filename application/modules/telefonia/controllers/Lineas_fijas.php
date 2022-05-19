<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lineas_fijas extends MY_Controller
{

    /**
     * Controlador de Líneas Fijas
     * Autor: Leandro
     * Creado: 05/09/2019
     * Modificado: 26/05/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('telefonia/Lineas_fijas_model');
        $this->load->model('Areas_model');
        $this->grupos_permitidos = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
        $this->grupos_solo_consulta = array('telefonia_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'array' => array('Activa' => 'Activa', 'Baja' => 'Baja', 'Todas' => 'Todas'))
        );
        $tableData = array(
            'columns' => array(
                array('label' => 'Línea', 'data' => 'linea', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo_linea', 'width' => 8),
                array('label' => 'Domicilio', 'data' => 'domicilio', 'width' => 20),
                array('label' => 'Área', 'data' => 'area', 'width' => 20),
                array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 17),
                array('label' => 'Inicio', 'data' => 'periodo_ini', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Fin', 'data' => 'periodo_fin', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'lineas_fijas_table',
            'source_url' => 'telefonia/lineas_fijas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_lineas_fijas_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
            'extraData' => 'd.estado = $("#estado").val(); '
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $fake_data = new stdClass();
        $fake_data->estado = 'Activa';
        $data['fields'] = $this->build_fields($fake_model->fields, $fake_data);
        $data['title_view'] = 'Listado de Líneas Fijas';
        $data['title'] = TITLE . ' - Líneas Fijas';
        $this->load_template('telefonia/lineas_fijas/lineas_fijas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $dt = $this->datatables
                ->select("tm_lineas_fijas.id, tm_lineas_fijas.linea, tm_lineas_fijas.domicilio, tm_lineas_fijas.observaciones, tm_lineas_fijas.periodo_ini, tm_lineas_fijas.periodo_fin, CONCAT(areas.codigo, ' - ', areas.nombre) as area, tm_lineas_fijas.tipo_linea")
                ->from('tm_lineas_fijas')
                ->join('areas', 'areas.id = tm_lineas_fijas.area_id', 'left');

        $estado = $this->input->post('estado');
        switch ($estado)
        {
            case 'Activa':
                $dt->where('tm_lineas_fijas.periodo_fin IS NULL');
                break;
            case 'Baja':
                $dt->where('tm_lineas_fijas.periodo_fin IS NOT NULL');
                break;
        }

        $dt->add_column('ver', '<a href="telefonia/lineas_fijas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="telefonia/lineas_fijas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="telefonia/lineas_fijas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('telefonia/lineas_fijas/listar', 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('NULL' => '-- Sin Área --'));

        $this->array_tipo_linea_control = $array_tipo_linea = array('Linea' => 'Linea', 'Speedy' => 'Speedy');

        $periodos = array();
        $inicio = '201403';
        $fin = date_format(new DateTime(), 'Ym');
        $periodos[$fin] = $fin;
        $periodo = date_format(new DateTime($fin . '01 -1 month'), 'Ym');
        while ($inicio <= $periodo)
        {
            $periodos[$periodo] = $periodo;
            $periodo = date_format(new DateTime($periodo . '01 -1 month'), 'Ym');
        }
        $periodos_fin = $periodos;
        $periodos_fin[NULL] = 'Activo';
        $this->array_periodo_ini_control = $array_periodo_ini = $periodos;
        $this->array_periodo_fin_control = $array_periodo_fin = $periodos_fin;

        $this->set_model_validation_rules($this->Lineas_fijas_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Lineas_fijas_model->create(array(
                'linea' => $this->input->post('linea'),
                'domicilio' => $this->input->post('domicilio'),
                'observaciones' => $this->input->post('observaciones'),
                'periodo_ini' => $this->input->post('periodo_ini'),
                'periodo_fin' => $this->input->post('periodo_fin'),
                'area_id' => $this->input->post('area'),
                'tipo_linea' => $this->input->post('tipo_linea')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Lineas_fijas_model->get_msg());
                redirect('telefonia/lineas_fijas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Lineas_fijas_model->get_error())
                {
                    $error_msg .= $this->Lineas_fijas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Lineas_fijas_model->fields['area']['array'] = $array_area;
        $this->Lineas_fijas_model->fields['tipo_linea']['array'] = $array_tipo_linea;
        $this->Lineas_fijas_model->fields['periodo_ini']['array'] = $array_periodo_ini;
        $this->Lineas_fijas_model->fields['periodo_fin']['array'] = $array_periodo_fin;
        $data['fields'] = $this->build_fields($this->Lineas_fijas_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Línea Fija';
        $data['title'] = TITLE . ' - Agregar Línea Fija';
        $this->load_template('telefonia/lineas_fijas/lineas_fijas_abm', $data);
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
            redirect("telefonia/lineas_fijas/ver/$id", 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('NULL' => '-- Sin Área --'));

        $this->array_tipo_linea_control = $array_tipo_linea = array('Linea' => 'Linea', 'Speedy' => 'Speedy');

        $periodos = array();
        $inicio = '201403';
        $fin = date_format(new DateTime(), 'Ym');
        $periodos[$fin] = $fin;
        $periodo = date_format(new DateTime($fin . '01 -1 month'), 'Ym');
        while ($inicio <= $periodo)
        {
            $periodos[$periodo] = $periodo;
            $periodo = date_format(new DateTime($periodo . '01 -1 month'), 'Ym');
        }
        $periodos_fin = $periodos;
        $periodos_fin[NULL] = 'Activo';
        $this->array_periodo_ini_control = $array_periodo_ini = $periodos;
        $this->array_periodo_fin_control = $array_periodo_fin = $periodos_fin;

        $lineas_fija = $this->Lineas_fijas_model->get(array('id' => $id));
        if (empty($lineas_fija))
        {
            show_error('No se encontró la Línea Fija', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Lineas_fijas_model);
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
                $trans_ok &= $this->Lineas_fijas_model->update(array(
                    'id' => $this->input->post('id'),
                    'linea' => $this->input->post('linea'),
                    'domicilio' => $this->input->post('domicilio'),
                    'observaciones' => $this->input->post('observaciones'),
                    'periodo_ini' => $this->input->post('periodo_ini'),
                    'periodo_fin' => $this->input->post('periodo_fin'),
                    'area_id' => $this->input->post('area'),
                    'tipo_linea' => $this->input->post('tipo_linea')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Lineas_fijas_model->get_msg());
                    redirect('telefonia/lineas_fijas/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Lineas_fijas_model->get_error())
                    {
                        $error_msg .= $this->Lineas_fijas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Lineas_fijas_model->fields['area']['array'] = $array_area;
        $this->Lineas_fijas_model->fields['tipo_linea']['array'] = $array_tipo_linea;
        $this->Lineas_fijas_model->fields['periodo_ini']['array'] = $array_periodo_ini;
        $this->Lineas_fijas_model->fields['periodo_fin']['array'] = $array_periodo_fin;
        $data['fields'] = $this->build_fields($this->Lineas_fijas_model->fields, $lineas_fija);
        $data['lineas_fija'] = $lineas_fija;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Línea Fija';
        $data['title'] = TITLE . ' - Editar Línea Fija';
        $this->load_template('telefonia/lineas_fijas/lineas_fijas_abm', $data);
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
            redirect("telefonia/lineas_fijas/ver/$id", 'refresh');
        }

        $lineas_fija = $this->Lineas_fijas_model->get_one($id);
        if (empty($lineas_fija))
        {
            show_error('No se encontró la Línea Fija', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Lineas_fijas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Lineas_fijas_model->get_msg());
                redirect('telefonia/lineas_fijas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Lineas_fijas_model->get_error())
                {
                    $error_msg .= $this->Lineas_fijas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Lineas_fijas_model->fields, $lineas_fija, TRUE);
        $data['lineas_fija'] = $lineas_fija;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Línea Fija';
        $data['title'] = TITLE . ' - Eliminar Línea Fija';
        $this->load_template('telefonia/lineas_fijas/lineas_fijas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $lineas_fija = $this->Lineas_fijas_model->get_one($id);
        if (empty($lineas_fija))
        {
            show_error('No se encontró la Línea Fija', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Lineas_fijas_model->fields, $lineas_fija, TRUE);
        $data['lineas_fija'] = $lineas_fija;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Línea Fija';
        $data['title'] = TITLE . ' - Ver Línea Fija';
        $this->load_template('telefonia/lineas_fijas/lineas_fijas_abm', $data);
    }
}
