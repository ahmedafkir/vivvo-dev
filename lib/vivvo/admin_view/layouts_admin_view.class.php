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
	class layouts_admin_view extends vivvo_admin_view {

		var $views = array('list_output');

		var $_template_root = 'templates/layouts/';

		function check_privileges() {
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->can('MANAGE_PLUGIN');
		}

		function handle_action(){
			vivvo_lite_site::get_instance()->execute_action();
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header() {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(array(
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/modal.css',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css'
			));
			$header->add_script(array(
				VIVVO_URL . 'js/control_modal.js',
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/layout.js'
			));
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$pm = $sm->get_plugin_manager();

			$template->assign('plugin_manager', $pm);
			if (version_compare(PHP_VERSION, '5.2.0', '>=') && class_exists('ZipArchive')) {
				$template->assign('INSTALL_CAPABLE', intval(1));
			} else {
				$template->assign('INSTALL_CAPABLE', intval(0));
			}
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->load_template($this->_template_root . 'list.xml'));
			$template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));
			return $template;
		}

		function _list_output() {
			return $this->load_template($this->_template_root . 'list.xml');
		}
	}
?>