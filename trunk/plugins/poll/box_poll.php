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
	 * @see  '/poll.class.php'
	 */
	require_once(dirname(__FILE__) . '/poll.class.php');

	/**
	 * @see  '/../../lib/vivvo/plugin/vivvo_plugin.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php');


	/**
	 * Box poll
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @see			plugin
	 *
	 */
	class box_poll extends plugin_module {
		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'poll';

		/**
		 * Turn the cahce on
		 *
		 * @var boolean
		 */
		var $cache = false;

		/**
		 * Generate box output
		 * Assign box_poll template with poll and answers
		 * Sets local language
		 *
		 * @param	array			$params
		 */
		function generate_output($params = '') {
			$this->set_configuration();
			$this->set_template($params);
			$this->set_lang();

			$poll_list = new vivvo_poll_question_list();

			if (isset($params['search_pid']) && $params['search_pid'] != '') {
				$poll = $poll_list->get_question($params['search_pid']);
			} else {
				$poll = $poll_list->get_question_by_status(1);
			}

			if ($poll && ($poll->status or $params['poll_list'])) {
				$this->_template->assign('poll', $poll);
				$this->_template->assign('answer_list', $poll->answer->list);
			}
		}
	}

	class box_poll_list extends plugin_module {
		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'poll';

		/**
		 * Turn the cahce on
		 *
		 * @var boolean
		 */
		var $cache = false;
		 /**
		  * Object
		  *
		  * @var vivvo_poll_question_list
		  *
		  */
		 var $poll_list_all;

		/**
		 * Object
		 *
		 * @var vivvo_poll_answer
		 */
		 var $poll;
		/**
		 * Generate box output
		 * Assign box_poll_all template with poll and answers
		 * Sets local language
		 *
		 * @param	array			$params
		 */
		function generate_output($params = ''){
			$this->set_configuration();

			$this->poll_list_all = new vivvo_poll_question_list();
			$this->poll = $this->poll_list_all->get_all_question();

			$this->set_template($params);

 	        $this->_template->assign('poll_list', $this->poll);
		}
	}
?>