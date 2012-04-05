<?php
/* =============================================================================
 * $Revision: 4824 $
 * $Date: 2010-03-29 17:03:03 +0200 (Mon, 29 Mar 2010) $
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
 * vivvo_cache_Hybrid class (a.k.a. two level cache)
 *
 * @copyright  SpoonLabs <http://www.spoonlabs.com>
 * @package    vivvo_framework
 * @subpackage cache_driver
 * @category   Driver
 * @version    $Revision: 4824 $
 * @author     Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 *
 * @since 4.5
 *
 */
class vivvo_cache_Hybrid implements vivvo_cache_driver {

	/**
	 * @var	vivvo_cache_driver	Fast (memory) cache driver
	 */
	private $fast;

	/**
	 * @var	vivvo_cache_driver	Slow (persistent storage) cache driver
	 */
	private $slow;

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

		if (empty($params['slow'])) {
			throw new CacheDriverException('Hybrid', "Required parameter missing 'slow'.");
		} if (!is_object($params['slow']) or !($params['slow'] instanceof vivvo_cache_driver)) {
			throw new CacheDriverException('Hybrid', "Slow driver does not implement vivvo_cache_driver interface.");
		}

		if (empty($params['fast'])) {
			throw new CacheDriverException('Hybrid', "Required parameter missing 'fast'.");
		} if (!is_object($params['fast']) or !($params['fast'] instanceof vivvo_cache_driver)) {
			throw new CacheDriverException('Hybrid', "Fast driver does not implement vivvo_cache_driver interface.");
		}

		$this->slow = $params['slow'];
		$this->fast = $params['fast'];
	}

	/**
	 * @var	array	List of available "slow" cache drivers, sorted by priority (biggest priority has driver with greater index)
	 * TODO: benchmark!
	 */
	public static $slow_drivers = array(
		100 => 'Cache_Lite',
		200 => 'MySQL',
		300 => 'SQLite'
	);

	/**
	 * @var	array	List of available "fast" cache drivers, sorted by priority (biggest priority has driver with greater index)
	 * TODO: benchmark!
	 */
	public static $fast_drivers = array(
		100 => 'XCache',
		200 => 'Memcached',
		201 => 'Memcache'
	);

	/**
	 * Creates new instance of Hybrid cache driver
	 *
	 * @param	string	$slow			(optional) Name of "slow" driver, if null the best available driver will be choosen
	 * @param	string	$fast			(optional) Name of "fast" driver, if null the best available driver will be choosen
	 * @param	array	$slow_params	(optional) Initialization parameters for slow driver
	 * @param	array	$fast_params	(optional) Initialization parameters for fast driver
	 * @return	vivvo_cache_Hybrid
	 */
	public static function factory($slow = null, $fast = null, array $slow_params = array(), array $fast_params = array()) {

		if ($slow == null) {
			if (defined('VIVVO_SYSTEM_CACHE_DRIVER_SLOW')) {
				$slow = VIVVO_SYSTEM_CACHE_DRIVER_SLOW;
			} else {

				$priorities = array_keys(self::$slow_drivers);
				rsort($priorities);

				foreach ($priorities as $priority) {
					if (vivvo_cache::is_supported(self::$slow_drivers[$priority]))	{
						$slow = self::$slow_drivers[$priority];
						break;
					}
				}
			}
		}

		if ($fast == null) {
			if (defined('VIVVO_SYSTEM_CACHE_DRIVER_FAST')) {
				$fast = VIVVO_SYSTEM_CACHE_DRIVER_FAST;
			} else {

				$priorities = array_keys(self::$fast_drivers);
				rsort($priorities);

				foreach ($priorities as $priority) {
					if (vivvo_cache::is_supported(self::$fast_drivers[$priority]))	{
						$fast = self::$fast_drivers[$priority];
						break;
					}
				}
			}
		}

		return new self(array(
			'slow' => vivvo_cache::get_instance($slow, $slow_params),
			'fast' => vivvo_cache::get_instance($fast, $fast_params)
		));
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

		$slow = $this->slow->put($id, $data, $tags, $lifetime);
		$fast = $this->fast->put($id, $data, null, $lifetime);

		return $slow && $fast;
	}

	/**
	 * Checks if cache contains data with specified id
	 *
	 * @param	string	$id
	 * @return	bool
	 */
	public function exists($id) {
		return $this->fast->exists($id) || $this->slow->exists($id);
	}

	/**
	 * Returns cached data by it's id, if there is no cached data with such id false is returned
	 *
	 * @param	string	$id
	 * @return	mixed
	 */
	public function get($id) {

		if (($data = $this->fast->get($id)) !== false) {
			return $data;
		}

		return $this->slow->get($id);
	}

	/**
	 * Returns all items with specified tag
	 *
	 * @param	string	$tag
	 * @return	array
	 */
	public function find($tag) {
		return $this->slow->find($tag);
	}

	/**
	 * Extends lifetime of cached item
	 *
	 * @param	string	$id
	 * @param	int		$lifetime
	 * @return	bool
	 */
	public function extend_lifetime($id, $lifetime) {

		$slow = $this->slow->extend_lifetime($id, $lifetime);
		$fast = $this->fast->extend_lifetime($id, $lifetime);

		return $slow && $fast;
	}

	/**
	 * Removes an item from cache
	 *
	 * @param	string	$id
	 * @return	bool
	 */
	public function delete($id) {

		$slow = $this->slow->delete($id);
		$fast = $this->fast->delete($id);

		return $slow && $fast;
	}

	/**
	 * Removes all items from cache with specified cache ids (cache id = md5(id))
	 *
	 * @param	array	$ids
	 * @return	bool
	 */
	public function delete_by_cache_id(array $ids) {

		$slow = $this->slow->delete_by_cache_id($ids);
		$fast = $this->fast->delete_by_cache_id($ids);

		return $slow && $fast;
	}

	/**
	 * Removes all items from cache with matching tag
	 *
	 * @param	string		$tag
	 * @param	bool		(optional) $return_ids
	 * @return	bool|array
	 */
	public function delete_by_tag($tag, $return_ids = false) {

		$ids = $this->slow->delete_by_tag($id, true);
		$fast = $this->fast->delete_by_cache_id($ids);

		return $return_ids ? $ids : $fast;
	}

	/**
	 * Removes all items from cache
	 *
	 * @return	bool
	 */
	public function delete_all() {

		$slow = $this->slow->delete_all($id);
		$fast = $this->fast->delete_all($id);

		return $slow && $fast;
	}

	/**
	 * Performs data store cleanup (removes all expired items)
	 *
	 * @return	bool
	 */
	public function cleanup() {

		$slow = $this->slow->cleanup();
		$fast = $this->fast->cleanup();

		return $slow && $fast;
	}
}
?>