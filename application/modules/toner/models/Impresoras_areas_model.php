<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Impresoras_areas_model extends MY_Model
{

	/**
	 * Modelo de Impresoras Áreas
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 14/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_impresoras_areas';
		$this->full_log = TRUE;
		$this->msg_name = 'Impresora Área';
		$this->id_name = 'id';
		$this->columnas = array('id', 'impresora_id', 'area_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'impresora' => array('label' => 'Impresora', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('impresora_id', 'area_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('areas', 'areas.id = gt_impresoras_areas.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area")),
				array('gt_impresoras', 'gt_impresoras.id = gt_impresoras_areas.impresora_id', 'LEFT'),
				array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT', array("CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"))
		);
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * get_impresoras: Devuelve las impresoras asociados al área.
	 *
	 * @param int $area_id
	 * @return array
	 */
	public function get_impresoras($area_id)
	{
		$impresoras = $this->db->query("SELECT gt_impresoras.id, gt_impresoras.modelo, gt_marcas.nombre
										 FROM gt_impresoras_areas
										 LEFT JOIN gt_impresoras ON gt_impresoras.id = gt_impresoras_areas.impresora_id
										 LEFT JOIN gt_marcas ON gt_marcas.id = gt_impresoras.marca_id
										 WHERE area_id = ?", array($area_id))->result();

		return $impresoras;
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