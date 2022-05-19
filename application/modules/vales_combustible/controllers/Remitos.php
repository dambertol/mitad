<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Remitos extends MY_Controller
{

    /**
     * Controlador de Remitos
     * Autor: Leandro
     * Creado: 10/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Vales_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('vales_combustible/Remitos_model');
        $this->load->model('vales_combustible/Facturas_model');
        $this->load->model('Personal_model');
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
                array('label' => 'Número', 'data' => 'remito', 'class' => 'dt-body-right', 'width' => 10),
                array('label' => 'Fecha', 'data' => 'fecha_remito', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 8),
                array('label' => 'M³/Litros', 'data' => 'litros', 'render' => 'numeric', 'class' => 'dt-body-right', 'width' => 5),
                array('label' => 'Tipo Comb.', 'data' => 'tipo_combustible', 'width' => 8),
                array('label' => 'Costo', 'data' => 'costo', 'render' => 'money', 'class' => 'dt-body-right', 'width' => 5),
                array('label' => 'Patente', 'data' => 'patente_maquinaria', 'width' => 6),
                array('label' => 'Legajo', 'data' => 'persona_id', 'class' => 'dt-body-right', 'width' => 7),
                array('label' => 'Persona', 'data' => 'persona', 'width' => 19),
                array('label' => 'Persona Externa', 'data' => 'persona_nombre', 'width' => 16),
                array('label' => 'Factura', 'data' => 'factura', 'class' => 'dt-body-right', 'width' => 10),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'remitos_table',
            'source_url' => 'vales_combustible/remitos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_remitos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de remitos';
        $data['title'] = TITLE . ' - Remitos';
        $this->load_template('vales_combustible/remitos/remitos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("vc_remitos.id, remito, vc_remitos.fecha as fecha_remito, litros, vc_tipos_combustible.nombre as tipo_combustible, costo, patente_maquinaria, persona_id, CONCAT(personal.Apellido, ', ', personal.Nombre) as persona, persona_nombre, vc_facturas.factura as factura")
                ->from('vc_remitos')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id', 'left')
                ->join('vc_facturas', 'vc_facturas.id = vc_remitos.factura_id', 'left')
                ->join('personal', 'personal.Legajo = vc_remitos.persona_id', 'left')
                ->add_column('ver', '<a href="vales_combustible/remitos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="vales_combustible/remitos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="vales_combustible/remitos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar($factura_id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/remitos/listar", 'refresh');
        }

        if (!empty($factura_id))
        {
            $factura = $this->Facturas_model->get(array('id' => $factura_id));
        }

        $vales = $this->Vales_model->get(array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                array(
                    'type' => 'left',
                    'table' => 'areas',
                    'where' => 'areas.id = vc_vales.area_id',
                    'columnas' => array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
            ),
            'where' => array(
                array(
                    'column' => 'estado',
                    'value' => 'Impreso'
                )
            )
        ));
        $array_vales = array();
        $array_vales_litros = array();
        $array_vales_vencimientos = array();
        if (!empty($vales))
        {
            foreach ($vales as $Vale)
            {
                $array_vales[$Vale->id] = "VC" . str_pad($Vale->id, 6, '0', STR_PAD_LEFT) . " - $Vale->tipo_combustible ($Vale->metros_cubicos lts) - $Vale->area";
                $array_vales_litros[$Vale->id] = $Vale->metros_cubicos;
                $array_vales_vencimientos[$Vale->id] = date_format(new DateTime($Vale->vencimiento), 'Y-m-d 00:00:00');
            }
        }

        if (!empty($this->input->post('tipo_combustible')))
        {
            $this->array_factura_control = $array_factura = $this->get_array('Facturas', 'factura', 'id', array('tipo_combustible_id' => $this->input->post('tipo_combustible')), array('NULL' => '-- Sin Factura --'));
        }
        else if (!empty($factura->tipo_combustible_id))
        {
            $this->array_factura_control = $array_factura = $this->get_array('Facturas', 'factura', 'id', array('tipo_combustible_id' => $factura->tipo_combustible_id), array('NULL' => '-- Sin Factura --'));
        }
        else
        {
            $this->array_factura_control = $array_factura = array('NULL' => '-- Sin Factura --');
        }
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->array_vale_control = array('' => '') + $array_vales;

        $fake_model = $this->Remitos_model;
        $fake_model->fields['vales'] = array('label' => 'Vales Asignados', 'input_type' => 'combo', 'type' => 'list', 'id_name' => 'vales');

        $this->set_model_validation_rules($fake_model);
        $this->form_validation->set_rules('vales[]', 'Vales', "callback_control_combo[vale]");
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Remitos_model->create(array(
                'factura_id' => $this->input->post('factura'),
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'litros' => $this->input->post('litros'),
                'costo' => $this->input->post('costo'),
                'remito' => $this->input->post('remito'),
                'fecha' => $fecha->format('Y-m-d'),
                'patente_maquinaria' => $this->input->post('patente_maquinaria'),
                'persona_id' => $this->input->post('persona'),
                'persona_nombre' => $this->input->post('persona_nombre'),
                'observaciones' => $this->input->post('observaciones'),
                'user_id' => $this->session->userdata('user_id')), FALSE);

            $remito_id = $this->Remitos_model->get_row_id();
            $vales_post = $this->input->post('vales');
            if (empty($vales_post))
            {
                $vales_post = array();
            }
            $trans_ok &= $this->Vales_model->intersect_vales($remito_id, $vales_post, FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Remitos_model->get_msg());
                if (!empty($factura))
                {
                    redirect("vales_combustible/facturas/ver/$factura->id", 'refresh');
                }
                else
                {
                    redirect("vales_combustible/remitos/listar", 'refresh');
                }
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Remitos_model->get_error())
                {
                    $error_msg .= $this->Remitos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        if (!empty($factura))
        {
            $remito = new stdClass();
            $remito->remito = NULL;
            $remito->fecha = NULL;
            $remito->tipo_combustible_id = $factura->tipo_combustible_id;
            $remito->litros = NULL;
            $remito->costo = NULL;
            $remito->patente_maquinaria = NULL;
            $remito->persona = NULL;
            $remito->persona_major = NULL;
            $remito->persona_nombre = NULL;
            $remito->factura_id = $factura->id;
            $remito->observaciones = NULL;
            $remito->vales = NULL;
        }

        $data['vales_litros'] = json_encode($array_vales_litros);
        $data['vales_vencimientos'] = json_encode($array_vales_vencimientos);
        $fake_model->fields['vales']['array'] = $array_vales;
        $fake_model->fields['factura']['array'] = $array_factura;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        if (!empty($factura))
        {
            $data['fields'] = $this->build_fields($fake_model->fields, $remito);
        }
        else
        {
            $data['fields'] = $this->build_fields($fake_model->fields);
        }
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar remito';
        $data['title'] = TITLE . ' - Agregar remito';
        $data['css'][] = 'vendor/duallistbox/css/bootstrap-duallistbox.min.css';
        $data['js'][] = 'vendor/duallistbox/js/jquery.bootstrap-duallistbox.min.js';
        $data['js'][] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/remitos/remitos_abm', $data);
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
            redirect("vales_combustible/remitos/ver/$id", 'refresh');
        }

        $remito = $this->Remitos_model->get(array('id' => $id));
        if (empty($remito))
        {
            show_error('No se encontró el Remito', 500, 'Registro no encontrado');
        }

        $vales = $this->Vales_model->get(array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                array(
                    'type' => 'left',
                    'table' => 'areas',
                    'where' => 'areas.id = vc_vales.area_id',
                    'columnas' => array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
            ),
            'where' => array(
                array(
                    'column' => "(estado = 'Impreso' OR remito_id = $id)",
                    'value' => '',
                    'override' => TRUE
                )
            )
        ));
        $array_vales = array();
        $array_vales_litros = array();
        $array_vales_vencimientos = array();
        if (!empty($vales))
        {
            foreach ($vales as $Vale)
            {
                $array_vales[$Vale->id] = "VC" . str_pad($Vale->id, 6, '0', STR_PAD_LEFT) . " - $Vale->tipo_combustible ($Vale->metros_cubicos lts) - $Vale->area";
                $array_vales_litros[$Vale->id] = $Vale->metros_cubicos;
                $array_vales_vencimientos[$Vale->id] = date_format(new DateTime($Vale->vencimiento), 'Y-m-d 00:00:00');
            }
        }

        $tipo_combustible_sel = !empty($this->input->post('tipo_combustible')) ? $this->input->post('tipo_combustible') : $remito->tipo_combustible_id;
        if (!empty($tipo_combustible_sel))
        {
            $this->array_factura_control = $array_factura = $this->get_array('Facturas', 'factura', 'id', array('tipo_combustible_id' => $tipo_combustible_sel), array('NULL' => '-- Sin Factura --'));
        }
        else
        {
            $this->array_factura_control = $array_factura = array('NULL' => '-- Sin Factura --');
        }
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->array_vale_control = array('' => '') + $array_vales;

        $fake_model = $this->Remitos_model;
        $fake_model->fields['vales'] = array('label' => 'Vales Asignados', 'input_type' => 'combo', 'type' => 'list', 'id_name' => 'vales');

        $this->set_model_validation_rules($fake_model);
        $this->form_validation->set_rules('vales[]', 'Vales', "callback_control_combo[vale]");
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
                $trans_ok &= $this->Remitos_model->update(array(
                    'id' => $this->input->post('id'),
                    'factura_id' => $this->input->post('factura'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'litros' => $this->input->post('litros'),
                    'costo' => $this->input->post('costo'),
                    'remito' => $this->input->post('remito'),
                    'fecha' => $fecha->format('Y-m-d'),
                    'patente_maquinaria' => $this->input->post('patente_maquinaria'),
                    'persona_id' => $this->input->post('persona'),
                    'persona_nombre' => $this->input->post('persona_nombre'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $vales_post = $this->input->post('vales');
                if (empty($vales_post))
                {
                    $vales_post = array();
                }
                $trans_ok &= $this->Vales_model->intersect_vales($this->input->post('id'), $vales_post, FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Remitos_model->get_msg());
                    redirect('vales_combustible/remitos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Remitos_model->get_error())
                    {
                        $error_msg .= $this->Remitos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $vales_asignados = $this->Vales_model->get(array('remito_id' => $id));
        $remito->vales = array();
        if (!empty($vales_asignados))
        {
            foreach ($vales_asignados as $Vale_asignado)
            {
                $remito->vales[] = $Vale_asignado->id;
            }
        }

        $data['vales_litros'] = json_encode($array_vales_litros);
        $data['vales_vencimientos'] = json_encode($array_vales_vencimientos);

        $fake_model->fields['factura']['array'] = $array_factura;
        $fake_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $fake_model->fields['vales']['array'] = $array_vales;
        $remito->persona = $remito->persona_id;
        $remito->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $remito);
        $data['remito'] = $remito;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar remito';
        $data['title'] = TITLE . ' - Editar remito';
        $data['css'][] = 'vendor/duallistbox/css/bootstrap-duallistbox.min.css';
        $data['js'][] = 'vendor/duallistbox/js/jquery.bootstrap-duallistbox.min.js';
        $data['js'][] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/remitos/remitos_abm', $data);
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
            redirect("vales_combustible/remitos/ver/$id", 'refresh');
        }

        $remito = $this->Remitos_model->get_one($id);
        if (empty($remito))
        {
            show_error('No se encontró el Remito', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Remitos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Remitos_model->get_msg());
                redirect('vales_combustible/remitos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Remitos_model->get_error())
                {
                    $error_msg .= $this->Remitos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $vales_asignados = $this->Vales_model->get(array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                array(
                    'type' => 'left',
                    'table' => 'areas',
                    'where' => 'areas.id = vc_vales.area_id',
                    'columnas' => array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
            ),
            'remito_id' => $id
        ));

        $data['vales_asignados'] = $vales_asignados;

        $remito->persona = $remito->persona_id;
        $remito->persona_major = NULL;
        $data['fields'] = $this->build_fields($this->Remitos_model->fields, $remito, TRUE);
        $data['remito'] = $remito;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar remito';
        $data['title'] = TITLE . ' - Eliminar remito';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/remitos/remitos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $remito = $this->Remitos_model->get_one($id);
        if (empty($remito))
        {
            show_error('No se encontró el Remito', 500, 'Registro no encontrado');
        }

        $vales_asignados = $this->Vales_model->get(array(
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'vc_tipos_combustible',
                    'where' => 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id',
                    'columnas' => array("vc_tipos_combustible.nombre as tipo_combustible")),
                array(
                    'type' => 'left',
                    'table' => 'areas',
                    'where' => 'areas.id = vc_vales.area_id',
                    'columnas' => array("CONCAT(areas.codigo, ' - ', areas.nombre) as area"))
            ),
            'remito_id' => $id
        ));

        $data['error'] = $this->session->flashdata('error');
        $data['vales_asignados'] = $vales_asignados;
        $temp_fields = $this->Remitos_model->fields;
        $temp_fields['usuario'] = array('label' => 'Usuario Creación');
        $remito->persona = $remito->persona_id;
        $remito->persona_major = NULL;
        $data['fields'] = $this->build_fields($temp_fields, $remito, TRUE);
        $data['remito'] = $remito;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver remito';
        $data['title'] = TITLE . ' - Ver remito';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/remitos/remitos_abm', $data);
    }

    public function get_datos_remito()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('remito', 'Remito', 'required|integer');
        if ($this->form_validation->run() === TRUE)
        {
            $remito = $this->Remitos_model->get(array(
                'id' => $this->input->post('remito'),
                'join' => array(
                    array(
                        'type' => 'left',
                        'table' => 'personal',
                        'where' => 'personal.Legajo = vc_remitos.persona_id',
                        'columnas' => array("CONCAT(personal.Apellido, ', ', personal.Nombre) as persona"))
                )
            ));
            if (empty($remito->persona))
            {
                $remito->persona = $remito->persona_nombre;
            }

            if (!empty($remito))
            {
                echo json_encode($remito);
            }
            else
            {
                echo json_encode("error");
            }
        }
    }
}
