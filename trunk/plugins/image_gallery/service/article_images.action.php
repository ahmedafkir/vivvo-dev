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
	 * Service definition for image gallery plugin
	 */
	$service_definition = array(
		"article_images.add" => array(
			"function" => "article_images_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Add images'
		),
		"article_images.remove" => array(
			"function" => "article_images_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Remove image.'
		),
		"article_images.editImage" => array(
			"function" => "article_images_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Edit image.'
		),
		"article_images.reorder" => array(
			"function" => "article_images_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Reorder images.'
		)
	);

	/**
	 * Article images service
	 *
	 * @param	site_manager	$sm
	 * @param	string			$action
	 * @param	string			$command
	 * @return	boolean			true on succes, or false on fail
	 */
	function article_images_service(&$sm, $action, $command){
		require_once(dirname(__FILE__) . '/article_images.service.php');
		$article_images_service = new article_images_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();
		$lang->load_from_source('plugins/image_gallery/admin/lang/');

		if ($command == 'add'){
			$in_article_images = $um->get_param('PLUGIN_IMAGE_GALLERY_data');
			$response = $article_images_service->add($in_article_images);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_IMAGE_GALLERY_ADMIN_ADD_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'editImage'){
			$image_id = $um->get_param('PLUGIN_IMAGE_GALLERY_id');
			$in_image = $um->get_param_regexp('/^PLUGIN_IMAGE_GALLERY_/');

			$response = $article_images_service->edit_image($image_id, $in_image);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_IMAGE_GALLERY_ADMIN_EDIT_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'remove'){
			$image_id = $um->get_param('PLUGIN_IMAGE_GALLERY_id');
			$response = $article_images_service->remove($image_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_IMAGE_GALLERY_ADMIN_REMOVE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'reorder'){
			$in_order = $um->get_param('PLUGIN_IMAGE_GALLERY_order');

			$response = $article_images_service->reorder($in_order);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_IMAGE_GALLERY_ADMIN_REORDER_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}
		return true;
	}


?>