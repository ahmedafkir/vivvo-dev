<?php
	function json_url_handler(&$sm, $url_array){
		$um =& $sm->get_url_manager();
		$um->list['url_module'] = 'json';
		array_shift($um->_url_array);
		$um->list['module'] = $url_array[1];
		array_shift($um->_url_array);
		$um->parse_url();
		return true;
	}
	
	function json_content_handler(&$sm){
		require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
		header('Content-type: application/json');
		$um =& $sm->get_url_manager();
		$output = '{}';
		if ($um->list['module']){
			$module =& $sm->load_module($um->list['module'], $um->list, $sm->template);
			if ($module){
				$output = $module->get_json_output();
			}
		}
		
		echo $output;
		exit;
	}
?>