<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Propietarios extends MY_Controller
{

    /**
     * Controlador de Propietarios
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Propietarios_model');
        $this->load->model('defunciones/Expedientes_model');
        $this->load->model('defunciones/Solicitantes_model');
        $this->load->model('defunciones/Ubicaciones_model');
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
                array('label' => 'Propietario', 'data' => 'propietario', 'width' => 14),
                array('label' => 'Cementerio', 'data' => 'cementerio', 'width' => 12),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 10),
                array('label' => 'Sector', 'data' => 'sector', 'width' => 6),
                array('label' => 'Cuadro', 'data' => 'cuadro', 'width' => 6),
                array('label' => 'Fila', 'data' => 'fila', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Nicho', 'data' => 'nicho', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Denominación', 'data' => 'denominacion', 'width' => 15),
                array('label' => 'Exp. Compra', 'data' => 'expediente_compra', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Exp. Construcción', 'data' => 'expediente_construccion', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'propietarios_table',
            'source_url' => 'defunciones/propietarios/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_propietarios_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Propietarios';
        $data['title'] = TITLE . ' - Propietarios';
        $this->load_template('defunciones/propietarios/propietarios_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("df_propietarios.id, df_solicitantes.nombre as propietario, df_cementerios.nombre as cementerio, df_ubicaciones.tipo as tipo, df_ubicaciones.sector as sector, df_ubicaciones.cuadro as cuadro, df_ubicaciones.fila as fila, df_ubicaciones.nicho as nicho, df_ubicaciones.denominacion as denominacion, CONCAT(ecomp.numero, '/', ecomp.ejercicio, ' ', COALESCE(ecomp.letra, '')) as expediente_compra, CONCAT(econs.numero, '/', econs.ejercicio, ' ', COALESCE(econs.letra, '')) as expediente_construccion")
                ->from('df_propietarios')
                ->join('df_solicitantes', 'df_solicitantes.id = df_propietarios.solicitante_id', 'left')
                ->join('df_ubicaciones', 'df_ubicaciones.id = df_propietarios.ubicacion_id', 'left')
                ->join('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'left')
                ->join('df_expedientes ecomp', 'ecomp.id = df_propietarios.expediente_compra_id', 'left')
                ->join('df_expedientes econs', 'econs.id = df_propietarios.expediente_construccion_id', 'left')
                ->add_column('ver', '<a href="defunciones/propietarios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/propietarios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/propietarios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/propietarios/listar', 'refresh');
        }

        $this->array_expediente_compra_control = $array_expediente_compra = $this->array_expediente_construccion_control = $array_expediente_construccion = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));
        $this->array_solicitante_control = $array_solicitante = $this->get_array('Solicitantes', 'solicitante', 'id', array('select' => array("id, CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', nombre) as solicitante"), 'sort_by' => 'dni'));
        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'));

        $this->set_model_validation_rules($this->Propietarios_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Propietarios_model->create(array(
                'expediente_compra_id' => $this->input->post('expediente_compra'),
                'expediente_construccion_id' => $this->input->post('expediente_construccion'),
                'solicitante_id' => $this->input->post('solicitante'),
                'ubicacion_id' => $this->input->post('ubicacion')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Propietarios_model->get_msg());
                redirect('defunciones/propietarios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Propietarios_model->get_error())
                {
                    $error_msg .= $this->Propietarios_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Propietarios_model->fields['expediente_compra']['array'] = $array_expediente_compra;
        $this->Propietarios_model->fields['expediente_construccion']['array'] = $array_expediente_construccion;
        $this->Propietarios_model->fields['solicitante']['array'] = $array_solicitante;
        $this->Propietarios_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Propietarios_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Propietario';
        $data['title'] = TITLE . ' - Agregar Propietario';
        $this->load_template('defunciones/propietarios/propietarios_abm', $data);
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
            redirect("defunciones/propietarios/ver/$id", 'refresh');
        }

        $this->array_expediente_compra_control = $array_expediente_compra = $this->array_expediente_construccion_control = $array_expediente_construccion = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));
        $this->array_solicitante_control = $array_solicitante = $this->get_array('Solicitantes', 'solicitante', 'id', array('select' => array("CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', nombre) as solicitante", 'id'), 'sort_by' => 'dni'));
        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'));

        $propietario = $this->Propietarios_model->get(array('id' => $id));
        if (empty($propietario))
        {
            show_error('No se encontró el Propietario', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Propietarios_model);
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
                $trans_ok &= $this->Propietarios_model->update(array(
                    'id' => $this->input->post('id'),
                    'expediente_compra_id' => $this->input->post('expediente_compra'),
                    'expediente_construccion_id' => $this->input->post('expediente_construccion'),
                    'solicitante_id' => $this->input->post('solicitante'),
                    'ubicacion_id' => $this->input->post('ubicacion')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Propietarios_model->get_msg());
                    redirect('defunciones/propietarios/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Propietarios_model->get_error())
                    {
                        $error_msg .= $this->Propietarios_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Propietarios_model->fields['expediente_compra']['array'] = $array_expediente_compra;
        $this->Propietarios_model->fields['expediente_construccion']['array'] = $array_expediente_construccion;
        $this->Propietarios_model->fields['solicitante']['array'] = $array_solicitante;
        $this->Propietarios_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Propietarios_model->fields, $propietario);
        $data['propietario'] = $propietario;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Propietario';
        $data['title'] = TITLE . ' - Editar Propietario';
        $this->load_template('defunciones/propietarios/propietarios_abm', $data);
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
            redirect("defunciones/propietarios/ver/$id", 'refresh');
        }

        $propietario = $this->Propietarios_model->get_one($id);
        if (empty($propietario))
        {
            show_error('No se encontró el Propietario', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Propietarios_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Propietarios_model->get_msg());
                redirect('defunciones/propietarios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Propietarios_model->get_error())
                {
                    $error_msg .= $this->Propietarios_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Propietarios_model->fields, $propietario, TRUE);
        $data['propietario'] = $propietario;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Propietario';
        $data['title'] = TITLE . ' - Eliminar Propietario';
        $this->load_template('defunciones/propietarios/propietarios_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $propietario = $this->Propietarios_model->get_one($id);
        if (empty($propietario))
        {
            show_error('No se encontró el Propietario', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Propietarios_model->fields, $propietario, TRUE);
        $data['propietario'] = $propietario;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Propietario';
        $data['title'] = TITLE . ' - Ver Propietario';
        $this->load_template('defunciones/propietarios/propietarios_abm', $data);
    }
}
