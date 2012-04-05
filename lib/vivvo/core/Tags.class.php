<?php
/* =============================================================================
 * $Revision: 5194 $
 * $Date: 2010-04-28 18:20:34 +0200 (Wed, 28 Apr 2010) $
 *
 * Vivvo CMS v4.7 (build 6082)
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
 * Tags object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see		vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class Tags extends vivvo_post_object {

	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * name
	 * Database field type:	varchar(20)
	 * Null status:		NO
	 *
	 * @var	string	$name
	 */
	var $name;

	var $rating;

	/**
	 * sefriendly
	 * Database field type:	varchar(255)
	 * Null status:		NO
	 *
	 * @var	string	$sefriendly
	 */
	public $sefriendly;

	/**
	 * group url
	 * @var	string	$group_url
	 */
	public $group_url;

	/**
	 * group name
	 * @var	string	$group_name
	 */
	public $group_name;

	/**
	 * @var	int	Topic id
	 */
	public $topic_id;


	var $_sql_table = 'tags';
	var $fontsize;


	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = (int) $id;
		return true;
	}

	/**
	 * Sets {@link $name}
	 *
	 * @param	string	$name
	 */
	function set_name($name){
		if ($name != ''){
			$this->name = html_entity_decode($name,ENT_QUOTES,'UTF-8'); 
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $rating}
	 *
	 * @param	integer	$rating
	 */
	function set_rating($rating){
		$this->rating = $rating;
	}

	/**
	 * Sets {@link $topic_id}
	 *
	 * @param int	$topic_id
	 */
	function set_topic_id($topic_id){
		$this->topic_id = $topic_id;
	}

	/**
	 * Sets {@link $sefriendly}
	 *
	 * @param	string	$sefriendly
	 */
	public function set_sefriendly($sefriendly) {
		$this->sefriendly = $sefriendly;
	}

	/**
	 * Sets {@link $group_url}
	 *
	 * @param	string	$group_url
	 */
	public function set_group_url($group_url) {
		$this->group_url = $group_url;
	}

	/**
	 * Sets {@link $group_name}
	 *
	 * @param	string	$group_name
	 */
	public function set_group_name($group_name) {
		$this->group_name = $group_name;
	}

	function set_fontsize($size){
		$this->fontsize = $size;
	}

	/**
	 * Gets $id
	 *
	 * @return	integer
	 */
	function get_id(){
		return $this->id;
	}
	/**
	 * Gets $name
	 *
	 * @return	string
	 */
	function get_name(){
		return $this->name;
	}
	/**
	 * Gets $rating
	 *
	 * @return	integer
	 */
	function get_rating(){
		return $this->rating;
	}

	/**
	 * Gets $sefriendly
	 *
	 * @return	string
	 */
	public function get_sefriendly(){
		return $this->sefriendly;
	}

	/**
	 * Gets {@link $topic_id}
	 *
	 * @return int
	 */
	function get_topic_id(){
		return $this->topic_id;
	}

	/**
	 * Gets $group_url
	 *
	 * @return	string
	 */
	public function get_group_url() {
		return $this->group_url;
	}

	/**
	 * Gets $group_name
	 *
	 * @return	string
	 */
	public function get_group_name() {
		return $this->group_name;
	}

	function get_fontsize(){
		return $this->fontsize;
	}

	function get_href(){
		return $this->format_href(null, $this->id, $this->name);
	}

	public $articles;

	function get_articles() {

		if (!is_array($this->articles)) {

			require_once(dirname(__FILE__) . '/Articles.class.php');

			$articles_list = new Articles_list();
			$this->articles = $articles_list->get_articles_by_tag_id($this->id);

			if ($this->articles == false) {
				$this->articles = array();
			}
		}

		return $this->articles;
	}

	function get_articles_count(){
		return $this->get_num_articles();
	}

	function get_articles_list($separator = ''){
		$articles = $this->get_articles();
		$ret = array();
		foreach ($articles as &$article){
			$ret[] = $article->get_title();
		}
		return implode($separator, $ret);
	}

	public $groups;

	function get_groups(){

		if (!is_array($this->groups)){

			require_once(dirname(__FILE__) . '/TagsGroups.class.php');

			$tags_groups_list = new TagsGroups_list();
			$this->groups = $tags_groups_list->get_groups_by_tag_id($this->id);

			if ($this->groups == false){
				$this->groups = array();
			}
		}

		return $this->groups;
	}

	function get_groups_list($delimiter = ', '){
		$group_names = array();
		foreach ($this->get_groups() as $group){
			$group_names[] = $group->get_name();
		}
		return implode($delimiter, $group_names);
	}

	private $num_articles = array();

	public function get_num_articles($topic_id = false) {

		$tag_id = $this->id;

		if ($topic_id === false) {

			if (!isset($this->num_articles['*'])) {

				$res = vivvo_lite_site::get_instance()->get_db()->query(
					'SELECT COUNT(*) FROM ' . VIVVO_DB_PREFIX . "articles_tags AS at
					 INNER JOIN articles AS a ON a.id = at.article_id
					 WHERE at.tag_id = $tag_id"
				);

				if (!PEAR::isError($res)) {
					$this->num_articles['*'] = $res->fetchOne();
					$res->free();
				} else {
					$this->num_articles['*'] = 0;
				}
			}

			return $this->num_articles['*'];
		}

		$topic_id = (int)$topic_id;

		if (!isset($this->num_articles[$topic_id])) {

			$res = vivvo_lite_site::get_instance()->get_db()->query(
				'SELECT COUNT(*) FROM ' . VIVVO_DB_PREFIX . 'articles_tags AS at
				 INNER JOIN ' . VIVVO_DB_PREFIX . "articles AS a ON a.id = at.article_id
				 WHERE at.tag_id = $tag_id AND at.tags_group_id = $topic_id"
			);

			if (!PEAR::isError($res)) {
				$this->num_articles[$topic_id] = $res->fetchOne();
				$res->free();
			} else {
				$this->num_articles[$topic_id] = 0;
			}
		}

		return $this->num_articles[$topic_id];
	}

	function format_href($sm, $id, $name){

		if (strlen($this->group_url)) {
			return make_proxied_url($this->group_url . urlencode($this->sefriendly));
		}

		return make_proxied_url('tag/' . urlencode($this->sefriendly));
	}

	function on_delete($post_master) {

		//delete directly to avoid calling on_delete actions recursively
		$post_master->sql_delete_list('articles_tags', "tag_id=$this->id");
		$post_master->sql_delete_list('tags_to_tags_groups', "tag_id=$this->id");


	}
}

