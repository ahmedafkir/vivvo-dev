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
	class ajax_admin_view extends vivvo_admin_view{
		var $views = array('user_filter', 'ajax_dump', 'article_list', 'upload_dialog', 'file_picker', 'cron_task', 'tag_list' , 'falsh_upload', 'user_list', 'timezone', 'file_tooltip', 'backup_files_list', 'xhr', 'main_nav');

		var $_template_root = 'templates/';

		var $_default_view = '_ajax_dump';

		function handle_action(){
			vivvo_lite_site::get_instance()->execute_action();
		}

		function _default_assignments() {
			$via_xhr = !empty($_SERVER['X_HTTP_REQUEST_WITH']) && $_SERVER['X_HTTP_REQUEST_WITH'] == 'XMLHttpRequest';
			vivvo_lite_site::get_instance()->get_template()->assign('via_xhr', $via_xhr);
		}

		function _ajax_dump() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->set_template_file($this->_template_root . 'components/ajax_dump.xml');

			return $template;
		}

		function _article_list(){
			$um = vivvo_lite_site::get_instance()->get_url_manager();
			$content_template =& $this->load_template($this->_template_root . 'components/article_list.xml');
			$params = array('search_query' => $um->get_param('search_query'), 'search_title_only'=> 1);
			$content_template->assign('comments_articles_params', $params);

			return $content_template;
		}

		function _user_filter(){
			$um = vivvo_lite_site::get_instance()->get_url_manager();
			$content_template =& $this->load_template($this->_template_root . 'components/user_filters_list.xml');
			$content_template->assign('user_filters_section', $um->get_param('section'));
			$content_template->assign('list_output', intval(1));

			return $content_template;
		}

		function _upload_dialog(){
			$um = vivvo_lite_site::get_instance()->get_url_manager();

			$content_template =& $this->load_template($this->_template_root . 'components/file_uploader.xml');

			if ($um->get_param('dir_tree_root')){
                $dir_tree_root = str_replace('..', '', $um->get_param('dir_tree_root'));
                $dir_tree_root = preg_replace('/^\//', '', $dir_tree_root);
                $dir_tree_root = htmlspecialchars($dir_tree_root, ENT_QUOTES, 'UTF-8');
				$content_template->assign('dir_tree_root', $dir_tree_root);
			}else{
				$content_template->assign('dir_tree_root', strval('files'));
			}

			return $content_template;
		}

		function _file_picker(){
			$um = vivvo_lite_site::get_instance()->get_url_manager();

			$content_template =& $this->load_template($this->_template_root . 'components/file_picker.xml');

			if ($um->get_param('dir_tree_root')){
                $dir_tree_root = str_replace('..', '', $um->get_param('dir_tree_root'));
                $dir_tree_root = preg_replace('/^\//', '', $dir_tree_root);
                $dir_tree_root = htmlspecialchars($dir_tree_root, ENT_QUOTES, 'UTF-8');
				$content_template->assign('dir_tree_root', $dir_tree_root);
			}else{
				$content_template->assign('dir_tree_root', strval('files'));
			}

			if ($um->get_param('pg')){
				$content_template->assign('pg', $um->get_param('pg'));
			}

			if ($um->get_param('search_ext')){
				$content_template->assign('search_ext', $um->get_param('search_ext'));
			}

			if ($um->get_param('search_dir')){
                $search_dir = str_replace('..', '', $um->get_param('search_dir'));
                $search_dir = preg_replace('/^\//', '', $search_dir);
				$content_template->assign('search_dir', $search_dir);
			}

			return $content_template;
		}

		function _cron_task() {
			$content_template =& $this->load_template($this->_template_root . 'components/cron_task.xml');

			return $content_template;
		}

		function _tag_list() {
			$content_template =& $this->load_template($this->_template_root . 'components/tag_list.xml');
			$um = vivvo_lite_site::get_instance()->get_url_manager();

			if ($um->isset_param('search_starting_with') && $um->get_param('search_starting_with') != ''){
				$content_template->assign('starting_with', $um->get_param('search_starting_with'));
			}

			return $content_template;
		}

		function _falsh_upload() {
			$sm = vivvo_lite_site::get_instance();
			$content_template =& $this->load_template($this->_template_root . 'components/flash_upload_response.xml');
			$action = $sm->action_status;

			if($action){
				if ($sm->action_response){
					$content_template->assign('action', $sm->action_response);
				}else{
					$content_template->assign('action', strval('Success'));
				}
			}else{
				$content_template->assign('action', intval(0));
			}

			return $content_template;
		}

		function _timezone(){
			$timezone_abbreviations = DateTimeZone::listIdentifiers();
			$um = vivvo_lite_site::get_instance()->get_url_manager();

			$timezone_list = array();
			$name = $um->get_param('name');
			if ($name != ''){
				foreach ($timezone_abbreviations as $tz){
					if (stripos($tz, $name) !== false){
						$timezone_list[] = $tz;
					}
					if (count($timezone_list) == 5){
						break;
					}
				}
			}else{
				for($i = 0; $i++; $i < 5){
					$timezone_list[$i] = $timezone_abbreviations[$i];
				}
			}

			$content_template =& $this->load_template($this->_template_root . 'components/timezone_list.xml');
			$content_template->assign('timezone', $timezone_list);
			return $content_template;
		}

		function _user_list() {
			$um = vivvo_lite_site::get_instance()->get_url_manager();
			$content_template =& $this->load_template($this->_template_root . 'components/user_list.xml');
			$content_template->assign('name', $um->get_param('name'));
			return $content_template;
		}

		function _file_tooltip() {

			$um = vivvo_lite_site::get_instance()->get_url_manager();

			require_once VIVVO_FS_ROOT . 'lib/vivvo/framework/asset_files.class.php';

			$asset_files = new AssetFiles_list();

			$content_template =& $this->load_template($this->_template_root . 'components/file_picker_tooltip.xml');
			$content_template->assign( 'file', $asset_files->get_asset_by_id( $um->get_param('file_id') ) );

			return $content_template;
		}

		function _backup_files_list() {
			return $this->load_template($this->_template_root . 'maintenance/backup_files.xml');
		}

		function _xhr() {
			$um = vivvo_lite_site::get_instance()->get_url_manager();
			if ($um->isset_param('xhr_template_output')) {
				return $this->load_template($this->_template_root . $um->get_param('xhr_template_output'));
			} else {
				return $this->load_template($this->_template_root . 'components/ajax_dump.xml');
			}
		}

		function _main_nav() {

			$sm = vivvo_lite_site::get_instance();

			if ($sm->check_token()) {

				$um = $sm->get_url_manager();

				$id = $um->get_param('id');
				$title = $um->get_param('title');
				$href = $um->get_param('href');
				$action = $um->get_param('action');
				$plugin_name = $um->get_param('plugin_name');

				if ($id and $title and $href and $plugin_name and $action) {

					if ($sm->user and $sm->user->can('MANAGE_PLUGIN', $plugin_name)) {

						$hook_name = 'plugin_' . $plugin_name . '_admin_main_nav_hook';

						if ($action == 'register') {

							ui_hooks::register($hook_name, 'admin_main_nav', array(
								'id' => $id,
								'href' => $href,
								'title' => $title
							), array(
								'file' => 'lib/vivvo/functions.php',
								'function' => 'main_nav_plugin_callback',
								'params' => array(
									'name' => $plugin_name
								)
							));

						} elseif ($action == 'unregister') {

							ui_hooks::unregister($hook_name);

						} else {
							$error = 'NO_ACTION';
						}

						header('X-Vivvo-Action-Status: 1');
					} else {
						$error = 'NO_PRIVILEGES';
					}
				} else {
					$error = 'INVALID_TOKEN';
				}

				if (!empty($error)) {
					header('X-Vivvo-Action-Status: 0');
					header('X-Vivvo-Action-Error: ' . $error);
				} else {
					$sm->get_configuration()->load_configuration(true);
				}

				return $this->load_template($this->_template_root . 'components/menu.xml');
			}
		}
	}
?>