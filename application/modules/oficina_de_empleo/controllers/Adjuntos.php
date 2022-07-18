<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once(APPPATH . 'core/MY_Upload.php');
class Adjuntos extends MY_Controller
{

	/**
	 * MY_Uploads
	 *metodo editado por yoel grosso
	 * 
	 * @package    CodeIgniter
	 * @subpackage core
	 * @category   controller
	 * @version    1.0.0
	 * @author     ZettaSys <info@zettasys.com.ar>
	 * 
	 */

	protected $grupos_permitidos = array();
	protected $modulo = NULL; //TODOS
	protected $entidad = NULL; //TODOS
	protected $directorio = NULL; //SOLO VER
	protected $archivo = NULL; //SOLO VER
	protected $archivo_id = NULL; //SOLO DESCARGAR
	//protected $grupos_areas = NULL; //Sólo si hay division de permisos por areas usando Usuarios_areas_model
	protected $entidad_id_nombre; //VER Y DESCARGAR
	protected $extensiones; //AGREGAR y AGREGARMODAL
	protected $verificar_archivo = TRUE; //SOLO VER
	protected $file_size = 4096; //SOLO AGREGAR

	function __construct()
	{
		parent::__construct();
            $this->grupos_permitidos = array('admin','oficina_empleo_general','oficina_empleo','user','tramites_online_publico');
    $this->modulo = 'oficina_de_empleo';
		$this->load->model("$this->modulo/Adjuntos_model");
	}

