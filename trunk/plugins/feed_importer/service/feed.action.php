<?php
/* =============================================================================
 * $Revision: 5360 $
 * $Date: 2010-05-20 16:47:48 +0200 (Thu, 20 May 2010) $
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
	 * @var	array	Service definition array
	 */
	$service_definition = array(
		'feed.edit' => array(
			'function' => 'feed_service',
			'signature' => array(array('bool', 'integer', 'string')),
			'docstring' => 'Edit feeds.'
		),
		'feed.preferences' => array(
			'function' => 'feed_service',
			'signature' => array(array('bool', 'string')),
			'docstring' => 'Edit plugin preferences.'
		),
	);


	/**
	 * Action handler function
	 *
	 * @param	&vivvo_lite_site	$sm
	 * @param	string				$action
	 * @param	string				$command
	 * @return	bool
	 */
	function feed_service($sm, $action, $command) {

		if ($action != 'feed') {
			return false;
		}

		require_once	dirname(__FILE__) . '/feed.service.php';

		$service = new feed_service($sm);

		$um = $sm->get_url_manager();
		$dm = $sm->get_dump_manager();
		$lang = vivvo_lang::get_instance();

		if ($command == 'edit') {
			$feed_id = $um->get_param('PLUGIN_FEED_id');
			$in_feed = $um->get_param_regexp('/^PLUGIN_FEED_/');

			if ($service->edit_feed($feed_id, $in_feed)) {
				$dm->add_dump('info', 0, $lang->get_value('LNG_INFO_PLUGIN_FEED_EDIT_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('feed_edit_postaction');
				return true;
			}

		} elseif ($command  == 'preferences') {

			if (is_array($um->list) and !empty($um->list)) {
				foreach ($um->list as $key => $value) {
					if (preg_match('/^(VIVVO_PLUGIN_FEED_IMPORTER_)/', $key)) {
						if (!($response = $service->preferences_feed($key, $um->list[$key]))) {
							break;
						}
					}
				}
			}

			if (!empty($response)) {
				$dm->add_dump('info', 0, $lang->get_value('LNG_INFO_PLUGIN_FEED_PREFERENCES_SUCCESS'));
				header('X-Vivvo-Action-Status: 1');
				vivvo_hooks_manager::call('feed_preferences_postaction');
				return true;
			}
		}

		header('X-Vivvo-Action-Status: 0');
		header('X-Vivvo-Action-Error: ' . json_encode($service->get_error_info()));
		return false;
	}

#EOF