<?php
/* =============================================================================
 * $Revision: 4856 $
 * $Date: 2010-04-01 14:57:16 +0200 (Thu, 01 Apr 2010) $
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
	class plugins_admin_view extends vivvo_admin_view{
		var $views = array('list_output', 'leftnav', 'install', 'uninstall');

		var $_template_root = 'templates/plugins/';

		function check_privileges(){
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->can('MANAGE_PLUGIN');
		}

		function handle_action() {
			vivvo_lite_site::get_instance()->execute_action();
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header() {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css');
			$header->add_script(array(
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/plugins.js'
			));
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->_leftnav());
			return $template;
		}

		function _list_output() {
			$sm = vivvo_lite_site::get_instance();
			$pm = $sm->get_plugin_manager();
			$content_template = $this->load_template($this->_template_root . 'list.xml');

            $plugin_list = $pm->get_all_plugins();

			usort($plugin_list, create_function('$a,$b', 'return $a["name"] > $b["name"] ? 1 : -1;'));

			$content_template->assign('plugin_manager', $pm);
			$content_template->assign('plugins', $plugin_list);

			return $content_template;
		}

		function _leftnav() {
			$sm = vivvo_lite_site::get_instance();
			$pm = $sm->get_plugin_manager();
			$content_template = $this->load_template($this->_template_root . 'tabs.xml');
			$content_template->assign('plugins', $pm->get_plugin_menu_item());

			return $content_template;
		}

		function _install() {
			$sm = vivvo_lite_site::get_instance();
			if (!$sm->user or !$sm->user->is_admin()) {
				exit;
			}
			$pm = $sm->get_plugin_manager();
			$um = $sm->get_url_manager();
			$template = $sm->get_template();

			if ($um->isset_param('plugin') && !isset($pm->plugins[$um->get_param('plugin')])) {
				$pm->set_fs_plugins();
				$plugin = $pm->get_fs_plugin_definiton($um->get_param('plugin'));
				if ($plugin !== false) {
				    $template->assign('PLUG_IN_NAME', $pm->fs_plugins[$um->get_param('plugin')]['name']);
					$pm->load_admin_lang($um->get_param('plugin'), true);
					echo $plugin->install($sm, $um->get_param('step'));
				}
			}
			exit;
		}

		function _uninstall() {
			$sm = vivvo_lite_site::get_instance();
			if (!$sm->user or !$sm->user->is_admin()) {
				exit;
			}
			$sm = vivvo_lite_site::get_instance();
			$pm = $sm->get_plugin_manager();
			$um = $sm->get_url_manager();
			$template = $sm->get_template();

			if ($um->isset_param('plugin') && isset($pm->plugins[$um->get_param('plugin')])) {
				$plugin = $pm->get_plugin_definiton($um->get_param('plugin'));
				if ($plugin !== false){
				    $template->assign('PLUG_IN_NAME', $pm->plugins[$um->get_param('plugin')]['name']);
					$plugin->load_admin_lang();
					ui_hooks::unregister('plugin_' . $um->get_param('plugin') . '_admin_main_nav_hook');
					echo $plugin->uninstall($sm, $um->get_param('step'));
				}
			}
			exit;
		}
	}
?>