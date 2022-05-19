<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tipos_combustible_model extends MY_Model
{

	/**
	 * Modelo de Tipos Combustible
	 * Autor: Leandro
	 * Creado: 08/11/2017
	 * Modificado: 27/09/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_tipos_combustible';
		$this->full_log = TRUE;
		$this->msg_name = 'Tipo de Combustible';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'estacion_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'required' => TRUE),
				'estacion' => array('label' => 'Estación predeterm.', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('nombre', 'estacion_id');
		$this->unicos = array('nombre');
		$this->default_join = array(
				array('vc_estaciones', 'vc_estaciones.id = vc_tipos_combustible.estacion_id', 'LEFT', array("vc_estaciones.nombre as estacion"))
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
		if ($this->db->where('tipo_combustible_id', $delete_id)->count_all_results('vc_vales') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vale.');
			return FALSE;
		}
		if ($this->db->where('tipo_combustible_id', $delete_id)->count_all_results('vc_vales_semanales') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vale Semanal.');
			return FALSE;
		}
		if ($this->db->where('tipo_combustible_id', $delete_id)->count_all_results('vc_remitos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Remito.');
			return FALSE;
		}
		if ($this->db->where('tipo_combustible_id', $delete_id)->count_all_results('vc_valores_combustible') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Valor de Combustible.');
			return FALSE;
		}
		if ($this->db->where('tipo_combustible_id', $delete_id)->count_all_results('vc_ordenes_compra_detalles') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Órden de Compra.');
			return FALSE;
		}
		if ($this->db->where('tipo_combustible_id', $delete_id)->count_all_results('vc_vehiculos_combustible') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Vehículo.');
			return FALSE;
		}
		return TRUE;
	}
}