<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Efectores_model extends MY_Model
{

	/**
	 * Modelo de Efectores
	 * Autor: Leandro
	 * Creado: 09/09/2019
	 * Modificado: 09/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'na_efectores';
		$this->full_log = TRUE;
		$this->msg_name = 'Efector';
		$this->id_name = 'id';
		$this->columnas = array('id', 'nombre', 'contacto', 'telefono', 'celular', 'email', 'domicilio_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'nombre' => array('label' => 'Nombre', 'maxlength' => '50', 'required' => TRUE),
				'contacto' => array('label' => 'Contacto', 'maxlength' => '50'),
				'telefono' => array('label' => 'Teléfono', 'maxlength' => '50', 'required' => TRUE),
				'celular' => array('label' => 'Celular', 'maxlength' => '50'),
				'email' => array('label' => 'Email', 'maxlength' => '100', 'required' => TRUE)
		);
		$this->requeridos = array('nombre', 'telefono', 'email', 'domicilio_id');
		$this->unicos = array('nombre');
		$this->default_join = array(
				array('domicilios', 'domicilios.id = na_efectores.domicilio_id', 'LEFT',
						array(
								'domicilios.calle',
								'domicilios.barrio',
								'domicilios.altura',
								'domicilios.piso',
								'domicilios.dpto',
								'domicilios.manzana',
								'domicilios.casa',
								'domicilios.localidad_id'
						)
				),
				array('localidades', 'localidades.id = domicilios.localidad_id', 'LEFT'),
				array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'),
				array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT',
						array(
								"CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad"
						)
				)
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
		if ($this->db->where('efector_id', $delete_id)->count_all_results('na_intervenciones_detalles') > 0)
		{
			$this->_set_error('No se ha podido eliminar el registro de ' . $this->msg_name . '. Verifique que no esté asociado a una Intervención.');
			return FALSE;
		}
		return TRUE;
	}
}