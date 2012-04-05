<?php
/* =============================================================================
 * $Revision: 5047 $
 * $Date: 2010-04-21 15:05:15 +0200 (Wed, 21 Apr 2010) $
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
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");
	/**
	 * @see  'lib/vivvo/framework/PEAR/Mail.php'
	 */
	require_once(dirname(__FILE__) . '/../../../lib/vivvo/framework/PEAR/Mail.php');


	/**
	 * Login service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	class login_service extends vivvo_service {

		var $user_data;
		var $_confgiuration;

		function is_logedin($username, $password) {

			if (!vivvo_hooks_manager::call('login_isLogedin', array(&$username, &$password))) {
				return vivvo_hooks_manager::get_status();
			}

			return false;
		}

		/**
		 * Login procedure
		 *
		 * @param	string	$username
		 * @param	string	$password
		 * @param	integer	$remember
		 * @return	boolean	true on success or false on fail
		 */
		function login ($username, $password, $remember) {

			if (!$this->check_token()) {
				return false;
			}

			if (!vivvo_hooks_manager::call('login_login', array(&$username, &$password, &$remember))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			$user = $sm->_user_manager->login($username, $password);

			if ($user) {
				$sm->user->reset_user_hash($remember == 1 ? 60 * 60 * 24 * 7 : 0);
				$sm->user->set_logins($sm->user->get_logins() + 1);
				$sm->user->set_last_login(date('Y-m-d H:i:s'));
				$this->_post_master->set_data_object($sm->user);
				$this->_post_master->sql_update();
				admin_log($username, 'Logged in');
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Logout procedure
		 * Destroy session and set user on false
		 *
		 * @return	boolean	true
		 */
		function logout () {

			if (!vivvo_hooks_manager::call('login_logout')) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

            if (is_object($sm->user)) {
                admin_log($sm->user->get_username(), 'Logged out');
                $logout = $sm->_user_manager->logout();
                if ($logout && VIVVO_CACHE_ENABLE == 3) {
                    setcookie("kill_cache", '1', 0, VIVVO_COOKIE_PATH,  VIVVO_COOKIE_DOMAIN);
                }
                return $logout;
            }
            return true;
		}

		/**
		 * Check user on system
		 *
		 * @param	array	$data
		 * @return	boolean	true on success or false on fail
		 */
		function check_user($data) {

			if (!vivvo_hooks_manager::call('login_checkUser', array(&$data))) {
				return vivvo_hooks_manager::get_status();
			}

			$user_list = new Users_list();

			if ($user_list->get_user_exists($data['username'])) {
				$this->set_error_code(2701);
				return false;
			} else {
				return true;
			}
		}

		/**
		 * Register user procedure
		 * Send email with confirmation link
		 *
		 * @param	array	$data
		 * @return	boolean	true on success or false on fail
		 */
		public function register($data, $captcha = '') {

			if (!$this->check_token()) {
				return false;
			}

			/**
			 * @see  'lib/vivvo/core/Users.class.php'
		 	 */
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Users.class.php');

			if (!vivvo_hooks_manager::call('login_register', array(&$data))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			$lang = vivvo_lang::get_instance();
			if (!$sm->user){
				if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['email_address'])) {
					$this->set_error_code(2702);
					return false;
				}

				$user_list = new Users_list();

				if ($user_list->get_email_exists($data['email_address'])) {
					$this->set_error_code(2703);
					return false;
				}

				if (preg_match("/[^a-zA-Z0-9\_\-]/",$data['username'])) {
					$this->set_error_code(2704);
					return false;
				}

				if ($user_list->get_user_exists($data['username'])) {
					$this->set_error_code(2705);
					return false;
				}

				if (strlen($data['password']) < 6) {
					$this->set_error_code(2706);
					return false;
				}

				if ($data['password'] != $data['retype_password']) {
					$this->set_error_code(2707);
					return false;
				}

				if (VIVVO_REGISTRATION_CAPTCHA == 1 && (empty($_SESSION['vivvo']['login_captcha']) || $_SESSION['vivvo']['login_captcha'] != $captcha)) {
                    $this->set_error_code(2714);
                    return false;
				}

				$data['password'] = md5($data['password']);
				$data['created'] = date('Y-m-d H:i:s');
				$data['activated'] = '0';

				array_walk($data, 'array_htmlspecialchars');

				$user = new Users();
				if ($user->populate($data, true) === false) {
					$this->set_error_info($user->get_error_info());
					return false;
				}

				$this->_post_master->set_data_object($user);

				if ($this->_post_master->sql_insert()) {
					$id = $this->_post_master->get_work_id();

					$user->set_userid($id);

					$user_manager = $sm->get_user_manager();
					$user_manager->set_user_groups($id, VIVVO_GROUP_DEFAULT_MEMBER);

					$crypt = md5($data['email_address'] . $data['username'] . $data['created']);

					$confirm_url = make_absolute_url('login.html?action=login&cmd=confirm&ack=' . $crypt);

					$recipients = $data['email_address'];

					$search = array('(',')','<','>','@',';',':','\\','"','.','[',']');
					$replace = array(' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ');
					$vivvo_website_title = str_replace($search, $replace , VIVVO_WEBSITE_TITLE);
					$vivvo_website_title = "=?UTF-8?B?".base64_encode($vivvo_website_title)."?=";
					$headers['From']    = $vivvo_website_title . '<'. VIVVO_EMAIL_SEND_FROM .'>';
					$recipients = array();
					$recipients[]      =  $data['email_address'];
					$headers['Subject'] = "=?UTF-8?B?".base64_encode(VIVVO_EMAIL_REGISTER_SUBJECT)."?=";
					$headers['Content-Type'] = "text/plain; charset=UTF-8;";

					if ($data['first_name'] != '' or $data['last_name'] != '') {
						$fullname = $data['first_name'] . ' ' . $data['last_name'];
					} else {
						$fullname = $data['email_address'];
					}

					$br = "\n";

					$sm->set_template();

					$body_template = new template();
					$template_sting = xml_template_node::xmlentities_decode(VIVVO_EMAIL_REGISTER_TEMPLATE);

					$body_template->set_string_template($template_sting);

					$body_template->assign('new_user', $fullname);
					$body_template->assign('activation_url', $confirm_url);

					$body = $body_template->get_output() . "\n\n";

					if (VIVVO_EMAIL_SMTP_PHP == 1) {
						$mail_object = new Mail();
						$mail_object->send($recipients, $headers, $body);
					} else {
						$mail_options['driver']    = 'smtp';
						$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
						$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
						$mail_options['localhost'] = 'localhost';

						if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != '') {
							$mail_options['auth'] = true;
							$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
							$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
						} else {
							$mail_options['auth'] = false;
							$mail_options['username']  = '';
							$mail_options['password']  = '';
						}

						$mail_object = Mail::factory('smtp', $mail_options);
						$mail_object->send($recipients, $headers, $body);
					}

					return true;

				} else {
					$this->set_error_code(2708);
					return false;
				}
				return true;
			} else {
				$this->set_error_code(2709);
				return false;
			}
		}

		function forgot_mail($username, $email) {
			if (!$this->check_token()) {
				return false;
			}

			if (!vivvo_hooks_manager::call('login_forgotMail', array(&$username, &$email))) {
				return vivvo_hooks_manager::get_status();
			}

			if (isset($_SESSION['vivvo']['register_time']) && (($_SESSION['vivvo']['register_time'] + 60) > VIVVO_START_TIME)) {
				$this->set_error_code(2755);
				return false;
			}

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Users.class.php');

			$lang = vivvo_lang::get_instance();

			$user_list = new Users_list();
			if (!$username && !$email) {
				$this->set_error_code(2712);
				return false;
			} else {

				if ($username) {
					$user_list->search(array('search_username' => $username));
					$user = current($user_list->list);
				} elseif ($email) {
					$user_list->search(array('search_email_address' => $email));
					$user = current($user_list->list);
				}

				if ($user) {

					$search = array('(',')','<','>','@',';',':','\\','"','.','[',']');
					$replace = array(' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ');
					$vivvo_website_title = str_replace($search, $replace , VIVVO_WEBSITE_TITLE);
					$vivvo_website_title = "=?UTF-8?B?".base64_encode($vivvo_website_title)."?=";
					$headers['From']    = $vivvo_website_title . '<'. VIVVO_EMAIL_SEND_FROM .'>';
					$recipients = array();
					$recipients[]      =  $user ->email_address;
					$headers['Subject'] = "=?UTF-8?B?".base64_encode(VIVVO_EMAIL_FORGOT_SUBJECT)."?=";
					$headers['Content-Type'] = "text/plain; charset=UTF-8;";

					$body_template = new template();
					$template_sting = xml_template_node::xmlentities_decode(VIVVO_EMAIL_FORGOT_BODY);

					$body_template->set_string_template($template_sting);

					$confirm_url = make_absolute_url('login.html?activation_key=' . md5($user->username . $user->email_address . $user->password));

					$body_template->assign('activation_url', $confirm_url);
					$body_template->assign('user', $user);

					$body = $body_template->get_output() . "\n\n";

					if (VIVVO_EMAIL_SMTP_PHP == 1) {
						$mail_object = new Mail();
						$mail_object->send($recipients, $headers, $body);
					} else {
						$mail_options['driver']    = 'smtp';
						$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
						$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
						$mail_options['localhost'] = 'localhost';

						if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != '') {
							$mail_options['auth'] = true;
							$mail_options['username']  = VIVVO_EMAIL_SMTP_PASSWORD;
							$mail_options['password']  = VIVVO_EMAIL_SMTP_USERNAME;
						} else {
							$mail_options['auth'] = false;
							$mail_options['username']  = '';
							$mail_options['password']  = '';
						}

						$mail_object = Mail::factory('smtp', $mail_options);
						$mail_object->send($recipients, $headers, $body);
					}
				}

				$_SESSION['vivvo']['register_time'] = time();
				return true;
			}
		}

		/**
		 * Confirmation procedure
		 *
		 * @param	string	$ack
		 * @return	boolean	true on success or false on fail
		 */
		function confirm($ack) {

			if (!vivvo_hooks_manager::call('login_confirm', array(&$ack))) {
				return vivvo_hooks_manager::get_status();
			}

			$user_list = new Users_list();
			$user = $user_list->get_user_by_md5($ack);

			if ($user === false) {
				$this->set_error_code(2710);
				return false;
			} else {
				$user->set_activated('1');
				vivvo_lite_site::get_instance()->get_template()->assign('CONFIRMED', 1);
				$this->_post_master->set_data_object($user);
				if ($this->_post_master->sql_update()) {
					return true;
				} else {
					$this->set_error_code(2711);
					return false;
				}
			}
		}

		/**
		 * Change password procedure
		 *
		 * @param	array	$user_data
		 * @return	boolean	true on success or false on fail
		 */
		function change_password($user_data) {

			if (!$this->check_token()) {
				return false;
			}

			if (!vivvo_hooks_manager::call('login_changePassword', array(&$user_data))) {
				return vivvo_hooks_manager::get_status();
			}

			$key = $user_data['key'];

			$user_list = new Users_list();
			$user = $user_list->get_user_by_forgot_md5($key);
			if ($user === false) {
				$this->set_error_code(2713);
				return false;
			} else {
				if (strlen($user_data['password']) < 6) {
					$this->set_error_code(2706);
					return false;
				}
				if ($user_data['password'] != $user_data['retype_password']) {
					$this->set_error_code(2707);
					return false;
				}

				$user_data['password'] = md5($user_data['password']);

				$user->set_password($user_data['password']);
				$this->_post_master->set_data_object($user);

				if ($this->_post_master->sql_update()) {
					admin_log($user->get_username(), ' Changed password');
					return true;
				} else {
					$this->set_error_code(2711);
					return false;
				}
			}
		}
	}
?>