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
					array('label' => 'cuil', 'data' => 'cuil', 'width' => 10, 'class' => 'dt-body-right'),
					array('label' => 'nombre', 'data' => 'nombre', 'width' => 16, 'class' => 'dt-body-right'),
					array('label' => 'apellido', 'data' => 'apellido', 'width' => 16, 'class' => 'dt-body-right'),
					array('label' => 'capacitacion', 'data' => 'capacitacion', 'width' => 5),
					array('label' => 'empleo', 'data' => 'busca_empleo', 'width' => 5),
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
				->select('cuil, nombre, apellido, capacitacion, busca_empleo, email, telefono, fecha_nac')
				->from('oe_cv') 
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'cuil')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'cuil')  
				->add_column('eliminar', '<a href="oficina_de_empleo/pedir_empleo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'cuil');  
		echo $this->datatables->generate();
		}else{
			$identidad=$this->session->userdata('identity');
			$this->datatables
				->select('cuil, nombre, apellido, capacitacion, busca_empleo, email, telefono, fecha_nac')
				->where("oe_cv.Dni=$identidad")      //esta linea es la que cambia
				->from('oe_cv') 
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'cuil')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/editar/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'cuil')  
				->add_column('eliminar', '<a href="oficina_de_empleo/pedir_empleo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'cuil');  
		echo $this->datatables->generate();
		}
	}

	public function agregar()    															//esta funcion es del boton que me da la funcion de agregar 
	{  
			if ($_POST || !in_groups($this->grupos_permitidos,$this->grupos))				//primer condicion de saltarse primer formulario
			{	
				if (!$_POST && !in_grups($this->grupos_permitidos,$this->grupos)) 		//condicion de que sea vecino y recien inicie
				{		
					$Dni=$this->session->userdata('identity');	//modificar esto***********************
                    $cuil = $this->personas_model->get(array(
					'select'=>array('cuil'),
					'where'=>(array("personas.dni=$Dni", "personas.apellido=$this->session->userdata('apellido')")))); 
					$this->agregarC($cuil);										
				}elseif($_POST && !$this->input->post('genero')){							//condicion de admin que haya enviado primer formulario
					$cuil=($this->input->post('cuil'));
					$this->form_validation->set_rules('cuil', 'cuil', 'required|validate_cuil');
					if ($this->form_validation->run() === TRUE)
					{
						$this->agregarC($cuil);
					}else{
						echo '<script language="javascript">alert("cuil no valido");</script>';
						redirect('oficina_de_empleo/pedir_empleo/agregar', 'refresh');}
					
			}else{		//agregar validacion  del dato cuil			
				$cuil=($this->input->post('cuil'));											//condicion de recepcion de segundo formulario
				$this->agregarC($cuil);
			}
			}else{																			//si no cumple lo anterior ser envia el primer formulario de Dni
		$Dni2 = array('cuil' => array('label' => 'CUIL', 'type' => 'number', 'minlength' => '10', 'maxlength' => '11', 'required' => TRUE) );
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

	//	$this->pedir_empleo_model->fields['Dni']['value'] = $this->session->userdata('identity'); 

		$data['fields'] = $this->build_fields($Dni2); 
		$data['txt_btn'] = 'Continuar';
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_Dni', $data); 
			}
	}

	public function agregarC($cuil)    //esta funcion es del boton que me da la funcion de agregar 
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			$nombre=$this->session->userdata('nombre');
			$apellido= $this->session->userdata('apellido');
			$Dni= $this->session->userdata('identity');
		}else{
			$nombre =$this->input->post('nombre');
			$apellido= $this->input->post('apellido');
			$Dni= substr((string)$cuil,2,8);
			if(isset($cuil)){
				$cuil =$this->input->post('cuil');
				$Dni= substr((string)$cuil,2,8);
			}
		}

		$empleo = $this->pedir_empleo_model->get(array('cuil' => $cuil)); //este metodo trae el cv
		if (!isset($empleo))
		{
			$this->editar($cuil);
		}else{
			
$persona = $this->personas_model->get(array('cuil' => $cuil)); 
				if (!empty($persona)) //************con esto no estoy haciendo nada
			{
				echo '<script language="javascript">alert("persona encontrada con essssito");</script>';

				$nombre = $persona['nombre'];
				$apellido=$persona['apellido'];
				$telefono= $persona['telefono'];
				$email = $persona['email'];
				$genero = $persona['sexo'];
				$fecha_nac =$persona['fecha_nacmiento'];
			//	$domicilio = $persona['domicilio_id'];
				$otro_cel = $persona['celular'];
				$pers=true;
			}else{
				echo '<script language="javascript">alert("no está creada la persona");</script>';

				isset($nombre)? "":$nombre="";
				isset($apellido)?"":$apellido="";
				$telefono="";
				$email = "";
				$genero = "";
				$fecha_nac ="";
			//	$domicilio =";
				$otro_cel = "";
				$pers=false;
			}

		$this->array_genero_control = $this->pedir_empleo_model->get_genero(); 			//*******esto valida los combos en my_controler**********
		$this->array_estudio_control = $this->pedir_empleo_model->get_estudio();

		$this->set_model_validation_rules($this->pedir_empleo_model); 
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			$fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_nac'));
	
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$trans_ok &= $this->pedir_empleo_model->create(array( 	 //cambiar la palabra magica por update
				//con estas anotaciones creo la base de datos
				//CREATE TABLE `wi_dev`.`oe_cv`(`cuil` BIGINT(12) PRIMARY KEY,`Dni` INT(9) not null,`user_id` BIGINT(12) not null, `nombre` VARCHAR(30) NOT NULL, `apellido` VARCHAR(30) NOT NULL, `telefono` BIGINT(15) NOT NULL, `email` VARCHAR(30) NOT NULL, `genero` VARCHAR(10) NOT NULL, `fecha_nac` date, `domicilio` VARCHAR(50) NOT NULL, `distrito` VARCHAR(25) NOT NULL, `otro_cel` BIGINT(15), `capacitacion` VARCHAR(1) NOT NULL, `horario_cap` VARCHAR(30), `intereses_cap` VARCHAR(300), `busca_empleo` VARCHAR(1), `condic` VARCHAR(40), `movilidad` VARCHAR(40), `movil_carnet` VARCHAR(20), `discapacidad` VARCHAR(30), `cud` VARCHAR(30), `estudio` VARCHAR(20), `estudiosOt` VARCHAR(30),`grado` VARCHAR(30),`gradoo` VARCHAR(30), `idiomas` VARCHAR(40), `computacion` VARCHAR(60), `cursos` VARCHAR(100), `oficios` VARCHAR(60),`experiencia` VARCHAR(100), `interes_lab` VARCHAR(100), `disponib_lab` VARCHAR(40),`exmuni` CHARACTER(1),`famimuni` CHARACTER(1), `aclaraciones` varchar(300),`pdf` varchar(30),`audi_usuario` int not null ,`audi_fecha` date,`audi_accion` CHARACTER(1))ENGINE = MyISAM;
				//INSERT INTO `oe_cv` (`cuil`, `Dni`,`user_id`, `nombre`, `apellido`, `telefono`, `email`, `genero`, `fecha_nac`, `domicilio`, `distrito`, `otro_cel`, `capacitacion`, `horario_cap`, `intereses_cap`, `busca_empleo`, `condic`, `movilidad`, `movil_carnet`, `discapacidad`, `cud`, `estudio`, `estudiosOt`, `grado`, `gradoo`, `idiomas`, `computacion`, `cursos`, `oficios`, `experiencia`, `interes_lab`, `disponib_lab`, `exmuni`, `famimuni`, `aclaraciones`, `pdf`, `audi_usuario`, `audi_fecha`, `audi_accion`) VALUES ('231234567', '12345678','12000', 'munilu', 'jan', '45613456', 'munilu@ya.com', 'femenino', '2022-05-11', 'siempre viva sn', 'lujan', '45613654', 's', 'noche', 'ponele que algo, carpinteria', 'n', 'teletrabajo', 'moto', 'b1', 'lelepancha,visual', NULL, 'primario', 'tecnico elctricista', 'licenciatura', 'electricista', 'ingles 0 - frances 1', 'windouu 0 - eccel 1 - paint 3', 'no tengo idea que wea iba aca', 'pasa pelotas', 'aburrido como el diablo', 'ninguno pero en texto', 'noche, tarde', 's', 'n', NULL, NULL, '', NULL, NULL);
				//CREATE TABLE `wi_dev_aud`.`oe_cv`(`audi_id` INT AUTO_INCREMENT PRIMARY KEY,`cuil` BIGINT(12) not null,`Dni` INT(9) not null,`user_id` BIGINT(12) not null, `nombre` VARCHAR(30) NOT NULL, `apellido` VARCHAR(30) NOT NULL, `telefono` BIGINT(15) NOT NULL, `email` VARCHAR(30) NOT NULL, `genero` VARCHAR(10) NOT NULL, `fecha_nac` date, `domicilio` VARCHAR(50) NOT NULL, `distrito` VARCHAR(25) NOT NULL, `otro_cel` BIGINT(15), `capacitacion` VARCHAR(1) NOT NULL, `horario_cap` VARCHAR(30), `intereses_cap` VARCHAR(300), `busca_empleo` VARCHAR(1), `condic` VARCHAR(40), `movilidad` VARCHAR(40), `movil_carnet` VARCHAR(20), `discapacidad` VARCHAR(40), `cud` VARCHAR(30), `estudio` VARCHAR(20), `estudiosOt` VARCHAR(30), `grado` VARCHAR(30),`gradoo` VARCHAR(30), `idiomas` VARCHAR(40), `computacion` VARCHAR(60), `cursos` VARCHAR(100),`oficios` VARCHAR(60), `experiencia` VARCHAR(100), `interes_lab` VARCHAR(100), `disponib_lab` VARCHAR(40),`exmuni` CHARACTER(1),`famimuni` CHARACTER(1), `aclaraciones` varchar(300),`pdf` varchar(30),`audi_usuario` int not null ,`audi_fecha` date,`audi_accion` CHARACTER(1))ENGINE = MyISAM;
				'cuil'=> $cuil,
				'Dni' => $this->input->post('Dni'),	
				'nombre' => $nombre,//$this->input->post('nombre'),
				'apellido'=>$this->input->post('apellido'),
				'telefono'=> $this->input->post('telefono'),
				'email' => $this->input->post('email'),
				'genero' => $this->input->post('genero'),
				'fecha_nac' => $fecha->format('Y-m-d'),
				'domicilio' => $this->input->post('domicilio'),
				'distrito' => $this->input->post('distrito'),
				'otro_cel' => $this->input->post('otro_cel'),
				'capacitacion'=>(empty($this->input->post('capacitacion'))?'n':'s'),
				'horario_cap' => $this->input->post('horario_cap'),
				'intereses_cap' => $this->input->post('intereses_cap'),
				'busca_empleo' => (empty($this->input->post('busca_empleo'))?'n':'s'),
				'condic' => $this->input->post('condic'),
				'movilidad' => $this->input->post('movilidad'),
				'movil_carnet' => $this->input->post('movil_tipo'),
				'discapacidad' => $this->input->post('discapacidad'),
				'cud' => $this->input->post('cud'),
				'estudio' => $this->input->post('estudio'),
				'estudiosOt' => $this->input->post('estudiosOt'),
				'grado' => $this->input->post('grado'),
				'gradoo' => $this->input->post('gradoo'),
				'idiomas' => $this->input->post('idiomas'),
				'computacion' => $this->input->post('computacion'),
				'cursos' => $this->input->post('cursos'),
				'oficios' => $this->input->post('oficios'),
				'experiencia' => $this->input->post('experiencia'),
				'interes_lab' => $this->input->post('interes_lab'),
				'disponib_lab' => $this->input->post('disponib_lab'),
				'exmuini' => (empty($this->input->post('exmuni'))?'n':'s'),
				'famimuni' => (empty($this->input->post('famimuni'))?'n':'s'),
				'aclaraciones' => $this->input->post('aclaraciones'),
				'pdf' => $this->input->post('pdf'),
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
		$this->pedir_empleo_model->fields['telefono']['value'] =$telefono; 
		$this->pedir_empleo_model->fields['email']['value'] =$email; 
		$this->pedir_empleo_model->fields['fecha_nac']['value'] =$fecha_nac; 
	//	$this->pedir_empleo_model->fields['domicilio']['value'] =$domicilio; 
		$this->pedir_empleo_model->fields['otro_cel']['value'] =$otro_cel; 
		$this->pedir_empleo_model->fields['estudio']['array'] = $this->pedir_empleo_model->get_estudio(); 

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields);
		$data['txt_btn'] = 'agregar';
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);
	}
	}



	public function editar($cuil = NULL) //esto lo redirecciono a agregarC, es el mismo metodo
	{
	
		//if (!in_groups($this->grupos_permitidos, $this->grupos) || $dni == NULL || !ctype_digit($dni)||!in_groups($this->grupos_solo_consulta, $this->grupos))
	//	{
	//		show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		//

		$empleo = $this->pedir_empleo_model->get(array('cuil' => $cuil));
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}
		$this->array_genero_control = $this->pedir_empleo_model->get_genero(); 			//*******esto valida los combos en my_controler**********
		$this->array_estudio_control = $this->pedir_empleo_model->get_estudio();

		$this->set_model_validation_rules($this->pedir_empleo_model); 
		if (isset($_POST) && !empty($_POST))
		{
			if ($cuil != $this->input->post('cuil'))
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
				'cuil'=> $cuil,
				'Dni' => $this->input->post('Dni'),	
				'nombre' => $this->input->post('nombre'),
				'apellido'=>$this->input->post('apellido'),
				'telefono'=> $this->input->post('telefono'),
				'email' => $this->input->post('email'),
				'genero' => $this->input->post('genero'),
				'fecha_nac' => $fecha->format('Y-m-d'),
				'domicilio' => $this->input->post('domicilio'),
				'distrito' => $this->input->post('distrito'),
				'otro_cel' => $this->input->post('otro_cel'),
				'capacitacion'=>(empty($this->input->post('capacitacion'))?'n':'s'),
				'horario_cap' => $this->input->post('horario_cap'),
				'intereses_cap' => $this->input->post('intereses_cap'),
				'busca_empleo' => (empty($this->input->post('busca_empleo'))?'n':'s'),
				'condic' => $this->input->post('condic'),
				'movilidad' => $this->input->post('movilidad'),
				'movil_carnet' => $this->input->post('movil_tipo'),
				'discapacidad' => $this->input->post('discapacidad'),
				'cud' => $this->input->post('cud'),
				'estudio' => $this->input->post('estudio'),
				'estudiosOt' => $this->input->post('estudiosOt'),
				'grado' => $this->input->post('grado'),
				'gradoo' => $this->input->post('gradoo'),
				'idiomas' => $this->input->post('idiomas'),
				'computacion' => $this->input->post('computacion'),
				'cursos' => $this->input->post('cursos'),
				'oficios' => $this->input->post('oficios'),
				'experiencia' => $this->input->post('experiencia'),
				'interes_lab' => $this->input->post('interes_lab'),
				'disponib_lab' => $this->input->post('disponib_lab'),
				'exmuini' => (empty($this->input->post('exmuni'))?'n':'s'),
				'famimuni' => (empty($this->input->post('famimuni'))?'n':'s'),
				'aclaraciones' => $this->input->post('aclaraciones'),
				'pdf' => $this->input->post('pdf'),
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

		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo); 
		$data['empleo'] = $empleo;
		$data['txt_btn'] = 'Editar';
		$data['title_view'] = 'Editar curriculum';
		$data['title'] = TITLE . ' - Editar curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}
	

	public function eliminar($dni = NULL)
	{
		$empleo = $this->pedir_empleo_model->get(array('cuil' => $dni)); 
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}
		$error_msg = FALSE;
		if (isset($_POST) && !empty($_POST))
		{/*
			if ($dni != $this->input->post('cuil')) //este control de seguridad  lo puedo saltar
			{
				$id2=$this->input->post('cuil');
//$PO= foreach($_POST as $key => $value) {
//	print_r ($key . $value);
//}
				show_error('Esta solicitud no pasó el control de seguridad.'. $PO);
			}*/

			$this->db->trans_begin();
			$trans_ok = TRUE;
		$trans_ok &= $this->pedir_empleo_model->delete(array('cuil' => $dni/*$this->input->post('Dni')*/)); 
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
				'cuil' => $dni));
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
public function agregar()			//metodo agregar personas
{
	if (!in_groups($this->grupos_permitidos, $this->grupos))
	{
		show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
	}

	if (in_groups($this->grupos_solo_consulta, $this->grupos))
	{
		$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
		redirect('personas/listar', 'refresh');
	}

	$this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino');
	$this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
	$this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));

	$this->Personas_model->fields['carga_domicilio'] = array('label' => 'Domicilio', 'input_type' => 'combo', 'id_name' => 'carga_domicilio', 'type' => 'bselect', 'required' => TRUE);
	$this->array_carga_domicilio_control = $array_carga_domicilio = array('SI' => 'SI', 'NO' => 'NO');

	$this->set_model_validation_rules($this->Personas_model);
	if ($this->input->post('carga_domicilio') === 'SI')
	{
		$this->set_model_validation_rules($this->Domicilios_model);
	}
	$error_msg = FALSE;
	if ($this->form_validation->run() === TRUE)
	{
		$this->db->trans_begin();
		$trans_ok = TRUE;

		if ($this->input->post('carga_domicilio') === 'SI')
		{
			$trans_ok &= $this->Domicilios_model->create(array(
				'calle' => $this->input->post('calle'),
				'barrio' => $this->input->post('barrio'),
				'altura' => $this->input->post('altura'),
				'piso' => $this->input->post('piso'),
				'dpto' => $this->input->post('dpto'),
				'manzana' => $this->input->post('manzana'),
				'casa' => $this->input->post('casa'),
				'localidad_id' => $this->input->post('localidad')), FALSE);

			$domicilio_id = $this->Domicilios_model->get_row_id();
		}
		else
		{
			$domicilio_id = 'NULL';
		}

		$trans_ok &= $this->Personas_model->create(array(
			'dni' => $this->input->post('dni'),
			'sexo' => $this->input->post('sexo'),
			'cuil' => $this->input->post('cuil'),
			'nombre' => $this->input->post('nombre'),
			'apellido' => $this->input->post('apellido'),
			'telefono' => $this->input->post('telefono'),
			'celular' => $this->input->post('celular'),
			'email' => $this->input->post('email'),
			'fecha_nacimiento' => $this->get_date_sql('fecha_nacimiento'),
			'nacionalidad_id' => $this->input->post('nacionalidad'),
			'domicilio_id' => $domicilio_id), FALSE);

		if (SIS_ORO_ACTIVE)
		{
			// ORO CRM
			$persona_id = $this->Personas_model->get_row_id();
			if ($this->db->trans_status() && $trans_ok)
			{
				$datos['id'] = $persona_id;
				$datos['dni'] = $this->input->post('dni');
				$datos['sexo'] = $this->input->post('sexo');
				$datos['cuil'] = $this->input->post('cuil');
				$datos['nombre'] = $this->input->post('nombre');
				$datos['apellido'] = $this->input->post('apellido');
				$datos['telefono'] = $this->input->post('telefono');
				$datos['celular'] = $this->input->post('celular');
				$datos['email'] = $this->input->post('email');
				$datos['fecha_nacimiento'] = $this->get_date_sql('fecha_nacimiento');
				$datos['nacionalidad_id'] = $this->input->post('nacionalidad');
				if ($this->input->post('carga_domicilio') === 'SI')
				{
					$datos['calle'] = $this->input->post('calle');
					$datos['barrio'] = $this->input->post('barrio');
					$datos['altura'] = $this->input->post('altura');
					$datos['piso'] = $this->input->post('piso');
					$datos['dpto'] = $this->input->post('dpto');
					$datos['manzana'] = $this->input->post('manzana');
					$datos['casa'] = $this->input->post('casa');
					$datos['localidad_id'] = $this->input->post('localidad');
				}
				$datos['tags'] = 'Sistema MLC';
				$this->Oro_model->send_data($datos);
			}
		}

		if ($this->db->trans_status() && $trans_ok)
		{
			$this->db->trans_commit();
			$this->session->set_flashdata('message', $this->Personas_model->get_msg());
			redirect('personas/listar', 'refresh');
		}
		else
		{
			$this->db->trans_rollback();
			$error_msg = '<br />Se ha producido un error con la base de datos.';
			if ($this->Personas_model->get_error())
			{
				$error_msg .= $this->Personas_model->get_error();
			}
			if ($this->Domicilios_model->get_error())
			{
				$error_msg .= $this->Domicilios_model->get_error();
			}
		}
	}
	$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

	$this->Personas_model->fields['sexo']['array'] = $array_sexo;
	$this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
	$this->Personas_model->fields['carga_domicilio']['array'] = $array_carga_domicilio;
	$data['fields'] = $this->build_fields($this->Personas_model->fields);
	$this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
	$data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields);
	$data['txt_btn'] = 'Agregar';
	$data['title_view'] = 'Agregar Persona';
	$data['title'] = TITLE . ' - Agregar Persona';
	$this->load_template('personas/personas_abm', $data);
}
*/
}