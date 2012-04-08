<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
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

defined('VIVVO_FS_FRAMEWORK') or define('VIVVO_FS_FRAMEWORK', dirname(__FILE__) . '/');
defined('VIVVO_RELATIVE_URL') or define('VIVVO_RELATIVE_URL', preg_replace('|^[^:]+://[^/]+|', '', rtrim(VIVVO_URL, '/') . '/'));
defined('VIVVO_FS_FILES_DIR') or define('VIVVO_FS_FILES_DIR', 'files/');

set_include_path(VIVVO_FS_FRAMEWORK . '/PEAR' . PATH_SEPARATOR . get_include_path());

if (!defined('VIVVO_COOKIE_DOMAIN')) {
    if (preg_match('/^\d+\.\d+\.\d+\.\d+$/', $_SERVER['SERVER_NAME'])) {
        define('VIVVO_COOKIE_DOMAIN', $_SERVER['SERVER_NAME']);
    } else if (preg_match('/^www\./', $_SERVER['SERVER_NAME'])) {
        define('VIVVO_COOKIE_DOMAIN', '.' . preg_replace('/^www\./','', $_SERVER['SERVER_NAME']));
    } else if (strpos($_SERVER['SERVER_NAME'], '.') === false) {
        define('VIVVO_COOKIE_DOMAIN', '');
    } else {
        define('VIVVO_COOKIE_DOMAIN', '.' . $_SERVER['SERVER_NAME']);
    }
}

defined('VIVVO_COOKIE_PATH') or define('VIVVO_COOKIE_PATH', VIVVO_RELATIVE_URL);
defined('VIVVO_COOKIE_PREFIX') or define('VIVVO_COOKIE_PREFIX', 'Vivvo');

ini_set('session.cookie_domain', VIVVO_COOKIE_DOMAIN);
ini_set('session.cookie_path', VIVVO_COOKIE_PATH);

if (!function_exists('json_decode') or !function_exists('json_encode')) {
	require_once(dirname(__FILE__) . '/PEAR/JSON.php');
	if (!function_exists('json_decode')) {
		function json_decode($str, $flag = false) {
			if ($flag) {
				$value = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
			} else {
				$value = new Services_JSON();
			}
			return $value->decode($str);
		}
	}
	if (!function_exists('json_encode')) {
		function json_encode($str) {
			$value = new Services_JSON();
			return $value->encode($str);
		}
	}
}

require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/box/ui_hooks.php';
require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_ga.php';

