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
	 * Newsletter plugin definition class
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
	class newsletter_definition extends plugin {

		/**
		 * @var string	Plugin identifier
		 */
		public $plugin_name = 'newsletter';

		/**
		 * @var string	Plugin title
		 */
		public $plugin_title = 'Newsletter';

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
				$conf->add_conf('VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_BODY', '', '&lt;vte:template&gt;Dear &lt;vte:value select=&quot;{email}&quot; /&gt;,'."\n\n".'Thank you for signing up! Click or copy and paste this URL to your browser to activate your account:'."\n".'&lt;vte:value  select=&quot;{activation_url}&quot; /&gt;'."\n\n".'Please note that your activation code is NOT your password.'."\n".'Thank you for using our service'."\n\n".'Best regards,'."\n".'&lt;vte:value select=&quot;{VIVVO_WEBSITE_TITLE}&quot; /&gt;'."\n".'&lt;vte:value select=&quot;{VIVVO_URL}&quot; /&gt;'."\n".'&lt;/vte:template&gt;', '', '');
				$conf->add_conf('VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_SUBJECT', '', 'Subscribe to Newsletter', '', '');
				$conf->add_conf('VIVVO_PLUGIN_NEWSLETTER_UNSUBSCRIBE_TEXT', '', 'Click following link to Unsubscribe:', '', '');
				$conf->add_conf('VIVVO_PLUGIN_NEWSLETTER_PERIOD', '', '60', '', '');
				$conf->add_conf('VIVVO_PLUGIN_NEWSLETTER_NUMBER_OF_MAILS', '', '50', '', '');

				$site_manager->register_action('newsletter',  $plugin_dir . 'service/newsletter.action.php');

				$site_manager->register_module('box_newsletter', 'box_newsletter', $plugin_dir . 'box_newsletter.php');
				$site_manager->register_module('box_maillist', 'box_maillist', $plugin_dir . '/box_newsletter.php');

				$site_manager->get_url_manager()->register_url('newsletter', $plugin_dir . 'newsletter_url_handler.php', 'newsletter_url_handler', 'newsletter_content_handler');

				require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php';

				$cm = new vivvo_cron_manager($site_manager);
				$cm->cron_job('11 * * * *', $plugin_dir . 'tasks/mail_queue.php', null, 'mail_queue');

				$site_manager->get_plugin_manager()->register($this->plugin_name, $plugin_dir, $this->plugin_title);

				//Copy files to templates and theme
				@copy($plugin_dir_abs . 'templates/plugin_newsletter.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/plugin_newsletter.tpl');
				@copy($plugin_dir_abs . 'templates/newsletter_confirm.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/newsletter_confirm.tpl');
				@copy($plugin_dir_abs . 'templates/newsletter_unsubscribe.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/newsletter_unsubscribe.tpl');
				@copy($plugin_dir_abs . 'templates/newsletter_user_unsubscribe.tpl', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/newsletter_user_unsubscribe.tpl');
				@copy($plugin_dir_abs . 'css/plugin_newsletter.css', VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/css/plugin_newsletter.css');
				@copy($plugin_dir_abs . 'js/plugin_newsletter.js', VIVVO_FS_ROOT . 'js/plugin_newsletter.js');

				@copy($plugin_dir_abs . 'templates/plugin_newsletter.tpl.original', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/plugin_newsletter.tpl.original');
				@copy($plugin_dir_abs . 'templates/newsletter_confirm.tpl.original', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/newsletter_confirm.tpl.original');
				@copy($plugin_dir_abs . 'templates/newsletter_unsubscribe.tpl.original', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/newsletter_unsubscribe.tpl.original');
				@copy($plugin_dir_abs . 'templates/newsletter_user_unsubscribe.tpl.original', VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'plugin/newsletter_user_unsubscribe.tpl.original');
				@copy($plugin_dir_abs . 'css/plugin_newsletter.css.original', VIVVO_FS_ROOT . 'themes/' . VIVVO_DEFAULT_THEME . '/css/plugin_newsletter.css.original');
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Newsletter uninstall procedure
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
				$conf->remove_global('VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_BODY');
				$conf->remove_global('VIVVO_PLUGIN_NEWSLETTER_SUBSCRIBE_SUBJECT');
				$conf->remove_global('VIVVO_PLUGIN_NEWSLETTER_UNSUBSCRIBE_TEXT');
				$conf->remove_global('VIVVO_PLUGIN_NEWSLETTER_PERIOD');
				$conf->remove_global('VIVVO_PLUGIN_NEWSLETTER_NUMBER_OF_MAILS');

				$site_manager->unregister_action('newsletter');

				$site_manager->unregister_module('box_newsletter');
				$site_manager->unregister_module('box_maillist');

				$site_manager->get_url_manager()->unregister_url('newsletter');

				require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php');

				$cm = new vivvo_cron_manager($site_manager);
				$cm->delete_cron_job($plugin_dir . 'tasks/mail_queue.php', null, 'mail_queue');

				$site_manager->get_plugin_manager()->unregister($this->plugin_name);
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}
	}

#EOF