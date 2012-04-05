<?php
/* =============================================================================
 * $Revision: 5035 $
 * $Date: 2010-04-19 17:13:50 +0200 (Mon, 19 Apr 2010) $
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
 * @see  'lib/vivvo/frameworkvivvo_admin_view.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_admin_view.php');

	/**
	 * Plugin class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class assets_admin_view extends vivvo_admin_view {

		/**
		 * @var	array	List of available views
		 */
		public $views = array('list_output', 'modal_output', 'leftside_output');

		/**
		 * @var	string	Path to admin templates
		 */
		public $_template_root = 'templates/assets/';

		/**
		 * Checks if user has privileges to access asset manager
		 *
		 * @return	bool
		 */
		public function check_privileges() {
			$user = vivvo_lite_site::get_instance()->user;
			return $user && $user->can('MANAGE_FILES');
		}

		/**
		 * Handles service action passed trough request
		 */
		public function handle_action() {
			vivvo_lite_site::get_instance()->execute_action();
		}

		/**
		 * Loads admin css/javascript
		 */
		public function load_admin_header () {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/assets.css');
			$header->add_script(array(
				VIVVO_URL . 'js/swfupload/swfupload.js',
				VIVVO_URL . 'js/swfupload/swfupload_queue.js',
				VIVVO_URL . 'js/swfupload/fileprogress.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/file_upload.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/assets.js'
			));
		}

		/**
		 * Assigns common template values
		 */
		public function _default_assignments() {

			$template = vivvo_lite_site::get_instance()->get_template();
			$um = vivvo_lite_site::get_instance()->get_url_manager();
			$dm = vivvo_lite_site::get_instance()->get_dump_manager();

			if ($um->isset_param('pg')) {
				$template->assign('pg', $um->get_param('pg'));
			}

			if ($um->isset_param('search_ext')) {
				$template->assign('search_ext', $um->get_param('search_ext'));
			}

			if ($um->isset_param('search_dir')) {
				$search_dir = preg_replace('/^\//', '', $search_dir);
                $search_dir = str_replace('..', '', $um->get_param('search_dir'));
			} else {
				$search_dir = VIVVO_FS_FILES_DIR;
			}
			$search_dir = rtrim($search_dir, '/') . '/';
			$template->assign('search_dir', $search_dir);

			if ($um->isset_param('dir_tree_root')) {
				$dir_tree_root = preg_replace('/^\//', '', $dir_tree_root);
                $dir_tree_root = str_replace('..', '', $um->get_param('dir_tree_root'));
                $dir_tree_root = htmlspecialchars($dir_tree_root, ENT_QUOTES, 'UTF-8');
				$template->assign('dir_tree_root', $dir_tree_root);
			} else {
				$template->assign('dir_tree_root', strval(VIVVO_FS_FILES_DIR));
			}
		}

		/**
		 * Default view
		 */
		public function _default_view() {

			$template = vivvo_lite_site::get_instance()->get_template();

			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));

			return $template;
		}

		/**
		 * Left-side (tabs) view
		 */
		public function _leftside_output(){
			return $this->load_template($this->_template_root . 'tabs.xml');
		}

		/**
		 * Content view
		 */
		public function _list_output($assign_list = true) {

			$um = vivvo_lite_site::get_instance()->get_url_manager();

			$template = $this->load_template($this->_template_root . 'list.xml');

			if ($um->isset_param('search_limit')) {
				$search_limit = $um->get_param('search_limit') << 0;
			} else {
				$search_limit = 10;
			}

			if ($um->isset_param('search_sort_by')) {
				$search_sort_by = $um->get_param('search_sort_by');
			} else {
				$search_sort_by = 'filename';
			}

			if ($um->get_param('search_order')) {
				$search_order = $um->get_param('search_order');
			} else {
				$search_order = 'ascending';
			}

			$search_sort_order = $search_sort_by . '.' . $search_order;

			$template->assign('search_limit', $search_limit);
			$template->assign('search_sort_by', $search_sort_by);
			$template->assign('search_order', $search_order);
			$template->assign('search_sort_order', $search_sort_order);

			$true = true;

			if ($um->isset_param('list_output')) {
				$template->assign('list_output', $true);
			}

			if ($um->get_param('search_do_advanced')) {

				$template->assign('search_do_advanced', $true);

				foreach (array('filename', 'exact_name', 'info', 'keywords', 'date_after', 'subfolders') as $param) {
					$param = 'search_' . $param;
					if ($um->isset_param($param)) {
						$template->assign($param, $um->get_param($param));
					}
				}
			}

			return $template;
		}

		/**
		 * Modal window view
		 */
		public function _modal_output() {

			$um = vivvo_lite_site::get_instance()->get_url_manager();

			$template = $this->load_template($this->_template_root . 'modal.xml');

			$filename = $um->get_param('file');

			require_once(VIVVO_FS_FRAMEWORK . '/asset_files.class.php');

			$asset_list = new AssetFiles_list(vivvo_lite_site::get_instance());
			$file = $asset_list->get_asset_by_fullname($filename);

			if ($file) {
				$template->assign('file', $file);
			}

			return $template;
		}
	}
?>