<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Entregas_model extends MY_Model
{

	/**
	 * Modelo de Entregas
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 08/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_entregas';
		$this->full_log = TRUE;
		$this->msg_name = 'Entrega';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha', 'descripcion', 'destino', 'beneficiario_id', 'responsable', 'trabajadora_social', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'beneficiario' => array('label' => 'Beneficiario', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'responsable' => array('label' => 'Responsable', 'maxlength' => '50', 'required' => TRUE),
				'destino' => array('label' => 'Destino', 'maxlength' => '50'),
				'descripcion' => array('label' => 'Descripción', 'maxlength' => '50'),
				'trabajadora_social' => array('label' => 'Trabajador Social', 'maxlength' => '50')
		);
		$this->requeridos = array('fecha', 'beneficiario_id', 'responsable', 'estado');
		//$this->unicos = array();
		$this->default_join = array(
				array('ds_beneficiarios', 'ds_beneficiarios.id = ds_entregas.beneficiario_id', 'LEFT', array('ds_beneficiarios.nro_apros as nro_apros', 'ds_beneficiarios.domicilio as domicilio', "CONCAT(ds_beneficiarios.apellido, ', ', ds_beneficiarios.nombre,  ' (', ds_beneficiarios.dni, ')') as beneficiario"))
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
		if ($this->db->where('entrega_id', $delete_id)->count_all_results('ds_detalle_entregas') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}