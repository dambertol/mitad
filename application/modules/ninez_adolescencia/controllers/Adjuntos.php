<?php

defined('BASEPATH') OR exit('No direct script access allowed');

include_once(APPPATH . 'core/MY_Upload.php');

class Adjuntos extends MY_Upload
{

	/**
	 * Controlador de Adjuntos
	 * Autor: Leandro
	 * Creado: 10/09/2019
	 * Modificado: 17/09/2019 (Leandro)
	 */
	function __construct()
	{
		parent::__construct();
		$this->load->model('ninez_adolescencia/Tipos_adjuntos_model');
		$this->grupos_permitidos = array('admin', 'ninez_adolescencia_admin', 'ninez_adolescencia_consulta_general');
		$this->grupos_solo_consulta = array('ninez_adolescencia_consulta_general');
		$this->modulo = 'ninez_adolescencia';
		// Inicializaciones necesarias colocar acá.
	}

	public function listar_data($expediente_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $expediente_id == NULL || !ctype_digit($expediente_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$this->datatables
				->select('na_adjuntos.id, na_adjuntos.fecha_subida, na_tipos_adjuntos.nombre as tipo, na_adjuntos.descripcion')
				->from('na_adjuntos')
				->join('na_tipos_adjuntos', 'na_tipos_adjuntos.id = na_adjuntos.tipo_id', 'left')
				->where('expediente_id', $expediente_id)
				->add_column('ver', '<a href="ninez_adolescencia/adjuntos/adjunto/$1" title="Ver" class="btn btn-primary btn-xs"><i class="fa fa-search"></i></a>', 'id')
				->add_column('eliminar', '<a href="ninez_adolescencia/adjuntos/eliminar/$1" title="Eliminar" class="btn btn-primary btn-xs"><i class="fa fa-times"></i></a>', 'id');

		echo $this->datatables->generate();
	}

	public function agregar($expediente_id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $expediente_id == NULL || !ctype_digit($expediente_id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		if (in_groups($this->grupos_solo_consulta, $this->grupos))
		{
			$this->session->set_flashdata('error', 'Usuario sin permisos de edición');
			redirect('ninez_adolescencia/adjuntos/listar', 'refresh');
		}

		$this->array_tipo_control = $array_tipo = $this->get_array('Tipos_adjuntos', 'nombre');
		$this->set_model_validation_rules($this->Adjuntos_model);
		$error_msg = FALSE;
		if ($this->form_validation->run() === TRUE)
		{
			if (!empty($_FILES['path']['name']))
			{
				$config['upload_path'] = "uploads/ninez_adolescencia/expedientes/" . $expediente_id . "/";
				if (!file_exists($config['upload_path']))
				{
					mkdir($config['upload_path'], 0755, TRUE);
				}
				$config['file_ext_tolower'] = TRUE;
				$config['allowed_types'] = 'pdf';
				$config['max_size'] = 4096;
				$this->load->library('upload', $config);
				if (!$this->upload->do_upload('path'))
				{
					$error_msg = $this->upload->display_errors();
				}
				else
				{
					$upload_data = $this->upload->data();
				}
			}

			$this->db->trans_begin();
			$trans_ok = TRUE;

			$trans_ok &= $this->Adjuntos_model->create(array(
					'tipo_id' => $this->input->post('tipo'),
					'nombre' => $upload_data['file_name'],
					'descripcion' => $this->input->post('descripcion'),
					'ruta' => $config['upload_path'],
					'tamanio' => round($upload_data['file_size'], 2),
					'hash' => md5_file($config['upload_path'] . $upload_data['file_name']),
					'fecha_subida' => (new DateTime())->format('Y-m-d H:i'),
					'usuario_subida' => $this->session->userdata('user_id'),
					'expediente_id' => $expediente_id), FALSE);

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Adjuntos_model->get_msg());
				redirect("ninez_adolescencia/expedientes/ver/$expediente_id", 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Adjuntos_model->get_error())
				{
					$error_msg .= $this->Adjuntos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));
		$this->Adjuntos_model->fields['tipo']['array'] = $array_tipo;
		$data['fields'] = $this->build_fields($this->Adjuntos_model->fields);
		$data['expediente_id'] = $expediente_id;
		$data['txt_btn'] = 'Agregar';
		$data['title_view'] = 'Agregar Adjunto';
		$data['title'] = TITLE . ' - Agregar Adjunto';
		$data['css'][] = 'vendor/bootstrap-fileinput/css/fileinput.css';
		$data['js'][] = 'vendor/bootstrap-fileinput/js/fileinput.js';
		$data['js'][] = 'vendor/bootstrap-fileinput/js/locales/es.js';
		$data['js'][] = 'vendor/bootstrap-fileinput/themes/fa/theme.js';
		$this->load_template('ninez_adolescencia/adjuntos/adjuntos_abm', $data);
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
			redirect("ninez_adolescencia/adjuntos/ver/$id", 'refresh');
		}

		$adjunto = $this->Adjuntos_model->get_one($id);
		if (empty($adjunto))
		{
			show_error('No se encontró el Adjunto', 500, 'Registro no encontrado');
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
			$trans_ok &= $this->Adjuntos_model->delete(array('id' => $this->input->post('id')), FALSE);
			$archivo = $adjunto->ruta . $adjunto->nombre;
			if (file_exists($archivo))
			{
				$borrado = unlink($archivo); //No funciona directo a $trans_ok
				if (!$borrado)
				{
					$trans_ok = FALSE;
				}
			}

			if ($this->db->trans_status() && $trans_ok)
			{
				$this->db->trans_commit();
				$this->session->set_flashdata('message', $this->Adjuntos_model->get_msg());
				redirect("ninez_adolescencia/expedientes/ver/$adjunto->expediente_id", 'refresh');
			}
			else
			{
				$this->db->trans_rollback();
				$error_msg = '<br />Se ha producido un error con la base de datos.';
				if ($this->Adjuntos_model->get_error())
				{
					$error_msg .= $this->Adjuntos_model->get_error();
				}
			}
		}
		$data['error'] = (!empty($error_msg)) ? $error_msg : ((validation_errors()) ? validation_errors() : $this->session->flashdata('error'));

		unset($this->Adjuntos_model->fields['path']);
		$data['fields'] = $this->build_fields($this->Adjuntos_model->fields, $adjunto, TRUE);
		$data['adjunto'] = $adjunto;
		$data['txt_btn'] = 'Eliminar';
		$data['title_view'] = 'Eliminar Adjunto';
		$data['expediente_id'] = $adjunto->expediente_id;
		$data['title'] = TITLE . ' - Eliminar Adjunto';
		$this->load_template('ninez_adolescencia/adjuntos/adjuntos_abm', $data);
	}

