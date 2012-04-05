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
 * Vivvo CMS
 *
 * LICENSE:
 *
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 * @copyright	SpoonLabs
 * @package		vivvo_framework
 * @subpackage	database
 * @version		$Revision: 5385 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */




	/**
	 * vivvo_site class
	 * Site manager
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	database
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 *
	 */
	class vivvo_post_master extends vivvo_object {

		/**
		 * Object containing post data
		 *
		 * @var vivvo_post_object
		 */
		var $data_object;

		/**
		 * SQL table name
		 *
		 * @var string table name
		 */
		var $_sql_table = '';
		/**
	     * Table field information (name, maxlen, not null)
	     *
	     * @var array
	     */

		var $_data_flags;

		/**
	     * Inbound data
	     *
	     * @var $_data_in
	     */

		var $_data_in;

		/**
	     * Metod to execute
	     *
	     * @var $metod
	     */

		var $metod;

		/**
	     * Missing fields
	     *
	     * @var array
	     */
		var $missing;

		/**
		 * MDB2 connector
		 *
		 * @var MDB2_Driver_Common
		 */
		var $_db;

		/**
		 * Vivvo DB manager
		 *
		 * @var vivvo_db_manager
		 */
		var $_db_manager;

		/**
		 * Id key name
		 *
		 * @var string
		 */
		var $id_key = 'id';

		/**
		 * Stack for transaction
		 *
		 * @var array
		 */
		var $stack = array();

		/**
		 * Sets {@link $data_object}
		 *
		 * @param	vivvo_post_object	$object
		 * @return	mixed				data on success, a false on fail
		 */
		function set_data_object(&$object) {
			if ($object instanceof vivvo_post_object) {
				$this->data_object = $object;
				return $this->get_request_data();
			} else {
				vivvo_lite_site::get_instance()->debug_push("core:", "vivvo_post_master::set_data_object failed.");
				return false;
			}
		}

		/**
		 * Enter description here...
		 *
		 * @param unknown_type $value
		 * @return unknown
		 */
		function secure_sql($value){
			return secure_sql($value);
		}

		/**
		 * Enter description here...
		 *
		 * @return	boolean
		 */
		function get_request_data(){
			if ($this->data_object instanceof vivvo_post_object) {
				$this->_data_in = array();
				$this->id_key = $this->data_object->id_key;
				if ($this->_sql_table !== $this->data_object->_sql_table) {
					$this->_sql_table = $this->data_object->_sql_table;
					$this->_set_data_flags();
				}
				foreach ($this->_data_flags as $k => $v) {
				    $class = get_class($this->data_object);
					if ($k != '' && vivvo_property_exists(get_class($this->data_object), $k)) {
						if ($this->data_object->$k ===  null) {
							//$this->_data_in[$k] = null;
						} elseif (is_array($this->data_object->$k)) {
							$this->_data_in[$k] = implode(",", $this->data_object->$k);
						} else {
							$this->_data_in[$k] = $this->data_object->$k;
						}
					} else if (is_array($this->data_object->elem) && key_exists($k, $this->data_object->elem)) {
						if ($this->data_object->elem[$k] ===  null) {
							//$this->_data_in[$k] = null;
						} elseif (is_array($this->data_object->elem[$k])) {
							$this->_data_in[$k] = implode(",", $this->data_object->elem[$k]);
						} else {
							$this->_data_in[$k] = $this->data_object->elem[$k];
						}
					}
				}
				return true;
			} else {
				return false;
			}
		}

		function prepare_insert($data, $where = null) {
			$key = $this->get_data_fields($data);
			if (!empty($key)) {
				$replacment = array();
				foreach ($key as $k => $v) {
					$replacment[] = ':' . $v;
				}
				foreach ($key as $k => $v) {
					$key[$k] = '`' . $v . '`';
				}
				$replacment = implode(',' , $replacment);
				$key = implode(',' , $key);
				$sth = vivvo_lite_site::get_instance()->get_db()->prepare('INSERT INTO `' . $this->_sql_table . '` (' . $key . ') VALUES (' . $replacment . ')', $types);
			}
			return $sth;
		}

		function prepare_update($data, $where = null, $from = null) {
			$key = $this->get_data_fields($data);
			if (!empty($key)) {
				$replacment = array();
				foreach ($key as $k => $v) {
					$replacment[] = '`' . $v . '` =:' . $v;
				}

				$replacment = implode(',' , $replacment);
				if (!$from) {
					$from = '`' . $this->_sql_table . '`';
				}
				if ($where) {
					$where = ' WHERE ' . $where;
				}

				$sth = vivvo_lite_site::get_instance()->get_db()->prepare('UPDATE ' . $from . ' SET ' . $replacment . $where, null);
			}
			return $sth;
		}

		/**
	     * Updates row in table
	     *
	     * @return boolean
	     * @access private
	     */
		function sql_update($commit = true) {

			if (!isset($this->_data_in[$this->id_key])) {
				return false;
			}

			$db = vivvo_lite_site::get_instance()->get_db();
			$sql = $this->prepare_update($this->_data_in, $this->id_key . ' = ' . $db->quote($this->_data_in[$this->id_key], 'integer'));

			$sm = vivvo_lite_site::get_instance();
			$sm->debug_push("sql:", $sql->query);

			if ($commit == true || !$db->supports('transactions')) {

				if (!$sql || PEAR::isError($sql)) {
					$sm->debug_push("sql:", $sql->getMessage(), 1);
					return false;
				}

				$hook = 'db_' . get_class($this->data_object) . '_update';

				if (vivvo_hooks_manager::call($hook . '_pre', array(&$this)) !== false) {

					$res = $sql->execute($this->_data_in);

					if (PEAR::isError($res)) {
						$sm->debug_push("sql:", $res->getMessage(), 1);
						return false;
					}

					vivvo_hooks_manager::call($hook . '_post', array(&$this));

				} elseif (!vivvo_hooks_manager::get_status()) {
					return false;
				}

			} else {
				$this->stack[] = $sql;
			}

			/**
			 * call on_update method
			 *
			 * {@link vivvo_post_object::on_update}
			 */
			if ($this->data_object instanceof vivvo_post_object){
				$this->data_object->on_update($this);
			}

			$this->metod = 'modify';
			return true;
		}

		/**
	     * Updates rows in table
	     *
	     * @var string $sql_table
	     * @var array $params
	     * @var string $where
	     * @var bool $commit
	     *
	     * @return boolean
	     * @access private
	     */
		function sql_update_list($sql_table, $params, $where, $from = false, $commit = true) {

			$this->_sql_table = $sql_table;
			$this->_set_data_flags();

			foreach($params as $k => $v) {
				if (!key_exists($k, $this->_data_flags)) {
					unset ($params[$k]);
				}
			}

			if (empty($params)) {
				return false;
			}

			if ($from) {
				$sql = $this->prepare_update($params, $where, $from);
			} else {
				$sql = $this->prepare_update($params, $where);
			}

			//if (defined ('VIVVO_DEBUG_MODE')) echo "<hr/>$sql<hr/>\n";
			$sm = vivvo_lite_site::get_instance();
			$sm->debug_push("sql:", $sql->query);

			if ($commit == true || !vivvo_lite_site::get_instance()->get_db()->supports('transactions')) {

				$hook = 'db_' . $this->_sql_table . '_update_list';

				if (vivvo_hooks_manager::call($hook . '_pre', array(&$this)) !== false) {

					$res = $sql->execute($params);

					if (PEAR::isError($res)){
						$sm->debug_push("sql:", $res->getMessage(), 1);
						return false;
					}

					vivvo_hooks_manager::call($hook . '_post', array(&$this));

				} elseif (!vivvo_hooks_manager::get_status()) {
					return false;
				}

			} else {
				$this->stack[] = $sql;
			}

			/**
			 * call on_update method
			 *
			 * {@link vivvo_post_object::on_update}
			 */
			if ($this->data_object instanceof vivvo_post_object) {
				$this->data_object->on_update($this);
			}

			$this->metod = 'modify';
			return true;
		}

		/**
	     * Inserts new row to table
	     *
	     * @return boolean
	     * @access private
	     */
		function sql_insert($commit = true, $id = false) {
			if ($this->_check_data() === true and !empty($this->_data_in)) {
				if ($id !== false) {
					$this->_data_in[$this->id_key] = $id;
				} else {
					if (isset($this->_data_in[$this->id_key])) {
						unset($this->_data_in[$this->id_key]);
					}
				}

				$sql = $this->prepare_insert($this->_data_in);

				$sm = vivvo_lite_site::get_instance();

				if (!PEAR::isError($sql)) {

					$sm->debug_push("sql:", $sql->query);

					if ($commit == true || !vivvo_lite_site::get_instance()->get_db()->supports('transactions')) {
						$hook = 'db_' . get_class($this->data_object) . '_insert';

						if (vivvo_hooks_manager::call($hook . '_pre', array(&$this)) !== false) {

							$res = $sql->execute($this->_data_in);

							if (PEAR::isError($res)) {
								$sm->debug_push("sql:", $res->getMessage(),1);
								return false;
							}

							vivvo_hooks_manager::call($hook . '_post', array(&$this));

						} elseif (!vivvo_hooks_manager::get_status()) {
							return false;
						}
					}else {
						$this->stack[] = $sql;
					}
				}else{
					$sm->debug_push("sql:", $sql->getMessage(),1);
				}

				/**
				 * call on_insert method
				 *
				 * {@link vivvo_post_object::on_insert}
				 */
				if (is_a($this->data_object, 'vivvo_post_object')){
					$this->data_object->on_insert($this);
				}
			}else{
				return false;
			}
			$this->metod = 'insert';
			return true;
		}

		/**
	     * Deletes row from table
	     *
	     * @return boolean
	     * @access private
	     */
		function sql_delete($commit = true){
			if ($this->_data_in[$this->id_key]){
				$db = vivvo_lite_site::get_instance()->get_db();
				$sql = 'DELETE FROM `' . $this->_sql_table . '` WHERE ' . $this->id_key . ' = ' . $db->quote($this->_data_in[$this->id_key], 'integer');

				if (defined ('VIVVO_DEBUG_MODE')) echo "<hr/>$sql<hr/>\n";
				$sm = vivvo_lite_site::get_instance();
				$sm->debug_push("sql:", $sql->query);

				if ($commit == true || !$db->supports('transactions')){

					$hook = 'db_' . get_class($this->data_object) . '_delete';

					if (vivvo_hooks_manager::call($hook . '_pre', array(&$this)) !== false) {

						$res = $db->query($sql);

						if (is_a($res, 'mdb2_error')){
							$sm->debug_push("sql:", $res->getMessage(),1);
							return false;
						}

						vivvo_hooks_manager::call($hook . '_post', array(&$this));

					} elseif (!vivvo_hooks_manager::get_status()) {
						return false;
					}

				}else{
					$this->stack[] = $sql;
				}

				/**
				 * call on_delete method
				 *
				 * {@link vivvo_post_object::on_delete}
				 */
				if (is_a($this->data_object, 'vivvo_post_object')){
					$this->data_object->on_delete($this);
				}

				return true;
			}
			$this->metod = 'delete';
			return false;
		}

		function sql_delete_list($sql_table, $where, $from = false, $commit = true){
			if ($from){
				$sql = 'DELETE FROM ' . $from . ' WHERE ' . $where;
				$sql_table = preg_replace('/^'.VIVVO_DB_PREFIX.'/', '', $from);

			}else{
				$sql = 'DELETE FROM `' . VIVVO_DB_PREFIX . $sql_table . '` WHERE ' . $where;
			}

			$db = vivvo_lite_site::get_instance()->get_db();

			if ($commit == true || !$db->supports('transactions')){

				$hook = 'db_' . $sql_table . '_delete_list';

				if (vivvo_hooks_manager::call($hook . '_pre', array(&$this)) !== false) {

					$res = $db->query($sql);

					if (PEAR::isError($res)) {
						vivvo_lite_site::get_instance()->debug_push("sql:", $res->getMessage(),1);
						return false;
					}

					vivvo_hooks_manager::call($hook . '_post', array(&$this));

				} elseif (!vivvo_hooks_manager::get_status()) {
					return false;
				}
			}else{
				$this->stack[] = $sql;
			}
			vivvo_cache::get_instance()->cleanup();
			return true;
		}

		/**
		 * Start transaction if support
		 *
		 * @return boolean
		 */
		function start_transaction(){
			$db = vivvo_lite_site::get_instance()->get_db();
			if ($db->supports('transactions')){
				$db->beginTransaction();
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Commit transaction if support, execute all sql command from stack and evacuates stack
		 * If catch error, feed _add_error.
		 * If tansaction crushed execute rollback
		 *
		 * @return	boolean
		 */
		function commit_transaction(){
			$db = vivvo_lite_site::get_instance()->get_db();
			if (!$db->supports('transactions')){
				while($this->stack){
					$sql = array_pop($this->stack);
					echo $sql;
					$res = $db->query($sql);
					if (is_a($res, 'mdb2_error')){
						//mdbd2 error handling
						$this->_add_error("Invalid query: " . mysql_error());
					}else{
						return false;
					}
				}
				return true;
			}if ($db->inTransaction()){
				$error_condition = false;
				while(!empty($this->stack)){
					$sql = array_pop($this->stack);
					$res = $db->query($sql);
					if (is_a($res, 'mdb2_error')){
						$this->_add_error("Invalid query: " . mysql_error());
						$error_condition = true;
					}
				}
				if ($error_condition) {
					$res = $db->rollback();
					return false;
				}else{
					$res = $db->commit();
					$this->stack = array();
					return true;
				}
			}
			return false;
		}



		/**
		 * Get last id after insert
		 *
		 * @return	mixed	integer on success a false on fail
		 */
		function get_work_id(){
			if ($this->_data_in[$this->id_key] == '') {
				if ($this->metod == 'insert') {
					$last_id = vivvo_lite_site::get_instance()->get_db()->lastInsertID();
					if (!PEAR::isError($last_id)) {
						return $last_id;
					} else {
						return false;
					}
				}
			} else {
				return $this->_data_in[$this->id_key];
			}
		}

		/**
	     * Gether table field flags
	     *
	     * @access private
	     *
	     */
		function _set_data_flags(){
			$this->_data_flags = vivvo_lite_site::get_instance()->get_db_manager()->get_fields_definitions($this->_sql_table);
		}

		function get_data_types(){
			$types = array();
			if (is_array($this->_data_in)){
				$keys = array_keys($this->_data_in);
				foreach ($keys as $v){
					 $types[] = $this->_data_flags[$v]['type'];
				}
			}
			return $types;
		}

		function get_data_fields($data){
			return array_keys($data);
		}

		/**
	     * Checks if there are any empty fielsd that are marked as not null
	     *
	     * @access private
	     *
	     * @return boolean
	     */
		function _check_data(){
			if (!$this->_data_flags) $this->_set_data_flags();

			$i = 0;
			foreach ($this->_data_flags as $k => $v) {
				if (($k != $this->id_key) && ($this->_data_flags['notnull'] == 1) && !isset($this->_data_in[$k])){
					if ($this->_data_in[$k] == null){
						$this->missing[$i] = $k;
						$i++;
					}
				}
			}

			if (!empty($this->missing)) {
				echo 'missing:';
				$this->_add_error('You must set values for ' . implode(', ',$this->missing) . '.');
				return false;
			}else{
				return true;
			}
		}

		/**
	     * Output from missing fields.
	     *
	     * @access private
	     *
	     * @return DOMElement
	     */

		function error_message(){
			if (is_array($this->missing)){
				return implode(', ',$this->missing);
			}else{
				return;
			}
		}

		/**
		 * Vivvo post master constuctor
		 *
		 * @param vivvo_site $sm
		 * @return vivvo_post_master
		 */
		function vivvo_post_master($sm = null){

			if ($sm == null) {
				$sm = vivvo_lite_site::get_instance();
			}

			$this->_dump_manager = $sm->get_dump_manager();
			$this->_error_manager = $sm->get_error_manager();
		}
}

#EOF