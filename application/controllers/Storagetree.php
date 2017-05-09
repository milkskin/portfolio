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

					$parent_path = readlink(FCPATH . 'data') . preg_replace("/^\/cloudstorage/", '', $parent_uri);
					$dir_path = $parent_path . rawurlencode($text);

					exec("mkdir {$dir_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot create directory');
					}
					unset($output);
					unset($status);

					exec("ls -ai {$dir_path} | awk '{print $1}' | sed '2,\$d'", $file_info);
					$inode = $file_info[0];
					unset($file_info);

					$result['id'] = "dir_{$inode}";
					$result['text'] = rawurlencode($text);
					break;
				case 'rename_node':
					$parent_uri = $this->input->post('parent_uri');
					$new_text = $this->input->post('new_text');
					$old_text = $this->input->post('old_text');

					$parent_path = readlink(FCPATH . 'data') . preg_replace("/^\/cloudstorage/", '', $parent_uri);
					$new_path = $parent_path . rawurlencode($new_text);
					$old_path = $parent_path . rawurlencode($old_text);

					exec("mv {$old_path} {$new_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot rename directory');
					}
					unset($output);
					unset($status);

					$result['text'] = rawurlencode($new_text);
					break;
				case 'delete_node':
					$uri = $this->input->post('uri');

					$dir_path = readlink(FCPATH . 'data') . preg_replace("/^\/cloudstorage/", '', $uri);

					exec("rm -r {$dir_path}", $output, $status);
					if ($status !== 0)
					{
						throw new Exception('Cannot delete directory');
					}
					unset($output);
					unset($status);

					break;
				case 'move_node':
					break;
				case 'copy_node':
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
