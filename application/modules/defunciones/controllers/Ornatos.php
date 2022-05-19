<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ornatos extends MY_Controller
{

    /**
     * Controlador de Ornatos
     * Autor: Leandro
     * Creado: 05/12/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Difuntos_model');
        $this->load->model('defunciones/Expedientes_model');
        $this->load->model('defunciones/Ornatos_model');
        $this->load->model('defunciones/Operaciones_model');
        $this->load->model('defunciones/Constructores_model');
        $this->load->model('defunciones/Solicitantes_model');
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
                array('label' => 'Solicitante', 'data' => 'solicitante', 'width' => 12),
                array('label' => 'Difunto', 'data' => 'difunto', 'width' => 12),
                array('label' => 'Cementerio', 'data' => 'cementerio', 'width' => 9),
                array('label' => 'Tipo', 'data' => 'ubicacion_tipo', 'width' => 6),
                array('label' => 'Ubicación', 'data' => 'ubicacion', 'width' => 18),
                array('label' => 'Constructor', 'data' => 'constructor', 'width' => 11),
                array('label' => 'Tipo de Ornato', 'data' => 'tipo_ornato', 'width' => 10),
                array('label' => 'Boleta', 'data' => 'boleta_pago', 'width' => 7),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'ornatos_table',
            'source_url' => 'defunciones/ornatos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_ornatos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['operacion_id'] = $operacion_id;

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Ornatos';
        $data['title'] = TITLE . ' - Ornatos';
        $this->load_template('defunciones/ornatos/ornatos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('defunciones/datatables_functions_helper');
        $this->datatables
                ->select("df_ornatos.id, df_operaciones.fecha_tramite, df_solicitantes.nombre as solicitante, CONCAT(df_difuntos.apellido, ', ', df_difuntos.nombre) as difunto, df_cementerios.nombre as cementerio, df_ubicaciones.tipo as ubicacion_tipo, 1 as ubicacion, df_constructores.nombre as constructor, df_ornatos.tipo_ornato, df_operaciones.boleta_pago as boleta_pago, df_ubicaciones.sector as sector, df_ubicaciones.fila as fila, df_ubicaciones.nicho as nicho, df_ubicaciones.cuadro as cuadro, df_ubicaciones.denominacion as denominacion, df_ornatos.operacion_id")
                ->from('df_ornatos')
                ->join('df_operaciones', 'df_operaciones.id = df_ornatos.operacion_id', 'left')
                ->join('df_solicitantes', 'df_solicitantes.id = df_operaciones.solicitante_id', 'left')
                ->join('df_difuntos', 'df_difuntos.id = df_operaciones.difunto_id', 'left')
                ->join('df_constructores', 'df_constructores.id = df_ornatos.constructor_id', 'left')
                ->join('df_ubicaciones', 'df_ubicaciones.id = df_ornatos.ubicacion_id', 'left')
                ->join('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'left')
                ->edit_column('ubicacion', '$1', 'dt_column_difuntos_ubicacion(ubicacion_tipo, sector, fila, nicho, cuadro, denominacion)', TRUE)
                ->add_column('imprimir', '<a href="defunciones/operaciones/imprimir/$1" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>', 'operacion_id')
                ->add_column('ver', '<a href="defunciones/ornatos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/ornatos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/ornatos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/ornatos/listar', 'refresh');
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
            redirect("defunciones/tramites/iniciar/$solicitante_id/$difunto_id/ornatos", 'refresh');
        }

        $this->array_constructor_control = $array_constructor = $this->get_array('Constructores', 'nombre');
        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'), array(NULL => NULL));
        $this->array_expediente_control = $array_expediente = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));
        $this->array_imprimir_control = $this->Ornatos_model->fields['imprimir']['array'];

        $this->set_model_validation_rules($this->Ornatos_model);
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
                'tipo_operacion' => '2', // 2 - Ornato
                'user_id' => $this->session->userdata('user_id')
                    ), FALSE);

            $operacion_id = $this->Operaciones_model->get_row_id();

            $trans_ok &= $this->Ornatos_model->create(array(
                'operacion_id' => $operacion_id,
                'tipo_ornato' => $this->input->post('tipo_ornato'),
                'constructor_id' => $this->input->post('constructor'),
                'ubicacion_id' => $difunto->ubicacion_id
                    ), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Ornatos_model->get_msg());
                if ($this->input->post('imprimir') === 'SI')
                {
                    redirect("defunciones/ornatos/listar/$operacion_id", 'refresh');
                }
                else
                {
                    redirect("defunciones/ornatos/listar", 'refresh');
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
                if ($this->Ornatos_model->get_error())
                {
                    $error_msg .= $this->Ornatos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $empty_ornato = new stdClass();
        $empty_ornato->tipo_ornato = NULL;
        $empty_ornato->fecha_tramite = NULL;
        $empty_ornato->constructor_id = NULL;
        $empty_ornato->fecha_pago = NULL;
        $empty_ornato->boleta_pago = NULL;
        $empty_ornato->expediente_id = NULL;
        $empty_ornato->ubicacion_id = $difunto->ubicacion_id;
        $empty_ornato->observaciones = NULL;
        $empty_ornato->imprimir = NULL;

        $this->Ornatos_model->fields['constructor']['array'] = $array_constructor;
        $this->Ornatos_model->fields['expediente']['array'] = $array_expediente;
        $this->Ornatos_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Ornatos_model->fields, $empty_ornato);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Ornato';
        $data['title'] = TITLE . ' - Agregar Ornato';
        $this->load_template('defunciones/ornatos/ornatos_abm', $data);
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
            redirect("defunciones/ornatos/ver/$id", 'refresh');
        }

        $this->array_constructor_control = $array_constructor = $this->get_array('Constructores', 'nombre');
        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'), array(NULL => NULL));
        $this->array_expediente_control = $array_expediente = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));

        $ornato = $this->Ornatos_model->get_one($id);
        if (empty($ornato))
        {
            show_error('No se encontró el Ornato', 500, 'Registro no encontrado');
        }

        $this->Ornatos_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date', 'readonly' => TRUE);
        unset($this->Ornatos_model->fields['imprimir']);

        $this->set_model_validation_rules($this->Ornatos_model);
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
                    'id' => $ornato->operacion_id,
                    'fecha_tramite' => $this->get_date_sql('fecha_tramite'),
                    'boleta_pago' => $this->input->post('boleta_pago'),
                    'fecha_pago' => $this->get_date_sql('fecha_pago'),
                    'expediente_id' => $this->input->post('expediente'),
                    'observaciones' => $this->input->post('observaciones')
                        ), FALSE);

                $trans_ok &= $this->Ornatos_model->update(array(
                    'id' => $this->input->post('id'),
                    'tipo_ornato' => $this->input->post('tipo_ornato'),
                    'constructor_id' => $this->input->post('constructor')
                        ), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Ornatos_model->get_msg());
                    redirect('defunciones/ornatos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Operaciones_model->get_error())
                    {
                        $error_msg .= $this->Operaciones_model->get_error();
                    }
                    if ($this->Ornatos_model->get_error())
                    {
                        $error_msg .= $this->Ornatos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $solicitante = $this->Solicitantes_model->get(array('id' => $ornato->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($ornato->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $this->Ornatos_model->fields['constructor']['array'] = $array_constructor;
        $this->Ornatos_model->fields['expediente']['array'] = $array_expediente;
        $this->Ornatos_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Ornatos_model->fields, $ornato);
        $data['ornato'] = $ornato;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Ornato';
        $data['title'] = TITLE . ' - Editar Ornato';
        $this->load_template('defunciones/ornatos/ornatos_abm', $data);
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
            redirect("defunciones/ornatos/ver/$id", 'refresh');
        }

        $ornato = $this->Ornatos_model->get_one($id);
        if (empty($ornato))
        {
            show_error('No se encontró el Ornato', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Ornatos_model->delete(array('id' => $this->input->post('id')), FALSE);
            $trans_ok &= $this->Operaciones_model->delete(array('id' => $ornato->operacion_id), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Ornatos_model->get_msg());
                redirect('defunciones/ornatos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Ornatos_model->get_error())
                {
                    $error_msg .= $this->Ornatos_model->get_error();
                }
                if ($this->Operaciones_model->get_error())
                {
                    $error_msg .= $this->Operaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Ornatos_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date');
        unset($this->Ornatos_model->fields['imprimir']);

        $solicitante = $this->Solicitantes_model->get(array('id' => $ornato->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($ornato->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $data['fields'] = $this->build_fields($this->Ornatos_model->fields, $ornato, TRUE);
        $data['ornato'] = $ornato;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Ornato';
        $data['title'] = TITLE . ' - Eliminar Ornato';
        $this->load_template('defunciones/ornatos/ornatos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $ornato = $this->Ornatos_model->get_one($id);
        if (empty($ornato))
        {
            show_error('No se encontró el Ornato', 500, 'Registro no encontrado');
        }

        $this->Ornatos_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date');
        unset($this->Ornatos_model->fields['imprimir']);

        $solicitante = $this->Solicitantes_model->get(array('id' => $ornato->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($ornato->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $data['fields'] = $this->build_fields($this->Ornatos_model->fields, $ornato, TRUE);
        $data['ornato'] = $ornato;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Ornato';
        $data['title'] = TITLE . ' - Ver Ornato';
        $this->load_template('defunciones/ornatos/ornatos_abm', $data);
    }
}
