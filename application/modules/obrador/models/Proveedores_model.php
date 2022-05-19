<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores_model extends MY_Model
{

	/**
	 * Modelo de Proveedores
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 21/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ob_proveedores';
		$this->full_log = TRUE;
		$this->msg_name = 'Proveedor';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_proveedor_id', 'razon_social', 'cuit', 'beneficiario', 'domicilio', 'localidad', 'codigo_postal', 'tipo_sociedad', 'iva_id', 'ganancia_id', 'fecha_inscripcion', 'ingresos_brutos', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_proveedor' => array('label' => 'Tipo de Proveedor', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'iva' => array('label' => 'IVA', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'ganancia' => array('label' => 'Ganancia', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'razon_social' => array('label' => 'Razón Social', 'maxlength' => '50', 'required' => TRUE),
				'fecha_inscripcion' => array('label' => 'Fecha Inscripción', 'type' => 'date'),
				'ingresos_brutos' => array('label' => 'Ingresos Brutos'),
				'beneficiario' => array('label' => 'Beneficiario', 'maxlength' => '50'),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '50'),
				'localidad' => array('label' => 'Localidad', 'maxlength' => '50'),
				'codigo_postal' => array('label' => 'Código Postal', 'maxlength' => '10'),
				'tipo_sociedad' => array('label' => 'Tipo de Sociedad', 'maxlength' => '50'),
				'cuit' => array('label' => 'CUIT', 'maxlength' => '13'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('tipo_proveedor_id', 'razon_social', 'iva_id', 'ganancia_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('ob_tipos_proveedores', 'ob_tipos_proveedores.id = ob_proveedores.tipo_proveedor_id', 'LEFT', array('ob_tipos_proveedores.descripcion as tipo_proveedor')),
				array('ob_situaciones_iva', 'ob_situaciones_iva.id = ob_proveedores.iva_id', 'LEFT', array('ob_situaciones_iva.descripcion as iva')),
				array('ob_ganancias', 'ob_ganancias.id = ob_proveedores.ganancia_id', 'LEFT', array('ob_ganancias.descripcion as ganancia'))
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
		if ($this->db->where('proveedor_id', $delete_id)->count_all_results('ob_compras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Compra.');
			return FALSE;
		}
		return TRUE;
	}
}