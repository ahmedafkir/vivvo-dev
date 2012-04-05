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
	 * @see  '/newsletter.class.php'
	 */
	require_once(dirname(__FILE__) . '/newsletter.class.php');

	/**
	 * @see  '/../../lib/vivvo/plugin/vivvo_plugin.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php');


	class box_newsletter extends plugin_module {
		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'newsletter';

		/**
		 * Turn the cahce on
		 *
		 * @var boolean
		 */
		var $cache = false;
		 /**
		  * Object
		  *
		  * @var newsletter_list
		  *
		  */
		 var $newsletter_list_all;

		/**
		 * Object
		 *
		 * @var vivvo_poll_answer
		 */
		 var $newsletter;

		 /**
		 * Generate box output
		 * Assign box_poll_all template with poll and answers
		 * Sets local language
		 *
		 * @param	array			$params
		 */
		function generate_output($params = ''){
			$this->set_configuration();

			$this->newsletter_list_all = new newsletter_list();
			$this->newsletter = $this->newsletter_list_all->get_all_newsletter();

			$this->set_template($params);
 	        //$this->set_lang();

 	        $this->_template->assign('newsletter_list', $this->newsletter);
		}
	}

	class box_maillist extends plugin_module {
		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'newsletter';

		/**
		 * Turn the cahce on
		 *
		 * @var boolean
		 */
		var $cache = false;
		 /**
		  * Object
		  *
		  * @var newsletter_list
		  *
		  */
		 var $maillist_list_all;

		/**
		 * Object
		 *
		 * @var vivvo_poll_answer
		 */
		 var $maillist;

		 /**
		 * Generate box output
		 * Assign box_poll_all template with poll and answers
		 * Sets local language
		 *
		 * @param	array			$params
		 */
		function generate_output($params = ''){

			$this->set_configuration();
			$this->set_template($params);

			$subscriber_list = new maillist_list(null, $params['fields']);

			$search_params = maillist_list::get_search_params(vivvo_lite_site::get_instance(), $params);

			$subscriber_list->search($search_params['search_options'], $search_params['search_sort_by'], $search_params['search_order'], $search_params['search_limit'],$search_params['offset']);

			if (!empty($subscriber_list->list)){
				$subscriber_list->set_pagination($params['pg']);
				$this->_template->assign('maillist_list', $subscriber_list->list);
				$this->_template->assign('maillist_list_object', $subscriber_list);
			}

		}
	}
?>