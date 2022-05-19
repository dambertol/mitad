<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Documentos_model extends MY_Model
{

	/**
	 * Modelo de Documentos
	 * Autor: Leandro
	 * Creado: 08/01/2020
	 * Modificado: 23/01/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'go_documentos';
		$this->full_log = TRUE;
		$this->msg_name = 'Documento';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'expt_ejercicio', 'expt_numero', 'expt_matricula', 'titulo', 'tipo_documento_id', 'parte_id', 'ejercicio', 'numero', 'texto', 'fecha_carga', 'usuario_carga', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_documento' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'numero' => array('label' => 'Número', 'type' => 'integer', 'maxlength' => '8', 'required' => TRUE),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'titulo' => array('label' => 'Título', 'maxlength' => '255', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'expt_numero' => array('label' => 'Expt Numero', 'type' => 'integer', 'maxlength' => '5'),
				'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4'),
				'expt_matricula' => array('label' => 'Expt Matricula', 'type' => 'integer', 'maxlength' => '1'),
				'texto' => array('label' => 'Texto', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
				'parte' => array('label' => 'Parte', 'input_type' => 'combo', 'type' => 'bselect')
		);
		$this->requeridos = array('fecha', 'titulo', 'tipo_documento_id', 'ejercicio', 'numero', 'fecha_carga', 'usuario_carga', 'estado');
		$this->unicos = array(array('tipo_documento_id', 'numero', 'ejercicio'));
		$this->default_join = array(
				array('go_tipos_documentos', 'go_tipos_documentos.id = go_documentos.tipo_documento_id', 'LEFT', array("go_tipos_documentos.nombre as tipo_documento")),
				array('go_partes', 'go_partes.id = go_documentos.parte_id', 'LEFT', array('go_partes.nombre as nombre_parte', 'go_partes.persona_id')),
				array('personas', 'personas.id = go_partes.persona_id', 'LEFT',
						array(
								'personas.sexo',
								'personas.cuil',
								'personas.dni',
								'personas.nombre',
								'personas.apellido',
								'personas.telefono',
								'personas.celular',
								'personas.email',
								'personas.fecha_nacimiento',
								'personas.nacionalidad_id',
								'personas.domicilio_id'
						)
				),
				array('domicilios', 'domicilios.id = personas.domicilio_id', 'LEFT',
						array(
								'domicilios.calle',
								'domicilios.barrio',
								'domicilios.altura',
								'domicilios.piso',
								'domicilios.dpto',
								'domicilios.manzana',
								'domicilios.casa',
								'domicilios.localidad_id'
						)
				),
				array('nacionalidades', 'nacionalidades.id = personas.nacionalidad_id', 'LEFT', array('nacionalidades.nombre as nacionalidad')),
				array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
				array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
				array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
						array(
								"CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
						)
				)
		);
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * get_ultimo_documento: Devuelve el último número de documento utilizado (según Tipo y Ejercicio).
	 *
	 * @param int $tipo_documento_id
	 * @param int $ejercicio
	 * @return int
	 */
	public function get_ultimo_documento($tipo_documento_id, $ejercicio)
	{
		$query_numero = "SELECT COALESCE(MAX(inicial),1) as inicial FROM
										(SELECT (MAX(numero) + 1) as inicial FROM go_documentos WHERE tipo_documento_id = $tipo_documento_id AND ejercicio = $ejercicio
										UNION
										SELECT numero_inicial as inicial FROM go_numeraciones WHERE tipo_documento_id = $tipo_documento_id AND ejercicio = $ejercicio) a";
		$ultimo_documento = $this->db->query($query_numero)->row();

		return $ultimo_documento->inicial;
	}

	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		return TRUE;
	}
}