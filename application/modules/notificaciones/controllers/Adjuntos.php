<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

    /**
     * Controlador de Adjuntos
     * Autor: Leandro
     * Creado: 10/06/2019
     * Modificado: 24/06/2019 (Leandro)
     */
    function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_areas', 'notificaciones_notificadores', 'notificaciones_control');
        $this->grupos_oficinas_externas = array('notificaciones_areas');
        $this->grupos_notificaciones = array('admin', 'notificaciones_user', 'notificaciones_notificadores', 'notificaciones_control');

        $this->modulo = 'notificaciones';
        $this->load->model('notificaciones/Adjuntos_model');
        $this->load->model('notificaciones/Cedulas_model');
        $this->load->helper('notificaciones/notificaciones_functions_helper');
        // Inicializaciones necesarias colocar acá.
    }

    public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
    {
        if ($entidad_nombre === 'modelo_cedula.docx') {
            $this->entidad = $entidad_nombre;
            $this->directorio = '';
            $this->verificar_archivo = FALSE;


            $path = "uploads/notificaciones/" ;
            $file = $path . $entidad_nombre;
            if (!file_exists($file))
            {
                show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
            }

            $this->load->helper('download');
            force_download($file, NULL);
            exit();



        } else {
            $this->entidad = $entidad_nombre;
            $this->archivo_id = $archivo_id;
            $this->entidad_id_nombre = 'cedula_id';

            $this->load->model("$this->modulo/Adjuntos_model");
            $adjunto = $this->Adjuntos_model->get_one($this->archivo_id);
            if (empty($adjunto)) {
                show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
            }
            $this->load->model("$this->modulo/{$this->entidad}_model");
            $entidad = $this->{"{$this->entidad}_model"}->get_one($adjunto->{$this->entidad_id_nombre});
            $this->load->model("$this->modulo/Usuarios_areas_model");
            //$this->grupos_limitados = array('vales_combustible_areas');


            if (!in_groups($this->grupos_permitidos, $this->grupos)) {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            } elseif (in_groups($this->grupos_oficinas_externas, $this->grupos) && (!$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $entidad->oficina_id))) {
                show_error('No tiene permisos para descargar la cedula', 500, 'Acción no autorizada');
            }


            if (!in_groups($this->grupos_permitidos, $this->grupos))
            {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            }

            $adjunto = $this->Adjuntos_model->get_one($this->archivo_id);
            if (empty($adjunto) || empty($adjunto->{$this->entidad_id_nombre}))
            {
                show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
            }

            $path = $adjunto->ruta;
            $file = $path . $adjunto->nombre;
            if (!file_exists($file))
            {
                show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
            }

            $this->load->helper('download');
            force_download($file, NULL);
            exit();

        }
      //  parent::descargar();
    }

    public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $archivo_id = NULL)
    {
        if ($entidad_nombre === 'manuales' || $entidad_nombre === 'modelo_cedula.docx') {
            $this->entidad = $entidad_nombre;
            $this->directorio = '';
            $this->archivo = $directorio_nombre;
            $this->verificar_archivo = FALSE;
        } else {

            $this->entidad = $entidad_nombre;
            $this->directorio = $directorio_nombre;
            $this->archivo = $archivo_id;
            $this->entidad_id_nombre = 'cedula_id';

            $this->load->model("$this->modulo/Adjuntos_model");
            $path = "uploads/$this->modulo/$this->entidad/$this->directorio/";
            $adjunto = $this->Adjuntos_model->get(array('ruta' => $path, 'nombre' => $this->archivo));
            if (empty($adjunto[0])) {
                show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
            }
            $this->load->model("$this->modulo/{$this->entidad}_model");
            $entidad = $this->{"{$this->entidad}_model"}->get_one($adjunto[0]->{$this->entidad_id_nombre});
            $this->load->model("$this->modulo/Usuarios_areas_model");
            //$this->grupos_limitados = array('vales_combustible_areas');
            /*
                    if (in_groups($this->grupos_permitidos, $this->grupos) && !$this->Usuarios_areas_model->in_area($this->session->userdata('user_id'), $entidad->area_id)) {
                        show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
                    }
            */
        }
        parent::ver();
    }

    public function modal_agregar($entidad_nombre = NULL)
    {
        $this->entidad = $entidad_nombre;
        $this->extensiones = '["jpg", "png", "jpeg", "pdf"]';
        parent::modal_agregar();
    }

    public function agregar($entidad_nombre = NULL)
    {
        $this->entidad = $entidad_nombre;
        $this->extensiones = 'jpg|jpeg|png|pdf';
        parent::agregar();
    }

    public function eliminar($id)
    {
        if (isset($_POST) && !empty($_POST)) {
            $adjunto = $this->Adjuntos_model->get_one($this->input->post('adjunto_id'));

            if ($id == $this->input->post('adjunto_id')) {
                if ($adjunto->nombre != $this->input->post('nombre')) {
                    echo json_encode(['error' => "Bad Request"]);
                }
                $this->db->trans_begin();
                $trans_ok = TRUE;
                $trans_ok &= $this->Adjuntos_model->delete(
                    array(
                        'id' => $this->input->post('adjunto_id'),
                    ), FALSE
                );
//dd($trans_ok);

                if ($this->db->trans_status() && $trans_ok) {
                    //    dd(1);
                    $this->db->trans_commit();
                    echo $this->Adjuntos_model->get_msg();
                } else {
                    $this->db->trans_rollback();
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Adjuntos_model->get_error()) {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                    //  dd($error_msg);
                    return $error_msg;
                }
            }
        } else {
            dd('error');
            return "Bad Request";
        }
    }
}