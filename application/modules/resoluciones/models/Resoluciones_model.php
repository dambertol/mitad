<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Resoluciones_model extends MY_Model
{

	/**
	 * Modelo de Resoluciones
	 * Autor: Leandro
	 * Creado: 29/11/2017
	 * Modificado: 12/06/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 're_resoluciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Resolución';
		$this->id_name = 'id';
		$this->columnas = array('fecha', 'fecha_carga', 'id', 'expt_ejercicio', 'expt_numero', 'expt_matricula', 'tipo_resolucion_id', 'ejercicio', 'numero', 'usuario_carga', 'titulo', 'texto', 'estado', 'motivo', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_resolucion' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'ejercicio' => array('label' => 'Ejercicio', 'type' => 'integer', 'maxlength' => '4', 'required' => TRUE),
				'titulo' => array('label' => 'Título', 'maxlength' => '255', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'date', 'required' => TRUE),
				'expt_numero' => array('label' => 'Expt Número', 'maxlength' => '50'),
				'expt_ejercicio' => array('label' => 'Expt Ejercicio', 'type' => 'integer', 'maxlength' => '4'),
				'expt_matricula' => array('label' => 'Expt Matrícula', 'type' => 'integer', 'maxlength' => '1'),
				'formato' => array('label' => 'Formato', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'texto' => array('label' => 'Texto *', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);
		$this->requeridos = array('tipo_resolucion_id', 'numero', 'ejercicio', 'titulo', 'fecha', 'fecha_carga', 'usuario_carga', 'estado');
		$this->unicos = array(array('tipo_resolucion_id', 'numero', 'ejercicio'));
		$this->default_join = array(
				array('re_tipos_resoluciones', 're_tipos_resoluciones.id = re_resoluciones.tipo_resolucion_id', 'LEFT', array("re_tipos_resoluciones.nombre as tipo_resolucion"))
		);
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * get_ultima_resolucion: Devuelve el último número de resolución utilizado (según Tipo y Ejercicio).
	 *
	 * @param int $tipo_resolucion_id
	 * @param int $ejercicio
	 * @return int
	 */
	public function get_ultima_resolucion($tipo_resolucion_id, $ejercicio)
	{
		$query_numero = "SELECT COALESCE(MAX(inicial),1) as inicial FROM
										(SELECT (MAX(numero) + 1) as inicial FROM re_resoluciones WHERE tipo_resolucion_id = $tipo_resolucion_id AND ejercicio = $ejercicio
										UNION
										SELECT numero_inicial as inicial FROM re_numeraciones WHERE tipo_resolucion_id = $tipo_resolucion_id AND ejercicio = $ejercicio) a";
		$ultima_resolucion = $this->db->query($query_numero)->row();

		return $ultima_resolucion->inicial;
	}

	/**
	 * _can_delete: Devuelve TRUE si puede eliminarse el registro.
	 *
	 * @param int $delete_id
	 * @return bool
	 */
	protected function _can_delete($delete_id)
	{
		if ($this->db->where('resolucion_id', $delete_id)->count_all_results('re_adjuntos_resoluciones') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a Adjuntos.');
			return FALSE;
		}
		return TRUE;
	}
}