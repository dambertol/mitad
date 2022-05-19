<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vales_model extends MY_Model
{

	/**
	 * Modelo de Vales
	 * Autor: Leandro
	 * Creado: 14/11/2017
	 * Modificado: 07/01/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'vc_vales';
		$this->full_log = TRUE;
		$this->msg_name = 'Vale';
		$this->id_name = 'id';
		$this->columnas = array('fecha', 'vencimiento', 'id', 'area_id', 'persona_id', 'persona_nombre', 'metros_cubicos', 'remito_id', 'vehiculo_id', 'tipo_combustible_id', 'forma_carga', 'periodicidad', 'nota', 'observaciones', 'orden_compra_id', 'estado', 'estacion_id', 'user_id', 'desanula_con', 'desanula_hac', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'numero' => array('label' => 'Número', 'disabled' => 'disabled'),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'vencimiento' => array('label' => 'Vencimiento', 'type' => 'date', 'required' => TRUE),
				'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'vehiculo' => array('label' => 'Vehículo', 'input_type' => 'combo', 'type' => 'bselect'),
				'tipo_combustible' => array('label' => 'Tipo Combustible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'metros_cubicos' => array('label' => 'M³/Litros', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'remito' => array('label' => 'Remito', 'input_type' => 'combo', 'type' => 'bselect'),
				'persona' => array('label' => 'Persona', 'type' => 'integer', 'disabled' => 'disabled'),
				'patente' => array('label' => 'Patente', 'maxlength' => '50', 'disabled' => 'disabled'),
				'periodicidad' => array('label' => 'Periodicidad', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nota' => array('label' => 'Nota', 'maxlength' => '50'),
				'orden_compra' => array('label' => 'Orden de Compra', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'estacion' => array('label' => 'Estación', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Justificación', 'maxlength' => '255', 'form_type' => 'textarea', 'rows' => 5)
		);
		$this->requeridos = array('fecha', 'vencimiento', 'area_id', 'metros_cubicos', 'tipo_combustible_id', 'periodicidad', 'estado', 'estacion_id', 'user_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('areas', 'areas.id = vc_vales.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area")),
				array('vc_vehiculos', 'vc_vehiculos.id = vc_vales.vehiculo_id', 'LEFT', array("CONCAT(vc_vehiculos.nombre, ' - ', COALESCE(vc_vehiculos.dominio, 'SIN DOMINIO/SERIE'), ' - ', vc_vehiculos.propiedad) as vehiculo")),
				array('vc_tipos_combustible', 'vc_tipos_combustible.id = vc_vales.tipo_combustible_id', 'LEFT', array("vc_tipos_combustible.nombre as tipo_combustible")),
				array('vc_ordenes_compra', 'vc_ordenes_compra.id = vc_vales.orden_compra_id', 'LEFT', array("CONCAT(vc_ordenes_compra.numero, '/', vc_ordenes_compra.ejercicio) as orden_compra")),
				array('vc_estaciones', 'vc_estaciones.id = vc_vales.estacion_id', 'LEFT', array("vc_estaciones.nombre as estacion")),
				array('vc_remitos', 'vc_remitos.id = vc_vales.remito_id', 'LEFT', array("vc_remitos.remito as remito")),
				array('users U', 'U.id = vc_vales.user_id', 'LEFT'),
				array('personas P', 'P.id = U.persona_id', 'LEFT', array("CONCAT(P.apellido, ', ', P.nombre) as usuario"))
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
		return FALSE;
	}

	/**
	 * marcar_impreso: Marca como impresos todos los vales del rango (que no esten anulados).
	 *
	 * @param int $desde
	 * @param int $hasta
	 */
	public function marcar_impreso($desde, $hasta, $area)
	{
		$this->db->where('id >=', $desde);
		$this->db->where('id <=', $hasta);
		$this->db->where('estado !=', 'Anulado');
		$this->db->where('estado !=', 'Asignado');
		$this->db->where('estado !=', 'Pendiente');
		if (!empty($area))
		{
			$this->db->where('area_id', $area);
		}

		if ($this->db->update($this->table_name, array('estado' => 'Impreso')))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * intersect_vales: Actualiza vales asignados a un remito.
	 *
	 * @param int $remito_id
	 * @param array $new_asignaciones
	 * @param bool $trans_enabled
	 * @return bool
	 */
	public function intersect_vales($remito_id, $new_asignaciones, $trans_enabled = FALSE)
	{
		if ($trans_enabled)
		{
			$this->db->trans_begin();
		}
		$trans_ok = TRUE;
		$old_asignaciones = $this->get(array('remito_id' => $remito_id));
		if (!empty($old_asignaciones))
		{
			foreach ($old_asignaciones as $Old)
			{
				$old_asignaciones_array[$Old->id] = $Old->id;
			}

			//Delete asignaciones
			$asignaciones_to_delete = $this->array_diff_no_cast($old_asignaciones_array, $new_asignaciones);
			foreach ($asignaciones_to_delete as $To_delete_key => $To_delete_value)
			{
				$trans_ok &= $this->update(array(
						'id' => $To_delete_key,
						'remito_id' => 'NULL',
						'estado' => 'Impreso'
						), FALSE);
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
				$trans_ok &= $this->update(array(
						'id' => $To_add,
						'remito_id' => $remito_id,
						'estado' => 'Asignado'
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