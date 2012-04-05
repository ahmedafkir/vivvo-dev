<?php
/* =============================================================================
 * $Revision: 3855 $
 * $Date: 2009-6-23 18:28:26 +0100 (Tue, 23 June 2009) $
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
	 * @see  '/poll.class.php'
	 */
	require_once(dirname(__FILE__) . '/form_builder.class.php');

	/**
	 * @see  '/../../lib/vivvo/plugin/vivvo_plugin.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php');


	/**
	 * Box from
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		1.0
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @see			plugin
	 *
	 */
	class box_form extends plugin_module {
		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'form_builder';

		/**
		 * Turn the cahce on
		 *
		 * @var boolean
		 */
		var $cache = false;

		var $_default_template_file = 'system/box_default/box_form.tpl';

		/**
		 * Generate box output
		 * Assign box_form template with form
		 * Sets local language
		 *
		 * @param	array			$params
		 */
		function generate_output($params = ''){
			$this->set_configuration();
			$this->set_template($params);

			$form_list =& new FormBuilderForms_list($this->_site_manager);
			if (VIVVO_ADMIN_MODE && isset($params['list'])){
				$form_list->get_all_forms();
				$this->_template->assign('form_list', $form_list->list);
			}elseif(VIVVO_ADMIN_MODE && isset($params['search_fid'])){
				$form =& $form_list->get_form_by_id($params['search_fid']);
				$this->_template->assign('form', $form);
				if ($form){
					$form_element_list =& new FormBuilderFields_list($this->_site_manager);
					$form_element_list->get_elements_by_form_id($form->id);

					$upload = false;
					foreach ($form_element_list->list as $element) {
						if ($element->get_type() == 'file_upload') {
							$upload = true;
							break;
						}
					}

					$this->_template->assign('form_elements', $form_element_list->list);
					$this->_template->assign('form_has_file_upload', $upload);
				}
			}elseif(isset($params['search_fid'])){
				$form =& $form_list->get_active_form_by_id($params['search_fid']);
				$this->_template->assign('form', $form);
				if ($form){
					$form_element_list =& new FormBuilderFields_list($this->_site_manager);
					$form_element_list->get_elements_by_form_id($form->id);

					$upload = false;
					foreach ($form_element_list->list as $element) {
						if ($element->get_type() == 'file_upload') {
							$upload = true;
							break;
						}
					}

					$this->_template->assign('form_elements', $form_element_list->list);
					$this->_template->assign('form_has_file_upload', $upload);
				}
			}
		}
	}
?>
