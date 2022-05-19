<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Numeraciones_model extends MY_Model
{

	/**
	 * Modelo de Numeraciones
	 * Autor: Leandro
	 * Creado: 08/01/2020
	 * Modificado: 08/01/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'go_numeraciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Numeración';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_documento_id', 'ejercicio', 'numero_inicial', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_documento' => array('label' => 'Tipo de Documento', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'numero_inicial' => array('label' => 'Número Inicial', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE)
		);
		$this->requeridos = array('tipo_documento_id', 'ejercicio', 'numero_inicial');
		$this->unicos = array(array('tipo_documento_id', 'ejercicio'));
		$this->default_join = array(
				array('go_tipos_documentos', 'go_tipos_documentos.id = go_numeraciones.tipo_documento_id', 'LEFT', array("go_tipos_documentos.nombre as tipo_documento"))
		);
		// Inicializaciones necesarias colocar acá.
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