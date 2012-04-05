<?php
/* =============================================================================
 * $Revision: 4856 $
 * $Date: 2010-04-01 14:57:16 +0200 (Thu, 01 Apr 2010) $
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
	 * @see	'lib/vivvo/framework/module.php'
	 */
	class_exists('module') or require VIVVO_FS_FRAMEWORK . 'module.class.php';

	/**
	 * UI Hooks module
	 *
	 * @version		$Revision: 4856 $
	 * @copyright	Spoonlabs
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class ui_hooks extends module {

		/**
		 * Registers UI hook
		 *
		 * @param	string	$name		Hook handler name (must be unique)
		 * @param	string	$hook		Name of the UI hook
		 * @param	array	$params		Hook params (passed to template)
		 * @param	array	$handler	(optional) Callback definition, invoked when hook should be applied
		 *
		 * @example
		 * <code>
		 *	# this will register hook for main navigation bar in admin panel, it provides callback to check for user privileges
		 *
		 *	function my_own_hook_callback($template, $params, &$hook) {
		 *	    # only display this menu item if user has privileges to manage our plugin
		 *	    $sm = vivvo_lite_site::get_instance();
		 *		return $sm->user && $sm->user->can('MANAGE_PLUGIN', 'my_own_plugin');
		 *  }
		 *
		 *  $params = array(
		 *  	'id' => 'my_own_plugin',
		 *  	'title' => 'My own plugin',
		 *  	'href' => 'plugins.php?plugin=my_own_plugin'
		 *  );
		 *
		 *  $handler = array(
		 *		'file' => substr(__FILE__, strlen(VIVVO_FS_INSTALL_ROOT) + 1),	# this file
		 *		'function' => 'my_own_hook_callback'
		 *	);
		 *
		 *  # let's hook to 'admin_main_nav' hook point:
		 *  ui_hooks::register('my_own_hook', 'admin_main_nav', $params, $handler);
		 * </code>
		 */
		public static function register($name, $hook, array $params, array $handler = array()) {

			$conf = vivvo_lite_site::get_instance()->get_configuration();

			$conf->add_conf($name, 'hook', $hook, 'ui_hooks');
			$conf->add_conf($name, 'params', serialize($params), 'ui_hooks');

			if (!empty($handler)) {
				$conf->add_conf($name, 'handler', serialize($handler), 'ui_hooks');
			}
		}

		/**
		 * Unregisters UI hook
		 *
		 * @param	string	$name	Hook handler name
		 */
		public static function unregister($name) {
			vivvo_lite_site::get_instance()->get_configuration()->remove_from_module('ui_hooks', $name);
		}

		/**
		 * @var	string	Template set to use
		 */
		public static $template_set = 'default';

		/**
		 * @var array	Array of registered hooks
		 */
		private static $hooks = false;

		/**
		 * Module constructor
		 *
		 * @param vivvo_site 	$site_manager
		 * @param array 		$params
		 * @param template		$parent_template
		 * @param bool 			$cache
		 * @param bool			$output
		 */
		public function __construct($site_manager, $params, $parent_template = NULL, $cache = true, $output = true) {

			if (self::$hooks === false) {

				self::$hooks = array();

				$hooks = $site_manager->get_configuration()->get_configuration_property_list('ui_hooks');

				foreach ($hooks as $name => $hook) {

					if (!isset(self::$hooks[$hook_name = $hook['hook']])) {
						self::$hooks[$hook_name] = array();
					}

					if (is_array($hook_params = @unserialize($hook['params']))) {

						$handler = false;

						if (is_array($handler = @unserialize($hook['handler'])) and !empty($handler['file']) and !empty($handler['function'])) {
							$handler['loaded'] = false;
							if (empty($handler['params'])) {
								$handler['params'] = array();
							}
						} else {
							$handler = false;
						}
					}

					$hook = array('name' => $name, 'params' => $hook_params);

					if ($handler) {
						$hook['handler'] = $handler;
					}

					self::$hooks[$hook_name][] = $hook;
				}
			}

			$this->module($site_manager, $params, $parent_template, $cache, $output);
		}

		/**
		 * Generates box output
		 *
		 * @param	array	$params		Parameters passed from VTE
		 */
		public function generate_output($params = array()) {

			if (empty($params['name']) or empty(self::$hooks[$params['name']])) {
				return;
			}

			!empty($params['template_set']) or $params['template_set'] = self::$template_set;

			if (empty($params['template_string'])) {

				$filename = VIVVO_FS_TEMPLATE_ROOT . "templates/_system/hooks/$params[template_set]/$params[name].xml";

				if (is_file($filename)) {
					$params['template_string'] = file_get_contents($filename);
				} else {
					return;
				}
			}

			if (!$this->_parent_template) {
				$this->_parent_template = vivvo_lite_site::get_instance()->get_template();
			}

			$output = '';

			foreach (self::$hooks[$params['name']] as &$hook) {

				$template = new template(vivvo_lite_site::get_instance(), $this->_parent_template);
				$template->set_string_template($params['template_string']);

				if (!empty($hook['handler'])) {

					if (!$hook['handler']['loaded']) {

						if (is_file(VIVVO_FS_INSTALL_ROOT . $hook['handler']['file'])) {
							include_once VIVVO_FS_INSTALL_ROOT . $hook['handler']['file'];
						}

						if (!empty($hook['handler']['class'])) {
							if (class_exists($hook['handler']['class']) and in_array($hook['handler']['function'], get_class_methods($hook['handler']['class']))) {
								$hook['handler']['function'] = array($hook['handler']['class'], $hook['handler']['function']);
								$hook['handler']['loaded'] = true;
							}
						} else if (function_exists($hook['handler']['function'])) {
							$hook['handler']['loaded'] = true;
						}
					}

					if ($hook['handler']['loaded']) {

						$ret = call_user_func_array($hook['handler']['function'], array(
							$template,
							$params,
							&$hook,	// byref
							$hook['handler']['params']
						));

						if ($ret !== true) {

							$output .= $ret;

							continue;
						}
					}
				}

				if (!empty($hook['params'])) {

					if (!empty($hook['params']['lang_file'])) {
						vivvo_lang::get_instance()->load_lang(
							defined('VIVVO_ADMIN_LANG') ? VIVVO_ADMIN_LANG : VIVVO_DEFAULT_LANG,
							$template->eval_string($hook['params']['lang_file'])
						);
					}

					foreach ($hook['params'] as $name => $value) {
						$value = $template->eval_string($value);
						$template->assign($name, $value);
					}

					$output .= $template->get_output();
				}
			}

			unset($hook);

			$this->set_template(array('template_string' => '<vte:value select="{output}" />'));
			$this->_template->assign('output', $output);
		}
	}
?>