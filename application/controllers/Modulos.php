<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Modulos extends MY_Controller
{

    /**
     * Controlador de Modulos
     * Autor: Leandro
     * Creado: 17/03/2017
     * Modificado: 04/01/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Modulos_model');
        $this->grupos_permitidos = array('admin');
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
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 41, 'responsive_class' => 'all'),
                array('label' => 'Límite Selección', 'data' => 'limite_seleccion', 'width' => 15),
                array('label' => 'Ícono', 'data' => 'icono', 'width' => 25),
                array('label' => 'Grupos', 'data' => 'grupos', 'width' => 10, 'class' => 'dt-body-right'),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'modulos_table',
            'source_url' => 'modulos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_modulos_table",
            'footer' => TRUE,
            'dom' => 't<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de módulos';
        $data['title'] = TITLE . ' - Módulos';
        $this->load_template('modulos/modulos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('datatables_functions_helper');
        $this->datatables
                ->select('id, nombre, limite_seleccion, icono, (SELECT COUNT(*) FROM `groups` WHERE modulo_id = modulos.id) as grupos')
                ->unset_column('id')
                ->custom_sort('grupos', '(SELECT COUNT(*) FROM `groups` WHERE modulo_id = modulos.id)')
                ->from('modulos')
                ->add_column('grupos', '$1', 'dt_column_modulos_grupos(grupos)', TRUE)
                ->add_column('ver', '<a href="modulos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="modulos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('eliminar', '<a href="modulos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->set_model_validation_rules($this->Modulos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $ok = $this->Modulos_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'limite_seleccion' => $this->input->post('limite_seleccion'),
                'icono' => $this->input->post('icono'))
            );
            if ($ok)
            {
                $this->session->set_flashdata('message', $this->Modulos_model->get_msg());
                redirect('modulos/listar', 'refresh');
            }
            else
            {
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Modulos_model->get_error())
                {
                    $error_msg .= $this->Modulos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Modulos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar módulo';
        $data['title'] = TITLE . ' - Agregar módulo';
        $this->load_template('modulos/modulos_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $modulo = $this->Modulos_model->get(array('id' => $id));
        if (empty($modulo))
        {
            show_error('No se encontró el módulo', 500, 'Registro no encontrado');
        }

        $this->set_model_validation_rules($this->Modulos_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id !== $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $ok = $this->Modulos_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'limite_seleccion' => $this->input->post('limite_seleccion'),
                    'icono' => $this->input->post('icono'))
                );
                if ($ok)
                {
                    $this->session->set_flashdata('message', $this->Modulos_model->get_msg());
                    redirect('modulos/listar', 'refresh');
                }
                else
                {
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Modulos_model->get_error())
                    {
                        $error_msg .= $this->Modulos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Modulos_model->fields, $modulo);
        $data['modulo'] = $modulo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar módulo';
        $data['title'] = TITLE . ' - Editar módulo';
        $this->load_template('modulos/modulos_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $modulo = $this->Modulos_model->get(array('id' => $id));
        if (empty($modulo))
        {
            show_error('No se encontró el módulo', 500, 'Registro no encontrado');
        }

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $ok = $this->Modulos_model->delete(array('id' => $this->input->post('id')));
            if ($ok)
            {
                $this->session->set_flashdata('message', $this->Modulos_model->get_msg());
                redirect('modulos/listar', 'refresh');
            }
            else
            {
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Modulos_model->get_error())
                {
                    $error_msg .= $this->Modulos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Modulos_model->fields, $modulo, TRUE);
        $data['modulo'] = $modulo;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar módulo';
        $data['title'] = TITLE . ' - Eliminar módulo';
        $this->load_template('modulos/modulos_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $modulo = $this->Modulos_model->get(array('id' => $id));
        if (empty($modulo))
        {
            show_error('No se encontró el módulo', 500, 'Registro no encontrado');
        }

        $data['fields'] = $this->build_fields($this->Modulos_model->fields, $modulo, TRUE);
        $data['modulo'] = $modulo;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver módulo';
        $data['title'] = TITLE . ' - Ver módulo';
        $this->load_template('modulos/modulos_abm', $data);
    }
}
