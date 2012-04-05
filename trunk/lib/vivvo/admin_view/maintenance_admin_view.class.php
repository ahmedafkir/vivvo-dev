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
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_admin_view.php');

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
	class maintenance_admin_view extends vivvo_admin_view{
		var $views = array();

		var $_template_root = 'templates/maintenance/';

		function check_privileges() {
			return vivvo_lite_site::get_instance()->user->is_admin();
		}

		function handle_action(){
			vivvo_lite_site::get_instance()->execute_action();
		}

		/**
		 * Load admin javaScript
		 *
		 */
		function load_admin_header() {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_script(array(
                VIVVO_URL . 'js/swfupload/swfupload.js',
                VIVVO_URL . 'js/swfupload/swfupload_queue.js',
                VIVVO_URL . 'js/swfupload/fileprogress.js',
                VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/file_upload.js',
                VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
                VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/maintenance.js'
            ));
		}

		function _default_assignments() {
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$_config = $sm->get_configuration();
			$cron_tasks = $_config->get_configuration_property_list('cron_task');
			ksort($cron_tasks);

			if (!empty($cron_tasks)){
				require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php');
				$cron_list = new vivvo_cron_list();
				foreach ($cron_tasks as $k => $v){
					$cron_job = $cron_list->get_cron_job_by_hash(md5($v['file'] . $v['class'] . $v['method'] . $v['arguments']));
					$cron_tasks[$k]['time_mask'] = $cron_job->time_mask;
					$cron_tasks[$k]['hash'] = md5 ($v['file'] . $v['class'] . $v['method'] . $v['arguments']);
					$cron_tasks[$k]['template'] = str_replace('admin/', VIVVO_FS_ADMIN_DIR, $v['template']);
				}
			}

			$template->assign('cron_task', $cron_tasks);
		}

		function _default_view() {
			$template = vivvo_lite_site::get_instance()->get_template();
			$template->assign_template('content', $this->load_template($this->_template_root . 'content.xml'));
			$template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));
			return $template;
		}
	}
?>