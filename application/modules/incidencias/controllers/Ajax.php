<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{

    /**
     * Controlador de Ajax
     * Autor: Leandro
     * Creado: 15/04/2019
     * Modificado: 26/06/2020 (Leandro)
     */
    public function __construct()
    {
        parent::__construct();

        $this->grupos_ajax = array('admin', 'incidencias_admin', 'incidencias_user', 'incidencias_area', 'incidencias_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function buscar_categoria_sector()
    {
        if (!in_groups($this->grupos_ajax, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('sector_id', 'Sector', 'required|integer');
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('incidencias/Categorias_model');
            $categorias = $this->Categorias_model->get(array(
                'sector_id' => $this->input->post('sector_id'),
                'sort_by' => 'descripcion'
            ));

            if (empty($categorias))
            {
                $datos['no_data'] = TRUE;
            }
            else
            {
                $datos['categorias'] = $categorias;
            }
        }
        else
        {
            $datos['no_data'] = TRUE;
        }

        echo json_encode($datos);
    }

    public function buscar_tecnico_categoria()
    {
        if (!in_groups($this->grupos_ajax, $this->grupos))
        {
            show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
        }

        $this->form_validation->set_rules('categoria_id', 'Categoría', 'required|integer');
        $this->form_validation->set_rules('incidencia_id', 'Incidencia', 'integer');
        if ($this->form_validation->run() === TRUE)
        {
            $this->load->model('Usuarios_model');
            $this->load->model('incidencias/Categorias_model');
            $categoria = $this->Categorias_model->get(array('id' => $this->input->post('categoria_id')));
            if (empty($categoria))
            {
                $datos['no_data'] = TRUE;
                $datos['error'] = 'No se encontró la Categoría';
                echo json_encode($datos);
                return;
            }

            if ($this->input->post('incidencia_id'))
            {
                $this->load->model('incidencias/Incidencias_model');
                $incidencia = $this->Incidencias_model->get(array('id' => $this->input->post('incidencia_id')));
                if (empty($incidencia))
                {
                    $datos['no_data'] = TRUE;
                    $datos['error'] = 'No se encontró la Incidencia';
                    echo json_encode($datos);
                    return;
                }

                $tecnicos = $this->Usuarios_model->get(array(
                    'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario",
                    'join' => array(
                        array('personas', 'personas.id = users.persona_id', 'LEFT'),
                        array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                        array('groups', 'users_groups.group_id = groups.id', 'LEFT'),
                        array('in_usuarios_sectores', 'in_usuarios_sectores.user_id = users.id', 'LEFT')
                    ),
                    'where' => array(
                        "(groups.name IN ('admin', 'incidencias_user', 'incidencias_admin') AND users.active = 1 AND in_usuarios_sectores.sector_id = $categoria->sector_id) OR users.id = $incidencia->tecnico_id"
                    ),
                    'group_by' => 'id, usuario',
                    'sort_by' => 'personas.apellido, personas.nombre'
                        )
                );
            }
            else
            {
                $tecnicos = $this->Usuarios_model->get(array(
                    'select' => "users.id, CONCAT(personas.apellido, ', ', personas.nombre) as usuario",
                    'join' => array(
                        array('personas', 'personas.id = users.persona_id', 'LEFT'),
                        array('users_groups', 'users_groups.user_id = users.id', 'LEFT'),
                        array('groups', 'users_groups.group_id = groups.id', 'LEFT'),
                        array('in_usuarios_sectores', 'in_usuarios_sectores.user_id = users.id', 'LEFT')
                    ),
                    'where' => array(
                        "(groups.name IN ('admin', 'incidencias_user', 'incidencias_admin') AND users.active = 1 AND in_usuarios_sectores.sector_id = $categoria->sector_id)"
                    ),
                    'group_by' => 'id, usuario',
                    'sort_by' => 'personas.apellido, personas.nombre'
                        )
                );
            }

            if (empty($tecnicos))
            {
                $datos['no_data'] = TRUE;
            }
            else
            {
                $datos['tecnicos'] = $tecnicos;
            }
        }
        else
        {
            $datos['no_data'] = TRUE;
        }

        echo json_encode($datos);
    }
}
