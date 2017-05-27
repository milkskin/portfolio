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
		$root_alter_uri = '/'.STORAGEURI;

		$uri_matched_path = STORAGEPATH.preg_replace('/^'.STORAGEURI.'/', '', uri_string());

		if ( ! is_dir($uri_matched_path) OR is_link($uri_matched_path))
		{
			$nondir_uri = preg_replace('/^'.STORAGEURI.'/', '', uri_string());
			$this->session->set_flashdata('error_dir', $nondir_uri);
			redirect(base_url().STORAGEURI);
		}

		$error_dir = $this->session->flashdata('error_dir');

		$data = array(
			'error_dir' => $error_dir,
			'error_alert' => ($error_dir !== null),
			'dir_list_markup' => structure_to_markup($root_struct, $root_alter_uri),
			'dir_list_json' => json_encode(structure_to_array($root_struct, $root_alter_uri)),
		);

		$this->layout->load_view('cloudstorage', $data);
	}
}
