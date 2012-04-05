<?php
/* =============================================================================
 * $Revision: 5174 $
 * $Date: 2010-04-27 14:56:01 +0200 (Tue, 27 Apr 2010) $
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
 * Required for setting/checking cache validity
 */
defined('VIVVO_START_TIME') or define('VIVVO_START_TIME', time());

/**
 * In case partial/full page caching is used this will not be defined when we are initializing driver
 */
defined('VIVVO_FS_FRAMEWORK') or define('VIVVO_FS_FRAMEWORK', rtrim(realpath(dirname(__FILE__) . '/../'), '/') . '/');

/**
 * vivvo_cache_Cache_Lite class
 *
 * @copyright  SpoonLabs <http://www.spoonlabs.com>
 * @package    vivvo_framework
 * @subpackage cache_driver
 * @category   Driver
 * @version    $Revision: 5174 $
 * @author     Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @author     Mihail Binev <mbinev@spoonlabs.com>
 *
 * @since 4.1.2
 * @since 4.5	Added support for $lifetime parameter
 *
 * TODO: add support for tags?
 */
class vivvo_cache_Cache_Lite implements vivvo_cache_driver {

	/**
	 * @var	Cache_Lite
	 */
	private $cache_lite;

	/**
	 * Default lifetime period
	 * @var	int
	 */
	private $lifetime = 300;

	/**
	 * Checks if this driver is supported by runtime environment
	 *
	 * @return bool
	 */
	public static function is_supported() {
		return true;
	}

	/**
	 * Class constructor.
	 *
	 * @param	array	$params
	 */
	public function __construct(array $params) {

		require_once(VIVVO_FS_FRAMEWORK . 'PEAR/Lite.php');

		$this->cache_lite = new Cache_Lite($params);

		if (!empty($params['lifetime'])) {
			$this->lifetime = $params['lifetime'];
		}

	}

	/**
	 * Stores data into cache
	 *
	 * @param	string	$id
	 * @param	mixed	$data
	 * @param	array	(optional) $tags
	 * @param	int		(optional) $lifetime
	 * @return	bool
	 */
	public function put($id, $data, array $tags = null, $lifetime = null) {

		if (($lifetime = (int)$lifetime) <= 0) {
			$lifetime = null;
		} elseif ($lifetime <= 2592000) {
			$lifetime += VIVVO_START_TIME - 1;
		} else {
			$lifetime -= 1;
		}

		$this->cache_lite->setLifeTime($lifetime);

		return $this->cache_lite->save($data, md5($id), 'vivvo_cache') === true;
	}

	/**
	 * Checks if cache contains data with specified id
	 *
	 * @param	string	$id
	 * @return	bool
	 */
	public function exists($id) {
		return $this->cache_lite->get(md5($id), 'vivvo_cache') !== false;
	}

	/**
	 * Returns cached data by it's id, if there is no cached data with such id false is returned
	 *
	 * @param	string	$id
	 * @return	mixed
	 */
	public function get($id) {
		return $this->cache_lite->get(md5($id), 'vivvo_cache');
	}

	/**
	 * Returns all items with specified tag
	 *
	 * Note: tags are not implemented!
	 *
	 * @param	string	$tag
	 * @return	array
	 */
	public function find($tag) {
		return array();
	}

	/**
	 * Extends lifetime of cached item
	 *
	 * Note: when $lfietime is between 1 and 2592000 (inclusive) that value will
	 *       be added to default lifetime period set when driver is initialized.
	 *
	 * @param	string	$id
	 * @param	int		$lifetime
	 * @return	bool
	 */
	public function extend_lifetime($id, $lifetime) {

		$value = $this->get($id);

		if ($value !== false) {

			if (($lifetime = (int)$lifetime) > 0 and $lifetime <= 2592000) {
				$lifetime = $this->lifetime + $lifetime;
			}

			return $this->put($id, $value, null, $lifetime);
		}

		return false;
	}

	/**
	 * Removes an item from cache
	 *
	 * @param	string	$id
	 * @return	bool
	 */
	public function delete($id) {
		return $this->cache_lite->remove(md5($id), 'vivvo_cache') === true;
	}

	/**
	 * Removes all items from cache with specified cache ids (cache id = md5(id))
	 *
	 * @param	array	$ids
	 * @return	bool
	 */
	public function delete_by_cache_id(array $ids) {

		if (empty($ids)) {
			return true;
		}

		foreach ($ids as $id) {
			if ($this->cache_lite->remove($id, 'vivvo_cache') !== true) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Removes all items from cache with matching tag
	 *
	 * Note: tags are not implemented!
	 *
	 * @param	string		$tag
	 * @param	bool		(optional) $return_ids
	 * @return	bool|array
	 */
	public function delete_by_tag($tag, $return_ids = false) {
		return $return_ids ? array() : false;
	}

	/**
	 * Removes all items from cache
	 *
	 * @return	bool
	 */
	public function delete_all() {
		return $this->cache_lite->clean('vivvo_cache') === true;
	}

	/**
	 * Performs data store cleanup (removes all expired items)
	 *
	 * @return	bool
	 */
	public function cleanup() {
		return $this->cache_lite->clean('vivvo_cache', 'old') === true;
	}
}
?>