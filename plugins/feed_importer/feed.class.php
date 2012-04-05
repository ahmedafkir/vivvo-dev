<?php
/* =============================================================================
 * $Revision: 3378 $
 * $Date: 2008-12-16 18:28:26 +0100 (Tue, 16 Dec 2008) $
 * 
 * Vivvo CMS v4.1.6 (build 4214) 
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 * 
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * ============================================================================ 
 */

 

/** 
 * Feeds object 
 * 
 * @license		http://www.vivvo.net/license.php 
 * @copyright	SpoonLabs 
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com> 
 * @author		Slobodan Utvic <utvara@spoonlabs.com> 
 * @see			vivvo_post_object 
 * @version		Vivvo Lite - Generic Database Engine
 */
class Feeds extends vivvo_post_object {

	/**
	 * id 
	 * Database field type:	int(11) 
	 * Null status:		NO
	 *
	 * @var	integer	$id
	 */
	var $id;
	
	/**
	 * category_id 
	 * Database field type:	int(11) 
	 * Null status:		NO
	 *
	 * @var	integer	$category_id
	 */
	var $category_id;
	
	/**
	 * feed 
	 * Database field type:	text 
	 * Null status:		NO
	 *
	 * @var	string	$feed
	 */
	var $feed;
	
	/**
	 * count 
	 * Database field type:	int(11) 
	 * Null status:		YES
	 *
	 * @var	integer	$count
	 */
	var $count;
	
	/**
	 * favicon 
	 * Database field type:	text 
	 * Null status:		YES
	 *
	 * @var	string	$favicon
	 */
	var $favicon;
	
	/**
	 * author
	 * Database field type:	text 
	 * Null status:		YES
	 * 
	 * @var string $author
	 */
	var $author;
	
	var $_sql_table = 'Feeds';

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
	 * Sets {@link $category_id}
	 *
	 * @param	integer	$category_id
	 */
	function set_category_id($category_id){
		if ($category_id == ''){
			return false;
		}else{
			$this->category_id = $category_id;
			return true;
		}
	}

	/**
	 * Sets {@link $feed}
	 *
	 * @param	string	$feed
	 */
	function set_feed($feed){
		if ($feed == ''){
			return false;
		}else{
			$this->feed = $feed;
			return true;
		}
		
	}

	/**
	 * Sets {@link $count}
	 *
	 * @param	integer	$count
	 */
	function set_count($count){
		$this->count = $count;
		return true;
	}

	/**
	 * Sets {@link $favicon}
	 *
	 * @param	string	$favicon
	 */
	function set_favicon($favicon){
		$this->favicon = $favicon;
		return true;
	}
	
