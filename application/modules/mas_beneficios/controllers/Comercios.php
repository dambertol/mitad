<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Comercios extends MY_Controller
{

    /**
     * Controlador de Comercios
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 07/01/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('mas_beneficios/Categorias_model');
        $this->load->model('mas_beneficios/Comercios_model');
        $this->load->model('mas_beneficios/Comercios_categorias_model');
        $this->load->model('Localidades_model');
        $this->load->model('Personas_model');
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
                array('label' => 'Nombre de Fantasía', 'data' => 'nombre', 'width' => 20),
                array('label' => 'Razón Social', 'data' => 'razon_social', 'width' => 15),
                array('label' => 'Padrón M', 'data' => 'padron', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Padrón C', 'data' => 'padron_c', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Encargado', 'data' => 'encargado', 'width' => 12),
                array('label' => 'Localidad', 'data' => 'localidad', 'width' => 8),
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 11),
                array('label' => 'Estado', 'data' => 'estado', 'width' => 8),
                array('label' => '', 'data' => 'ver', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'anular', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 2, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'comercios_table',
            'source_url' => 'mas_beneficios/comercios/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_comercios_table",
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
        $data['title_view'] = 'Listado de Comercios';
        $data['title'] = TITLE . ' - Comercios';
        $this->load_template('mas_beneficios/comercios/comercios_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('mas_beneficios/datatables_functions_helper');
        $dt = $this->datatables
                ->select("ta_comercios.id, ta_comercios.nombre, ta_comercios.razon_social, ta_comercios.padron, ta_comercios.padron_c, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as encargado, localidades.nombre as localidad, (SELECT GROUP_CONCAT(ta_categorias.nombre SEPARATOR ', ') FROM ta_comercios_categorias JOIN ta_categorias ON ta_categorias.id = ta_comercios_categorias.categoria_id WHERE ta_comercios_categorias.comercio_id = ta_comercios.id) AS categoria, ta_comercios.estado as estado")
                ->unset_column('id')
                ->from('ta_comercios')
                ->join('localidades', 'localidades.id = ta_comercios.localidad_id', 'left')
                ->join('personas', 'personas.id = ta_comercios.encargado_id', 'left')
                ->where("ta_comercios.id IN (SELECT ta_comercios.id  
                    FROM ta_comercios 
                    LEFT JOIN ta_comercios_categorias ON ta_comercios_categorias.comercio_id = ta_comercios.id
                    LEFT JOIN ta_categorias ON ta_comercios_categorias.categoria_id = ta_categorias.id
                    WHERE ta_categorias.agrupamiento_id = $this->agrupamiento_id_comercio)");

        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            $dt->where('ta_comercios.encargado_id', $this->session->userdata('persona_id'));
        }

        $dt->edit_column('estado', '$1', 'dt_column_comercios_estado(estado)', TRUE)
                ->add_column('ver', '<a href="mas_beneficios/comercios/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="mas_beneficios/comercios/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
                ->add_column('anular', '<a href="mas_beneficios/comercios/anular/$1" title="Anular" class="btn btn-primary btn-xs"><i class="fa fa-ban"></i></a>', 'id')
                ->add_column('eliminar', '<a href="mas_beneficios/comercios/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $dt->add_column('aprobar', '$1', 'dt_column_comercios_aprobar(estado, id)');
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
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('mas_beneficios/comercios/listar', 'refresh');
        }

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            redirect('mas_beneficios/comercios/agregar_admin', 'refresh');
        }

        $fake_model_comercio = new stdClass();
        $fake_model_comercio->fields = array(
            'nombre' => array('label' => 'Nombre de Fantasía', 'maxlength' => '100', 'required' => TRUE),
            'razon_social' => array('label' => 'Razón Social / Titular', 'maxlength' => '100', 'required' => TRUE),
            'padron' => array('label' => 'Padrón Municipal', 'type' => 'integer', 'maxlength' => '6'),
            'padron_c' => array('label' => 'Padrón Comercial', 'type' => 'integer', 'maxlength' => '6'),
            'cuit' => array('label' => 'CUIL / CUIT', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13', 'required' => TRUE),
            'calle' => array('label' => 'Calle', 'maxlength' => '100', 'required' => TRUE),
            'altura' => array('label' => 'Altura', 'maxlength' => '50', 'required' => TRUE),
            'localidad' => array('label' => 'Localidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'telefono' => array('label' => 'Teléfono', 'type' => 'integer', 'maxlength' => '13', 'required' => TRUE),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100', 'required' => TRUE),
            'web' => array('label' => "Web <br /><span class='red' style='font-size:70%;'>Ej: lujandecuyo.gob.ar</span>", 'maxlength' => '100'),
            'facebook' => array('label' => "Facebook <br /><span class='red' style='font-size:70%;'>Ej: facebook.com/munilujandecuyo</span>", 'maxlength' => '100'),
            'instagram' => array('label' => "Instagram <br /><span class='red' style='font-size:70%;'>Ej: instagram.com/lujandecuyomza</span>", 'maxlength' => '100'),
            'twitter' => array('label' => "Twitter <br /><span class='red' style='font-size:70%;'>Ej: twitter.com/MuniLujanDeCuyo</span>", 'maxlength' => '100'),
            'envio_domicilio' => array('label' => 'Envío a domicilio', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'envio_domicilio', 'required' => TRUE),
            'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'required' => TRUE),
            'imagen_url' => array('label' => "Imagen <br /><span class='red' style='font-size:70%;'>Recomendado 400px x 320px</span>", 'type' => 'file', 'maxlength' => '255'),
            'comentarios' => array('label' => 'Comentarios', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );

        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'nombre', 'id', array('departamento_id' => 345));
        $this->array_envio_domicilio_control = $array_envio_domicilio = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'nombre', 'id', array('agrupamiento_id' => $this->agrupamiento_id_comercio));
        $this->set_model_validation_rules($fake_model_comercio);
        $error_msg = FALSE;
        $error_msg_file = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $cuit = str_replace('-', '', $this->input->post('cuit'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            if (!empty($_FILES['imagen_url']['name']))
            {
                $this->load->library('upload');
                $config['upload_path'] = "uploads/mas_beneficios/comercios/";
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

            $trans_ok &= $this->Comercios_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'razon_social' => $this->input->post('razon_social'),
                'padron' => $this->input->post('padron'),
                'padron_c' => $this->input->post('padron_c'),
                'cuit' => $cuit,
                'calle' => $this->input->post('calle'),
                'altura' => $this->input->post('altura'),
                'localidad_id' => $this->input->post('localidad'),
                'telefono' => $this->input->post('telefono'),
                'email' => $this->input->post('email'),
                'web' => $this->input->post('web'),
                'facebook' => $this->input->post('facebook'),
                'instagram' => $this->input->post('instagram'),
                'twitter' => $this->input->post('twitter'),
                'envio_domicilio' => $this->input->post('envio_domicilio'),
                'encargado_id' => $this->session->userdata('persona_id'),
                'imagen_url' => !empty($upload) ? $config['upload_path'] . $upload['file_name'] : 'NULL',
                'comentarios' => $this->input->post('comentarios')), FALSE);

            $comercio_id = $this->Comercios_model->get_row_id();

            $comercios_categorias = $this->input->post('categoria');
            foreach ($comercios_categorias as $CC)
            {
                $trans_ok &= $this->Comercios_categorias_model->create(array(
                    'comercio_id' => $comercio_id,
                    'categoria_id' => $CC,
                    'principal' => 'SI'), FALSE);
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Comercios_model->get_msg());
                redirect('mas_beneficios/comercios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                if (!empty($upload))
                {
                    unlink($config['upload_path'] . $upload['file_name']);
                }
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Comercios_model->get_error())
                {
                    $error_msg .= $this->Comercios_model->get_error();
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model_comercio->fields['localidad']['array'] = $array_localidad;
        $fake_model_comercio->fields['envio_domicilio']['array'] = $array_envio_domicilio;
        $fake_model_comercio->fields['categoria']['array'] = $array_categoria;
        $data['fields'] = $this->build_fields($fake_model_comercio->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Comercio';
        $data['title'] = TITLE . ' - Agregar Comercio';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.min.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.min.js';
        $this->load_template('mas_beneficios/comercios/comercios_abm', $data);
    }

    public function agregar_admin()
    {
        if (!in_groups($this->grupos_control, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect('mas_beneficios/comercios/listar', 'refresh');
        }

        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'nombre', 'id', array('departamento_id' => 345));
        $this->array_encargado_control = $array_encargado = $this->get_array('Personas', 'encargado', 'id', array(
            'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', users.username, ')') as encargado",
            'join' => array(
                array('users', 'personas.id = users.persona_id', 'LEFT'),
                array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                array('groups', 'users_groups.group_id = groups.id', 'LEFT')
            ),
            'where' => array(
                array('column' => 'groups.name IN', 'value' => "('mas_beneficios_control', 'mas_beneficios_publico')", 'override' => TRUE),
                array('column' => 'users.active', 'value' => '1'),
            ),
            'sort_by' => 'personas.apellido, personas.nombre, users.username'
        ));

        unset($this->Comercios_model->fields['cuit']['required']);
        $this->array_envio_domicilio_control = $array_envio_domicilio = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'nombre', 'id', array('agrupamiento_id' => $this->agrupamiento_id_comercio));
        $this->set_model_validation_rules($this->Comercios_model);
        $error_msg = FALSE;
        $error_msg_file = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $cuit = str_replace('-', '', $this->input->post('cuit'));

            $this->db->trans_begin();
            $trans_ok = TRUE;
            if (!empty($_FILES['imagen_url']['name']))
            {
                $this->load->library('upload');
                $config['upload_path'] = "uploads/mas_beneficios/comercios/";
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

            $trans_ok &= $this->Comercios_model->create(array(
                'nombre' => $this->input->post('nombre'),
                'razon_social' => $this->input->post('razon_social'),
                'padron' => $this->input->post('padron'),
                'padron_c' => $this->input->post('padron_c'),
                'cuit' => $cuit,
                'calle' => $this->input->post('calle'),
                'altura' => $this->input->post('altura'),
                'localidad_id' => $this->input->post('localidad'),
                'telefono' => $this->input->post('telefono'),
                'email' => $this->input->post('email'),
                'web' => $this->input->post('web'),
                'facebook' => $this->input->post('facebook'),
                'instagram' => $this->input->post('instagram'),
                'twitter' => $this->input->post('twitter'),
                'envio_domicilio' => $this->input->post('envio_domicilio'),
                'encargado_id' => $this->input->post('encargado'),
                'imagen_url' => !empty($upload) ? $config['upload_path'] . $upload['file_name'] : 'NULL',
                'comentarios' => $this->input->post('comentarios')), FALSE);

            $comercio_id = $this->Comercios_model->get_row_id();

            $comercios_categorias = $this->input->post('categoria');
            foreach ($comercios_categorias as $CC)
            {
                $trans_ok &= $this->Comercios_categorias_model->create(array(
                    'comercio_id' => $comercio_id,
                    'categoria_id' => $CC,
                    'principal' => 'SI'), FALSE);
            }

            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Comercios_model->get_msg());
                redirect('mas_beneficios/comercios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                if (!empty($upload))
                {
                    unlink($config['upload_path'] . $upload['file_name']);
                }
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Comercios_model->get_error())
                {
                    $error_msg .= $this->Comercios_model->get_error();
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Comercios_model->fields['localidad']['array'] = $array_localidad;
        $this->Comercios_model->fields['encargado']['array'] = $array_encargado;
        $this->Comercios_model->fields['envio_domicilio']['array'] = $array_envio_domicilio;
        $this->Comercios_model->fields['categoria']['array'] = $array_categoria;
        $data['fields'] = $this->build_fields($this->Comercios_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Comercio';
        $data['title'] = TITLE . ' - Agregar Comercio';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.min.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.min.js';
        $this->load_template('mas_beneficios/comercios/comercios_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("mas_beneficios/comercios/ver/$id", 'refresh');
        }

        $this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'nombre', 'id', array('departamento_id' => 345));
        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $this->array_encargado_control = $array_encargado = $this->get_array('Personas', 'encargado', 'id', array(
                'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', users.username, ')') as encargado",
                'join' => array(
                    array('users', 'personas.id = users.persona_id', 'LEFT'),
                    array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                    array('groups', 'users_groups.group_id = groups.id', 'LEFT')
                ),
                'where' => array(
                    array('column' => 'groups.name IN', 'value' => "('mas_beneficios_control', 'mas_beneficios_publico')", 'override' => TRUE),
                    array('column' => 'users.active', 'value' => '1'),
                ),
                'sort_by' => 'personas.apellido, personas.nombre, users.username'
            ));
        }
        else
        {
            $this->array_encargado_control = $array_encargado = $this->get_array('Personas', 'encargado', 'id', array(
                'select' => "personas.id, CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.dni, ')') as encargado",
                'where' => array(
                    array('column' => 'id', 'value' => $this->session->userdata('persona_id')),
                ),
                'sort_by' => 'personas.apellido, personas.nombre, personas.dni'
            ));
        }
        $this->array_envio_domicilio_control = $array_envio_domicilio = array('SI' => 'SI', 'NO' => 'NO');
        $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'nombre', 'id', array('agrupamiento_id' => $this->agrupamiento_id_comercio));
        $comercio = $this->Comercios_model->get_one($id);
        if (empty($comercio))
        {
            show_error('No se encontró el Comercio', 500, 'Registro no encontrado');
        }
        $comercio->categoria_id = array();
        $comercios_categorias = $this->Comercios_categorias_model->get(array(
            'comercio_id' => $id,
            'join' => array(
                array('ta_categorias', 'ta_categorias.id = ta_comercios_categorias.categoria_id', 'left', 'ta_categorias.nombre as categoria, ta_categorias.agrupamiento_id as agrupamiento_id')
            ),
            'where' => array(
                array('column' => 'ta_categorias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio)
            )
        ));
        if (!empty($comercios_categorias))
        {
            foreach ($comercios_categorias as $CC)
            {
                $comercio->categoria_id[] = $CC->categoria_id;
            }
        }
        else
        {
            show_error('No se encontró la Categoría del Comercio', 500, 'Registro no encontrado');
        }

        if (!in_groups($this->grupos_admin, $this->grupos) && $comercio->encargado_id !== $this->session->userdata('persona_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }
        else
        {
            unset($this->Comercios_model->fields['cuit']['required']);
        }

        $this->set_model_validation_rules($this->Comercios_model);
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
                $cuit = str_replace('-', '', $this->input->post('cuit'));

                $this->db->trans_begin();
                $trans_ok = TRUE;
                if (!empty($_FILES['imagen_url']['name']))
                {
                    $this->load->library('upload');
                    $config['upload_path'] = "uploads/mas_beneficios/comercios/";
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

                $trans_ok &= $this->Comercios_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $this->input->post('nombre'),
                    'razon_social' => $this->input->post('razon_social'),
                    'padron_c' => $this->input->post('padron_c'),
                    'cuit' => $cuit,
                    'calle' => $this->input->post('calle'),
                    'altura' => $this->input->post('altura'),
                    'localidad_id' => $this->input->post('localidad'),
                    'telefono' => $this->input->post('telefono'),
                    'email' => $this->input->post('email'),
                    'web' => $this->input->post('web'),
                    'facebook' => $this->input->post('facebook'),
                    'instagram' => $this->input->post('instagram'),
                    'twitter' => $this->input->post('twitter'),
                    'envio_domicilio' => $this->input->post('envio_domicilio'),
                    'encargado_id' => $this->input->post('encargado'),
                    'imagen_url' => !empty($upload) ? $config['upload_path'] . $upload['file_name'] : $comercio->imagen_url,
                    'comentarios' => $this->input->post('comentarios'),
                    'estado' => 'Pendiente'), FALSE);

                $categorias = $this->input->post('categoria');
                if (empty($categorias))
                {
                    $categorias = array();
                }
                $trans_ok &= $this->Comercios_categorias_model->intersect_asignaciones($id, $categorias, FALSE);

                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    if (!empty($upload) && !empty($comercio->imagen_url))
                    {
                        unlink($comercio->imagen_url);
                    }
                    $this->session->set_flashdata('message', $this->Comercios_model->get_msg());
                    redirect('mas_beneficios/comercios/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    if (!empty($upload))
                    {
                        unlink($config['upload_path'] . $upload['file_name']);
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Comercios_model->get_error())
                    {
                        $error_msg .= $this->Comercios_model->get_error();
                    }
                    if ($this->Comercios_categorias_model->get_error())
                    {
                        $error_msg .= $this->Comercios_categorias_model->get_error();
                    }
                }
            }
        }
        if (!empty($error_msg_file))
        {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Comercios_model->fields['localidad']['array'] = $array_localidad;
        $this->Comercios_model->fields['encargado']['array'] = $array_encargado;
        $this->Comercios_model->fields['envio_domicilio']['array'] = $array_envio_domicilio;
        $this->Comercios_model->fields['categoria']['array'] = $array_categoria;
        $this->Comercios_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Comercios_model->fields, $comercio);
        $data['comercio'] = $comercio;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Comercio';
        $data['title'] = TITLE . ' - Editar Comercio';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.min.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.min.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.min.js';
        $this->load_template('mas_beneficios/comercios/comercios_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("mas_beneficios/comercios/ver/$id", 'refresh');
        }

        $comercio = $this->Comercios_model->get_one($id);
        if (empty($comercio))
        {
            show_error('No se encontró el Comercio', 500, 'Registro no encontrado');
        }
        $categorias = array();
        $comercios_categorias = $this->Comercios_categorias_model->get(array(
            'comercio_id' => $id,
            'join' => array(
                array('ta_categorias', 'ta_categorias.id = ta_comercios_categorias.categoria_id', 'left', 'ta_categorias.nombre as categoria, ta_categorias.agrupamiento_id as agrupamiento_id')
            ),
            'where' => array(
                array('column' => 'ta_categorias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio)
            )
        ));
        if (!empty($comercios_categorias))
        {
            foreach ($comercios_categorias as $CC)
            {
                $categorias[] = $CC->categoria;
            }
        }
        else
        {
            show_error('No se encontró la Categoría del Comercio', 500, 'Registro no encontrado');
        }
        $comercio->categoria = implode(', ', $categorias);

        if (!in_groups($this->grupos_admin, $this->grupos) && $comercio->encargado_id !== $this->session->userdata('persona_id'))
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
            $trans_ok &= $this->Comercios_categorias_model->delete_asignaciones($this->input->post('id'));
            $trans_ok &= $this->Comercios_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                if (!empty($comercio->imagen_url))
                {
                    unlink($comercio->imagen_url);
                }
                $this->session->set_flashdata('message', $this->Comercios_model->get_msg());
                redirect('mas_beneficios/comercios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Comercios_categorias_model->get_error())
                {
                    $error_msg .= $this->Comercios_categorias_model->get_error();
                }
                if ($this->Comercios_model->get_error())
                {
                    $error_msg .= $this->Comercios_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Comercios_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Comercios_model->fields, $comercio, TRUE);
        $data['comercio'] = $comercio;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Comercio';
        $data['title'] = TITLE . ' - Eliminar Comercio';
        $this->load_template('mas_beneficios/comercios/comercios_abm', $data);
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
            redirect("mas_beneficios/comercios/ver/$id", 'refresh');
        }

        $comercio = $this->Comercios_model->get_one($id);
        if (empty($comercio))
        {
            show_error('No se encontró el Comercio', 500, 'Registro no encontrado');
        }
        $categorias = array();
        $comercios_categorias = $this->Comercios_categorias_model->get(array(
            'comercio_id' => $id,
            'join' => array(
                array('ta_categorias', 'ta_categorias.id = ta_comercios_categorias.categoria_id', 'left', 'ta_categorias.nombre as categoria, ta_categorias.agrupamiento_id as agrupamiento_id')
            ),
            'where' => array(
                array('column' => 'ta_categorias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio)
            )
        ));
        if (!empty($comercios_categorias))
        {
            foreach ($comercios_categorias as $CC)
            {
                $categorias[] = $CC->categoria;
            }
        }
        else
        {
            show_error('No se encontró la Categoría del Comercio', 500, 'Registro no encontrado');
        }
        $comercio->categoria = implode(', ', $categorias);

        if (!in_groups($this->grupos_control, $this->grupos) && $comercio->encargado_id !== $this->session->userdata('persona_id'))
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
            $trans_ok &= $this->Comercios_model->update(array(
                'id' => $this->input->post('id'),
                'estado' => 'Anulado'), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Comercios_model->get_msg());
                redirect('mas_beneficios/comercios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Comercios_model->get_error())
                {
                    $error_msg .= $this->Comercios_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Comercios_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Comercios_model->fields, $comercio, TRUE);
        $data['comercio'] = $comercio;
        $data['txt_btn'] = 'Anular';
        $data['title_view'] = 'Anular Comercio';
        $data['title'] = TITLE . ' - Anular Comercio';
        $this->load_template('mas_beneficios/comercios/comercios_abm', $data);
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
            redirect("mas_beneficios/comercios/ver/$id", 'refresh');
        }

        $comercio = $this->Comercios_model->get_one($id);
        if (empty($comercio))
        {
            show_error('No se encontró el Comercio', 500, 'Registro no encontrado');
        }
        $categorias = array();
        $comercios_categorias = $this->Comercios_categorias_model->get(array(
            'comercio_id' => $id,
            'join' => array(
                array('ta_categorias', 'ta_categorias.id = ta_comercios_categorias.categoria_id', 'left', 'ta_categorias.nombre as categoria, ta_categorias.agrupamiento_id as agrupamiento_id')
            ),
            'where' => array(
                array('column' => 'ta_categorias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio)
            )
        ));
        if (!empty($comercios_categorias))
        {
            foreach ($comercios_categorias as $CC)
            {
                $categorias[] = $CC->categoria;
            }
        }
        else
        {
            show_error('No se encontró la Categoría del Comercio', 500, 'Registro no encontrado');
        }
        $comercio->categoria = implode(', ', $categorias);

        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Comercios_model->update(array(
                'id' => $this->input->post('id'),
                'estado' => 'Aprobado'), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Comercios_model->get_msg());
                redirect('mas_beneficios/comercios/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Comercios_model->get_error())
                {
                    $error_msg .= $this->Comercios_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Comercios_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Comercios_model->fields, $comercio, TRUE);
        $data['comercio'] = $comercio;
        $data['txt_btn'] = 'Aprobar';
        $data['title_view'] = 'Aprobar Comecio';
        $data['title'] = TITLE . ' - Aprobar Comecio';
        $this->load_template('mas_beneficios/comercios/comercios_abm', $data);
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $comercio = $this->Comercios_model->get_one($id);
        if (empty($comercio))
        {
            show_error('No se encontró el Comercio', 500, 'Registro no encontrado');
        }
        $categorias = array();
        $comercios_categorias = $this->Comercios_categorias_model->get(array(
            'comercio_id' => $id,
            'join' => array(
                array('ta_categorias', 'ta_categorias.id = ta_comercios_categorias.categoria_id', 'left', 'ta_categorias.nombre as categoria, ta_categorias.agrupamiento_id as agrupamiento_id')
            ),
            'where' => array(
                array('column' => 'ta_categorias.agrupamiento_id', 'value' => $this->agrupamiento_id_comercio)
            )
        ));
        if (!empty($comercios_categorias))
        {
            foreach ($comercios_categorias as $CC)
            {
                $categorias[] = $CC->categoria;
            }
        }
        else
        {
            show_error('No se encontró la Categoría del Comercio', 500, 'Registro no encontrado');
        }
        $comercio->categoria = implode(', ', $categorias);

        if (!in_groups($this->grupos_admin, $this->grupos) && $comercio->encargado_id !== $this->session->userdata('persona_id'))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->Comercios_model->fields['imagen_url']['form_type'] = 'file';
        $data['fields'] = $this->build_fields($this->Comercios_model->fields, $comercio, TRUE);
        $data['comercio'] = $comercio;
        $data['txt_btn'] = NULL;
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Ver Comercio';
        $data['title'] = TITLE . ' - Ver Comercio';
        $this->load_template('mas_beneficios/comercios/comercios_abm', $data);
    }
}
