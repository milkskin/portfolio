<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('structure_to_markup'))
{
	function structure_to_markup($dir_struct = array(), $alter_uri = '/')
	{
		$uri_segment = explode('/', uri_string());
		$dir_path = readlink(FCPATH . 'data') . preg_replace("/^\/{$uri_segment[0]}/", '', $alter_uri);
		$open_tag = '<ul>';
		$inner_tag = '';
		$close_tag = '</ul>';

		foreach ($dir_struct as $key => $value)
		{
			$file_path = $dir_path . preg_replace('/\/$/', '', $key);

			if (is_array($value) && ! is_link($file_path))
			{
				$inner_tag .= '<li>';
				$inner_tag .= ('<a href="' . $alter_uri . $key . '">');
				$inner_tag .= $key;
				$inner_tag .= '</a>';

				if ( ! empty($value))
				{
					$inner_tag .= structure_to_markup($value, $alter_uri . $key);
				}

				$inner_tag .= '</li>';
			}
		}

		if ($inner_tag === '')
		{
			$open_tag = '';
			$close_tag = '';
		}

		return ($open_tag . $inner_tag . $close_tag);
	}
}

if ( ! function_exists('structure_to_array'))
{
	function structure_to_array($dir_struct = array(), $alter_uri = '/', $parent_id = '#')
	{
		$is_root = ($parent_id === '#');
		$uri_segment = explode('/', uri_string());
		$dir_path = readlink(FCPATH . 'data') . preg_replace("/^\/{$uri_segment[0]}/", '', $alter_uri);
		$node_list = array();

		foreach ($dir_struct as $key => $value)
		{
			$file_path = $dir_path . preg_replace('/\/$/', '', $key);

			if (is_array($value) && ! is_link($file_path))
			{
				exec("ls -ai {$file_path} | awk '{print $1}' | sed '2,\$d'", $file_info);
				$inode = $file_info[0];
				unset($file_info);

				$node_info = array(
					'id' => "dir_{$inode}",
					'parent' => $parent_id,
					'text' => $key,
					'state' => array(
						'opened' => FALSE,
						'selected' => FALSE,
					),
					'a_attr' => array(
						'href' => $alter_uri . $key,
					),
					'type' => ($is_root ? 'root' : 'default'),
				);
				$node_list[] = $node_info;

				if ( ! empty($value))
				{
					$subtree_node_list = structure_to_array($value, $alter_uri . $key, "dir_{$inode}");
					$node_list = array_merge($node_list, $subtree_node_list);
				}
			}
		}

		return $node_list;
	}
}
