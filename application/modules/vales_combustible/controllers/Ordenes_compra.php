<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ordenes_compra extends MY_Controller
{

    /**
     * Controlador de Órdenes de Compra
     * Autor: Leandro
     * Creado: 13/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Ordenes_compra_model');
        $this->load->model('vales_combustible/Ordenes_compra_detalles_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('vales_combustible/Facturas_model');
        $this->load->model('vales_combustible/Vales_model');
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
                array('label' => 'Fecha', 'data' => 'fecha', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 32),
                array('label' => 'Ejercicio', 'data' => 'ejercicio', 'class' => 'dt-body-right', 'width' => 15),
                array('label' => 'Número', 'data' => 'numero', 'class' => 'dt-body-right', 'width' => 15),
                array('label' => 'Total', 'data' => 'total', 'render' => 'money', 'class' => 'dt-body-right', 'width' => 32),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'ordenes_compra_table',
            'source_url' => 'vales_combustible/ordenes_compra/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_ordenes_compra_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de órdenes de comra';
        $data['title'] = TITLE . ' - Órdenes de compra';
        $this->load_template('vales_combustible/ordenes_compra/ordenes_compra_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, fecha, ejercicio, numero, total')
                ->from('vc_ordenes_compra')
                ->add_column('ver', '<a href="vales_combustible/ordenes_compra/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/ordenes_compra/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/ordenes_compra/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect("vales_combustible/ordenes_compra/listar", 'refresh');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->set_model_validation_rules($this->Ordenes_compra_model);
        $this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
        if ($this->input->post('cant_rows'))
        {
            $cant_rows = 1;
            for ($i = 1; $i <= $cant_rows; $i++)
            {
                $this->form_validation->set_rules('tipo_combustible_' . $i, 'Tipo de Combustible ' . $i, 'required|callback_control_combo[tipo_combustible]');
                $this->form_validation->set_rules('litros_' . $i, 'M³/Litros ' . $i, 'required|numeric');
                $this->form_validation->set_rules('costo_unitario_' . $i, 'Costo ' . $i, 'required|numeric');
                $this->form_validation->set_rules('costo_total_' . $i, 'Costo Total ' . $i, 'required|numeric');
            }
        }

        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Ordenes_compra_model->create(array(
                'fecha' => $fecha->format('Y-m-d'),
                'ejercicio' => $this->input->post('ejercicio'),
                'numero' => $this->input->post('numero'),
                'total' => $this->input->post('total')), FALSE);
            $orden_compra_id = $this->Ordenes_compra_model->get_row_id();
            for ($i = 1; $i <= $cant_rows; $i++)
            {
                $tipo_combustible_id = $this->input->post('tipo_combustible_' . $i);
                $litros = $this->input->post('litros_' . $i);
                $costo_unitario = $this->input->post('costo_unitario_' . $i);
                $trans_ok &= $this->Ordenes_compra_detalles_model->create(array(
                    'orden_compra_id' => $orden_compra_id,
                    'tipo_combustible_id' => $tipo_combustible_id,
                    'litros' => $litros,
                    'costo_unitario' => $costo_unitario), FALSE);
            }
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Ordenes_compra_model->get_msg());
                redirect("vales_combustible/ordenes_compra/listar", 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Ordenes_compra_model->get_error())
                {
                    $error_msg .= $this->Ordenes_compra_model->get_error();
                }
                if ($this->Ordenes_compra_detalles_model->get_error())
                {
                    $error_msg .= $this->Ordenes_compra_detalles_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Ordenes_compra_model->fields);

        $rows = $this->form_validation->set_value('cant_rows', 1);
        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++)
        {
            $fake_model_fields = array(
                "tipo_combustible_$i" => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                "litros_$i" => array('label' => 'M³/Litros', 'type' => 'numeric', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_unitario_$i" => array('label' => 'Costo Unitario', 'type' => 'money', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_total_$i" => array('label' => 'Costo Total', 'type' => 'money', 'required' => TRUE, 'readonly' => TRUE)
            );

            $fake_model_fields["tipo_combustible_$i"]['array'] = $array_tipo_combustible;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, NULL, FALSE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar orden de compra';
        $data['title'] = TITLE . ' - Agregar orden de compra';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/ordenes_compra/ordenes_compra_abm', $data);
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
            redirect("vales_combustible/ordenes_compra/ver/$id", 'refresh');
        }

        $orden_compra = $this->Ordenes_compra_model->get(array('id' => $id));
        if (empty($orden_compra))
        {
            show_error('No se encontró la Orden de Compra', 500, 'Registro no encontrado');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->set_model_validation_rules($this->Ordenes_compra_model);
        $this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
        if ($this->input->post('cant_rows'))
        {
            $cant_rows = 1;
            for ($i = 1; $i <= $cant_rows; $i++)
            {
                $this->form_validation->set_rules('tipo_combustible_' . $i, 'Tipo de Combustible ' . $i, 'required|callback_control_combo[tipo_combustible]');
                $this->form_validation->set_rules('litros_' . $i, 'M³/Litros ' . $i, 'required|numeric');
                $this->form_validation->set_rules('costo_unitario_' . $i, 'Costo ' . $i, 'required|numeric');
                $this->form_validation->set_rules('costo_total_' . $i, 'Costo Total ' . $i, 'required|numeric');
            }
        }

        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Ordenes_compra_model->update(array(
                    'id' => $this->input->post('id'),
                    'fecha' => $fecha->format('Y-m-d'),
                    'ejercicio' => $this->input->post('ejercicio'),
                    'numero' => $this->input->post('numero'),
                    'total' => $this->input->post('total')), FALSE);

                $post_detalles_update = array();
                $post_detalles_create = array();
                $detalles_actuales = $this->Ordenes_compra_detalles_model->get(array('orden_compra_id' => $id));
                for ($i = 1; $i <= $cant_rows; $i++)
                {
                    $detalle_post = new stdClass();
                    $detalle_post->id = $this->input->post('id_detalle_' . $i);
                    $detalle_post->tipo_combustible_id = $this->input->post('tipo_combustible_' . $i);
                    $detalle_post->litros = $this->input->post('litros_' . $i);
                    $detalle_post->costo_unitario = $this->input->post('costo_unitario_' . $i);
                    if (empty($detalle_post->id) || $detalle_post->id === 'nuevo')
                    {
                        $post_detalles_create[] = $detalle_post;
                    }
                    else
                    {
                        $post_detalles_update[$detalle_post->id] = $detalle_post;
                    }
                }
                if (!empty($detalles_actuales))
                {
                    foreach ($detalles_actuales as $Detalle_actual)
                    {
                        if (isset($post_detalles_update[$Detalle_actual->id]))
                        {
                            $trans_ok &= $this->Ordenes_compra_detalles_model->update(array(
                                'id' => $Detalle_actual->id,
                                'tipo_combustible_id' => $post_detalles_update[$Detalle_actual->id]->tipo_combustible_id,
                                'litros' => $post_detalles_update[$Detalle_actual->id]->litros,
                                'costo_unitario' => $post_detalles_update[$Detalle_actual->id]->costo_unitario), FALSE);
                        }
                        else
                        {
                            $trans_ok &= $this->Ordenes_compra_detalles_model->delete(array(
                                'id' => $Detalle_actual->id), FALSE);
                        }
                    }
                }
                foreach ($post_detalles_create as $Detalle_actual)
                {
                    $trans_ok &= $this->Ordenes_compra_detalles_model->create(array(
                        'orden_compra_id' => $id,
                        'tipo_combustible_id' => $Detalle_actual->tipo_combustible_id,
                        'litros' => $Detalle_actual->litros,
                        'costo_unitario' => $Detalle_actual->costo_unitario), FALSE);
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Ordenes_compra_model->get_msg());
                    redirect('vales_combustible/ordenes_compra/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Ordenes_compra_model->get_error())
                    {
                        $error_msg .= $this->Ordenes_compra_model->get_error();
                    }
                    if ($this->Ordenes_compra_detalles_model->get_error())
                    {
                        $error_msg .= $this->Ordenes_compra_detalles_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Ordenes_compra_model->fields, $orden_compra);

        if (empty($_POST))
        {
            $detalles = $this->Ordenes_compra_detalles_model->get(array('orden_compra_id' => $id));
        }
        else
        {
            $detalles = array();
        }
        $rows = $this->form_validation->set_value('cant_rows', sizeof($detalles));
        $data['fields_detalle_array'] = array();


        $data['fields_detalle_array'] = array();

        for ($i = 1; $i <= $rows; $i++)
        {
            $fake_model_fields = array(
                "tipo_combustible_$i" => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                "litros_$i" => array('label' => 'M³/Litros', 'type' => 'numeric', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_unitario_$i" => array('label' => 'Costo Unitario', 'type' => 'money', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_total_$i" => array('label' => 'Costo Total', 'type' => 'money', 'required' => TRUE, 'readonly' => TRUE)
            );
            if (empty($_POST))
            {
                $temp_detalle = new stdClass();
                $temp_detalle->{"orden_compra_{$i}_id"} = $detalles[$i - 1]->orden_compra_id;
                $temp_detalle->{"tipo_combustible_{$i}_id"} = $detalles[$i - 1]->tipo_combustible_id;
                $temp_detalle->{"litros_{$i}"} = $detalles[$i - 1]->litros;
                $temp_detalle->{"costo_unitario_{$i}"} = $detalles[$i - 1]->costo_unitario;
                $temp_detalle->{"costo_total_{$i}"} = number_format($detalles[$i - 1]->costo_unitario * $detalles[$i - 1]->litros, 2, ',', '');
            }
            else
            {
                $temp_detalle = NULL;
            }
            $fake_model_fields["tipo_combustible_$i"]['array'] = $array_tipo_combustible;
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, FALSE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $asignados = $this->Ordenes_compra_detalles_model->get(array(
            'select' => array(
                'vc_tipos_combustible.nombre as tipo_combustible',
                'SUM(vc_ordenes_compra_detalles.litros) as litros',
                "(SELECT SUM(R.litros) FROM vc_facturas F LEFT JOIN vc_remitos R ON R.factura_id = F.id WHERE R.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND F.orden_compra_id = $orden_compra->id) as asignado_remitos",
                "(SELECT SUM(V.metros_cubicos) FROM vc_vales V WHERE V.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND V.orden_compra_id = $orden_compra->id) as asignado_vales"
            ),
            'join' => array(
                array(
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_ordenes_compra_detalles.tipo_combustible_id',
                    'type' => 'LEFT')
            ),
            'where' => array(
                array('column' => 'vc_ordenes_compra_detalles.orden_compra_id', 'value' => $orden_compra->id)
            ),
            'group_by' => 'vc_ordenes_compra_detalles.tipo_combustible_id, vc_tipos_combustible.nombre',
            'sort_by' => 'vc_tipos_combustible.nombre'
        ));

        $facturas_asignadas = $this->Facturas_model->get(array(
            'select' => array("vc_facturas.id, vc_facturas.factura, vc_tipos_combustible.nombre as tipo_combustible, SUM(vc_remitos.litros) as litros, SUM(vc_remitos.costo) as costo"),
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_remitos',
                    'where' => 'vc_remitos.factura_id = vc_facturas.id'),
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id')
            ),
            'orden_compra_id' => $id,
            'group_by' => 'vc_facturas.id, vc_facturas.factura, vc_tipos_combustible.nombre'
        ));

        $vales_asignados = $this->Vales_model->get(array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible"))
            ),
            'orden_compra_id' => $id
        ));

        $data['asignados'] = $asignados;
        $data['facturas_asignadas'] = $facturas_asignadas;
        $data['vales_asignados'] = $vales_asignados;

        $data['orden_compra'] = $orden_compra;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar orden de compra';
        $data['title'] = TITLE . ' - Editar orden de compra';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/ordenes_compra/ordenes_compra_abm', $data);
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
            redirect("vales_combustible/ordenes_compra/ver/$id", 'refresh');
        }

        $orden_compra = $this->Ordenes_compra_model->get_one($id);
        if (empty($orden_compra))
        {
            show_error('No se encontró la Orden de Compra', 500, 'Registro no encontrado');
        }

        $detalles = $this->Ordenes_compra_detalles_model->get(array(
            'orden_compra_id' => $orden_compra->id,
            'join' => array(
                array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_ordenes_compra_detalles.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible"))
            )
        ));
        if (empty($detalles))
        {
            show_error('No se encontró el Detalle de la Orden de Compra', 500, 'Registro no encontrado');
        }
        else
        {
            $rows = count($detalles);
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
            $trans_ok &= $this->Ordenes_compra_detalles_model->delete_detalles($this->input->post('id'), FALSE);
            $trans_ok &= $this->Ordenes_compra_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Ordenes_compra_model->get_msg());
                redirect('vales_combustible/ordenes_compra/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Ordenes_compra_model->get_error())
                {
                    $error_msg .= $this->Ordenes_compra_model->get_error();
                }
            }
        }
        $data['fields'] = $this->build_fields($this->Ordenes_compra_model->fields, $orden_compra, TRUE);

        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++)
        {
            $fake_model_fields = array(
                "tipo_combustible_$i" => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                "litros_$i" => array('label' => 'M³/Litros', 'type' => 'numeric', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_unitario_$i" => array('label' => 'Costo Unitario', 'type' => 'money', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_total_$i" => array('label' => 'Costo Total', 'type' => 'money', 'required' => TRUE, 'readonly' => TRUE)
            );
            $temp_detalle = new stdClass();
            $temp_detalle->{"orden_compra_{$i}_id"} = $detalles[$i - 1]->orden_compra_id;
            $temp_detalle->{"tipo_combustible_{$i}"} = $detalles[$i - 1]->tipo_combustible;
            $temp_detalle->{"litros_{$i}"} = $detalles[$i - 1]->litros;
            $temp_detalle->{"costo_unitario_{$i}"} = $detalles[$i - 1]->costo_unitario;
            $temp_detalle->{"costo_total_{$i}"} = number_format($detalles[$i - 1]->costo_unitario * $detalles[$i - 1]->litros, 2, ',', '');
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $asignados = $this->Ordenes_compra_detalles_model->get(array(
            'select' => array(
                'vc_tipos_combustible.nombre as tipo_combustible',
                'SUM(vc_ordenes_compra_detalles.litros) as litros',
                "(SELECT SUM(R.litros) FROM vc_facturas F LEFT JOIN vc_remitos R ON F.id = R.factura_id WHERE R.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND F.orden_compra_id = $orden_compra->id) as asignado_remitos",
                "(SELECT SUM(V.metros_cubicos) FROM vc_vales V WHERE V.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND V.orden_compra_id = $orden_compra->id) as asignado_vales"
            ),
            'join' => array(
                array(
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_ordenes_compra_detalles.tipo_combustible_id',
                    'type' => 'LEFT')
            ),
            'where' => array(
                array('column' => 'vc_ordenes_compra_detalles.orden_compra_id', 'value' => $orden_compra->id)
            ),
            'group_by' => 'vc_ordenes_compra_detalles.tipo_combustible_id, vc_tipos_combustible.nombre',
            'sort_by' => 'vc_tipos_combustible.nombre'
        ));

        $facturas_asignadas = $this->Facturas_model->get(array(
            'select' => array("vc_facturas.id, vc_facturas.factura, vc_tipos_combustible.nombre as tipo_combustible, SUM(vc_remitos.litros) as litros, SUM(vc_remitos.costo) as costo"),
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_remitos',
                    'where' => 'vc_remitos.factura_id = vc_facturas.id'),
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id')
            ),
            'orden_compra_id' => $id,
            'group_by' => 'vc_facturas.id, vc_facturas.factura, vc_tipos_combustible.nombre'
        ));

        $vales_asignados = $this->Vales_model->get(array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible"))
            ),
            'orden_compra_id' => $id
        ));

        $data['asignados'] = $asignados;
        $data['facturas_asignadas'] = $facturas_asignadas;
        $data['vales_asignados'] = $vales_asignados;

        $data['orden_compra'] = $orden_compra;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar orden de compra';
        $data['title'] = TITLE . ' - Eliminar orden de compra';
        $this->load_template('vales_combustible/ordenes_compra/ordenes_compra_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $orden_compra = $this->Ordenes_compra_model->get_one($id);
        if (empty($orden_compra))
        {
            show_error('No se encontró la Orden de Compra', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Ordenes_compra_model->fields, $orden_compra, TRUE);

        $detalles = $this->Ordenes_compra_detalles_model->get(array(
            'orden_compra_id' => $orden_compra->id,
            'join' => array(
                array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_ordenes_compra_detalles.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible"))
            )
        ));
        if (empty($detalles))
        {
            show_error('No se encontró el Detalle de la Orden de Compra', 500, 'Registro no encontrado');
        }
        else
        {
            $rows = count($detalles);
        }

        $data['fields_detalle_array'] = array();
        for ($i = 1; $i <= $rows; $i++)
        {
            $fake_model_fields = array(
                "tipo_combustible_$i" => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
                "litros_$i" => array('label' => 'M³/Litros', 'type' => 'numeric', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_unitario_$i" => array('label' => 'Costo Unitario', 'type' => 'money', 'required' => TRUE, 'class' => 'costo_total_calculo'),
                "costo_total_$i" => array('label' => 'Costo Total', 'type' => 'money', 'required' => TRUE, 'readonly' => TRUE)
            );
            $temp_detalle = new stdClass();
            $temp_detalle->{"orden_compra_{$i}_id"} = $detalles[$i - 1]->orden_compra_id;
            $temp_detalle->{"tipo_combustible_{$i}"} = $detalles[$i - 1]->tipo_combustible;
            $temp_detalle->{"litros_{$i}"} = $detalles[$i - 1]->litros;
            $temp_detalle->{"costo_unitario_{$i}"} = $detalles[$i - 1]->costo_unitario;
            $temp_detalle->{"costo_total_{$i}"} = number_format($detalles[$i - 1]->costo_unitario * $detalles[$i - 1]->litros, 2, ',', '');
            $data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
        }

        $data['cant_rows'] = array(
            'name' => 'cant_rows',
            'id' => 'cant_rows',
            'type' => 'hidden',
            'value' => $rows
        );

        $asignados = $this->Ordenes_compra_detalles_model->get(array(
            'select' => array(
                'vc_tipos_combustible.nombre as tipo_combustible',
                'SUM(vc_ordenes_compra_detalles.litros) as litros',
                "(SELECT SUM(R.litros) FROM vc_facturas F LEFT JOIN vc_remitos R ON F.id = R.factura_id WHERE R.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND F.orden_compra_id = $orden_compra->id) as asignado_remitos",
                "(SELECT SUM(V.metros_cubicos) FROM vc_vales V WHERE V.tipo_combustible_id = vc_ordenes_compra_detalles.tipo_combustible_id AND V.orden_compra_id = $orden_compra->id) as asignado_vales"
            ),
            'join' => array(
                array(
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_ordenes_compra_detalles.tipo_combustible_id',
                    'type' => 'LEFT')
            ),
            'where' => array(
                array('column' => 'vc_ordenes_compra_detalles.orden_compra_id', 'value' => $orden_compra->id)
            ),
            'group_by' => 'vc_ordenes_compra_detalles.tipo_combustible_id, vc_tipos_combustible.nombre',
            'sort_by' => 'vc_tipos_combustible.nombre'
        ));

        $facturas_asignadas = $this->Facturas_model->get(array(
            'select' => array("vc_facturas.id, vc_facturas.factura, vc_tipos_combustible.nombre as tipo_combustible, SUM(vc_remitos.litros) as litros, SUM(vc_remitos.costo) as costo"),
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_remitos',
                    'where' => 'vc_remitos.factura_id = vc_facturas.id'),
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id')
            ),
            'orden_compra_id' => $id,
            'group_by' => 'vc_facturas.id, vc_facturas.factura, vc_tipos_combustible.nombre'
        ));

        $vales_asignados = $this->Vales_model->get(array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible"))
            ),
            'orden_compra_id' => $id
        ));

        $data['error'] = $this->session->flashdata('error');
        $data['asignados'] = $asignados;
        $data['facturas_asignadas'] = $facturas_asignadas;
        $data['vales_asignados'] = $vales_asignados;
        $data['orden_compra'] = $orden_compra;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver orden de compra';
        $data['title'] = TITLE . ' - Ver orden de compra';
        $this->load_template('vales_combustible/ordenes_compra/ordenes_compra_abm', $data);
    }

    public function get_ordenes_tipo()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('tipo', 'Tipo', 'required|integer');
        if ($this->form_validation->run() === TRUE)
        {
            $ordenes = $this->Ordenes_compra_model->get(array(
                'select' => array('vc_ordenes_compra.id', 'CONCAT(numero, \'/\', ejercicio) as orden'),
                'join' => array(
                    array(
                        'type' => 'LEFT',
                        'table' => 'vc_ordenes_compra_detalles',
                        'where' => 'vc_ordenes_compra_detalles.orden_compra_id = vc_ordenes_compra.id'
                    )
                ),
                'where' => array(array('column' => 'vc_ordenes_compra_detalles.tipo_combustible_id', 'value' => $this->input->post('tipo'))),
                'sort_by' => 'ejercicio, numero'
            ));
            if (!empty($ordenes))
            {
                echo json_encode($ordenes);
            }
            else
            {
                echo json_encode("error");
            }
        }
    }
}
