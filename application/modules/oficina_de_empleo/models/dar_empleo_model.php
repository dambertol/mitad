<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo de dar_empleo 
 * Autor: Leandro
 * Creado: 10/10/2018
 * Modificado: 18/10/2018 (Pablo)
 */
/*
*****************************************esta clase se usa como repositorio de arrays declarados aca mismo*****************************************
*/
class dar_empleo_model extends MY_Model
{

	private $array_estadooooooooooos = array(    
			'FINALIZADO POR DECLARACION JURADA' => 'FINALIZADO POR DECLARACION JURADA',
			'FINALIZADO C/SUP GIS' => 'FINALIZADO CON SUPERFICIE GIS',
			'FINALIZADO S/SUP GIS' => 'FINALIZADO SIN SUPERFICIE GIS',
			'RECALCULO' => 'RECALCULO',
			'PENDIENTE' => 'PENDIENTE',
			'PENDIENTE P/ INSPECCION' => 'PENDIENTE P/ INSPECCION'
	);
	private $array_genero = array(
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
			'SI' => 'SI',
			'NO' => 'NO'
	);
	private $array_si_no = array(
			'SI' => 'SI',
			'NO' => 'NO'
	);

	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'dar_empleo'; 
		$this->full_log = TRUE;
		$this->msg_name = 'personas en busqueda';
		$this->id_name = 'id';
		$this->columnas = array('id', 'padron', 'agente', 'tipo', 'estado', 'inspeccion', 'cubierta_existente', 'pileta_existente', 'cubierta_gis_existente', 'pileta_gis_existente', 'cubierta_gis_nueva', 'pileta_gis_nueva', 'cubierta_declarada', 'pileta_declarada', 'observaciones', 'n_nota', 'telefono_contacto', 'si_no', 'fecha', 'n_orden', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(    //estos campos son del formulario propio*******************************************
			
				'n_orden' => array('label' => 'N° Orden', 'type' => 'integer', 'maxlength' => '11', 'disabled' => TRUE),
				'agente' => array('label' => 'Agente', 'maxlength' => '200', 'disabled' => TRUE),
				'padron' => array('label' => 'Padrón', 'type' => 'integer', 'maxlength' => '6', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'n_nota' => array('label' => 'N° Nota', 'maxlength' => '100'),
				'telefono_contacto' => array('label' => 'Teléfono', 'maxlength' => '255'),
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'id_name' => 'tipo', 'type' => 'bselect', 'required' => TRUE),
				'estado' => array('label' => 'Estado', 'input_type' => 'combo', 'id_name' => 'estado', 'type' => 'bselect', 'required' => TRUE),
				'inspeccion' => array('label' => 'Inspeccion', 'input_type' => 'combo', 'id_name' => 'inspeccion', 'type' => 'bselect', 'required' => TRUE),
				'si_no' => array('label' => 'Correccion de Capa', 'input_type' => 'combo', 'id_name' => 'si_no', 'type' => 'bselect', 'required' => TRUE),
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
		$this->requeridos = array('padron', 'estado', 'fecha');
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
		return $this->array_estadooooooooooos;
	}

	function get_tipos()
	{
		return $this->array_genero;
	}

	function get_inspeccion()
	{
		return $this->array_inspeccion;
	}

	function get_si_no()
	{
		return $this->array_si_no;
	}
}


/*
private $array_estadooooooooooos = array(                      estado del tramite
		
	private $array_genero = array(					como fue solicitado
	
	private $array_inspeccion = array(				s/n
	
	private $array_si_no = array(			S/N

	public function __construct()

	protected function _can_delete($delete_id)     s/n	

	function get_estados()		return $this->array_estadooooooooooos;

	function get_tipos()	return $this->array_genero;

	function get_inspeccion()	return $this->array_inspeccion;

	function get_si_no()	return $this->array_si_no;
	
*/