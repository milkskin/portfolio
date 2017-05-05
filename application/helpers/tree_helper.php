<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('structure_to_markup'))
{
	function structure_to_markup($dir_struct = array(), $alter_uri = '/')
	{
		$uri_segment = explode('/', uri_string());
		$open_tag = '<ul>';
		$inner_tag = '';
		$close_tag = '</ul>';

		foreach ($dir_struct as $key => $value)
		{
			$file_path = readlink(FCPATH . 'data') . preg_replace("/^\/{$uri_segment[0]}/", '', $alter_uri) . preg_replace('/\/$/', '', $key);

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
