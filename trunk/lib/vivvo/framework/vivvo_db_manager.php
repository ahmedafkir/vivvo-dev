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
	 * vivvo_db_manager class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	database
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_db_manager extends vivvo_object {
		/**
		 * DB
		 *
		 * @var MDB2_DRIVER_common
		 */
		var $_db;

		function get_table_fields($table){
			return vivvo_lite_site::get_instance()->get_db()->manager->listTableFields($table);
		}

		function get_fields_definitions ($table){

			$db = vivvo_lite_site::get_instance()->get_db();
			$table_res = $db->query('SELECT * FROM `' . $table . '` WHERE 1 < 0');

			if (!PEAR::isError($table_res)) {

				$new_table_fields = $db->reverse->tableInfo($table_res);
				$table_res->free();
				$new_fields_def = array();

				if (!PEAR::isError($new_table_fields)){
					if (is_array($new_table_fields)){
						foreach ($new_table_fields as $field){
							$new_fields_def[$field['name']] = $field;
						}
						return $new_fields_def;
					}
				}
			}
			return false;
		}

		function table_exists($table) {
			$tables = vivvo_lite_site::get_instance()->get_db()->manager->listTables();
			return in_array($table, $tables);
		}

		function check_table($table, $fields){
			if ($this->table_exists($table)){
				$fields_def = $this->get_fields_definitions($table);
				if ($fields_def){
					if (is_array($fields)){
						foreach ($fields as $key => $def){
							if (!key_exists($key, $fields_def)){
								return false;
							}
							$test = array_diff_assoc($fields, $fields_def);
							if (!empty($test)){
								return false;
							}
						}
					}
				}
				return true;
			}
			//print_r($db->getTableFieldDefinition($table, 'question'));
		}

		/**
		 * Db manager
		 *
		 * @param vivvo_site $site_manager
		 */
		function vivvo_db_manager($site_manager = null){
			$db = vivvo_lite_site::get_instance()->get_db();
			$db->loadModule('Manager', null, true);
			$db->loadModule('Reverse', null, true);
		}
	}

#EOF