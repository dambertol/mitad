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
		$this->load->model('Personas_model');    
		$this->load->model('Nacionalidades_model');
        $this->load->model('Domicilios_model');
        $this->load->model('Localidades_model');
		$this->load->model('Auth0_model');
        $this->load->model('Oro_model');
		$this->grupos_permitidos = array('admin','oficina_empleo_general','oficina_empleo');
		$this->grupos_solo_consulta = array('user','tramites_online_publico');
		
		// Inicializaciones necesarias colocar acá.
	}

	public function listar()   //************esta funcion fija los datos a mostrar y la opcion de busqueda, debera mostrar nombre,Dni y cv, solo si tiene cv cargado */
	{
		$tableData = array(					//esta tabla la usa el script curriculums_listar y la manda con el template para imprimir la lista en pantalla
				'columns' => array(
					array('label' => 'Cuil', 'data' => 'cuil', 'width' => 10, 'class' => 'dt-body-right'),
					array('label' => 'Nombre', 'data' => 'nombre', 'width' => 16, 'class' => 'dt-body-right'),
					array('label' => 'Apellido', 'data' => 'apellido', 'width' => 16, 'class' => 'dt-body-right'),
					array('label' => 'Capacitacion', 'data' => 'capacitacion', 'width' => 5),
					array('label' => 'Empleo', 'data' => 'busca_empleo', 'width' => 5),
					array('label' => 'Email', 'data' => 'email', 'width' => 16),
			        array('label' => 'Teléfono', 'data' => 'celular', 'width' => 16),
					array('label' => 'Nacimiento', 'data' => 'fecha_nacimiento', 'width' => 10, 'render' => 'date', 'class' => 'dt-body-right'),
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
				->select('oe_cv.cuil, personas.nombre, personas.apellido, oe_cv.capacitacion, oe_cv.busca_empleo, personas.email, oe_cv.celular , personas.fecha_nacimiento, oe_cv.persona_id')
				->from('oe_cv')
				->join('personas', 'personas.id = oe_cv.persona_id', 'left')
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'cuil')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/agregarC/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'cuil')  
				->add_column('eliminar', '<a href="oficina_de_empleo/pedir_empleo/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'cuil');  
		echo $this->datatables->generate();
		}else{
			$identidad=$this->session->userdata('identity');
			$this->datatables
				->select('oe_cv.cuil, personas.nombre, personas.apellido, oe_cv.capacitacion, oe_cv.busca_empleo, personas.email, oe_cv.celular , personas.fecha_nacimiento')
				->from('oe_cv')
				->join('personas', 'personas.cuil = oe_cv.cuil', 'left')
				->where("oe_cv.Dni=$identidad")      //esta linea es la que cambia
				->add_column('ver', '<a href="oficina_de_empleo/pedir_empleo/ver/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'cuil')  
				->add_column('editar', '<a href="oficina_de_empleo/pedir_empleo/agregarC/$1" title="Editar" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i></a>', 'cuil')  
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
                    $cuil = $this->Personas_model->get(array(
					'select'=>array('cuil'),
					'where'=>(array("personas.dni=$Dni", "personas.apellido=$this->session->userdata('apellido')")))); 
					if(!empty($cuil))
					{
					$this->agregarC($cuil);
				}									
				}elseif($_POST && !$this->input->post('sexo')){							//condicion de admin que haya enviado primer formulario
					$cuil=($this->input->post('cuil'));
					$this->form_validation->set_rules('cuil', 'cuil', 'required|validate_cuil');
					if ($this->form_validation->run() === TRUE)
					{
						$this->agregarC($cuil);
					}else{
						echo '<script language="javascript">alert("cuil no valido");</script>';
						redirect('oficina_de_empleo/pedir_empleo/agregar', 'refresh');
					}
					
				}elseif($_POST && $this->input->post('sexo')){		//agregar validacion  del dato cuil			
				$cuil=($this->input->post('cuil'));											//condicion de recepcion de segundo formulario
				$this->agregarC($cuil);
				}
			}else{																			//si no cumple lo anterior ser envia el primer formulario de Dni
		$Dni2 = array('cuil' => array('label' => 'CUIL', 'type' => 'number', 'minlength' => '10', 'maxlength' => '11', 'required' => TRUE) );
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$data['fields'] = $this->build_fields($Dni2); 
		$data['txt_btn'] = 'Continuar';
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_Dni', $data); 
			}
	}

	public function agregarC($cuil)    //esta funcion es del boton que me da la funcion de agregar 
	{
		if (empty($cuil)){
			echo '<script language="javascript">alert("algo raro ocurrio");</script>';
			redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh'); 
		}

		$foraneo=false;

		$empleo = $this->pedir_empleo_model->get(array('cuil' => $cuil)); 	//este metodo trae el cv
		if (!empty($empleo))												//existe curriculum
		{
			$curri=true;
		}else{
			$curri=false;
		}
		$person = $this->Personas_model->get(array('cuil' => $cuil)); 
			//este metodo lo tengo para poder traer los datos asociados a persona que solo se trean
		//	!empty($person)?:"";
		if (!empty($person)) 										//existe la persona
		{
			$persona_id=get_object_vars($person[0])['id'];
			$persona=$this->Personas_model->get_one($persona_id);	
			echo '<script language="javascript">alert("persona encontrada con exito'.get_object_vars($person[0])['id'].'");</script>';
			$pers=true;
		}else{ 															//no existe la persona
						 //metodo de creacion / no edicion
			$persona="";
		//	$persona_id="";
			echo '<script language="javascript">alert("no está creada la persona");</script>';
				if (!in_groups($this->grupos_permitidos, $this->grupos))	//es un usuario de otra plataforma sin crear persona
				{
					$nombre=$this->session->userdata('nombre');
					$apellido= $this->session->userdata('apellido');
					$Dni= $this->session->userdata('identity');
					if(!str_contains($cuil,$Dni)){
						echo '<script language="javascript">alert("el cuil no coincide");</script>';
						redirect('oficina_de_empleo/pedir_empleo/listar', 'refresh'); 
					}
					$foraneo=true;
				}
				$pers=false;
			}
			if (!empty($persona->domicilio_id)) 							//existe domicilio
			{
			//	$person[0]->carga_domicilio = 'SI';
				$domi=true;
			}else{ 															//no existe domicilio
			//	$person[0]->carga_domicilio = 'SI';	
				$domi=false;
			}

		$this->array_sexo_control = $array_sexo = array('Femenino' => 'Femenino', 'Masculino' => 'Masculino', 'No bin'=>'no binario', 'hom trs'=>'hombre trans', 'muj trs'=>'mujer trans', 'Desconocido'=>'otro'); 			//*******esto valida los combos en my_controler**********
		$this->array_estudio_control = $this->pedir_empleo_model->get_estudio();
		$this->array_nacionalidad_control = $array_nacionalidad = $this->get_array('Nacionalidades', 'nombre');
		$this->array_localidad_control = $array_localidad = $this->get_array('Localidades', 'localidad', 'id', array('select' => "localidades.id, CONCAT(localidades.nombre, ' - ', departamentos.nombre, ' - ', provincias.nombre) as localidad", 'join' => array(array('departamentos', 'departamentos.id = localidades.departamento_id', 'LEFT'), array('provincias', 'provincias.id = departamentos.provincia_id', 'LEFT')), 'sort_by' => 'localidades.nombre, departamentos.nombre, provincias.nombre'));
		$sexo=in_array($this->input->post('sexo'),$array_sexo)?$this->input->post('sexo'):'Desconocido';
		$this->set_model_validation_rules($this->Personas_model);
		$this->set_model_validation_rules($this->Domicilios_model);
		$this->set_model_validation_rules($this->pedir_empleo_model); 

$error_msg = FALSE;
if ($this->form_validation->run() === TRUE)
{
	if ($pers == false || in_groups($this->grupos_permitidos,$this->grupos))
	{
//	$error_msg = FALSE;
		$this->db->trans_begin();
		$trans_ok = TRUE;
		$domic=($domi?'update':'create');
		$var1=($domi?($persona->domicilio_id):'');
		$trans_ok &= $this->Domicilios_model->$domic(array(
			'id'=>$var1,
			'calle' => $this->input->post('calle'),
			'barrio' => $this->input->post('barrio'),
			'altura' => $this->input->post('altura'),
			'piso' => $this->input->post('piso'),
			'dpto' => $this->input->post('dpto'),
			'manzana' => $this->input->post('manzana'),
			'casa' => $this->input->post('casa'),
			'localidad_id' => $this->input->post('localidad')), FALSE);
			$domi?($domicilio_id = $persona->domicilio_id):($domicilio_id = $this->Domicilios_model->get_row_id());

			$pe=($pers?'update':'create');
			$var2=($pers?($persona_id):'');

		$trans_ok &= $this->Personas_model->$pe(array(
			'id'=>$var2,
			'dni' => $this->input->post('dni'),
			'sexo' => $sexo,
			'cuil' => $this->input->post('cuil'),
			'nombre' => $this->input->post('nombre'),
			'apellido' => $this->input->post('apellido'),
			'telefono' => $this->input->post('telefono'),
			'celular' => $this->input->post('celular'),
			'email' => $this->input->post('email'),
			'fecha_nacimiento' => $this->get_date_sql('fecha_nacimiento'),
			'nacionalidad_id' => $this->input->post('nacionalidad'),
			'domicilio_id' => $domicilio_id), FALSE);
			
			isset($persona_id)?$persona_id:($persona_id = $this->Personas_model->get_row_id());

					if (SIS_ORO_ACTIVE)
				{
					// ORO CRM
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

						$datos['calle'] = $this->input->post('calle');
						$datos['barrio'] = $this->input->post('barrio');
						$datos['altura'] = $this->input->post('altura');
						$datos['piso'] = $this->input->post('piso');
						$datos['dpto'] = $this->input->post('dpto');
						$datos['manzana'] = $this->input->post('manzana');
						$datos['casa'] = $this->input->post('casa');
						$datos['localidad_id'] = $this->input->post('localidad');	
						$datos['tags'] = 'Sistema MLC';
						$this->Oro_model->send_data($datos);
					}
				}

                if ($pers==true && $persona_id !== 0) //Persona con usuario
                {
                    if (SIS_AUTH_MODE === 'auth0')
                    {
                        // AUTH0
                        if ($this->db->trans_status() && $trans_ok)
                        {
                            $data['nombre'] = $this->input->post('nombre');
                            $data['apellido'] = $this->input->post('apellido');
                            $data['email'] = $this->input->post('email');
                            $trans_ok = $this->Auth0_model->update_user($persona_id, $data);
                        }
                    }
                }
				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$this->session->set_flashdata('message', $this->Personas_model->get_msg());
				}else{
					$this->db->trans_rollback();
					$error_msg = '<br />Se ha producido un error con estaaa la  '.$persona_id.' base de datos.';
					if ($this->Personas_model->get_error())
					{
						echo '<script language="javascript">alert("persona error");</script>';

						$error_msg .= $this->Personas_model->get_error();
					}
					if ($this->Domicilios_model->get_error())
					{
						echo '<script language="javascript">alert("domicilio error");</script>';

						$error_msg .= $this->Domicilios_model->get_error();
					}
					if ($pers==true && $this->Auth0_model->errors())
                    {
						echo '<script language="javascript">alert("auto error");</script>';

                        $error_msg .= $this->Auth0_model->errors();
                    }
				}
			
			}
			$fecha = DateTime::createFromFormat('d/m/Y', $this->input->post('fecha_nac'));
			$this->db->trans_begin();
			$trans_ok = TRUE;
			$curr=$curri?'update':'create';
			$trans_ok &= $this->pedir_empleo_model->$curr(array( 	 //cambiar la palabra magica por update
				'cuil'=> $cuil,
				'persona_id'=>$persona_id,
				'sexo' => $this->input->post('sexo'),
				'celular' => $this->input->post('celular'),
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

	//	$this->Personas_model->fields['celular']['value'] =$celular; 
		$this->pedir_empleo_model->fields['estudio']['array'] = $this->pedir_empleo_model->get_estudio(); 
		$this->Personas_model->fields['sexo']['array'] = $array_sexo;
		$this->Personas_model->fields['nacionalidad']['array'] = $array_nacionalidad;
		$this->Domicilios_model->fields['localidad']['array'] = $array_localidad;
		$foraneo?$this->Personas_model->fields['dni']['value'] = $Dni:"";
		$foraneo?$this->Personas_model->fields['nombre']['value'] = $nombre:"";
		$foraneo?$this->Personas_model->fields['apellido']['value'] = $apellido:"";
		$pers?($persona->sexo = $empleo->sexo):"";

		$pers?($data['field'] = $this->build_fields($this->Personas_model->fields,$persona)):($data['field'] = $this->build_fields($this->Personas_model->fields));
        $domi?($data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $persona)):($data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields));
		$pers?($data['persona'] = $persona):"";
		$curri?($data['fields'] = $this->build_fields($this->pedir_empleo_model->fields,$empleo)):($data['fields'] = $this->build_fields($this->pedir_empleo_model->fields));
		$curri?($data['empleo']=$empleo):"";
		$curri?($data['txt_btn'] = 'editar'):($data['txt_btn'] = 'agregar');
		$data['title_view'] = 'Cargar curriculum';
		$data['title'] = TITLE . ' - CV';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);
	}

				//con estas anotaciones creo la base de datos
				//CREATE TABLE `wi_dev`.`oe_cv`(`cuil` BIGINT(12) PRIMARY KEY,`persona_id` int(10) not null, `sexo` varchar(10) not null, `celular` BIGINT(15), `capacitacion` VARCHAR(1) NOT NULL, `horario_cap` VARCHAR(30), `intereses_cap` VARCHAR(300), `busca_empleo` VARCHAR(1), `condic` VARCHAR(40), `movilidad` VARCHAR(40), `movil_carnet` VARCHAR(20), `discapacidad` VARCHAR(30), `cud` VARCHAR(30), `estudio` VARCHAR(20), `estudiosOt` VARCHAR(40),`grado` VARCHAR(30),`gradoo` VARCHAR(30), `idiomas` VARCHAR(40), `computacion` VARCHAR(60), `cursos` VARCHAR(100), `oficios` VARCHAR(60),`experiencia` VARCHAR(100), `interes_lab` VARCHAR(100), `disponib_lab` VARCHAR(40),`exmuni` CHARACTER(1),`famimuni` CHARACTER(1), `aclaraciones` varchar(300),`pdf` varchar(30),`audi_usuario` int not null ,`audi_fecha` date,`audi_accion` CHARACTER(1))ENGINE = MyISAM;
				//CREATE TABLE `wi_dev_aud`.`oe_cv`(`audi_id` INT AUTO_INCREMENT PRIMARY KEY,`cuil` BIGINT(12) not null, `persona_id` INT(10) not null, `sexo` varchar(10) not null, `celular` BIGINT(15), `capacitacion` VARCHAR(1) NOT NULL, `horario_cap` VARCHAR(30), `intereses_cap` VARCHAR(300), `busca_empleo` VARCHAR(1), `condic` VARCHAR(40), `movilidad` VARCHAR(40), `movil_carnet` VARCHAR(20), `discapacidad` VARCHAR(30), `cud` VARCHAR(30), `estudio` VARCHAR(20), `estudiosOt` VARCHAR(40),`grado` VARCHAR(30),`gradoo` VARCHAR(30), `idiomas` VARCHAR(40), `computacion` VARCHAR(60), `cursos` VARCHAR(100), `oficios` VARCHAR(60),`experiencia` VARCHAR(100), `interes_lab` VARCHAR(100), `disponib_lab` VARCHAR(40),`exmuni` CHARACTER(1),`famimuni` CHARACTER(1), `aclaraciones` varchar(300),`pdf` varchar(30),`audi_usuario` int not null ,`audi_fecha` date,`audi_accion` CHARACTER(1))ENGINE = MyISAM;


	public function eliminar($cuil = NULL)
	{

		if (!in_groups($this->grupos_permitidos, $this->grupos) || $cuil == NULL/* || !ctype_digit($dni)*/)
		{
			$Dni=$this->session->userdata('identity');	//esto es redundante, puede no estar
                    $cuil = $this->Personas_model->get(array(
					'select'=>array('cuil'),
					'where'=>(array("personas.dni=$Dni", "personas.apellido=$this->session->userdata('apellido')")))); 
					$cuil=$cuil[0];
		}
		$empleo = $this->pedir_empleo_model->get(array('cuil' => $cuil));
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}

		$person = $this->Personas_model->get(array('cuil' => $cuil)); 
		$persona=$this->Personas_model->get_one(get_object_vars($person[0])['id']);


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
		$trans_ok &= $this->pedir_empleo_model->delete(array('cuil' => $cuil/*$this->input->post('dni')*/)); 
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
        $data['field'] = $this->build_fields($this->Personas_model->fields, $persona, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $persona, TRUE);
        $data['persona'] = $persona;
		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo, TRUE); 
		$data['empleo'] = $empleo;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar curriculum';
		$data['title'] = TITLE . ' - Eliminar curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}

	public function ver($cuil = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $cuil == NULL/* || !ctype_digit($dni)*/)
		{
			$Dni=$this->session->userdata('identity');	//esto es redundante, puede no estar
                    $cuil = $this->Personas_model->get(array(
					'select'=>array('cuil'),
					'where'=>(array("personas.dni=$Dni", "personas.apellido=$this->session->userdata('apellido')")))); 
					$cuil=$cuil[0];
		}
		$empleo = $this->pedir_empleo_model->get(array('cuil' => $cuil));
		if (empty($empleo))
		{
			show_error('No se encontró el curriculum', 500, 'Registro no encontrado');
		}

		$person = $this->Personas_model->get(array('cuil' => $cuil)); 
		$persona=$this->Personas_model->get_one(get_object_vars($person[0])['id']);



		$persona->sexo = $empleo->sexo;
				$this->load->helper('audi_helper');
		$data['audi_modal'] = audi_modal($empleo);
        $data['field'] = $this->build_fields($this->Personas_model->fields, $persona, TRUE);
        $data['fields_domicilio'] = $this->build_fields($this->Domicilios_model->fields, $persona, TRUE);
        $data['persona'] = $persona;
		$data['fields'] = $this->build_fields($this->pedir_empleo_model->fields, $empleo, TRUE); 
		$data['empleo'] = $empleo;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver curriculum';
		$data['title'] = TITLE . ' - Ver curriculum';
		$this->load_template('oficina_de_empleo/pedir_empleo/pedir_empleo_abm', $data);   
	}

}