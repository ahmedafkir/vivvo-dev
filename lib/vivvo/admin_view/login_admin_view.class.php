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
	class login_admin_view extends vivvo_admin_view{
		var $views = array();

		var $_template_root = 'templates/login/';

		function handle_action(){
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();

			if ($action) {
				$dm->store_dump(md5(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'index.php'));
				header('Location:index.php');
				exit();
			}
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$dm->restore_dump(md5(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'index.php'));
			$template->set_template_file(VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/login/frame.xml');

			if (VIVVO_USER_SOURCE != 'vivvo@localhost'){
				$template->assign('FOREIGN_LOGIN_TEMPLATE', $sm->_user_manager->get_admin_login_tempalte());
			}

			$template->assign('DUMP_MESSAGES', $dm->_dump_pool);
		}

		function _default_view() {
			return vivvo_lite_site::get_instance()->get_template();
		}
	}
?>