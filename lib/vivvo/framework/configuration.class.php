<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
 *
 * Vivvo CMS v4.6 (build 6085)
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
 * @version		$Revision: 6846 $
 * @category	CMS
 * @author		Slobodan Utvic <utvara@spoonlabs.com>
 */


	/**
	 * Configuration class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	configuration
	 * @version		$Revision: 6846 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see vivvo_object
	 */
	class configuration extends vivvo_object  {

		/**
		 * Configuration data
		 *
		 * @var array
		 */
		var $_config_data = array();

		/**
		 * Post master
		 *
		 * @var vivvo_post_master
		 */
		var $_post_master;

		/**
		 * Load configuration data from database
		 */
		function load_configuration($force = false) {
			if (!$force and ($data = vivvo_cache::get_instance()->get('configuration')) !== false) {
                if (!empty($data)) {
                    $this->_config_data = $data;
                    return;
                }
            }
            
            $db = vivvo_lite_site::get_instance()->get_db();
            $res = $db->query('SELECT * FROM ' . VIVVO_DB_PREFIX . 'configuration');
            $this->_config_data = array();
            if (!PEAR::isError($res)) {
                while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {
                    $this->_config_data[] = $row;
                }
                $res->free();
            }
            vivvo_cache::get_instance()->put('configuration', $this->_config_data);
		}

		/**
		 * Sets variable name
		 *
		 * @param	string	$module		Module name
		 * @param	boolean	$define		Define configuration data as constants
		 * @return	array				Configuration array slice
		 */
		function get_configuration($module = '', $define = false){

			if (empty ($this->_config_data)){
				$this->load_configuration();
			}

			$data = array();
			if ($module != ''){
				foreach ($this->_config_data as $key => $val){
					if ($this->_config_data[$key]['module'] == $module){
						$data[] =& $this->_config_data[$key];
					}
				}
			}else if ($module == 'all'){
				$data =& $this->_config_data;
			}else{
				foreach ($this->_config_data as $key => $val){
					if ($this->_config_data[$key]['module'] == ''){
						$data[] =& $this->_config_data[$key];
					}
				}
			}

			if ($define === true){
				if ($data){
					foreach ($data as $conf){
						if ($conf['variable_property'] != ''){
							$const_name = $conf['variable_name'] . '_' . $conf['variable_property'];
						}else{
							$const_name = $conf['variable_name'];
						}
						if (!defined($const_name)){
							define($const_name, $conf['variable_value']);
						}
					}
				}
			}

			return $data;
		}

		/**
		 * Sets variable name
		 *
		 * @param	string	$module		Module name
		 * @param	boolean	$define		Define configuration data as constants
		 * @param	boolean	$domain_id	Domain identifyer
		 *
		 * @return	array	Formated configuration data
		 */
		function get_configuration_property_list($module = '', $define = false, $domain_id = 0){
			$data = $this->get_configuration($module, $define);
			$obj_data = array();
			if (is_array($data)){
				foreach ($data as $def){
					if ($def['variable_property'] != ''){
						if (!isset($obj_data[$def['variable_name']])){
							$obj_data[$def['variable_name']] = array();
						}
						$obj_data[$def['variable_name']][$def['variable_property']] = $def['variable_value'];
					}else{
						$obj_data[$def['variable_name']] = $def['variable_value'];
					}
				}
			}
			return $obj_data;
		}

		/**
		 * Remove gobal configuration from database
		 *
		 * @param string $name Config name
		 *
		 * @return boolean True on success, false on fail
		 */
		function remove_global($name){
			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');
			if (!$this->_post_master) $this->set__post_master();
			$config_list = new preferences_list();
			if ($config_list->get_preference_by_all_variable($name,'','','',1)){
				vivvo_cache::get_instance()->delete('configuration');
				return $config_list->sql_delete_list($this->_post_master);
			}
		}

		/**
		 * Remove module configuration
		 *
		 * @param string $module_name	Module name
		 * @param string $name			Variable name
		 * @param string $value			Variable value
		 *
		 * @return boolean True on success, false on fail
		 */
		function remove_from_module($module_name, $name, $value = ''){
			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');
			if (!$this->_post_master) $this->set__post_master();
			$config_list = new preferences_list();
			if ($config_list->get_preference_by_all_variable($name,'',$value,$module_name,1)){
				vivvo_cache::get_instance()->delete('configuration');
				return $config_list->sql_delete_list($this->_post_master);
			}
		}


		/**
		 * Remove module configuration
		 *
		 * @param string $name Module name
		 *
		 * @return boolean True on success, false on fail
		 */
		function remove_module($name){
			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');
			if (!$this->_post_master) $this->set__post_master();
			$config_list = new preferences_list();
			if ($config_list->get_preference_by_all_variable('','','',$name,1)){
				vivvo_cache::get_instance()->delete('configuration');
				return $config_list->sql_delete_list($this->_post_master);
			}
		}

		/**
		 * Add configuration data
		 *
		 * @param string $name		Variable name
		 * @param string $property	Variable property name
		 * @param string $value		Variable value
		 * @param string $module	Module name
		 * @param string $reg_exp	Variable value restriction
		 * @param string $doamin_id	Doamin restriction
		 *
		 * @return boolean True on success, false on fail
		 */
		function add_conf($name, $property, $value, $module = '', $reg_exp = '', $domain_id = 1) {

			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');

			if (!$this->_post_master) {
				$this->set__post_master();
			}

			$config = new preferences();
			$config->set_variable_name($name);
			$config->set_variable_property($property);
			$config->set_variable_value($value);
			$config->set_module($module);
			$config->set_reg_exp($reg_exp);
			$config->set_domain_id($domain_id);
			$this->_post_master->set_data_object($config);
			$result = $this->_post_master->sql_insert();

			vivvo_cache::get_instance()->delete('configuration');

			return $result;
		}

		/**
		 * Set configuration data
		 *
		 * @param string $name		Variable name
		 * @param string $property	Variable property name
		 * @param string $value		Variable value
		 * @param string $module	Module name
		 * @param string $reg_exp	Variable value restriction
		 * @param string $doamin_id	Doamin restriction
		 *
		 * @return boolean True on success, false on fail
		 */
		function set_conf($name, $property='', $value, $module = '', $reg_exp = '', $domain_id = 1) {

			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_preference.php');

			if (!$this->_post_master) {
				$this->set__post_master();
			}

			$config_list = new preferences_list();

			$conf = $config_list->get_preference_by_all_variable($name, '', '', '');

			if ($conf) {
				if ($property != '') {
					$conf->set_variable_property($property);
				}

				$conf->set_variable_value($value);

				if ($module != '') {
					$conf->set_module($module);
				}

				if ($reg_exp != '') {
					$conf->set_reg_exp($reg_exp);
				}

				$conf->set_domain_id($domain_id);
				$this->_post_master->set_data_object($conf);
				$this->_post_master->sql_update();

				vivvo_cache::get_instance()->delete('configuration');

			} else {
				$this->add_conf($name, $property, $value, $module, $reg_exp, $domain_id);
			}
		}


		/**
		 * Set post master
		 *
		 */
		function set__post_master(){
			require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
			$this->_post_master = new vivvo_post_master();
		}

	}

#EOF