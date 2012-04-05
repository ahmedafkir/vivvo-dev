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
	 * Service definition for category
	 */
	$service_definition = array(
		"vivvoCore.preferences" => array(
			"function" => "vivvo_core_service",
			"signature" => array(array('bool', 'string', 'string')),
			"docstring" => 'Vivvo Preferences.'
		),
		"vivvoCore.cronTask" => array(
			"function" => "vivvo_core_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Vivvo cron task set.'
		),
		"vivvoCore.activityLog" => array(
			"function" => "vivvo_core_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Activity log.'
		),
		"vivvoCore.groupAdd" => array(
			"function" => "vivvo_core_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Add user group.'
		),
		"vivvoCore.groupEdit" => array(
			"function" => "vivvo_core_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Edit user group.'
		),
		"vivvoCore.groupDelete" => array(
			"function" => "vivvo_core_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Delete user group.'
		)
	);

	/**
	 * Box def move service function
	 *
	 * @param vivvo_lite_site $sm
	 * @return xmlrpcresp
	 */
	function vivvo_core_service(&$sm, $action, $command) {

		require_once(dirname(__FILE__) . '/vivvo_core.service.php');

		$um = $sm->get_url_manager();
		$dm = $sm->get_dump_manager();
		$lang = $sm->get_lang();

		$vivvo_core_service = new vivvo_core_service($sm);

		if ($command == 'preferences') {
			$success = true;
			if (is_array($um->list) && !empty($um->list)){
				foreach ($um->list as $key => $value){
					if (preg_match('/^(VIVVO_|PLUGIN_)/', $key)){
						if (is_array($um->list[$key])){
							$value = implode(',', $um->list[$key]);
							$response =  $vivvo_core_service->edit_preferences($key, $value);
							if (!$response){
								$success = false;
							}
						}else{
							$response = $vivvo_core_service->edit_preferences($key, $um->list[$key]);
							if (!$response){
								$success = false;
							}
						}
					}
				}
			}

			if ($success){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PREFERENCES_SAVE_SUCCESS'));
				vivvo_hooks_manager::call('vivvoCore_preferences_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($vivvo_core_service->get_error_info()));
				return false;
			}
		}else if ($command == 'cronTask'){
			$success = true;
			if (is_array($um->list) && !empty($um->list)){
				foreach ($um->list as $key => $value){
					if (preg_match('/^(VIVVO_|PLUGIN_)/', $key)){
						if (is_array($um->list[$key])){
							$value = implode(',', $um->list[$key]);
							$response =  $vivvo_core_service->edit_preferences($key, $value);
							if (!$response){
								$success = false;
							}
						}else{
							$response = $vivvo_core_service->edit_preferences($key, $um->list[$key]);
							if (!$response){
								$success = false;
							}
						}
					}
				}
			}
			$in_task = $um->get_param_regexp('/^TASK_/');
			$success = $success && $vivvo_core_service->edit_cron_task($in_task);
			if ($success){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_TASK_SAVE_SUCCESS'));
				vivvo_hooks_manager::call('vivvoCore_cronTask_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($vivvo_core_service->get_error_info()));
				return false;
			}
		}else if ($command == 'groupAdd'){
			$in_group = $um->get_param_regexp('/^GROUP_/');
			$success = $vivvo_core_service->group_add($in_group);
			if ($success){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_GROUP_SAVE_SUCCESS'));
				vivvo_hooks_manager::call('vivvoCore_groupAdd_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($vivvo_core_service->get_error_info()));
				return false;
			}
		}else if ($command == 'groupEdit'){
			$group_id = $um->get_param('GROUP_id');
			$in_group = $um->get_param_regexp('/^GROUP_/');
			$success = $vivvo_core_service->group_edit($group_id, $in_group);
			if ($success){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_GROUP_EDIT_SUCCESS'));
				vivvo_hooks_manager::call('vivvoCore_groupEdit_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($vivvo_core_service->get_error_info()));
				return false;
			}
		}else if ($command == 'groupDelete'){
			$group_id = $um->get_param('GROUP_id');
			$success = $vivvo_core_service->group_delete($group_id);
			if ($success){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_GROUP_DELETE_SUCCESS'));
				vivvo_hooks_manager::call('vivvoCore_groupDelete_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($vivvo_core_service->get_error_info()));
				return false;
			}
		}
	}
?>