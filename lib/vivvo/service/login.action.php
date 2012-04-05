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
	 * Service definition for category
	 */
	$service_definition = array(
		"login.check" => array(
			"function" => "login_service",
			"signature" => array(array('string')),
			"docstring" => 'Check if user is loged in via cookie.'
		),
		"login.login" => array(
			"function" => "login_service",
			"signature" => array(array('string', 'string','string')),
			"docstring" => 'Login user.'
		),
		"login.logout" => array(
			"function" => "login_service",
			"signature" => array(array('string')),
			"docstring" => 'Logout user.'
		),
		"login.register" => array(
			"function" => "login_service",
			"signature" => array(array('string')),
			"docstring" => 'Registration user.'
		),
		"login.confirm" => array(
			"function" => "login_service",
			"signature" => array(array('string')),
			"docstring" => 'Confirm user.'
		),
		"login.checkUser" => array(
			"function" => "login_service",
			"signature" => array(array('string')),
			"docstring" => 'Check if user is loged in via cookie.'
		),
		"login.forgotMail" => array(
			"function" => "login_service",
			"signature" => array(array('string')),
			"docstring" => 'Send mail for new password.'
		),
		"login.changePassword" => array(
			"function" => "login_service",
			"signature" => array(array('string')),
			"docstring" => 'Change password.'
		)
	);


	function login_service(&$sm, $action, $command){

		require_once(dirname(__FILE__) . '/login.service.php');

		$login_service =& new login_service($sm);

		$um =& $sm->get_url_manager();
		$dm =& $sm->get_dump_manager();
		$lang =& $sm->get_lang();

		// check
		if ($command == 'check'){
			$response = $login_service->is_logedin();
			if ($response){
				vivvo_hooks_manager::call('login_check_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'logout'){
			$response = $login_service->logout();
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_LOGIN_LOGUOUT_SUCCESS'));
				vivvo_hooks_manager::call('login_logout_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'login'){
			$username = $um->get_param('LOGIN_username');
			$password = $um->get_param('LOGIN_password');
			$remeber = $um->get_param('LOGIN_remember');

			$response = $login_service->login($username, $password, $remeber);
			if ($response){
				vivvo_hooks_manager::call('login_login_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'register'){
			$in_user = $um->get_param_regexp('/^USER_/');
			$captcha = $um->get_param('USER_captcha');
			$response = $login_service->register($in_user, $captcha);

			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_REGISTER_SUCCESS'));
				vivvo_hooks_manager::call('login_register_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'forgotMail'){
			$username = $um->get_param('LOGIN_username');
			$email = $um->get_param('LOGIN_email');
			$response = $login_service->forgot_mail($username, $email);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FORGOT_PASSWORD_NOTICE'));
				vivvo_hooks_manager::call('login_forgotMail_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}
		}elseif ($command == 'changePassword'){
			$in_user = $um->get_param_regexp('/^USER_/');
			$response = $login_service->change_password($in_user);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_FORGOT_PASSWORD__HAS_BEEN_SUCCESSFULLY_CHANGED'));
				vivvo_hooks_manager::call('login_changePassword_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}

		}elseif($command == 'checkUser'){
			$in_user = $um->get_param_regexp('/^USER_/');
			$response = $login_service->check_user($in_user);
			if ($response){
				vivvo_hooks_manager::call('login_checkUser_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}
		}elseif($command == 'confirm'){
			$activation_code = $um->get_param('ack');;
			$response = $login_service->confirm($activation_code);
			if ($response){
				$dm->add_dump('info', 0 , $lang->get_value('LNG_INFO_USER_CONFIRM_SUCCESS'));
				vivvo_hooks_manager::call('login_confirm_postaction');
				header('X-Vivvo-Action-Status: 1');
				return true;
			}else{
				header('X-Vivvo-Action-Status: 0');
				header('X-Vivvo-Action-Error: ' . json_encode($login_service->get_error_info()));
				return false;
			}
		}
		return true;
	}
?>