<?php
/* =============================================================================
 * $Revision: 5385 $
 * $Date: 2010-05-25 11:51:09 +0200 (Tue, 25 May 2010) $
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

	if (ini_get('zlib.output_compression')) {
		ob_start();
	} elseif (function_exists('ob_gzhandler')) {
		ob_start('ob_gzhandler');
	} else {
		ob_start();
	}

	ini_set('memory_limit', '64M');

	require_once dirname(__FILE__) . '/../conf.php';

	if (!defined('VIVVO_FS_INSTALL_ROOT')) {
		$url_parts = explode('/', rtrim($_SERVER['REQUEST_URI'], '/'));
		if (end($url_parts) == basename(dirname(__FILE__))) {
			array_pop($url_parts);
		} else {
			$url_parts = array_slice($url_parts, 0, count($url_parts) - 2);
		}
		header('Location: http://' . $_SERVER['HTTP_HOST'] . implode('/', $url_parts) . '/installer/install.html');
		exit;
	}

	defined('VIVVO_COOKIE_PREFIX') or define('VIVVO_COOKIE_PREFIX', 'Vivvo');

	if (isset($_REQUEST['VSID'])) {
		$_COOKIE[VIVVO_COOKIE_PREFIX . 'SessionId'] = $_REQUEST['VSID'];
		$_REQUEST[VIVVO_COOKIE_PREFIX . 'SessionId'] = $_REQUEST['VSID'];
	}

    if (isset($_REQUEST['VUH'])) {
        $_COOKIE[VIVVO_COOKIE_PREFIX . 'UserHash'] = $_REQUEST['VUH'];
        $_REQUEST[VIVVO_COOKIE_PREFIX . 'UserHash'] = $_REQUEST['VUH'];
    }

	defined('VIVVO_RELATIVE_URL') or define('VIVVO_RELATIVE_URL', preg_replace('|^[^:]+://[^/]+|', '', rtrim(VIVVO_URL, '/') . '/'));

	require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php';
	require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/box/vivvo_box.php';

	define('VIVVO_ADMIN_MODE', true);
	define('VIVVO_JS_COMPRESOR', VIVVO_URL . 'compress.php?js,');
	define('VIVVO_CSS_COMPRESOR', VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/compress.php?css,');

	$sm = vivvo_lite_site::get_instance();
	$template = $sm->get_template();
	$sm->get_categories();
	$um = $sm->get_url_manager();
	$dm = $sm->get_dump_manager();



	// Set Admin lang
	$admin_lang = $sm->get_lang();
	if ($um->isset_param('admin_lang') && file_exists(VIVVO_FS_LANG_ROOT . 'lang/admin/' . $um->get_param('admin_lang') . '.php')) {
		$admin_lang->load_lang($um->get_param('admin_lang'), 'lang/admin/', 'admin');
		$_SESSION['vivvo']['admin_lang'] = $um->get_param('admin_lang');
	} elseif (!empty($_SESSION['vivvo']['admin_lang'])) {
		$admin_lang->load_lang($_SESSION['vivvo']['admin_lang'], 'lang/admin/', 'admin');
	} else {
		$admin_lang->load_lang('vietnamese', 'lang/admin/', 'admin');
		$_SESSION['vivvo']['admin_lang'] = 'vietnamese';
	}
	define('VIVVO_ADMIN_LANG', $_SESSION['vivvo']['admin_lang']);
	$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	define('CURRENT_URL', $url);

	// Add default CSS and JavaScript
	$header = $sm->get_header_manager();
	$header->add_css(array(
		VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/layout.css',
		VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/dashboard.css',
		VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/forms.css'
	));
	$header->add_script(array(
		VIVVO_URL . 'js/framework/prototype.js',
		VIVVO_URL . 'js/framework/protohover.js',
		VIVVO_URL . 'js/control_tabs.js',
		VIVVO_URL . 'js/framework/scriptaculous.js',
		VIVVO_URL . 'js/framework/builder.js',
		VIVVO_URL . 'js/framework/effects.js',
		VIVVO_URL . 'js/framework/dragdrop.js',
		VIVVO_URL . 'js/framework/controls.js',
		VIVVO_URL . 'js/framework/slider.js',
		VIVVO_URL . 'js/cookies.js',
		VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/vivvo.js'
	));

	// Use Chrome Frame if available
	header('X-UA-Compatible: chrome=1');

	// Login
	if ($sm->user === false || $um->get_param('action') == 'login') {
		$view = $sm->get_admin_view('login');
		if ($view) {
			$view->run();
		}
		exit;
	}

	// Check access
	if (!$sm->user->can('ACCESS_ADMIN') || !in_allowed_ip_adresses($sm->user->ip)) {
		go_404();
	}

	define('SUHOSIN_SESSION_ENCRYPT_ENABLED', ini_get_bool('suhosin.session.encrypt') && ini_get_bool('suhosin.session.cryptua'));
	define('CACHE_FOLDER_NOT_WRITABLE', !(is_dir(VIVVO_FS_INSTALL_ROOT . 'cache') && is_writable(VIVVO_FS_INSTALL_ROOT . 'cache')));
	define('INSTALLER_DIR_PRESENT', is_dir(VIVVO_FS_INSTALL_ROOT . 'installer'));

	// some info to help debugging
	if ($sm->user->is_admin() and CURENT_ADMIN_VIEW == 'dashboard') {
		if (!empty($_GET['phpinfo'])) {
			phpinfo();
			exit;
		} elseif (!empty($_GET['dump_defines'])) {
			header('Content-Type: text/plain; charset=UTF-8');
			print_r(array_reverse(get_defined_constants(true)));
			exit;
		}
	}

	$template->set_template_file(VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/frame.xml');
	$template->assign('current_user', $sm->user);

	// Personal settings
	if (isset($_COOKIE['__CJ_adminPersonalSettings' . $sm->user->get_id()])) {
        $my_setting = $_COOKIE['__CJ_adminPersonalSettings' . $sm->user->get_id()];
        $my_setting = ereg_replace('%u([[:alnum:]]{4})', '&#x\1;',$my_setting);
        $my_setting = urldecode($my_setting);
        $my_setting = stripslashes($my_setting);
		$my_setting = json_decode($my_setting, true);
	} else {
		$my_setting = array('adminMenuHide' => 0);
	}
	$my_setting['adminMenuHide'] = (empty($my_setting['adminMenu']) && $my_setting['adminMenuHide'] == '1') ? 1 : 0;
	$template->assign('PERSONAL_SETTINGS', $my_setting);

	// Restore dump
	$dm->restore_dump(md5($url));
	$template->assign_ref('DUMP_MESSAGES', $dm->_dump_pool);

	// Load admin view
	if (defined('CURENT_ADMIN_VIEW')) {
		$view = $sm->get_admin_view(CURENT_ADMIN_VIEW);
		if ($view) {
			$view->run();
		}
		ob_end_flush();
	}

	function ini_get_bool($name) {
		return ($value = ini_get($name)) && in_array(strtolower($value), array('1', 'on', 'yes'));
	}
?>