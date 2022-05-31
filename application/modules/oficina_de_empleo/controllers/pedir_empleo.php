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
	public function __construct()
	{
		parent::__construct();
		$this->load->model('oficina_de_empleo/pedir_empleo_model');    
		$this->load->model('personas_model');    
		$this->grupos_permitidos = array('admin','oficina_empleo_general','oficina_empleo');
		$this->grupos_solo_consulta = array('user','tramites_online_publico');
		
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()   //************esta funcion fija los datos a mostrar y la opcion de busqueda, debera mostrar nombre,Dni y cv, solo si tiene cv cargado */
	{
		$tableData = array(					//esta tabla la usa el script curriculums_listar y la manda con el template para imprimir la lista en pantalla
				'columns' => array(
					array('label' => 'Dni', 'data' => 'Dni', 'width' => 10, 'class' => 'dt-body-right'),
					array('label' => 'nombre', 'data' => 'nombre', 'width' => 16, 'class' => 'dt-body-right'),
					array('label' => 'apellido', 'data' => 'apellido', 'width' => 16, 'class' => 'dt-body-right'),
					array('label' => 'capacitacion', 'data' => 'capacitacion', 'width' => 10),
					array('label' => 'empleo', 'data' => 'busca_empleo', 'width' => 10),
					array('label' => 'email', 'data' => 'email', 'width' => 16),
			        array('label' => 'Teléfono', 'data' => 'telefono', 'width' => 16),
					array('label' => 'Fecha nac', 'data' => 'fecha_nac', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
					array('label' => '', 'data' => 'ver', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
					array('label' => '', 'data' => 'editar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false'),
					array('label' => '', 'data' => 'eliminar', 'width' => 3, 'class' => 'dt-body-center', 'responsive_class' => 'all', 'sortable' => 'false', 'searchable' => 'false')
			),
				'source_url' => 'oficina_de_empleo/pedir_empleo/listar_data',
				'table_id' => 'pedir_empleo_table', 
				'reuse_var' => TRUE,
				'initComplete' => "complete_pedir_empleo_table", 
				'footer' => TRUE,
				'dom' => 'rt<"row"<"col-sm-6"i><"col-sm-6"p>>'
		);
		$data['html_table'] = buildHTML($tableData);
		$data['js_table'] = buildJS($tableData);
		$data['error'] = $this->session->flashdata('error');
		$data['message'] = $this->session->flashdata('message');
		$data['title_view'] = 'Listado de registros'; 
		$data['title'] = TITLE . ' - pedir_empleo'; 
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_listar', $data);   
	}

	public function listar_data()			//esto lo usa el metodo de arriba para traer los datos de tabla ->para admin
	{
		if (in_groups($this->grupos_permitidos, $this->grupos))
		{
			$this->datatables
				->select('Dni, nombre, apellido, capacitacion, busca_empleo, email, telefono, fecha_nac')
				->from('oe_cv') 
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'Dni')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'Dni')  
				->add_column('eliminar', '<a href="oficina_de_empleo/pedir_empleo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'Dni');  
		echo $this->datatables->generate();
		}else{
			$identidad=$this->session->userdata('identity');
			$this->datatables
				->select('Dni, nombre, apellido, capacitacion, busca_empleo, email, telefono, fecha_nac')
				->where("oe_cv.Dni=$identidad")      //esta linea es la que cambia
				->from('oe_cv') 
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'Dni')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'Dni')  
				->add_column('eliminar', '<a href="oficina_de_empleo/pedir_empleo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'Dni');  
		echo $this->datatables->generate();
		}
		
	}

	public function agregar()    //esta funcion es del boton que me da la funcion de agregar 
	{  
			if ($_POST || in_groups($this->grupos_solo_consulta,$this->grupos))		//primer condicion de saltarse primer formulario
			{	
				if (!$_POST && in_grups($this->grupos_solo_consulta,$this->grupos)) 		//condicion de que sea vecino y recien inicie
				{		
					$Dni=$this->session->userdata('identity');
					$this->agregarC($Dni);												//condicion de admin que haya enviado primer formulario
				}elseif($_POST && !$this->input->post('genero')){
					$Dni=($this->input->post('Dni'));
					$this->agregarC($Dni);
					//	redirect('oficina_de_empleo/pedir_empleo/agregarC', 'refresh'); 
			}else{					
				$Dni=($this->input->post('Dni'));			//condicion de recepcion de segundo formulario
				$this->agregarC($Dni);
			}
			}else{																			//si no cumple lo anterior ser envia el primer formulario de Dni
		$Dni2 = array('Dni' => array('label' => 'DNI', 'type' => 'natural', 'minlength' => '7', 'maxlength' => '8', 'required' => TRUE) );
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

	//	$this->pedir_empleo_model->fields['Dni']['value'] = $this->session->userdata('identity'); 

		$data['fields'] = $this->build_fields($Dni2); 
		$data['txt_btn'] = 'Continuar';
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_Dni', $data); 
			}
	}

	public function agregarC($Dni)    //esta funcion es del boton que me da la funcion de agregar 
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			$nombre=$this->session->userdata('nombre');
			$apellido= $this->session->userdata('apellido');
		}else{
			$nombre =$this->input->post('nombre');
			$apellido= $this->input->post('apellido');
			if(isset($Dni)){
				$Dni =$this->input->post('Dni');
			}
		}
		$modoCarga='create';
		$boton="Agregar";
		
		$empleo = $this->pedir_empleo_model->get(array('Dni' => $Dni)); //este metodo trae el cv
		if (isset($empleo)||$empleo==null)
		{
			$persona = $this->personas_model->get(array('Dni' => $Dni)); 
				if (!empty($persona)) //************con esto no estoy haciendo nada
			{
				$persona="";
			}else{
				$persona = $this->pedir_persona_model->get(array('Dni' => $Dni)); //este metodo trae el cv
			}
		}else{
			$modoCarga='update';
			$boton="Actualizar";
		}



		$this->array_genero_control = $this->pedir_empleo_model->get_genero(); 			//*******esto valida los combos en my_controler**********
		$this->array_estudio_control = $this->pedir_empleo_model->get_estudio();
		
		$this->array_capacitacion_control = $this->pedir_empleo_model->get_si_no();		
		$this->array_busca_empleo_control = $this->pedir_empleo_model->get_si_no(); 	 
		$this->array_freelance_control = $this->pedir_empleo_model->get_si_no(); 
		$this->array_teletrabajo_control = $this->pedir_empleo_model->get_si_no(); 
		$this->array_viajante_control = $this->pedir_empleo_model->get_si_no();
		$this->array_cama_adentro_control = $this->pedir_empleo_model->get_si_no(); 
		$this->array_casero_control = $this->pedir_empleo_model->get_si_no();
	//	$this->array_exmuni_control = $this->pedir_empleo_model->get_si_no(); 
	//	$this->array_famimuni_control = $this->pedir_empleo_model->get_si_no();

		$this->set_model_validation_rules($this->pedir_empleo_model); 
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_nac'));

	
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->pedir_empleo_model->$modoCarga(array( 	 //cambiar la palabra magica por update
				//con estas anotaciones creo la base de datos
				//CREATE TABLE `wi_dev`.`oe_cv`(`Dni` INT(11) PRIMARY KEY, `nombre` VARCHAR(30) NOT NULL, `apellido` VARCHAR(30) NOT NULL, `telefono` VARCHAR(30) NOT NULL, `email` VARCHAR(30) NOT NULL, `genero` VARCHAR(30) NOT NULL, `fecha_nac` date, `domicilio` VARCHAR(30) NOT NULL, `distrito` VARCHAR(30) NOT NULL, `otro_cel` int(15), `capacitacion` VARCHAR(30) NOT NULL, `horario_cap` VARCHAR(30), `intereses_cap` VARCHAR(300), `busca_empleo` VARCHAR(30), `movilidad` VARCHAR(30), `movil_carnet` VARCHAR(30), `discapacidad` VARCHAR(30), `cud` VARCHAR(30), `estudio` VARCHAR(30), `estudiosOt` VARCHAR(30),`grado` VARCHAR(30), `idiomas` VARCHAR(30), `computacion` VARCHAR(30), `cursos` VARCHAR(30), `experiencia` VARCHAR(30), `interes_lab` VARCHAR(30), `disponib_lab` VARCHAR(30), `freelance` varchar(30), `teletrabajo` varchar(30), `viajante` varchar(30), `cama_adentro` varchar(30), `casero` varchar(30), `aclaraciones` varchar(300),`cuil` int(11),`audi_usuario` int not null ,`audi_fecha` date,`audi_accion` CHARACTER(1))ENGINE = MyISAM;
				//INSERT INTO `oe_cv` (`Dni`, `nombre`, `apellido`, `telefono`, `email`, `genero`, `fecha_nac`, `domicilio`, `distrito`, `otro_cel`, `capacitacion`, `horario_cap`, `intereses_cap`, `busca_empleo`, `movilidad`, `movil_carnet`, `discapacidad`, `cud`, `estudio`, `estudiosOt`, `grado`, `idiomas`, `computacion`, `cursos`, `experiencia`, `interes_lab`, `disponib_lab`, `freelance`, `teletrabajo`, `viajante`, `cama_adentro`, `casero`, `aclaraciones`, `cuil`, `audi_usuario`, `audi_fecha`, `audi_accion`) VALUES ('12345678', 'munilu', 'jan', '45613000', 'munilu@ya.com', 'femenino', '2022-05-09', 'siempre viva sn', 'lujan', '40013654', '0', 'noche', 'ACA VA ALGO\r\n\r\n', '0', 'moto', 'B1', 'lelepancha', NULL, 'una banda', 'otro mas', 'secundario', 'español', 'windouu', 'cartomancia', '100 años de existencia', 'no tengo', 'full', '0', '0', '0', '0', '0', 'el mejor municipio', '45613278', '', NULL, NULL);
				//CREATE TABLE `wi_dev_aud`.`oe_cv`(`audi_id` INT AUTO_INCREMENT PRIMARY KEY,`Dni` INT(11) not null, `nombre` VARCHAR(30) NOT NULL, `apellido` VARCHAR(30) NOT NULL, `telefono` VARCHAR(30) NOT NULL, `email` VARCHAR(30) NOT NULL, `genero` VARCHAR(30) NOT NULL, `fecha_nac` date, `domicilio` VARCHAR(30) NOT NULL, `distrito` VARCHAR(30) NOT NULL, `otro_cel` int(15), `capacitacion` VARCHAR(30) NOT NULL, `horario_cap` VARCHAR(30), `intereses_cap` VARCHAR(300), `busca_empleo` VARCHAR(30), `movilidad` VARCHAR(30), `movil_carnet` VARCHAR(30), `discapacidad` VARCHAR(30), `cud` VARCHAR(30), `estudio` VARCHAR(30), `estudiosOt` VARCHAR(30), `grado` VARCHAR(30), `idiomas` VARCHAR(30), `computacion` VARCHAR(30), `cursos` VARCHAR(30), `experiencia` VARCHAR(30), `interes_lab` VARCHAR(30), `disponib_lab` VARCHAR(30), `freelance` varchar(30), `teletrabajo` varchar(30), `viajante` varchar(30), `cama_adentro` varchar(30), `casero` varchar(30), `aclaraciones` varchar(300),`cuil` int(11),`audi_usuario` int not null ,`audi_fecha` date,`audi_accion` CHARACTER(1))ENGINE = MyISAM;
					'Dni' => $Dni,
					'nombre' => $this->input->post('nombre'),
					'apellido'=>$this->input->post('apellido'),
					'telefono'=> $this->input->post('telefono'),
					'email' => $this->input->post('email'),
					'genero' => $this->input->post('genero'),
					'fecha_nac' => $fecha->format('Y-m-d'),
					'domicilio' => $this->input->post('domicilio'),
					'distrito' => $this->input->post('distrito'),
					'otro_cel' => $this->input->post('otro_cel'),
					'capacitacion' => $this->input->post('capacitacion'),
					'horario_cap' => $this->input->post('horario_cap'),
					'intereses_cap' => $this->input->post('intereses_cap'),
					'busca_empleo' => $this->input->post('busca_empleo'),
					'movilidad' => $this->input->post('movilidad'),		//a este lo tengo que cambiar
					'movil_carnet' => $this->input->post('movil_tipo'),
					'discapacidad' => $this->input->post('discapacidad'),
					'cud' => $this->input->post('cud'),
					'estudio' => $this->input->post('estudio'),
					'estudiosOt' => $this->input->post('estudiosOt'),
					'grado' => $this->input->post('grado'),
					'idiomas' => $this->input->post('idiomas'),
					//'idiomas_niv' => $this->input->post('idiomas_niv'),
					'computacion' => $this->input->post('computacion'),
					//'compu_niv' => $this->input->post('compu_niv'),
					'cursos' => $this->input->post('cursos'),
					'experiencia' => $this->input->post('experiencia'),
					'interes_lab' => $this->input->post('interes_lab'),
					'disponib_lab' => $this->input->post('disponib_lab'),
					'freelance' => $this->input->post('freelance'),
					'teletrabajo' => $this->input->post('teletrabajo'),
					'viajante' => $this->input->post('viajante'),
					'cama_adentro' => $this->input->post('cama_adentro'),
					'casero' => $this->input->post('casero'),
				//	'exmuini' => $this->input->post('exmuni'),
				//	'famimuni' => $this->input->post('famimuni'),
					'aclaraciones' => $this->input->post('aclaraciones'),
				//	'pdf' => $this->input->post('pdf'),

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
				$error_msg = '<br />Se ha producido un error con laaaaa base de datos.'; // me esta saltando este error
				if ($this->pedir_empleo_model->get_error()) 
				{
					$error_msg .= $this->pedir_empleo_model->get_error(); 
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		$this->pedir_empleo_model->fields['genero']['array'] = $this->pedir_empleo_model->get_genero();
		$this->pedir_empleo_model->fields['Dni']['value'] = $Dni; 
		$this->pedir_empleo_model->fields['nombre']['value'] =$nombre; 
		$this->pedir_empleo_model->fields['apellido']['value'] =$apellido; 
		$this->pedir_empleo_model->fields['estudio']['array'] = $this->pedir_empleo_model->get_estudio(); 
		$this->pedir_empleo_model->fields['capacitacion']['array'] = $this->pedir_empleo_model->get_si_no();
		$this->pedir_empleo_model->fields['busca_empleo']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['freelance']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['teletrabajo']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['viajante']['array'] = $this->pedir_empleo_model->get_si_no();
		$this->pedir_empleo_model->fields['cama_adentro']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['casero']['array'] = $this->pedir_empleo_model->get_si_no();
	//	$this->pedir_empleo_model->fields['exmuni']['array'] = $this->pedir_empleo_model->get_si_no(); 
		//$this->pedir_empleo_model->fields['famimuni']['array'] = $this->pedir_empleo_model->get_si_no();

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields);
		$boton=='actualizar'?$data['empleo'] = $empleo:'';
		$data['txt_btn'] = $boton;
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);
	}


	public function editar($dni = NULL) //esto lo redirecciono a agregarC, es el mismo metodo
	{
		/*if (!in_groups($this->grupos_permitidos, $this->grupos) || $dni == NULL || !ctype_digit($dni)||!in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}*/

		$empleo = $this->pedir_empleo_model->get(array('Dni' => $dni)); 
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}
		$this->array_genero_control = $this->pedir_empleo_model->get_genero(); 			//*******esto valida los combos en my_controler**********
		$this->array_estudio_control = $this->pedir_empleo_model->get_estudio();

		$this->array_capacitacion_control = $this->pedir_empleo_model->get_si_no();		
		$this->array_busca_empleo_control = $this->pedir_empleo_model->get_si_no(); 
		$this->array_freelance_control = $this->pedir_empleo_model->get_si_no(); 
		$this->array_teletrabajo_control = $this->pedir_empleo_model->get_si_no(); 
		$this->array_viajante_control = $this->pedir_empleo_model->get_si_no();
		$this->array_cama_adentro_control = $this->pedir_empleo_model->get_si_no(); 
		$this->array_casero_control = $this->pedir_empleo_model->get_si_no();
	//	$this->array_exmuni_control = $this->pedir_empleo_model->get_si_no(); 
	//	$this->array_famimuni_control = $this->pedir_empleo_model->get_si_no();


		$this->set_model_validation_rules($this->pedir_empleo_model); 
		if (isset($_POST) && !empty($_POST))
		{
			if ($dni != $this->input->post('Dni'))
			{
				show_error('Esta solicitud no pasó el control de seguridad.');
			}

			$error_msg = FALSE;
			if ($this->form_validation->run() === TRUE)
			{
				$fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_nac'));

				$this->db->trans_begin();
				$trans_ok = TRUE;
				$trans_ok &= $this->pedir_empleo_model->update(array( 
																															//estos campos son propios
					'Dni' => $dni,			
					'nombre' => $this->input->post('nombre'),
					'apellido'=>$this->input->post('apellido'),
					'telefono'=> $this->input->post('telefono'),
					'email' => $this->input->post('email'),
					'genero' => $this->input->post('genero'),
					'fecha_nac' => $fecha->format('Y-m-d'),
					'domicilio' => $this->input->post('domicilio'),
					'distrito' => $this->input->post('distrito'),
					'otro_cel' => $this->input->post('otro_cel'),
					'capacitacion' => $this->input->post('capacitacion'),
					'horario_cap' => $this->input->post('horario_cap'),
					'intereses_cap' => $this->input->post('intereses_cap'),
					'busca_empleo' => $this->input->post('busca_empleo'),
					'movilidad' => $this->input->post('movilidad'),//a este lo tengo que cambiar
					'movil_carnet' => $this->input->post('movil_tipo'),
					'discapacidad' => $this->input->post('discapacidad'),
					'cud' => $this->input->post('cud'),
					'estudio' => $this->input->post('estudio'),
					'estudiosOt' => $this->input->post('estudiosOt'),
					'grado' => $this->input->post('grado'),
					'idiomas' => $this->input->post('idiomas'),
					//'idiomas_niv' => $this->input->post('idiomas_niv'),
					'computacion' => $this->input->post('computacion'),
					//'compu_niv' => $this->input->post('compu_niv'),
					'cursos' => $this->input->post('cursos'),
					'experiencia' => $this->input->post('experiencia'),
					'interes_lab' => $this->input->post('interes_lab'),
					'disponib_lab' => $this->input->post('disponib_lab'),
					'freelance' => $this->input->post('freelance'),
					'teletrabajo' => $this->input->post('teletrabajo'),
					'viajante' => $this->input->post('viajante'),
					'cama_adentro' => $this->input->post('cama_adentro'),
					'casero' => $this->input->post('casero'),
				//	'exmuni' => $this->input->post('exmuni'),
				//	'famimuni' => $this->input->post('famimuni'),
					'aclaraciones' => $this->input->post('aclaracion'),
					//'pdf' => $this->input->post('pdf'),  //tengo 	ue hace r el manejo de esta porqueria

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

		$this->pedir_empleo_model->fields['genero']['array'] = $this->pedir_empleo_model->get_genero(); 
		$this->pedir_empleo_model->fields['estudio']['array'] = $this->pedir_empleo_model->get_estudio(); 
		$this->pedir_empleo_model->fields['capacitacion']['array'] = $this->pedir_empleo_model->get_si_no();
		$this->pedir_empleo_model->fields['busca_empleo']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['freelance']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['teletrabajo']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['viajante']['array'] = $this->pedir_empleo_model->get_si_no();
		$this->pedir_empleo_model->fields['cama_adentro']['array'] = $this->pedir_empleo_model->get_si_no(); 
		$this->pedir_empleo_model->fields['casero']['array'] = $this->pedir_empleo_model->get_si_no();
	//	$this->pedir_empleo_model->fields['exmuni']['array'] = $this->pedir_empleo_model->get_si_no(); 
	//	$this->pedir_empleo_model->fields['famimuni']['array'] = $this->pedir_empleo_model->get_si_no();


		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo); 
		$data['empleo'] = $empleo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar curriculum';
		$data['title'] = TITLE . ' - Editar curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}

	public function eliminar($dni = NULL)
	{
		$empleo = $this->pedir_empleo_model->get(array('Dni' => $dni)); 
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}
		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{
			if ($dni != $this->input->post('Dni')) //este control de seguridad  lo puedo saltar
			{
				$id2=$this->input->post('Dni');
//$PO= foreach($_POST as $key => $value) {
//	print_r ($key . $value);
//}
				show_error('Esta solicitud no pasó el control de seguridad.'. $PO);
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;
		$trans_ok &= $this->pedir_empleo_model->delete(array('Dni' => $dni/*$this->input->post('Dni')*/)); 
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
		$data['empleo'] = $empleo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar curriculum';
		$data['title'] = TITLE . ' - Eliminar curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}

	public function ver($dni = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $dni == NULL || !ctype_digit($dni))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
		$empleo = $this->pedir_empleo_model->get(array(   
				'Dni' => $dni));
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}

		$this->load->helper('audi_helper');
		$data['audi_modal'] = audi_modal($empleo);

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo, TRUE); 
		$data['empleo'] = $empleo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver curriculum';
		$data['title'] = TITLE . ' - Ver curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}
/*
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
			/*
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

	public function set_Dni($Dni)
	{
		$this->Dni = $Dni;
	//	return $this;
	}

	public function get_Dni()
	{
		return $this->Dni;
	}
*/
	
}