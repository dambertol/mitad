<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo de Reclamos Potrerillos
 * Autor: Leandro
 * Creado: 14/03/2019
 * Modificado: 05/04/2019 (Leandro)
 */
class Reclamos_potrerillos_model extends MY_Model
{

	private $array_estados = array(
			NULL => 'SIN ESPECIFICAR',
			'FINALIZADO POR DECLARACION JURADA' => 'FINALIZADO POR DECLARACION JURADA',
			'FINALIZADO C/SUP GIS' => 'FINALIZADO CON SUPERFICIE GIS',
			'FINALIZADO S/SUP GIS' => 'FINALIZADO SIN SUPERFICIE GIS',
			'RECALCULO' => 'RECALCULO',
			'PENDIENTE' => 'PENDIENTE',
			'PENDIENTE P/ INSPECCION' => 'PENDIENTE P/ INSPECCION'
	);
	private $array_tipo = array(
			NULL => 'SIN ESPECIFICAR',
			'NOTA' => 'NOTA',
			'PRESENCIAL' => 'PRESENCIAL',
			'CORREO' => 'CORREO',
			'FORMULARIO BIC' => 'FORMULARIO BIC',
			'LLAMADO TEL' => 'LLAMADO TEL',
			'OFICIO' => 'OFICIO',
			'TRANSFERENCIA' => 'TRANSFERENCIA',
			'OTRO' => 'OTRO'
	);
	private $array_inspeccion = array(
			NULL => 'SIN ESPECIFICAR',
			'SI' => 'SI',
			'NO' => 'NO'
	);
	private $array_correccion_capa = array(
			NULL => 'SIN ESPECIFICAR',
			'SI' => 'SI',
			'NO' => 'NO'
	);

	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'gis_reclamos_potrerillos';
		$this->full_log = TRUE;
		$this->msg_name = 'Reclamo Potrerillos';
		$this->id_name = 'id';
		$this->columnas = array('id', 'padron', 'nomenclatura', 'agente', 'tipo', 'estado', 'inspeccion', 'cubierta_existente', 'pileta_existente', 'cubierta_gis_existente', 'pileta_gis_existente', 'cubierta_gis_nueva', 'pileta_gis_nueva', 'cubierta_declarada', 'pileta_declarada', 'observaciones', 'n_nota', 'telefono_contacto', 'correccion_capa', 'fecha', 'n_orden', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'n_orden' => array('label' => 'N° Orden', 'type' => 'integer', 'maxlength' => '11', 'disabled' => TRUE),
				'agente' => array('label' => 'Agente', 'maxlength' => '200', 'disabled' => TRUE),
				'padron' => array('label' => 'Padrón', 'type' => 'integer', 'maxlength' => '6'),
				'nomenclatura' => array('label' => 'Nomenclatura', 'type' => 'natural', 'maxlength' => '25', 'required' => TRUE),  
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime'),
				'n_nota' => array('label' => 'N° Nota', 'maxlength' => '100'),
				'telefono_contacto' => array('label' => 'Teléfono', 'maxlength' => '255'),
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'id_name' => 'tipo', 'type' => 'bselect'),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'id_name' => 'estado', 'type' => 'bselect'),
				'inspeccion' => array('label' => 'Inspeccion', 'input_type' => 'combo', 'id_name' => 'inspeccion', 'type' => 'bselect'),
				'correccion_capa' => array('label' => 'Correccion de Capa', 'input_type' => 'combo', 'id_name' => 'correccion_capa', 'type' => 'bselect'),
				'cubierta_existente' => array('label' => 'Cubierta Existente', 'type' => 'integer', 'maxlength' => '11'),
				'pileta_existente' => array('label' => 'Pileta Existente', 'type' => 'integer', 'maxlength' => '11'),
				'cubierta_gis_existente' => array('label' => 'Cubierta Gis Existente', 'type' => 'integer', 'maxlength' => '11'),
				'pileta_gis_existente' => array('label' => 'Pileta Gis Existente', 'type' => 'integer', 'maxlength' => '11'),
				'cubierta_gis_nueva' => array('label' => 'Cubierta Gis Nueva', 'type' => 'integer', 'maxlength' => '11'),
				'pileta_gis_nueva' => array('label' => 'Pileta de Gis Nueva', 'type' => 'integer', 'maxlength' => '11'),
				'cubierta_declarada' => array('label' => 'Cubierta Declarada', 'type' => 'integer', 'maxlength' => '11'),
				'pileta_declarada' => array('label' => 'Pileta Declarada', 'type' => 'integer', 'maxlength' => '11'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999'),
		);
		$this->requeridos = array('nomenclatura');
		$this->unicos = array('padron');
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

	function get_estados()
	{
		return $this->array_estados;
	}

	function get_tipos()
	{
		return $this->array_tipo;
	}

	function get_inspeccion()
	{
		return $this->array_inspeccion;
	}

	function get_correccion_capa()
	{
		return $this->array_correccion_capa;
	}
}