<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Personal_model extends MY_Model
{

	/**
	 * Modelo de Personal
	 * Autor: Leandro 
	 * Creado: 10/11/2017
	 * Modificado: 10/11/2017 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'personal';
		$this->full_log = FALSE;
		$this->msg_name = 'Personal';
		$this->id_name = 'Legajo';
		$this->columnas = array('Legajo', 'Apellido', 'Nombre', 'Area');
		$this->requeridos = array('Legajo');
		$this->unicos = array('Legajo');
		// Inicializaciones necesarias colocar acá.
	}

	public function get_sp()
	{
		$this->db->cache_on();
		$query = $this->db->query("SP_GET_PERSONAL");
		if ($query->num_rows() == 0)
		{
			$this->_set_error('No hay resultados a mostrar');
			return FALSE;
		}
		$result = $query->result();
		$this->db->cache_off();
		return $result;
	}
}