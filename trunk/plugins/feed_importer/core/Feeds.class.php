<?php
/* =============================================================================
 * $Revision: 5418 $
 * $Date: 2010-05-28 12:09:06 +0200 (Fri, 28 May 2010) $
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
	 * Feeds object
	 *
	 * @version		$Revision: 5418 $
	 * @copyright	SpoonLabs
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 * @see			vivvo_post_object
	 */
	class Feeds extends vivvo_post_object {

		/**
		 * @var	int		`id` INT UNSIGNED NOT NULL AUTO_INCREMENT
		 */
		public $id;

		/**
		 * @var	int		`category_id` INT NOT NULL
		 */
		public $category_id;

		/**
		 * @var	string	`feed` TEXT NOT NULL
		 */
		public $feed;

	    /**
		 * @var	int		`count` INT NOT NULL DEFAULT 0
		 */
		public $count = 0;

		/**
		 * @var	string	`favicon` VARCHAR(250) NULL DEFAULT NULL
		 */
		public $favicon;

		/**
		 * @var	string	`author` VARCHAR(100) NULL DEFAULT NULL
		 */
		public $author;


		/**
		 *	@var	string	Table name
		 */
		var $_sql_table = 'feeds';


		/**
		 * Sets $id
		 *
		 * @param	int		$id
		 * @return	Feeds
		 */
		public function set_id($id) {
			$this->id = $id;
			return $this;
		}

		/**
		 * Sets $category_id
		 *
		 * @param	int			$category_id
		 * @return	false|Feeds
		 */
		public function set_category_id($category_id) {
			if (!$category_id) {
			   return false;
			}
			$this->category_id = $category_id;
			return $this;
		}

		/**
		 * Sets $feed
		 *
		 * @param	string		$feed
		 * @return	false|Feeds
		 */
		public function set_feed($feed) {
			if (!($feed = trim($feed))) {
			   return false;
			}
			$this->feed = $feed;
			return $this;
		}

		/**
		 * Sets $count
		 *
		 * @param	int		$count
		 * @return	Feeds
		 */
		public function set_count($count) {
			$this->count = $count;
			return $this;
		}

		/**
		 * Sets $favicon
		 *
		 * @param	string	$favicon
		 * @return	Feeds
		 */
		public function set_favicon($favicon) {
			$this->favicon = $favicon;
			return $this;
		}

		/**
		 * Sets $author
		 *
		 * @param	string	$author
		 * @return	Feeds
		 */
		public function set_author($author) {
			$this->author = $author;
			return $this;
		}

		/**
		 * Gets $id
		 *
		 * @return	int
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Gets $category_id
		 *
		 * @return	int
		 */
		public function get_category_id() {
			return $this->category_id;
		}

		/**
		 * Gets $feed
		 *
		 * @return	string
		 */
		public function get_feed() {
			return $this->feed;
		}

		/**
		 * Gets $count
		 *
		 * @return	int
		 */
		public function get_count() {
			return $this->count;
		}

		/**
		 * Gets $favicon
		 *
		 * @return	string
		 */
		public function get_favicon() {
			return $this->favicon;
		}

		/**
		 * Gets $author
		 *
		 * @return	string
		 */
		public function get_author() {
			return $this->author;
		}

		/**
		 * @var	Categories
		 */
		protected $category = false;

		/**
		 * Returns category object linked with this feed
		 *
		 * @return	Categories|false
		 */
		public function get_category() {

			if ($this->category === false and $this->category_id) {

				$categories = vivvo_lite_site::get_instance()->get_categories();

				if (empty($categories->list[$this->category_id])) {
					$this->category_id = 0;
				} else {
					$this->category = $categories->list[$this->category_id];
				}
			}

			return $this->category;
		}

		/**
		 * Returns name of the category linked with this feed
		 *
		 * @return	string
		 */
		public function get_category_name() {

			if (!$this->get_category()) {
				return '';
			}

			return $this->category->get_category_name();
		}


		/**
		 * Imports articles from feed
		 *
		 * @return	array	Number of imported (index: 0) and updated (index: 1) articles
		 */
		public function import_articles() {

			require_once VIVVO_FS_INSTALL_ROOT . 'lib/simplepie/simplepie.php';
			require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php';
			require_once VIVVO_FS_FRAMEWORK . 'vivvo_post.php';

			$sm = vivvo_lite_site::get_instance();

			$db = $sm->get_db();

			$post_master = new vivvo_post_master($sm);

			$simplepie = new SimplePie();

			$simplepie->enable_cache(false);
			$simplepie->set_feed_url($feed_url = $this->get_feed());
			$simplepie->enable_order_by_date(true);
			@$simplepie->init();

			if ($simplepie->error()) {
				return array(0, 0);
			}

			$now = date('Y-m-d H:i:00', $now_ts = time());

			$count_added = 0;
			$count_updated = 0;

			$imported = array();

			if (VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE) {
				$auto_delete_ts = VIVVO_PLUGIN_FEED_IMPORTER_AUTO_DELETE * 86400;
			} else {
				$auto_delete_ts = false;
			}

			if (VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE) {
				$auto_archive_ts = VIVVO_PLUGIN_FEED_IMPORTER_AUTO_ARCHIVE * 86400;
			} else {
				$auto_archive_ts = false;
			}

			foreach ($simplepie->get_items() as $item) {

				if (($item_datetime = $item->get_date('Y-m-d H:i:00')) != null) {
					$item_datetime_ts = strtotime($item_datetime);
					// make sure not to import articles which should be deleted or archived
					if ($auto_delete_ts and $now_ts - $item_datetime_ts > $auto_delete_ts or
						$auto_archive_ts and $now_ts - $item_datetime_ts > $auto_archive_ts) {
						continue;
					}
				}

				$guid = $item->get_item_tags('', 'guid');
				$guid = $guid[0]['data'];

				if (!$guid and !($guid = $item->get_title() . $item->get_permalink())) {
					continue;	// can't determine reliable unique identifier
				}

				$feed_item_id = md5($feed_url . $guid);

				if (in_array($feed_item_id, $imported)) {
					continue;	// already imported this one, feed has duplicate items?
				}

				$res = $db->query('SELECT id, created FROM ' . VIVVO_DB_PREFIX . "articles WHERE feed_item_id = '$feed_item_id' LIMIT 1");

				if (PEAR::isError($res)) {
					continue;
				}

				$update = false;

				if ($res->numRows() and ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {
					if (VIVVO_PLUGIN_FEED_IMPORTER_UPDATE_ARTICLES and $item_datetime != null and time($row['created']) < $item_datetime_ts) {
						$update = true;
					} else {
						$res->free();
						continue;	// timestamp not changed consider content is the same too...
					}
				}

				$res->free();

				$imported[] = $feed_item_id;

				if (!($author = $this->get_author()) and !($author = $item->get_author())) {
					if (preg_match('/^[^:]+:\/\/(www\.)?([^\/]+)/', $item->get_permalink(), $author)) {
						$author = $author[2];
					} else {
						$author = '';
					}
				}

				$article = new Articles($sm, array(
					'category_id' => $this->category_id,
					'user_id' => VIVVO_PLUGIN_FEED_IMPORTER_USER_ID,
					'author' => $author,
					'title' => $title = $item->get_title(),
					'created' => $item_datetime ? $item_datetime : $now,
					'body' => $item->get_description(),
					'status' => VIVVO_PLUGIN_FEED_IMPORTER_STATUS,
					'sefriendly' => make_sefriendly($title),
					'link' => $item->get_permalink(),
					'show_comment' => VIVVO_PLUGIN_FEED_IMPORTER_SHOW_COMMENT,
					'feed_item_id' => $feed_item_id
				));

				$post_master->set_data_object($article);
				if ($update) {
					$article->set_id($row['id']);
					$post_master->sql_update() and $count_updated++;
				} elseif ($post_master->sql_insert()) {
					$count_added++;
				}
			}

			$this->set_favicon($simplepie->get_favicon());
			$this->set_count($this->get_count() + $count_added);

			if (VIVVO_PLUGIN_FEED_IMPORTER_USE_LOGO and $this->get_category() and
			   ($image_url = $simplepie->get_image_url()) and
				preg_replace('/_\d+(\.[^.]+)$/', '$1', $this->category->get_image()) != ($basename = basename($image_url))) {

				class_exists('HTTP_Request2') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/PEAR/HTTP/Request2.php';

				try {

					$request = new HTTP_Request2($image_url);
					$response = $request->send();

					if ($response->getStatus() == 200) {

						$file_contents = $response->getBody();

						$basename = $sm->get_file_manager()->random_file_name($basename);
						file_put_contents(VIVVO_FS_INSTALL_ROOT . VIVVO_FS_FILES_DIR . $basename, $file_contents);

						$this->category->set_image($basename);
						$post_master->set_data_object($this->category);
						$post_master->sql_update();
					}

				} catch (Exception $e) {
					if (defined('VIVVO_CRONJOB_MODE')) {
						echo 'exception: ' . $e->getMessage() . PHP_EOL;
					}
				}
			}

			return array($count_added, $count_updated);
		}
	}

	/**
	 * Feeds list
	 *
	 * @version		$Revision: 5418 $
	 * @copyright	SpoonLabs
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class Feeds_list extends vivvo_db_paged_list {

		/**
		 * @var	string	Name of the table in database
		 */
		var $_sql_table = 'feeds';

		/**
		 * @var	string	Name of the class representing row in a table
		 */
		var $post_object_type = 'Feeds';

		/**
		 * Returns new instance of Game_list class
		 *
		 * @return	ArticlesRevisions_list
		 */
		public static function factory() {
			return new self();
		}

		/**
		 * Sets default SQL query
		 *
		 * @param	bool	$reset	Set to true to reset query to empty
		 */
		public function _default_query($reset = false) {

			if ($reset) {
				$this->_query->reset_query();
			}

		   $this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table . ' AS fd');
		   $this->_query->add_fields('fd.*');
		}

		/**
		 * Adds filter to the WHERE clause of the query
		 *
		 * @param	string	$type
		 * @param	mixed	$condition
		 */
		public function add_filter($type, $condition = '') {

			$condition = secure_sql($condition);

			switch ($type) {

				case 'not_id':
				case 'not_category':
				case 'not_count':
					$flag = true;
					$type = substr($type, 4);
				case 'id':
				case 'category_id':
				case 'count':
					$condition = secure_sql_in($condition);
					$operator = ($flag ? 'NOT ' : '') . 'IN';
					$this->_query->add_where("fd.$type $operator ($condition)");
				break;

				case 'not_feed':
				case 'not_author':
					$flag = true;
					$type = substr($type, 4);
				case 'feed':
				case 'author':
					$condition = secure_sql_in($condition, true);
					$operator = ($flag ? 'NOT ' : '') . 'IN';
					$this->_query->add_where("fd.$type $operator ($condition)");
				break;

				default:
					// ignore
			}
		}

		/**
		 * @var	array	Search param -> filter name mapping
		 */
		public static $search_params = array(
			'search_id' => 'id', 'search_not_id' => 'not_id',
			'search_category_id' => 'category_id', 'search_not_category_id' => 'not_category_id',
			'search_count' => 'count', 'search_not_count' => 'not_count',
			'search_feed' => 'feed', 'search_not_feed' => 'not_feed',
			'search_author' => 'author', 'search_not_author' => 'not_author'
		);

		/**
		 * Advaced search list generator
		 *
		 * @param	array	$params	Search parameters
		 * @param	string	$order	Order parameters
		 * @param	integer	$limit	Limit
		 * @param	integer	$offset	Offset
		 * @return	array	Array of team elements
		 */
		public function search($params, $order = '', $direction = 'ascending', $limit = 0, $offset = 0, $set_list = true) {

			$this->_default_query(true);
			$this->_query->set_limit((int)$limit);
			$this->_query->set_offset((int)$offset);

			foreach ($params as $param => $value) {
				if (isset(self::$search_params[$param])) {
					$this->add_filter(self::$search_params[$param], $value);
				}
			}

			$order_by = explode(',', $order);
			$search_direction = explode(',', $direction);

			if (count($search_direction) < count($order_by)) {
				$search_direction = array_merge(
					$search_direction,
					array_fill(
						0,
						count($order_by) - count($search_direction),
						end($search_direction)
					)
				);
			}

			$sql_order = array();

			foreach ($order_by as $index => $order) {

				$direction = ($search_direction[$index] === 'descending') ? 'DESC' : 'ASC';

				switch ($order) {
					case 'id':
					case 'category_id':
					case 'author':
					case 'feed':
						$sql_order[] = "fd.$order $direction";
					case '%no-sort':
					default:
						// ignore
				}
			}

			if (!empty($sql_order)) {
				$this->_query->add_order(implode(',', $sql_order));
			}

			if (!empty($params['group_by'])) {
				switch ($field = $params['group_by']) {
					case 'id':
					case 'category_id':
					case 'author':
					case 'feed':
						$this->_query->add_group_by("fd.$field");
					default:
						// ignore
				}
			}

#error_log('DEBUG: [SQL] Feeds_list -> ' . $this->_query->get_query());

			if ($set_list) {
				$this->set_list();
				return $this->list;
			}

			return array();
		}

		/**
		 * Returns item by it's id (or false)
		 *
		 * @param	int		$id
		 * @return	mixed
		 */
		public function get_item_by_id($id) {

			if (!$id) {
				return false;
			}

			$list = $this->search(array('search_id' => $id));

			if (!empty($list)) {
				return reset($list);
			}

			return false;
		}

		/**
		 * Performs db search
		 *
		 * @return	array|false
		 */
		public function search_from_params(array $params) {

			$params = self::get_search_params(null, $params);

			return $this->search(
				$params['search_options'],
				$params['search_sort_by'],
				$params['search_order'],
				$params['search_limit'],
				$params['offset']
			);
		}

		/**
		 * Parses search params
		 *
		 * @param	&vivvo_lite_site	$sm
		 * @param	array				$in_params
		 * @return	array
		 */
		public static function get_search_params($sm, $in_params) {

			$params = array();

			if (!empty($in_params['search_limit'])) {
				$params['search_limit'] = $in_params['search_limit'];
			} else {
				$params['search_limit'] = 10;
			}

			$params['search_options'] = array();

			if (!empty($in_params['search_options']) && is_array($in_params['search_options'])) {
				$params['search_options'] = $in_params['search_options'];
				unset($in_params['search_options']);
			}

			if (!empty($in_params['search_params']) && is_array($in_params['search_params'])) {
				$in_params = array_merge($in_params['search_params'], $in_params);
				unset($in_params['search_params']);
			}

			foreach ($in_params as $param => $value) {
				if (isset($value) and isset(self::$search_params[$param])) {
					$params['search_options'][$param] = $value;
				}
			}

			if (!empty($in_params['group_by'])) {
				$params['search_options']['group_by'] = $in_params['group_by'];
			}

			if (!empty($in_params['search_sort_by'])) {
				$params['search_sort_by'] = $in_params['search_sort_by'];
			} else {
				$params['search_sort_by'] = defined('VIVVO_ADMIN_MODE') ? 'id' : '%no-sort';
			}

			if (!empty($in_params['search_order'])) {
				$params['search_order'] = $in_params['search_order'];
			} else {
				$params['search_order'] = 'descending';
			}

			if (isset($in_params['pg'])) {
				$cur_page = +$in_params['pg'];
			}

			if (empty($cur_page)) {
				$cur_page = 1;
			}

			$params['pg'] = $cur_page;

			$params['offset'] = ($cur_page - 1) * $params['search_limit'];

			if (empty($params['offset'])) {
				$params['offset'] = 0;
			}

			if (!empty($in_params['cache'])) {
				$params['cache'] = $in_params['cache'];
			}

			return $params;
		}

		/**
		 * Parses search params from URL
		 *
		 * @param	&vivvo_lite_site	$sm
		 * @return	array
		 */
		public static function get_search_params_from_url($sm) {
			return self::get_search_params(null, $sm->get_url_manager()->list);
		}

		/**
		 * Performs db search based on parameters from url manager
		 *
		 * @return	array
		 */
		public static function search_from_url() {

			$sm = vivvo_lite_site::get_instance();
			$params = self::get_search_params_from_url($sm);
			$list = new self($sm);

			return $list->search(
				$params['search_options'],
				$params['search_sort_by'],
				$params['search_order'],
				$params['search_limit'],
				$params['offset']
			);
		}
	}

#EOF