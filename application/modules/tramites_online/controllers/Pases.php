<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Pases extends MY_Controller
{

    /**
     * Controlador de Pases
     * Autor: Leandro
     * Creado: 16/03/2020
     * Modificado: 08/08/2021 (Leandro)
     * Modificado: 11/01/2022 (Matias)
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tramites_online/Adjuntos_model');
        $this->load->model('tramites_online/Campos_model');
        $this->load->model('tramites_online/Datos_model');
        $this->load->model('tramites_online/Tramites_padrones_model');
        $this->load->model('tramites_online/Tramites_model');
        $this->load->model('tramites_online/Padrones_model');
        $this->load->model('tramites_online/Pases_model');
        $this->load->model('tramites_online/Pasos_model');
        $this->load->model('tramites_online/Usuarios_oficinas_model');
        $this->load->model('Personas_model');
        $this->load->model('tramites_online/Iniciadores_model');
        $this->grupos_permitidos = array('admin', 'tramites_online_admin', 'tramites_online_area', 'tramites_online_publico', 'tramites_online_consulta_general');
        $this->grupos_publico = array('tramites_online_publico');
        $this->grupos_area = array('tramites_online_area');
        $this->grupos_solo_consulta = array('tramites_online_consulta_general');
        // Inicializaciones necesarias colocar acá.
    }

    public function modal_ver($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            return $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        $pase = $this->Pases_model->get_one($id);
        if (empty($pase)) {
            return $this->modal_error('No se encontró el Pase', 'Registro no encontrado');
        }

        $persona = $this->Personas_model->get(array(
                    'select' => array('personas.dni', 'personas.cuil', 'personas.nombre', 'personas.apellido', 'personas.celular','personas.telefono', 'personas.email'),
                    'join' => array(
                        array('to2_iniciadores', 'to2_iniciadores.persona_id = personas.id', 'LEFT'),
                    ),
                    'where' => array('to2_iniciadores.persona_id = personas.id')           
        ));
    

        // BUSCA LOS PASOS Y FORMULARIOS NECESARIOS PARA EL ESTADO ACTUAl
        $pasos = $this->Pasos_model->get(array(
                'select' => array('to2_formularios.nombre', 'to2_formularios.descripcion', 'to2_pasos.orden', 'to2_pasos.modo', 'to2_pasos.regla', 'to2_pasos.padron', 'to2_pasos.formulario_id', 'to2_pasos.mensaje'),
                'join' => array(
                    array('to2_formularios', 'to2_formularios.id = to2_pasos.formulario_id', 'left'),
                ),
                'estado_id' => $pase->eo_id,
                'sort_by' => 'orden')
        );
        if (empty($pasos)) {
            show_error('No se encontraron Pasos', 500, 'Registro no encontrado');
        }

        foreach ($pasos as $Paso) {
            // TODO: Manejar distintas posiblidades para el enum padron
            if ($Paso->padron === 'Obligatorio') {
                $datos_padron = $this->Tramites_padrones_model->get(
                    array(
                        'select' => array('to2_tramites_padrones.repeticion', 'to2_padrones.nomenclatura', 'to2_padrones.padron', 'to2_padrones.tit_dni', 'to2_padrones.tit_apellido', 'to2_padrones.tit_nombre', 'to2_tramites_padrones.adjunto_id', 'to2_tramites_padrones.consulta', 'to2_adjuntos.ruta', 'to2_adjuntos.nombre'),
                        'join' => array(
                            array('to2_padrones', 'to2_tramites_padrones.padron_id = to2_padrones.id', 'left'),
                            array('to2_adjuntos', 'to2_tramites_padrones.adjunto_id = to2_adjuntos.id', 'left')
                        ),
                        'pase_id' => $pase->id
                    )
                );

                $cant_[$Paso->orden] = sizeof($datos_padron);

                $array_datos_padron = array();
                if (!empty($datos_padron)) {
                    foreach ($datos_padron as $Dato) {
                        $array_datos_padron["nomenclatura"][$Dato->repeticion] = !empty($Dato->nomenclatura) ? $Dato->nomenclatura : "";
                        $array_datos_padron["padron"][$Dato->repeticion] = !empty($Dato->padron) ? $Dato->padron : "";
                        $array_datos_padron["tit_dni"][$Dato->repeticion] = !empty($Dato->tit_dni) ? $Dato->tit_dni : "";
                        $array_datos_padron["tit_apellido"][$Dato->repeticion] = !empty($Dato->tit_apellido) ? $Dato->tit_apellido : "";
                        $array_datos_padron["tit_nombre"][$Dato->repeticion] = !empty($Dato->tit_nombre) ? $Dato->tit_nombre : "";
                        $array_datos_padron["consulta"][$Dato->repeticion] = !empty($Dato->consulta) ? $Dato->consulta : "";
                        $array_datos_padron["comprobante"][$Dato->repeticion] = !empty($Dato->adjunto_id) ? $Dato->ruta . $Dato->nombre : "";
                    }
                }

                // CREA MODELO PARA EL PADRON
                $fake_models[$Paso->orden] = new stdClass();
                $fake_models[$Paso->orden]->nombre = 'Inmueble';
                $fake_models[$Paso->orden]->subtitulo = 'Datos del inmueble';
                $fake_models[$Paso->orden]->regla = $Paso->regla;
                $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                $fake_models[$Paso->orden]->allFields = new stdClass();
                for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                    $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                    $fake_models[$Paso->orden]->allFields->{$i}->fields = array(
                        "{$Paso->orden}_nomenclatura_{$i}" => array('label' => 'Nomenclatura', 'type' => 'natural', 'maxlength' => '20', 'minlength' => '20', 'extra_button' => 'Buscar', 'extra_button_click' => "buscar_inmueble(this);", 'required' => TRUE),
                        "{$Paso->orden}_padron_{$i}" => array('label' => 'Padrón Municipal', 'maxlength' => '20', 'readonly' => TRUE, 'required' => TRUE),
                        "{$Paso->orden}_tit_dni_{$i}" => array('label' => 'Documento Titular', 'maxlength' => '20', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_apellido_{$i}" => array('label' => 'Apellido Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_nombre_{$i}" => array('label' => 'Nombre Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_sup_terreno_{$i}" => array('label' => 'Superficie Terreno', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_calle_{$i}" => array('label' => 'Calle', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_distrito_{$i}" => array('label' => 'Distrito', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_zona_urb_{$i}" => array('label' => 'Zona Urbanística', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_ordenanza_{$i}" => array('label' => 'Ordenanza', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_deuda_{$i}" => array('label' => 'Deuda', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_consulta_{$i}" => array('label' => 'Fecha Consulta', 'type' => 'date', 'readonly' => TRUE),
                        "{$Paso->orden}_comprobante_{$i}" => array('label' => 'Comprobante Pago', 'type' => 'file', 'form_type' => 'file', 'readonly' => TRUE)
                    );
                    $fake_models[$Paso->orden]->allFields->{$i}->valores = new stdClass();
                    $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});

                    // VALORES
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_nomenclatura_{$i}"} = !empty($array_datos_padron["nomenclatura"][$i]) ? $array_datos_padron["nomenclatura"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_padron_{$i}"} = !empty($array_datos_padron["padron"][$i]) ? $array_datos_padron["padron"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_dni_{$i}"} = !empty($array_datos_padron["tit_dni"][$i]) ? $array_datos_padron["tit_dni"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_apellido_{$i}"} = !empty($array_datos_padron["tit_apellido"][$i]) ? $array_datos_padron["tit_apellido"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_nombre_{$i}"} = !empty($array_datos_padron["tit_nombre"][$i]) ? $array_datos_padron["tit_nombre"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_sup_terreno_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_calle_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_distrito_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_zona_urb_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_ordenanza_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_deuda_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_consulta_{$i}"} = !empty($array_datos_padron["consulta"][$i]) ? $array_datos_padron["consulta"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_comprobante_{$i}"} = !empty($array_datos_padron["comprobante"][$i]) ? $array_datos_padron["comprobante"][$i] : NULL;
                }
            } else {
                // DUPLICACION DE LOS FIELDS DE UN FORM
                if ($Paso->regla === 'Multiple') {
                    $cantidad = $this->db->query("SELECT COUNT(to2_datos.id) as cantidad "
                        . 'FROM to2_campos '
                        . 'JOIN to2_datos ON to2_datos.campo_id = to2_campos.id AND to2_datos.pase_id = ? '
                        . 'WHERE formulario_id = ? '
                        . 'GROUP BY to2_campos.id ', array($pase->id, $Paso->formulario_id))->row();

                    if (!empty($cantidad->cantidad)) {
                        $cant_[$Paso->orden] = $cantidad->cantidad;
                    } else {
                        $cant_[$Paso->orden] = 1;
                    }
                } else {
                    $cant_[$Paso->orden] = 1;
                }

                $campos = $this->Campos_model->get(
                    array(
                        'select' => array('to2_campos.id', 'to2_campos.etiqueta', 'to2_campos.opciones', 'to2_campos.tipo', 'to2_campos.obligatorio'),
                        'formulario_id' => $Paso->formulario_id,
                        'sort_by' => 'posicion'
                    )
                );

                $datos = $this->Campos_model->get(
                    array(
                        'select' => array('to2_campos.id', 'to2_datos.repeticion', 'to2_datos.valor', 'to2_datos.adjunto_id', 'to2_adjuntos.ruta', 'to2_adjuntos.nombre'),
                        'join' => array(
                            array('to2_datos', "to2_datos.campo_id = to2_campos.id AND to2_datos.pase_id = $pase->id", 'left'),
                            array('to2_adjuntos', "to2_adjuntos.id = to2_datos.adjunto_id", 'left')
                        ),
                        'formulario_id' => $Paso->formulario_id,
                        'sort_by' => 'posicion'
                    )
                );

                $array_datos = array();
                if (!empty($datos)) {
                    foreach ($datos as $Dato) {
                        $array_datos[$Dato->id][$Dato->repeticion] = !empty($Dato->valor) ? $Dato->valor : (!empty($Dato->adjunto_id) ? $Dato->ruta . $Dato->nombre : "");
                    }
                }

                if (!empty($campos)) {
                    // CREA MODELO PARA EL FORMULARIO
                    $fake_models[$Paso->orden] = new stdClass();
                    $fake_models[$Paso->orden]->nombre = $Paso->nombre;
                    $fake_models[$Paso->orden]->subtitulo = $Paso->descripcion;
                    $fake_models[$Paso->orden]->regla = $Paso->regla;
                    $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                    $fake_models[$Paso->orden]->allFields = new stdClass();
                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                        $fake_models[$Paso->orden]->allFields->{$i}->fields = [];
                        $fake_models[$Paso->orden]->allFields->{$i}->valores = new stdClass();
                    }

                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        foreach ($campos as $Campo) {
                            // TODO: Manejo de validaciones (required, maxlength, etc)
                            switch ($Campo->tipo) {
                                case 'combo':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'input_type' => $Campo->tipo, 'type' => 'bselect', 'required' => TRUE];
                                    if(!empty($Campo->funcion)){
                                        $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['onchange'] = $Campo->funcion;
                                    }
                                    $opciones_tmp = explode("|", $Campo->opciones);
                                    $opciones = array();
                                    if (!empty($opciones_tmp)) {
                                        foreach ($opciones_tmp as $Opcion) {
                                            $opciones[$Opcion] = $Opcion;
                                        }
                                    }
                                    $this->{"array_campo_{$Campo->id}_{$i}_control"} = ${"array_campo_{$Campo->id}_{$i}"} = $opciones;
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['array'] = ${"array_campo_{$Campo->id}_{$i}"};
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'input':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'maxlength' => '50', 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'file':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'form_type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'textarea':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'h3':
                                case 'h4':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'value' => $Campo->etiqueta];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = $Campo->etiqueta;
                                default:
                                    break;
                            }
                        }
                        $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});
                    }
                }
            }
        }

        $fake_model_solicitante = new stdClass();
        $fake_model_solicitante->fields = array(
            'dni' => array('label' => 'DNI', 'maxlength' => '50'),
            'cuil' => array('label' => 'CUIL', 'maxlength' => '13'),
            'nombre' => array('label' => 'Nombre', 'maxlength' => '50'),
            'apellido' => array('label' => 'Apellido', 'maxlength' => '50'),
            'email' => array('label' => 'Email', 'type' => 'email', 'maxlength' => '100'),
            'telefono' => array('label' => 'Teléfono', 'maxlength' => '12'),
            'celular' => array('label' => 'Celular', 'maxlength' => '12')
        );
        $data['fields_solicitante'] = $this->build_fields($fake_model_solicitante->fields, $persona[0], TRUE);
        
        
        $data['fields'] = $this->build_fields($this->Pases_model->fields, $pase, TRUE);
        $data['pase'] = $pase;


        if (!empty($fake_models)) {
            foreach ($fake_models as $paso_id => $paso) {
                $data['fields_group'][$paso_id]['nombre'] = $paso->nombre;
                $data['fields_group'][$paso_id]['subtitulo'] = $paso->subtitulo;
                $data['fields_group'][$paso_id]['regla'] = $paso->regla;
                $data['fields_group'][$paso_id]['mensaje'] = $paso->mensaje;
                foreach ($paso->allFields as $array_fields) {
                    $data['fields_group'][$paso_id]['allFields'][] = $this->build_fields($array_fields->fields, $array_fields->valores, TRUE);
                }
            }
        }
        
        $data['txt_btn'] = NULL;
        $data['title_view'] = 'Ver Pase';
        $data['title'] = TITLE . ' - Ver Pase';
        $data['js'][] = 'js/tramites_online/base.js';
        $this->load->view('tramites_online/pases/pases_modal_abm', $data);
    }


    /**
     * @param $id
     */

    public function modal_editar($id = NULL)
    {
        if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)) {
            return $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
        }

        $pase = $this->Pases_model->get_one($id);
        if (empty($pase)) {
            return $this->modal_error('No se encontró el Pase', 'Registro no encontrado');
        }

        $tramite = $this->Tramites_model->get_one($pase->tramite_id);
        // El tramite es editable?
        if (!$tramite->editable) {
            return $this->modal_error('El tramite no se puede editar ', 'Tramite no editable');
        }
        // El pase es editable?
        if ($pase->estado_origen_editable !== 'SI') {
            return $this->modal_error('El pase no se puede editar ', 'Pase no editable');
        }


        /**
         * Control de edicion para publico y areas
         * si es publico [ oficina no publica || no es suyo el tramite ]
         */
        if (in_groups($this->grupos_publico, $this->grupos)) {
            // Si el usuario es del grupo publico: la oficina no es la publica y el tramite no es de él
            if (!is_null($pase->estado_origen_oficina) || $this->Personas_model->get_user_id($tramite->persona_id) !== $this->session->userdata('user_id')) {
                return $this->modal_error('No puede editar este Pase', 'Pase no publico');
            }
        } else if (in_groups($this->grupos_area, $this->grupos)) {
            // El usuario es del grupo areas, el pase es de su area

            $usuario_oficinas = $this->Usuarios_oficinas_model->get_oficina_id($this->session->userdata('user_id'));
            $este_pase = $this->Pases_model->get(array(
                    'id' => $id,
                    'select' => array('oficina_id'),
                    'join' => array(
                        array('to2_estados', 'to2_estados.id = to2_pases.estado_origen_id', 'left'
                        ),
                        array('to2_oficinas', 'to2_oficinas.id = to2_estados.oficina_id', 'left',
                            [
                                'to2_oficinas.oficina_id'
                            ]
                        ),
                    ),
                )
            );

            if (!in_array($este_pase->oficina_id, $usuario_oficinas)) {
                return $this->modal_error('No puede editar este Pase', 'Pase no es de su area');
            }
        }

        // BUSCA LOS PASOS Y FORMULARIOS NECESARIOS PARA EL ESTADO ACTUAl
        $pasos = $this->Pasos_model->get(array(
                'select' => array('to2_formularios.nombre', 'to2_formularios.descripcion', 'to2_pasos.orden', 'to2_pasos.modo', 'to2_pasos.regla', 'to2_pasos.padron', 'to2_pasos.formulario_id', 'to2_pasos.mensaje'),
                'join' => array(
                    array('to2_formularios', 'to2_formularios.id = to2_pasos.formulario_id', 'left'),
                ),
                'estado_id' => $pase->eo_id,
                'sort_by' => 'orden')
        );
        if (empty($pasos)) {
            show_error('No se encontraron Pasos', 500, 'Registro no encontrado');
        }


        foreach ($pasos as $Paso) {
            // TODO: Manejar distintas posiblidades para el enum padron
            if ($Paso->padron === 'Obligatorio') {
                $datos_padron = $this->Tramites_padrones_model->get(
                    array(
                        'select' => array('to2_tramites_padrones.repeticion', 'to2_padrones.nomenclatura', 'to2_padrones.tit_dni', 'to2_padrones.tit_apellido', 'to2_padrones.tit_nombre', 'to2_tramites_padrones.adjunto_id', 'to2_tramites_padrones.consulta', 'to2_adjuntos.ruta', 'to2_adjuntos.nombre'),
                        'join' => array(
                            array('to2_padrones', 'to2_tramites_padrones.padron_id = to2_padrones.id', 'left'),
                            array('to2_adjuntos', 'to2_tramites_padrones.adjunto_id = to2_adjuntos.id', 'left')
                        ),
                        'pase_id' => $pase->id
                    )
                );

                $cant_[$Paso->orden] = sizeof($datos_padron);

                $array_datos_padron = array();
                if (!empty($datos_padron)) {
                    foreach ($datos_padron as $Dato) {
                        $array_datos_padron["nomenclatura"][$Dato->repeticion] = !empty($Dato->nomenclatura) ? $Dato->nomenclatura : "";
                        $array_datos_padron["padron"][$Dato->repeticion] = !empty($Dato->padron) ? $Dato->padron : "";
                        $array_datos_padron["tit_dni"][$Dato->repeticion] = !empty($Dato->tit_dni) ? $Dato->tit_dni : "";
                        $array_datos_padron["tit_apellido"][$Dato->repeticion] = !empty($Dato->tit_apellido) ? $Dato->tit_apellido : "";
                        $array_datos_padron["tit_nombre"][$Dato->repeticion] = !empty($Dato->tit_nombre) ? $Dato->tit_nombre : "";
                        $array_datos_padron["consulta"][$Dato->repeticion] = !empty($Dato->consulta) ? $Dato->consulta : "";
                        $array_datos_padron["comprobante"][$Dato->repeticion] = !empty($Dato->adjunto_id) ? $Dato->ruta . $Dato->nombre : "";
                    }
                }
                //     $this->dump($datos_padron);

                // CREA MODELO PARA EL PADRON
                $fake_models[$Paso->orden] = new stdClass();
                $fake_models[$Paso->orden]->nombre = 'Inmueble';
                $fake_models[$Paso->orden]->subtitulo = 'Datos del inmueble';
                $fake_models[$Paso->orden]->regla = $Paso->regla;
                $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                $fake_models[$Paso->orden]->allFields = new stdClass();

                for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                    $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                    $fake_models[$Paso->orden]->allFields->{$i}->fields = array(
                        "{$Paso->orden}_nomenclatura_{$i}" => array('label' => 'Nomenclatura', 'type' => 'natural', 'maxlength' => '20', 'minlength' => '20', 'extra_button' => 'Buscar', 'extra_button_click' => "buscar_inmueble(this);", 'required' => TRUE),
                        "{$Paso->orden}_padron_{$i}" => array('label' => 'Padrón Municipal', 'maxlength' => '20', 'readonly' => TRUE, 'required' => TRUE),
                        "{$Paso->orden}_tit_dni_{$i}" => array('label' => 'Documento Titular', 'maxlength' => '20', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_apellido_{$i}" => array('label' => 'Apellido Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_tit_nombre_{$i}" => array('label' => 'Nombre Titular', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_sup_terreno_{$i}" => array('label' => 'Superficie Terreno', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_calle_{$i}" => array('label' => 'Calle', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_distrito_{$i}" => array('label' => 'Distrito', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_zona_urb_{$i}" => array('label' => 'Zona Urbanística', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_ordenanza_{$i}" => array('label' => 'Ordenanza', 'maxlength' => '50', 'readonly' => TRUE),
                        "{$Paso->orden}_deuda_{$i}" => array('label' => 'Deuda', 'type' => 'numeric', 'readonly' => TRUE),
                        "{$Paso->orden}_consulta_{$i}" => array('label' => 'Fecha Consulta', 'type' => 'date', 'readonly' => TRUE),
                        "{$Paso->orden}_comprobante_{$i}" => array('label' => 'Comprobante Pago', 'type' => 'file', 'form_type' => 'file')
                    );
                    $fake_models[$Paso->orden]->allFields->{$i}->valores = new stdClass();
                    //$this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});

                    // VALORES
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_nomenclatura_{$i}"} = !empty($array_datos_padron["nomenclatura"][$i]) ? $array_datos_padron["nomenclatura"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_padron_{$i}"} = !empty($array_datos_padron["padron"][$i]) ? $array_datos_padron["padron"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_dni_{$i}"} = !empty($array_datos_padron["tit_dni"][$i]) ? $array_datos_padron["tit_dni"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_apellido_{$i}"} = !empty($array_datos_padron["tit_apellido"][$i]) ? $array_datos_padron["tit_apellido"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_tit_nombre_{$i}"} = !empty($array_datos_padron["tit_nombre"][$i]) ? $array_datos_padron["tit_nombre"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_sup_terreno_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_calle_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_distrito_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_zona_urb_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_ordenanza_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_deuda_{$i}"} = NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_consulta_{$i}"} = !empty($array_datos_padron["consulta"][$i]) ? $array_datos_padron["consulta"][$i] : NULL;
                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"{$Paso->orden}_comprobante_{$i}"} = !empty($array_datos_padron["comprobante"][$i]) ? $array_datos_padron["comprobante"][$i] : NULL;
                }
            } else {
                // DUPLICACION DE LOS FIELDS DE UN FORM
                if ($Paso->regla === 'Multiple') {
                    $cantidad = $this->db->query("SELECT COUNT(to2_datos.id) as cantidad "
                        . 'FROM to2_campos '
                        . 'JOIN to2_datos ON to2_datos.campo_id = to2_campos.id AND to2_datos.pase_id = ? '
                        . 'WHERE formulario_id = ? '
                        . 'GROUP BY to2_campos.id ', array($pase->id, $Paso->formulario_id))->row();

                    if (!empty($cantidad->cantidad)) {
                        $cant_[$Paso->orden] = $cantidad->cantidad;
                    } else {
                        $cant_[$Paso->orden] = 1;
                    }
                } else {
                    $cant_[$Paso->orden] = 1;
                }

                $campos = $this->Campos_model->get(
                    array(
                        'select' => array('to2_campos.id', 'to2_campos.etiqueta', 'to2_campos.opciones', 'to2_campos.tipo', 'to2_campos.obligatorio'),
                        'formulario_id' => $Paso->formulario_id,
                        'sort_by' => 'posicion'
                    )
                );

                $datos = $this->Campos_model->get(
                    array(
                        'select' => array('to2_campos.id', 'to2_datos.repeticion', 'to2_datos.valor', 'to2_datos.adjunto_id', 'to2_adjuntos.ruta', 'to2_adjuntos.nombre'),
                        'join' => array(
                            array('to2_datos', "to2_datos.campo_id = to2_campos.id AND to2_datos.pase_id = $pase->id", 'left'),
                            array('to2_adjuntos', "to2_adjuntos.id = to2_datos.adjunto_id", 'left')
                        ),
                        'formulario_id' => $Paso->formulario_id,
                        'sort_by' => 'posicion'
                    )
                );

                $array_datos = array();
                if (!empty($datos)) {
                    foreach ($datos as $Dato) {
                        $array_datos[$Dato->id][$Dato->repeticion] = !empty($Dato->valor) ? $Dato->valor : (!empty($Dato->adjunto_id) ? $Dato->ruta . $Dato->nombre : "");
                    }
                }

                if (!empty($campos)) {
                    // CREA MODELO PARA EL FORMULARIO
                    $fake_models[$Paso->orden] = new stdClass();
                    $fake_models[$Paso->orden]->nombre = $Paso->nombre;
                    $fake_models[$Paso->orden]->subtitulo = $Paso->descripcion;
                    $fake_models[$Paso->orden]->regla = $Paso->regla;
                    $fake_models[$Paso->orden]->mensaje = $Paso->mensaje;
                    $fake_models[$Paso->orden]->allFields = new stdClass();
                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        $fake_models[$Paso->orden]->allFields->{$i} = new stdClass();
                        $fake_models[$Paso->orden]->allFields->{$i}->fields = [];
                        $fake_models[$Paso->orden]->allFields->{$i}->valores = new stdClass();
                    }

                    for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {
                        foreach ($campos as $Campo) {
                            // TODO: Manejo de validaciones (required, maxlength, etc)
                            switch ($Campo->tipo) {
                                case 'combo':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'input_type' => $Campo->tipo, 'type' => 'bselect', 'required' => TRUE];
                                    if(!empty($Campo->funcion)){
                                        $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['onchange'] = $Campo->funcion;
                                    }
                                    $opciones_tmp = explode("|", $Campo->opciones);
                                    $opciones = array();
                                    if (!empty($opciones_tmp)) {
                                        foreach ($opciones_tmp as $Opcion) {
                                            $opciones[$Opcion] = $Opcion;
                                        }
                                    }
                                    $this->{"array_campo_{$Campo->id}_{$i}_control"} = ${"array_campo_{$Campo->id}_{$i}"} = $opciones;
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"]['array'] = ${"array_campo_{$Campo->id}_{$i}"};
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}_id"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'input':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'maxlength' => '50', 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'file':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'form_type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'textarea':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'required' => $Campo->obligatorio ? TRUE : FALSE, 'extra_param' => $Campo->nombre];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = !empty($array_datos[$Campo->id][$i]) ? $array_datos[$Campo->id][$i] : NULL;
                                    break;
                                case 'h3':
                                case 'h4':
                                    $fake_models[$Paso->orden]->allFields->{$i}->fields["campo_{$Campo->id}_{$i}"] = ['label' => $Campo->etiqueta, 'type' => $Campo->tipo, 'value' => $Campo->etiqueta];
                                    $fake_models[$Paso->orden]->allFields->{$i}->valores->{"campo_{$Campo->id}_{$i}"} = $Campo->etiqueta;
                                    break;
                                default:
                                    break;
                            }
                        }
                        $this->set_model_validation_rules($fake_models[$Paso->orden]->allFields->{$i});
                    }
                }
            }
        }


        $data['fields'] = $this->build_fields($this->Pases_model->fields, $pase, TRUE);
        $data['pase'] = $pase;


        if (!empty($fake_models)) {
            foreach ($fake_models as $paso_id => $paso) {
                $data['fields_group'][$paso_id]['nombre'] = $paso->nombre;
                $data['fields_group'][$paso_id]['subtitulo'] = $paso->subtitulo;
                $data['fields_group'][$paso_id]['regla'] = $paso->regla;
                $data['fields_group'][$paso_id]['mensaje'] = $paso->mensaje;
                foreach ($paso->allFields as $array_fields) {
                    $data['fields_group'][$paso_id]['allFields'][] = $this->build_fields($array_fields->fields, $array_fields->valores, FALSE);
                }
            }
        }


        if (isset($_POST) && !empty($_POST)) {

            if ($id != $this->input->post('id')) {
                show_error('Esta solicitud no pasó el control de seguridad.');
            }


            // Agregamos los nombres de los archivos al post para la validacion
            if (!empty($_FILES)) {
                foreach ($_FILES as $k => $v) {
                    $_POST[$k] = $v['name'];
                }
            }


            $error_msg = NULL;
            if ($this->form_validation->run() === TRUE && empty($error_msg)) {
                ////////////////////////////////////////////////////////////////////////////////////////////////////////
                ///  CONFIGURACION DEL LIB UPLOAD

                $uploads = array();
                if (!empty($_FILES)) {
                    $config_adjuntos['upload_path'] = "uploads/tramites_online/tramites/" . str_pad($pase->tramite_id, 6, "0", STR_PAD_LEFT) . "/";
                    if (!file_exists($config_adjuntos['upload_path'])) {
                        mkdir($config_adjuntos['upload_path'], 0755, TRUE);
                    }
                    $config_adjuntos['encrypt_name'] = TRUE;
                    $config_adjuntos['file_ext_tolower'] = TRUE;
                    $config_adjuntos['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx|dwg|dxf|dwf|zip|rar|application/octet-stream';
                    $config_adjuntos['max_size'] = 8192;
                    $this->load->library('upload', $config_adjuntos);
                }
                /////////////////////////////////////////////////////////////////////////////////////////////////////////

                $pase_id = $id;

                $fecha = new DateTime();
                $this->db->trans_begin();
                $trans_ok = TRUE;

                // GUARDA TODA LA INFO INGRESADA EN LOS DISTINTOS PASOS
                foreach ($pasos as $Paso) {
                    // SI TIENE PADRON ES UN CASO ESPECIAL
                    if ($Paso->padron === 'Obligatorio') {

                        // Actualizar cada padron y subir el comprabante si es que tiene
                        for ($i = 1; $i <= $cant_[$Paso->orden]; $i++) {


                            $tramites_padrones = $this->Tramites_padrones_model->get(array(
                                    'pase_id' => $pase_id,
                                    'repeticion' => $i,
                                )
                            )[0];

                            $padron_id = NULL;
                            $padron = $this->Padrones_model->get(array('nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i")));
                            if (empty($padron)) {
                                $trans_ok &= $this->Padrones_model->create(
                                    array(
                                        'codigo' => 1,
                                        'padron' => $this->input->post("{$Paso->orden}_padron_$i"),
                                        'nomenclatura' => $this->input->post("{$Paso->orden}_nomenclatura_$i")
                                    ), FALSE);

                                $padron_id = $this->Padrones_model->get_row_id();
                            } else {
                                $padron_id = $padron[0]->id;
                            }

                            if (!empty($padron_id)) {
                                // actualiza el tramite_padron
                                $trans_ok &= $this->Tramites_padrones_model->update(
                                    array(
                                        'id' => $tramites_padrones->id,
                                        'padron_id' => $padron_id
                                    ), FALSE);
                            }


                            // subir comprobante de los inmuebles
                            $uploads = array();
                            if (!empty($_FILES)) {
                                foreach ($_FILES as $id => $file) {
                                    $file_name = explode('_', $id, 3);
                                    // PASAN SOLO LOS CAMPOS DEL Comprobante (ID_comprobante_REPETICION)
                                    if ($file_name[1] === 'comprobante' && is_numeric($file_name[0])) {

                                        if (!empty($file['name'])) {
                                            if (!$this->upload->do_upload($id)) {
                                                $error_msg_file = $this->upload->display_errors();
                                                $trans_ok &= FALSE;
                                            } else {
                                                $uploads[$id] = $this->upload->data();
                                            }
                                        }
                                    }
                                }

                                if ($trans_ok) {
                                    if (!empty($uploads)) {
                                        foreach ($uploads as $key => $Upload) {
                                            $campo = explode('_', $key, 3);
                                            $trans_ok &= $this->Adjuntos_model->create(
                                                array(
                                                    'tipo_id' => 1, // 1 = Adjunto generico (HC)
                                                    'nombre' => $Upload['file_name'],
                                                    'ruta' => $config_adjuntos['upload_path'],
                                                    'tamanio' => round($Upload['file_size'], 2),
                                                    'hash' => md5_file($config_adjuntos['upload_path'] . $Upload['file_name']),
                                                    'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                                    'usuario_subida' => $this->session->userdata('user_id')
                                                ), FALSE);


                                            // Eliminar archivo viejo y registro de la db
                                            $adjunto_viejo = $this->db
                                                ->select(['adjunto_id', 'nombre', 'ruta'])
                                                ->join('to2_adjuntos', "to2_adjuntos.id = to2_tramites_padrones.adjunto_id", 'LEFT')
                                                ->get_where('to2_tramites_padrones', array(
                                                        'pase_id' => $pase_id,
                                                        'padron_id', $campo[0],
                                                        'repeticion' => $campo[2])
                                                )->row();


                                            $adjunto_id = $this->Adjuntos_model->get_row_id();
                                            // inserta el nuevo registro
                                            $this->db
                                                ->set('adjunto_id', $adjunto_id)
                                                ->where('pase_id', $pase->id)
                                                ->where('padron_id', $campo[0])
                                                ->where('repeticion', $campo[2]);
                                            $this->db->update('to2_tramites_padrones');

                                            if ($adjunto_viejo) {
                                                $this->Adjuntos_model->delete(array('id' => $adjunto_viejo->adjunto_id), FALSE);

                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }


                foreach ($_POST as $id => $valor) {
                    // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                    $post_name = explode('_', $id, 3);
                    if ($post_name[0] === 'campo' && is_numeric($post_name[1])) {

                        $this->db
                            ->set('valor', $this->input->post($id))
                            ->where('pase_id', $pase_id)
                            ->where('campo_id', $post_name[1])
                            ->where('repeticion', $post_name[2]);
                        $this->db->update('to2_datos');
                    }
                }

                $uploads = array();
                if (!empty($_FILES)) {

                    foreach ($_FILES as $id => $file) {
                        $file_name = explode('_', $id, 3);
                        // PASAN SOLO LOS CAMPOS DEL FORMULARIO (campo_ID_REPETICION)
                        if ($file_name[0] === 'campo' && is_numeric($file_name[1])) {

                            if (!empty($file['name'])) {
                                if (!$this->upload->do_upload($id)) {
                                    $error_msg_file = $this->upload->display_errors();
                                    $trans_ok = FALSE;
                                } else {
                                    $uploads[$id] = $this->upload->data();
                                }
                            }
                        }
                    }

                    if ($trans_ok) {
                        if (!empty($uploads)) {
                            foreach ($uploads as $key => $Upload) {
                                $campo = explode('_', $key, 3);
                                $trans_ok &= $this->Adjuntos_model->create(
                                    array(
                                        'tipo_id' => 1, // 1 = Adjunto generico (HC)
                                        'nombre' => $Upload['file_name'],
                                        'ruta' => $config_adjuntos['upload_path'],
                                        'tamanio' => round($Upload['file_size'], 2),
                                        'hash' => md5_file($config_adjuntos['upload_path'] . $Upload['file_name']),
                                        'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
                                        'usuario_subida' => $this->session->userdata('user_id')
                                    ), FALSE);

                                $adjunto_id = $this->Adjuntos_model->get_row_id();

                                // Eliminar archivo viejo y registro de la db
                                $adjunto_viejo = $this->db
                                    ->select(['adjunto_id', 'nombre', 'ruta'])
                                    ->join('to2_adjuntos', "to2_adjuntos.id = to2_datos.adjunto_id", 'LEFT')
                                    ->get_where('to2_datos', array(
                                            'pase_id' => $pase_id,
                                            'campo_id' => $campo[1],
                                            'repeticion' => $campo[2])
                                    )->row();

                                // inserta el nuevo registro
                                $this->db
                                    ->set('adjunto_id', $adjunto_id)
                                    ->where('pase_id', $pase_id)
                                    ->where('campo_id', $campo[1])
                                    ->where('repeticion', $campo[2]);
                                $this->db->update('to2_datos');


                                if (isset($adjunto_viejo) && $adjunto_viejo) {
                                    $this->Adjuntos_model->delete(array('id' => $adjunto_viejo->adjunto_id), FALSE);
                                }
                            }
                        }
                    }
                }

                if ($this->db->trans_status() && $trans_ok) {
                    $this->db->trans_commit();

                    //borrar archivo viejo
                    if (isset($adjunto_viejo) && $adjunto_viejo) {
                        unlink($config_adjuntos['upload_path'] . $adjunto_viejo->nombre);
                    }
                    $this->session->set_flashdata('message', $this->Pases_model->get_msg());
                    redirect('tramites_online/tramites/ver/' . $pase->tramite_id . '/bandeja_entrada', 'refresh');

                } else {
                    $this->db->trans_rollback();
                    // BORRA ARCHIVOS SI FALLO ALGO
                    if (!empty($uploads)) {
                        foreach ($uploads as $Upload) {
                            unlink($config_adjuntos['upload_path'] . $Upload['file_name']);
                        }
                    }
                    $error_msg = '<br />Se ha producido un error con la base de datos.';
                    if ($this->Pases_model->get_error()) {
                        $error_msg .= $this->Pases_model->get_error();
                    }
                    if ($this->Adjuntos_model->get_error()) {
                        $error_msg .= $this->Adjuntos_model->get_error();
                    }
                    if (isset($lleva_padron)) {
                        if ($this->Padrones_model->get_error()) {
                            $error_msg .= $this->Padrones_model->get_error();
                        }

                        if ($this->Tramites_padrones_model->get_error()) {
                            $error_msg .= $this->Tramites_padrones_model->get_error();
                        }
                    }
                    if ($this->Datos_model->get_error()) {
                        $error_msg .= $this->Datos_model->get_error();
                    }
                }
            }
        }


        if (!empty($error_msg_file)) {
            $error_msg .= $error_msg_file;
        }
        $data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

        $data['txt_btn'] = "Editar";
        $data['title_view'] = 'Editar Pase';
        $data['title'] = TITLE . ' - Editar Pase';
        $data['js'][] = 'js/tramites_online/base.js';

        $this->load->view('tramites_online/pases/pases_modal_abm', $data);
    }

    private function dump($var, bool $show_query = TRUE)
    {
        echo "<pre>";
        if ($show_query) {
            print_r($this->db->last_query());
            echo "<br>";
        }
        print_r(html_escape($var));
        echo "</pre>";
        exit;
    }

}
