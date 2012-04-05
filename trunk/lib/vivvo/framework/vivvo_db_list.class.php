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

	/**
	 * vivvo_db_list class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	database
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_db_list extends vivvo_object {

		/**
		 * MDB2_Driver_Common
		 *
		 * @var MDB2_Driver_Common
		 */
		var $_db;

		var $_sql_table = '';

		/**
		 * query builder
		 *
		 * @var vivvo_query_builder
		 */
		var $_query;

		var $_fields = '*';

		var $list = array();
		var $post_object_type;
		var $id_key = 'id';

		/**
		 * @deprecated
		 */
		function set_db() {
		}

		function set_query() {
			$this->_query = new vivvo_query_builder();
		}

		function _default_query() {
			$this->_query->set_from(VIVVO_DB_PREFIX . $this->_sql_table);
			if (is_array($this->_fields) && !empty($this->_fields)) {
				foreach ($this->_fields as $field) {
					$this->_query->add_fields($field);
				}
			} else {
				$this->_query->add_fields('*');
			}
		}

		/**
		 * Delete all list objects from database
		 *
		 * @param vivvo_post_master $post_master
		 * @param array $restiction		Restriction ids
		 *
		 * @return bool	succes
		 */
		function sql_delete_list($post_master, $restriction = array(), $all_matching = false) {
			$result = true;
			if (empty($this->list)) {
				$this->set_list();
			}
			$ids = $this->get_list_ids();

			if ($ids && is_array($restriction) && !empty($restriction)) {
				$ids = array_intersect($ids, $restriction);
			}

			if (!empty($ids)) {

				$db = $this->get_db();

				foreach ($ids as $id) {
					$where[] = (int)$id;
					$this->list[$id]->on_delete($post_master);

				}

				$where = $this->id_key . ' IN ('. implode(',' , $where) .')';
				$result = $post_master->sql_delete_list($this->_sql_table, $where);

				foreach ($ids as $id) {
					unset($this->list[$id]);
				}
			} elseif ($all_matching === true) {
				$where = str_replace('WHERE', ' ', $this->_query->get_where());
				$from = str_replace('FROM', ' ', $this->_query->get_from());
				$result = $post_master->sql_delete_list($this->_sql_table, $where, $from);
			}
			return $result;
		}

		/**
		 * Insert all objects from list to databas
		 *
		 * @param vivvo_post_master $post_master
		 *
		 * @return bool	succes
		 */
		function sql_insert_list($post_master){
			$result = true;
			if (!empty($this->list)){
				foreach ($this->list as $k => $v){
					$result = $result && $post_master->sql_insert($this->list[$k]);
				}
			}
			return $result;
		}

		/**
		 * Insert all objects from list to databas
		 *
		 * @param vivvo_post_master $post_master
		 * @param array $params Params to change assoc array
		 * @param array $restiction		Restriction ids
		 *
		 * @return bool	succes
		 */
		function sql_update_list($post_master, $params, $restriction = NULL, $all_matching = false){
			$result = true;
			$ids = $this->get_list_ids(true);

			if ($ids && is_array($restriction) && !empty($restriction)){
				$ids = array_intersect($ids, $restriction);
			}
			if ($ids){
				$db = $this->get_db();
				foreach ($ids as $id){
					$where[] = $db->quote($id);
				}

				$where = $this->id_key . ' IN ('. implode(',' , $where) .')';
				$result = $post_master->sql_update_list(VIVVO_DB_PREFIX . $this->_sql_table, $params, $where);
			}elseif ($all_matching === true){
				$where = str_replace('WHERE', ' ', $this->_query->get_where());
				$from = str_replace('FROM', ' ', $this->_query->get_from());
				$result = $post_master->sql_update_list(VIVVO_DB_PREFIX . $this->_sql_table, $params, $where, $from);
			}
			return $result;
		}

		function &get_item_by_property($prop, $value, $index = 0){
			$curent_index = 0;

			if (is_array($this->list) && !empty($this->list)){
				$keys = $this->get_list_ids();
				foreach($keys as $key){
					if ($this->list[$key]->__get($prop) == $value){
						if ($curent_index == $index){
							return $this->list[$key];
						}
						$curent_index++;
					}
				}
			}
			return false;
		}

		function get_sublist($ids){
			if (is_array($ids) && !empty($ids)){
				$array = array();
				foreach ($ids as $id){
					if (is_array($this->list) && key_exists($id,$this->list)){
						$array[$id] = $this->list[$id];
					}
				}
				return $array;
			}
			return false;
		}

		function get_db(){
			return vivvo_lite_site::get_instance()->get_db();
		}

		function get_db_manager(){
			return vivvo_lite_site::get_instance()->get_db_manager();
		}

		function get_ids_by_property($prop, $value){
			if (is_array($this->list) && !empty($this->list)){
				$array = array();
				$keys = $this->get_list_ids();
				foreach($keys as $key){
					if (is_array($value)){
						if (in_array($this->list[$key]->__get($prop), $value)){
							$array[] = $key;
						}
					}else{
						if ($this->list[$key]->__get($prop) == $value){
							$array[] = $key;
						}
					}
				}
				return $array;
			}
			return false;
		}

		function &get_item_list_by_property($prop, $value){
			if (is_array($this->list) && !empty($this->list)){
				$array = $this->get_ids_by_property($prop, $value);
				return $this->get_sublist($array);
			}
			return false;
		}

		function get_property_list($prop_name){
			if (is_array($this->list) && !empty($this->list)){
				$array = array();
				$keys = $this->get_list_ids();
				foreach($keys as $key){
					$array[] = $this->list[$key]->__get($prop_name);
				}
				return $array;
			}
			return false;
		}

		function get_total_count(){
			$query = $this->_query->get_total_count_query();
			$db = $this->get_db();
			$res = $db->query($query);
			if (!PEAR::isError($res)){
				$sum = 0;
				$num = 0;
				while ($count = $res->fetchOne()){
					$sum += $count;
					$num++;
				}
				$res->free();
				if ($num > 1){
					return $num;
				}else{
					return $sum;
				}
			}else {
				return false;
			}
		}

		function get_list_ids($force = false) {

			if (empty($this->list) and $force) {

				$query = 'SELECT a.' . $this->id_key . ' ' . $this->_query->get_from() . ' ' . $this->_query->get_join() . ' ' . $this->_query->get_where() . ' ' . $this->_query->get_group_by() . ' ' . $this->_query->get_having() . ' ' . $this->_query->get_order() . ' ' . $this->_query->get_limit();
				$res = $this->get_db()->query($query);

				if (!PEAR::isError($res)) {
					return $res->fetchCol();
				}
			} elseif (is_array($this->list)) {
				return array_keys($this->list);
			}

			return false;
		}

		function get_total_number_of_fields(){
			$db = $this->get_db();
			$res = $db->query('SELECT count(*) FROM ' . VIVVO_DB_PREFIX . $this->_sql_table);
			if (!is_a($res, 'mdb2_error')){
				$sum = 0;
				while ($count = $res->fetchOne()){
					$sum += $count;
				}
				$res->free();
				return $sum;
			}else {
				return false;
			}
		}

		function get_count($search_params = false){
			if ($search_params !== false){
				$this->search($search_params, '', '', 0, 0, false);
			}else{
				$this->_default_query();
			}

			return $this->get_total_count();
		}

		function get_max($field, $search_params = false){
			if ($search_params !== false){
				$this->search($search_params, '', '', 0, 0, false);
			}else{
				$this->_default_query();
			}


			$query = $this->_query->get_max_query($field);

			$res = $this->get_db()->query($query);
			if (!is_a($res, 'mdb2_error')){
				return $res->fetchOne();
			}else {
				return false;
			}
		}

		function get_min($field, $search_params = false){
			if ($search_params !== false){
				$this->search($search_params, '', '', 0, 0, false);
			}else{
				$this->_default_query();
			}

			$query = $this->_query->get_min_query($field);
			$res = $this->get_db()->query($query);
			if (!is_a($res, 'mdb2_error')){
				return $res->fetchOne();
			}else {
				return false;
			}
		}

		function get_sum($field, $search_params = false){
			if ($search_params !== false){
				$this->search($search_params, '', '', 0, 0, false);
			}else{
				$this->_default_query();
			}

			$query = $this->_query->get_sum_query($field);
			$res = $this->get_db()->query($query);
			if (!is_a($res, 'mdb2_error')){
				return $res->fetchOne();
			}else {
				return false;
			}
		}

		function prepare_for_xml($data){
//			return $data;
			return str_replace(array('&', '"', "'", '<', '>'), array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), $data);
		}

		function remove_xml_escapes($data){
//			return $data;
			return str_replace(array('&amp;', '&quot;', '&apos;', '&lt;', '&gt;'), array('&', '"', "'", '<', '>'), $data);
		}

		function set_list(){
			$this->list = array();
			if (!empty($this->post_object_type) && class_exists($this->post_object_type)){
				$query = $this->_query->get_query();
				if ($query != ''){
					$sm = vivvo_lite_site::get_instance();
					$cache_manager = $sm->get_object_cache_manager();
					$res = $this->get_db()->query($query);
					if (!is_a($res, 'mdb2_error')){
						$class = $this->post_object_type;
						while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))){
							if ($cache_manager->is_cached($class, $row[$this->id_key])){
								$this->list[$row[$this->id_key]] = $cache_manager->retrive($class, $row[$this->id_key]);
								$this->list[$row[$this->id_key]]->populate($row);
							}else{
								$object = new $class(null, $row);
								$cache_manager->add($class, $row[$this->id_key], $object);
								$this->list[$row[$this->id_key]] = $object;
							}
						}
						$res->free();
					}else{
						$sm->debug_push("sql:", $query, 1);
						$sm->debug_push("sql:", $res->getMessage(), 1);
					}
				}else{
					$sm->debug_push("sql:", 'Empty query called from ' . get_class($this), 1);
				}
			}else{
				$sm->debug_push("core:", 'Class missing ' . $this->post_object_type . ' called from ' . get_class($this), 1);
			}
		}

		function generic_list(){
			$this->_default_query();
			$this->set_list();
		}

		/**
		 * Advaced search list generator
		 *
		 * @param array $params Search parameters
		 * @param string $order Order parameters
		 * @param integer $limit Limit
		 * @param integer $offset Offset
		 * @return array
		 */
		function search($params = array(), $order='', $direction = 'ascending', $limit =0, $offset =0){
			$this->generic_list();
			return $this->list;
		}

		function generic_add_filter($params, $prefix = ''){
			if (is_array($params) && !empty($params)){
				$keys = array_keys($params);
				foreach ($keys as $k){
					if (preg_match('/^search_field_(.*)_(lt|gt|eq|neq|in|notin|between|notnull|isnull)$/', $k, $arr)){
						$condition = secure_sql($params[$k]);
						switch ($arr[2]){
							case 'lt':
								$this->_query->add_where('(' . $prefix . $arr[1] . ' < \'' . $condition . '\')');
							break;
							case 'gt':
								$this->_query->add_where('(' . $prefix . $arr[1] . ' > \'' . $condition . '\')');
							break;
							case 'eq':
								$this->_query->add_where('(' . $prefix . $arr[1] . ' = \'' . $condition . '\')');
							break;
							case 'neq':
								$this->_query->add_where('(' . $prefix . $arr[1] . ' != \'' . $condition . '\')');
							break;
							case 'in':
								$condition = secure_sql_in($condition);
								$this->_query->add_where('(' . $prefix . $arr[1] . ' IN ' . $condition . ')');
							break;
							case 'notin':
								$condition = secure_sql_in($condition);
								$this->_query->add_where('(' . $prefix . $arr[1] . ' NOT IN ' . $condition . ')');
							break;
							case 'notnull':
								$this->_query->add_where('(' . $prefix . $arr[1] . ' IS NOT NULL )');
							break;
							case 'isnull':
								$this->_query->add_where('(' . $prefix . $arr[1] . ' IS NULL )');
							break;
							case 'between':
								$between = explode(',', $condition);
								if (is_array ($between) && count($between) == 2){
									$this->_query->add_where('(' . $prefix . $arr[1] . ' BETWEEN \'' . $between[0] . '\' AND \'' . $between[1] . '\')');
								}
							break;
						}
					}
				}
			}
		}

		/**
		 * Generic serach params filter
		 *
		 * @param vivvo_site $sm
		 * @param string $table
		 * @param array $in_params
		 */
		function generic_get_search_params($sm, $table, $in_params){
			if (is_array($in_params) && !empty($in_params)){
				$dbm = self::get_db_manager();
				$fields = $dbm->get_table_fields($table);
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
		function generic_sort($prefix, $sort, $direction){
			if (!empty($sort)){
				$dbm = self::get_db_manager();
				$fields = $dbm->get_table_fields(VIVVO_DB_PREFIX . $this->_sql_table);
				if (!empty($fields)){
					if (in_array($sort, $fields)){
						$this->_query->add_order($prefix . $sort . ' ' . $direction);
					}
					return true;
				}
			}
			return false;
		}

		function reset_list_query(){
			$this->_query->reset_query();
		}

		function vivvo_db_list($site_manager = null, $fields = ''){

			$this->set_query();

			if (!empty($fields)){
				$fields = explode(',', $fields);
				if (!in_array($this->id_key , $fields)){
					$fields[] = $this->id_key;
				}
				$this->_fields = $fields;
			}else{
				$this->_fields = '.*';
			}
		}


		function __destruct() {
			parent::__destruct();
			if (!empty($this->_db)) unset($this->_db);
			if (!empty($this->_query)) unset($this->_query);
		}
	}

#EOF