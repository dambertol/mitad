<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios_grupos_model extends MY_Model
{

	/**
	 * Modelo de Usuarios grupos
	 * Autor: Leandro
	 * Creado: 16/03/2017
	 * Modificado: 16/03/2017 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'users_groups';
		$this->full_log = TRUE;
		$this->msg_name = 'Asignación de grupo';
		$this->id_name = 'id';
		$this->columnas = array('id', 'user_id', 'group_id');
		$this->fields = array(
			'grupos' => array('label' => 'Grupos', 'input_type' => 'combo', 'type' => 'list', 'id_name' => 'grupos')
		);
		$this->requeridos = array('user_id', 'group_id');
		$this->unicos = array(array('user_id', 'group_id'));
	}

	/**
	 * _can_delete: Devuelve true si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		return TRUE;
	}

	/**
	 * intersect_asignaciones: Actualiza asignaciones de usuario a grupos.
	 *
	 * @param int $user_id
	 * @param array $new_asignaciones
	 * @param bool $trans_enabled
	 * @return bool
	 */
	public function intersect_asignaciones($user_id, $new_asignaciones, $trans_enabled = false)
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
				$old_asignaciones_array[$Old->id] = $Old->group_id;
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
					'group_id' => $To_add
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