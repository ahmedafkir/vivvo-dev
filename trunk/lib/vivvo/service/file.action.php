<?php
/* =============================================================================
 * $Revision: 4857 $
 * $Date: 2010-04-05 10:48:34 +0200 (Mon, 05 Apr 2010) $
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
		"file.revert" => array(
			"function" => "file_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Revert file.'
		),
		"file.save" => array(
			"function" => "file_service",
			"signature" => array(array('string', 'string','string')),
			"docstring" => 'Save file.'
		),
		"file.duplicate" => array(
			"function" => "file_service",
			"signature" => array(array('string', 'string','string')),
			"docstring" => 'Duplicate file.'
		),
		"file.delete" => array(
			"function" => "file_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Delete file.'
		),
		"file.move" => array(
			"function" => "file_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Move file.'
		),
		"file.upload" => array(
			"function" => "file_service",
			"signature" => array(array('string', 'string')),
			"docstring" => 'Upload file.'
		)
	);

	/**
	 * file service function
	 *
	 * @param xmlrpcmsg $m
	 * @return xmlrpcresp
	 */
	function file_service(&$sm, $action, $command){

		require_once(dirname(__FILE__) . '/file.service.php');
		$file_service =& new file_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();

		if ($command == 'revert'){
			$filename = $um->get_param('FILE_filename');
			$response = $file_service->revert($filename);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_REVERT_SUCCESS'));
				vivvo_hooks_manager::call('file_revert_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($file_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'save'){
			$filename = $um->get_param('FILE_filename');
			$content = $um->get_param('FILE_content');
			$response = $file_service->save($filename, $content);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_SAVE_SUCCESS'));
				vivvo_hooks_manager::call('file_save_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($file_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'duplicate'){
			$filename = $um->get_param('FILE_filename');
			$new_name = $um->get_param('FILE_new_filename');
			$response = $file_service->duplicate($filename, $new_name);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_DUPLICATE_SUCCESS'));
				vivvo_hooks_manager::call('file_duplicate_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($file_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'delete'){
			$filename = $um->get_param('FILE_filename');
			if (is_array($filename)){
				$response = true;
				foreach ($filename as $file){
					$response = $response && $file_service->delete($file);
				}
			}else{
				$response = $file_service->delete($filename);
			}
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_DELETE_SUCCESS'));
				vivvo_hooks_manager::call('file_delete_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($file_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'move'){
			$filename = $um->get_param('FILE_filename');
			$destination = $um->get_param('FILE_destination');
			if (is_array($filename)){
				$response = true;
				foreach ($filename as $file){
					$response = $response && $file_service->move($file, $destination);
				}
			}else{
				$response = $file_service->move($filename, $destination);
			}
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FILE_MOVED_SUCCESS'));
				vivvo_hooks_manager::call('file_move_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($file_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'upload'){
			if ($um->get_param('UPLOAD_destination')){
				$response = $file_service->upload('UPLOAD_file', $um->get_param('UPLOAD_destination'));
			}else{
				$response = $file_service->upload('UPLOAD_file');
			}

			if ($response){
				$sm->action_response = $response;
				$dm->add_dump('info', 0, $lang->get_value('LNG_INFO_FILE_UPLOAD_SUCCESS'));
				vivvo_hooks_manager::call('file_upload_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($file_service->get_error_info()));
				return false;
			}
		}
		return true;
	}
?>