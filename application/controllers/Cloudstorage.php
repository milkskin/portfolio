<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cloudstorage extends CI_Controller {

	public function index()
	{
		$this->load->helper('directory');
		$this->load->helper('tree');

		$uri_segment = explode('/', uri_string());
		$dir_struct = directory_map(FCPATH . 'data');

		$data = array(
			'dir_list' => structure_to_markup(array('/' => $dir_struct), "/{$uri_segment[0]}"),
		);

		$this->layout->load_view('default', $data);
	}
}
