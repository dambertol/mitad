<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Manuales extends MY_Controller
{

    /**
     * Controlador Escritorio
     * Autor: Leandro
     * Creado: 29/10/2018
     * Modificado: 26/11/2018 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->grupos_permitidos = array('admin', 'notificaciones_user', 'notificaciones_areas', 'notificaciones_notificadores', 'notificaciones_control');
        $this->grupos_admin = array('admin', 'notificaciones_user', 'notificaciones_notificadores', 'notificaciones_control');
        $this->grupos_notificaciones = array('notificaciones_user', 'notificaciones_control');
        $this->grupos_notificadores = array('notificaciones_notificadores');
        $this->grupos_oficinas = array('notificaciones_areas');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos)) {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_admin, $this->grupos) || in_groups($this->grupos_notificaciones, $this->grupos)) {
            $data['municipal'] = 'uploads/notificaciones/manuales/manual_usuario.pdf';
        } elseif (in_groups($this->grupos_notificadores, $this->grupos)) {
            $data['municipal'] = 'uploads/notificaciones/manuales/manual_usuario.pdf';
        } else {
        }
            $data['municipal'] = 'uploads/notificaciones/manuales/manual_usuario.pdf';

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Manual de Usuario';
        $data['title'] = TITLE . ' - Manuales';
        $data['accesos_esc'] = load_permisos_notificaciones_escritorio($this->grupos);
        $this->load_template('notificaciones/manuales/content', $data);
    }
}