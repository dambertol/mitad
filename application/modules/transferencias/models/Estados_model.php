<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Estados_model extends MY_Model
{

	/**
	 * Modelo de Estados
	 * Autor: Leandro
	 * Creado: 21/06/2018
	 * Modificado: 11/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_estados';
		$this->full_log = TRUE;
		$this->msg_name = 'Estado';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'oficina_id', 'mensaje', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'oficina' => array('label' => 'Oficina', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'mensaje' => array('label' => 'Mensaje', 'maxlength' => '9999')
		);
		$this->requeridos = array('nombre', 'oficina_id');
		$this->unicos = array(array('nombre', 'oficina_id'));
		$this->default_join = array(
				array('tr_oficinas', 'tr_oficinas.id = tr_estados.oficina_id', 'LEFT',
						array(
								'tr_oficinas.nombre as oficina'
						)
		));
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
		if ($this->db->where('estado_origen_id', $delete_id)->count_all_results('tr_pases') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pase.');
			return FALSE;
		}
		if ($this->db->where('estado_destino_id', $delete_id)->count_all_results('tr_pases') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pase.');
			return FALSE;
		}
		if ($this->db->where('estado_id', $delete_id)->count_all_results('tr_estados_secuencias') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Secuencia.');
			return FALSE;
		}
		if ($this->db->where('estado_posterior_id', $delete_id)->count_all_results('tr_estados_secuencias') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Secuencia.');
			return FALSE;
		}
		return TRUE;
	}
}