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

	function check_close_site($sm = null){
		$sm != null or $sm = vivvo_lite_site::get_instance();
		if (VIVVO_CLOSE_SITE == 1 && !($sm->user && $sm->user->is_admin())) {
			$template = $sm->get_template();
			$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/close_site.tpl');
			echo $template->get_output();
			exit;
		}
	}

	function default_header($sm = null){
		$sm != null or $sm = vivvo_lite_site::get_instance();
		define('VIVVO_JS_COMPRESOR', VIVVO_STATIC_URL . 'compress.php?js,');

		$header = $sm->get_header_manager();

		$header->add_script(VIVVO_URL . 'js/framework/prototype.js');
		$header->add_script(VIVVO_URL . 'js/control_tabs.js');
		$header->add_script(VIVVO_URL . 'js/functions.js');

		if (VIVVO_MODULES_FEED == 1) {
			$header->add_rss(switch_format('rss', VIVVO_ABSOLUTE_URL), VIVVO_WEBSITE_TITLE);
		}

		$sm->set_theme();

		if (file_exists(VIVVO_FS_THEME_ROOT . str_replace(VIVVO_URL, '', VIVVO_THEME) . '/css/compress.php')){
			define ('VIVVO_CSS_COMPRESOR', VIVVO_THEME . 'css/compress.php?css,');
		}
	}

	function get_template_output($sm = null) {
		$sm != null or $sm = vivvo_lite_site::get_instance();
		$um = $sm->get_url_manager();
		$template = $sm->get_template();

		$template_file = $um->get_param('template_output');

		$template_file = preg_replace('/[^a-zA-Z0-9\_\-\/]/', '', $template_file);
		if (file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $template_file . '.tpl')){
			$box_template =& new template($sm, $template);
			if (!empty($_GET)){
				foreach ($_GET as $k => $v){
					if ($k != 'template_output'){
						$box_template->assign($k, htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
					}
				}
			}
			$action = $sm->action_status;
			$box_template->assign('ajax_output', intval(1));
			$box_template->assign('action', intval($action));
			$box_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . $template_file . '.tpl');
			$box_template->assign('DUMP_MESSAGES', $sm->get_dump_manager()->_dump_pool);

			echo $box_template->get_output();
		}

		exit();
	}

	function main_nav_plugin_callback($template, $params, &$hook, $my_params) {
		$sm = vivvo_lite_site::get_instance();
		return $sm->user && $sm->user->can('MANAGE_PLUGIN', $my_params['name']);
	}

	/**
	 * Filter function used by compress.php to minify contents of javascript files
	 *
	 * @param	string	$content
	 * @return	$content
	 */
	function jsmin_compress_filter($content) {

		class_exists('JSMin') or require VIVVO_FS_INSTALL_ROOT . 'lib/minify/JSMin.php';

		try {
			$content = JSMin::minify($content);
		} catch (Exception $e) {
		}

		return $content;
	}

	/**
	 * Filter function used by compress.php to minify contents of css files
	 *
	 * @param	string	$content
	 * @return	$content
	 */
	function cssmin_compress_filter($content) {

		class_exists('Minify_CSS_Compressor') or require VIVVO_FS_INSTALL_ROOT . 'lib/minify/Minify/CSS/Compressor.php';

		return Minify_CSS_Compressor::process($content);
	}

#EOF