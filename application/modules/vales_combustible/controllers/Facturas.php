<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Facturas extends MY_Controller
{

    /**
     * Controlador de Facturas
     * Autor: Leandro
     * Creado: 08/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Facturas_model');
        $this->load->model('vales_combustible/Remitos_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('vales_combustible/Ordenes_compra_model');
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
                array('label' => 'Número', 'data' => 'factura', 'class' => 'dt-body-right', 'width' => 16),
                array('label' => 'Fecha', 'data' => 'fecha', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 14),
                array('label' => 'Tipo Combustible', 'data' => 'tipo_combustible', 'width' => 18),
                array('label' => 'Total M³/Litros', 'data' => 'total_litros', 'render' => 'numeric', 'class' => 'dt-body-right', 'width' => 16),
                array('label' => 'Total Costo', 'data' => 'total_costo', 'render' => 'money', 'class' => 'dt-body-right', 'width' => 16),
                array('label' => 'Orden Compra', 'data' => 'orden_compra', 'class' => 'dt-body-right', 'width' => 14),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'facturas_table',
            'source_url' => 'vales_combustible/facturas/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_facturas_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de facturas';
        $data['title'] = TITLE . ' - Facturas';
        $this->load_template('vales_combustible/facturas/facturas_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("vc_facturas.id, vc_facturas.factura, vc_facturas.fecha, vc_tipos_combustible.nombre as tipo_combustible, vc_facturas.total_litros, vc_facturas.total_costo, CONCAT(vc_ordenes_compra.numero, '/', vc_ordenes_compra.ejercicio) as orden_compra")
                ->from('vc_facturas')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_facturas.tipo_combustible_id', 'left')
                ->join('vc_ordenes_compra', 'vc_ordenes_compra.id = vc_facturas.orden_compra_id', 'left')
                ->add_column('ver', '<a href="vales_combustible/facturas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/facturas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/facturas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect("vales_combustible/facturas/listar", 'refresh');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        if (!empty($this->input->post('tipo_combustible')))
        {
            $this->array_orden_compra_control = $array_orden_compra = $this->get_array('Ordenes_compra', 'orden', 'id', array(
                'select' => array('vc_ordenes_compra.id', 'CONCAT(numero, \'/\', ejercicio) as orden'),
                'join' => array(
                    array(
                        'type' => 'LEFT',
                        'table' => 'vc_ordenes_compra_detalles',
                        'where' => 'vc_ordenes_compra_detalles.orden_compra_id = vc_ordenes_compra.id'
                    )
                ),
                'where' => array(array('column' => 'vc_ordenes_compra_detalles.tipo_combustible_id', 'value' => $this->input->post('tipo_combustible'))),
                'sort_by' => 'ejercicio, numero'
                    ), array('NULL' => '-- Sin Orden de Compra --')
            );
        }
        else
        {
            $this->array_orden_compra_control = $array_orden_compra = array('NULL' => '-- Sin Orden de Compra --');
        }

        $this->set_model_validation_rules($this->Facturas_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Facturas_model->create(array(
                'factura' => $this->input->post('factura'),
                'fecha' => $fecha->format('Y-m-d'),
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'total_litros' => $this->input->post('total_litros'),
                'total_costo' => $this->input->post('total_costo'),
                'orden_compra_id' => $this->input->post('orden_compra'),
                'user_id' => $this->session->userdata('user_id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Facturas_model->get_msg());
                redirect('vales_combustible/facturas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Facturas_model->get_error())
                {
                    $error_msg .= $this->Facturas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Facturas_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $this->Facturas_model->fields['orden_compra']['array'] = $array_orden_compra;
        $data['fields'] = $this->build_fields($this->Facturas_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar factura';
        $data['title'] = TITLE . ' - Agregar factura';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/facturas/facturas_abm', $data);
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
            redirect("vales_combustible/facturas/ver/$id", 'refresh');
        }

        $factura = $this->Facturas_model->get(array('id' => $id));
        if (empty($factura))
        {
            show_error('No se encontró la Factura', 500, 'Registro no encontrado');
        }

        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $tipo_combustible_sel = !empty($this->input->post('tipo_combustible')) ? $this->input->post('tipo_combustible') : $factura->tipo_combustible_id;
        if (!empty($tipo_combustible_sel))
        {
            $this->array_orden_compra_control = $array_orden_compra = $this->get_array('Ordenes_compra', 'orden', 'id', array(
                'select' => array('vc_ordenes_compra.id', 'CONCAT(numero, \'/\', ejercicio) as orden'),
                'join' => array(
                    array(
                        'type' => 'LEFT',
                        'table' => 'vc_ordenes_compra_detalles',
                        'where' => 'vc_ordenes_compra_detalles.orden_compra_id = vc_ordenes_compra.id'
                    )
                ),
                'where' => array(array('column' => 'vc_ordenes_compra_detalles.tipo_combustible_id', 'value' => $tipo_combustible_sel)),
                'sort_by' => 'ejercicio, numero'
                    ), array('NULL' => '-- Sin Orden de Compra --')
            );
        }
        else
        {
            $this->array_orden_compra_control = $array_orden_compra = array('NULL' => '-- Sin Orden de Compra --');
        }

        $this->set_model_validation_rules($this->Facturas_model);
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
                $trans_ok &= $this->Facturas_model->update(array(
                    'id' => $this->input->post('id'),
                    'factura' => $this->input->post('factura'),
                    'fecha' => $fecha->format('Y-m-d'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'total_litros' => $this->input->post('total_litros'),
                    'total_costo' => $this->input->post('total_costo'),
                    'orden_compra_id' => $this->input->post('orden_compra')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Facturas_model->get_msg());
                    redirect('vales_combustible/facturas/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Facturas_model->get_error())
                    {
                        $error_msg .= $this->Facturas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $remitos_asignados = $this->Remitos_model->get(array(
            'factura_id' => $id,
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible"))
            ),
            'sort_by' => 'remito'
        ));
        $data['remitos_asignados'] = $remitos_asignados;

        $this->Facturas_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $this->Facturas_model->fields['orden_compra']['array'] = $array_orden_compra;
        $data['fields'] = $this->build_fields($this->Facturas_model->fields, $factura);
        $data['factura'] = $factura;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar factura';
        $data['title'] = TITLE . ' - Editar factura';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/facturas/facturas_abm', $data);
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
            redirect("vales_combustible/facturas/ver/$id", 'refresh');
        }

        $factura = $this->Facturas_model->get_one($id);
        if (empty($factura))
        {
            show_error('No se encontró la Factura', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Facturas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Facturas_model->get_msg());
                redirect('vales_combustible/facturas/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Facturas_model->get_error())
                {
                    $error_msg .= $this->Facturas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $remitos_asignados = $this->Remitos_model->get(array(
            'factura_id' => $id,
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible"))
            ),
            'sort_by' => 'remito'
        ));
        $data['remitos_asignados'] = $remitos_asignados;

        $data['fields'] = $this->build_fields($this->Facturas_model->fields, $factura, TRUE);
        $data['factura'] = $factura;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar factura';
        $data['title'] = TITLE . ' - Eliminar factura';
        $this->load_template('vales_combustible/facturas/facturas_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $factura = $this->Facturas_model->get_one($id);
        if (empty($factura))
        {
            show_error('No se encontró la Factura', 500, 'Registro no encontrado');
        }

        $remitos_asignados = $this->Remitos_model->get(array(
            'factura_id' => $id,
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible"))
            ),
            'sort_by' => 'remito'
        ));
        $data['error'] = $this->session->flashdata('error');
        $data['remitos_asignados'] = $remitos_asignados;
        $temp_fields = $this->Facturas_model->fields;
        $temp_fields['usuario'] = array('label' => 'Usuario Creación');
        $data['fields'] = $this->build_fields($temp_fields, $factura, TRUE);
        $data['factura'] = $factura;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver factura';
        $data['title'] = TITLE . ' - Ver factura';
        $this->load_template('vales_combustible/facturas/facturas_abm', $data);
    }

    public function get_facturas_tipo()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('tipo', 'Tipo', 'required|integer');
        if ($this->form_validation->run() === TRUE)
        {
            $facturas = $this->Facturas_model->get(array('tipo_combustible_id' => $this->input->post('tipo')));
            if (!empty($facturas))
            {
                echo json_encode($facturas);
            }
            else
            {
                echo json_encode("error");
            }
        }
    }
}
