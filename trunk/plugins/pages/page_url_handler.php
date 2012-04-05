<?php
	function page_url_handler(&$sm, $url_array) {
		$um = $sm->get_url_manager();

		if ($url_array[0] != '404') {
			require_once(dirname(__FILE__) . '/Pages.class.php');
			$page_list = new Pages_list($sm);
			$page = $page_list->get_pages_by_sefriendly(str_replace('.html', '', $url_array[0]));
			if ($page) {
				return array('url_module' => $url_array[0], 'search_page' => $page->id);
			} else {
				go_404();
			}
		}
		return false;
	}

	function page_content_handler(&$sm) {
		$um = $sm->get_url_manager();
		$sm->set_theme();

		if ($um->get_param('search_page')) {
			require_once(dirname(__FILE__) . '/Pages.class.php');
			$page_list = new Pages_list($sm);
			$page = $page_list->get_pages_by_id($um->get_param('search_page'));

			if ($page) {
				$sm->_template->assign('PAGE_TITLE', strval($page->title));
				if ($um->isset_param('force_template') && $sm->user && $sm->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/' . $um->get_param('force_template'))) {
					$sm->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/' . $um->get_param('force_template'));
				} elseif ($page->template && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/' . $page->template)) {
					$sm->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/' . $page->template);
				} else {
					$sm->_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
				}
				$sm->_template->assign('PAGE_CONTENT', $page->body);
				$sm->_template->assign('PAGE_OBJECT', $page);
			}
		}
	}
?>