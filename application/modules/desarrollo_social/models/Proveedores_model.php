<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedores_model extends MY_Model
{

	/**
	 * Modelo de Proveedores
	 * Autor: Leandro
	 * Creado: 01/10/2019
	 * Modificado: 08/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ds_proveedores';
		$this->full_log = TRUE;
		$this->msg_name = 'Proveedor';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_proveedor_id', 'razon_social', 'domicilio', 'localidad', 'fecha_inscripcion', 'cuit', 'telefono', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'razon_social' => array('label' => 'Razon Social', 'maxlength' => '50', 'required' => TRUE),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '50'),
				'localidad' => array('label' => 'Localidad', 'maxlength' => '50'),
				'tipo_proveedor' => array('label' => 'Tipo de Proveedor', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_inscripcion' => array('label' => 'Fecha Inscripción', 'type' => 'date'),
				'cuit' => array('label' => 'CUIT', 'maxlength' => '13', 'type' => 'cuil', 'minlength' => '11', 'maxlength' => '13'),
				'telefono' => array('label' => 'Teléfono', 'maxlength' => '20'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('tipo_proveedor_id', 'razon_social');
		//$this->unicos = array();
		$this->default_join = array(
				array('ds_tipos_proveedores', 'ds_tipos_proveedores.id = ds_proveedores.tipo_proveedor_id', 'LEFT', array('ds_tipos_proveedores.descripcion as tipo_proveedor'))
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
		if ($this->db->where('proveedor_id', $delete_id)->count_all_results('ds_compras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Compra.');
			return FALSE;
		}
		return TRUE;
	}
}