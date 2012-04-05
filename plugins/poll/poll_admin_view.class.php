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
	Class poll_admin_view extends vivvo_admin_view{

		//List of views
		var $views = array('list_output','leftnav');

		var $_plugin = 'poll';

		//U odnosu na plugin se formira putanja ako treba a treba
		var $_template_root = 'admin/templates/';

		function check_privileges(){
			if (vivvo_lite_site::get_instance()->user->is_admin()){
				return true;
			}
			return false;
		}

		function handle_action(){
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();

			if ($action && !$um->isset_param('list_output')){
				$dm->store_dump(md5(CURRENT_URL));
				header('Location: http://' . CURRENT_URL);
				exit();
			}
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header() {
			$sm = vivvo_lite_site::get_instance();
			$header = $sm->get_header_manager();
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js');
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js');
			$pm = $sm->get_plugin_manager();
			$header->add_script(VIVVO_URL . $pm->plugins['poll']['root_dir'] . 'admin/js/poll_admin.js');
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

		function _list_output($assign_list = true){
			$sm = vivvo_lite_site::get_instance();
			$pm = $sm->get_plugin_manager();
			require_once(dirname(__FILE__) . '/poll.class.php');

			$content_template =& $this->load_template($this->_template_root . 'list.xml');
			$content_template->assign('PLUGIN_ROOT', $pm->plugins['poll']['root_dir']);

			$um = $sm->get_url_manager();

			if ($um->isset_param('action') && $um->isset_param('cmd') && ($um->get_param('cmd') == 'add') && $sm->action_status === false){
				$in_poll_question = $um->get_param_regexp('/^PLUGIN_POLL_/');
				$current_poll_question = new vivvo_poll_question(null, $in_poll_question);
				$content_template->assign('poll', $current_poll_question);
			} elseif($um->isset_param('search_pid') && $um->get_param('search_pid') != ''){
				$poll_list = new vivvo_poll_question_list();
				$poll =& $poll_list->get_question($um->get_param('search_pid'));
				$content_template->assign('poll', $poll);
				$content_template->assign('answer_list', $poll->answer->list);
			}else{
				$poll_list = new vivvo_poll_question_list();
				$poll =& $poll_list->get_question_by_status(1);
				$content_template->assign('poll', $poll);
				$content_template->assign('answer_list', $poll->answer->list);


			}
			if($um->isset_param('action') && $um->isset_param('cmd') && (($um->get_param('cmd') == 'delete') or ($um->get_param('cmd') == 'deactivate') or ($um->get_param('cmd') == 'activate'))){
				$content_template->assign('list_output', strval(1));
			}

			return $content_template;
		}
	}
?>