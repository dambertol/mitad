<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Autorizaciones extends MY_Controller
{

    /**
     * Controlador de Autorizaciones
     * Autor: Leandro
     * Creado: 17/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('vales_combustible/Autorizaciones_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('vales_combustible/Vehiculos_model');
        $this->load->model('vales_combustible/Autorizaciones_model');
        $this->load->model('Personal_model');
        $this->grupos_permitidos = array('admin', 'vales_combustible_autorizaciones', 'vales_combustible_contaduria', 'vales_combustible_obrador', 'vales_combustible_consulta_general');
        $this->grupos_estacion = array('admin', 'vales_combustible_autorizaciones', 'vales_combustible_estacion', 'vales_combustible_consulta_general');
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
                array('label' => 'ID', 'data' => 'id_aut', 'width' => 4, 'class' => 'dt-body-right'),
                array('label' => 'Vehículo', 'data' => 'vehiculo', 'width' => 14),
                array('label' => 'Tipo Combustible', 'data' => 'tipo_combustible', 'width' => 10),
                array('label' => 'Litros Autor.', 'data' => 'litros_autorizados', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Fecha Autor.', 'data' => 'fecha_autorizacion', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Legajo', 'data' => 'persona_id', 'width' => 9, 'class' => 'dt-body-right'),
                array('label' => 'Chofer', 'data' => 'persona', 'width' => 17),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => 'Fecha Carga', 'data' => 'fecha_carga', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Litros Carg.', 'data' => 'litros_cargados', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'autorizaciones_table',
            'source_url' => 'vales_combustible/autorizaciones/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_autorizaciones_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        $data['array_estados'] = array('' => 'Todas', 'Anulada' => 'Anulada', 'Autorizada' => 'Autorizada', 'Cargada' => 'Cargada');
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Autorizaciones';
        $data['title'] = TITLE . ' - Autorizaciones';
        $this->load_template('vales_combustible/autorizaciones/autorizaciones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('vales_combustible/datatables_functions_helper');
        $this->datatables
                ->select("vc_autorizaciones.id, vc_autorizaciones.id as id_aut, CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(vc_vehiculos.dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo, vc_tipos_combustible.nombre as tipo_combustible, vc_autorizaciones.lleno, vc_autorizaciones.litros_autorizados, vc_autorizaciones.fecha_autorizacion, vc_autorizaciones.persona_id, CONCAT(personal.Apellido, ', ', personal.Nombre) as persona, vc_autorizaciones.estado, vc_autorizaciones.fecha_carga, vc_autorizaciones.litros_cargados")
                ->from('vc_autorizaciones')
                ->join('vc_vehiculos', 'vc_vehiculos.id = vc_autorizaciones.vehiculo_id', 'left')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_autorizaciones.tipo_combustible_id', 'left')
                ->join('personal', 'personal.Legajo = vc_autorizaciones.persona_id', 'left')
                ->edit_column('litros_autorizados', '$1', 'dt_column_autorizaciones_litros(lleno, litros_autorizados)')
                ->edit_column('estado', '$1', 'dt_column_autorizaciones_estado(estado)', TRUE)
                ->add_column('ver', '<a href="vales_combustible/autorizaciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_autorizaciones_editar(estado, id)')
                ->add_column('anular', '$1', 'dt_column_autorizaciones_anular(estado, id)');

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
            redirect("vales_combustible/autorizaciones/listar", 'refresh');
        }

        $this->array_lleno_control = $array_lleno = array('NO' => 'NO', 'SI' => 'SI');
        $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array(
            'select' => "vc_vehiculos.id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO'), ' - ', vc_vehiculos.propiedad, ' (Últ. Carga: ', COALESCE(CONCAT(DATE_FORMAT(vc_autorizaciones.fecha_carga, '%d/%m/%Y %H:%i'), ' - ', vc_autorizaciones.litros_cargados, ' litros'), 'SIN DATOS'), ')') as vehiculo",
            'join' => array(
                array('vc_autorizaciones', 'vc_autorizaciones.vehiculo_id = vc_vehiculos.id AND vc_autorizaciones.estado = "Cargada"', 'LEFT'),
                array('vc_autorizaciones A', 'A.vehiculo_id = vc_vehiculos.id AND A.estado = "Cargada" AND vc_autorizaciones.fecha_carga < A.fecha_carga', 'LEFT OUTER'),
            ),
            'where' => array(
                'A.id IS NULL',
                array('column' => 'vc_vehiculos.estado', 'value' => 'Aprobado')
            )
        ));
        if ($this->input->post('vehiculo'))
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre', 'id', array(
                'join' => array(array('vc_vehiculos_combustible', 'vc_vehiculos_combustible.tipo_combustible_id = vc_tipos_combustible.id', 'LEFT')),
                'where' => array(
                    array('column' => 'vc_vehiculos_combustible.vehiculo_id', 'value' => $this->input->post('vehiculo')))
                    )
            );
        }
        else
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = array();
        }
        $this->set_model_validation_rules($this->Autorizaciones_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $fecha_autorizacion = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_autorizacion'));
            $tanque_lleno = $this->input->post('lleno');
            if ($tanque_lleno === 'SI')
            {
                $litros_autorizados = 9999;
            }
            else
            {
                $litros_autorizados = $this->input->post('litros_autorizados');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Autorizaciones_model->create(array(
                'vehiculo_id' => $this->input->post('vehiculo'),
                'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                'lleno' => $tanque_lleno,
                'litros_autorizados' => $litros_autorizados,
                'fecha_autorizacion' => $fecha_autorizacion->format('Y-m-d'),
                'persona_id' => $this->input->post('persona'),
                'persona_nombre' => $this->input->post('persona_nombre'),
                'observaciones' => $this->input->post('observaciones'),
                'estado' => 'Autorizada',
                'autoriza_id' => $this->session->userdata('user_id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Autorizaciones_model->get_msg());
                redirect('vales_combustible/autorizaciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Autorizaciones_model->get_error())
                {
                    $error_msg .= $this->Autorizaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Autorizaciones_model->fields['lleno']['array'] = $array_lleno;
        $this->Autorizaciones_model->fields['vehiculo']['array'] = $array_vehiculo;
        $this->Autorizaciones_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($this->Autorizaciones_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Autorización';
        $data['title'] = TITLE . ' - Agregar Autorización';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/autorizaciones/autorizaciones_abm', $data);
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
            redirect("vales_combustible/autorizaciones/ver/$id", 'refresh');
        }

        $autorizacion = $this->Autorizaciones_model->get(array(
            'id' => $id,
            'join' => array(
                array('vc_vehiculos', 'vc_vehiculos.id = vc_autorizaciones.vehiculo_id', 'LEFT'),
                array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_autorizaciones.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible")),
            ))
        );
        if (empty($autorizacion) || $autorizacion->estado !== 'Autorizada')
        {
            show_error('No se encontró la Autorización', 500, 'Registro no encontrado');
        }

        $this->array_lleno_control = $array_lleno = array('NO' => 'NO', 'SI' => 'SI');
        $this->array_vehiculo_control = $array_vehiculo = $this->get_array('Vehiculos', 'vehiculo', 'id', array('select' => "id, CONCAT(nombre, ' - ', COALESCE(dominio, 'SIN DOMINIO') , ' - ', vc_vehiculos.propiedad) as vehiculo", 'where' => array("(estado = 'Aprobado' OR id = $autorizacion->vehiculo_id)")));
        if ($this->input->post('vehiculo'))
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre', 'id', array(
                'join' => array(array('vc_vehiculos_combustible', 'vc_vehiculos_combustible.tipo_combustible_id = vc_tipos_combustible.id', 'LEFT')),
                'where' => array(
                    array('column' => 'vc_vehiculos_combustible.vehiculo_id', 'value' => $this->input->post('vehiculo')))
                    )
            );
        }
        else
        {
            $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre', 'id', array(
                'join' => array(array('vc_vehiculos_combustible', 'vc_vehiculos_combustible.tipo_combustible_id = vc_tipos_combustible.id', 'LEFT')),
                'where' => array(
                    array('column' => 'vc_vehiculos_combustible.vehiculo_id', 'value' => $autorizacion->vehiculo_id))
                    )
            );
        }
        $this->set_model_validation_rules($this->Autorizaciones_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $fecha_autorizacion = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_autorizacion'));
                $tanque_lleno = $this->input->post('lleno');
                if ($tanque_lleno === 'SI')
                {
                    $litros_autorizados = 9999;
                }
                else
                {
                    $litros_autorizados = $this->input->post('litros_autorizados');
                }

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Autorizaciones_model->update(array(
                    'id' => $this->input->post('id'),
                    'vehiculo_id' => $this->input->post('vehiculo'),
                    'tipo_combustible_id' => $this->input->post('tipo_combustible'),
                    'lleno' => $tanque_lleno,
                    'litros_autorizados' => $litros_autorizados,
                    'fecha_autorizacion' => $fecha_autorizacion->format('Y-m-d'),
                    'persona_id' => $this->input->post('persona'),
                    'persona_nombre' => $this->input->post('persona_nombre'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Autorizaciones_model->get_msg());
                    redirect('vales_combustible/autorizaciones/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Autorizaciones_model->get_error())
                    {
                        $error_msg .= $this->Autorizaciones_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Autorizaciones_model->fields['lleno']['array'] = $array_lleno;
        $this->Autorizaciones_model->fields['vehiculo']['array'] = $array_vehiculo;
        $this->Autorizaciones_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $autorizacion->persona = $autorizacion->persona_id;
        $autorizacion->persona_major = NULL;
        $data['fields'] = $this->build_fields($this->Autorizaciones_model->fields, $autorizacion);
        $data['autorizacion'] = $autorizacion;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Autorización';
        $data['title'] = TITLE . ' - Editar Autorización';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/autorizaciones/autorizaciones_abm', $data);
    }

    public function anular($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/autorizaciones/ver/$id", 'refresh');
        }

        $autorizacion = $this->Autorizaciones_model->get_one($id);
        if (empty($autorizacion) || $autorizacion->estado === 'Anulada' || $autorizacion->estado === 'Cargada')
        {
            show_error('No se encontró la Autorización', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'vehiculo' => array('label' => 'Vehiculo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'lleno' => array('label' => 'Tanque Lleno', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled', 'id_name' => 'lleno'),
            'litros_autorizados' => array('label' => 'Litros Autorizados', 'type' => 'numeric', 'maxlength' => '50', 'disabled' => 'disabled'),
            'fecha_autorizacion' => array('label' => 'Fecha Autorización', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'persona' => array('label' => 'Legajo Chofer', 'type' => 'integer', 'disabled' => 'disabled'),
            'persona_major' => array('label' => 'Chofer', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Chofer Externo', 'maxlength' => '50', 'disabled' => 'disabled'),
            'observaciones' => array('label' => 'Observaciones', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled'),
            'usuario' => array('label' => 'Usuario Autorización', 'disabled' => 'disabled')
        );

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Autorizaciones_model->update(array('id' => $this->input->post('id'), 'estado' => 'Anulada'), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Autorizaciones_model->get_msg());
                redirect('vales_combustible/autorizaciones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Autorizaciones_model->get_error())
                {
                    $error_msg .= $this->Autorizaciones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $autorizacion->persona = $autorizacion->persona_id;
        $autorizacion->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $autorizacion, TRUE);
        $data['autorizacion'] = $autorizacion;
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular Autorización';
        $data['title'] = TITLE . ' - Anular Autorización';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/autorizaciones/autorizaciones_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $autorizacion = $this->Autorizaciones_model->get_one($id);
        if (empty($autorizacion))
        {
            show_error('No se encontró el Autorización', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'vehiculo' => array('label' => 'Vehiculo', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled'),
            'lleno' => array('label' => 'Tanque Lleno', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => 'disabled', 'id_name' => 'lleno'),
            'litros_autorizados' => array('label' => 'Litros Autorizados', 'type' => 'numeric', 'maxlength' => '50', 'disabled' => 'disabled'),
            'fecha_autorizacion' => array('label' => 'Fecha Autorización', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'persona' => array('label' => 'Legajo Chofer', 'type' => 'integer', 'disabled' => 'disabled'),
            'persona_major' => array('label' => 'Chofer', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Chofer Externo', 'maxlength' => '50', 'disabled' => 'disabled'),
            'observaciones' => array('label' => 'Observaciones', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled'),
            'usuario' => array('label' => 'Usuario Autorización', 'disabled' => 'disabled'),
            'litros_cargados' => array('label' => 'Litros Cargados', 'type' => 'numeric', 'disabled' => 'disabled'),
            'fecha_carga' => array('label' => 'Fecha Carga', 'type' => 'datetime', 'disabled' => 'disabled'),
            'usuario_carga' => array('label' => 'Usuario Carga', 'disabled' => 'disabled')
        );

        $data['error'] = $this->session->flashdata('error');
        $autorizacion->persona = $autorizacion->persona_id;
        $autorizacion->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $autorizacion, TRUE);
        $data['autorizacion'] = $autorizacion;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Autorización';
        $data['title'] = TITLE . ' - Ver Autorización';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/autorizaciones/autorizaciones_abm', $data);
    }

    public function listar_pendientes()
    {
        if (!in_groups($this->grupos_estacion, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Vehículo', 'data' => 'vehiculo', 'width' => 22),
                array('label' => 'Combustible', 'data' => 'tipo_combustible', 'width' => 14),
                array('label' => 'Litros', 'data' => 'litros_autorizados', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Dominio', 'data' => 'dominio', 'width' => 12, 'class' => 'dt-body-right'),
                array('label' => 'Legajo', 'data' => 'persona_id', 'width' => 12, 'class' => 'dt-body-right'),
                array('label' => 'Chofer', 'data' => 'persona', 'width' => 28),
                array('label' => '', 'data' => 'cargar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'autorizaciones_table',
            'source_url' => 'vales_combustible/autorizaciones/listar_pendientes_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_autorizaciones_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Autorizaciones Pendientes de Carga';
        $data['title'] = TITLE . ' - Autorizaciones Pendientes de Carga';
        $this->load_template('vales_combustible/autorizaciones/autorizaciones_listar_pendientes', $data);
    }

    public function listar_pendientes_data()
    {
        if (!in_groups($this->grupos_estacion, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fecha_hoy = new DateTime();
        $this->load->helper('vales_combustible/datatables_functions_helper');
        $this->datatables
                ->select("vc_autorizaciones.id, CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(vc_vehiculos.dominio, 'SIN DOMINIO'), ' - ', vc_vehiculos.propiedad) as vehiculo, vc_tipos_combustible.nombre as tipo_combustible, vc_autorizaciones.lleno, vc_autorizaciones.litros_autorizados, vc_vehiculos.dominio as dominio, vc_autorizaciones.persona_id, CONCAT(personal.Apellido, ', ', personal.Nombre) as persona")
                ->from('vc_autorizaciones')
                ->join('vc_vehiculos', 'vc_vehiculos.id = vc_autorizaciones.vehiculo_id', 'left')
                ->join('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_autorizaciones.tipo_combustible_id', 'left')
                ->join('personal', 'personal.Legajo = vc_autorizaciones.persona_id', 'left')
                ->where('vc_autorizaciones.estado', 'Autorizada')
                ->where('fecha_autorizacion <=', $fecha_hoy->format('Y-m-d'))
                ->edit_column('litros_autorizados', '$1', 'dt_column_autorizaciones_litros(lleno, litros_autorizados)')
                ->add_column('cargar', '<a href="vales_combustible/autorizaciones/cargar/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-thumbs-o-up"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function cargar($id = NULL)
    {
        if (!in_groups($this->grupos_estacion, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/autorizaciones/ver/$id", 'refresh');
        }

        $autorizacion = $this->Autorizaciones_model->get_one($id);
        if (empty($autorizacion) || $autorizacion->estado !== 'Autorizada')
        {
            show_error('No se encontró la Autorización', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'vehiculo' => array('label' => 'Vehiculo', 'disabled' => 'disabled'),
            'tipo_combustible' => array('label' => 'Tipo Combustible', 'disabled' => 'disabled'),
            'lleno' => array('label' => 'Tanque Lleno', 'disabled' => 'disabled', 'id_name' => 'lleno'),
            'litros_autorizados' => array('label' => 'Litros Autorizados', 'type' => 'numeric', 'maxlength' => '50', 'disabled' => 'disabled'),
            'fecha_autorizacion' => array('label' => 'Fecha Autorización', 'type' => 'date', 'disabled' => 'disabled'),
            'estado' => array('label' => 'Estado', 'disabled' => 'disabled'),
            'persona' => array('label' => 'Legajo Chofer', 'type' => 'integer', 'disabled' => 'disabled'),
            'persona_major' => array('label' => 'Chofer', 'disabled' => 'disabled'),
            'persona_nombre' => array('label' => 'Chofer Externo', 'maxlength' => '50', 'disabled' => 'disabled'),
            'observaciones' => array('label' => 'Observaciones', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5, 'disabled' => 'disabled'),
            'usuario' => array('label' => 'Usuario Autorización', 'disabled' => 'disabled'),
            'litros_cargados' => array('label' => 'Litros Cargados', 'type' => 'numeric', 'required' => TRUE),
        );

        $this->set_model_validation_rules($fake_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $fecha_hoy = new DateTime();

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Autorizaciones_model->update(array(
                    'id' => $this->input->post('id'),
                    'estado' => 'Cargada',
                    'litros_cargados' => $this->input->post('litros_cargados'),
                    'fecha_carga' => $fecha_hoy->format('Y-m-d H:i'),
                    'carga_id' => $this->session->userdata('user_id')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Autorizaciones_model->get_msg());
                    redirect('vales_combustible/autorizaciones/listar_pendientes', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Autorizaciones_model->get_error())
                    {
                        $error_msg .= $this->Autorizaciones_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $autorizacion->persona = $autorizacion->persona_id;
        $autorizacion->persona_major = NULL;
        $data['fields'] = $this->build_fields($fake_model->fields, $autorizacion);
        $data['autorizacion'] = $autorizacion;
        $data['txt_btn'] = 'Cargar';
        $data['title_view'] = 'Cargar Combustible';
        $data['title'] = TITLE . ' - Cargar Combustible';
        $data['js'] = 'js/vales_combustible/base.js';
        $this->load_template('vales_combustible/autorizaciones/autorizaciones_abm', $data);
    }
}
