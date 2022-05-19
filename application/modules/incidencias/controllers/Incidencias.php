<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Incidencias extends MY_Controller
{

    /**
     * Controlador de Incidencias
     * Autor: Leandro
     * Creado: 12/04/2019
     * Modificado: 14/04/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('incidencias/Adjuntos_model');
        $this->load->model('incidencias/Incidencias_model');
        $this->load->model('incidencias/Observaciones_incidencias_model');
        $this->load->model('Areas_model');
        $this->load->model('incidencias/Sectores_model');
        $this->load->model('incidencias/Categorias_model');
        $this->load->model('Usuarios_model');
        $this->load->model('incidencias/Usuarios_areas_model');
        $this->load->model('incidencias/Usuarios_sectores_model');
        $this->grupos_admin = array('admin', 'incidencias_admin', 'incidencias_consulta_general');
        $this->grupos_tecnico = array('incidencias_user');
        $this->grupos_area = array('incidencias_area');
        $this->grupos_permitidos = array('admin', 'incidencias_admin', 'incidencias_user', 'incidencias_area', 'incidencias_consulta_general');
        $this->grupos_solo_consulta = array('incidencias_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Área', 'data' => 'area', 'width' => 12),
                array('label' => 'Sector', 'data' => 'sector', 'width' => 8),
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 10),
                array('label' => 'Detalle', 'data' => 'detalle', 'width' => 20),
                array('label' => 'Técnico', 'data' => 'tecnico', 'width' => 10),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 7),
                array('label' => 'Finalización', 'data' => 'fecha_finalizacion', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'repetir', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'incidencias_table',
            'source_url' => 'incidencias/incidencias/listar_data',
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
        $this->load_template('incidencias/incidencias/incidencias_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('incidencias/datatables_functions_helper');
        $this->datatables
                ->select("in_incidencias.id, in_incidencias.fecha_inicio, CONCAT(areas.codigo, ' - ', areas.nombre) as area, in_sectores.descripcion as sector, in_categorias.descripcion as categoria, in_incidencias.detalle, CONCAT(personas.apellido, ', ', personas.nombre) as tecnico, in_incidencias.estado, in_incidencias.fecha_finalizacion, in_categorias.sector_id as incidente_sector_id, (SELECT COUNT(id) FROM in_adjuntos WHERE in_adjuntos.incidencia_id = in_incidencias.id) as cant_adjuntos")
                ->from('in_incidencias')
                ->join('areas', 'areas.id = in_incidencias.area_id', 'left')
                ->join('in_categorias', 'in_categorias.id = in_incidencias.categoria_id', 'left')
                ->join('in_sectores', 'in_sectores.id = in_categorias.sector_id', 'left')
                ->join('users', 'users.id = in_incidencias.tecnico_id', 'left')
                ->join('personas', 'personas.id = users.persona_id', 'left')
                ->edit_column('id', '$1', 'dt_column_incidencias_numero(id, cant_adjuntos)', TRUE)
                ->edit_column('estado', '$1', 'dt_column_incidencias_estado(estado)', TRUE)
                ->add_column('ver', '<a href="incidencias/incidencias/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_incidencias_editar(estado, id)')
                ->add_column('anular', '$1', 'dt_column_incidencias_anular(estado, id)')
                ->add_column('repetir', '<a href="#" onclick="duplicar_incidencia($1);return false;" title="Repetir" class="btn btn-primary btn-xs"><i class="fa fa-repeat"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function listar_tecnico()
    {
        if (!in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $usuario_sector = $this->Usuarios_sectores_model->get(array('user_id' => $this->session->userdata('user_id')));
        if (empty($usuario_sector))
        {
            show_error('No se encontró asignación a Sector', 500, 'Registro no encontrado');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Área', 'data' => 'area', 'width' => 12),
                array('label' => 'Sector', 'data' => 'sector', 'width' => 10),
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 10),
                array('label' => 'Detalle', 'data' => 'detalle', 'width' => 21),
                array('label' => 'Técnico', 'data' => 'tecnico', 'width' => 10),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 7),
                array('label' => 'Finalización', 'data' => 'fecha_finalizacion', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'finalizar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'incidencias_table',
            'source_url' => 'incidencias/incidencias/listar_tecnico_data',
            'order' => array(array(1, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_incidencias_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_estados'] = array('' => 'Todos', 'Anulada' => 'Anulada', 'Cerrada' => 'Cerrada', 'Solucionada' => 'Solucionada', 'En Proceso' => 'En Proceso', 'Pendiente' => 'Pendiente');
        $data['add_url'] = 'agregar_tecnico';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Incidencias';
        $data['title'] = TITLE . ' - Incidencias';
        $this->load_template('incidencias/incidencias/incidencias_listar', $data);
    }

    public function listar_tecnico_data()
    {
        if (!in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $sectores_id = array();
        $usuario_sectores = $this->Usuarios_sectores_model->get(array('user_id' => $this->session->userdata('user_id')));
        if (!empty($usuario_sectores))
        {
            foreach ($usuario_sectores as $Usuario_sector)
            {
                $sectores_id[] = $Usuario_sector->sector_id;
            }
        }

        $this->load->helper('incidencias/datatables_functions_helper');
        $this->datatables
                ->select("in_incidencias.id, in_incidencias.fecha_inicio, CONCAT(areas.codigo, ' - ', areas.nombre) as area, in_sectores.descripcion as sector, in_categorias.descripcion as categoria, in_incidencias.detalle, CONCAT(personas.apellido, ', ', personas.nombre) as tecnico, in_incidencias.estado, in_incidencias.fecha_finalizacion, in_categorias.sector_id as incidente_sector_id, (SELECT COUNT(id) FROM in_adjuntos WHERE in_adjuntos.incidencia_id = in_incidencias.id) as cant_adjuntos")
                ->from('in_incidencias')
                ->join('areas', 'areas.id = in_incidencias.area_id', 'left')
                ->join('in_categorias', 'in_categorias.id = in_incidencias.categoria_id', 'left')
                ->join('in_sectores', 'in_sectores.id = in_categorias.sector_id', 'left')
                ->join('users', 'users.id = in_incidencias.tecnico_id', 'left')
                ->join('personas', 'personas.id = users.persona_id', 'left')
                ->where_in('(in_categorias.sector_id', $sectores_id, FALSE)
                ->or_where("in_incidencias.tecnico_id = {$this->session->userdata('user_id')})", NULL, FALSE)
                ->edit_column('id', '$1', 'dt_column_incidencias_numero(id, cant_adjuntos)', TRUE)
                ->edit_column('estado', '$1', 'dt_column_incidencias_estado(estado)', TRUE)
                ->add_column('ver', '<a href="incidencias/incidencias/ver_tecnico/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_incidencias_editar(estado, id, "editar_tecnico")')
                ->add_column('finalizar', '$1', 'dt_column_incidencias_finalizar(estado, id)');

        echo $this->datatables->generate();
    }

    public function listar_area()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'N°', 'data' => 'id', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'Inicio', 'data' => 'fecha_inicio', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Área', 'data' => 'area', 'width' => 12),
                array('label' => 'Sector', 'data' => 'sector', 'width' => 10),
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 10),
                array('label' => 'Detalle', 'data' => 'detalle', 'width' => 21),
                array('label' => 'Técnico', 'data' => 'tecnico', 'width' => 10),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 7),
                array('label' => 'Finalización', 'data' => 'fecha_finalizacion', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'incidencias_table',
            'source_url' => 'incidencias/incidencias/listar_area_data',
            'order' => array(array(1, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_incidencias_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_estados'] = array('' => 'Todos', 'Anulada' => 'Anulada', 'Cerrada' => 'Cerrada', 'Solucionada' => 'Solucionada', 'En Proceso' => 'En Proceso', 'Pendiente' => 'Pendiente');
        $data['add_url'] = 'agregar_area';
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Incidencias';
        $data['title'] = TITLE . ' - Incidencias';
        $this->load_template('incidencias/incidencias/incidencias_listar', $data);
    }

    public function listar_area_data()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('incidencias/datatables_functions_helper');
        $this->datatables
                ->select("in_incidencias.id, in_incidencias.fecha_inicio, CONCAT(areas.codigo, ' - ', areas.nombre) as area, in_sectores.descripcion as sector, in_categorias.descripcion as categoria, in_incidencias.detalle, CONCAT(personas.apellido, ', ', personas.nombre) as tecnico, in_incidencias.estado, in_incidencias.fecha_finalizacion, in_categorias.sector_id as incidente_sector_id, (SELECT COUNT(id) FROM in_adjuntos WHERE in_adjuntos.incidencia_id = in_incidencias.id) as cant_adjuntos")
                ->from('in_incidencias')
                ->join('areas', 'areas.id = in_incidencias.area_id', 'left')
                ->join('in_categorias', 'in_categorias.id = in_incidencias.categoria_id', 'left')
                ->join('in_sectores', 'in_sectores.id = in_categorias.sector_id', 'left')
                ->join('users', 'users.id = in_incidencias.tecnico_id', 'left')
                ->join('personas', 'personas.id = users.persona_id', 'left')
                ->join('in_usuarios_areas', 'in_usuarios_areas.area_id = areas.id ', 'left')
                ->where('in_usuarios_areas.user_id', $this->session->userdata('user_id'))
                ->edit_column('id', '$1', 'dt_column_incidencias_numero(id, cant_adjuntos)', TRUE)
                ->edit_column('estado', '$1', 'dt_column_incidencias_estado(estado)', TRUE)
                ->add_column('ver', '<a href="incidencias/incidencias/ver_area/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '$1', 'dt_column_incidencias_area_editar(estado, id)')
                ->add_column('anular', '$1', 'dt_column_incidencias_area_anular(estado, id)');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('incidencias/incidencias/listar', 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        if ($this->input->post('sector'))
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $this->input->post('sector')))
                    )
            );
        }
        else
        {
            $this->array_categoria_control = $array_categoria = array();
        }
        $this->array_tecnico_control = $array_tecnico = $this->get_array('Usuarios', 'usuario', 'id', array(
            'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario",
            'join' => array(
                array('personas', 'personas.id = users.persona_id', 'LEFT'),
                array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                array('groups', 'users_groups.group_id = groups.id', 'LEFT')
            ),
            'where' => array(
                array('column' => 'groups.name IN', 'value' => "('admin', 'incidencias_user', 'incidencias_admin')", 'override' => TRUE),
                array('column' => 'users.active', 'value' => '1')
            ),
            'group_by' => 'id, usuario',
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array(NULL => '-- Sin Técnico Asignado --')
        );
        $this->set_model_validation_rules($this->Incidencias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Incidencias_model->create(array(
                'area_id' => $this->input->post('area'),
                'fecha_inicio' => $this->get_datetime_sql('fecha_inicio'),
                'contacto' => $this->input->post('contacto'),
                'telefono' => $this->input->post('telefono'),
                'categoria_id' => $this->input->post('categoria'),
                'detalle' => $this->input->post('detalle'),
                'estado' => 'Pendiente',
                'tecnico_id' => $this->input->post('tecnico'),
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
                            $nueva_ruta = "uploads/incidencias/incidencias/" . str_pad($incidencia_id, 6, "0", STR_PAD_LEFT) . "/";
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
                redirect('incidencias/incidencias/listar', 'refresh');
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

        $this->load->model('incidencias/Adjuntos_model');
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
                    array('column' => 'in_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
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
                    array('column' => 'in_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
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
        $this->Incidencias_model->fields['sector']['array'] = $array_sector;
        $this->Incidencias_model->fields['tecnico']['array'] = $array_tecnico;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields);
        $data['buscar_tecnico'] = TRUE;
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
        $data['js'][] = 'js/incidencias/base.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function agregar_tecnico()
    {
        if (!in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('incidencias/incidencias/listar', 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        if ($this->input->post('sector'))
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $this->input->post('sector')))
                    )
            );
        }
        else
        {
            $this->array_categoria_control = $array_categoria = array();
        }
        unset($this->Incidencias_model->fields['tecnico']);
        $this->set_model_validation_rules($this->Incidencias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Incidencias_model->create(array(
                'area_id' => $this->input->post('area'),
                'fecha_inicio' => $this->get_datetime_sql('fecha_inicio'),
                'contacto' => $this->input->post('contacto'),
                'telefono' => $this->input->post('telefono'),
                'categoria_id' => $this->input->post('categoria'),
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
                            $nueva_ruta = "uploads/incidencias/incidencias/" . str_pad($incidencia_id, 6, "0", STR_PAD_LEFT) . "/";
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
                redirect('incidencias/incidencias/listar_tecnico', 'refresh');
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
        $this->Incidencias_model->fields['area']['array'] = $array_area;
        $this->Incidencias_model->fields['sector']['array'] = $array_sector;
        $this->Incidencias_model->fields['categoria']['array'] = $array_categoria;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields);

        $data['buscar_tecnico'] = FALSE;
        $data['back_url'] = 'listar_tecnico';
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Incidencia';
        $data['title'] = TITLE . ' - Agregar Incidencia';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $data['js'][] = 'js/incidencias/base.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function agregar_area()
    {
        if (!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_tecnico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('incidencias/incidencias/listar', 'refresh');
        }

        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('areas.id', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'join' => array(array('in_usuarios_areas', 'in_usuarios_areas.area_id = areas.id', 'LEFT')),
            'where' => array("nombre <> '-'", "in_usuarios_areas.user_id = " . $this->session->userdata('user_id')),
            'sort_by' => 'codigo')
        );
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        if ($this->input->post('sector'))
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $this->input->post('sector')))
                    )
            );
        }
        else
        {
            $this->array_categoria_control = $array_categoria = array();
        }
        unset($this->Incidencias_model->fields['fecha_inicio']);
        unset($this->Incidencias_model->fields['tecnico']);
        $this->set_model_validation_rules($this->Incidencias_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Incidencias_model->create(array(
                'area_id' => $this->input->post('area'),
                'fecha_inicio' => date_format(new DateTime(), 'Y-m-d H:i'),
                'contacto' => $this->input->post('contacto'),
                'telefono' => $this->input->post('telefono'),
                'categoria_id' => $this->input->post('categoria'),
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
                            $nueva_ruta = "uploads/incidencias/incidencias/" . str_pad($incidencia_id, 6, "0", STR_PAD_LEFT) . "/";
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
                redirect('incidencias/incidencias/listar_area', 'refresh');
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
        $this->Incidencias_model->fields['area']['array'] = $array_area;
        $this->Incidencias_model->fields['sector']['array'] = $array_sector;
        $this->Incidencias_model->fields['categoria']['array'] = $array_categoria;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields);

        $data['buscar_tecnico'] = FALSE;
        $data['back_url'] = 'listar_area';
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Incidencia';
        $data['title'] = TITLE . ' - Agregar Incidencia';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $data['js'][] = 'js/incidencias/base.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }
        if ($incidencia->estado === 'Solucionada' || $incidencia->estado === 'Cerrada' || $incidencia->estado === 'Anulada')
        {
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }
        $incidencia->observacion = NULL;

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $array_todas_categoria = $this->get_array('Categorias'); // SOLO PARA OBSERVACION DEL CAMBIO
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        if ($this->input->post('sector'))
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $this->input->post('sector')))
                    )
            );
        }
        else
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $incidencia->sector_id))
                    )
            );
        }
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'));

        if (isset($_POST) && !empty($_POST))
        {
            $categoria = $this->Categorias_model->get(array('id' => $this->input->post('categoria')));
            if (empty($categoria))
            {
                show_error('No se encontró la Categoría', 500, 'Registro no encontrado');
            }
            $sector_tecnico = $categoria->sector_id;
        }
        else
        {
            $sector_tecnico = $incidencia->sector_id;
        }

        $this->array_tecnico_control = $array_tecnico = $this->get_array('Usuarios', 'usuario', 'id', array(
            'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario",
            'join' => array(
                array('personas', 'personas.id = users.persona_id', 'LEFT'),
                array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                array('groups', 'users_groups.group_id = groups.id', 'LEFT'),
                array('in_usuarios_sectores', 'in_usuarios_sectores.user_id = users.id', 'LEFT')
            ),
            'where' => array(
                "(groups.name IN ('admin', 'incidencias_user', 'incidencias_admin') AND users.active = 1 AND in_usuarios_sectores.sector_id = $sector_tecnico) OR users.id = $incidencia->tecnico_id"
            ),
            'group_by' => 'id, usuario',
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array(NULL => '-- Sin Técnico Asignado --')
        );

        $this->Incidencias_model->fields['estado'] = array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'required' => TRUE);
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
                $datos_incidente['fecha_inicio'] = $this->get_datetime_sql('fecha_inicio');
                $datos_incidente['categoria_id'] = $this->input->post('categoria');
                $datos_incidente['estado'] = $this->input->post('estado');

                if ($this->input->post('estado') === 'Solucionada' || $this->input->post('estado') === 'Cerrada')
                {
                    $datos_incidente['resolucion'] = $this->input->post('observacion');
                    $datos_incidente['fecha_finalizacion'] = date_format(new DateTime(), 'Y-m-d H:i');
                }

                $datos_incidente['area_id'] = $this->input->post('area');
                $datos_incidente['contacto'] = $this->input->post('contacto');
                $datos_incidente['telefono'] = $this->input->post('telefono');
                $datos_incidente['detalle'] = $this->input->post('detalle');
                $datos_incidente['tecnico_id'] = $this->input->post('tecnico');

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

                if ($this->get_datetime_sql('fecha_inicio') != $incidencia->fecha_inicio)
                {
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia fecha inicio: " . date_format(new DateTime($incidencia->fecha_inicio), 'd/m/Y H:i') . " => " . $this->input->post('fecha_inicio'),
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->input->post('categoria') != $incidencia->categoria_id)
                {
                    $cat_ant = $array_todas_categoria[$incidencia->categoria_id];
                    $cat_nueva = $array_todas_categoria[$this->input->post('categoria')];
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia categoria: $cat_ant => $cat_nueva",
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

                if ($this->input->post('tecnico') != $incidencia->tecnico_id)
                {
                    $tec_ant = $array_tecnico[$incidencia->tecnico_id];
                    $tec_nuevo = $array_tecnico[$this->input->post('tecnico')];
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia técnico: $tec_ant => $tec_nuevo",
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
                                $nueva_ruta = "uploads/incidencias/incidencias/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/";
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
                    redirect('incidencias/incidencias/listar', 'refresh');
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
                    array('column' => 'in_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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
                    array('column' => 'in_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $this->Incidencias_model->fields['sector']['array'] = $array_sector;
        $this->Incidencias_model->fields['categoria']['array'] = $array_categoria;
        $this->Incidencias_model->fields['area']['array'] = $array_area;
        $this->Incidencias_model->fields['tecnico']['array'] = $array_tecnico;
        $this->Incidencias_model->fields['estado']['array'] = $array_estado;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields, $incidencia);
        $data['incidencia'] = $incidencia;

        $observaciones = $this->Observaciones_incidencias_model->get(array(
            'incidencia_id' => $incidencia->id,
            'join' => array(
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        $data['buscar_tecnico'] = TRUE;
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
        $data['js'][] = 'js/incidencias/base.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function editar_tecnico($id = NULL)
    {
        if (!in_groups($this->grupos_tecnico, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }
        if ($incidencia->estado === 'Solucionada' || $incidencia->estado === 'Cerrada' || $incidencia->estado === 'Anulada')
        {
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }
        $incidencia->observacion = NULL;

        if (!$this->Usuarios_sectores_model->in_sector($this->session->userdata('user_id'), $incidencia->sector_id) &&
                $this->session->userdata('user_id') !== $incidencia->tecnico_id)
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $array_todas_categoria = $this->get_array('Categorias'); // SOLO PARA OBSERVACION DEL CAMBIO
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        if ($this->input->post('sector'))
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $this->input->post('sector')))
                    )
            );
        }
        else
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $incidencia->sector_id))
                    )
            );
        }
        $this->array_tecnico_control = $array_tecnico = $this->get_array('Usuarios', 'usuario', 'id', array(
            'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario",
            'join' => array(
                array('personas', 'personas.id = users.persona_id', 'LEFT'),
                array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                array('groups', 'users_groups.group_id = groups.id', 'LEFT'),
                array('in_usuarios_sectores', 'in_usuarios_sectores.user_id = users.id', 'LEFT')
            ),
            'where' => array(
                "(groups.name IN ('admin', 'incidencias_user', 'incidencias_admin') AND users.active = 1 AND in_usuarios_sectores.sector_id = $incidencia->sector_id) OR users.id = $incidencia->tecnico_id"
            ),
            'group_by' => 'id, usuario',
            'sort_by' => 'personas.apellido, personas.nombre'
                ), array(NULL => '-- Sin Técnico Asignado --')
        );
        unset($this->Incidencias_model->fields['area']['input_type']);
        unset($this->Incidencias_model->fields['area']['required']);
        $this->Incidencias_model->fields['area']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['tecnico']['input_type']);
        unset($this->Incidencias_model->fields['tecnico']['required']);
        $this->Incidencias_model->fields['tecnico']['disabled'] = TRUE;
        $this->Incidencias_model->fields['estado'] = array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'required' => TRUE);
        $this->Incidencias_model->fields['observacion'] = array('label' => 'Observación', 'form_type' => 'textarea', 'rows' => 5);
        if ($incidencia->estado === 'Pendiente')
        {
            $this->array_estado_control = $array_estado = array('Pendiente' => 'Pendiente', 'En Proceso' => 'En Proceso', 'Solucionada' => 'Solucionada', 'Cerrada' => 'Cerrada');
        }
        else
        {
            $this->array_estado_control = $array_estado = array('En Proceso' => 'En Proceso', 'Solucionada' => 'Solucionada', 'Cerrada' => 'Cerrada');
        }
        unset($this->Incidencias_model->fields['area']['input_type']);
        unset($this->Incidencias_model->fields['area']['required']);
        $this->Incidencias_model->fields['area']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['fecha_inicio']['required']);
        $this->Incidencias_model->fields['fecha_inicio']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['contacto']['required']);
        $this->Incidencias_model->fields['contacto']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['telefono']['required']);
        $this->Incidencias_model->fields['telefono']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['detalle']['required']);
        $this->Incidencias_model->fields['detalle']['disabled'] = TRUE;

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
                $datos_incidente['categoria_id'] = $this->input->post('categoria');
                $datos_incidente['estado'] = $this->input->post('estado');

                if ($this->input->post('estado') === 'Solucionada' || $this->input->post('estado') === 'Cerrada')
                {
                    $datos_incidente['resolucion'] = $this->input->post('observacion');
                    $datos_incidente['fecha_finalizacion'] = date_format(new DateTime(), 'Y-m-d H:i');
                }

                // SI CAMBIA A UNA CATEGORIA DE OTRO SECTOR LE QUITA EL TECNICO ASIGNADO
                $cate = $this->Categorias_model->get(array('id' => $this->input->post('categoria')));
                if (empty($cate))
                {
                    show_error('No se encontró la Categoría', 500, 'Registro no encontrado');
                }

                $usuarios_sector = $this->Usuarios_sectores_model->get(array('user_id' => $this->session->userdata('user_id')));
                if (empty($usuarios_sector))
                {
                    show_error('No se encontró el Usuario', 500, 'Registro no encontrado');
                }
                if ($cate->sector_id === $usuarios_sector[0]->sector_id)
                {
                    $datos_incidente['tecnico_id'] = $this->session->userdata('user_id');
                }
                else
                {
                    $datos_incidente['tecnico_id'] = NULL;
                }

                $trans_ok &= $this->Incidencias_model->update($datos_incidente, FALSE);

                if ($this->input->post('categoria') != $incidencia->categoria_id)
                {
                    $cat_ant = $array_todas_categoria[$incidencia->categoria_id];
                    $cat_nueva = $array_todas_categoria[$this->input->post('categoria')];
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia categoria: $cat_ant => $cat_nueva",
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($datos_incidente['tecnico_id'] != $incidencia->tecnico_id)
                {
                    $tec_ant = $array_tecnico[$incidencia->tecnico_id];
                    $tec_nuevo = $array_tecnico[$datos_incidente['tecnico_id']];
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia técnico: $tec_ant => $tec_nuevo",
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

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Incidencias_model->get_msg());
                    redirect('incidencias/incidencias/listar_tecnico', 'refresh');
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

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $data['edita_adjuntos'] = FALSE;

        $this->Incidencias_model->fields['sector']['array'] = $array_sector;
        $this->Incidencias_model->fields['categoria']['array'] = $array_categoria;
        $this->Incidencias_model->fields['estado']['array'] = $array_estado;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields, $incidencia);
        $data['incidencia'] = $incidencia;

        $observaciones = $this->Observaciones_incidencias_model->get(array(
            'incidencia_id' => $incidencia->id,
            'join' => array(
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        $data['buscar_tecnico'] = FALSE;
        $data['asigna_tecnico'] = TRUE;
        $data['back_url'] = 'listar_tecnico';
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Incidencia';
        $data['title'] = TITLE . ' - Editar Incidencia';
        $data['js'] = 'js/incidencias/base.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function anular($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }
        if ($incidencia->estado === 'Solucionada' || $this->input->post('estado') === 'Cerrada' || $incidencia->estado === 'Anulada')
        {
            redirect("incidencias/incidencias/ver/$id", 'refresh');
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
                redirect('incidencias/incidencias/listar', 'refresh');
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

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        $data['buscar_tecnico'] = FALSE;
        $data['back_url'] = 'listar';
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular Incidencia';
        $data['title'] = TITLE . ' - Anular Incidencia';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function editar_area($id = NULL)
    {
        if ((!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_tecnico, $this->grupos)) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }
        if ($incidencia->estado === 'Solucionada' || $incidencia->estado === 'Cerrada' || $incidencia->estado === 'Anulada' || $incidencia->estado === 'En Proceso')
        {
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }
        $incidencia->observacion = NULL;

        if (in_groups($this->grupos_area, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $incidencia->area_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $array_todas_categoria = $this->get_array('Categorias'); // SOLO PARA OBSERVACION DEL CAMBIO
        $this->array_sector_control = $array_sector = $this->get_array('Sectores');
        if ($this->input->post('sector'))
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $this->input->post('sector')))
                    )
            );
        }
        else
        {
            $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'descripcion', 'id', array(
                'where' => array(
                    array('column' => 'in_categorias.sector_id', 'value' => $incidencia->sector_id))
                    )
            );
        }
        $this->Incidencias_model->fields['estado'] = array('label' => 'Estado', 'id_name' => 'estado', 'disabled' => TRUE);
        unset($this->Incidencias_model->fields['area']['input_type']);
        unset($this->Incidencias_model->fields['area']['required']);
        $this->Incidencias_model->fields['area']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['tecnico']['input_type']);
        unset($this->Incidencias_model->fields['tecnico']['required']);
        $this->Incidencias_model->fields['tecnico']['disabled'] = TRUE;
        $this->Incidencias_model->fields['observacion'] = array('label' => 'Observación', 'form_type' => 'textarea', 'rows' => 5);
        unset($this->Incidencias_model->fields['fecha_inicio']['required']);
        $this->Incidencias_model->fields['fecha_inicio']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['contacto']['required']);
        $this->Incidencias_model->fields['contacto']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['telefono']['required']);
        $this->Incidencias_model->fields['telefono']['disabled'] = TRUE;
        unset($this->Incidencias_model->fields['detalle']['required']);
        $this->Incidencias_model->fields['detalle']['disabled'] = TRUE;

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
                $datos_incidente['categoria_id'] = $this->input->post('categoria');

                $trans_ok &= $this->Incidencias_model->update($datos_incidente, FALSE);

                if ($this->input->post('categoria') != $incidencia->categoria_id)
                {
                    $cat_ant = $array_todas_categoria[$incidencia->categoria_id];
                    $cat_nueva = $array_todas_categoria[$this->input->post('categoria')];
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => "Cambia categoria: $cat_ant => $cat_nueva",
                        'user_id' => $this->session->userdata('user_id')), FALSE);
                }

                if ($this->input->post('observacion') !== '')
                {
                    $trans_ok &= $this->Observaciones_incidencias_model->create(array(
                        'fecha' => date_format(new DateTime(), 'Y-m-d H:i'),
                        'incidencia_id' => $this->input->post('id'),
                        'observacion' => $this->input->post('observacion'),
                        'user_id' => $this->session->userdata('user_id')
                            ), FALSE);
                }

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Incidencias_model->get_msg());
                    redirect('incidencias/incidencias/listar_area', 'refresh');
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

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $data['edita_adjuntos'] = FALSE;

        $this->Incidencias_model->fields['sector']['array'] = $array_sector;
        $this->Incidencias_model->fields['categoria']['array'] = $array_categoria;
        $data['fields'] = $this->build_fields($this->Incidencias_model->fields, $incidencia);
        $data['incidencia'] = $incidencia;

        $observaciones = $this->Observaciones_incidencias_model->get(array(
            'incidencia_id' => $incidencia->id,
            'join' => array(
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        $data['buscar_tecnico'] = FALSE;
        $data['asigna_tecnico'] = FALSE;
        $data['back_url'] = 'listar_area';
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Incidencia';
        $data['title'] = TITLE . ' - Editar Incidencia';
        $data['js'] = 'js/incidencias/base.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function anular_area($id = NULL)
    {
        if ((!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_tecnico, $this->grupos)) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }
        if ($incidencia->estado === 'Solucionada' || $incidencia->estado === 'Cerrada' || $incidencia->estado === 'Anulada' || $incidencia->estado === 'En Proceso')
        {
            redirect("incidencias/incidencias/ver/$id", 'refresh');
        }

        if (in_groups($this->grupos_area, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $incidencia->area_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
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
                redirect('incidencias/incidencias/listar_area', 'refresh');
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

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;

        $data['buscar_tecnico'] = FALSE;
        $data['back_url'] = 'listar_area';
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular Incidencia';
        $data['title'] = TITLE . ' - Anular Incidencia';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;
        $data['back_url'] = 'listar';
        $data['buscar_tecnico'] = FALSE;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Incidencia';
        $data['title'] = TITLE . ' - Ver Incidencia';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function ver_area($id = NULL)
    {
        if ((!in_groups($this->grupos_area, $this->grupos) && !in_groups($this->grupos_tecnico, $this->grupos)) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_area, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $incidencia->area_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        else
        {
            $data['back_url'] = 'listar_area';
        }

        if (in_groups($this->grupos_tecnico, $this->grupos))
        {
            $sector_usuario = $this->Usuarios_sectores_model->get(array('user_id' => $this->session->userdata('user_id')));
            if (empty($sector_usuario))
            {
                show_error('No se encontró asignación a Sector', 500, 'Registro no encontrado');
            }

            if ($incidencia->sector_id !== $sector_usuario[0]->sector_id && $incidencia->tecnico_id !== $sector_usuario[0]->user_id)
            {
                if (!$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $incidencia->area_id))
                {
                    show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
                }
                else
                {
                    $data['back_url'] = 'listar_area';
                }
            }
            else
            {
                $data['back_url'] = 'listar_tecnico';
            }
        }

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;
        $data['buscar_tecnico'] = FALSE;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Incidencia';
        $data['title'] = TITLE . ' - Ver Incidencia';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
    }

    public function ver_tecnico($id = NULL)
    {
        if (!in_groups($this->grupos_tecnico, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $incidencia = $this->Incidencias_model->get_one($id);
        if (empty($incidencia))
        {
            show_error('No se encontró la Incidencia', 500, 'Registro no encontrado');
        }

        if (!$this->Usuarios_sectores_model->in_sector($this->session->userdata('user_id'), $incidencia->sector_id) &&
                $this->session->userdata('user_id') !== $incidencia->tecnico_id)
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        else
        {
            $data['back_url'] = 'listar_tecnico';
        }

        $this->load->model('incidencias/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'incidencia_id' => $id,
            'join' => array(
                array('in_tipos_adjuntos', 'in_tipos_adjuntos.id = in_adjuntos.tipo_id', 'LEFT', array('in_tipos_adjuntos.nombre as tipo_adjunto'))
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
                array('users', 'users.id = in_observaciones_incidencias.user_id', 'LEFT'),
                array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre) as usuario")),
            ))
        );
        $data['observaciones'] = $observaciones;
        $data['buscar_tecnico'] = FALSE;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Incidencia';
        $data['title'] = TITLE . ' - Ver Incidencia';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('incidencias/incidencias/incidencias_abm', $data);
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
            redirect("incidencias/incidencias/ver/$id", 'refresh');
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

        redirect('incidencias/incidencias/listar_tecnico', 'refresh');
    }

    public function repetir()
    {
        if ((!in_groups($this->grupos_admin, $this->grupos)))
        {
            $this->output->set_status_header('403');
            $return_data['message'] = 'No tiene permisos para la acción solicitada';
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->output->set_status_header('403');
            $return_data['message'] = 'Usuario sin permisos de edición';
        }

        $this->form_validation->set_rules('incidencia_id', 'Incidencia', 'integer|required');
        $this->form_validation->set_rules('fecha', 'Fecha', 'date|required');
        if ($this->form_validation->run() === TRUE)
        {
            $incidencia = $this->Incidencias_model->get(array('id' => $this->input->post('incidencia_id')));
            if (empty($incidencia))
            {
                $this->output->set_status_header('500');
                $return_data['message'] = 'No se encontró la Incidencia';
            }

            if (!empty($incidencia))
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Incidencias_model->create(array(
                    'area_id' => $incidencia->area_id,
                    'fecha_inicio' => $this->get_datetime_sql('fecha'),
                    'contacto' => $incidencia->contacto,
                    'telefono' => $incidencia->telefono,
                    'categoria_id' => $incidencia->categoria_id,
                    'detalle' => $incidencia->detalle,
                    'estado' => 'Pendiente',
                    'tecnico_id' => $incidencia->tecnico_id,
                    'user_id' => $this->session->userdata('user_id')), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->output->set_status_header('200');
                    $return_data['message'] = 'Incidencia duplicada correctamente';
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = 'Se ha producido un error con la base de datos.';
                    if ($this->Vales_model->get_error())
                    {
                        $error_msg .= $this->Vales_model->get_error();
                    }
                    $this->output->set_status_header('500');
                    $return_data['message'] = 'ERROR: ' . $error_msg;
                }
            }
            else
            {
                $this->output->set_status_header('500');
                $return_data['message'] = 'No se puede duplicar esta incidencia';
            }
        }
        else
        {
            $this->output->set_status_header('400');
            $return_data['message'] = validation_errors();
        }

        echo json_encode($return_data);
    }
}
