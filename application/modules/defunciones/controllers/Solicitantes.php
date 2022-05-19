<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Solicitantes extends MY_Controller
{

    /**
     * Controlador de Solicitantes
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Solicitantes_model');
        $this->load->model('defunciones/Operaciones_model');
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
                array('label' => 'DNI', 'data' => 'dni', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 15),
                array('label' => 'Domicilio', 'data' => 'domicilio', 'width' => 24),
                array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => 'Email', 'data' => 'email', 'width' => 14),
                array('label' => 'Domicilio Alt.', 'data' => 'domicilio_alt', 'width' => 22),
                array('label' => 'Teléfono Alt.', 'data' => 'telefono_alt', 'width' => 7, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'solicitantes_table',
            'source_url' => 'defunciones/solicitantes/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_solicitantes_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Solicitantes';
        $data['title'] = TITLE . ' - Solicitantes';
        $this->load_template('defunciones/solicitantes/solicitantes_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, nombre, dni, domicilio, telefono, email, domicilio_alt, telefono_alt')
                ->from('df_solicitantes')
                ->add_column('ver', '<a href="defunciones/solicitantes/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/solicitantes/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="defunciones/solicitantes/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar($tipo_operacion = NULL, $difunto_id = NULL, $nuevo_tramite = 0)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('defunciones/solicitantes/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Solicitantes_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Solicitantes_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'dni' => $this->input->post('dni'),
                'domicilio' => $this->input->post('domicilio'),
                'telefono' => $this->input->post('telefono'),
                'email' => $this->input->post('email'),
                'domicilio_alt' => $this->input->post('domicilio_alt'),
                'telefono_alt' => $this->input->post('telefono_alt')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Solicitantes_model->get_msg());
                $solicitante_id = $this->Solicitantes_model->get_row_id();
                if (!empty($tipo_operacion))
                {
                    if ($tipo_operacion === 'concesiones')
                    {
                        if (empty($difunto_id))
                        {
                            redirect("defunciones/difuntos/agregar/$solicitante_id/concesiones/$nuevo_tramite", 'refresh');
                        }
                        else
                        {
                            redirect("defunciones/concesiones/agregar/$solicitante_id/$difunto_id/$nuevo_tramite", 'refresh');
                        }
                    }
                    else
                    {
                        if (empty($difunto_id))
                        {
                            redirect("defunciones/difuntos/agregar/$solicitante_id/$tipo_operacion", 'refresh');
                        }
                        else
                        {
                            redirect("defunciones/$tipo_operacion/agregar/$solicitante_id/$difunto_id", 'refresh');
                        }
                    }
                }
                else
                {
                    redirect('defunciones/solicitantes/listar', 'refresh');
                }
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Solicitantes_model->get_error())
                {
                    $error_msg .= $this->Solicitantes_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Solicitantes_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Solicitante';
        $data['title'] = TITLE . ' - Agregar Solicitante';
        $this->load_template('defunciones/solicitantes/solicitantes_abm', $data);
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
            redirect("defunciones/solicitantes/ver/$id", 'refresh');
        }

        $solicitant = $this->Solicitantes_model->get(array('id' => $id));
        if (empty($solicitant))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Solicitantes_model);
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
                $trans_ok &= $this->Solicitantes_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'dni' => $this->input->post('dni'),
                    'domicilio' => $this->input->post('domicilio'),
                    'telefono' => $this->input->post('telefono'),
                    'email' => $this->input->post('email'),
                    'domicilio_alt' => $this->input->post('domicilio_alt'),
                    'telefono_alt' => $this->input->post('telefono_alt')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Solicitantes_model->get_msg());
                    redirect('defunciones/solicitantes/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Solicitantes_model->get_error())
                    {
                        $error_msg .= $this->Solicitantes_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $operaciones = $this->Operaciones_model->get(array(
            'solicitante_id' => $solicitant->id,
            'join' => array(array(
                    'table' => 'df_difuntos',
                    'where' => 'df_difuntos.id=df_operaciones.difunto_id',
                    'columnas' => array('df_difuntos.nombre as difunto_nombre', 'df_difuntos.apellido as difunto_apellido')),
                array(
                    'type' => 'left',
                    'table' => 'df_expedientes',
                    'where' => 'df_expedientes.id=df_operaciones.expediente_id',
                    'columnas' => array("CONCAT(df_expedientes.numero, '/', df_expedientes.ejercicio) as expediente"))
            ),
            'sort_by' => 'fecha_tramite',
            'sort_direction' => 'desc'));

        $data['fields'] = $this->build_fields($this->Solicitantes_model->fields, $solicitant);
        $data['solicitant'] = $solicitant;
        $data['operaciones'] = $operaciones;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Solicitante';
        $data['title'] = TITLE . ' - Editar Solicitante';
        $this->load_template('defunciones/solicitantes/solicitantes_abm', $data);
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
            redirect("defunciones/solicitantes/ver/$id", 'refresh');
        }

        $solicitant = $this->Solicitantes_model->get_one($id);
        if (empty($solicitant))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Solicitantes_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Solicitantes_model->get_msg());
                redirect('defunciones/solicitantes/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Solicitantes_model->get_error())
                {
                    $error_msg .= $this->Solicitantes_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $operaciones = $this->Operaciones_model->get(array(
            'solicitante_id' => $solicitant->id,
            'join' => array(array(
                    'table' => 'df_difuntos',
                    'where' => 'df_difuntos.id=df_operaciones.difunto_id',
                    'columnas' => array('df_difuntos.nombre as difunto_nombre', 'df_difuntos.apellido as difunto_apellido')),
                array(
                    'type' => 'left',
                    'table' => 'df_expedientes',
                    'where' => 'df_expedientes.id=df_operaciones.expediente_id',
                    'columnas' => array("CONCAT(df_expedientes.numero, '/', df_expedientes.ejercicio) as expediente"))
            ),
            'sort_by' => 'fecha_tramite',
            'sort_direction' => 'desc'));

        $data['fields'] = $this->build_fields($this->Solicitantes_model->fields, $solicitant, TRUE);
        $data['solicitant'] = $solicitant;
        $data['operaciones'] = $operaciones;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Solicitante';
        $data['title'] = TITLE . ' - Eliminar Solicitante';
        $this->load_template('defunciones/solicitantes/solicitantes_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $solicitant = $this->Solicitantes_model->get_one($id);
        if (empty($solicitant))
        {
            show_error('No se encontró el Solicitante', 500, 'Registro no encontrado');
        }

        $operaciones = $this->Operaciones_model->get(array(
            'solicitante_id' => $solicitant->id,
            'join' => array(array(
                    'table' => 'df_difuntos',
                    'where' => 'df_difuntos.id=df_operaciones.difunto_id',
                    'columnas' => array('df_difuntos.nombre as difunto_nombre', 'df_difuntos.apellido as difunto_apellido')),
                array(
                    'type' => 'left',
                    'table' => 'df_expedientes',
                    'where' => 'df_expedientes.id=df_operaciones.expediente_id',
                    'columnas' => array("CONCAT(df_expedientes.numero, '/', df_expedientes.ejercicio) as expediente"))
            ),
            'sort_by' => 'fecha_tramite',
            'sort_direction' => 'desc'));

        $data['fields'] = $this->build_fields($this->Solicitantes_model->fields, $solicitant, TRUE);
        $data['solicitant'] = $solicitant;
        $data['operaciones'] = $operaciones;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Solicitante';
        $data['title'] = TITLE . ' - Ver Solicitante';
        $this->load_template('defunciones/solicitantes/solicitantes_abm', $data);
    }
}
