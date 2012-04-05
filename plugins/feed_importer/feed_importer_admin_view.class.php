<?php
/* =============================================================================
 * $Revision: 5409 $
 * $Date: 2010-05-27 17:57:42 +0200 (Thu, 27 May 2010) $
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
	 * @see  'lib/vivvo/framework/vivvo_admin_view.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_admin_view.php');

	/**
	 * Feed Importer Admin View class
	 *
	 * @copyright	Spoonlabs
	 * @version		$Revision: 5409 $
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class feed_importer_admin_view extends vivvo_admin_view {

		/**
		 * @var	array	List of available views
		 */
		public $views = array('list_output', 'leftnav', 'force_import');

		/**
		 * @var string	Plugin identifier
		 */
		public $_plugin = 'feed_importer';

		/**
		 * @var string	Path to template files
		 */
		public $_template_root = 'admin/templates/';

		/**
		 * Checks if user has privileges to access plugin
		 *
		 * @return	bool
		 */
		public function check_privileges() {
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->can('MANAGE_PLUGIN', $this->_plugin);
		}

		/**
		 * Handles service action passed trough request
		 */
		public function handle_action() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();

			if ($action and (empty($_SERVER['HTTP_X_REQUESTED_WITH']) or $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')) {

				$url = VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . CURRENT_ADMIN_VIEW_FILE;

				$dm->store_dump(md5($_SERVER['HTTP_HOST'] . $url));

				header('Location: ' . $url);
				exit;
			}
		}

		/**
		 * Loads admin css/javascript
		 */
		public function load_admin_header() {

			$sm = vivvo_lite_site::get_instance();
			$header = $sm->get_header_manager();
			$pm = $sm->get_plugin_manager();

			$header->add_script(array(
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js',
				VIVVO_URL . $pm->plugins['feed_importer']['root_dir'] . 'admin/js/feed_admin.js'
			));
		}

		/**
		 * Default view
		 */
		public function _default_view() {

			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->_leftnav());

			return $template;
		}

		/**
		 * Tabs view
		 */
		public function _leftnav() {
			return $this->load_template($this->_template_root . 'tabs.xml');
		}

		/**
		 * Feed list view
		 */
		public function _list_output($assign_list = true) {

			$pm = vivvo_lite_site::get_instance()->get_plugin_manager();

			$content_template = $this->load_template($this->_template_root . 'list.xml');

			require_once dirname(__FILE__) . '/core/Feeds.class.php';

			$feed_list = new Feeds_list;
			$feed_list->search(array());

			$content_template->assign('feed_list', $feed_list->list);

			return $content_template;
		}

		public function _force_import() {

			require_once dirname(__FILE__) . '/tasks/auto_import_feeds.php';

			define('VIVVO_FEED_IMPORTER_ADMIN_VIEW', true);

			auto_import_feeds(vivvo_lite_site::get_instance());

			header('Content-Type: application/json');

			exit;
		}
	}

#EOF