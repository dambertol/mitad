<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Datos_extra_hobbies_model extends MY_Model
{

	/**
	 * Modelo de Dato Extra Hobbies
	 * Autor: Leandro
	 * Creado: 12/08/2019
	 * Modificado: 12/08/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_datos_extra_hobbies';
		$this->full_log = TRUE;
		$this->msg_name = 'Dato Extra Hobby';
		$this->id_name = 'id';
		$this->columnas = array('id', 'dato_extra_id', 'hobby_id');
		$this->fields = array();
		$this->requeridos = array('dato_extra_id', 'hobby_id');
		$this->unicos = array(array('dato_extra_id', 'hobby_id'));
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

	/**
	 * intersect_asignaciones: Actualiza asignaciones de hobbies.
	 *
	 * @param int $dato_extra_id
	 * @param array $new_asignaciones
	 * @param bool $trans_enabled
	 * @return bool
	 */
	public function intersect_asignaciones($dato_extra_id, $new_asignaciones, $trans_enabled = false)
	{
		if ($trans_enabled)
		{
			$this->db->trans_begin();
		}
		$trans_ok = TRUE;
		$old_asignaciones = $this->get(array('dato_extra_id' => $dato_extra_id));
		if (!empty($old_asignaciones))
		{
			foreach ($old_asignaciones as $Old)
			{
				$old_asignaciones_array[$Old->id] = $Old->hobby_id;
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
						'dato_extra_id' => $dato_extra_id,
						'hobby_id' => $To_add
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
}