<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controlador de pedir_empleo 
 * Autor: Leandro
 //editado por yoel grosso
 * Creado: 10/10/2018
 * Modificado: 06/01/2020 (Leandro)
 */
class pedir_empleo  extends MY_Controller      
{
	private $cuil="";
	public function __construct()
	{
		parent::__construct();
		$this->load->model('oficina_de_empleo/pedir_empleo_model');    
		$this->load->model('personas_model');    
		$this->grupos_permitidos = array('admin','oficina_empleo_general','oficina_empleo');
		$this->grupos_solo_consulta = array('user');
		
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()   //************esta funcion fija los datos a mostrar y la opcion de busqueda, debera mostrar nombre,cuil y cv, solo si tiene cv cargado */
	{
		$tableData = array(					//esta tabla la usa el script curriculums_listar y la manda con el template para imprimir la lista en pantalla
				'columns' => array(
					array('label' => 'cuil', 'data' => 'cuil', 'width' => 10, 'class' => 'dt-body-right'),
					array('label' => 'nombre y apellido', 'data' => 'nombre', 'width' => 16, 'class' => 'dt-body-right'),
					array('label' => 'capacitacion', 'data' => 'capacitacion', 'width' => 10),
					array('label' => 'empleo', 'data' => 'busca_empleo', 'width' => 10),
					array('label' => 'email', 'data' => 'email', 'width' => 16),
			        array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 16),
					array('label' => 'Fecha nac', 'data' => 'fecha_nac', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
					array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
					array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
					array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
				'source_url' => 'oficina_de_empleo/pedir_empleo/listar_data('.$this->session->userdata('user_id').')',
				'table_id' => 'pedir_empleo_table', 
				'reuse_var' => TRUE,
				'initComplete' => "complete pedir_empleo_table", 
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de pedir_empleo'; 
		$data['title'] = TITLE . ' - pedir_empleo'; 
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_listar', $data);   
	}

	public function listar_data()			//esto lo usa el metodo de arriba para traer los datos de tabla ->para admin
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
				$this->datatables
				->select('cuil, nombre, capacitacion, busca_empleo, email, telefono, fecha_nac')
				->where("pedir_emplo.cuil=($cuil)")      //esta linea es la que cambia
				->from('pedir_empleo') 
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')  
				->add_column('eliminar', '<a href="oficina_de_empleo/pedir_empleo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');  

		echo $this->datatables->generate();
		}else{
		$this->datatables
				->select('cuil, nombre, capacitacion, busca_empleo, email, telefono, fecha_nac')
				->from('pedir_empleo') 
			//	->join('personas', 'personas.cuil = pedir_empleo.cuil', 'inner')
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'id')  
				->add_column('eliminar', '<a href="oficina_de_empleo/pedir_empleo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');  

		echo $this->datatables->generate();
		}
	}
	

