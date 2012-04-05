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
	class users_admin_view extends vivvo_admin_view{
		var $views = array('list_output', 'group_output', 'group_list');

		var $_template_root = 'templates/users/';

		function check_privileges(){
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->is_admin();
		}

		function handle_action() {
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();

			if ($action && !$um->isset_param('list_output')){
				$dm->store_dump(md5(CURRENT_URL));
				header('Location: ' . VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . CURRENT_ADMIN_VIEW_FILE . preg_replace('/^[^\?]+/', '', CURRENT_URL));
				exit;
			}
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header() {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css');
			$header->add_script(array(
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/users.js',
				VIVVO_URL . 'js/tooltips.js'
			));
		}

		function _default_assignments() {

			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$user_manager = $sm->get_user_manager();
			$gl = $user_manager->get_group_list();

			$groups = $gl->get_all_groups();
			$template->assign('group_list', $groups);

			$um = $sm->get_url_manager();

			if ($um->isset_param('search_activated')) {
				$template->assign('search_activated', $um->get_param('search_activated'));
			}
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output(false));
			$template->assign_template('left', $this->_leftnav());
			return $template;
		}

		function _list_output($list_output = true){
			$content_template = $this->load_template($this->_template_root . 'list.xml');
			$sm = vivvo_lite_site::get_instance();
			$user_manager = $sm->get_user_manager();
			$users = $user_manager->get_user_list();

			$params = $users->get_search_params_from_url($sm);

			if (isset($params['search_options']['search_user_type']) && !empty($params['search_options']['search_user_type']) && is_numeric($params['search_options']['search_user_type'])){
                $gl = $user_manager->get_group_list();
                $current_group = $gl->get_group_by_id($params['search_options']['search_user_type']);
                $content_template->assign('current_search_group', $current_group);
			}

			if ($list_output){
				$content_template->assign('list_output', strval(1));
			}

			$content_template->assign('users_params', $params);
			$content_template->assign('search_limit', $params['search_limit']);
			$content_template->assign('search_sort_by', strval($params['search_sort_by'] . '.' . $params['search_order']));

			return $content_template;
		}

		function _leftnav() {
			$content_template = $this->load_template($this->_template_root . 'tabs.xml');
			$content_template->assign('user_filters_section', strval('user'));
			return $content_template;
		}

		function _group_output() {
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$user_manager = $sm->get_user_manager();
			$gl = $user_manager->get_group_list();
			$group = $gl->get_group_by_id($um->get_param('search_gid'));

			$content_template = $this->load_template($this->_template_root . 'group_content.xml');
			$content_template->assign('group', $group);

			$pm = $sm->get_plugin_manager();
			$content_template->assign('plugins', $pm->get_all_plugins());

			return $content_template;
		}

		function _user_list() {
			$content_template = $this->load_template(VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/components/user_list.xml');
			$content_template->assign('name', $um->get_param('name'));
			return $content_template;
		}

		function _group_list() {
			return $this->load_template($this->_template_root . 'group_list.xml');
		}
	}
?>