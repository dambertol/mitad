<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Modelo de Intermediacion
 * Autor: Leandro
 * Creado: 14/03/2019
 * Modificado: 05/04/2019 (Leandro)
 */
class Intermediacion_model extends MY_Model
{

	/*private $array_estados = array(
			NULL => 'SIN ESPECIFICAR',
			'FINALIZADO POR DECLARACION JURADA' => 'FINALIZADO POR DECLARACION JURADA',
			'FINALIZADO C/SUP GIS' => 'FINALIZADO CON SUPERFICIE GIS',
			'FINALIZADO S/SUP GIS' => 'FINALIZADO SIN SUPERFICIE GIS',
			'RECALCULO' => 'RECALCULO',
			'PENDIENTE' => 'PENDIENTE',
			'PENDIENTE P/ INSPECCION' => 'PENDIENTE P/ INSPECCION'
	);*/
	/*private $array_tipo = array(
			NULL => 'SIN ESPECIFICAR',
			'NOTA' => 'NOTA',
			'PRESENCIAL' => 'PRESENCIAL',
			'CORREO' => 'CORREO',
			'FORMULARIO BIC' => 'FORMULARIO BIC',
			'LLAMADO TEL' => 'LLAMADO TEL',
			'OFICIO' => 'OFICIO',
			'TRANSFERENCIA' => 'TRANSFERENCIA',
			'OTRO' => 'OTRO'
	);*/
	//private $array_inspeccion = array(
			//NULL => 'SIN ESPECIFICAR',
			//'SI' => 'SI',
			//'NO' => 'NO'
//	);
	//private $array_correccion_capa = array(
		//	NULL => 'SIN ESPECIFICAR',
			//'SI' => 'SI',
			//'NO' => 'NO'
	//);
	private $array_carrera = array(
		'abogacia' => 'Abogacía',
		'acomp_terap' => 'Acompañante Terapeutico',
		'administ' => 'Administración',
		'administ_contable' => 'Administración Contable',
		'agente_sanitario' => 'Agente Sanitario',
		'agronomia' => 'Agronomía',
		'anestesista' => 'Anestesista',
		'arquitectura' => 'Arquitectura',
		'bioimagen' => 'Bioimágen',
		'bioquimica' => 'Bioquímica',
		'bromatologia' => 'Bromatología',
		'cienc_polít_administ_ubl' => 'Ciencias Políticas y Administración Pública',
		'cine_video' => 'Cine y Video',
		'civil' =>'Civil',
		'comercio_exterior' => 'Comercio Exterior',
		'comunic_digital' => 'Comunicación Digital',
		'comunic_social' => 'Comunicación Social',
		'contador' => 'Contador',
		'criminologia' => 'Criminología',
		'diagnost_imagen' => 'Diagnóstico por Imágen',
		'diseno_grafico' => 'Diseño Grafico',
		'diseno_induist' => 'Diseño Induistrial',
		'diseno_animacion' => 'Diseño y Animación',
		'economia' => 'Economía',
		'educacion_fisica' => 'Educación Física',
		'educacion_inicial' => 'Educación Inicial',
		'educacion_primaria' => 'Educación Primaria',
		'educacion_secundaria' => 'Educación Secundaria',
		'educacion_ter_niver' => 'Educación Terciaria/Universitaria',
		'electromec' => 'Electromecánica',
		'electronica' => 'Electrónica',
		'enfermeria' => 'Enfermería',
		'enologia' => 'Enología',
		'farmacia' => 'Farmacia',
		'fonoaudiologia' => 'Fonoaudiología',
		'gastronomia' => 'Gastronomía',
		'geologia' => 'Geología',
		'gestion_ambiental' => 'Gestión Ambiental',
		'gestor' => 'Gestor',
		'grafologia' => 'Grafología',
		'guia_turismo' => 'Guía Turismo',
		'higiene_seg_laboral' => 'Higiene y Seguridad Laboral',
		'hoteleria_turismo' => 'Hotelería y Turismo',
		'industrial' => 'Industrial',
		'instrum_quirurgico' => 'Instrumentista Quirúrgico',
		'internacionales' => 'Internacionales',
		'jardin_maternal' => 'Jardin Maternal',
		'kinesiologia' => 'Kinesiología',
		'laboratorio' => 'Laboratorio',
		'letras' => 'Letras',
		'logistica_transporte' => 'Logística y Transporte',
		'marketing' => 'Marketing',
		'martillero_publico_corredor_inmob' => 'Martillero Público, Corredor Inmobiliario',
		'masoterapia' => 'Masoterapia',
		'mecanica_prod_automat' => 'Mecánica y Producción Automatizada',
		'medicina' => 'Medicina',
		'metalmecanico' => 'Metalmecánico',
		'mineria_gas_sustent' => 'Minería y Gas Sustentable',
		'ninez_adolesc_familia' => 'Niñez, Adolescencia y Familia',
		'nutricion' => 'Nutrición',
		'obstetricia' => 'Obstetricia',
		'odontologia' => 'Odontología',
		'otros' => 'Otros',
		'petroleo_gas' => 'Petroleo y Gas',
		'programador' => 'Programador',
		'protesis_dental' => 'Protesis Dental',
		'psicologia' => 'Psicología',
		'psicopedagogia' => 'Psicopedagogía',
		'publicidad' => 'Publicidad',
		'quimica_industrial' => 'Química Industrial',
		'radiologia' => 'Radiología',
		'recursos_humanos' => 'Recursos Humanos',
		'recursos_naturales' => 'Recursos Naturales',
		'relaciones_humanas' => 'Relaciones Humanas',
		'seguridad_publica_penit' => 'Seguridad Pública y Penitenciaría',
		'sistemas' => 'Sistemas',
		'sonido' => 'Sonido',
		'trabajo_social' => 'Trabajo Social'
	);

