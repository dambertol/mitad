<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Concesiones extends MY_Controller
{

    /**
     * Controlador de Concesiones
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Cementerios_model');
        $this->load->model('defunciones/Concesiones_model');
        $this->load->model('defunciones/Difuntos_model');
        $this->load->model('defunciones/Expedientes_model');
        $this->load->model('defunciones/Operaciones_model');
        $this->load->model('defunciones/Ubicaciones_model');
        $this->load->model('defunciones/Solicitantes_model');
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
                array('label' => 'Ficha', 'data' => 'ficha', 'width' => 4, 'class' => 'dt-body-right'),
                array('label' => 'Difunto', 'data' => 'difunto', 'width' => 12),
                array('label' => 'Cementerio', 'data' => 'cementerio', 'width' => 8),
                array('label' => 'Tipo', 'data' => 'ubicacion_tipo', 'width' => 6),
                array('label' => 'Ubicación', 'data' => 'ubicacion', 'width' => 14),
                array('label' => 'Inicio', 'data' => 'inicio', 'width' => 6, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Fin', 'data' => 'fin', 'width' => 6, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Tiempo', 'data' => 'tiempo_concesion', 'width' => 6),
                array('label' => 'Tipo', 'data' => 'tipo_concesion', 'width' => 6),
                array('label' => 'Boleta', 'data' => 'boleta_pago', 'width' => 6),
                array('label' => '', 'data' => 'imprimir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'concesiones_table',
            'source_url' => 'defunciones/concesiones/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_concesiones_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['operacion_id'] = $operacion_id;

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Concesiones';
        $data['title'] = TITLE . ' - Concesiones';
        $this->load_template('defunciones/concesiones/concesiones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('defunciones/datatables_functions_helper');
        $this->datatables
                ->select("df_concesiones.id, df_operaciones.fecha_tramite, df_solicitantes.nombre as solicitante, df_difuntos.ficha, CONCAT(df_difuntos.apellido, ', ', df_difuntos.nombre) as difunto, df_cementerios.nombre as cementerio, df_ubicaciones.tipo as ubicacion_tipo, 1 as ubicacion, df_concesiones.inicio, df_concesiones.fin, df_concesiones.tiempo_concesion, df_concesiones.tipo_concesion, df_operaciones.boleta_pago as boleta_pago, df_ubicaciones.sector as sector, df_ubicaciones.fila as fila, df_ubicaciones.nicho as nicho, df_ubicaciones.cuadro as cuadro, df_ubicaciones.denominacion as denominacion, df_concesiones.operacion_id")
                ->from('df_concesiones')
                ->join('df_operaciones', 'df_operaciones.id = df_concesiones.operacion_id', 'left')
                ->join('df_solicitantes', 'df_solicitantes.id = df_operaciones.solicitante_id', 'left')
                ->join('df_difuntos', 'df_difuntos.id = df_operaciones.difunto_id', 'left')
                ->join('df_ubicaciones', 'df_ubicaciones.id = df_concesiones.ubicacion_id', 'left')
                ->join('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'left')
                ->edit_column('ubicacion', '$1', 'dt_column_difuntos_ubicacion(ubicacion_tipo, sector, fila, nicho, cuadro, denominacion)', TRUE)
                ->add_column('imprimir', '<a href="defunciones/operaciones/imprimir/$1" title="Imprimir" target="_blank" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>', 'operacion_id')
                ->add_column('ver', '<a href="defunciones/concesiones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/concesiones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/concesiones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar($solicitante_id = NULL, $difunto_id = NULL, $nuevo_tramite = 0)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $solicitante_id == NULL || !ctype_digit($solicitante_id) || $difunto_id == NULL || !ctype_digit($difunto_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('defunciones/concesiones/listar', 'refresh');
        }

        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'), array('agregar' => '-- Agregar --'));
        $this->array_tipo_concesion_control = $this->Concesiones_model->fields['tipo_concesion']['array'];
        $this->array_cementerio_control = $array_cementerio = $this->get_array('Cementerios', 'nombre');
        $this->array_tipo_control = $this->Ubicaciones_model->fields['tipo']['array'];
        $this->array_expediente_control = $array_expediente = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));
        $this->array_imprimir_control = $this->Concesiones_model->fields['imprimir']['array'];

        if ($nuevo_tramite == 1)
        {
            $this->Concesiones_model->fields['hora_ingreso'] = array('label' => 'Hora Ingreso', 'type' => 'datetime');
        }

        if ($this->input->post('tipo_concesion') === 'Perpetua')
        {
            unset($this->Concesiones_model->fields['fin']['required']);
        }

        $this->Ubicaciones_model->fields['ub_observaciones'] = $this->Ubicaciones_model->fields['observaciones'];
        unset($this->Ubicaciones_model->fields['observaciones']);

        $this->set_model_validation_rules($this->Concesiones_model);
        if ($this->input->post('ubicacion') === 'agregar')
        {
            $this->set_model_validation_rules($this->Ubicaciones_model);
        }
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
                'tipo_operacion' => '1', // 1 - Concesión
                'user_id' => $this->session->userdata('user_id')
                    ), FALSE);

            $operacion_id = $this->Operaciones_model->get_row_id();

            if ($this->input->post('ubicacion') === 'agregar')
            {
                $trans_ok &= $this->Ubicaciones_model->create(array(
                    'cementerio_id' => $this->input->post('cementerio'),
                    'tipo' => $this->input->post('tipo'),
                    'sector' => $this->input->post('sector'),
                    'cuadro' => $this->input->post('cuadro'),
                    'fila' => $this->input->post('fila'),
                    'nicho' => $this->input->post('nicho'),
                    'denominacion' => $this->input->post('denominacion'),
                    'nomenclatura' => $this->input->post('nomenclatura'),
                    'observaciones' => $this->input->post('ub_observaciones')
                        ), FALSE);

                $ubicacion_id = $this->Ubicaciones_model->get_row_id();
            }
            else
            {
                $ubicacion_id = $this->input->post('ubicacion');
            }

            $trans_ok &= $this->Concesiones_model->create(array(
                'operacion_id' => $operacion_id,
                'tipo_concesion' => $this->input->post('tipo_concesion'),
                'ubicacion_id' => $ubicacion_id,
                'tiempo_concesion' => $this->input->post('tiempo_concesion'),
                'inicio' => $this->get_date_sql('inicio'),
                'fin' => $this->get_date_sql('fin'),
                'hora_ingreso' => $this->get_datetime_sql('hora_ingreso'),
                'ingreso' => $nuevo_tramite == 0 ? '0' : '1'), FALSE);

            $concesion_id = $this->Concesiones_model->get_row_id();

            $trans_ok &= $this->Difuntos_model->update(array(
                'id' => $difunto_id,
                'ubicacion_id' => $ubicacion_id,
                'ultima_concesion_id' => $concesion_id
                    ), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Concesiones_model->get_msg());
                if ($this->input->post('imprimir') === 'SI')
                {
                    redirect("defunciones/concesiones/listar/$operacion_id", 'refresh');
                }
                else
                {
                    redirect("defunciones/concesiones/listar", 'refresh');
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
                if ($this->Ubicaciones_model->get_error())
                {
                    $error_msg .= $this->Ubicaciones_model->get_error();
                }
                if ($this->Concesiones_model->get_error())
                {
                    $error_msg .= $this->Concesiones_model->get_error();
                }
                if ($this->Difuntos_model->get_error())
                {
                    $error_msg .= $this->Difuntos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

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

        $this->Ubicaciones_model->fields['cementerio']['array'] = $array_cementerio;
        $data['fields_ubicacion'] = $this->build_fields($this->Ubicaciones_model->fields);
        $this->Concesiones_model->fields['expediente']['array'] = $array_expediente;
        $this->Concesiones_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Concesiones_model->fields);
        $data['nuevo'] = $nuevo_tramite;
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Concesión';
        $data['title'] = TITLE . ' - Agregar Concesión';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/concesiones/concesiones_abm', $data);
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
            redirect("defunciones/concesiones/ver/$id", 'refresh');
        }

        $this->array_ubicacion_control = $array_ubicacion = $this->get_array('Ubicaciones', 'ubicacion', 'id', array('select' => array("df_ubicaciones.id, CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"), 'join' => array(array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT')), 'sort_by' => 'df_cementerios.nombre, tipo, sector, cuadro, fila, nicho, denominacion'), array(NULL => NULL));
        $this->array_tipo_concesion_control = $this->Concesiones_model->fields['tipo_concesion']['array'];
        $this->array_cementerio_control = $array_cementerio = $this->get_array('Cementerios', 'nombre');
        $this->array_tipo_control = $this->Ubicaciones_model->fields['tipo']['array'];
        $this->array_expediente_control = $array_expediente = $this->get_array('Expedientes', 'descripcion', 'id', array('select' => array("id, CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as descripcion"), 'sort_by' => 'ejercicio, numero'), array(NULL => '-- Sin Asignar --'));

        $concesion = $this->Concesiones_model->get_one($id);
        if (empty($concesion))
        {
            show_error('No se encontró la Concesión', 500, 'Registro no encontrado');
        }

        $this->Concesiones_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date', 'readonly' => TRUE);
        $this->Concesiones_model->fields['hora_ingreso'] = array('label' => 'Hora Ingreso', 'type' => 'datetime');
        $this->Concesiones_model->fields['ubicacion']['disabled'] = TRUE;
        unset($this->Concesiones_model->fields['imprimir']);

        if ($this->input->post('tipo_concesion') === 'Perpetua')
        {
            unset($this->Concesiones_model->fields['fin']['required']);
        }

        $this->set_model_validation_rules($this->Concesiones_model);
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
                    'id' => $concesion->operacion_id,
                    'fecha_tramite' => $this->get_date_sql('fecha_tramite'),
                    'boleta_pago' => $this->input->post('boleta_pago'),
                    'fecha_pago' => $this->get_date_sql('fecha_pago'),
                    'expediente_id' => $this->input->post('expediente'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $trans_ok &= $this->Concesiones_model->update(array(
                    'id' => $this->input->post('id'),
                    'inicio' => $this->get_date_sql('inicio'),
                    'fin' => $this->get_date_sql('fin'),
                    'tiempo_concesion' => $this->input->post('tiempo_concesion'),
                    'tipo_concesion' => $this->input->post('tipo_concesion'),
                    'hora_ingreso' => $this->get_datetime_sql('hora_ingreso')), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Concesiones_model->get_msg());
                    redirect('defunciones/concesiones/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Operaciones_model->get_error())
                    {
                        $error_msg .= $this->Operaciones_model->get_error();
                    }
                    if ($this->Concesiones_model->get_error())
                    {
                        $error_msg .= $this->Concesiones_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $solicitante = $this->Solicitantes_model->get(array('id' => $concesion->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($concesion->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $this->Concesiones_model->fields['expediente']['array'] = $array_expediente;
        $this->Concesiones_model->fields['ubicacion']['array'] = $array_ubicacion;
        $data['fields'] = $this->build_fields($this->Concesiones_model->fields, $concesion);
        $data['concesion'] = $concesion;
        $data['nuevo'] = 1;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Concesión';
        $data['title'] = TITLE . ' - Editar Concesión';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/concesiones/concesiones_abm', $data);
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
            redirect("defunciones/concesiones/ver/$id", 'refresh');
        }

        $concesion = $this->Concesiones_model->get_one($id);
        if (empty($concesion))
        {
            show_error('No se encontró la Concesión', 500, 'Registro no encontrado');
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
            $difunto = $this->Difuntos_model->get(array('id' => $concesion->difunto_id));
            if ($difunto->ultima_concesion_id === $concesion->id)
            {
                $concesiones_difunto = $this->Concesiones_model->get(array(
                    'id !=' => $concesion->id,
                    'where' => array("operacion_id IN (SELECT id FROM df_operaciones WHERE difunto_id=$difunto->id)"),
                    'sort_by' => 'inicio', 'sort_direction' => 'desc'
                ));
                if (empty($concesiones_difunto))
                {
                    $trans_ok &= $this->Difuntos_model->update(array('id' => $difunto->id, 'ultima_concesion_id' => 'NULL'), FALSE);
                }
                else
                {
                    $trans_ok &= $this->Difuntos_model->update(array('id' => $difunto->id, 'ultima_concesion_id' => $concesiones_difunto[0]->id), FALSE);
                }
            }
            $trans_ok &= $this->Concesiones_model->delete(array('id' => $this->input->post('id')), FALSE);
            $trans_ok &= $this->Operaciones_model->delete(array('id' => $concesion->operacion_id), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Concesiones_model->get_msg());
                redirect('defunciones/concesiones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Difuntos_model->get_error())
                {
                    $error_msg .= $this->Difuntos_model->get_error();
                }
                if ($this->Concesiones_model->get_error())
                {
                    $error_msg .= $this->Concesiones_model->get_error();
                }
                if ($this->Operaciones_model->get_error())
                {
                    $error_msg .= $this->Operaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Concesiones_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date');
        $this->Concesiones_model->fields['hora_ingreso'] = array('label' => 'Hora Ingreso', 'type' => 'datetime');
        unset($this->Concesiones_model->fields['imprimir']);

        $solicitante = $this->Solicitantes_model->get(array('id' => $concesion->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($concesion->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $data['fields'] = $this->build_fields($this->Concesiones_model->fields, $concesion, TRUE);
        $data['concesion'] = $concesion;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Concesión';
        $data['title'] = TITLE . ' - Eliminar Concesión';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/concesiones/concesiones_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $concesion = $this->Concesiones_model->get_one($id);
        if (empty($concesion))
        {
            show_error('No se encontró la Concesión', 500, 'Registro no encontrado');
        }

        $this->Concesiones_model->fields['fecha_carga'] = array('label' => 'Fecha Carga', 'type' => 'date');
        $this->Concesiones_model->fields['hora_ingreso'] = array('label' => 'Hora Ingreso', 'type' => 'datetime');
        unset($this->Concesiones_model->fields['imprimir']);

        $solicitante = $this->Solicitantes_model->get(array('id' => $concesion->solicitante_id));
        if (empty($solicitante))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }
        $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);

        $difunto = $this->Difuntos_model->get_one($concesion->difunto_id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }
        $data['fields_difunto'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);

        $data['fields'] = $this->build_fields($this->Concesiones_model->fields, $concesion, TRUE);
        $data['concesion'] = $concesion;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Concesión';
        $data['title'] = TITLE . ' - Ver Concesión';
        $data['js'][] = 'js/defunciones/base.js';
        $this->load_template('defunciones/concesiones/concesiones_abm', $data);
    }

    public function imprimir($id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $concesion = $this->Concesiones_model->get(array(
            'id' => $id,
            'join' => array(
                array(
                    'table' => 'df_operaciones',
                    'where' => 'df_operaciones.id=df_concesiones.operacion_id',
                    'columnas' => array('fecha', 'fecha_tramite', 'fecha_pago', 'boleta_pago', 'df_operaciones.observaciones', 'solicitante_id', 'difunto_id')
                ),
                array(
                    'table' => 'df_difuntos',
                    'where' => 'df_difuntos.id=df_operaciones.difunto_id',
                    'columnas' => array("df_difuntos.nombre + ' ' + df_difuntos.apellido AS difunto", "defuncion")
                ),
                array(
                    'table' => 'df_solicitantes',
                    'where' => 'df_solicitantes.id=df_operaciones.solicitante_id',
                    'columnas' => array("COALESCE(df_solicitantes.dni, 'Sin DNI') AS solicitante_dni", "df_solicitantes.nombre AS solicitante", "df_solicitantes.domicilio AS solicitante_domicilio", "df_solicitantes.telefono AS solicitante_telefono")
                ),
                array(
                    'table' => 'df_ubicaciones',
                    'where' => 'df_ubicaciones.id=df_concesiones.ubicacion_id'
                ),
                array(
                    'table' => 'df_cementerios',
                    'where' => 'df_cementerios.id=df_ubicaciones.cementerio_id',
                    'columnas' => array("CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion")
                ),
                array(
                    'table' => 'df_expedientes', 'type' => 'left',
                    'where' => 'df_expedientes.id=df_operaciones.expediente_id',
                    'columnas' => array("CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as expediente")
                )
            )
        ));
        if (empty($concesion))
        {
            show_error('No se encontró la Concesión', 500, 'Registro no encontrado');
        }

        $data['concesion'] = $concesion;
        $html = $this->load->view('defunciones/concesiones/concesiones_pdf', $data, TRUE);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'format' => 'A4',
            'margin_left' => 6,
            'margin_right' => 6,
            'margin_top' => 6,
            'margin_bottom' => 6,
            'margin_header' => 9,
            'margin_footer' => 9
        ]);
        $mpdf->SetDisplayMode('fullwidth');
        $mpdf->simpleTables = true;
        $mpdf->SetTitle("Vencimiento Concesión");
        $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output("vencimiento_concesion.pdf", 'I');
    }
}
