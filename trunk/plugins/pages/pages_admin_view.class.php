<?php
/* =============================================================================
 * $Revision: 5079 $
 * $Date: 2010-04-22 17:07:18 +0200 (Thu, 22 Apr 2010) $
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
	 * @version		$Revision: 5079 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class pages_admin_view extends vivvo_admin_view{
		var $views = array('list_output','leftnav');

		var $_plugin = 'pages';

		var $_template_root = 'admin/templates/';

		function check_privileges() {
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->can('MANAGE_PLUGIN', 'pages');
		}

		function handle_action(){
            
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();
			
            
			$action = $sm->execute_action();
			$url = VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . 'plugins.php?plugin=pages&search_pid=' . $um->get_param('search_pid');

			if ($action){
				$dm->store_dump(md5($_SERVER['HTTP_HOST'] . $url));
				header('Location: ' . $url);
				exit;
			}
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header() {
			$sm = vivvo_lite_site::get_instance();
			$pm = $sm->get_plugin_manager();
			$header = $sm->get_header_manager();
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js');
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js');
			$header->add_script(VIVVO_URL . $pm->plugins['pages']['root_dir'] . 'admin/js/pages.js');
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/embed.js');
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$pm = $sm->get_plugin_manager();
			$dm = $sm->get_dump_manager();


			$template->assign('PLUGIN_ROOT', $pm->plugins['pages']['root_dir']);

			$action = $sm->action_status;

			$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			if ($action && !$um->isset_param('list_output')){
				$dm->store_dump(md5($url));
				header('Location: http://' . $url);
				exit();
			}

			require_once (VIVVO_FS_PLUGIN_ROOT . 'plugins/pages/Pages.class.php');

			if ($um->isset_param('action') && $action === false){
				$in_page = $um->get_param_regexp('/^PAGE_/');
				$page = new Pages($sm, $in_page);
				$template->assign('page', $page);
			} elseif ($um->get_param('search_pid')) {
				$pl = new Pages_list();
				$page = $pl->get_pages_by_id($um->get_param('search_pid'));
				if ($page){
					$template->assign('page', $page);
				}
			}
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output(false));
			$template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));

			return $template;
		}

		function _list_output($assign_list = true){
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			$content_template =& $this->load_template($this->_template_root . 'form.xml');

			if ($assign_list){
				$um = $sm->get_url_manager();
				$content_template->assign('list_output', $um->isset_param('list_output'));
			}

			return $content_template;
		}

		function _leftnav() {
			return $this->load_template($this->_template_root . 'page_list.xml');
		}
	}
?>