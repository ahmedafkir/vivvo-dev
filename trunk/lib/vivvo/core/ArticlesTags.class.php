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
 * ArticlesTags object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @see			vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class ArticlesTags extends vivvo_post_object {

	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * tag_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$tag_id
	 */
	var $tag_id;

	/**
	 * article_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$article_id
	 */
	var $article_id;

	/**
	 * tags_group_id
	 * Database field type:	int(10) unsigned
	 * Null status:		NO
	 *
	 * @var	integer	$tags_group_id
	 */
	public $tags_group_id;

	/**
	 * user_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$user_id
	 */
	var $user_id;


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


	var $name;

	public $sefriendly;

	var $count;

	var $_sql_table = 'articles_tags';

	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = $id;
	}

	/**
	 * Sets {@link $tag_id}
	 *
	 * @param	integer	$tag_id
	 */
	function set_tag_id($tag_id){
		$this->tag_id = $tag_id;
	}

	/**
	 * Sets {@link $article_id}
	 *
	 * @param	integer	$article_id
	 */
	function set_article_id($article_id){
		$this->article_id = $article_id;
	}

	/**
	 * Sets {@link $tags_group_id}
	 *
	 * @param	integer	$tags_group_id
	 */
	function set_tags_group_id($tags_group_id){
		$this->tags_group_id = $tags_group_id;
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

	/**
	 * Sets {@link $user_id}
	 *
	 * @param	integer	$user_id
	 */
	function set_user_id($user_id){
		$this->user_id = $user_id;
	}


	function set_name($name){
		$this->name = $name;
	}

	/**
	 * Sets {@link $sefriendly}
	 *
	 * @param	string	$sefriendly
	 **/
	public function set_sefriendly($sefriendly){
		$this->sefriendly = $sefriendly;
	}

	function set_count($count){
		$this->count = $count;
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
	 * Gets $tag_id
	 *
	 * @return	integer
	 */
	function get_tag_id(){
		return $this->tag_id;
	}
	/**
	 * Gets $article_id
	 *
	 * @return	integer
	 */
	function get_article_id(){
		return $this->article_id;
	}
	/**
	 * Gets $tags_group_id
	 *
	 * @return	integer
	 */
	public function get_tags_group_id(){
		return $this->tags_group_id;
	}

	public function get_topic_id() {
		return $this->tags_group_id;
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

	/**
	 * Gets $user_id
	 *
	 * @return	integer
	 */
	function get_user_id(){
		return $this->user_id;
	}

	private $group = false;

	/**
	 * Gets $group
	 *
	 * @return TagsGroups
	 */
	public function get_group() {

		if ($this->group === false) {

			require_once VIVVO_FS_ROOT . 'lib/vivvo/core/TagsGroups.class.php';

			$group_list = new TagsGroups_list();
			$this->group = $group_list->get_group_by_id($this->tags_group_id);
		}

		return $this->group;
	}

	function get_name(){
		return $this->name;
	}

	/**
	 * Gets $sefriendly
	 *
	 * @return string
	 */
	public function get_sefriendly(){
		return $this->sefriendly;
	}

	function get_count(){
		return $this->count;
	}

	function get_href(){
		return $this->format_href(null, $this->tag_id, $this->name);
	}

	function format_href($sm, $id, $name){

		if ($this->tags_group_id > 0){
			$group = urlencode($this->get_group()->get_url());
			$name = urlencode($this->sefriendly);
		} elseif (strlen($this->group_url)) {
			$group = $group_url;
			$name = urlencode($this->sefriendly);
		} else {
			$group = 'tag';
			$name = urlencode($this->sefriendly);
		}

		return make_proxied_url($group . '/' . $name);
	}
}

/**
 * ArticlesTags list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class ArticlesTags_list extends vivvo_db_list {
	var $_sql_table = 'articles_tags';
	var $post_object_type = 'ArticlesTags';

	function _default_query() {
		$this->_query->set_from(
			VIVVO_DB_PREFIX . $this->_sql_table . ' AS at ' .
			'INNER JOIN ' . VIVVO_DB_PREFIX . 'tags AS t ON at.tag_id = t.id'
		);
		$this->_query->add_fields('at.*');
		$this->_query->add_fields('t.name');
		$this->_query->add_fields('t.sefriendly');
	}

	function add_filter($type, $condition = '') {
		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
			case 'article_id':
			case 'user_id':
			case 'tag_id':
			case 'tags_group_id':
				$this->_query->add_where("(at.$type = '$condition')");
			break;
			case 'not_in_groups':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("(at.tags_group_id NOT IN ($condition))");
			break;
			case 'not_in_tags':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("(at.tag_id NOT IN ($condition))");
			break;
			default:
				$this->_query->add_where("(at.id = '$condition')");
			break;
		}
	}

	/**
	 * Advaced search list generator
	 *
	 * @param	array	$params	Search parameters
	 * @param	string	$order	Order parameters
	 * @param	integer	$limit	Limit
	 * @param	integer	$offset	Offset
	 * @return	array	Array of articles
	 */
	function &search($params, $order = '', $direction = 'ascending', $limit = 0, $offset = 0, $set_list = true) {
		//search_query
		if (isset($params['search_id'])) {
			$this->add_filter('id', $params['search_id']);
		}
		if (isset($params['search_tag_id'])) {
			$this->add_filter('tag_id', $params['search_tag_id']);
		}
		if (isset($params['search_article_id'])) {
			$this->add_filter('article_id', $params['search_article_id']);
		}
		if (isset($params['search_user_id'])) {
			$this->add_filter('user_id', $params['search_user_id']);
		}
		if (isset($params['search_tags_group_id'])) {
			$this->add_filter('tags_group_id', $params['search_tags_group_id']);
		}
		if (isset($params['search_topic_id'])) {
			$this->add_filter('topic_id', $params['search_topic_id']);
		}
		if (isset($params['search_not_in_groups'])) {
			$this->add_filter('not_in_groups', $params['search_not_in_groups']);
		}
		if (isset($params['search_not_topic_id'])) {
			$this->add_filter('not_in_groups', $params['search_not_topic_id']);
		}
		if (isset($params['search_not_in_tags'])) {
			$this->add_filter('not_in_tags', $params['search_not_in_tags']);
		}

		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		if (in_array($order, array('id', 'tag_id', 'article_id', 'user_id', 'tags_group_id'))) {
			$this->_query->add_order('at.' . $order . $search_direction);
		} else {
			$order = 'id';
			$this->_query->add_order('at.id DESC');
		}

		$limit = (int) $limit;
		$this->_query->set_limit($limit);
		$offset = (int) $offset;
		$this->_query->set_offset($offset);
		$this->_default_query(true);

		if ($set_list){
			$this->set_list();
			return $this->list;
		}
	}

	function &get_articles_tags_by_id($id){
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

	function &get_articles_tags_by_tag_id($id){
		$id = (int) $id;
		$this->_default_query();
		$this->add_filter('tag_id' , $id);
		$this->set_list();
		if (!empty($this->list)){
			return current($this->list);
		}else{
			return false;
		}
	}

	function &get_all_articles_tags_by_tag_id($id){
		$id = (int) $id;
		$this->_default_query();
		$this->add_filter('tag_id' , $id);
		$this->set_list();
		if (!empty($this->list)){
			return $this->list;
		}else{
			return false;
		}
	}

	function &get_articles_tags_by_article($id, $system = false) {
		$id = (int)$id;
		$this->_default_query();
		$this->add_filter('article_id' , $id);
		$this->add_filter($system ? 'not_in_groups' : 'tags_group_id', 0);
		$this->_query->add_group_by($system ? 'at.id' : 'at.tag_id');
		$this->_query->add_fields('COUNT(DISTINCT at.user_id) AS count');
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		} else {
			return false;
		}
	}

	function get_articles_tags_by_topic_id($article_id, $topic_id) {

		$tag_list = array();

		require_once(dirname(__FILE__) . '/TagsGroups.class.php');

		$tags_groups_list = new TagsGroups_list();

		$groups = $tags_groups_list->get_groups_by_ids($topic_id);

		if (!$groups) return false;

		foreach ($groups as $group) {

			$group_url = $group->get_href();
			$group_name = $group->get_name();

			$this->_query->reset_query();
			$this->_default_query();
			$this->add_filter('article_id', $article_id);
			$this->add_filter('tags_group_id', $group->get_id());

			$this->set_list();

			if ( !empty($this->list) ) {

				foreach ($this->list as &$tag) {
					$tag->set_group_url($group_url);
					$tag->set_group_name($group_name);
				}
			}

			$tag_list = array_merge($tag_list, $this->list);
		}

		$this->list = $tag_list;

		return empty($this->list) ? false : $this->list;
	}

	function &get_by_article_user($article_id, $user_id, $system = false){
		$article_id = (int)$article_id;
		$user_id = (int)$user_id;
		$this->_default_query();
		$this->add_filter('article_id', $article_id);
		$this->add_filter('user_id', $user_id);
		$this->add_filter($system ? 'not_in_groups' : 'tags_group_id', 0);

		$this->set_list();

		if (!empty($this->list)) {
			return $this->list;
		} else {
			return false;
		}
	}

	function &get_by_not_in_tag_article_user($not_in_tags,$article_id,$user_id){
		$not_in_tags = secure_sql($not_in_tags);
		$article_id = (int) $article_id;
		$user_id = (int) $user_id;

		$this->_default_query();
		$this->add_filter('not_in_tags' , $not_in_tags);
		$this->add_filter('article_id' , $article_id);
		$this->add_filter('user_id' , $user_id);

		$this->set_list();

		if (!empty($this->list)){
			return $this->list;
		}else{
			return false;
		}
	}
}

?>