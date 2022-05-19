<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Difuntos_model extends MY_Model
{

	/**
	 * Modelo de Difuntos
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 29/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_difuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Difunto';
		$this->id_name = 'id';
		$this->columnas = array('id', 'ficha', 'dni', 'apellido', 'nombre', 'defuncion', 'edad', 'causa_muerte', 'licencia_inhumacion', 'registro_civil', 'cocheria_id', 'ubicacion_id', 'ultima_concesion_id', 'nacimiento', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'ficha' => array('label' => 'Ficha', 'type' => 'integer', 'maxlength' => '11'),
				'dni' => array('label' => 'DNI', 'type' => 'natural', 'maxlength' => '11'),
				'apellido' => array('label' => 'Apellido', 'maxlength' => '50', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'defuncion' => array('label' => 'Fecha Defunción', 'type' => 'date', 'required' => TRUE),
				'edad' => array('label' => 'Edad', 'maxlength' => '50', 'required' => TRUE),
				'causa_muerte' => array('label' => 'Causa de Muerte', 'maxlength' => '100'),
				'nacimiento' => array('label' => 'Fecha de Nacimiento', 'type' => 'date'),
				'licencia_inhumacion' => array('label' => 'Licencia de Inhumación', 'maxlength' => '50'),
				'registro_civil' => array('label' => 'Registro Civil', 'maxlength' => '50'),
				'cocheria' => array('label' => 'Cochería', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('apellido', 'nombre', 'defuncion', 'edad', 'cocheria_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('df_cocherias', 'df_cocherias.id = df_difuntos.cocheria_id', 'LEFT', array("df_cocherias.nombre as cocheria")),
				array('df_concesiones', 'df_concesiones.id = df_difuntos.ultima_concesion_id', 'LEFT', array("(CASE WHEN df_concesiones.tipo_concesion ='Alquiler' THEN CONCAT(df_concesiones.tipo_concesion, ' desde ', DATE_FORMAT(df_concesiones.inicio, '%d/%m/%Y'), ' hasta ', DATE_FORMAT(df_concesiones.fin, '%d/%m/%Y')) ELSE CONCAT(df_concesiones.tipo_concesion, ' desde ', df_concesiones.inicio) END) as ultima_concesion")),
				array('df_ubicaciones', 'df_ubicaciones.id = df_difuntos.ubicacion_id', 'LEFT'),
				array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT', array("CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion"))
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
		if ($this->db->where('difunto_id', $delete_id)->count_all_results('df_operaciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Operación.');
			return FALSE;
		}
		return TRUE;
	}
}