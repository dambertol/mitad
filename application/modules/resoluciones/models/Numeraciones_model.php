<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Numeraciones_model extends MY_Model
{

	/**
	 * Modelo de Numeraciones
	 * Autor: Leandro
	 * Creado: 06/12/2017
	 * Modificado: 21/03/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 're_numeraciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Numeración';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_resolucion_id', 'ejercicio', 'numero_inicial', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_resolucion' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'numero_inicial' => array('label' => 'Número Inicial', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE)
		);
		$this->requeridos = array('tipo_resolucion_id', 'ejercicio', 'numero_inicial');
		$this->unicos = array(array('tipo_resolucion_id', 'ejercicio'));
		$this->default_join = array(
				array('re_tipos_resoluciones', 're_tipos_resoluciones.id = re_numeraciones.tipo_resolucion_id', 'LEFT', array("re_tipos_resoluciones.nombre as tipo_resolucion"))
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