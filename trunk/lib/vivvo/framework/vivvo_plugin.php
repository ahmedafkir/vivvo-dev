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
	 * Plugin manager class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	plugin
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */

	class plugin_manager extends vivvo_object{
		/**
		 * Plugin direcory
		 *
		 * @var string
		 */

		var $plugin_dir;

		/**
		 * Plugins avalable
		 *
		 * @var array
		 */
		var $plugins = array();

		/**
		 * FS plugins
		 *
		 * @var array
		 */
		var $fs_plugins;

		/**
		 * Reads configuration from info.php files
		 * in all plugins directory like bm, newsletter, poll, sitemap, ...
		 *
		 */
		function set_fs_plugins(){
			global $plugin_definition;
			if ($handle = opendir($this->plugin_dir)) {
				while ((false !== ($file = readdir($handle))) && ($temp == 0)) {
					if ($file[0] != '.'){
						$file = $this->plugin_dir . DIRECTORY_SEPARATOR . $file;
						if (is_dir($file) && is_file ($file . DIRECTORY_SEPARATOR . 'info.php')){
							include($file . DIRECTORY_SEPARATOR . 'info.php');
							$this->fs_plugins[$plugin_definition['href']] = $plugin_definition;
						}
					}
				}
			}
		}

		/**
		 * Reads configuration from configuration table
		 *
		 */

		function set_plugins(){
			$config = vivvo_lite_site::get_instance()->get_configuration();
			$plugin_list = $config->get_configuration('plugins');
			if (is_array($plugin_list) && !empty($plugin_list)){
				foreach ($plugin_list as $pld){
					$plugin_def = $config->get_configuration($pld['variable_value']);
					$this->plugins[$pld['variable_value']] = array();
					if (is_array($plugin_def) && !empty($plugin_def)){
						foreach ($plugin_def as $pd){
							$this->plugins[$pld['variable_value']][$pd['variable_name']] = $pd['variable_value'];
						}
					}
				}
			}
			asort($this->plugins);
		}

		function is_installed($name){
			if (is_array($this->plugins) && key_exists($name, $this->plugins)){
				return true;
			}
			return false;
		}

		function get_all_plugins() {
			$plugin_array = array();
			$config = vivvo_lite_site::get_instance()->get_configuration();
			$this->set_fs_plugins();
			if (is_array($this->fs_plugins) && !empty($this->fs_plugins)){
				foreach ($this->fs_plugins as $key => $pd){
					if (!key_exists($key, $this->plugins)){
						$plugin = $pd;
						if (is_array($plugin) && !empty($plugin)){
							$plugin_array[$key] = array();
							foreach ($plugin as $k => $property){
								$plugin_array[$key][$k] = $property;
							}
							$plugin_array[$key]['href'] = $key;
							$plugin_array[$key]['installed'] = 0;
							if (file_exists($this->plugin_dir . "/{$key}/description.html")){
								$plugin_array[$key]['description'] = file_get_contents($this->plugin_dir . "/{$key}/description.html");
							}
						}
						if (!isset($plugin_array[$key]['version'])){
							$plugin_array[$key]['version'] = '1.0';
						}
					}
				}
			}

			if (is_array($this->plugins) && !empty($this->plugins)){
				foreach ($this->plugins as $key => $pd){
					$plugin = $config->get_configuration($key);
					if (is_array($plugin) && !empty($plugin)){
						$plugin_array[$key] = array();
						foreach ($plugin as $property){
							$plugin_array[$key][$property['variable_name']] = $property['variable_value'];
						}
						if (isset($this->fs_plugins[$key]['version'])){
							$plugin_array[$key]['version'] = $this->fs_plugins[$key]['version'];
						}
						$plugin_array[$key]['href'] = $key;
						$plugin_array[$key]['installed'] = 1;
						if (file_exists($this->plugin_dir . "/{$key}/description.html")){
							$plugin_array[$key]['description'] = file_get_contents($this->plugin_dir . "/{$key}/description.html");
						}
						if (!isset($plugin_array[$key]['version'])){
							$plugin_array[$key]['version'] = '1.0';
						}
					}
				}
			}
			ksort($plugin_array);
			return $plugin_array;
		}

		function get_plugin_menu_item (){
			$plugin_array = array();
			if (is_array($this->plugins) && !empty($this->plugins)){
				foreach ($this->plugins as $key => $pd){
					if (file_exists($this->plugin_dir . '/'. $key . '/' . $key . '_admin_view.class.php')){
						$plugin_array[] = array('name' => $pd['name'], 'href' => $key);
					//For plugins before version 4.1
					}elseif (file_exists($this->plugin_dir . '/'. $key . '/' . $key . '_definition.class.php')){
						require_once($this->plugin_dir . '/'.  $key . '/' . $key . '_definition.class.php');
						$plugin_obj = $key . '_definition';
						if (call_user_func(array($plugin_obj,'get_plugin_menu_item'))){
							$plugin_array[] = call_user_func(array($plugin_obj,'get_plugin_menu_item'));
						}
					}
				}
			}
			ksort($plugin_array);
			return $plugin_array;
		}

		function get_local_menu () {
			$menu = '';
			if (is_array($this->plugins) && !empty($this->plugins)){
				foreach ($this->plugins as $key => $pd){
					if (file_exists($this->plugin_dir . '/'. $key . '/' . $key . '_definition.class.php')){
						require_once($this->plugin_dir . '/'.  $key . '/' . $key . '_definition.class.php');
						$plugin_obj = $key . '_definition';
						$menu .= call_user_func(array($plugin_obj,'get_local_menu'));
					}
				}
			}
			return $menu;
		}

		function url_handler($plugin_name, $url_array){
			$parsed_url = false;

			if (is_array($this->plugins) && key_exists($plugin_name, $this->plugins)){

				if (file_exists($this->plugin_dir . '/'. $plugin_name . '/' . $plugin_name . '_definition.class.php')){

					require_once($this->plugin_dir . '/'.  $plugin_name . '/' . $plugin_name . '_definition.class.php');
					$plugin_obj = $plugin_name . '_definition';
					$parsed_url = call_user_func(array($plugin_obj,'url_handler'), $url_array);
				}
			}
			return $parsed_url;
		}

		function get_content($plugin_name){
			$content = false;
			if (is_array($this->plugins) && key_exists($plugin_name, $this->plugins)){
				if (file_exists($this->plugin_dir . '/'. $plugin_name . '/' . $plugin_name . '_definition.class.php')){
					require_once($this->plugin_dir . '/'.  $plugin_name . '/' . $plugin_name . '_definition.class.php');
					$plugin_obj = $plugin_name . '_definition';
					$content = call_user_func(array($plugin_obj, 'get_content'), vivvo_lite_site::get_instance());
				}
			}
			return $content;
		}

		function get_admin_content($plugin_name){
			$content = false;
			if (is_array($this->plugins) && key_exists($plugin_name, $this->plugins)){
				if (file_exists($this->plugin_dir . '/'. $plugin_name . '/' . $plugin_name . '_definition.class.php')){
					require_once($this->plugin_dir . '/'.  $plugin_name . '/' . $plugin_name . '_definition.class.php');
					$plugin_obj = $plugin_name . '_definition';
					$content = call_user_func(array($plugin_obj, 'get_admin_content'), vivvo_lite_site::get_instance());
				}
			}
			return $content;
		}

		function get_admin_view($plugin_name){
			if (file_exists($this->plugin_dir . '/'. $plugin_name . '/' . $plugin_name . '_admin_view.class.php')){
				require_once($this->plugin_dir . '/'. $plugin_name . '/' . $plugin_name . '_admin_view.class.php');
				$plugin_obj = $plugin_name . '_admin_view';
				return new $plugin_obj(vivvo_lite_site::get_instance());
			}
			return false;
		}

		function &get_plugin_definiton ($plugin_name){
			if (is_array($this->plugins) && key_exists($plugin_name, $this->plugins)){
				if (file_exists($this->plugin_dir . '/'. $plugin_name . '/' . $plugin_name . '_definition.class.php')){
					require_once($this->plugin_dir . '/'.  $plugin_name . '/' . $plugin_name . '_definition.class.php');
					$plugin_obj = $plugin_name . '_definition';
					if (class_exists($plugin_obj)){
						return new $plugin_obj(vivvo_lite_site::get_instance());
					}else{
						return false;
					}
				}
			}
			return false;
		}

		function get_fs_plugin_definiton ($plugin_name){
			if (is_array($this->fs_plugins) && key_exists($plugin_name, $this->fs_plugins)){
				if (file_exists($this->plugin_dir . '/'. $plugin_name . '/' . $plugin_name . '_definition.class.php')){
					require_once($this->plugin_dir . '/'.  $plugin_name . '/' . $plugin_name . '_definition.class.php');
					$plugin_obj = $plugin_name . '_definition';
					if (class_exists($plugin_obj)){
						return new $plugin_obj(vivvo_lite_site::get_instance());
					}else{
						return false;
					}
				}
			}
			return false;
		}

		function load_lang (){
			$lang = vivvo_lang::get_instance();
			if (is_array($this->plugins)){
				foreach ($this->plugins as $key => $pd){
					$lang->load_lang(VIVVO_DEFAULT_LANG, $this->plugins[$key]['root_dir'] . 'lang/');
				}
			}
		}

		function load_admin_lang($plugin_name, $force = false){
			if ($this->is_installed($plugin_name)){
				$lang = vivvo_lang::get_instance();
				$lang->load_lang(VIVVO_ADMIN_LANG, $this->plugins[$plugin_name]['root_dir'] . 'admin/lang/');
			}else if ($force){
				$lang = vivvo_lang::get_instance();
				$lang->load_lang(VIVVO_ADMIN_LANG, $this->plugin_dir . '/'. $plugin_name . '/admin/lang/');
			}

		}

		function register($plugin_name, $root_dir, $name){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->add_conf('name', '', $plugin_name, 'plugins');
			$configuration->add_conf('name', '', $name, $plugin_name);
			$configuration->add_conf('root_dir', '', $root_dir, $plugin_name);
			$configuration->add_conf('installed', '', '1', $plugin_name);
		}

		function unregister($plugin_name){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->remove_from_module('plugins', 'name', $plugin_name);
			$configuration->remove_module($plugin_name);
		}

		/**
		 * Constuctor
		 *
		 * @return plugin_manager
		 */

		function plugin_manager($site_manager = null){
			$this->plugin_dir = VIVVO_FS_PLUGIN_ROOT . 'plugins';
			$this->set_plugins();
		}
	}

	/**
	 * Plugin class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	plugin
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */

	Class plugin extends vivvo_object{
		/**
		 * Plugin identifyer
		 *
		 * @var string
		 */
		var $plugin_name;

		/**
		 * Plugin definition file location
		 *
		 * @var string
		 */
		var $definition_file;

		function get_root_dir(){
			$plugin_root_dir = dirname($this->definition_file) . '/';
			$plugin_root_dir = substr($plugin_root_dir, strlen(VIVVO_FS_PLUGIN_ROOT));
			$plugin_root_dir = str_replace('\\', '/', $plugin_root_dir);
			return $plugin_root_dir;
		}

		/**
		 * Plugin install procedure
		 *
		 * @param	vivvo_site	$site_manager
		 * @param	integer		$step
		 */
		function install(&$site_manager, $step = 1){
			$template = $site_manager->get_template();

			$template->assign('PLUGIN_ROOT', $this->get_root_dir());
			$template->assign('PLUG_IN_HREF', $this->plugin_name);

			$content_template = new template(null, $template);

			if ($step == 2){
				$content_template->set_template_file(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/plugins/install/step_2.xml');
			}else{
				$content_template->set_template_file(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/plugins/install/step_1.xml');
			}

			$template->assign('content', $content_template->get_output());
			$template->assign('step', strval($step));

			$search_template = new template(null, $template);
			$search_template->set_template_file(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/plugins/install/tabs.xml');

			$template->assign('left', $search_template->get_output());
		}

		/**
		 * Uninstall Plugin
		 *
		 * @param vivvo_site 	$site_manager
		 * @param integer		$step
		 */
		function uninstall(&$site_manager, $step = 1){
			$template = $site_manager->get_template();

			$template->assign('PLUGIN_ROOT', $this->get_root_dir());
			$template->assign('PLUG_IN_HREF', $this->plugin_name);

			$content_template = new template(null, $template);

			if ($step == 2){
				$content_template->set_template_file(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/plugins/uninstall/step_2.xml');;
			}else{
				$content_template->set_template_file(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/plugins/uninstall/step_1.xml');
			}

			$template->assign('content', $content_template->get_output());
			$template->assign('step', strval($step));

			$search_template = new template(null, $template);
			$search_template->set_template_file(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/plugins/uninstall/tabs.xml');

			$template->assign('left', $search_template->get_output());
		}

		/**
		 * Analyze local part of URL
		 *
		 * @return mixed array with sorted data|false on fail
		 * @deprecated deprecated since version 4.1
		 */
		function url_handler($url_array){
			return false;
		}

		/**
		 * Load admin javaScript
		 *
		 * @deprecated deprecated since version 4.1
		 */
		function load_admin_js (){
			return false;
		}

		/**
		 * Get plugins page left nav menu item definition
		 *
		 * @return array|string Menu item definition
		 * @deprecated deprecated since version 4.1
		 */
		function get_plugin_menu_item (){
			return '';
		}


		/**
		 * Load admin menu
		 *
		 * @deprecated deprecated since version 4.1
		 */
		function get_local_menu () {
			return '';
		}

		/**
		 * Load frontend content
		 *
		 * @param vivvo_lite_site $site_manager
		 * @return string|boolean Template output
		 * @deprecated deprecated since version 4.1
		 */
		function get_content(&$site_manager){
			return false;
		}

		/**
		 * Load admin lang
		 *
		 * @deprecated deprecated since version 4.1
		 */
		function load_admin_lang(){
			$pm = vivvo_lite_site::get_instance()->get_plugin_manager();
			$lang = vivvo_lang::get_instance();
			$lang->load_lang(VIVVO_ADMIN_LANG, $pm->plugins[$this->plugin_name]['root_dir'] . 'admin/lang/');
		}

		/**
		 * Load frontend lang
		 *
		 * @deprecated deprecated since version 4.1
		 */
		function load_lang(){
			$pm = vivvo_lite_site::get_instance()->get_plugin_manager();
			$lang = vivvo_lang::get_instance();
			$lang->load_lang(VIVVO_ADMIN_LANG, $pm->plugins[$this->plugin_name]['root_dir'] . 'lang/');
		}

		/**
		 * Load administration content
		 *
		 * @param vivvo_lite_site $sm
		 * @deprecated deprecated since version 4.1
		 */
		function get_admin_content ($sm = null){
			header("Location: " . VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'plugins.php');
		}
	}

	/**
	 * Plugin_module class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	plugin
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */

	class plugin_module extends module{
		/**
		 * Plugin identifyer
		 *
		 * @var string
		 */
		var $plugin_name;

		function set_lang(){
		}

		/**
		 * Assign plugin configuration
		 *
		 * @param array $config
		 * @return boolean
		 */
		function set_configuration($config = ''){
			if (is_array($config)){
				$this->configuration = $config;
				return true;
			}else{
				$config = vivvo_lite_site::get_instance()->get_configuration();
				$this->_configuration = $config->get_configuration_property_list($this->plugin_name);
				return true;
			}
		}

		/**
		 * Get configuration
		 *
		 * @param string $param_name
		 * @return mixed
		 */
		function get_configuration ($param_name){
			if (is_array($this->_configuration) && key_exists($param_name, $this->_configuration)){
				return $this->_configuration[$param_name];
			}else{
				return false;
			}
		}
	}

#EOF