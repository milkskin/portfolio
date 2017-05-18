<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Storagetree extends CI_Controller {

	public function index()
	{
		$operation = $this->input->get('operation');

		try
		{
			$result = array();

			switch ($operation)
			{
				case 'create_node':
					$parent_uri = $this->input->post('parent_uri');
					$text = $this->input->post('text');

					$parent_path = STORAGEPATH . preg_replace("/^\/".STORAGEDIR."/", '', $parent_uri);
					$dir_path = $parent_path . rawurlencode($text);

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

					$parent_path = STORAGEPATH . preg_replace("/^\/".STORAGEDIR."/", '', $parent_uri);
					$new_path = $parent_path . rawurlencode($new_text);
					$old_path = $parent_path . rawurlencode($old_text);

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

					$dir_path = STORAGEPATH . preg_replace("/^\/".STORAGEDIR."/", '', $uri);

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

					$new_path = STORAGEPATH . preg_replace("/^\/".STORAGEDIR."/", '', $new_parent_uri) . rawurlencode($text);
					$old_path = STORAGEPATH . preg_replace("/^\/".STORAGEDIR."/", '', $old_parent_uri) . rawurlencode($text);

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

					$new_path = STORAGEPATH . preg_replace("/^\/".STORAGEDIR."/", '', $new_parent_uri) . rawurlencode($text);
					$old_path = STORAGEPATH . preg_replace("/^\/".STORAGEDIR."/", '', $old_parent_uri) . rawurlencode($text);

					exec("cp -r {$old_path} {$new_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot copy directory');
					}
					unset($output);
					unset($status);

					break;
				default:
					throw new Exception('Unsupported operation: ' . $operation);
					break;
			}

			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($result);
		}
		catch (Exception $e)
		{
			header($this->input->server('SERVER_PROTOCOL') . ' 500 Server Error');
			header('Status: 500 Server Error');
			echo $e->getMessage();
		}
		die();
	}
}
