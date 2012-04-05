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
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	Class multiple_attachments_definition extends plugin{
		/**
		 * Plugin identifyer
		 *
		 * @var string
		 */
		var $plugin_name = 'multiple_attachments';

		var $plugin_menu_name = 'Multiple attachments';

		var $definition_file = __FILE__;

		/**
		 * Multiple attachments install procedure
		 *
		 * @param	vivvo_site	$site_manager
		 * @param	integer		$step
		 */
		function install(&$site_manager, $step = 1){
			parent::install($site_manager, $step);

			if ($step == 2){
				//install procedure here

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
							'comment' => 'Article Attachments',
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

				$table = VIVVO_DB_PREFIX . 'article_attachments';

				$dbm =& $site_manager->get_db_manager();

				if ($dbm->table_exists($table)){
					if ($dbm->check_table($table, $fields)){
						$db->manager->dropTable($table);
					}
				}
				$db->manager->createTable($table, $fields, $options);

				$configuration =& $site_manager->get_configuration();

				$configuration->add_conf('multiple_attachments', 'id', 'multiple_attachments_tab', 'article_tabs');
				$configuration->add_conf('multiple_attachments', 'name', 'Attachments', 'article_tabs');
				$configuration->add_conf('multiple_attachments', 'template', $this->get_root_dir() . 'admin/templates/multiple_attachments_tab.xml', 'article_tabs');

				if (!$site_manager->_modules['box_paged_files']){
					$site_manager->register_module('box_paged_files', 'box_paged_files', 'lib/vivvo/box/box_paged_files.php');
				}

				$site_manager->register_module('box_article_attachments', 'box_article_attachments', $this->get_root_dir() . 'box_article_attachments.php');

				if ($handle = opendir(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/')) {
					while (false !== ($filename = readdir($handle))) {
						if ($filename[0] != "." && !is_dir(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/' . $filename)) {
							@copy(VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir() . 'templates/' . $filename, VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/' . $filename);
						}
					}
				}

				// register new plugin
				$pl_manager =& $site_manager->get_plugin_manager();
				$pl_manager->register('multiple_attachments', $this->get_root_dir(), 'Attachments');
				$site_manager->register_action('article_attachments',  $this->get_root_dir() . 'service/article_attachments.action.php');
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Uninstall Multiple attachments
		 *
		 * @param vivvo_site 	$site_manager
		 * @param integer		$step
		 */
		function uninstall(&$site_manager, $step = 1){
			parent::uninstall($site_manager, $step);

			if ($step == 2){
				//Uninstall procedure

				$db =& $site_manager->get_db();
				$dbm =& $site_manager->get_db_manager();

				$db->manager->dropTable(VIVVO_DB_PREFIX . 'article_attachments');

				$configuration =& $site_manager->get_configuration();
				$configuration->remove_from_module('article_tabs', 'multiple_attachments');
				$site_manager->unregister_module('box_article_attachments');

				$pl_manager =& $site_manager->get_plugin_manager();
				$pl_manager->unregister('multiple_attachments');
				$site_manager->unregister_action('article_attachments');
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

			$content_template->set_template_file(VIVVO_FS_PLUGIN_ROOT . $pm->plugins['multiple_attachments']['root_dir'] . 'admin/templates/attachment_list.xml');
			echo $content_template->get_output();
		}

		function load_admin_lang(){
			$pm = vivvo_lite_site::get_instance()->get_plugin_manager();
			$lang = vivvo_lang::get_instance();
			$lang->load_lang(VIVVO_ADMIN_LANG, $pm->plugins['multiple_attachments']['root_dir'] . 'admin/lang/');
		}
	}
?>