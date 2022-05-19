<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Legajos extends MY_Controller
{

    /**
     * Controlador de Legajos
     * Autor: Leandro
     * Creado: 02/02/2017
     * Modificado: 11/05/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('recursos_humanos/Legajos_model');
        $this->load->model('recursos_humanos/Datos_extra_model');
        $this->load->model('recursos_humanos/Datos_extra_hobbies_model');
        $this->load->model('recursos_humanos/Datos_extra_oficinas_model');
        $this->load->model('recursos_humanos/Categorias_model');
        $this->load->model('recursos_humanos/Adjuntos_model');
        $this->grupos_permitidos = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_director', 'recursos_humanos_publico', 'recursos_humanos_consulta_general');
        $this->grupos_edicion = array('admin', 'recursos_humanos_admin', 'recursos_humanos_user', 'recursos_humanos_consulta_general');
        $this->grupos_admin = array('admin', 'recursos_humanos_admin', 'recursos_humanos_consulta_general');
        $this->grupos_director = array('recursos_humanos_director');
        $this->grupos_publico = array('recursos_humanos_publico');
        $this->grupos_solo_consulta = array('recursos_humanos_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar()
    {
        if (!in_groups($this->grupos_edicion, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Legajo', 'data' => 'legajo', 'width' => 11, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 35),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 35),
                array('label' => 'Público', 'data' => 'publico', 'width' => 10),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'legajos_table',
            'source_url' => 'recursos_humanos/legajos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_legajos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['edicion'] = TRUE;
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de legajos';
        $data['title'] = TITLE . ' - Legajos';
        $this->load_template('recursos_humanos/legajos/legajos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_edicion, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, legajo, nombre, apellido, publico')
                ->from('rh_legajos')
                ->add_column('ver', '<a href="recursos_humanos/legajos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
                ->add_column('editar', '<a href="recursos_humanos/legajos/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id');

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $this->datatables->add_column('eliminar', '<a href="recursos_humanos/legajos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');
        }
        else
        {
            $this->datatables->add_column('eliminar', '', 'id');
        }

        echo $this->datatables->generate();
    }

    public function listar_publicos()
    {
        if (!in_groups($this->grupos_publico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Legajo', 'data' => 'legajo', 'width' => 11, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 38),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 38),
                array('label' => 'Público', 'data' => 'publico', 'width' => 10),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'legajos_table',
            'source_url' => 'recursos_humanos/legajos/listar_publicos_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_legajos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['edicion'] = FALSE;
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de legajos públicos';
        $data['title'] = TITLE . ' - Legajos';
        $this->load_template('recursos_humanos/legajos/legajos_listar', $data);
    }

    public function listar_publicos_data()
    {
        if (!in_groups($this->grupos_publico, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('id, legajo, nombre, apellido, publico')
                ->from('rh_legajos')
                ->where("rh_legajos.publico = 'SI'")
                ->add_column('ver', '<a href="recursos_humanos/legajos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function listar_director()
    {
        if (!in_groups($this->grupos_director, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Legajo', 'data' => 'legajo', 'width' => 11, 'class' => 'dt-body-right'),
                array('label' => 'Nombre', 'data' => 'nombre', 'width' => 38),
                array('label' => 'Apellido', 'data' => 'apellido', 'width' => 38),
                array('label' => 'Público', 'data' => 'publico', 'width' => 10),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'legajos_table',
            'source_url' => 'recursos_humanos/legajos/listar_director_data/' . $this->session->userdata('user_id'),
            'reuse_var' => TRUE,
            'initComplete' => "complete_legajos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['edicion'] = FALSE;
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de legajos';
        $data['title'] = TITLE . ' - Legajos';
        $this->load_template('recursos_humanos/legajos/legajos_listar', $data);
    }

    public function listar_director_data($user_id = NULL)
    {
        if (!in_groups($this->grupos_director, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select('rh_legajos.id, rh_legajos.legajo, rh_legajos.nombre, rh_legajos.apellido, rh_legajos.publico')
                ->from('rh_legajos')
                ->join('rh_usuarios_legajos', 'rh_usuarios_legajos.legajo_id = rh_legajos.id', 'LEFT')
                ->where("rh_usuarios_legajos.user_id = $user_id")
                ->add_column('ver', '<a href="recursos_humanos/legajos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id');

        echo $this->datatables->generate();
    }

    public function agregar()
    {
        if (!in_groups($this->grupos_edicion, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("recursos_humanos/legajos/listar", 'refresh');
        }

        $this->array_publico_control = $this->Legajos_model->fields['publico']['array'];
        $this->set_model_validation_rules($this->Legajos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $this->db->trans_begin();
            $trans_ok = TRUE;
            $trans_ok &= $this->Legajos_model->create(array(
                'legajo' => $this->input->post('legajo'),
                'nombre' => mb_strtoupper($this->input->post('nombre'), 'UTF-8'),
                'apellido' => mb_strtoupper($this->input->post('apellido'), 'UTF-8'),
                'publico' => $this->input->post('publico')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                $this->session->set_flashdata('message', $this->Legajos_model->get_msg());
                redirect('recursos_humanos/legajos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Legajos_model->get_error())
                {
                    $error_msg .= $this->Legajos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        unset($this->Legajos_model->fields['legajo']);
        $data['legajo'] = array(
            'name' => 'legajo',
            'id' => 'legajo',
            'type' => 'text',
            'value' => $this->form_validation->set_value('legajo')
        );
        $data['fields'] = $this->build_fields($this->Legajos_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar legajo';
        $data['title'] = TITLE . ' - Agregar legajo';
        $data['js'] = 'js/recursos_humanos/base.js';
        $this->load_template('recursos_humanos/legajos/legajos_abm', $data);
    }

    public function editar($id = NULL)
    {
        if (!in_groups($this->grupos_edicion, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("recursos_humanos/legajos/ver/$id", 'refresh');
        }

        $legajo = $this->Legajos_model->get(array('id' => $id));
        if (empty($legajo))
        {
            show_error('No se encontró el Legajo', 500, 'Registro no encontrado');
        }

        $this->array_publico_control = $this->Legajos_model->fields['publico']['array'];
        $this->set_model_validation_rules($this->Legajos_model);
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
                $trans_ok &= $this->Legajos_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => mb_strtoupper($this->input->post('nombre'), 'UTF-8'),
                    'apellido' => mb_strtoupper($this->input->post('apellido'), 'UTF-8'),
                    'publico' => $this->input->post('publico')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Legajos_model->get_msg());
                    redirect('recursos_humanos/legajos/listar', 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Legajos_model->get_error())
                    {
                        $error_msg .= $this->Legajos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $this->Legajos_model->fields['legajo']['readonly'] = TRUE;
        $data['fields'] = $this->build_fields($this->Legajos_model->fields, $legajo);
        $data['legajo'] = $legajo;
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar legajo';
        $data['title'] = TITLE . ' - Editar legajo';
        $this->load_template('recursos_humanos/legajos/legajos_abm', $data);
    }

    public function eliminar($id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("recursos_humanos/legajos/ver/$id", 'refresh');
        }

        $legajo = $this->Legajos_model->get(array('id' => $id));
        if (empty($legajo))
        {
            show_error('No se encontró el Legajo', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Legajos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                if (!empty($legajo->legajo) && is_dir('uploads/recursos_humanos/' . $legajo->legajo)) //Nunca debería estar vacio
                {
                    $this->removeDirectory('uploads/recursos_humanos/' . $legajo->legajo);
                }
                $this->session->set_flashdata('message', $this->Legajos_model->get_msg());
                redirect('recursos_humanos/legajos/listar', 'refresh');
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Legajos_model->get_error())
                {
                    $error_msg .= $this->Legajos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($this->Legajos_model->fields, $legajo, TRUE);
        $data['legajo'] = $legajo;
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar legajo';
        $data['title'] = TITLE . ' - Eliminar legajo';
        $this->load_template('recursos_humanos/legajos/legajos_abm', $data);
    }

    private function removeDirectory($path)
    {
        if (!in_groups($this->grupos_admin, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $files = glob($path . '/*');
        foreach ($files as $file)
        {
            is_dir($file) ? $this->removeDirectory($file) : unlink($file);
        }

        rmdir($path);

        return;
    }

    public function ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $legajo = $this->Legajos_model->get(array('id' => $id));
        if (empty($legajo))
        {
            show_error('No se encontró el Legajo', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $legajo->publico === 'NO')
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_director, $this->grupos))
        {
            $permitido = FALSE;
            $this->load->model('recursos_humanos/Usuarios_legajos_model');
            $legajos = $this->Usuarios_legajos_model->get(array('user_id' => $this->session->userdata('user_id')));
            if (!empty($legajos))
            {
                foreach ($legajos as $Leg)
                {
                    if ($Leg->legajo_id === $legajo->id)
                    {
                        $permitido = TRUE;
                        break;
                    }
                }
            }
            if (!$permitido)
            {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            }
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        try
        {
            $http_response_empleado = $guzzleHttp->request('GET', "personas/datos", ['query' => ['labo_Codigo' => $legajo->legajo, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
            $empleado = json_decode($http_response_empleado->getBody()->getContents());

            $http_response_datos_personales = $guzzleHttp->request('GET', "personas/datos_personales", ['query' => ['labo_Codigo' => $legajo->legajo]]);
            $datos_personales = json_decode($http_response_datos_personales->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleado = NULL;
            $datos_personales = NULL;
        }

        if (!empty($datos_personales->comunicaciones))
        {
            foreach ($datos_personales->comunicaciones as $Comunicacion)
            {
                switch ($Comunicacion->tcom_Tipo)
                {
                    case '1': //Telefono
                        $empleado->telefono = $Comunicacion->comu_Identificacion;
                        break;
                    case '10': //Celular
                        $empleado->celular = $Comunicacion->comu_Identificacion;
                        break;
                }
            }
        }

        if (!empty($datos_personales->novedades))
        {
            foreach ($datos_personales->novedades as $Novedad)
            {
                switch ($Novedad->vari_Nombre)
                {
                    case '@GRUPOSANG': //Grupo Sanguineo
                        $empleado->grupo_sanguineo = $Novedad->vava_Descripcion;
                        break;
                }
            }
        }

        $documentos_legajo = $this->Adjuntos_model->get(array(
            'legajo_id' => $id,
            'join' => array(
                array(
                    'type' => 'left',
                    'table' => 'rh_categorias',
                    'where' => 'rh_adjuntos.categoria_id = rh_categorias.id',
                    'columnas' => 'rh_categorias.nombre as categoria_nombre',
                )
            ),
            'sort_by' => "DATE_FORMAT(rh_adjuntos.fecha_presentacion, '%Y'), rh_categorias.nombre"
        ));
        $anios = array();
        $categorias = array();
        $documentos = array();
        if (!empty($documentos_legajo))
        {
            $tag_anio = 0;
            $ultimo_anio = date_format(new DateTime($documentos_legajo[0]->fecha_presentacion), 'Y');
            $ultima_categoria = $documentos_legajo[0]->categoria_nombre;
            foreach ($documentos_legajo as $Documento_legajo)
            {
                if ($Documento_legajo->categoria_nombre !== $ultima_categoria || date_format(new DateTime($Documento_legajo->fecha_presentacion), 'Y') !== $ultimo_anio)
                {
                    $cat = new stdClass();
                    $cat->text = $ultima_categoria;
                    $cat->tags = array(count($documentos));
                    if (count($documentos) > 0)
                    {
                        $cat->nodes = $documentos;
                    }
                    $categorias[] = $cat;
                    $tag_anio += count($documentos);
                    $ultima_categoria = $Documento_legajo->categoria_nombre;
                    $documentos = array();
                }

                if (date_format(new DateTime($Documento_legajo->fecha_presentacion), 'Y') !== $ultimo_anio)
                {
                    $anio = new stdClass();
                    $anio->text = $ultimo_anio;
                    $anio->tags = array($tag_anio);
                    if (count($categorias) > 0)
                    {
                        $anio->nodes = $categorias;
                    }
                    $anios[] = $anio;
                    $tag_anio = 0;
                    $ultimo_anio = date_format(new DateTime($Documento_legajo->fecha_presentacion), 'Y');
                    $categorias = array();
                    $documentos = array();
                }

                $doc = new stdClass();
                if ($Documento_legajo->tamanio >= 1024)
                {
                    $tamanio = number_format($Documento_legajo->tamanio / 1024, 2) . " MB";
                }
                else
                {
                    $tamanio = number_format($Documento_legajo->tamanio, 2) . " KB";
                }
                $doc->text = $Documento_legajo->descripcion . '<br><small>Creado el ' . date_format(new DateTime($Documento_legajo->fecha_subida), 'd/m/Y H:i') . '</small> - <small>Tamaño ' . $tamanio . '</small>';
                $doc->href = 'recursos_humanos/documentos_legajo/ver/' . $Documento_legajo->id . '/legajos/' . $id;
                $doc->icon = 'glyphicon glyphicon-file';
                $doc->view = 'recursos_humanos/documentos_legajo/ver/' . $Documento_legajo->id . '/legajos/' . $id;
                if (in_groups($this->grupos_admin, $this->grupos))
                {
                    $doc->edit = 'recursos_humanos/documentos_legajo/editar/' . $Documento_legajo->id . '/legajos/' . $id;
                    $doc->delete = 'recursos_humanos/documentos_legajo/eliminar/' . $Documento_legajo->id . '/legajos/' . $id;
                }
                $documentos[] = $doc;
            }

            $cat = new stdClass();
            $cat->text = $Documento_legajo->categoria_nombre;
            $cat->tags = array(count($documentos));
            if (count($documentos) > 0)
            {
                $cat->nodes = $documentos;
            }
            $categorias[] = $cat;
            $tag_anio += count($documentos);

            $anio = new stdClass();
            $anio->text = date_format(new DateTime($Documento_legajo->fecha_presentacion), 'Y');
            $anio->tags = array($tag_anio);
            if (count($categorias) > 0)
            {
                $anio->nodes = $categorias;
            }
            $anios[] = $anio;
        }

        $tableData = array(
            'columns' => array(
                array('label' => 'Embargo', 'data' => 'emba_Numero', 'width' => 4, 'class' => 'dt-body-right', 'responsive_class' => 'all'),
                array('label' => 'Tipo', 'data' => 'emba_Tipo', 'width' => 8),
                array('label' => 'Fecha', 'data' => 'emba_Fecha', 'render' => 'date', 'width' => 8),
                array('label' => 'Caratula', 'data' => 'emba_Caratula', 'width' => 24),
                array('label' => 'Juzgado', 'data' => 'emba_Juzgado', 'width' => 18),
                array('label' => 'Dpto Judicial', 'data' => 'emba_DptoJudicial', 'width' => 12),
                array('label' => 'Transferir a', 'data' => 'emba_Transferir', 'width' => 10),
                array('label' => 'Total', 'data' => 'emba_MontoTotal', 'render' => 'money', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Liquidado', 'data' => 'emba_MontoLiquidado', 'render' => 'money', 'width' => 8, 'class' => 'dt-body-right')
            ),
            'table_id' => 'embargos_table',
            'server_side' => FALSE,
            'source_url' => "recursos_humanos/legajos/embargos_listar_data/$legajo->legajo",
            'order' => array(array(0, 'desc')),
            'reuse_var' => TRUE,
            'initComplete' => "complete_embargos_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-5"i><"col-sm-2"B><"col-sm-5"p>>',
            'buttons' => array(
                array(
                    'extend' => 'print',
                    'className' => 'btn btn-primary',
                    'title' => "Listado de Embargos $legajo->legajo"
                )
            )
        );

        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);

        $datos_extra_temp = $this->Datos_extra_model->get(array('legajo_id' => $id));
        if (empty($datos_extra_temp))
        {
            $datos_extra = new stdClass();
            $datos_extra->id = NULL;
            $datos_extra->hobbies = NULL;
            $datos_extra->experiencias = NULL;
            $datos_extra->conformidad_oficina = NULL;
            $datos_extra->posibles_oficinas = NULL;
        }
        else
        {
            $datos_extra = $datos_extra_temp[0];

            $hobbies_tmp = array();
            $hobbies = $this->Datos_extra_hobbies_model->get(array(
                'dato_extra_id' => $datos_extra->id,
                'join' => array(
                    array('rh_hobbies', 'rh_hobbies.id = rh_datos_extra_hobbies.hobby_id', 'LEFT', 'rh_hobbies.nombre as hobby'),
                )
            ));
            if (!empty($hobbies))
            {
                foreach ($hobbies as $Hobby)
                {
                    $hobbies_tmp[] = $Hobby->hobby;
                }
            }
            $datos_extra->hobbies = implode(', ', $hobbies_tmp);

            $oficinas_tmp = array();
            $posibles_oficinas = $this->Datos_extra_oficinas_model->get(array(
                'dato_extra_id' => $datos_extra->id,
                'join' => array(
                    array('areas', 'areas.id = rh_datos_extra_oficinas.area_id', 'LEFT', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'),
                )
            ));
            if (!empty($posibles_oficinas))
            {
                foreach ($posibles_oficinas as $Oficina)
                {
                    $oficinas_tmp[] = $Oficina->area;
                }
            }
            $datos_extra->posibles_oficinas = implode(', ', $oficinas_tmp);
        }

        if (in_groups($this->grupos_edicion, $this->grupos))
        {
            $data['edicion'] = TRUE;
        }
        else
        {
            $data['edicion'] = FALSE;
        }

        $data['fields_datos'] = $this->build_fields($this->Datos_extra_model->fields, $datos_extra, TRUE);
        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Legajos_model->fields, $legajo, TRUE);
        $data['legajo'] = $legajo;
        $data['empleado'] = $empleado;
        $data['datos_personales'] = $datos_personales;
        $data['datos_extra'] = $datos_extra;
        $data['anios'] = json_encode($anios);
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver legajo';
        $data['title'] = TITLE . ' - Ver legajo';
        $data['css'] = 'vendor/bootstrap-treeview/css/bootstrap-treeview.min.css';
        $data['js'] = 'vendor/bootstrap-treeview/js/bootstrap-treeview.min.js';
        $this->load_template('recursos_humanos/legajos/legajos_ver', $data);
    }

    public function embargos_listar_data($labo_Codigo = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $labo_Codigo === NULL || !ctype_digit($labo_Codigo))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        try
        {
            $http_response_embargos = $guzzleHttp->request('GET', "personas/embargos", ['query' => ['labo_Codigo' => $labo_Codigo]]);
            $embargos = json_decode($http_response_embargos->getBody()->getContents());
        } catch (Exception $e)
        {
            $embargos = NULL;
        }

        if (!empty($embargos))
        {
            $embargos_persona['data'] = $embargos;
            foreach ($embargos_persona['data'] as $i => $Embargo)
            {
//				$embargos_persona['data'][$i]->ver = '<a href="recursos_humanos/legajos/ver_embargo/' . $labo_Codigo . '/' . $Embargo->emba_Numero . '" title="Ver embargo" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>';
            }
            echo json_encode($embargos_persona);
        }
        else
        {
            echo json_encode(array('data' => array()));
        }
    }

    public function buscar()
    {
        if (!in_groups($this->grupos_edicion, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('legajo', 'Legajo', 'required|integer|max_length[8]');

        if ($this->form_validation->run() === TRUE)
        {
            $labo_Codigo = $this->input->post('legajo');

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            try
            {
                $http_response_empleado = $guzzleHttp->request('GET', "personas/datos", ['query' => ['labo_Codigo' => $labo_Codigo, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
                $empleado = json_decode($http_response_empleado->getBody()->getContents());
            } catch (Exception $e)
            {
                $empleado = NULL;
            }

            if (!empty($empleado))
            {
                $data['empleado'] = $empleado;
            }
            else
            {
                $data['error'] = 'Error al buscar datos del legajo en Major. Puede continuar cargando los datos manualmente.';
            }

            echo json_encode($data);
        }
    }
}
