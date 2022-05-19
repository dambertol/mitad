<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Incidencias extends MY_Controller
{

    /**
     * Controlador de Incidencias
     * Autor: Leandro
     * Creado: 17/12/2019
     * Modificado: 04/11/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('reclamos_major/Adjuntos_model');
        $this->load->model('reclamos_major/Incidencias_model');
        $this->load->model('reclamos_major/Observaciones_incidencias_model');
        $this->load->model('Areas_model');
        $this->load->model('reclamos_major/Categorias_model');
        $this->load->model('Usuarios_model');
        $this->grupos_permitidos = array('admin', 'reclamos_major_admin', 'reclamos_major_consulta_general');
        $this->grupos_solo_consulta = array('reclamos_major_consulta_general');
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
                array('label' => 'N°', 'data' => 'numero', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 7, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Área', 'data' => 'area', 'width' => 12),
                array('label' => 'Contacto', 'data' => 'contacto', 'width' => 7),
                array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 7),
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 8),
                array('label' => 'Título', 'data' => 'titulo', 'width' => 10),
                array('label' => 'Detalle', 'data' => 'detalle', 'width' => 16),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 7),
                array('label' => 'Finalización', 'data' => 'fecha_finalizacion', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            ),
            'table_id' => 'incidencias_table',
            'source_url' => 'reclamos_major/incidencias/listar_data',
            'order' => array(array(1, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_incidencias_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_estados'] = array('' => 'Todos', 'Anulada' => 'Anulada', 'Cerrada' => 'Cerrada', 'Solucionada' => 'Solucionada', 'En Proceso' => 'En Proceso', 'Pendiente' => 'Pendiente');
        $data['add_url'] = 'agregar';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Incidencias';
        $data['title'] = TITLE . ' - Incidencias';
        $this->load_template('reclamos_major/incidencias/incidencias_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('reclamos_major/datatables_functions_helper');
        $this->datatables
                ->select("rm_incidencias.id, rm_incidencias.numero, rm_incidencias.fecha_inicio, CONCAT(areas.codigo, ' - ', areas.nombre) as area, rm_incidencias.contacto, rm_incidencias.telefono, rm_categorias.descripcion as categoria, rm_incidencias.titulo, rm_incidencias.detalle, rm_incidencias.estado, rm_incidencias.fecha_finalizacion, (SELECT COUNT(id) FROM rm_adjuntos WHERE rm_adjuntos.incidencia_id = rm_incidencias.id) as cant_adjuntos")
                ->from('rm_incidencias')
                ->join('areas', 'areas.id = rm_incidencias.area_id', 'left')
                ->join('rm_categorias', 'rm_categorias.id = rm_incidencias.categoria_id', 'left')
                ->edit_column('numero', '$1', 'dt_column_incidencias_numero(numero, cant_adjuntos)', TRUE)
                ->edit_column('estado', '$1', 'dt_column_incidencias_estado(estado)', TRUE)
                ->add_column('ver', '<a href="reclamos_major/incidencias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_incidencias_editar(estado, id)')
                ->add_column('anular', '$1', 'dt_column_incidencias_anular(estado, id)');

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
            redirect('reclamos_major/incidencias/listar', 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $this->array_categoria_control = $array_categoria = $this->get_array('Categorias');

        $this->set_model_validation_rules($this->Incidencias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Incidencias_model->create(array(
                'numero' => $this->input->post('numero'),
                'area_id' => $this->input->post('area'),
                'fecha_inicio' => $this->get_date_sql('fecha_inicio'),
                'contacto' => $this->input->post('contacto'),
                'telefono' => $this->input->post('telefono'),
                'categoria_id' => $this->input->post('categoria'),
                'titulo' => $this->input->post('titulo'),
                'detalle' => $this->input->post('detalle'),
                'estado' => 'Pendiente',
                'user_id' => $this->session->userdata('user_id')), FALSE);

            $incidencia_id = $this->Incidencias_model->get_row_id();

            $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
            if (!empty($adjuntos_agregar_post))
            {
                foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));

                    if (!empty($adjunto) && empty($adjunto->incidencia_id))
                    {
                        $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                        if (file_exists($viejo_archivo))
                        {
                            $nueva_ruta = "uploads/reclamos_major/incidencias/" . str_pad($incidencia_id, 6, "0", STR_PAD_LEFT) . "/";
                            if (!file_exists($nueva_ruta))
                            {
                                mkdir($nueva_ruta, 0755, TRUE);
                            }
                            $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                            $trans_ok &= $this->Adjuntos_model->update(array(
                                'id' => $Adjunto_id,
                                'nombre' => $nuevo_nombre,
                                'ruta' => $nueva_ruta,
                                'incidencia_id' => $incidencia_id
                                    ), FALSE);
                            $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                            if (!$renombrado)
                            {
                                $trans_ok = FALSE;
                            }
                        }
                        else
                        {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Se ha producido un error con los adjuntos.';
                        }
                    }
                    else
                    {
                        $trans_ok = FALSE;
                        $error_msg = '<br />Se ha producido un error con los adjuntos.';
                    }
                }
            }

            $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
            if (!empty($adjuntos_eliminar_post))
            {
                foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));

                    if (!empty($adjunto) && empty($adjunto->incidencia_id))
                    {
                        $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                        if (file_exists($viejo_archivo))
                        {
                            $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                            $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                            if (!$borrado)
                            {
                                $trans_ok = FALSE;
                            }
                        }
                    }
                }
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Incidencias_model->get_msg());
                redirect('reclamos_major/incidencias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Incidencias_model->get_error())
                {
                    $error_msg .= $this->Incidencias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->load->model('reclamos_major/Adjuntos_model');
        $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
        if (!empty($adjuntos_agregar_post))
        {
            $adjuntos_agregar_id = array();
            foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_agregar_id[] = $Adjunto_id;
            }

            $adjuntos_agregar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'rm_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                )
            ));

            $array_adjuntos_agregar = array();
            if (!empty($adjuntos_agregar))
            {
                foreach ($adjuntos_agregar as $Adjunto)
                {
                    $array_adjuntos_agregar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_agregar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_agregar'] = $array_adjuntos_agregar;
        }

        $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
        if (!empty($adjuntos_eliminar_post))
        {
            $adjuntos_eliminar_id = array();
            foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_eliminar_id[] = $Adjunto_id;
            }

            $adjuntos_eliminar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'rm_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                )
            ));

            $array_adjuntos_eliminar = array();
            if (!empty($adjuntos_eliminar))
            {
                foreach ($adjuntos_eliminar as $Adjunto)
                {
                    $array_adjuntos_eliminar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_eliminar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_eliminar'] = $array_adjuntos_eliminar;
        }

        $data['adjuntos_eliminar_existente_post'] = array();

        $this->Incidencias_model->fields['area']['array'] = $array_area;
        $this->Incidencias_model->fields['categoria']['array'] = $array_categoria;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields);
        $data['back_url'] = 'listar';
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Incidencia';
        $data['title'] = TITLE . ' - Agregar Incidencia';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('reclamos_major/incidencias/incidencias_abm', $data);
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
            redirect("reclamos_major/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }
        if ($incidencia->estado === 'Solucionada' || $incidencia->estado === 'Cerrada' || $incidencia->estado === 'Anulada')
        {
            redirect("reclamos_major/incidencias/ver/$id", 'refresh');
        }
        $incidencia->observacion = NULL;

        $this->load->model('reclamos_major/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('rm_tipos_adjuntos', 'rm_tipos_adjuntos.id = rm_adjuntos.tipo_id', 'LEFT', array('rm_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $this->array_categoria_control = $array_categoria = $this->get_array('Categorias');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));

        $this->Incidencias_model->fields['estado'] = array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'required' => TRUE);
        $this->Incidencias_model->fields['fecha_finalizacion'] = array('label' => 'Fecha Finalización', 'type' => 'datetime');
        $this->Incidencias_model->fields['observacion'] = array('label' => 'Observación', 'form_type' => 'textarea', 'rows' => 5);
        if ($incidencia->estado === 'Pendiente')
        {
            $this->array_estado_control = $array_estado = array('Pendiente' => 'Pendiente', 'En Proceso' => 'En Proceso', 'Solucionada' => 'Solucionada', 'Cerrada' => 'Cerrada');
        }
        else
        {
            $this->array_estado_control = $array_estado = array('En Proceso' => 'En Proceso', 'Solucionada' => 'Solucionada', 'Cerrada' => 'Cerrada');
        }

        $this->set_model_validation_rules($this->Incidencias_model);
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

                $datos_incidente['id'] = $this->input->post('id');
                $datos_incidente['numero'] = $this->input->post('numero');
                $datos_incidente['fecha_inicio'] = $this->get_date_sql('fecha_inicio');
                $datos_incidente['categoria_id'] = $this->input->post('categoria');
                $datos_incidente['estado'] = $this->input->post('estado');
                $datos_incidente['area_id'] = $this->input->post('area');
                $datos_incidente['contacto'] = $this->input->post('contacto');
                $datos_incidente['telefono'] = $this->input->post('telefono');
                $datos_incidente['titulo'] = $this->input->post('titulo');
                $datos_incidente['detalle'] = $this->input->post('detalle');
                $datos_incidente['tecnico_id'] = $this->input->post('tecnico');
                $datos_incidente['fecha_finalizacion'] = $this->get_datetime_sql('fecha_finalizacion');

                if ($this->input->post('estado') === 'Solucionada' || $this->input->post('estado') === 'Cerrada')
                {
                    $datos_incidente['resolucion'] = $this->input->post('observacion');
                    if (empty($datos_incidente['fecha_finalizacion']) || $datos_incidente['fecha_finalizacion'] === 'NULL')
                    {
                        $datos_incidente['fecha_finalizacion'] = date_format(new DateTime(), 'Y-m-d H:i:s');
                    }
                }

                $trans_ok &= $this->Incidencias_model->update($datos_incidente, FALSE);

                if ($this->input->post('area') != $incidencia->area_id)
                {
                    $area_ant = $array_area[$incidencia->area_id];
                    $area_nueva = $array_area[$this->input->post('area')];
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia area: $area_ant => $area_nueva",
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->get_date_sql('fecha_inicio') != $incidencia->fecha_inicio)
                {
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia fecha inicio: " . date_format(new DateTime($incidencia->fecha_inicio), 'd/m/Y') . " => " . $this->input->post('fecha_inicio'),
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->input->post('categoria') != $incidencia->categoria_id)
                {
                    $cat_ant = $array_categoria[$incidencia->categoria_id];
                    $cat_nueva = $array_categoria[$this->input->post('categoria')];
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia categoria: $cat_ant => $cat_nueva",
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->input->post('titulo') != $incidencia->titulo)
                {
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia titulo: $incidencia->titulo => " . $this->input->post('titulo'),
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->input->post('detalle') != $incidencia->detalle)
                {
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia detalle: $incidencia->detalle => " . $this->input->post('detalle'),
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->input->post('estado') != $incidencia->estado)
                {
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia estado: $incidencia->estado => " . $this->input->post('estado'),
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if (($this->input->post('estado') !== 'Solucionada' && $this->input->post('estado') !== 'Cerrada') && $this->input->post('observacion') !== '')
                {
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => $this->input->post('observacion'),
                        'user_id' => $this->session->userdata('user_id')
                            ), FALSE);
                }

                $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
                if (!empty($adjuntos_agregar_post))
                {
                    foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->incidencia_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/reclamos_major/incidencias/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'incidencia_id' => $id
                                        ), FALSE);
                                $renombrado = rename($viejo_archivo, $nueva_ruta . $nuevo_nombre);
                                if (!$renombrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                            else
                            {
                                $trans_ok = FALSE;
                                $error_msg = '<br />Se ha producido un error con los adjuntos.';
                            }
                        }
                        else
                        {
                            $trans_ok = FALSE;
                            $error_msg = '<br />Se ha producido un error con los adjuntos.';
                        }
                    }
                }

                $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
                if (!empty($adjuntos_eliminar_post))
                {
                    foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'usuario_subida' => $this->session->userdata('user_id')
                        ));

                        if (!empty($adjunto) && empty($adjunto->incidencia_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                                $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                                if (!$borrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                        }
                    }
                }

                $adjuntos_eliminar_existente_post = $this->input->post('adjunto_eliminar_existente');
                if (!empty($adjuntos_eliminar_existente_post))
                {
                    foreach ($adjuntos_eliminar_existente_post as $Adjunto_id => $Adjunto_name)
                    {
                        $adjunto = $this->Adjuntos_model->get(array(
                            'id' => $Adjunto_id,
                            'nombre' => $Adjunto_name,
                            'incidencia_id' => $this->input->post('id')
                        ));

                        if (!empty($adjunto))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $trans_ok &= $this->Adjuntos_model->delete(array('id' => $Adjunto_id), FALSE);
                                $borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok
                                if (!$borrado)
                                {
                                    $trans_ok = FALSE;
                                }
                            }
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Incidencias_model->get_msg());
                    redirect('reclamos_major/incidencias/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Incidencias_model->get_error())
                    {
                        $error_msg .= $this->Incidencias_model->get_error();
                    }
                    if ($this->Observaciones_incidencias_model->get_error())
                    {
                        $error_msg .= $this->Observaciones_incidencias_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->name = pathinfo($Adjunto->nombre)['filename'];
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $this->load->model('vales_combustible/Adjuntos_model');
        $adjuntos_agregar_post = $this->input->post('adjunto_agregar');
        if (!empty($adjuntos_agregar_post))
        {
            $adjuntos_agregar_id = array();
            foreach ($adjuntos_agregar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_agregar_id[] = $Adjunto_id;
            }

            $adjuntos_agregar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'rm_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('rm_tipos_adjuntos', 'rm_tipos_adjuntos.id = rm_adjuntos.tipo_id', 'LEFT', array('rm_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_agregar = array();
            if (!empty($adjuntos_agregar))
            {
                foreach ($adjuntos_agregar as $Adjunto)
                {
                    $array_adjuntos_agregar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_agregar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_agregar'] = $array_adjuntos_agregar;
        }

        $adjuntos_eliminar_post = $this->input->post('adjunto_eliminar');
        if (!empty($adjuntos_eliminar_post))
        {
            $adjuntos_eliminar_id = array();
            foreach ($adjuntos_eliminar_post as $Adjunto_id => $Adjunto_name)
            {
                $adjuntos_eliminar_id[] = $Adjunto_id;
            }

            $adjuntos_eliminar = $this->Adjuntos_model->get(array(
                'where' => array(
                    array('column' => 'rm_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('rm_tipos_adjuntos', 'rm_tipos_adjuntos.id = rm_adjuntos.tipo_id', 'LEFT', array('rm_tipos_adjuntos.nombre as tipo_adjunto'))
                )
            ));

            $array_adjuntos_eliminar = array();
            if (!empty($adjuntos_eliminar))
            {
                foreach ($adjuntos_eliminar as $Adjunto)
                {
                    $array_adjuntos_eliminar[$Adjunto->id] = $Adjunto;
                    $array_adjuntos_eliminar[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
                }
            }
            $data['array_adjuntos_eliminar'] = $array_adjuntos_eliminar;
        }

        if ($this->input->post('adjunto_eliminar_existente'))
        {
            $data['adjuntos_eliminar_existente_post'] = $this->input->post('adjunto_eliminar_existente');
        }
        else
        {
            $data['adjuntos_eliminar_existente_post'] = array();
        }

        $data['edita_adjuntos'] = TRUE;

        $this->Incidencias_model->fields['categoria']['array'] = $array_categoria;
        $this->Incidencias_model->fields['area']['array'] = $array_area;
        $this->Incidencias_model->fields['estado']['array'] = $array_estado;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields, $incidencia);
        $data['incidencia'] = $incidencia;

        $observaciones = $this->Observaciones_incidencias_model->get(array(
            'incidencia_id' => $incidencia->id,
            'join' => array(
                array('users', 'users.id = rm_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        $data['back_url'] = 'listar';
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Incidencia';
        $data['title'] = TITLE . ' - Editar Incidencia';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('reclamos_major/incidencias/incidencias_abm', $data);
    }

    public function anular($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("reclamos_major/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }
        if ($incidencia->estado === 'Solucionada' || $incidencia->estado === 'Cerrada' || $incidencia->estado === 'Anulada')
        {
            redirect("reclamos_major/incidencias/ver/$id", 'refresh');
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
            $trans_ok &= $this->Incidencias_model->update(array('id' => $this->input->post('id'), 'estado' => 'Anulada'), FALSE);
            $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                'incidencia_id' => $this->input->post('id'),
                'observacion' => "Anula incidencia",
                'user_id' => $this->session->userdata('user_id')
                    ), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Incidencias_model->get_msg());
                redirect('reclamos_major/incidencias/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Incidencias_model->get_error())
                {
                    $error_msg .= $this->Incidencias_model->get_error();
                }
                if ($this->Observaciones_incidencias_model->get_error())
                {
                    $error_msg .= $this->Observaciones_incidencias_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->load->model('reclamos_major/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('rm_tipos_adjuntos', 'rm_tipos_adjuntos.id = rm_adjuntos.tipo_id', 'LEFT', array('rm_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $data['adjuntos_eliminar_existente_post'] = array();

        $this->Incidencias_model->fields['estado'] = array('label' => 'Estado', 'required' => TRUE);
        $this->Incidencias_model->fields['user'] = array('label' => 'Usuario Carga', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE);
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields, $incidencia, TRUE);
        $data['incidencia'] = $incidencia;

        $observaciones = $this->Observaciones_incidencias_model->get(array(
            'incidencia_id' => $incidencia->id,
            'join' => array(
                array('users', 'users.id = rm_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        $data['back_url'] = 'listar';
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular Incidencia';
        $data['title'] = TITLE . ' - Anular Incidencia';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('reclamos_major/incidencias/incidencias_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }

        $this->load->model('reclamos_major/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('rm_tipos_adjuntos', 'rm_tipos_adjuntos.id = rm_adjuntos.tipo_id', 'LEFT', array('rm_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $array_adjuntos = array();
        if (!empty($adjuntos))
        {
            foreach ($adjuntos as $Adjunto)
            {
                $array_adjuntos[$Adjunto->id] = $Adjunto;
                $array_adjuntos[$Adjunto->id]->extension = pathinfo($Adjunto->nombre)['extension'];
            }
        }
        $data['array_adjuntos'] = $array_adjuntos;

        $data['adjuntos_eliminar_existente_post'] = array();

        $this->Incidencias_model->fields['estado'] = array('label' => 'Estado', 'required' => TRUE);
        $this->Incidencias_model->fields['resolucion'] = array('label' => 'Resolución', 'form_type' => 'textarea', 'rows' => 5);
        $this->Incidencias_model->fields['user'] = array('label' => 'Usuario Carga', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE);
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields, $incidencia, TRUE);
        $data['incidencia'] = $incidencia;

        $observaciones = $this->Observaciones_incidencias_model->get(array(
            'incidencia_id' => $incidencia->id,
            'join' => array(
                array('users', 'users.id = rm_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        if (in_groups($this->grupos_permitidos, $this->grupos))
        {
            $data['back_url'] = 'listar';
        }
        else
        {
            $data['back_url'] = 'listar_tecnico';
        }
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Incidencia';
        $data['title'] = TITLE . ' - Ver Incidencia';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('reclamos_major/incidencias/incidencias_abm', $data);
    }

    public function finalizar($id = NULL)
    {
        if (!in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("reclamos_major/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get(array('id' => $id));
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }

        if ($incidencia->estado === 'Solucionada' || $incidencia->estado === 'Cerrada' || $incidencia->estado === 'Anulada')
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }

        $this->db->trans_begin();
        $trans_ok = TRUE;
        $trans_ok &= $this->Incidencias_model->update(array(
            'id' => $incidencia->id,
            'estado' => 'Solucionada',
            'fecha_finalizacion' => date_format(new DateTime(), 'Y-m-d H:i'),
            'tecnico_id' => $this->session->userdata('user_id')), FALSE);

        if ($this->db->trans_status() && $trans_ok)
        {
            $this->db->trans_commit();
            $this->session->set_flashdata('message', "<br />Incidencia $incidencia->id finalizada");
        }
        else
        {
            $this->db->trans_rollback();
            if ($this->Incidencias_model->get_error())
            {
                $this->session->set_flashdata('error', $this->Incidencias_model->get_error());
            }
        }

        redirect('reclamos_major/incidencias/listar_tecnico', 'refresh');
    }
}
