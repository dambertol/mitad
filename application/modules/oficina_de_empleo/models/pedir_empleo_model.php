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
	private $array_estudio = array(  
			'primario incom'=>'primario incom',
			'pimario'=>'pimario',
			'secundario incom'=>'secundario incom',
			'secundario'=>'secundario',
			'terciario incom'=>'terciario incom',
			'terciario'=>'terciario',
			'posgrado'=>'posgrado',
	);

	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'oe_cv'; 				//nombre de la tabla de la base de datos
		$this->full_log = TRUE;
		$this->msg_name = 'base de datos de curriculum';
		$this->id_name = 'cuil';
		$this->columnas = array('cuil','persona_id','sexo','celular','capacitacion','horario_cap','intereses_cap','busca_empleo','condic','movilidad','movil_carnet','discapacidad','hijos','estudio','estudiosOt','grado','gradoo','idiomas','computacion','cursos','oficios','experiencia','interes_lab','disponib_lab','exmuni','famimuni','aclaraciones');
		$this->fields = array(    //estos campos son del formulario propio*******************************************
																														//estos datos son de otra base y no son editables
				'cuil' => array('label' => 'Cuil', 'type' => 'text', 'maxlength' => '12', 'disabled' => false),    		//dni
				'sexo' => array('label' => 'genero',  'type'=>'text', 'maxlength'=>'12'),		//horarios disponbles		'style'=>"display:none" ,
				'celular' => array('label' => 'Otro Telefono', 'type' => 'number', 'maxlength' => '15', 'required' => TRUE),    	// otro telefono
	
				'capacitacion' => array('label' => 'Capacitación','style'=>"display:none", 'type'=>'text', 'id_name' => 'capacitacion', 'maxlength' => '1'),			//capacitacion sn
				'horario_cap' => array('label' => 'horarios disponibles',  'type'=>'text','style'=>"display:none" , 'maxlength'=>'30'),		//horarios disponbles		
				'intereses_cap' => array('label' => 'intereses', 'type'=>'text' , 'maxlength'=>'300'),					//intereses    ,por rubro
				'busca_empleo' => array('label' => 'Busqueda de empleo','style'=>"display:none",  'type'=>'text', 'maxlength'=>'1'),		//busca trabajo ,s/n
				'interes_lab' => array('label' => 'interes laboral', 'type'=>'text','style'=>"display:none" , 'maxlength' => '100'),								//campo rellenable
				'disponib_lab' => array('label' => 'disponibilidad horaria', 'type' => 'text','style'=>"display:none" , 'maxlength' => '40'),					//combo de oppp y rotativo s/n franquero s/n
				'condic' => array('label' => 'condiciones especiales', 'type' => 'text','style'=>"display:none" , 'maxlength' => '40'),					//combo de oppp y rotativo s/n franquero s/n

				'movilidad' => array('label' => 'vehiculo propio','type'=>'text','style'=>"display:none" , 'maxlength' => '40'),		//movilidad  tipo y categoria de carnet habilitante
				'movil_carnet'	=> array('label' => 'tipo de carnet','type'=>'text','style'=>"display:none" , 'maxlength' => '20'),		//movilidad  tipo y categoria de carnet habilitante
				'discapacidad' => array('label' => 'dicapacidad', 'type' => 'text','style'=>"display:none" , 'maxlength' => '30'),								//discapacidad
				'hijos' => array('label' => 'hijos a cargo', 'type'=>'text','style'=>"display:none", 'id_name' => 'hijos', 'maxlength' => '1'),
				'estudio' => array('label' => 'Nivel maximo de estudios','input_type' => 'combo', 'id_name' => 'estudio', 'type' => 'bselect'),		//nivel de estudios 
				'estudiosOt' => array('label' => 'titulo secundario', 'type' => 'text','style'=>"display:none" , 'maxlength' => '40'),
				'grado' => array('label' => 'estudios de grado', 'type' => 'text','style'=>"display:none" , 'maxlength' => '30'),							//otros estudio
				'gradoo' => array('label' => 'estudios de grado', 'type' => 'text','style'=>"display:none" , 'maxlength' => '30'),							//otros estudio
				'idiomas' => array('label' => 'Idiomas', 'type' => 'text', 'style'=>"display:none" ,'maxlength' => '40'),							//idioma y nivel del 1-5
				'computacion' => array('label' => 'programa de Informatica', 'type' => 'text','style'=>"display:none" , 'maxlength' => '60'),				//programa y nivel del 1-5
				'cursos' => array('label' => 'Cursos', 'type' => 'text', 'rows' => 5, 'maxlength' => '100'),//otros cursos
				'oficios' => array('label' => 'oficios', 'type' => 'text', 'style'=>"display:none" ,'maxlength' => '60'),
				'experiencia' => array('label' => 'experiencia laboral', 'type' => 'text', 'maxlength' => '300'),				//rubro-puesto-duracion-personal a cargo s/n
				'exmuni' => array('label' => 'trabajo en la municipalidad','style'=>"display:none", 'type'=>'varchar','maxlength' => '3'),		//sn
				'famimuni' => array('label' => 'Familiares en la municipalidad','style'=>"display:none", 'type'=>'varchar','maxlength' => '3'),		//sn
				'aclaraciones' => array('label' => 'Aclaraciones', 'form_type' => 'textarea', 'rows' => 5, 'maxlength' => '100'),
		);
		$this->requeridos = array('cuil');
		$this->unicos = array('cuil');
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
	function get_estudio()
	{
		return $this->array_estudio;
	}

}