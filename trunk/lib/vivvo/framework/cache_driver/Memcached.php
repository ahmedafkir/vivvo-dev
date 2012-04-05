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
 * We're extending Memcache class
 */
class_exists('vivvo_cache_Memcache') or require VIVVO_FS_CACHE_DRIVER . 'Memcache.php';


/**
 * Memcached cache driver will try to create a pool of memcache servers.
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
 * @author      Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 */
class vivvo_cache_Memcached extends vivvo_cache_Memcache {

    /**
	 * Checks if this driver is supported by runtime environment
	 *
	 * @return bool
	 */
	public static function is_supported() {
		return extension_loaded('memcached');
	}

    /**
	 * Class constructor.
	 *
	 * @param	array	$params
	 */
    public function __construct(array $params) {

        if (!isset($params['servers']) or empty($params['servers'])) {
            throw new CacheDriverException('Memcached', 'To use Memcached you must set at least one Memcache server. Obviously, you have none.');
        }

        // Create default parameters
        $params += array('compression' => false, 'lifetime' => 300, 'namespace' => 'vivvo');

        // Default lifetime
        $this->lifetime = (int)$params['lifetime'];

        // Prefix ids
        $this->namespace = $params['namespace'];

        // Create a sunshine
        $this->memcache = new Memcached;

        // zlib compression
        if ($params['compression'] === true) {
            $this->memcache->setOption(Memcached::OPT_COMPRESSION, true);
        }

        // Turn on libketama compatibility
        $this->memcache->setOption(Memcached::OPT_LIBKETAMA_COMPATIBLE, true);

        $servers = array();

        // Create server pool
        foreach ($params['servers'] as $server) {

            // Populate all required parameters with default values
            $server += array('host' => '127.0.0.1', 'port' => 11211, 'weight' => 1);

            $servers[] = array($server['host'], $server['port'], $server['weight']);
        }

        $this->memcache->addServers($servers);
    }

    /**
     * Stores tags array to memcache
     */
    protected function store_tags() {
        $this->memcache->set($this->namespace . ':tags', self::$tags, 0);
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
            foreach ($tags as $tag) {
                self::$tags[$tag][$id] = $cache_id;
            }
            $this->store_tags();
        }

        return $this->memcache->set($this->namespace . ':' . $cache_id, $data, $lifetime);
    }
}
?>