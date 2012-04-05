<?php

/**
 * ArticleImages object
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see		vivvo_post_object
 * @version		Vivvo Lite - Generic Database Engine
 */
class ArticleImages extends vivvo_post_object {

	/**
	 * Name of table without tblprefix
	 *
	 * @var   string
	 */
	var $_sql_table = 'article_images';

	/**
	 * id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$id
	 */
	var $id;

	/**
	 * article_id
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$article_id
	 */
	var $article_id;

	/**
	 * real_path
	 * Database field type:	varchar(255)
	 * Null status:		NO
	 *
	 * @var	string	$real_path
	 */
	var $real_path;

	/**
	 * title
	 * Database field type:	text
	 * Null status:		YES
	 *
	 * @var	string	$title
	 */
	var $title;

	/**
	 * order_number
	 * Database field type:	int(11)
	 * Null status:		NO
	 *
	 * @var	integer	$order_number
	 */
	var $order_number;



	/**
	 * Sets {@link $id}
	 *
	 * @param	integer	$id
	 */
	function set_id($id){
		$this->id = $id;
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
	 * Sets {@link $real_path}
	 *
	 * @param	string	$real_path
	 */
	function set_real_path($real_path){
		$this->real_path = $real_path;
	}

	/**
	 * Sets {@link $title}
	 *
	 * @param	string	$title
	 */
	function set_title($title){
		$this->title = $title;
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
	 * Gets $article_id
	 *
	 * @return	integer
	 */
	function get_article_id(){
		return $this->article_id;
	}

	/**
	 * Gets $real_path
	 *
	 * @return	string
	 */
	function get_real_path(){
		return $this->real_path;
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
	 * Gets $order_number
	 *
	 * @return	integer
	 */
	function get_order_number(){
		return $this->order_number;
	}

}

/**
 * ArticleImages list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class ArticleImages_list extends vivvo_db_paged_list {
	var $_sql_table = 'article_images';
	var $post_object_type = 'ArticleImages';

	function _default_query(){
		$this->_query->set_from(
								VIVVO_DB_PREFIX . 'article_images ');
		$this->_query->add_fields('*');
	}

	function add_filter($type, $condition = ''){

		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
				$this->_query->add_where('(id = \'' . $condition . '\')');
			break;
			case 'article_id':
				$this->_query->add_where('(article_id = \'' . $condition . '\')');
			break;
			case 'real_path':
				$this->_query->add_where('(real_path = \'' . $condition . '\')');
			break;
			case 'title':
				$this->_query->add_where('(title = \'' . $condition . '\')');
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
	function &search($params, $order='', $direction = 'ascending', $limit =0, $offset =0){
		//search_query
		if (isset($params['search_id'])){
			$this->add_filter('id', $params['search_id']);
			$this->add_generator_option('search_id', $params['search_id']);
		}
		if (isset($params['search_article_id'])){
			$this->add_filter('article_id', $params['search_article_id']);
			$this->add_generator_option('search_article_id', $params['search_article_id']);
		}
		if (isset($params['search_real_path'])){
			$this->add_filter('real_path', $params['search_real_path']);
			$this->add_generator_option('search_real_path', $params['search_real_path']);
		}
		if (isset($params['search_title'])){
			$this->add_filter('title', $params['search_title']);
			$this->add_generator_option('search_title', $params['search_title']);
		}
		if (isset($params['search_order_number'])){
			$this->add_filter('order_number', $params['search_order_number']);
			$this->add_generator_option('search_order_number', $params['search_order_number']);
		}


		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'id':
				$this->_query->add_order('id' . $search_direction);
				break;
			case 'article_id':
				$this->_query->add_order('article_id' . $search_direction);
				break;
			case 'real_path':
				$this->_query->add_order('real_path' . $search_direction);
				break;
			case 'title':
				$this->_query->add_order('title' . $search_direction);
				break;
			case 'order_number':
				$this->_query->add_order('order_number' . $search_direction);
				break;

			default:
				$order = 'id';
				$this->_query->add_order('id' . ' DESC');
				break;
		}
		$this->add_order_option($order, $direction);

			$limit = (int) $limit;
			$this->_query->set_limit($limit);
			$offset = (int) $offset;
			$this->_query->set_offset($offset);
			$this->_default_query(true);

			$this->set_list();
			return $this->list;
	}

}

?>