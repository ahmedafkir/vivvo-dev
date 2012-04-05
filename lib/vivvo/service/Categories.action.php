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
		"category.add" => array(
			"function" => "category_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Add new category.'
		),
		"category.delete" => array(
			"function" => "category_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Delete category.'
		),
		"category.edit" => array(
			"function" => "category_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Edit category.'
		),
		"category.reorder" => array(
			"function" => "category_service",
			"signature" => array(array('bool', 'integer','string')),
			"docstring" => 'Reoder categories.'
		),
		"category.deleteImage" => array(
			"function" => "category_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Delete category image'
		)
	);


	function category_service(&$sm, $action, $command) {

		require_once(dirname(__FILE__) . '/Categories.service.php');

		$category_service = new category_service($sm);
		$um = $sm->get_url_manager();
		$dm = $sm->get_dump_manager();
		$lang = $sm->get_lang();
		$category_id = $um->get_param('CATEGORY_id');

		if ($command == 'add') {
			$in_category = $um->get_param_regexp('/^CATEGORY_/');
			$response = $category_service->add_category($in_category);
			if ($response) {
				$dm->add_dump('info', 0, $lang->get_value($um->get_param('CATEGORY_redirect') ? 'LNG_INFO_LINK_ADD_SUCCESS' : 'LNG_INFO_CATEGORY_ADD_SUCCESS'));
				vivvo_cache::get_instance()->delete('categories');
				vivvo_hooks_manager::call('category_add_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($category_service->get_error_info()));
				return false;
			}
		} elseif($command == 'delete') {
			$response = $category_service->delete_category($category_id);
			if ($response) {
				$dm->add_dump('info', 0, $lang->get_value($um->get_param('CATEGORY_redirect') ? 'LNG_INFO_LINK_DELETE_SUCCESS' : 'LNG_INFO_CATEGORY_DELETE_SUCCESS'));
				vivvo_cache::get_instance()->delete('categories');
				vivvo_hooks_manager::call('category_delete_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($category_service->get_error_info()));
				return false;
			}
		} elseif($command == 'edit') {
			$category_id = $um->get_param('CATEGORY_id');
			$in_category = $um->get_param_regexp('/^CATEGORY_/');

			$response = $category_service->edit_category($category_id,$in_category);
			if ($response) {
				$dm->add_dump('info', 0, $lang->get_value($um->get_param('CATEGORY_redirect') ? 'LNG_INFO_LINK_EDIT_SUCCESS' : 'LNG_INFO_CATEGORY_EDIT_SUCCESS'));
				vivvo_cache::get_instance()->delete('categories');
				vivvo_hooks_manager::call('category_edit_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($category_service->get_error_info()));
				return false;
			}
		} elseif ($command ==  'reorder') {
			$in_category = $um->get_param('category_list');
			$in_category = json_decode($in_category, true);
			$response = $category_service->reorder_category(0, $in_category);
			if ($response !== false) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_CATEGORY_REORDER_SUCCESS'));
				vivvo_cache::get_instance()->delete('categories');
				vivvo_hooks_manager::call('category_reorder_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($category_service->get_error_info()));
				return false;
			}
		} elseif($command == 'deleteImage') {
			$category_id = $um->get_param('CATEGORY_id');
			$response = $category_service->delete_image($category_id);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_CATEGORY_DELETE_IMAGE_SUCCESS'));
				vivvo_cache::get_instance()->delete('categories');
				vivvo_hooks_manager::call('category_deleteImage_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($category_service->get_error_info()));
				return false;
			}
		}
		return true;
	}
?>