<?php
/* =============================================================================
 * $Revision: 5084 $
 * $Date: 2010-04-23 14:39:38 +0200 (Fri, 23 Apr 2010) $
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
 * @author		Ivan Dilber <idilber@spoonlabs.com>
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @version		Vivvo Lite - Generic Database Engine
 */
class TagsGroups extends vivvo_post_object {

	/**
	* id
	* Database field type:	int(11)
	* Null status:		NO
	* @var	integer	$id
	*/
	public $id;

	/**
	 * name
	 * Database field type:	varchar(255)
	 * Null status:		NO
	 * @var	string	$name
	 */
	public $name;

	/**
	* url prefix for tag pages
	* Database field type:	varchar(255)
	* Null status:		NO
	* @var	string	$url
	*/
	public $url;

	/**
	* template name
	* Database field type:	varchar(100)
	* Null status:		NO
	* @var	string	$template
	*/
	public $template;

    /**
	 * tag template name
	 * Database field type:	varchar(100)
	 * Null status:		NO
	 * @var	string	$tag_template
	 */
	public $tag_template;

	/**
	* Metadata - serialized extra data
	* Database field type:	text
	* Null status:		YES
	* @var	string	$metadata
	*/
	public $metadata;

    var $_sql_table = 'tags_groups';

	/**
	 * Sets {@link $id}
	 * @param	integer	$id
	 */
	public function set_id($id) {
		$this->id = $id;
	}

	/**
	 * Sets {@link $name}
	 * @param	string	$name
	 */
	public function set_name($name) {
        if ($name = trim($name)) {
            $this->name = $name;
			return true;
	    } else {
			return false;
		}
	}

	/**
	 * Sets {@link $url}
	 * @param	string $url
	 */
	public function set_url($url) {
        if ($url = trim($url)) {
            $this->url = $url;
	    }
	}

    /**
	 * Sets {@link $template}
	 * @param	string $template
	 */
	public function set_template($template) {
	    if ($template = trim($template)) {
            $this->template = $template;
	    }
	}

    /**
	 * Sets {@link $tag_template}
	 * @param	string $tag_template
	 */
	public function set_tag_template($tag_template) {
	    if ($tag_template = trim($tag_template)) {
            $this->tag_template = $tag_template;
	    }
	}

	/**
	 * Sets {@link $metadata}
	 * @param	string $metadata
	 */
	public function set_metadata($metadata) {
		$this->metadata = is_string($metadata) ? $metadata : serialize($metadata);
	}

	/**
	 * Gets $id
	 * @return	integer
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Gets $name
	 * @return	string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Gets $url
	 * @return	string
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * Gets $template
	 * @return	string
	 */
	public function get_template() {
		return $this->template;
	}

    /**
	 * Gets $tag_template
	 * @return	string
	 */
	public function get_tag_template() {
		return $this->tag_template;
	}

	/**
	 * Gets $metadata
	 * @return	string
	 */
	public function get_metadata() {
		return $this->metadata = serialize($this->meta);
	}

	/**
	 * Gets unserialized metadata
	 */
	public function get_meta() {
		return ($meta = @unserialize($this->metadata)) ? $meta : array();
	}

	private $tags;

	/**
	 * Gets tags from this group
	 *
	 * @return array
	 */
	public function get_tags() {

		if (!is_array($this->tags)) {

			require_once(dirname(__FILE__) . '/Tags.class.php');

			$tags_list = new Tags_list();
			$this->tags = $tags_list->get_tags_by_group_id($this->id);

			if (!is_array($this->tags)) {
				$this->tags = array();
			}
		}

		return $this->tags;
	}

	/**
	 * @var	array	List of tags in this topic applied to specific article
	 */
	private $article_tags = array();

	/**
	 * Assigns article tags
	 *
	 * @param	array	$tags
	 */
	public function set_article_tags($tags) {
		$this->article_tags = $tags;
	}

	/**
	 * Returns list of article tags
	 *
	 * @return	array
	 */
	public function get_article_tags() {
		return $this->article_tags;
	}

	/**
	 * Gets href
	 */
	public function get_href() {
		return make_proxied_url(urlencode($this->url)) . '/';
	}

	public function get_absolute_href() {
		return make_absolute_url($this->get_href(), false);
	}

	private $new_tags;

	public function set_new_tags($tags) {
		$this->new_tags = $tags;
	}

	public function get_new_tags() {
		return $this->new_tags;
	}

	function on_delete($post_master){

		require_once(dirname(__FILE__) . '/TagsToTagsGroups.class.php');
		$list = new TagsToTagsGroups_list();

		$list->get_rel_by_group_id($this->id);
		$list->sql_delete_list($post_master);

		vivvo_lite_site::get_instance()->get_url_manager()->unregister_url(urlencode($this->url));

		// THE REST OF DELETES IS DONE WITHIN on_delete HANDLERS IN RELEVANT CLASSES
	}

}

