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
	require_once(dirname(__FILE__) . '/article_images.class.php');

	/**
	 * @see  '/../../lib/vivvo/plugin/vivvo_plugin.php'
	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php');

	/**
	 * Generate box article images list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		vivvo_plugins
	 * @subpackage	image_gallery
	 * @see			plugin_module
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class box_article_images extends plugin_module {

		/**
		 * Name
		 *
		 * @var string
		 *
		 */
		var $plugin_name = 'image_gallery';

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
				$article_images_list = new ArticleImages_list();
				$article_images = $article_images_list->get_images_by_article_id($params['search_id']);

				if ($article_images){
					$this->_template->assign('image_list', $article_images);
					$this->_template->assign('image_list_object', $article_images_list);
				}
			}else{
				$this->_template->assign('image_list', strval(''));
				$this->_template->assign('image_list_object', strval(''));
			}
		}
	}

?>