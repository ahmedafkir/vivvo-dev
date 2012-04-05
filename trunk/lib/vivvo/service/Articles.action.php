<?php
/* =============================================================================
 * $Revision: 5137 $
 * $Date: 2010-04-26 11:29:28 +0200 (Mon, 26 Apr 2010) $
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
		"article.add" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Add new article.'
		),
		"article.delete" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Delete article.'
		),
		"article.edit" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'integer', 'string')),
			"docstring" => 'Edit article.'
		),
		"article.reorder" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Reorder article.'
		),
		"article.normalize_order" => array(
			"function" => "article_service",
			"signature" => array(array('bool')),
			"docstring" => 'Normalize order article.'
		),
		"article.setField" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Set filed'
		),
		"article.deleteImage" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Delete article image'
		),
		"article.deleteDocument" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Delete article document'
		),
		"article.vote" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'ineteger', 'ineteger')),
			"docstring" => 'Vote on article'
		),
		"article.mail" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'ineteger', 'ineteger')),
			"docstring" => 'E-mail to a friend'
		),
		"article.purge_trashbin" => array(
			"function" => "article_service",
			"signature" => array(array('bool', 'ineteger')),
			"docstring" => 'Delete all articles in trashbin.'
		),
	);

	function article_service(&$sm, $action, $command)
	{
		require_once(dirname(__FILE__) . '/Articles.service.php');
		$article_service = new articles_service($sm);
		$um = $sm->get_url_manager();
		$dm = $sm->get_dump_manager();
		$lang = $sm->get_lang();

		$article_id = $um->get_param('article_id');

		if ($command == 'add') {
			$in_article = $um->get_param_regexp('/^ARTICLE_/');
			if ($um->get_param('publish') == 1 && intval($in_article['status']) < 1){
				$in_article['status'] = 1;
			}
			$response = $article_service->add_article($in_article);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_ADD_SUCCESS') . ' <a href="' . VIVVO_URL . 'index.php?news=' . $um->get_param('ARTICLE_id') . '" target="_blank">' . $lang->get_value('LNG_ADMIN_PREVIEW') . '</a>');
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_add_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		} elseif($command == 'delete') {
			if (is_array($um->get_param('ids'))) {
				$article_id = $um->get_param('ids');
			} else {
				$article_id = array($um->get_param('ARTICLE_id'));
			}

			$all_matching = $um->get_param('all_matching');

			$response = $article_service->delete_article($article_id, $all_matching);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_DELETE_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_delete_postaction');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		} elseif($command == 'edit') {
			$article_id = $um->get_param('ARTICLE_id');
			$in_article = $um->get_param_regexp('/^ARTICLE_/');

			if ($um->get_param('publish') == 1 && intval($in_article['status']) < 1){
				$in_article['status'] = 1;
			}
			$response = $article_service->edit_article($article_id, $in_article);
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_EDIT_SUCCESS') . ' <a href="'.VIVVO_URL.'index.php?news='.$article_id.'" target="_blank">' . $lang->get_value('LNG_ADMIN_PREVIEW') . '</a>');
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_edit_postaction');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		} elseif($command == 'reorder') {
			$in_article = $um->get_param('article_list');
			$response = $article_service->reorder_article($in_article);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_REORDER_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_reorder_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		}elseif($command == 'normalize_order'){
			$response = $article_service->normalize_order();
			if ($response){
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_normalizeOrder_postaction');
				return true;
			}else{
				return false;
			}
		}elseif($command == 'setField'){
			$articles_ids = $um->get_param('ids');

			$field_name = $um->get_param('field_name');
			$filed_value = $um->get_param('field_value');

			$all_matching = $um->get_param('all_matching');

			$response = $article_service->set_field($articles_ids, $field_name, $filed_value, $all_matching);
			if ($response){
				if ($field_name == 'status' and $filed_value == -2) {
					$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_TRASH_SUCCESS'));
				} else {
					$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_SET_FIELD_SUCCESS'));
				}
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_setField_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		}elseif($command == 'deleteImage'){
			$article_id = $um->get_param('ARTICLE_id');
			$response = $article_service->delete_image($article_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_DELETE_IMAGE_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_deleteImage_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		}elseif($command == 'deleteDocument'){
			$article_id = $um->get_param('ARTICLE_id');
			$response = $article_service->delete_document($article_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_DELETE_DOCUMENT_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_deleteDocument_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'vote'){
			$article_id = $um->get_param('ARTICLE_id');
			$vote = $um->get_param('ARTICLE_vote');
			$response = $article_service->vote($article_id, $vote);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_ARTICLE_VOTE_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_vote_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'mail'){
			$article_id = $um->get_param('ARTICLE_id');
			$article_to = $um->get_param('ARTICLE_to');
			$article_bcc = $um->get_param('ARTICLE_bcc');
			$article_your_email = $um->get_param('ARTICLE_your_email');
			$article_message = $um->get_param('ARTICLE_message');
			$response = $article_service->email_to_a_friend($article_id, $article_to, $article_bcc, $article_your_email, $article_message);
			if ($response){
				$mail_sent = str_replace('<MAIL_TO>', $article_to, $lang->get_value('LNG_INFO_ARTICLE_E_MAIL_TO_A_FRIEND_SENT_SUCCESS'));
				$dm->add_dump('info', 0 , $mail_sent);
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_mail_postaction');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		} elseif ($command == 'purge_trashbin') {
			$response = $article_service->purge_trashbin();
			if ($response) {
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_TRASHBIN_DELETE_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('article_delete_postaction');
				return true;
			} else {
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($article_service->get_error_info()));
				return false;
			}
		}
		return true;
	}
?>