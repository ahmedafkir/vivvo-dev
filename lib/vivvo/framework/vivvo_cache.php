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
 * Path to a folder where cache drivers resides.
 */
define('VIVVO_FS_CACHE_DRIVER', dirname(__FILE__) . '/cache_driver/');

/**
 * Default internal cache driver. This value can be set in <root>/conf.php
 */
defined('VIVVO_SYSTEM_CACHE_DRIVER') or define('VIVVO_SYSTEM_CACHE_DRIVER', 'Cache_Lite');

/**
 * Interface for cache drivers.
 *
 * @copyright  SpoonLabs
 * @package    vivvo_framework
 * @subpackage framework
 * @version    $Revision: 5385 $
 * @category   interface
 * @author     Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @author     Mihail Binev <mbinev@spoonlabs.com>
 */
interface vivvo_cache_driver {
	public static function is_supported();
	public function __construct(array $params);
	public function put($id, $value, array $tags = null, $lifetime = null);
	public function exists($id);
	public function get($id);
	public function find($tag);
	public function extend_lifetime($id, $lifetime);
	public function delete($id);
	public function delete_by_cache_id(array $ids);
	public function delete_by_tag($tag, $return_ids = false);
	public function delete_all();
	public function cleanup();
}

/**
 * vivvo_cache class.
 *
 * @copyright  SpoonLabs <http://www.spoonlabs.com>
 * @package    vivvo_framework
 * @subpackage framework
 * @version    $Revision: 5385 $
 * @category   CMS
 * @author     Mihail Binev <mbinev@spoonlabs.com>
 * @author     Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 */
class vivvo_cache {

	/**
	 * Configuration array for vivvo_cache drivers.
	 * @var array
	 */
	private static $conf = null;

	/**
	 * List of instantiated cache classes (for singleton implementation).
	 * @var array
	 */
	private static $drivers = array();

	/**
	 * Constructor creates cache driver by name. Parameters for
	 * the specific driver may be deployed by the second argument.
	 *
	 * @param string|false     The name of the driver
	 * @return void
	 *
	 * TODO normalize case-sensitive names
	 */
	private function __construct($name = null, array $params = null) {

		// load configuration
		if (self::$conf === null) {

			// default configuration
			self::$conf = array(
				'Cache_Lite' => array(
					'cacheDir' => VIVVO_FS_INSTALL_ROOT . 'cache/',
					'lifeTime' => null,
					'automaticSerialization' => true,
					'errorHandlingAPIBreak' => true,
					'group' => 'vivvo_cache'
				),
				'SQLite' => array(
					'file' => VIVVO_FS_INSTALL_ROOT . 'cache/sqlite.db',
					'schema' => 'CREATE TABLE cache(
						id VARCHAR(100) PRIMARY KEY,
						tags VARCHAR(255),
						lifetime INTEGER,
						data TEXT);'
				),
				'MySQL' => array(
					'host' => VIVVO_DB_HOST,
					'username' => VIVVO_DB_USER,
					'password' => VIVVO_DB_PASSWORD,
					'database' => VIVVO_DB_DATABASE,
					'data_table' => VIVVO_DB_PREFIX . 'cache',
					'tags_table' => VIVVO_DB_PREFIX . 'cache_tags'
				)
			);

			if (defined('VIVVO_SYSTEM_CACHE_DRIVER_PARAMS')) {
				self::$conf = array_merge(self::$conf, json_decode(VIVVO_SYSTEM_CACHE_DRIVER_PARAMS, true));
			}
		}

		$name = ($name === null) ? VIVVO_SYSTEM_CACHE_DRIVER : trim($name);
		$driver = 'vivvo_cache_' . $name;
		$file = VIVVO_FS_CACHE_DRIVER . $name . '.php';

		if (!file_exists($file)) {
			throw new CacheDriverException($driver, 'Driver file could not be found.');
		}

		require_once $file;

		// Passed params overwrites those in conf.cache.php since they are on-demand
		$params = ($params !== null) ? array_merge(self::$conf[$name], $params) : self::$conf[$name];

		is_array($params) or $params = array();

		// Create our cache driver
		self::$drivers[$name] = new $driver($params);

		if (!self::$drivers[$name] instanceof vivvo_cache_driver) {
			throw new CacheDriverException($driver, 'Driver class does not implement vivvo_cache_driver interface.');
		}
	}

	/**
	 * Checks if specific driver is supported by runtime environment
	 *
	 * @param	string	$driver
	 * @return	bool
	 */
	public static function is_supported($driver) {

		$file = VIVVO_FS_CACHE_DRIVER . $driver . '.php';
		$driver = 'vivvo_cache_' . $driver;

		if (!file_exists($file)) {
			return false;
		}

		require_once $file;

		return call_user_func(array($driver, 'is_supported'));
	}

	/**
	 * Returns a singleton instance of vivvo_cache driver by name.
	 *
	 * Additional parameters can be passed by the second argument.
	 *
	 * @param 	string       Name if the driver, or empty for default cache driver
	 * @param 	array        (optional) Configuration parameters for the driver
	 * @return 	vivvo_cache  Instance of cache driver
	 */
	public static function &get_instance($name = null, array $params = null) {

		empty($name) and $name = VIVVO_SYSTEM_CACHE_DRIVER;

		if (empty(self::$drivers[$name])) {
			new self($name, $params);
		}

		return self::$drivers[$name];
	}

	/**
	 * Returns all driver instances.
	 *
	 * @return array     An array of all instantiated cache drivers
	 */
	public static function get_drivers() {
		return self::$drivers;
	}
}

/**
 * CacheDriverException class, thrown by cache driver classes
 *
 * @copyright  SpoonLabs <http://www.spoonlabs.com>
 * @package    vivvo_framework
 * @subpackage framework
 * @version    $Revision: 5385 $
 * @category   CMS
 * @author     Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 */
class CacheDriverException extends Exception {

	/**
	 * Creates new exception object
	 *
	 * @param	string	$driver
	 * @param	string	$message
	 * @param	int		$code
	 */
	public function __construct($driver, $message, $code = 0) {
		parent::__construct("[$driver] $message", $code);
	}
}

#EOF