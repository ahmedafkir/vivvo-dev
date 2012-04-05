<?php
/* =============================================================================
 * $Revision: 4834 $
 * $Date: 2010-03-30 11:39:23 +0200 (Tue, 30 Mar 2010) $
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
	 * ArticlesRevisions object
	 *
	 * @version		$Revision: 4834 $
	 * @copyright	Spoonlabs
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class ArticlesRevisions extends vivvo_post_object {

		/**
		 * @var	string	DB table name
		 */
		public $_sql_table = 'articles_revisions';

		/**
		 * @var	int		Row id - INT UNSIGNED
		 */
		public $id;

		/**
		 * @var	int		Article id - INT UNSIGNED
		 */
		public $article_id;

		/**
		 * @var	int		Version number - INT UNSIGNED
		 */
		public $version;

		/**
		 * @var	string	Article title - VARCHAR(255)
		 */
		public $title;

		/**
		 * @var	string	Article body - TEXT
		 */
		public $body;

		/**
		 * @var	string	Article abstract - TEXT
		 */
		public $abstract;

		/**
		 * @var	int		Creator id - INT UNSIGNED
		 */
		public $creator_id;

		/**
		 * @var	int		Created time - TIMESTAMP
		 */
		public $created_time;

		/**
		 * @var	int		Revision type - TINYINT
		 * 	0 - revision
		 * 	1 - auto draft
		 */
		public $type;


		/**
		 * Sets $id
		 *
		 * @param	int			$id
		 * @return	ArticlesRevisions
		 */
		public function set_id($id) {
			$this->id = $id;
			return $this;
		}

		/**
		 * Sets $article_id
		 *
		 * @param	int			$tag_id
		 * @return	ArticlesRevisions
		 */
		public function set_article_id($article_id) {
			$this->article_id = $article_id;
			return $this;
		}

		/**
		 * Sets $version
		 *
		 * @param	int			$version
		 * @return	ArticlesRevisions
		 */
		public function set_version($version) {
			$this->version = $version;
			return $this;
		}

		/**
		 * Sets $title
		 *
		 * @param	string		$title
		 * @return	ArticlesRevisions|false
		 */
		public function set_title($title) {
			if (!empty($title)) {
				$this->title = $title;
				return $this;
			}
			return false;
		}

		/**
		 * Sets $body
		 *
		 * @param	string		$body
		 * @return	ArticlesRevisions
		 */
		public function set_body($body) {
			$this->body = $body;
			return $this;
		}

		/**
		 * Sets $abstract
		 *
		 * @param	string		$abstract
		 * @return	ArticlesRevisions
		 */
		public function set_abstract($abstract) {
			$this->abstract = $abstract;
			return $this;
		}

		/**
		 * Sets $creator_id
		 *
		 * @param	int			$creator_id
		 * @return	ArticlesRevisions
		 */
		public function set_creator_id($creator_id) {
			$this->creator_id = $creator_id;
			return $this;
		}

		/**
		 * Sets $created_time
		 *
		 * @param	mixed		$created_time
		 * @return	ArticlesRevisions
		 */
		public function set_created_time($created_time) {
			if (is_numeric($created_time)) {
				$created_time = date('Y-m-d H:i:s', $created_time);
			} elseif (!preg_match('/\d{4}-\d{2}-\d{2}(\s+\d{2}:\d{2}:\d{2})?/', $created_time)) {
				$created_time = date('Y-m-d H:i:s', strtotime($created_time));
			}
			$this->created_time = $created_time;
			return $this;
		}

		/**
		 * Sets $type
		 *
		 * @param	int			$type
		 * @return	ArticlesRevisions
		 */
		public function set_type($type) {
			$this->type = $type;
			return $this;
		}

		/**
		 * Returns $id
		 *
		 * @return	int
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Returns $article_id
		 *
		 * @return	int
		 */
		public function get_article_id() {
			return $this->article_id;
		}

		/**
		 * @var	Articles	Article object
		 */
		private $article = false;

		/**
		 * Returns article object
		 *
		 * @return	Articles
		 */
		public function get_article() {

			if ($this->article === false) {

				class_exists('Articles') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php';

				$article_list = new Articles_list();
				$this->article = $article_list->get_article_by_id($this->article_id);
			}

			return $this->article;
		}

		/**
		 * Returns $version
		 *
		 * @return	int
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Returns $title
		 *
		 * @return	string
		 */
		public function get_title() {
			return $this->title;
		}

		/**
		 * Returns $body
		 *
		 * @return	string
		 */
		public function get_body() {
			return $this->body;
		}

		/**
		 * Returns $abstract
		 *
		 * @return	string
		 */
		public function get_abstract() {
			return $this->abstract;
		}

		/**
		 * Returns $creator_id
		 *
		 * @return	int
		 */
		public function get_creator_id() {
			return $this->creator_id;
		}

		/**
		 * @var	object	User object
		 */
		private $creator = false;

		/**
		 * Returns user object for creator
		 *
		 * @return	object
		 */
		public function get_creator() {

			if ($this->creator === false and $this->creator_id) {
				$this->creator = vivvo_lite_site::get_instance()->get_user_manager()->get_user_by_id($this->creator_id);
			}

			return $this->creator;
		}

		/**
		 * Returns $created_time
		 *
		 * @return	string
		 */
		public function get_created_time() {
			return $this->created_time;
		}

		/**
		 * Returns $type
		 *
		 * @return	int
		 */
		public function get_type() {
			return $this->type;
		}

		/**
		 * Saves new revision for an article (also makes sure no more than VIVVO_REVISIONS_KEEP versions exists)
		 *
		 * @param	int		$article_id
		 * @param	string	$title
		 * @param	string	$abstract
		 * @param	string	$body
		 * @param	string	$time
		 * @param	int		$user
		 * @return	ArticlesRevisions|false
		 */
		public static function save_revision($article_id, $title, $abstract, $body, $time, $user) {

			$sm = vivvo_lite_site::get_instance();

			$res = $sm->get_db()->query(
				'SELECT (
					SELECT COUNT(*)
					FROM ' . VIVVO_DB_PREFIX . "articles_revisions
					WHERE article_id = $article_id AND type = 0
				 ), version
				 FROM " . VIVVO_DB_PREFIX . "articles_revisions
				 WHERE article_id = $article_id AND type = 0
				 ORDER BY version DESC
				 LIMIT 1"
			);

			if (!PEAR::isError($res)) {

				if (!$res->numRows()) {
					$version = 0;
					$total = 0;
				} else {
					list($total, $version) = $res->fetchRow();
				}

				$res->free();

				$revision = new ArticlesRevisions($sm, array(
					'article_id' => $article_id,
					'version' => $version + 1,
					'title' => $title,
					'abstract' => $abstract,
					'body' => $body,
					'created_time' => $time,
					'creator_id' => $user,
					'type' => 0
				));

				class_exists('vivvo_post_master') or require VIVVO_FS_FRAMEWORK . 'vivvo_post.php';

				$post_master = new vivvo_post_master($sm);
				$post_master->set_data_object($revision);

				if ($post_master->sql_insert()) {

					if ($total >= VIVVO_REVISIONS_KEEP) {
						$sm->get_db()->exec(
							'DELETE FROM ' . VIVVO_DB_PREFIX . "articles_revisions
							 WHERE article_id = $article_id AND type = 0
							 ORDER BY version ASC
							 LIMIT " . ($total - VIVVO_REVISIONS_KEEP + 1)
						);
					}

					return $revision;
				}
			}

			return false;
		}

		/**
		 * Saves auto-draft of an article
		 *
		 * @param	int		$article_id
		 * @param	string	$title
		 * @param	string	$abstract
		 * @param	string	$body
		 * @param	string	$time
		 * @param	int		$user
		 * @return	ArticlesRevisions|false
		 */
		public static function save_auto_draft($article_id, $title, $abstract, $body, $time, $user) {

			$sm = vivvo_lite_site::get_instance();

			// remove all other auto-drafts for this arrticle (if any), as there should be only one
			$sm->get_db()->exec(
				'DELETE FROM ' . VIVVO_DB_PREFIX . "articles_revisions
				 WHERE article_id = $article_id AND type = 1"
			);

			$revision = new ArticlesRevisions($sm, array(
				'article_id' => $article_id,
				'version' => 0,
				'title' => $title,
				'abstract' => $abstract,
				'body' => $body,
				'created_time' => $time,
				'creator_id' => $user,
				'type' => 1
			));

			class_exists('vivvo_post_master') or require VIVVO_FS_FRAMEWORK . 'vivvo_post.php';

			$post_master = new vivvo_post_master($sm);
			$post_master->set_data_object($revision);

			if ($post_master->sql_insert()) {
				return $revision;
			}

			return false;
		}
	}

	/**
	 * ArticlesRevisions_list object
	 *
	 * @version		$Revision: 4834 $
	 * @copyright	Spoonlabs
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class ArticlesRevisions_list extends vivvo_db_paged_list {

		/**
		 * @var	string	Name of the table in database
		 */
		var $_sql_table = 'articles_revisions';

		/**
		 * @var	string	Name of the class representing row in a table
		 */
		var $post_object_type = 'ArticlesRevisions';


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
		function _default_query($reset = false) {

			if ($reset) {
				$this->_query->reset_query();
			}

			$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table .' AS ar');
			$this->_query->add_fields('ar.*');
		}

		/**
		 * Adds filter to the WHERE clause of the query
		 *
		 * @param	string	$type
		 * @param	mixed	$condition
		 */
		function add_filter($type, $condition = '') {

			$condition = secure_sql($condition);
			$flag = false;

			switch ($type) {

				case 'not_id':
				case 'not_article_id':
				case 'not_version':
				case 'not_creator_id':
				case 'not_type':
					$flag = true;
					$type = substr($type, 4);
				case 'id':
				case 'article_id':
				case 'version':
				case 'creator_id':
				case 'type':
					$condition = secure_sql_in($condition);
					$operator = ($flag ? 'NOT ' : '') . 'IN';
					$this->_query->add_where("ar.$type $operator ($condition)");
				break;

				case 'title_matches':
					$this->_query->add_where("ar.title LIKE '%$condition%'");
				break;

				case 'title':
					$this->_query->add_where("ar.title = '$condition'");
				break;

				case 'not_created_time':
					$flag = true;
				case 'created_time':
					$condition = (int)$condition;
					$operator = $flag ? '!=' : '=';
					$this->_query->add_where("ar.created_time $operator $condition");
				break;

				case 'created_time_lte':
				case 'created_time_gte':
					$flag = true;
				case 'created_time_lt':
				case 'created_time_gt':
					$condition = (int)$condition;
					$operator = ($type[13] == 'g' ? '>' : '<') . ($flag ? '=' : '');
					$this->_query->add_where("ar.created_time $operator $condition");
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
			'search_article_id' => 'article_id', 'search_not_article_id' => 'not_article_id',
			'search_version' => 'version', 'search_not_version' => 'not_version',
			'search_creator_id' => 'creator_id', 'search_not_creator_id' => 'not_creator_id',
			'search_title' => 'title', 'search_created_time' => 'created_time',
			'search_not_created_time' => 'not_created_time', 'search_created_time_lt' => 'created_time_lt',
			'search_created_time_lte' => 'created_time_lte', 'search_created_time_gt' => 'created_time_gt',
			'search_created_time_gte' => 'created_time_gte', 'search_type' => 'type',
			'search_not_type' => 'not_type'
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

			if (!empty($params['search_title']) and defined('VIVVO_ADMIN_MODE')) {
				$this->add_filter('title_matches', $params['search_title']);
				unset($params['search_title']);
			}

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
					case 'article_id':
					case 'version':
					case 'title':
					case 'created_time':
					case 'type':
						$sql_order[] = "ar.$order $direction";
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
					case 'title':
					case 'article_id':
					case 'version':
					case 'creator_id':
					case 'created_time':
						$this->_query->add_group_by("ar.$field");
					default:
						// ignore
				}
			}
#error_log('DEBUG: [SQL] ArticlesRevisions -> ' . $this->_query->get_query());
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
		 * Returns revisions by article id (or false)
		 *
		 * @param	int		$article_id
		 * @return	mixed
		 */
		public function get_revisions_by_article_id($article_id) {

			if (!$article_id) {
				return false;
			}

			$list = $this->search(array('search_article_id' => $article_id));

			if (!empty($list)) {
				return $list;
			}

			return false;
		}

		/**
		 * Returns item by article_id and version number (or false)
		 *
		 * @param	int		$article_id
		 * @param	int		$rev
		 * @return	mixed
		 */
		public function get_revision_by_article_id_ver($article_id, $rev) {

			if (!$article_id or !$rev) {
				return false;
			}

			$list = $this->search(array('search_article_id' => $article_id, 'search_version' => $rev));

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
		public static function get_search_params($sm, $in_params){

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
		public static function get_search_params_from_url(&$sm) {
			return self::get_search_params($sm, $sm->get_url_manager()->list);
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
?>