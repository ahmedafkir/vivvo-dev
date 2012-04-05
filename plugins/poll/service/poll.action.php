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
	 * Service definition for poll plugin
	 */
	$service_definition = array(
		"poll.delete" => array(
			"function" => "poll_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Delete poll.'
		),
		"poll.activate" => array(
			"function" => "poll_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Activate poll.'
		),
		"poll.deactivate" => array(
			"function" => "poll_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Deactivate poll.'
		),
		"poll.edit" => array(
			"function" => "poll_service",
			"signature" => array(array('bool', 'integer','string')),
			"docstring" => 'Edit poll.'
		),
		"poll.add" => array(
			"function" => "poll_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Add poll.'
		),
		"poll.vote" => array(
			"function" => "poll_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Vote.'
		)
	);


	function poll_service(&$sm, $action, $command){
		require_once(dirname(__FILE__) . '/poll.service.php');
		$poll_service = new poll_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();


		if ($command == 'add'){
			$in_poll = $um->get_param_regexp('/^PLUGIN_POLL_/');
			$response = $poll_service->add_poll($in_poll);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_POLL_ADD_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'edit'){
			$poll_id = $um->get_param('PLUGIN_POLL_id');
			$in_poll = $um->get_param_regexp('/^PLUGIN_POLL_/');
			$response = $poll_service->edit_poll($poll_id, $in_poll);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_POLL_EDIT_SUCCESS'));
				return true;
			}else{
				return  false;
			}
		}elseif ($command  == 'delete'){
			$poll_id = $um->get_param('PLUGIN_POLL_id');
			$response = $poll_service->delete_poll($poll_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_POLL_DELETE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command  == 'activate'){
			$poll_id = $um->get_param('PLUGIN_POLL_id');
			$response = $poll_service->activate($poll_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_POLL_ACTIVATE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'deactivate'){
			$poll_id = $um->get_param('PLUGIN_POLL_id');
			$response = $poll_service->deactivate($poll_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_POLL_DEACTIVATE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command  ==  'vote'){
			$answer_id = $um->get_param('PLUGIN_POLL_answer_id');
			$response = $poll_service->poll_vote($answer_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_POLL_ANSWER_VOTE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}
		return true;
	}


?>