    public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}
        {
            $this->entidad = $entidad_nombre;
            $this->archivo_id = $archivo_id;
            $this->entidad_id_nombre = 'id';
            $this->load->model("$this->modulo/Adjuntos_model");
            $adjunto = $this->Adjuntos_model->get_one($this->archivo_id);
            if (empty($adjunto))
            {
                show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
            }
            $this->load->model("$this->modulo/{$this->entidad}_model");
            $entidad = $this->{"{$this->entidad}_model"}->get_one($adjunto->{$this->entidad_id_nombre});
		$adjunto = $this->Adjuntos_model->get_one($this->archivo_id);
		if (empty($adjunto) || empty($adjunto->{$this->entidad_id_nombre}))
		{
			show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
		}

		$path = $adjunto->ruta;
		$file = $path . $adjunto->nombre;
		if (!file_exists($file))
		{
			show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
		}

		$this->load->helper('download');
		force_download($file, NULL);
		exit();
	}
}

    public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $archivo_id = NULL)
    {
        if ($entidad_nombre === 'manuales')
        {
            $this->entidad = $entidad_nombre;
            $this->directorio = '';
            $this->archivo = $directorio_nombre;
            $this->verificar_archivo = FALSE;
        }
        else
        {
            $this->entidad = $entidad_nombre;
            $this->directorio = $directorio_nombre;
            $this->archivo = $archivo_id;
            $this->entidad_id_nombre = 'empleo_id';
            $this->load->model("$this->modulo/Adjuntos_model");
            $path = "uploads/$this->modulo/$this->entidad/$this->directorio/";
            $adjunto = $this->Adjuntos_model->get(array('ruta' => $path, 'nombre' => $this->archivo));
            if (empty($adjunto[0]))
            {
                show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
            }

            if ($directorio_nombre !== 'tmp')
            {
                $this->load->model("$this->modulo/{$this->entidad}_model");
                $entidad = $this->{"{$this->entidad}_model"}->get_one($adjunto[0]->{$this->entidad_id_nombre});
            }
        }

		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$path = "uploads/$this->modulo/$this->entidad/$this->directorio/";
		$file = $path . $this->archivo;
		if (!file_exists($file))
		{
			show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
		}

		$adjunto = $this->Adjuntos_model->get(array('ruta' => $path, 'nombre' => $this->archivo));
		if ($this->directorio !== 'tmp' && $this->verificar_archivo)
		{
			if (empty($adjunto[0]) || empty($adjunto[0]->{$this->entidad_id_nombre}))
			{
				show_error('No se encontró el archivo solicitado', 404, 'Archivo no encontrado');
			}
			if (md5_file($file) !== $adjunto[0]->hash)
			{
				show_error('Archivo inválido', 500, 'Registro no encontrado');
			}
		}

		$this->load->helper('file');
		header('Content-Type: ' . get_mime_by_extension($file));
		$last_modified = gmdate('D, d M Y H:i:s', filemtime($file));
		$etag = '"' . md5($last_modified) . '"';
		header("Last-Modified: $last_modified GMT");
		header('ETag: ' . $etag);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 100000000) . ' GMT');
		readfile($file);
		exit();
	}
    

    public function modal_agregar($entidad_nombre = NULL, $documento_id =NULL)
    {
        $this->entidad = $entidad_nombre;
        $this->extensiones = '["pdf"]';
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			return $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
		}
       // echo '<script language="javascript">alert("el cuil no coincide");</script>';

		$fake_model = new stdClass();
		$fake_model->fields = array(
				'path' => array('label' => 'Archivo', 'type' => 'file'),
				'tipo_adjunto' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
		);
		$this->array_tipo_adjunto_control = $array_tipo_adjunto =$this->Adjuntos_model->get_tipo_adjunto();

		$data['extensiones'] = $this->extensiones;
		$data['modulo_nombre'] = $this->modulo;
		$data['entidad_nombre'] = $this->entidad;
		$data['accion_url'] = "$this->modulo/adjuntos/agregar/$this->entidad/$documento_id";
		$fake_model->fields['tipo_adjunto']['array'] = $array_tipo_adjunto;
		$data['fields'] = $this->build_fields($fake_model->fields);
		$data['txt_btn'] = 'Agregar';
		$data['title'] = 'Agregar Adjunto';
		$this->load->view('adjuntos/adjuntos_modal_abm', $data);
	}

    public function agregar($entidad_nombre = NULL, $documento_id =NULL)
    {
        $this->entidad = $entidad_nombre;
        $this->extensiones = 'pdf';
		if (!in_groups($this->grupos_permitidos, $this->grupos))
		{
			return $this->modal_error('No tiene permisos para la acción solicitada', 'Acción no autorizada');
		}

		$adjunto = $this->Adjuntos_model->get(array(
			'documento_id' => $documento_id
		));
		if(!empty($adjunto)){
			if($_POST){
			foreach ($adjunto as $key => $value) {
				if($value->tipo_id==$this->input->post('tipo_adjunto')){
					$conf= "<script type='text/javascript'>confirm('Si continua el archivo anterior será borrado')</script>";
					echo $conf;
					if($conf==true){
						$viejo_archivo = $value->ruta . $value->nombre;
										$trans_ok &= $this->Adjuntos_model->delete(array('id' => $value->id), FALSE);
										$borrado = unlink($viejo_archivo); //No funciona directo a $trans_ok 
										if (!$borrado)
										{
											$trans_ok = FALSE;
										}
									
					}else{
						exit;
					}

				}
			}

			}
		}


		$fake_model = new stdClass();
		$fake_model->fields = array(
				'path' => array('label' => 'Archivo', 'type' => 'file'),
				'tipo_adjunto' => array('label' => 'Tipo', 'input_type' => 'combo', 'type' => 'bselect', 'required' => TRUE),
		);
		$this->array_tipo_adjunto_control = $array_tipo_adjunto = $this->Adjuntos_model->get_tipo_adjunto();

		$this->set_model_validation_rules($fake_model);
		if ($this->form_validation->run() === TRUE)
		{
			$fecha = new DateTime();
			$output = NULL;

			if (!empty($_FILES['path']['name']))
			{
				$config['upload_path'] = "uploads/$this->modulo/$this->entidad/$documento_id/";
				if (!file_exists($config['upload_path']))
				{
					mkdir($config['upload_path'], 0755, TRUE);
				}
				$config['allowed_types'] = $this->extensiones;
				$config['file_ext_tolower'] = TRUE;
				$config['encrypt_name'] = TRUE;
				$config['max_size'] = $this->file_size;
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('path'))
				{
					$output = array('uploaded' => 'ERROR', 'error' => $this->upload->display_errors());
				}
				else
				{
					$upload_data = $this->upload->data();
				}
			}
			else
			{
				$output = array('uploaded' => 'ERROR', 'error' => 'Debe seleccionar algún archivo para subir');
			}

			if (empty($output) && !empty($upload_data))
			{
				$this->db->trans_begin();
				$trans_ok = TRUE;

				$trans_ok &= $this->Adjuntos_model->create(array(
						'tipo_id' => $this->input->post('tipo_adjunto'),
						'documento_id' => $documento_id,
						'nombre' => $upload_data['file_name'],
						'ruta' => $config['upload_path'],
						'tamanio' => round($upload_data['file_size'], 2),
						'hash' => md5_file($config['upload_path'] . $upload_data['file_name']),
						'fecha_subida' => $fecha->format('Y-m-d H:i:s'),
						'usuario_subida' => $this->session->userdata('user_id')), FALSE);

				$adjunto_id = $this->Adjuntos_model->get_row_id();

				if ($this->db->trans_status() && $trans_ok)
				{
					$this->db->trans_commit();
					$output = array(
							'uploaded' => 'OK',
							'adjunto' => array(
									'id' => $adjunto_id,
									'nombre' => $upload_data['file_name'],
									'extension' => pathinfo($upload_data['file_name'])['extension'],
									'archivo' => $config['upload_path'] . $upload_data['file_name'],
									'tipo_id' => $this->input->post('tipo_adjunto'),
									'tipo' => $array_tipo_adjunto[$this->input->post('tipo_adjunto')],
									'documento_id' => $this->input->post('documento_id')
							),
					);
				}
				else
				{
					$this->db->trans_rollback();
					if (!empty($upload_data))
					{
						unlink($config['upload_path'] . $upload_data['file_name']);
					}
					$output = array('uploaded' => 'ERROR', 'error' => 'Se ha producido un error con la base de datos');
				}
			}
		}

		echo json_encode($output);
	}
}
