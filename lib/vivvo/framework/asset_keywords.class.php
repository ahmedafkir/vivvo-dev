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
	 * AssetKeywords object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 * @see			vivvo_post_object
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class AssetKeywords extends vivvo_post_object {

		var $_sql_table = 'asset_keywords';

		/**
		 * id
		 * @var	integer	$id
		 */
		var $id;
        /**
		 * asset id (foreign key)
		 * @var	integer	$asset_id
		 */
		var $asset_id;
		/**
		 * keyword value
		 * @var	string $keyword
		 */
		var $keyword;

		function set_id($id) {
			$this->id = $id;
		}
		function set_asset_id($id) {
			$this->asset_id = $id;
		}
		function set_keyword($keyword) {
			$this->keyword = $keyword;
		}

		function get_id() {
			return $this->id;
		}
		function get_asset_id() {
			return $this->asset_id;
		}
		function get_keyword() {
			return $this->keyword;
		}

	} //end_class

	/**
	 * AssetFiles DB list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 * @see			vivvo_db_paged_list
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class AssetKeywords_list extends vivvo_db_paged_list {
		var $_sql_table = 'asset_keywords';
		var $post_object_type = 'AssetKeywords';

		function _default_query(){
			$this->_query->set_from(VIVVO_DB_PREFIX . 'asset_keywords AS k');
			$this->_query->add_fields('k.*');
		}

		function add_filter($type, $cond = ''){

			$condition = secure_sql($cond);
			switch ($type){
				case 'id':
					$condition = secure_sql_in($condition);
					$this->_query->add_where("(k.id IN ($condition))");
					break;
				case 'notid':
					$condition = secure_sql_in($condition);
					$this->_query->add_where("(k.id NOT IN ($condition))");
					break;
				case 'asset_id':
					$this->_query->add_where("(k.asset_id='$condition')");
					break;
				case 'asset_id_list':
					$condition = secure_sql_in($condition);
					$this->_query->add_where("(k.asset_id IN ($condition))");
					break;
				case 'keyword':
					$this->_query->add_where("(k.keyword='$condition')");
					break;
				case 'keyword_starts_with':
					$this->_query->add_where("(k.keyword LIKE '$condition%')");
					break;
				case 'fulltext':
					$this->_query->add_where("MATCH (k.keyword) AGAINST ('$condition' IN BOOLEAN MODE)");
					break;
				case 'like':
					$condition = escape_sql_like($condition);
					$this->_query->add_where("k.keyword LIKE '%$condition%'");
			}
		}

		/**
		 * Advaced search list generator
		 *
		 * @param	array	$params	Search parameters
		 * @param	string	$order	Order parameters
		 * @param	integer	$limit	Limit
		 * @param	integer	$offset	Offset
		 * @return	array	Array of content_items
		 */
		function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
			//search_query
			if (!empty($params['search_id'])){
				if (is_array($params['search_id']))
					$params['search_id'] = implode(',', $params['search_id']);
				$this->add_filter('id',$params['search_id']);
			}

			if (isset($params['search_notid'])){
				if (is_array($params['search_notid']))
					$params['search_notid'] = implode(',', $params['search_notid']);
				$this->add_filter('notid',$params['search_notid']);
			}

			if (isset($params['search_asset_id'])){
				$this->add_filter('asset_id',$params['search_asset_id']);
			}

			if (isset($params['search_asset_id_list'])){
				if (is_array($params['search_asset_id_list']))
					$params['search_asset_id_list'] = implode(',', $params['search_asset_id_list']);
				$this->add_filter('asset_id_list',$params['search_asset_id_list']);
			}

			if (isset($params['search_keyword'])){
				$this->add_filter('keyword',$params['search_keyword']);
			}

			if (isset($params['search_keyword_starts_with'])){
				$this->add_filter('keyword_starts_with',$params['search_keyword_starts_with']);
			}

			if (isset($params['search_fulltext'])){
				$this->add_filter('fulltext', $params['search_fulltext']);
			}

			if (isset($params['search_like'])){
				$this->add_filter('like', $params['search_like']);
			}

			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'asset_id':
					$this->_query->add_order('asset_id' . $search_direction);
					break;
				case 'keyword':
					$this->_query->add_order('keyword' . $search_direction);
					break;
				default:
					$order = 'id';
					$this->_query->add_order('id' . ' DESC');
					break;
			}

			$limit = (int) $limit;
			$this->_query->set_limit($limit);
			$offset = (int) $offset;
			$this->_query->set_offset($offset);
			$this->_default_query(true);

#			error_log( 'KEY QUERY: '.$this->_query->get_query() );

			if ($set_list){
				$this->set_list();
				return $this->list;
			}
		}

		function get_keyword_by_id($id){
			$id = (int)$id;
			$this->search(array('search_id'=>$id));
			if (empty($this->list)){
				return false;
			}else{
				return current($this->list);
			}
		}

		function get_keywords_by_asset_id($id){
			$this->search(array('search_asset_id'=>$id));
			if (empty($this->list)){
				return false;
			}else{
				return $this->list;
			}
		}

		function get_keywords_by_fulltext($keyword) {

			$this->search( array('search_fulltext' => $keyword) );

			return empty($this->list)? false : $this->list;
		}

		function get_keywords_by_name($keyword) {

			if (is_array($keyword)) {
				$keyword = reset($keyword);
			} elseif (strpos($keyword, ',') !== false) {
				$keyword = trim(reset(explode(',', $keyword)));
			}

			$this->search(array('search_like' => $keyword));

			return empty($this->list)? false : $this->list;
		}

	}//end_class


#EOF