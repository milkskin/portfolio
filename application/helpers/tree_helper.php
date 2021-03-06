<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('structure_to_markup'))
{
	function structure_to_markup($dir_struct = array(), $alter_uri = '/', $is_root = TRUE)
	{
		$uri_segment = explode('/', uri_string());
		$dir_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $alter_uri);
		$open_tag = '<ul>';
		$inner_tag = '';
		$close_tag = '</ul>';

		ksort($dir_struct);

		foreach ($dir_struct as $key => $value)
		{
			$file_path = $dir_path.preg_replace('/'.preg_quote(DIR_SEPARATOR, '/').'$/', '', $key);

			if (is_array($value) && ! is_link($file_path))
			{
				$inner_tag .= '<li>';
				$inner_tag .= ('<a href="'.$alter_uri.$key.'">');
				$inner_tag .= ($is_root ? $key : rawurldecode(preg_replace('/'.preg_quote(DIR_SEPARATOR, '/').'$/', '', $key)));
				$inner_tag .= '</a>';

				if ( ! empty($value))
				{
					$inner_tag .= structure_to_markup($value, $alter_uri.$key, FALSE);
				}

				$inner_tag .= '</li>';
			}
		}

		if ($inner_tag === '')
		{
			$open_tag = '';
			$close_tag = '';
		}

		return ($open_tag.$inner_tag.$close_tag);
	}
}

if ( ! function_exists('structure_to_array'))
{
	function structure_to_array($dir_struct = array(), $alter_uri = '/', $parent_id = '#')
	{
		$is_root = ($parent_id === '#');
		$uri_segment = explode('/', uri_string());
		$uri_matched_path = STORAGEPATH.preg_replace('/^'.STORAGEURI.'/', '', uri_string());
		$dir_path = STORAGEPATH.preg_replace('/^\/'.STORAGEURI.'/', '', $alter_uri);
		$node_list = array();

		ksort($dir_struct);

		foreach ($dir_struct as $key => $value)
		{
			$file_path = $dir_path.preg_replace('/'.preg_quote(DIR_SEPARATOR, '/').'$/', '', $key);

			if (is_array($value) && ! is_link($file_path))
			{
				exec("tree -dfiv --inodes ".DIR_SEPARATOR."data | grep {$file_path}$ | awk '{print substr($2, 0, length($2) - 1)}'", $file_info);
				$inode = $file_info[0];
				unset($file_info);

				$node_info = array(
					'id' => 'dir_'.$inode,
					'parent' => $parent_id,
					'text' => ($is_root ? $key : rawurldecode(preg_replace('/'.preg_quote(DIR_SEPARATOR, '/').'$/', '', $key))),
					'state' => array(
						'opened' => FALSE,
						'selected' => ($file_path === $uri_matched_path),
					),
					'a_attr' => array(
						'href' => $alter_uri.$key,
					),
					'type' => ($is_root ? 'root' : 'default'),
				);
				$node_list[] = $node_info;

				if ( ! empty($value))
				{
					$subtree_node_list = structure_to_array($value, $alter_uri.$key, 'dir_'.$inode);
					$node_list = array_merge($node_list, $subtree_node_list);
				}
			}
		}

		return $node_list;
	}
}

if ( ! function_exists('path_encode'))
{
	function path_encode($dir_path, $separator = DIR_SEPARATOR)
	{
		$segment = explode($separator, $dir_path);

		foreach ($segment as $key => $value)
		{
			$segment[$key] = rawurlencode($value);
		}

		return implode($separator, $segment);
	}
}
