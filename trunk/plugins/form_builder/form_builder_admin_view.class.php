<?php
/* =============================================================================
 * $Revision: 5406 $
 * $Date: 2010-5-27 18:28:26 +0100 (Wed, 27 May 2010) $
 *
 * Vivvo CMS 4.5
 * Copyright 2005-10 SpoonLabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * ============================================================================
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
	 * @version		$Revision: 2738 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	Class form_builder_admin_view extends vivvo_admin_view{

		//List of views
		var $views = array('list_output','leftnav','form_properties','form_list');

		var $_plugin = 'form_builder';

		var $_template_root = 'admin/templates/';

		function check_privileges(){
			if ($this->_site_manager->user->is_admin()){
				return true;
			}
			return false;
		}

		function handle_action(){
			$sm =& $this->_site_manager;
			$template =& $sm->get_template();
			$um =& $sm->get_url_manager();
			$dm =& $sm->get_dump_manager();

			$action = $sm->execute_action();
			$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

			if ($action && !$um->isset_param('list_output')){
				$dm->store_dump(md5($url));
				HTTP_Session::pause();
				header('Location: http://' . $url);
				exit();
			}
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header (){
			$sm =& $this->_site_manager;
			$pm =& $sm->get_plugin_manager();
			$header =& $this->_site_manager->get_header_manager();
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/tree.js');
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js');
			$header->add_script(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js');
			$header->add_script(VIVVO_URL . 'js/tooltips.js');
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css');
			$header->add_script(VIVVO_URL . $pm->plugins['form_builder']['root_dir'] . 'admin/js/formBuilder.js');
			$header->add_css(VIVVO_URL . $pm->plugins['form_builder']['root_dir'] . 'admin/css/form_builder.css');
			$header->add_script(VIVVO_URL . 'js/calendar/calendar_date_select.js');
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/calendar.css');
		}

		function _leftnav() {
			$sm =& $this->_site_manager;
			$pm =& $sm->get_plugin_manager();
			$um =& $sm->get_url_manager();

			$leftnav =& $this->load_template($this->_template_root . 'tabs.xml');
			$leftnav->assign('PLUGIN_ROOT', $pm->plugins['form_builder']['root_dir']);

			require_once(VIVVO_FS_PLUGIN_ROOT . $pm->plugins['form_builder']['root_dir'] . 'form_builder.class.php');

			$form_list =& new FormBuilderForms_list($this->_site_manager);

			if ($um->isset_param('search_fid')){
				$form =& $form_list->get_form_by_id($um->get_param('search_fid'));
			}else{
				$form =& $form_list->get_latest_form();
			}

			$leftnav->assign('form', $form);
			return $leftnav;
		}

		function _form_properties() {
			$sm =& $this->_site_manager;
			$pm =& $sm->get_plugin_manager();
			$um =& $sm->get_url_manager();

			$form_properties =& $this->load_template($this->_template_root . 'preferences.xml');

			require_once(VIVVO_FS_PLUGIN_ROOT . $pm->plugins['form_builder']['root_dir'] . 'form_builder.class.php');

			$form_list =& new FormBuilderForms_list($this->_site_manager);

			if ($um->isset_param('search_fid')){
				$form =& $form_list->get_form_by_id($um->get_param('search_fid'));
				$form_properties->assign('form', $form);
			}

			return $form_properties;
		}

		function _form_list() {
			$form_list =& $this->load_template($this->_template_root . 'form_list.xml');
			return $form_list;
		}

		function _default_view() {
			$template =& $this->_site_manager->get_template();
			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->_leftnav());

			return $template;
		}

		function _list_output($assign_list = true){
			$sm =& $this->_site_manager;
			$pm =& $sm->get_plugin_manager();
			$um =& $sm->get_url_manager();

			$content_template =& $this->load_template($this->_template_root . 'list.xml');
			$content_template->assign('PLUGIN_ROOT', $pm->plugins['form_builder']['root_dir']);


			require_once(VIVVO_FS_PLUGIN_ROOT . $pm->plugins['form_builder']['root_dir'] . 'form_builder.class.php');

			$form_list =& new FormBuilderForms_list($this->_site_manager);

			if ($um->isset_param('search_fid')){
				$form =& $form_list->get_form_by_id($um->get_param('search_fid'));
			}else{
				$form =& $form_list->get_latest_form();
			}

			$content_template->assign('form', $form);
			if ($form){
				$form_element_list =& new FormBuilderFields_list($this->_site_manager);
				$form_element_list->get_elements_by_form_id($form->id);

				$content_template->assign('form_elements', $form_element_list->list);
			}


			return $content_template;
		}
	}
?>