<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends MY_Controller
{

	/**
	 * Controlador Ajax
	 * Autor: Leandro
	 * Creado: 13/03/2017
	 * Modificado: 14/11/2018 (Leandro)
	 */
	public function __construct()
	{
		parent::__construct();
		// Inicializaciones necesarias colocar acá.
	}

	public function set_menu_collapse()
	{
		$this->form_validation->set_rules('value', 'Valor', 'integer|required');
		if ($this->form_validation->run() === TRUE)
		{
			$this->session->set_userdata('menu_collapse', $this->input->post('value'));
			echo json_encode(array('ok' => 'ok'));
			return;
		}
		echo json_encode(array('error' => 'error'));
		return;
	}

	public function update_alertas()
	{
		$this->load->model('alertas_model');
		$grupos = groups_names($this->ion_auth->get_users_groups()->result_array());
		$alertas = $this->alertas_model->get($grupos);

		if (!empty($alertas))
		{
			$alerta_html = '';
			foreach ($alertas as $Alerta)
			{
				$alerta_html .= '<li><a href="' . $Alerta->url . '"><i class="' . $Alerta->iclass . '"></i><b>' . $Alerta->value . '</b> ' . $Alerta->label . '</a></li>';
			}
			$this->output->set_status_header('200');
			$return_data['message'] = $alerta_html;
			$return_data['count'] = count($alertas);
		}
		else
		{
			$return_data['message'] = '<li><i class="fa fa-square"></i>Sin notificaciones pendientes</li>';
			$return_data['count'] = 0;
		}
		echo json_encode($return_data);
		return;
	}
	
	
// Buscador ***********************************************************
		
		
		

		public function search()
	{
		$this->load->model('tramites_online/Procesos_model');
		
		
		    

        $query = $this->input->get('query');

        $this->db->like('name', $query);


        $data = $this->db->get("tags")->result();


        echo json_encode( $data);

    }
	
}
			
			
/*

$this->db->select('age');
$this->db->where('id', '3');
$q = $this->db->get('my_users_table');
$data = $q->result_array();

*/
