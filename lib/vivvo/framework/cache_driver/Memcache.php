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
 * Memcache cache driver will try to create a pool of memcache servers.
 * To use only one memcache server, extend this class and override the
 * constructor using connect() instead addServer().
 *
 * It also supports tags, which are not available natively by Memcache.
 *
 * @copyright   SpoonLabs <http://www.spoonlabs.com>
 * @package     vivvo_framework
 * @subpackage  cache_driver
 * @category    Driver
 * @version     $Revision: 5174 $
 * @author      Mihail Binev <mbinev@spoonlabs.com>
 * @author      Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 */
class vivvo_cache_Memcache implements vivvo_cache_driver {

    /**
     * Memcache object
     * @var Memcache
     */
    protected $memcache;

    /**
     * Unique identifier for cached items
     * @var string
     */
    protected $namespace;

    /**
     * Default lifetime period
     * @var int
     */
    protected $lifetime;

    /**
     * Driver is enhanced with tags support.
     * @var array
     * @see vivvo_cache_Memcache::put()
     */
    protected static $tags = array();

    /**
	 * Checks if this driver is supported by runtime environment
	 *
	 * @return bool
	 */
	public static function is_supported() {
		return extension_loaded('memcache');
	}

    /**
	 * Class constructor.
	 *
	 * @param	array	$params
	 */
    public function __construct(array $params) {

        if (!isset($params['servers']) or empty($params['servers'])) {
            throw new CacheDriverException('Memcache', 'To use Memcache you must set at least one Memcache server. Obviously, you have none.');
        }

        // Create default parameters
        $params += array('compression' => false, 'lifetime' => 300, 'namespace' => 'vivvo');

        // zlib compression
        if ($params['compression'] === true) {
            $this->compression = MEMCACHE_COMPRESSED;
        }

        // Default lifetime
        $this->lifetime = (int)$params['lifetime'];

        // Prefix ids
        $this->namespace = $params['namespace'];

        // Create a sunshine
        $this->memcache = new Memcache;

        // Create server pool
        foreach ($params['servers'] as $server) {

            // Populate all required parameters with default values
            $server += array('host' => '127.0.0.1', 'port' => 11211, 'persistent' => false);

            // TODO: add to log if connection fails
            $this->memcache->addServer($server['host'], $server['port'], $server['persistent']);
        }
    }

    /**
     * Refreshes tags array
     */
    protected function fetch_tags() {
        self::$tags = $this->memcache->get($this->namespace . ':tags');
        is_array(self::$tags) or self::$tags = array();
    }

    /**
     * Stores tags array to memcache
     */
    protected function store_tags() {
        $this->memcache->set($this->namespace . ':tags', self::$tags, $this->compression, 0);
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

        if ($lifetime === null or ($lifetime = (int)$lifetime) <= 0) {
			$lifetime = 0;
		} elseif ($lifetime <= 2592000) {
			$lifetime += VIVVO_START_TIME - 1;
		} else {
			$lifetime -= 1;
		}

        $cache_id = md5($id);

        if (!empty($tags)) {

            $this->fetch_tags();

            foreach ($tags as $tag) {
                self::$tags[$tag][$id] = $cache_id;
            }

            $this->store_tags();
        }

        return $this->memcache->set($this->namespace . ':' . $cache_id, $data, $this->compression, $lifetime);
    }

    /**
	 * Checks if cache contains data with specified id
	 *
	 * @param	string	$id
	 * @return	bool
	 */
    public function exists($id) {
        return $this->memcache->get($this->namespace . ':' . md5($id)) !== false;
    }

    /**
	 * Returns cached data by it's id, if there is no cached data with such id false is returned
	 *
	 * @param	string	$id
	 * @return	mixed
	 */
    public function get($id) {
        return $this->memcache->get($this->namespace . ':' . md5($id));
    }

    /**
	 * Returns all items with specified tag
	 *
	 * @param	string	$tag
	 * @return	array
	 */
    public function find($tag) {

        $this->fetch_tags();

        $items = array();

        if (!empty(self::$tags[$tag])) {

            $ids = array();

            foreach (self::$tags[$tag] as $id) {
                $ids[] = $this->namespace . ':' . $id;
            }

            if (is_array($items = $this->memcache->get($ids))) {
                $items = array_values($items);
            } else {
                $items = array();
            }
        }

        return $items;
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

        if (($lifetime = (int)$lifetime) > 0 and $lifetime <= 2592000) {
			$lifetime = $this->lifetime + $lifetime;
		} elseif ($lifetime <= 0) {
			$lifetime = 0x7fffffff; // "never" expires
		}

        return $this->memcache->delete($this->namespace . ':' . md5($id), $lifetime);
    }

    /**
	 * Removes an item from cache
	 *
	 * @param	string	$id
	 * @return	bool
	 */
    public function delete($id) {

        $this->fetch_tags();

        foreach (self::$tags as $tag => $t) {
            if (isset(self::$tags[$tag][$id])) {
                unset(self::$tags[$tag][$id]);
            }
        }

        $this->store_tags();

        return $this->memcache->delete($this->namespace . ':' . md5($id));
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
            if (!$this->memcache->delete($this->namespace . ':' . $id)) {
                return false;
            }
		}

        return true;
	}

    /**
	 * Removes all items from cache with matching tag
	 *
	 * @param	string		$tag
	 * @param	bool		(optional) $return_ids
	 * @return	bool|array
	 */
	public function delete_by_tag($tag, $return_ids = false) {

        $this->fetch_tags();

        $ids = array();

        if (!empty(self::$tags[$tag])) {

            $ids = array_values(self::$tags[$tag]);

            foreach ($ids as $id) {
                if (!$this->memcache->delete($this->namespace . ':' . $id)) {
                    return $return_ids ? $ids : false;
                }
            }
        }

        return $return_ids ? $ids : true;
	}

    /**
	 * Removes all items from cache
	 *
	 * @return	bool
	 */
    public function delete_all() {

        if ($flushed = $this->memcache->flush()) {
            // Memcached has one second granularity, which means it must wait
            // at least 1 sec after flushing, that is, flush() will expire all items
            // set within that second and new items will be invalidated immediately
            sleep(1);
			self::$tags = array();
            $this->store_tags();
        }

        return $flushed;
    }

    /**
	 * Performs data store cleanup (removes all expired items)
	 *
	 * @return	bool
	 */
	public function cleanup() {
        return true;    // Memcache takes care of expired items on its own
	}
}
?>