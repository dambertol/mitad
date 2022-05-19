<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Actas extends MY_Controller
{

    /**
     * Controlador de Actas
     * Autor: Leandro
     * Creado: 24/10/2019
     * Modificado: 20/05/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('actasisp/Actas_model');
        $this->load->model('actasisp/Inspectores_model');
        $this->load->model('actasisp/Inspectores_actas_model');
        $this->load->model('actasisp/Motivos_model');
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
        $this->grupos_permitidos = array('admin', 'actasisp_user', 'actasisp_inspector', 'actasisp_consulta_general');
        $this->grupos_solo_consulta = array('actasisp_inspector', 'actasisp_consulta_general');
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
                array('label' => 'Número', 'data' => 'numero', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 8),
                array('label' => 'Fecha', 'data' => 'fecha', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => 'Padron', 'data' => 'padron_municipal', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Domiclio', 'data' => 'domicilio', 'width' => 10),
                array('label' => 'Localidad', 'data' => 'localidad', 'width' => 12),
                array('label' => 'Inspector 1', 'data' => 'inspector_1', 'width' => 11),
                array('label' => 'Inspector 2', 'data' => 'inspector_2', 'width' => 11),
                array('label' => 'Motivo', 'data' => 'motivo', 'width' => 13),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'actas_table',
            'source_url' => 'actasisp/actas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_actas_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Actas';
        $data['title'] = TITLE . ' - Actas';
        $this->load_template('actasisp/actas/actas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("act_actas.id, act_actas.numero, act_actas.tipo, act_actas.fecha, act_actas.estado, act_actas.padron_municipal, CONCAT(COALESCE(domicilios.calle, ''), ' ', COALESCE(domicilios.altura, ''), ' ', COALESCE(domicilios.piso, ''), ' ', COALESCE(domicilios.dpto, ''), ' ', COALESCE(domicilios.manzana, ''), ' ', COALESCE(domicilios.casa, '')) as domicilio, localidades.nombre as localidad, CONCAT(P1.apellido, ', ', P1.nombre, ' (', P1.dni,  ')') as inspector_1, CONCAT(P2.apellido, ', ', P2.nombre, ' (', P2.dni,  ')') as inspector_2, CONCAT(act_motivos.codigo, ' - ', act_motivos.motivo) as motivo")
                ->from('act_actas')
                ->join('act_motivos', 'act_motivos.id = act_actas.motivo_id', 'left')
                ->join('act_inspectores_actas IA1', 'IA1.acta_id = act_actas.id AND IA1.posicion = 1', 'left')
                ->join('act_inspectores I1', 'IA1.inspector_id = I1.id', 'left')
                ->join('personas P1', 'P1.id = I1.persona_id', 'left')
                ->join('act_inspectores_actas IA2', 'IA2.acta_id = act_actas.id AND IA2.posicion = 2', 'left')
                ->join('act_inspectores I2', 'IA2.inspector_id = I2.id', 'left')
                ->join('personas P2', 'P2.id = I2.persona_id', 'left')
                ->join('domicilios', 'domicilios.id = act_actas.domicilio_id', 'left')
                ->join('localidades', 'localidades.id = domicilios.localidad_id', 'left')
                ->add_column('ver', '<a href="actasisp/actas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="actasisp/actas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="actasisp/actas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('actasisp/actas/listar', 'refresh');
        }

        $this->array_tipo_control = $array_tipo = array('Constatada' => 'Constatada', 'Emplazada' => 'Emplazada', 'Notificada' => 'Notificada');
        $this->array_estado_control = $array_estado = array('Anulada' => 'Anulada', 'Baja' => 'Baja', 'Cumplio' => 'Cumplio', 'Pendiente' => 'Pendiente');
        $this->array_inspector_1_control = $array_inspector_1 = $this->get_array('Inspectores', 'inspector', 'id', array(
            'select' => "act_inspectores.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as inspector",
            'join' => array(array('personas', 'personas.id = act_inspectores.persona_id', 'LEFT')),
            'where' => array(array('column' => 'estado', 'value' => 'Activo')),
            'sort_by' => 'personas.apellido, personas.nombre'
                )
        );
        $this->array_inspector_2_control = $array_inspector_2 = $this->get_array('Inspectores', 'inspector', 'id', array(
            'select' => "act_inspectores.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as inspector",
            'join' => array(array('personas', 'personas.id = act_inspectores.persona_id', 'LEFT')),
            'where' => array(array('column' => 'estado', 'value' => 'Activo')),
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array(NULL => '-- Sin Inspector 2 --')
        );
        $this->array_motivo_control = $array_motivo = $this->get_array('Motivos', 'motivo', 'id', array(
            'select' => "id, CONCAT(act_motivos.codigo, ' - ', act_motivos.motivo) as motivo",
            'sort_by' => 'act_motivos.codigo'
                )
        );
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        $this->set_model_validation_rules($this->Domicilios_model);
        $this->set_model_validation_rules($this->Actas_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Domicilios_model->create(array(
                'calle' => $this->input->post('calle'),
                'barrio' => $this->input->post('barrio'),
                'altura' => $this->input->post('altura'),
                'piso' => $this->input->post('piso'),
                'dpto' => $this->input->post('dpto'),
                'manzana' => $this->input->post('manzana'),
                'casa' => $this->input->post('casa'),
                'localidad_id' => $this->input->post('localidad')), FALSE);

            $domicilio_id = $this->Domicilios_model->get_row_id();

            $trans_ok &= $this->Actas_model->create(array(
                'numero' => $this->input->post('numero'),
                'tipo' => $this->input->post('tipo'),
                'fecha' => $this->get_datetime_sql('fecha'),
                'estado' => $this->input->post('estado'),
                'padron_municipal' => $this->input->post('padron_municipal'),
                'domicilio_id' => $domicilio_id,
                'motivo_id' => $this->input->post('motivo'),
                'observaciones' => $this->input->post('observaciones')), FALSE);

            $acta_id = $this->Actas_model->get_row_id();

            $trans_ok &= $this->Inspectores_actas_model->create(array(
                'inspector_id' => $this->input->post('inspector_1'),
                'acta_id' => $acta_id,
                'posicion' => 1), FALSE);

            if ($this->input->post('inspector_2'))
            {
                $trans_ok &= $this->Inspectores_actas_model->create(array(
                    'inspector_id' => $this->input->post('inspector_2'),
                    'acta_id' => $acta_id,
                    'posicion' => 2), FALSE);
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Actas_model->get_msg());
                redirect('actasisp/actas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Domicilios_model->get_error())
                {
                    $error_msg .= $this->Domicilios_model->get_error();
                }
                if ($this->Actas_model->get_error())
                {
                    $error_msg .= $this->Actas_model->get_error();
                }
                if ($this->Inspectores_actas_model->get_error())
                {
                    $error_msg .= $this->Inspectores_actas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Actas_model->fields['tipo']['array'] = $array_tipo;
        $this->Actas_model->fields['estado']['array'] = $array_estado;
        $this->Actas_model->fields['inspector_1']['array'] = $array_inspector_1;
        $this->Actas_model->fields['inspector_2']['array'] = $array_inspector_2;
        $this->Actas_model->fields['motivo']['array'] = $array_motivo;
        $data['fields'] = $this->build_fields($this->Actas_model->fields);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Acta';
        $data['title'] = TITLE . ' - Agregar Acta';
        $data['js'] = 'js/actasisp/base.js';
        $this->load_template('actasisp/actas/actas_abm', $data);
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
            redirect("actasisp/actas/ver/$id", 'refresh');
        }

        $acta = $this->Actas_model->get_one($id);
        if (empty($acta))
        {
            show_error('No se encontró el Acta', 500, 'Registro no encontrado');
        }
        
        $this->array_tipo_control = $array_tipo = array('Constatada' => 'Constatada', 'Emplazada' => 'Emplazada', 'Notificada' => 'Notificada');
        $this->array_estado_control = $array_estado = array('Anulada' => 'Anulada', 'Baja' => 'Baja', 'Cumplio' => 'Cumplio', 'Pendiente' => 'Pendiente');
        $this->array_inspector_1_control = $array_inspector_1 = $this->get_array('Inspectores', 'inspector', 'id', array(
            'select' => "act_inspectores.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as inspector",
            'join' => array(array('personas', 'personas.id = act_inspectores.persona_id', 'LEFT')),
            'where' => array('estado = "Activo" OR act_inspectores.id = ' . $acta->inspector_1_id),
                )
        );
        $this->array_inspector_2_control = $array_inspector_2 = $this->get_array('Inspectores', 'inspector', 'id', array(
            'select' => "act_inspectores.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as inspector",
            'join' => array(array('personas', 'personas.id = act_inspectores.persona_id', 'LEFT')),
            'where' => array('estado = "Activo" OR act_inspectores.id = ' . $acta->inspector_2_id),
                ), array(NULL => '-- Sin Inspector 2 --')
        ); 
        $this->array_motivo_control = $array_motivo = $this->get_array('Motivos', 'motivo', 'id', array(
            'select' => "id, CONCAT(act_motivos.codigo, ' - ', act_motivos.motivo) as motivo",
                )
        );
        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

        $this->set_model_validation_rules($this->Actas_model);
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

                $trans_ok &= $this->Domicilios_model->update(array(
                    'id' => $acta->domicilio_id,
                    'calle' => $this->input->post('calle'),
                    'barrio' => $this->input->post('barrio'),
                    'altura' => $this->input->post('altura'),
                    'piso' => $this->input->post('piso'),
                    'dpto' => $this->input->post('dpto'),
                    'manzana' => $this->input->post('manzana'),
                    'casa' => $this->input->post('casa'),
                    'localidad_id' => $this->input->post('localidad')), FALSE);

                $trans_ok &= $this->Actas_model->update(array(
                    'id' => $this->input->post('id'),
                    'numero' => $this->input->post('numero'),
                    'tipo' => $this->input->post('tipo'),
                    'fecha' => $this->get_datetime_sql('fecha'),
                    'estado' => $this->input->post('estado'),
                    'padron_municipal' => $this->input->post('padron_municipal'),
                    'domicilio_id' => $acta->domicilio_id,
                    'motivo_id' => $this->input->post('motivo'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $trans_ok &= $this->Inspectores_actas_model->update(array(
                    'id' => $acta->inspector_acta_1_id,
                    'inspector_id' => $this->input->post('inspector_1'),
                    'acta_id' => $acta->id,
                    'posicion' => 1), FALSE);

                if ($this->input->post('inspector_2'))
                {
                    if (empty($acta->inspector_acta_2_id))
                    {
                        $trans_ok &= $this->Inspectores_actas_model->create(array(
                            'inspector_id' => $this->input->post('inspector_2'),
                            'acta_id' => $acta->id,
                            'posicion' => 2), FALSE);
                    }
                    else
                    {
                        $trans_ok &= $this->Inspectores_actas_model->update(array(
                            'id' => $acta->inspector_acta_2_id,
                            'inspector_id' => $this->input->post('inspector_2'),
                            'acta_id' => $acta->id,
                            'posicion' => 2), FALSE);
                    }
                }
                else
                {
                    if (!empty($acta->inspector_acta_2_id))
                    {
                        $trans_ok &= $this->Inspectores_actas_model->delete(array('id' => $acta->inspector_acta_2_id), FALSE);
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Actas_model->get_msg());
                    redirect('actasisp/actas/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Domicilios_model->get_error())
                    {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Actas_model->get_error())
                    {
                        $error_msg .= $this->Actas_model->get_error();
                    }
                    if ($this->Inspectores_actas_model->get_error())
                    {
                        $error_msg .= $this->Inspectores_actas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Actas_model->fields['tipo']['array'] = $array_tipo;
        $this->Actas_model->fields['estado']['array'] = $array_estado;
        $this->Actas_model->fields['inspector_1']['array'] = $array_inspector_1;
        $this->Actas_model->fields['inspector_2']['array'] = $array_inspector_2;
        $this->Actas_model->fields['motivo']['array'] = $array_motivo;
        $data['fields'] = $this->build_fields($this->Actas_model->fields, $acta);
        $this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $acta);
        $data['acta'] = $acta;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Acta';
        $data['title'] = TITLE . ' - Editar Acta';
        $this->load_template('actasisp/actas/actas_abm', $data);
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
            redirect("actasisp/actas/ver/$id", 'refresh');
        }

        $acta = $this->Actas_model->get_one($id);
        if (empty($acta))
        {
            show_error('No se encontró el Acta', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Inspectores_actas_model->delete(array('id' => $acta->inspector_acta_1_id), FALSE);
            if (!empty($acta->inspector_acta_2_id))
            {
                $trans_ok &= $this->Inspectores_actas_model->delete(array('id' => $acta->inspector_acta_2_id), FALSE);
            }
            $trans_ok &= $this->Actas_model->delete(array('id' => $this->input->post('id')), FALSE);
            $trans_ok &= $this->Domicilios_model->delete(array('id' => $acta->domicilio_id), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Actas_model->get_msg());
                redirect('actasisp/actas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Domicilios_model->get_error())
                {
                    $error_msg .= $this->Domicilios_model->get_error();
                }
                if ($this->Actas_model->get_error())
                {
                    $error_msg .= $this->Actas_model->get_error();
                }
                if ($this->Inspectores_actas_model->get_error())
                {
                    $error_msg .= $this->Inspectores_actas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Actas_model->fields, $acta, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $acta, TRUE);
        $data['acta'] = $acta;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Acta';
        $data['title'] = TITLE . ' - Eliminar Acta';
        $this->load_template('actasisp/actas/actas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $acta = $this->Actas_model->get_one($id);
        if (empty($acta))
        {
            show_error('No se encontró el Acta', 500, 'Registro no encontrado');
        }
        $data['fields'] = $this->build_fields($this->Actas_model->fields, $acta, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $acta, TRUE);
        $data['acta'] = $acta;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Acta';
        $data['title'] = TITLE . ' - Ver Acta';
        $this->load_template('actasisp/actas/actas_abm', $data);
    }

    public function buscar_padron()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('padron', 'Padrón', 'required|integer|max_length[8]');
        if ($this->form_validation->run() === TRUE)
        {
            $trib_Cuenta = $this->input->post('padron');

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            try
            {
                $http_response_padron = $guzzleHttp->request('GET', "inmuebles/datos", ['query' => ['id' => $trib_Cuenta]]);
                $padron = json_decode($http_response_padron->getBody()->getContents());
            } catch (Exception $e)
            {
                $padron = NULL;
            }

            if (!empty($padron))
            {
                switch ($padron->loca_Codigo)
                {
                    case '00000A':
                        $padron->localidad_id = 56;
                        break;
                    case '00000D':
                        $padron->localidad_id = 63;
                        break;
                    case '00000E':
                        $padron->localidad_id = 58;
                        break;
                    case '00000G':
                        $padron->localidad_id = 57;
                        break;
                    case '00000I':
                        $padron->localidad_id = 66;
                        break;
                    case '00000P':
                        $padron->localidad_id = 61;
                        break;
                    case '00000S':
                        $padron->localidad_id = 60;
                        break;
                    case '00000U':
                        $padron->localidad_id = 64;
                        break;
                    case '00000V':
                        $padron->localidad_id = 67;
                        break;
                    case '00000W':
                        $padron->localidad_id = 65;
                        break;
                    case '00000X':
                        $padron->localidad_id = 55;
                        break;
                    case '00000Z':
                        $padron->localidad_id = 69;
                        break;
                    case '0000CH':
                        $padron->localidad_id = 59;
                        break;
                    case '0000CO':
                        $padron->localidad_id = 62;
                        break;
                    case '0000VP':
                        $padron->localidad_id = 68;
                        break;
                    default:
                        $padron->localidad_id = null;
                        break;
                }
                $data['padron'] = $padron;
            }
            else
            {
                $data['error'] = 'Padrón no encontrado';
            }
        }
        else
        {
            $data['error'] = 'Debe ingresar un padrón válido';
        }
        echo json_encode($data);
    }
}
