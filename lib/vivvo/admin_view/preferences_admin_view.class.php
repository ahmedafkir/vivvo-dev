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
	class preferences_admin_view extends vivvo_admin_view {

		var $views = array('list_output');

		var $_template_root = 'templates/preferences/';

		function check_privileges(){
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->is_admin();
		}

		function handle_action() {
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();
			$action = $sm->execute_action();

			if ($action && !$um->isset_param('list_output')) {
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
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/preferences.js'
			));
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$template =& $sm->get_template();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			if ($um->isset_param('layout')){
				$section_title = 'layout';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/layout.xml';
			} elseif ($um->isset_param('caching')) {
				$section_title = 'caching';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/caching.xml';
			} elseif ($um->isset_param('articles')) {
				$section_title = 'articles';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/articles.xml';
			} elseif ($um->isset_param('comments')) {
				$section_title = 'comments';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/comments.xml';
			} elseif ($um->isset_param('categories')) {
				$section_title = 'categories';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/categories.xml';
			} elseif ($um->isset_param('email')) {
				$section_title = 'email';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/email.xml';
			} elseif ($um->isset_param('analytics')) {
				$section_title = 'analytics';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/analytics.xml';
				$template->assign('profiles_list', vivvo_ga::factory()->getProfiles());
			} elseif ($um->isset_param('users')) {
				$section_title = 'users';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/users.xml';

				$user_manager = $sm->get_user_manager();
				$gl = $user_manager->get_group_list();
				$groups = $gl->get_all_groups();
				$template->assign('group_list', $groups);
			} else {
				$section_title = 'general';
				$section_file = VIVVO_FS_ADMIN_DIR . 'templates/preferences/general.xml';
			}

			$template->assign('section_title', $section_title);
			$template->assign('section_file', $section_file);
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->load_template($this->_template_root . 'frame.xml'));
			$template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));
			return $template;
		}

		function _list_output() {
			return $this->load_template($this->_template_root . 'frame.xml');
		}
	}
?>