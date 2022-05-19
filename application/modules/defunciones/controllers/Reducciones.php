<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reducciones extends MY_Controller
{

    /**
     * Controlador de Reducciones
     * Autor: Leandro
     * Creado: 05/12/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Expedientes_model');
        $this->load->model('defunciones/Difuntos_model');
        $this->load->model('defunciones/Reducciones_model');
        $this->load->model('defunciones/Solicitantes_model');
        $this->load->model('defunciones/Operaciones_model');
        $this->load->model('defunciones/Ubicaciones_model');
        $this->grupos_permitidos = array('admin', 'defunciones_user', 'defunciones_consulta_general');
        $this->grupos_solo_consulta = array('defunciones_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar($operacion_id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Fecha', 'data' => 'fecha_tramite', 'width' => 7, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Solicitante', 'data' => 'solicitante', 'width' => 17),
                array('label' => 'Difunto', 'data' => 'difunto', 'width' => 17),
                array('label' => 'Cementerio', 'data' => 'cementerio', 'width' => 11),
                array('label' => 'Tipo', 'data' => 'ubicacion_tipo', 'width' => 8),
                array('label' => 'Ubicación', 'data' => 'ubicacion', 'width' => 24),
                array('label' => 'Boleta', 'data' => 'boleta_pago', 'width' => 8),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'reducciones_table',
            'source_url' => 'defunciones/reducciones/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_reducciones_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['operacion_id'] = $operacion_id;

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Reducciones';
        $data['title'] = TITLE . ' - Reducciones';
        $this->load_template('defunciones/reducciones/reducciones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('defunciones/datatables_functions_helper');
        $this->datatables
                ->select("df_reducciones.id, df_operaciones.fecha_tramite, df_solicitantes.nombre as solicitante, CONCAT(df_difuntos.apellido, ', ', df_difuntos.nombre) as difunto, df_cementerios.nombre as cementerio, df_ubicaciones.tipo as ubicacion_tipo, 1 as ubicacion, df_operaciones.boleta_pago as boleta_pago, df_ubicaciones.sector as sector, df_ubicaciones.fila as fila, df_ubicaciones.nicho as nicho, df_ubicaciones.cuadro as cuadro, df_ubicaciones.denominacion as denominacion, df_reducciones.operacion_id")
                ->from('df_reducciones')
                ->join('df_operaciones', 'df_operaciones.id = df_reducciones.operacion_id', 'left')
                ->join('df_solicitantes', 'df_solicitantes.id = df_operaciones.solicitante_id', 'left')
                ->join('df_difuntos', 'df_difuntos.id = df_operaciones.difunto_id', 'left')
                ->join('df_ubicaciones', 'df_ubicaciones.id = df_reducciones.ubicacion_id', 'left')
                ->join('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'left')
                ->edit_column('ubicacion', '$1', 'dt_column_difuntos_ubicacion(ubicacion_tipo, sector, fila, nicho, cuadro, denominacion)', TRUE)
                ->add_column('imprimir', '<a href="defunciones/operaciones/imprimir/$1" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>', 'operacion_id')
                ->add_column('ver', '<a href="defunciones/reducciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/reducciones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/reducciones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar($solicitante_id = NULL, $difunto_id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $solicitante_id == NULL || !ctype_digit($solicitante_id) || $difunto_id == NULL || !ctype_digit($difunto_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('defunciones/reducciones/listar', 'refresh');
        }

        if ($solicitante_id !== NULL)
        {
            $solicitante = $this->Solicitantes_model->get(array('id' => $solicitante_id));
            if (empty($solicitante))
            {
                show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
            }
            $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);
        }

        if ($difunto_id !== NULL)
        {
            $difunto = $this->Difuntos_model->get_one($difunto_id);
            if (empty($difunto))
            {
                show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
            }
            $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);
        }

        if (empty($difunto->ubicacion_id))
        {
            $this->session->set_flashdata('error', 'El difunto no posee una ubicación actual');
            redirect("defunciones/tramites/iniciar/$solicitante_id/$difunto_id/reducciones", 'refresh');
        }

        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'), array(NULL => NULL));
        $this->array_expediente_control = $array_expediente = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));
        $this->array_imprimir_control = $this->Reducciones_model->fields['imprimir']['array'];

        $this->set_model_validation_rules($this->Reducciones_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Operaciones_model->create(array(
                'fecha' => date_format(new DateTime(), 'Y-m-d'),
                'fecha_tramite' => $this->get_date_sql('fecha_tramite'),
                'solicitante_id' => $solicitante_id,
                'difunto_id' => $difunto_id,
                'boleta_pago' => $this->input->post('boleta_pago'),
                'fecha_pago' => $this->get_date_sql('fecha_pago'),
                'expediente_id' => $this->input->post('expediente'),
                'observaciones' => $this->input->post('observaciones'),
                'tipo_operacion' => '3', // 3 - Reducción
                'user_id' => $this->session->userdata('user_id')), FALSE);

            $operacion_id = $this->Operaciones_model->get_row_id();

            $trans_ok &= $this->Reducciones_model->create(array(
                'operacion_id' => $operacion_id,
                'ubicacion_id' => $difunto->ubicacion_id,), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Reducciones_model->get_msg());
                if ($this->input->post('imprimir') === 'SI')
                {
                    redirect("defunciones/reducciones/listar/$operacion_id", 'refresh');
                }
                else
                {
                    redirect("defunciones/reducciones/listar", 'refresh');
                }
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Operaciones_model->get_error())
                {
                    $error_msg .= $this->Operaciones_model->get_error();
                }
                if ($this->Reducciones_model->get_error())
                {
                    $error_msg .= $this->Reducciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $empty_reduccion = new stdClass();
        $empty_reduccion->fecha_realizacion = NULL;
        $empty_reduccion->fecha_tramite = NULL;
        $empty_reduccion->fecha_pago = NULL;
        $empty_reduccion->boleta_pago = NULL;
        $empty_reduccion->expediente_id = NULL;
        $empty_reduccion->ubicacion_id = $difunto->ubicacion_id;
        $empty_reduccion->observaciones = NULL;
        $empty_reduccion->imprimir = NULL;

        $this->Reducciones_model->fields['expediente']['array'] = $array_expediente;
        $this->Reducciones_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Reducciones_model->fields, $empty_reduccion);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Reducción';
        $data['title'] = TITLE . ' - Agregar Reducción';
        $this->load_template('defunciones/reducciones/reducciones_abm', $data);
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
            redirect("defunciones/reducciones/ver/$id", 'refresh');
        }

        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'), array(NULL => NULL));
        $this->array_expediente_control = $array_expediente = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));
        $reduccion = $this->Reducciones_model->get_one($id);
        if (empty($reduccion))
        {
            show_error('No se encontró la Reducción', 500, 'Registro no encontrado');
        }

        $this->Reducciones_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date', 'readonly' => TRUE);
        unset($this->Reducciones_model->fields['imprimir']);

        $this->set_model_validation_rules($this->Reducciones_model);
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

                $trans_ok &= $this->Operaciones_model->update(array(
                    'id' => $reduccion->operacion_id,
                    'fecha_tramite' => $this->get_date_sql('fecha_tramite'),
                    'boleta_pago' => $this->input->post('boleta_pago'),
                    'fecha_pago' => $this->get_date_sql('fecha_pago'),
                    'expediente_id' => $this->input->post('expediente'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $trans_ok &= $this->Reducciones_model->update(array(
                    'id' => $this->input->post('id'),
                    'fecha_realizacion' => $this->get_date_sql('fecha_realizacion')), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Reducciones_model->get_msg());
                    redirect('defunciones/reducciones/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Operaciones_model->get_error())
                    {
                        $error_msg .= $this->Operaciones_model->get_error();
                    }
                    if ($this->Reducciones_model->get_error())
                    {
                        $error_msg .= $this->Reducciones_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $solicitante = $this->Solicitantes_model->get(array('id' => $reduccion->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($reduccion->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $this->Reducciones_model->fields['expediente']['array'] = $array_expediente;
        $this->Reducciones_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Reducciones_model->fields, $reduccion);
        $data['reduccion'] = $reduccion;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Reducción';
        $data['title'] = TITLE . ' - Editar Reducción';
        $this->load_template('defunciones/reducciones/reducciones_abm', $data);
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
            redirect("defunciones/reducciones/ver/$id", 'refresh');
        }

        $reduccion = $this->Reducciones_model->get_one($id);
        if (empty($reduccion))
        {
            show_error('No se encontró la Reducción', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Reducciones_model->delete(array('id' => $this->input->post('id')), FALSE);
            $trans_ok &= $this->Operaciones_model->delete(array('id' => $reduccion->operacion_id), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Reducciones_model->get_msg());
                redirect('defunciones/reducciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Reducciones_model->get_error())
                {
                    $error_msg .= $this->Reducciones_model->get_error();
                }
                if ($this->Operaciones_model->get_error())
                {
                    $error_msg .= $this->Operaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Reducciones_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date');
        unset($this->Reducciones_model->fields['imprimir']);

        $solicitante = $this->Solicitantes_model->get(array('id' => $reduccion->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($reduccion->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $data['fields'] = $this->build_fields($this->Reducciones_model->fields, $reduccion, TRUE);
        $data['reduccion'] = $reduccion;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Reducción';
        $data['title'] = TITLE . ' - Eliminar Reducción';
        $this->load_template('defunciones/reducciones/reducciones_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $reduccion = $this->Reducciones_model->get_one($id);
        if (empty($reduccion))
        {
            show_error('No se encontró la Reducción', 500, 'Registro no encontrado');
        }

        $this->Reducciones_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date');
        unset($this->Reducciones_model->fields['imprimir']);

        $solicitante = $this->Solicitantes_model->get(array('id' => $reduccion->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($reduccion->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $data['fields'] = $this->build_fields($this->Reducciones_model->fields, $reduccion, TRUE);
        $data['reduccion'] = $reduccion;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Reducción';
        $data['title'] = TITLE . ' - Ver Reducción';
        $this->load_template('defunciones/reducciones/reducciones_abm', $data);
    }
}