/**
 * Vivvo CMS
 *
 * LICENSE:
 *
 * @package    vivvo_framework
 * @subpackage framework
 * @author     Slobodan Utvic <utvara@spoonlabs.com>
 * @copyright  SpoonLabs
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * @version    $Revision: 6846 $
 * @category   CMS
 */

	/**
	 * vivvo_site class
	 * Site manager
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	*/
	class vivvo_site {

		/**
		 * PEAR DB object
		 *
		 * @var object
		 */
		var $_db;

		/**
		 * Configuration manager
		 *
		 * @var configuration
		 */
		var $_configuration;

		/**
		 * Debug stack
		 *
		 * @var array
		 */
		 var $_debug_stack;

		/**
		 * Loaded Modules
		 *
		 * @var array
		 */
		var $_modules = array();

		/**
		 * Cashed modules
		 *
		 * @var array
		 */
		var $_loaded_modules = array();

		/**
		 * Main template manager
		 *
		 * @var template
		 */
		var $_template;

		/**
		 * Main lang template manager
		 *
		 * @var vivvo_lang
		 */
		//var $_lang;

		/**
		 * Plugin manager
		 *
		 * @var plugin_manager
		 */
		var $_plugin_manager;

		/**
		 * User manager
		 *
		 * @var vivvo_user_manager
		 */
		var $_user_manager;

		/**
		 * URL manager
		 *
		 * @var vivvo_url_manager
		 */
		var $_url_manager;

		/**
		 * Logged in user
		 *
		 * @var user
		 */
		var $user;

		/**
		 * Global dump manager
		 *
		 * @var dump_manager
		 */
		var $_dump_manager;

		/**
		 * Error manager
		 *
		 * @var vivvo_error
		 */
		var $_error_manager;

		/**
		 * Header manager
		 *
		 * @var header_manager
		 */
		var $_header_manager;

		var $_cache_manager;

		/**
		 * Vivvo file manager
		 *
		 * @var vivvo_file_manager
		 */
		var $_file_manager;


		/**
		 * Vivvo db manager
		 *
		 * @var vivvo_db_manager
		 */
		var $_db_manager;

		/*
		 * Vivvo cron manager
		 *
		 * @var vivvo_cron_manager
		 */
		var $_cron_manager;

		/**
		 * Object caching manager
		 *
		 * @var vivvo_object_cache
		 */
		var $_object_cache_manager;


		var $url_modules = array();

		/**
		 * Action (service) hadlers
		 *
		 * @var array
		 */
		var $_action_handlers = array();

		var $action_status = false;

		var $action_response = NULL;

		/**
		 * Site output
		 *
		 * @var string
		 */
		var $output;

		function get_fs_root(){
			$root = explode(DIRECTORY_SEPARATOR, dirname(__FILE__));
			array_pop($root);
			array_pop($root);
			array_pop($root);
			$root = implode(DIRECTORY_SEPARATOR, $root) . DIRECTORY_SEPARATOR;
			return $root;
		}

		function debug_push($type, $message, $level = 0){
			$this->_debug_stack[] = array($type, $message, $level);
			if (defined('VIVVO_DEBUG_MODE') && VIVVO_DEBUG_MODE > 1){
				echo $type, ': ', $message,"\n";
			}
		}

		function format_debug_output () {
			$output = '<div id="debug_output" onmouseover="this.style.height=\'200px\';"  onmouseout="this.style.height=\'40px\';" style="font-family:monospace;font-size:12px;background:#FFFFFF;height:40px;opacity:0.9;overflow-x:hidden;overflow-y:scroll;padding:6px;position:fixed;text-align:left;top:0px;border-bottom:3px solid black;z-index:10000;">';
			if (!empty($this->_debug_stack)){
				foreach ($this->_debug_stack as $debug){
					if ($debug[2] == 0){
						$output .= '<div style="padding:3px;border-bottom:1px solid black;"><span class="debug_type"> ' . $debug[0] . '</span>' . $debug[1]. '</div>';
					}else{
						$output .= '<div style="padding:3px;border-bottom:1px solid black;color:#F00;"><span class="debug_type"> ' . $debug[0] . '</span>' . $debug[1]. '</div>';
					}
				}
			}
			$output .= '</div>';
			return $output;
		}

		/**
		 * Set domain ID
		 *
		 */
		function set_domain_id(){
			$db = $this->get_db();
			$query = 'SELECT id, root_category FROM ' . VIVVO_DB_PREFIX . 'domain WHERE name = \'' . VIVVO_DOMAIN . '\'';
			$res = $db->query($query);
			if (!PEAR::isError($res)){
				$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
				$this->domain_id = $row['id'];
				define('VIVVO_DOMAIN_ID', $this->domain_id);
				define('VIVVO_DOMAIN_CATEGORY_ROOT', $row['root_category']);
			}
		}

		/**
		 * Set PEAR:MDB2 object reference
		 *
		 * @return object
		 * @uses  PEAR:MDB2
		 */
		function &set_db(){
			//dodati error handling
			require_once(dirname(__FILE__) . '/PEAR/MDB2.php');
			$dsn = array(
			    'phptype'  => VIVVO_DB_TYPE,
			    'username' => VIVVO_DB_USER,
			    'password' => VIVVO_DB_PASSWORD,
			    'hostspec' => VIVVO_DB_HOST,
			    'database' => VIVVO_DB_DATABASE,
			    'new_link' => true
			);

			$options = array(
			    'debug'       => 2,
			    'portability' => MDB2_PORTABILITY_ALL
			);

			$this->_db = MDB2::connect($dsn, $options);
			if (PEAR::isError($this->_db)) {
			    die($this->_db->getMessage());
			}
			$this->_db->query('SET NAMES utf8;');
			$mysql = $this->_db->getServerVersion();
			if (defined('VIVVO_FORCE_USE_COLLATE') || ($mysql['major'] > 4 || ($mysql['major'] == 4 && $mysql['minor'] >= 1))){
				define ('VIVVO_USE_COLLATE', true);
			}else{
				define ('VIVVO_USE_COLLATE', false);
			}
			return $this->_db;
		}

		/**
		 * Get PEAR:MDB2 object
		 *
		 * @return MDB2_Driver_Common PEAR:MDB2 object
		 */
		function &get_db(){
			if (!is_a($this->_db, 'MDB2_Driver_Common')){
				return $this->set_db();
			}else {
				return $this->_db;
			}
		}

		/**
		 * Load nessery files for actions
		 *
		 * @param string	$action		service name
		 */
		function load_action_handlers($action = false){
			global $service_definition;

			if ($action === false) $action = $this->_url_manager->get_param('action');

			if ($action){
				if (!isset($this->_action_handlers[$action])){
					$configuration = $this->get_configuration();
					$vivvo_service_files = $configuration->get_configuration_property_list('vivvo_action');
					$plugin_service_files = $configuration->get_configuration('vivvo_plugins_action');

					if (is_array($vivvo_service_files) && key_exists($action, $vivvo_service_files)){
						if (file_exists(VIVVO_FS_INSTALL_ROOT . $vivvo_service_files[$action])){
							require_once(VIVVO_FS_INSTALL_ROOT . $vivvo_service_files[$action]);
							$this->_action_handlers[$action] = $service_definition;
						}
					}else if (is_array($plugin_service_files) && key_exists($action, $plugin_service_files)){
						if (file_exists(VIVVO_FS_PLUGIN_ROOT . $vivvo_service_files[$action])){
							require_once(VIVVO_FS_PLUGIN_ROOT . $vivvo_service_files[$action]);
							$this->_action_handlers[$action] = $service_definition;
						}
					}
				}
			}

		}

		/**
		 * Register action handler
		 *
		 * @param {string} $name  action handler name
		 * @param {string} $path  action handler file path
		 *
		 */
		function register_action($name, $path){
			$this->_configuration->add_conf($name, '', $path, 'vivvo_action');
		}

		/**
		 * Unregister action handler
		 *
		 * @param {string} $name  action handler name
		 *
		 */
		function unregister_action($name){
			$this->_configuration->remove_from_module('vivvo_action', $name);
		}

		/**
		 * Enter description here...
		 *
		 * @param string		$action		service name
		 * @param string		$command	service command
		 */
		function execute_action($action = false, $command = false){
			if ($action === false) $action = $this->_url_manager->get_param('action');
			if ($command === false) $command = $this->_url_manager->get_param('cmd');

			if ($action && $command){
				if (isset($this->_action_handlers[$action])){
					if (isset($this->_action_handlers[$action][$action . '.' . $command])){
						$this->action_status = call_user_func_array($this->_action_handlers[$action][$action . '.' . $command]['function'], array(&$this, $action, $command));
						return $this->action_status;
					}
				}
			}
		}

		function check_token(){
			if (defined('VIVVO_SECURITY_TOKEN')){
				if ($this->_url_manager->isset_param('SECURITY_TOKEN') && $this->_url_manager->get_param('SECURITY_TOKEN') == VIVVO_SECURITY_TOKEN){
					return true;
				}
			}
			return false;
		}

		/**
		 * Set PEAR:Cache_Lite object reference
		 *
		 * @deprecated
		 * @return Cache_Lite
		 * @uses  PEAR:Cache_Lite
		 */
		function &set_cache_manager() {
			return false;
		}

		/**
		 * Get PEAR:Cache_Lite object
		 *
		 * @deprecated
		 * @return Cache_Lite PEAR:Cache_Lite object
		 */
		function &get_cache_manager(){
			$cm = false;
			return $cm;
		}

		/**
		 * Set user manager reference
		 *
		 * @return vivvo_user_manager
		 */
		function &set_user_manager(){
			require_once(dirname(__FILE__) . '/vivvo_user_manager.php');
			$this->_user_manager = new vivvo_user_manager($this);
			return $this->_user_manager;
		}

		/**
		 * Get user manager reference
		 *
		 * @return vivvo_user_manager
		 */
		function &get_user_manager(){
			if (!is_a($this->_user_manager, 'vivvo_user_manager'))
				return $this->set_user_manager();
			return $this->_user_manager;
		}

		/**
		 * Set file manager reference
		 *
		 * @return vivvo_file_manager
		 */
		function &set_file_manager(){
			require_once(dirname(__FILE__) . '/vivvo_file.php');
			$this->_file_manager = new vivvo_file_manager($this);
			return $this->_file_manager;
		}

		/**
		 * Get file manager reference
		 *
		 * @return vivvo_file_manager
		 */
		function &get_file_manager(){
			if (!is_a($this->_file_manager, 'vivvo_file_manager'))
				return $this->set_file_manager();
			return $this->_file_manager;
		}

		/**
		 * Set db manager reference
		 *
		 * @return vivvo_db_manager
		 */
		function &set_db_manager(){
			require_once(dirname(__FILE__) . '/vivvo_db_manager.php');
			$this->_db_manager = new vivvo_db_manager($this);
			return $this->_db_manager;
		}

		/**
		 * Get db manager reference
		 *
		 * @return vivvo_db_manager
		 */
		function &get_db_manager(){
			if (!is_a($this->_db_manager, 'vivvo_db_manager'))
				return $this->set_db_manager();
			return $this->_db_manager;
		}

		/**
		 * Set cron manager reference
		 *
		 * @return vivvo_cron_manager
		 */
		function &set_cron_manager(){
			require_once(dirname(__FILE__) . '/vivvo_cron.php');
			$this->_cron_manager = new vivvo_cron_manager($this);
			return $this->_cron_manager;
		}

		/**
		 * Get cron manager reference
		 *
		 * @return vivvo_cron_manager
		 */
		function &get_cron_manager(){
			if (!is_a($this->_cron_manager, 'vivvo_cron_manager'))
				return $this->set_cron_manager();
			return $this->_cron_manager_manager;
		}

		/**
		 * Set template reference
		 *
		 * @return template
		 */
		function &set_template(){
			require_once(dirname(__FILE__) . '/vivvo_xml_template.php');
			$parent = false;
			$this->_template = new template($this, $parent);
			return $this->_template;
		}

		/**
		 * Get template reference
		 *
		 * @return template
		 */
		function &get_template(){
			if (!is_a($this->_template, 'template'))
				return $this->set_template();
			return $this->_template;
		}

		function &set_object_cache_manager(){
			$this->_object_cache_manager = new vivvo_object_cache($this);
			return $this->_object_cache_manager;
		}

		/**
		 * Get object cache manager
		 *
		 * @return vivvo_object_cache
		 */
		function &get_object_cache_manager(){
			if (!is_a($this->_object_cache_manager, 'vivvo_object_cache'))
				return $this->set_object_cache_manager();
			return $this->_object_cache_manager;
		}

		function &set_url_manager(){
			$this->_url_manager = new vivvo_url_manager($this);
			return $this->_url_manager;
		}

		/**
		 * Get url manager
		 *
		 * @return vivvo_url_manager
		 */
		function &get_url_manager(){
			if (!is_a($this->_url_manager, 'vivvo_url_manager'))
				return $this->set_url_manager();
			return $this->_url_manager;
		}


		function set_url_modules(){
			$this->url_modules = $this->_configuration->get_configuration_property_list('url_modules');
		}

		function get_url_modules(){
			return $this->url_modules;
		}

		function is_registered_url($url){
			if (!empty($this->url_modules)){
                return (key_exists($url, $this->url_modules)) ? true : false;    
            }
			return false;
		}

		function load_url_handler($url){
			if (key_exists($url, $this->url_modules)){
				if (file_exists(VIVVO_FS_INSTALL_ROOT . $this->url_modules[$url]['file'])){
					require_once(VIVVO_FS_INSTALL_ROOT . $this->url_modules[$url]['file']);
				} elseif (file_exists(VIVVO_FS_PLUGIN_ROOT . $this->url_modules[$url]['file'])){
					require_once(VIVVO_FS_PLUGIN_ROOT . $this->url_modules[$url]['file']);
				}
				else return false;

				return true;
			}
			else return false;
		}

		function call_url_handler($url, $url_array){
			if ($this->load_url_handler($url)){
				if (function_exists($this->url_modules[$url]['url_handler_function'])){
					return call_user_func_array($this->url_modules[$url]['url_handler_function'], array(&$this, $url_array));
				}
				else return false;
			}
			return false;
		}

		function call_url_content_handler($url){
			if ($this->load_url_handler($url)){
				if (function_exists($this->url_modules[$url]['content_handler_function'])){
					return call_user_func_array($this->url_modules[$url]['content_handler_function'], array(&$this));
				}
				else return false;
			}
			else return false;
		}

		/**
		 * Set plugin manager reference
		 *
		 * @return plugin_manager
		 */
		function &set_plugin_manager(){
			require_once(dirname(__FILE__) . '/vivvo_plugin.php');
			$this->_plugin_manager = new plugin_manager($this);
			return $this->_plugin_manager;
		}

		/**
		 * Get plugin manager reference
		 *
		 * @return plugin_manager
		 */
		function &get_plugin_manager(){
			if (!is_a($this->_plugin_manager, 'plugin_manager'))
				return $this->set_plugin_manager();
			return $this->_plugin_manager;
		}

		function set_user(){
			$debug = isset($_GET['debug']) ? true : false;
			unset($_GET['debug']);

			$this->user = $this->_user_manager->get_user_from_session();
			$id = session_id();
			setcookie(VIVVO_COOKIE_PREFIX . 'SessionId', $id, 0, VIVVO_COOKIE_PATH,  VIVVO_COOKIE_DOMAIN);

			isset($_SESSION['vivvo']) or $_SESSION['vivvo'] = array();
			isset($_SESSION['vivvo']['token']) or $_SESSION['vivvo']['token'] = substr(md5(VIVVO_START_TIME . $id), 5, 10);

			define('VIVVO_SECURITY_TOKEN', $_SESSION['vivvo']['token']);

			if ($this->user) {
				$this->user->set_privileges();

				$_SESSION['vivvo']['ip'] = $this->user->ip = $this->user->user_ip();

				if ($this->user->is_admin() && $debug) {
					define('VIVVO_DEBUG_MODE', 1);
				}
			}
		}

		/**
		 * Get site user
		 *
		 * @return user
		 */

		function &get_user() {
			return $this->user;
		}

		/**
		 * Sets lang reference
		 *
		 * @return vivvo_lang
		 */
		function set_lang() {
			if ($this->_url_manager->get_param('search_lang')){
				define('VIVVO_CURRENT_LANG', $this->_url_manager->get_param('search_lang'));
			}
			else define('VIVVO_CURRENT_LANG', VIVVO_DEFAULT_LANG);

			$this->_lang = new vivvo_lang($this, VIVVO_CURRENT_LANG);

			if (!defined('VIVVO_DB_COLLATION'))
				define ('VIVVO_DB_COLLATION', 'utf8_unicode_ci');

			return $this->_lang;
		}

		/**
		 * Get lang reference
		 *
		 * @return vivvo_lang
		 */
		function get_lang() {
			if (!isset($this->_lang))
				return $this->set_lang();
			return $this->_lang;
		}

		/**
		 * Set header_manager reference
		 *
		 * @return header_manager
		 */
		function &set_header_manager(){
			$this->_header_manager = new header_manager($this);
			return $this->_header_manager;
		}

		/**
		 * Get header_manager reference
		 *
		 * @return header_manager
		 */
		function &get_header_manager(){
			if (!is_a($this->_header_manager, 'header_manager')){
				return $this->set_header_manager();
			}else {
				return $this->_header_manager;
			}
		}

		/**
		 * Set dump_manager reference
		 *
		 * @return dump_manager
		 */
		function &set_dump_manager(){
			$this->_dump_manager = new dump_manager();
			return $this->_dump_manager;
		}

		/**
		 * Get dump_manager reference
		 *
		 * @return dump_manager
		 */
		function &get_dump_manager() {
			if (!($this->_dump_manager instanceof dump_manager)) {
				$this->set_dump_manager();
			}
			return $this->_dump_manager;
		}

		/**
		 * Set error_manager reference
		 *
		 * @return vivvo_error
		 */
		function &set_error_manager(){
			$this->_error_manager = new vivvo_error();
			return $this->_error_manager;
		}

		/**
		 * Get error_manager reference
		 *
		 * @return vivvo_error
		 */
		function &get_error_manager(){
			if (!is_a($this->_error_manager, 'vivvo_error')){
				return $this->set_error_manager();
			}
			else return $this->_error_manager;
		}

		/**
		 * Set configuration handler
		 *
		 * @return configuration
		 */

		function &set_configuration(){
			require_once(dirname(__FILE__) . '/configuration.class.php');
			$this->_configuration = new configuration($this);
			return $this->_configuration;
		}

		/**
		 * Get configuration handler
		 *
		 * @return configuration
		 */
		function &get_configuration(){
			if (!is_a($this->_configuration, 'configuration')){
				return $this->set_configuration();
			}else {
				return $this->_configuration;
			}
		}

		/**
		 * Sets Vivvo registered modules.
		 *
		 * @return void
		 */
		function set_modules(){
			$this->_modules = $this->_configuration->get_configuration_property_list('modules');
		}

		/**
		 * Loads module from file.
		 *
		 * @param string   Module name
		 * @return bool    true on success, false otherwise
		 * @see register_module()
		 * @see load_module()
		 */
		function load_module_file($module_name){
			if (file_exists(VIVVO_FS_INSTALL_ROOT . $this->_modules[$module_name]['file'])){
				require_once(VIVVO_FS_INSTALL_ROOT . $this->_modules[$module_name]['file']);
				return true;
			}
			elseif (file_exists(VIVVO_FS_PLUGIN_ROOT . $this->_modules[$module_name]['file'])){
				require_once(VIVVO_FS_PLUGIN_ROOT . $this->_modules[$module_name]['file']);
				return true;
			}
			else return false;
		}

		/**
		 * Load module
		 *
		 * @param string $module_name Module name
		 * @param array $params Module constructor parameters
		 * @param integer $instance_id Instance identifyer
		 * @param boolean $cache Return cached module
		 * @return module
		 */
		function &load_module($module_name, $params = array(), &$parent_template, $instance_id = -1, $cache = false){
			$instance_id = (int) $instance_id;
			if (key_exists($module_name, $this->_modules)){
				if (($cache !== false) && key_exists($module_name, $this->_loaded_modules)){
					if ($instance_id != -1){
						return $this->_loaded_modules[$module_name][$instance_id];
					}
				}
				else{
					if (!$this->load_module_file($module_name)){
						//TODO error handling call to unknown module!
						$this->debug_push("core:", 'Box ' . $module_name .' missing (' . $this->_modules[$module_name]['class_name'] . ' from file ' . $this->_modules[$module_name]['file'] . ')' );
						return false;
					}
					$this->_loaded_modules[$module_name] = array();
					if ($instance_id === -1) $instance_id = 0;
					if (class_exists($this->_modules[$module_name]['class_name'])){
						$this->_loaded_modules[$module_name][$instance_id] = new $this->_modules[$module_name]['class_name']($this, $params, $parent_template);
						return $this->_loaded_modules[$module_name][$instance_id];
					}
					else return false;
				}
			}
			else return false;
		}

		function register_module($name, $class_name, $file){
			$configuration = $this->get_configuration();
			$configuration->add_conf($name, 'class_name', $class_name, 'modules');
			$configuration->add_conf($name, 'file', $file, 'modules');
		}

		function unregister_module($name){
			$configuration = $this->get_configuration();
			$configuration->remove_from_module('modules', $name);
		}

		/**
		 * Releases module from module stack.
		 *
		 * @param string $Module name
		 * @param integer Instance identifier
		 */
		function release_module($module_name, $instance_id = -1){
			if ($instance_id === -1)
				$instance_id = count ($this->_loaded_modules[$module_name])	- 1;

			if (isset($this->_loaded_modules[$module_name][$instance_id]))
				unset($this->_loaded_modules[$module_name][$instance_id]);
		}

		/**
		 * Get module output
		 *
		 * @param string $module_name Module name
		 * @param array $params Module constructor parameters
		 * @param template $parent_template Parent temlpate for module
		 * @return string Module output
		 */
		function get_module_output($module_name, $params = array(), &$parent_template){

			if ($module_name == 'ui_hooks') {
				$module = new ui_hooks($this, $params, $parent_template);
				return $module->get_output();
			}

			$output = '';

			if (key_exists($module_name, $this->_modules)){
				if (!$this->load_module_file($module_name)){
					$this->debug_push("core:", 'Box ' . $module_name .' missing (' . $this->_modules[$module_name]['class_name'] . ' from file ' . $this->_modules[$module_name]['file'] . ')', 1);
					return false;
				}
				$this->_loaded_modules[$module_name] = array();
				if (!isset($instance_id) || $instance_id === -1) $instance_id = 0;
				if (class_exists($this->_modules[$module_name]['class_name'])){
					$module = new $this->_modules[$module_name]['class_name']($this, $params, $parent_template);
					if (is_a($module,'module')){
						$output = $module->get_output();
						$module->__destruct();
						unset($module);
					}
				}
			}
			else $this->debug_push("core:", 'Called unregistered module ' . $module_name , 1);

			return $output;
		}

		function get_module_definition ($module_name){
			if (key_exists($module_name, $this->_modules))
				return $this->_modules[$module_name];
			return false;
		}

		/**
		 * Sets VIVVO_THEME constant if not defined.
		 *
		 * @param string   (optional) Theme name, or sets default theme if empty
		 * @return void
		 */
		function set_theme($theme = ''){
			if (!defined('VIVVO_THEME')){
				if ($theme == ''){
					$theme = VIVVO_DEFAULT_THEME;
				} elseif ($this->_url_manager->get_param('theme')){
					$theme = $this->_url_manager->get_param('theme');
				}
				define('VIVVO_THEME', VIVVO_URL . 'themes/' . $theme . '/');
			}
		}

		/**
		 * Starts vivvo session.
		 *
		 * @return void
		 */
		function start_session() {

			if (defined('VIVVO_SYSTEM_SESSION_SAVE_HANDLER')) {
				ini_set('session.save_handler', VIVVO_SYSTEM_SESSION_SAVE_HANDLER);
				if (defined('VIVVO_SYSTEM_SESSION_SAVE_PATH')) {
					ini_set('session.save_path', VIVVO_SYSTEM_SESSION_SAVE_PATH);
				}
			}

			session_name(VIVVO_COOKIE_PREFIX . 'SessionId');
			session_start();
		}

		/**
		 * Load gloabal configuration parameters from database
		 *
		 */
		function define_global_param(){
			static $defined = false;
			if ($defined) {
				return;
			}
			$defined = true;
			$config = $this->get_configuration();
			$config->get_configuration('', true);
		}

		/**
		 * Deletes cached data from database.
		 *
		 * @param string
		 * @param integer
		 * @return void
		 */
		function drop_db_cache($type = '', $id = 0) {
			vivvo_cache::get_instance()->cleanup();
		}

		/**
		 * Saves output data in database cache tables.
		 *
		 * @param string
		 * @return void
		 */
		function save_db_cache($output) {
			vivvo_cache::get_instance()->put(CACHE_URL, $output, array('page'), VIVVO_CACHE_TIME);
			$this->debug_push("cache:", 'Save from ' . CACHE_URL);
		}

		/**
		 * Get site output.
		 *
		 * @return string   Parsed generated content as HTML string.
		 */
		public function get_output() {
			$output = $this->_template->get_output();
			$output = str_replace('{$css__}', $this->_header_manager->get_css_output(), $output);
			$output = str_replace('{$scripts__}', $this->_header_manager->get_script_output(), $output);
			$output = str_replace('{$rss__}', $this->_header_manager->get_rss_output(), $output);
			$output = str_replace('{$meta_names__}', $this->_header_manager->get_meta_output(), $output);
			$output = str_replace('{$dump__}', $this->_dump_manager->get_output(), $output);
			return $output;
		}

		function vivvo_site(){
		}

		/**
		 * Defines VIVVO_PROXY_URL in case it is not already defined.
		 */
		function define_proxy_url() {

			if (!defined('VIVVO_PROXY_URL')) {
				if (defined('VIVVO_SYSTEM_PROXY_URL')) {
					define('VIVVO_PROXY_URL', VIVVO_SYSTEM_PROXY_URL);
				} else {
					define('VIVVO_PROXY_URL', '');
				}
			}

			defined('VIVVO_ABSOLUTE_URL') or define('VIVVO_ABSOLUTE_URL', make_absolute_url(''));
		}
	}

	/**
	 * vivvo_object class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_object {
		/**
		 * Reference to site manager
		 *
		 * @var vivvo_site
		 */
		//var $_site_manager;

		/**
		 * Dump manager
		 *
		 * @var dump_manager
		 */
		var $_dump_handler;

		/**
		 * Dump message
		 *
		 * @var dump_message
		 */
		var $dump_object;

		private $error_info;

		/**
		 *
		 *
		 * @return dump_message
		 */

		function set_error_code($error, $aditional_info = '') {
			if (!$this->_dump_handler) {
				$this->set_dump_handler();
			}

			$this->dump_object = $this->_dump_handler->add_dump(
				'error',
				$error,
				vivvo_lang::get_instance()->get_value('LNG_ERROR_' . $error)
			);
			$this->dump_object->additional_info = $aditional_info;
			$this->error_info = array(
				'code' => $error,
				'info' => $additional_info
			);
			return $this->dump_object;
		}

		public function get_error_info() {
			return $this->error_info;
		}

		public function set_error_info($error_info) {
			$this->error_info = $error_info;
		}

		/**
		 * Get error code
		 *
		 * @return integer
		 */
		function get_error_code(){
			if ($this->dump_object){
				return $this->dump_object->get_error_code() << 0;
			}else{
				return 0;
			}
		}

		function get_error_message(){
			if ($this->dump_object){
				if (!empty($this->dump_object->error_code)){
					return $this->dump_object->get_message();
				}
			}
			return '';
		}

		/**
		 * Assign default dump handler
		 *
		 */

		function set_dump_handler(){
			$this->_dump_handler = vivvo_lite_site::get_instance()->get_dump_manager();
		}

		/**
		 * Add new dump
		 *
		 * @param string $dump
		 * @param string $type
		 */
		function add_dump($type, $dump){
			if (!$this->_dump_handler) $this->set_dump_handler();
			$this->dump_object = $this->_dump_handler->add_dump($type, 0, $dump);
		}

		/**
		 * @deprecated
		 */
		function set_site_manager($site_manager){
		}

		/**
		 * Site manager reference
		 *
		 * @return vivvo_site
		 */
		function &get_site_manager(){
			return vivvo_lite_site::get_instance();
		}

		/**
		 * Class constructor.
		 *
		 * @deprecated
		 * @param vivvo_lite_site    Site manager instance
		 * @return void
		 */
		function vivvo_object($site_manager = null){
		}

		/**
		 * Class destructor.
		 *
		 * @return void
		 */
		function __destruct () {
			unset($this->_dump_handler);
			unset($this->dump_object);
		}

		/**
		 * Getter for _site_manager, for backward compatibility only
		 *
		 * @param	string	$name
		 * @return	vivvo_lite_site|null
		 */
		public function __get($name) {
			if ($name == '_site_manager') {
				return vivvo_lite_site::get_instance();
			}
			return null;
		}
	}

	/**
	 * vivvo_object_cache class.
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_object_cache extends vivvo_object{
		var $data = array();

		function add ($key, $id, $object){
			if (class_exists($key)){
				$this->data[$key][$id] = $object;
			}
		}

		function remove ($key, $id) {
			if (class_exists($key)){
				if (isset($this->data[$key][$id])){
					$this->data[$key][$id] = null;
					return true;
				}
			}
			return false;
		}

		function is_cached ($key, $id){
			if (class_exists($key)){
				if (isset($this->data[$key][$id])){
					if (!empty($this->data[$key][$id]))
						return true;
				}
			}
			return false;
		}


		function &retrive ($key, $id){
			if (class_exists($key)){
				if (isset($this->data[$key][$id])){
					return $this->data[$key][$id];
				}
			}
			return false;
		}

		function get_used_objects (){
			$result = array();
			if (!empty($this->data)){
				$types = array_keys($this->data);
				$types = array_unique($types);
				foreach ($types as $type){
					if (!empty($this->data[$type])){
						$ids = array_keys($this->data[$type]);
						$ids = array_unique($ids);
						foreach($ids as $id){
							$result[] = array($type, $id);
						}
					}
				}
			}
			return $result;
		}
	}

	require_once(VIVVO_FS_FRAMEWORK . 'module.class.php');

	/**
	 * dump_message class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class dump_message {
		var $message;
		var $type;
		var $id;
		var $additional_info;
		var $error_code;

		//seters geters

		function get_message (){
			return $this->message;
		}

		function get_error_code(){
			return $this->error_code;
		}

		function dump_message($id, $type, $error_code, $message){
			$this->id = $id;
			$this->type = $type;
			$this->error_code = $error_code;
			$this->message = $message;
		}
	}

	/**
	 * Dump manager class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class dump_manager{
		/**
		 * Dump stack
		 *
		 * @var array
		 */
		var $_dump_pool = array();

		/**
		 * Add message to stack
		 *
		 * @param string $message Message text
		 * @param string $type Message type (template to use)
		 */
		function &add_dump($type, $error_code, $message){
			$id = count ($this->_dump_pool);
			$this->_dump_pool[$id] = new dump_message($id, $type, $error_code, $message);
			return $this->_dump_pool[$id];
		}

		function restore_dump($hash){
			if ($hash){
				if (!empty($_SESSION['vivvo']['dump'][$hash])){
					$this->_dump_pool = unserialize($_SESSION['vivvo']['dump'][$hash]);
					unset ($_SESSION['vivvo']['dump'][$hash]);
				}
			}
		}

		function store_dump ($hash){
			if ($hash){
				if (!isset($_SESSION['vivvo'])){
					$_SESSION['vivvo'] = array();
				}
				if (!isset($_SESSION['vivvo']['dump'])){
					$_SESSION['vivvo']['dump'] = array();
				}
				$_SESSION['vivvo']['dump'][$hash] = serialize($this->_dump_pool);
			}
		}
	}

	/**
	 * Header_manager class.
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class header_manager extends vivvo_object{
		/**
		 * Java Scripts for current page
		 *
		 * @var array
		 */
		var $_scripts = array();

		/**
		 * CSS files to be loaded
		 *
		 * @var array
		 */
		var $_css = array();

		/**
		 * RSS links
		 *
		 * @var unknown_type
		 */
		var $_rss = array();

		var $_keywords = array();

		var $_description = array();

		/**
		 * Add CSS file to stack
		 *
		 * @param string $file 		CSS absolute link
		 * @param string $media 	CSS media type
		 */
		function add_css($file, $media = 'screen') {
			if (is_array($file)) {
				while(list(, $f) = each($file)) {
					$this->_css[] = array('file' => $f, 'media' => $media);
				}
			}
			elseif ($file != '') {
				$this->_css[] = array('file' => $file, 'media' => $media);
			}
		}

		/**
		 * Get CSS header definition
		 *
		 * @return string
		 */
		function get_css_output() {
			$css_dump = '';
			//$this->_css = array_unique($this->_css);

			if (is_array($this->_css) && !empty($this->_css)) {
				if (defined('VIVVO_CSS_COMPRESOR')) {
					$aditional_css = '';
					$css_dumps = '';
					$css_dump = '<link rel="stylesheet" type="text/css" href="' . VIVVO_CSS_COMPRESOR;

					$screen_css = array();

					$sm = vivvo_lite_site::get_instance();

					foreach ($this->_css as $css) {
						if ($css['media'] == 'screen' ) {
							array_push($screen_css, $css['file']);
							if (file_exists(VIVVO_FS_THEME_ROOT. str_replace(VIVVO_STATIC_URL, '', $css['file']))){
								$sm->debug_push("css:", str_replace(VIVVO_STATIC_URL, '', $css['file']));
							} elseif (file_exists(VIVVO_FS_ADMIN_ROOT. str_replace(VIVVO_STATIC_URL, '', $css['file']))){
								$sm->debug_push("css:", str_replace(VIVVO_STATIC_URL, '', $css['file']));
							} else {
								$sm->debug_push("css:", str_replace(VIVVO_STATIC_URL, '', $css['file']), 1);
							}
						} else {
							$aditional_css .= '<link rel="stylesheet" type="text/css" href="'.$css['file'].'" media="' .$css['media']. "\"/>\n";
						}
					}

					$screen_css = array_unique($screen_css);
					foreach ($screen_css as $css) {
						$css_dumps .= $css .',';
					}

					$css_dumps = rtrim($css_dumps, ',');
					$css_dumps = str_replace(VIVVO_STATIC_URL, '', $css_dumps);
					$css_dumps = str_replace('.css', '', $css_dumps);
					$css_dump .= $css_dumps;
					$css_dump .= "\" media=\"screen\"/>\n";
					$css_dump .= $aditional_css;
				} else {
					foreach ($this->_css as $css) {
						$css_dump .= '<link rel="stylesheet" type="text/css" href="'.$css['file'].'" media="' .$css['media']. "\"/>\n";
					}
				}
			}
			return $css_dump;
		}

		/**
		 * Add Script link to header
		 *
		 * @param string $file		JS file absolute link
		 */

		function add_script($file) {
			if (is_array($file)) {
				while(list(, $f) = each($file)) {
					$this->_scripts[] = $f;
				}
			}
			elseif ($file != '') {
				$this->_scripts[] = $file;
			}
		}

		/**
		 * Get Script header loading instuctions
		 *
		 * @return string
		 */
		function get_script_output() {
			    $script_dump = '';
			    $script_merge = VIVVO_HEADER_MERGE_JS;
			    $script_compress = VIVVO_HEADER_COMPRESS_JS;
			    $foreign_script = '';

			    $this->_scripts = array_unique($this->_scripts);

			    /*
			     * Script merge
			     */
			    $closed = true;
			    if ($script_merge && !isset($_COOKIE['VivvoDebugJS'])) {
				    if (is_array($this->_scripts)) {
					    foreach ($this->_scripts as $file) {
						    if (strpos($file, 'vjsf') !== false) {
							    if ($closed) {
								    $script_dump .= '<script type="text/javascript">' . "\n\r<!--\n\r";
								    $closed = false;
							    }
							    $script_dump .= "\n\r" . '// ' .$file . "\n\r";
							    $script = '';
							    $file = str_replace(VIVVO_STATIC_URL, VIVVO_FS_ROOT, $file);
							    $script = @file_get_contents($file) . "\n\r";
							    if ($script_compress) {
								    $script = preg_replace(
									    array(
										    '/^\s*\/\/.*$/m',
										    '/^\s*\/\*.*\*\//msU',
										    '/^[\s]*/m',
										    '/\s*$/m',
										    '/(;|\(|\{|,)[\r\n]+/',
										    '/[ \t]+/',
										    '/\)\s*[\r\n]+\{/m',
										    '/[\r\n]+\)/m'
									    ),
									    array(
										    '',
										    ' ',
										    '',
										    '',
										    '$1 ',
										    ' ',
										    ') {',
										    ')'
									    ),
									    $script
								    );
							    }
							    $script_dump .= $script;
						    } else {
							    if (!$closed) {
								    $script_dump .= "//-->\n\r</script>\n\r";
								    $closed = true;
							    }
							    $script_dump .= '<script type="text/javascript" src="'.$file."\"></script>\n";
						    }
					    }
				    }
				    if (!$closed) {
					    $script_dump .= "//-->\n\r</script>\n\r";
					    $closed = true;
				    }
			    } elseif (defined('VIVVO_JS_COMPRESOR') && !isset($_COOKIE['VivvoDebugJS'])) {
				    if (is_array($this->_scripts)){
					    $sm = vivvo_lite_site::get_instance();
					    foreach($this->_scripts as $scr) {
						    if (file_exists(VIVVO_FS_ROOT. str_replace(VIVVO_STATIC_URL, '', $scr))) {
							    $sm->debug_push("js:", str_replace(VIVVO_STATIC_URL, '', $scr));
						    } else {
							    $sm->debug_push("js:", str_replace(VIVVO_STATIC_URL, '', $scr), 1);
						    }
					    }
					    $script_dump = '<script type="text/javascript" src="' . VIVVO_JS_COMPRESOR;
					    $scripts_dump = implode(',', $this->_scripts);
					    $scripts_dump = str_replace(VIVVO_STATIC_URL, '', $scripts_dump);
					    $scripts_dump = str_replace('.js', '', $scripts_dump);
					    $script_dump .= $scripts_dump;
					    $script_dump .= "\"></script>\n";
				    }
			    } else {
			    /*
			     * Individual script load
			     */
				    if (is_array($this->_scripts)) {
					    foreach ($this->_scripts as $file) {
						    $script_dump .= '<script type="text/javascript" src="'.$file."\"></script>\n";
					    }
				    }
			    }
			    return $script_dump;
        }

		/**
		 * Add rss link to header
		 *
		 * @param string $file		alternate file absolute link
		 */

		function add_rss($file, $title){
			if ($file != ''){
				array_push($this->_rss, array('href' => $file, 'title' => $title));
			}
		}

		/**
		 * Get RSS header loading instuctions
		 *
		 * @return string
		 */
		function get_rss_output(){
			$rss_dump = '';
			if (is_array($this->_rss)){
				foreach ($this->_rss as $file){
					$rss_dump .= '<link rel="alternate" type="application/rss+xml" title="'.$file['title'].'" href="'.$file['href']."\" />\n";
				}
			}
			return $rss_dump;
		}

		function add_keyword($keyword){
			if ($keyword != ''){
				$keyword = explode(',', $keyword);
				$this->_keywords = array_merge($this->_keywords, $keyword);
				$this->_keywords = array_unique($this->_keywords);
			}
		}

		function add_description($description){
			if ($description != ''){
				$description = explode(',', $description);
				$this->_description = array_merge($this->_description, $description);
				$this->_description = array_unique($this->_description);
			}
		}

		/**
		 * Get RSS header loading instuctions
		 *
		 * @return string
		 */
		function get_meta_output(){
			$dump = '';
			if (is_array($this->_description)){
				if (empty($this->_description)){
					$description = VIVVO_GENERAL_META_DESCRIPTION;
				}else{
					$description = implode(',', $this->_description);
				}
				$dump .= "\n\t\t<meta name=\"Description\" content=\"".$description."\" />";
			}
			if (is_array($this->_keywords)){
				if (empty($this->_keywords)){
					$keywords = VIVVO_GENERAL_META_KEYWORDS;
				}else{
					$keywords = implode(',', $this->_keywords);
				}
				$dump .= "\n\t\t<meta name=\"Keywords\" content=\"".$keywords."\" />";
			}
			return $dump;
		}
	}

    
	/**
	 * vivvo_lang class handles language strings in Vivvo CMS.
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author      Mihail Binev
	 */
	class vivvo_lang {

		/**
		 * @var array    An array of all language strings.
		 */
		public $_lang_stack = array();

		/**
		 * @var array   An array of all default language strings.
		 */
		public $_lang_def = array();

		/**
		 * @var vivvo_lang    Instance of vivvo_lang object used for singleton pattern.
		 */
		private static $instance;

		/**
		 * Gets language string by keyname.
		 *
		 * @param string    Key name
		 * @return string   Language string, or false if not exists
		 */
		public function get_value($key) {
			if (is_array($this->_lang_stack)){
				if (key_exists($key, $this->_lang_stack)) {
					return $this->_lang_stack[$key];
				}
			}
			return false;
		}

		/**
		 * Loads language file for administration backend, or frontend.
		 *
		 * @param string   Directory path where language file resides.
		 * @return void
		 */
		public function load_from_source($src) {
			if (defined('VIVVO_ADMIN_LANG')) {
				$this->load_lang(VIVVO_ADMIN_LANG, $src);
			}
			else $this->load_lang(VIVVO_DEFAULT_LANG, $src);
		}

		/**
		 * Loads language strings from file into global scope.
		 * Default language is "vietnamese"
		 *
		 * @param string   (optional) Language name
		 * @param string   (optional) Directory for language file
		 * @return void
		 */
		public function load_lang($lang_name = '', $dir = '') {
			$lang_name = preg_replace('/[^a-zA-Z0-9\-\_]/', '', $lang_name);

			if (is_array($this->_lang_stack)) {
				global $lang;

				if ($lang_name == '') $lang_name = 'vietnamese';
				if ($dir == '') $dir = 'lang/';

				if (file_exists(VIVVO_FS_LANG_ROOT . $dir . $lang_name . '.php')){
					$lang_file = VIVVO_FS_LANG_ROOT . $dir . $lang_name . '.php';
					if ($lang_name != 'vietnamese')
						$default_lang = VIVVO_FS_LANG_ROOT . $dir . 'vietnamese.php'; //use english as default
				} elseif (file_exists(VIVVO_FS_PLUGIN_ROOT . $dir . $lang_name . '.php')) {
					$lang_file = VIVVO_FS_PLUGIN_ROOT . $dir . $lang_name . '.php';
					if ($lang_name != 'vietnamese')
						$default_lang = VIVVO_FS_PLUGIN_ROOT . $dir . 'vietnamese.php';
				}
				else $lang_file = VIVVO_FS_LANG_ROOT . $dir . 'vietnamese.php';

				if (isset($default_lang) and file_exists($default_lang) ) { //include default language, as fallback
					include($default_lang);
					$this->_lang_stack = array_merge ($this->_lang_stack, $lang);
				}

				if (file_exists($lang_file)) {
					include($lang_file);
					$this->_lang_stack = array_merge($this->_lang_stack, $lang);
				}
			}
		}

		/**
		 * Class constructor sets site manager instance and loads language file.
		 *
		 * @param vivvo_lite_site reference
		 * @param string  (optional) Language name, default is "vietnamese"
		 * @param string  (optional) Directory where language file lives
		 * @return void
		 *
		 * TODO $site_manager needs to be removed in future releases
		 */
		public function __construct($site_manager, $lang_name = '', $dir = '') {
			$this->load_lang($lang_name, $dir);
			self::$instance = $this;
		}

		/**
		 * Creates new instance of vivvo_lang.
		 *
		 * @param string  (optional) Language name, default is "vietnamese"
		 * @param string  (optional) Directory where language file lives
		 * @return void
		 */
		public static function factory($lang_name = '', $dir = '') {
			return new self(vivvo_lite_site::get_instance(), $lang_name, $dir);
		}

		/**
		 * Returns singleton instance of vivvo_lang (if created), or creates an
		 * instance of vivvo_lang and returns it.
		 *
		 * @return vivvo_lang
		 */
		public static function get_instance() {
			if (empty(self::$instance))
				new self(vivvo_lite_site::get_instance());
			return self::$instance;
		}
	}

	/**
	 * vivvo_error class.
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_error {
		function sql_error($query,$message="bad query"){
			$err_umsg = " sql_error: $message \r\n";
			$err_dmsg .= " error: " . mysql_error()."\r\n";
			$err_dmsg .= " query: $query \r\n";
			if (isset($GLOBALS['debug_mode']))
			{
				echo nl2br($err_umsg . $err_dmsg);
			} else {
				user_error_log($err_umsg, $err_dmsg);
				return;
			}
		}

		function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars){
			// define an assoc array of error string
			// in reality the only entries we should
			// consider are E_WARNING, E_NOTICE, E_USER_ERROR,
			// E_USER_WARNING and E_USER_NOTICE
			$errortype = array (
				E_ERROR           => "Error",
				E_WARNING         => "Warning",
				E_PARSE           => "Parsing Error",
				E_NOTICE          => "Notice",
				E_CORE_ERROR      => "Core Error",
				E_CORE_WARNING    => "Core Warning",
				E_COMPILE_ERROR   => "Compile Error",
				E_COMPILE_WARNING => "Compile Warning",
				E_USER_ERROR      => "User Error",
				E_USER_WARNING    => "User Warning",
				E_USER_NOTICE     => "User Notice",
				E_STRICT          => "Runtime Notice",
				E_DEPRECATED      => 'Deprecated',
				E_USER_DEPRECATED => 'User Deprecated',
				);

			$err_umsg = " error: $errno : {$errortype[$errno]} \r\n";
			$err_dmsg = " msg: $errmsg \r\n script: $filename line: $linenum \r\n";

			if ($errno != E_NOTICE && $errno != E_STRICT) {
				if (isset($GLOBALS['debug_mode'])){
					echo nl2br($err_umsg . $err_dmsg);
				} else {
					echo nl2br($err_umsg);
					user_error_log($err_umsg, $err_dmsg);
				}
			}
		}

		function die_log($errmsg = '', $filename = '', $linenum = ''){
			$err_umsg = " error:  : DIE \r\n";
			$err_dmsg = " msg: $errmsg \r\n script: $filename line: $linenum \r\n";
			echo nl2br($errmsg);
			user_error_log($err_umsg, $err_dmsg);
			die();
		}

		function user_error_log($err_umsg, $err_dmsg){
			$error_file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'php_error.txt';
			if (!file_exists($error_file)){
				$fp = @fopen($error_file, 'w');
				@fclose($fp);
				@chmod($error_file,0666);
			}
			if (is_writeable($error_file)){
				$dt = date("Y-m-d H:i:s");
				$fp = fopen($error_file, 'a+');
				fwrite($fp, "\r\n");
				fwrite($fp, $dt);
				fwrite($fp, $err_umsg);
				fwrite($fp, $err_dmsg);
				@fclose($fp);
			}
		}
	}

	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_db_list.class.php');

	/**
	 * vivvo_db_paged_list class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	database
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see			vivvo_db_list
	 */
	class vivvo_db_paged_list extends vivvo_db_list{
		var $_pagination = array();

		function add_generator_option($param_name, $param_value){
		}

		function add_pagination_option($param_name, $param_value){
			$this->_pagination[$param_name] = $param_value;
		}

		function add_order_option($param_name, $param_value = ''){
		}

		function set_pagination($cur_page = 1, $link = ''){
			if (empty($cur_page)) $cur_page = 1;
			$this->add_pagination_option('pg_href' , $link);
			$this->add_pagination_option('pg_records_per_page' , $this->_query->_limit);
			$this->add_pagination_option('pg_current' , $cur_page);
			$this->add_pagination_option('pg_total' , $this->get_total_count());
		}

		function get_pagination(){
			return $this->_pagination;
		}

		function _default_query(){

		}

		function generic_list(){
			$this->_default_query();
			$this->_query->set_offset($this->_pagination['pg_current'] * $this->_pagination['pg_records_per_page']);
			$this->_query->set_limit($this->_pagination['pg_records_per_page']);
			$this->set_list();
		}
	}

	/**
	 * vivvo_query_builder class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	database
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_query_builder extends vivvo_object{

		var $_where = array();
		var $_offset = '';
		var $_limit = '';
		var $_from = '';
		var $_fields = array();
		var $_having = array();
		var $_order = array();
		var $_group_by =  array();
		var $_count_condition = '*';
		var $_join = array();


		/**
		 * Reset query
		 *
		 */
		function reset_query(){
			$this->_where = array();
			$this->_offset = '';
			$this->_limit = '';
			$this->_from = '';
			$this->_fields = array();
			$this->_having = array();
			$this->_order = array();
			$this->_group_by = array();
			$this->_count_condition = '*';
			$this->_join = array();
		}
		/**
		 * Set sql row limit
		 *
		 * @param integer $limit
		 */
		function set_limit($limit){
			$this->_limit = (int) $limit;
		}

		/**
		 * Set SQL offset
		 *
		 * @param integer $offset
		 */
		function set_offset($offset){
			$this->_offset = (int) $offset;
		}

		function get_limit(){
			$limit = '';
			if ($this->_limit > 0){
					$limit = ' LIMIT ' . intval($this->_offset) . ',' . intval($this->_limit);
			}
			return $limit;
		}

		function add_where($condition, $operator = 'AND') {
			if ($condition != '') {
				$this->_where[] = array('condition' => $condition, 'operator' => $operator);
			}
		}

		function get_where(){
			$where = '';
			if (is_array($this->_where) && !empty($this->_where)){
				foreach ($this->_where as $condition){
					if ($where != ''){
						$where .= $condition['operator'] . ' (' . $condition['condition'] . ') ';
					} else {
						$where .= '(' . $condition['condition'] . ') ';
					}
				}
				return 'WHERE ' . $where;
			}else{
				return '';
			}

		}

		function add_having($condition, $operator = 'AND'){
			if ($condition != ''){
				$this->_having[] = array('condition' => $condition, 'operator' => $operator);
			}
		}

		function get_having(){
			$having = '';
			if (is_array($this->_having) && !empty($this->_having)){
				foreach ($this->_having as $condition){
					if ($having != ''){
						$having .= $condition['operator'] . ' (' . $condition['condition'] . ') ';
					}else {
						$having .= '(' . $condition['condition'] . ') ';
					}
				}
				return 'HAVING ' .  $having;
			}else{
				return '';
			}
		}

		function add_order($condition) {
			if ($condition != ''){
				$this->_order[] = $condition;
			}
		}

		function get_order(){
			$order = '';
			if (is_array($this->_order) && !empty($this->_order)) {
				$order = implode(', ', $this->_order);
				return 'ORDER BY ' . $order;
			} else {
				return $order;
			}
		}

		function add_group_by($condition) {
			if ($condition != '') {
				$this->_group_by[] = $condition;
			}
		}

		function get_group_by() {
			$group_by = '';
			if (is_array($this->_group_by) && !empty($this->_group_by)) {
				$group_by = implode(', ', $this->_group_by);
				return 'GROUP BY ' . $group_by;
			} else {
				return '';
			}
		}

		function add_fields($field) {
			if ($field != '') {
				$this->_fields[$field] = 1;
			}
		}

		function set_fileds($fields) {
			if (is_string($fields)) {
				$fields = explode(',', $fields);
			} else {
				$fields = (array)$fields;
			}
			$this->_fields = array_combine($fields, range(1, count($fields)));
		}

		function get_fields(){
			$fields = '';
			if (is_array($this->_fields)) {
				$fields = implode(', ', array_keys($this->_fields));
			}else{
				$fields = '*';
			}
			return 'SELECT ' . $fields;
		}

		function set_count_condition($condition){
			if ($condition == '') {
				$this->_count_condition = '*';
			} else {
				$this->_count_condition = $condition;
			}
		}

		function set_from ($from) {
			$this->_from = $from;
		}

		function get_from() {
			return 'FROM ' . $this->_from;
		}

		function add_join($condition, $key) {
			if ($condition != '' && $key != '') {
				$this->_join[$key] = $condition;
			}
		}


		/**
		 * Clears all joins
		 */
		public function clear_all_joins() {
			$this->_join = array();
		}

		/**
		 * Removes join by key
		 *
		 * @param	string	$key
		 */
		public function remove_join($key) {
			unset($this->_join[$key]);
		}

		function get_join(){
			$join = '';
			if (is_array($this->_join) && !empty($this->_join)){
				$join = implode(' ', $this->_join);
				return ' ' . $join . ' ';
			}else{
				return ' ';
			}
		}

		function get_max_query($field){
			$query = '';
			$query = 'SELECT MAX(' . $field . ') as max ' . $this->get_from() . ' ' . $this->get_join() . ' ' .
					$this->get_where() . ' ' . $this->get_group_by() . ' ' . $this->get_having() . ' ';
			vivvo_lite_site::get_instance()->debug_push("sql:", $query);
			return $query;
		}

		function get_min_query($field){
			$query = '';
			$query = 'SELECT MIN(' . $field . ') as min ' . $this->get_from() . ' ' . $this->get_join() . ' ' .
					$this->get_where() . ' ' . $this->get_group_by() . ' ' . $this->get_having() . ' ';
			vivvo_lite_site::get_instance()->debug_push("sql:", $query);
			return $query;
		}

		function get_sum_query($field){
			$query = '';
			$query = 'SELECT SUM(' . $field . ') as sum ' . $this->get_from() . ' ' . $this->get_join() . ' ' .
					$this->get_where() . ' ' . $this->get_group_by() . ' ' . $this->get_having() . ' ';
			vivvo_lite_site::get_instance()->debug_push("sql:", $query);
			return $query;
		}

		function get_total_count_query(){
			$query = '';
			$query = 'SELECT count(' . $this->_count_condition . ') AS count ' . $this->get_from() . ' ' . $this->get_join() . ' ' .
					$this->get_where() . ' ' . $this->get_group_by() . ' ' . $this->get_having() . ' ';
			vivvo_lite_site::get_instance()->debug_push("sql:", $query);
			return $query;
		}

		function get_query(){
			$query = '';
			$query = $this->get_fields() . ' ' . $this->get_from() . ' ' . $this->get_join() . ' ' . $this->get_where() . ' '.
					$this->get_group_by() . ' ' . $this->get_having() . ' ' . $this->get_order() . ' ' . $this->get_limit();
			vivvo_lite_site::get_instance()->debug_push("sql:", $query);
			return $query;
		}
	}


	/**
	 * Vivvo generic posting class.
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	database
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_post_object extends vivvo_object {
		/**
		 * SQL table to post to
		 *
		 * @var string table name
		 */
		var $_sql_table;

		/**
		 * Unique identifier field
		 *
		 * @var string id name
		 */
		var $id_key = 'id';

		var $elem = array();

		var $dump = false;

		// Callback method for getting a property
		function __get($prop_name) {

			if ($prop_name == '_site_manager') {
				return vivvo_lite_site::get_instance();	// backward compatibility
			}

			if (vivvo_property_exists(get_class($this), $prop_name)){
				return $this->$prop_name;
			}
			if (isset($this->elem[$prop_name])) {
				if (method_exists($this, 'get_' . $prop_name)){
					return $this->{'get_' . $prop_name}();
				}else{
					return $this->elem[$prop_name];
				}
			} else {
				return false;
			}
		}

		// Callback method for setting a property
		function __set($prop_name, $prop_value)
		{
			if (method_exists($this, 'set_' . $prop_name)){
				return $this->{'set_' . $prop_name}($prop_value);
			}else{
				$this->elem[$prop_name] = $prop_value;
				return true;
			}
		}

		/**
		 * Method to invoke on object data sql delete
		 *
		 * @param vivvo_post_master $post_master
		 */
		function on_delete($post_master){
			vivvo_cache::get_instance()->cleanup();
		}

		/**
		 * Method to invoke on object data sql update
		 *
		 * @param vivvo_post_master $post_master
		 */
		function on_update($post_master){
			vivvo_cache::get_instance()->cleanup();
		}

		/**
		 * Method to invoke on object data sql insert
		 *
		 * @param vivvo_post_master $post_master
		 */
		function on_insert($post_master){
			vivvo_cache::get_instance()->cleanup();
		}
        
     

		/**
		 * Populate object properties array data
		 *
		 * @param array $array data in array
		 */
		function populate($array, $dump = NULL){

			if (is_null($dump)){
				$dump = $this->dump;
			}

			if ($dump){
				$missing = array();
				$lang = vivvo_lang::get_instance();
			}

			$status = true;
			if (is_array($array) && !empty($array)){
				foreach ($array as $key => $value){
					if (vivvo_property_exists(get_class($this), $key)){
						if (($this->{'set_' . $key} ($value)) === false){
							if ($dump){
								$missing[] = $lang->get_value('LNG_DB_' . substr($this->_sql_table, strlen(VIVVO_DB_PREFIX)) . '_' . $key);
							}
							$status = false;
						}
					}else{
						$this->__set($key, $value);
					}
				}
			}
			if ($dump && (!empty($missing))){
				$this->set_error_code(12, implode(', ', $missing));

			}
            
        	return $status;
		}
        


		function vivvo_post_object($site_manager = null, $data = '', $dump = false) {
			$this->dump = $dump;
			$this->_sql_table = VIVVO_DB_PREFIX . $this->_sql_table;
			//$this->set_site_manager($site_manager);
			if (is_array($data)) $this->populate($data);
		}
	}

	//overload('vivvo_post_object');

	/**
	 * Vivvo Url Manager
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_url_manager extends vivvo_object {

		//List of param
		var $list = array();
		var $_url_array = array();


		function array_stripslashes($array){
			foreach($array as $k => $v){
				if (is_array($v)){
					$array[$k] = $this->array_stripslashes($v);
				}else{
					$array[$k] = stripslashes($v);
				}
			}
			return $array;
		}

		/**
		 * Sets param in $list
		 *
		 * @param	string	$key
		 * @param	string 	$value
		 */
		function set_param($key,$value){
     	 	if(get_magic_quotes_gpc()){
				if (is_array($value)){
					$this->list[$key] = $this->array_stripslashes($value);
				}else{
					$this->list[$key] = stripslashes($value);
				}
			}else{
				$this->list[$key] = $value;
			}
		}

		/**
		 * Gets value from $list
		 *
		 * @param	string	$key
		 * @return	mixed	string on succes or false on fail
		 */
		function get_param($key){
			return (key_exists($key, $this->list)) ? $this->list[$key] : false;
		}

		function get_param_regexp($regexp, $strip = true){
			$return_array = array();
			if (is_array($this->list) && !empty($this->list) && !empty($regexp)){
				foreach ($this->list as $key => $val){
					if (preg_match($regexp, $key)){
						if ($strip){
							$new_key = preg_replace($regexp, '', $key);
							$return_array[$new_key] = $val;
						}else{
							$return_array[$key] = $val;
						}
					}
				}
			}
			return ($return_array);
		}

		function isset_param($key){
			return (key_exists($key, $this->list));
		}

		/**
		 * Parse argument
		 *
		 * @param	string	$arg
		 * @return	string
		 */
		function parse_request($arg){
			return $arg;
		}

		function register_url($url, $file, $url_hanlder, $content_handler = ''){
			if (!preg_match('/[^a-zA-Z\d\-_\.%~]/i', $url) && !vivvo_lite_site::get_instance()->is_registered_url($url) && !empty($file) && !empty($url_hanlder)){
				require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
				require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');
				$config = new preferences();
				$pm = new vivvo_post_master();

				$config->set_variable_name($url);
				$config->set_variable_property('file');
				$config->set_variable_value($file);
				$config->set_module('url_modules');
				$config->set_domain_id(1);
				$pm->set_data_object($config);
				$pm->sql_insert();

				$config->set_variable_name($url);
				$config->set_variable_property('url_handler_function');
				$config->set_variable_value($url_hanlder);
				$config->set_module('url_modules');
				$config->set_domain_id(1);
				$pm->set_data_object($config);
				$pm->sql_insert();

				if (!empty($content_handler)){
					$config->set_variable_name($url);
					$config->set_variable_property('content_handler_function');
					$config->set_variable_value($content_handler);
					$config->set_module('url_modules');
					$config->set_domain_id(1);
					$pm->set_data_object($config);
					$pm->sql_insert();
				}

				return true;
			}
			return false;
		}

		function unregister_url($url){
			$sm =& vivvo_lite_site::get_instance();
			if ($sm->is_registered_url($url)){
				require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
				require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');
				$pm = new vivvo_post_master();

				$config_list = new preferences_list();
				if ($config_list->get_preference_by_all_variable($url,'','','url_modules',1)){
					$config_list->sql_delete_list($pm);
					unset($sm->url_modules[$url]);
					return true;
				}
			}
			return false;
		}

		function parse_url($force = false){
			//TODO srediti non friendly urls

			$sm = vivvo_lite_site::get_instance();

			if (!$force && (defined('VIVVO_ADMIN_MODE') || defined('VIVVO_SKIP_URL_PARSING'))){
				return;
			}

			if (preg_match('/[a-zA-Z0-9_]*\.php/', $this->_url_array[0])){
				return;
			}elseif (strtolower($this->_url_array[0])=='404.html'){
				$this->list['404'] = true;
			}elseif ($sm->is_registered_url($this->_url_array[0] . $this->list['output_type'])){
				$res = $sm->call_url_handler($this->_url_array[0] . $this->list['output_type'], $this->_url_array);
				if ($res === false){
					go_404();
				}else{
					if (is_array($res) && !empty($res)){
						$this->list = array_merge($this->list, $res);
					}
				}
				return;
			}elseif ($sm->is_registered_url($this->_url_array[0])){
				$res = $sm->call_url_handler($this->_url_array[0], $this->_url_array);
				if ($res === false){
					go_404();
				}else{
					if (is_array($res) && !empty($res)){
						$this->list = array_merge($this->list, $res);
					}
				}
				return;
			// Plugin URL
			}elseif (strtolower($this->_url_array[0]) == 'app'){
				if (isset($this->_url_array[1])){
					$this->list['plugin'] = $this->_url_array[1];
					$pm = $sm->get_plugin_manager();
					$plagin_name = $this->_url_array[1];
					array_shift($this->_url_array);
					array_shift($this->_url_array);
					$parsed_url = $pm->url_handler($plagin_name, $this->_url_array);
					if (is_array($parsed_url) && !empty ($parsed_url)){
						foreach ($parsed_url as $k => $v) $this->list[$k] = $v;
					}
				}
			}
		}

		function parse_lang(){
			if (!empty($this->_url_array) && strpos($this->_url_array[count($this->_url_array) - 1], '.') !== false){
				if(preg_match('/[\w\-]+\.([a-z]{2})\.[\w]{3,4}/i',$this->_url_array[count($this->_url_array) - 1], $temp)){
					$this->list[$this->parse_request('ln')] = strtolower($temp[1]);
				}
			}
		}

		function parse_type(){
			if (!isset($this->list['output_type'])){
				if (!empty($this->_url_array) && strpos($this->_url_array[count($this->_url_array) - 1], '.') !== false){
					if(preg_match('/[\w\-]+\.([\w]{3,4})$/i',$this->_url_array[count($this->_url_array) - 1], $temp)){
						$this->list['output_type'] = strtolower($temp[1]);
					}
				}
			}
		}

		/**
		 * Constructor
		 *
		 * @param	vivvo_site (deprecated) $site_manager
		 * @return	vivvo_url_manager
		 */
		function vivvo_url_manager($site_manager = null) {

			if (isset($_REQUEST['search_options'])){
				$search_options = unserialize(base64_decode(strtr($_REQUEST['search_options'], '-_', '+/')));
				if (is_array($search_options) && !empty($search_options)){
					foreach($search_options as $k => $v){
						if ($k != 'output_type'){
							$_REQUEST[$k] = $v;
						}
					}
				}
			}

			foreach($_REQUEST as $k => $v) $this->set_param($this->parse_request($k), $v);

			$REQUEST_URI = rtrim($_SERVER['REQUEST_URI'],'/');
			$REQUEST_URI = preg_replace('/\?.*$/','', $REQUEST_URI);

			if (defined('VIVVO_SYSTEM_PROXY_URL')){
				$temp = preg_replace('/http[s]?:\/\/[^\/]+/i', '', VIVVO_SYSTEM_PROXY_URL);
				$temp = rtrim($temp,'/');

				$REQUEST_URI = preg_replace('/^' .  preg_quote($temp, '/') . '/', '', $REQUEST_URI);
			}

			$temp = preg_replace('/http[s]?:\/\/[^\/]+/i', '',VIVVO_URL);
			$temp = rtrim($temp,'/');

			$chunk = preg_replace('/^' .  preg_quote($temp, '/') . '/', '', $REQUEST_URI);
			$chunk = str_replace(' ','-',$chunk);
			$chunk = str_replace('%20','-',$chunk);
			$chunk = ltrim($chunk,'/');

			if ($chunk != ''){
				$this->_url_array = explode('/',$chunk);
			}else{
				$this->_url_array = array();
			}
			$this->parse_lang();
			$this->parse_type();
		}
	}

	/**
	 * Shows 404 page.
	 *
	 * @param	string	$url	URL to be reported as Not Found.
	 */
	function go_404($url = null) {

		$url or $url = CURRENT_URL;

		header('Content-Type: text/html; charset=UTF-8', true, 404);

		$sm = vivvo_lite_site::get_instance();
		$sm->set_template_dir();
		$sm->set_theme();
		$sm->set_lang();

		$template = $sm->get_template();
		$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/404.tpl');
		$template->assign('URL', $url);

		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
		echo $sm->get_output();

		exit;
	}

	/**
	 * Formats date acording to user preferences.
	 *
	 * @param string $in_date
	 * @param string|boolean $format
	 * @param bool|null $no_localization
	 * @return string
	 */
	function format_date($in_date = '', $format = false, $no_localization = null){
		$localize = true;
		if ($no_localization) {
			$localize = false;
			$format = $no_localization;
		}
		if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $in_date)){
			try {
				$dateTime = new DateTime($in_date);
			} catch (Exception $e) {
				$dateTime = false;
			}
			$_format = $format;
		}elseif (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $format)){
			try {
				$dateTime = new DateTime($format);
			} catch (Exception $e) {
				$dateTime = false;
			}
			$dateTime = new DateTime($format);
			$_format = $in_date;
		}elseif (is_numeric($format)){
			try {
				$dateTime = new DateTime("@$format");
			} catch (Exception $e) {
				$dateTime = false;
			}
		}

		if ($dateTime){
			try {
				$dateTimeZone = new DateTimeZone(VIVVO_GENERAL_TIME_ZONE_FORMAT);
			} catch (Exception $e) {
				$dateTimeZone = false;
			}

			if ($dateTimeZone){
				$dateTime->setTimezone($dateTimeZone);
 			}

			if ($dateTime){
				if ($_format === true){
					$date = $localize ? localized_date('Y-m-d H:i:s', $dateTime) : $dateTime->format('Y-m-d H:i:s');
				}elseif ($_format !== false){
					$date = $localize ? localized_date($_format, $dateTime) : $dateTime->format($_format);
				}else{
					$date = $localize ? localized_date(VIVVO_DATE_FORMAT, $dateTime) : $dateTime->format(VIVVO_DATE_FORMAT);
				}
			}
			return $date;
		}
		return '';
	}

	/**
	 * Formats date acording to user prefs
	 *
	 * @param integer $in_time unix timestamp
	 * @param boolean $iso
	 * @return string
	 */
	function format_time($in_time = 0, $iso = false){
		if ($iso){
			$date = localized_date('Y-m-d H:i O', $in_time);
		}else {
			try {
				$dateTime = new DateTime("@$in_time");
			} catch (Exception $e) {
				$dateTime = false;
			}
			try {
				$dateTimeZone = new DateTimeZone(VIVVO_GENERAL_TIME_ZONE_FORMAT);
			} catch (Exception $e) {
				$dateTimeZone = false;
			}
			if ($dateTime){
				if ($dateTimeZone){
					$dateTime->setTimezone($dateTimeZone);
				}
				$date = localized_date(VIVVO_DATE_FORMAT, $dateTime);

			}
		}
		return $date;
	}

	function localized_date($format, $time = null) {
		$format = preg_replace('/(?<!\\\\)[MF]/', '[[\\\\$0]]', $format);
		if ($time instanceof DateTime) {
			$date = $time->format($format);
			$n = $time->format('n');
		} else {
			if (!is_numeric($time)) {
				if (!strlen($time)) {
					$time = VIVVO_START_TIME;
				} else {
					$time = strtotime($time);
				}
			}
			$date = date($format, $time);
			$n = date('n', $time);
		}

		$lang = vivvo_lite_site::get_instance()->get_lang();
		$m = $lang->get_value("LNG_MONTH_SHORT_$n");
		$f = $lang->get_value("LNG_MONTH_$n");

		return str_replace(array('[[F]]', '[[M]]'), array($f, $m), $date);
	}


	function vivvo_property_exists($class, $property) {
		if (is_object($class)) $class = get_class($class);
		return array_key_exists($property, get_class_vars($class));
	}


	/**
	 * Makes $value secure for usage in SQL expressions
	 *
	 * @param	mixed	$value
	 * @return	string
	 */
	function secure_sql($value) {

		if (is_array($value)) {
			foreach ($value as $key => $val) {
				$value[$k] = secure_sql($val);
			}
		} else {
			$value = str_replace(
				array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'),
				array('&', '"', "'", '<', '>'),
				$value
			);

			if (get_magic_quotes_gpc()) {
				$value = stripslashes( $value );
			}

			if (function_exists('mysql_real_escape_string')) {
				$value = mysql_real_escape_string($value);
			} else {
				$value = addslashes($value);
			}
		}

		return $value;
	}

	/**
	 * Makes $value secure for usage in SQL IN (...) expression
	 *
	 * @param	mixed	$value		Array or comma-delimited string
	 * @param	bool	$numeric	Should values be casted to integers or not
	 * @return	string
	 */
	function secure_sql_in($value, $numeric = true) {

		is_array($value) or $value = array_map('trim', explode(',', $value));

		$result = array();

		foreach ($value as $item) {
			$result[] = $numeric ? intval($item) : "'$item'";
		}

		return implode(',', $result);
	}

	/**
	 * Escapes SQL LIKE wildcard characters in $value
	 *
	 * @param	string	$value
	 * @return	string
	 */
	function escape_sql_like($value) {
		return str_replace(array('%', '_'), array('\\%', '\\_'), $value);
	}

	function secure_file($value){
		$value = str_replace(array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), array('&', '"', "'", '<', '>'), $value);
		if( get_magic_quotes_gpc() ){
			$value = stripslashes( $value );
		}
		return $value;
	}

	/**
	 * Converts $value to MySQL DATE string format
	 *
	 * @param	mixed	$value		UNIX timestamp or strtotime-compatible string
	 * @return	string
	 */
	function to_mysql_date($value) {
		$valid = false;
		if (!is_numeric($value) && !($valid = preg_match('/^\d{4}(-\d{2}){2}$/', $value))) {
			$value = strtotime($value);
		}
		if (!$valid) {
			$value = date('Y-m-d', $value);
		}
		return $value;
	}

	/**
	 * Converts $value to MySQL DATETIME string format
	 *
	 * @param	mixed	$value		UNIX timestamp or strtotime-compatible string
	 * @return	string
	 */
	function to_mysql_datetime($value) {
		$valid = false;
		if (!is_numeric($value) && !($valid = preg_match('/^\d{4}(-\d{2}){2} \d{2}(:\d{2}){2}$/', $value))) {
			$value = strtotime($value);
		}
		if (!$valid) {
			$value = date('Y-m-d H:i:s', $value);
		}
		return $value;
	}

	function array_htmlspecialchars(&$item, $key) {
		if (is_string($item)){
			$item = htmlspecialchars($item, ENT_QUOTES, 'UTF-8');
		}
	}

	function admin_log($staff, $action) {

		if (!file_exists(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . 'logs')) {
			@mkdir(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_FILES_DIR . 'logs', 0777);
		}

		$log_file = VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . 'logs/' . date('Y') . '-' . date('m') . '.txt';

		if (!file_exists($log_file)) {
			$handle = @fopen($log_file, 'w');
			fclose($handle);
		}

		$handle = @fopen($log_file, 'r+');

		if ($handle) {
			$contents = file_get_contents($log_file);
			$contents = '"' . date('Y-m-d H:i:s') . '","' . $staff . '","' . $action . '"' . "\r\n" . $contents;
			fclose($handle);
		}

		$handle = @fopen($log_file, 'w');

		if ($handle) {
			fwrite($handle, $contents);
			fclose($handle);
		}
	}

	function array_item($key, $array) {
		return isset($array[$key]) ? $array[$key] : null;
	}

	function seems_utf8($Str) { # by bmorel at ssi dot fr
		$length = strlen($Str);
		for ($i=0; $i < $length; $i++) {
			if (ord($Str[$i]) < 0x80) continue; # 0bbbbbbb
			elseif ((ord($Str[$i]) & 0xE0) == 0xC0) $n=1; # 110bbbbb
			elseif ((ord($Str[$i]) & 0xF0) == 0xE0) $n=2; # 1110bbbb
			elseif ((ord($Str[$i]) & 0xF8) == 0xF0) $n=3; # 11110bbb
			elseif ((ord($Str[$i]) & 0xFC) == 0xF8) $n=4; # 111110bb
			elseif ((ord($Str[$i]) & 0xFE) == 0xFC) $n=5; # 1111110b
			else return false; # Does not match any model
			for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
				if ((++$i == $length) || ((ord($Str[$i]) & 0xC0) != 0x80))
					return false;
			}
		}
		return true;
	}

	function remove_accents($string) {
		if ( !preg_match('/[\x80-\xff]/', $string) )
			return $string;

		if (seems_utf8($string)) {
			$chars = array(
			// Decompositions for Latin-1 Supplement
			chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
			chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
			chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
			chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
			chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
			chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
			chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
			chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
			chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
			chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
			chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
			chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
			chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
			chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
			chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
			chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
			chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
			chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
			chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
			chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
			chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
			chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
			chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
			chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
			chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
			chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
			chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
			chr(195).chr(191) => 'y',
			// Decompositions for Latin Extended-A
			chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
			chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
			chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
			chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
			chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
			chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
			chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
			chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
			chr(196).chr(144) => 'Dj', chr(196).chr(145) => 'dj',
			chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
			chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
			chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
			chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
			chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
			chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
			chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
			chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
			chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
			chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
			chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
			chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
			chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
			chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
			chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
			chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
			chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
			chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
			chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
			chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
			chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
			chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
			chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
			chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
			chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
			chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
			chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
			chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
			chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
			chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
			chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
			chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
			chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
			chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
			chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
			chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
			chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
			chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
			chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
			chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
			chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
			chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
			chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
			chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
			chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
			chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
			chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
			chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
			chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
			chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
			chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
			chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
			chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
			chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
			chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
			// Euro Sign
			chr(226).chr(130).chr(172) => 'E',
			// GBP (Pound) Sign
			chr(194).chr(163) => '');

			$string = strtr($string, $chars);
		} else {
			// Assume ISO-8859-1 if not UTF-8
			$chars['in'] = chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
				.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
				.chr(195).chr(196).chr(197).chr(199).chr(200).chr(201).chr(202)
				.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
				.chr(211).chr(212).chr(213).chr(214).chr(216).chr(217).chr(218)
				.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
				.chr(228).chr(229).chr(231).chr(232).chr(233).chr(234).chr(235)
				.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
				.chr(244).chr(245).chr(246).chr(248).chr(249).chr(250).chr(251)
				.chr(252).chr(253).chr(255);

			$chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

			$string = strtr($string, $chars['in'], $chars['out']);
			$double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
			$double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
			$string = str_replace($double_chars['in'], $double_chars['out'], $string);
		}
		return $string;
	}
    

	function make_sefriendly($string) {
		$original_string = $string;
        
        $string = remove_accents(html_entity_decode($string,ENT_QUOTES,'UTF-8'));
		
        if (preg_match('/^[\x00-\x7f]*$/', $string)) {
			$string = preg_replace( '/[^a-z_0-9\_]/', '-', strtolower($string) );
		} else {
			if (function_exists('mb_strtolower')) {
                $string = mb_strtolower(html_entity_decode($original_string,ENT_QUOTES,'UTF-8'), 'UTF-8');
			}
			$string = preg_replace('/\s+|\!|\?|\^|\/|\:|\@|\#|\$|\%|\^|&|\*|\(|\)|\`|\~|\'/u', '-', $string);
		}
            //$string = preg_replace('/\s+|\!|\?|\^|\/|\:|\@|\#|\$|\%|\^|&|\*|\(|\)|\`|\~|\'/u', '-', $original_string); 
		return htmlspecialchars( trim( preg_replace('/(--+|\/)/u', '-', $string), '-' ),ENT_QUOTES, 'UTF-8');
	}

	/**
	 * Checks if the passed string would match the given shell wildcard pattern.
	 * 	- Not available on non-posix systems
	 * 	- since 5.3.0 on Windows
	 * @param string   The shell wildcard pattern
	 * @param string   The tested string
	 * @return bool    True if filename against a pattern matched
	 * @see http://php.net/fnmatch
	 */
	if (!function_exists('fnmatch')) {
		function fnmatch($pattern, $string) {
			$pattern = trim($pattern);
			$string  = trim($string);
			return (bool) preg_match('/^' . strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.')) . '$/i', $string);
		}
	}

	/**
	 * Checks if IP address is in a list of allowed IP addresses.
	 *
	 * @param string  IP address
	 * @return bool
	 */
	function in_allowed_ip_adresses($ip) {
		$addresses = VIVVO_ALLOWED_IP_ADDRESSES;
		if (empty($addresses)) {
			return true;
		}

		$addresses = explode(',', $addresses);
		foreach ($addresses as $addr) {
			if (fnmatch($addr, $ip)) {
				$allowed = true;
				break;
			}
			$allowed = false;
		}
		return $allowed;
	}

	/**
	 * Makes absolute URL out of relative one
	 *
	 * @param	string	$url
	 * @param	bool	$prepend_proxy
	 * @return	string
	 */
	function make_absolute_url($url, $prepend_proxy = true) {

		if ($prepend_proxy) {
			$url = make_proxied_url($url);
		}

		if (!preg_match('/^[^:\/\.\?]+:/', $url)) {
			$url = VIVVO_URL . $url;
		}

		return $url;
	}

	/**
	 * Prepends proxy in front of URL
	 *
	 * @param	string	$url
	 * @return	string
	 */
	function make_proxied_url($url) {

		if (!preg_match('/^[^:\/\.\?]+:/', $url)) {

			if (defined('VIVVO_PROXY_URL_OVERWRITE')) {
				$proxy = VIVVO_PROXY_URL_OVERWRITE;
			} elseif (defined('VIVVO_PROXY_URL')) {
				$proxy = VIVVO_PROXY_URL;
			}

			if (!empty($proxy) and substr($url, 0, strlen($proxy)) != $proxy) {
				return $proxy . $url;
			}
		}

		return $url;
	}

	/**
	 * Encodes $value in JSON-format and escapes all XML entities so it can be safely used in XML attributes
	 *
	 * @param	mixed	$value
	 * @return	string
	 */
	function json_encode_attr($value) {
		return strtr(json_encode($value), array(
			'"' => '&quot;',
			'&' => '&amp;',
			'<' => '&lt;',
			'>' => '&gt;'
		));
	}

#EOF