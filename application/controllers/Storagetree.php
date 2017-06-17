<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Storagetree extends CI_Controller {

	public function index()
	{
		$this->load->helper('tree');

		$operation = $this->input->get('operation');

		try
		{
			$result = array();

			switch ($operation)
			{
				case 'create_node':
					$parent_uri = $this->input->post('parent_uri');
					$text = $this->input->post('text');

					if (empty($parent_uri) OR empty($text))
					{
						throw new Exception('Cannot create directory');
					}

					$parent_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $parent_uri);
					$dir_path = path_encode($parent_path.$text, '/');

					exec("mkdir {$dir_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot create directory');
					}
					unset($output);
					unset($status);

					break;
				case 'rename_node':
					$parent_uri = $this->input->post('parent_uri');
					$new_text = $this->input->post('new_text');
					$old_text = $this->input->post('old_text');

					if (empty($parent_uri) OR empty($new_text) OR empty($old_text))
					{
						throw new Exception('Cannot rename directory');
					}

					$parent_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $parent_uri);
					$new_path = path_encode($parent_path.$new_text, '/');
					$old_path = path_encode($parent_path.$old_text, '/');

					exec("mv {$old_path} {$new_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot rename directory');
					}
					unset($output);
					unset($status);

					break;
				case 'delete_node':
					$uri = $this->input->post('uri');

					if (empty($uri))
					{
						throw new Exception('Cannot delete directory');
					}

					$dir_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $uri);
					$dir_path = path_encode($dir_path, '/');

					exec("rm -r {$dir_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot delete directory');
					}
					unset($output);
					unset($status);

					break;
				case 'move_node':
					$new_parent_uri = $this->input->post('new_parent_uri');
					$old_parent_uri = $this->input->post('old_parent_uri');
					$text = $this->input->post('text');

					if (empty($new_parent_uri) OR empty($old_parent_uri) OR empty($text))
					{
						throw new Exception('Cannot move directory');
					}

					$new_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $new_parent_uri).$text;
					$new_path = path_encode($new_path, '/');
					$old_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $old_parent_uri).$text;
					$old_path = path_encode($old_path, '/');

					exec("mv {$old_path} {$new_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot move directory');
					}
					unset($output);
					unset($status);

					break;
				case 'copy_node':
					$new_parent_uri = $this->input->post('new_parent_uri');
					$old_parent_uri = $this->input->post('old_parent_uri');
					$text = $this->input->post('text');

					if (empty($new_parent_uri) OR empty($old_parent_uri) OR empty($text))
					{
						throw new Exception('Cannot copy directory');
					}

					$new_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $new_parent_uri).$text;
					$new_path = path_encode($new_path, '/');
					$old_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $old_parent_uri).$text;
					$old_path = path_encode($old_path, '/');

					exec("cp -r {$old_path} {$new_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot copy directory');
					}
					unset($output);
					unset($status);

					break;
				default:
					throw new Exception('Unsupported operation: '.$operation);
					break;
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($result);
		}
		catch (Exception $e)
		{
			header($this->input->server('SERVER_PROTOCOL').' 500 Server Error');
			header('Status: 500 Server Error');
			echo $e->getMessage();
		}
		die();
	}
}
