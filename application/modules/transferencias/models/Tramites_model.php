<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tramites_model extends MY_Model
{

	/**
	 * Modelo de Trámites
	 * Autor: Leandro
	 * Creado: 21/05/2018
	 * Modificado: 24/10/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_tramites';
		$this->full_log = TRUE;
		$this->msg_name = 'Trámite';
		$this->id_name = 'id';
		$this->columnas = array('id', 'fecha_inicio', 'tipo_id', 'escribano_id', 'inmueble_id', 'observaciones', 'fecha_fin', 'escritura_nro', 'escritura_foja', 'escritura_fecha', 'transferencia_nro', 'transferencia_eje', 'relacionado_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
				'relacionado' => array('label' => 'Trámite relacionado', 'input_type' => 'combo', 'type' => 'bselect')
		);
		$this->requeridos = array('fecha_inicio', 'tipo_id', 'escribano_id', 'inmueble_id');
		//$this->unicos = array();
		$this->default_join = array(
				array('tr_tramites_tipos', 'tr_tramites_tipos.id = tr_tramites.tipo_id', 'LEFT',
						array(
								'tr_tramites_tipos.nombre as tipo'
						)
				),
				array('tr_escribanos', 'tr_escribanos.id = tr_tramites.escribano_id', 'LEFT',
						array(
								'tr_escribanos.matricula_nro',
								'tr_escribanos.registro_nro',
								'tr_escribanos.registro_tipo'
						)
				),
				array('personas', 'personas.id = tr_escribanos.persona_id', 'LEFT',
						array(
								'personas.cuil',
								'personas.nombre',
								'personas.apellido',
								'personas.telefono',
								'personas.celular',
								'personas.email',
								'personas.domicilio_id'
						)
				),
				array('tr_inmuebles', 'tr_inmuebles.id = tr_tramites.inmueble_id', 'LEFT',
						array(
								'tr_inmuebles.padron',
								'tr_inmuebles.nomenclatura',
								'tr_inmuebles.sup_titulo',
								'tr_inmuebles.sup_mensura',
								'tr_inmuebles.sup_afectada',
								'tr_inmuebles.sup_cubierta'
						)
				),
				array('tr_tramites R', 'R.id = tr_tramites.relacionado_id', 'LEFT',
						array(
								"CONCAT('Trámite N° ', R.id) as relacionado"
						)
				),
		);
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * generar_transferencia: Genera el número de transferencia (según Ejercicio).
	 *
	 * @param int $tramite_id
	 * @param int $ejercicio
	 * @return int
	 */
	public function generar_transferencia($tramite_id, $ejercicio)
	{
		$query = "UPDATE $this->table_name 
							SET transferencia_eje = $ejercicio,
									transferencia_nro = (
										SELECT COALESCE(MAX(num),1) as num FROM
											( SELECT (MAX(t.transferencia_nro) + 1) as num FROM tr_tramites t WHERE t.transferencia_eje = $ejercicio
											  UNION
											  SELECT numero_inicial as num FROM tr_numeraciones WHERE ejercicio = $ejercicio
											) a
										)
							WHERE id = $tramite_id";

		$result = $this->db->query($query);

		return $result;
	}

	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('tramite_id', $delete_id)->count_all_results('tr_adjuntos') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Adjunto.');
			return FALSE;
		}
		if ($this->db->where('tramite_id', $delete_id)->count_all_results('tr_intervinientes') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Interviniente.');
			return FALSE;
		}
		if ($this->db->where('tramite_id', $delete_id)->count_all_results('tr_pases') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a un Pases.');
			return FALSE;
		}
		return TRUE;
	}
}