<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Traslados_model extends MY_Model
{

	/**
	 * Modelo de Traslados
	 * Autor: Leandro
	 * Creado: 05/12/2019
	 * Modificado: 05/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_traslados';
		$this->full_log = TRUE;
		$this->msg_name = 'Traslado';
		$this->id_name = 'id';
		$this->columnas = array('id', 'operacion_id', 'tipo_traslado', 'ubicacion_origen_id', 'ubicacion_destino_id', 'fecha_realizacion', 'cocheria_traslado_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_traslado' => array('label' => 'Tipo de Traslado', 'maxlength' => '50', 'required' => TRUE),
				'fecha_tramite' => array('label' => 'Fecha Trámite', 'type' => 'date', 'required' => TRUE),
				'fecha_realizacion' => array('label' => 'Fecha Realización', 'type' => 'date'),
				'cocheria_traslado' => array('label' => 'Cochería Traslado', 'input_type' => 'combo', 'type' => 'bselect'),
				'fecha_pago' => array('label' => 'Fecha Pago', 'type' => 'date'),
				'boleta_pago' => array('label' => 'Boleta Pago', 'maxlength' => '50'),
				'expediente' => array('label' => 'Expediente', 'input_type' => 'combo', 'type' => 'bselect'),
				'ubicacion_origen' => array('label' => 'Ubicación Origen', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => TRUE),
				'ubicacion_destino' => array('label' => 'Ubicación Destino', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
				'imprimir' => array('label' => 'Imprimir', 'input_type' => 'combo', 'id_name' => 'imprimir', 'type' => 'bselect', 'array' => array(NULL => 'NO', 'SI' => 'SI'))
		);
		$this->requeridos = array('operacion_id', 'tipo_traslado', 'ubicacion_origen_id', 'ubicacion_destino_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('df_operaciones', 'df_operaciones.id = df_traslados.operacion_id', 'LEFT', array("df_operaciones.fecha_tramite as fecha_tramite", "df_operaciones.fecha_pago as fecha_pago", "df_operaciones.boleta_pago as boleta_pago", "df_operaciones.observaciones as observaciones", "df_operaciones.fecha as fecha_carga", "df_operaciones.solicitante_id as solicitante_id", "df_operaciones.difunto_id as difunto_id", "df_operaciones.expediente_id as expediente_id")),
				array('df_expedientes', 'df_expedientes.id = df_operaciones.expediente_id', 'LEFT', array("CONCAT(numero, '/', ejercicio, ' ', COALESCE(letra, '')) as expediente")),
				array('df_solicitantes', 'df_solicitantes.id = df_operaciones.solicitante_id', 'LEFT', array("df_solicitantes.nombre as solicitante")),
				array('df_difuntos', 'df_difuntos.id = df_operaciones.difunto_id', 'LEFT', array("df_difuntos.ficha as ficha", "CONCAT(df_difuntos.apellido, ', ', df_difuntos.nombre) as difunto")),
				array('df_cocherias', 'df_cocherias.id = df_traslados.cocheria_traslado_id', 'LEFT', array("df_cocherias.nombre as cocheria_traslado")),
				array('df_ubicaciones UO', 'UO.id = df_traslados.ubicacion_origen_id', 'LEFT'),
				array('df_cementerios CO', 'CO.id = UO.cementerio_id', 'LEFT', array("CONCAT(CO.nombre, ': ', UO.tipo, (CASE WHEN UO.tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(UO.sector,''), ' - F: ', COALESCE(UO.fila,''), ' - N: ', COALESCE(UO.nicho,'')) WHEN UO.tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(UO.sector,''), ' - C: ', COALESCE(UO.cuadro,''), ' - F: ', COALESCE(UO.fila,''), ' - P: ', COALESCE(UO.nicho,'')) WHEN UO.tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(UO.cuadro,''), ' - D: ', COALESCE(UO.denominacion,'')) WHEN UO.tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(UO.cuadro,''), ' - D: ', COALESCE(UO.denominacion,'')) WHEN UO.tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(UO.sector,''), ' - F: ', COALESCE(UO.fila,''), ' - N: ', COALESCE(UO.nicho,'')) ELSE '' END)) as ubicacion_origen")),
				array('df_ubicaciones UD', 'UD.id = df_traslados.ubicacion_destino_id', 'LEFT'),
				array('df_cementerios CD', 'CD.id = UD.cementerio_id', 'LEFT', array("CONCAT(CD.nombre, ': ', UD.tipo, (CASE WHEN UD.tipo ='Nicho' THEN CONCAT(' S: ', COALESCE(UD.sector,''), ' - F: ', COALESCE(UD.fila,''), ' - N: ', COALESCE(UD.nicho,'')) WHEN UD.tipo ='Tierra' THEN CONCAT(' S: ', COALESCE(UD.sector,''), ' - C: ', COALESCE(UD.cuadro,''), ' - F: ', COALESCE(UD.fila,''), ' - P: ', COALESCE(UD.nicho,'')) WHEN UD.tipo ='Mausoleo' THEN CONCAT(' C: ', COALESCE(UD.cuadro,''), ' - D: ', COALESCE(UD.denominacion,'')) WHEN UD.tipo ='Pileta' THEN CONCAT(' C: ', COALESCE(UD.cuadro,''), ' - D: ', COALESCE(UD.denominacion,'')) WHEN UD.tipo ='Nicho Urna' THEN CONCAT(' S: ', COALESCE(UD.sector,''), ' - F: ', COALESCE(UD.fila,''), ' - N: ', COALESCE(UD.nicho,'')) ELSE '' END)) as ubicacion_destino"))
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