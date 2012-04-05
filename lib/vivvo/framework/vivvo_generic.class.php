<?php
/* =============================================================================
 * $Revision: 5385 $
 * $Date: 2010-05-25 11:51:09 +0200 (Tue, 25 May 2010) $
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

/*
<vte:box module="box_generic">
	<vte:params>
		<vte:param name="table" value="Categories" />
		<vte:param name="cache" value="1" />
		<vte:param name="search_field_status_eq" value="2" />
		<vte:param name="search_order" value="descending" />
		<vte:param name="search_limit" value="3" />
	</vte:params>
	<vte:template>
		<div>
			<vte:foreach item = "article" from = "{list}">
				<div class="short">
					<div class="short_holder">
						<vte:if test="{article.image}">
							<div class="image">
								<img src="{VIVVO_STATIC_URL}thumbnail.php?file={article.image}&amp;size=summary_medium" alt="image" /><br />
							</div>
						</vte:if>
						<h2><a href="{article.href}"><vte:value select="{article.title}" /></a></h2>
						<span class="summary"><vte:value select="{article.abstact}" /></span>
					</div>
				</div>
			</vte:foreach>
		</div>
	</vte:template>
</vte:box>

<vte:box module="box_generic">
	<vte:params>
		<vte:param name="database" value="vbulletin" />
		<vte:param name="table" value="tblvb_forum" />
		<vte:param name="primary_key" value="forumid" />
		<vte:param name="search_field_forumid_gt" value="1" />
		<vte:param name="search_sort_by" value="lastpostid" />
		<vte:param name="cache" value="1" />
		<vte:param name="search_limit" value="10" />
	</vte:params>
	<vte:template>
		<div>
			<vte:foreach item = "forum" from = "{list}">
				<div class="short">
					<div class="short_holder">
						<h2><a href="http://91.185.112.29/dev/forum/forumdisplay.php?f={forum.forumid}"><vte:value select="{forum.title}" /></a></h2>
						<span class="summary"><vte:value select="{forum.description}" /></span>
					</div>
				</div>
			</vte:foreach>
		</div>
	</vte:template>
</vte:box>

<vte:box module="box_generic">
	<vte:params>
		<vte:param name="dsn" value="mysql://vivvonet:xxx@www.vivvo.net/vivvonet_forum" />
		<vte:param name="table" value="forum" />
		<vte:param name="primary_key" value="forumid" />
		<vte:param name="search_field_forumid_gt" value="1" />
		<vte:param name="search_sort_by" value="lastpostid" />
		<vte:param name="cache" value="1" />
		<vte:param name="search_limit" value="10" />
	</vte:params>
	<vte:template>
		<div>
			<vte:foreach item = "forum" from = "{list}">
				<div class="short">
					<div class="short_holder">
						<h2><a href="http://91.185.112.29/dev/forum/forumdisplay.php?f={forum.forumid}"><vte:value select="{forum.title}" /></a></h2>
						<span class="summary"><vte:value select="{forum.description}" /></span>
					</div>
				</div>
			</vte:foreach>
		</div>
	</vte:template>
</vte:box>

<vte:box module="box_generic">
	<vte:params>
		<vte:param name="dsn" value="mysql://root:xxx@localhost/diopta" />
		<vte:param name="table" value="tblProducts" />
		<vte:param name="cache" value="1" />
		<vte:param name="search_limit" value="10" />
	</vte:params>
	<vte:template>
		<ul>
			<vte:foreach item = "model" from = "{list}">
				<li>
					<vte:value select="{model.model}" />
				</li>
			</vte:foreach>
		</ul>
	</vte:template>
</vte:box>
*/

/**
 * vivvo generic list
 *
 * @license		http://www.vivvo.net/license.php
 * @copyright	SpoonLabs
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 * @see			vivvo_db_paged_list
 * @version		Vivvo Lite - Generic Database Engine
 */
class vivvo_generic_list extends vivvo_db_paged_list {
	var $_sql_table = '';
	var $post_object_type = 'vivvo_post_object';

	function _default_query(){
		$this->_query->set_from($this->_sql_table);
		if (is_array($this->_fields) && !empty($this->_fields)){
			foreach ($this->_fields as $field){
				$this->_query->add_fields($field);
			}
		}else{
			$this->_query->add_fields('*');
		}
	}

