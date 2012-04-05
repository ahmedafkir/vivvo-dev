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
	Class mobile_definition extends plugin{

		/**
		 * Plugin identifyer
		 *
		 * @var string
		 */
		var $plugin_name = 'mobile';

		/**
		 * This file location, used to determent plugin location
		 *
		 * @var string
		 */
		var $definition_file = __FILE__;


		/**
		 * Mobile install procedure
		 *
		 * @param	vivvo_site	$site_manager
		 * @param	integer		$step
		 */
		function install(&$site_manager, $step = 1){
			parent::install($site_manager, $step);

			if ($step == 2){

				//Definition
				$pl_manager =& $site_manager->get_plugin_manager();
				$pl_manager->register('mobile', $this->get_root_dir(), 'Mobile');

				//Url
				$um =& $site_manager->get_url_manager();
				$um->register_url('mobile', $this->get_root_dir() . 'mobile_url_manager.php', 'mobile_url_handler');
			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;
		}

		/**
		 * Uninstall Mobile
		 *
		 * @param vivvo_site 	$site_manager
		 * @param integer		$step
		 */
		function uninstall(&$site_manager, $step = 1){
			parent::uninstall($site_manager, $step);

			if ($step == 2){

				//Definition
				$pl_manager =& $site_manager->get_plugin_manager();
				$pl_manager->unregister('mobile');

				//Url
				$um =& $site_manager->get_url_manager();
				$um->unregister_url('mobile');

			}

			$output = $site_manager->get_output();

			echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
			echo $output;

		}
	}
?>