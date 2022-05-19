<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Intervenciones extends MY_Controller
{

	/**
	 * Controlador de Intervenciones
	 * Autor: Leandro
	 * Creado: 13/09/2019
	 * Modificado: 17/09/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('ninez_adolescencia/Efectores_model');
		$this->load->model('ninez_adolescencia/Intervenciones_model');
		$this->load->model('ninez_adolescencia/Intervenciones_detalles_model');
		$this->load->model('ninez_adolescencia/Motivos_model');
		$this->load->model('ninez_adolescencia/Tipos_intervenciones_model');
		$this->grupos_permitidos = array('admin', 'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general');
		$this->grupos_solo_consulta = array('ninez_adolescencia_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar_data($expediente_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $expediente_id == NULL || !ctype_digit($expediente_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('na_intervenciones.id, na_intervenciones.fecha_intervencion, na_tipos_intervenciones.nombre as tipo_intervencion')
				->from('na_intervenciones')
				->join('na_tipos_intervenciones', 'na_tipos_intervenciones.id = na_intervenciones.tipo_intervencion_id', 'left')
				->where('expediente_id', $expediente_id)
				->add_column('ver', '<a href="ninez_adolescencia/intervenciones/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '<a href="ninez_adolescencia/intervenciones/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')
				->add_column('eliminar', '<a href="ninez_adolescencia/intervenciones/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function agregar($expediente_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $expediente_id == NULL || !ctype_digit($expediente_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect('ninez_adolescencia/intervenciones/listar', 'refresh');
		}

		$this->array_tipo_intervencion_control = $array_tipo_intervencion = $this->get_array('Tipos_intervenciones', 'nombre');
		$this->array_efector_control = $array_efector = $this->get_array('Efectores', 'nombre');
		$this->array_motivo_control = $array_motivo = $this->get_array('Motivos', 'nombre');

		$this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
		if ($this->input->post('cant_rows'))
		{
			$cant_rows = $this->input->post('cant_rows');
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$this->form_validation->set_rules('efector_' . $i, 'Efector ' . $i, 'required|callback_control_combo[efector]');
				$this->form_validation->set_rules('motivo_' . $i, 'Motivo ' . $i, 'required|callback_control_combo[motivo]');
				$this->form_validation->set_rules('observaciones_' . $i, 'Observaciones ' . $i, 'max_length[99999]');
			}
		}

		$this->set_model_validation_rules($this->Intervenciones_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Intervenciones_model->create(array(
					'expediente_id' => $expediente_id,
					'fecha_intervencion' => $this->get_date_sql('fecha_intervencion'),
					'tipo_intervencion_id' => $this->input->post('tipo_intervencion')), FALSE);

			$intervencion_id = $this->Intervenciones_model->get_row_id();
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$efector_id = $this->input->post('efector_' . $i);
				$motivo_id = $this->input->post('motivo_' . $i);
				$observaciones = $this->input->post('observaciones_' . $i);
				$trans_ok &= $this->Intervenciones_detalles_model->create(array(
						'intervencion_id' => $intervencion_id,
						'efector_id' => $efector_id,
						'motivo_id' => $motivo_id,
						'observaciones' => $observaciones), FALSE);
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Intervenciones_model->get_msg());
				redirect("ninez_adolescencia/expedientes/ver/$expediente_id", 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Intervenciones_model->get_error())
				{
					$error_msg .= $this->Intervenciones_model->get_error();
				}
				if ($this->Pedidos_consumibles_detalles_model->get_error())
				{
					$error_msg .= $this->Pedidos_consumibles_detalles_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Intervenciones_model->fields['tipo_intervencion']['array'] = $array_tipo_intervencion;
		$data['fields'] = $this->build_fields($this->Intervenciones_model->fields);

		$rows = $this->form_validation->set_value('cant_rows', 1);
		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"efector_$i" => array('label' => 'Efector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"motivo_$i" => array('label' => 'Motivo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"observaciones_$i" => array('label' => 'Observaciones', 'type' => 'text', 'required' => TRUE)
			);

			$fake_model_fields["efector_$i"]['array'] = $array_efector;
			$fake_model_fields["motivo_$i"]['array'] = $array_motivo;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, NULL, FALSE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['expediente_id'] = $expediente_id;
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Intervención';
		$data['title'] = TITLE . ' - Agregar Intervención';
		$data['js'] = 'js/ninez_adolescencia/base.js';
		$this->load_template('ninez_adolescencia/intervenciones/intervenciones_abm', $data);
	}

	public function editar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("ninez_adolescencia/intervenciones/ver/$id", 'refresh');
		}

		$this->array_tipo_intervencion_control = $array_tipo_intervencion = $this->get_array('Tipos_intervenciones', 'nombre');
		$intervencion = $this->Intervenciones_model->get(array('id' => $id));
		if (empty($intervencion))
		{
			show_error('No se encontró el Intervención', 500, 'Registro no encontrado');
		}

		$detalles_actuales = $this->Intervenciones_detalles_model->get(array('intervencion_id' => $intervencion->id));
		
		$this->array_efector_control = $array_efector = $this->get_array('Efectores', 'nombre');
		$this->array_motivo_control = $array_motivo = $this->get_array('Motivos', 'nombre');

		$this->form_validation->set_rules('cant_rows', 'Cantidad de Detalles', 'required|integer');
		if ($this->input->post('cant_rows'))
		{
			$cant_rows = $this->input->post('cant_rows');
			for ($i = 1; $i <= $cant_rows; $i++)
			{
				$this->form_validation->set_rules('efector_' . $i, 'Efector ' . $i, 'required|callback_control_combo[efector]');
				$this->form_validation->set_rules('motivo_' . $i, 'Motivo ' . $i, 'required|callback_control_combo[motivo]');
				$this->form_validation->set_rules('observaciones_' . $i, 'Observaciones ' . $i, 'max_length[99999]');
			}
		}

		$this->set_model_validation_rules($this->Intervenciones_model);
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Intervenciones_model->update(array(
						'id' => $this->input->post('id'),
						'expediente_id' => $intervencion->expediente_id,
						'fecha_intervencion' => $this->get_date_sql('fecha_intervencion'),
						'tipo_intervencion_id' => $this->input->post('tipo_intervencion')), FALSE);

				$post_detalles_update = array();
				$post_detalles_create = array();
				for ($i = 1; $i <= $cant_rows; $i++)
				{
					$detalle_post = new stdClass();
					$detalle_post->id = $this->input->post('id_detalle_' . $i);
					$detalle_post->efector = $this->input->post('efector_' . $i);
					$detalle_post->motivo = $this->input->post('motivo_' . $i);
					$detalle_post->observaciones = $this->input->post('observaciones_' . $i);
					if (empty($detalle_post->id) || $detalle_post->id === 'nuevo')
					{
						$post_detalles_create[] = $detalle_post;
					}
					else
					{
						$post_detalles_update[$detalle_post->id] = $detalle_post;
					}
				}

				if (!empty($detalles_actuales))
				{
					foreach ($detalles_actuales as $Detalle_actual)
					{
						if (isset($post_detalles_update[$Detalle_actual->id]))
						{
							$trans_ok &= $this->Intervenciones_detalles_model->update(array(
									'id' => $Detalle_actual->id,
									'efector_id' => $post_detalles_update[$Detalle_actual->id]->efector,
									'motivo_id' => $post_detalles_update[$Detalle_actual->id]->motivo,
									'observaciones' => $post_detalles_update[$Detalle_actual->id]->observaciones), FALSE);
						}
						else
						{
							$trans_ok &= $this->Intervenciones_detalles_model->delete(array(
									'id' => $Detalle_actual->id), FALSE);
						}
					}
				}
				foreach ($post_detalles_create as $Detalle_actual)
				{
					$trans_ok &= $this->Intervenciones_detalles_model->create(array(
							'intervencion_id' => $id,
							'efector_id' => $Detalle_actual->efector,
							'motivo_id' => $Detalle_actual->motivo,
							'observaciones' => $Detalle_actual->observaciones), FALSE);
				}

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Intervenciones_model->get_msg());
					redirect("ninez_adolescencia/expedientes/ver/$intervencion->expediente_id", 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Intervenciones_model->get_error())
					{
						$error_msg .= $this->Intervenciones_model->get_error();
					}
					if ($this->Intervenciones_detalles_model->get_error())
					{
						$error_msg .= $this->Intervenciones_detalles_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Intervenciones_model->fields['tipo_intervencion']['array'] = $array_tipo_intervencion;
		$data['fields'] = $this->build_fields($this->Intervenciones_model->fields, $intervencion);

		if (empty($_POST))
		{
			$detalles = $detalles_actuales;
		}
		else
		{
			$detalles = array();
		}
		$rows = $this->form_validation->set_value('cant_rows', sizeof($detalles));
		$data['fields_detalle_array'] = array();

		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"efector_$i" => array('label' => 'Efector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"motivo_$i" => array('label' => 'Motivo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"observaciones_$i" => array('label' => 'Observaciones', 'type' => 'text', 'required' => TRUE)
			);

			if (empty($_POST))
			{
				$temp_detalle = new stdClass();
				$temp_detalle->{"id_detalle_{$i}"} = $detalles[$i - 1]->id;
				$temp_detalle->{"efector_{$i}_id"} = $detalles[$i - 1]->efector_id;
				$temp_detalle->{"motivo_{$i}_id"} = $detalles[$i - 1]->motivo_id;
				$temp_detalle->{"observaciones_{$i}"} = $detalles[$i - 1]->observaciones;
			}
			else
			{
				$temp_detalle = NULL;
			}

			$fake_model_fields["efector_$i"]['array'] = $array_efector;
			$fake_model_fields["motivo_$i"]['array'] = $array_motivo;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, FALSE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['intervencion'] = $intervencion;
		$data['expediente_id'] = $intervencion->expediente_id;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Intervención';
		$data['title'] = TITLE . ' - Editar Intervención';
		$data['js'] = 'js/ninez_adolescencia/base.js';
		$this->load_template('ninez_adolescencia/intervenciones/intervenciones_abm', $data);
	}

	public function eliminar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("ninez_adolescencia/intervenciones/ver/$id", 'refresh');
		}

		$intervencion = $this->Intervenciones_model->get_one($id);
		if (empty($intervencion))
		{
			show_error('No se encontró el Intervención', 500, 'Registro no encontrado');
		}

		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Intervenciones_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Intervenciones_model->get_msg());
				redirect('ninez_adolescencia/intervenciones/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Intervenciones_model->get_error())
				{
					$error_msg .= $this->Intervenciones_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$detalles = $this->Intervenciones_detalles_model->get(array(
				'intervencion_id' => $intervencion->id,
				'join' => array(
						array('na_efectores', 'na_efectores.id = na_intervenciones_detalles.efector_id', 'LEFT', array('na_efectores.nombre as efector')),
						array('na_motivos', 'na_motivos.id = na_intervenciones_detalles.motivo_id', 'LEFT', array('na_motivos.nombre as motivo')),
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Intervención', 500, 'Registro no encontrado');
		}
		else
		{
			$rows = count($detalles);
		}

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"efector_$i" => array('label' => 'Efector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"motivo_$i" => array('label' => 'Motivo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"observaciones_$i" => array('label' => 'Observaciones', 'type' => 'text', 'required' => TRUE)
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"efector_{$i}"} = $detalles[$i - 1]->efector;
			$temp_detalle->{"motivo_{$i}"} = $detalles[$i - 1]->motivo;
			$temp_detalle->{"observaciones_{$i}"} = $detalles[$i - 1]->observaciones;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['fields'] = $this->build_fields($this->Intervenciones_model->fields, $intervencion, TRUE);
		$data['intervencion'] = $intervencion;
		$data['expediente_id'] = $intervencion->expediente_id;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Intervención';
		$data['title'] = TITLE . ' - Eliminar Intervención';
		$this->load_template('ninez_adolescencia/intervenciones/intervenciones_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$intervencion = $this->Intervenciones_model->get_one($id);
		if (empty($intervencion))
		{
			show_error('No se encontró el Intervención', 500, 'Registro no encontrado');
		}

		$detalles = $this->Intervenciones_detalles_model->get(array(
				'intervencion_id' => $intervencion->id,
				'join' => array(
						array('na_efectores', 'na_efectores.id = na_intervenciones_detalles.efector_id', 'LEFT', array('na_efectores.nombre as efector')),
						array('na_motivos', 'na_motivos.id = na_intervenciones_detalles.motivo_id', 'LEFT', array('na_motivos.nombre as motivo')),
				)
		));
		if (empty($detalles))
		{
			show_error('No se encontró el Detalle de la Intervención', 500, 'Registro no encontrado');
		}
		else
		{
			$rows = count($detalles);
		}

		$data['fields_detalle_array'] = array();
		for ($i = 1; $i <= $rows; $i++)
		{
			$fake_model_fields = array(
					"efector_$i" => array('label' => 'Efector', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"motivo_$i" => array('label' => 'Motivo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
					"observaciones_$i" => array('label' => 'Observaciones', 'type' => 'text', 'required' => TRUE)
			);

			$temp_detalle = new stdClass();
			$temp_detalle->{"efector_{$i}"} = $detalles[$i - 1]->efector;
			$temp_detalle->{"motivo_{$i}"} = $detalles[$i - 1]->motivo;
			$temp_detalle->{"observaciones_{$i}"} = $detalles[$i - 1]->observaciones;
			$data['fields_detalle_array'][] = $this->build_fields($fake_model_fields, $temp_detalle, TRUE, 'table');
		}

		$data['cant_rows'] = array(
				'name' => 'cant_rows',
				'id' => 'cant_rows',
				'type' => 'hidden',
				'value' => $rows
		);

		$data['fields'] = $this->build_fields($this->Intervenciones_model->fields, $intervencion, TRUE);
		$data['intervencion'] = $intervencion;
		$data['expediente_id'] = $intervencion->expediente_id;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Intervención';
		$data['title'] = TITLE . ' - Ver Intervención';
		$this->load_template('ninez_adolescencia/intervenciones/intervenciones_abm', $data);
	}
}