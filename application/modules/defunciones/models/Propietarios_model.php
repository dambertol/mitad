<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Propietarios_model extends MY_Model
{

	/**
	 * Modelo de Propietarios
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_propietarios';
		$this->full_log = TRUE;
		$this->msg_name = 'Propietario';
		$this->id_name = 'id';
		$this->columnas = array('id', 'expediente_compra_id', 'expediente_construccion_id', 'solicitante_id', 'ubicacion_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'solicitante' => array('label' => 'Propietario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'ubicacion' => array('label' => 'Ubicación', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'expediente_compra' => array('label' => 'Expediente Compra', 'input_type' => 'combo', 'type' => 'bselect'),
				'expediente_construccion' => array('label' => 'Expediente Construcción', 'input_type' => 'combo', 'type' => 'bselect')
		);
		$this->requeridos = array('expediente_compra_id', 'expediente_construccion_id', 'solicitante_id', 'ubicacion_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('df_solicitantes', 'df_solicitantes.id = df_propietarios.solicitante_id', 'LEFT', array("df_solicitantes.nombre as solicitante")),
				array('df_ubicaciones', 'df_ubicaciones.id = df_propietarios.ubicacion_id', 'LEFT'),
				array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT', array("CONCAT(df_cementerios.nombre, ': ', df_ubicaciones.tipo, (CASE WHEN tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - C: ', COALESCE(df_ubicaciones.cuadro,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - P: ', COALESCE(df_ubicaciones.nicho,'')) WHEN tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(df_ubicaciones.cuadro,''), ' - D: ', COALESCE(df_ubicaciones.denominacion,'')) WHEN tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(df_ubicaciones.sector,''), ' - F: ', COALESCE(df_ubicaciones.fila,''), ' - N: ', COALESCE(df_ubicaciones.nicho,'')) ELSE '' END)) as ubicacion")),
				array('df_expedientes ecomp', 'ecomp.id = df_propietarios.expediente_compra_id', 'LEFT', array("CONCAT(ecomp.numero, '/', ecomp.ejercicio, ' ', COALESCE(ecomp.letra, '')) as expediente_compra")),
				array('df_expedientes econs', 'econs.id = df_propietarios.expediente_construccion_id', 'LEFT', array("CONCAT(econs.numero, '/', econs.ejercicio, ' ', COALESCE(econs.letra, '')) as expediente_construccion"))
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