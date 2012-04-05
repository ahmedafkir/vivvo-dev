<?php
/* =============================================================================
 * $Revision: 5403 $
 * $Date: 2010-5-27 18:28:26 +0100 (Wed, 27 May 2010) $
 *
 * Vivvo CMS 4.5
 * Copyright 2005-10 SpoonLabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * ============================================================================
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
	 * Plugin class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		$Revision: 3378 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	class form_builder_definition extends plugin {

		/**
		 * Plugin identifier
		 *
		 * @var string
		 */
		var $plugin_name = 'form_builder';

		/**
		 * This file location, used to determent plugin location
		 *
		 * @var string
		 */
		var $definition_file = __FILE__;

		/**
		 * Form builder install procedure
		 *
		 * @param	vivvo_site	$site_manager
		 * @param	integer		$step
		 */

		function install($site_manager, $step = 1) {

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

				$pl_manager = $site_manager->get_plugin_manager();
				$pl_manager->register('form_builder', $plugin_dir, 'Form builder');

				//Action
				$site_manager->register_action('form_builder', $plugin_dir . 'service/form_builder.action.php');

				//Box
				$site_manager->register_module('box_form', 'box_form', $plugin_dir . 'box_form.php');

				//Url
				$um = $site_manager->get_url_manager();
				$um->register_url('form_builder', $plugin_dir . 'form_builder_url_handler.php', 'form_builder_url_handler', 'form_builder_content_handler');

				$um->register_url('contact', $plugin_dir . 'form_builder_url_handler.php', 'form_builder_url_handler', 'form_builder_content_handler');

				@copy(VIVVO_FS_PLUGIN_ROOT . $plugin_dir . 'css/plugin_form_builder.css', VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/css/plugin_form_builder.css');
				@copy(VIVVO_FS_PLUGIN_ROOT . $plugin_dir . 'img/icon_pref_help.gif', VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/img/icon_pref_help.gif');
				@copy(VIVVO_FS_PLUGIN_ROOT . $plugin_dir . 'js/form_builder.js', VIVVO_FS_ROOT . 'js/form_builder.js');
				@copy(VIVVO_FS_PLUGIN_ROOT . $plugin_dir . 'img/icon_required.gif', VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/img/icon_required.gif');
				@copy(VIVVO_FS_PLUGIN_ROOT . $plugin_dir . 'templates/box_form.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/box_default/box_form.tpl');
				@copy(VIVVO_FS_PLUGIN_ROOT . $plugin_dir . 'templates/plugin_form_builder.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/plugin_form_builder.tpl');
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Uninstall Form builder
		 *
		 * @param vivvo_site 	$site_manager
		 * @param integer		$step
		 */
		function uninstall($site_manager, $step = 1) {

			parent::uninstall($site_manager, $step);

			if ($step == 2){
				//Uninstall procedure

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

				//Definition
				$pl_manager =& $site_manager->get_plugin_manager();
				$pl_manager->unregister('form_builder');

				//Action
				$site_manager->unregister_action('form_builder');

				//Box
				$site_manager->unregister_module('box_form');

				//Url
				$um =& $site_manager->get_url_manager();
				$um->unregister_url('form_builder');
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}
	}

#EOF