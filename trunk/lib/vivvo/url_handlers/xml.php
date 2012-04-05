<?php
	function xml_url_handler(&$sm, $url_array){
		$um =& $sm->get_url_manager();
		$um->list['url_module'] = 'xml';
		array_shift($um->_url_array);
		$um->list['module'] = $url_array[1];
		array_shift($um->_url_array);
		$um->parse_url();
		return true;
	}
	
	function xml_content_handler(&$sm){
		require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
		header('Content-type: text/xml');
		$um =& $sm->get_url_manager();
		$output = '<?xml version="1.0" encoding="UTF-8"?>';
		if ($um->list['module']){
			$module =& $sm->load_module($um->list['module'], $um->list, $sm->template);
			if ($module){
				$output = $module->get_xml_output();
			}
		}
		
		echo $output;
		exit;
	}
?>