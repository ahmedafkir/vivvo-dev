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
	 * @see  '/../core/Feeds.class.php'
 	 */
	require_once dirname(__FILE__) . '/../core/Feeds.class.php';

	/**
	 * @see  'lib/vivvo/framework/vivvo_service.class.php'
 	 */
	require_once VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php';

	/**
	 * Feed Importer service class
	 *
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class feed_service extends vivvo_service {

		/**
		 * Edit feed
		 *
		 * @param	integer	$feed_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		public function edit_feed($feed_id, $data) {

			if (!$this->check_token()) {
				return false;
			}

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user and $sm->user->can('MANAGE_PLUGIN', 'feed_importer')) {

				if (!vivvo_hooks_manager::call('feed_edit', array(&$feed_id, &$data))) {
					return vivvo_hooks_manager::get_status();
				}

				$feed_list = new Feeds_list();
				$feed_list->search(array());

				if (!empty($data['feed'])) {
					$remove_keys = array_diff(array_keys($feed_list->list), array_keys($data['feed']));
				} else {
					$remove_keys = array_keys($feed_list->list);
				}

				if (!empty($remove_keys)) {
					$feed_list->sql_delete_list($this->_post_master, $remove_keys);
				}

				$edit_keys = $feed_list->get_list_ids();
				$feed_check = array();

				require_once VIVVO_FS_INSTALL_ROOT . 'lib/simplepie/simplepie.php';

				if (is_array($edit_keys) and !empty($edit_keys)) {

					foreach ($edit_keys as $edit_key) {

						if (!in_array($data['feed'][$edit_key]['feed'], $feed_check)) {

							$feed_check[] = $data['feed'][$edit_key]['feed'];
							$feed_list->list[$edit_key]->set_feed($data['feed'][$edit_key]['feed']);
							$feed_list->list[$edit_key]->set_category_id($data['feed'][$edit_key]['category_id']);
							$feed_list->list[$edit_key]->set_author($data['feed'][$edit_key]['author']);

							$simplepie = new SimplePie();
							$simplepie->enable_cache(false);
							$simplepie->set_feed_url($data['feed'][$edit_key]['feed']);
							@$simplepie->init();

							if (!$simplepie->error()) {
								$feed_list->list[$edit_key]->set_favicon($simplepie->get_favicon());
								$this->_post_master->set_data_object($feed_list->list[$edit_key]);
								$this->_post_master->sql_update();
							}
						} else {
							$this->_post_master->set_data_object($feed_list->list[$edit_key]);
							$this->_post_master->sql_delete();
						}
					}
				}

				if (is_array($data['new_feed']) and !empty($data['new_feed'])) {

					foreach ($data['new_feed'] as $add_key => $value) {

						if (!in_array($data['new_feed'][$add_key]['feed'], $feed_check)) {

							$feed_check[] = $data['new_feed'][$add_key]['feed'];
							$new_feed_object = new Feeds();
							$new_feed_object->set_feed($data['new_feed'][$add_key]['feed']);
							$new_feed_object->set_category_id($data['new_feed'][$add_key]['category_id']);
							$new_feed_object->set_author($data['new_feed'][$add_key]['author']);

							$simplepie = new SimplePie();
							$simplepie->enable_cache(false);
							$simplepie->set_feed_url($data['new_feed'][$add_key]['feed']);
							@$simplepie->init();

							if (!$simplepie->error()) {
								$new_feed_object->set_favicon($simplepie->get_favicon());
								$this->_post_master->set_data_object($new_feed_object);
								$this->_post_master->sql_insert();
							}
						}
					}
				}
				return true;
			} else {
				$this->set_error_code(10103);	// you don't have sufficient privileges for this action
				return false;
			}
		}

		/**
		 * Edit plugin preferences.
		 *
		 * @param	string	$variable_name
		 * @param	int		$variable_value
		 * @return	bool	true on succes or false on failure
		 */
		public function preferences_feed($variable_name, $variable_value) {

			if (!$this->check_token()) {
				return false;
			}

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user and $sm->user->can('MANAGE_PLUGIN', 'feed_importer')) {

				if (!vivvo_hooks_manager::call('feed_preferences', array(&$variable_name, &$variable_value))) {
					return vivvo_hooks_manager::get_status();
				}

				require_once VIVVO_FS_FRAMEWORK . 'vivvo_preference.php';

				$preferences_list = new preferences_list();
				$preferences = $preferences_list->get_preference_by_variable_name($variable_name);

				if ($preferences != false) {

					if ($variable_name == 'VIVVO_PLUGIN_FEED_IMPORTER_USER_ID' and $variable_value < 0) {
						$variable_value = 1;
					} elseif ($variable_name == 'VIVVO_PLUGIN_FEED_IMPORTER_STATUS' and $variable_value != 1) {
						$variable_value = 0;
					} elseif ($variable_name == 'VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE' and $variable_value <= 0) {
						$variable_value = 0;
					} elseif ($variable_name == 'VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE' and $variable_value <= 0) {
						$variable_value = 0;
					} elseif ($variable_name == 'VIVVO_PLUGIN_FEED_IMPORTER_PERIOD' and ($variable_value = (int)$variable_value) <= 0) {
						$variable_value = 1;
					}

					$preferences->set_variable_value($variable_value);
					$this->_post_master->set_data_object($preferences);

					if ($this->_post_master->sql_update()) {
						if ($variable_name == 'VIVVO_PLUGIN_FEED_IMPORTER_PERIOD') {
							$pm = $sm->get_plugin_manager();
							$cm = $sm->get_cron_manager();
							if ($variable_value == 1) {
								$variable_value = '*';
							} else {
								$variable_value = '*/' . $variable_value;
							}
							$cm->cron_job("19 $variable_value * * *", $pm->plugins['feed_importer']['root_dir'] . 'tasks/auto_import_feeds.php', null, 'auto_import_feeds');
						}
						return true;
					} else {
						$this->set_error_code(10101);	// can't update plugin preferences
						return false;
					}
				} else {
					$this->set_error_code(10102);	// preference does not exists
					return false;
				}
			} else {
				$this->set_error_code(10103);	// you don't have sufficient privileges for this action
				return false;
			}
		}
	}

#EOF