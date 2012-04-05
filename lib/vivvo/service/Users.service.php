<?php
/* =============================================================================
 * $Revision: 6887 $
 * $Date: 2011-05-18 15:11:09 +0200 (Wed, 18 May 2011) $
 *
 * Vivvo CMS v4.6 (build 6082)
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
	 * @see  'lib/vivvo/core/Users.class.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Users.class.php');
	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");

	/**
	 * Users service class
	 * Add user, delete user, edit user, set mass fields, delete user abstract image
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage service
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	 * @author Vedran Martinovic <vmartinovic@spoonlabs.com>
	*/
	class user_service extends vivvo_service {

		/**
		 * Add new user with abstract image if is uploaded
		 *
		 * @param array $in_user
		 * @return mixed array on succes, or false on fail
		 */
		function add_user($in_user){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('user_add', array(&$in_user) ) )
				return vivvo_hooks_manager::get_status();


			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){

					$groups = $in_user['groups'];
					unset($in_user['groups']);

					if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $in_user['email_address'])){
						$this->set_error_code(2301);
						return false;
					}

					$user_list = new Users_list();

					if ($user_list->get_email_exists($in_user['email_address'])){
						$this->set_error_code(2302);
						return false;
					}

					if (preg_match("/[^a-zA-Z0-9\_\-]/",$in_user['username'])){
						$this->set_error_code(2303);
						return false;
					}

					$user_list = new Users_list();
					if ($user_list->get_user_exists($in_user['username'])) {
						$this->set_error_code(2705);
						return false;
					}

					if (strlen($in_user['password']) < 6) {
						$this->set_error_code(2304);
						return false;
					}

					if ($in_user['password'] != $in_user['retype_password']) {
						$this->set_error_code(2305);
						return false;
					}

					$in_user['password'] = md5($in_user['password']);
					$in_user['created'] = date('Y-m-d H:i:s');

					array_walk($in_user, 'array_htmlspecialchars');

					$user = new Users($sm);
					if ($user->populate($in_user, true) === false) {
						$this->set_error_info($user->get_error_info());
						return false;
					}

					$this->_post_master->set_data_object($user);

					if ($this->_post_master->sql_insert()) {
						$id = $this->_post_master->get_work_id();

						$user->set_userid($id);
						$sm->get_url_manager()->set_param('search_id', $id);

						if (!vivvo_hooks_manager::call('user_add_postinsert', array(&$user))) {
							return vivvo_hooks_manager::get_status();
						}

						$user_manager = $sm->get_user_manager();
						$user_manager->set_user_groups($id, $groups);

						$fm = $sm->get_file_manager();
						if ($fm->is_uploaded('USER_picture')) {
							$user_image = $fm->upload('USER_picture','','jpg,jpeg,gif,png');
							if ($user_image != false) {
								$user->set_picture($user_image);
								$this->_post_master->set_data_object($user);
								$this->_post_master->sql_update();
							}
						}
						admin_log($sm->user->get_username(), 'Added user #' . $id);
						return true;
					}else{
						$this->set_error_code(2306);
						return false;
					}
				}else{
					$this->set_error_code(2307);
					return false;
				}
			}else{
				$this->set_error_code(2308);
				return false;
			}
		}

		/**
		 * Delete user
		 *
		 * @param integer $user_id
		 * @return mixed array on succes, or false on fail
		 */
		function delete_user($user_id, $all_matching = 0) {

			if (!$this->check_token()) {
				return false;
			}

			if (!vivvo_hooks_manager::call('user_delete', array(&$user_id, &$all_matching))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user) {
				if($sm->user->is_admin()) {
					$user_list = new Users_list();
					if ($all_matching == 1) {

						$user_params = $user_list->get_search_params_from_url($sm);
						$user_list->search($user_params['search_options'], '', 'ascending', 0, 0, false);

						if (in_array($sm->user->get_id(), $user_list->get_list_ids())) {
							$this->set_error_code(2342);	// you can't delete user account you are logged in with.
							return false;
						}

						if ($user_list->sql_delete_list($this->_post_master, NULL, true)) {
							admin_log($sm->user->get_username(), 'Deleted all selected users');
							return true;
						} else {
							$this->set_error_code(2309);
							return false;
						}
					} else {

						if (in_array($sm->user->get_id(), (array)$user_id)) {
							$this->set_error_code(2342);	// you can't delete user account you are logged in with.
							return false;
						}

						if ($user_list->get_users_by_ids($user_id)){
							if ($user_list->sql_delete_list($this->_post_master)) {
								admin_log($sm->user->get_username(), 'Deleted user(s) #' . trim(implode(',', $user_id)));
								return true;
							} else {
								$this->set_error_code(2310);
								return false;
							}
						} else {
							$this->set_error_code(2311);
							return false;
						}
					}
				} else {
					$this->set_error_code(2312);
					return false;
				}
			} else {
				$this->set_error_code(2313);
				return false;
			}
		}

		/**
		 * Edit user data and replace picture if is uploaded
		 *
		 * @param integer $user_id
		 * @param array $data
		 * @return mixed array on succes, or false on fail
		 */
		function edit_user($user_id, $data) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('user_edit', array(&$user_id, &$data))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				unset($data['user_id']);
				unset($data['username']);
				if($sm->user->is_admin()){
					$user_list = new Users_list();
					$user = $user_list->get_user_by_id($user_id);

					if ($user !== false) {

						if ($data['password'] == '') {
							unset($data['password']);
						} else {
							if (strlen($data['password']) < 6) {
								$this->set_error_code(2314);
								return false;
							}

							if ($data['password'] != $data['retype_password']) {
								$this->set_error_code(2315);
								return false;
							}
							$data['password'] = md5($data['password']);
						}
						$data['email_address'] = strtolower($data['email_address']);

						if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['email_address'])) {
							$this->set_error_code(2316);
							return false;
						}

						$user_list = new Users_list();
						$email_check = $user_list->get_email_exists($data['email_address'], true);

						if ($email_check && ($user_id != $email_check)) {
							$this->set_error_code(2302);
							return false;
						}

						array_walk ($data, 'array_htmlspecialchars');
						$groups = $data['groups'];
						unset($data['groups']);

						$user->populate($data);
						$this->_post_master->set_data_object($user);

						if ($this->_post_master->sql_update()) {

							$user_manager = $sm->get_user_manager();
							$user_manager->set_user_groups($user_id, $groups);

							$fm = $sm->get_file_manager();
							if ($fm->is_uploaded('USER_picture')) {
								$user_image = $fm->upload('USER_picture','','jpg,jpeg,gif,png');
								if ($user_image != false) {
									if ($user->picture != ''){
										$fm->delete_fs(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $user->picture);
									}
									$user->set_userid($user_id);
									$user->set_picture($user_image);
									$this->_post_master->set_data_object($user);
									if (!$this->_post_master->sql_update()){
										$this->set_error_code(2317);
										return false;
									}
								} else {
									$this->set_error_code($fm->get_error_code());
									return false;
								}
							}
							admin_log($sm->user->get_username(), 'Edited user #' . $user_id);
							return true;
						} else {
							$this->set_error_code(2318);
							return false;
						}
					} else {
						$this->set_error_code(2319);
						return false;
					}
				} elseif($sm->user) {
					if ($user_id == $sm->user->get_id()){
						$user_list = new Users_list();
						$user = $user_list->get_user_by_id($user_id);
						if ($user !== false) {
							if ($data['password'] == '') {
								unset($data['password']);
							} else {
								if (strlen($data['password']) < 6) {
									$this->set_error_code(2320);
									return false;
								}
								if($data['password'] != $data['retype_password']) {
									$this->set_error_code(2321);
									return false;
								}

								$data['password'] = md5($data['password']);
							}
							$data['email_address'] = strtolower($data['email_address']);
							if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['email_address'])) {
								$this->set_error_code(2322);
								return false;
							}

							$user_list = new Users_list();
							$email_check = $user_list->get_email_exists($data['email_address'], true);
							if ($user_id != $email_check) {
								$this->set_error_code(2302);
								return false;
							}

							unset($data['activated']);

							array_walk ($data, 'array_htmlspecialchars');

							$user->populate($data);
							$this->_post_master->set_data_object($user);
							if ($this->_post_master->sql_update()){
								if ($sm->user->can('ACCESS_ADMIN')){
									$fm = $sm->get_file_manager();
									if ($fm->is_uploaded('USER_picture')){
										$user_image = $fm->upload('USER_picture','','jpg,jpeg,gif,png');
										if ($user_image != false) {
											if ($user->picture != '') {
												$fm->delete_fs(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $user->picture);
											}
											$user->set_userid($user_id);
											$user->set_picture($user_image);
											$this->_post_master->set_data_object($user);
											if (!$this->_post_master->sql_update()) {
												$this->set_error_code(2323);
												return false;
											}
										} else {
											$this->set_error_code($fm->get_error_code());
											return false;
										}
									}
								}
								admin_log($sm->user->get_username(), 'Edited user #' . $user_id);
								return true;
							} else {
								$this->set_error_code(2324);
								return false;
							}
						} else {
							$this->set_error_code(2325);
							return false;
						}
					} else {
						$this->set_error_code(2326);
						return false;
					}
				} else {
					$this->set_error_code(2327);
					return false;
				}
			} else {
				$this->set_error_code(2328);
				return false;
			}
		}

		/**
		 * Set mass fields in user table with some value
		 *
		 * @param string $user_ids
		 * @param string $field_name
		 * @param string $value
		 * @return mixed array on succes, or false on fail
		 */
		function set_field($user_ids, $field_name, $value, $all_matching = 0) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('user_setField', array(&$user_ids, &$field_name, &$value, &$all_matching))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					$user_list = new Users_list();
					if ($all_matching == 1){
						$user_params = $user_list->get_search_params_from_url($sm);
						$user_list->search($user_params['search_options'], '', 'ascending', 0, 0, false);
						if ($user_list->sql_update_list($this->_post_master, array($field_name => $value), NULL, true)) {
							admin_log($sm->user->get_username(), 'Edited users #' . trim(implode(',', $user_ids)));
							return true;
						} else {
							$this->set_error_code(2329);
							return false;
						}
					} else {
						if ($user_list->get_users_by_ids($user_ids)) {
							if ($user_list->sql_update_list($this->_post_master, array($field_name => $value))) {
								admin_log($sm->user->get_username(), 'Edited users #' . trim(implode(',', $user_ids)));
								return true;
							} else {
								$this->set_error_code(2330);
								return false;
							}
						} else {
							return false;
						}
					}
				} else {
					$this->set_error_code(2331);
					return false;
				}
			} else {
				$this->set_error_code(2332);
				return false;
			}
		}

		/**
		 * Add users to group
		 *
		 * @param string $group_id
		 * @param string $user_ids
		 * @return mixed array on succes, or false on fail
		 */
		function add_to_group($group_id, $user_ids){
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('user_addToGroup', array(&$group_id, &$user_ids))) {
				return vivvo_hooks_manager::get_status();
			}


			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if($sm->user->is_admin()) {
					$user_manager = $sm->get_user_manager();
					if (is_array($user_ids) && !empty($user_ids)) {
						foreach($user_ids as $id) {
							if (!$user_manager->add_user_to_group(intval($id), intval($group_id))) {
								$this->set_error_code(2333);
								return false;
							}
						}
					}
					admin_log($sm->user->get_username(), 'Added users #' . trim(implode(',', $user_ids)) . ' to group #' . $group_id);
					return true;
				}else{
					$this->set_error_code(2334);
					return false;
				}
			}else{
				$this->set_error_code(2335);
				return false;
			}
		}

		/**
		 * Remove users from group
		 *
		 * @param string $group_id
		 * @param string $user_ids
		 * @return mixed array on succes, or false on fail
		 */
		function remove_from_group($group_id, $user_ids) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('user_removeFromGroup', array(&$group_id, &$user_ids))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if($sm->user->is_admin()) {
					$user_manager = $sm->get_user_manager();
					if (is_array($user_ids) && !empty($user_ids)) {
						foreach($user_ids as $id) {
							if (!$user_manager->remove_user_from_group(intval($id), intval($group_id))) {
								$this->set_error_code(2336);
								return false;
							}
						}
					}
					admin_log($sm->user->get_username(), 'Removed users #' . trim(implode(',', $user_ids)) . ' from group #' . $group_id);
					return true;
				} else {
					$this->set_error_code(2337);
					return false;
				}
			} else {
				$this->set_error_code(2338);
				return false;
			}
		}

		/**
		 * Delete image from FS and update database with new value in picture field
		 *
		 * @param integer $user_id
		 * @return mixed array on succes, or false on fail
		 */
		function delete_image($user_id) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('user_deleteImage', array(&$user_id))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				$user_list = new Users_list();
				$user = $user_list->get_user_by_id($user_id);

				if ($user !== false){
					$fm = $sm->get_file_manager();
					if ($user->picture != ''){
						if ($fm->delete_fs(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $user->picture)) {
							$user->set_picture('');
							$this->_post_master->set_data_object($user);
							if ($this->_post_master->sql_update()){
								return true;
							}else{
								$this->set_error_code(2339);
								return false;
							}
						}
					}
				}else{
					$this->set_error_code(2340);
					return false;
				}
			}else{
				$this->set_error_code(2341);
				return false;
			}
		}


		function check_username($username) {

			if (!vivvo_hooks_manager::call('user_checkUsername', array(&$username))) {
				return vivvo_hooks_manager::get_status();
			}

			$user_list = new Users_list();

			echo json_encode(!!$user_list->get_user_exists($username));
			exit;
		}

		function check_email($email) {

			if (!vivvo_hooks_manager::call('user_checkEmail', array(&$email))) {
				return vivvo_hooks_manager::get_status();
			}

			$user_list = new Users_list();

			echo json_encode(!!$user_list->get_email_exists($email));
			exit;
		}

		/**
		 * Report 404 error via e-mail
		 *
		 * @param	integer		$comment_id
		 * @return	boolean		true on succes, or false on fail
		 */
		function report_404($url,$referrer = '') {

			if (!vivvo_hooks_manager::call('user_report404', array(&$url))) {
				return vivvo_hooks_manager::get_status();
			}

			require_once(dirname(__FILE__) . '/../../../lib/vivvo/framework/PEAR/Mail.php');
			$url = htmlspecialchars($url);
			$referrer = htmlspecialchars($referrer);
			
			if (VIVVO_EMAIL_ENABLE == 1){

				$lang = vivvo_lang::get_instance();

				$body .= $lang->get_value('LNG_REPORT_404_BODY') . "\n";
				$body .= 'URL : '. $url . "\n";
				$body .= 'Referrer : '.$referrer ."\n\n";
				
				$to = VIVVO_ADMINISTRATORS_EMAIL;
				$headers['From'] = VIVVO_ADMINISTRATORS_EMAIL;
				$headers['To'] = VIVVO_ADMINISTRATORS_EMAIL;

				$subject = str_replace('<WEBSITE_TITLE>' , VIVVO_WEBSITE_TITLE, $lang->get_value('LNG_REPORT_404_MAIL_SUBJECT'));

				$headers['Subject'] = "=?UTF-8?B?".base64_encode($subject)."?=";
				$headers['Content-Type'] = "text/plain; charset=UTF-8;";

				if (VIVVO_EMAIL_SMTP_PHP == 1){
					$mail_object = new Mail();
					$mail_object->send($to, $headers, $body);
				}else{
					$mail_options['driver']    = 'smtp';
					$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
					$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
					$mail_options['localhost'] = 'localhost';

					if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != ''){
						$mail_options['auth'] = true;
						$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME ;
						$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
					}else{
						$mail_options['auth'] = false;
						$mail_options['username']  = '';
						$mail_options['password']  = '';
					}

					$mail_object =& Mail::factory('smtp', $mail_options);

					$mail_object->send($to, $headers, $body);
				}
				return true;
			}
		}
	}
?>