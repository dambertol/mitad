<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vehiculos extends MY_Controller
{

    /**
     * Controlador de Vehículos
     * Autor: Leandro
     * Creado: 17/11/2017
     * Modificado: 22/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Areas_model');
        $this->load->model('vales_combustible/Adjuntos_model');
        $this->load->model('vales_combustible/Tipos_combustible_model');
        $this->load->model('vales_combustible/Tipos_vehiculo_model');
        $this->load->model('vales_combustible/Usuarios_areas_model');
        $this->load->model('vales_combustible/Vehiculos_model');
        $this->load->model('vales_combustible/Vehiculos_combustible_model');
        $this->grupos_permitidos = array('admin', 'vales_combustible_autorizaciones', 'vales_combustible_contaduria', 'vales_combustible_areas', 'vales_combustible_obrador', 'vales_combustible_consulta_general');
        $this->grupos_admin = array('admin', 'vales_combustible_contaduria', 'vales_combustible_consulta_general');
        $this->grupos_areas = array('vales_combustible_areas');
        $this->grupos_solo_consulta = array('vales_combustible_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $columns = array(
                array('label' => 'Nombre', 'data' => 'vehiculo', 'width' => 16),
                array('label' => 'Propiedad', 'data' => 'propiedad', 'width' => 8),
                array('label' => 'Propietario', 'data' => 'propietario', 'width' => 10),
                array('label' => 'Tipo', 'data' => 'tipo_vehiculo', 'width' => 8),
                array('label' => 'Dominio/Serie', 'data' => 'dominio', 'width' => 8),
                array('label' => 'Tipo Combustible', 'data' => 'tipo_combustible', 'width' => 8),
                array('label' => 'Área', 'data' => 'area', 'width' => 16),
                array('label' => 'Venc Seguro', 'data' => 'vencimiento_seguro', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 8),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => '', 'data' => 'aprobar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'marcar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            );
        }
        else if (in_groups($this->grupos_areas, $this->grupos))
        {
            $columns = array(
                array('label' => 'Nombre', 'data' => 'vehiculo', 'width' => 18),
                array('label' => 'Propiedad', 'data' => 'propiedad', 'width' => 8),
                array('label' => 'Propietario', 'data' => 'propietario', 'width' => 10),
                array('label' => 'Tipo', 'data' => 'tipo_vehiculo', 'width' => 8),
                array('label' => 'Dominio/Serie', 'data' => 'dominio', 'width' => 8),
                array('label' => 'Tipo Combustible', 'data' => 'tipo_combustible', 'width' => 8),
                array('label' => 'Área', 'data' => 'area', 'width' => 23),
                array('label' => 'Venc Seguro', 'data' => 'vencimiento_seguro', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 8),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            );
        }
        else
        {
            $columns = array(
                array('label' => 'Nombre', 'data' => 'vehiculo', 'width' => 18),
                array('label' => 'Propiedad', 'data' => 'propiedad', 'width' => 8),
                array('label' => 'Propietario', 'data' => 'propietario', 'width' => 9),
                array('label' => 'Tipo', 'data' => 'tipo_vehiculo', 'width' => 8),
                array('label' => 'Dominio/Serie', 'data' => 'dominio', 'width' => 10),
                array('label' => 'Tipo Combustible', 'data' => 'tipo_combustible', 'width' => 8),
                array('label' => 'Área', 'data' => 'area', 'width' => 24),
                array('label' => 'Venc Seguro', 'data' => 'vencimiento_seguro', 'render' => 'date', 'class' => 'dt-body-right', 'width' => 8),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            );
        }

        $tableData = array(
            'columns' => $columns,
            'table_id' => 'vehiculos_table',
            'source_url' => 'vales_combustible/vehiculos/listar_data',
            'fnDrawCallback' => "drawCallback_vehiculos_table",
            'reuse_var' => TRUE,
            'initComplete' => "complete_vehiculos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_estados'] = array('' => 'Todos', 'Aprobado' => 'Aprobado', 'Anulado' => 'Anulado', 'Pendiente' => 'Pendiente');
        $data['array_tipos'] = $this->get_array('Tipos_combustible', 'nombre', 'nombre', array(), array('' => 'Todos'));
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $data['acciones_masivas'] = TRUE;
            $data['boton_agregar'] = TRUE;
        }
        else
        {
            $data['acciones_masivas'] = FALSE;
            if (in_groups($this->grupos_areas, $this->grupos))
            {
                $data['boton_agregar'] = TRUE;
            }
            else
            {
                $data['boton_agregar'] = FALSE;
            }
        }
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Vehículos';
        $data['title'] = TITLE . ' - Vehículos';
        $this->load_template('vales_combustible/vehiculos/vehiculos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('vales_combustible/datatables_functions_helper');
        $dt = $this->datatables
                ->select('vc_vehiculos.id, vc_vehiculos.nombre as vehiculo, propiedad, propietario, vc_tipos_vehiculo.nombre as tipo_vehiculo, vc_vehiculos.dominio, (SELECT GROUP_CONCAT(vc_tipos_combustible.nombre SEPARATOR ", ") FROM vc_vehiculos_combustible JOIN vc_tipos_combustible ON vc_tipos_combustible.id = vc_vehiculos_combustible.tipo_combustible_id WHERE vc_vehiculos_combustible.vehiculo_id = vc_vehiculos.id) AS tipo_combustible, ((CASE WHEN areas.codigo IS NULL THEN "Todas las Áreas" ELSE CONCAT(areas.codigo, " - ", areas.nombre) END)) as area, vencimiento_seguro, vc_vehiculos.estado')
                ->from('vc_vehiculos')
                ->join('vc_tipos_vehiculo', 'vc_tipos_vehiculo.id = vc_vehiculos.tipo_vehiculo_id', 'left')
                ->join('areas', 'areas.id = vc_vehiculos.area_id', 'left');

        if (in_groups($this->grupos_areas, $this->grupos))
        {
            $dt->join('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id ', 'left')
                    ->where('(vc_usuarios_areas.user_id = ' . $this->session->userdata('user_id') . ')');
        }

        $dt->edit_column('estado', '$1', 'dt_column_vehiculos_estado(estado)', TRUE)
                ->add_column('ver', '<a href="vales_combustible/vehiculos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id');

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $dt->add_column('editar', '<a href="vales_combustible/vehiculos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                    ->add_column('eliminar', '<a href="vales_combustible/vehiculos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id')
                    ->add_column('aprobar', '$1', 'dt_column_vehiculos_aprobar(estado, id)')
                    ->add_column('marcar', '<input type="checkbox" name="vehiculo[]" value="$1">', 'id');
        }
        else if (in_groups($this->grupos_areas, $this->grupos))
        {
            $dt->edit_column('editar', '$1', 'dt_column_vehiculos_editar(propiedad, id)', TRUE);
        }

        echo $dt->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            if (in_groups($this->grupos_areas, $this->grupos))
            {
                redirect('vales_combustible/vehiculos/agregar_area', 'refresh');
            }
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vechiculos/listar", 'refresh');
        }

        $this->array_propiedad_control = $array_propiedad = array('Oficial' => 'Oficial', 'Particular' => 'Particular', 'Alquilado' => 'Alquilado');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('' => 'Todas las Áreas'));
        $this->array_estado_control = $array_estado = array('Aprobado' => 'Aprobado', 'Anulado' => 'Anulado', 'Pendiente' => 'Pendiente');
        $this->array_tipo_vehiculo_control = $array_tipo_vehiculo = $this->get_array('Tipos_vehiculo', 'nombre');
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');

        $this->set_model_validation_rules($this->Vehiculos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $seguro = FALSE;
            $tarjeta_verde = FALSE;
            if (!empty($this->input->post('adjunto_agregar')))
            {
                foreach ($this->input->post('adjunto_agregar') as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));
                    if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                    {
                        if ($adjunto->tipo_id === '1') //HC: Seguro
                        {
                            $seguro = TRUE;
                        }
                        else if ($adjunto->tipo_id === '2') //HC: Tarjeta Verde
                        {
                            $tarjeta_verde = TRUE;
                        }
                    }
                }
            }

            $tipo_vehiculo_id = $this->input->post('tipo_vehiculo');
            if ($tipo_vehiculo_id !== '5' && (!$seguro || !$tarjeta_verde))
            {
                $error_msg = '<br />Debe adjuntar Seguro y Tarjeta Verde del Vehículo.';
            }

            if (empty($error_msg))
            {
                $vencimiento_seguro = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento_seguro'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Vehiculos_model->create(array(
                    'nombre' => $this->input->post('nombre'),
                    'propiedad' => $this->input->post('propiedad'),
                    'propietario' => $this->input->post('propietario'),
                    'area_id' => $this->input->post('area'),
                    'tipo_vehiculo_id' => $tipo_vehiculo_id,
                    'dominio' => $this->input->post('dominio'),
                    'consumo' => $this->input->post('consumo'),
                    'capacidad_tanque' => $this->input->post('capacidad_tanque'),
                    'consumo_semanal' => $this->input->post('consumo_semanal'),
                    'vencimiento_seguro' => $vencimiento_seguro->format('Y-m-d'),
                    'estado' => $this->input->post('estado'),
                    'observaciones' => $this->input->post('observaciones'),
                    'user_id' => $this->session->userdata('user_id')), FALSE);

                $vehiculo_id = $this->Vehiculos_model->get_row_id();
                $tipos_combustible = $this->input->post('tipo_combustible');

                foreach ($tipos_combustible as $Tipo)
                {
                    $trans_ok &= $this->Vehiculos_combustible_model->create(array(
                        'vehiculo_id' => $vehiculo_id,
                        'tipo_combustible_id' => $Tipo), FALSE);
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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/vales_combustible/vehiculos/" . str_pad($vehiculo_id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'vehiculo_id' => $vehiculo_id
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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
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
                    $this->session->set_flashdata('message', $this->Vehiculos_model->get_msg());
                    redirect("vales_combustible/vehiculos/listar", 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    if (empty($error_msg))
                    {
                        $error_msg = '<br />Se ha producido un error con la base de datos.';
                        if ($this->Vehiculos_model->get_error())
                        {
                            $error_msg .= $this->Vehiculos_model->get_error();
                        }
                        if ($this->Vehiculos_combustible_model->get_error())
                        {
                            $error_msg .= $this->Vehiculos_combustible_model->get_error();
                        }
                        if ($this->Adjuntos_model->get_error())
                        {
                            $error_msg .= $this->Adjuntos_model->get_error();
                        }
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $this->Vehiculos_model->fields['propiedad']['array'] = $array_propiedad;
        $this->Vehiculos_model->fields['area']['array'] = $array_area;
        $this->Vehiculos_model->fields['tipo_vehiculo']['array'] = $array_tipo_vehiculo;
        $this->Vehiculos_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $this->Vehiculos_model->fields['estado']['array'] = $array_estado;
        $data['fields'] = $this->build_fields($this->Vehiculos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Vehículo';
        $data['title'] = TITLE . ' - Agregar Vehículo';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('vales_combustible/vehiculos/vehiculos_abm', $data);
    }

    public function agregar_area()
    {
        if (!in_groups($this->grupos_areas, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vechiculos/listar", 'refresh');
        }

        $this->array_propiedad_control = $array_propiedad = array('Particular' => 'Particular', 'Alquilado' => 'Alquilado');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('areas.id', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'join' => array(array('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id', 'LEFT')),
            'where' => array("nombre <> '-'", "vc_usuarios_areas.user_id = " . $this->session->userdata('user_id')),
            'sort_by' => 'codigo')
        );
        $this->array_tipo_vehiculo_control = $array_tipo_vehiculo = $this->get_array('Tipos_vehiculo', 'nombre');
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');

        $vehiculos_model = $this->Vehiculos_model;
        unset($vehiculos_model->fields['estado']);

        $this->set_model_validation_rules($this->Vehiculos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $seguro = FALSE;
            $tarjeta_verde = FALSE;
            if (!empty($this->input->post('adjunto_agregar')))
            {
                foreach ($this->input->post('adjunto_agregar') as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));
                    if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                    {
                        if ($adjunto->tipo_id === '1') //HC: Seguro
                        {
                            $seguro = TRUE;
                        }
                        else if ($adjunto->tipo_id === '2') //HC: Tarjeta Verde
                        {
                            $tarjeta_verde = TRUE;
                        }
                    }
                }
            }
            $tipo_vehiculo_id = $this->input->post('tipo_vehiculo');
            if ($tipo_vehiculo_id !== '5' && (!$seguro || !$tarjeta_verde))
            {
                $error_msg = '<br />Debe adjuntar Seguro y Tarjeta Verde del Vehículo.';
            }

            if (empty($error_msg))
            {
                $vencimiento_seguro = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento_seguro'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Vehiculos_model->create(array(
                    'nombre' => $this->input->post('nombre'),
                    'propiedad' => $this->input->post('propiedad'),
                    'propietario' => $this->input->post('propietario'),
                    'area_id' => $this->input->post('area'),
                    'tipo_vehiculo_id' => $tipo_vehiculo_id,
                    'dominio' => $this->input->post('dominio'),
                    'consumo' => $this->input->post('consumo'),
                    'capacidad_tanque' => $this->input->post('capacidad_tanque'),
                    'consumo_semanal' => $this->input->post('consumo_semanal'),
                    'vencimiento_seguro' => $vencimiento_seguro->format('Y-m-d'),
                    'estado' => 'Pendiente',
                    'observaciones' => $this->input->post('observaciones'),
                    'user_id' => $this->session->userdata('user_id')), FALSE);

                $vehiculo_id = $this->Vehiculos_model->get_row_id();
                $tipos_combustible = $this->input->post('tipo_combustible');

                foreach ($tipos_combustible as $Tipo)
                {
                    $trans_ok &= $this->Vehiculos_combustible_model->create(array(
                        'vehiculo_id' => $vehiculo_id,
                        'tipo_combustible_id' => $Tipo), FALSE);
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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/vales_combustible/vehiculos/" . str_pad($vehiculo_id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'vehiculo_id' => $vehiculo_id
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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
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
                    $this->session->set_flashdata('message', $this->Vehiculos_model->get_msg());
                    redirect('vales_combustible/vehiculos/listar/', 'refresh');
                }
                else
                {
                    if (empty($error_msg))
                    {
                        $error_msg = '<br />Se ha producido un error con la base de datos.';
                        if ($this->Vehiculos_model->get_error())
                        {
                            $error_msg .= $this->Vehiculos_model->get_error();
                        }
                        if ($this->Vehiculos_combustible_model->get_error())
                        {
                            $error_msg .= $this->Vehiculos_combustible_model->get_error();
                        }
                        if ($this->Adjuntos_model->get_error())
                        {
                            $error_msg .= $this->Adjuntos_model->get_error();
                        }
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $this->Vehiculos_model->fields['propiedad']['array'] = $array_propiedad;
        $this->Vehiculos_model->fields['area']['array'] = $array_area;
        $this->Vehiculos_model->fields['tipo_vehiculo']['array'] = $array_tipo_vehiculo;
        $this->Vehiculos_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($this->Vehiculos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Vehículo';
        $data['title'] = TITLE . ' - Agregar Vehículo';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('vales_combustible/vehiculos/vehiculos_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            if (in_groups($this->grupos_areas, $this->grupos))
            {
                redirect("vales_combustible/vehiculos/editar_area/$id", 'refresh');
            }
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vehiculos/ver/$id", 'refresh');
        }

        $vehiculo = $this->Vehiculos_model->get_one($id);
        if (empty($vehiculo))
        {
            show_error('No se encontró el Vehículo', 500, 'Registro no encontrado');
        }
        $vehiculo->tipo_combustible_id = array();
        $vehiculos_combustible = $this->Vehiculos_combustible_model->get(array('vehiculo_id' => $id));
        if (!empty($vehiculos_combustible))
        {
            foreach ($vehiculos_combustible as $Combustible)
            {
                $vehiculo->tipo_combustible_id[] = $Combustible->tipo_combustible_id;
            }
        }

        $this->load->model('vales_combustible/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'vehiculo_id' => $id,
            'join' => array(
                array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $this->array_propiedad_control = $array_propiedad = array('Oficial' => 'Oficial', 'Particular' => 'Particular', 'Alquilado' => 'Alquilado');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('' => 'Todas las Áreas'));
        $this->array_estado_control = $array_estado = array('Aprobado' => 'Aprobado', 'Anulado' => 'Anulado', 'Pendiente' => 'Pendiente');
        $this->array_tipo_vehiculo_control = $array_tipo_vehiculo = $this->get_array('Tipos_vehiculo', 'nombre');
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');
        $this->set_model_validation_rules($this->Vehiculos_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            $seguro = FALSE;
            $tarjeta_verde = FALSE;
            if (!empty($this->input->post('adjunto_agregar')))
            {
                foreach ($this->input->post('adjunto_agregar') as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));
                    if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                    {
                        if ($adjunto->tipo_id === '1') //HC: Seguro
                        {
                            $seguro = TRUE;
                        }
                        else if ($adjunto->tipo_id === '2') //HC: Tarjeta Verde
                        {
                            $tarjeta_verde = TRUE;
                        }
                    }
                }
            }
            if (!empty($adjuntos))
            {
                if ($this->input->post('adjunto_eliminar_existente'))
                {
                    $adjuntos_eliminar_existente_tmp = $this->input->post('adjunto_eliminar_existente');
                }
                else
                {
                    $adjuntos_eliminar_existente_tmp = array();
                }
                foreach ($adjuntos as $Adjunto)
                {
                    if (!array_key_exists($Adjunto->id, $adjuntos_eliminar_existente_tmp))
                    {
                        if ($Adjunto->tipo_id === '1') //HC: Seguro
                        {
                            $seguro = TRUE;
                        }
                        else if ($Adjunto->tipo_id === '2') //HC: Tarjeta Verde
                        {
                            $tarjeta_verde = TRUE;
                        }
                    }
                }
            }
            $tipo_vehiculo_id = $this->input->post('tipo_vehiculo');
            if ($tipo_vehiculo_id !== '5' && (!$seguro || !$tarjeta_verde))
            {
                $error_msg = '<br />Debe adjuntar Seguro y Tarjeta Verde del Vehículo.';
            }

            if ($this->form_validation->run() === TRUE && empty($error_msg))
            {
                $vencimiento_seguro = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento_seguro'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Vehiculos_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'propiedad' => $this->input->post('propiedad'),
                    'propietario' => $this->input->post('propietario'),
                    'area_id' => $this->input->post('area'),
                    'tipo_vehiculo_id' => $tipo_vehiculo_id,
                    'dominio' => $this->input->post('dominio'),
                    'consumo' => $this->input->post('consumo'),
                    'capacidad_tanque' => $this->input->post('capacidad_tanque'),
                    'consumo_semanal' => $this->input->post('consumo_semanal'),
                    'vencimiento_seguro' => $vencimiento_seguro->format('Y-m-d'),
                    'estado' => $this->input->post('estado'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $tipos_combustible = $this->input->post('tipo_combustible');

                if (empty($tipos_combustible))
                {
                    $tipos_combustible = array();
                }
                $trans_ok &= $this->Vehiculos_combustible_model->intersect_asignaciones($id, $tipos_combustible, FALSE);

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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/vales_combustible/vehiculos/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'vehiculo_id' => $id
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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
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
                            'vehiculo_id' => $this->input->post('id')
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
                    $this->session->set_flashdata('message', $this->Vehiculos_model->get_msg());
                    redirect('vales_combustible/vehiculos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Vehiculos_model->get_error())
                    {
                        $error_msg .= $this->Vehiculos_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $this->Vehiculos_model->fields['propiedad']['array'] = $array_propiedad;
        $this->Vehiculos_model->fields['area']['array'] = $array_area;
        $this->Vehiculos_model->fields['estado']['array'] = $array_estado;
        $this->Vehiculos_model->fields['tipo_vehiculo']['array'] = $array_tipo_vehiculo;
        $this->Vehiculos_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($this->Vehiculos_model->fields, $vehiculo);
        $data['vehiculo'] = $vehiculo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Vehículo';
        $data['title'] = TITLE . ' - Editar Vehículo';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('vales_combustible/vehiculos/vehiculos_abm', $data);
    }

    public function editar_area($id = NULL)
    {
        if (!in_groups($this->grupos_areas, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vehiculos/ver/$id", 'refresh');
        }

        $vehiculo = $this->Vehiculos_model->get_one($id);
        if (empty($vehiculo) || $vehiculo->propiedad === 'Oficial')
        {
            show_error('No se encontró el Vehículo', 500, 'Registro no encontrado');
        }
        $vehiculo->tipo_combustible_id = array();
        $vehiculos_combustible = $this->Vehiculos_combustible_model->get(array('vehiculo_id' => $id));
        if (!empty($vehiculos_combustible))
        {
            foreach ($vehiculos_combustible as $Combustible)
            {
                $vehiculo->tipo_combustible_id[] = $Combustible->tipo_combustible_id;
            }
        }

        if (in_groups($this->grupos_areas, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $vehiculo->area_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'vehiculo_id' => $id,
            'join' => array(
                array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
            )
        ));

        $this->array_propiedad_control = $array_propiedad = array('Particular' => 'Particular', 'Alquilado' => 'Alquilado');
        $this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array(
            'select' => array('areas.id', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
            'join' => array(array('vc_usuarios_areas', 'vc_usuarios_areas.area_id = areas.id', 'LEFT')),
            'where' => array("nombre <> '-'", "vc_usuarios_areas.user_id = " . $this->session->userdata('user_id')),
            'sort_by' => 'codigo')
        );
        $this->array_tipo_vehiculo_control = $array_tipo_vehiculo = $this->get_array('Tipos_vehiculo', 'nombre');
        $this->array_tipo_combustible_control = $array_tipo_combustible = $this->get_array('Tipos_combustible', 'nombre');

        $vehiculos_model = $this->Vehiculos_model;
        unset($vehiculos_model->fields['estado']);

        $this->set_model_validation_rules($this->Vehiculos_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            $seguro = FALSE;
            $tarjeta_verde = FALSE;
            if (!empty($this->input->post('adjunto_agregar')))
            {
                foreach ($this->input->post('adjunto_agregar') as $Adjunto_id => $Adjunto_name)
                {
                    $adjunto = $this->Adjuntos_model->get(array(
                        'id' => $Adjunto_id,
                        'nombre' => $Adjunto_name,
                        'usuario_subida' => $this->session->userdata('user_id')
                    ));
                    if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                    {
                        if ($adjunto->tipo_id === '1') //HC: Seguro
                        {
                            $seguro = TRUE;
                        }
                        else if ($adjunto->tipo_id === '2') //HC: Tarjeta Verde
                        {
                            $tarjeta_verde = TRUE;
                        }
                    }
                }
            }
            if (!empty($adjuntos))
            {
                if ($this->input->post('adjunto_eliminar_existente'))
                {
                    $adjuntos_eliminar_existente_tmp = $this->input->post('adjunto_eliminar_existente');
                }
                else
                {
                    $adjuntos_eliminar_existente_tmp = array();
                }
                foreach ($adjuntos as $Adjunto)
                {
                    if (!array_key_exists($Adjunto->id, $adjuntos_eliminar_existente_tmp))
                    {
                        if ($Adjunto->tipo_id === '1') //HC: Seguro
                        {
                            $seguro = TRUE;
                        }
                        else if ($Adjunto->tipo_id === '2') //HC: Tarjeta Verde
                        {
                            $tarjeta_verde = TRUE;
                        }
                    }
                }
            }
            $tipo_vehiculo_id = $this->input->post('tipo_vehiculo');
            if ($tipo_vehiculo_id !== '5' && (!$seguro || !$tarjeta_verde))
            {
                $error_msg = '<br />Debe adjuntar Seguro y Tarjeta Verde del Vehículo.';
            }

            if ($this->form_validation->run() === TRUE && empty($error_msg))
            {
                $vencimiento_seguro = DateTime::createFromFormat('d/m/Y', $this->input->post('vencimiento_seguro'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Vehiculos_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'propiedad' => $this->input->post('propiedad'),
                    'propietario' => $this->input->post('propietario'),
                    'area_id' => $this->input->post('area'),
                    'tipo_vehiculo_id' => $tipo_vehiculo_id,
                    'dominio' => $this->input->post('dominio'),
                    'consumo' => $this->input->post('consumo'),
                    'capacidad_tanque' => $this->input->post('capacidad_tanque'),
                    'consumo_semanal' => $this->input->post('consumo_semanal'),
                    'vencimiento_seguro' => $vencimiento_seguro->format('Y-m-d'),
                    'estado' => 'Pendiente',
                    'observaciones' => $this->input->post('observaciones')), FALSE);

                $tipos_combustible = $this->input->post('tipo_combustible');

                if (empty($tipos_combustible))
                {
                    $tipos_combustible = array();
                }
                $trans_ok &= $this->Vehiculos_combustible_model->intersect_asignaciones($id, $tipos_combustible, FALSE);

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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
                        {
                            $viejo_archivo = $adjunto->ruta . $adjunto->nombre;
                            if (file_exists($viejo_archivo))
                            {
                                $nueva_ruta = "uploads/vales_combustible/vehiculos/" . str_pad($id, 6, "0", STR_PAD_LEFT) . "/";
                                if (!file_exists($nueva_ruta))
                                {
                                    mkdir($nueva_ruta, 0755, TRUE);
                                }
                                $nuevo_nombre = str_pad($Adjunto_id, 6, "0", STR_PAD_LEFT) . "." . pathinfo($adjunto->nombre)['extension'];
                                $trans_ok &= $this->Adjuntos_model->update(array(
                                    'id' => $Adjunto_id,
                                    'nombre' => $nuevo_nombre,
                                    'ruta' => $nueva_ruta,
                                    'vehiculo_id' => $id
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

                        if (!empty($adjunto) && empty($adjunto->vehiculo_id))
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
                            'vehiculo_id' => $this->input->post('id')
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
                    $this->session->set_flashdata('message', $this->Vehiculos_model->get_msg());
                    redirect('vales_combustible/vehiculos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Vehiculos_model->get_error())
                    {
                        $error_msg .= $this->Vehiculos_model->get_error();
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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_agregar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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
                    array('column' => 'vc_adjuntos.id IN', 'value' => '(' . implode(',', $adjuntos_eliminar_id) . ')', 'override' => TRUE)
                ),
                'join' => array(
                    array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $this->Vehiculos_model->fields['propiedad']['array'] = $array_propiedad;
        $this->Vehiculos_model->fields['area']['array'] = $array_area;
        $this->Vehiculos_model->fields['tipo_vehiculo']['array'] = $array_tipo_vehiculo;
        $this->Vehiculos_model->fields['tipo_combustible']['array'] = $array_tipo_combustible;
        $data['fields'] = $this->build_fields($this->Vehiculos_model->fields, $vehiculo);
        $data['vehiculo'] = $vehiculo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Vehículo';
        $data['title'] = TITLE . ' - Editar Vehículo';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('vales_combustible/vehiculos/vehiculos_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vehiculos/ver/$id", 'refresh');
        }

        $vehiculo = $this->Vehiculos_model->get_one($id);
        if (empty($vehiculo))
        {
            show_error('No se encontró el Vehículo', 500, 'Registro no encontrado');
        }
        $tipos_combustible = array();
        $vehiculos_combustible = $this->Vehiculos_combustible_model->get(array(
            'vehiculo_id' => $id,
            'join' => array(
                array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_vehiculos_combustible.tipo_combustible_id', 'left', 'vc_tipos_combustible.nombre as tipo_combustible')
            )
        ));
        if (!empty($vehiculos_combustible))
        {
            foreach ($vehiculos_combustible as $Combustible)
            {
                $tipos_combustible[] = $Combustible->tipo_combustible;
            }
        }
        $vehiculo->tipo_combustible = implode(', ', $tipos_combustible);

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->load->model('vales_combustible/Adjuntos_model');

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Vehiculos_combustible_model->delete_asignaciones($this->input->post('id'));
            $trans_ok &= $this->Adjuntos_model->delete_adjuntos($this->input->post('id'));
            $trans_ok &= $this->Vehiculos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();

                $dir = "uploads/vales_combustible/vehiculos/" . str_pad($this->input->post('id'), 6, "0", STR_PAD_LEFT);
                $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $file)
                {
                    if ($file->isDir())
                    {
                        rmdir($file->getRealPath());
                    }
                    else
                    {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($dir);

                $this->session->set_flashdata('message', $this->Vehiculos_model->get_msg());
                redirect('vales_combustible/vehiculos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vehiculos_model->get_error())
                {
                    $error_msg .= $this->Vehiculos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->load->model('vales_combustible/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'vehiculo_id' => $id,
            'join' => array(
                array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $data['fields'] = $this->build_fields($this->Vehiculos_model->fields, $vehiculo, TRUE);
        $data['vehiculo'] = $vehiculo;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Vehículo';
        $data['title'] = TITLE . ' - Eliminar Vehículo';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('vales_combustible/vehiculos/vehiculos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id === NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $vehiculo = $this->Vehiculos_model->get_one($id);
        if (empty($vehiculo))
        {
            show_error('No se encontró el Vehículo', 500, 'Registro no encontrado');
        }
        $tipos_combustible = array();
        $vehiculos_combustible = $this->Vehiculos_combustible_model->get(array(
            'vehiculo_id' => $id,
            'join' => array(
                array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_vehiculos_combustible.tipo_combustible_id', 'left', 'vc_tipos_combustible.nombre as tipo_combustible')
            )
        ));
        if (!empty($vehiculos_combustible))
        {
            foreach ($vehiculos_combustible as $Combustible)
            {
                $tipos_combustible[] = $Combustible->tipo_combustible;
            }
        }
        $vehiculo->tipo_combustible = implode(', ', $tipos_combustible);

        if (in_groups($this->grupos_areas, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $vehiculo->area_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->model('vales_combustible/Adjuntos_model');
        $adjuntos = $this->Adjuntos_model->get(array(
            'vehiculo_id' => $id,
            'join' => array(
                array('vc_tipos_adjuntos', 'vc_tipos_adjuntos.id = vc_adjuntos.tipo_id', 'LEFT', array('vc_tipos_adjuntos.nombre as tipo_adjunto'))
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

        $this->Vehiculos_model->fields['usuario'] = array('label' => 'Usuario Creación', 'disabled' => 'disabled');

        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Vehiculos_model->fields, $vehiculo, TRUE);
        $data['vehiculo'] = $vehiculo;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Vehículo';
        $data['title'] = TITLE . ' - Ver Vehículo';
        $data['css'][] = 'vendor/lightbox/css/ekko-lightbox.min.css';
        $data['js'][] = 'vendor/lightbox/js/ekko-lightbox.min.js';
        $this->load_template('vales_combustible/vehiculos/vehiculos_abm', $data);
    }

    public function aprobar($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vehiculos/ver/$id", 'refresh');
        }

        $vehiculo = $this->Vehiculos_model->get(array('id' => $id));
        if (empty($vehiculo))
        {
            show_error('No se encontró el Vehículo', 500, 'Registro no encontrado');
        }

        $this->db->trans_begin();
        $trans_ok = TRUE;
        $trans_ok &= $this->Vehiculos_model->update(array(//USANDO TODOS LOS CAMPOS PARA EVITAR EDICIONES AL MEDIO DEL PROCESO
            'id' => $vehiculo->id,
            'nombre' => $vehiculo->nombre,
            'propiedad' => $vehiculo->propiedad,
            'tipo_vehiculo_id' => $vehiculo->tipo_vehiculo_id,
            'dominio' => $vehiculo->dominio,
            'consumo' => $vehiculo->consumo,
            'vencimiento_seguro' => $vehiculo->vencimiento_seguro,
            'estado' => 'Aprobado',
            'observaciones' => $vehiculo->observaciones), FALSE);
        if ($this->db->trans_status() && $trans_ok)
        {
            $this->db->trans_commit();
            $this->session->set_flashdata('message', "<br />Vehículo $vehiculo->nombre - $vehiculo->dominio aprobado");
        }
        else
        {
            $this->db->trans_rollback();
            if ($this->Vehiculos_model->get_error())
            {
                $this->session->set_flashdata('error', $this->Vehiculos_model->get_error());
            }
        }

        redirect('vales_combustible/vehiculos/listar', 'refresh');
    }

    public function acciones_masivas_Aprobar()
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("vales_combustible/vales/listar", 'refresh');
        }

        $this->array_tipo_control = array('Aprobar' => 'Aprobar');
        $fake_model = new stdClass();
        $fake_model->fields = array(
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'required' => TRUE),
            'vehiculo[]' => array('label' => 'Vehículos', 'required' => TRUE),
            'back_url' => array('label' => 'URL', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $vehiculos = $this->input->post('vehiculo');
            $this->db->trans_begin();
            $trans_ok = TRUE;
            foreach ($vehiculos as $Vehiculo_id)
            {
                $vehiculo = $this->Vehiculos_model->get(array('id' => $Vehiculo_id));
                if (!empty($vehiculo) && $vehiculo->estado === 'Pendiente')
                {
                    $trans_ok &= $this->Vehiculos_model->update(array(
                        'id' => $Vehiculo_id,
                        'nombre' => $vehiculo->nombre,
                        'propiedad' => $vehiculo->propiedad,
                        'tipo_vehiculo_id' => $vehiculo->tipo_vehiculo_id,
                        'dominio' => $vehiculo->dominio,
                        'consumo' => $vehiculo->consumo,
                        'vencimiento_seguro' => $vehiculo->vencimiento_seguro,
                        'estado' => 'Aprobado',
                        'observaciones' => $vehiculo->observaciones), FALSE);
                }
            }
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', '<br />Vehículos aprobados correctamente');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Vehiculos_model->get_error())
                {
                    $error_msg .= '<br>' . $this->Vehiculos_model->get_error();
                }
                $this->session->set_flashdata('error', !empty($error_msg) ? $error_msg : '');
            }
        }
        else
        {
            $this->session->set_flashdata('error', validation_errors() ? validation_errors() : '');
        }
        redirect('vales_combustible/vehiculos/' . $this->input->post('back_url'), 'refresh');
    }
}
