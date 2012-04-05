<?php
/* =============================================================================
 * $Revision: 5418 $
 * $Date: 2010-05-28 12:09:06 +0200 (Fri, 28 May 2010) $
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
	 * Service definition for files
	 */
	$service_definition = array(

		"asset.delete" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Delete file.'
		),
		"asset.move" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Move file.'
		),
		"asset.rename" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Rename file.'
		),
		"asset.upload" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Upload file.'
		),
		"asset.edit" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Edit file properties.'
		),
		"asset.scan_dir" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Scan dir.'
		),
		"asset.new_dir" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Add new dir.'
		),
		"asset.delete_dir" => array(
			"function" => "assets_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Remove dir.'
		),

	);

	/**
	 * file service function
	 *
	 * @param xmlrpcmsg $m
	 * @return xmlrpcresp
	 */
	function assets_service(&$sm, $action, $command){

		require_once(dirname(__FILE__) . '/assets.service.php');

		$assets_service =& new assets_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();

		if ($command == 'delete'){

			$filename = $um->get_param('FILE_filename');
			if (is_array($filename)){
				$response = true;
				foreach ($filename as $file){
					$response = $response && $assets_service->delete($file);
				}
			}else{
				$response = $assets_service->delete($filename);
			}
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_DELETE_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_delete_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		//----------------------------------------------------------
		} elseif ($command == 'move'){
			$filename = $um->get_param('FILE_filename');
			$destination = $um->get_param('FILE_destination');


			if (is_array($filename)){
				$response = true;
				foreach ($filename as $file){
					$response = $response && $assets_service->move($file, $destination);
				}
			}else{
				$response = $assets_service->move($filename, $destination);
			}
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_MOVED_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_move_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		//----------------------------------------------------------
		} elseif ($command == 'rename'){
			$filename = $um->get_param('FILE_filename');
			$newname = $um->get_param('FILE_newname');
			if (is_array($filename)){
				$response = $assets_service->rename($filename[0], $newname);
			}else{
				$response = $assets_service->rename($filename, $newname);
			}

			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_RENAMED_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_rename_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		//----------------------------------------------------------
		} elseif ($command == 'upload'){
			if ($um->get_param('UPLOAD_destination')){
				$response = $assets_service->upload('UPLOAD_file', $um->get_param('UPLOAD_destination'));
			}else{
				$response = $assets_service->upload('UPLOAD_file');
			}

			if ($response){
				$sm->action_response = $response;
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_UPLOAD_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_upload_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		//----------------------------------------------------------
		} elseif ($command == 'edit'){
			$asset_id = $um->get_param('ASSET_id');
			$data = $um->get_param_regexp('/^ASSET_/');

			$response = $assets_service->edit($asset_id, $data);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_EDIT_DATA_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_edit_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		//----------------------------------------------------------
		} elseif ($command == 'scan_dir'){

			$response = $assets_service->scan_dir($um->get_param('FILE_dir'));

			if ($response){
				$sm->action_response = $response;
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_DIRSCAN_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_scanDir_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		//----------------------------------------------------------
		} elseif ($command == 'new_dir'){
			$current = $um->get_param('FILE_current');
			$newdir = $um->get_param('FILE_newdir');
			$response = $assets_service->new_dir($current, $newdir);

			if ($response){
				$sm->action_response = $response;
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_ADD_DIR_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_newDir_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		//----------------------------------------------------------
		} elseif ($command == 'delete_dir'){

			$response = $assets_service->rm_dir($um->get_param('FILE_rmdir'));

			if ($response){
				$sm->action_response = $response;
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_DELETE_DIR_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('asset_deleteDir_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($assets_service->get_error_info()));
				return false;
			}
		}
		return true;
	}
?>