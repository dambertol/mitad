<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Incidencias_model extends MY_Model
{

	/**
	 * Modelo de Incidencias
	 * Autor: Leandro
	 * Creado: 17/12/2019
	 * Modificado: 30/01/2020 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'rm_incidencias';
		$this->full_log = TRUE;
		$this->msg_name = 'Incidencia';
		$this->id_name = 'id';
		$this->columnas = array('id', 'numero', 'fecha_inicio', 'area_id', 'contacto', 'telefono', 'categoria_id', 'titulo', 'detalle', 'estado', 'fecha_finalizacion', 'resolucion', 'user_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'numero' => array('label' => 'Número', 'type' => 'integer'),
				'area' => array('label' => 'Área', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'fecha_inicio' => array('label' => 'Fecha Inicio', 'type' => 'date', 'required' => TRUE),
				'contacto' => array('label' => 'Contacto', 'maxlength' => '100', 'required' => TRUE),
				'telefono' => array('label' => 'Teléfono', 'maxlength' => '50'),
				'categoria' => array('label' => 'Categoría', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'titulo' => array('label' => 'Título', 'maxlength' => '100'),
				'detalle' => array('label' => 'Detalle', 'form_type' => 'textarea', 'rows' => 5)
		);
		$this->requeridos = array('area_id', 'fecha_inicio', 'contacto', 'categoria_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('areas', 'areas.id = rm_incidencias.area_id', 'LEFT', array("CONCAT(areas.codigo, ' - ', areas.nombre) as area")),
				array('rm_categorias', 'rm_categorias.id = rm_incidencias.categoria_id', 'LEFT', array('rm_categorias.descripcion as categoria')),
				array('users', 'users.id = rm_incidencias.user_id', 'LEFT'),
				array('personas', 'personas.id = users.persona_id', 'LEFT', array("CONCAT(personas.apellido, ', ', personas.nombre, ' (', users.username, ')') as user"))
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
		if ($this->db->where('incidencia_id', $delete_id)->count_all_results('rm_observaciones_incidencias') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Observación.');
			return FALSE;
		}
		return TRUE;
	}
}