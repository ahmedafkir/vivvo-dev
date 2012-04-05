<?php
function search_url_handler(&$sm, $url_array){
	if (VIVVO_MODULES_SEARCH != 1){
		go_404();
	}

	return array('url_module' => 'search.html');
}


function search_content_handler(&$sm){
	$um =& $sm->get_url_manager();


	if ($um->isset_param('search_do_advanced')){
		require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
		$params = Articles_list::get_search_params_from_url($sm);
		unset($params['pg']);
		if ($um->isset_param('search_archive')){
			$params['search_archive'] = 1;
		}
		$params['search_do_advanced'] = true;
		$params = strtr(rtrim(base64_encode(serialize($params)), '='), '+/', '-_');
		//$params = rtrim($params, '=');

		if ($um->isset_param('search_archive')){
			header ('Location:' . make_proxied_url('archive/index.html?search_options=' . $params));
		} else {
			header ('Location:' . make_proxied_url('index.html?search_options=' . $params));
		}
		exit();
	}

	$template = $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');

	$content_template = new template($sm, $template);
	$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/advanced_search.tpl');
	$template->assign('PAGE_CONTENT', $content_template->get_output());
}
?>