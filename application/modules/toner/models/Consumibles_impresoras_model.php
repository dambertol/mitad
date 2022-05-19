<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Consumibles_impresoras_model extends MY_Model
{

	/**
	 * Modelo de Consumibles Impresora
	 * Autor: Leandro
	 * Creado: 07/05/2019
	 * Modificado: 14/05/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gt_consumibles_impresoras';
		$this->full_log = TRUE;
		$this->msg_name = 'Consumible Impresora';
		$this->id_name = 'id';
		$this->columnas = array('id', 'consumible_id', 'impresora_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'consumible' => array('label' => 'Consumible', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'impresora' => array('label' => 'Impresora', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE)
		);
		$this->requeridos = array('consumible_id', 'impresora_id');
		$this->unicos = array(array('consumible_id', 'impresora_id'));
		$this->default_join = array(
				array('gt_consumibles', 'gt_consumibles.id = gt_consumibles_impresoras.consumible_id', 'LEFT', array("CONCAT(gt_consumibles.modelo, ' - ', gt_consumibles.descripcion) as consumible")),
				array('gt_impresoras', 'gt_impresoras.id = gt_consumibles_impresoras.impresora_id', 'LEFT'),
				array('gt_marcas', 'gt_marcas.id = gt_impresoras.marca_id', 'LEFT', array("CONCAT(gt_marcas.nombre, ' - ', gt_impresoras.modelo) as impresora"))
		);
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * get_consumibles: Devuelve los consumibles asociados a la impresora.
	 *
	 * @param int $impesora_id
	 * @return array
	 */
	public function get_consumibles($impesora_id)
	{
		$consumibles = $this->db->query("SELECT gt_consumibles.id, gt_consumibles.modelo, gt_consumibles.descripcion
										 FROM gt_consumibles_impresoras
										 LEFT JOIN gt_consumibles ON gt_consumibles.id = gt_consumibles_impresoras.consumible_id
										 WHERE impresora_id = ?", array($impesora_id))->result();

		return $consumibles;
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