	public function agregar1()    //esta funcion es del boton que me da la funcion de agregar ,verifico si es usuario o admin y decido
	{
		if (!isset($cuil)) {		//si no es ingresado por el usuario este campo estara vacio
			$agregar2=array($cuil="",true);
		}

		$this->set_model_validation_rules($this->personas_model);    //tengo que ver esto
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{

			$cuil = $this->input->post('cuil');

			//metodo inventado de yo para traer los datos de usuario
			$match = array(
				'select'=>('personas.cuil'), //"personas.cuil, personas.nombre, personas.apellido, personas.email, personas.telefono, personas.sexo, personas.fecha_nacimiento"
                'from'=>('personas'),
                'where'=>("personas.cuil=$cuil")
			);
			if(isset($match['cuil'])){
			$agregar=array($cuil,false);		//ingresado por admin y no presente en base de datos de personas
			}else{
			$agregar=array($cuil,true);			//ingresado por admin y presente en bd
			}

//
$this->load->model('oficina_de_empleo/pedir_empleo_model');  

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'desde' => array('label' => 'Fecha Desde', 'type' => 'date', 'required' => TRUE),
				'hasta' => array('label' => 'Fecha Hasta', 'type' => 'date', 'required' => TRUE)
		);

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
			$hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

			$options['select'] = array(
					"pedir_empleo.id as id", 
					'pedir_empleo.n_orden', 
					'pedir_empleo.padron', 
					'pedir_empleo.agente', 
					'pedir_empleo.n_nota', 
					'pedir_empleo.fecha', 
					'pedir_empleo.tipo', 
					'pedir_empleo.estado', 
					'pedir_empleo.inspeccion', 
					'pedir_empleo.si_no', 
					'pedir_empleo.cubierta_existente',
					'pedir_empleo.pileta_existente', 
					'pedir_empleo.cubierta_gis_existente', 
					'pedir_empleo.pileta_gis_existente', 
					'pedir_empleo.cubierta_gis_nueva', 
					'pedir_empleo.pileta_gis_nueva', 
					'pedir_empleo.cubierta_declarada', 
					'pedir_empleo.pileta_declarada', 
					'pedir_empleo.telefono_contacto', 
					'pedir_empleo.observaciones', 
			);
		
			$options['fecha >='] = $desde->format('Y-m-d');
			$hasta->add(new DateInterval('P1D'));
			$options['fecha <'] = $hasta->format('Y-m-d');

			$options['sort_by'] = 'pedir_empleo.id'; 
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->pedir_empleo_model->get($options); 

			if (!empty($print_data))
			{
				foreach ($print_data as $key => $value)
				{
					$print_data[$key][5] = date_format(new DateTime($value[5]), 'd-m-Y');
					//  $print_data[$key]['vencimiento'] = date_format(new DateTime($value['vencimiento']), 'd-m-Y');
				}

				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()
						->setCreator("SistemaMLC")
						->setLastModifiedBy("SistemaMLC")
						->setTitle("Informe de pedir_empleo Gis") 
						->setDescription("Informe de pedir_empleo Gis"); 
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
				$sheet->setTitle("Informe de pedir_empleo Gis"); 
				$sheet->getColumnDimension('A')->setWidth(14);
				$sheet->getColumnDimension('B')->setWidth(14);
				$sheet->getColumnDimension('C')->setWidth(14);
				$sheet->getColumnDimension('D')->setWidth(14);
				$sheet->getColumnDimension('E')->setWidth(14);
				$sheet->getColumnDimension('F')->setWidth(18);
				$sheet->getColumnDimension('G')->setWidth(14);
				$sheet->getColumnDimension('H')->setWidth(14);
				$sheet->getColumnDimension('I')->setWidth(14);
				$sheet->getColumnDimension('J')->setWidth(14);
				$sheet->getColumnDimension('K')->setWidth(14);
				$sheet->getColumnDimension('L')->setWidth(14);
				$sheet->getColumnDimension('M')->setWidth(14);
				$sheet->getColumnDimension('N')->setWidth(14);
				$sheet->getColumnDimension('O')->setWidth(14);
				$sheet->getColumnDimension('P')->setWidth(14);
				$sheet->getColumnDimension('Q')->setWidth(14);
				$sheet->getColumnDimension('R')->setWidth(14);
				$sheet->getColumnDimension('S')->setWidth(14);
				$sheet->getColumnDimension('T')->setWidth(100);

				$sheet->getStyle('A1:T1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array(
								'ID', 'N_Orden', 'Padron', 'Agente', 'N_Nota', 'Fecha',
								'Tipo', 'Estado', 'Inspeccion', 'Correcion Capa',
								'Cubierta Existente', 'Pileta Existente', 'Cubierta Gis Existente', 'Pileta Gis Existente',
								'Cubierta Gis Nueva', 'Pileta Gis Nueva', 'Cubierta Declarada', 'Pileta Declarada',
								'Telefono de Contacto', 'Observaciones'
						)), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter('A1:T' . $sheet->getHighestRow());

				$BStyle1 = array(
						'borders' => array(
								'left' => array(
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
								)
						)
				);
				$sheet->getStyle('U1:U' . (sizeof($print_data) + 1))->applyFromArray($BStyle1);

				$BStyle2 = array(
						'borders' => array(
								'bottom' => array(
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
								)
						)
				);
				$sheet->getStyle('A' . (sizeof($print_data) + 1) . ':T' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

				$nombreArchivo = 'Informepedir_empleo_' . date('Ymd'); 
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
				header("Cache-Control: max-age=0");

				$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			}
			else
			{
				$error_msg = '<br />Sin datos para el periodo seleccionado';
			}
		}
//
			$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

