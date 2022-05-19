<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cedulas_devoluciones_model extends MY_Model
{

	/**
	 * Modelo de Devoluciones de Cédula
	 * Autor: GENERATOR_MLC
	 * Creado: 02/07/2019
	 * Modificado: 02/07/2019 (GENERATOR_MLC)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'nv_cedulas_devoluciones';
		$this->full_log = TRUE;
		$this->msg_name = 'Devolución de Cédula';
		$this->id_name = 'id';
		$this->columnas = array('id', 'observaciones', 'cedula_id', 'tipo_devolucion_id');//, 'audi_usuario', 'audi_fecha', 'audi_accion', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
			'observaciones' => array('label' => 'Observaciones'),
			'cedula' => array('label' => 'Cedula', 'input_type' => 'combo'),
			'tipo_devolucion' => array('label' => 'Tipo de Devolucion', 'input_type' => 'combo'),
//			'audi_usuario' => array('label' => 'Audi de Usuario', 'type' => 'integer', 'maxlength' => '11'),
//			'audi_fecha' => array('label' => 'Audi de Fecha', 'type' => 'date'),
//			'audi_accion' => array('label' => 'Audi de Accion', 'maxlength' => '1')
		);
		$this->requeridos = array();
		//$this->unicos = array();
		$this->default_join = array();
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


    public function get_devolucion($cedula_id)
    {
        return $this->get(
            [
                'cedula_id' => $cedula_id,
                'join' => [
                    [
                        'nv_cedulas_devoluciones_tipo', 'nv_cedulas_devoluciones_tipo.id = nv_cedulas_devoluciones.tipo_devolucion_id', 'LEFT',
                        [
                            'nv_cedulas_devoluciones_tipo.descripcion as tipo_devolucion'
                        ]
                    ]
                ],
            ]
        )[0];
    }
    //Cedulas_devoluciones_model
    //Cedulas_devoluciones_tipo_model

}