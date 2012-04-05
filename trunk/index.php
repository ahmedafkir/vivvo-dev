<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
 *
 * Vivvo CMS v4.6 (build 6082)
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

	if (ini_get('zlib.output_compression')) {
		ob_start();
	} elseif (function_exists('ob_gzhandler')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}

	require_once (dirname(__FILE__). '/conf.php');

	if (!defined('VIVVO_FS_INSTALL_ROOT')){
		$_SERVER['REQUEST_URI'] = preg_replace('/index.php/','', $_SERVER['REQUEST_URI']);
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']. 'installer/install.html');
		exit();
	}

	define('CURRENT_URL', htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));

	// leave query part of URL only for revision preview pages (cache each revision separately)
	if (preg_match('/index\.php\?news=\d+&rev=\d+$/', $_SERVER['REQUEST_URI'])) {
		define('CACHE_URL', CURRENT_URL);
	} else {
		define('CACHE_URL', htmlspecialchars('http://' . $_SERVER['HTTP_HOST'] . preg_replace('/\?.*$/','', $_SERVER['REQUEST_URI'])));
	}

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/full_page_cache.functions.php');

	list($output, $uncached_boxes) = full_page_cache_load();

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php');
	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/box/vivvo_box.php');
	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/functions.php');

	$sm = vivvo_lite_site::get_instance();
	$sm->output = $output;
	$um = $sm->get_url_manager();
	$dm = $sm->get_dump_manager();

	if (defined('VIVVO_ROOT_CATEGORY')){
		$cat = $sm->get_categories();
		define('VIVVO_FORCE_CATEGORY_RESTRICTION', implode(',', $cat->root_category->get_descendent_ids()));
	}
	$cat = $sm->get_categories();

	$template = $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'homepage/' . VIVVO_HOMEPAGE_LAYOUT);
	
	if (!$um->isset_param('cmd') && $um->get_param('cmd')!='forgotMail' && !$um->isset_param('activation_key') ){
		check_close_site($sm);
	}
	$action = $sm->execute_action();

    if ($um->isset_param('action') && $um->get_param('action')){
        $template->assign('REQUEST_ACTION', $um->get_param('action'));
    }

    if ($um->isset_param('cmd') && $um->get_param('cmd')){
        $template->assign('REQUEST_CMD', $um->get_param('cmd'));
    }
    
	if ($um->isset_param('action') && $um->get_param('action') == 'login' && !$action) {
		$in_user = $um->get_param_regexp('/^USER_/');
		$user = new Users($sm, $in_user);
		$template->assign('user', $user);
	}

	if ($um->isset_param('cmd') && $um->get_param('cmd') == 'logout' && $action){
		header('Location: ' . VIVVO_URL);
		exit;
	}

	$template->assign_ref('DUMP_MESSAGES', $dm->_dump_pool);

	if ($sm->user){
		$template->assign('CURRENT_USER', $sm->user);
	}
	$sm->set_content();

	full_page_cache_replace_boxes($sm, $uncached_boxes);

	default_header($sm);
	if ($um->isset_param('template_output')) {
		get_template_output($sm);
	}
	$output = $sm->get_output();

	if (defined('VIVVO_RSS_FEED')) {
		header('Content-Type: text/xml; charset=UTF-8');
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . $output;
	} else {
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
		echo str_replace('$generation_time$', time() - VIVVO_START_TIME . 's', $output);
	}
	full_page_cache_save($sm, $output);
	ob_end_flush();

#EOF