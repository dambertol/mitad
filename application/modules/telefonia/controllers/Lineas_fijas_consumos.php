<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Lineas_fijas_consumos extends MY_Controller
{

	/**
	 * Controlador de Consumos Líneas Fijas
	 * Autor: Leandro
	 * Creado: 05/09/2019
	 * Modificado: 19/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('telefonia/Lineas_fijas_consumos_model');
		$this->load->model('telefonia/Lineas_fijas_model');
		$this->grupos_permitidos = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
		$this->grupos_solo_consulta = array('telefonia_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar($periodo_get = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || ($periodo_get !== NULL && !ctype_digit($periodo_get)))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$periodos = array();
		$inicio = '201403';
		$fin = date_format(new DateTime(), 'Ym');
		$periodos[$fin] = $fin;
		$periodo = date_format(new DateTime($fin . '01 -1 month'), 'Ym');
		while ($inicio <= $periodo)
		{
			$periodos[$periodo] = $periodo;
			$periodo = date_format(new DateTime($periodo . '01 -1 month'), 'Ym');
		}
		if (empty($periodo_get))
		{
			$periodo_get = $fin;
		}

		$consumos = $this->Lineas_fijas_consumos_model->get(array(
				'periodo' => $periodo_get,
				'join' => array(
						array(
								'table' => 'tm_lineas_fijas',
								'where' => 'tm_lineas_fijas.id=tm_lineas_fijas_consumos.telefono_id',
								'columnas' => array('tm_lineas_fijas.linea as linea', 'tm_lineas_fijas.domicilio as domicilio', 'tm_lineas_fijas.observaciones as observaciones')
						),
						array(
								'type' => 'left',
								'table' => 'areas',
								'where' => 'areas.id=tm_lineas_fijas.area_id',
								'columnas' => array('areas.codigo as cod_area', 'areas.nombre as area')
						),
				),
				'sort_by' => 'tm_lineas_fijas.linea'
		));

		$data['error'] = $this->session->flashdata('error');

		$data['periodo_opt'] = $periodos;
		$data['periodo_id'] = $periodo_get;
		$data['consumos'] = $consumos;
		$data['title_view'] = 'Consumos de Líneas Fijas';
		$data['title'] = TITLE . ' - Consumos de Líneas Fijas';
		$this->load_template('telefonia/lineas_fijas_consumos/lineas_fijas_consumos_listar', $data);
	}

	public function cargar($periodo_get = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || ($periodo_get !== NULL && !ctype_digit($periodo_get)))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect("telefonia/lineas_fijas_consumos/listar", 'refresh');
		}

		if (empty($periodo_get))
		{
			$periodo_get = date_format(new DateTime(), 'Ym');
		}

		$consumos = $this->Lineas_fijas_consumos_model->get(array(
				'periodo' => $periodo_get,
				'join' => array(
						array(
								'table' => 'tm_lineas_fijas',
								'where' => 'tm_lineas_fijas.id=tm_lineas_fijas_consumos.telefono_id',
								'columnas' => array('tm_lineas_fijas.linea as linea', 'tm_lineas_fijas.domicilio as domicilio', 'tm_lineas_fijas.observaciones as observaciones')
						),
						array(
								'type' => 'left',
								'table' => 'areas',
								'where' => 'areas.id=tm_lineas_fijas.area_id',
								'columnas' => array('areas.codigo as cod_area', 'areas.nombre as area')
						),
				),
				'sort_by' => 'tm_lineas_fijas.linea'
		));

		$lineas_periodo = $this->Lineas_fijas_model->get(array(
				'periodo_ini <=' => $periodo_get,
				'where' => array(
						array(
								'column' => '(periodo_fin >=',
								'value' => "$periodo_get OR periodo_fin IS NULL)",
								'override' => TRUE
						),
						array(
								'column' => 'tm_lineas_fijas.id NOT IN',
								'value' => " (SELECT telefono_id FROM tm_lineas_fijas_consumos WHERE periodo=$periodo_get)",
								'override' => TRUE
						)
				),
				'join' => array(
						array(
								'type' => 'left',
								'table' => 'areas',
								'where' => 'areas.id=tm_lineas_fijas.area_id',
								'columnas' => array('areas.codigo as cod_area', 'areas.nombre as area')
						),
				),
				'sort_by' => 'tm_lineas_fijas.linea'
		));

		$this->array_estado_control = $array_estado = array('Impago' => 'Impago', 'Pago' => 'Pago');

		if (!empty($consumos))
		{
			foreach ($consumos as $consumo)
			{
				$this->form_validation->set_rules('consumos_' . $consumo->id, 'Consumo ' . $consumo->linea, 'required|numeric');
				$this->form_validation->set_rules('consumos_estado_' . $consumo->id, 'Estado ' . $consumo->linea, 'required|callback_control_combo[estado]');
			}
		}

		if (!empty($lineas_periodo))
		{
			foreach ($lineas_periodo as $linea_periodo)
			{
				$this->form_validation->set_rules('lineas_periodo_' . $linea_periodo->id, 'Linea ' . $linea_periodo->linea, 'required|numeric');
				$this->form_validation->set_rules('lineas_periodo_estado_' . $linea_periodo->id, 'Estado ' . $linea_periodo->linea, 'required|callback_control_combo[estado]');
			}
		}

		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$this->db->trans_begin();
			$trans_ok = TRUE;
			if (!empty($consumos))
			{
				foreach ($consumos as $consumo)
				{
					$trans_ok &= $this->Lineas_fijas_consumos_model->update(array(
							'id' => $consumo->id,
							'monto' => $this->input->post('consumos_' . $consumo->id),
							'estado' => $this->input->post('consumos_estado_' . $consumo->id)
							), FALSE);
				}
			}

			if (!empty($lineas_periodo))
			{
				foreach ($lineas_periodo as $linea_periodo)
				{
					$trans_ok &= $this->Lineas_fijas_consumos_model->create(array(
							'telefono_id' => $linea_periodo->id,
							'periodo' => $periodo_get,
							'monto' => $this->input->post('lineas_periodo_' . $linea_periodo->id),
							'estado' => $this->input->post('lineas_periodo_estado_' . $linea_periodo->id)
							), FALSE);
				}
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Lineas_fijas_consumos_model->get_msg());
				redirect('telefonia/lineas_fijas_consumos/listar/' . $periodo_get, 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Lineas_fijas_consumos_model->get_error())
				{
					$error_msg .= $this->Lineas_fijas_consumos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$array_form_consumos = array();
		if (!empty($consumos))
		{
			foreach ($consumos as $Consumo)
			{
				$array_form_consumos[] = array(
						'campo' => $Consumo,
						'form' => array(
								'name' => 'consumos_' . $Consumo->id,
								'class' => 'form-control numberFormat',
								'pattern' => '[-]?[0-9]*[.,]?[0-9]+',
								'title' => 'Debe ingresar sólo números decimales',
								'id' => 'consumos_' . $Consumo->id,
								'value' => $this->form_validation->set_value('consumos_' . $Consumo->id, $Consumo->monto),
						),
						'estado' => 'consumos_estado_' . $Consumo->id,
						'estado_opt' => $array_estado,
						'estado_opt_selected' => $this->form_validation->set_value('consumos_estado_' . $Consumo->id, $Consumo->estado)
				);
			}
		}

		$array_form_lineas_periodo = array();
		if (!empty($lineas_periodo))
		{
			foreach ($lineas_periodo as $Linea)
			{
				$array_form_lineas_periodo[] = array(
						'campo' => $Linea,
						'form' => array(
								'name' => 'lineas_periodo_' . $Linea->id,
								'class' => 'form-control numberFormat',
								'pattern' => '[-]?[0-9]*[.,]?[0-9]+',
								'title' => 'Debe ingresar sólo números decimales',
								'id' => 'lineas_periodo_' . $Linea->id,
								'value' => $this->form_validation->set_value('lineas_periodo_' . $Linea->id, 0)
						),
						'estado' => 'lineas_periodo_estado_' . $Linea->id,
						'estado_opt' => $array_estado,
						'estado_opt_selected' => $this->form_validation->set_value('lineas_periodo_estado_' . $Linea->id, 'Impaga')
				);
			}
		}

		$data['consumos'] = $array_form_consumos;
		$data['lineas_periodo'] = $array_form_lineas_periodo;
		$data['periodo'] = $periodo_get;
		$data['txt_btn'] = 'Cargar';
		$data['title_view'] = "Cargar Consumos Líneas Fijas $periodo_get";
		$data['title'] = TITLE . ' - Cargar Consumos Línea Fijas';
		$this->load_template('telefonia/lineas_fijas_consumos/lineas_fijas_consumos_abm', $data);
	}
}