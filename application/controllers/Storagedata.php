<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require(APPPATH.'libraries/UploadHandler.php');

class Storagedata extends CI_Controller {

	public function index()
	{
		$option = array(
			'script_url' => '/storagedata/',
			'upload_dir' => STORAGEPATH.DIR_SEPARATOR,
			'upload_url' => '/storagedata/',
			'download_via_php' => 1,
			'image_versions' => array(
				'' => array(
					'auto_orient' => true,
				),
				'thumbnail' => array(
					'upload_dir' => THUMBNAILPATH.DIR_SEPARATOR,
					'max_width' => 100,
					'max_height' => 100,
				),
			),
		);

		$upload_handler = new UploadHandler($option);
	}
}
