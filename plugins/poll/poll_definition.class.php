<?php
/* =============================================================================
 * $Revision: 5367 $
 * $Date: 2010-05-21 15:48:34 +0200 (Fri, 21 May 2010) $
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
	 * Poll plugin definition class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		$Revision: 5367 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class poll_definition extends plugin {

		/**
		 * @var string	Plugin identifier
		 */
		public $plugin_name = 'poll';

		/**
		 * @var string	Plugin title
		 */
		public $plugin_title = 'Poll';

		/**
		 * @var string	This file location, used to determine plugin location
		 */
		public $definition_file = __FILE__;

		/**
		 * Poll install procedure
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

				$sqlRunner->variables['VIVVO_POLL_START_DATE'] = date('Y-m-d H:i:s', VIVVO_START_TIME);
				$sqlRunner->variables['VIVVO_POLL_END_DATE'] = date('Y-m-d H:i:s', VIVVO_START_TIME + 2592000);

				try {
					$sqlRunner->load() and $sqlRunner->run();
				} catch (SQLRunnerException $e) {
					$this->set_error_code(14, $e->getMessage());	// error executing sql script file
					$this->install($site_manager, 1);
					return;
				}

				$site_manager->register_action('poll', $plugin_dir . 'service/poll.action.php');

				$site_manager->register_module('box_poll', 'box_poll', $plugin_dir . 'box_poll.php');
				$site_manager->register_module('box_poll_list', 'box_poll_list', $plugin_dir . 'box_poll.php');

				$site_manager->get_url_manager()->register_url('poll', $plugin_dir . 'poll_url_handler.php', 'poll_url_handler', 'poll_content_handler');

				$site_manager->get_plugin_manager()->register($this->plugin_name, $plugin_dir, $this->plugin_title);

				//Copy files to templates and theme
				@copy($plugin_dir_abs . 'templates/poll.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/poll.tpl');
				@copy($plugin_dir_abs . 'templates/plugin_poll.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/plugin_poll.tpl');
				@copy($plugin_dir_abs . 'css/plugin_poll.css', VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/css/plugin_poll.css');

				@copy($plugin_dir_abs . 'templates/poll.tpl.original', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/poll.tpl.original');
				@copy($plugin_dir_abs . 'templates/plugin_poll.tpl.original', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/plugin_poll.tpl.original');
				@copy($plugin_dir_abs . 'css/plugin_poll.css.original', VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/css/plugin_poll.css.original');

			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Poll uninstall procedure
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

				$site_manager->unregister_action('poll');

				$site_manager->unregister_module('box_poll');
				$site_manager->unregister_module('box_poll_list');

				$site_manager->get_url_manager()->unregister_url('poll');

				$site_manager->get_plugin_manager()->unregister($this->plugin_name);
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}
	}

#EOF