	public function adjunto($id = NULL)
	{
		if (!in_groups($this->grupos_permitidos, $this->grupos) || $id == NULL || !ctype_digit($id))
		{
			show_error('No tiene permisos para la acción solicitada', 500, 'Acción no autorizada');
		}

		$adjunto = $this->Adjuntos_model->get_one($id);
		if (empty($adjunto))
		{
			show_error('No se encontró el Adjunto', 500, 'Registro no encontrado');
		}
		unset($this->Adjuntos_model->fields['path']);
		$data['fields'] = $this->build_fields($this->Adjuntos_model->fields, $adjunto, TRUE);
		$data['adjunto'] = $adjunto;
		$data['txt_btn'] = NULL;
		$data['title_view'] = 'Ver Adjunto';
		$data['expediente_id'] = $adjunto->expediente_id;
		$data['title'] = TITLE . ' - Ver Adjunto';
		$this->load_template('ninez_adolescencia/adjuntos/adjuntos_abm', $data);
	}

	public function descargar($entidad_nombre = NULL, $archivo_id = NULL)
	{
		return null;
	}

	public function ver($entidad_nombre = NULL, $directorio_nombre = NULL, $archivo_id = NULL)
	{
		$this->entidad = $entidad_nombre;
		$this->directorio = $directorio_nombre;
		$this->archivo = $archivo_id;
		$this->entidad_id_nombre = 'expediente_id';

		parent::ver();
	}

	public function modal_agregar($entidad_nombre = NULL)
	{
		return null;
	}
}