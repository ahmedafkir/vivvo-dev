<?php
/* =============================================================================
 * $Revision: 5385 $
 * $Date: 2010-05-25 11:51:09 +0200 (Tue, 25 May 2010) $
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
 * Vivvo CMS
 *
 * LICENSE:
 *
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * @copyright	SpoonLabs
 * @package		vivvo_framework
 * @subpackage	plugin
 * @version		$Revision: 5385 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */

	/**
	 * Admin view class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	plugin
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */

	class vivvo_admin_view extends vivvo_object {
		var $views = array();

		var $_template_root = 'admin/templates/';

		var $_plugin = false;

		var $_default_view = false;

		function load_template($tpl){
			$_template = new template(null, vivvo_lite_site::get_instance()->get_template());
			$_template->set_template_file($tpl);
			return $_template;
		}

		function check_privileges(){
			return true;
		}

		function handle_action(){

		}

		function load_admin_header (){

		}

		function _default_assignments() {

		}

		function &_default_view() {
			return vivvo_lite_site::get_instance()->get_template();
		}

		function get_view(){
			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$pm = $sm->get_plugin_manager();
			$dm = $sm->get_dump_manager();

			$template->assign('DUMP_MESSAGES', $dm->_dump_pool);

			$this->_default_assignments();

			$um_keys = array_keys($um->list);
			$views = array_intersect($this->views, $um_keys);

			$default_view = false;

			if (!empty($views)){
				$view = current($views);

				if (method_exists( $this, '_' . $view)){
					$tpl = call_user_func(array($this, '_' . $view));
				}elseif (file_exists($this->_template_root . $view . '.xml')){
					$tpl = $this->load_template ($this->_template_root . $view . '.xml');
				}else{
					die('No such view');
				}

				echo $tpl->get_output();
			}else if ($this->_default_view){
				$tpl =& call_user_func(array($this, $this->_default_view));
				echo $tpl->get_output();
			}else{
				$tpl = $this->_default_view();
				echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
				echo $sm->get_output();
			}

			if ($sm->user && $sm->user->is_admin()){
				$sm->fb_debug_output();
			}
		}

		function run() {
			$this->handle_action();
			$this->load_admin_header();
			$this->get_view();
		}

		function vivvo_admin_view($site_manager = null){
			if ($this->_plugin){
				$this->_template_root = VIVVO_FS_PLUGIN_ROOT . 'plugins/' . $this->_plugin . '/' . $this->_template_root;
			}else{
				$this->_template_root = VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . $this->_template_root;
			}
		}
	}

#EOF