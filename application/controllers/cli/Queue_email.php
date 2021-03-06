<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Queue_email extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->input->is_cli_request())
		{
			show_404();
		}
		$this->load->library('email');
	}

	public function index()
	{
		show_404();
	}

	public function send_queue()
	{
		$this->email->send_queue();
	}

	public function retry_queue()
	{
		$this->email->retry_queue();
	}
}