	function set_list(){
		$this->list = array();
		$query = $this->_query->get_query();
		if ($query != ''){
			$res = vivvo_lite_site::get_instance()->get_db()->query($query);
			if (!is_a($res, 'mdb2_error')){
				$class = $this->post_object_type;
				while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))){
					$this->list[$row[$this->id_key]] = new vivvo_post_object(null, $row);
					$this->list[$row[$this->id_key]]->_sql_table = $this->_sql_table;
				}
				$res->free();
			}else{
				//print_r($this->_db);
				if (isset($_GET['debug'])) echo "<hr/>query error: $query \n<hr/>\n";
			}
		}else{
			if (isset($_GET['debug'])) echo "bbbbb";
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
		if ($params !== false){
			$this->generic_add_filter($params);
		}

		// search order //
		$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

		if ($order != ''){
			if (!$this->generic_sort($order, $search_direction)){
				$order = $this->id_key;
				$this->_query->add_order($this->id_key . ' DESC');
			}
		}else{
			$order = $this->id_key;
			$this->_query->add_order($this->id_key . ' DESC');
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


	function get_search_params($in_params){
		$params = array ();

		if (!empty($in_params['search_limit'])){
			$params['search_limit'] = $in_params['search_limit'];
		}else{
			$params['search_limit'] = 10;
		}

		$params['search_options'] = array();

		if (isset($in_params['search_options']) && is_array($in_params['search_options']) && !empty($in_params['search_options'])) $params['search_options'] = $in_params['search_options'];

		if (!empty($in_params['search_sort_by'])){
			$params['search_sort_by'] = $in_params['search_sort_by'];
		}

		if (isset($in_params['search_order']) && !empty($in_params['search_order'])){
			$params['search_order'] = $in_params['search_order'];
		}else{
			$params['search_order'] = 'descending';
		}

		if (!isset($in_params['search_options']) || !is_array($in_params['search_options']) || empty($in_params['search_options'])){
			$params['search_options'] = $this->generic_get_search_params($in_params);
		}

		if (isset($in_params['pg'])){
			$cur_page = (int) $in_params['pg'];
		}
		if (empty($cur_page)) $cur_page=1;

		$params['pg'] = $cur_page;

		$params['offset'] = ($cur_page-1) * $params['search_limit'];
		if (empty($params['offset'])) $params['offset'] = 0;

		if (!empty($in_params['cache'])) $params['cache'] = $in_params['cache'];

		return $params;
	}

	function get_search_params_from_url(){
		$um = vivvo_lite_site::get_instance()->get_url_manager();
		$params = $this->get_search_params($um->list);
		return $params;
	}

	/**
	 * Generic serach params filter
	 *
	 * @param vivvo_site $sm
	 * @param string $table
	 * @param array $in_params
	 */
	function generic_get_search_params($in_params){
		if (is_array($in_params) && !empty($in_params)){
			$fields = vivvo_lite_site::get_instance()->get_db()->manager->listTableFields($this->_sql_table);
			if (!empty($fields)){
				$our_params = array();
				$keys = array_keys($in_params);
				$field_keys = implode('|', $fields);

				foreach ($keys as $k){
					if (preg_match('/^search_field_(' . $field_keys . ')_(lt|gt|eq|neq|in|notin|between|notnull|isnull)$/', $k)){
						$our_params[$k] = $in_params[$k];
					}
				}
				return $our_params;
			}
		}
		return false;
	}

	/**
	 * Generic sort
	 *
	 * @param string $prefix
	 * @param string $sort
	 * @param string $direction
	 */
	function generic_sort($sort, $direction){
		if (!empty($sort)){
			$fields = vivvo_lite_site::get_instance()->get_db()->manager->listTableFields($this->_sql_table);
			if (!empty($fields)){
				if (in_array($sort, $fields)){
					$this->_query->add_order($prefix . $sort . ' ' . $direction);
				}
				return true;
			}
		}
		return false;
	}

	/**
	 * @deprecated
	 */
	function set_db($database, $dsn){
	}

	function vivvo_generic_list($site_manager = null, $table = '', $primary_key = 'id', $database = '', $dsn = ''){
		if ($table){
			$this->_sql_table = $table;
			$this->id_key = $primary_key;
			$this->set_query();
		}
	}
}

#EOF