<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expedientes_model extends MY_Model
{

	/**
	 * Modelo de Expedientes
	 * Autor: Leandro
	 * Creado: 12/09/2019
	 * Modificado: 13/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_expedientes';
		$this->full_log = TRUE;
		$this->msg_name = 'Expediente';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nro_expediente', 'fecha_desde_exp', 'fecha_hasta_exp', 'fecha_movimiento', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nro_expediente' => array('label' => 'N° Expediente', 'maxlength' => '45', 'required' => TRUE),
				'fecha_desde_exp' => array('label' => 'Fecha Desde', 'type' => 'date', 'required' => TRUE),
				'fecha_hasta_exp' => array('label' => 'Fecha Hasta', 'type' => 'date')
		);
		$this->requeridos = array('nro_expediente', 'fecha_desde_exp', 'fecha_movimiento');
		$this->unicos = array('nro_expediente');
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
		if ($this->db->where('expediente_id', $delete_id)->count_all_results('na_adjuntos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Adjunto.');
			return FALSE;
		}
		if ($this->db->where('expediente_id', $delete_id)->count_all_results('na_adultos_responsables') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Adulto Responsable.');
			return FALSE;
		}
		if ($this->db->where('adulto_responsable_id', $delete_id)->count_all_results('na_intervenciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Intervención.');
			return FALSE;
		}
		if ($this->db->where('expediente_id', $delete_id)->count_all_results('na_menores') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Menor.');
			return FALSE;
		}
		return TRUE;
	}
}