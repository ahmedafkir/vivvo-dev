<?php
/* =============================================================================
 * $Revision: 5370 $
 * $Date: 2010-05-24 11:42:36 +0200 (Mon, 24 May 2010) $
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
	 * Pages object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Ivan Dilber <idilber@spoonlabs.com>
	 * @see			vivvo_post_object
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class AssetFileTypes extends vivvo_post_object {

		/**
		 * id
		 * @var	integer	$id
		 */
		var $id;
        /**
		 * file type
		 * @var	string $type
		 */
		var $type;
		/**
		 * comma separated list of extensions (for file list filters)
		 * @var	string $extension
		 */
		var $extensions;
		/**
		 * prefix to add to the file path for files of this type
		 * [reserved for future use]
		 * @var	string	$path_prefix
		 */
		var $path_prefix;

		function set_id($id) {
			$this->id = $id;
		}
		function set_type($type) {
			$this->type = $type;
		}
		function set_extensions($ext) {
			$this->extensions = $ext;
		}
		function set_path_prefix($prefix) {
			$this->path_prefix = $prefix;
		}

		function get_id() {
			return $this->id;
		}
		function get_type() {
			return $this->type;
		}
		function get_extensions() {
			return $this->extensions;
		}
		function get_path_prefix() {
			return $this->path_prefix;
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
	class AssetFileTypes_list extends vivvo_db_list {
		var $_sql_table = 'asset_file_types';
		var $post_object_type = 'AssetFileTypes';

		function _default_query(){
			$this->_query->reset_query();
			$this->_query->set_from(VIVVO_DB_PREFIX . 'asset_file_types AS t');
			$this->_query->add_fields('t.*');
		}

		function add_filter($type, $cond = ''){

			$condition = secure_sql($cond);
			switch ($type){
				case 'id':
					$condition = secure_sql_in($condition);
					$this->_query->add_where('(t.id IN (' . $condition . '))');
					break;
				case 'notid':
					$condition = secure_sql_in($condition);
					$this->_query->add_where('(t.id NOT IN (' . $condition . '))');
					break;
				case 'type':
					$this->_query->add_where("(t.type='$condition')");
					break;
				case 'ext':
					$this->_query->add_where("(t.extensions LIKE '%$condition%')");
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
		 * @return	array	Array of content_items
		 */
		function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
			$this->_default_query(true);

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

			if (isset($params['search_type'])){
				$this->add_filter('type',$params['search_type']);
			}
			if (isset($params['search_ext'])){
				$this->add_filter('ext',$params['search_ext']);
			}

			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'type':
					$this->_query->add_order('type' . $search_direction);
					break;
				case 'extensions':
					$this->_query->add_order('extensions' . $search_direction);
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


			if ($set_list){
				$this->set_list();
				return $this->list;
			}
		}

		function get_filetype_by_id($id){
			$id = (int)$id;
			$this->search(array('search_id'=>$id));
			return empty($this->list)? false : current($this->list);
		}

		function get_filetype_by_type($sefriendly){
			$this->search(array('search_sefriendly'=>$sefriendly));
			return empty($this->list)? false : current($this->list);
		}

		function get_filetype_by_ext($ext){
			$this->search(array('search_ext'=>$ext));
			return empty($this->list)? false : current($this->list);
		}

	} //end_class


#EOF