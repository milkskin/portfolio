<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Layout {

	protected $CI;
	protected $uri_segment = array('main', 'index');

	public function __construct()
	{
		$this->CI =& get_instance();

		$temp_segment = explode('/', uri_string());

		foreach ($temp_segment as $key => $value)
		{
			if ($value !== '')
			{
				$this->uri_segment[$key] = $value;
			}
		}
	}

	public function load_view($layout_style = 'default', $data = array())
	{
		$data['_header'] = VIEWPATH.$this->uri_segment[0].DIR_SEPARATOR.'header.php';
		$data['_container'] = VIEWPATH.$this->uri_segment[0].DIR_SEPARATOR.$this->uri_segment[1].'.php';
		$data['_footer'] = VIEWPATH.$this->uri_segment[0].DIR_SEPARATOR.'footer.php';

		$layout = 'layout'.DIR_SEPARATOR.$layout_style;

		if ($layout_style === 'cloudstorage')
		{
			$data['_container'] = VIEWPATH.$this->uri_segment[0].DIR_SEPARATOR.'index.php';
			$layout = 'layout'.DIR_SEPARATOR.'default';
		}

		$this->CI->load->view($layout, $data);
	}
}
