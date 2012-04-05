<?php
/* =============================================================================
 * $Revision: 5074 $
 * $Date: 2010-04-22 16:02:05 +0200 (Thu, 22 Apr 2010) $
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
 * Vivvo CMS
 *
 * LICENSE:
 *
 * @package    Vivvo
 * @subpackage service
 * @author     Slobodan Utvic <utvara@spoonlabs.com>
 * @copyright  Spoonlbas
 * @license    http://www.vivvo.net/license.php
 * @version    0.1
 * @category   CMS
 */

	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");


	/**
	 * Vivvo core service class
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage service
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	*/
	class vivvo_core_service extends vivvo_service {

		function edit_preferences($variable_name,$variable_value){
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('vivvoCore_preferences', array(&$variable_name, &$variable_value))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

			if ($this->_user && $this->_user->is_admin()){
				require_once (VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');
				$preferences_list = new preferences_list();
				$preferences = $preferences_list->get_preference_by_variable_name($variable_name);

				if ($preferences != false){
					$variable_value = htmlspecialchars($variable_value, ENT_QUOTES, 'UTF-8');
					if ($variable_name == 'VIVVO_GENERAL_TIME_ZONE_FORMAT') {
						$timezone_abbreviations = DateTimeZone::listIdentifiers();

						if (in_array($variable_value, $timezone_abbreviations)) {
							$preferences->set_variable_value($variable_value);
							$this->_post_master->set_data_object($preferences);
						} else {
							$this->set_error_code(5122);
							return false;
						}
					} elseif ($variable_name == 'VIVVO_ALLOWED_IP_ADDRESSES') {
						// normalize passed string (convert to comma-separated IPs)
						$variable_value = str_replace(array("\r\n", "\n"), ',', $variable_value);
						$variable_value = preg_replace('/\s+/', '', $variable_value);
						$variable_value = trim(preg_replace('/,+/', ',', $variable_value), ',');

						if (!empty($variable_value)) {
							$addresses = explode(',', $variable_value);

							// check valid IP format (with wildcards)
							foreach ($addresses as $addr) {
								$parts = explode('.', $addr);
								if (count($parts) == 4) {
									foreach ($parts as $part) {
										if ($part == '*' or (is_numeric($part) and ($part = (int)$part) > -1 and $part < 256)) {
											continue;
										}
										$this->set_error_code(5120);
										return false;
									}
								} else {
									$this->set_error_code(5120);
									return false;
								}
							}

							// prevent user blocking itself
							$listed = false;
							foreach ($addresses as $addr) {
								if (fnmatch($addr, $this->_user->ip)) {
									$listed = true;
									break;
								}
							}
							if ($listed == false) {
								$this->set_error_code(5119);
								return false;
							}

							unset($addresses, $addr, $parts, $part);
						}

						$preferences->set_variable_value($variable_value);
						$this->_post_master->set_data_object($preferences);

					} elseif ($variable_name == 'VIVVO_EMAIL_SMTP_PHP' && $variable_value == 0) {

						$um = $sm->get_url_manager();
						$lang = vivvo_lang::get_instance();

						$missing = array();

						$host = $um->get_param('VIVVO_EMAIL_SMTP_HOST');
						$username = $um->get_param('VIVVO_EMAIL_SMTP_USERNAME');
						$password = $um->get_param('VIVVO_EMAIL_SMTP_PASSWORD');

						empty($host) and $missing[] = $lang->get_value('LNG_CONF_VIVVO_EMAIL_SMTP_HOST');
						empty($username) and $missing[] = $lang->get_value('LNG_CONF_VIVVO_EMAIL_SMTP_USERNAME');
						empty($password) and $missing[] = $lang->get_value('LNG_CONF_VIVVO_EMAIL_SMTP_PASSWORD');

						if (!empty($missing)) {

							$this->set_error_code(5121, implode(', ', $missing));

							$template = $sm->get_template();
							$template->assign('VIVVO_EMAIL_SMTP_HOST', $host);
							$template->assign('VIVVO_EMAIL_SMTP_USERNAME', $username);
							$template->assign('VIVVO_EMAIL_SMTP_PASSWORD', $password);

							return false;
						}

						$preferences->set_variable_value($variable_value);
						$this->_post_master->set_data_object($preferences);

					} elseif ($variable_name == 'VIVVO_GA_ENABLED' and $variable_value) {

						$um = $sm->get_url_manager();
						$lang = vivvo_lang::get_instance();

						$missing = array();

						$email = $um->get_param('VIVVO_GA_EMAIL');
						$password = $um->get_param('VIVVO_GA_PASSWORD');
						$code = $um->get_param('VIVVO_GA_CODE');

						empty($email) and $missing[] = $lang->get_value('LNG_CONF_VIVVO_GA_EMAIL');
						empty($password) and $missing[] = $lang->get_value('LNG_CONF_VIVVO_GA_PASSWORD');
						empty($code) and $missing[] = $lang->get_value('LNG_CONF_VIVVO_GA_CODE');

						if (!empty($missing)) {

							$this->set_error_code(5123, implode(', ', $missing));	// missing google analytics parameter(s)

							$template = $sm->get_template();
							$template->assign('VIVVO_GA_EMAIL', $email);
							$template->assign('VIVVO_GA_PASSWORD', $password);
							$template->assign('VIVVO_GA_CODE', $code);

							return false;
						}

						$params = array(
							'username' => $email,
							'password' => $password,
							'no_auth' => true
						);

						if (vivvo_ga::get_instance($params)->authorize($email, $password, true) == false) {

							$this->set_error_code(5124);	// google analytics authentication failed.

							$template = $sm->get_template();
							$template->assign('VIVVO_GA_EMAIL', $email);
							$template->assign('VIVVO_GA_PASSWORD', $password);
							$template->assign('VIVVO_GA_CODE', $code);

							return false;
						}

						$preferences->set_variable_value($variable_value);
						$this->_post_master->set_data_object($preferences);

					} elseif ($preferences->check_value ($variable_value)) {
						$preferences->set_variable_value($variable_value);
						$this->_post_master->set_data_object($preferences);
					} else {
						$preferences->set_error_code();
						return false;
					}

					if ($this->_post_master->sql_update()) {
						admin_log($sm->user->get_username(), 'Edited vivvo preferences.');
						return true;
					} else {
						$this->set_error_code(5101);
						return false;
					}
				} else {
					$this->set_error_code(5102, $variable_name);
					return false;
				}
			} else {
				$this->set_error_code(5103);
				return false;
			}
		}

		function edit_cron_task($in_task) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('vivvoCore_cronTask', array(&$in_task))) {
				return vivvo_hooks_manager::get_status();
			}

			if ($this->_user && $this->_user->is_admin()) {
				if (isset($in_task['name'])) {
					$sm = vivvo_lite_site::get_instance();
					$_config = $sm->get_configuration();
					$cron_tasks = $_config->get_configuration_property_list('cron_task');

					if (array_key_exists($in_task['name'], $cron_tasks)){
						require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php');
						$cm = new vivvo_cron_manager();
						if (trim($in_task['time_mask']) != ''){
							//edit
							$cm->cron_job($in_task['time_mask'], $cron_tasks[$in_task['name']]['file'], $cron_tasks[$in_task['name']]['class'], $cron_tasks[$in_task['name']]['method'], unserialize($cron_tasks[$in_task['name']]['arguments']));
							admin_log($sm->user->get_username(), 'Edited cron #' . $in_task['name']);
						}else{
							//delete
							$cm->delete_cron_job($cron_tasks[$in_task['name']]['file'], $cron_tasks[$in_task['name']]['class'], $cron_tasks[$in_task['name']]['method'], unserialize($cron_tasks[$in_task['name']]['arguments']));
							admin_log($sm->user->get_username(), 'Deleted cron #' . $in_task['name']);
						}
						return true;
					}else{
						$this->set_error_code(5104);
						return false;
					}
				}else{
					$this->set_error_code(5105);
					return false;
				}
			}else {
				$this->set_error_code(5106);
				return false;
			}
		}

		function group_add($in_group) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('vivvoCore_groupAdd', array(&$in_group))) {
				return vivvo_hooks_manager::get_status();
			}

			if ($this->_user && $this->_user->is_admin()) {
				if (VIVVO_USER_SOURCE == 'vivvo@localhost'){
					$sm = vivvo_lite_site::get_instance();
					$user_manager = $sm->get_user_manager();

					$new_group = $user_manager->get_group_object();
					$new_group->name = $in_group['name'];
					$this->_post_master->set_data_object($new_group);

					if ($this->_post_master->sql_insert()){
						$id = $this->_post_master->get_work_id();
						$new_group->id = $id;

						if( !vivvo_hooks_manager::call('vivvoCore_groupAdd_postinsert', array(&$in_group) ) )
							return vivvo_hooks_manager::get_status();

						if ($user_manager->save_group_privileges($new_group, $in_group['privileges'])){
							admin_log($sm->user->get_username(), 'Added group #' . $id);
							return true;
						}else{
							$this->set_error_code(5107);
							return false;
						}
					}else{
						$this->set_error_code(5108);
						return false;
					}
				}else{
					$this->set_error_code(5109);
					return false;
				}
			}else{
				$this->set_error_code(5110);
				return false;
			}
		}

		function group_delete($group_id){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('vivvoCore_groupDelete', array(&$group_id) ) )
				return vivvo_hooks_manager::get_status();


			if ($this->_user && $this->_user->is_admin()) {
				if (VIVVO_USER_SOURCE == 'vivvo@localhost'){
					$sm = vivvo_lite_site::get_instance();
					$user_manager = $sm->get_user_manager();
					$group = $user_manager->get_group_by_id($group_id);

					if ($group) {
						$this->_post_master->set_data_object($group);
						if ($this->_post_master->sql_delete()){
							admin_log($sm->user->get_username(), 'Deleted group #' . $group_id);
							return true;
						}else{
							$this->set_error_code(5111);
							return false;
						}
					}else{
						$this->set_error_code(5112);
						return false;
					}
				}else{
					$this->set_error_code(5113);
					return false;
				}
			}else{
				$this->set_error_code(5114);
				return false;
			}
		}

		function group_edit($group_id, $in_group){
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('vivvoCore_groupEdit', array(&$group_id, &$in_group))) {
				return vivvo_hooks_manager::get_status();
			}

			if ($this->_user && $this->_user->is_admin()) {
				$sm = vivvo_lite_site::get_instance();
				$user_manager = $sm->get_user_manager();
				$group = $user_manager->get_group_by_id($group_id);
				if ($group) {
					if (VIVVO_USER_SOURCE == 'vivvo@localhost') {
						$group->name = $in_group['name'];
						$this->_post_master->set_data_object($group);

						if (!$this->_post_master->sql_update()){
							$this->set_error_code(5115);
							return false;
						}
					}
					if ($user_manager->save_group_privileges($group, $in_group['privileges'])){
						admin_log($sm->user->get_username(), 'Edited group #' . $group_id);
						return true;
					}else{
						$this->set_error_code(5116);
						return false;
					}
				}else{
					$this->set_error_code(5117);
					return false;
				}
			}else{
				$this->set_error_code(5118);
				return false;
			}
		}
	}
?>