	/**
	 * Sets {@link $author}
	 * 
	 * @param	string	$author
	 */
	function set_author($author){
		$this->author = $author;
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
	 * Gets $category_id
	 *
	 * @return	integer
	 */
	function get_category_id(){
		return $this->category_id;
	}
	/** 
	 * Gets $feed
	 *
	 * @return	string
	 */
	function get_feed(){
		return $this->feed;
	}
	/** 
	 * Gets $status
	 *
	 * @return	integer
	 */
	function get_count(){
		return $this->count;
	}
	
	/** 
	 * Gets $favicon
	 *
	 * @return	string
	 */
	function get_favicon(){
		return $this->favicon;
	}
	
	/**
	 * Gets $author
	 * 
	 * @return	string
	 */
	function get_author(){
		return $this->author;
	}
	
	function get_category_name(){
		$category =& $this->_site_manager->get_categories();
		if (isset($category->list[$this->category_id])){
			return $category->list[$this->category_id]->get_category_name();
		}else{
			return '';
		}
	}
	

	function get_articles(){
		$sm =& $this->_site_manager;
		require_once(VIVVO_FS_INSTALL_ROOT . '/lib/simplepie/simplepie.php');
		$feed_r = new SimplePie();
		$feed_r->enable_cache(false);
		
		$feed_r->set_feed_url($this->get_feed());
		$feed_r->init();
		
		foreach($feed_r->get_items() as $item){
			$status = true;
			$guid = $item->get_item_tags('','guid');
			$guid = $guid[0]['data'];
			if ($guid != '' ){
				$sql ='SELECT count( * ) as count FROM '.VIVVO_DB_PREFIX.'Articles WHERE feed_data = \''.md5($guid).'\' LIMIT 1';
				$feed_data = md5($guid);
			}else{
				$sql ='SELECT count( * ) as count FROM '.VIVVO_DB_PREFIX.'Articles WHERE feed_data = \''.md5($item->get_title() . $item->get_permalink()).'\'LIMIT 1';
				$feed_data = md5($item->get_title() . $item->get_permalink());
			}
			$res =& $sm->_db->query($sql);
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			if ($row['count']){
				$status = false;
			}
			
			if ($status){
				require_once(VIVVO_FS_INSTALL_ROOT . '/lib/vivvo/core/Articles.class.php');
				require_once(VIVVO_FS_FRAMEWORK. 'vivvo_post.php');
				$article = new Articles($sm);
				
				if ($this->get_author() != ''){
					$author = $this->get_author();
				}elseif($item->get_author() != ''){
					$author = $item->get_author();		
				}else{
					preg_match('/^http:\/\/(www\.)?([^\/]+)/',$item->get_permalink(), $author);	
					$author = $author[2];
				}
				
				if(VIVVO_PLUGIN_FEED_IMPORTER_FRIENDLY == 1){
					$sefriendly_url = strtolower(preg_replace('/[^a-zA-Z\d\-]/i', '_', $item->get_title()));
					$keywords = preg_split("/[\s_]+/", $sefriendly_url, VIVVO_PLUGIN_FEED_IMPORTER_MAX_WORD_NUM + 1 , PREG_SPLIT_NO_EMPTY);
					
					$output_string = '';
					for ($i=0;$i < VIVVO_PLUGIN_FEED_IMPORTER_MAX_WORD_NUM; $i++){
						$output_string .= '_' . $keywords[$i];
					}

					$sefriendly_url = trim($output_string, "_");
					
					//If sefriendly exists
					$sql ='SELECT count( * ) as count FROM '.VIVVO_DB_PREFIX.'Articles WHERE sefriendly = \'' . secure_sql($sefriendly_url) . '\' LIMIT 1';
						
					$res =& $sm->_db->query($sql);
					$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
					if ($row['count']){
						$sefriendly_url = '';
					}
					
				}else{				
					$sefriendly_url = '';
				}
				
				$sql ='SELECT max( order_num ) as max FROM '.VIVVO_DB_PREFIX.'Articles';
				$res =& $sm->_db->query($sql);
	
				if ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)){
					$order_num = $row['max'] + 1;
				}else{
					$order_num = 1;
				}
				
				$data = array(
							'category_id' => $this->category_id, 
							'user_id' => VIVVO_PLUGIN_FEED_IMPORTER_USER_ID,
							'author' => $author,
							'title' => $item->get_title(),
							'created' => $item->get_date('Y-m-d H:i:s'),
							'body' => $item->get_description(),
							'status' => VIVVO_PLUGIN_FEED_IMPORTER_STATUS,
							'sefriendly' => $sefriendly_url,
							'link' => $item->get_permalink(),
							'order_num' => $order_num,
							'feed_data' => $feed_data,
						);
				$article->populate($data, true);
				$post_master = new vivvo_post_master($sm);

				$post_master->set_data_object($article);
				
				if ($post_master->sql_insert()){
					$this->set_count($this->get_count() + 1);
				}
			}
		}
		$this->set_favicon($feed_r->get_favicon());
	}
	
	//function Feeds(){
		//Obrisi sve feedove cije su kategorije izbrisane!
	//}
}

/** 
 * Feeds list 
 *
 * @license		http://www.vivvo.net/license.php 
 * @copyright	SpoonLabs 
 * @author		Slobodan Utvic <utvara@spoonlabs.com> 
 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com> 
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class Feeds_list extends vivvo_db_paged_list {
	var $_sql_table = 'Feeds';
	var $post_object_type = 'Feeds';
	
	function _default_query(){
		$this->_query->set_from(
								VIVVO_DB_PREFIX . 'Feeds ');
		$this->_query->add_fields('*');
	}
	
	function add_filter($type, $condition = ''){
			
		$condition = secure_sql($condition);
		switch ($type){
			case 'id':
				$this->_query->add_where('(id = \'' . $condition . '\')');
			break;
			case 'category_id':
				$this->_query->add_where('(category_id = \'' . $condition . '\')');
			break;
			case 'feed':
				$this->_query->add_where('(feed = \'' . $condition . '\')');
			break;
			case 'status':
				$this->_query->add_where('(status = \'' . $condition . '\')');
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
			$this->add_generator_option('search_id', $params['search_id']);
		}
		if (isset($params['search_category_id'])){
			$this->add_filter('category_id',$params['search_category_id']);
			$this->add_generator_option('search_category_id', $params['search_category_id']);
		}
		if (isset($params['search_feed'])){
			$this->add_filter('feed',$params['search_feed']);
			$this->add_generator_option('search_feed', $params['search_feed']);
		}
		if (isset($params['search_status'])){
			$this->add_filter('status',$params['search_status']);
			$this->add_generator_option('search_status', $params['search_status']);
		}

			
		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order){
			case 'id':
				$this->_query->add_order('id' . $search_direction);
				break;
			case 'category_id':
				$this->_query->add_order('category_id' . $search_direction);
				break;
			case 'feed':
				$this->_query->add_order('feed' . $search_direction);
				break;
			case 'status':
				$this->_query->add_order('status' . $search_direction);
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

		if ($set_list){
			$this->set_list();
			return $this->list;
		}
	}
	
	function get_feed_by_id($feed_id){
			
		$this->_default_query();
		$this->add_filter('id' , $feed_id);
		$this->set_list();
		if (!empty($this->list)){
			return  current($this->list);
		}else{
			return false;
		}		
	}
	
}

?>