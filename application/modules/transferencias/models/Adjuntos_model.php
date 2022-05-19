<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Adjuntos_model extends MY_Model
{

	/**
	 * Modelo de Adjuntos
	 * Autor: Leandro
	 * Creado: 05/06/2018
	 * Modificado: 10/06/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'tr_adjuntos';
		$this->full_log = TRUE;
		$this->msg_name = 'Adjunto';
		$this->id_name = 'id';
		$this->columnas = array('id', 'tipo_id', 'nombre', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida', 'tramite_id', 'pase_id', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'tipo_adjunto' => array('label' => 'Tipo', 'id_name' => 'tipo_id', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
				'nombre' => array('label' => 'Nombre', 'maxlength' => '100', 'required' => TRUE),
				'ruta' => array('label' => 'Ruta', 'maxlength' => '100', 'required' => TRUE),
				'tamanio' => array('label' => 'Tamaño', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'hash' => array('label' => 'Hash', 'required' => TRUE),
				'fecha_subida' => array('label' => 'Fecha Subida', 'type' => 'date', 'required' => TRUE),
				'usuario_subida' => array('label' => 'Usuario Subida', 'type' => 'integer', 'maxlength' => '10', 'required' => TRUE),
				'tramite' => array('label' => 'Trámite', 'input_type' => 'combo', 'type' => 'bselect'),
				'pase' => array('label' => 'Pase', 'input_type' => 'combo', 'type' => 'bselect')
		);
		$this->requeridos = array('tipo_id', 'nombre', 'ruta', 'tamanio', 'hash', 'fecha_subida', 'usuario_subida');
		$this->unicos = array(array('nombre', 'ruta'));
		$this->default_join = array(
				array('tr_adjuntos_tipos', 'tr_adjuntos_tipos.id = tr_adjuntos.tipo_id', 'LEFT',
						array(
								'tr_adjuntos_tipos.nombre as tipo_adjunto'
						)
		));
		// Inicializaciones necesarias colocar acá.
	}

	/**
	 * delete_adjuntos: Elimina adjuntos de un trámite.
	 *
	 * @param int $tramite_id
	 * @param int $tipo_id
	 * @return bool
	 */
	public function delete_adjuntos($tramite_id, $tipo_id)
	{
		$this->db->where('tramite_id', $tramite_id);
		$this->db->where('tipo_id', $tipo_id);

		if ($this->db->delete($this->table_name))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * escribano_adjunto: Devuelve el id del escribano al que pertenece el adjunto.
	 *
	 * @param string $ruta
	 * @param string $nombre
	 * @return int $escribano_id
	 */
	public function escribano_adjunto($ruta, $nombre)
	{
		$this->db->where('ruta', $ruta);
		$this->db->where('nombre', $nombre);
		$adjunto = $this->db->get($this->table_name)->row(0);

		if (!empty($adjunto))
		{
			if (!empty($adjunto->tramite_id))
			{
				$tramite_id = $adjunto->tramite_id;
			}
			elseif (!empty($adjunto->pase_id))
			{
				$this->db->where('id', $adjunto->pase_id);
				$pase = $this->db->get('tr_pases')->row(0);
				if (!empty($pase->tramite_id))
				{
					$tramite_id = $pase->tramite_id;
				}
			}
		}

		if (!empty($tramite_id))
		{
			$this->db->where('id', $tramite_id);
			$tramite = $this->db->get('tr_tramites')->row(0);
			;
			if (!empty($tramite))
			{
				return $tramite->escribano_id;
			}
		}

		return NULL;
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