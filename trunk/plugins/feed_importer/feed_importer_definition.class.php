<?php
/* =============================================================================
 * $Revision: 5407 $
 * $Date: 2010-05-27 16:36:49 +0200 (Thu, 27 May 2010) $
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
 * @see  'sqlrunner.class.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'sqlrunner.class.php');

	/**
	 * Feed Importer plugin definition class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		$Revision: 5407 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class feed_importer_definition extends plugin {

		/**
		 * @var string	Plugin identifier
		 */
		public $plugin_name = 'feed_importer';

		/**
		 * @var string	Plugin title
		 */
		public $plugin_title = 'Feed Importer';

		/**
		 * @var string	This file location, used to determine plugin location
		 */
		public $definition_file = __FILE__;

		/**
		 * Feed importer install procedure
		 *
		 * @param	vivvo_site 	$site_manager
		 * @param 	int			$step
		 */
		public function install($site_manager, $step = 1) {

			parent::install($site_manager, $step);

			if ($step == 2) {

				$plugin_dir_abs = VIVVO_FS_PLUGIN_ROOT . ($plugin_dir = $this->get_root_dir());

				$sqlRunner = new SQLRunner(array(
					'db' => $site_manager->get_db(),
					'filename' => $plugin_dir_abs . 'db/install.sql'
				));

				try {
					$sqlRunner->load() and $sqlRunner->run();
				} catch (SQLRunnerException $e) {
					$this->set_error_code(14, $e->getMessage());	// error executing sql script file
					$this->install($site_manager, 1);
					return;
				}

				$conf = $site_manager->get_configuration();
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_AUTHOR', '', '', '');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE', '', 15, '', '\d*');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE', '', 30, '', '\d*');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_PERIOD', '', 3, '', '[1-9]\d*');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_STATUS', '', 1, '', '0|1');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_SHOW_COMMENT', '', 1, '', '0|1');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_UPDATE_ARTICLES', '', 1, '', '0|1');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_USE_LOGO', '', 0, '', '0|1');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_TRASH', '', 0, '', '0|1');
				$conf->add_conf('VIVVO_PLUGIN_FEED_IMPORTER_USER_ID', '', '', '', '[1-9]\d*');

				$site_manager->register_action('feed',  $plugin_dir . 'service/feed.action.php');

				require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php';

				$cm = new vivvo_cron_manager($site_manager);
				$cm->cron_job('19 */3 * * *', $plugin_dir . 'tasks/auto_import_feeds.php', null, 'auto_import_feeds');

				$site_manager->get_plugin_manager()->register($this->plugin_name, $plugin_dir, $this->plugin_title);
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Feed Importer uninstall procedure
		 *
		 * @param vivvo_site 	$site_manager
		 * @param int			$step
		 */
		public function uninstall($site_manager, $step = 1) {

			parent::uninstall($site_manager, $step);

			if ($step == 2) {

				$sqlRunner = new SQLRunner(array(
					'db' => $site_manager->get_db(),
					'filename' => VIVVO_FS_PLUGIN_ROOT . ($plugin_dir = $this->get_root_dir()) . 'db/uninstall.sql'
				));

				try {
					$sqlRunner->load() and $sqlRunner->run();
				} catch (SQLRunnerException $e) {
					$this->set_error_code(14, $e->getMessage());	// error executing sql script file
					$this->uninstall($site_manager, 1);
					return;
				}

				$conf = $site_manager->get_configuration();
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_AUTHOR');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_PERIOD');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_STATUS');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_SHOW_COMMENT');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_UPDATE_ARTICLES');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_USE_LOGO');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_TRASH');
				$conf->remove_global('VIVVO_PLUGIN_FEED_IMPORTER_USER_ID');

				$site_manager->unregister_action('feed');

				require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php');

				$cm = new vivvo_cron_manager($site_manager);
				$cm->delete_cron_job($plugin_dir . 'tasks/auto_import_feeds.php', null, 'auto_import_feeds');

				$site_manager->get_plugin_manager()->unregister($this->plugin_name);
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}
	}

#EOF