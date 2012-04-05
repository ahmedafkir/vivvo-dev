<?php
/* =============================================================================
 * $Revision: 5127 $
 * $Date: 2010-04-26 10:03:56 +0200 (Mon, 26 Apr 2010) $
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
	 * @see  '/../../lib/vivvo/plugin/vivvo_plugin.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php');


	/**
	 * box_pages
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
	class box_pages extends plugin_module {
		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'pages';

		var $_default_template_file = 'system/box_default/box_pages.tpl';

		/**
		 * Generate box output
		 * Assign box_pages template
		 * Sets local language
		 *
		 * @param	array			$params
		 */

		function generate_output($params) {
			$this->set_template($params);
			require_once(VIVVO_FS_PLUGIN_ROOT . 'plugins/pages/Pages.class.php');
			$pg_list = new Pages_list();
			if (isset($params['search_id'])) {
				$pg_list->search(array('search_id' => intval($params['search_id'])), 'order_number', 'descending');
			} else {
				$pg_list->search(array(), 'order_number', 'descending');
			}
			$this->_template->assign('page_list', $pg_list->list);
		}
	}
?>