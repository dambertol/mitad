<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Movimientos extends MY_Controller
{

	/**
	 * Controlador de Movimientos
	 * Autor: Leandro
	 * Creado: 03/09/2019
	 * Modificado: 14/11/2019 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('telefonia/Movimientos_model');
		$this->load->model('telefonia/Equipos_model');
		$this->load->model('Areas_model');
		$this->load->model('Personal_model');
		$this->load->model('telefonia/Lineas_model');
		$this->load->model('telefonia/Comodatos_model');
		$this->grupos_permitidos = array('admin', 'telefonia_admin', 'telefonia_consulta_general');
		$this->grupos_solo_consulta = array('telefonia_consulta_general');
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$tableData = array(
				'columns' => array(
						array('label' => 'Fecha', 'data' => 'fecha', 'width' => 9, 'render' => 'datetime', 'class' => 'dt-body-right'),
						array('label' => 'Tipo', 'data' => 'tipo', 'width' => 7),
						array('label' => 'Modelo', 'data' => 'modelo', 'width' => 11),
						array('label' => 'IMEI', 'data' => 'imei', 'width' => 10, 'class' => 'dt-body-right'),
						array('label' => 'Prestador', 'data' => 'prestador', 'width' => 8),
						array('label' => 'Línea', 'data' => 'linea', 'width' => 8, 'class' => 'dt-body-right'),
						array('label' => 'Area', 'data' => 'area', 'width' => 12),
						array('label' => 'Personal', 'data' => 'persona_movimiento', 'width' => 12),
						array('label' => 'Com', 'data' => 'numero_comodato', 'width' => 3, 'class' => 'dt-body-right'),
						array('label' => 'Observaciones', 'data' => 'observaciones', 'width' => 8),
						array('label' => '', 'data' => 'comodato', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
						array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
				),
				'table_id' => 'movimientos_table',
				'source_url' => 'telefonia/movimientos/listar_data',
				'order' => array(array(0, 'desc')),
				'reuse_var' => TRUE,
				'initComplete' => 'complete_movimientos_table',
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['array_tipos'] = array('' => 'Todos', 'Alta de Equipo' => 'Alta de Equipo', 'Alta de Línea' => 'Alta de Línea', 'Baja de Equipo' => 'Baja de Equipo', 'Cambio de Equipo' => 'Cambio de Equipo', 'Cambio de Plan' => 'Cambio de Plan', 'Entrega' => 'Entrega', 'Recepción' => 'Recepción');
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de Movimientos';
		$data['title'] = TITLE . ' - Movimientos';
		$this->load_template('telefonia/movimientos/movimientos_listar', $data);
	}

	public function listar_data()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->helper('telefonia/datatables_functions_helper');
		$this->datatables
				->select("tm_movimientos.id, tm_movimientos.fecha, tm_movimientos.tipo, tm_modelos.nombre as modelo, tm_equipos.imei as imei, tm_prestadores.nombre as prestador, tm_lineas.numero as linea, CONCAT(areas.codigo, ' - ', areas.nombre) as area, COALESCE(CONCAT(tm_movimientos.labo_Codigo, COALESCE(CONCAT(' - ', personal.Apellido, ', ', personal.Nombre), '')), CONCAT('EXT: ', tm_movimientos.persona), '') as persona_movimiento, tm_comodatos.id as numero_comodato, tm_movimientos.observaciones")
				->from('tm_movimientos')
				->join('tm_equipos', 'tm_equipos.id = tm_movimientos.equipo_id', 'left')
				->join('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'left')
				->join('tm_lineas', 'tm_lineas.id = tm_movimientos.linea_id', 'left')
				->join('tm_prestadores', 'tm_prestadores.id = tm_lineas.prestador_id', 'left')
				->join('areas', 'areas.id = tm_movimientos.area_id', 'left')
				->join('personal', 'personal.Legajo = tm_movimientos.labo_Codigo', 'left')
				->join('tm_comodatos', 'tm_comodatos.movimiento_id = tm_movimientos.id', 'left')
				->add_column('comodato', '$1', 'dt_column_movimientos_comodato(tipo, numero_comodato, id)')
				->add_column('ver', '<a href="telefonia/movimientos/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('editar', '$1', 'dt_column_movimientos_editar(tipo, id)')
				->add_column('eliminar', '<a href="telefonia/movimientos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function agregar($tipo_id)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $tipo_id == NULL || !ctype_digit($tipo_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect('telefonia/movimientos/listar', 'refresh');
		}

		switch ($tipo_id)
		{
			case 1:
				$this->array_linea_control = $array_linea = $this->get_array('Lineas', 'numero', 'id', array('select' => "tm_lineas.id, tm_lineas.numero", 'where' => array(array('column' => 'estado', 'value' => 'Disponible')), 'sort_by' => 'tm_lineas.numero'), array('NULL' => '-- Sin Línea --'));
				$this->array_equipo_control = $array_equipo = $this->get_array('Equipos', 'equipo', 'id', array('select' => "tm_equipos.id, CONCAT(tm_modelos.nombre, ' - ', tm_equipos.imei) as equipo", 'join' => array(array('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'LEFT')), 'where' => array(array('column' => 'estado', 'value' => 'Disponible')), 'sort_by' => 'tm_modelos.nombre, imei'), array('NULL' => '-- Sin Equipo --'));
				$this->array_persona_control = $array_persona = $this->get_array('Personal', 'persona', 'id', array('select' => array("Legajo as id, CONCAT(Apellido, ', ', Nombre, ' (', Legajo, ')') as persona"), 'sort_by' => 'Apellido, Nombre'), array('NULL' => '-- Sin Personal --'));
				$this->array_area_control = $array_area = $this->get_array('Areas', 'area', 'id', array('select' => array('id', 'codigo', 'CONCAT(areas.codigo, \' - \', areas.nombre) as area'), 'where' => array("nombre<>'-'"), 'sort_by' => 'codigo'), array('NULL' => '-- Sin Área --'));
				$tipo = 'Entrega';
				break;
			case 2:
				$this->array_linea_control = $array_linea = $this->get_array('Lineas', 'numero', 'id', array('select' => "tm_lineas.id, tm_lineas.numero", 'where' => array(array('column' => 'estado', 'value' => 'En Uso')), 'sort_by' => 'tm_lineas.numero'), array('NULL' => '-- Sin Línea --'));
				$this->array_equipo_control = $array_equipo = $this->get_array('Equipos', 'equipo', 'id', array('select' => "tm_equipos.id, CONCAT(tm_modelos.nombre, ' - ', tm_equipos.imei) as equipo", 'join' => array(array('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'LEFT')), 'where' => array(array('column' => 'estado', 'value' => 'En Uso')), 'sort_by' => 'tm_modelos.nombre, imei'), array('NULL' => '-- Sin Equipo --'));
				$this->Movimientos_model->fields['persona'] = array('label' => 'Persona', 'readonly' => TRUE);
				$this->Movimientos_model->fields['area'] = array('label' => 'Área', 'readonly' => TRUE);
				$this->Movimientos_model->fields['persona_externa'] = array('label' => 'Persona Externa', 'readonly' => TRUE);
				$this->Movimientos_model->fields['estado_equipo'] = array('label' => 'Estado Equipo', 'maxlength' => '50');
				$tipo = 'Recepción';
				break;
			case 3:
				$this->array_linea_control = $array_linea = $this->get_array('Lineas', 'numero', 'id', array('select' => "tm_lineas.id, tm_lineas.numero", 'where' => array(array('column' => 'estado', 'value' => 'En Uso')), 'sort_by' => 'tm_lineas.numero'), array('NULL' => '-- Sin Línea --'));
				$this->Movimientos_model->fields['equipo'] = array('label' => 'Equipo', 'readonly' => TRUE);
				$this->Movimientos_model->fields['persona'] = array('label' => 'Persona', 'readonly' => TRUE);
				$this->Movimientos_model->fields['area'] = array('label' => 'Área', 'readonly' => TRUE);
				$this->Movimientos_model->fields['persona_externa'] = array('label' => 'Persona Externa', 'readonly' => TRUE);
				$this->Movimientos_model->fields['min_internacional'] = array('label' => 'Minutos Internacional', 'type' => 'integer', 'maxlength' => '6');
				$this->Movimientos_model->fields['min_nacional'] = array('label' => 'Minutos Nacional', 'type' => 'integer', 'maxlength' => '6');
				$this->Movimientos_model->fields['min_interno'] = array('label' => 'Minutos Interno', 'type' => 'integer', 'maxlength' => '6');
				$this->Movimientos_model->fields['datos'] = array('label' => 'Datos', 'type' => 'integer', 'maxlength' => '6');
				$tipo = 'Cambio de Plan';
				break;
			case 4:
				$this->array_linea_control = $array_linea = $this->get_array('Lineas', 'numero', 'id', array('select' => "tm_lineas.id, tm_lineas.numero", 'where' => array(array('column' => 'estado', 'value' => 'En Uso')), 'sort_by' => 'tm_lineas.numero'), array('NULL' => '-- Sin Línea --'));
				$this->array_equipo_control = $array_equipo = $this->get_array('Equipos', 'equipo', 'id', array('select' => "tm_equipos.id, CONCAT(tm_modelos.nombre, ' - ', tm_equipos.imei) as equipo", 'join' => array(array('tm_modelos', 'tm_modelos.id = tm_equipos.modelo_id', 'LEFT')), 'where' => array(array('column' => 'estado', 'value' => 'Disponible')), 'sort_by' => 'tm_modelos.nombre, imei'), array('NULL' => '-- Sin Equipo --'));
				$this->Movimientos_model->fields['equipo_ant'] = array('label' => 'Equipo Anterior', 'readonly' => TRUE);
				$this->Movimientos_model->fields['persona'] = array('label' => 'Persona', 'readonly' => TRUE);
				$this->Movimientos_model->fields['area'] = array('label' => 'Área', 'readonly' => TRUE);
				$this->Movimientos_model->fields['persona_externa'] = array('label' => 'Persona Externa', 'readonly' => TRUE);
				$tipo = 'Cambio de Equipo';
				break;
		}

		$this->set_model_validation_rules($this->Movimientos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			if (empty($this->input->post('equipo')) || $this->input->post('equipo') === 'NULL')
			{
				$equipo = NULL;
			}
			else
			{
				$equipo = $this->input->post('equipo');
				$equipo_db = $this->Equipos_model->get_one($equipo);

				switch ($tipo)
				{
					case 'Entrega':
						$labo_Codigo = $this->input->post('persona');
						$area = $this->input->post('area');
						$persona_ext = $this->input->post('persona_externa');
						break;
					case 'Recepción':
						$labo_Codigo = $equipo_db->labo_Codigo;
						$area = $equipo_db->area_id;
						$persona_ext = $equipo_db->persona;
						break;
					case 'Cambio de Plan':
						$labo_Codigo = $equipo_db->labo_Codigo;
						$area = $equipo_db->area_id;
						$persona_ext = $equipo_db->persona;
						break;
					case 'Cambio de Equipo':
						$labo_Codigo = $equipo_db->labo_Codigo;
						$area = $equipo_db->area_id;
						$persona_ext = $equipo_db->persona;
						break;
				}
			}

			if (empty($this->input->post('linea')) || $this->input->post('linea') === 'NULL')
			{
				$linea = NULL;
			}
			else
			{
				$linea = $this->input->post('linea');
				$linea_db = $this->Lineas_model->get_one($linea);

				switch ($tipo)
				{
					case 'Entrega':
						$labo_Codigo = $this->input->post('persona');
						$area = $this->input->post('area');
						$persona_ext = $this->input->post('persona_externa');
						$min_internacional = $linea_db->min_internacional;
						$min_nacional = $linea_db->min_nacional;
						$min_interno = $linea_db->min_interno;
						$datos = $linea_db->datos;
						break;
					case 'Recepción':
						$equipo = $linea_db->equipo_id;
						$labo_Codigo = $linea_db->labo_Codigo;
						$area = $linea_db->area_id;
						$persona_ext = $linea_db->persona;
						$min_internacional = $linea_db->min_internacional;
						$min_nacional = $linea_db->min_nacional;
						$min_interno = $linea_db->min_interno;
						$datos = $linea_db->datos;
						break;
					case 'Cambio de Plan':
						$equipo = $linea_db->equipo_id;
						$labo_Codigo = $linea_db->labo_Codigo;
						$area = $linea_db->area_id;
						$persona_ext = $linea_db->persona;
						$min_internacional = $this->input->post('min_internacional');
						$min_nacional = $this->input->post('min_nacional');
						$min_interno = $this->input->post('min_interno');
						$datos = $this->input->post('datos');
						break;
					case 'Cambio de Equipo':
						$labo_Codigo = $linea_db->labo_Codigo;
						$area = $linea_db->area_id;
						$persona_ext = $linea_db->persona;
						$min_internacional = $linea_db->min_internacional;
						$min_nacional = $linea_db->min_nacional;
						$min_interno = $linea_db->min_interno;
						$datos = $linea_db->datos;
						break;
				}
			}

			if ($tipo == 'Entrega' && empty($this->input->post('persona')) && empty($this->input->post('area')) && empty($this->input->post('persona_externa')))
			{
				$error_msg = '<br> No se puede realizar una entrega si no se ha definido un área o una persona';
			}
			else if (empty($equipo) && empty($linea))
			{
				$error_msg = '<br> No se puede realizar un movimiento sin equipo ni línea';
			}
			else
			{
				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->Movimientos_model->create(array(
						'fecha' => $this->get_datetime_sql('fecha'),
						'tipo' => $tipo,
						'equipo_id' => $equipo,
						'linea_id' => $linea,
						'estado_equipo' => $this->input->post('estado_equipo'),
						'labo_Codigo' => $labo_Codigo,
						'area_id' => $area,
						'persona' => $persona_ext,
						'min_internacional' => $min_internacional,
						'min_nacional' => $min_nacional,
						'min_interno' => $min_interno,
						'datos' => $datos,
						'observaciones' => $this->input->post('observaciones')), FALSE);

				$movimiento_id = $this->Movimientos_model->get_row_id();
				switch ($tipo)
				{
					case 'Entrega':
						if (!empty($linea))
						{
							$trans_ok &= $this->Lineas_model->update(array(
									'id' => $linea,
									'equipo_id' => $equipo,
									'labo_Codigo' => $labo_Codigo,
									'area_id' => $area,
									'persona' => $persona_ext,
									'estado' => 'En Uso'), FALSE);

							$linea_result = $this->Lineas_model->get_one($linea);
						}
						if (!empty($equipo))
						{
							$trans_ok &= $this->Equipos_model->update(array(
									'id' => $equipo,
									'labo_Codigo' => $labo_Codigo,
									'area_id' => $area,
									'persona' => $persona_ext,
									'estado' => 'En Uso'), FALSE);

							$equipo_result = $this->Equipos_model->get_one($equipo);
						}

						$trans_ok &= $this->Comodatos_model->create(array(
								'movimiento_id' => $movimiento_id,
								'marca' => !empty($equipo_result) ? $equipo_result->marca : NULL,
								'modelo' => !empty($equipo_result) ? $equipo_result->modelo : NULL,
								'imei' => !empty($equipo_result) ? $equipo_result->imei : NULL,
								'accesorios' => !empty($equipo_result) ? $equipo_result->accesorios : NULL,
								'persona_equipo' => !empty($equipo_result->nombre_personal) ? $equipo_result->nombre_personal : (!empty($equipo_result) ? $equipo_result->persona : NULL),
								'dni_persona_equipo' => !empty($equipo_result) ? $equipo_result->dni_personal : NULL,
								'area_equipo' => !empty($equipo_result) ? $equipo_result->nombre_area : NULL,
								'prestador' => !empty($linea_result) ? $linea_result->prestador : NULL,
								'numero' => !empty($linea_result) ? $linea_result->numero : NULL,
								'sim' => !empty($linea_result) ? $linea_result->numero_sim : NULL,
								'min_internacional' => !empty($linea_result) ? $linea_result->min_internacional : NULL,
								'min_nacional' => !empty($linea_result) ? $linea_result->min_nacional : NULL,
								'min_interno' => !empty($linea_result) ? $linea_result->min_interno : NULL,
								'datos' => !empty($linea_result) ? $linea_result->datos : NULL,
								'persona_linea' => !empty($linea_result->nombre_personal) ? $linea_result->nombre_personal : (!empty($linea_result) ? $linea_result->persona : NULL),
								'dni_persona_linea' => !empty($linea_result) ? $linea_result->dni_personal : NULL,
								'area_linea' => !empty($linea_result) ? $linea_result->nombre_area : NULL,
								'user_id' => $this->session->userdata('user_id'),
								'fecha_generacion' => date_format(new DateTime(), 'Y/m/d H:i'),
								'tipo' => 'Entrega',
								'observaciones' => $this->input->post('observaciones')), FALSE);
						break;
					case 'Recepción':
						if (!empty($equipo))
						{
							$equipo_result = $this->Equipos_model->get_one($equipo);
							$trans_ok &= $this->Equipos_model->update(array(
									'id' => $equipo,
									'labo_Codigo' => 'NULL',
									'area_id' => 'NULL',
									'persona' => 'NULL',
									'estado' => 'Disponible'), FALSE);
						}

						if (!empty($linea))
						{
							$linea_result = $this->Lineas_model->get_one($linea);
							$trans_ok &= $this->Lineas_model->update(array(
									'id' => $linea,
									'labo_Codigo' => 'NULL',
									'area_id' => 'NULL',
									'persona' => 'NULL',
									'equipo_id' => 'NULL',
									'estado' => 'Disponible'), FALSE);
						}

						$trans_ok &= $this->Comodatos_model->create(array(
								'movimiento_id' => $movimiento_id,
								'marca' => !empty($equipo_result) ? $equipo_result->marca : NULL,
								'modelo' => !empty($equipo_result) ? $equipo_result->modelo : NULL,
								'imei' => !empty($equipo_result) ? $equipo_result->imei : NULL,
								'accesorios' => !empty($equipo_result) ? $equipo_result->accesorios : NULL,
								'persona_equipo' => !empty($equipo_result->nombre_personal) ? $equipo_result->nombre_personal : (!empty($equipo_result) ? $equipo_result->persona : NULL),
								'dni_persona_equipo' => !empty($equipo_result) ? $equipo_result->dni_personal : NULL,
								'area_equipo' => !empty($equipo_result) ? $equipo_result->nombre_area : NULL,
								'prestador' => !empty($linea_result) ? $linea_result->prestador : NULL,
								'numero' => !empty($linea_result) ? $linea_result->numero : NULL,
								'sim' => !empty($linea_result) ? $linea_result->numero_sim : NULL,
								'min_internacional' => !empty($linea_result) ? $linea_result->min_internacional : NULL,
								'min_nacional' => !empty($linea_result) ? $linea_result->min_nacional : NULL,
								'min_interno' => !empty($linea_result) ? $linea_result->min_interno : NULL,
								'datos' => !empty($linea_result) ? $linea_result->datos : NULL,
								'persona_linea' => !empty($linea_result->nombre_personal) ? $linea_result->nombre_personal : (!empty($linea_result) ? $linea_result->persona : NULL),
								'dni_persona_linea' => !empty($linea_result) ? $linea_result->dni_personal : NULL,
								'area_linea' => !empty($linea_result) ? $linea_result->nombre_area : NULL,
								'user_id' => $this->session->userdata('user_id'),
								'fecha_generacion' => date_format(new DateTime(), 'Y/m/d H:i'),
								'tipo' => 'Recepción',
								'estado_equipo' => $this->input->post('estado_equipo'),
								'observaciones' => $this->input->post('observaciones')), FALSE);
						break;
					case 'Cambio de Plan':
						if (!empty($linea))
						{
							$trans_ok &= $this->Lineas_model->update(array(
									'id' => $linea,
									'min_internacional' => $min_internacional,
									'min_nacional' => $min_nacional,
									'min_interno' => $min_interno,
									'datos' => $datos), FALSE);
						}
						break;
					case 'Cambio de Equipo':
						$trans_ok &= $this->Lineas_model->update(array(
								'id' => $linea,
								'equipo_id' => $equipo), FALSE);

						$trans_ok &= $this->Equipos_model->update(array(
								'id' => $equipo,
								'labo_Codigo' => $labo_Codigo,
								'area_id' => $area,
								'persona' => $persona_ext,
								'estado' => 'En Uso'), FALSE);

						if (!empty($linea_db->equipo_id))
						{
							$trans_ok &= $this->Equipos_model->update(array(
									'id' => $linea_db->equipo_id,
									'estado' => 'Disponible'), FALSE);
						}
						break;
				}

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Movimientos_model->get_msg());
					redirect('telefonia/movimientos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Movimientos_model->get_error())
					{
						$error_msg .= $this->Movimientos_model->get_error();
					}
					if ($this->Equipos_model->get_error())
					{
						$error_msg .= $this->Equipos_model->get_error();
					}
					if ($this->Lineas_model->get_error())
					{
						$error_msg .= $this->Lineas_model->get_error();
					}
					if ($this->Comodatos_model->get_error())
					{
						$error_msg .= $this->Comodatos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->Movimientos_model->fields['linea']['array'] = $array_linea;
		switch ($tipo_id)
		{
			case 1:
				$this->Movimientos_model->fields['equipo']['array'] = $array_equipo;
				$this->Movimientos_model->fields['persona']['array'] = $array_persona;
				$this->Movimientos_model->fields['area']['array'] = $array_area;
				break;
			case 2:
				$this->Movimientos_model->fields['equipo']['array'] = $array_equipo;
				break;
			case 4:
				$this->Movimientos_model->fields['equipo']['array'] = $array_equipo;
				break;
		}
		$data['fields'] = $this->build_fields($this->Movimientos_model->fields);
		$data['tipo_id'] = $tipo_id;
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Movimiento';
		$data['title'] = TITLE . ' - Agregar Movimiento';
		$data['js'] = 'js/telefonia/base.js';
		$this->load_template('telefonia/movimientos/movimientos_abm', $data);
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
			redirect("telefonia/movimientos/ver/$id", 'refresh');
		}

		$this->array_equipo_control = $array_equipo = $this->get_array('Equipos');
		$this->array_area_control = $array_area = $this->get_array('Areas');
		$this->array_linea_control = $array_linea = $this->get_array('Lineas');
		$movimiento = $this->Movimientos_model->get(array('id' => $id));
		if (empty($movimiento))
		{
			show_error('No se encontró el Movimiento', 500, 'Registro no encontrado');
		}

		$this->set_model_validation_rules($this->Movimientos_model);
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
				$trans_ok &= $this->Movimientos_model->update(array(
						'id' => $this->input->post('id'),
						'equipo_id' => $this->input->post('equipo'),
						'fecha' => $this->input->post('fecha'),
						'tipo' => $this->input->post('tipo'),
						'labo_Codigo' => $this->input->post('labo_Codigo'),
						'area_id' => $this->input->post('area'),
						'linea_id' => $this->input->post('linea'),
						'min_internacional' => $this->input->post('min_internacional'),
						'min_nacional' => $this->input->post('min_nacional'),
						'min_interno' => $this->input->post('min_interno'),
						'datos' => $this->input->post('datos'),
						'observaciones' => $this->input->post('observaciones'),
						'persona' => $this->input->post('persona'),
						'estado_equipo' => $this->input->post('estado_equipo'),
						'audi_usuario' => $this->input->post('audi_usuario'),
						'audi_fecha' => $this->input->post('audi_fecha'),
						'audi_accion' => $this->input->post('audi_accion')), FALSE);
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Movimientos_model->get_msg());
					redirect('telefonia/movimientos/listar', 'refresh');
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->Movimientos_model->get_error())
					{
						$error_msg .= $this->Movimientos_model->get_error();
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Movimientos_model->fields['equipo']['array'] = $array_equipo;
		$this->Movimientos_model->fields['area']['array'] = $array_area;
		$this->Movimientos_model->fields['linea']['array'] = $array_linea;
		$data['fields'] = $this->build_fields($this->Movimientos_model->fields, $movimiento);
		$data['movimiento'] = $movimiento;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar Movimiento';
		$data['title'] = TITLE . ' - Editar Movimiento';
		$this->load_template('telefonia/movimientos/movimientos_abm', $data);
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
			redirect("telefonia/movimientos/ver/$id", 'refresh');
		}

		$movimiento = $this->Movimientos_model->get_one($id);
		if (empty($movimiento))
		{
			show_error('No se encontró el Movimiento', 500, 'Registro no encontrado');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'linea' => array('label' => 'Linea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'equipo' => array('label' => 'Equipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'persona_linea' => array('label' => 'Persona Línea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'area_linea' => array('label' => 'Area Línea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'persona_externa_linea' => array('label' => 'Persona Externa Línea', 'maxlength' => '50'),
				'min_internacional' => array('label' => 'Minutos Internacional', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'min_nacional' => array('label' => 'Minutos Nacional', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'min_interno' => array('label' => 'Minutos Interno', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'datos' => array('label' => 'Datos', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'persona_equipo' => array('label' => 'Persona Equipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'area_equipo' => array('label' => 'Area Equipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'persona_externa_equipo' => array('label' => 'Persona Externa Equipo', 'maxlength' => '50'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);

		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->Comodatos_model->delete_comodato_movimiento($this->input->post('id'));
			$trans_ok &= $this->Movimientos_model->delete(array('id' => $this->input->post('id')), FALSE);
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Movimientos_model->get_msg());
				redirect('telefonia/movimientos/listar', 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Movimientos_model->get_error())
				{
					$error_msg .= $this->Movimientos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($fake_model->fields, $movimiento, TRUE);
		$data['movimiento'] = $movimiento;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Movimiento';
		$data['title'] = TITLE . ' - Eliminar Movimiento';
		$this->load_template('telefonia/movimientos/movimientos_abm', $data);
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$movimiento = $this->Movimientos_model->get_one($id);
		if (empty($movimiento))
		{
			show_error('No se encontró el Movimiento', 500, 'Registro no encontrado');
		}

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'tipo' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'linea' => array('label' => 'Linea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'equipo' => array('label' => 'Equipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime', 'required' => TRUE),
				'persona_linea' => array('label' => 'Persona Línea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'area_linea' => array('label' => 'Area Línea', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'persona_externa_linea' => array('label' => 'Persona Externa Línea', 'maxlength' => '50'),
				'min_internacional' => array('label' => 'Minutos Internacional', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'min_nacional' => array('label' => 'Minutos Nacional', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'min_interno' => array('label' => 'Minutos Interno', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'datos' => array('label' => 'Datos', 'type' => 'integer', 'maxlength' => '6', 'readonly' => TRUE),
				'persona_equipo' => array('label' => 'Persona Equipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'area_equipo' => array('label' => 'Area Equipo', 'input_type' => 'combo', 'type' => 'bselect', 'bselect_title' => 'null'),
				'persona_externa_equipo' => array('label' => 'Persona Externa Equipo', 'maxlength' => '50'),
				'observaciones' => array('label' => 'Observaciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '99999')
		);

		$data['fields'] = $this->build_fields($fake_model->fields, $movimiento, TRUE);
		$data['movimiento'] = $movimiento;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Movimiento';
		$data['title'] = TITLE . ' - Ver Movimiento';
		$this->load_template('telefonia/movimientos/movimientos_abm', $data);
	}

	public function imprimir_comodato($movimiento_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $movimiento_id == NULL || !ctype_digit($movimiento_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$comodato = $this->Comodatos_model->get(array('movimiento_id' => $movimiento_id));
		if (empty($comodato[0]))
		{
			$this->session->set_flashdata('error', 'No existe el comodato');
			redirect("telefonia/movimientos/ver/$movimiento_id", 'refresh');
		}
		else
		{
			if ($comodato[0]->tipo === 'Entrega')
			{
				$view = 'telefonia/movimientos/movimientos_content_print_pdf';
			}
			else
			{
				$view = 'telefonia/movimientos/movimientos_content_print_pdf_r';
			}
			$data['comodato'] = $comodato[0];
			$html = $this->load->view($view, $data, TRUE);

			$mpdf = new \Mpdf\Mpdf([
					'mode' => 'c',
					'format' => 'A4',
					'margin_left' => 12,
					'margin_right' => 12,
					'margin_top' => 12,
					'margin_bottom' => 12,
					'margin_header' => 9,
					'margin_footer' => 9
			]);
			$mpdf->SetDisplayMode('fullwidth');
			$mpdf->simpleTables = true;
			$mpdf->SetTitle("Comodato");
			$mpdf->SetAuthor('Municipalidad de Luján de Cuyo');
			//$mpdf->WriteHTML($stylesheet, 1);
			$mpdf->WriteHTML($html, 2);
			$mpdf->Output("comodato_movimiento_$movimiento_id.pdf", 'I');
		}
	}
}