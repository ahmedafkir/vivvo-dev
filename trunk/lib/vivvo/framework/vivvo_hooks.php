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
 * @package    Vivvo
 * @subpackage framework
 * @author     Ivan Dilber <idilber@spoonlabs.com>
 * @copyright  Spoonlabs
 * @license    http://www.vivvo.net/license.php
 * @category   CMS
 */


	/**
	 *  Hooks Manager class
	 *
	 * @author	Ivan Dilber <idilber@spoonlabs.com>
	 */
	class vivvo_hooks_manager {

		/**
		 * List of all registered hooks
		 * Each callback can be accessed as $hooks[<hook_name>][<priority>][<callback_hash]
		 * and for each callback we keep: key, priority, filename, function, class, enabled
		 *
		 * @var	array $hooks
		 */
		private static $hooks;

		/**
		 * List of all callbacks registered, used for quick lookup
		 * Each entry is in the form $callbacks[<callback_hash>][<hook_name>] = <priority>
		 *
		 * @var array $callbacks
		 */
		private static $callbacks;


		/**
		 * Pririty of current callback that is being processed
		 *
		 * @var integer $current_priority
		 */
		private static $current_priority;

		/**
		 * Switch used to control the return value od call() method, it's
		 * used to indicate whether default action in main code should be
		 * skipped after hook call or not
		 *
		 * @var boolean $do_default
		 */
		private static $do_default = true;

		/**
		 * Value is used to stop bubbling of calls on given level. Handler
		 * will be called only if it's priority is higher than this value
		 *
		 * @var integer $propagate_limit
		 */
		private static $propagate_limit = 0;

		/**
		 * Used to pass some value to main code (used in conjuction with $do_default)
		 *
		 * @var mixed $return_status
		 */
		private static $return_status = true;


		/**
		 * Loads a list of hooks from configuration table
		 *
		 * @return void
		 */
		public static function load() {

			self::$hooks = array();
			self::$callbacks = array();

			$sm = vivvo_lite_site::get_instance();

			$configuration = $sm->get_configuration();
			$config = $configuration->get_configuration_property_list('vivvo_hooks');

			foreach($config as $hookname => $handlers) {
				foreach($handlers as $key=>$val) {
					if( $callback = unserialize($val) and is_array($callback) ) {

						$key = $callback['key'];
						$priority = $callback['priority'];

						if( !is_array(self::$hooks[$hookname]) )
							self::$hooks[$hookname] = array();

						if( !is_array(self::$hooks[$hookname][ $priority ]) )
							self::$hooks[$hookname][$priority] = array();

						self::$hooks[$hookname][$priority][$key] = $callback;

						if( !is_array(self::$callbacks[$key]) )
							self::$callbacks[$key] = array();

						self::$callbacks[$key][$hookname] = $priority;
					}
				}
			}
		}

		/**
		 * Check if load() was called
		 *
		 * @return boolean
		 */
		public static function is_loaded() {

			return is_array(self::$hooks);
		}

		/**
		 * Set value of $return_status variable
		 *
		 * @param mixed $status
		 * @return void
		 */
		public static function set_status($status) {

			self::$return_status = $status;
		}

		/**
		 * Get value of $return_status variable
		 *
		 * @return mixed
		 */
		public static function get_status() {

			return self::$return_status;
		}

		/**
		 * Generate unique key that is used to identify each callback
		 *
		 * @param string $callback_filename - Filename of the script, relative to Vivvo installation path
		 * @param string $callback_function - Name of the callback function or method
		 * @param string $callback_class - Name of the class (optional)
		 * @return string
		 */
		private static function make_callback_key($callback_filename, $callback_function, $callback_class='') {

			if( empty($callback_class) )
				$callback_class = '';

			return md5($callback_filename . $callback_function . $callback_class);
		}

		/**
		 * Get the callback data, if any registered for given hook. Otherwise return false.
		 *
		 * @param string $hook_name - Name of the hook
		 * @param string $callback_filename - Filename of the script, relative to Vivvo installation path
		 * @param string $callback_function - Name of the callback function or method
		 * @param string $callback_class - Name of the class (optional)
		 * @return mixed
		 */
		public static function get_callback($hook_name, $callback_filename, $callback_function, $callback_class='') {

			$key = self::make_callback_key($callback_filename, $callback_function, $callback_class);

			if( !isset(self::$callbacks[$key]) or !isset(self::$callbacks[$key][$hook_name]) )
				return false;

			$priority =  self::$callbacks[$key][$hook_name];

			return self::$hooks[$hook_name][$priority][$key];
		}

		/**
		 * Register callback
		 *
		 * @param string $hook_name - Name of the hook
		 * @param integer $priority = Priority of callback (any positive number). Higher priorities are executed first
		 * @param string $callback_filename - Filename of the script, relative to Vivvo installation path
		 * @param string $callback_function - Name of the callback function or method
		 * @param string $callback_class - Name of the class (optional)
		 * @param boolean $enabled - You can enable/disable callback initiall with this parameter
		 * @return boolean
		 */
		public static function register($hook_name, $priority, $callback_filename, $callback_function, $callback_class = '', $enabled = true) {

			if( self::get_callback($hook_name, $callback_filename, $callback_function, $callback_class) )
				return false;

			$sm = vivvo_lite_site::get_instance();

			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');

			$key = self::make_callback_key($callback_filename, $callback_function, $callback_class);

			$data = array(
				'key'		=> $key,
				'priority'	=> $priority,
				'filename'	=> $callback_filename,
				'function'	=> $callback_function,
				'class'		=> $callback_class,
				'enabled'	=> $enabled,
			);

			$config =& new preferences($sm);
			$config->set_variable_name($hook_name);
			$config->set_variable_property($key);
			$config->set_variable_value( serialize($data) );
			$config->set_module('vivvo_hooks');
			$config->set_domain_id(1);

			$pm =& new vivvo_post_master($sm);
			$pm->set_data_object($config);
			$pm->sql_insert();

			return true;
		}

		/**
		 * Unregister callback
		 *
		 * @param string $hook_name - Name of the hook
		 * @param string $callback_filename - Filename of the script, relative to Vivvo installation path
		 * @param string $callback_function - Name of the callback function or method
		 * @param string $callback_class - Name of the class (optional)
		 * @return boolean
		 */
		public static function unregister($hook_name, $callback_filename, $callback_function, $callback_class='') {

			$sm = vivvo_lite_site::get_instance();

			if( !self::get_callback($hook_name, $callback_filename, $callback_function, $callback_class) )
				return false;

			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');

			$key = self::make_callback_key($callback_filename, $callback_function, $callback_class);

			$config_list =& new preferences_list($sm);
			if ($config_list->get_preference_by_all_variable($hook_name, $key, '', 'vivvo_hooks', 1)){

				$pm =& new vivvo_post_master($sm);
				$config_list->sql_delete_list($pm);

				return true;
			}

			return false;
		}

		/**
		 * Enable callback
		 * Callback is enabled just during this script call, it's not saved in the database
		 *
		 * @param string $hook_name - Name of the hook
		 * @param string $callback_filename - Filename of the script, relative to Vivvo installation path
		 * @param string $callback_function - Name of the callback function or method
		 * @param string $callback_class - Name of the class (optional)
		 * @return boolean
		 */
		public static function enable($hook_name, $callback_filename, $callback_function, $callback_class='') {

			if( $callback = self::get_callback($hook_name, $callback_filename, $callback_function, $callback_class) ) {
				$key = $callback['key'];
				$priority = $callback['priority'];

				self::$hooks[$hook_name][$priority][$key]['enabled'] = true;
				return true;
			}

			return false;
		}

		/**
		 * Disable callback
		 * Callback is disabled just during this script call, it's not saved in the database
		 *
		 * @param string $hook_name - Name of the hook
		 * @param string $callback_filename - Filename of the script, relative to Vivvo installation path
		 * @param string $callback_function - Name of the callback function or method
		 * @param string $callback_class - Name of the class (optional)
		 * @return boolean
		 */
		public static function disable($hook_name, $callback_filename, $callback_function, $callback_class='') {

			if( $callback = self::get_callback($hook_name, $callback_filename, $callback_function, $callback_class) ) {
				$key = $callback['key'];
				$priority = $callback['priority'];

				self::$hooks[$hook_name][$priority][$key]['enabled'] = false;
			}

			return false;
		}

		/**
		 * List all callbacks registered to handle given hook. If priority is given then only callbacks with
		 * that priority will be listed
		 *
		 * @param string $hook_name - Name of the hook
		 * @param integer $priority - Priority (optional)
		 * @return array
		 */
		public static function list_callbacks($hook_name, $priority=0) {

			if( empty(self::$hooks[$hook_name]) )
					return array();

			$out = array();

			if($priority) {
				if( empty(self::$hooks[$hook_name][$priority]) )
					return array();

				foreach(self::$hooks[$hook_name][$priority] as $val) {

					$out[] = $val;
				}

			} else {
				foreach(self::$hooks[$hook_name] as $pri => $v) {

					foreach(self::$hooks[$hook_name][$pri] as $val)
						$out[] = $val;
				}
			}

			return $out;
		}

		/**
		 * Call all callbacks registered to handle the given hook. Callbacks are called
		 * one by one, higher priorities first.
		 *
		 * @param string $hook_name - Name of the hook
		 * @param mixed $params - Parameters that are passed to callback function
		 * @return boolean
		 */
		public static function call($hook_name, $params=false) {

			if( empty(self::$hooks) or empty(self::$hooks[$hook_name]) )
				return true;

			//reset to defaults
			self::$do_default = true;
			self::$propagate_limit = 0;
			self::$return_status = true;
			self::$current_priority = 0;

			// get priorities, higher first
			$p = array_keys( self::$hooks[$hook_name]);
			rsort($p, SORT_NUMERIC);

			// process callbacks in order of priorities

			foreach($p as $priority) {

				self::$current_priority = $priority;

				if( !isset(self::$hooks[$hook_name][$priority]) ) //skip empty
					continue;

				foreach(self::$hooks[$hook_name][$priority] as $callback) {

					if( empty($callback) or empty($callback['enabled']) ) //skip disabled or not set
						continue;

					if($priority <= self::$propagate_limit) // stop further propagation
						break 2;

					// call handler function
					if( !file_exists(VIVVO_FS_INSTALL_ROOT . $callback['filename']) )
						continue;

					require_once(VIVVO_FS_INSTALL_ROOT . $callback['filename']);

					if( $callback['class'] ) {

						if(!method_exists ( $callback['class'], $callback['function'] ) )
							continue;

						$func = array($callback['class'], $callback['function']);

					} else {
						$func = $callback['function'];

						if(!function_exists($func) )
							continue;
					}
					// do the call
					call_user_func_array($func, array($hook_name, &$params) );
				}
			}

			return self::$do_default;
		}

		/**
		 * Get the priority of callback that is running at the moment
		 *
		 * @return integer
		 */
		public static function get_current_priority(){
			return (int) self::$current_priority;
		}

		/**
		 * Call this inside callback function to indicate that default action in main code
		 * shouldn't happen after hook call returns (applies just to some hooks, mainly in services)
		 *
		 * @return void
		 */
		public static function prevent_default() {
			self::$do_default = false;
		}

		/**
		 * Call this inside callback function to stop other registered callbacks from being called after
		 * this one. If priority is given than only callbacks of that priority or less will be skipped.
		 *
		 * @param integer $priority - Priority (optional)
		 * @return void
		 */
		public static function stop_propagation($priority=0) {
			self::$propagate_limit = $priority? $priority : self::get_current_priority();
		}

	} //end_class vivvo_hooks_manager

#EOF