	private $array_distrito = array(
		'agrelo' => 'Agrelo',
		'cacheuta' => 'Cacheuta',
		'carrodilla' => 'Carrodilla',
		'chacras' => 'Chacras de Coria',
		'lujan' => 'Lujan de Cuyo',
		'drummond' => 'Mayor Drummond',
		'carrizal' => 'El Carrizal',
		'puntilla' => 'La Puntilla',
		'compuertas' => 'Las Compuertas',
		'perdriel' => 'Perdriel',
		'potrerillos' => 'Potrerillos',
		'ugarteche' => 'Ugarteche',
		'pedemonte' => 'Vertientes del Pedemonte',
		'vistalba' => 'Vistalba',
	);

	private $array_estudios = array(
		'primario' => 'Primario',
		'secundario' => 'Secundario',
		'terciario' => 'Terciario',
		'universitario' => 'Universitario',
	);

	private $array_nivel_estudios = array(
		'incompleto' => 'Incompleto',
		'en curso' => 'En Curso',
		'completo' => 'Completo',
	);

	private $array_genero = array(
		'masc' => 'Masculino',
		'fem' => 'Femenino',
		'indis' => 'Indistinto',
	);

	private $array_tipo_solicitud = array(
		'directa' => 'Búsqueda Directa',
		'enlace' => 'Enlace',
		'enlazados' => 'Enlazados',
		'ept' => 'EPT',
		'pil' => 'PIL',
		'promover' => 'Promover',
		'te_sumo' => 'Te Sumo',
	);

	public function __construct()
	{
		parent::__construct();
		$this->table_name = 'intermediacion';
		$this->full_log = TRUE;
		$this->msg_name = 'Intermediacion';
		$this->id_name = 'id';
		$this->columnas = array('id', 'domicilio', 'razon_social', 'distrito', 'telefono_empresa', 'email', 'puesto_requerido', 'cantidad_personas', 'genero', 'rango_edad', 'estudios', 'nivel_estudios', 'estado', 'carrera', 'experiencia_requerida', 'tareas_realizar', 'datos_adicionales', 'tipo_solicitud', 'correccion_capa', 'fecha', 'cuit', 'audi_usuario', 'audi_fecha', 'audi_accion');
		$this->fields = array(
				'cuit' => array('label' => 'CUIT', 'type' => 'integer', 'maxlength' => '11', 'disabled' => TRUE),
				'distrito' => array('label' => 'Distrito', 'input_type' => 'combo', 'id_name' => 'distrito', 'type' => 'bselect', 'required' => TRUE),
				'domicilio' => array('label' => 'Domicilio', 'maxlength' => '50', 'required' => TRUE),
				'razon_social' => array('label' => 'Razon Social', 'type' => 'natural', 'maxlength' => '100', 'required' => TRUE),
				'fecha' => array('label' => 'Fecha', 'type' => 'datetime'),
				'telefono_empresa' => array('label' => 'Telefono Empresa', 'maxlength' => '255', 'required' => TRUE),
				'email' => array('label' => 'Email', 'maxlength' => '100'),
				'puesto_requerido' => array('label' => 'Puesto Requerido', 'maxlength' => '255', 'required' => TRUE),
				'cantidad_personas' => array('label' => 'Cantidad Personas', 'type' => 'integer', 'maxlength' => '11', 'required' => TRUE),
				'genero' => array('label' => 'Genero', 'input_type' => 'combo', 'id_name' => 'genero', 'type' => 'bselect', 'required' => TRUE),
				'rango_edad' => array('label' => 'Rango Edad', 'maxlength' => '50', 'required' => TRUE),
				'estudios' => array('label' => 'Estudios', 'input_type' => 'combo', 'id_name' => 'estudios', 'type' => 'bselect', 'required' => TRUE),
				'nivel_estudios' => array('label' => 'Nivel de Estudios', 'input_type' => 'combo', 'id_name' => 'nivel_estudios', 'type' => 'bselect', 'required' => TRUE),
				'carrera' => array('label' => 'Carrera', 'input_type' => 'combo', 'id_name' => 'carrera', 'type' => 'bselect', 'required' => TRUE),
				'experiencia_requerida' => array('label' => 'Experiencia Requerida', 'maxlength' => '255', 'required' => TRUE),
				'tareas_realizar' => array('label' => 'Tareas a Realizar', 'maxlength' => '255', 'required' => TRUE),
				'datos_adicionales' => array('label' => 'Datos Adicionales', 'maxlength' => '255', 'required' => TRUE),
				'tipo_solicitud' => array('label' => 'Tipo de Solicitud', 'input_type' => 'combo', 'id_name' => 'nivel_estudios', 'type' => 'bselect', 'required' => TRUE),
		);
		$this->requeridos = array('nomenclatura');
		$this->unicos = array('padron');
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

	//function get_email()
	//{
		//return $this->array_email;
	//}

	//function get_telefono_alternativo()
	//{
	//	return $this->array_telefono_alternativo;
	//}

	//function get_inspeccion()
	//{
	//	return $this->array_inspeccion;
	//}

	//function get_correccion_capa()
	//{
		//return $this->array_correccion_capa;
	//}

	function get_distrito()
	{
		return $this->array_distrito;
	}

	function get_nivel_estudios()
	{
		return $this->array_nivel_estudios;
	}

	function get_genero()
	{
		return $this->array_genero;
	}

	function get_estudios()
	{
		return $this->array_estudios;
	}

	function get_tipo_solicitud()
	{
		return $this->array_tipo_solicitud;
	}

	function get_carrera()
	{
		return $this->array_carrera;
	}

}