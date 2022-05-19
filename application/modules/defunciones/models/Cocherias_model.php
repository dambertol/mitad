<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cocherias_model extends MY_Model
{

	/**
	 * Modelo de Cocherías
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 29/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_cocherias';
		$this->full_log = TRUE;
		$this->msg_name = 'Cochería';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'domicilio', 'telefono', 'email', 'responsable', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '100'),
				'telefono' => array('label' => 'Teléfono', 'maxlength' => '50'),
				'email' => array('label' => 'Email', 'maxlength' => '50', 'type' => 'email'),
				'responsable' => array('label' => 'Responsable', 'maxlength' => '100'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('nombre');
		$this->unicos = array('nombre');
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
		if ($this->db->where('cocheria_id', $delete_id)->count_all_results('df_difuntos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Difunto.');
			return FALSE;
		}
		if ($this->db->where('cocheria_traslado_id', $delete_id)->count_all_results('df_traslados') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Traslado.');
			return FALSE;
		}
		return TRUE;
	}
}