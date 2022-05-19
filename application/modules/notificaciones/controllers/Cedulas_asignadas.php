<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas_asignadas extends MY_Controller
{

    /**
     * Controlador de Cédulas
     * Autor: GENERATOR_MLC
     * Creado: 02/07/2019
     * Modificado: 17/09/2019 (Pablo Gimenez)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notificaciones/Cedulas_model');
        $this->load->model('Usuarios_model');
        $this->load->model('Areas_model');
        $this->load->model('notificaciones/Adjuntos_model');
        $this->load->model('notificaciones/Tipos_documentos_model');
        $this->load->model('notificaciones/Destinatarios_model');
        $this->load->model('notificaciones/Domicilios_model');
        $this->load->model('notificaciones/Hojas_rutas_model');
        $this->load->model('notificaciones/Zonas_model');
        $this->load->model('notificaciones/Cedulas_estados_model');
        $this->load->model('notificaciones/Cedulas_devoluciones_model');
        $this->load->model('notificaciones/Cedulas_devoluciones_tipo_model');
        $this->load->model('notificaciones/Cedulas_movimientos_model');
        $this->load->model('notificaciones/Usuarios_areas_model');
        $this->load->helper('notificaciones/notificaciones_functions_helper');
        $this->load->helper('datatables_helper');
        $this->load->helper('datatables_functions_helper');
        /**
         * Grupos: admin: adminitrador,
         *      - notificaciones_user: usuario de notificaciones
         *      - notificaciones_areas: usuario de oficina externas a notificaciones
         *      - notificaciones_notificadores: usuario Notificador
         *      - notificaciones_control: usuario de Control y Estadisticas
         */

        $this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_notificadores');
        $this->grupos_admin = array('admin');
//        $this->grupos_notificaciones = array('admin', 'notificaciones_user');
        $this->grupos_notificaciones = array('admin', 'notificaciones_user');
        $this->grupos_notificadores = array('admin', 'notificaciones_notificadores');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(//@todo arreglar anchos de columnas
                array('label' => 'Id', 'data' => 'id', 'width' => 4),
                array('label' => 'Nro de Documento', 'data' => 'n_documento', 'width' => 10),
                array('label' => 'Año', 'data' => 'anio', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Nro de Cedula', 'data' => 'n_cedula', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Texto', 'data' => 'texto', 'width' => 10),
//				array('label' => 'Rotacion de Insp', 'data' => 'rotacion_insp', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 10),
                array('label' => 'Oficina', 'data' => 'oficina_id', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Tipo de Doc', 'data' => 'tipo_doc_id', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Destinatario', 'data' => 'destinatario_id', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Domicilio', 'data' => 'domicilio_id', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Hoja de Ruta', 'data' => 'hoja_ruta_id', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Notificador', 'data' => 'notificador_id', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Zona', 'data' => 'zona_id', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Estado', 'data' => 'estado_id', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => 'Fecha de Creacion', 'data' => 'fecha_creacion', 'width' => 10, 'render' => 'datetime'),
//                array('label' => 'Fecha de Update', 'data' => 'fecha_update', 'width' => 10, 'render' => 'datetime'),
//                array('label' => 'Fecha de Probable Entrega', 'data' => 'fecha_probable_entrega', 'width' => 10, 'render' => 'date'),
//				array('label' => 'Fecha de Notificacion', 'data' => 'fecha_notificacion', 'width' => 10, 'render' => 'datetime'),
//				array('label' => 'Fecha de Delete', 'data' => 'fecha_delete', 'width' => 10, 'render' => 'datetime'),
//				array('label' => 'Audi de Usuario', 'data' => 'audi_usuario', 'width' => 10, 'class' => 'dt-body-right'),
//				array('label' => 'Audi de Fecha', 'data' => 'audi_fecha', 'width' => 10, 'render' => 'datetime'),
//				array('label' => 'Audi de Accion', 'data' => 'audi_accion', 'width' => 10),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
//                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
//                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'cedulas_table',
            //'source_url' => 'notificaciones/cedulas_asignadas/listar_data',
            'reuse_var' => TRUE,
            'order' => [[7, 'desc']],
            'initComplete' => 'complete_cedulas_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );

        if (in_groups($this->grupos_notificaciones, $this->grupos)) {
            $tableData['source_url'] = 'notificaciones/cedulas_asignadas/listar_data';
        } elseif (in_groups($this->grupos_notificadores, $this->grupos)) {
            $tableData['source_url'] = 'notificaciones/cedulas_asignadas/listar_data_notificador';
        }


        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Cédulas';
        $data['title'] = TITLE . ' - Cédulas';
        $this->load_template('notificaciones/cedulas_asignadas/cedulas_listar', $data);
    }


    /**
     * Genera el datatable con todas oficinas (USO PARA NOTICACIONES)
     */
    public function listar_data()
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('notificaciones/datatables_functions_helper');
        // Buscar las celulas que tiene el usuario en su oficina o todas si sos de notificaciones

        $this->datatables
            ->select(
                'nv_cedulas.id, n_documento, anio, n_cedula, CONCAT(areas.codigo, " - ", areas.nombre) as oficina_id, prioridad, nv_tipos_documentos.descripcion as tipo_doc_id, nv_cedulas_estados.id as estado_id, nv_cedulas_estados.descripcion as estado_desc, fecha_creacion, fecha_update, fecha_probable_entrega'
            )
            ->from('nv_cedulas')
            ->join('nv_cedulas_estados', 'nv_cedulas.estado_id = nv_cedulas_estados.id', 'left')
            ->join('nv_tipos_documentos', 'nv_cedulas.tipo_doc_id = nv_tipos_documentos.id', 'left')
            ->join('areas', 'nv_cedulas.oficina_id = areas.id', 'left')
            ->edit_column('id', '$1', 'dt_column_notificaciones_cedulas_prioridad(id, prioridad)', TRUE)
            ->edit_column('estado_id', '$1', 'dt_column_notificaciones_cedulas_estado(estado_id, estado_desc)', TRUE)
            ->add_column('ver', '<a href="notificaciones/cedulas_asignadas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            //           ->add_column('editar', '<a href="notificaciones/cedulas_asignadas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            //           ->add_column('eliminar', '<a href="notificaciones/cedulas_asignadas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id')
            ->add_column('editar', NULL, 'id')
            ->add_column('eliminar', NULL, 'id');

        echo $this->datatables->generate();
    }



    /**
     * Genera el datatable correspondiente al notificador
     */
    public function listar_data_notificador()
    {
        if (!in_groups($this->grupos_notificadores, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->load->helper('notificaciones/datatables_functions_helper');
        // Buscar las celulas que tiene el usuario en su oficina o todas si sos de notificaciones


        $this->datatables
            ->select(
                'nv_cedulas.id, n_documento, anio, n_cedula, oficina_id, nv_tipos_documentos.descripcion as tipo_doc_id, nv_cedulas_estados.id as estado_id, nv_cedulas_estados.descripcion as estado_desc, fecha_creacion, fecha_update, fecha_probable_entrega'
            )
            ->from('nv_cedulas')
            ->join('nv_cedulas_estados', 'nv_cedulas.estado_id = nv_cedulas_estados.id', 'left')
            ->join('nv_tipos_documentos', 'nv_cedulas.tipo_doc_id = nv_tipos_documentos.id', 'left')
            ->join('areas', 'nv_cedulas.oficina_id = areas.id', 'left')
            ->where('nv_cedulas.notificador_id', $this->session->userdata('user_id'))
            ->edit_column('estado_id', '$1', 'dt_column_notificaciones_cedulas_estado(estado_id, estado_desc)', TRUE)
            ->add_column('ver', '<a href="notificaciones/cedulas_asignadas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            //           ->add_column('editar', '<a href="notificaciones/cedulas_asignadas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            //           ->add_column('eliminar', '<a href="notificaciones/cedulas_asignadas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id')
            ->add_column('editar', NULL, 'id')
            ->add_column('eliminar', NULL, 'id');

        echo $this->datatables->generate();
    }


    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $cedula = $this->Cedulas_model->get_one($id);

        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }


        if (!$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $cedula->oficina_id) && !in_groups($this->grupos_notificaciones, $this->grupos) && !in_groups($this->grupos_notificadores, $this->grupos)) {
            show_error('No tiene permisos para ver la cedula', 500, 'Acción no autorizada');
        }


        $data['fields'] = $this->build_fields($this->Cedulas_model->fields, $cedula, TRUE);
        $data['cedula'] = $cedula;

        // Relacionados
        $data['destinatario'] = $this->Destinatarios_model->get_one($cedula->destinatario_id);
        $data['domicilio'] = $this->Domicilios_model->get_one($cedula->domicilio_id);
        $data['estado'] = $this->Cedulas_estados_model->get_one($cedula->estado_id);
        $data['oficina'] = $this->Areas_model->get(['id' => $cedula->oficina_id]);
        $data['tipo_documento'] = $this->Tipos_documentos_model->get_one($cedula->tipo_doc_id);

        // Movimientos
        $data['movimientos'] = $this->Cedulas_movimientos_model->get_movimientos($cedula->id);
        $data['movimiento_actual'] = $this->Cedulas_movimientos_model->get_last_movimiento($cedula->id);

        // Devolucion
        $data['devolucion'] = $this->Cedulas_devoluciones_model->get_devolucion($cedula->id);


        // Notificador
        $data['notificador'] = $this->Cedulas_model->get_notificador($cedula->notificador_id);

        // Zona
        $zona = NULL;
        if (!is_null($cedula->zona_id)) {
            $zona = $this->Zonas_model->get_one($cedula->zona_id);
        }
        $data['zona'] = $zona;

        //Adjuntos
        $adjuntos = $this->Adjuntos_model->get(array(
            'cedula_id' => $id,
            'join' => array(
                array('nv_tipos_adjuntos', 'nv_tipos_adjuntos.id = nv_adjuntos.tipo_id', 'LEFT', array('nv_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));
        $array_adjuntos = array();
        if (!empty($adjuntos)) {
            foreach ($adjuntos as $Adjunto) {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;
        $data['cantidad_adjuntos'] = empty($adjuntos) ? 0 : count($adjuntos);


        $data['adjuntos_eliminar_existente_post'] = array();


        $data['txt_btn'] = FALSE;
        $data['txt_tipo_user'] = FALSE;
        $data['txt_btn_erronea'] = FALSE;


        if (in_groups($this->grupos_notificaciones, $this->grupos)) {
            $data['txt_tipo_user'] = "OFICINA_NOTIFICACIONES";
            if ($this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::SOLICITUD_REALIZADA)) {
                $data['txt_btn'] = "Realizada";
            } elseif ($this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::SOLICITUD_ACEPTADA)) {
                $data['txt_btn'] = "Asignar";
            } elseif ($this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::NOTIFICADOR_ASIGNADO)) {
                $data['txt_btn'] = "Imprimir";
            } elseif ($this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::CEDULA_IMPRESA)) {
                $data['txt_btn'] = "Entrega";
            }
        } elseif (in_groups($this->grupos_notificadores, $this->grupos)) {
            $data['txt_tipo_user'] = "NOTIFICADOR";
            if ($this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::NOTIFICADOR_ASIGNADO)) {
                $data['txt_btn'] = "Imprimir";
            } elseif ($this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::CEDULA_IMPRESA)) {
                $data['txt_btn'] = "Entrega";
            }
        }

        $data['title_view'] = 'Ver Cédula';
        $data['title'] = TITLE . ' - Ver Cédula';

        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';

        $this->load_template('notificaciones/cedulas_asignadas/cedulas_ver_tab', $data);
    }


    /**
     * Imprimir Cedula
     * @param null $id
     */

    public function despachar($id = NULL)
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas_asignadas/ver/$id", 'refresh');
        }

        $cedula = $this->Cedulas_model->get(array('id' => $id));
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }
        // Reimprimir cedula
        if ($this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::CEDULA_IMPRESA)) {
            $this->imprimir_cedula($cedula);
        } else if (!$this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::NOTIFICADOR_ASIGNADO)) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        } else {
            $error_msg = FALSE;

            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Cedulas_model->update(array(
                'id' => $cedula->id,
                'estado_id' => Cedulas_estados_model::CEDULA_IMPRESA,
            ), FALSE);

            $trans_ok &= $this->Cedulas_movimientos_model->add_movimiento($cedula->id, Cedulas_estados_model::CEDULA_IMPRESA);

            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
                redirect('notificaciones/cedulas_asignadas/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cedulas_model->get_error()) {
                    $error_msg .= $this->Cedulas_model->get_error();
                }
            }

            $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
            $this->imprimir_cedula($cedula);
        }
    }

    public function imprimir($id = NULL)
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas_asignadas/ver/$id", 'refresh');
        }

        $cedula = $this->Cedulas_model->get(array('id' => $id));
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }
        // Reimprimir cedula
        if (!$this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::CEDULA_IMPRESA)) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        }
        $this->descargar_pdf_cedula($cedula);
    }

    public function vista_previa_impresion($id)
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas_asignadas/ver/$id", 'refresh');
        }

        $cedula = $this->Cedulas_model->get(array('id' => $id));
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }
        // Reimprimir cedula
        if (!$this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::CEDULA_IMPRESA)) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        }


        $cedula = $this->Cedulas_model->get(array('id' => $id));
        $data['cedula'] = $cedula;
        $data['tipo_cedula'] = $this->Tipos_documentos_model->get_one($cedula->tipo_doc_id);
        $data['destinatario'] = $this->Destinatarios_model->get_one($cedula->destinatario_id);
        $data['domicilio'] = $this->Domicilios_model->get_one($cedula->domicilio_id);

        // $this->load->view('notificaciones/cedulas_asignadas/cedulas_pdf', $data, TRUE);

        $data['css'] = 'vendor/bootstrap/css/bootstrap.min.css';
        $data['css'] = 'css/notificaciones/impresion.css';
        $data['title'] = TITLE . ' - Vista previa Cédula';

        $this->load_template('notificaciones/cedulas_asignadas/cedulas_pdf_impresion', $data);
    }


    private function descargar_pdf_cedula($cedula)
    {
//        $data['mes_planilla'] = ucfirst(strftime("%B %Y", strtotime($fecha)));
        $data['cedula'] = $cedula;
        $data['tipo_cedula'] = $this->Tipos_documentos_model->get_one($cedula->tipo_doc_id);
        $data['destinatario'] = $this->Destinatarios_model->get_one($cedula->destinatario_id);
        $data['domicilio'] = $this->Domicilios_model->get_one($cedula->domicilio_id);

        $html = $this->load->view('notificaciones/cedulas_asignadas/cedulas_pdf_impresion', $data, TRUE);

        $stylesheet = file_get_contents('vendor/bootstrap/css/bootstrap.min.css');
        $stylesheet .= file_get_contents('css/notificaciones/impresion.css');

        try {

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'c',
                'format' => 'A4',
                'margin_left' => 30,
                'margin_right' => 20,
                'margin_top' => 6,
                'margin_bottom' => 6,
                'margin_header' => 9,
                'margin_footer' => 9
            ]);
            $mpdf->SetDisplayMode('fullwidth');
            $mpdf->simpleTables = true;
            $mpdf->SetTitle("Notificacion " . $cedula->n_cedula);
            $mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
            $mpdf->WriteHTML($stylesheet, 1);
            $mpdf->WriteHTML($html, 2);
            $mpdf->Output("Notificacion_" . $cedula->n_cedula . '.pdf', 'I');
        } catch (Exception $e) {
            dd($e);
        }


        $this->ver($cedula->id);
    }


    public function entrega($id = NULL)
    {

        if (!in_groups($this->grupos_notificaciones, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas_asignadas/ver/$id", 'refresh');
        }

        $cedula = $this->Cedulas_model->get(array('id' => $id));
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }

        if (!$this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::CEDULA_IMPRESA)) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        }

        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id') || !in_array($this->input->post('tipo_devolucion_id'), [1, 2, 3])) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            //dd($this->input->post());

            switch ($this->input->post('tipo_devolucion_id')) {
                case 1:
                    $estado_cedula = Cedulas_estados_model::ENTREGA_POSITIVA_MANO;
                    break;
                case 2:
                    $estado_cedula = Cedulas_estados_model::ENTREGA_POSITIVA_BAJO_PUERTA;
                    break;
                case 3:
                    $estado_cedula = Cedulas_estados_model::ENTREGA_NEGATIVA;
                    break;
            }

            $error_msg = FALSE;

            $this->db->trans_begin();
            $trans_ok = TRUE;

            $observaciones = empty($this->input->post('observaciones_devolucion')) ? "SIN OBSERVACIONES" : $this->input->post('observaciones_devolucion');

            $trans_ok &= $this->Cedulas_devoluciones_model->create(array(
                'cedula_id' => $cedula->id,
                'tipo_devolucion_id' => $this->input->post('tipo_devolucion_id'),
                'observaciones' => $observaciones,
            ), FALSE);

            $trans_ok &= $this->Cedulas_model->update(array(
                'id' => $cedula->id,
                'estado_id' => $estado_cedula,
            ), FALSE);

            $trans_ok &= $this->Cedulas_movimientos_model->add_movimiento($cedula->id, $estado_cedula);

            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
                redirect('notificaciones/cedulas_asignadas/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cedulas_model->get_error()) {
                    $error_msg .= $this->Cedulas_model->get_error();
                }
            }

            $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        }


        // Tipo de Devolucion
        $data['tipos_devoluciones'] = $this->Cedulas_devoluciones_tipo_model->get();
        $data['domicilio'] = $this->Domicilios_model->get_one($cedula->domicilio_id);

        $data['notificador'] = $this->Cedulas_model->get_notificador($cedula->notificador_id);

        // Zona
        $zona = NULL;
        if (!is_null($cedula->zona_id)) {
            $zona = $this->Zonas_model->get_one($cedula->zona_id);
        }
        $data['zona'] = $zona;

        $data['cedula'] = $cedula;
        $data['txt_btn'] = 'Entrega';
        $data['title_view'] = 'Cargar Entrega';
        $data['title'] = TITLE . ' - Cargar Entrega';
        $this->load_template('notificaciones/cedulas_asignadas/cedulas_entrega', $data);


    }
    
}