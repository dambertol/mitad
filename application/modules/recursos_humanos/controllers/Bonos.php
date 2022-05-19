<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bonos extends MY_Controller
{

    /**
     * Controlador de Bonos de Sueldo
     * Autor: Leandro
     * Creado: 26/02/2020
     * Modificado: 30/04/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('recursos_humanos/Bonos_model');
        $this->grupos_permitidos = array('admin', 'recursos_humanos_bonos');
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
                array('label' => 'Año', 'data' => 'liqu_Anio', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Mes', 'data' => 'liqu_Mes', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'N°', 'data' => 'liqu_Numero', 'width' => 6, 'class' => 'dt-body-right'),
                array('label' => 'Categoría', 'data' => 'categoria', 'width' => 17),
                array('label' => 'Oficina', 'data' => 'oficina', 'width' => 18),
                array('label' => 'Legajo', 'data' => 'legajo', 'width' => 8, 'class' => 'dt-body-right'),
                array('label' => 'Código', 'data' => 'codigo', 'width' => 8),
                array('label' => 'Carga', 'data' => 'fecha', 'width' => 8, 'render' => 'datetime', 'class' => 'dt-body-right'),
                array('label' => 'Email', 'data' => 'email', 'width' => 12),
                array('label' => 'Envío', 'data' => 'envio', 'width' => 8),
                array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
            ),
            'table_id' => 'bonos_table',
            'source_url' => 'recursos_humanos/bonos/listar_data',
            'reuse_var' => TRUE,
            'initComplete' => 'complete_bonos_table',
            'footer' => TRUE,
            'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
        );
        $data['html_table'] = buildHTML($tableData);
        $data['js_table'] = buildJS($tableData);
        $data['array_envios'] = array('' => 'Todos', 'NULL' => 'Sin Envío', 'Pendiente' => 'Pendiente', 'Enviando' => 'Enviando', 'Enviado' => 'Enviado', 'Falló' => 'Falló');

        $data['error'] = $this->session->flashdata('error');
        $data['message'] = $this->session->flashdata('message');
        $data['title_view'] = 'Listado de Envíos de Bonos';
        $data['title'] = TITLE . ' - Envíos de Bonos';
        $this->load_template('recursos_humanos/bonos/bonos_listar', $data);
    }

    public function listar_data()
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->load->helper('recursos_humanos/datatables_functions_helper');
        $this->datatables
                ->select("rh_bonos.id, rh_bonos.legajo, rh_bonos.liqu_Anio, rh_bonos.liqu_Mes, rh_bonos.liqu_Numero, CONCAT(rh_bonos.cate_Codigo, ' - ', rh_bonos.cate_Descripcion) as categoria, CONCAT(rh_bonos.ofi_Oficina, ' - ', rh_bonos.ofi_Descripcion) as oficina, rh_bonos.codigo, rh_bonos.fecha, email_queue.to as email, email_queue.status as envio")
                ->from('rh_bonos')
                ->join('email_queue', 'email_queue.id = rh_bonos.envio_id', 'left')
                ->edit_column('envio', '$1', 'dt_column_bonos_envio(envio)', TRUE)
                ->add_column('ver', '', 'id');

        echo $this->datatables->generate();
    }

    public function enviar_emails()
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

        try
        {
            $http_response_categorias = $guzzleHttp->request('GET', "personas/categorias");
            $categorias_major = json_decode($http_response_categorias->getBody()->getContents());
        } catch (Exception $e)
        {
            $categorias_major = NULL;
        }

        $array_categoria = array();
        if (!empty($categorias_major))
        {
            foreach ($categorias_major as $Categoria_major)
            {
                $array_categoria[$Categoria_major->cate_Codigo] = $Categoria_major->cate_Descripcion;
            }
        }
        else
        {
            $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
            redirect('recursos_humanos/escritorio', 'refresh');
        }
        $this->array_categoria_control = $array_categoria;

        $this->array_tipo_control = $array_tipo = array(
            '1' => 'MENSUAL MUNICIPAL',
            '2' => 'MENSUAL HCD',
            '5' => 'MENSUAL SEOS',
            '3' => 'AGUINALDO MUNICIPAL',
            '4' => 'AGUINALDO HCD',
            '6' => 'AGUINALDO SEOS'
        );

        $fake_model = new stdClass();
        $fake_model->fields = array(
            'mes' => array('label' => 'Mes', 'type' => 'monthyear', 'required' => TRUE),
            'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
            'categoria' => array('label' => 'Categorías', 'input_type' => 'combo', 'type' => 'multiple_bselect', 'bselect_all' => TRUE, 'required' => TRUE),
            'leyenda' => array('label' => 'Leyenda', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
        );

        $this->set_model_validation_rules($fake_model);
        $error_msg = FALSE;
        if ($this->form_validation->run() === TRUE)
        {
            $cate_Codigo = $this->input->post('categoria');
            $liqu_Numero = $this->input->post('tipo');
            $mes = explode('/', $this->input->post('mes'));
            $leyenda = $this->input->post('leyenda');

            $bono = $this->Bonos_model->get(array(
                'where_in' => array(array('column' => 'cate_Codigo', 'value' => $cate_Codigo)),
                'liqu_Numero' => $liqu_Numero,
                'liqu_Anio' => $mes[1],
                'liqu_Mes' => $mes[0]
            ));
            if (!empty($bono))
            {
                $error_msg = '<br>Ya se generaron los envíos para la Categoría seleccionada';
            }

            if (empty($error_msg))
            {
                //BUSCA TODOS LOS EMPLEADOS DE LAS CATEGORIAS SELECCIONADAS
                try
                {
                    $http_response_personal = $guzzleHttp->request('GET', "personas/empleados_categorias", ['query' => ['cate_Codigo' => $cate_Codigo, 'liqu_Numero' => $liqu_Numero, 'liqu_Anio' => $mes[1], 'liqu_Mes' => $mes[0]]]);
                    $personal = json_decode($http_response_personal->getBody()->getContents());
                } catch (Exception $e)
                {
                    $personal = NULL;
                }

                if (!empty($personal))
                {
                    $this->db->trans_begin();
                    $trans_ok = TRUE;
                    foreach ($personal as $Per)
                    {
                        $envio_id = NULL;
                        if (!empty($Per->comu_Identificacion))
                        {
                            $result = $this->queue_email(
                                    'recursos_humanos/email/bono_sueldo',
                                    'Bono de Sueldo',
                                    $Per->comu_Identificacion,
                                    array('apellido' => $Per->pers_Apellido, 'nombre' => $Per->pers_Nombre, 'codigo' => $Per->codigo, 'leyenda' => $leyenda)
                            );
                            if ($result)
                            {
                                $envio_id = $result;
                            }
                        }

                        $trans_ok &= $this->Bonos_model->create(array(
                            'legajo' => $Per->labo_Codigo,
                            'liqu_Anio' => $mes[1],
                            'liqu_Mes' => $mes[0],
                            'liqu_Numero' => $liqu_Numero,
                            'cate_Codigo' => $Per->cate_Codigo,
                            'cate_Descripcion' => $Per->cate_Descripcion,
                            'ofi_Oficina' => $Per->ofi_Oficina,
                            'ofi_Descripcion' => $Per->ofi_Descripcion,
                            'codigo' => $Per->codigo,
                            'fecha' => (new DateTime())->format('y-m-d H:i'),
                            'envio_id' => $envio_id), FALSE);
                    }

                    if ($this->db->trans_status() && $trans_ok)
                    {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('message', $this->Bonos_model->get_msg());
                        redirect('recursos_humanos/bonos/listar', 'refresh');
                    }
                    else
                    {
                        $this->db->trans_rollback();
                        $error_msg = '<br />Se ha producido un error con la base de datos.';
                        if ($this->Bonos_model->get_error())
                        {
                            $error_msg .= $this->Bonos_model->get_error();
                        }
                    }
                }
                elseif (isset($personal['data']->error))
                {
                    $this->session->set_flashdata('error', '<br />Error al conectarse a Major<br />Intente nuevamente más tarde');
                    redirect('recursos_humanos/escritorio', 'refresh');
                }
                else
                {
                    $this->session->set_flashdata('error', '<br />No se encontraron personas en la categoria seleccionada');
                    redirect('recursos_humanos/bonos/enviar_emails', 'refresh');
                }
            }
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $fake_model->fields['tipo']['array'] = $array_tipo;
        $fake_model->fields['categoria']['array'] = $array_categoria;

        //OPCIONES POR DEFECTO
        $default = new stdClass();
        $default->categoria_id = NULL;
        $default->tipo_id = NULL;
        $default->mes = NULL;
        $default->anio = 'now';

        $data['fields'] = $this->build_fields($fake_model->fields, $default);
        $data['message'] = $this->session->flashdata('message');
        $data['txt_btn'] = 'Enviar';
        $data['title_view'] = 'Envío masivo de Bonos de Sueldo';
        $data['title'] = TITLE . ' - Bonos de Sueldo';
        $this->load_template('recursos_humanos/bonos/bonos_enviar', $data);
    }

    private function queue_email($template, $title, $to, $data)
    {
        if (SIS_EMAIL_MODULO)
        {
            $this->email->initialize();
            $message = $this->load->view($template, $data, TRUE);
            $this->email->clear(TRUE);
            $this->email->set_mailtype("html");
            $this->email->from($this->config->item('admin_email', 'ion_auth'), $this->config->item('site_title', 'ion_auth'));
            $this->email->to($to);
            $this->email->subject($this->config->item('site_title', 'ion_auth') . ' - ' . $title);
            $this->email->message($message);

            return $this->email->queue();
        }
        else
        {
            return TRUE;
        }
    }
}
