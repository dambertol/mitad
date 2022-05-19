<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Difuntos extends MY_Controller
{

    /**
     * Controlador de Difuntos
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Difuntos_model');
        $this->load->model('defunciones/Cocherias_model');
        $this->load->model('defunciones/Ubicaciones_model');
        $this->load->model('defunciones/Operaciones_model');
        $this->load->model('defunciones/Concesiones_model');
        $this->grupos_permitidos = array('admin', 'defunciones_user', 'defunciones_consulta_general');
        $this->grupos_solo_consulta = array('defunciones_consulta_general');
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
                array('label' => 'Ficha', 'data' => 'ficha', 'width' => 5, 'class' => 'dt-body-right'),
                array('label' => 'DNI', 'data' => 'dni', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 10),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 10),
                array('label' => 'Defunción', 'data' => 'defuncion', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Edad', 'data' => 'edad', 'width' => 6),
                array('label' => 'Inhumación', 'data' => 'licencia_inhumacion', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Cementerio', 'data' => 'cementerio', 'width' => 10),
                array('label' => 'Tipo', 'data' => 'tipo', 'width' => 6),
                array('label' => 'Ubicación', 'data' => 'ubicacion', 'width' => 16),
                array('label' => 'Vto. Concesión', 'data' => 'vencimiento_concesion', 'width' => 8, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'difuntos_table',
            'source_url' => 'defunciones/difuntos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_difuntos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Difuntos';
        $data['title'] = TITLE . ' - Difuntos';
        $this->load_template('defunciones/difuntos/difuntos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('defunciones/datatables_functions_helper');
        $this->datatables
                ->select('df_difuntos.id, df_difuntos.ficha, df_difuntos.dni, df_difuntos.apellido, df_difuntos.nombre, df_difuntos.defuncion, df_difuntos.edad, df_difuntos.licencia_inhumacion, df_cementerios.nombre as cementerio, df_ubicaciones.tipo as tipo, 1 as ubicacion, df_concesiones.fin as vencimiento_concesion, df_ubicaciones.sector as sector, df_ubicaciones.fila as fila, df_ubicaciones.nicho as nicho, df_ubicaciones.cuadro as cuadro, df_ubicaciones.denominacion as denominacion')
                ->from('df_difuntos')
                ->join('df_ubicaciones', 'df_ubicaciones.id = df_difuntos.ubicacion_id', 'left')
                ->join('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'left')
                ->join('df_concesiones', 'df_concesiones.id = df_difuntos.ultima_concesion_id', 'left')
                ->edit_column('ubicacion', '$1', 'dt_column_difuntos_ubicacion(tipo, sector, fila, nicho, cuadro, denominacion)', TRUE)
                ->add_column('ver', '<a href="defunciones/difuntos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/difuntos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/difuntos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar($solicitante_id = NULL, $tipo_operacion = NULL, $nuevo_tramite = 0)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('defunciones/difuntos/listar', 'refresh');
        }

        $this->array_cocheria_control = $array_cocheria = $this->get_array('Cocherias', 'nombre');
        $this->set_model_validation_rules($this->Difuntos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Difuntos_model->create(array(
                'ficha' => $this->input->post('ficha'),
                'dni' => $this->input->post('dni'),
                'apellido' => $this->input->post('apellido'),
                'nombre' => $this->input->post('nombre'),
                'defuncion' => $this->get_date_sql('defuncion'),
                'edad' => $this->input->post('edad'),
                'causa_muerte' => $this->input->post('causa_muerte'),
                'licencia_inhumacion' => $this->input->post('licencia_inhumacion'),
                'registro_civil' => $this->input->post('registro_civil'),
                'cocheria_id' => $this->input->post('cocheria'),
                'nacimiento' => $this->get_date_sql('nacimiento'),
                'observaciones' => $this->input->post('observaciones')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Difuntos_model->get_msg());
                $difunto_id = $this->Difuntos_model->get_row_id();
                if (empty($solicitante_id))
                {
                    redirect('defunciones/difuntos/listar', 'refresh');
                }
                else
                {
                    if (empty($tipo_operacion) || $tipo_operacion === 'concesiones')
                    {
                        redirect("defunciones/concesiones/agregar/$solicitante_id/$difunto_id/$nuevo_tramite", 'refresh');
                    }
                    else
                    {
                        redirect("defunciones/$tipo_operacion/agregar/$solicitante_id/$difunto_id", 'refresh');
                    }
                }
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Difuntos_model->get_error())
                {
                    $error_msg .= $this->Difuntos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        if ($solicitante_id !== NULL)
        {
            $this->load->model('defunciones/Solicitantes_model');
            $solicitante = $this->Solicitantes_model->get(array('id' => $solicitante_id));
            if (empty($solicitante))
            {
                show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
            }
            $data['fields_solicitante'] = $this->build_fields($this->Solicitantes_model->fields, $solicitante, TRUE);
        }

        $this->Difuntos_model->fields['cocheria']['array'] = $array_cocheria;
        $data['fields'] = $this->build_fields($this->Difuntos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Difunto';
        $data['title'] = TITLE . ' - Agregar Difunto';
        $this->load_template('defunciones/difuntos/difuntos_abm', $data);
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
            redirect("defunciones/difuntos/ver/$id", 'refresh');
        }

        $this->array_cocheria_control = $array_cocheria = $this->get_array('Cocherias', 'nombre');
        $difunto = $this->Difuntos_model->get_one($id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Difuntos_model);
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
                $trans_ok &= $this->Difuntos_model->update(array(
                    'id' => $this->input->post('id'),
                    'ficha' => $this->input->post('ficha'),
                    'dni' => $this->input->post('dni'),
                    'apellido' => $this->input->post('apellido'),
                    'nombre' => $this->input->post('nombre'),
                    'defuncion' => $this->get_date_sql('defuncion'),
                    'edad' => $this->input->post('edad'),
                    'causa_muerte' => $this->input->post('causa_muerte'),
                    'licencia_inhumacion' => $this->input->post('licencia_inhumacion'),
                    'registro_civil' => $this->input->post('registro_civil'),
                    'cocheria_id' => $this->input->post('cocheria'),
                    'nacimiento' => $this->get_date_sql('nacimiento'),
                    'observaciones' => $this->input->post('observaciones')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Difuntos_model->get_msg());
                    redirect('defunciones/difuntos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Difuntos_model->get_error())
                    {
                        $error_msg .= $this->Difuntos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $operaciones = $this->Operaciones_model->get(array(
            'difunto_id' => $difunto->id,
            'join' => array(array(
                    'table' => 'df_solicitantes',
                    'where' => 'df_solicitantes.id=df_operaciones.solicitante_id',
                    'columnas' => array('df_solicitantes.nombre as solicitante')),
                array(
                    'type' => 'left',
                    'table' => 'df_expedientes',
                    'where' => 'df_expedientes.id=df_operaciones.expediente_id',
                    'columnas' => array("CONCAT(df_expedientes.numero, '/', df_expedientes.ejercicio) as expediente"))
            ),
            'sort_by' => 'fecha_tramite',
            'sort_direction' => 'desc'));

        $this->Difuntos_model->fields['ubicacion'] = array('label' => 'Ubicación', 'readonly' => TRUE);
        $this->Difuntos_model->fields['ultima_concesion'] = array('label' => 'Última concesión', 'readonly' => TRUE);

        $this->Difuntos_model->fields['cocheria']['array'] = $array_cocheria;
        $data['fields'] = $this->build_fields($this->Difuntos_model->fields, $difunto);
        $data['difunto'] = $difunto;
        $data['operaciones'] = $operaciones;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Difunto';
        $data['title'] = TITLE . ' - Editar Difunto';
        $this->load_template('defunciones/difuntos/difuntos_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("defunciones/difuntos/ver/$id", 'refresh');
        }

        $difunto = $this->Difuntos_model->get_one($id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Difuntos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Difuntos_model->get_msg());
                redirect('defunciones/difuntos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Difuntos_model->get_error())
                {
                    $error_msg .= $this->Difuntos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $operaciones = $this->Operaciones_model->get(array(
            'difunto_id' => $difunto->id,
            'join' => array(array(
                    'table' => 'df_solicitantes',
                    'where' => 'df_solicitantes.id=df_operaciones.solicitante_id',
                    'columnas' => array('df_solicitantes.nombre as solicitante')),
                array(
                    'type' => 'left',
                    'table' => 'df_expedientes',
                    'where' => 'df_expedientes.id=df_operaciones.expediente_id',
                    'columnas' => array("CONCAT(df_expedientes.numero, '/', df_expedientes.ejercicio) as expediente"))
            ),
            'sort_by' => 'fecha_tramite',
            'sort_direction' => 'desc'));

        $this->Difuntos_model->fields['ubicacion'] = array('label' => 'Ubicación', 'required' => TRUE);
        $this->Difuntos_model->fields['ultima_concesion'] = array('label' => 'Última concesión', 'required' => TRUE);

        $data['fields'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);
        $data['difunto'] = $difunto;
        $data['operaciones'] = $operaciones;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Difunto';
        $data['title'] = TITLE . ' - Eliminar Difunto';
        $this->load_template('defunciones/difuntos/difuntos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $difunto = $this->Difuntos_model->get_one($id);
        if (empty($difunto))
        {
            show_error('No se encontró el Difunto', 500, 'Registro no encontrado');
        }

        $operaciones = $this->Operaciones_model->get(array(
            'difunto_id' => $difunto->id,
            'join' => array(array(
                    'table' => 'df_solicitantes',
                    'where' => 'df_solicitantes.id=df_operaciones.solicitante_id',
                    'columnas' => array('df_solicitantes.nombre as solicitante')),
                array(
                    'type' => 'left',
                    'table' => 'df_expedientes',
                    'where' => 'df_expedientes.id=df_operaciones.expediente_id',
                    'columnas' => array("CONCAT(df_expedientes.numero, '/', df_expedientes.ejercicio) as expediente"))
            ),
            'sort_by' => 'fecha_tramite',
            'sort_direction' => 'desc'));

        $this->Difuntos_model->fields['ubicacion'] = array('label' => 'Ubicación', 'required' => TRUE);
        $this->Difuntos_model->fields['ultima_concesion'] = array('label' => 'Última concesión', 'required' => TRUE);
        $data['fields'] = $this->build_fields($this->Difuntos_model->fields, $difunto, TRUE);
        $data['difunto'] = $difunto;
        $data['operaciones'] = $operaciones;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Difunto';
        $data['title'] = TITLE . ' - Ver Difunto';
        $this->load_template('defunciones/difuntos/difuntos_abm', $data);
    }
}
