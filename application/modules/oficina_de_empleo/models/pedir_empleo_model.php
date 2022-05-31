<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo de pedir_empleo 
 * Autor: Leandro
 * Creado: 10/10/2018
 * Modificado: 18/10/2018 (Pablo)
 */
/*
*****************************************esta clase se usa como repositorio de arrays declarados aca mismo*****************************************
*/
class pedir_empleo_model extends MY_Model
{
	private $array_genero = array(
			'masc' => 'masculino',
			'fem' => 'femenino',
			'nobi' => 'no binario',
			'trans' => 'trans',
	);
	private $array_estudio = array(  
			'primario incom'=>'primario incom',
			'pimario'=>'pimario',
			'secundario incom'=>'secundario incom',
			'secundario'=>'secundario',
			'terciario incom'=>'terciario incom',
			'terciario'=>'terciario',
			'posgrado'=>'posgrado',
	);
	private $array_si_no = array(
			true => 'SI',
			false => 'NO',
	);

	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'oe_cv'; 				//nombre de la tabla de la base de datos
		$this->full_log = TRUE;
		$this->msg_name = 'base de datos de curriculum';
		$this->id_name = 'Dni';
	$this->columnas = array('Dni','nombre','apellido','telefono','email','genero','fecha_nac','domicilio','distrito','otro_cel','capacitacion','horario_cap','intereses_cap','busca_empleo','movilidad','movil_carnet','discapacidad','cud','estudio','estudiosOt','grado','idiomas','computacion','cursos','experiencia','interes_lab','disponib_lab','freelance','teletrabajo','viajante','cama_adentro','casero',/*'exmuni','famimuni',*/'aclaraciones',/*'pdf'*/);
		$this->fields = array(    //estos campos son del formulario propio*******************************************
																														//estos datos son de otra base y no son editables
				'Dni' => array('label' => 'Dni', 'type' => 'integer', 'maxlength' => '11', 'disabled' => false),    		//Dni
				'nombre' => array('label' => 'Nombres', 'type'=>'varchar', 'maxlength' => '30', 'required' => TRUE),			//nombre del usuario
				'apellido' => array('label' => 'Apellido', 'type'=>'varchar', 'maxlength' => '30', 'required' => TRUE),			//nombre del usuario
				'telefono' => array('label' => 'Telefono', 'type' => 'varchar', 'maxlength' => '30', 'required' => TRUE),    	//telefono
				'email' => array('label' => 'email', 'type'=>'varchar', 'maxlength' => '30', 'required' => TRUE ),								//correo electronico
																													//estos campos son propios
				'genero' => array('label' => 'Genero', 'input_type' => 'combo', 'id_name' => 'genero', 'type' => 'bselect', 'required' => TRUE),			//genero
				'fecha_nac' => array('label' => 'Fecha de nacimiento', 'type' => 'date', 'required' => TRUE),						//nacimiento
				'domicilio' => array('label' => 'Domicilio', 'tipe'=>'varchar','maxlength' => '30', 'required'=> TRUE),			//domicilio
				'distrito' => array('label' => 'Distrito', 'tipe'=>'varchar','maxlength' => '30', 'required'=> TRUE),			//distrito
				'otro_cel' => array('label' => 'Otro Telefono', 'type' => 'integer', 'maxlength' => '15', 'required' => TRUE),    	// otro telefono
				'capacitacion' => array('label' => 'Capacitación', 'input_type' => 'combo', 'id_name' => 'capacitacion', 'type' => 'bselect', 'required' => TRUE),			//capacitacion sn
				'horario_cap' => array('label' => 'horarios disponibles',  'type' => 'varchar', 'maxlength'=>'30'),		//horarios disponbles		
				'intereses_cap' => array('label' => 'intereses',  'type' => 'varchar', 'maxlength'=>'300'),					//intereses    ,por rubro
				'busca_empleo' => array('label' => 'Busqueda de empleo', 'input_type' => 'combo', 'id_name' => 'busca_empleo', 'type' => 'bselect', 'required' => TRUE),		//busca trabajo ,s/n
				
				'movilidad' => array('label' => 'vehiculo propio','type'=>'varchar', 'maxlength' => '30'),		//movilidad  tipo y categoria de carnet habilitante
				'movil_carnet'	=> array('label' => 'tipo de carnet','type'=>'varchar', 'maxlength' => '30'),		//movilidad  tipo y categoria de carnet habilitante
				'discapacidad' => array('label' => 'dicapacidad', 'type' => 'varchar', 'maxlength' => '30'),								//discapacidad
				'cud' => array('label' => 'CUD', 'type' => 'varchar', 'maxlength' => '30'),										//nombre del archivo de imagen
				'estudio' => array('label' => 'Nivel maximo de estudios alcanzado','input_type' => 'combo', 'id_name' => 'estudio', 'type' => 'bselect'),		//nivel de estudios 
				'estudiosOt' => array('label' => 'titulo secundario', 'type' => 'varchar', 'maxlength' => '30'),
				'grado' => array('label' => 'estudios de grado', 'type' => 'varchar', 'maxlength' => '30'),							//otros estudio
				'idiomas' => array('label' => 'Idiomas', 'type' => 'varchar', 'maxlength' => '30'),							//idioma y nivel del 1-5
				//'idiomas_niv' => array('label' => 'estudio', 'type' => 'varchar', 'maxlength' => '30'),							//idioma y nivel del 1-5

				'computacion' => array('label' => 'programa de Informatica', 'type' => 'varchar', 'maxlength' => '30'),				//programa y nivel del 1-5
				//'compu_niv' => array('label' => 'nivel ', 'type' => 'varchar', 'maxlength' => '30'),				//programa y nivel del 1-5

				'cursos' => array('label' => 'Cursos', 'type' => 'varchar', 'rows' => 5, 'maxlength' => '30'),				//otros cursos
				'experiencia' => array('label' => 'experiencia laboral', 'type' => 'varchar', 'maxlength' => '30'),				//rubro-puesto-duracion-personal a cargo s/n
				'interes_lab' => array('label' => 'interes laboral', 'type' => 'varchar', 'maxlength' => '30'),								//campo rellenable
				'disponib_lab' => array('label' => 'disponibilidad horaria', 'type' => 'varchar', 'maxlength' => '30'),					//combo de oppp y rotativo s/n franquero s/n
				'freelance' => array('label' => 'Freelance', 'input_type' => 'combo', 'id_name' => 'freelance', 'type' => 'bselect'),		//s/n
				'teletrabajo' => array('label' => 'Teletrabajo', 'input_type' => 'combo', 'id_name' => 'teletrabajo', 'type' => 'bselect'),		//sn
				'viajante' => array('label' => 'Viajante', 'input_type' => 'combo', 'id_name' => 'viajante', 'type' => 'bselect'),		//sn
				'cama_adentro' => array('label' => 'Cama adentro', 'input_type' => 'combo', 'id_name' => 'cama_adentro', 'type' => 'bselect'),		//sn
				'casero' => array('label' => 'Casero', 'input_type' => 'combo', 'id_name' => 'casero', 'type' => 'bselect'),		//sn
			//	'exmuni' => array('label' => 'trabajo en la municipalidad', 'input_type' => 'combo', 'id_name' => 'exmuni', 'type' => 'bselect'),		//sn
				//'famimuni' => array('label' => 'Familiares en la municipalidad', 'input_type' => 'combo', 'id_name' => 'famimuni', 'type' => 'bselect'),		//sn
				'aclaraciones' => array('label' => 'Aclaraciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '300'),
			//	'pdf' => array('label' => 'carga de curriculum', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '300')

		);
		$this->requeridos = array('Dni');
		$this->unicos = array('Dni');
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
	function get_genero()
	{
		return $this->array_genero;
	}
	function get_estudio()
	{
		return $this->array_estudio;
	}
	function get_si_no()
	{
		return $this->array_si_no;
	}
}