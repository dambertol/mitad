<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reducciones_model extends MY_Model
{

	/**
	 * Modelo de Reducciones
	 * Autor: Leandro
	 * Creado: 05/12/2019
	 * Modificado: 05/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_reducciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Reducción';
		$this->id_name = 'id';
		$this->columnas = array('id', 'operacion_id', 'ubicacion_id', 'fecha_realizacion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'fecha_tramite' => array('label' => 'Fecha Trámite', 'type' => 'date', 'required' => TRUE),
				'fecha_realizacion' => array('label' => 'Fecha Realización', 'type' => 'date'),
				'fecha_pago' => array('label' => 'Fecha Pago', 'type' => 'date'),
				'boleta_pago' => array('label' => 'Boleta Pago', 'maxlength' => '50'),
				'expediente' => array('label' => 'Expediente', 'input_type' => 'combo', 'type' => 'bselect'),
				'ubicacion' => array('label' => 'Ubicación', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
				'imprimir' => array('label' => 'Imprimir', 'input_type' => 'combo', 'id_name' => 'imprimir', 'type' => 'bselect', 'array' => array(NULL => 'NO', 'SI' => 'SI'))
		);
		$this->requeridos = array('operacion_id', 'ubicacion_id');
		//$this->unicos = array();
		$this->default_join = array(array('df_operaciones', 'df_operaciones.id = df_reducciones.operacion_id', 'LEFT', array("df_operaciones.fecha_tramite as fecha_tramite", "df_operaciones.fecha_pago as fecha_pago", "df_operaciones.boleta_pago as boleta_pago", "df_operaciones.observaciones as observaciones", "df_operaciones.fecha as fecha_carga", "df_operaciones.solicitante_id as solicitante_id", "df_operaciones.difunto_id as difunto_id", "df_operaciones.expediente_id as expediente_id")),
				array('df_expedientes', 'df_expedientes.id = df_operaciones.expediente_id', 'LEFT', array("CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as expediente")),
				array('df_solicitantes', 'df_solicitantes.id = df_operaciones.solicitante_id', 'LEFT', array("df_solicitantes.nombre as solicitante")),
				array('df_difuntos', 'df_difuntos.id = df_operaciones.difunto_id', 'LEFT', array("df_difuntos.ficha as ficha", "CONCAT(df_difuntos.apellido, ', ', df_difuntos.nombre) as difunto")),
				array('df_ubicaciones', 'df_ubicaciones.id = df_reducciones.ubicacion_id', 'LEFT'),
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
		return TRUE;
	}
}