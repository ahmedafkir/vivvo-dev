<?php
/* =============================================================================
 * $Revision: 5423 $
 * $Date: 2010-05-28 15:00:49 +0200 (Fri, 28 May 2010) $
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
	 * Service definition for newsletter plugin
	 */
	$service_definition = array(
		"newsletter.subscriberAdd" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Add subscriber.'
		),
		"newsletter.subscriberConfirm" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Confirm subscriber.'
		),
		"newsletter.subscriberUnSubscribe" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Unsubscribe.'
		),
		"newsletter.subscriberUserAdd" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Add user-subscriber.'
		),
		"newsletter.subscriberUserRemove" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Remove user-subscriber.'
		),
		"newsletter.userUnSubscribe" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Unsubscribe user.'
		),
		"newsletter.setField" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Set filed(s)'
		),
		"newsletter.add" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'array')),
			"docstring" => 'Add newsletter.'
		),
		"newsletter.edit" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'array','integer')),
			"docstring" => 'Edit newsletter.'
		),
		"newsletter.delete" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'integer')),
			"docstring" => 'Delete newsletter.'
		),
		"newsletter.send" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'array', 'integer')),
			"docstring" => 'Send newsletter.'
		),
		"newsletter.subscriberDelete" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string')),
			"docstring" => 'Delete subscriber(s).'
		),
		"newsletter.preferences" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Set preferences'
		),
		"newsletter.export" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Export maillist'
		),
		"newsletter.import" => array(
			"function" => "newsletter_service",
			"signature" => array(array('bool', 'string', 'string', 'string')),
			"docstring" => 'Importing mails'
		)



	);

	function newsletter_service($sm, $action, $command){
		require_once(dirname(__FILE__) . '/newsletter.service.php');
		$newsletter_service = new newsletter_service($sm);

		$um = $sm->get_url_manager();
		$dm = $sm->get_dump_manager();
		$lang = $sm->get_lang();

		$pm = $sm->get_plugin_manager();
		$lang->load_lang(VIVVO_DEFAULT_LANG, $pm->plugins['newsletter']['root_dir'] . 'lang/');

		if ($command == 'subscriberAdd'){

			$in_subscriber = $um->get_param_regexp('/^PLUGIN_NEWSLETTER_/');
			$response = $newsletter_service->add_subscriber($in_subscriber);

			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_ADD_SUBSCRIBER_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'subscriberConfirm'){

			$activation_code = $um->get_param('ack');

			$response = $newsletter_service->confirm_subscriber($activation_code);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_CONFIRM_SUBSCRIBER_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'subscriberUnSubscribe'){

			$deactivation_code = $um->get_param('dack');

			$response = $newsletter_service->unsubscribe_subscriber($deactivation_code);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_UNSUBSCRIBE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'subscriberUserAdd'){

			$response = $newsletter_service->add_user_subscriber();
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_ADD_USER_SUBSCRIBER_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'subscriberUserRemove'){

			$response = $newsletter_service->remove_user_subscriber();
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_REMOVE_USER_SUBSCRIBER_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'userUnSubscribe'){

			$deactivation_code = $um->get_param('dack');
			$response = $newsletter_service->unsubscribe_user($deactivation_code);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_USER_UNSUBSCRIBE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'subscriberDelete'){

			if (is_array($um->get_param('ids'))){
				$subscribers_ids = $um->get_param('ids');
			}else{
				$subscribers_ids = array($um->get_param('SUBSCRIBER_id'));
			}

			$response = $newsletter_service->delete_subscriber($subscribers_ids);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_DELETE_SUBSCRIBER_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif($command == 'setField'){
			if (is_array($um->get_param('ids'))){
				$subscribers_ids = $um->get_param('ids');
			}else{
				$subscribers_ids = array($um->get_param('SUBSCRIBER_id'));
			}
			$field_name = $um->get_param('field_name');
			$filed_value = $um->get_param('field_value');

			$response = $newsletter_service->set_field($subscribers_ids, $field_name, $filed_value);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_EDIT_FIELD_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'add'){
			$in_newsletter = $um->get_param_regexp('/^PLUGIN_NEWSLETTER_/');

			$response = $newsletter_service->add_newsletter($in_newsletter);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_ADD_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'edit'){

			$newsletter_id = $um->get_param('PLUGIN_NEWSLETTER_id');
			$in_newsletter = $um->get_param_regexp('/^PLUGIN_NEWSLETTER_/');

			$response = $newsletter_service->edit_newsletter($in_newsletter, $newsletter_id);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_EDIT_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'delete'){

			$newsletter_id = $um->get_param('PLUGIN_NEWSLETTER_id');
			$response = $newsletter_service->delete_newsletter($newsletter_id);

			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_DELETE_SUCCESS'));
				return true;
			}else{
				return false;
			}
		}elseif ($command == 'send'){

			$newsletter_id = $um->get_param('PLUGIN_NEWSLETTER_id');
			$in_newsletter = $um->get_param_regexp('/^PLUGIN_NEWSLETTER_/');

			if ($newsletter_id > 0){
				$response = $newsletter_service->edit_newsletter($in_newsletter, $newsletter_id);
			}else{
				$response = $newsletter_service->add_newsletter($in_newsletter);
				if ($response){
					$newsletter_id = $response;
				}else{
					$newsletter_id = 0;
				}
			}

			if ($response){
				$response = $newsletter_service->send_newsletter($newsletter_id);
				if ($response){
					if ($in_newsletter['test'] == 1){
						$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_SEND_TEST_MAIL_SUCCESS'));
					}else{
						$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_PREPARE_MAILS_FOR_SEND'));
					}
					return true;
				}else{
					return false;
				}
			}else{
				return false;
			}
		}elseif($command == 'preferences'){
			if (is_array($um->list) && !empty($um->list)){
				foreach ($um->list as $key => $value){
					if (preg_match('/^(VIVVO_PLUGIN_NEWSLETTER_)/', $key)){
						$response = $newsletter_service->preferences_newsletter($key, $um->list[$key]);
						if (!$response){
							break;
						}
					}
				}
			}
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_PREFERENCES_SUCCESS'));
				return true;
			}else{
				return  false;
			}
		}elseif ($command == 'export'){
			$response = $newsletter_service->export();
		}elseif ($command == 'import'){

			$response = $newsletter_service->import();

			if ($response){
					$dm->add_dump('info', 0 , str_replace('<NUM_OF_EMAILS>',($response - 1), $lang->get_value('LNG_INFO_PLUGIN_NEWSLETTER_IMPORT_MAILS_SUCCESS')));
				return true;
			}else{
				return false;
			}
		}
	}

?>