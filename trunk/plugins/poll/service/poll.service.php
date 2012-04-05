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
	 * @see  '/../poll.class.php'
 	 */
	require_once(dirname(__FILE__) . '/../poll.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_post.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

	/**
	 * Plugin poll service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package 	vivvo_plugins
	 * @subpackage	poll
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	class poll_service extends vivvo_service {

		/**
		 * Add poll
		 * Add new poll with answers
		 *
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function add_poll ($data){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user && $sm->user->can('MANAGE_PLUGIN','poll')){
					$poll = new vivvo_poll_question();
					array_walk ($data, 'array_htmlspecialchars');
					if ($poll->populate(array('name' => $data['name'], 'question' => $data['question']),true)){
						$this->_post_master->set_data_object($poll);
						if ($this->_post_master->sql_insert()){
							$poll_id = $this->_post_master->get_work_id();
							array_walk ($data['new_answer'], 'array_htmlspecialchars');
							foreach ($data['new_answer'] as $answer){
								$answer = urldecode($answer);
								$answer_object = new vivvo_poll_answer();
								if ($answer_object->set_answer($answer) && $answer_object->set_poll_id($poll_id)){
									$this->_post_master->set_data_object($answer_object);
									$this->_post_master->sql_insert();
								}
							}
							return true;
						}else{
							$this->set_error_code(10001);
							return false;
						}
					}else{
						return false;
					}
				}else{
					$this->set_error_code(10002);
					return false;
				}
			}else{
				$this->set_error_code(10003);
				return false;
			}
		}

		/**
		 * Edit poll.
		 * Reorganize answers and edit poll question.
		 *
		 * @param	integer	$poll_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function edit_poll ($poll_id, $data){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user && $sm->user->can('MANAGE_PLUGIN','poll')){
					$poll_id = (int) $poll_id;
					$poll_list = new vivvo_poll_question_list();
					$poll = $poll_list->get_question($poll_id);

					if ($poll !== false){
						array_walk ($data, 'array_htmlspecialchars');
						if ($poll->populate(array('name' => $data['name'], 'question' => $data['question']),true)){
							$this->_post_master->set_data_object($poll);
							if ($this->_post_master->sql_update()){
								$answer_list = $poll->answer;

								if (!empty($data['answer'])){
									$remove_keys = array_diff(array_keys($answer_list->list), array_keys($data['answer']));
								}else{
									$remove_keys = array_keys($answer_list->list);
								}

								if (!empty($remove_keys)){
									$answer_list->sql_delete_list($this->_post_master, $remove_keys);
								}

								$edit_keys = $answer_list->get_list_ids();

								if (is_array($edit_keys) && !empty($edit_keys)) {
									array_walk ($data['answer'], 'array_htmlspecialchars');
									foreach ($edit_keys as $edit_key){
										$data['answer'][$edit_key] = urldecode($data['answer'][$edit_key]);
										$answer_list->list[$edit_key]->set_answer($data['answer'][$edit_key]);
										$this->_post_master->set_data_object($answer_list->list[$edit_key]);
										$this->_post_master->sql_update();
									}
								}

								if (is_array($data['new_answer']) && !empty($data['new_answer'])){
									array_walk ($data['new_answer'], 'array_htmlspecialchars');
									foreach ($data['new_answer'] as $answer){
										$answer = urldecode($answer);
										$answer_object = new vivvo_poll_answer();
										$answer_object->set_answer($answer);
										$answer_object->set_poll_id($poll_id);
										$this->_post_master->set_data_object($answer_object);
										$this->_post_master->sql_insert();
									}
								}
								return true;
							}else{
								$this->set_error_code(10004);
								return false;
							}
						}
					}else{
						$this->set_error_code(10005);
						return false;
					}
				}else{
					$this->set_error_code(10006);
						return false;
				}
			}else{
				$this->set_error_code(10007);
						return false;
			}
		}

		/**
		 * Delete poll.
		 * Delete poll question with all answers.
		 *
		 * @param	integer	$poll_id
		 * @return	boolean	true on succes, or false on fail
		 */
		function delete_poll ($poll_id){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user && $sm->user->can('MANAGE_PLUGIN','poll')){
					$poll_id = (int) $poll_id;
					$poll_list =& new vivvo_poll_question_list();
					$poll =& $poll_list->get_question($poll_id);
					$this->_post_master->set_data_object($poll);
					if ($this->_post_master->sql_delete()){
						return true;
					}else{
						$this->set_error_code(10008);
						return false;
					}
				}else{
					$this->set_error_code(10009);
					return false;
				}
			}else{
				$this->set_error_code(10010);
				return false;
			}
		}

		/**
		 * Activate poll.
		 * Deactivate poll if activated and activate new poll.
		 *
		 * @param	integer	$poll_id
		 * @return	boolean	true on succes, or false on fail
		 */
		function activate ($poll_id){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user && $sm->user->can('MANAGE_PLUGIN','poll')){
					$poll_id = (int) $poll_id;

					//Deactivate old poll if exist
					/*$old_poll_list =& new vivvo_poll_question_list(vivvo_lite_site::get_instance());
					$old_poll =& $old_poll_list->get_question_by_status('1');
					if ($old_poll !== false){
						$old_poll->set_status('0');
						$this->_post_master->set_data_object($old_poll);
						if (!($this->_post_master->sql_update())){
							$this->set_error_code(10011);
							return false;
						}
					}*/

					//Set new active poll
					$poll_list = new vivvo_poll_question_list();
					$poll = $poll_list->get_question($poll_id);
					if ($poll !== false){
						$poll->set_status('1');
						$this->_post_master->set_data_object($poll);
						if ($this->_post_master->sql_update()){
							return true;
						}else{
							$this->set_error_code(10012);
							return false;
						}
					}else{
						$this->set_error_code(10013);
						return false;
					}
				}else{
					$this->set_error_code(10014);
					return false;
				}
			}else{
				$this->set_error_code(10015);
				return false;
			}
		}

		/**
		 * Deactivate poll.
		 *
		 * @param	integer	$poll_id
		 * @return	boolean	true on succes, or false on fail
		 */
		function deactivate ($poll_id){
			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){
				if ($sm->user && $sm->user->can('MANAGE_PLUGIN','poll')){
					$poll_id = (int) $poll_id;

					$poll_list = new vivvo_poll_question_list();
					$poll = $poll_list->get_question($poll_id);
					$poll->set_status('0');

					$this->_post_master->set_data_object($poll);

					if ($this->_post_master->sql_update()){
							return true;
					}else{
						$this->set_error_code(10016);
						return false;
					}
				}else{
					$this->set_error_code(10017);
					return false;
				}
			}else{
				$this->set_error_code(10018);
				return false;
			}
		}

		/**
		 * Vote on poll.
		 *
		 * @param	integer	$answer_id
		 * @return	boolean	true on succes, or false on fail
		 */
		function poll_vote($answer_id){
			$answer_id = (int) $answer_id;
			$answer_list = new vivvo_poll_answer_list();
			$answer = $answer_list->get_answer_by_id($answer_id);
			$answer->vote++;

			if (isset ($_SESSION['vivvo']) && isset($_SESSION['vivvo']['poll']) && $_SESSION['vivvo']['poll'][$answer->poll_id]){
				return false;
			}elseif(!(isset ($_SESSION['vivvo']) && !isset($_SESSION['vivvo']['poll']))){
				if (!isset($_SESSION['vivvo'])){
					$_SESSION['vivvo'] = array();
				}
				if (!isset($_SESSION['vivvo']['poll'])){
					$_SESSION['vivvo']['poll'] = array();
				}
			}

			if (key_exists($answer->poll_id, $_SESSION['vivvo']['poll'])){
				$this->set_error_code(10019);
				return false;
			}else{
				$this->_post_master->set_data_object($answer);
				if ($this->_post_master->sql_update()){
					$_SESSION['vivvo']['poll'][$answer->poll_id] = 1;
					return true;
				}else{
					$this->set_error_code(10020);
					return false;
				}
			}
		}

	}
?>