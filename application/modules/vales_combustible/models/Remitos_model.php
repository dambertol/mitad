<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Remitos_model extends MY_Model
{

	/**
	 * Modelo de Remitos
	 * Autor: Leandro
	 * Creado: 10/11/2017
	 * Modificado: 17/07/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_remitos';
		$this->full_log = TRUE;
		$this->msg_name = 'Remito';
		$this->id_name = 'id';
		$this->columnas = array('id', 'factura_id', 'tipo_combustible_id', 'litros', 'costo', 'remito', 'patente_maquinaria', 'persona_id', 'persona_nombre', 'observaciones', 'user_id', 'fecha', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'remito' => array('label' => 'Número', 'maxlength' => '50', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'litros' => array('label' => 'M³/Litros', 'type' => 'numeric', 'required' => TRUE),
				'costo' => array('label' => 'Costo Total', 'type' => 'money', 'required' => TRUE),
				'patente_maquinaria' => array('label' => 'Patente', 'maxlength' => '50'),
				'persona' => array('label' => 'Legajo', 'type' => 'integer', 'maxlength' => '8'),
				'persona_major' => array('label' => 'Persona', 'disabled' => TRUE),
				'persona_nombre' => array('label' => 'Persona Externa', 'maxlength' => '50'),
				'factura' => array('label' => 'Factura', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'observaciones' => array('label' => 'Observaciones', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5)
		);
		$this->requeridos = array('tipo_combustible_id', 'litros', 'costo', 'remito', 'user_id', 'fecha');
		$this->unicos = array('remito');
		$this->default_join = array(
				array('vc_facturas', 'vc_facturas.id = vc_remitos.factura_id', 'LEFT', array("factura as factura")),
				array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_remitos.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible")),
				array('users U', 'U.id = vc_remitos.user_id', 'LEFT'),
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
		if ($this->db->where('remito_id', $delete_id)->count_all_results('vc_vales') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vale.');
			return FALSE;
		}
		return TRUE;
	}
}