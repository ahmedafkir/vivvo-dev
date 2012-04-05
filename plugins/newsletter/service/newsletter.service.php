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

	require_once(dirname(__FILE__) . '/../newsletter.class.php');
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Users.class.php');
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
	require_once(VIVVO_FS_FRAMEWORK . 'PEAR/Mail.php');
	require_once(VIVVO_FS_FRAMEWORK . 'PEAR/Mail/Queue.php');

	class newsletter_service extends vivvo_service {

		/**
		 * Add subscriber to mailing list
		 *
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function add_subscriber ($data){

			$lang = vivvo_lang::get_instance();

			$subsciber = new maillist();
			if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['email'])) {
				$this->set_error_code(10251);
				return false;
			}

			$email_list = new maillist_list();
			if ($email_list->get_subscriber_by_email($data['email']) === false){

				$data['ip'] = $_SERVER['REMOTE_ADDR'];
				$data['confirm'] = '0';
				$data['domain_id'] = '1';
				$data['time'] = time();

				if ($subsciber->populate($data, true) === false){
					return false;
				}

				$this->_post_master->set_data_object($subsciber);
				if ($this->_post_master->sql_insert()){

					$crypt = md5($data['email'] . $data['ip'] . $data['time']);

					$confirm_url = VIVVO_PROXY_URL . 'newsletter/index.html?action=newsletter&cmd=subscriberConfirm&ack=' . $crypt;

					if (!preg_match('/^[^:]+:/', $confirm_url)) {
						$confirm_url = VIVVO_URL . $confirm_url;
					}

					$search = array('(',')','<','>','@',';',':','\\','"','.','[',']');
					$replace = array(' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ');
					$vivvo_website_title = str_replace($search, $replace , VIVVO_WEBSITE_TITLE);
					$vivvo_website_title = "=?UTF-8?B?".base64_encode($vivvo_website_title)."?=";
					$headers['From'] = $vivvo_website_title . '<'. VIVVO_EMAIL_SEND_FROM .'>';
					$recipients = array();
					$recipients[] = $data['email'];
					$headers['To'] = $data['email'];
					$headers['Subject'] = "=?UTF-8?B?".base64_encode(VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_SUBJECT)."?=";
					$headers['Content-Type'] = "text/plain; charset=UTF-8;";

					vivvo_lite_site::get_instance()->set_template();

					$body_template = new template ();
					$template_sting = xml_template_node::xmlentities_decode(VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_BODY);

					$body_template->set_string_template($template_sting);

					$body_template->assign('email', $data['email']);
					$body_template->assign('activation_url', $confirm_url);

					$body = $body_template->get_output() . "\n\n";

					if (VIVVO_EMAIL_SMTP_PHP == 1){
						$mail_object = new Mail();
						$mail_object->send($recipients, $headers, $body);
					}else{
						$mail_options['driver']    = 'smtp';
						$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
						$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
						$mail_options['localhost'] = 'localhost';

						if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != ''){
							$mail_options['auth'] = true;
							$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
							$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
						}else{
							$mail_options['auth'] = false;
							$mail_options['username']  = '';
							$mail_options['password']  = '';
						}

						$mail_object =& Mail::factory('smtp', $mail_options);

						$mail_object->send($recipients, $headers, $body);
					}
					return true;
				}else{
					$this->set_error_code(10252);
					return false;
				}
			}else{
				$this->set_error_code(10253);
				return false;
			}
		}

		/**
		 * Confirm subscriber to mailing list
		 *
		 * @param	string	$param
		 * @return	boolean	true on succes, or false on fail
		 */
		function confirm_subscriber($param){

			if ($param != ''){
				$subscriber = new maillist_list();
				$subscriber = $subscriber->get_subscriber_by_md5($param);
				if ($subscriber === false){
					$this->set_error_code(10254);
					return false;
				}else{
					$subscriber->set_confirm('1');
					$this->_post_master->set_data_object($subscriber);
					if ($this->_post_master->sql_update()){
						return true;
					}else{
						$this->set_error_code(10255);
						return false;
					}
				}
			}else{
				$this->set_error_code(10256);
				return false;
			}
		}

		/**
		 * Unsubscribe from mailing list
		 *
		 * @param	string	$param
		 * @return	boolean	true on succes, or false on fail
		 */
		function unsubscribe_subscriber($param){

			if ($param != ''){
				$subscriber = new maillist_list();
				$subscriber = $subscriber->get_subscriber_by_md5_unsubscribe($param);
				if ($subscriber === false){
					$this->set_error_code(10257);
					return false;
				}else{
					$this->_post_master->set_data_object($subscriber);
					if ($this->_post_master->sql_delete()){
						return true;
					}else{
						$this->set_error_code(10258);
						return false;
					}
				}
			}else{
				$this->set_error_code(10259);
				return false;

			}
		}

		function add_user_subscriber(){
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){

				$user_list = new Users_list();
				$user = $user_list->get_user_from_session();

				if ($user !== false){
					$user->__set('subscriber', 1);
					$this->_post_master->set_data_object($user);

					if ($this->_post_master->sql_update()){

						$subsciber = new maillist();
						$subsciber->populate(array(
							'email' => $user->get_email_address(),
							'ip' => $user->user_ip(),
							'time' => time(),
							'confirm' => 1,
							'domain_id' => 1,
							'user_id' => $user->get_id()
						));

						$this->_post_master->set_data_object($subsciber);
						if ($this->_post_master->sql_insert()) {
							return true;
						} else {
							$this->set_error_code(10252);
							return false;
						}
					}else{
						$this->set_error_code(10260);
						return false;
					}
				}else{
					$this->set_error_code(10261);
					return false;
				}
			}else{
				$this->set_error_code(10262);
				return false;
			}
		}

		function remove_user_subscriber(){
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){

				$user_list = new Users_list();
				$user = $user_list->get_user_from_session();

				if ($user !== false){
					$user->__set('subscriber', 0);
					$this->_post_master->set_data_object($user);

					if ($this->_post_master->sql_update()){
						$this->_post_master->sql_delete_list('maillist', "user_id=".$user->get_id());
						return true;
					}else{
						$this->set_error_code(10263);
						return false;
					}
				}else{
					$this->set_error_code(10264);
					return false;
				}
			}else{
				$this->set_error_code(10265);
				return false;
			}
		}

		/**
		 * Unsubscribe user
		 *
		 * @param	string	$param
		 * @return	boolean	true on succes, or false on fail
		 */
		function unsubscribe_user($param){
			$sm = vivvo_lite_site::get_instance();
			$db = $sm->get_db();
			if ($param != ''){
				$res =& $db->query('SELECT userid FROM '.VIVVO_DB_PREFIX.'users WHERE md5( concat( `email_address` , `userid` , `created` ) ) = \''.$param.'\'');
				if (!is_a($res, 'mdb2_error')){
					if($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)){
						$res1 =& $db->query('UPDATE `'.VIVVO_DB_PREFIX.'users` SET `subscriber` = 0 WHERE `userid` = \''.$row['userid'].'\'');
						if (!is_a($res, 'mdb2_error')){
							return true;
						}else{
							$this->set_error_code(10266);
							return false;
						}
					}else{
						$this->set_error_code(10267);
						return false;
					}
				}else{
					$this->set_error_code(10268);
					return false;
				}
			}else{
				$this->set_error_code(10269);
				return false;
			}
		}

		/**
		 * Delete subscriber(s)
		 *
		 * @param	array	$subscribers_ids
		 * @return	boolean	true on success or false on fail
		 */
		function delete_subscriber($subscribers_ids){

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if($sm->user->is_admin()){
					$subscribers_list = new maillist_list();
					$subscribers_list->get_subscribers_by_ids($subscribers_ids);
					if ($subscribers_list->sql_delete_list($this->_post_master)){
						return true;
					}else{
						$this->set_error_code(10201);
						return false;
					}
				}else{
					$this->set_error_code(10202);
					return false;
				}
			}else{
				$this->set_error_code(10203);
				return false;
			}
		}

		/**
		 * Set fields
		 *
		 * @param	array	$subscribers_ids
		 * @param	string	$field_name
		 * @param	string	$field_value
		 * @return	boolean	true on success or false on fail
		 */
		function set_field($subscribers_ids, $field_name, $field_value){
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					$maillist_list = new maillist_list();
					$maillist_list->get_subscribers_by_ids($subscribers_ids);

					if ($maillist_list->sql_update_list($this->_post_master, array($field_name => $field_value))){
						return true;
					}else{
						$this->set_error_code(10204);
						return false;
					}
				}else{
					$this->set_error_code(10205);
					return false;
				}
			}else{
				$this->set_error_code(10206);
				return false;
			}
		}


		function add_newsletter($data){

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					array_walk ($data, 'array_htmlspecialchars');

					if ($data['test'] == 1){
						if ($data['test_email'] != ''){
							if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['test_email'])){
								$this->set_error_code(10207);
								return false;
							}
						}else{
							$this->set_error_code(10208);
							return false;
						}

					}else{
						if ($data['test_email'] != ''){
							if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['test_email'])){
								$this->set_error_code(10209);
								return false;
							}
						}
					}

					if (!empty($data['groups'])){
						$data['groups'] = implode(',',$data['groups']);
					}elseif($data['test'] != 1){
						$this->set_error_code(10210);
						return false;
					}

					$newsletter = new newsletter();

					if ($newsletter->populate($data, true) === false){
						return false;
					}

					$this->_post_master->set_data_object($newsletter);

					if ($this->_post_master->sql_insert()){
						return $this->_post_master->get_work_id();
					}else{
						$this->set_error_code(10211);
						return false;
					}
				}else{
					$this->set_error_code(10212);
					return false;
				}
			}else{
				$this->set_error_code(10213);
				return false;
			}

		}


		function edit_newsletter($data, $newsletter_id){

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					array_walk ($data, 'array_htmlspecialchars');

					if ($data['test'] == 1){
						if ($data['test_email'] != ''){
							if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['test_email'])){
								$this->set_error_code(10214);
								return false;
							}
						}else{
							$this->set_error_code(10215);
							return false;
						}
					}else{
						if ($data['test_email'] != ''){
							if (!preg_match("/^[-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/", $data['test_email'])){
								$this->set_error_code(10216);
								return false;
							}
						}
					}

					if (!empty($data['groups'])){
						$data['groups'] = implode(',',$data['groups']);
					}elseif($data['test'] != 1){
						$this->set_error_code(10217);
						return false;
					}


					$newsletter_list = new newsletter_list();

					$newsletter = $newsletter_list->get_newsletter_by_id($newsletter_id);

					if ($newsletter !== false){
						if ($newsletter->populate($data, true) === false){
							return false;
						}
						$this->_post_master->set_data_object($newsletter);

						if ($this->_post_master->sql_update()){
							return true;
						}else{
							$this->set_error_code(10218);
							return false;
						}
					}else{
						$this->set_error_code(10219);
						return false;
					}
				}else{
					$this->set_error_code(10220);
					return false;
				}
			}else{
				$this->set_error_code(10221);
				return false;
			}
		}

		function delete_newsletter($newsletter_id){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if($sm->user->is_admin()){
					$newsletter_list = new newsletter_list();
					$newsletter =& $newsletter_list->get_newsletter_by_id($newsletter_id);

					$this->_post_master->set_data_object($newsletter);
					if ($this->_post_master->sql_delete()){
						return true;
					}else{
						$this->set_error_code(10222);
						return false;
					}
				}else{
					$this->set_error_code(10223);
					return false;
				}
			}else{
				$this->set_error_code(10224);
				return false;
			}
		}

		function send_newsletter($newsletter_id){

			$sm = vivvo_lite_site::get_instance();
			$db = $sm->get_db();

			if ($sm->user){
				if($sm->user->is_admin()){

					$newsletter_list = new newsletter_list();
					$newsletter = $newsletter_list->get_newsletter_by_id($newsletter_id);

					if ($newsletter !== false){

						if ($newsletter->test == 1){

							$to = $newsletter->test_email;

							if ($newsletter->vte_template == '1'){
								$body_template = new template();
								$template_sting = xml_template_node::xmlentities_decode($newsletter->body);
								$body_template->set_string_template($template_sting);
								$body = $body_template->get_output() . "\n\n";
							}else{
								$body = xml_template_node::xmlentities_decode($newsletter->body);
							}

							$unsubscribe_url = VIVVO_PROXY_URL . 'newsletter/index.html?&action=newsletter&cmd=subscriberUnSubscribe&dack=' . md5('test');

							if (!preg_match('/^[^:]+:/', $unsubscribe_url)) {
								$unsubscribe_url = VIVVO_URL . $unsubscribe_url;
							}

							$body = $body . "\n\n\n" . VIVVO_PLUGIN_NEWSLETTER_UNSUBSCRIBE_TEXT . "\n" . $unsubscribe_url;

							$from = VIVVO_ADMINISTRATORS_EMAIL;
							$search = array('(',')','<','>','@',';',':','\\','"','.','[',']');
							$replace = array(' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ');
							$from_name = str_replace($search, $replace , VIVVO_WEBSITE_TITLE);
							$from_name = "=?UTF-8?B?".base64_encode($from_name)."?=";
							$from_params      = !empty($from_name) ? '"'.$from_name.'" <'.$from.'>' : '<'.$from.'>';

							$headers['From']      = $from_params;

							$recipients = array();
							$recipients[] = $to;
							$subject = "=?UTF-8?B?".base64_encode($newsletter->subject)."?=";
							$headers['Subject'] = $subject;
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
									$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
									$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
								}else{
									$mail_options['auth'] = false;
									$mail_options['username']  = '';
									$mail_options['password']  = '';
								}

								$mail_object =& Mail::factory('smtp', $mail_options);

								$mail_object->send($to, $headers, $body);
							}

						}else{

							$container_options = array(
								'type'			=> 'mdb2',
								'dsn'			=> VIVVO_DB_TYPE.'://'.VIVVO_DB_USER.':'.VIVVO_DB_PASSWORD.'@'.VIVVO_DB_HOST.'/'.VIVVO_DB_DATABASE,
								'mail_table'	=> VIVVO_DB_PREFIX.'mail_queue'
							);

							$mail_options['driver']    = 'smtp';
							$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
							$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
							$mail_options['localhost'] = 'localhost';

							if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != ''){
								$mail_options['auth'] = true;
								$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
								$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
							}else{
								$mail_options['auth'] = false;
								$mail_options['username']  = '';
								$mail_options['password']  = '';
							}

							$mail_queue = new Mail_Queue($container_options, $mail_options);

							$from = VIVVO_ADMINISTRATORS_EMAIL;
							$search = array('(',')','<','>','@',';',':','\\','"','.','[',']');
							$replace = array(' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ',' ');
							$from_name = str_replace($search, $replace , VIVVO_WEBSITE_TITLE);
							$from_name = "=?UTF-8?B?".base64_encode($from_name)."?=";
							$from_params      = !empty($from_name) ? '"'.$from_name.'" <'.$from.'>' : '<'.$from.'>';

							if ($newsletter->vte_template == '1'){
								$body_template = new template();
								$template_sting = xml_template_node::xmlentities_decode($newsletter->body);
								$body_template->set_string_template($template_sting);
								$body = $body_template->get_output() . "\n\n";
							}else{
								$body = xml_template_node::xmlentities_decode($newsletter->body);
							}

							$groups = explode(',',$newsletter->groups);

							foreach($groups as $key){
								if ($key == -1){

									$res =& $db->query('SELECT email, ip, time FROM '.VIVVO_DB_PREFIX.'maillist WHERE `confirm` = \'1\'');
									if (!is_a($res, 'mdb2_error')){
										while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)){
											$subject = "=?UTF-8?B?".base64_encode($newsletter->subject)."?=";
											$recipient = $row['email'];
											$hdrs = array( 'From'    => $from_params,
											               'To'      => $recipient,
											               'Subject' => $subject,
											               'Content-Type' => "text/plain; charset=UTF-8;" );

											$body_all = $body . "\n\n\n" . VIVVO_PLUGIN_NEWSLETTER_UNSUBSCRIBE_TEXT . "\n" . VIVVO_URL . 'newsletter/index.html?action=newsletter&cmd=subscriberUnSubscribe&dack=' . md5($row['ip'] . $row['time'] . $row['email']);
											$mail_queue->put( $from, $recipient, $hdrs, $body_all );
										}
									}

								}elseif($key >= 0){

									$res = $db->query('SELECT email_address, userid, created FROM '.VIVVO_DB_PREFIX.'users as u
														INNER JOIN '.VIVVO_DB_PREFIX.'group_user  as gu
														ON gu.user_id=u.userid
														WHERE gu.group_id = \''.$key.'\' and `subscriber` = \'1\'');

									if (!is_a($res, 'mdb2_error')){
										while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)){
											$recipient = $row['email_address'];
											$subject = "=?UTF-8?B?".base64_encode($newsletter->subject)."?=";
											$hdrs = array( 'From'    => $from_params,
											               'To'      => $recipient,
											               'Subject' => $subject,
											               'Content-Type' => "text/plain; charset=UTF-8;"  );

											$unsubscribe_url = VIVVO_PROXY_URL . 'newsletter/index.html?action=newsletter&cmd=userUnSubscribe&dack=' . md5($row['email_address'] . $row['userid'] . $row['created']);

											if (!preg_match('/^[^:]+:/', $unsubscribe_url)) {
												$unsubscribe_url = VIVVO_URL . $unsubscribe_url;
											}

											$body_all = $body . "\n\n\n" . VIVVO_PLUGIN_NEWSLETTER_UNSUBSCRIBE_TEXT . "\n" . $unsubscribe_url;
											$mail_queue->put( $from, $recipient, $hdrs, $body_all );
										}
									}
								}
							}
						}
						return true;
					}else{
						$this->set_error_code(10225);
						return false;
					}
				}else{
					$this->set_error_code(10226);
					return false;
				}
			}else{
				$this->set_error_code(10227);
				return false;
			}
		}


		/**
		 * Preferences
		 * Edit preferences for newsletter
		 *
		 * @param	string	$variable_name
		 * @param	integer	$variable_value
		 * @return	boolean	true on succes, or false on fail
		 */
		function preferences_newsletter($variable_name,$variable_value){
			if ($this->_user && $this->_user->is_admin()){
				require_once (VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');
				$preferences_list = new preferences_list();
				$preferences = $preferences_list->get_preference_by_variable_name($variable_name);

				if ($preferences != false){

					if ($variable_name == 'VIVVO_PLUGIN_NEWSLETTER_NUMBER_OF_MAILS'){
						$variable_value = (int) $variable_value;
						if($variable_value <= 0){
							$variable_value = 500;
						}
					}elseif($variable_name == 'VIVVO_PLUGIN_NEWSLETTER_PERIOD'){
						$variable_value = (int) $variable_value;
						if($variable_value <= 0){
							$variable_value = 60;
						}
					}elseif(($variable_name == 'VIVVO_PLUGIN_NEWSLETTER_UNSUBSCRIBE_TEXT') && ($variable_value == '')){
						$variable_value = 'Click following link to Unsubscribe:';
					}elseif(($variable_name == 'VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_SUBJECT') && ($variable_value == '')){
						$variable_value = 'Subscribe to Newsletter';
					}elseif(($variable_name == 'VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_BODY') && ($variable_value == '')){
						$variable_value = '<vte:template>Dear <vte:value select="{email}" />,'."\n\n".'Thank you for signing up! Click or copy and paste this URL to your browser to activate your account:'."\n".'<vte:value  select="{activation_url}" />'."\n\n".'Please note that your activation code is NOT your password.'."\n".'Thank you for using our service'."\n\n".'Best regards,'."\n".'<vte:value select="{VIVVO_WEBSITE_TITLE}" />'."\n".'<vte:value select="{VIVVO_URL}" />'."\n".'</vte:template>';
					}

					$variable_value = htmlspecialchars($variable_value, ENT_QUOTES, 'UTF-8');

					$preferences->set_variable_value($variable_value);
					$this->_post_master->set_data_object($preferences);
					if ($this->_post_master->sql_update()){
						if ($variable_name == 'VIVVO_PLUGIN_NEWSLETTER_PERIOD'){
							$sm = vivvo_lite_site::get_instance();
							$pm = $sm->get_plugin_manager();
							$cm = $sm->get_cron_manager();
							$cm->cron_job(array('every_i'=>$variable_value),$pm->plugins['newsletter']['root_dir'].'tasks/mail_queue.php','','mail_queue');
						}
						return true;
					}else{
						$this->set_error_code(10228);
						return false;
					}
				}else{
					$this->set_error_code(10229);
					return false;
				}
			}else{
				$this->set_error_code(10230);
				return false;
			}
		}

		function export(){
			$sm = vivvo_lite_site::get_instance();
			$db = $sm->get_db();
			if ($this->_user && $this->_user->is_admin()){
				$backup = '';
				$res = $db->query('SELECT email FROM '.VIVVO_DB_PREFIX.'maillist WHERE `confirm` = \'1\'');
				if (!PEAR::isError($res)){
					while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
						$backup .= $row['email'] . "\n";
					}
				}

				$file_path = VIVVO_FS_ROOT . "backup/";
				$file_name = "backup_mailist_" . time() . ".csv";
				$fw = fopen ($file_path . $file_name, "w+");
				fwrite ($fw, $backup);
				fclose ($fw);

				header("Content-Type: application/octet-stream");
				header("Content-Disposition: attachment; filename=$file_name");
				header("Content-Length: ".filesize($file_path . $file_name));
				header("Accept-Ranges: bytes");
				header("Pragma: no-cache");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Content-transfer-encoding: binary");

				@readfile($file_path . $file_name);
				unlink ($file_path . $file_name);
				exit();
			}else{
				$this->set_error_code(10231);
				return false;
			}

		}

		function import(){

			$sm = vivvo_lite_site::get_instance();
			if ($this->_user && $this->_user->is_admin()){
				$fm = $sm->get_file_manager();

				if ($fm->is_uploaded('MAILLIST')){
					$FILE = $_FILES['MAILLIST'];
					$row = 1;
					preg_match_all("/[-a-z0-9\._]+@[-a-z0-9\._]+\.[a-z]{2,4}/", file_get_contents($FILE['tmp_name']), $emails);
					$number_of_mails = 1;
					foreach($emails[0] as $email){

						$subsciber = new maillist();

						$email_list = new maillist_list();
						if ($email_list->get_subscriber_by_email($email) === false){

							$data['email'] = $email;
							$data['ip'] = $_SERVER['REMOTE_ADDR'];
							$data['confirm'] = '1';
							$data['domain_id'] = '1';
							$data['time'] = time();

							if ($subsciber->populate($data, true) === false){
								return false;
							}

							$this->_post_master->set_data_object($subsciber);
							if ($this->_post_master->sql_insert()){
								$number_of_mails++;
							}
						}
					}
					return $number_of_mails;
				}else{
					$this->set_error_code(10232);
					return false;
				}
			}else{
				$this->set_error_code(10233);
				return false;
			}
		}

	}
?>