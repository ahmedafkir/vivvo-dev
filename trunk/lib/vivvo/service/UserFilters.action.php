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


	$service_definition = array(
		"UserFilter.add" => array(
			"function" => "UserFilters_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Add new user filter.'
		),
		"UserFilter.delete" => array(
			"function" => "UserFilters_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Delete user filter.'
		)
	);

	function UserFilters_service(&$sm, $action, $command){

		require_once(dirname(__FILE__) . '/UserFilters.service.php');
		$UserFilters_service =& new UserFilters_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();

		if ($command == 'add'){
			$filter_name = $um->get_param('FILTER_name');
			$filter_section = $um->get_param('FILTER_section');
			$filter_data = $um->get_param('FILTER_data');

			$response = $UserFilters_service->add_UserFilters($filter_name,$filter_section,$filter_data);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_FILTER_ADD_SUCCESS'));
				vivvo_hooks_manager::call('userFilter_add_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($UserFilters_service->get_error_info()));
				return false;
			}
		}elseif($command == 'delete'){
			$filter_id = $um->get_param('FILTER_id');
			$response = $UserFilters_service->delete_UserFilters($filter_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_FILTER_DELETE_SUCCESS'));
				vivvo_hooks_manager::call('userFilter_delete_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($UserFilters_service->get_error_info()));
				return false;
			}
		}
	}
?>