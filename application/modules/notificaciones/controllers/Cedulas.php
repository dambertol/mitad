<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas extends MY_Controller
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
         *      - notificaciones_user: usuario de notificaciones
         *      - notificaciones_areas: usuario de oficina externas a notificaciones
         *      - notificaciones_notificadores: usuario Notificador
         *      - notificaciones_control: usuario de Control y Estadisticas
         */

        $this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_areas', 'notificaciones_notificadores', 'notificaciones_control');
        $this->grupos_admin = array('admin');
        $this->grupos_oficinas = array('notificaciones_areas');
        $this->grupos_notificaciones = array('admin', 'notificaciones_user');
        $this->grupos_notificadores = array('admin', 'notificaciones_notificadores');
        $this->grupos_solo_consulta = array('notificaciones_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar($estado = 0)
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
                array('label' => 'Fecha de Update', 'data' => 'fecha_update', 'width' => 10, 'render' => 'datetime'),
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
            //'source_url' => 'notificaciones/cedulas/listar_data',
            'reuse_var' => TRUE,
            'order' => [[7, 'desc']],
            'initComplete' => 'complete_cedulas_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );

        $data['show_btn'] = FALSE;

        if (in_groups($this->grupos_oficinas, $this->grupos)) {
            $tableData['source_url'] = "notificaciones/cedulas/listar_data_areas/$estado";
        } elseif (in_groups($this->grupos_notificaciones, $this->grupos)) {
            $tableData['source_url'] = "notificaciones/cedulas/listar_data/$estado";
            $data['vencidas'] = count($this->_get_cedulas_vencidas());
            $data['show_btn'] = TRUE;
        } elseif (in_groups($this->grupos_notificadores, $this->grupos)) {
            $tableData['source_url'] = "notificaciones/cedulas/listar_data_notificador/$estado";
        }


        $array_estados = [
            1 => "SOLICITUD REALIZADA",
            2 => "SOLICITUD ACEPTADA",
            3 => "NOTIFICADOR ASIGNADO",
            4 => "ENTREGA POSITIVA_MANO",
            5 => "ENTREGA POSITIVA_BAJO_PUERTA",
            6 => "ENTREGA NEGATIVA",
            7 => "DATOS INCORRECTOS",
            8 => "SOLICITUD ANULADA",
            9 => "CEDULA IMPRESA",
            // 9 => "EN RUTA"
        ];


        $data['array_estados'] = $array_estados;
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Cédulas';
        $data['title'] = TITLE . ' - Cédulas';
        $this->load_template('notificaciones/cedulas/cedulas_listar', $data);
    }


    /**
     * Genera el datatable con todas oficinas (USO PARA NOTICACIONES)
     */
    public function listar_data($estado = 0)
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('notificaciones/datatables_functions_helper');
        // Buscar las celulas que tiene el usuario en su oficina o todas si sos de notificaciones

        $this->datatables
            ->select(
                'nv_cedulas.id, n_documento, anio, n_cedula, 
                CONCAT(areas.codigo, " - ", areas.nombre) as oficina_id, 
                prioridad, 
                nv_tipos_documentos.descripcion as tipo_doc_id, 
                nv_cedulas_movimientos.tipo_movimiento_id as estado_id, 
                nv_cedulas_movimientos_tipos.descripcion as estado_desc, 
                fecha_creacion, fecha_update, fecha_probable_entrega, fecha_delete'
            )
            ->from('nv_cedulas')
            //  ->join('nv_cedulas_estados', 'nv_cedulas.estado_id = nv_cedulas_estados.id', 'left')
            //    ->join('nv_cedulas_movimientos', 'nv_cedulas.id = nv_cedulas_movimientos.cedula_id', 'left')

            ->join('areas', 'nv_cedulas.oficina_id = areas.id', 'left')
            ->join('nv_tipos_documentos', 'nv_cedulas.tipo_doc_id = nv_tipos_documentos.id', 'left')
            // obtener el ultimo estado de los movimientos
            ->join('nv_cedulas_movimientos', 'nv_cedulas_movimientos.cedula_id = nv_cedulas.id', 'left')
            ->join('nv_cedulas_movimientos P', 'P.cedula_id = nv_cedulas.id AND nv_cedulas_movimientos.fecha < P.fecha', 'left outer')
            ->join('nv_cedulas_movimientos_tipos', 'nv_cedulas_movimientos.tipo_movimiento_id = nv_cedulas_movimientos_tipos.id', 'left')
            ->where('nv_cedulas.fecha_delete IS NULL')
            ->where('P.id IS NULL')
            ->edit_column('id', '$1', 'dt_column_notificaciones_cedulas_prioridad(id, prioridad)', TRUE)
            ->edit_column('estado_id', '$1', 'dt_column_notificaciones_cedulas_estado(estado_id, estado_desc)', TRUE)
            ->add_column('ver', '<a href="notificaciones/cedulas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            //           ->add_column('editar', '<a href="notificaciones/cedulas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            //           ->add_column('eliminar', '<a href="notificaciones/cedulas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id')
            ->add_column('editar', NULL, 'id')
            ->add_column('eliminar', NULL, 'id');


        switch ($estado) {
            case 1:
                // Pendientes
                $this->datatables->where('nv_cedulas_movimientos.tipo_movimiento_id IN (1,7)');
                break;
            case 2:
                // En proceso
                $this->datatables->where('nv_cedulas_movimientos.tipo_movimiento_id IN (2,3,9)');
                break;
            case 3:
                // Cerradas
                $this->datatables->where('nv_cedulas_movimientos.tipo_movimiento_id IN (4,5,6,8)');
                break;
            case 0:
                // Ver todas
                break;
        }

        echo $this->datatables->generate();
    }


    /**
     * Genera el datatable correspondiente a la oficina del usuario
     */
    public function listar_data_areas()
    {
        if (!in_groups($this->grupos_oficinas, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        $this->load->helper('notificaciones/datatables_functions_helper');
        // Buscar las celulas que tiene el usuario en su oficina o todas si sos de notificaciones


        $this->datatables
            ->select(
                'nv_cedulas.id, n_documento, anio, n_cedula, , CONCAT(areas.codigo, " - ", areas.nombre) as oficina_id, nv_tipos_documentos.descripcion as tipo_doc_id, nv_cedulas_estados.id as estado_id, nv_cedulas_estados.descripcion as estado_desc, fecha_creacion, fecha_update, fecha_probable_entrega'
            )
            ->from('nv_cedulas')
            ->join('nv_cedulas_estados', 'nv_cedulas.estado_id = nv_cedulas_estados.id', 'left')
            ->join('nv_tipos_documentos', 'nv_cedulas.tipo_doc_id = nv_tipos_documentos.id', 'left')
            ->join('nv_usuarios_areas', 'nv_cedulas.oficina_id = nv_usuarios_areas.area_id', 'left')
            ->join('areas', 'nv_cedulas.oficina_id = areas.id', 'left')
            ->where('nv_usuarios_areas.user_id', $this->session->userdata('user_id'))
            ->where('nv_cedulas.fecha_delete IS NULL')
            ->edit_column('estado_id', '$1', 'dt_column_notificaciones_cedulas_estado(estado_id, estado_desc)', TRUE)
            ->add_column('ver', '<a href="notificaciones/cedulas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            //           ->add_column('editar', '<a href="notificaciones/cedulas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            //           ->add_column('eliminar', '<a href="notificaciones/cedulas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id')
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
            ->where('nv_cedulas.fecha_delete IS NULL')
            ->edit_column('estado_id', '$1', 'dt_column_notificaciones_cedulas_estado(estado_id, estado_desc)', TRUE)
            ->add_column('ver', '<a href="notificaciones/cedulas/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
            //           ->add_column('editar', '<a href="notificaciones/cedulas/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
            //           ->add_column('eliminar', '<a href="notificaciones/cedulas/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id')
            ->add_column('editar', NULL, 'id')
            ->add_column('eliminar', NULL, 'id');

        echo $this->datatables->generate();
    }


    public function solicitar()
    {
        if (!in_groups($this->grupos_oficinas, $this->grupos) && !in_groups($this->grupos_notificaciones, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('notificaciones/cedulas/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Cedulas_model);

        $this->form_validation->set_rules('n_identificacion', 'Nro de Identificacion', 'required');
        $this->form_validation->set_rules('prioridad', 'Prioridad', 'required');
        $this->form_validation->set_rules('n_documento', 'Nro de Documento', 'required|min_length[1]');
        $this->form_validation->set_rules('anio', 'Año del Expediente o Nota', 'required|exact_length[4]');
        $this->form_validation->set_rules('texto', 'Cuerpo de la Cédula', 'required|max_length[2500]');
        $this->form_validation->set_rules('observaciones', 'Observaciones', 'max_length[1000]');

        $this->form_validation->set_rules('oficina_id', 'Oficina Origen', 'required');
        $this->form_validation->set_rules('tipo_documento_id', 'Tipo Documento', 'required');


        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE) {

            $fecha = new DateTime();
            $this->db->trans_begin();
            $trans_ok = TRUE;


            /// Crear Destinatario
            $trans_ok &= $this->Destinatarios_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'apellido' => $this->input->post('apellido'),
                'tipo_identificacion' => $this->input->post('tipo_identificacion'),
                'n_identificacion' => $this->input->post('n_identificacion'),
            ), FALSE);
            $destinatario_id = $this->Destinatarios_model->get_row_id();

            /// Crear Domicilio
            $trans_ok &= $this->Domicilios_model->create(array(
                'direccion' => $this->input->post('domicilio'),
                'num' => $this->input->post('altura_domiicilio'),
                'localidad' => $this->input->post('localidad'),
                'alternativo' => $this->input->post('domicilio_alternativo'),
                'codigo_postal' => $this->input->post('codigo_postal'),
                'coordenadas' => null,
                'departamento_id' => null,
                'fecha_creacion' => $fecha->format('Y-m-d H:i:s'),
            ), FALSE);

            $domicilio_id = $this->Domicilios_model->get_row_id();


            //  dd($this->input->post());
            /*
             * Se calcula la fecha probable de entrega segun la prioridad
             * 0 = Urgente 24 hs
             * 7 = Media 7 dias
             * 14 = Baja 14 dias
             *
             */

            $prioridad = intval($this->input->post('prioridad'));
            switch ($prioridad) {
                case 1:
                    $cantidad_dias = 1;
                    break;
                case 7:
                    $cantidad_dias = 7;
                    break;
                default:
                    $cantidad_dias = 14;
                    break;
            }
            $fecha_probable_entrega = $this->_calcular_fecha_entrega(new DateTime(), $cantidad_dias);


            /// Crear Cedula
            $trans_ok &= $this->Cedulas_model->create(array(
                'n_documento' => $this->input->post('n_documento'),
                'anio' => $this->input->post('anio'),
                'n_cedula' => $this->get_next_n_cedula(),
                'prioridad' => $this->input->post('prioridad'),
                'texto' => $this->input->post('texto'),
//				'rotacion_insp' => $this->input->post('rotacion_insp'),
                'observaciones' => $this->input->post('observaciones'),
                'oficina_id' => $this->input->post('oficina_id'),
                'tipo_doc_id' => $this->input->post('tipo_documento_id'),
                'destinatario_id' => $destinatario_id,
                'domicilio_id' => $domicilio_id,
//				'hoja_ruta_id' => $this->input->post('hoja_ruta'),
//				'notificador_id' => $this->input->post('notificador'),
//				'zona_id' => $this->input->post('zona'),
                'estado_id' => Cedulas_estados_model::SOLICITUD_REALIZADA, //$this->input->post('estado'),
                'fecha_creacion' => $fecha->format('Y-m-d H:i:s'),
                'fecha_probable_entrega' => $fecha_probable_entrega->format('Y-m-d H:i:s'),
//              'fecha_update' => $this->input->post('fecha_update'),
//				'fecha_notificacion' => $this->input->post('fecha_notificacion'),
//				'fecha_delete' => $this->input->post('fecha_delete'),

            ), FALSE);

            $cedula_id = $this->Cedulas_model->get_row_id();


            /// Crear Movimiento de la cedula
            $trans_ok &= $this->Cedulas_movimientos_model->add_movimiento($cedula_id, Cedulas_estados_model::SOLICITUD_REALIZADA);


            /// Adjuntos
            $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
            if (!empty($adjuntos_agregar_post)) {
                foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name) {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));

                    if (!empty($adjunto) && empty($adjunto->vehiculo_id)) {
                        $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                        if (file_exists($viejo_archivo)) {
                            $nueva_ruta = "uploads/notificaciones/cedulas/" . str_pad($cedula_id, 6, "0", STR_PAD_LEFT) . "/";
                            if (!file_exists($nueva_ruta)) {
                                mkdir($nueva_ruta, 0755, TRUE);
                            }
                            $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                            $trans_ok &= $this->Adjuntos_model->update(array(
                                'id' => $Adjunto_id,
                                'nombre' => $nuevo_nombre,
                                'ruta' => $nueva_ruta,
                                'cedula_id' => $cedula_id
                            ), FALSE);
                            $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                            if (!$renombrado) {
                                $trans_ok = FALSE;
                            }
                        } else {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Se ha producido un error con los adjuntos.';
                        }
                    } else {
                        $trans_ok = FALSE;
                        $error_msg = '<br />Se ha producido un error con los adjuntos.';
                    }
                }
            }


            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
                redirect('notificaciones/cedulas/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cedulas_model->get_error()) {
                    $error_msg .= $this->Cedulas_model->get_error();
                }
            }
        }

        $data['fields'] = $this->build_fields($this->Cedulas_model->fields);
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['areas'] = $this->Areas_model->get();

        $data['areas_usuario'] = NULL;
        if (in_groups($this->grupos_oficinas, $this->grupos)) {
            $data['areas_usuario'] = $this->Usuarios_areas_model->get_areas((int)$this->session->userdata('user_id'));
        }

        $data['tipos_documentos'] = $this->Tipos_documentos_model->get();

        $data['txt_btn'] = 'Solicitar';
        $data['title_view'] = 'Solicitar Cédula';
        $data['title'] = TITLE . ' - Solicitar Cédula';

        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';


        $data['adjuntos_view'] = $this->load->view('notificaciones/cedulas/cedulas_adjuntos', $data, TRUE);
        $this->load_template('notificaciones/cedulas/cedulas_solicitar', $data);
    }


    /**
     * Devuelve el valor de la proxima cedula a crear. Ultima cedula creada + 1
     * @return int
     */
    public function get_next_n_cedula()
    {
        return $this->Cedulas_model->get(
                [
                    "select" => "nv_cedulas.n_cedula",
                    "sort_by" => "nv_cedulas.n_cedula desc",
                    'limit' => 1
                ]
            )[0]->n_cedula + 1;
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
        $data['notificador_suplente'] = $this->Cedulas_model->get_notificador($cedula->notificador_suplente_id);

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

        if (in_groups($this->grupos_oficinas, $this->grupos)) {
            $data['txt_tipo_user'] = "OFICINA_EXTERNA";
            if ($this->Cedulas_model->tiene_estado($cedula->id, [Cedulas_estados_model::SOLICITUD_REALIZADA, Cedulas_estados_model::DATOS_INCORRECTOS])) {
                $data['txt_btn'] = "Editar";
            }
        }

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

        $this->load_template('notificaciones/cedulas/cedulas_ver_tab', $data);
    }


    public function editar($id = NULL)
    {
        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
        }

        if ((!in_groups($this->grupos_oficinas, $this->grupos) && !in_groups($this->grupos_admin, $this->grupos)) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }


        $cedula = $this->Cedulas_model->get_one($id);

        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }


        if (!in_groups($this->grupos_admin, $this->grupos)) {
            if (!$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $cedula->oficina_id)) {
                show_error('No tiene permisos para editar la cedula', 500, 'Acción no autorizada');
            }
        }

        if (!$this->Cedulas_model->tiene_estado($cedula->id, [Cedulas_estados_model::SOLICITUD_REALIZADA, Cedulas_estados_model::DATOS_INCORRECTOS])) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        }

        // Busqueda de relacionados
        $destinatario = $this->Destinatarios_model->get_one($cedula->destinatario_id);
        $domicilio = $this->Domicilios_model->get_one($cedula->domicilio_id);

        $this->set_model_validation_rules($this->Cedulas_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE) {

                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;

                /// Crear Destinatario
                /**
                 * TODO: busqueda de destinatario
                 */
                $destinatario_id = $destinatario->id;

                $d_new = $this->Destinatarios_model->buscar_destinatario($this->input->post('tipo_identificacion'), $this->input->post('n_identificacion'));

                if (is_null($d_new)) {
                    $trans_ok &= $this->Destinatarios_model->create(array(
                        'nombre' => $this->input->post('nombre'),
                        'apellido' => $this->input->post('apellido'),
                        'tipo_identificacion' => $this->input->post('tipo_identificacion'),
                        'n_identificacion' => $this->input->post('n_identificacion'),
                    ), FALSE);
                    $destinatario_id = $this->Destinatarios_model->get_row_id();
                } else {
                    // existe update
                    $trans_ok &= $this->Destinatarios_model->update(array(
                        'id' => $destinatario_id,
                        'nombre' => $this->input->post('nombre'),
                        'apellido' => $this->input->post('apellido'),
                        'tipo_identificacion' => $this->input->post('tipo_identificacion'),
                        'n_identificacion' => $this->input->post('n_identificacion'),
                    ), FALSE);
                }
$t1 = $trans_ok;
                /// Busqueda Domicilio
                /**
                 * TODO: busqueda de domicilio (siempre actualiza)
                 */

                $domicilio_id = $domicilio->id;
                // if (($domicilio->direccion != $this->input->post('domicilio')) || ($domicilio->num != $this->input->post('altura_domiicilio'))) {
                $trans_ok &= $this->Domicilios_model->update(array(
                    'id' => $domicilio_id,
                    'direccion' => $this->input->post('domicilio'),
                    'num' => $this->input->post('altura_domiicilio'),
                    'localidad' => $this->input->post('localidad'),
                    'alternativo' => $this->input->post('domicilio_alternativo'),
                    'codigo_postal' => $this->input->post('codigo_postal'),
                    'coordenadas' => null,
                    'departamento_id' => null,
                    'fecha_creacion' => $fecha->format('Y-m-d H:i:s'),
                ), FALSE);
                $t2 = $trans_ok;
                /**
                 * Calculo de la fecha de entrega (Siempre Actualiza)
                 *
                 */
                $prioridad = intval($this->input->post('prioridad'));
                switch ($prioridad) {
                    case 1:
                        $cantidad_dias = 1;
                        break;
                    case 7:
                        $cantidad_dias = 7;
                        break;
                    default:
                        $cantidad_dias = 14;
                        break;
                }
                $fecha_probable_entrega = $this->_calcular_fecha_entrega(new DateTime(), $cantidad_dias);


                /// Editar Cedula
                ///
                $trans_ok &= $this->Cedulas_model->update(array(
                    'id' => $this->input->post('id'),
                    'n_documento' => $this->input->post('n_documento'),
                    'anio' => $this->input->post('anio'),
                    'n_cedula' => $cedula->n_cedula,
                    'prioridad' => $this->input->post('prioridad'),
                    'texto' => $this->input->post('texto'),
//				'rotacion_insp' => $this->input->post('rotacion_insp'),
                    'observaciones' => $this->input->post('observaciones'),
                    'oficina_id' => $this->input->post('oficina_id'),
                    'tipo_doc_id' => $this->input->post('tipo_documento_id'),
                    'destinatario_id' => $destinatario_id,
                    'domicilio_id' => $domicilio_id,
                    'fecha_creacion' => $cedula->fecha_creacion,//$fecha->format('Y-m-d H:i:s'),
                    'fecha_probable_entrega' => $fecha_probable_entrega->format('Y-m-d H:i:s'),
                    'estado_id' => Cedulas_estados_model::SOLICITUD_REALIZADA,
                ), FALSE);
                $t3 = $trans_ok;
                $cedula_id = $id;
                $error_msg = "";
                /// Crear Movimiento de la cedula
                $trans_ok &= $this->Cedulas_movimientos_model->add_movimiento($cedula_id, Cedulas_estados_model::SOLICITUD_REALIZADA);

                /// Adjuntos
                $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
                if (!empty($adjuntos_agregar_post)) {
                    foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name) {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id)) {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo)) {
                                $nueva_ruta = "uploads/notificaciones/cedulas/" . str_pad($cedula_id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta)) {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'cedula_id' => $cedula_id
                                ), FALSE);
                                $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                                if (!$renombrado) {
                                    $trans_ok = FALSE;
                                }
                            } else {
                                $trans_ok = FALSE;
                                $error_msg .= '<br />Se ha producido un error con los adjuntos.';
                            }
                        } else {
                            $trans_ok = FALSE;
                            $error_msg .= '<br />Se ha producido un error con los adjuntos.';
                        }
                    }
                }
                $t4 = $trans_ok;

                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
                    redirect('notificaciones/cedulas/listar', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Cedulas_model->get_error()) {
                        $error_msg .= $this->Cedulas_model->get_error();
                    }
                    if ($this->Destinatarios_model->get_error()) {
                        $error_msg .= $this->Destinatarios_model->get_error();
                    }
                    if ($this->Domicilios_model->get_error()) {
                        $error_msg .= $this->Domicilios_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error()) {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                    if ($t1) $error_msg .= "t1";
                    if ($t2) $error_msg .= "t2";
                    if ($t3) $error_msg .= "t3";
                    if ($t4) $error_msg .= "t4";
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));


        $data['areas'] = $this->Areas_model->get();
        $data['tipos_documentos'] = $this->Tipos_documentos_model->get();

        $data['destinatario'] = $destinatario;
        $data['domicilio'] = $domicilio;

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
        $data['adjuntos_eliminar_existente_post'] = array();


        $data['fields'] = $this->build_fields($this->Cedulas_model->fields, $cedula);
        $data['cedula'] = $cedula;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Cédula';
        $data['title'] = TITLE . ' - Editar Cédula';

        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';

        $data['adjuntos_view'] = $this->load->view('notificaciones/cedulas/cedulas_adjuntos', $data, TRUE);

        $this->load_template('notificaciones/cedulas/cedulas_editar', $data);
    }


    /**
     * Asigna el notificador
     */
    public function asignar($id = NULL)
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
        }


        $cedula = $this->Cedulas_model->get(array('id' => $id));
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }

        if (!$this->Cedulas_model->tiene_estado($cedula->id, [Cedulas_estados_model::SOLICITUD_ACEPTADA, Cedulas_estados_model::NOTIFICADOR_ASIGNADO, Cedulas_estados_model::CEDULA_IMPRESA])) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        }

        $this->set_model_validation_rules($this->Cedulas_model);
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE) {


                $this->db->trans_begin();
                $trans_ok = TRUE;

                $notificador_suplente_id = ($this->input->post('notificador_suplente_id') == 0) ? '' : $this->input->post('notificador_suplente_id');

                /// Edita Cedula
                $trans_ok &= $this->Cedulas_model->update(array(
                    'id' => $this->input->post('id'),
                    'zona_id' => $this->input->post('zona_id'),
                    'notificador_id' => $this->input->post('notificador_id'),
                    'notificador_suplente_id' => $notificador_suplente_id,
                    'estado_id' => Cedulas_estados_model::NOTIFICADOR_ASIGNADO,
                ), FALSE);

                $cedula_id = $id;

                /// Crear Movimiento de la cedula
                $trans_ok &= $this->Cedulas_movimientos_model->add_movimiento($cedula_id, Cedulas_estados_model::NOTIFICADOR_ASIGNADO);


                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
                    redirect("notificaciones/cedulas/ver/$id", 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Cedulas_model->get_error()) {
                        $error_msg .= $this->Cedulas_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        // Relacionados
        $data['destinatario'] = $this->Destinatarios_model->get_one($cedula->destinatario_id);
        $data['domicilio'] = $this->Domicilios_model->get_one($cedula->domicilio_id);
        $data['estado'] = $this->Cedulas_estados_model->get_one($cedula->estado_id);
        $data['oficina'] = $this->Areas_model->get_one($cedula->oficina_id);
        $data['tipo_documento'] = $this->Tipos_documentos_model->get_one($cedula->tipo_doc_id);

        // Movimientos
        $data['movimientos'] = $this->Cedulas_movimientos_model->get_movimientos($cedula->id);
        $data['movimiento_actual'] = $this->Cedulas_movimientos_model->get_last_movimiento($cedula->id);


        $data['notificadores'] = $this->Cedulas_model->list_notificadores();
        $data['zonas'] = $this->Zonas_model->get();


        $data['fields'] = $this->build_fields($this->Cedulas_model->fields, $cedula, TRUE);
        $data['cedula'] = $cedula;
        $data['txt_btn'] = 'Asignar';
        $data['title_view'] = 'Asignar Notificador';
        $data['title'] = TITLE . ' - Asignar Notificador';

        $this->load_template('notificaciones/cedulas/cedulas_asignar_notificador_tab', $data);
    }

    /**
     * Cambiar el estado el estado de la cedula cuando los datos son correctos
     * @param null $id
     */

    public function aceptar($id = NULL)
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
        }


        $cedula = $this->Cedulas_model->get(array('id' => $id));
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }

        if (!$this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::SOLICITUD_REALIZADA)) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        }

        $error_msg = FALSE;

        $this->db->trans_begin();
        $trans_ok = TRUE;

        $trans_ok &= $this->Cedulas_model->update(array(
            'id' => $cedula->id,
            'estado_id' => Cedulas_estados_model::SOLICITUD_ACEPTADA,
        ), FALSE);


        $trans_ok &= $this->Cedulas_movimientos_model->add_movimiento($cedula->id, Cedulas_estados_model::SOLICITUD_ACEPTADA);

        if ($this->db->trans_status() && $trans_ok) {
            $this->db->trans_commit();
            $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
        } else {
            $this->db->trans_rollback();
            $error_msg = '<br />Se ha producido un error con la base de datos.';
            if ($this->Cedulas_model->get_error()) {
                $error_msg .= $this->Cedulas_model->get_error();
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->ver($id);
    }


    /**
     * Setear estado en la cedula cuando los datos introducidos son Erroneos.
     * @param null $id
     */

    public function erronea($id = NULL)
    {
        if (!in_groups($this->grupos_notificaciones, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
        }

        $cedula = $this->Cedulas_model->get(array('id' => $id));
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }

        if (!$this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::SOLICITUD_REALIZADA)) {
            show_error('No se puede editar la Cédula', 500, 'Estado incorrecto');
        }

        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id') || !in_array($this->input->post('tipo_devolucion_id'), [1, 2, 'other_reason'])) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            switch ($this->input->post('tipo_devolucion_id')) {
                case 1:
                    $observaciones = "Destinatario Erroneo";
                    break;
                case 2:
                    $observaciones = "Domicilio Inexistente";
                    break;
                case 'other_reason':
                    $observaciones = $this->input->post('otro_motivo_devolucion');
                    break;
            }

            $error_msg = FALSE;

            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Cedulas_model->update(array(
                'id' => $cedula->id,
                'estado_id' => Cedulas_estados_model::DATOS_INCORRECTOS,
            ), FALSE);


            $trans_ok &= $this->Cedulas_movimientos_model->add_movimiento($cedula->id, Cedulas_estados_model::DATOS_INCORRECTOS, $observaciones);

            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
                redirect('notificaciones/cedulas/ver/' . $cedula->id, 'refresh');
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
        $data['destinatario'] = $this->Destinatarios_model->get_one($cedula->destinatario_id);

        $data['cedula'] = $cedula;
        $data['txt_btn'] = 'Erronea';
        $data['title_view'] = 'Cargar Motivo Cedula Erronea';
        $data['title'] = TITLE . ' - Cargar Motivo Cedula Erronea';
        $this->load_template('notificaciones/cedulas/cedulas_erronea', $data);


    }


    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos)) {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
        }

        $cedula = $this->Cedulas_model->get_one($id);
        if (empty($cedula)) {
            show_error('No se encontró el Cédula', 500, 'Registro no encontrado');
        }

        if (!in_groups($this->grupos_admin, $this->grupos)) {
            if (!$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $cedula->oficina_id)) {
                show_error('No tiene permisos para eliminar la cedula', 500, 'Acción no autorizada');
            }
        }

        if (!$this->Cedulas_model->tiene_estado($cedula->id, Cedulas_estados_model::SOLICITUD_REALIZADA)) {
            show_error('No se puede eliminar la Cédula', 500, 'Estado incorrecto');
        }

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST)) {
            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;

            $last_movimiento_id = $this->Cedulas_movimientos_model->get_last_movimiento($cedula->id)->id;
            $trans_ok &= $this->Cedulas_movimientos_model->delete(array('id' => $last_movimiento_id), FALSE);
            $trans_ok &= $this->Cedulas_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok) {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Cedulas_model->get_msg());
                redirect('notificaciones/cedulas/listar', 'refresh');
            } else {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Cedulas_model->get_error()) {
                    $error_msg .= $this->Cedulas_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Cedulas_model->fields, $cedula, TRUE);
        $data['cedula'] = $cedula;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Cédula';
        $data['title'] = TITLE . ' - Eliminar Cédula';
        $this->load_template('notificaciones/cedulas/cedulas_abm', $data);
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
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
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
                redirect("notificaciones/cedulas/ver/$id", 'refresh');
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
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
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
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
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
        $data['oficina'] = $this->Areas_model->get(['id' => $cedula->oficina_id]);
        $data['tipo_cedula'] = $this->Tipos_documentos_model->get_one($cedula->tipo_doc_id);
        $data['destinatario'] = $this->Destinatarios_model->get_one($cedula->destinatario_id);
        $data['domicilio'] = $this->Domicilios_model->get_one($cedula->domicilio_id);

        // $this->load->view('notificaciones/cedulas/cedulas_pdf', $data, TRUE);

        $data['css'] = 'vendor/bootstrap/css/bootstrap.min.css';
        $data['css'] = 'css/notificaciones/impresion.css';
        $data['title'] = TITLE . ' - Vista previa Cédula';

        $this->load_template('notificaciones/cedulas/cedulas_pdf_impresion', $data);
    }


    private function descargar_pdf_cedula($cedula)
    {
//        $data['mes_planilla'] = ucfirst(strftime("%B %Y", strtotime($fecha)));
        $data['cedula'] = $cedula;
        $data['oficina'] = $this->Areas_model->get(['id' => $cedula->oficina_id]);
        $data['tipo_cedula'] = $this->Tipos_documentos_model->get_one($cedula->tipo_doc_id);
        $data['destinatario'] = $this->Destinatarios_model->get_one($cedula->destinatario_id);
        $data['domicilio'] = $this->Domicilios_model->get_one($cedula->domicilio_id);

        $html = $this->load->view('notificaciones/cedulas/cedulas_pdf_impresion', $data, TRUE);

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
            redirect("notificaciones/cedulas/ver/$id", 'refresh');
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
                redirect('notificaciones/cedulas/listar', 'refresh');
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
        $this->load_template('notificaciones/cedulas/cedulas_entrega', $data);


    }

    public function buscar()
    {
        $tipo = $this->input->post('tipo');
        $legajo = $this->input->post('legajo');
        $data['destinatario'] = array();
        try {
            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            $http_response_empleado = $guzzleHttp->request('GET', "personas/tabla_persona", ['query' => ['tper_Codigo' => $tipo, 'pers_Numero' => $legajo]]);
            $persona = json_decode($http_response_empleado->getBody()->getContents());

            $data['destinatario'] = array(
                'pers_Nombre' => $persona->pers_Nombre,
                'pers_Apellido' => $persona->pers_Apellido,
                'pers_Calle' => $persona->pers_Calle,
                'pers_Altura' => $persona->pers_Altura,
                'pers_Localidad' => $persona->pers_Localidad,
                'pers_CodigoPostal' => $persona->pers_CodigoPostal + 0,
            );

        } catch (Exception $e) {
            $data['error'] = $e;
            $persona = NULL;
        }
        echo json_encode($data);
    }


    public function get_usuario_name($id)
    {
        return $this->get_array('Usuarios', 'usuario', 'id', array(
                'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', username, ')') as usuario",
                'join' => array(
                    array('personas', 'personas.id = users.persona_id', 'LEFT'),
                ),
                'where' => array(
                    array('column' => 'users.id', 'value' => $id),
                ),
                'sort_by' => 'personas.apellido, personas.nombre, username'
            )
        );
    }

    /**
     * Calcula la fecha probable de entrega de una cedula
     * @param DateTime $fecha
     * @param int $desplazamiento
     */
    private function _calcular_fecha_entrega(DateTime $fecha, $desplazamiento = 0)
    {
        $fecha_entrega = $fecha->modify('+' . $desplazamiento . ' day');

        if ($fecha_entrega->format("w") == 6) {
            $fecha_entrega = $fecha->modify('+2 day'); // Si es sabado agrego 2 dias mas
        } elseif ($fecha_entrega->format("w") == 0) {
            $fecha_entrega = $fecha->modify('+1 day'); // Si es domingo agrego 1 dias mas
        }

        return $fecha_entrega;
    }

    private function _get_cedulas_vencidas()
    {
        $cedulas = $this->Cedulas_model->load_cedulas();
        $cedulas_vencidas = array();
        $cant = 0;
        // dd($cedulas);
        foreach ($cedulas as $cedula) {


            if (is_null($cedula->fecha_delete)) { // sola las cedulas que no estan eliminadas
                if (!in_array($cedula->estado_id, [4, 5, 6, 7, 8])) { //VER ESTADOS que no sean entregados o finalizados o anuladas

                    if (is_null($cedula->fecha_probable_entrega)) { // si la fecha probable de entrega es nula, lo agregago
                        array_push($cedulas_vencidas, $cedula);
                        $cant++;
                    } else { //calculo de fecha
                        $datetime2 = date_create($cedula->fecha_probable_entrega);
                        $cant_diff_dias = date_diff(new DateTime(), $datetime2)->format("%a");
                        if ($cedula->prioridad <= $cant_diff_dias) {
                            array_push($cedulas_vencidas, $cedula);
                        }
                    }
                }
            }
        }

        return $cedulas_vencidas;
    }
}