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
 * @see  'vivvo_plugin.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_admin_view.php');

	/**
	 * Plugin class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class templates_admin_view extends vivvo_admin_view{
		var $views = array('list_output', 'leftnav', 'editor');

		var $_template_root = 'templates/templates/';

		function check_privileges() {
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->is_admin();
		}

		function handle_action(){
			vivvo_lite_site::get_instance()->execute_action();
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header (){
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/tree.js');
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/template.js');
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();

			$template->set_template_file(VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/templates/frame.xml');

			$um = $sm->get_url_manager();

			if ($um->isset_param('template_set')) {
				$template->assign('template_set', $um->get_param('template_set'));
				$template->assign('template_set_dir', strval($um->get_param('template_set') . '/'));
				$template->assign('theme', strval(''));
			} elseif ($um->isset_param('theme')) {
				$template->assign('theme', $um->get_param('theme'));
				$template->assign('theme_dir', strval($um->get_param('theme') . '/'));
				$template->assign('template_set', strval(''));
			} else {
				$template->assign('template_set', strval('templates'));
				$template->assign('theme', strval('themes'));
				$template->assign('template_set_dir', strval(''));
				$template->assign('theme_dir', strval(''));
			}
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));
			return $template;
		}

		function _editor() {
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();

			$dir = false;
			$file_name = false;

			if ($um->isset_param('FILE_basename') && $um->isset_param('FILE_dir')) {
				$file_name = $um->get_param('FILE_basename');
				$dir = $um->get_param('FILE_dir');
			} else if ($um->isset_param('dir') && $um->isset_param('file')) {
				$dir = $um->get_param('dir');
				$file_name = $um->get_param('file');
			}

			if ($dir && $file_name) {
				$fm = $sm->get_file_manager();
				$file = $fm->create_file($file_name, $dir);
				$template->assign('file', $file);
			}

			$template->set_template_file(VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/templates/editor_frame.xml');
			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $sm->get_output();
			exit;
		}

		function _leftnav() {
			return $this->load_template($this->_template_root . 'tabs.xml');
		}

		function _list_output() {
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$template = $sm->get_template();
			$action = $sm->action_status;

			$dir = false;
			$file_name = false;

			if ($action) {
				if ($um->get_param('cmd') == 'duplicate') {
					$file_name = $um->get_param('FILE_new_filename') .'.'. $um->get_param('FILE_extension');
					$dir =  $um->get_param('FILE_dir');
				} elseif ($um->isset_param('FILE_basename') && $um->isset_param('FILE_dir')) {
					$file_name = $um->get_param('FILE_basename');
					$dir = $um->get_param('FILE_dir');
				}
			} else if ($um->isset_param('dir') && $um->isset_param('file')) {
				$dir = $um->get_param('dir');
				$file_name = $um->get_param('file');
			}

			if ($dir && $file_name) {
				$fm = $sm->get_file_manager();
				$file = $fm->create_file($file_name, $dir);
				$content_template = $this->load_template($this->_template_root . 'form.xml');
				$content_template->assign('file', $file);
			} else {
				$content_template = $this->load_template($this->_template_root . 'intro.xml');
			}

			return $content_template;
		}
	}
?>