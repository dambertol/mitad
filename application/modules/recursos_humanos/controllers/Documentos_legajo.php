<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Documentos_legajo extends MY_Controller
{

    /**
     * Controlador de Documentos
     * Autor: Leandro
     * Creado: 20/02/2017
     * Modificado: 07/05/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('recursos_humanos/Adjuntos_model');
        $this->load->model('recursos_humanos/Legajos_model');
        $this->load->model('recursos_humanos/Categorias_model');
        $this->load->model('Usuarios_model');
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
                array('label' => 'Legajo', 'data' => 'legajo', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Presentación', 'data' => 'fecha_presentacion', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 14),
                array('label' => 'Descripción', 'data' => 'descripcion', 'width' => 33),
                array('label' => 'Fecha Carga', 'data' => 'fecha_subida', 'width' => 10, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Usuario Carga', 'data' => 'usuario', 'width' => 16),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
            ),
            'table_id' => 'documentos_legajo_table',
            'source_url' => 'recursos_humanos/documentos_legajo/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => "complete_documentos_legajo_table",
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);

        $archivos_last_week = $this->Adjuntos_model->get(array('select' => 'COUNT(DISTINCT legajo_id) as total_legajos, COUNT(DISTINCT categoria_id) as total_categorias, COUNT(id) as total_documentos, COALESCE(SUM(tamanio),0) as total_tamanio', 'where' => array("fecha_subida < '" . date_format(new DateTime('-1 month'), 'Y-m-d H:i') . "'")));
        $archivos = $this->Adjuntos_model->get(array('select' => 'COUNT(DISTINCT legajo_id) as total_legajos, COUNT(DISTINCT categoria_id) as total_categorias, COUNT(id) as total_documentos, SUM(tamanio) as total_tamanio'));
        $indicadores = array();
        $indicadores['legajos']['total'] = number_format($archivos[0]->total_legajos, 0, ',', '.');
        $indicadores['legajos']['variacion'] = $archivos_last_week[0]->total_legajos > 0 ? number_format((($archivos[0]->total_legajos / $archivos_last_week[0]->total_legajos) - 1) * 100, 2, ',', '.') . "% Desde el mes pasado" : 'Sin datos del mes pasado';
        $indicadores['categorias']['total'] = number_format($archivos[0]->total_categorias, 0, ',', '.');
        $indicadores['categorias']['variacion'] = $archivos_last_week[0]->total_categorias > 0 ? number_format((($archivos[0]->total_categorias / $archivos_last_week[0]->total_categorias) - 1) * 100, 2, ',', '.') . "% Desde el mes pasado" : 'Sin datos del mes pasado';
        $indicadores['documentos']['total'] = number_format($archivos[0]->total_documentos, 0, ',', '.');
        $indicadores['documentos']['variacion'] = $archivos_last_week[0]->total_documentos > 0 ? number_format((($archivos[0]->total_documentos / $archivos_last_week[0]->total_documentos) - 1) * 100, 2, ',', '.') . "% Desde el mes pasado" : 'Sin datos del mes pasado';
        if ($archivos[0]->total_tamanio >= 1048576)
        {
            $indicadores['tamanios']['total'] = number_format($archivos[0]->total_tamanio / 1048576, 2, ',', '.') . " GB";
        }
        elseif ($archivos[0]->total_tamanio >= 1024)
        {
            $indicadores['tamanios']['total'] = number_format($archivos[0]->total_tamanio / 1024, 2, ',', '.') . " MB";
        }
        else
        {
            $indicadores['tamanios']['total'] = number_format($archivos[0]->total_tamanio, 2, ',', '.') . " KB";
        }
        $indicadores['tamanios']['variacion'] = $archivos_last_week[0]->total_tamanio > 0 ? number_format((($archivos[0]->total_tamanio / $archivos_last_week[0]->total_tamanio) - 1) * 100, 2, ',', '.') . "% Desde el mes pasado" : 'Sin datos del mes pasado';
        $data['indicadores'] = $indicadores;

        $data['array_categorias'] = $this->get_array('Categorias', 'nombre', 'nombre', array(), array('' => 'Todas'));
        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de documentos';
        $data['title'] = TITLE . ' - Documentos';
        $this->load_template('recursos_humanos/documentos_legajo/documentos_legajo_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_edicion, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->datatables
                ->select("rh_adjuntos.id, rh_legajos.legajo as legajo, rh_adjuntos.fecha_presentacion as fecha_presentacion, rh_categorias.nombre as categoria, descripcion, fecha_subida, CONCAT(personas.apellido, ', ', personas.nombre) as usuario")
                ->from('rh_adjuntos')
                ->join('rh_legajos', 'rh_legajos.id = rh_adjuntos.legajo_id', 'left')
                ->join('rh_categorias', 'rh_categorias.id = rh_adjuntos.categoria_id', 'left')
                ->join('users', 'users.id = rh_adjuntos.usuario_subida', 'left')
                ->join('personas', 'personas.id = users.persona_id', 'left')
                ->add_column('ver', '<a href="recursos_humanos/documentos_legajo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id');

        if (in_groups($this->grupos_admin, $this->grupos))
        {
            $this->datatables->add_column('editar', '<a href="recursos_humanos/documentos_legajo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id');
            $this->datatables->add_column('eliminar', '<a href="recursos_humanos/documentos_legajo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');
        }
        else
        {
            $this->datatables->add_column('editar', '', 'id');
            $this->datatables->add_column('eliminar', '', 'id');
        }

        echo $this->datatables->generate();
    }

    public function agregar($legajo_id = NULL)
    {
        if (!in_groups($this->grupos_edicion, $this->grupos) || $legajo_id == NULL || !ctype_digit($legajo_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("recursos_humanos/documentos_legajo/listar", 'refresh');
        }

        $legajo = $this->Legajos_model->get(array('id' => $legajo_id));
        if (empty($legajo))
        {
            show_error('No se encontró el legajo', 500, 'Registro no encontrado');
        }
        $this->array_categoria_control = $array_categoria = $this->get_array('Categorias', 'nombre');
        unset($this->Adjuntos_model->fields['nombre']);
        unset($this->Adjuntos_model->fields['tamanio']);
        unset($this->Adjuntos_model->fields['fecha_subida']);
        unset($this->Adjuntos_model->fields['usuario_subida']);
        $this->set_model_validation_rules($this->Adjuntos_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $categoria = $this->Categorias_model->get(array('id' => $this->input->post('categoria')));
            $fecha_presentacion = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_presentacion'));
            $config = array();
            $config['upload_path'] = 'uploads/recursos_humanos/' . $legajo->legajo . '/' . $fecha_presentacion->format('Y') . '/' . $categoria->ruta . '/';
            if (!file_exists($config['upload_path']))
            {
                mkdir($config['upload_path'], 0755, TRUE);
            }
            $config['allowed_types'] = 'pdf';
            $config['max_size'] = 4096;
            $config['encrypt_name'] = TRUE;
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('ruta'))
            {
                $error_msg = $this->upload->display_errors();
            }
            else
            {
                $upload_data = $this->upload->data();
            }

            if (empty($error_msg))
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Adjuntos_model->create(array(
                    'legajo_id' => $legajo_id,
                    'fecha_presentacion' => $fecha_presentacion->format('Y-m-d'),
                    'categoria_id' => $this->input->post('categoria'),
                    'nombre' => $upload_data['file_name'],
                    'descripcion' => $this->input->post('descripcion'),
                    'ruta' => $config['upload_path'],
                    'tamanio' => round($upload_data['file_size'], 2),
                    'hash' => md5_file($config['upload_path'] . $upload_data['file_name']),
                    'fecha_subida' => date_format(new DateTime(), 'Y-m-d H:i'),
                    'usuario_subida' => $this->session->userdata('user_id')), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Adjuntos_model->get_msg());
                    redirect("recursos_humanos/legajos/ver/$legajo_id", 'refresh');
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $documentos_legajo = new stdClass();
        $documentos_legajo->categoria_id = NULL;
        $documentos_legajo->fecha_presentacion = (new DateTime())->format('d-m-Y');
        $documentos_legajo->descripcion = NULL;
        $documentos_legajo->ruta = NULL;
        $this->Adjuntos_model->fields['categoria']['array'] = $array_categoria;
        $data['fields'] = $this->build_fields($this->Adjuntos_model->fields, $documentos_legajo);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Agregar Documento';
        $data['title'] = TITLE . ' - Agregar Documento';
        $data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
        $data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
        $this->load_template('recursos_humanos/documentos_legajo/documentos_legajo_abm', $data);
    }

    public function editar($id = NULL, $back_url = NULL, $back_id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("recursos_humanos/documentos_legajo/ver/$id", 'refresh');
        }

        $documentos_legajo = $this->Adjuntos_model->get_one($id);
        if (empty($documentos_legajo))
        {
            show_error('No se encontró el Documento', 500, 'Registro no encontrado');
        }

        $path = $documentos_legajo->ruta;
        $file = $path . $documentos_legajo->nombre;
        if (!file_exists($file) || md5_file($file) !== $documentos_legajo->hash)
        {
            show_error('Archivo inválido', 500, 'Registro no encontrado');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'categoria' => array('label' => 'Categoría', 'disabled' => TRUE),
            'fecha_presentacion' => array('label' => 'Presentación', 'type' => 'date', 'disabled' => TRUE),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'disabled' => TRUE),
            'descripcion' => array('label' => 'Descripción', 'required' => TRUE),
            'tamanio' => array('label' => 'Tamaño', 'type' => 'integer', 'maxlength' => '4', 'disabled' => TRUE),
            'fecha_subida' => array('label' => 'Fecha Carga', 'type' => 'datetime', 'disabled' => TRUE),
            'usuario_subida' => array('label' => 'Usuario Carga', 'disabled' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if (isset($_POST) && !empty($_POST))
        {
            if ($id != $this->input->post('id'))
            {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }

            if ($this->form_validation->run() === TRUE)
            {
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Adjuntos_model->update(array(
                    'id' => $this->input->post('id'),
                    'nombre' => $documentos_legajo->nombre,
                    'descripcion' => $this->input->post('descripcion'),
                    'ruta' => $documentos_legajo->ruta
                        ), FALSE);
                if ($this->db->trans_status() && $trans_ok)
                {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('message', $this->Adjuntos_model->get_msg());
                    if (!empty($back_url) && !empty($back_id))
                    {
                        redirect("recursos_humanos/$back_url/ver/$back_id", 'refresh');
                    }
                    else
                    {
                        redirect('recursos_humanos/documentos_legajo/listar', 'refresh');
                    }
                }
                else
                {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Adjuntos_model->get_error())
                    {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        if ($documentos_legajo->tamanio >= 1024)
        {
            $documentos_legajo->tamanio = number_format($documentos_legajo->tamanio / 1024, 2, ',', '.') . " MB";
        }
        else
        {
            $documentos_legajo->tamanio = number_format($documentos_legajo->tamanio, 2, ',', '.') . " KB";
        }

        $data['fields'] = $this->build_fields($fake_model->fields, $documentos_legajo);
        $data['documentos_legajo'] = $documentos_legajo;
        if (!empty($back_url) && !empty($back_id))
        {
            $data['back_url'] = $back_url;
            $data['back_id'] = $back_id;
        }
        $data['txt_btn'] = 'Editar';
        $data['title_view'] = 'Editar Documento';
        $data['title'] = TITLE . ' - Editar Documento';
        $this->load_template('recursos_humanos/documentos_legajo/documentos_legajo_abm', $data);
    }

    public function eliminar($id = NULL, $back_url = NULL, $back_id = NULL)
    {
        if (!in_groups($this->grupos_admin, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', '<br />Usuario sin permisos de edición');
            redirect("recursos_humanos/documentos_legajo/ver/$id", 'refresh');
        }

        $documentos_legajo = $this->Adjuntos_model->get_one($id);
        if (empty($documentos_legajo))
        {
            show_error('No se encontró el Documento', 500, 'Registro no encontrado');
        }

        $path = $documentos_legajo->ruta;
        $file = $path . $documentos_legajo->nombre;
        if (!file_exists($file) || md5_file($file) !== $documentos_legajo->hash)
        {
            show_error('Archivo inválido', 500, 'Registro no encontrado');
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
            $trans_ok &= $this->Adjuntos_model->delete(array('id' => $this->input->post('id')), FALSE);
            if ($this->db->trans_status() && $trans_ok)
            {
                $this->db->trans_commit();
                unlink($file);
                $this->session->set_flashdata('message', $this->Adjuntos_model->get_msg());
                if (!empty($back_url) && !empty($back_id))
                {
                    redirect("recursos_humanos/$back_url/ver/$back_id", 'refresh');
                }
                else
                {
                    redirect('recursos_humanos/documentos_legajo/listar', 'refresh');
                }
            }
            else
            {
                $this->db->trans_rollback();
                $error_msg = '<br />Se ha producido un error con la base de datos.';
                if ($this->Adjuntos_model->get_error())
                {
                    $error_msg .= $this->Adjuntos_model->get_error();
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        if ($documentos_legajo->tamanio >= 1024)
        {
            $documentos_legajo->tamanio = number_format($documentos_legajo->tamanio / 1024, 2, ',', '.') . " MB";
        }
        else
        {
            $documentos_legajo->tamanio = number_format($documentos_legajo->tamanio, 2, ',', '.') . " KB";
        }
        unset($this->Adjuntos_model->fields['ruta']);
        $data['fields'] = $this->build_fields($this->Adjuntos_model->fields, $documentos_legajo, TRUE);
        $data['documentos_legajo'] = $documentos_legajo;
        if (!empty($back_url) && !empty($back_id))
        {
            $data['back_url'] = $back_url;
            $data['back_id'] = $back_id;
        }
        $data['txt_btn'] = 'Eliminar';
        $data['title_view'] = 'Eliminar Documento';
        $data['title'] = TITLE . ' - Eliminar Documento';
        $this->load_template('recursos_humanos/documentos_legajo/documentos_legajo_abm', $data);
    }

    public function ver($id = NULL, $back_url = NULL, $back_id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $documentos_legajo = $this->Adjuntos_model->get_one($id);
        if (empty($documentos_legajo))
        {
            show_error('No se encontró el Documento', 500, 'Registro no encontrado');
        }

        if (in_groups($this->grupos_publico, $this->grupos) && $documentos_legajo->publico === 'NO')
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
                    if ($Leg->legajo_id === $documentos_legajo->legajo_id)
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

        $path = $documentos_legajo->ruta;
        $file = $path . $documentos_legajo->nombre;
        if (!file_exists($file) || md5_file($file) !== $documentos_legajo->hash)
        {
            show_error('Archivo inválido', 500, 'Registro no encontrado');
        }

        if ($documentos_legajo->tamanio >= 1024)
        {
            $documentos_legajo->tamanio = number_format($documentos_legajo->tamanio / 1024, 2, ',', '.') . " MB";
        }
        else
        {
            $documentos_legajo->tamanio = number_format($documentos_legajo->tamanio, 2, ',', '.') . " KB";
        }
        unset($this->Adjuntos_model->fields['ruta']);
        $data['error'] = $this->session->flashdata('error');
        $data['fields'] = $this->build_fields($this->Adjuntos_model->fields, $documentos_legajo, TRUE);
        $data['documentos_legajo'] = $documentos_legajo;
        if (!empty($back_url) && !empty($back_id))
        {
            $data['back_url'] = $back_url;
            $data['back_id'] = $back_id;
        }
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Documento';
        $data['title'] = TITLE . ' - Ver Documento';
        $this->load_template('recursos_humanos/documentos_legajo/documentos_legajo_abm', $data);
    }
}
