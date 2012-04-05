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
 * vivvo_cache_MySQL class
 *
 * @copyright  SpoonLabs <http://www.spoonlabs.com>
 * @package    vivvo_framework
 * @subpackage cache_driver
 * @category   Driver
 * @version    $Revision: 5174 $
 * @author     Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 *
 * @since 4.5
 *
 */
class vivvo_cache_MySQL implements vivvo_cache_driver {

	/**
	 * Link to MySQL server
	 * @var	resource
	 */
	private $link;

	/**
	 * CacheData table name
	 * @var string
	 */
	private $tbl_data;

	/**
	 * CacheTags table name
	 * @var string
	 */
	private $tbl_tags;

	/**
	 * Internal (memory) cache
	 * @var	array
	 */
	private $cache = array();

	/**
	 * Checks if this driver is supported by runtime environment
	 *
	 * @return bool
	 */
	public static function is_supported() {
		return function_exists('mysql_connect');
	}

	/**
	 * Class constructor.
	 *
	 * @param	array	$params
	 */
	public function __construct(array $params) {

		if (!empty($params['link']) and is_resource($params['link'])) {

			$this->link = $params['link'];

			return;

		} else {

			foreach (array('host', 'username', 'password', 'database', 'data_table', 'tags_table') as $param) {
				if (empty($params[$param])) {
					throw new CacheDriverException('MySQL', "Required parameter missing '$param'.");
				}
			}
		}

		if (!empty($params['port'])) {
			$params['host'] .= ':' . $params['host'];
		}

		$this->link = @mysql_connect($params['host'], $params['username'], $params['password']);

		if (!($open = is_resource($this->link)) or !mysql_select_db($params['database'], $this->link)) {

			if ($open) {
				mysql_close($this->link);
			}

			throw new CacheDriverException('MySQL', 'Error establishing connection with database.');
		}

		@mysql_query('SET NAMES utf8', $this->link);

		$this->tbl_data = $params['data_table'];
		$this->tbl_tags = $params['tags_table'];
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

		$cache_id = md5($id);

		if (is_string($data)) {
			$cache_data = $data;
			$serialized = 0;
		} else {
			$cache_data = serialize($data);
			$serialized = 1;
		}

		$cache_data = mysql_real_escape_string($cache_data, $this->link);

		if ($lifetime === null or ($lifetime = (int)$lifetime) <= 0) {
			$lifetime = '0xffffffff';
		} elseif ($lifetime <= 2592000) {
			$lifetime += VIVVO_START_TIME - 1;
		} else {
			$lifetime -= 1;
		}

		if (!mysql_query(

			"INSERT INTO `{$this->tbl_data}`
			 VALUES ('$cache_id', @DATA:='$cache_data', $lifetime, $serialized)
			 ON DUPLICATE KEY UPDATE
				`expires` = $lifetime,
				`data` = @DATA,
				`serialized` = $serialized", $this->link)) {

			return false;
		}

		$this->cache[$id] = array(
			'id' => $cache_id,
			'expires' => $expires,
			'data' => $data,
			'serialized' => $serialized
		);

		if (!empty($tags)) {

			$values = array();

			foreach ($tags as $tag) {
				$tag = mysql_real_escape_string($tag, $this->link);
				$values[] = "('$cache_id', '$tag')";
			}

			$values = implode(',', $values);

			// possible failure of this query is ignored
			mysql_query(
				"INSERT INTO `{$this->tbl_tags}`
				 VALUES $values
				 ON DUPLICATE KEY UPDATE tag = tag", $this->link);
		}

		return true;
	}

	/**
	 * Checks if cache contains data with specified id
	 *
	 * @param	string	$id
	 * @return	bool
	 */
	public function exists($id) {

		if (array_key_exists($id, $this->cache)) {

			return true;
		}

		$cache_id = md5($id);

		$now = VIVVO_START_TIME;

		$res = mysql_query("SELECT * FROM `{$this->tbl_data}` WHERE `id` = '$cache_id' AND `expires` > $now LIMIT 1");

		if (is_resource($res) and mysql_num_rows($res)) {

			$row = mysql_fetch_assoc($res);
			mysql_free_result($res);

			if ($row['serialized'] == 1 and ($row['data'] = @unserialize($row['data'])) === false) {
				return false;
			}

			$this->cache[$id] = $row;

			return true;
		}

		return false;
	}

	/**
	 * Returns cached data by it's id, if there is no cached data with such id false is returned
	 *
	 * @param	string	$id
	 * @return	mixed
	 */
	public function get($id) {

		if ($this->exists($id)) {
			return $this->cache[$id]['data'];
		}

		return false;
	}

	/**
	 * Returns all items with specified tag
	 *
	 * @param	string	$tag
	 * @return	array
	 */
	public function find($tag) {

		$now = VIVVO_START_TIME;
		$tag = mysql_real_escape_string($tag, $this->link);

		$res = mysql_query(
			"SELECT d.data, d.serialized
			 FROM `{$this->tbl_data}` AS d
			 LEFT JOIN `{$this->tbl_tags}` AS t ON t.cache_id = d.id
			 WHERE t.tag = '$tag' AND d.expires > $now", $this->link);

		$items = array();

		if (is_resource($res)) {

			while (($row = mysql_fetch_assoc($res)) !== false) {

				if ($row['serialized'] == 1 and ($row['data'] = @unserialize($row['data'])) === false) {
					continue;
				}

				$items[] = $row['data'];
			}

			mysql_free_result($res);
		}

		return $items;
	}

	/**
	 * Extends lifetime of cached item
	 *
	 * @param	string	$id
	 * @param	int		$lifetime
	 * @return	bool
	 */
	public function extend_lifetime($id, $lifetime) {

		if (($lifetime = (int)$lifetime) > 0 and $lifetime <= 2592000) {
			$lifetime = '`lifetime` + ' . $lifetime;
		} elseif ($lifetime <= 0) {
			$lifetime = '0xffffffff';
		}

		$id = md5($id);

		return mysql_query("UPDATE `{$this->tbl_data}` SET `lifetime` = $lifetime WHERE `id` = '$id'");
	}

	/**
	 * Removes an item from cache
	 *
	 * @param	string	$id
	 * @return	bool
	 */
	public function delete($id) {

		if (array_key_exists($id, $this->cache)) {
			unset($this->cache[$id]);
		}

		$id = md5($id);

		return mysql_query("DELETE FROM `{$this->tbl_data}` WHERE `id` = '$id' LIMIT 1", $this->link);
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

		foreach ($ids as &$id) {
			$id = "'" . mysql_real_escape_string($id, $this->link) . "'";
		}
		unset($id);

		$ids = implode(',', $ids);

		return mysql_query("DELETE FROM `{$this->tbl_data}` WHERE `id` IN ($ids)", $this->link);
	}

	/**
	 * Removes all items from cache with matching tag
	 *
	 * @param	string		$tag
	 * @param	bool		(optional) $return_ids
	 * @return	bool|array
	 */
	public function delete_by_tag($tag, $return_ids = false) {

		$tag = mysql_real_escape_string($tag, $this->link);

		if ($return_ids) {

			$res = mysql_query(
				"SELECT d.id FROM `{$this->tbl_data}` AS d
				 INNER JOIN `{$this->tbl_tags}` AS t ON d.id = t.cache_id
				 WHERE t.tag = '$tag'",
				$this->link
			);

			$ids = array();

			if (is_resource($res)) {

				while (($row = mysql_fetch_row($res)) !== false) {
					$ids[] = reset($row);
				}

				if (!empty($ids)) {

					$id_list = implode(',', $ids);

					if (!mysql_query("DELETE FROM `{$this->tbl_data}` WHERE `id` IN ($id_list)", $this->link)) {
						return array();
					}

					foreach ($this->cache as $key => $value) {
						if (in_array($this->cache['id'], $ids)) {
							unset($this->cache[$key]);
						}
					}
				}
			}

			return $ids;

		} else {

			// bit expensive to discard complete internal cache but better to be safe
			$this->cache = array();

			return mysql_query(
				"DELETE FROM d
				 USING `{$this->tbl_data}` AS d
				 INNER JOIN `{$this->tbl_tags}` AS t
				 WHERE t.cache_id = d.id AND t.tag = '$tag'", $this->link);
		}
	}

	/**
	 * Removes all items from cache
	 *
	 * @return	bool
	 */
	public function delete_all() {

		$this->cache = array();

		return mysql_query("TRUNCATE TABLE `{$this->tbl_data}`", $this->link);
	}

	/**
	 * Performs data store cleanup (removes all expired items)
	 *
	 * @return	bool
	 */
	public function cleanup() {

		// bit expensive to discard complete internal cache but better to be safe
		$this->cache = array();

		return mysql_query("DELETE FROM `{$this->tbl_data}` WHERE `expires` <= " . VIVVO_START_TIME, $this->link);
	}
}
?>