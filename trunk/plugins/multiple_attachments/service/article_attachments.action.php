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

	/**
	 * Service definition for attachment gallery plugin
	 */
	$service_definition = array(
		"article_attachments.add" => array(
			"function" => "article_attachments_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Add attachments'
		),
		"article_attachments.remove" => array(
			"function" => "article_attachments_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Remove attachment.'
		),
		"article_attachments.editAttachment" => array(
			"function" => "article_attachments_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Edit attachment.'
		),
		"article_attachments.reorder" => array(
			"function" => "article_attachments_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Reorder attachments.'
		)
	);

	/**
	 * Article attachments service
	 *
	 * @param	site_manager	$sm
	 * @param	string			$action
	 * @param	string			$command
	 * @return	boolean			true on succes, or false on fail
	 */
	function article_attachments_service(&$sm, $action, $command){
		require_once(dirname(__FILE__) . '/article_attachments.service.php');
		$article_attachments_service = new article_attachments_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();
		$lang->load_from_source('plugins/multiple_attachments/admin/lang/');

		if ($command == 'add'){
			$in_article_attachments = $um->get_param('PLUGIN_MULTIPLE_ATTACHMENTS_data');
			$response = $article_attachments_service->add($in_article_attachments);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_MULTIPLE_ATTACHMENTS_ADD_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'editAttachment'){
			$attachment_id = $um->get_param('PLUGIN_MULTIPLE_ATTACHMENTS_id');
			$in_attachment = $um->get_param_regexp('/^PLUGIN_MULTIPLE_ATTACHMENTS_/');

			$response = $article_attachments_service->edit_attachment($attachment_id, $in_attachment);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_MULTIPLE_ATTACHMENTS_EDIT_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'remove'){
			$attachment_id = $um->get_param('PLUGIN_MULTIPLE_ATTACHMENTS_id');
			$response = $article_attachments_service->remove($attachment_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_MULTIPLE_ATTACHMENTS_REMOVE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'reorder'){
			$in_order = $um->get_param('PLUGIN_MULTIPLE_ATTACHMENTS_order');

			$response = $article_attachments_service->reorder($in_order);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_MULTIPLE_ATTACHMENTS_REORDER_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}
		return true;
	}


?>