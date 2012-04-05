<?php
/* =============================================================================
 * $Revision: 5719 $
 * $Date: 2010-08-31 15:23:37 +0200 (Tue, 31 Aug 2010) $
 *
 * Vivvo CMS v4.7 (build 6082)
 *
 * Copyright (c) 2012, Spoonlabs d.o.o.
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
	 * Video Box plugin definition class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		$Revision: 5719 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class video_box_definition extends plugin {

		/**
		 * @var string	Plugin identifier
		 */
		public $plugin_name = 'video_box';

		/**
		 * @var string	Plugin title
		 */
		public $plugin_title = 'Video Box';

		/**
		 * @var string	This file location, used to determine plugin location
		 */
		public $definition_file = __FILE__;

		/**
		 * Video box install procedure
		 *
		 * @param	vivvo_site	$site_manager
		 * @param	integer		$step
		 */
		public function install(&$site_manager, $step = 1) {

			parent::install($site_manager, $step);

			if ($step == 2) {

				$plugin_dir = VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir();

				$sqlRunner = new SQLRunner(array(
					'db' => $site_manager->get_db(),
					'filename' => $plugin_dir . 'db/install.sql'
				));

				try {
					$sqlRunner->load() and $sqlRunner->run();
				} catch (SQLRunnerException $e) {
					$this->set_error_code(14, $e->getMessage());	// error executing sql script file
					$this->install($site_manager, 1);
					return;
				}

				ui_hooks::register('plugin_video_box_form_field', 'admin_article_form_before_body', array(
					'lang_file' => $this->get_root_dir() . 'admin/lang/',
					'gui_element' => 'file_picker',
					'label' => '{LNG_PLUGIN_VIDEO_BOX_ADMIN_VIDEO}',
					'name' => 'ARTICLE_video_attachment',
					'value' => '{article.video_attachment}',
					'input_id' => 'ARTICLE_video_attachment',
					'link_id' => 'video_attachment_choose',
					'options' => 'inputId: "ARTICLE_video_attachment", search_ext: "mp4,flv,mov", upload: true, butonLabel: "{LNG_PLUGIN_VIDEO_BOX_ADMIN_SELECT_VIDEO}", noneSelected: "{LNG_PLUGIN_VIDEO_BOX_ADMIN_NO_VIDEO_SELECTED}"'
				), array(
					'file' => 'lib/vivvo/functions.php',
					'function' => 'main_nav_plugin_callback',
					'params' => array(
						'name' => 'video_box'
					)
				));

				$site_manager->get_url_manager()->register_url('xspf', $this->get_root_dir() . 'url_handlers/xspf_url_handler.php', 'xspf_url_handler');

				if (!$site_manager->_modules['box_paged_files']) {
					$site_manager->register_module('box_paged_files', 'box_paged_files', 'lib/vivvo/box/box_paged_files.php');
				}

				$files = array(
					'templates/xspf.tpl' => 'templates/_syndication/xspf.tpl',
					'templates/box/plugin_video_box.tpl' => VIVVO_TEMPLATE_DIR . '/box/plugin_video_box.tpl',
					'templates/box/plugin_video_headlines.tpl' => VIVVO_TEMPLATE_DIR . '/box/plugin_video_headlines.tpl',
					'templates/box/plugin_video_playlist.tpl' => VIVVO_TEMPLATE_DIR . '/box/plugin_video_playlist.tpl',
					'templates/box/plugin_video_article.tpl' => VIVVO_TEMPLATE_DIR . '/box/plugin_video_article.tpl',
					'templates/box/plugin_video_player.tpl' => VIVVO_TEMPLATE_DIR . '/box/plugin_video_player.tpl',
					'templates/box/plugin_video_tabs.tpl' => VIVVO_TEMPLATE_DIR . '/box/plugin_video_tabs.tpl',
					'templates/summary/video_short.tpl' => VIVVO_TEMPLATE_DIR . '/summary/video_short.tpl',
					'templates/article/two_column_video.tpl' => VIVVO_TEMPLATE_DIR . '/article/two_column_video.tpl',
					'templates/category/non_equal_video.tpl' => VIVVO_TEMPLATE_DIR . '/category/non_equal_video.tpl',
					'css/plugin_video.css' => 'themes/' . VIVVO_DEFAULT_THEME . '/css/plugin_video.css',
					'img/button_play.gif' => 'themes/' . VIVVO_DEFAULT_THEME . '/img/button_play.gif',
					'modifiers/video_box.modifier.php' => 'modifiers/video_box.modifier.php'
				);

				foreach ($files as $src => $dest) {
					@copy($plugin_dir . $src, VIVVO_FS_INSTALL_ROOT . $dest);
				}

				if (!is_dir(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . 'video')) {
					@mkdir(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . 'video', 0777);
				}

				$site_manager->get_plugin_manager()->register($this->plugin_name, $this->get_root_dir(), $this->plugin_title);
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Video box uninstall procedure
		 *
		 * @param vivvo_site 	$site_manager
		 * @param integer		$step
		 */
		public function uninstall(&$site_manager, $step = 1) {

			parent::uninstall($site_manager, $step);

			if ($step == 2) {

				$plugin_dir = VIVVO_FS_PLUGIN_ROOT . $this->get_root_dir();

				$sqlRunner = new SQLRunner(array(
					'db' => $site_manager->get_db(),
					'filename' => $plugin_dir . 'db/uninstall.sql'
				));

				try {
					$sqlRunner->load() and $sqlRunner->run();
				} catch (SQLRunnerException $e) {
					$this->set_error_code(14, $e->getMessage());	// error executing sql script file
					$this->uninstall($site_manager, 1);
					return;
				}

				ui_hooks::unregister('plugin_video_box_form_field');

				$site_manager->get_url_manager()->unregister_url('xspf');

				$site_manager->get_plugin_manager()->unregister($this->plugin_name);
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}
	}
?>