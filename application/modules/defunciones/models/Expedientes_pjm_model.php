<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expedientes_pjm_model extends MY_Model
{

	/**
	 * Modelo de Expedientes PJM
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 06/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_expedientes_pjm';
		$this->full_log = TRUE;
		$this->msg_name = 'Expediente PJM';
		$this->id_name = 'id';
		$this->columnas = array('id', 'anio', 'mes', 'licencias', 'parcelas', 'monto_ut', 'monto_pesos', 'fecha_pago', 'cheque', 'boleta_pago', 'expediente', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'anio' => array('label' => 'Año', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'mes' => array('label' => 'Mes', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'fecha_pago' => array('label' => 'Fecha de Pago', 'type' => 'datetime'),
				'expediente' => array('label' => 'Expediente', 'maxlength' => '50'),
				'monto_ut' => array('label' => 'Monto UT'),
				'monto_pesos' => array('label' => 'Monto $'),
				'licencias' => array('label' => 'Licencias', 'maxlength' => '50'),
				'parcelas' => array('label' => 'Parcelas', 'maxlength' => '50'),
				'cheque' => array('label' => 'Cheque', 'maxlength' => '50'),
				'boleta_pago' => array('label' => 'Boleta de Pago', 'maxlength' => '50')
		);
		$this->requeridos = array('anio', 'mes');
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
		return TRUE;
	}
}