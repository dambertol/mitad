<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pases extends MY_Controller
{

    /**
     * Controlador de Pases
     * Autor: Leandro
     * Creado: 28/06/2018
     * Modificado: 11/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('transferencias/Adjuntos_model');
        $this->load->model('transferencias/Pases_model');
        $this->grupos_permitidos = array('admin', 'transferencias_municipal', 'transferencias_area', 'transferencias_publico', 'transferencias_consulta_general');
        $this->grupos_municipal = array('admin', 'transferencias_municipal', 'transferencias_area', 'transferencias_consulta_general');
        $this->grupos_solo_consulta = array('transferencias_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function modal_ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
        {
            return $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        if (in_groups($this->grupos_municipal, $this->grupos))
        {
            $this->Pases_model->fields['usuario_origen'] = array('label' => 'Usuario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE);
        }

        $pase = $this->Pases_model->get_one($id);
        if (empty($pase))
        {
            return $this->modal_error('No se encontró el Pase', 'Registro no encontrado');
        }

        $adjuntos = $this->Adjuntos_model->get(array('pase_id' => $id));

        $data['fields'] = $this->build_fields($this->Pases_model->fields, $pase, TRUE);
        $data['pase'] = $pase;
        $data['adjuntos'] = $adjuntos;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Pase';
        $data['title'] = TITLE . ' - Ver Pase';
        $this->load->view('transferencias/pases/pases_modal_abm', $data);
    }
}
