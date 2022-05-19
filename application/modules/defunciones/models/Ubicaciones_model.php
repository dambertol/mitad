<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ubicaciones_model extends MY_Model
{

	/**
	 * Modelo de Ubicaciones
	 * Autor: Leandro
	 * Creado: 22/11/2019
	 * Modificado: 22/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'df_ubicaciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Ubicación';
		$this->id_name = 'id';
		$this->columnas = array('id', 'cementerio_id', 'tipo', 'sector', 'cuadro', 'fila', 'nicho', 'denominacion', 'nomenclatura', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'cementerio' => array('label' => 'Cementerio', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'tipo', 'array' => array('Nicho' => 'Nicho', 'Tierra' => 'Tierra', 'Pileta' => 'Pileta', 'Mausoleo' => 'Mausoleo', 'Nicho Urna' => 'Nicho Urna'), 'required' => TRUE),
				'sector' => array('label' => 'Sector', 'maxlength' => '100'),
				'cuadro' => array('label' => 'Cuadro', 'maxlength' => '100'),
				'fila' => array('label' => 'Fila', 'maxlength' => '10'),
				'nicho' => array('label' => 'Nicho', 'type' => 'integer', 'maxlength' => '11'),
				'denominacion' => array('label' => 'Denominación', 'maxlength' => '100'),
				'nomenclatura' => array('label' => 'Nomenclatura', 'maxlength' => '50'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('cementerio_id', 'tipo');
		//$this->unicos = array();
		$this->default_join = array(
				array('df_cementerios', 'df_cementerios.id = df_ubicaciones.cementerio_id', 'LEFT', array("df_cementerios.nombre as cementerio"))
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
		if ($this->db->where('ubicacion_id', $delete_id)->count_all_results('df_compras_terrenos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Compra de Terreno.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_id', $delete_id)->count_all_results('df_concesiones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Concesión.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_id', $delete_id)->count_all_results('df_construcciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Construcción.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_id', $delete_id)->count_all_results('df_difuntos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Difunto.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_id', $delete_id)->count_all_results('df_ornatos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Ornato.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_id', $delete_id)->count_all_results('df_propietarios') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Propietario.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_id', $delete_id)->count_all_results('df_reducciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Reducción.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_destino_id', $delete_id)->count_all_results('df_traslados') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Traslado.');
			return FALSE;
		}
		if ($this->db->where('ubicacion_origen_id', $delete_id)->count_all_results('df_traslados') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Traslado.');
			return FALSE;
		}
		return TRUE;
	}
}