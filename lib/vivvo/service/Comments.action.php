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
		"comment.add" => array(
			"function" => "comment_service",
			"signature" => array(array('bool', 'string','integer')),
			"docstring" => 'Add new comment.'
		),
		"comment.delete" => array(
			"function" => "comment_service",
			"signature" =>  array(array('bool', 'integer')),
			"docstring" => 'Delete comment.'
		),
		"comment.edit" => array(
			"function" => "comment_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Edit comment.'
		),
		"comment.setField" => array(
			"function" => "comment_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Set filed'
		),
		"comment.reportInappropriateContent" => array(
			"function" => "comment_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Set filed'
		),
		"comment.vote" => array(
			"function" => "comment_service",
			"signature" => array(array('bool', 'ineteger', 'ineteger')),
			"docstring" => 'Vote'
		),
		'comment.proxy' => array(
			'function' => 'comment_service',
			'signature' => array(array('bool', 'ineteger', 'ineteger')),
			'docstring' => ''
		)
	);


	function comment_service(&$sm, $action, $command){

		require_once(dirname(__FILE__) . '/Comments.service.php');
		$comment_service =& new comment_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();

		if ($command == 'add'){
			$in_comment = $um->get_param_regexp('/^COMMENT_/');
			$captcha = $um->get_param('COMMENT_captcha');
			$response = $comment_service->add_comment($in_comment, $captcha);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_COMMENT_ADD_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('comment_add_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($comment_service->get_error_info()));
				return false;
			}
		}elseif($command == 'delete'){
			if (is_array($um->get_param('ids'))){
				$comment_id = $um->get_param('ids');
			}else{
				$comment_id = array($um->get_param('COMMENT_id'));
			}
			$all_matching = $um->get_param('all_matching');
			$response = $comment_service->delete_comment($comment_id, $all_matching);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_COMMENT_DELETE_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('comment_delete_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($comment_service->get_error_info()));
				return false;
			}
		}elseif($command == 'edit'){
			$comment_id = $um->get_param('COMMENT_id');
			$in_comment = $um->get_param_regexp('/^COMMENT_/');
			$response = $comment_service->edit_comment($comment_id, $in_comment);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_COMMENT_EDIT_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('comment_edit_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($comment_service->get_error_info()));
				return false;
			}
		}elseif($command == 'setField'){
			$comments_ids = $um->get_param('ids');
			$all_matching = $um->get_param('all_matching');
			$field_name = $um->get_param('field_name');
			$field_value = $um->get_param('field_value');
			$response = $comment_service->set_field($comments_ids, $field_name, $field_value, $all_matching);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_COMMENT_SET_FIELD_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('comment_setField_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($comment_service->get_error_info()));
				return false;
			}
		}elseif($command == 'reportInappropriateContent'){
			$comment_id = $um->get_param('COMMENT_id');
			$response = $comment_service->report_inappropriate_content($comment_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_COMMENT_REPORTING_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('comment_reportContent_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($comment_service->get_error_info()));
				return false;
			}
		}elseif($command == 'vote'){

			$comment_id = $um->get_param('COMMENT_id');
			$vote = $um->get_param('COMMENT_vote');

			$response = $comment_service->vote($comment_id, $vote);
			if ($response){
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('comment_vote_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($comment_service->get_error_info()));
				return false;
			}
		} elseif ($command == 'proxy') {
			class _fakeArticle__ {
				public $get_id;
				public function __get($name) {
					return $this->get_id;
				}
			}
			$fake_article = new _fakeArticle__;
			$fake_article->get_id = $um->get_param('article_id');
			$sm->get_template()->assign('pg', $um->get_param('pg'));
			$sm->get_template()->assign('CURRENT_URL', $um->get_param('CURRENT_URL'));
			$sm->get_template()->assign('article', $fake_article);
			$sm->get_template()->assign('ajax_output', intval(1));

			vivvo_hooks_manager::call('comment_proxy_postaction');
		}
		return true;
	}
?>