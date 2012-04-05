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
 * @see  'vivvo_plugin.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_plugin.php');

	/**
	 * Plugin class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		$Revision: 4834 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	Class image_gallery_definition extends plugin{

		/**
		 * Plugin identifier
		 *
		 * @var string
		 */
		var $plugin_name = 'image_gallery';

		/**
		 * This file location, used to determent plugin location
		 *
		 * @var string
		 */
		var $definition_file = __FILE__;

		/**
		 * Image gallery install procedure
		 *
		 * @param	vivvo_site	$site_manager
		 * @param	integer		$step
		 */
		function install(&$site_manager, $step = 1){
			parent::install($site_manager, $step);

			if ($step == 2){

				$db =& $site_manager->get_db();
				$fields = array(
					    'id' => array(
					        'type'     => 'integer',
					        'autoincrement'  => true
					    ),
					    'article_id' => array(
					        'type'     => 'integer',
					        'length'   => 4,
					        'notnull'  => 1
					    ),
					    'real_path'  => array(
					        'type'     => 'text',
					        'length'   => 255,
					        'notnull'  => 1,
   					        'collation' => 'latin1_swedish_ci'
					    ),
					    'title'  => array(
					        'type'     => 'text',
					        'length'   => 255,
					        'notnull'  => 0,
					        'collation' => 'utf8_unicode_ci'
					    ),
					    'description'  => array(
					        'type'     => 'text',
					        'notnull'  => 0,
					        'collation' => 'utf8_unicode_ci'
					    ),
					    'order_number'  => array(
					        'type'     => 'integer',
					        'length'   => 4,
					        'notnull'  => 1
					    )
					);

				$options = array(
							'comment' => 'Article Images',
							'charset' => 'utf8',
							'collate' => 'utf8_unicode_ci',
							'type'    => 'MyISAM'
				);

				$mysql = $db->getServerVersion();

				if ($mysql['major'] < 4 || ($mysql['major'] == 4 && $mysql['minor'] == 0)){
					foreach($fields as $k=> $v){
						unset($fields[$k]['collation']);
					}
					$options = array();
				}

				$table = VIVVO_DB_PREFIX . 'article_images';

				$dbm =& $site_manager->get_db_manager();

				$db->manager->createTable($table, $fields, $options);

				//Definition
				$pl_manager =& $site_manager->get_plugin_manager();
				$pl_manager->register('image_gallery', $this->get_root_dir(), 'Image Gallery');

				//Configuration
				$configuration =& $site_manager->get_configuration();

				$configuration->add_conf('image_gallery', 'id', 'image_gallery_tab', 'article_tabs');
				$configuration->add_conf('image_gallery', 'name', 'Image gallery', 'article_tabs');
				$configuration->add_conf('image_gallery', 'template', $this->get_root_dir() . 'admin/templates/image_gallery_tab.xml', 'article_tabs');

				//Action
				$site_manager->register_action('article_images',  $this->get_root_dir() . 'service/article_images.action.php');


				if (!$site_manager->_modules['box_paged_files']){
					$site_manager->register_module('box_paged_files', 'box_paged_files', 'lib/vivvo/box/box_paged_files.php');
				}

				//Boxes
				$site_manager->register_module('box_article_images', 'box_article_images', $this->get_root_dir() . 'box_article_images.php');

				if ($handle = opendir(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/article/')) {
					while (false !== ($filename = readdir($handle))) {
						if ($filename[0] != "." && !is_dir(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/article/' . $filename)) {
							@copy(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/article/' . $filename, VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'article/' . $filename);
						}
					}
				}

				if ($handle = opendir(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/box/')) {
					while (false !== ($filename = readdir($handle))) {
						if ($filename[0] != "." && !is_dir(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/box/' . $filename)) {
							@copy(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/box/' . $filename, VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/' . $filename);
						}
					}
				}

				if ($handle = opendir(VIVVO_FS_ROOT . $this->get_root_dir() . 'css/')) {
					while (false !== ($filename = readdir($handle))) {
						if ($filename[0] != "." && !is_dir(VIVVO_FS_ROOT . $this->get_root_dir() . 'css/' . $filename)) {
		        			@copy(VIVVO_FS_ROOT . $this->get_root_dir() . 'css/' . $filename, VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/css/' . $filename);
						}
		        	}
		        }

				if ($handle = opendir(VIVVO_FS_ROOT . $this->get_root_dir() . 'img/')) {
					while (false !== ($filename = readdir($handle))) {
						if ($filename[0] != "." && !is_dir(VIVVO_FS_ROOT . $this->get_root_dir() . 'img/' . $filename)) {
		        			@copy(VIVVO_FS_ROOT . $this->get_root_dir() . 'img/' . $filename, VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/img/' . $filename);
						}
		        	}
		        }

			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Uninstall Image gallery
		 *
		 * @param vivvo_site 	$site_manager
		 * @param integer		$step
		 */
		function uninstall(&$site_manager, $step = 1){
			parent::uninstall($site_manager, $step);

			if ($step == 2){

				//Table
				$db =& $site_manager->get_db();
				$dbm =& $site_manager->get_db_manager();

				$db->manager->dropTable(VIVVO_DB_PREFIX . 'article_images');

				//Definition
				$pl_manager =& $site_manager->get_plugin_manager();
				$pl_manager->unregister('image_gallery');

				//Action
				$site_manager->unregister_action('article_images');


				//Configuration
				$configuration =& $site_manager->get_configuration();
				$configuration->remove_from_module('article_tabs', 'image_gallery');

				//Boxes
				$site_manager->unregister_module('box_article_images');

			}
			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		function get_admin_content (&$sm){
			$template =& $sm->get_template();
			$um =& $sm->get_url_manager();
			$pm =& $sm->get_plugin_manager();

			$content_template = new template($sm, $template);

			if ($um->get_param('search_id')){
				$content_template->assign('search_id', $um->get_param('search_id'));
			}

			$content_template->set_template_file(VIVVO_FS_PLUGIN_ROOT . $pm->plugins['image_gallery']['root_dir'] . 'admin/templates/image_list.xml');
			echo $content_template->get_output();
		}

		function load_admin_lang() {
			$pm = vivvo_lite_site::get_instance()->get_plugin_manager();
			$lang = vivvo_lang::get_instance();
			$lang->load_lang(VIVVO_ADMIN_LANG, $pm->plugins['image_gallery']['root_dir'] . 'admin/lang/');
		}

	}
?>