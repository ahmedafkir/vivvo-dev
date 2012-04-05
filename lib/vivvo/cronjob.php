<?php
/* =============================================================================
 * $Revision: 5362 $
 * $Date: 2010-05-20 17:26:42 +0200 (Thu, 20 May 2010) $
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
	 * Vivvo CMS cronjob script
	 *
	 * @copyright	Spoonlabs
	 * @version		$Revision: 5362 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */

	if (PHP_SAPI != 'cli') {
		cronjob_error('This script must be run from command line.');
	}

	$defaultOptions = array(
		'task' => false,
		'touch' => false,
		'list' => false,
		'plugin' => null,
		'help' => null
	);

	$argv = $_SERVER['argv'];
	$argc = $_SERVER['argc'];

	if ($argc < 2) {
		cronjob_error('ERROR: Insufficient arguments. Type --help for usage info.');
	}

	define('VIVVO_CRONJOB_MODE', true);

	require_once dirname(dirname(dirname(__FILE__))) . '/conf.php';

	$options = parseOptions($argc, $argv, $defaultOptions);

	if ($options['help']) {
		help();
		exit;
	}

	if ($options['task']) {
		action_task($options);
	} elseif ($options['list']) {
		action_list($options);
	} elseif ($options['touch']) {
		action_touch($options);
	} else {
		cronjob_error('ERROR: Required arguments missing. Type --help for usage info.');
	}

	/**
	 * Handles cronjob.php --task=<task_name> [--plugin=<plugin_name>]
	 */
	function action_task($options) {

		if ($options['plugin']) {
			if (!is_dir($filename = VIVVO_FS_PLUGIN_ROOT . 'plugins/' . $options['plugin'])) {
				cronjob_error("ERROR: Plugin '$options[plugin]' could not be found.");
			}
			if (!file_exists($filename .= '/tasks/' . $options['task'] . '.php')) {
				cronjob_error("ERROR: Task '$options[plugin]::$options[task]' could not be found.");
			}
		} elseif (!file_exists($filename = VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/tasks/' . $options['task'] . '.php')) {
			cronjob_error("ERROR: Task '$options[task]' could not be found.");
		}

		include $filename;

		if (!function_exists($options['task'])) {
			cronjob_error("ERROR: Task '$options[task]' could not be found.");
		}

		require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php';

		error_reporting(E_ERROR);

		define('VIVVO_SKIP_URL_PARSING', true);

		$args = array(vivvo_lite_site::get_instance());

		call_user_func_array($options['task'], $args);
	}

	/**
	 * Handles cronjob.php --list [--plugin=<plugin_name>]
	 */
	function action_list($options) {

		if ($options['plugin']) {
			if (!is_dir($directory = VIVVO_FS_PLUGIN_ROOT . 'plugins/' . $options['plugin'] . '/')) {
				cronjob_error("ERROR: Plugin '$options[plugin]' could not be found.");
			}
			if (!is_dir($directory .= 'tasks/')) {
				cronjob_error("No tasks found for '$options[plugin]' plugin.");
			}
		} else {
			$directory = VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/tasks/';
		}

		if (($dir = opendir($directory))) {

			$list = array();

			while (($file = readdir($dir)) !== false) {
				if (strtolower(substr($file, -4)) == '.php') {

					$info = '';
					$task = substr($file, 0, -4);

					@include_once($directory . $file);

					if (function_exists($task)) {
						if ($info) {
							$list[] = '  ' . $task . ' - ' . $info;
						} else {
							$list[] = '  ' . $task;
						}
					}
				}
			}

			closedir($dir);

			if (empty($list)) {
				echo 'No tasks found.' . PHP_EOL;
			} else {
				echo 'Available tasks' . ($options['plugin'] ? ' (' . $options['plugin'] . ')' : '') . ':';
				echo PHP_EOL . implode(PHP_EOL, $list) . PHP_EOL;
			}

		} else {
			cronjob_error("ERROR: Could not access '$directory' directory.");
		}
	}

	/**
	 * Handles cronjob.php --touch
	 */
	function action_touch($options) {

		defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time());

		$cron_flag = VIVVO_FS_ROOT . 'cache/.cron.lock';

		// call cron only if it's been at least a minute since the last
		if (file_exists($cron_flag) and VIVVO_START_TIME - filemtime($cron_flag) < 60) {
			cronjob_error('Cron manager has already been called less than a minute ago.');
		}

		// reset flag
		@unlink($cron_flag);
		touch($cron_flag);

		require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php';
		require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_cron.php';

		error_reporting(E_ERROR);

		define('VIVVO_SKIP_URL_PARSING', true);

		$sm = vivvo_lite_site::get_instance();
		$cm = new vivvo_cron_manager($sm);
		$cm->execute();

		echo 'Cron manager executed successfully.' . PHP_EOL;
	}

	/**
	 * Handles cronjob.php --help (diplays usage info)
	 */
	function help() {

		global $X_VERSION, $X_REVISION;	// for development version support

		require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/vivvo_lite_site.php';

		echo 'Vivvo CMS ' . VIVVO_VERSION . ' (build ' . VIVVO_CODE_REVISION . ') cronjob script' . PHP_EOL . PHP_EOL .
			 'usage: 1. cronjob.php --task=<task_name> [--plugin=<plugin_name>]' . PHP_EOL .
			 '       2. cronjob.php --list [--plugin=<plugin_name>]' . PHP_EOL .
			 '       3. cronjob.php --touch' . PHP_EOL .
			 '       4. cronjob.php --help' . PHP_EOL . PHP_EOL .
			 '  1. Execute specific task, if optional --plugin parameter is passed' . PHP_EOL .
			 '     task will be loaded from that plugin\'s directory.' . PHP_EOL . PHP_EOL .
			 '  2. Displays list of all available tasks, if optional --plugin' . PHP_EOL .
			 '     parameter is passed that plugin\'s tasks are listed.' . PHP_EOL . PHP_EOL .
			 '  3. Run internal cron manager which will load tasks from `cron` table' . PHP_EOL .
			 '     and execute those which should be runned according to cron_mask set.' . PHP_EOL . PHP_EOL .
             '  4. Display this text.' . PHP_EOL . PHP_EOL .
			 'example: php ./cronjob.php --task=auto_backup' . PHP_EOL . PHP_EOL;
	}

	function parseOptions($num_options, $options, $defaultOptions) {

		$final_options = array();

		foreach ($options as $key => $option_candidate) {

			if ($key == 0) {
				continue;
			}

			$parts = explode('=', $option_candidate, 2);
			$option_candidate_raw = ltrim($parts[0], '-');

			if (count($parts) != 2) {
				if (array_key_exists($option_candidate_raw, $defaultOptions)) {
					$option_candidate_value = true;
				} else {
					continue;
				}
			} else {
				$option_candidate_value = $parts[1];
			}

			if (!array_key_exists($option_candidate_raw, $defaultOptions)) {
				echo "WARNING: Unrecognised option $parts[0]." . PHP_EOL;
				continue;
			}

			if (in_array($option_candidate_value, array("true", "false"))) {
				$final_options[$option_candidate_raw] = $option_candidate_value == "true"  ? true : false;
			} else {
				$final_options[$option_candidate_raw] = $option_candidate_value;
			}
		}

		return array_merge($defaultOptions, $final_options);
	}

	function cronjob_error($msg) {
		echo $msg . PHP_EOL;
		exit;
	}

#EOF