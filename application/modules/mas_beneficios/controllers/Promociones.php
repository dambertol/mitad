<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Promociones extends MY_Controller
{

    /**
     * Controlador de Promociones
     * Autor: Leandro
     * Creado: 20/07/2020
     * Modificado: 05/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mas_beneficios/Promociones_model');
        $this->load->model('mas_beneficios/Comercios_model');
        $this->load->model('mas_beneficios/Campanias_model');
        $this->grupos_admin = array('admin', 'mas_beneficios_control', 'mas_beneficios_consulta_general');
        $this->grupos_control = array('admin', 'mas_beneficios_control');
        $this->grupos_permitidos = array('admin', 'mas_beneficios_control', 'mas_beneficios_publico', 'mas_beneficios_consulta_general');
        $this->grupos_solo_consulta = array('mas_beneficios_consulta_general');
        $this->agrupamiento_id_comercio = '1';
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
                array('label' => 'Campaña', 'data' => 'campania', 'width' => 20),
                array('label' => 'Comercio', 'data' => 'comercio', 'width' => 22),
                array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 40),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'promociones_table',
            'source_url' => 'mas_beneficios/promociones/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_promociones_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $tableData['columns'][] = array('label' => '', 'data' => 'aprobar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false');
        }

        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['agregar'] = 'agregar';
        if (in_groups($this->grupos_control, $this->grupos))
        {
            $data['agregar'] = 'agregar_admin';
        }
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Promociones';
        $data['title'] = TITLE . ' - Promociones';
        $this->load_template('mas_beneficios/promociones/promociones_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('mas_beneficios/datatables_functions_helper');
        $dt = $this->datatables
                ->select('ta_promociones.id, ta_campanias.nombre as campania, ta_comercios.nombre as comercio, ta_promociones.descripcion, ta_promociones.estado')
                ->from('ta_promociones')
                ->join('ta_campanias', 'ta_campanias.id = ta_promociones.campania_id', 'left')
                ->join('ta_comercios', 'ta_comercios.id = ta_promociones.comercio_id', 'left')
                ->where("ta_comercios.id IN (SELECT ta_comercios.id  
                    FROM ta_comercios 
                    LEFT JOIN ta_comercios_categorias ON ta_comercios_categorias.comercio_id = ta_comercios.id
                    LEFT JOIN ta_categorias ON ta_comercios_categorias.categoria_id = ta_categorias.id
                    WHERE ta_categorias.agrupamiento_id = $this->agrupamiento_id_comercio)");

        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            $dt->where('ta_comercios.encargado_id', $this->session->userdata('persona_id'));
        }

        $dt->edit_column('estado', '$1', 'dt_column_promociones_estado(estado)', TRUE)
                ->add_column('ver', '<a href="mas_beneficios/promociones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="mas_beneficios/promociones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('anular', '<a href="mas_beneficios/promociones/anular/$1" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>', 'id')
                ->add_column('eliminar', '<a href="mas_beneficios/promociones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $dt->add_column('aprobar', '$1', 'dt_column_promociones_aprobar(estado, id)');
        }

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
            redirect('mas_beneficios/promociones/listar', 'refresh');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            redirect('mas_beneficios/promociones/agregar_admin', 'refresh');
        }

        $this->array_comercio_control = $array_comercio = $this->get_array('Comercios', 'nombre', 'id', array(
            'select' => 'ta_comercios.id, ta_comercios.nombre',
            'join' => array(
                array('personas', 'personas.id = ta_comercios.encargado_id', 'left'),
            ),
            'where' => array(
                array('column' => 'personas.id', 'value' => $this->session->userdata('persona_id')),
                array('column' => 'ta_comercios.id IN',
                    'value' => "(SELECT ta_comercios.id  
                    FROM ta_comercios 
                    LEFT JOIN ta_comercios_categorias ON ta_comercios_categorias.comercio_id = ta_comercios.id
                    LEFT JOIN ta_categorias ON ta_comercios_categorias.categoria_id = ta_categorias.id
                    WHERE ta_categorias.agrupamiento_id = $this->agrupamiento_id_comercio)",
                    'override' => TRUE
                ),
            ),
            'sort_by' => 'ta_comercios.nombre'
        ));
        $this->array_campania_control = $array_campania = $this->get_array('Campanias', 'nombre', 'id', array(
            'select' => 'ta_campanias.id, ta_campanias.nombre',
            'where' => array(
                array('column' => 'ta_campanias.activo', 'value' => 'SI'),
                array('column' => 'ta_campanias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio),
            ),
            'sort_by' => 'ta_campanias.nombre'
        ));

        $this->set_model_validation_rules($this->Promociones_model);
        $error_msg = FALSE;
        $error_msg_file = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            if (!empty($_FILES['imagen_url']['name']))
            {
                $this->load->library('upload');
                $config['upload_path'] = "uploads/mas_beneficios/promociones/";
                if (!file_exists($config['upload_path']))
                {
                    mkdir($config['upload_path'], 0755, TRUE);
                }
                $config['file_ext_tolower'] = TRUE;
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['max_size'] = 256;
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);
                if (!$this->upload->do_upload('imagen_url'))
                {
                    $error_msg_file = $this->upload->display_errors();
                    $trans_ok = FALSE;
                }
                else
                {
                    $upload = $this->upload->data();

                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $upload['full_path'];
                    $config['maintain_ratio'] = FALSE;
                    $config['width'] = 400;
                    $config['height'] = 320;

                    $this->load->library('image_lib', $config);

                    $this->image_lib->resize();
                }
            }

            $trans_ok &= $this->Promociones_model->create(array(
                'comercio_id' => $this->input->post('comercio'),
                'campania_id' => $this->input->post('campania'),
                'descripcion' => $this->input->post('descripcion'),
                'imagen_url' => !empty($upload) ? $config['upload_path'] . $upload['file_name'] : 'NULL'), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Promociones_model->get_msg());
                redirect('mas_beneficios/promociones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                if (!empty($upload))
                {
                    unlink($config['upload_path'] . $upload['file_name']);
                }
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Promociones_model->get_error())
                {
                    $error_msg .= $this->Promociones_model->get_error();
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Promociones_model->fields['comercio']['array'] = $array_comercio;
        $this->Promociones_model->fields['campania']['array'] = $array_campania;
        $data['fields'] = $this->build_fields($this->Promociones_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Promoción';
        $data['title'] = TITLE . ' - Agregar Promoción';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.min.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.min.js';
        $this->load_template('mas_beneficios/promociones/promociones_abm', $data);
    }

    public function agregar_admin()
    {
        if (!in_groups($this->grupos_control, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect('mas_beneficios/promociones/listar', 'refresh');
        }

        $this->array_comercio_control = $array_comercio = $this->get_array('Comercios', 'nombre', 'id', array(
            'select' => 'ta_comercios.id, ta_comercios.nombre',
            'where' => array(
                array('column' => 'ta_comercios.id IN',
                    'value' => "(SELECT ta_comercios.id  
                    FROM ta_comercios 
                    LEFT JOIN ta_comercios_categorias ON ta_comercios_categorias.comercio_id = ta_comercios.id
                    LEFT JOIN ta_categorias ON ta_comercios_categorias.categoria_id = ta_categorias.id
                    WHERE ta_categorias.agrupamiento_id = $this->agrupamiento_id_comercio)",
                    'override' => TRUE
                ),
            ),
            'sort_by' => 'ta_comercios.nombre'
        ));
        $this->array_campania_control = $array_campania = $this->get_array('Campanias', 'nombre', 'id', array(
            'select' => 'ta_campanias.id, ta_campanias.nombre',
            'where' => array(
                array('column' => 'ta_campanias.activo', 'value' => 'SI'),
                array('column' => 'ta_campanias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio),
            ),
            'sort_by' => 'ta_campanias.nombre'
        ));

        $this->set_model_validation_rules($this->Promociones_model);
        $error_msg = FALSE;
        $error_msg_file = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            if (!empty($_FILES['imagen_url']['name']))
            {
                $this->load->library('upload');
                $config['upload_path'] = "uploads/mas_beneficios/promociones/";
                if (!file_exists($config['upload_path']))
                {
                    mkdir($config['upload_path'], 0755, TRUE);
                }
                $config['file_ext_tolower'] = TRUE;
                $config['allowed_types'] = 'jpg|jpeg|png';
                $config['max_size'] = 256;
                $config['encrypt_name'] = TRUE;

                $this->upload->initialize($config);
                if (!$this->upload->do_upload('imagen_url'))
                {
                    $error_msg_file = $this->upload->display_errors();
                    $trans_ok = FALSE;
                }
                else
                {
                    $upload = $this->upload->data();

                    $config['image_library'] = 'gd2';
                    $config['source_image'] = $upload['full_path'];
                    $config['maintain_ratio'] = FALSE;
                    $config['width'] = 400;
                    $config['height'] = 320;

                    $this->load->library('image_lib', $config);

                    $this->image_lib->resize();
                }
            }

            $trans_ok &= $this->Promociones_model->create(array(
                'comercio_id' => $this->input->post('comercio'),
                'campania_id' => $this->input->post('campania'),
                'descripcion' => $this->input->post('descripcion'),
                'imagen_url' => !empty($upload) ? $config['upload_path'] . $upload['file_name'] : 'NULL'), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Promociones_model->get_msg());
                redirect('mas_beneficios/promociones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                if (!empty($upload))
                {
                    unlink($config['upload_path'] . $upload['file_name']);
                }
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Promociones_model->get_error())
                {
                    $error_msg .= $this->Promociones_model->get_error();
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Promociones_model->fields['comercio']['array'] = $array_comercio;
        $this->Promociones_model->fields['campania']['array'] = $array_campania;
        $data['fields'] = $this->build_fields($this->Promociones_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Promoción';
        $data['title'] = TITLE . ' - Agregar Promoción';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.min.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.min.js';
        $this->load_template('mas_beneficios/promociones/promociones_abm', $data);
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
            redirect("mas_beneficios/promociones/ver/$id", 'refresh');
        }


        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $this->array_comercio_control = $array_comercio = $this->get_array('Comercios', 'nombre', 'id', array(
                'select' => 'ta_comercios.id, ta_comercios.nombre',
                'where' => array(
                    array('column' => 'ta_comercios.id IN',
                        'value' => "(SELECT ta_comercios.id  
                    FROM ta_comercios 
                    LEFT JOIN ta_comercios_categorias ON ta_comercios_categorias.comercio_id = ta_comercios.id
                    LEFT JOIN ta_categorias ON ta_comercios_categorias.categoria_id = ta_categorias.id
                    WHERE ta_categorias.agrupamiento_id = $this->agrupamiento_id_comercio)",
                        'override' => TRUE
                    ),
                ),
                'sort_by' => 'ta_comercios.nombre'
            ));
        }
        else
        {
            $this->array_comercio_control = $array_comercio = $this->get_array('Comercios', 'nombre', 'id', array(
                'select' => 'ta_comercios.id, ta_comercios.nombre',
                'join' => array(
                    array('personas', 'personas.id = ta_comercios.encargado_id', 'left'),
                ),
                'where' => array(
                    array('column' => 'personas.id', 'value' => $this->session->userdata('persona_id')),
                    array('column' => 'ta_comercios.id IN',
                        'value' => "(SELECT ta_comercios.id  
                    FROM ta_comercios 
                    LEFT JOIN ta_comercios_categorias ON ta_comercios_categorias.comercio_id = ta_comercios.id
                    LEFT JOIN ta_categorias ON ta_comercios_categorias.categoria_id = ta_categorias.id
                    WHERE ta_categorias.agrupamiento_id = $this->agrupamiento_id_comercio)",
                        'override' => TRUE
                    ),
                ),
                'sort_by' => 'ta_comercios.nombre'
            ));
        }
        $this->array_campania_control = $array_campania = $this->get_array('Campanias', 'nombre', 'id', array(
            'select' => 'ta_campanias.id, ta_campanias.nombre',
            'where' => array(
                // array('column' => 'ta_campanias.activa', 'value' => 'SI'),
                array('column' => 'ta_campanias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio),
            ),
            'sort_by' => 'ta_campanias.nombre'
        ));

        $promocion = $this->Promociones_model->get_one($id);
        if (empty($promocion) || $promocion->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Promoción', 500, 'Registro no encontrado');
        }

        if (!in_groups($this->grupos_control, $this->grupos) && $promocion->encargado_id !== $this->session->userdata('persona_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->set_model_validation_rules($this->Promociones_model);
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $error_msg = FALSE;
            $error_msg_file = FALSE;
            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                if (!empty($_FILES['imagen_url']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = "uploads/mas_beneficios/promociones/";
                    if (!file_exists($config['upload_path']))
                    {
                        mkdir($config['upload_path'], 0755, TRUE);
                    }
                    $config['file_ext_tolower'] = TRUE;
                    $config['allowed_types'] = 'jpg|jpeg|png';
                    $config['max_size'] = 256;
                    $config['encrypt_name'] = TRUE;

                    $this->upload->initialize($config);
                    if (!$this->upload->do_upload('imagen_url'))
                    {
                        $error_msg_file = $this->upload->display_errors();
                        $trans_ok = FALSE;
                    }
                    else
                    {
                        $upload = $this->upload->data();

                        $config['image_library'] = 'gd2';
                        $config['source_image'] = $upload['full_path'];
                        $config['maintain_ratio'] = FALSE;
                        $config['width'] = 400;
                        $config['height'] = 320;

                        $this->load->library('image_lib', $config);

                        $this->image_lib->resize();
                    }
                }

                $trans_ok &= $this->Promociones_model->update(array(
                    'id' => $this->input->post('id'),
                    'comercio_id' => $this->input->post('comercio'),
                    'campania_id' => $this->input->post('campania'),
                    'descripcion' => $this->input->post('descripcion'),
                    'imagen_url' => !empty($upload) ? $config['upload_path'] . $upload['file_name'] : $comercio->imagen_url,
                    'estado' => 'Pendiente'), FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    if (!empty($upload) && !empty($promocion->imagen_url))
                    {
                        unlink($promocion->imagen_url);
                    }
                    $this->session->set_flashdata('message', $this->Promociones_model->get_msg());
                    redirect('mas_beneficios/promociones/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    if (!empty($upload))
                    {
                        unlink($config['upload_path'] . $upload['file_name']);
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Promociones_model->get_error())
                    {
                        $error_msg .= $this->Promociones_model->get_error();
                    }
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Promociones_model->fields['comercio']['array'] = $array_comercio;
        $this->Promociones_model->fields['campania']['array'] = $array_campania;
        $this->Promociones_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Promociones_model->fields, $promocion);
        $data['promocion'] = $promocion;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Promoción';
        $data['title'] = TITLE . ' - Editar Promoción';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.min.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.min.js';
        $this->load_template('mas_beneficios/promociones/promociones_abm', $data);
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
            redirect("mas_beneficios/promociones/ver/$id", 'refresh');
        }

        $promocion = $this->Promociones_model->get_one($id);
        if (empty($promocion) || $promocion->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Promoción', 500, 'Registro no encontrado');
        }

        if (!in_groups($this->grupos_control, $this->grupos) && $promocion->encargado_id !== $this->session->userdata('persona_id'))
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
            $trans_ok &= $this->Promociones_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                if (!empty($promocion->imagen_url))
                {
                    unlink($promocion->imagen_url);
                }
                $this->session->set_flashdata('message', $this->Promociones_model->get_msg());
                redirect('mas_beneficios/promociones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Promociones_model->get_error())
                {
                    $error_msg .= $this->Promociones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
        $this->Promociones_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Promociones_model->fields, $promocion, TRUE);
        $data['promocion'] = $promocion;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Promoción';
        $data['title'] = TITLE . ' - Eliminar Promoción';
        $this->load_template('mas_beneficios/promociones/promociones_abm', $data);
    }

    public function anular($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("mas_beneficios/promociones/ver/$id", 'refresh');
        }

        $promocion = $this->Promociones_model->get_one($id);
        if (empty($promocion) || $promocion->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Promoción', 500, 'Registro no encontrado');
        }

        if (!in_groups($this->grupos_control, $this->grupos) && $promocion->encargado_id !== $this->session->userdata('persona_id'))
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
            $trans_ok &= $this->Promociones_model->update(array(
                'id' => $this->input->post('id'),
                'estado' => 'Anulado'), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Promociones_model->get_msg());
                redirect('mas_beneficios/promociones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Promociones_model->get_error())
                {
                    $error_msg .= $this->Promociones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Promociones_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Promociones_model->fields, $promocion, TRUE);
        $data['promocion'] = $promocion;
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular Promoción';
        $data['title'] = TITLE . ' - Anular Promoción';
        $this->load_template('mas_beneficios/promociones/promociones_abm', $data);
    }

    public function aprobar($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos) && !in_groups($this->grupos_control, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("mas_beneficios/promociones/ver/$id", 'refresh');
        }

        $promocion = $this->Promociones_model->get_one($id);
        if (empty($promocion) || $promocion->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Promoción', 500, 'Registro no encontrado');
        }

        if (!in_groups($this->grupos_control, $this->grupos) && $promocion->encargado_id !== $this->session->userdata('persona_id'))
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
            $trans_ok &= $this->Promociones_model->update(array(
                'id' => $this->input->post('id'),
                'estado' => 'Aprobado'), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Promociones_model->get_msg());
                redirect('mas_beneficios/promociones/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Promociones_model->get_error())
                {
                    $error_msg .= $this->Promociones_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Promociones_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Promociones_model->fields, $promocion, TRUE);
        $data['promocion'] = $promocion;
        $data['txt_btn'] = 'Aprobar';
        $data['title_view'] = 'Aprobar Promoción';
        $data['title'] = TITLE . ' - Aprobar Promoción';
        $this->load_template('mas_beneficios/promociones/promociones_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $promocion = $this->Promociones_model->get_one($id);
        if (empty($promocion) || $promocion->agrupamiento_id !== $this->agrupamiento_id_comercio)
        {
            show_error('No se encontró la Promoción', 500, 'Registro no encontrado');
        }

        if (!in_groups($this->grupos_control, $this->grupos) && $promocion->encargado_id !== $this->session->userdata('persona_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->Promociones_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Promociones_model->fields, $promocion, TRUE);
        $data['promocion'] = $promocion;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Promoción';
        $data['title'] = TITLE . ' - Ver Promoción';
        $this->load_template('mas_beneficios/promociones/promociones_abm', $data);
    }
}
