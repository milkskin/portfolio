<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'libraries/UploadHandler.php');

class Storagedata extends CI_Controller {

	public function index()
	{
		$option = array(
			'script_url' => '/storagedata/',
			'upload_dir' => STORAGEPATH.'/',
			'upload_url' => '/storagedata/',
			'download_via_php' => 1,
		);

		$upload_handler = new UploadHandler($option);
	}
}
