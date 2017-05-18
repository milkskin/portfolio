<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cloudstorage extends CI_Controller {

	public function index()
	{
		$this->load->helper('directory');
		$this->load->helper('tree');

		$uri_segment = explode('/', uri_string());
		$dir_struct = directory_map(STORAGEPATH);

		$root_struct = array(
			DIR_SEPARATOR => $dir_struct,
		);
		$root_alter_uri = "/cloudstorage";

		$data = array(
			'dir_list_markup' => structure_to_markup($root_struct, $root_alter_uri),
			'dir_list_json' => json_encode(structure_to_array($root_struct, $root_alter_uri)),
		);

		$this->layout->load_view('cloudstorage', $data);
	}
}
