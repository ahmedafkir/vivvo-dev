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
 * TagsGroups object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @see			vivvo_post_object
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @version		Vivvo Lite - Generic Database Engine
 */
class TagsToTagsGroups extends vivvo_post_object {

	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:		NO
	 * @var	integer	$id
	 */
	public $id;

	/**
	 * tags_group_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 * @var	integer	$id
	 */
	public $tags_group_id;

	/**
	 * tag_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 * @var	integer	$id
	 */
	public $tag_id;

	var $_sql_table = 'tags_to_tags_groups';

	/**
	 * Sets {@link $id}
	 * @param	integer	$id
	 */
	public function set_id($id) {
		$this->id = $id;
	}

	/**
	 * Sets {@link $tags_group_id}
	 * @param	integer	$tags_group_id
	 */
	public function set_tags_group_id($tags_group_id) {
		$this->tags_group_id = $tags_group_id;
	}

	/**
	 * Sets {@link $tag_id}
	 * @param	integer	$tag_id
	 */
	public function set_tag_id($tag_id) {
		$this->tag_id = $tag_id;
	}

	/**
	 * Gets $id
	 * @return	integer
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets $tags_group_id
	 * @return	integer
	 */
	public function get_tags_group_id() {
		return $this->tags_group_id;
	}

	/**
	 * Gets $tag_id
	 * @return	integer
	 */
	public function get_tag_id() {
		return $this->tag_id;
	}

	function on_delete($post_master){

		// if tag is not related to any other group, delete it
		$list = new TagsToTagsGroups_list();

		//check if tag is related to any other group, if not remove it
		if( !$list->search( array( 'search_tag_id'=> $this->tag_id, 'search_not_id'=> $this->id )) ) {
			//delete directly to avoid calling on_delete actions recursively
			$post_master->sql_delete_list('articles_tags', "tag_id=$this->tag_id");
			$post_master->sql_delete_list('tags', "id=$this->tag_id");
		}

	}

}

/**
 * TagsToTagsGroups list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Ivan Dilber <idilber@spoonlabs.com>
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class TagsToTagsGroups_list extends vivvo_db_list {

	var $_sql_table = 'tags_to_tags_groups';
	var $post_object_type = 'TagsToTagsGroups';

	function _default_query() {
		$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table);
		$this->_query->add_fields('*');
	}

	function add_filter($type, $condition = '') {
		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
			case 'tag_id':
			case 'tags_group_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("($type IN ($condition))");
			break;
			case 'not_id':
				$this->_query->add_where("(id<>'$condition')");
			break;
			default:
				$condition = secure_sql_in($condition);
				$this->_query->add_where("(id IN ($condition))");
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
		if (isset($params['search_id'])){
			$this->add_filter('id', $params['search_id']);
		}
		if (isset($params['search_not_id'])){
			$this->add_filter('not_id', $params['search_not_id']);
		}
		if (isset($params['search_tag_id'])){
			$this->add_filter('tag_id', $params['search_tag_id']);
		}
		if (isset($params['search_tags_group_id'])){
			$this->add_filter('tags_group_id', $params['search_tags_group_id']);
		}
		if (isset($params['search_topic_id'])){
			$this->add_filter('tags_group_id', $params['search_topic_id']);
		}

		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		if (in_array($order, array('id', 'tag_id', 'tags_group_id'))) {
			$this->_query->add_order($order . $search_direction);
		} else {
			$order = 'id';
			$this->_query->add_order('id DESC');
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

	public function &get_rel_by_id($id) {
		$this->_default_query();
		$this->add_filter('id', (int)$id);
		$this->set_list();
		if (!empty($this->list)) {
			return current($this->list);
		}
		return intval(false);
	}

	public function &get_rel($tag_id, $group_id) {
		$this->_default_query();
		$this->add_filter('tag_id', $tag_id);
		$this->add_filter('tags_group_id', $group_id);

		$this->set_list();
		if (!empty($this->list)) {
			return current($this->list);
		}
		return intval(false);
	}

	public function &get_rel_by_tag_id($tag_id) {
		$this->_default_query();
		$this->add_filter('tag_id', $tag_id);
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		}
		return intval(false);
	}

	public function &get_rel_by_group_id($group_id) {
		$this->_default_query();
		$this->add_filter('tags_group_id', $group_id);
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		}
		return intval(false);
	}

	public function get_all_rels(){
		$this->_default_query();
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		}
		return array();
	}
}
?>