<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_legajos_model extends MY_Model
{

	/**
	 * Modelo de Usuario Legajos
	 * Autor: Leandro
	 * Creado: 10/12/2019
	 * Modificado: 10/12/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rh_usuarios_legajos';
		$this->full_log = TRUE;
		$this->msg_name = 'Asignación de legajo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'user_id', 'legajo_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'user' => array('label' => 'Usuario', 'input_type' => 'combo', 'type' => 'bselect', 'disabled' => TRUE),
				'legajos' => array('label' => 'Legajos Asignados', 'input_type' => 'combo', 'type' => 'list', 'id_name' => 'legajos')
		);
		$this->requeridos = array('user_id', 'legajo_id');
		$this->unicos = array(array('user_id', 'legajo_id'));
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
	 * intersect_asignaciones: Actualiza asignaciones de usuario a legajos.
	 *
	 * @param int $user_id
	 * @param array $new_asignaciones
	 * @param bool $trans_enabled
	 * @return bool
	 */
	public function intersect_asignaciones($user_id, $new_asignaciones, $trans_enabled = FALSE)
	{
		if ($trans_enabled)
		{
			$this->db->trans_begin();
		}
		$trans_ok = TRUE;
		$old_asignaciones = $this->get(array('user_id' => $user_id));
		if (!empty($old_asignaciones))
		{
			foreach ($old_asignaciones as $Old)
			{
				$old_asignaciones_array[$Old->id] = $Old->legajo_id;
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
						'user_id' => $user_id,
						'legajo_id' => $To_add
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