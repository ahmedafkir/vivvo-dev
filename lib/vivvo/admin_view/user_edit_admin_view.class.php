<?php
/* =============================================================================
 * $Revision: 4877 $
 * $Date: 2010-04-06 10:49:38 +0200 (Tue, 06 Apr 2010) $
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
	class user_edit_admin_view extends vivvo_admin_view {

		var $views = array();

		var $_template_root = 'templates/user_edit/';

		function handle_action() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			if ($sm->execute_action()) {

				is_array($_SESSION['vivvo']) or $_SESSION['vivvo'] = array();
				is_array($_SESSION['vivvo']['save_plus']) or $_SESSION['vivvo']['save_plus'] = array();

				$_SESSION['vivvo']['save_plus']['user_edit'] = array();

				switch ($save_action = $um->get_param('save_action')) {

					case 'continue':
						$url = VIVVO_FS_ADMIN_DIR . CURRENT_ADMIN_VIEW_FILE .'?search_id=' . $um->get_param('search_id');
					break;

					case 'add':
						$url = VIVVO_FS_ADMIN_DIR . CURRENT_ADMIN_VIEW_FILE;
						$_SESSION['vivvo']['save_plus']['user_edit']['activated'] = $um->get_param('USER_activated');
						$_SESSION['vivvo']['save_plus']['user_edit']['groups'] = $um->get_param('USER_groups');
					break;

					default:
						$save_action = 'return';
						$url = VIVVO_FS_ADMIN_DIR . 'users.php';
					break;
				}

				$_SESSION['vivvo']['save_plus']['user_edit']['save_action'] = $save_action;

				$dm->store_dump(md5($_SERVER['HTTP_HOST'] . VIVVO_RELATIVE_URL . $url));

				header('Location: ' . VIVVO_RELATIVE_URL . $url);
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
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/save_plus.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/users_edit.js',
				VIVVO_URL . 'js/tooltips.js'
			));
		}

		function _default_assignments() {

			$sm = vivvo_lite_site::get_instance();
			$template =& $sm->get_template();
			$um = $sm->get_url_manager();
			$action = $sm->action_status;

			if (isset($_SESSION['vivvo'], $_SESSION['vivvo']['save_plus'], $_SESSION['vivvo']['save_plus']['user_edit'])) {

				!empty($_SESSION['vivvo']['save_plus']['user_edit']['save_action']) or $_SESSION['vivvo']['save_plus']['user_edit']['save_action'] = 'return';

				$template->assign('save_action', $_SESSION['vivvo']['save_plus']['user_edit']['save_action']);

				$um = $sm->get_url_manager();

				$um->set_param('save_action', $_SESSION['vivvo']['save_plus']['user_edit']['save_action']);

				empty($_SESSION['vivvo']['save_plus']['user_edit']['activated']) or $um->set_param('USER_activated', $_SESSION['vivvo']['save_plus']['user_edit']['activated']);
				empty($_SESSION['vivvo']['save_plus']['user_edit']['groups']) or $um->set_param('USER_groups', $_SESSION['vivvo']['save_plus']['user_edit']['groups']);
			}

			$params = array();
			if (!$sm->user->is_admin()) {
				$params['search_id'] = $sm->user->get_id();
			} else {
				$params['search_id'] = $um->get_param('search_id');
			}

			$template->set_template_file(VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/user_edit/frame.xml');

			if ($um->isset_param('action') && $action === false or ($um->get_param('save_action') == 'add' and !$um->get_param('search_id'))) {
				$in_user = $um->get_param_regexp('/^USER_/');
				$user = $sm->_user_manager->get_user_object();
				$user->populate($in_user);
				if (!$sm->user->is_admin()) {
					$user->set_id($sm->user->get_id());
				}
			} else {
				$user = $sm->_user_manager->get_user_by_id($params['search_id']);
			}

			if ($user) {
				$user->set_privileges();
			}
			$template->assign('user', $user);

			$user_manager = $sm->get_user_manager();
			$gl = $user_manager->get_group_list();
			$groups = $gl->get_all_groups();

			$template->assign('group_list', $groups);
		}

		function _default_view() {
			return vivvo_lite_site::get_instance()->get_template();
		}
	}
?>