/**
 * Tags list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class Tags_list extends vivvo_db_paged_list {

	var $_sql_table = 'tags';
	var $post_object_type = 'Tags';

	function _default_query($advanced = false, $rating = false){

		$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table . ' AS t');

		if (is_array($this->_fields) && !empty($this->_fields)) {
			foreach ($this->_fields as $field) {
				$this->_query->add_fields('t.' . $field);
			}
		} else {
			$this->_query->add_fields('t.*');
		}

		if (!$advanced) {
			$this->_query->add_fields('COUNT(DISTINCT at.article_id) AS rating');
			$this->_query->add_fields('at.tags_group_id AS topic_id');
			$this->_query->add_join(' ' . ($rating ? 'INNER' : 'LEFT') . ' JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
			$this->_query->add_group_by('t.id');
		}
	}

	function get_total_count() {

		$query = 'SELECT count( DISTINCT t.id ) as count ' . $this->_query->get_from() . ' ' . $this->_query->get_join() . ' ' .
				$this->_query->get_where() . ' ' . $this->_query->get_group_by() . ' ' . $this->_query->get_having() . ' ';

		$res = vivvo_lite_site::get_instance()->get_db()->query($query);

		if (!PEAR::isError($res)){
			$sum = 0;
			$num = 0;
			while ($count = $res->fetchOne()) {
				$sum += $count;
				$num++;
			}
			$res->free();

			if ($num > 1) {
				return $num;
			} else {
				return $sum;
			}
		} else {
			return false;
		}
	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
			case 'name':
			case 'sefriendly':
				$this->_query->add_where("t.$type = '$condition'");
			break;
			case 'name_like':
				$condition = str_replace(array('%', '_'), array('\\%', '\\_'), $condition);
				$this->_query->add_where("t.name LIKE '%$condition%'");
			break;
			case 'article_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_fields('at.tags_group_id AS topic_id');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
				$this->_query->add_where("at.article_id IN ($condition)");
			break;
			case 'tags_group_id':
			case 'topic_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'tags_to_tags_groups AS ttg ON ttg.tag_id = t.id ', 'ttg');
				$this->_query->add_where("ttg.tags_group_id IN ($condition)");
			break;
			case 'not_tags_group_id':
			case 'not_topic_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'tags_to_tags_groups AS ttg ON ttg.tag_id = t.id ', 'ttg');
				$this->_query->add_where("ttg.tags_group_id NOT IN ($condition)");
			break;
			case 'user_tag_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_fields('at.tags_group_id AS topic_id');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
				$this->_query->add_where("at.tags_group_id IN ($condition)");
			break;
			case 'not_user_tag_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_fields('at.tags_group_id AS topic_id');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
				$this->_query->add_where("at.tags_group_id NOT IN ($condition)");
			break;
			case 'id_in':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("t.id IN ($condition)");
			break;
			case 'name_array':
			case 'sefriendly_array':
				foreach ($condition as &$item) {
					$item = "'$item'";
				}
				unset($item);
				$condition = implode(',', $condition);
				$type = str_replace('_array', '', $type);
				$this->_query->add_where("t.$type IN ($condition)");
			break;
			case 'search_starting_with':
				$condition = str_replace('%', '\%', $condition);
				$this->_query->add_fields('at.tags_group_id AS topic_id');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
				$this->_query->add_where("t.name LIKE '%$condition%'");
			break;
			case 'search_label_starting_with':
				$condition = str_replace('%', '\%', $condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'tags_to_tags_groups AS tg ON tg.tag_id = t.id ', 'tg');
				$this->_query->add_where("t.name LIKE '%$condition%'");
			break;
			case 'user_group_id':
				$condition = secure_sql_in(explode(',', $condition));
				$this->_query->add_fields('at.tags_group_id AS topic_id');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
				$this->_query->add_join(' LEFT JOIN '.VIVVO_DB_PREFIX.'group_user AS gu ON gu.user_id = at.user_id', 'gu');
				$this->_query->add_where("gu.group_id IN ($condition)");
			break;
			case 'not_user_group_id':
				$condition = secure_sql_in(explode(',', $condition));
				$this->_query->add_fields('at.tags_group_id AS topic_id');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
				$this->_query->add_join(' LEFT JOIN '.VIVVO_DB_PREFIX.'group_user AS gu ON gu.user_id = at.user_id', 'gu');
				$this->_query->add_where("gu.group_id NOT IN ($condition)");
			break;
			case 'article_status':
				$condition = secure_sql_in($condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tag_id = t.id ', 'at');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles AS a ON at.article_id = a.id ', 'a');
				$this->_query->add_where("a.status IN ($condition)");
			break;
			default:
		}
	}

	/**
	 * @var	array	Search param -> filter name mapping
	 */
	public static $search_params = array(
		'search_id' => 'id', 'search_name' => 'not_name', 'search_sefriendly' => 'sefriendly',
		'search_starting_with' => 'search_starting_with', 'search_id_in' => 'id_in',
		'search_tags_group_id' => 'tags_group_id', 'search_topic_id' => 'topic_id', 'search_not_topic_id' => 'not_topic_id',
		'search_name_array' => 'name_array', 'search_name_like' => 'name_like',
		'search_sefriendly_array' => 'sefriendly_array', 'search_user_group_id' => 'user_group_id',
		'search_not_user_group_id' => 'not_user_group_id',
		'search_label_starting_with' => 'search_label_starting_with',
		'search_user_tag_id' => 'user_tag_id', 'search_not_user_tag_id' => 'not_user_tag_id',
		'search_article_status' => 'article_status'
	);

	/**
	 * Advaced search list generator
	 *
	 * @param	array	$params	Search parameters
	 * @param	string	$order	Order parameters
	 * @param	integer	$limit	Limit
	 * @param	integer	$offset	Offset
	 * @return	array	Array of articles
	 */
	function &search($params, $order = '', $direction = 'ascending', $limit = 0, $offset = 0, $set_list = true){

		$this->_default_query(($advanced = !empty($params['search_advanced_mode'])) and empty($params['set_rating']), !empty($params['set_rating']));
		$this->_query->set_limit((int)$limit);
		$this->_query->set_offset((int)$offset);

		if ($advanced and defined('VIVVO_ADMIN_MODE') and !empty($params['search_name'])) {
			$params['search_name_like'] = $params['search_name'];
			unset($params['search_name']);
		}

		foreach ($params as $param => $value) {
			if (isset(self::$search_params[$param])) {
				$this->add_filter(self::$search_params[$param], $value);
			}
		}

		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order) {
			case 'id':
			case 'name':
			case 'sefriendly':
				$this->_query->add_order("t.$order $search_direction");
			case '%no-sort':
			default:
				// ignore
		}

		if (!empty($params['set_rating'])) {
			$this->_query->add_order('rating DESC');
			$this->_query->add_fields('at.tags_group_id AS topic_id');
			if (!defined('VIVVO_ADMIN_MODE')) {
				$this->add_filter('article_status', 1);
			}
		}

		if ($set_list) {

			$this->set_list();

			if (!empty($params['set_rating'])) {

				$this->set_fontsize();

				class_exists('TagsGroups') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php';

				$topic_list = TagsGroups_list::factory();

				foreach ($this->list as $tag) {
					if ($topic = $topic_list->get_group_by_id($tag->get_topic_id())) {
						$tag->set_group_name($topic->get_name());
						$tag->set_group_url($topic->get_href());
					}
				}
			}

			return $this->list;
		}
	}

	function &get_tag_by_name($condition){
		$this->_default_query();
		if (is_array($condition)){
			$this->add_filter('name_array', $condition);
		}else{
			$this->add_filter('name', $condition);
		}

		$this->set_list();
		if (!empty($this->list)){
			return current($this->list);
		}else{
			return intval(false);
		}
	}

	public function &get_tag_by_sefriendly($condition){
		$this->_default_query();
		if (is_array($condition)) {
			$this->add_filter('sefriendly_array', $condition);
		} else {
			$this->add_filter('sefriendly', $condition);
		}

		$this->set_list();
		if (!empty($this->list)) {
			return current($this->list);
		} else {
			return intval(0);
		}
	}

	function &get_tag_by_id($id){
		$id = (int) $id;
		$this->_default_query();
		$this->add_filter('id' , $id);
		$this->set_list();
		if (!empty($this->list)){
			return current($this->list);
		}else{
			return false;
		}
	}

	function get_tags_by_ids($ids){
		$this->_default_query();
		$this->add_filter('id_in' , $ids);
		$this->set_list();
		if (!empty($this->list)){
			return $this->list;
		}
		return false;
	}

	function get_tags_by_group_id($group_id, $set_url = true) {

		$tag_list = array();

		require_once(dirname(__FILE__) . '/TagsGroups.class.php');

		$tags_groups_list = new TagsGroups_list();

		$groups = $tags_groups_list->get_groups_by_ids($group_id);

		foreach ($groups as $group) {

			$group_url = $group->get_href();
			$group_name = $group->get_name();

			$this->_query->reset_query();
			$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table . ' as t');
			$this->_query->add_fields('t.*');
			$this->_query->add_group_by('t.id');
			$this->add_filter('tags_group_id', $group->get_id());

			$this->set_list();

			if ( !empty($this->list) && $set_url ) {

				foreach ($this->list as &$tag) {
					$tag->set_group_url($group_url);
					$tag->set_group_name($group_name);
                   // $tag->set_tag_name($tag->name);
                    
                    
				}
			}

			$tag_list += $this->list;
		}

		$this->list = $tag_list;

		return empty($this->list) ? false : $this->list;
	}

	/**
	 * Returns list of tags by article and topic
	 *
	 * @param	Articles	$article
	 * @param	TagsGroups	$topic
	 * @return	array
	 */
	public function get_tags_by_article_topic($article, $topic, $clone = false) {

		$topic_url = $topic->get_href();
		$topic_name = $topic->get_name();

		$this->_query->reset_query();
		$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table . ' AS t');
		$this->_query->add_fields('t.*');
		$this->_query->add_group_by('t.id');
		$this->add_filter('tags_group_id', $topic->get_id());
		$this->add_filter('article_id', $article->get_id());

		$this->set_list();

		if (!empty($this->list)) {
			foreach ($this->list as &$tag) {
				$clone and $tag = clone $tag;
				$tag->set_group_url($topic_url);
                $tag->set_group_name($topic_name);

			}
			unset($tag);
		}
		return $this->list;
	}

	function &get_all_tags(){
		$this->_default_query();

		$this->set_list();
		if (!empty($this->list)){
			return $this->list;
		}else{
			return false;
		}
	}

	function &get_orphan_tags($tag_array = array()){
		$this->_default_query();
		if (is_array($tag_array) && !empty($tag_array)){
			$tag_array = implode(',',$tag_array);
			$this->add_filter('id_in',$tag_array);
		}

		$this->_query->add_fields('COUNT(DISTINCT at.tag_id) AS tag_link_count');
		$this->_query->add_having('tag_link_count = 0');
		$this->_query->add_where('at.group_id = 0');

		$this->set_list();
		if (!empty($this->list)){
			return $this->list;
		}else{
			return false;
		}

	}

	function set_fontsize(){
		$min = 0;
		$max = 0;
		if (is_array($this->list) && !empty($this->list)){
			foreach ($this->list as $v){
				$min = min ($min, $v->rating);
				$max = max ($max, $v->rating);
			}
			foreach ($this->list as &$tag){
				if ($min == $max){
					$ratio = 2;
				}else{
					$ratio = floor( 5 * ($tag->get_rating() - $min) / ($max - $min) );
				}
				switch ($ratio){
					case 5:
						$fonsize = 24;
						break;
					case 4:
						$fonsize = 18;
						break;
					case 3:
						$fonsize = 14;
						break;
					case 2:
						$fonsize = 12;
						break;
					case 1:
						$fonsize = 10;
						break;
					default:
						$fonsize = 9;
						break;
				}
				$tag->set_fontsize($fonsize);
			}
		}
	}

	function &get_rated_tags($limit = 0, $group = -1, $category = -1){
		$this->_default_query();

		$this->add_filter('user_tag_id', $group);
		$this->_query->add_order('rating DESC');

		if ($category != -1) {
			$this->add_filter('category_id', $category);
		}

		$this->_query->set_limit($limit);
		$this->set_list();
		if (!empty($this->list)){
			$ids = array();
			foreach ($this->list as $tag){
				$ids[] = $tag->id;
			}
			$this->_query->reset_query();

			$this->_default_query();

			$this->add_filter('id_in', implode(',' , $ids));

			$this->_query->add_order('name ASC');

			$this->_query->set_limit($limit);
			$this->set_list();

			if ($group >= 0) {

				require_once(dirname(__FILE__) . '/TagsGroups.class.php');

				$tags_groups_list = new TagsGroups_list();

				$topic = $tags_groups_list->get_group_by_id($group);
				$group_url = $topic->get_href();
				$group_name = $topic->get_name();

				foreach ($this->list as &$tag) {
					$tag->set_group_name($group_name);
					$tag->set_group_url($group_url);
				}
				unset($tag);
			}

			return $this->list;
		}else{
			return false;
		}
	}

	/**
	 * Performs db search
	 *
	 * @return	array|false
	 */
	public function search_from_params(array $params) {

		$params = self::get_search_params(null, $params);

		$list = $this->search(
			$params['search_options'],
			$params['search_sort_by'],
			$params['search_order'],
			$params['search_limit'],
			$params['offset']
		);

		if (!empty($list)) {
			$this->set_pagination($params['pg']);
		}

		return $list;
	}

	public static function factory() {
		return new self();
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

		if (isset($in_params['search_advanced_mode'])) {
			$params['search_options']['search_advanced_mode'] = $in_params['search_advanced_mode'];
		}

		if (isset($in_params['set_rating'])) {
			$params['search_options']['set_rating'] = $in_params['set_rating'];
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