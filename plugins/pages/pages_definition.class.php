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
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	class pages_definition extends plugin {
		/**
		 * Plugin identifier
		 *
		 * @var string
		 */
		var $plugin_name = 'pages';

		/**
		 * This file location, used to determent plugin location
		 *
		 * @var string
		 */
		var $definition_file = __FILE__;

		/**
		 * Pages install procedure
		 *
		 * @param	vivvo_site	$site_manager
		 * @param	integer		$step
		 */
		function install(&$site_manager, $step = 1) {
			parent::install($site_manager, $step);

			if ($step == 2){
				//install procedure here

				$db = $site_manager->get_db();
				$mysql = $db->getServerVersion();

				$fields = array(
					'id' => array(
						'type'     => 'integer',
						'autoincrement'  => true
					),
					'title' => array(
						'type'     => 'text',
						'length'   => 255,
						'notnull'  => 1,
						'collation' => 'utf8_unicode_ci'
					),
					'body'  => array(
						'type'     => 'clob',
						'notnull'  => 0,
						'default'  => NULL,
						'collate' => 'utf8_unicode_ci',
					),
					'hide'  => array(
						'type'     => 'integer',
						'length' => 1,
						'notnull'  => 0,
						'default'  => NULL
					),
					'sefriendly' => array(
						'type'     => 'text',
						'length'   => 255,
						'notnull'  => 1,
						'collation' => 'utf8_general_ci'
					),
					'template' => array(
						'type'     => 'text',
						'length'   => 255,
						'collation' => 'latin1_swedish_ci'
					),
					'order_number'  => array(
						'type'     => 'integer',
						'length' => 5,
						'notnull'  => 0,
						'default'  => NULL
					)
				);

				$options = array(
					'comment' => 'Pages',
					'charset' => 'utf8',
					'collate' => 'utf8_unicode_ci',
					'type'    => 'MyISAM'
				);

				$table = VIVVO_DB_PREFIX . 'pages';

				$dbm = $site_manager->get_db_manager();

				if (!$dbm->table_exists(strtolower($table))) {
					$db->manager->createTable($table, $fields, $options);
				} else {
					$db->manager->alterTable(VIVVO_DB_PREFIX . 'pages', array('add' => array( 'hide' => array( 'type' => 'integer', 'length' => '1', 'notnull'  => 0, 'default'  => NULL))), false);
				}

				$site_manager->unregister_module('box_pages');
				$site_manager->register_module('box_pages', 'box_pages', $this->get_root_dir() . 'box_pages.php');

				$site_manager->unregister_action('page');
				$site_manager->register_action('page', $this->get_root_dir() . 'service/Pages.action.php');

				// register new plugin
				$pl_manager = $site_manager->get_plugin_manager();
				$pl_manager->register('pages', $this->get_root_dir(), 'Pages');

				ui_hooks::register('plugin_pages_admin_content_subnav_hook', 'admin_content_subnav', array(
					'id' => 'menu_sub_plugin_pages',
					'href' => 'plugins.php?plugin=pages',
					'title' => 'Pages'
				), array(
					'file' => 'lib/vivvo/functions.php',
					'function' => 'main_nav_plugin_callback',
					'params' => array(
						'name' => 'pages'
					)
				));
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Uninstall Pages
		 *
		 * @param vivvo_site 	$site_manager
		 * @param integer		$step
		 */
		function uninstall(&$site_manager, $step = 1){
			parent::uninstall($site_manager, $step);

			if ($step == 2){
				//Uninstall procedure

				//Table
				$db =& $site_manager->get_db();
				$dbm =& $site_manager->get_db_manager();

				$db->manager->dropTable(VIVVO_DB_PREFIX . 'pages');

				$site_manager->unregister_module('box_pages');

				$site_manager->unregister_action('pages');

				$pl_manager = $site_manager->get_plugin_manager();
				$pl_manager->unregister('pages');

				ui_hooks::unregister('plugin_pages_admin_content_subnav_hook');
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}
	}
?>