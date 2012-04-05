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
	 * @version		$Revision: 4834 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	Class newsletter_admin_view extends vivvo_admin_view{

		//List of views
		var $views = array('leftnav', 'maillist', 'preferences', 'list_output');

		var $_plugin = 'newsletter';

		var $_template_root = 'admin/templates/';

		function check_privileges(){
			if (vivvo_lite_site::get_instance()->user->is_admin()){
				return true;
			}
			return false;
		}

		function handle_action(){
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();
			$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			if ($action && !$um->isset_param('list_output')){
				$dm->store_dump(md5($url));
				header('Location: http://' . $url);
				exit();
			}
		}

		function load_admin_header (){
			$sm = vivvo_lite_site::get_instance();
			$pm = $sm->get_plugin_manager();
			$header = $sm->get_header_manager();
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js');
			$header->add_script(VIVVO_URL . 'js/tooltips.js');
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR. 'js/functions.js');
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css');
			$header->add_script(VIVVO_URL . $pm->plugins['newsletter']['root_dir'] . 'admin/js/newsletter_admin.js');
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$pm = $sm->get_plugin_manager();
			$template->assign('PLUGIN_ROOT', $pm->plugins['newsletter']['root_dir']);
		}

		function _leftnav() {
			return $this->load_template($this->_template_root . 'tabs.xml');
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->_leftnav());
			return $template;
		}

		function _maillist() {
			$sm = vivvo_lite_site::get_instance();

			require_once(dirname(__FILE__) . '/newsletter.class.php');

			$params = maillist_list::get_search_params_from_url($sm);
			$maillist = $this->load_template($this->_template_root . 'list.xml');
			$maillist->assign('maillist_params', $params);
			$maillist->assign('search_limit', $params['search_limit']);
			$maillist->assign('search_sort_by', strval($params['search_sort_by'] . '.' . $params['search_order']));

			return $maillist;
		}

		function _preferences() {
			return $this->load_template($this->_template_root . 'preferences.xml');
		}

		function _list_output($assign_list = true){
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			$list_output = $this->load_template($this->_template_root . 'newsletter.xml');

			$user_manager = $sm->get_user_manager();
			$gl = $user_manager->get_group_list();
			$groups = $gl->get_all_groups();

			$list_output->assign('group_list', $groups);


			if ($um->isset_param('action') && $sm->action_status == false){
				require_once(dirname(__FILE__) . '/newsletter.class.php');
				$in_newsletter = $um->get_param_regexp('/^PLUGIN_NEWSLETTER_/');
				$current_newsletter = new newsletter(null, $in_newsletter);
				$list_output->assign('newsletter', $current_newsletter);
			}elseif ($um->isset_param('search_id')){
				require_once(dirname(__FILE__) . '/newsletter.class.php');
				$nl = new newsletter_list();
				$newsletter = $nl->get_newsletter_by_id($um->get_param('search_id'));

				if ($newsletter){
					$list_output->assign('newsletter', $newsletter);
				}
			}

			return $list_output;
		}
	}
?>