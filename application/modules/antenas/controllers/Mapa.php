<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Mapa extends MY_Controller
{

	/**
	 * Controlador de Mapa
	 * Autor: Leandro
	 * Creado: 21/03/2019
	 * Modificado: 21/03/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('antenas/Proveedores_model');
		$this->load->model('antenas/Torres_model');
		$this->load->model('Localidades_model');
		$this->grupos_permitidos = array('admin', 'antenas_admin', 'antenas_consulta_general');
		$this->grupos_solo_consulta = array('antenas_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function ver()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$array_proveedor = $this->get_array('Proveedores', 'nombre', 'id', array(), array('Todos' => 'Todos'));
		$array_distrito = $this->get_array('Localidades', 'nombre', 'id', array('select' => "localidades.id, localidades.nombre", 'where' => array(array('column' => 'departamento_id', 'value' => 345))), array('Todos' => 'Todos'));

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'proveedor' => array('label' => 'Proveedor', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'distrito' => array('label' => 'Distrito', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);

		$sesion = new stdClass();
		if ($this->session->userdata('an_mapa_proveedor_selected') !== FALSE)
		{
			$sesion->proveedor_id = $this->session->userdata('an_mapa_proveedor_selected');
		}
		else
		{
			$sesion->proveedor_id = 'Todos';
		}

		if ($this->session->userdata('an_mapa_distrito_selected') !== FALSE)
		{
			$sesion->distrito_id = $this->session->userdata('an_mapa_distrito_selected');
		}
		else
		{
			$sesion->distrito_id = 'Todos';
		}

		$data['figuras'] = array(
				'name' => 'figuras',
				'id' => 'figuras',
				'type' => 'hidden',
				'value' => ''
		);
		$tipos_figuras[] = (object) array(
						'id' => -1,
						'nombre' => 'Antena Telefonía',
						'icono' => 'img/antenas/generales/antenas_48.png',
						'tipo' => 'marker',
						'color' => ''
		);
		$data['tipos_figuras'] = $tipos_figuras;

		$fake_model->fields['proveedor']['array'] = $array_proveedor;
		$fake_model->fields['distrito']['array'] = $array_distrito;
		$data['fields'] = $this->build_fields($fake_model->fields, $sesion);
		$data['message'] = $this->session->flashdata('message');
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Mapa de Torres';
		$data['title'] = TITLE . ' - Mapa de Torres';
		$data['js'][] = 'https://maps.google.com/maps/api/js?language=es&amp;key=AIzaSyALTbWXMGGenqreTH6wRAZaDHJj6PkLOqw';
		$data['js'][] = 'js/antenas/map-general.js';
		$data['css'][] = 'css/antenas/antenas-varios.css';
		$this->load_template('antenas/mapa/mapa_content_view', $data);
	}

	public function getData()
	{
		$this->array_proveedor_control = $this->get_array('Proveedores', 'nombre', 'id', array(), array('Todos' => 'Todos'));
		$this->array_distrito_control = $this->get_array('Localidades', 'nombre', 'id', array('select' => "localidades.id, localidades.nombre", 'where' => array(array('column' => 'departamento_id', 'value' => 345))), array('Todos' => 'Todos'));

		$this->form_validation->set_rules('proveedor', 'Proveedor', 'callback_control_combo[proveedor]');
		$this->form_validation->set_rules('distrito', 'Distrito', 'callback_control_combo[distrito]');
		if ($this->form_validation->run() === TRUE)
		{
			$figuras_mapa = array();

			$torres_get_options = array(
					'join' => array(
							array('table' => 'an_proveedores', 'where' => 'an_proveedores.id=an_torres.proveedor_id', 'columnas' => array('an_proveedores.nombre as proveedor')),
							array('table' => 'localidades', 'where' => 'localidades.id=an_torres.distrito_id', 'columnas' => array('localidades.nombre as distrito'))
					)
			);
			if (!empty($_POST['proveedor']) && $this->input->post('proveedor') != 'Todos')
			{
				$torres_get_options['where'][] = array('column' => 'proveedor_id', 'value' => $this->input->post('proveedor'));
				$this->session->set_userdata('an_mapa_proveedor_selected', $this->input->post('proveedor'));
			}
			else if (empty($_POST['proveedor']) && $this->session->userdata('an_mapa_proveedor_selected') !== FALSE)
			{
				$torres_get_options['where'][] = array('column' => 'proveedor_id', 'value' => $this->session->userdata('an_mapa_proveedor_selected'));
			}
			else
			{
				$this->session->unset_userdata('an_mapa_proveedor_selected');
			}

			if (!empty($_POST['distrito']) && $this->input->post('distrito') != 'Todos')
			{
				$torres_get_options['where'][] = array('column' => 'distrito_id', 'value' => $this->input->post('distrito'));
				$this->session->set_userdata('an_mapa_distrito_selected', $this->input->post('distrito'));
			}
			else if (empty($_POST['distrito']) && $this->session->userdata('an_mapa_distrito_selected') !== FALSE)
			{
				$torres_get_options['where'][] = array('column' => 'distrito_id', 'value' => $this->session->userdata('an_mapa_distrito_selected'));
			}
			else
			{
				$this->session->unset_userdata('an_mapa_distrito_selected');
			}

			$torres = $this->Torres_model->get($torres_get_options);
			if (!empty($torres))
			{
				foreach ($torres as $Torre)
				{
					$figuras_mapa[] = array(
							'tipo' => 'marker',
							'puntos' => array((object) array('lat' => $Torre->latitud, 'lng' => $Torre->longitud)),
							'tipo_figura_id' => "Torre",
							'tipo_figura' => "Torre",
							'option' => 'img/antenas/generales/antenas_48.png',
							'tooltip' => '<div id="content" style="width: 400px;">' .
							'<div id="siteNotice"></div>' .
							'<div class="hero-unit-maps" id="firstHeading">' . $Torre->calle . '</div>' .
							'<div id="bodyContent" style="overflow: auto;">' .
							'<div style="overflow:auto; max-height:300px;">' .
							'<p><b>Servicio: </b>' . $Torre->servicio . '</p>' .
							'<p><b>Proveedor: </b>' . $Torre->proveedor . '</p>' .
							'<p><b>Distrito: </b>' . $Torre->distrito . '</p>' .
							'<p><b>Padrón: </b>' . $Torre->padron . '</p>' .
							'<p><b>Latitud: </b>' . $Torre->latitud . '</p>' .
							'<p><b>Longitud: </b>' . $Torre->longitud . '</p>' .
							'<p><b>Zonificación: </b>' . $Torre->zonificacion . '</p>' .
							'<p><b>Entorno: </b>' . $Torre->entorno . '</p>' .
							'<p><b>Características: </b>' . $Torre->caracteristicas . '</p>' .
							'<p><b>Observaciones: </b>' . $Torre->observaciones . '</p>' .
							'</div><hr>' .
							'<div class="text-center">' .
							'<a href="antenas/torres/editar/' . $Torre->id . '" title="Editar Torre" class="btn btn-primary"><i class="fa fa-pencil"></i></a>' .
							'</div>' .
							'</div></div>'
					);
				}
			}

			echo json_encode($figuras_mapa);
		}
		else
		{
			echo validation_errors();
		}
	}
}