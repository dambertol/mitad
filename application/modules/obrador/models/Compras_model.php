<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compras_model extends MY_Model
{

	/**
	 * Modelo de Compras
	 * Autor: Leandro
	 * Creado: 21/10/2019
	 * Modificado: 28/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'ob_compras';
		$this->full_log = TRUE;
		$this->msg_name = 'Compra';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha_recepcion', 'proveedor_id', 'lugar_fisico', 'nro_orden', 'recepcionista', 'expediente', 'destino', 'estado', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'proveedor' => array('label' => 'Proveedor', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_recepcion' => array('label' => 'Fecha Recepción', 'type' => 'datetime', 'required' => TRUE),
				'recepcionista' => array('label' => 'Recepcionista', 'maxlength' => '50', 'required' => TRUE),
				'lugar_fisico' => array('label' => 'Lugar Físico', 'maxlength' => '50'),
				'nro_orden' => array('label' => 'Nro Orden', 'maxlength' => '50'),
				'expediente' => array('label' => 'Expediente', 'maxlength' => '50'),
				'destino' => array('label' => 'Destino', 'maxlength' => '50', 'required' => TRUE)
		);
		$this->requeridos = array('fecha_recepcion', 'proveedor_id', 'recepcionista', 'destino', 'estado');
		//$this->unicos = array();
		$this->default_join = array(
				array('ob_proveedores', 'ob_proveedores.id = ob_compras.proveedor_id', 'LEFT', array('ob_proveedores.razon_social as proveedor'))
		);
		// Inicializaciones necesarias colocar acá.
	}

		public function update_stock($id, $cantidad)
	{
		if (is_numeric($cantidad))
		{
			$this->db->set('cant_real', 'cant_real - ' . $cantidad, false);
			$this->db->where($this->id_name, $id);
			$this->db->update($this->table_name);
			$rows = $this->db->affected_rows();
			if ($rows > -1)
			{
				$this->_create_log('update', $this->ion_auth->user()->row()->id, date_format(new DateTime(), 'Y/m/d H:i:s'), $this->table_name, $id, $this->input->ip_address());
				$this->_set_msg('Registro de ' . $this->msg_name . ' modificado');
				return TRUE;
			}
			else
			{
				$this->_set_error('No se ha podido modificar el registro de ' . $this->msg_name);
				return FALSE;
			}
		}
		else
		{
			$this->_set_error('Cantidad debe ser un valor numérico');
			return FALSE;
		}
	}
	
	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('compra_id', $delete_id)->count_all_results('ob_detalle_compras') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Detalle.');
			return FALSE;
		}
		return TRUE;
	}
}