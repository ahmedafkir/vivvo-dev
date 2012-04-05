<?php
/* =============================================================================
 * $Revision: 5127 $
 * $Date: 2010-04-26 10:03:56 +0200 (Mon, 26 Apr 2010) $
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
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see			vivvo_post_object
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class Pages extends vivvo_post_object {

		/**
		 * id
		 * Database field type:	smallint(5) unsigned
		 * Null status:		NO
		 *
		 * @var	integer	$id
		 */
		var $id;

		/**
		 * title
		 * Database field type:	varchar(255)
		 * Null status:		NO
		 *
		 * @var	string	$title
		 */
		var $title;

		/**
		 * body
		 * Database field type:	text
		 * Null status:		YES
		 *
		 * @var	string	$body
		 */
		var $body;

		/**
		 * hide
		 * Database field type:	int(1)
		 * Null status:		NO
		 *
		 * @var	string	$hide
		 */
		var $hide;

		/**
		 * sefriendly
		 * Database field type:	varchar(255)
		 * Null status:		NO
		 *
		 * @var	string	$sefriendly
		 */
		var $sefriendly;

		/**
		 * template
		 * Database field type:	varchar(255)
		 * Null status:		NO
		 *
		 * @var	string	$template
		 */
		var $template;

		/**
		 * category_id
		 * Database field type:	int(11) unsigned
		 * Null status:		YES
		 *
		 * @var	integer	$category_id
		 */
		var $category_id;

		/**
		 * order_number
		 * Database field type:	smallint(5) unsigned
		 * Null status:		NO
		 *
		 * @var	integer	$order_number
		 */
		var $order_number;

		var $_sql_table = 'pages';

		/**
		 * Sets {@link $id}
		 *
		 * @param	integer	$id
		 */
		function set_id($id){
			$this->id = $id;
			return true;
		}

		/**
		 * Sets {@link $title}
		 *
		 * @param	string	$title
		 */
		function set_title($title){
			$title = trim($title);
			if($title != ''){
				$this->title = $title;
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Sets {@link $body}
		 *
		 * @param	string	$body
		 */
		function set_body($body){
			$this->body = $body;
			return true;
		}

		/**
		 * Sets {@link $hide}
		 *
		 * @param	integer	$hide
		 */
		function set_hide($hide){
			if ($hide == '1'){
				$this->hide = '1';
			}else{
				$this->hide = '0';
			}

			return true;
		}

		/**
		 * Sets {@link $sefriendly}
		 *
		 * @param	string	$sefriendly
		 */
		function set_sefriendly($sefriendly){
			if ($sefriendly != ''){
				$this->sefriendly = $sefriendly;
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Sets {@link $template}
		 *
		 * @param	string	$template
		 */
		function set_template($template){
			$this->template = $template;
			return true;
		}

		/**
		 * Sets {@link $category_id}
		 *
		 * @param	integer	$category_id
		 */
		function set_category_id($category_id){
			$this->category_id = $category_id;
			return true;
		}

		/**
		 * Sets {@link $order_number}
		 *
		 * @param	integer	$order_number
		 */
		function set_order_number($order_number){
			$this->order_number = $order_number;
			return true;
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
		 * Gets $title
		 *
		 * @return	string
		 */
		function get_title(){
			return $this->title;
		}
		/**
		 * Gets $body
		 *
		 * @return	string
		 */
		function get_body(){
			return $this->body;
		}

		/**
		 * Gets $hide
		 *
		 * @return	integer
		 */
		function get_hide(){
			return $this->hide;
		}

		/**
		 * Gets $sefriendly
		 *
		 * @return	string
		 */
		function get_sefriendly(){
			return $this->sefriendly;
		}
		/**
		 * Gets $template
		 *
		 * @return	string
		 */
		function get_template(){
			return $this->template;
		}
		/**
		 * Gets $category_id
		 *
		 * @return	integer
		 */
		function get_category_id(){
			return $this->category_id;
		}
		/**
		 * Gets $order_number
		 *
		 * @return	integer
		 */
		function get_order_number(){
			return $this->order_number;
		}

		/**
		 * Gets page href
		 *
		 * @return	integer
		 */
		function get_href() {
			return $this->format_href(null, $this->id, $this->sefriendly);
		}

		function format_href($sm, $id, $sefriendly) {
			if ($sefriendly != '') {
				return make_proxied_url($sefriendly . '.html');
			} else {
				return make_proxied_url('404.html');
			}
		}
	}

	/**
	 * Pages list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @see			vivvo_db_paged_list
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class Pages_list extends vivvo_db_list {
		var $_sql_table = 'pages';
		var $post_object_type = 'Pages';

		function _default_query(){
			$this->_query->set_from(
									VIVVO_DB_PREFIX . 'pages ');
			$this->_query->add_fields('*');
		}

		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$condition = secure_sql_in($condition);
					$this->_query->add_where('(id IN (' . $condition . '))');
				break;
				case 'notid':
					$condition = secure_sql_in($condition);
					$this->_query->add_where('(id NOT IN (' . $condition . '))');
				break;
				case 'title':
					$this->_query->add_where('(title = \'' . $condition . '\')');
				break;
				case 'body':
					$this->_query->add_where('(body = \'' . $condition . '\')');
				break;
				case 'hide':
					$this->_query->add_where('(hide = \'' . $condition . '\')');
				break;
				case 'sefriendly':
					$this->_query->add_where('(sefriendly = \'' . $condition . '\')');
				break;
				case 'template':
					$this->_query->add_where('(template = \'' . $condition . '\')');
				break;
				case 'order_number':
					$this->_query->add_where('(order_number = \'' . $condition . '\')');
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
			//search_query
			if (isset($params['search_id'])){
				if (is_array($params['search_id'])){
					if (!in_array(0, $params['search_id'])){
						$params['search_id'] = implode(',', $params['search_id']);
						$this->add_filter('id',$params['search_id']);
					}
				}else{
					if ($params['search_id'] != 0){
						$this->add_filter('id',$params['search_id']);
					}
				}
			}

			if (isset($params['search_notid'])){
				$this->add_filter('notid',$params['search_notid']);
			}

			if (isset($params['search_title'])){
				$this->add_filter('title',$params['search_title']);
			}
			if (isset($params['search_body'])){
				$this->add_filter('body',$params['search_body']);
			}
			if (isset($params['search_hide'])){
				$this->add_filter('hide',$params['search_hide']);
			}
			if (isset($params['search_sefriendly'])){
				$this->add_filter('sefriendly',$params['search_sefriendly']);
			}
			if (isset($params['search_template'])){
				$this->add_filter('template',$params['search_template']);
			}
			if (isset($params['search_order_number'])){
				$this->add_filter('order_number',$params['search_order_number']);
			}


			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'title':
					$this->_query->add_order('title' . $search_direction);
					break;
				case 'body':
					$this->_query->add_order('body' . $search_direction);
					break;
				case 'hide':
					$this->_query->add_order('hide' . $search_direction);
					break;
				case 'sefriendly':
					$this->_query->add_order('sefriendly' . $search_direction);
					break;
				case 'template':
					$this->_query->add_order('template' . $search_direction);
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

		function get_pages_by_id($id){
			$id = (int)$id;
			$this->search(array('search_id'=>$id));
			if (empty($this->list)){
				return false;
			}else{
				return current($this->list);
			}
		}

		function get_pages_by_sefriendly($sefriendly){
			$this->search(array('search_sefriendly'=>$sefriendly));
			if (empty($this->list)){
				return false;
			}else{
				return current($this->list);
			}
		}
	}
?>