<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vehiculos_combustible_model extends MY_Model
{

	/**
	 * Modelo de Vehículos Combustible
	 * Autor: Leandro
	 * Creado: 25/09/2018
	 * Modificado: 25/09/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_vehiculos_combustible';
		$this->full_log = TRUE;
		$this->msg_name = 'Vehículo Combustible';
		$this->id_name = 'id';
		$this->columnas = array('id', 'vehiculo_id', 'tipo_combustible_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'vehiculo' => array('label' => 'Vehiculo', 'input_type' => 'combo'),
				'tipo_combustible' => array('label' => 'Tipo de Combustible', 'input_type' => 'combo')
		);
		$this->requeridos = array('vehiculo_id', 'tipo_combustible_id');
		$this->unicos = array(array('vehiculo_id', 'tipo_combustible_id'));
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * intersect_asignaciones: Actualiza asignaciones de vehiculo a tipo de combustible.
	 *
	 * @param int $vehiculo_id
	 * @param array $new_asignaciones
	 * @param bool $trans_enabled
	 * @return bool
	 */
	public function intersect_asignaciones($vehiculo_id, $new_asignaciones, $trans_enabled = false)
	{
		if ($trans_enabled)
		{
			$this->db->trans_begin();
		}
		$trans_ok = TRUE;
		$old_asignaciones = $this->get(array('vehiculo_id' => $vehiculo_id));
		if (!empty($old_asignaciones))
		{
			foreach ($old_asignaciones as $Old)
			{
				$old_asignaciones_array[$Old->id] = $Old->tipo_combustible_id;
			}

			//Delete asignaciones
			$asignaciones_to_delete = $this->array_diff_no_cast($old_asignaciones_array, $new_asignaciones);
			foreach ($asignaciones_to_delete as $To_delete_key => $To_delete_value)
			{
				$trans_ok &= $this->delete(array('id' => $To_delete_key), FALSE);
			}

			$asignaciones_to_add = $this->array_diff_no_cast($new_asignaciones, $old_asignaciones_array);
		}
		else
		{
			$asignaciones_to_add = $new_asignaciones;
		}

		if (!empty($asignaciones_to_add))
		{
			//Add asignaciones
			foreach ($asignaciones_to_add as $To_add)
			{
				$trans_ok &= $this->create(array(
						'vehiculo_id' => $vehiculo_id,
						'tipo_combustible_id' => $To_add
						), FALSE);
			}
		}

		if ($trans_enabled)
		{
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				return true;
			}
			else
			{
				$this->db->trans_rollback();
				return false;
			}
		}
		else
		{
			return $trans_ok;
		}
	}

	function array_diff_no_cast(&$ar1, &$ar2)
	{
		$diff = Array();
		foreach ($ar1 as $key => $val1)
		{
			if (array_search($val1, $ar2) === false)
			{
				$diff[$key] = $val1;
			}
		}

		return $diff;
	}

	/**
	 * delete_asignaciones: Elimina asignaciones de vehiculo a tipo de combustible.
	 *
	 * @param int $vehiculo_id
	 * @return bool
	 */
	public function delete_asignaciones($vehiculo_id)
	{
		$this->db->where('vehiculo_id', $vehiculo_id);

		if ($this->db->delete($this->table_name))
		{
			return TRUE;
		}
		else
		{
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
		return TRUE;
	}
}