/**
 * TagsGroups list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Ivan Dilber <idilber@spoonlabs.com>
 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class TagsGroups_list extends vivvo_db_paged_list {

	var $_sql_table = 'tags_groups';
	var $post_object_type = 'TagsGroups';

	function _default_query($reset = false) {

		if ($reset) {
			$this->_query->reset_query();
		}

		$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table . ' AS tg');
		$this->_query->add_fields('tg.*');
	}

	function add_filter($type, $condition = '') {
		$condition = secure_sql($condition);
		switch ($type){
			case 'name':
			case 'url':
			case 'template':
            case 'tag_template':
				$this->_query->add_where("(tg.$type = '$condition')");
			break;
			case 'not_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_where("(tg.id NOT IN ($condition))");
			break;
			case 'starting_with':
				$condition = str_replace('%', '\%', $condition);
				$this->_query->add_where("(tg.name LIKE '$condition%')");
			break;
			case 'tag_id':
				$condition = secure_sql_in($condition);
				$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table . ' AS tg, ' . VIVVO_DB_PREFIX . 'tags_to_tags_groups AS ttg');
				$this->_query->add_where("ttg.tag_id IN ($condition) AND ttg.tags_group_id = tg.id");
			break;
			case 'category_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tags_group_id = tg.id ', 'at');
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles AS a ON a.id = at.article_id ', 'a');
				$this->_query->add_where("a.category_id IN ($condition)");
			break;
			case 'article_id':
				$condition = secure_sql_in($condition);
				$this->_query->add_join(' LEFT JOIN ' . VIVVO_DB_PREFIX . 'articles_tags AS at ON at.tags_group_id = tg.id ', 'at');
				$this->_query->add_where("at.article_id IN ($condition)");
			break;
			case 'id':
			default:
				$condition = secure_sql_in($condition);
				$this->_query->add_where("tg.id IN ($condition)");
			break;
		}
	}

	/**
	 * @var	array	Search param -> filter name mapping
	 */
	public static $search_params = array(
		'search_id' => 'id', 'search_not_id' => 'not_id', 'search_url' => 'url',
		'search_template' => 'template', 'search_tag_template' => 'tag_template',
		'search_starting_with' => 'starting_with', 'search_tag_id' => 'tag_id',
		'search_category_id' => 'category_id', 'search_article_id' => 'article_id'
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
	function search($params, $order = '', $direction = 'ascending', $limit = 0, $offset = 0, $set_list = true) {

		$this->_default_query(true);
		$this->_query->set_limit((int)$limit);
		$this->_query->set_offset((int)$offset);

		foreach ($params as $param => $value) {
			if (isset(self::$search_params[$param])) {
				$this->add_filter(self::$search_params[$param], $value);
			}
		}

		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		switch ($order) {
			case 'id':
			case 'name':
			case 'url':
			case 'template':
				$this->_query->add_order("tg.$order $search_direction");
			case '%no-sort':
			default:
				// ignore
		}

		$limit = (int)$limit;
		$this->_query->set_limit($limit);
		$offset = (int)$offset;
		$this->_query->set_offset($offset);

		if ($set_list) {
			$this->set_list();
			return $this->list;
		}

		return array();
	}

	private static $object_cache = array();

    private function get_cached_object($id) {
        if (!empty(self::$object_cache[$id])) {   
            if (empty($this->list[$id])){
                $this->list[$id] = self::$object_cache[$id];
            }
            return self::$object_cache[$id];
        }
        return false;
    }
    
	public function set_list() {
		parent::set_list();
		if (is_array($this->list)) {
			foreach($this->list as $id => $topic){
                self::$object_cache[$id] = $this->list[$id];
            }
		}
		reset($this->list);
	}

	public function get_group_by_id($id) {
        $cached = $this->get_cached_object($id);
        
		if (!empty($cached)) {
			return $cached;
		}
		
		$this->_default_query(true);
		$this->add_filter('id', (int)$id);
		$this->set_list();
		if (!empty($this->list)) {
			return reset($this->list);
		}
		return false;
	}

	public function get_groups_by_ids($id) {
		$this->_default_query();
		$this->add_filter('id', $id);
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		}
		return false;
	}

	public function get_groups_by_tag_id($tag_id) {
		$this->_default_query();
		$this->add_filter('tag_id', $tag_id);
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		}
		return false;
	}

	public function get_groups_by_article_id($article_id, $exclude = false) {
		$this->_default_query();
		$this->add_filter('article_id', $article_id);
		if ($exclude !== false) {
			$this->add_filter('not_id', $exclude);
		}
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		}
		return false;
	}

	public function &get_group_by_url($url) {
		$this->_default_query();
		$this->add_filter('url', $url);
		$this->set_list();
		if (!empty($this->list)) {
			return current($this->list);
		}
		return intval(false);
	}

	public function &get_group_by_name($name) {
		$this->_default_query();
		$this->add_filter('name', $name);
		$this->set_list();
		if (!empty($this->list)) {
			return current($this->list);
		}
		return intval(false);
	}

	public function get_all_groups(){
		$this->_default_query();
		$this->set_list();
		if (!empty($this->list)) {
			return $this->list;
		}
		return array();
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