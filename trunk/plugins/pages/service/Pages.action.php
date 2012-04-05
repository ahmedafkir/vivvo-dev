<?php
/* =============================================================================
 * $Revision: 5127 $
 * $Date: 2010-04-26 10:03:56 +0200 (Mon, 26 Apr 2010) $
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
		"page.add" => array(
			"function" => "page_service",
			"signature" => array(array('bool', 'string','integer')),
			"docstring" => 'Add new page.'
		),
		"page.delete" => array(
			"function" => "page_service",
			"signature" =>  array(array('bool', 'integer')),
			"docstring" => 'Delete page.'
		),
		"page.edit" => array(
			"function" => "page_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Edit page.'
		),
		"page.reorder" => array(
			"function" => "page_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Reorder pages.'
		)
	);

	function page_service(&$sm, $action, $command) {

		require_once(dirname(__FILE__) . '/Pages.service.php');
		$page_service = new page_service($sm);

		$um = $sm->get_url_manager();
		$dm = $sm->get_dump_manager();
		$lang = $sm->get_lang();
		$lang->load_from_source('plugins/pages/admin/lang/');

		if ($command == 'add') {
			$in_page = $um->get_param_regexp('/^PAGE_/');
			$response = $page_service->add_page($in_page);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PAGE_ADD_SUCCESS'));
				return true;
			} else {
				return false;
			}
		} elseif($command == 'delete') {
			$page_id = $um->get_param('PAGE_id');
			$response = $page_service->delete_page($page_id);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PAGE_DELETE_SUCCESS'));
				return true;
			} else {
				return false;
			}
		} elseif($command == 'edit') {
			$page_id = $um->get_param('PAGE_id');
			$in_page = $um->get_param_regexp('/^PAGE_/');
			$response = $page_service->edit_page($page_id, $in_page);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PAGE_EDIT_SUCCESS'));
				return true;
			} else {
				return false;
			}
		} elseif($command == 'reorder') {
			$in_page = $um->get_param('page_list');
			$response = $page_service->reorder_pages($in_page);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PAGE_REORDER_SUCCESS'));
				return true;
			} else {
				return false;
			}
		}
	}
?>