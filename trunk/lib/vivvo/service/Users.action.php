<?php
/* =============================================================================
 * $Revision: 6887 $
 * $Date: 2011-05-18 15:11:09 +0200 (Wed, 18 May 2011) $
 *
 * Vivvo CMS v4.6 (build 6082)
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


	$service_definition = array(
		"user.add" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Add new user.'
		),
		"user.delete" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Delete user.'
		),
		"user.edit" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Edit user.'
		),
		"user.setField" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Set filed'
		),
		"user.addToGroup" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Add users to group'
		),
		"user.removeFromGroup" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Remove users from group'
		),
		"user.deleteImage" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Delete user image'
		),
		"user.checkUsername" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Chek username'
		),
		"user.checkEmail" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Check email'
		),
		"user.report404" => array(
			"function" => "user_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Report 404'
		)
	);

	function user_service(&$sm, $action, $command){

		require_once(dirname(__FILE__) . '/Users.service.php');
		$user_service =& new user_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();

		if($command == 'add'){
			$in_user = $um->get_param_regexp('/^USER_/');
			$response = $user_service->add_user($in_user);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_ADD_SUCCESS'));
				vivvo_hooks_manager::call('user_add_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'delete'){

			if (is_array($um->get_param('ids'))){
				$user_id = $um->get_param('ids');
			}else{
				$user_id = array($um->get_param('USER_id'));
			}
			$all_matching = $um->get_param('all_matching');
			$response = $user_service->delete_user($user_id, $all_matching);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_DELETE_SUCCESS'));
				vivvo_hooks_manager::call('user_delete_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'edit'){
			$user_id = $um->get_param('USER_id');
			$in_user = $um->get_param_regexp('/^USER_/');

			$response = $user_service->edit_user($user_id,$in_user);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_EDIT_SUCCESS'));
				vivvo_hooks_manager::call('user_edit_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'setField'){
			$users_ids = $um->get_param('ids');
			$field_name = $um->get_param('field_name');
			$filed_value = $um->get_param('field_value');
			$all_matching = $um->get_param('all_matching');
			$response = $user_service->set_field($users_ids, $field_name, $filed_value, $all_matching);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_SET_FIELD_SUCCESS'));
				vivvo_hooks_manager::call('user_setField_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'addToGroup'){
			$users_ids = $um->get_param('ids');
			$group_id = $um->get_param('USER_group');
			$response = $user_service->add_to_group($group_id, $users_ids);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_SET_FIELD_SUCCESS'));
				vivvo_hooks_manager::call('user_addToGroup_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'removeFromGroup'){
			$users_ids = $um->get_param('ids');
			$group_id = $um->get_param('USER_group');
			$response = $user_service->remove_from_group($group_id, $users_ids);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_SET_FIELD_SUCCESS'));
				vivvo_hooks_manager::call('user_removeFromGroup_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'deleteImage'){
			$user_id = $um->get_param('USER_id');
			$response = $user_service->delete_image($user_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_DELETE_IMAGE_SUCCESS'));
				vivvo_hooks_manager::call('user_deleteImage_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'checkUsername'){
			$username = $um->get_param('USER_username');
			$response = $user_service->check_username($username);
			if ($response){
				vivvo_hooks_manager::call('user_checkUsername_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'checkEmail'){
			$email = $um->get_param('USER_email');
			$response = $user_service->check_email($email);
			if ($response){
				vivvo_hooks_manager::call('user_checkEmail_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}elseif($command == 'report404'){
			$url = $um->get_param('url');
			$referrer = $um->get_param('referrer');
			$response = $user_service->report_404($url,$referrer);

			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_REPORTED_404_SUCCESS'));
				vivvo_hooks_manager::call('user_report404_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($user_service->get_error_info()));
				return false;
			}
		}
		return true;
	}
?>