<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Constructores extends MY_Controller
{

    /**
     * Controlador de Constructores
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Constructores_model');
        $this->load->model('defunciones/Permisos_constructores_model');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 26),
                array('label' => 'DNI', 'data' => 'dni', 'width' => 12, 'class' => 'dt-body-right'),
                array('label' => 'Domicilio', 'data' => 'domicilio', 'width' => 30),
                array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 14, 'class' => 'dt-body-right'),
                array('label' => 'Venc. Permiso', 'data' => 'vencimiento_permiso', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'renovar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'constructores_table',
            'source_url' => 'defunciones/constructores/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_constructores_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Constructores';
        $data['title'] = TITLE . ' - Constructores';
        $this->load_template('defunciones/constructores/constructores_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('df_constructores.id, df_constructores.nombre, df_constructores.dni, df_constructores.domicilio, df_constructores.telefono, df_permisos_constructores.vencimiento as vencimiento_permiso, df_constructores.ultimo_permiso_id')
                ->from('df_constructores')
                ->join('df_permisos_constructores', 'df_constructores.ultimo_permiso_id = df_permisos_constructores.id', 'left')
                ->add_column('ver', '<a href="defunciones/constructores/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="defunciones/constructores/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('renovar', '<a href="defunciones/constructores/renovar_permiso/$1" title="Renovar" class="btn btn-primary btn-xs"><i class="fa fa-history"></i></a>', 'ultimo_permiso_id')
                ->add_column('eliminar', '<a href="defunciones/constructores/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

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
            redirect('defunciones/constructores/listar', 'refresh');
        }

        $this->set_model_validation_rules($this->Constructores_model);
        $this->set_model_validation_rules($this->Permisos_constructores_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Constructores_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'dni' => $this->input->post('dni'),
                'domicilio' => $this->input->post('domicilio'),
                'telefono' => $this->input->post('telefono')), FALSE);

            $constructor_id = $this->Constructores_model->get_row_id();

            $trans_ok &= $this->Permisos_constructores_model->create(array(
                'constructor_id' => $constructor_id,
                'fecha_pago' => $this->get_datetime_sql('fecha_pago'),
                'boleta_pago' => $this->input->post('boleta_pago'),
                'vencimiento' => $this->get_date_sql('vencimiento')), FALSE);

            $permiso_id = $this->Permisos_constructores_model->get_row_id();

            $trans_ok &= $this->Constructores_model->update(array('id' => $constructor_id, 'ultimo_permiso_id' => $permiso_id), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Constructores_model->get_msg());
                redirect('defunciones/constructores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Constructores_model->get_error())
                {
                    $error_msg .= $this->Constructores_model->get_error();
                }
                if ($this->Permisos_constructores_model->get_error())
                {
                    $error_msg .= $this->Permisos_constructores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $data['fields'] = $this->build_fields($this->Constructores_model->fields);
        $data['fields_permiso'] = $this->build_fields($this->Permisos_constructores_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Constructor';
        $data['title'] = TITLE . ' - Agregar Constructor';
        $this->load_template('defunciones/constructores/constructores_abm', $data);
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
            redirect("defunciones/constructores/ver/$id", 'refresh');
        }

        $constructor = $this->Constructores_model->get(array('id' => $id));
        if (empty($constructor))
        {
            show_error('No se encontró el Constructor', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Constructores_model);
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
                $trans_ok &= $this->Constructores_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'dni' => $this->input->post('dni'),
                    'domicilio' => $this->input->post('domicilio'),
                    'telefono' => $this->input->post('telefono')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Constructores_model->get_msg());
                    redirect('defunciones/constructores/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Constructores_model->get_error())
                    {
                        $error_msg .= $this->Constructores_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $permisos = $this->Permisos_constructores_model->get(array('constructor_id' => $constructor->id, 'sort_direction' => 'desc'));

        $data['fields'] = $this->build_fields($this->Constructores_model->fields, $constructor);
        $data['constructor'] = $constructor;
        $data['permisos'] = $permisos;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Constructor';
        $data['title'] = TITLE . ' - Editar Constructor';
        $this->load_template('defunciones/constructores/constructores_abm', $data);
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
            redirect("defunciones/constructores/ver/$id", 'refresh');
        }

        $constructor = $this->Constructores_model->get_one($id);
        if (empty($constructor))
        {
            show_error('No se encontró el Constructor', 500, 'Registro no encontrado');
        }

        $permisos = $this->Permisos_constructores_model->get(array('constructor_id' => $constructor->id, 'sort_direction' => 'desc'));

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;

            $trans_ok &= $this->Constructores_model->update(array('id' => $id, 'ultimo_permiso_id' => 'NULL'), FALSE);

            if (!empty($permisos))
            {
                foreach ($permisos as $Permiso)
                {
                    $trans_ok &= $this->Permisos_constructores_model->delete(array('id' => $Permiso->id), FALSE);
                }
            }

            $trans_ok &= $this->Constructores_model->delete(array('id' => $this->input->post('id')), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Constructores_model->get_msg());
                redirect('defunciones/constructores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Constructores_model->get_error())
                {
                    $error_msg .= $this->Constructores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Constructores_model->fields, $constructor, TRUE);
        $data['constructor'] = $constructor;
        $data['permisos'] = $permisos;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Constructor';
        $data['title'] = TITLE . ' - Eliminar Constructor';
        $this->load_template('defunciones/constructores/constructores_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $constructor = $this->Constructores_model->get_one($id);
        if (empty($constructor))
        {
            show_error('No se encontró el Constructor', 500, 'Registro no encontrado');
        }

        $permisos = $this->Permisos_constructores_model->get(array('constructor_id' => $constructor->id, 'sort_direction' => 'desc'));

        $data['fields'] = $this->build_fields($this->Constructores_model->fields, $constructor, TRUE);
        $data['constructor'] = $constructor;
        $data['permisos'] = $permisos;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Constructor';
        $data['title'] = TITLE . ' - Ver Constructor';
        $this->load_template('defunciones/constructores/constructores_abm', $data);
    }

    public function renovar_permiso($ultimo_permiso_id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $ultimo_permiso_id == NULL || !ctype_digit($ultimo_permiso_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("defunciones/constructores/ver/$id", 'refresh');
        }

        $ultimo_permiso = $this->Permisos_constructores_model->get(array(
            'id' => $ultimo_permiso_id,
            'join' => array(array(
                    'table' => 'df_constructores',
                    'where' => 'df_constructores.id=df_permisos_constructores.constructor_id',
                    'columnas' => array("CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', df_constructores.nombre) as constructor")
                ))
        ));
        if (empty($ultimo_permiso))
        {
            show_error('No se encontró el Último Permiso', 500, 'Registro no encontrado');
        }

        $constructor = $this->Constructores_model->get_one($ultimo_permiso->constructor_id);
        if (empty($constructor))
        {
            show_error('No se encontró el Constructor', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Permisos_constructores_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Permisos_constructores_model->create(array(
                'constructor_id' => $ultimo_permiso->constructor_id,
                'fecha_pago' => $this->get_datetime_sql('fecha_pago'),
                'boleta_pago' => $this->input->post('boleta_pago'),
                'vencimiento' => $this->get_date_sql('vencimiento')), FALSE);

            $permiso_id = $this->Permisos_constructores_model->get_row_id();

            $trans_ok &= $this->Constructores_model->update(array('id' => $ultimo_permiso->constructor_id, 'ultimo_permiso_id' => $permiso_id), FALSE);

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Constructores_model->get_msg());
                redirect('defunciones/constructores/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Constructores_model->get_error())
                {
                    $error_msg .= $this->Constructores_model->get_error();
                }
                if ($this->Permisos_constructores_model->get_error())
                {
                    $error_msg .= $this->Permisos_constructores_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $permisos = $this->Permisos_constructores_model->get(array('constructor_id' => $constructor->id, 'sort_direction' => 'desc'));

        $data['fields'] = $this->build_fields($this->Constructores_model->fields, $constructor, TRUE);
        $data['fields_permiso'] = $this->build_fields($this->Permisos_constructores_model->fields);
        $data['permisos'] = $permisos;
        $data['txt_btn'] = 'Renovar';
        $data['title_view'] = 'Renovar Permiso';
        $data['title'] = TITLE . ' - Renovar Permiso';
        $this->load_template('defunciones/constructores/constructores_abm', $data);
    }
}
