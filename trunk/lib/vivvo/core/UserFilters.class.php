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
 * UsersFilters object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see		vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class UserFilters extends vivvo_post_object {

	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * user_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$user_id
	 */
	var $user_id;

	/**
	 * query
	 * Database field type:	text
	 * Null status:		YES
	 *
	 * @var	string	$query
	 */
	var $query;

	/**
	 * section
	 * Database field type:	varchar(45)
	 * Null status:		NO
	 *
	 * @var	string	$section
	 */
	var $section;

	/**
	 * name
	 * Database field type:	varchar(50)
	 * Null status:		NO
	 *
	 * @var	string	$name
	 */
	var $name;

	/**
	 * name
	 * Database field type:	varchar(50)
	 * Null status:		NO
	 *
	 * @var	string	$html_tag_id
	 */
	var $html_tag_id;

	/**
	 * name
	 * Database field type:	integer
	 * Null status:		NO
	 *
	 * @var	integer	$order_number
	 */
	var $order_number;

	var $_sql_table = 'user_filters';

	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = $id;
	}

	/**
	 * Sets {@link $user_id}
	 *
	 * @param	integer	$user_id
	 */
	function set_user_id($user_id){
		if ((int) $user_id > 0 ){
			$this->user_id = (int) $user_id;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $query}
	 *
	 * @param	string	$query
	 */
	function set_query($query){
		$this->query = $query;
		return true;
	}

	/**
	 * Sets {@link $service}
	 *
	 * @param	string	$service
	 */
	function set_section($section){
		if ($section != ''){
			$this->section = $section;
			return true;
		}else{
			return false;
		}

	}

	/**
	 * Sets {@link $name}
	 *
	 * @param	string	$name
	 */
	function set_name($name){
		if ($name != ''){
			$this->name = $name;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $name}
	 *
	 * @param	string	$html_tag_id
	 */
	function set_html_tag_id($html_tag_id){
		if ($html_tag_id != ''){
			$this->html_tag_id = $html_tag_id;
			return true;
		}else{
			return false;
		}
	}

	/**
	 * Sets {@link $order_number}
	 *
	 * @param	integer	$order_number
	 */
	function set_order_number($order_number){
		$this->order_number = $order_number;
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
	 * Gets $user_id
	 *
	 * @return	integer
	 */
	function get_user_id(){
		return $this->user_id;
	}
	/**
	 * Gets $query
	 *
	 * @return	string
	 */
	function get_query(){
		return $this->query;
	}

	function get_query_array(){
		return unserialize(base64_decode($this->query));
	}

	/**
	 * Gets $service
	 *
	 * @return	string
	 */
	function get_service(){
		return $this->service;
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
	 * Gets $html_tag_id
	 *
	 * @return	string
	 */
	function get_html_tag_id(){
		return $this->html_tag_id;
	}

	/**
	 * Gets $order_number
	 *
	 * @return	integer
	 */
	function get_order_number(){
		return $this->order_number;
	}
}

/**
 * UsersFilters list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class UserFilters_list extends vivvo_db_list {
	var $_sql_table = 'user_filters';
	var $post_object_type = 'UserFilters';

	function _default_query(){
		$this->_query->set_from(VIVVO_DB_PREFIX . 'user_filters ');

		if (is_array($this->_fields) && !empty($this->_fields)){
			foreach ($this->_fields as $field){
				$this->_query->add_fields($field);
			}
		}else{
			$this->_query->add_fields('*');
		}

	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
				$this->_query->add_where('(id = \'' . $condition . '\')');
			break;
			case 'user_id':
				$this->_query->add_where('(user_id = \'' . $condition . '\')');
			break;
			case 'query':
				$this->_query->add_where('(query = \'' . $condition . '\')');
			break;
			case 'section':
				$this->_query->add_where('(section = \'' . $condition . '\')');
			break;
			case 'name':
				$this->_query->add_where('(name = \'' . $condition . '\')');
			break;
			case 'html_tag_id':
				$this->_query->add_where('(html_tag_id = \'' . $condition . '\')');
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
	function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0, $set_list = true){
		//search_query
		if (isset($params['search_id'])){
			$this->add_filter('id',$params['search_id']);
		}
		if (isset($params['search_user_id'])){
			$this->add_filter('user_id',$params['search_user_id']);
		}
		if (isset($params['search_query'])){
			$this->add_filter('query',$params['search_query']);
		}
		if (isset($params['search_section'])){
			$this->add_filter('section',$params['search_section']);
		}
		if (isset($params['search_name'])){
			$this->add_filter('name',$params['search_name']);
		}

		if (isset($params['search_html_tag_id'])){
			$this->add_filter('html_tag_id',$params['search_html_tag_id']);
		}


		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'id':
				$this->_query->add_order('id' . $search_direction);
				break;
			case 'user_id':
				$this->_query->add_order('user_id' . $search_direction);
				break;
			case 'query':
				$this->_query->add_order('query' . $search_direction);
				break;
			case 'section':
				$this->_query->add_order('section' . $search_direction);
				break;
			case 'name':
				$this->_query->add_order('name' . $search_direction);
				break;
			case 'html_tag_id':
				$this->_query->add_order('html_tag_id' . $search_direction);
				break;
			case 'order_number':
				$this->_query->add_order('order_number' . $search_direction);
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

		if ($set_list){
			$this->set_list();
			return $this->list;
		}
	}

	function get_UserFilter_by_id($filer_id){
		$filer_id = (int) $filer_id;
		$this->search(array('search_id'=>$filer_id));
		if (empty($this->list)){
			return false;
		}else{
			return current($this->list);
		}
	}

}

?>