			$data['fields'] = $this->build_fields($fake_model->fields);
			$data['txt_btn'] = 'Generar';
			$data['title_view'] = 'Ingresar cuil';
			$data['title'] = TITLE . ' - Informe de Turnos';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_cuil', $data);   //tengo que crear unn  tamplate para esto*************************************
		
		};
	}

	public function agregar()    //esta funcion es del boton que me da la funcion de agregar 
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh');  //(recl@mos_gis)//(recl@mos)
		}

//		$this->array_estado_control = $this->pedir_empleo_model->get_estados(); //(recl@mos)
//		$this->array_tipo_control = $this->pedir_empleo_model->get_genero(); //(recl@mos)
//		$this->array_inspeccion_control = $this->pedir_empleo_model->get_inspeccion(); //(recl@mos)
//		$this->array_si_no_control = $this->pedir_empleo_model->get_si_no(); //(recl@mos)
//		$this->array_si_no_control = $this->pedir_empleo_model->get_si_no(); //(recl@mos)
		$this->array_capacitacion = $this->pedir_empleo_model->get_si_no();
		$this->array_trabajo = $this->pedir_empleo_model->get_si_no(); 
		$this->array_freelance = $this->pedir_empleo_model->get_si_no(); 
		$this->array_teletrabajo = $this->pedir_empleo_model->get_si_no(); 
		$this->array_viajante = $this->pedir_empleo_model->get_si_no();
		$this->array_cama_adentro = $this->pedir_empleo_model->get_si_no(); 
		$this->array_casero = $this->pedir_empleo_model->get_si_no();

		$this->set_model_validation_rules($this->pedir_empleo_model); //(recl@mos)
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{

			$n_orden = $this->get_last_id_n_orden();//esto mepa que no va
			$fecha = Date::createFromFormat('d/m/Y', $this->input->post('fecha'));
			$cuil = $this->session->userdata('id') ;
			$nombre = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');
		//	$telefono = $this->session->userdata('nombre');
			$email = $this->session->userdata('email') ;

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->pedir_empleo_model->create(array(    //(recl@mos)
					'cuil' => $cuil,
					'nombre' => $nombre,
					'telefono'=> $telefono,
					'email' => $email,
					'genero' => $this->input->post('genero'),
					'fecha_nac' => $this->input->$fecha->format('Y-m-d'),
					'domicilio' => $this->input->post('domicilio'),
					'distrito' => $this->input->post('distrito'),
					'otro_tel' => $this->input->post('otro_tel'),
					'capacitacion' => $this->input->post('capacitacion'),
					'horario_cap' => $this->input->post('horario_cap'),
					'intereses_cap' => $this->input->post('intereses_cap'),
					'busca_empleo' => $this->input->post('busca_empleo'),
					'movilidad' => $this->input->post('movilidad'),
					'discapacidad' => $this->input->post('discapacidad'),
					'cud' => $this->input->post('cud'),
					'estudios' => $this->input->post('estudios'),
					'grado' => $this->input->post('grado'),
					'idiomas' => $this->input->post('idiomas'),
					'computacion' => $this->input->post('computacion'),
					'cursos' => $this->input->post('cursos'),
					'experiencia' => $this->input->post('experiencia'),
					'interes_lab' => $this->input->post('interes_lab'),
					'disponib_lab' => $this->input->post('disponib_lab'),
					'freelance' => $this->input->post('freelance'),
					'teletrabajo' => $this->input->post('teletrabajo'),
					'viajante' => $this->input->post('viajante'),
					'cama_adentro' => $this->input->post('cama_adentro'),
					'casero' => $this->input->post('casero'),
					'aclaraciones' => $this->input->post('aclaracion'),
				), FALSE);
				
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->pedir_empleo_model->get_msg()); //(recl@mos)
				redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh'); //(recl@mos_gis) //(recl@mos)
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->pedir_empleo_model->get_error()) //(recl@mos)
				{
					$error_msg .= $this->pedir_empleo_model->get_error(); //(recl@mos)
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		//$this->pedir_empleo_model->fields[8]['array'] = $this->pedir_empleo_model->get_estados(); //(recl@mos) //(recl@mos)
		$this->pedir_empleo_model->fields['genero']['array'] = $this->pedir_empleo_model->get_genero(); //(recl@mos) //(recl@mos)
	//	$this->pedir_empleo_model->fields['email']['value'] = $this->pedir_empleo_model->get_inspeccion(); //(recl@mos) //(recl@mos)
		$this->pedir_empleo_model->fields['cuil']['value'] = $this->session->userdata('identity'); //(recl@mos) //(recl@mos)
		$this->pedir_empleo_model->fields['nombre']['value'] = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido'); //(recl@mos)
		$this->pedir_empleo_model->fields['userdata'] = $this->session->userdata(); //(recl@mos)

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields); //(recl@mos)
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data); //(recl@mos) //(recl@mos_gis) //(recl@mos)
	}

	public function agregar2($agregar)    //esta funcion es del boton que me da la funcion de agregar ,verifico si es usuario o admin y decido
	{
		if($condi===true){
	
		}
		
		$this->array_genero = $this->pedir_empleo_model->get_genero(); 
		$this->$array_si_no = $this->pedir_empleo_model->get_si_no();

		$this->set_model_validation_rules($this->pedir_empleo_model);    //tengo que ver esto
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{

		//	$n_orden = $this->get_last_id_n_orden();		//****************esto mepa que no va*******************
			$fecha = Date::createFromFormat('d/m/Y', $this->input->post('fecha'));
			$cuil = $this->session->userdata('user_id');
			$nombre = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');

			//metodo inventado de yo para traer los datos de usuario
			//if

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->pedir_empleo_model->create(array(    
					'domicilio' => $this->input->post('domicilio'),
					'distrito' => $this->input->post('distrito'),//
					'capacitacion' => $this->input->post('capacitacion'),
					'horario_cap' => $this->input->post('horario_cap'),
					'intereses_cap' => $this->input->post('intereses_cap'),
					'busca_empleo' => $this->input->post('busca_empleo'),
					'movilidad' => $this->input->post('movilidad'),
					'discapacidad' => $this->input->post('discapacidad'),
					'cud' => $this->input->post('cud'),
					'estudios' => $this->input->post('estudios'),
					'grado' => $this->input->post('grado'),
					'idiomas' => $this->input->post('idiomas'),
					'computacion' => $this->input->post('computacion'),
					'cursos' => $this->input->post('cursos'),
					'experiencia' => $this->input->post('experiencia'),
					'interes_lab' => $this->input->post('interes_lab'),
					'disponib_lab' => $this->input->post('disponib_lab'),
					'freelance' => $this->input->post('freelance'),
					'teletrabajo' => $this->input->post('teletrabajo'),
					'viajante' => $this->input->post('viajante'),
					'cama_adentro' => $this->input->post('cama_adentro'),
					'casero' => $this->input->post('casero'),
					'aclaraciones' => $this->input->post('aclaracion'),
				), FALSE);
				
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->pedir_empleo_model->get_msg()); 
				redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh');  
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->pedir_empleo_model->get_error()) 
				{
					$error_msg .= $this->pedir_empleo_model->get_error(); 
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->pedir_empleo_model->fields['si_no']['array'] = $this->pedir_empleo_model->get_si_no();  
		$this->pedir_empleo_model->fields['genero']['array'] = $this->pedir_empleo_model->get_genero();  
		$this->pedir_empleo_model->fields['email']['value'] = $this->pedir_empleo_model->get_inspeccion();  
		$this->pedir_empleo_model->fields['cuil']['value'] = $this->pedir_empleo_model->get_si_no();  
		$this->pedir_empleo_model->fields['nombre']['value'] = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido'); 
		$this->pedir_empleo_model->fields['userdata'] = $this->session->userdata(); 

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields); 
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}

	public function agregar3()    //esta funcion es del boton que me da la funcion de agregar ,verifico si es usuario o admin y decido
	{
	
		 if (isset($cuil))
	 	{
			$cuil="";
			$nombre="";
	 	};
		$this->array_genero = $this->pedir_empleo_model->get_genero(); 
		$this->$array_si_no = $this->pedir_empleo_model->get_si_no();

		$this->set_model_validation_rules($this->pedir_empleo_model);    //tengo que ver esto
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{

		//	$n_orden = $this->get_last_id_n_orden();		//****************esto mepa que no va*******************
			$fecha = Date::createFromFormat('d/m/Y', $this->input->post('fecha'));
			$cuil = $this->session->userdata('user_id');
			$nombre = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido');

			//metodo inventado de yo para traer los datos de usuario
			//if

			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->pedir_empleo_model->create(array(    
					'domicilio' => $this->input->post('domicilio'),
					'distrito' => $this->input->post('distrito'),//
					'capacitacion' => $this->input->post('capacitacion'),
					'horario_cap' => $this->input->post('horario_cap'),
					'intereses_cap' => $this->input->post('intereses_cap'),
					'busca_empleo' => $this->input->post('busca_empleo'),
					'movilidad' => $this->input->post('movilidad'),
					'discapacidad' => $this->input->post('discapacidad'),
					'cud' => $this->input->post('cud'),
					'estudios' => $this->input->post('estudios'),
					'grado' => $this->input->post('grado'),
					'idiomas' => $this->input->post('idiomas'),
					'computacion' => $this->input->post('computacion'),
					'cursos' => $this->input->post('cursos'),
					'experiencia' => $this->input->post('experiencia'),
					'interes_lab' => $this->input->post('interes_lab'),
					'disponib_lab' => $this->input->post('disponib_lab'),
					'freelance' => $this->input->post('freelance'),
					'teletrabajo' => $this->input->post('teletrabajo'),
					'viajante' => $this->input->post('viajante'),
					'cama_adentro' => $this->input->post('cama_adentro'),
					'casero' => $this->input->post('casero'),
					'aclaraciones' => $this->input->post('aclaracion'),
				), FALSE);
				
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->pedir_empleo_model->get_msg()); 
				redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh');  
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->pedir_empleo_model->get_error()) 
				{
					$error_msg .= $this->pedir_empleo_model->get_error(); 
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->pedir_empleo_model->fields['si_no']['array'] = $this->pedir_empleo_model->get_si_no();  
		$this->pedir_empleo_model->fields['genero']['array'] = $this->pedir_empleo_model->get_genero();  
		$this->pedir_empleo_model->fields['email']['value'] = $this->pedir_empleo_model->get_inspeccion();  
		$this->pedir_empleo_model->fields['cuil']['value'] = $this->pedir_empleo_model->get_si_no();  
		$this->pedir_empleo_model->fields['nombre']['value'] = $this->session->userdata('nombre') . " " . $this->session->userdata('apellido'); 
		$this->pedir_empleo_model->fields['userdata'] = $this->session->userdata(); 

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields); 
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}










	public function editar($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id)||!in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$empleo = $this->pedir_empleo_model->get(array('id' => $id)); 
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}

		$this->array_si_no_control = $this->pedir_si_no_model->get_si_no(); 
		$this->array_genero_control = $this->pedir_empleo_model->get_genero(); 

		$this->set_model_validation_rules($this->pedir_empleo_model); 
		if (isset($_POST) && !empty($_POST))
		{
			if ($id != $this->input->post('id'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$fecha = DateTime::createFromFormat('d/m/Y H:i', $this->input->post('fecha'));

				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->pedir_empleo_model->update(array( 
																															//estos campos son propios
						'genero' => $this->input->post('genero'),			//genero
						'domicilio' => $this->input->post('domicilio'),			//domicilio
						'distrito' => $this->input->post('distrito'),			//distrito
						'otro_tel' => $this->input->post('otro_tel'),    	// otro telefono
					
						'capacitacion' => $this->input->post('capacitacion'),			//capacitacion sn
						'horario_cap' => $this->input->post('horario_cap'),		//horarios disponbles		
						'intereses_cap' => $this->input->post('intereses_cap'),					//intereses    ,por rubro
						
						'busca_empleo' => $this->input->post('busca_empleo'),		//busca trabajo ,s/n
						
						'movil_tipo' => $this->input->post('movil_tipo'),		//movilidad  tipo y categoria de carnet habilitante
						'movil_carnet'	=> $this->input->post('movil_carnet'),		//movilidad  tipo y categoria de carnet habilitante
						
						'discapacidad' => $this->input->post('discapacidad'),								//discapacidad
						'cud' => $this->input->post('cud'),										//nombre del archivo de imagen
						
						'nivel_estudio' => $this->input->post('nivel_estudio'),		//nivel de estudios 
						'estudiosOt' => $this->input->post('estudioOt'),
						'grado' => $this->input->post('grado'),							//otros estudios
		
						'idiomas' => $this->input->post('idiomas'),							//idioma y nivel del 1-5
						'idiomas_niv' => $this->input->post('idiomas_niv'),							//idioma y nivel del 1-5
		
						'computacion' => $this->input->post('computacion'),				//programa y nivel del 1-5
						'compu_niv' => $this->input->post('compu_niv'),				//programa y nivel del 1-5
		
						'cursos' => $this->input->post('cursos'),				//otros cursos
						'experiencia' => $this->input->post('experiencias'),				//rubro-puesto-duracion-personal a cargo s/n
						'interes_lab' => $this->input->post('interes_lab'),								//campo rellenable
						'disponib_lab' => $this->input->post('disponib_lab'),					//combo de oppp y rotativo s/n franquero s/n
						'freelance' => $this->input->post('freelance'),		//s/n
						'teletrabajo' => $this->input->post('teletrabajo'),		//sn
						'viajante' => $this->input->post('viajante'),		//sn
						'cama_adentro' => $this->input->post('cama_adentro'),		//sn
						'casero' => $this->input->post('casero'),		//sn
						'aclaraciones' => $this->input->post('aclaraciones')
						), FALSE);

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->pedir_empleo_model->get_msg()); 
					redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh');  
				}
				else
				{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con la base de datos.';
					if ($this->pedir_empleo_model->get_error()) 
					{
						$error_msg .= $this->pedir_empleo_model->get_error(); 
					}
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->pedir_empleo_model->fields['si_no']['array'] = $this->pedir_empleo_model->get_si_no();  
		$this->pedir_empleo_model->fields['genero']['array'] = $this->pedir_empleo_model->get_genero();  

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo); 
		$data['curriculum'] = $empleo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar curriculum';
		$data['title'] = TITLE . ' - Editar curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
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
			redirect("oficina_de_empleo/pedir_empleo/ver/$id", 'refresh');  
		}

		$empleo = $this->pedir_empleo_model->get(array('id' => $id)); 
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->pedir_empleo_model->delete(array('id' => $this->input->post('id'))); 
			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->pedir_empleo_model->get_msg()); 
				redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh');  
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->pedir_empleo_model->get_error()) 
				{
					$error_msg .= $this->pedir_empleo_model->get_error(); 
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo, TRUE); 
		$data['curriculum'] = $empleo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar curriculum';
		$data['title'] = TITLE . ' - Eliminar curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}

	public function ver($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$empleo = $this->pedir_empleo_model->get(array(   
				'id' => $id,
				'join' => array(
						array(
								'type' => 'LEFT',
								'table' => 'users',
								'where' => 'users.id = pedir_empleo.audi_usuario' 
						),
						array(
								'type' => 'LEFT',
								'table' => 'personas',
								'where' => 'personas.id = users.persona_id',
								'columnas' => "CONCAT(personas.apellido, ', ', personas.nombre, ' (', personas.cuil, ')') as audi_usuario",
						)
				)
		));
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}

		$this->load->helper('audi_helper');
		$data['audi_modal'] = audi_modal($empleo);

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo, TRUE); 
		$data['curriculum'] = $empleo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver curriculum';
		$data['title'] = TITLE . ' - Ver curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}

	public function exportar()
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->load->model('oficina_de_empleo/pedir_empleo_model');  

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'desde' => array('label' => 'Fecha Desde', 'type' => 'date', 'required' => TRUE),
				'hasta' => array('label' => 'Fecha Hasta', 'type' => 'date', 'required' => TRUE)
		);

		$this->set_model_validation_rules($fake_model);
		$error_msg = NULL;
		if ($this->form_validation->run() === TRUE)
		{
			$desde = DateTime::createFromFormat('d/m/Y', $this->input->post('desde'));
			$hasta = DateTime::createFromFormat('d/m/Y', $this->input->post('hasta'));

			$options['select'] = array(
					"pedir_empleo.id as id", 
					'pedir_empleo.n_orden', 
					'pedir_empleo.padron', 
					'pedir_empleo.agente', 
					'pedir_empleo.n_nota', 
					'pedir_empleo.fecha', 
					'pedir_empleo.tipo', 
					'pedir_empleo.estado', 
					'pedir_empleo.inspeccion', 
					'pedir_empleo.si_no', 
					'pedir_empleo.cubierta_existente',
					'pedir_empleo.pileta_existente', 
					'pedir_empleo.cubierta_gis_existente', 
					'pedir_empleo.pileta_gis_existente', 
					'pedir_empleo.cubierta_gis_nueva', 
					'pedir_empleo.pileta_gis_nueva', 
					'pedir_empleo.cubierta_declarada', 
					'pedir_empleo.pileta_declarada', 
					'pedir_empleo.telefono_contacto', 
					'pedir_empleo.observaciones', 
			);
			/*
			  $where['column'] = 'pedir_empleo .vencimiento <';
			  $where['value'] = "'" . date_format(new DateTime(), 'Y/m/d') . "'";
			  $where['override'] = TRUE;
			 */
			/*
			  $where['column'] = "pedir_empleo .estado NOT IN ('Anulado', 'Asignado', 'Pendiente')";
			  $where['value'] = '';
			  $options['where'] = array($where);
			  //$options['where'][] = $where;
			 */
			$options['fecha >='] = $desde->format('Y-m-d');
			$hasta->add(new DateInterval('P1D'));
			$options['fecha <'] = $hasta->format('Y-m-d');

			$options['sort_by'] = 'pedir_empleo.id'; 
			$options['sort_direction'] = 'asc';
			$options['return_array'] = TRUE;
			$print_data = $this->pedir_empleo_model->get($options); 

			if (!empty($print_data))
			{
				foreach ($print_data as $key => $value)
				{
					$print_data[$key][5] = date_format(new DateTime($value[5]), 'd-m-Y');
					//  $print_data[$key]['vencimiento'] = date_format(new DateTime($value['vencimiento']), 'd-m-Y');
				}

				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$spreadsheet->getProperties()
						->setCreator("SistemaMLC")
						->setLastModifiedBy("SistemaMLC")
						->setTitle("Informe de pedir_empleo Gis") 
						->setDescription("Informe de pedir_empleo Gis"); 
				$spreadsheet->setActiveSheetIndex(0);

				$sheet = $spreadsheet->getActiveSheet();
				$sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
				$sheet->setTitle("Informe de pedir_empleo Gis"); 
				$sheet->getColumnDimension('A')->setWidth(14);
				$sheet->getColumnDimension('B')->setWidth(14);
				$sheet->getColumnDimension('C')->setWidth(14);
				$sheet->getColumnDimension('D')->setWidth(14);
				$sheet->getColumnDimension('E')->setWidth(14);
				$sheet->getColumnDimension('F')->setWidth(18);
				$sheet->getColumnDimension('G')->setWidth(14);
				$sheet->getColumnDimension('H')->setWidth(14);
				$sheet->getColumnDimension('I')->setWidth(14);
				$sheet->getColumnDimension('J')->setWidth(14);
				$sheet->getColumnDimension('K')->setWidth(14);
				$sheet->getColumnDimension('L')->setWidth(14);
				$sheet->getColumnDimension('M')->setWidth(14);
				$sheet->getColumnDimension('N')->setWidth(14);
				$sheet->getColumnDimension('O')->setWidth(14);
				$sheet->getColumnDimension('P')->setWidth(14);
				$sheet->getColumnDimension('Q')->setWidth(14);
				$sheet->getColumnDimension('R')->setWidth(14);
				$sheet->getColumnDimension('S')->setWidth(14);
				$sheet->getColumnDimension('T')->setWidth(100);

				$sheet->getStyle('A1:T1')->getFont()->setBold(TRUE);
				$sheet->fromArray(array(array(
								'ID', 'N_Orden', 'Padron', 'Agente', 'N_Nota', 'Fecha',
								'Tipo', 'Estado', 'Inspeccion', 'Correcion Capa',
								'Cubierta Existente', 'Pileta Existente', 'Cubierta Gis Existente', 'Pileta Gis Existente',
								'Cubierta Gis Nueva', 'Pileta Gis Nueva', 'Cubierta Declarada', 'Pileta Declarada',
								'Telefono de Contacto', 'Observaciones'
						)), NULL, 'A1');
				$sheet->fromArray($print_data, NULL, 'A2');
				$sheet->setAutoFilter('A1:T' . $sheet->getHighestRow());

				$BStyle1 = array(
						'borders' => array(
								'left' => array(
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
								)
						)
				);
				$sheet->getStyle('U1:U' . (sizeof($print_data) + 1))->applyFromArray($BStyle1);

				$BStyle2 = array(
						'borders' => array(
								'bottom' => array(
										'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM
								)
						)
				);
				$sheet->getStyle('A' . (sizeof($print_data) + 1) . ':T' . (sizeof($print_data) + 1))->applyFromArray($BStyle2);

				$nombreArchivo = 'Informepedir_empleo_' . date('Ymd'); 
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header("Content-Disposition: attachment; filename=\"$nombreArchivo.xlsx\"");
				header("Cache-Control: max-age=0");

				$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
				$writer->save('php://output');
				exit();
			}
			else
			{
				$error_msg = '<br />Sin datos para el periodo seleccionado';
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Generar';
		$data['title_view'] = 'Informe de Turnos';
		$data['title'] = TITLE . ' - Informe de Turnos';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_exportar', $data);   
	}
}