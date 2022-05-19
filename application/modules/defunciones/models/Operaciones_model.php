<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Operaciones_model extends MY_Model
{

	/**
	 * Modelo de Operaciones
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_operaciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Operación';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'difunto_id', 'solicitante_id', 'tipo_operacion', 'boleta_pago', 'fecha_pago', 'expediente_id', 'user_id', 'fecha_tramite', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'fecha' => array('label' => 'Fecha', 'type' => 'date'),
				'difunto' => array('label' => 'Difunto', 'input_type' => 'combo'),
				'solicitante' => array('label' => 'Solicitante', 'input_type' => 'combo'),
				'tipo_operacion' => array('label' => 'Tipo Operación', 'type' => 'integer', 'maxlength' => '11'),
				'boleta_pago' => array('label' => 'Boleta Pago', 'maxlength' => '50'),
				'fecha_pago' => array('label' => 'Fecha Pago', 'type' => 'date'),
				'expediente' => array('label' => 'Expediente', 'input_type' => 'combo'),
				'user' => array('label' => 'User', 'input_type' => 'combo'),
				'fecha_tramite' => array('label' => 'Fecha Trámite', 'type' => 'date'),
				'observaciones' => array('label' => 'Observaciones')
		);
		$this->requeridos = array('fecha', 'solicitante_id', 'tipo_operacion', 'user_id', 'fecha_tramite');
		//$this->unicos = array();
		$this->default_join = array();
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
		if ($this->db->where('operacion_id', $delete_id)->count_all_results('df_compras_terrenos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Compra de Terreno.');
			return FALSE;
		}
		if ($this->db->where('operacion_id', $delete_id)->count_all_results('df_concesiones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Concesión.');
			return FALSE;
		}
		if ($this->db->where('operacion_id', $delete_id)->count_all_results('df_ornatos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Ornato.');
			return FALSE;
		}
		if ($this->db->where('operacion_id', $delete_id)->count_all_results('df_reducciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Reducción.');
			return FALSE;
		}
		if ($this->db->where('operacion_id', $delete_id)->count_all_results('df_traslados') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Traslado.');
			return FALSE;
		}
		return TRUE;
	}
}