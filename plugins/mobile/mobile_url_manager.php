<?php
/* =============================================================================
 * $Revision: 4834 $
 * $Date: 2010-03-30 11:39:23 +0200 (Tue, 30 Mar 2010) $
 *
 * Vivvo CMS v4.5.2r (build 6084)
 *
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */

	/**
	 * Mobile url manager
	 */
	function mobile_url_handler(&$sm, $url_array) {

		$um = $sm->get_url_manager();
		$sm->set_theme('mobile');
		array_shift($um->_url_array);

		define('VIVVO_TEMPLATE_DIR_OVERWRITE', 'mobile');


		$template = $sm->get_template();

		if (defined('VIVVO_SYSTEM_PROXY_URL')) {
			if (!defined('VIVVO_PROXY_URL')) {
				define('VIVVO_PROXY_URL', VIVVO_SYSTEM_PROXY_URL . 'mobile/');
			} else {
				define('VIVVO_PROXY_URL_OVERWRITE', VIVVO_SYSTEM_PROXY_URL . 'mobile/');
				$template->assign('VIVVO_PROXY_URL', strval(VIVVO_PROXY_URL_OVERWRITE));
			}
		} elseif (defined('VIVVO_PROXY_URL')) {
			define('VIVVO_PROXY_URL_OVERWRITE', VIVVO_PROXY_URL . 'mobile/');
		} else {
			define('VIVVO_PROXY_URL', 'mobile/');
		}

		if (defined('VIVVO_ABSOLUTE_URL')) {
			$template->assign('VIVVO_ABSOLUTE_URL', make_absolute_url(''));
		} else {
			define('VIVVO_ABSOLUTE_URL', make_absolute_url(''));
		}

		if ($um->_url_array[0] == 'search'){
			if ($um->isset_param('search_do_advanced')){
				require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
				$params = Articles_list::get_search_params_from_url($sm);
				unset($params['pg']);
				if ($um->isset_param('search_archive')){
					$params['search_archive'] = 1;
				}
				$params['search_do_advanced'] = true;
				$params = base64_encode(serialize($params));
				$params = rtrim($params, '=');

				header ('Location:' . make_absolute_url('mobile/index.html?search_options=' . $params));
				exit();
			}

			$sm->set_template_dir();
			$sm->load_action_handlers();

			$sm->set_theme();

			$template->set_template_file(VIVVO_FS_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');

			$content_template = new template($sm, $template);
			$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/advanced_search.tpl');
			$template->assign_template('PAGE_CONTENT', $content_template);

			$output = $sm->get_output();
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
			echo str_replace('$generation_time$', time() - $_generation_time . 's', $output);
			exit;
		} elseif ($um->_url_array[0] == 'login') {
			$dm = $sm->get_dump_manager();
			$dm->restore_dump(md5(VIVVO_URL . 'mobile/login'));

			$sm->set_template_dir();
			$sm->load_action_handlers();

			$sm->set_theme();

			$action = $sm->execute_action();

			$template->set_template_file(VIVVO_FS_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');

			$template->assign('DUMP_MESSAGES', $dm->_dump_pool);

			if ($sm->user){
				$template->assign('CURRENT_USER', $sm->user);
			}

			if ($um->isset_param('action') && $action === false) {
				$in_user = $um->get_param_regexp('/^USER_/');
				$current_user = new Users($sm, $in_user);
				$template->assign('user', $current_user);
			} else {
				$template->assign('REGISTRATION', strval('0'));
			}


			$content_template = new template($sm, $template);

			$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/register.tpl');
			$template->assign_template('PAGE_CONTENT', $content_template);

			$output = $sm->get_output();
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
			echo str_replace('$generation_time$', time() - $_generation_time . 's', $output);

			ob_end_flush();
			exit;
		} else {
			$um->parse_url();
		}

		return true;
	}
?>