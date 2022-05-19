<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inicio extends MY_Controller
{

    /**
     * Controlador de Inicio
     * Autor: Leandro
     * Creado: 12/07/2018
     * Modificado: 22/03/2021 (Leandro)
     */
    public function __construct()
    {
        $this->auth = FALSE;
        parent::__construct();
        $this->load->model('lujan_pass/Agrupamientos_model');
        $this->load->model('lujan_pass/Campanias_model');
        $this->load->model('lujan_pass/Categorias_model');
        $this->load->model('lujan_pass/Comercios_model');
        $this->load->model('lujan_pass/Comercios_categorias_model');
        $this->load->model('lujan_pass/Promociones_model');
        $this->load->model('Localidades_model');
        $this->agrupamiento_id_turismo = '2';
        // Inicializaciones necesarias colocar acá.
    }

    public function index($agrupamiento_id = NULL, $categoria_id = NULL)
    {
        if ($agrupamiento_id == NULL || (!ctype_digit($agrupamiento_id) && $agrupamiento_id !== 'promo'))
        {
            $agrupamiento_id = $this->agrupamiento_id_turismo;
        }

        $agrupamientos = $this->Agrupamientos_model->get(array('id' => $this->agrupamiento_id_turismo));

        if ($agrupamiento_id === 'promo')
        {
            $categorias = $this->Campanias_model->get(array(
                'agrupamiento_id' => $this->agrupamiento_id_turismo,
                'activo' => 'SI',
                'visible' => 'SI',
                'sort_by' => 'orden'
            ));
        }
        else
        {
            $categorias = $this->Categorias_model->get(array(
                'agrupamiento_id' => $agrupamiento_id,
                'sort_by' => 'orden'
            ));
        }

        if (empty($categorias))
        {
            redirect('lujan_pass/front/inicio');
        }

        $data['error'] = json_encode($this->session->flashdata('error'));
        $data['message'] = json_encode($this->session->flashdata('message'));

        $data['agrupamiento_id'] = $agrupamiento_id;
        $data['categoria_id'] = $categoria_id;
        $data['agrupamiento_id_turismo'] = $this->agrupamiento_id_turismo;
        $data['categorias'] = $categorias;
        $data['image'] = '';
        $data['title'] = TITLE;
        $data['usuario_logueado'] = $this->ion_auth->logged_in();
        $this->load_template('lujan_pass/front/inicio/inicio_content', $data);
    }

    public function get_comercios()
    {
        $this->form_validation->set_rules('categoria_id', 'Categoría', 'integer');
        $this->form_validation->set_rules('texto', 'Texto', 'trim');
        if ($this->form_validation->run() === TRUE)
        {
            $options = array(
                'select' => array(
                    'ta_comercios.id', 'ta_comercios.imagen_url', 'ta_comercios.nombre as comercio', "COALESCE(ta_comercios.comentarios, ' ') as comentarios", 'MIN(ta_categorias.nombre) as categoria', 'ta_comercios.web', 'ta_comercios.facebook', 'ta_comercios.instagram', 'ta_comercios.twitter', 'ta_comercios.latitud', 'ta_comercios.longitud'
                ),
                'join' => array(
                    array('ta_comercios_categorias', 'ta_comercios_categorias.comercio_id = ta_comercios.id', 'LEFT'),
                    array('ta_categorias', 'ta_categorias.id = ta_comercios_categorias.categoria_id', 'LEFT')
                ),
                'estado' => 'Aprobado',
                'sort_by' => 'RAND()'
            );

            if ($this->input->post('categoria_id') && $this->input->post('categoria_id') !== '0')
            {
                $options['where'] = array(array('column' => 'ta_categorias.id', 'value' => $this->input->post('categoria_id')));
            }

            if ($this->input->post('texto') && $this->input->post('texto') !== '')
            {
                if ($this->input->post('tipo') === 'categoria')
                {
                    $options['where'] = array(array('column' => 'ta_categorias.nombre LIKE', 'value' => '%' . $this->input->post('texto') . '%'));
                }
                else
                {
                    $options['where'] = array(array('column' => 'ta_comercios.nombre LIKE', 'value' => '%' . $this->input->post('texto') . '%'));
                }
            }

            $options['group_by'] = array('ta_comercios.id', 'ta_comercios.imagen_url', 'ta_comercios.nombre', 'ta_comercios.comentarios', 'ta_comercios.web', 'ta_comercios.facebook', 'ta_comercios.instagram', 'ta_comercios.twitter');
            $comercios = $this->Comercios_model->get($options);
            if (!empty($comercios))
            {
                foreach ($comercios as $key => $Comercio)
                {
                    if (!file_exists($Comercio->imagen_url))
                    {
                        $comercios[$key]->imagen_url = 'img/lujan_pass/noimage.png';
                    }
                }
                $data['comercios'] = $comercios;
            }
            else
            {
                $data['error'] = 'Comercios no encontrados';
            }
        }
        else
        {
            $data['error'] = 'Falló validación';
        }
        echo json_encode($data);
    }

    public function get_promociones()
    {
        $this->form_validation->set_rules('categoria_id', 'Categoría', 'integer');
        $this->form_validation->set_rules('texto', 'Texto', 'trim');
        if ($this->form_validation->run() === TRUE)
        {
            $options = array(
                'select' => array(
                    'ta_promociones.id', 'ta_promociones.imagen_url', 'ta_comercios.imagen_url as imagen_url_comercio', 'ta_comercios.nombre as comercio', "COALESCE(ta_promociones.descripcion, ' ') as comentarios", 'ta_campanias.nombre as categoria', 'ta_comercios.web', 'ta_comercios.facebook', 'ta_comercios.instagram', 'ta_comercios.twitter', 'ta_comercios.latitud', 'ta_comercios.longitud'
                ),
                'join' => array(
                    array('ta_campanias', 'ta_campanias.id = ta_promociones.campania_id', 'LEFT'),
                    array('ta_comercios', 'ta_comercios.id = ta_promociones.comercio_id', 'LEFT')
                ),
                'estado' => 'Aprobado',
                'sort_by' => 'RAND()'
            );

            if ($this->input->post('categoria_id') && $this->input->post('categoria_id') !== '0')
            {
                $options['where'] = array(array('column' => 'ta_campanias.id', 'value' => $this->input->post('categoria_id')));
            }

            if ($this->input->post('texto') && $this->input->post('texto') !== '')
            {
                if ($this->input->post('tipo') === 'categoria')
                {
                    $options['where'] = array(array('column' => 'ta_campanias.nombre LIKE', 'value' => '%' . $this->input->post('texto') . '%'));
                }
                else
                {
                    $options['where'] = array(array('column' => 'ta_comercios.nombre LIKE', 'value' => '%' . $this->input->post('texto') . '%'));
                }
            }

            $promociones = $this->Promociones_model->get($options);
            if (!empty($promociones))
            {
                foreach ($promociones as $key => $Promocion)
                {
                    if (!file_exists($Promocion->imagen_url))
                    {
                        if (!file_exists($Promocion->imagen_url_comercio))
                        {
                            $promociones[$key]->imagen_url = 'img/lujan_pass/noimage.png';
                        }
                        else
                        {
                            $promociones[$key]->imagen_url = $promociones[$key]->imagen_url_comercio;
                        }
                    }
                }
                $data['comercios'] = $promociones;
            }
            else
            {
                $data['error'] = 'Descuentos no encontrados';
            }
        }
        else
        {
            $data['error'] = 'Falló validación';
        }
        echo json_encode($data);
    }

    public function get_comercio()
    {
        $this->form_validation->set_rules('comercio_id', 'Comercio', 'integer');
        if ($this->form_validation->run() === TRUE)
        {
            $comercio = $this->Comercios_model->get_one($this->input->post('comercio_id'));
            if (!empty($comercio))
            {
                $categorias = array();
                $comercios_categorias = $this->Comercios_categorias_model->get(array(
                    'comercio_id' => $comercio->id,
                    'join' => array(
                        array('ta_categorias', 'ta_categorias.id = ta_comercios_categorias.categoria_id', 'left', 'ta_categorias.nombre as categoria')
                    )
                ));
                if (!empty($comercios_categorias))
                {
                    foreach ($comercios_categorias as $CC)
                    {
                        $categorias[] = $CC->categoria;
                    }
                }
                $comercio->categoria = implode(', ', $categorias);
                $data['comercio'] = $comercio;
            }
            else
            {
                $data['error'] = 'Comercio no encontrado';
            }
        }
        else
        {
            $data['error'] = 'Falló validación';
        }
        $this->load->view('lujan_pass/front/inicio/comercio_content', $data);
    }

    public function get_promocion()
    {
        $this->form_validation->set_rules('comercio_id', 'Comercio', 'integer');
        if ($this->form_validation->run() === TRUE)
        {
            $promocion = $this->Promociones_model->get_one($this->input->post('comercio_id'));
            if (!empty($promocion))
            {
                $promocion->categoria = $promocion->campania;
                $data['promocion'] = $promocion;
            }
            else
            {
                $data['error'] = 'Descuento no encontrado';
            }
        }
        else
        {
            $data['error'] = 'Falló validación';
        }
        $this->load->view('lujan_pass/front/inicio/promocion_content', $data);
    }

    protected function load_template($contenido = 'general', $datos = NULL)
    {
        $data['menu'] = $this->load->view('lujan_pass/front/template/menu', $datos, TRUE);
        $data['content'] = $this->load->view($contenido, $datos, TRUE);
        $data['footer'] = $this->load->view('lujan_pass/front/template/footer', $datos, TRUE);
        $this->load->view('lujan_pass/front/template/template', $data);
    }
}
