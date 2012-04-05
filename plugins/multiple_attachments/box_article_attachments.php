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
	 * @see  '/banner_manager.class.php'
	 */
	require_once(dirname(__FILE__) . '/ArticleAttachments.class.php');

	/**
	 * @see  '/../../lib/vivvo/plugin/vivvo_plugin.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php');

	/**
	 * Generate box article attachments list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		vivvo_plugins
	 * @subpackage	multiple_attachments
	 * @see			plugin_module
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class box_article_attachments extends plugin_module {

		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'multiple_attachments';

		/**
		 * Turn the cahce on
		 *
		 * @var boolean
		 */
		var $cache = false;

		/**
		 * Generate box output
		 * Sets local language
		 *
		 * @param	array			$params
		 */
		function generate_output($params = ''){

			// Load plugin configuration form database
			$this->set_configuration();

			// Set local template
			$this->set_template($params);

			if ($params['search_id'] > 0){
				$article_images_list = new ArticleAttachments_list();
				$article_images = $article_images_list->get_attachments_by_article_id($params['search_id']);

				if ($article_images){
					$this->_template->assign('attachment_list', $article_images);
					$this->_template->assign('attachment_list_object', $article_images_list);
				}
			}else{
				$this->_template->assign('attachment_list', strval(''));
				$this->_template->assign('attachment_list_object', strval(''));
			}
		}
	}

?>