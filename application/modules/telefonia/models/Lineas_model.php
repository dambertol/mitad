<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lineas_model extends MY_Model
{

	/**
	 * Modelo de Líneas
	 * Autor: Leandro
	 * Creado: 03/09/2019
	 * Modificado: 22/10/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tm_lineas';
		$this->full_log = TRUE;
		$this->msg_name = 'Línea';
		$this->id_name = 'id';
		$this->columnas = array('id', 'numero', 'numero_corto', 'min_internacional', 'min_nacional', 'min_interno', 'datos', 'equipo_id', 'area_id', 'labo_Codigo', 'persona', 'estado', 'numero_sim', 'prestador_id', 'observaciones', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'prestador' => array('label' => 'Prestador', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'numero' => array('label' => 'Número', 'type' => 'integer', 'required' => TRUE),
				'numero_corto' => array('label' => 'Número Corto', 'type' => 'integer', 'maxlength' => '11'),
				'numero_sim' => array('label' => 'Número SIM', 'maxlength' => '50'),
				'min_internacional' => array('label' => 'Min. Internacional', 'type' => 'integer', 'maxlength' => '11'),
				'min_nacional' => array('label' => 'Min. Nacional', 'type' => 'integer', 'maxlength' => '11'),
				'min_interno' => array('label' => 'Min. Interno', 'type' => 'integer', 'maxlength' => '11'),
				'datos' => array('label' => 'Plan Datos', 'type' => 'integer', 'maxlength' => '11'),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'type' => 'bselect', 'id_name' => 'estado', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('prestador_id', 'numero', 'estado');
		$this->unicos = array('numero');
		$this->default_join = array(
				array('tm_prestadores', 'tm_prestadores.id = tm_lineas.prestador_id', 'LEFT', array("tm_prestadores.nombre as prestador")),
				array('personal', 'personal.Legajo = tm_lineas.labo_Codigo', 'LEFT', array("personal.Legajo as dni_personal, CONCAT(personal.Nombre, ' ', personal.Apellido) as nombre_personal")),
				array('areas', 'areas.id = tm_lineas.area_id', 'LEFT', array("areas.nombre as nombre_area")),
				array('tm_equipos', 'tm_equipos.id = tm_lineas.equipo_id', 'LEFT', array("tm_equipos.imei as imei")),
				array('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'LEFT', array("tm_modelos.nombre as modelo"))
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
		if ($this->db->where('linea_id', $delete_id)->count_all_results('tm_movimientos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Movimiento.');
			return FALSE;
		}
		return TRUE;
	}
}