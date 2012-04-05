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
 * @subpackage	configuration
 * @version		$Revision: 5385 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */

	/**
	 * preferences class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	configuration
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see 		vivvo_post_object
	 */
	class preferences extends vivvo_post_object {

		/**
		 * Id
		 *
		 * @var integer
		 */
		var $id;
		/**
		 * Variable name
		 *
		 * @var string
		 */
		var $variable_name;
		/**
		 * Variable property
		 *
		 * @var string
		 */
		var $variable_property;
		/**
		 * Variable value
		 *
		 * @var string
		 */
		var $variable_value;
		/**
		 * Module
		 *
		 * @var string
		 */
		var $module;
		/**
		 * Domain id
		 *
		 * @var integer
		 */
		var $domain_id = 1;

		/**
		 * Regular expression restriction
		 *
		 * @var string
		 */
		var $reg_exp;

		/**
		 * Sets name of sql table
		 *
		 * @var string
		 */
		var $_sql_table = 'configuration';

		/**
		 * Set error
		 *
		 * @return dump_message
		 */

		function &set_error_code($error= false, $aditional_info = ''){
			if (!$error) $error = 13;
			if (!$this->_dump_handler) $this->set_dump_handler();
			$lang = vivvo_lang::get_instance();
			$this->dump_object =& $this->_dump_handler->add_dump('error', $error, $lang->get_value('LNG_ERROR_'. $error));
			$this->dump_object->additional_info = $lang->get_value('LNG_CONF_' . $this->variable_name) . $this->variable_name;
			return $this->dump_object;
		}

		/**
		 * Sets id
		 *
		 * @param	integer	$id
		 */
		function set_id($id){
			$id = (int)$id;
			$this->id = $id;
		}
		/**
		 * Sets variable name
		 *
		 * @param	string	$variable_name
		 */
		function set_variable_name($variable_name){
			$this->variable_name = $variable_name;
		}
		/**
		 * Sets variable property
		 *
		 * @param	string	$variable_property
		 */
		function set_variable_property($variable_property){
			$this->variable_property = $variable_property;
		}
		/**
		 * Sets variable value
		 *
		 * @param	string	$variable_value
		 */
		function set_variable_value($variable_value){
			$this->variable_value = $variable_value;
		}
		/**
		 * Sets module
		 *
		 * @param	string	$module
		 */
		function set_module($module){
			$this->module = $module;
		}
		/**
		 * Sets domain id
		 *
		 * @param	integer	$domain_id
		 */
		function set_domain_id($domain_id){
			$domain_id = (int)$domain_id;
			$this->domain_id = $domain_id;
		}
		function set_reg_exp($reg_exp){
			$this->reg_exp = $reg_exp;
		}
		/**
		 * Gets id
		 *
		 * @return	integer
		 */
		function get_id(){
			return $this->id;
		}
		/**
		 * Gets variable name
		 *
		 * @return	string
		 */
		function get_variable_name(){
			return $this->variable_name;
		}
		/**
		 * Gets variable property
		 *
		 * @return	string
		 */
		function get_variable_property(){
			return $this->variable_property;
		}
		/**
		 * Gets variable value
		 *
		 * @return	string
		 */
		function get_variable_value(){
			return $this->variable_value;
		}
		/**
		 * Gets module
		 *
		 * @return	integer
		 */
		function get_module(){
			return $this->module;
		}
		/**
		 * Gets domain id
		 *
		 * @return	integer
		 */
		function get_domain_id(){
			return $this->domain_id;
		}
		function get_reg_exp(){
			return $this->reg_exp;
		}
		function check_value($value){
			if ($this->reg_exp == '' || preg_match('/^' . $this->reg_exp . '$/', $value)){
				return true;
			}
			return false;
		}

		function on_delete($post_master = null) {
			vivvo_cache::get_instance()->delete('configuration');
		}

		function on_insert($post_master = null) {
			vivvo_cache::get_instance()->delete('configuration');
		}

		function on_update($post_master = null) {
			vivvo_cache::get_instance()->delete('configuration');
		}
	}

	/**
	 * preferences_list class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	configuration
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see 		vivvo_post_object
	 */
	class preferences_list extends vivvo_db_list {

		/**
		 * Sets name of sql table
		 *
		 * @var	string
		 */
		var $_sql_table = 'configuration';
		/**
		 * Sets vivvo post object
		 *
		 * @var unknown_type
		 */
		var $post_object_type = 'preferences';

		/**
		 * Creates default query for tbl_configuration
		 *
		 */
		function _default_query(){
			$this->_query->set_fileds('*');
			$this->_query->set_from(VIVVO_DB_PREFIX . 'configuration');
		}

		/**
		 * Gets list of preferences object
		 *
		 * @param	string	$variable_name
		 * @return	mixed	list on succes or false on faild
		 */
		function &get_preference_by_variable_name($variable_name){
			$variable_name = secure_sql($variable_name);
			$this->_default_query();
			$this->_query->add_where("(variable_name = '" . $variable_name . "')");
			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}

		function &get_preference_by_all_variable($variable_name,$variable_property,$variable_value,$module,$domain_id = '', $all = 0){
			$this->_default_query();
			if ($variable_name != ''){
				$variable_name = secure_sql($variable_name);
				$this->_query->add_where("(variable_name = '" . $variable_name . "')");
			}
			if ($variable_property != ''){
				$this->_query->add_where("(variable_property = '" . $variable_property . "')");
			}
			if ($variable_value != ''){
				$this->_query->add_where("(variable_value = '" . $variable_value . "')");
			}
			if ($module != ''){
				$this->_query->add_where("(module = '" . $module . "')");
			}else{
				$this->_query->add_where("(module = '' OR ISNULL(module))");
			}
			if ($domain_id != ''){
				$this->_query->add_where("(domain_id = '" . $domain_id . "')");
			}

			$this->set_list();
			if (!empty($this->list)){
				if ($all){
					return $this->list;
				}else{
					return current($this->list);
				}
			}else{
				return false;
			}
		}

		function on_update($post_master = null) {
			vivvo_cache::get_instance()->delete('configuration');
		}
	}

#EOF