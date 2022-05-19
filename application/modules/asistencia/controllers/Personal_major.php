<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Personal_major extends MY_Controller
{

    /**
     * Controlador de Personal
     * Autor: Leandro
     * Creado: 16/09/2016
     * Modificado: 02/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('asistencia/Usuarios_oficinas_model');
        $this->grupos_permitidos = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_consulta_general');
        $this->grupos_rrhh = array('admin', 'asistencia_rrhh', 'asistencia_control', 'asistencia_consulta_general');
        $this->grupos_solo_consulta = array('asistencia_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function listar($secretaria = NULL, $ofi_Oficina = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            if ($ofi_Oficina !== NULL && !in_array($ofi_Oficina, $array_oficinas))
            {
                show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
            }
            if (!empty($secretaria))
            {
                try
                {
                    $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                        'form_params' => [
                            'con_Personal' => TRUE,
                            'ofi_Oficina' => $array_oficinas,
                            'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                            'ofi_Tipo' => 'Interna',
                            'ofi_Agrupamiento' => "$secretaria%"
                    ]]);
                    $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
                } catch (Exception $e)
                {
                    $oficinas_major = NULL;
                }
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_Oficina' => $array_oficinas,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }
        else
        {
            if (!empty($secretaria))
            {
                try
                {
                    $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                        'form_params' => [
                            'con_Personal' => TRUE,
                            'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                            'ofi_Tipo' => 'Interna',
                            'ofi_Agrupamiento' => "$secretaria%"
                    ]]);
                    $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
                } catch (Exception $e)
                {
                    $oficinas_major = NULL;
                }
            }
            try
            {
                $http_response_secretarias = $guzzleHttp->request('POST', "personas/oficinas", [
                    'form_params' => [
                        'solo_Secretarias' => TRUE,
                        'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                        'ofi_Tipo' => 'Interna',
                ]]);
                $secretarias_major = json_decode($http_response_secretarias->getBody()->getContents());
            } catch (Exception $e)
            {
                $secretarias_major = NULL;
            }
        }

        $array_secretaria = array();
        if (!empty($secretarias_major))
        {
            foreach ($secretarias_major as $Secretaria_major)
            {
                $array_secretaria[substr($Secretaria_major->ofi_Agrupamiento, 0, 5)] = $Secretaria_major->ofi_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('asistencia/escritorio', 'refresh');
        }
        $data['secretaria_opt'] = $array_secretaria;
        $data['secretaria_id'] = $secretaria;

        $array_oficinas = array();
        if (!empty($oficinas_major))
        {
            foreach ($oficinas_major as $Oficina_major)
            {
                $array_oficinas[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
            }
        }
        $data['oficina_opt'] = $array_oficinas;
        $data['oficina_id'] = $ofi_Oficina;

        if ($ofi_Oficina !== NULL && ctype_digit($ofi_Oficina))
        {
            $tableData = array(
                'columns' => array(
                    array('label' => 'Legajo', 'data' => 'labo_Codigo', 'width' => 12, 'class' => 'dt-body-right', 'responsive_class' => 'all'),
                    array('label' => 'Apellido', 'data' => 'pers_Apellido', 'width' => 18),
                    array('label' => 'Nombre', 'data' => 'pers_Nombre', 'width' => 18),
                    array('label' => 'Cód. Horario', 'data' => 'hora_Codigo', 'class' => 'dt-body-right', 'width' => 10),
                    array('label' => 'Horario', 'data' => 'hora_Descripcion', 'width' => 26),
                    array('label' => 'Inicio Secuencia', 'data' => 'hoca_FechaSecuencia1', 'render' => 'date', 'width' => 10, 'class' => 'dt-body-right'),
                    array('label' => '', 'data' => 'calendario', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                    array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
                ),
                'table_id' => 'personal_table',
                'server_side' => FALSE,
                'source_url' => "asistencia/personal_major/listar_data/$ofi_Oficina",
                'reuse_var' => TRUE,
                'initComplete' => "complete_personal_table",
                'footer' => TRUE,
                'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
            );

            $data['html_table'] = buildHTML($tableData);
            $data['js_table'] = buildJS($tableData);
        }
        $data['error'] = !empty($data['error']) ? $data['error'] : $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de personal con horarios Major';
        $data['title'] = TITLE . ' - Personal Major';
        $data['js'] = 'js/asistencia/base.js';
        $this->load_template('asistencia/personal_major/personal_listar', $data);
    }

    public function listar_data($ofi_Oficina = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $ofi_Oficina === NULL || !ctype_digit($ofi_Oficina))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        if (!in_groups($this->grupos_rrhh, $this->grupos))
        {
            $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
            if (empty($oficinas))
            {
                $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                redirect('asistencia/escritorio', 'refresh');
            }
            $array_oficinas = array();
            foreach ($oficinas as $Oficina)
            {
                $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
            }
            if ($ofi_Oficina !== NULL && !in_array($ofi_Oficina, $array_oficinas))
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
            $http_response_empleado = $guzzleHttp->request('GET', "personas/listar", ['query' => ['ofi_Oficina' => $ofi_Oficina, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
            $empleados = json_decode($http_response_empleado->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleados = NULL;
        }

        if (!empty($empleados))
        {
            $personal['data'] = $empleados;
            foreach ($personal['data'] as $i => $Personal)
            {
                $personal['data'][$i]->calendario = '<a href="asistencia/personal_major/calendario/' . $Personal->labo_Codigo . '" title="Ver calendario" class="btn btn-primary btn-xs"><i class="fa fa-calendar"></i></a>';
                $personal['data'][$i]->ver = '<a href="asistencia/fichadas/ver/' . $Personal->labo_Codigo . '" title="Ver fichadas" class="btn btn-primary btn-xs"><i class="fa fa-clock-o"></i></a>';
            }
            echo json_encode($personal);
        }
        else
        {
            echo json_encode(array('data' => array()));
        }
    }

    public function buscador()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'legajo' => array('label' => 'Legajo', 'type' => 'integer', 'maxlength' => '8'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
        );
        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $labo_Codigo = $this->input->post('legajo');
            $pers_Apellido = $this->input->post('apellido');
            if (empty($labo_Codigo) && empty($pers_Apellido))
            {
                $error_msg = '<br />Debe ingresar Legajo o Apellido';
            }
            else
            {
                $guzzleHttp = new GuzzleHttp\Client([
                    'base_uri' => $this->config->item('rest_server2'),
                    'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                    'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
                ]);

                try
                {
                    $http_response_empleado = $guzzleHttp->request('GET', "personas/datos_leg_ape", ['query' => ['pers_Apellido' => $pers_Apellido, 'labo_Codigo' => $labo_Codigo, 'fecha' => date_format(new Datetime(), 'Ymd')]]);
                    $empleados = json_decode($http_response_empleado->getBody()->getContents());
                } catch (Exception $e)
                {
                    $empleados = NULL;
                }

                if (!empty($empleados))
                {
                    if (!in_groups($this->grupos_rrhh, $this->grupos) && $labo_Codigo !== $this->session->userdata('username'))
                    {
                        $this->load->model('asistencia/Usuarios_oficinas_model');
                        $ofi_Oficina = array();
                        $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id')));
                        if (!empty($oficinas))
                        {
                            foreach ($oficinas as $Oficina)
                            {
                                $ofi_Oficina[] = $Oficina->ofi_Oficina;
                            }
                        }
                        foreach ($empleados as $key => $Empleado)
                        {
                            if (!in_array($Empleado->ofi_Oficina, $ofi_Oficina))
                            {
                                unset($empleados[$key]);
                            }
                        }
                    }
                    $data['empleados'] = $empleados;
                }

                if (empty($empleados))
                {
                    $error_msg = '<br />No se encontraron empleados';
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['fields'] = $this->build_fields($fake_model->fields);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Buscar';
        $data['title_view'] = 'Buscador de personal por legajo o apellido';
        $data['title'] = TITLE . ' - Buscador';
        $this->load_template('asistencia/personal_major/personal_buscador', $data);
    }

    public function buscar()
    {
        if (!in_groups($this->grupos_rrhh, $this->grupos))
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
                $this->load->model('asistencia/Usuarios_model');
                $usuario = $this->Usuarios_model->get(array('username' => $labo_Codigo));
                if (empty($usuario))
                {
                    $data['usuario'] = 'Sin Usuario';
                }
                else
                {
                    $grupos_asistencia = array('asistencia_rrhh', 'asistencia_control', 'asistencia_contralor', 'asistencia_director', 'asistencia_user');
                    if ($this->ion_auth->in_group($grupos_asistencia, $usuario[0]->id))
                    {
                        $data['usuario'] = 'Usuario Asistencia';
                    }
                    else
                    {
                        $data['usuario'] = 'Usuario General';
                    }
                }
                $data['empleado'] = $empleado;
            }
            else
            {
                $data['error'] = 'Legajo no encontrado';
            }
        }
        else
        {
            $data['error'] = 'Debe ingresar un legajo válido';
        }
        echo json_encode($data);
    }

    public function calendario($labo_Codigo = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $labo_Codigo == NULL || !ctype_digit($labo_Codigo))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'legajo' => array('label' => 'Legajo', 'disabled' => 'disabled'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'disabled' => 'disabled'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'disabled' => 'disabled')
        );

        $guzzleHttp = new GuzzleHttp\Client([
            'base_uri' => $this->config->item('rest_server2'),
            'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
            'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
        ]);

        //BUSCO DATOS DEL PERSONAL DE MAJOR
        try
        {
            $http_response_empleado = $guzzleHttp->request('GET', "personas/datos_sh", ['query' => ['labo_Codigo' => $labo_Codigo]]);
            $empleado = json_decode($http_response_empleado->getBody()->getContents());
        } catch (Exception $e)
        {
            $empleado = NULL;
        }

        if (!empty($empleado))
        {
            $personal_major['data'] = $empleado;
            $personal_horario = new stdClass();
            $personal_horario->legajo = $personal_major['data']->labo_Codigo;
            $personal_horario->apellido = $personal_major['data']->pers_Apellido;
            $personal_horario->nombre = $personal_major['data']->pers_Nombre;
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Legajo no encontrado');
            redirect('asistencia/personal_major/listar', 'refresh');
        }

        $data['fields'] = $this->build_fields($fake_model->fields, $personal_horario, TRUE);
        $data['personal_horario'] = $personal_horario;
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver calendario de personal';
        $data['css'][] = 'css/asistencia/asistencia.css';
        $data['css'][] = 'vendor/fullcalendar/packages/core/main.min.css';
        $data['css'][] = 'vendor/fullcalendar/packages/daygrid/main.min.css';
        $data['js'][] = 'vendor/fullcalendar/packages/core/main.min.js';
        $data['js'][] = 'vendor/fullcalendar/packages/core/locales/es.js';
        $data['js'][] = 'vendor/fullcalendar/packages/daygrid/main.min.js';
        $data['title'] = TITLE . ' - Ver calendario de personal';
        $this->load_template('asistencia/personal_major/personal_calendario', $data);
    }

    public function get_oficinas_secretaria()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('secretaria', 'Secretaría', 'required');
        if ($this->form_validation->run() === TRUE)
        {
            $secretaria = $this->input->post('secretaria');
            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            if (!in_groups($this->grupos_rrhh, $this->grupos))
            {
                $oficinas = $this->Usuarios_oficinas_model->get(array('user_id' => $this->session->userdata('user_id'), 'sort_by' => 'ofi_Oficina'));
                if (empty($oficinas))
                {
                    $this->session->set_flashdata('error', '<br />No tiene oficinas asignadas');
                    redirect('asistencia/escritorio', 'refresh');
                }
                $array_oficinas = array();
                foreach ($oficinas as $Oficina)
                {
                    $array_oficinas[$Oficina->ofi_Oficina] = $Oficina->ofi_Oficina;
                }
                try
                {
                    $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                        'form_params' => [
                            'con_Personal' => TRUE,
                            'ofi_Oficina' => $array_oficinas,
                            'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                            'ofi_Tipo' => 'Interna',
                            'ofi_Agrupamiento' => "$secretaria%"
                    ]]);
                    $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
                } catch (Exception $e)
                {
                    $oficinas_major = NULL;
                }
            }
            else
            {
                try
                {
                    $http_response_oficinas = $guzzleHttp->request('POST', "personas/oficinas", [
                        'form_params' => [
                            'con_Personal' => TRUE,
                            'ofi_OficinaEjercicio' => date_format(new Datetime(), 'Y'),
                            'ofi_Tipo' => 'Interna',
                            'ofi_Agrupamiento' => "$secretaria%"
                    ]]);
                    $oficinas_major = json_decode($http_response_oficinas->getBody()->getContents());
                } catch (Exception $e)
                {
                    $oficinas_major = NULL;
                }
            }

            $array_oficina = array();
            if (!empty($oficinas_major))
            {
                foreach ($oficinas_major as $Oficina_major)
                {
                    $array_oficina[$Oficina_major->ofi_Oficina] = "$Oficina_major->ofi_Oficina - $Oficina_major->ofi_Descripcion";
                }
            }

            echo json_encode($array_oficina);
        }
    }

    public function get_feriados()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data_feriados = NULL;
        $this->form_validation->set_rules('start', 'Inicio', 'date|required');
        $this->form_validation->set_rules('end', 'Fin', 'date|required');
        if ($this->form_validation->run() === TRUE)
        {
            $inicio = substr($this->input->post('start'), 0, 10);
            $fin = substr($this->input->post('end'), 0, 10);

            //INICIALIZO FECHAS
            $fecha_inicial = new DateTime($inicio);
            $fecha_inicial->setTime(0, 0);
            $fecha_final = new DateTime($fin);
            $fecha_final->setTime(0, 0);

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $fecha_inicial->format('Ymd'), 'hasta' => $fecha_final->format('Ymd')]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
			} catch (Exception $e)
            {
                $feriados = NULL;
            }

            if (!empty($feriados))
            {
                $feriados['data'] = $feriados;
                foreach ($feriados['data'] as $Feriado)
                {
                    $data_feriados[] = array('nombre' => $Feriado->feri_Descripcion, 'fecha' => (new DateTime($Feriado->feri_Fecha))->format('Y-m-d'));
                }
            }
        }
        else
        {
            echo 'error';
        }

        echo json_encode($data_feriados);
    }

    public function get_horarios()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $data_calendario = NULL;
        $this->form_validation->set_rules('legajo', 'Legajo', 'integer|required');
        $this->form_validation->set_rules('start', 'Inicio', 'date|required');
        $this->form_validation->set_rules('end', 'Fin', 'date|required');
        if ($this->form_validation->run() === TRUE)
        {
            $labo_Codigo = $this->input->post('legajo');
            $inicio = substr($this->input->post('start'), 0, 10);
            $fin = substr($this->input->post('end'), 0, 10);

            //INICIALIZO FECHAS
            $fecha_inicial = new DateTime($inicio);
            $fecha_inicial->setTime(0, 0);
            $fecha_final = new DateTime($fin);
            $fecha_final->setTime(0, 0);

            $guzzleHttp = new GuzzleHttp\Client([
                'base_uri' => $this->config->item('rest_server2'),
                'auth' => [$this->config->item('rest_server2_user'), $this->config->item('rest_server2_pass'), 'digest'],
                'headers' => ['API-KEY' => $this->config->item('rest_server2_key')]
            ]);
            //BUSCO DATOS DEL PERSONAL DE MAJOR
            try
            {
                $http_response_empleado = $guzzleHttp->request('GET', "personas/horario_detallado", ['query' => ['labo_Codigo' => $labo_Codigo, 'desde' => $fecha_inicial->format('Ymd'), 'hasta' => $fecha_final->format('Ymd')]]);
                $personal_major = json_decode($http_response_empleado->getBody()->getContents());
            } catch (Exception $e)
            {
                $personal_major = NULL;
            }

            //BUSCO FERIADOS
            try
            {
                $http_response_feriados = $guzzleHttp->request('GET', "personas/feriados", ['query' => ['desde' => $fecha_inicial->format('Ymd'), 'hasta' => $fecha_final->format('Ymd')]]);
                $feriados = json_decode($http_response_feriados->getBody()->getContents());
            } catch (Exception $e)
            {
                $feriados = NULL;
            }

            $tmp_feriados = array();
            if (!empty($feriados))
            {
                foreach ($feriados as $Feriado)
                {
                    $tmp_feriados[(new DateTime($Feriado->feri_Fecha))->format('dmY')] = $Feriado->feri_Descripcion;
                }
            }

            //BUSCO HORARIOS DIARIOS
            try
            {
                $http_response_horarios_diarios = $guzzleHttp->request('GET', "personas/horarios_diarios", ['query' => ['labo_Codigo' => $labo_Codigo, 'desde' => $fecha_inicial->format('Ymd'), 'hasta' => $fecha_final->format('Ymd')]]);
                $horarios_diarios = json_decode($http_response_horarios_diarios->getBody()->getContents());
            } catch (Exception $e)
            {
                $horarios_diarios = NULL;
            }

            $tmp_horarios_diarios = array();
            if (!empty($horarios_diarios))
            {
                foreach ($horarios_diarios as $Diario)
                {
                    $tmp_horarios_diarios[(new DateTime($Diario->hodi_Fecha))->format('dmY')] = array('E' => $Diario->hodi_Entrada, 'S' => $Diario->hodi_Salida);
                }
            }

            if (!empty($personal_major))
            {
                $sin_fecha_hasta = 0;
                foreach ($personal_major as $Emp)
                {
                    //INICIALIZO FECHAS (HORARIO)
                    $fecha_inicial_horario = new Datetime($Emp->hoca_FechaDesde);
                    $fecha_final_horario = new Datetime($Emp->hoca_FechaHasta);
                    if ($fecha_final_horario->format('Ymd') === '21000101')
                    {
                        $sin_fecha_hasta++;
                    }
                    if ($fecha_inicial_horario <= $fecha_final && $fecha_final_horario > $fecha_inicial)
                    {
                        if ($fecha_inicial_horario > $fecha_inicial)
                        {
                            $fecha_inicial_while = clone $fecha_inicial_horario;
                        }
                        else
                        {
                            $fecha_inicial_while = clone $fecha_inicial;
                        }
                        if ($fecha_final_horario < $fecha_final)
                        {
                            $fecha_final_while = clone $fecha_final_horario;
                        }
                        else
                        {
                            $fecha_final_while = clone $fecha_final;
                        }

                        if (!empty($Emp->hora_Codigo))
                        {
                            if ($Emp->hora_Tipo === 'N' || $Emp->hora_Tipo === 'F')
                            {
                                while ($fecha_inicial_while <= $fecha_final_while)
                                {
                                    $fecha = clone $fecha_inicial_while;
                                    if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_horarios_diarios))
                                    {
                                        $horario_diario = $tmp_horarios_diarios[$fecha_inicial_while->format('dmY')];
                                        if ($horario_diario['E'] !== '00:00')
                                        {
                                            $fecha->setTime(substr($horario_diario['E'], 0, 2), substr($horario_diario['E'], 3, 2));
                                            $data_calendario[] = array('title' => 'Entrada', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#31708f', 'borderColor' => '#31708f');
                                        }
                                        if ($horario_diario['S'] !== '00:00') //DIA CON S PARA EL EMPLEADO
                                        {
                                            $fecha->setTime(substr($horario_diario['S'], 0, 2), substr($horario_diario['S'], 3, 2));
                                            $data_calendario[] = array('title' => 'Salida', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#31708f', 'borderColor' => '#31708f');
                                        }
                                    }
                                    else
                                    {
                                        if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_feriados))
                                        {
                                            $numero_dia = 8;
                                        }
                                        else
                                        {
                                            $numero_dia = $fecha->format('w');
                                            if ($numero_dia === '0')
                                            {
                                                $numero_dia = 7; //DOMINGOS ES 7 EN MAJOR
                                            }
                                        }

                                        if ($Emp->{'hora_DiaSec' . $numero_dia . 'Ent'} !== '00:00') //DIA CON E PARA EL EMPLEADO
                                        {
                                            $fecha->setTime(substr($Emp->{'hora_DiaSec' . $numero_dia . 'Ent'}, 0, 2), substr($Emp->{'hora_DiaSec' . $numero_dia . 'Ent'}, 3, 2));
                                            $data_calendario[] = array('title' => 'Entrada', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#5cb85c', 'borderColor' => '#5cb85c');
                                        }
                                        if ($Emp->{'hora_DiaSec' . $numero_dia . 'Sal'} !== '00:00') //DIA CON S PARA EL EMPLEADO
                                        {
                                            $fecha->setTime(substr($Emp->{'hora_DiaSec' . $numero_dia . 'Sal'}, 0, 2), substr($Emp->{'hora_DiaSec' . $numero_dia . 'Sal'}, 3, 2));
                                            $data_calendario[] = array('title' => 'Salida', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#d9534f', 'borderColor' => '#d9534f');
                                        }
                                    }

                                    $fecha_inicial_while->add(new DateInterval('P1D'));
                                }
                            }
                            elseif ($Emp->hora_Tipo === 'R')
                            {
                                $inicio_sec = new DateTime($Emp->hoca_FechaSecuencia1);
                                if ($fecha_inicial < $inicio_sec)
                                {
                                    $numero_dia = 1;
                                    $fecha_inicial = clone $inicio_sec;
                                    $data['error'] = '<br />Verifique la fecha de inicio de la secuencia del personal';
                                }

                                $dias_inicio_sec = $fecha_inicial->diff($inicio_sec)->format("%a");
                                $dias_inicio_sec++; //SI ES EL PRIMER DÍA DEVUELVE 0 Y DEBERIA SER 1
                                $sum_dias_sec = 0;
                                for ($i = 1; $i <= 20; $i++)
                                {
                                    $sum_dias_sec += (int) $Emp->{"hora_DiaSec" . $i . "Cant"};
                                }

                                if ($fecha_inicial_while < $inicio_sec)
                                {
                                    $fecha_inicial_while = clone $inicio_sec;
                                }
                                while ($fecha_inicial_while < $fecha_final_while)
                                {
                                    $fecha = clone $fecha_inicial_while;
                                    if (array_key_exists($fecha_inicial_while->format('dmY'), $tmp_horarios_diarios))
                                    {
                                        $horario_diario = $tmp_horarios_diarios[$fecha_inicial_while->format('dmY')];
                                        if ($horario_diario['E'] !== '00:00')
                                        {
                                            $fecha->setTime(substr($horario_diario['E'], 0, 2), substr($horario_diario['E'], 3, 2));
                                            $data_calendario[] = array('title' => 'Entrada', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#31708f', 'borderColor' => '#31708f');
                                        }
                                        if ($horario_diario['S'] !== '00:00') //DIA CON S PARA EL EMPLEADO
                                        {
                                            $fecha->setTime(substr($horario_diario['S'], 0, 2), substr($horario_diario['S'], 3, 2));
                                            $data_calendario[] = array('title' => 'Salida', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#31708f', 'borderColor' => '#31708f');
                                        }
                                    }
                                    else
                                    {
                                        $resto_sec = $dias_inicio_sec % $sum_dias_sec;
                                        if ($resto_sec === 0)
                                        {
                                            $resto_sec = $sum_dias_sec;
                                        }
                                        for ($i = 1; $i <= 20; $i++)
                                        {
                                            if ($resto_sec <= (int) $Emp->{"hora_DiaSec" . $i . "Cant"})
                                            {
                                                $numero_dia = $i;
                                                break;
                                            }
                                            else
                                            {
                                                $resto_sec -= (int) $Emp->{"hora_DiaSec" . $i . "Cant"};
                                            }
                                        }

                                        if ($Emp->{'hora_DiaSec' . $numero_dia . 'Ent'} !== '00:00') //DIA CON E PARA EL EMPLEADO
                                        {
                                            $fecha->setTime(substr($Emp->{'hora_DiaSec' . $numero_dia . 'Ent'}, 0, 2), substr($Emp->{'hora_DiaSec' . $numero_dia . 'Ent'}, 3, 2));
                                            $data_calendario[] = array('title' => 'Entrada', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#5cb85c', 'borderColor' => '#5cb85c');
                                        }
                                        if ($Emp->{'hora_DiaSec' . $numero_dia . 'Sal'} !== '00:00') //DIA CON S PARA EL EMPLEADO
                                        {
                                            $fecha->setTime(substr($Emp->{'hora_DiaSec' . $numero_dia . 'Sal'}, 0, 2), substr($Emp->{'hora_DiaSec' . $numero_dia . 'Sal'}, 3, 2));
                                            $data_calendario[] = array('title' => 'Salida', 'start' => $fecha->format('c'), 'allDay' => false, 'backgroundColor' => '#d9534f', 'borderColor' => '#d9534f');
                                        }
                                    }
                                    $fecha_inicial_while->add(new DateInterval('P1D'));
                                    $dias_inicio_sec++;
                                }
                            }
                        }
                        else
                        {
                            $data['error'] = '<br />Verifique el horario del personal';
                        }
                    }
                }
                if ($sin_fecha_hasta > 1)
                {
                    $data['error'] = '<br />Verifique que los horarios anteriores tengan fecha hasta';
                }
            }
            else
            {
                echo 'error';
            }
        }
        else
        {
            echo 'error';
        }

        echo json_encode($data_calendario);
    }
}
