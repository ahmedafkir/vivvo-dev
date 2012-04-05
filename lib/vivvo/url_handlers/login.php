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

function login_url_handler(&$sm, $url_array){
	if (VIVVO_USER_SOURCE != 'vivvo@localhost'){
		go_404();
	}
	return array('url_module' => 'login.html');
}

function login_content_handler(&$sm) {

	$template = $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');

	$um = $sm->get_url_manager();
	$dm = $sm->get_dump_manager();

	$dm->restore_dump(md5(make_proxied_url('login.html')));

	if (defined('VIVVO_ABSOLUTE_URL')) {
		$index_url = VIVVO_ABSOLUTE_URL;
	} else {
		$index_url = make_absolute_url('');
	}

	$action = $sm->action_status;

	$template->assign('DUMP_MESSAGES', $dm->_dump_pool);

	if ($action){

		if ($um->get_param('action') == 'login' && $um->get_param('cmd') == 'login'){
			header('Location: ' . $index_url);
			exit;
		}elseif (($um->get_param('action') == 'login' && $um->get_param('cmd') == 'register')
			or ($um->get_param('action') == 'login' && $um->get_param('cmd') == 'confirm')){
			$template->assign('REGISTRATION', strval('1'));
		}elseif (($um->get_param('action') == 'login' && $um->get_param('cmd') == 'changePassword')){
			$template->assign('REGISTRATION', strval('0'));
		}else{
			$template->assign('REGISTRATION', strval('0'));
		}
	}elseif ($um->isset_param('action') && $action === false){
		$in_user = $um->get_param_regexp('/^USER_/');
		$current_user = new Users($sm, $in_user);
		$template->assign('user', $current_user);
	}else{
		$template->assign('REGISTRATION', strval('0'));
	}

	$content_template = new template($sm, $template);
	if (($um->get_param('activation_key'))){
		$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/change_password.tpl');
		$content_template->assign('key', htmlspecialchars($um->get_param('activation_key')));
	}else{
		$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/register.tpl');
	}
	$template->assign('PAGE_CONTENT', $content_template->get_output());
}

?>