<?php
/* =============================================================================
 * $Revision: 5170 $
 * $Date: 2010-04-27 09:55:14 +0200 (Tue, 27 Apr 2010) $
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
	 * @see  'lib/vivvo/core/Comments.class.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Comments.class.php');
	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');

	/**
	 * Comments service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class comment_service extends vivvo_service {

		/**
		 * Add new comment
		 *
		 * @param	array	$in_article
		 * @param	string	$captcha
		 * @return	boolean	true on success or false on fail
		 */
		function add_comment($in_comment, $captcha = '') {

			if (!vivvo_hooks_manager::call('comment_add', array(&$in_comment))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if (VIVVO_COMMENTS_ENABLE == 1) {

				if ($this->bad_ip_filter()) {
					$this->set_error_code(2201);
					return false;
				}

				$is_guest = !$sm->user && $sm->guest_group;

				if (($is_guest && ($sm->guest_group->group_privileges['ARTICLE_COMMENT'] > 0)) || ($sm->user && ($sm->user->privileges['ARTICLE_COMMENT'] > 0))) {
					if ($sm->user) {
						$in_comment['user_id'] = $sm->user->get_id();
						$in_comment['author'] = $sm->user->get_name();
						$in_comment['email'] = $sm->user->email_address;
						$in_comment['www'] = $sm->user->www;
					}

					$in_comment['create_dt'] = date('Y-m-d H:i:s');
					$in_comment['ip'] = $_SERVER['REMOTE_ADDR'];

					if (($is_guest && ($sm->guest_group->group_privileges['ARTICLE_COMMENT'] == 1)) || ($sm->user && ($sm->user->privileges['ARTICLE_COMMENT'] == 1))) {
						$in_comment['status'] = '1';
					}

					if ((VIVVO_COMMENTS_CAPTHA == 0) || (is_array($_SESSION['vivvo']['comment_captcha']) && $_SESSION['vivvo']['comment_captcha'][$in_comment['article_id']] == $captcha)) {
						if (isset($_SESSION['vivvo']['comment_time']) && (($_SESSION['vivvo']['comment_time'] + VIVVO_COMMENTS_FLOOD_PROTECTION) > VIVVO_START_TIME)) {
							$this->set_error_code(2202);
							return false;
						} else {
							array_walk($in_comment, 'array_htmlspecialchars');
							$in_comment['description'] = $this->bad_words_filter($in_comment['description']);

							$in_comment['description'] = trim($in_comment['description']);
							if ($in_comment['description'] == '') {
								$this->set_error_code(2221);
								return false;
							}

							if (VIVVO_COMMENTS_ENABLE_BBCODE) {

								if ($is_guest) {
									$in_comment['description'] = preg_replace('/\[url(.+?)?\]|\[\/url\]/', '', $in_comment['description']);
								}

								$in_comment['description_src'] = $in_comment['description'];
								$in_comment['description'] = Comments::parse_bbcode($in_comment['description']);
							}

							if (VIVVO_COMMENTS_ENABLE_THREADED and empty($in_comment['root_comment']) and !empty($in_comment['reply_to'])) {
								$in_comment['root_comment'] = $in_comment['reply_to'];
							}

							$comment = new Comments(null, $in_comment);

							$this->_post_master->set_data_object($comment);

							if ($this->_post_master->sql_insert()) {

								$id = $this->_post_master->get_work_id();
								$comment->set_id($id);

								if (!vivvo_hooks_manager::call('comment_add_postinsert', array(&$comment))) {
									return vivvo_hooks_manager::get_status();
								}

								if (is_array($_SESSION['vivvo']['comment_captcha'])) {

									$key = array_search($captcha, $_SESSION['vivvo']['comment_captcha']);
									unset($_SESSION['vivvo']['comment_captcha'][$key]);

									class_exists('Articles') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php';
									Articles_list::factory()->get_article_by_id($comment->get_article_id())->generate_captcha();
								}

								$_SESSION['vivvo']['comment_time'] = time();
								return true;
							} else {
								$this->set_error_code(2203);
								return false;
							}
						}
					} else {
						$this->set_error_code(2204);
						return false;
					}
				} else {
					$this->set_error_code(2205);
					return false;
				}
			} else {
				$this->set_error_code(2206);
				return false;
			}
		}

		/**
		 * Delete comment
		 *
		 * @param	integer		$comment_id
		 * @param	integer		$all_matching
		 * @return	boolean		true on succes, or false on fail
		 */
		function delete_comment($comment_id, $all_matching) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('comment_delete', array(&$comment_id, &$all_matching))) {
				return vivvo_hooks_manager::get_status();
			}


			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if ($sm->user->can('MODERATE_COMMENTS')) {
					$comment_list = new Comments_list();
					if ($all_matching == 1) {
						$comment_params = Comments_list::get_search_params_from_url($sm);
						$comment_list->search($comment_params['search_options'], '', 'ascending', 0, 0);
						if ($comment_list->sql_delete_list($this->_post_master, NULL, true)) {
							admin_log($sm->user->get_username(), 'Deleted all selected comments');
							return true;
						} else {
							$this->set_error_code(2207);
							return false;
						}
					} else {
						if ($comment_list->get_comments_by_ids($comment_id)) {
							if ($comment_list->sql_delete_list($this->_post_master)) {
								admin_log($sm->user->get_username(), 'Deleted comments #' . trim(implode(',', $comment_id)));
								return true;
							} else {
								$this->set_error_code(2208);
								return false;
							}
						} else {
							return false;
						}
					}
				} else {
					$this->set_error_code(2209);
					return false;
				}
			} else {
				$this->set_error_code(2210);
				return false;
			}
		}

		/**
		 * Edit comment
		 *
		 * @param	integer		$comment_id
		 * @param	array		$data
		 * @return	boolean		true on succes, or false on fail
		 */
		function edit_comment($comment_id, $data) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('comment_edit', array(&$comment_id, &$data))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if ($sm->user->can('MODERATE_COMMENTS')) {
					$comment_list = new Comments_list();
					$comment = $comment_list->get_comments_by_id($comment_id);

					array_walk ($data, 'array_htmlspecialchars');

					if (VIVVO_COMMENTS_ENABLE_BBCODE) {
						$data['description_src'] = $data['description'];
						$data['description'] = Comments::parse_bbcode($data['description']);
					}

					if ($comment !== false) {
						$comment->populate($data);
						$this->_post_master->set_data_object($comment);
						if ($this->_post_master->sql_update()) {
							admin_log($sm->user->get_username(), 'Edited comment #' . $comment_id);
							return true;
						} else {
							$this->set_error_code(2211);
							return false;
						}
					} else {
						$this->set_error_code(2212);
						return false;
					}
				} else {
					$this->set_error_code(2213);
					return false;
				}
			} else {
				$this->set_error_code(2214);
				return false;
			}
		}

		/**
		 * Set field
		 *
		 * @param	integer		$comments_ids
		 * @param	string		$field_name
		 * @param	string		$value
		 * @param	integer		$all_matching
		 * @return	boolean		true on succes, or false on fail
		 */
		function set_field($comments_ids, $field_name, $value, $all_matching = 0) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('comment_setField', array(&$comments_ids, &$field_name, &$value, &$all_matching))) {
				return vivvo_hooks_manager::get_status();
			}


			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				if($sm->user->can('MODERATE_COMMENTS')) {
					$comment_list = new Comments_list();
					if ($all_matching == 1) {
						$comment_params = Comments_list::get_search_params_from_url($sm);
						$comment_list->search($comment_params['search_options'], '', 'ascending', 0, 0, false);
						if ($comment_list->sql_update_list($this->_post_master, array($field_name => $value), NULL, true)) {
							admin_log($sm->user->get_username(), 'Edited all selected comments');
							return true;
						} else {
							$this->set_error_code(2215);
							return false;
						}
					} else {
						if ($comment_list->get_comments_by_ids($comments_ids)) {
							if ($comment_list->sql_update_list($this->_post_master, array($field_name => $value))) {
								admin_log($sm->user->get_username(), 'Edited comments #' . trim(implode(',', $comments_ids)));
								return true;
							} else {
								$this->set_error_code(2216);
								return false;
							}
						} else {
							return false;
						}
					}
				} else {
					$this->set_error_code(2217);
					return false;
				}
			} else {
				$this->set_error_code(2218);
				return false;
			}
		}

		/**
		 * Report Inappropriate Content via e-mail
		 *
		 * @param	integer		$comment_id
		 * @return	boolean		true on succes, or false on fail
		 */
		function report_inappropriate_content($comment_id) {

			require_once(dirname(__FILE__) . '/../../../lib/vivvo/framework/PEAR/Mail.php');

			if (!vivvo_hooks_manager::call('comment_reportInappropriateContent', array(&$comments_id))) {
				return vivvo_hooks_manager::get_status();
			}


			if (VIVVO_EMAIL_ENABLE == 1) {

				$sm = vivvo_lite_site::get_instance();
				$lang = vivvo_lang::get_instance();

				if ((!$sm->user && $sm->guest_group && ($sm->guest_group->group_privileges['ARTICLE_COMMENT'] > 0)) || ($sm->user && ($sm->user->privileges['ARTICLE_COMMENT'] > 0))) {

					$comment_list = new Comments_list();
					$comment = $comment_list->get_comments_by_id($comment_id);

					if ($comment !== false) {

						if ($sm->user) {
							$user = $sm->user->get_name();
						} else {
							$user = $lang->get_value('LNG_REPORT_COMMENT_GUEST_USER');
						}

						$body .= $comment->get_article_absolute_href() . '#comment_' . $comment->get_id(). "\n\n";

						$body .= $lang->get_value('LNG_REPORT_COMMENT_MESSAGE_2') . ':' . "\n";
						$body .= $comment->get_article_title() . "\n";
						$body .= $comment->get_article_absolute_href() . "\n\n";

						$body .= $lang->get_value('LNG_REPORT_COMMENT_IP_ADDRESS_AUTHOR_COMMENT') . ': ' . $comment->get_ip() . "\n";
						$body .= $lang->get_value('LNG_REPORT_COMMENT_REPORTED_COMMENT_INFO') . ': ' . "\n";
						$body .= $user . ', (IP:' . $_SERVER['REMOTE_ADDR'] . ")\n\n";
						$body .= $lang->get_value('LNG_REPORT_COMMENT_MESSAGE_1');

						$to = VIVVO_ADMINISTRATORS_EMAIL;
						$headers['From'] = VIVVO_EMAIL_SEND_FROM;
						$headers['To'] = VIVVO_ADMINISTRATORS_EMAIL;

						$subject = str_replace('<WEBSITE_TITLE>' , VIVVO_WEBSITE_TITLE, $lang->get_value('LNG_REPORT_COMMENT_MAIL_SUBJECT'));

						$headers['Subject'] = "=?UTF-8?B?".base64_encode($subject)."?=";
						$headers['Content-Type'] = "text/plain; charset=UTF-8;";

						if (VIVVO_EMAIL_SMTP_PHP == 1) {
							$mail_object = new Mail();
							$mail_object->send($to, $headers, $body);
						} else {
							$mail_options['driver']    = 'smtp';
							$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
							$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
							$mail_options['localhost'] = 'localhost';

							if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != '') {
								$mail_options['auth'] = true;
								$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME ;
								$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
							} else {
								$mail_options['auth'] = false;
								$mail_options['username']  = '';
								$mail_options['password']  = '';
							}

							$mail_object = Mail::factory('smtp', $mail_options);

							$mail_object->send($to, $headers, $body);
						}
						return true;
					} else {
						$this->set_error_code(2219);
						return false;
					}
				} else {
					$this->set_error_code(2220);
					return false;
				}
			}
		}

		/**
		 * Bad words filter.
		 *
		 * @param	string	$input_words
		 * @return	string	$input_words
		 */
		function bad_words_filter($input_words) {

			preg_match_all('/[^\"\.,\s;<>\\\{\}\]\[\(\)]+/m', strtolower(VIVVO_COMMENTS_BAD_WORDS), $bad_words_array);

			$original_input = $input_words; //needed for hook call below

			foreach ($bad_words_array[0] as $bw) {
				$bw = preg_quote($bw, '/');
				$pattern = "/(?:\b)".$bw."(?:\b)/i";
				$replacement = str_repeat("*", strlen($bw));
				$input_words = preg_replace($pattern, $replacement, $input_words);
			}

			vivvo_hooks_manager::call('comment_bad_words_filter', array(&$original_input, &$input_words, &$bad_words_array) );

			return $input_words;
		}

		/**
		 * Bad ip filter. Detect bad ip.
		 *
		 * @return	boolean		true on succes, or false on fail
		 */
		function bad_ip_filter() {
			$ip = $_SERVER['REMOTE_ADDR'];
			preg_match_all('/[^\",\s;<>\\\{\}\]\[\(\)]+/m', VIVVO_COMMENTS_IP_FITER, $bad_ip_array);

			foreach ($bad_ip_array[0] as $bad_ip) {

				$bad_ip = str_replace('?','\d',$bad_ip);
				$bad_ip = str_replace('.','\.',$bad_ip);
				$bad_ip = str_replace('*','[^\.]',$bad_ip);
				$pattern = "/".$bad_ip."/";

				if (preg_match($pattern, $ip)) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Edit comment
		 *
		 * @param	integer		$comment_id
		 * @param	array		$data
		 * @return	boolean		true on succes, or false on fail
		 */
		function vote($comment_id, $vote) {

			if (!vivvo_hooks_manager::call('comment_vote', array(&$comment_id, &$vote))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			$dm = $sm->get_dump_manager();

			if ((!$sm->user && $sm->guest_group && ($sm->guest_group->group_privileges['ARTICLE_COMMENT'] > 0)) || ($sm->user && ($sm->user->privileges['ARTICLE_COMMENT'] > 0))) {

				if (!isset($_SESSION['vivvo']['comment'])) {
					$_SESSION['vivvo']['comment'] = array();
				}

				if (in_array($comment_id, $_SESSION['vivvo']['comment'])) {
					$this->set_error_code(2222);
					return false;
				} else {
					if (($vote != 1) and ($vote != -1)) {
						$this->set_error_code(2223);
						return false;
					}

					$comment_list = new Comments_list();
					$comment = $comment_list->get_comments_by_id($comment_id);

					if ($comment !== false) {
						$vote += $comment->get_vote();
						$comment->set_vote($vote);
						$this->_post_master->set_data_object($comment);
						if ($this->_post_master->sql_update()) {
							$_SESSION['vivvo']['comment'][] = $comment->id;
							$dm->add_dump('info', 0 , $vote);
							return true;
						} else {
							$this->set_error_code(2224);
							return false;
						}
					} else {
						$this->set_error_code(2225);
						return false;
					}
				}
			} else {
				$this->set_error_code(2226);
				return false;
			}
		}
	}
?>