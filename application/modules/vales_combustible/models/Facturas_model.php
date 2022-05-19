<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Facturas_model extends MY_Model
{

	/**
	 * Modelo de Facturas
	 * Autor: Leandro
	 * Creado: 08/11/2017
	 * Modificado: 17/07/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_facturas';
		$this->full_log = TRUE;
		$this->msg_name = 'Factura';
		$this->id_name = 'id';
		$this->columnas = array('id', 'factura', 'fecha', 'tipo_combustible_id', 'total_litros', 'total_costo', 'user_id', 'orden_compra_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'factura' => array('label' => 'Número', 'maxlength' => '50', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'total_litros' => array('label' => 'Total M³/Litros', 'type' => 'numeric', 'required' => TRUE),
				'total_costo' => array('label' => 'Total Costo', 'type' => 'money', 'required' => TRUE),
				'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null')
		);
		$this->requeridos = array('factura', 'fecha', 'tipo_combustible_id', 'total_litros', 'total_costo', 'user_id');
		$this->unicos = array('factura');
		$this->default_join = array(
				array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_facturas.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible")),
				array('vc_ordenes_compra', 'vc_ordenes_compra.id = vc_facturas.orden_compra_id', 'LEFT', array("CONCAT(numero, '/', ejercicio) as orden_compra")),
				array('users U', 'U.id = vc_facturas.user_id', 'LEFT'),
				array('personas P', 'P.id = U.persona_id', 'LEFT', array("CONCAT(P.apellido, ', ', P.nombre) as usuario"))
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
		if ($this->db->where('factura_id', $delete_id)->count_all_results('vc_remitos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Remito.');
			return FALSE;
		}
		return TRUE;
	}
}