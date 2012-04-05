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
	class comments_admin_view extends vivvo_admin_view {

		var $views = array('list_output');

		var $_template_root = 'templates/comments/';

		function check_privileges() {
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->can('MODERATE_COMMENTS');
		}

		function handle_action() {
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();
			$action = $sm->execute_action();
			if ($action && !$um->isset_param('list_output')) {
				$dm->store_dump(md5(CURRENT_URL));
				header('Location: ' . VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . CURRENT_ADMIN_VIEW_FILE);
				exit();
			}
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header (){
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css');
			$header->add_script(array(
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/comments.js'
			));
		}

		function _default_assignments() {

			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Comments.class.php');

			$params = Comments_list::get_search_params_from_url($sm);

			$template->assign('comments_params', $params);
			$template->assign('search_limit', $um->get_param('search_limit'));
			$template->assign('search_sort_by', strval($params['search_sort_by'] . '.' . $params['search_order']));
			$template->assign('user_filters_section', strval('comment'));
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));

			return $template;
		}

		function _list_output() {
			$um = vivvo_lite_site::get_instance()->get_url_manager();
			$template = $this->load_template($this->_template_root . 'list.xml');
			foreach (array('search_article_id', 'search_author_name') as $filter) {
				if ($um->isset_param($filter)) {
					$template->assign($filter, $um->get_param($filter));
				}
			}
			return $template;
		}
	}
?>