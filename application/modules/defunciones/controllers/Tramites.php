<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites extends MY_Controller
{

    /**
     * Controlador de Operaciones
     * Autor: Leandro
     * Creado: 22/11/2019
     * Modificado: 10/03/2021 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('defunciones/Difuntos_model');
        $this->load->model('defunciones/Solicitantes_model');
        $this->grupos_permitidos = array('admin', 'defunciones_user', 'defunciones_consulta_general');
        $this->grupos_solo_consulta = array('defunciones_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function index()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        redirect('defunciones/tramites/iniciar', 'refresh');
    }

    public function nuevo()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("defunciones/operaciones/listar", 'refresh');
        }

        $this->array_solicitante_control = $array_solicitante = $this->get_array('Solicitantes', 'descripcion', 'id', array('select' => array("id, CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', nombre) as descripcion"), 'sort_by' => 'dni'), array('0' => '--- Agregar ---'));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'solicitante' => array('label' => 'Solicitante', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );

        $this->set_model_validation_rules($fake_model);
        if ($this->form_validation->run() === TRUE)
        {
            $solicitante_id = $this->input->post('solicitante');
            if ($solicitante_id === '0')
            {
                redirect('defunciones/solicitantes/agregar/concesiones/0/1', 'refresh');
            }
            else
            {
                redirect("defunciones/difuntos/agregar/$solicitante_id/0/1", 'refresh');
            }
        }
        $data['error'] = validation_errors();

        $fake_model->fields['solicitante']['array'] = $array_solicitante;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Nuevo Trámite';
        $data['title'] = TITLE . ' - Nuevo Trámite';
        $this->load_template('defunciones/tramites/tramites_nuevo', $data);
    }

    public function nuevo_ver_dif($tipo_operacion, $difunto_id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || empty($tipo_operacion) || empty($difunto_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("defunciones/operaciones/listar", 'refresh');
        }

        $this->array_solicitante_control = $array_solicitante = $this->get_array('Solicitantes', 'descripcion', 'id', array('select' => array("id, CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', nombre) as descripcion"), 'sort_by' => 'dni'), array('0' => '--- Agregar ---'));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'solicitante' => array('label' => 'Solicitante', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        if ($this->form_validation->run() === TRUE)
        {
            $solicitante_id = $this->input->post('solicitante');
            if ($solicitante_id === '0')
            {
                redirect("defunciones/solicitantes/agregar/$tipo_operacion/$difunto_id", 'refresh');
            }
            else
            {
                redirect("defunciones/$tipo_operacion/agregar/$solicitante_id/$difunto_id", 'refresh');
            }
        }
        $data['error'] = validation_errors();

        $fake_model->fields['solicitante']['array'] = $array_solicitante;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Nuevo Trámite';
        $data['title'] = TITLE . ' - Nuevo Trámite';
        $this->load_template('defunciones/tramites/tramites_nuevo', $data);
    }

    public function nuevo_ver_sol($tipo_operacion, $solicitante_id)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || empty($tipo_operacion) || empty($solicitante_id))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("defunciones/operaciones/listar", 'refresh');
        }

        $this->array_difunto_control = $array_difunto = $this->get_array('Difuntos', 'descripcion', 'id', array('select' => array("id, CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', apellido, ', ', nombre) as descripcion"), 'sort_by' => 'dni'), array('0' => '--- Agregar ---'));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'difunto' => array('label' => 'Difunto', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        if ($this->form_validation->run() === TRUE)
        {
            $difunto_id = $this->input->post('difunto');
            if ($difunto_id === '0')
            {
                redirect("defunciones/difuntos/agregar/$solicitante_id/$tipo_operacion", 'refresh');
            }
            else
            {
                redirect("defunciones/$tipo_operacion/agregar/$solicitante_id/$difunto_id", 'refresh');
            }
        }

        $fake_model->fields['difunto']['array'] = $array_difunto;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Nuevo Trámite';
        $data['title'] = TITLE . ' - Nuevo Trámite';
        $this->load_template('defunciones/tramites/tramites_nuevo', $data);
    }

    public function iniciar($solicitante_id = 0, $difunto_id = 0, $operacion_id = 'concesiones') //No quitar $operacion_id
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("defunciones/operaciones/listar", 'refresh');
        }

        $this->array_solicitante_control = $array_solicitante = $this->get_array('Solicitantes', 'descripcion', 'id', array('select' => array("id, CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', nombre) as descripcion"), 'sort_by' => 'dni'), array('0' => '--- Agregar ---'));
        $this->array_difunto_control = $array_difunto = $this->get_array('Difuntos', 'descripcion', 'id', array('select' => array("id, CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', apellido, ', ', nombre) as descripcion"), 'sort_by' => 'dni'), array('0' => '--- Agregar ---'));
        $this->array_operacion_control = $array_operacion = array('concesiones' => 'Concesión', 'ornatos' => 'Ornato', 'reducciones' => 'Reducción', 'traslados' => 'Traslado');

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'solicitante' => array('label' => 'Solicitante', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'difunto' => array('label' => 'Difunto', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'operacion' => array('label' => 'Operación', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
        );
        $this->set_model_validation_rules($fake_model);
        if ($this->form_validation->run() === TRUE)
        {
            $solicitante_id = $this->input->post('solicitante');
            $difunto_id = $this->input->post('difunto');
            $operacion = $this->input->post('operacion');
            if ($solicitante_id === '0')
            {
                if ($difunto_id === '0')
                {
                    redirect("defunciones/solicitantes/agregar/$operacion", 'refresh');
                }
                else
                {
                    redirect("defunciones/solicitantes/agregar/$operacion/$difunto_id", 'refresh');
                }
            }
            elseif ($difunto_id === '0')
            {
                redirect("defunciones/difuntos/agregar/$solicitante_id/$operacion", 'refresh');
            }
            else
            {
                redirect("defunciones/$operacion/agregar/$solicitante_id/$difunto_id", 'refresh');
            }
        }
        $data['error'] = validation_errors();

        $fake_model->fields['difunto']['array'] = $array_difunto;
        $fake_model->fields['solicitante']['array'] = $array_solicitante;
        $fake_model->fields['operacion']['array'] = $array_operacion;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Iniciar Trámite';
        $data['title'] = TITLE . ' - Iniciar Trámite';
        $this->load_template('defunciones/tramites/tramites_nuevo', $data);
    }

    public function iniciar_compra($solicitante_id = 0)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (in_groups($this->grupos_solo_consulta, $this->grupos))
        {
            $this->session->set_flashdata('error', 'Usuario sin permisos de edición');
            redirect("defunciones/operaciones/listar", 'refresh');
        }

        $this->array_solicitante_control = $array_solicitante = $this->get_array('Solicitantes', 'descripcion', 'id', array('select' => array("id, CONCAT(COALESCE(dni, 'Sin DNI'), ' - ', nombre) as descripcion"), 'sort_by' => 'dni'), array('0' => '--- Agregar ---'));

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'solicitante' => array('label' => 'Solicitante', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
        );
        $this->set_model_validation_rules($fake_model);
        if ($this->form_validation->run() === TRUE)
        {
            $solicitante_id = $this->input->post('solicitante');
            if ($solicitante_id === '0')
            {
                redirect("defunciones/solicitantes/agregar/compras_terrenos", 'refresh');
            }
            else
            {
                redirect("defunciones/compras_terrenos/agregar/$solicitante_id", 'refresh');
            }
        }
        $data['error'] = validation_errors();

        $fake_model->fields['solicitante']['array'] = $array_solicitante;
        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['txt_btn'] = 'Agregar';
        $data['title_view'] = 'Iniciar Compra de Terreno';
        $data['title'] = TITLE . ' - Iniciar Compra de Terreno';
        $this->load_template('defunciones/tramites/tramites_nuevo', $data);
    }
}
