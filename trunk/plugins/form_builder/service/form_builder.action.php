<?php
/* =============================================================================
 * $Revision: 5533 $
 * $Date: 2010-6-15 18:28:26 +0100 (Tue, 15 June 2010) $
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
	 * Service definition for form builder plugin
	 */
	$service_definition = array(
		"form_builder.add" => array(
			"function" => "form_builder_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Add form'
		),
		"form_builder.edit" => array(
			"function" => "form_builder_service",
			"signature" => array(array('bool', 'integer','array')),
			"docstring" => 'Edit form.'
		),
		"form_builder.delete" => array(
			"function" => "form_builder_service",
			"signature" => array(array('bool', 'integer','array')),
			"docstring" => 'Delete form.'
		),
		"form_builder.mail" => array(
			"function" => "form_builder_service",
			"signature" => array(array('bool', 'integer','array')),
			"docstring" => 'Mail form.'
		),
		"form_builder.my_action" => array(
			"function" => "form_builder_service",
			"signature" => array(array('bool', 'integer','array')),
			"docstring" => 'My action.'
		)
	);



	/**
	 * Form builder manager service
	 *
	 * @param	site_manager	$sm
	 * @param	string			$action
	 * @param	string			$command
	 * @return	boolean			true on succes, or false on fail
	 */
	function form_builder_service($sm, $action, $command) {

		require_once(dirname(__FILE__) . '/form_builder.service.php');

		$form_builder_service = new form_builder_service($sm);

		$um = $sm->get_url_manager();
		$dm = $sm->get_dump_manager();
		$lang = $sm->get_lang();
		$pm = $sm->get_plugin_manager();
		$lang->load_lang(VIVVO_ADMIN_LANG, $pm->plugins['form_builder']['root_dir'] . 'admin/lang/');

		if ($command == 'add'){
			$in_form = $um->get_param_regexp('/^PLUGIN_FORM_BUILDER_/');
			unset($in_form['elements']);
			$in_form_elemnet = $um->get_param('PLUGIN_FORM_BUILDER_elements');

			$response = $form_builder_service->add($in_form, $in_form_elemnet);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_FORM_BUILDER_FORM_ADD_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('form_builder_add_postaction');
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'edit'){
			$form_id = $um->get_param('PLUGIN_FORM_BUILDER_id');
			$in_form = $um->get_param_regexp('/^PLUGIN_FORM_BUILDER_/');
			unset($in_form['elements']);
			$in_form_elemnet = $um->get_param('PLUGIN_FORM_BUILDER_elements');

			$response = $form_builder_service->edit($form_id, $in_form, $in_form_elemnet);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_FORM_BUILDER_FORM_EDIT_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('form_builder_edit_postaction');
				return true;
			}else{
				return  false;
			}
		}elseif ($command  == 'delete'){
			$form_id = $um->get_param('PLUGIN_FORM_BUILDER_id');
			$response = $form_builder_service->delete($form_id);

			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_FORM_BUILDER_FORM_DELETE_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('form_builder_delete_postaction');
				return true;
			}else{
				return false;
			}

		}elseif ($command  == 'mail'){
			$form_id = $um->get_param('PFB_form_id');
			$in_form = $um->get_param_regexp('/^PFB_/');

			$response = $form_builder_service->mail($form_id, $in_form);

			if ($response !== false){
				$dm->add_dump('info', 0 , $response);
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('form_builder_mail_postaction');
				return true;
			}else{
				return false;
			}

		}elseif ($command  == 'my_action'){
			$form_id = $um->get_param('PFB_form_id');
			$in_form = $um->get_param_regexp('/^PFB_/');

			$response = $form_builder_service->my_action($form_id, $in_form);

			if ($response !== false){
				$dm->add_dump('info', 0 , $response);
				return true;
			}else{
				return false;
			}

		}

		header('X-Vivvo-Action-Status: 0');
		header('X-Vivvo-Action-Error: ' . json_encode($form_builder_service->get_error_info()));
		return false;
	}

#EOF