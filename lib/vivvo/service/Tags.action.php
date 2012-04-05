<?php
/* =============================================================================
 * $Revision: 4835 $
 * $Date: 2010-03-30 14:02:50 +0200 (Tue, 30 Mar 2010) $
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

		"tag.add" => array(
			"function" => "tag_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Add new tag'
		),
		'tag.addGroup' => array(
			'function' => 'tag_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Add new tag group.'
		),
		"tag.edit" => array(
			"function" => "tag_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Edit tag.'
		),
		'tag.editGroup' => array(
			'function' => 'tag_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Edit tag group.'
		),
		"tag.delete" => array(
			"function" => "tag_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Delete tag.'
		),
		'tag.deleteGroup' => array(
			'function' => 'tag_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Delete tag group.'
		),
		"tag.deleteLink" => array(
			"function" => "tag_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Delete tag link.'
		),
		"tag.addLink" => array(
			"function" => "tag_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Add tag link.'
		),
		'tag.addTagToGroup' => array(
			'function' => 'tag_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Add tag to tag group'
		),
		'tag.addTagsToTopic' => array(
			'function' => 'tag_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Add tag to tag group'
		),
		'tag.removeTagFromGroup' => array(
			'function' => 'tag_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Remove tag from tag group'
		)
	);

	function tag_service(&$sm, $action, $command){

		require_once(dirname(__FILE__) . '/Tags.service.php');

		$tag_service = new tag_service($sm);

		$dm = $sm->get_dump_manager();
		$um = $sm->get_url_manager();
		$lang = $sm->get_lang();

		if ($command == 'add') {
			$name = $um->get_param('TAG_name');
			$sefriendly = $um->get_param('TAG_sefriendly');
			if ( $tag_service->add_tag($name, $sefriendly) ) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_TAG_ADD_SUCCESS'));
				vivvo_hooks_manager::call('tag_add_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'addGroup') {
			$name = $um->get_param('TAG_GROUP_name');
			$url = $um->get_param('TAG_GROUP_url');
			$template = $um->get_param('TAG_GROUP_template');
            $tag_template = $um->get_param('TAG_GROUP_tag_template');
			$metadata = $um->get_param_regexp('/^TAG_GROUP_META_/');
			$via_ajax = $um->isset_param('json_output');
			$new_tags = $um->get_param('TAG_GROUP_new_tags');
			if ( $group_id = $tag_service->add_tag_group($name, $url, $template, $tag_template, $metadata, $via_ajax, $new_tags) ) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_TAG_GROUP_ADD_SUCCESS'));
				vivvo_hooks_manager::call('tag_addGroup_postaction');
				header('X-Vivvo-Action-Status: 1');
				if ($via_ajax){
					exit('{"new_tags_group_id":"'.$group_id.'"}');
				}
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'edit') {
			$id = $um->get_param('TAG_id');
			$name = $um->get_param('TAG_name');
			$sefriendly = $um->get_param('TAG_sefriendly');
			if ( $tag_service->edit_tag($id, $name, $sefriendly) ) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_TAG_EDIT_SUCCESS'));
				vivvo_hooks_manager::call('tag_edit_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'editGroup') {
			$id = $um->get_param('TAG_GROUP_id');
			$name = $um->get_param('TAG_GROUP_name');
			$url = $um->get_param('TAG_GROUP_url');
			$template = $um->get_param('TAG_GROUP_template');
            $tag_template = $um->get_param('TAG_GROUP_tag_template');
			$metadata = $um->get_param_regexp('/^TAG_GROUP_META_/');
			$new_tags = $um->get_param('TAG_GROUP_new_tags');
			if ( $tag_service->edit_tag_group($id, $name, $url, $template, $tag_template, $metadata, $new_tags) ) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_TAG_GROUP_EDIT_SUCCESS'));
				vivvo_hooks_manager::call('tag_editGroup_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'delete') {
			if ($tag_service->delete_tag($um->get_param('TAG_id'), $um->get_param('all_matching'))) {
				$dm->_dump_pool = array();
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_TAG_DELETE_SUCCESS'));
				vivvo_hooks_manager::call('tag_delete_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'deleteGroup') {
			if ( $tag_service->delete_tag_group( $um->get_param('TAG_GROUP_id') ) ) {
				$dm->_dump_pool = array();
				$dm->add_dump( 'info', 0 , $lang->get_value('LNG_INFO_TAG_GROUP_DELETE_SUCCESS') );
				vivvo_hooks_manager::call('tag_deleteGroup_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'addTagToGroup') {
			$tag_id = $um->get_param('TAG_id');
			$group_id = $um->get_param('TAG_GROUP_id');
			if ( $tag_service->add_tag_to_group($tag_id, $group_id) ) {
				$dm->_dump_pool = array();
				$dm->add_dump( 'info', 0 , $lang->get_value('LNG_INFO_TAG_GROUP_LINK_SUCCESS') );
				vivvo_hooks_manager::call('tag_addTagToGroup_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} else if ($command == 'addTagsToTopic') {

			$tag_names = $um->get_param('TAG_names');
			$topic_id = $um->get_param('TAG_topic_id');
			if ($tag_service->add_tag_names_to_topic($tag_names, $topic_id)) {
				$dm->_dump_pool = array();
				$dm->add_dump('info', 0, $lang->get_value('LNG_INFO_TAG_GROUP_LINK_SUCCESS') );
				vivvo_hooks_manager::call('tag_addTagsToTopic_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'removeTagFromGroup') {
			$tag_id = $um->get_param('TAG_id');
			$group_id = $um->get_param('TAG_GROUP_id');
			if ( $tag_service->remove_tag_from_group($tag_id, $group_id) ) {
				$dm->_dump_pool = array();
				$dm->add_dump( 'info', 0 , $lang->get_value('LNG_INFO_TAG_GROUP_UNLINK_SUCCESS') );
				vivvo_hooks_manager::call('tag_removeTagFromGroup_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'deleteLink') {
			$tag_id = $um->get_param('TAG_link_id');
			if ( $tag_service->delete_tag_link($tag_id) ) {
				vivvo_hooks_manager::call('tag_deleteLink_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		} elseif ($command == 'addLink') {
			$article_id = $um->get_param('TAG_article_id');
			$name = $um->get_param('TAG_name');
			if ( $tag_service->add_tag_link($article_id, $name) ){
				vivvo_hooks_manager::call('tag_addLink_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}
			header('X-Vivvo-Action-Status: 0');
			header('X-Vivvo-Action-Error: ' . json_encode($tag_service->get_error_info()));
			return false;
		}
	}
?>