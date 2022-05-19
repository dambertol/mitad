<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Intervenciones_model extends MY_Model
{

	/**
	 * Modelo de Intervenciones
	 * Autor: Leandro
	 * Creado: 13/09/2019
	 * Modificado: 13/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_intervenciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Intervención';
		$this->id_name = 'id';
		$this->columnas = array('id', 'expediente_id', 'fecha_intervencion', 'tipo_intervencion_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_intervencion' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_intervencion' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE)
		);
		$this->requeridos = array('expediente_id', 'fecha_intervencion', 'tipo_intervencion_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('na_tipos_intervenciones', 'na_tipos_intervenciones.id = na_intervenciones.tipo_intervencion_id', 'LEFT', array('na_tipos_intervenciones.nombre as tipo_intervencion'))
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
		if ($this->db->where('intervencion_id', $delete_id)->count_all_results('na_intervenciones_detalles') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}