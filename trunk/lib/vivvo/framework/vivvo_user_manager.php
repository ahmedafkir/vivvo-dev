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
	 * vivvo_user_manager class
	 *
	 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
	 * @copyright	SpoonLabs
	 * @package		vivvo_framework
	 * @subpackage	framework
	 * @version		$Revision: 5385 $
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class vivvo_user_manager extends vivvo_object{
		/**
		 * DB post master
		 *
		 * @access protected
		 * @var vivvo_post_master
		 */
		var $_post_master;

		/**
		 * Vivvo user related configuration
		 * In configuration table module = 'user'
		 *
		 * @var array
		 */
		var $_configuration = array();
		var $_login_handler;

		/**
		 * References to loaded user data
		 *
		 * @var array
		 */
		var $_user_stack = array();

		/**
		 * References to loaded group data
		 *
		 * @var array
		 */
		var $_group_stack = array();

		/**
		 * Load user related configuration
		 */
		function set__configuration () {
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$this->_configuration = $configuration->get_configuration_property_list('user');
		}

		/**
		 * Get appropriate object/class name based on vivvo user source
		 *
		 * @param vivvo_site 	$sm				Site manager
		 * @param string 		$type			Object type (user|user_list|group|groue_list)
		 * @param string		$domain			Vivvo user source
		 * @param boolean 		$class_only		retrive only class name
		 *
		 * @return object|string|false
		 */
		function &_get_object($sm, $type, $domain = '', $class_only = false){
			switch ($type){
				case 'group':
					$file = 'file';
					$object = 'group_object';
					break;
				case 'group_list':
					$file = 'file';
					$object = 'group_list';
					break;
				case 'user':
					$file = 'file';
					$object = 'user_object';
					break;
				case 'user_list':
					$file = 'file';
					$object = 'user_list';
					break;
				default:
					return false;
			}
			if (!empty($this->_configuration)){
				if ($domain == '' || empty($this->_configuration[$domain])) $domain = VIVVO_USER_SOURCE;
				if (!empty($this->_configuration[$domain])){
					if (file_exists(VIVVO_FS_INSTALL_ROOT . $this->_configuration[$domain][$file])){
						require_once(VIVVO_FS_INSTALL_ROOT . $this->_configuration[$domain][$file]);
						if (class_exists($this->_configuration[$domain][$object])){
							if ($class_only){
								return $this->_configuration[$domain][$object];
							}else{
								return new $this->_configuration[$domain][$object]();
							}
						}
					}elseif (file_exists(VIVVO_FS_PLUGIN_ROOT . $this->_configuration[$domain][$file])){
						require_once(VIVVO_FS_PLUGIN_ROOT . $this->_configuration[$domain][$file]);
						if (class_exists($this->_configuration[$domain][$object])){
							if ($class_only){
								return $this->_configuration[$domain][$object];
							}else{
								return new $this->_configuration[$domain][$object]();
							}
						}
					}
				}
			}
			return false;
		}

		/**
		 * Get instance of appropriate group object
		 *
		 * @param	vivvo_site	$sm		Site manager
		 * @param 	string		$domain	Vivvo user source
		 *
		 * @return	group	Group object
		 */
		function &get_group_object ($sm = null, $domain = ''){
			$group = $this->_get_object($sm, 'group', $domain);
			if ($group){
				return $group;
			}
			return new group($sm);
		}

		/**
		 * Get instance of appropriate group list object
		 *
		 * @param	vivvo_site	$sm		Site manager
		 * @param 	string		$domain	Vivvo user source
		 *
		 * @return	group_list	Group list object
		 */
		function &get_group_list ($sm = null, $domain = ''){
			$group_list = $this->_get_object(null, 'group_list', $domain);
			if ($group_list){
				return $group_list;
			}
			return new group_list($sm);
		}

		/**
		 * Get instance of appropriate user object
		 *
		 * @param	vivvo_site	$sm		Site manager
		 * @param 	string		$domain	Vivvo user source
		 *
		 * @return	user	User object
		 */
		function &get_user_object ($sm = null, $domain = ''){
			$user = $this->_get_object($sm, 'user', $domain);
			if ($user){
				return $user;
			}
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Users.class.php');
			return new Users($sm);
		}

		/**
		 * Get instance of appropriate user list object
		 *
		 * @param	vivvo_site	$sm		Site manager
		 * @param 	string		$domain	Vivvo user source
		 *
		 * @return	user_list	User list object
		 */
		function get_user_list ($sm = null, $domain = ''){
			$user_list = $this->_get_object(null, 'user_list', $domain);

			if ($user_list){
				return $user_list;
			}
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Users.class.php');
			return new Users_list($sm);
		}


		/**
		 * Login user
		 *
		 * @param	string	$username		User name
		 * @param 	string	$password		User password
		 *
		 * @return	boolean	login status
		 */
		function login ($username, $password){
			if (!isset($_SESSION['vivvo'])){
				$_SESSION['vivvo'] = array();
			}
			if (isset($_SESSION['vivvo']['login_fail_time']) && ($_SESSION['vivvo']['login_fail_time'] < (VIVVO_START_TIME - 60*60))){
				$_SESSION['vivvo']['login_fail'] = 0;
				$_SESSION['vivvo']['login_fail_time'] = 0;
			}

			if ($_SESSION['vivvo']['login_fail'] >= 3){
				$this->set_error_code(2751);
				return false;
			}

			$user_list = $this->get_user_list(null);
			$user = $user_list->get_user_data($username, $password);
			if ($user == false){
				$_SESSION['vivvo']['login_fail'] = $_SESSION['vivvo']['login_fail'] + 1;
				$_SESSION['vivvo']['login_fail_time'] = time();
				if ($_SESSION['vivvo']['login_fail'] >= 3){
					$this->set_error_code(2751);
				}elseif ($_SESSION['vivvo']['login_fail'] >= 2){
					$this->set_error_code(2752);
				}else{
					$this->set_error_code(2753);
				}
				return false;
			}else{
				if ($user->get_activated() == '1'){
					$sm =& vivvo_lite_site::get_instance();
					$sm->user = $user;
					$sm->user->set_privileges();
					if (!isset($_SESSION['vivvo'])){
						$_SESSION['vivvo'] = array();
					}
					$_SESSION['vivvo']['user_id'] = $sm->user->get_id();
					$_SESSION['vivvo']['user_domain'] = VIVVO_USER_SOURCE;
					$_SESSION['vivvo']['login_fail'] = 0;
					$_SESSION['vivvo']['login_fail_time'] = 0;
					session_regenerate_id();
					return true;
				}else{
					$this->set_error_code(2754);
					return false;
				}
			}
		}

		/**
		 * Get user from session
		 *
		 * @param 	string	$domain	Vivvo user source
		 *
		 * @return	user|false	User object or false
		 */
		function get_user_from_session($domain = ''){
			if ($domain == '' || empty($this->_configuration[$domain])) $domain = VIVVO_USER_SOURCE;
			$user_list = $this->get_user_list(null, $domain);
			return $user_list->get_user_from_session();
		}

		function &get_user_by_id($id, $domain = ''){
			if ($domain == '' || empty($this->_configuration[$domain])) $domain = VIVVO_USER_SOURCE;
			if (is_array($this->_user_stack[$domain]) && key_exists($id, $this->_user_stack[$domain])){
				return $this->_user_stack[$domain][$id];
			}
			$id = (int) $id;
			$user_list = $this->get_user_list(null, $domain);
			if (!is_array($this->_user_stack[$domain])) $this->_user_stack[$domain] = array();
			$this->_user_stack[$domain][$id] = $user_list->get_user_by_id($id);
			return $this->_user_stack[$domain][$id];
		}

		function get_user_by_name($name, $domain = ''){
			if ($domain != '' && empty($this->_configuration[$domain])){
				$user_list = $this->get_user_list(null, $domain);
				return $user_list->get_user_by_name($name);
			}else{
				$users = array ();
				foreach ($this->_configuration as $k => $v){
					$user_list = $this->get_user_list(null, $k);
					$user_list->get_user_by_name($name);
					$users[$k] = $user_list->get_list_ids();
				}
				return $users;
			}
		}

		function get_user_by_exact_name($e_name, $domain = ''){
			if ($domain != '' && empty($this->_configuration[$domain])){
				$user_list = $this->get_user_list(null, $domain);
				return $user_list->get_user_by_exact_name($e_name);
			}else{
				$users = array ();
				foreach ($this->_configuration as $k => $v){
					$user_list = $this->get_user_list(null, $k);
					$user_list->get_user_by_exact_name($e_name);

					$users[$k] = $user_list->get_list_ids();
				}
				return $users;
			}
		}

		function &get_group_by_id($id, $domain = ''){
			if ($domain == '' || empty($this->_configuration[$domain])) $domain = VIVVO_USER_SOURCE;
			if (is_array($this->_group_stack[$domain]) && key_exists($id, $this->_group_stack[$domain])){
				return $this->_group_stack[$domain][$id];
			}
			$id = (int) $id;
			$group_list = $this->get_group_list(null, $domain);
			if (!isset($this->_group_stack[$domain])) $this->_group_stack[$domain] = array();
			$this->_group_stack[$domain][$id] = $group_list->get_group_by_id($id);
			return $this->_group_stack[$domain][$id];
		}

		function get_admin_login_tempalte(){
			$user_list_class = $this->_get_object(null, 'user_list', VIVVO_USER_SOURCE, true);
			if ($user_list_class){
				return call_user_func(array($user_list_class, 'get_admin_login_tempalte'));
			}
			return '';
		}

		function set_login_tempalte(){

			$template = '';
			$user_list_class = $this->_get_object(null, 'user_list', VIVVO_USER_SOURCE, true);

			if ($user_list_class){
				$template = call_user_func(array($user_list_class, 'get_login_tempalte'));
			}

			if (!defined('VIVVO_LOGIN_TEMPLATE')){
				if ($template != ''){
					define('VIVVO_LOGIN_TEMPLATE', $template);
				}else{
					define('VIVVO_LOGIN_TEMPLATE', 'box/login.tpl');
				}
			}
		}


		function get_groups_with_privilege ($name, $type = false, $id = false){
			$out_groups = array();
			$group_list = $this->get_group_list(null);
			$groups = $group_list->get_all_groups();
			if (!empty($groups)){
				foreach($groups as $k => $v){
					if ($groups[$k]->is($name, $type, $id)){
						$out_groups[$k] = $groups[$k];
					}
				}
			}
			return $out_groups;
		}

		function get_groups_without_privilege ($name, $type = false, $id = false){
			$out_groups = array();
			$group_list = $this->get_group_list(null);
			$groups = $group_list->get_all_groups();
			if (!empty($groups)){
				foreach($groups as $k => $v){
					if (!$groups[$k]->is($name, $type, $id)){
						$out_groups[$k] = $groups[$k];
					}
				}
			}
			return $out_groups;
		}

		/**
		 * Get vivvo post master
		 *
		 * @return vivvo_post_master
		 */
		function &get_post_master(){

			if (!$this->_post_master){
				require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');
				$this->_post_master = new vivvo_post_master(null);
			}
			return $this->_post_master;
		}

		function add_user_to_group($user_id, $group_id, $expire = false){
			if (is_integer($user_id) && is_integer($group_id) && $user_id > 0 && $group_id > 0){
				$gul = new group_user_list(null);
				$group_user = $gul->check_user_group($user_id, $group_id);
				if ($group_user === false){
					$gu = new group_user(null);
					$gu->set_user_id($user_id);
					$gu->set_group_id($group_id);
					if ($expire !== false){
						$gu->set_expires($expire);
					}
					$pm =& $this->get_post_master();
					$pm->set_data_object($gu);
					return $pm->sql_insert();
				}
				return true;
			}else{
				return false;
			}
		}

		function remove_user_from_group($user_id, $group_id){
			if ($user_id > 0 && $group_id > 0){
				$gul =& new group_user_list(null);
				$group_user =& $gul->check_user_group($user_id, $group_id);
				if ($group_user !== false){
					$pm =& $this->get_post_master();
					$pm->set_data_object($group_user);
					return $pm->sql_delete();
				}
				return true;
			}else{
				return false;
			}
		}

		function set_user_groups($user_id, $group_ids){
			if (is_numeric($group_ids) && $group_ids > 0){
				$group_ids = array($group_ids);
			}else if (!is_array($group_ids)){
				return false;
			}
			//print_r($group_ids);
			$add_groups = $group_ids;

			$gul =& new group_user_list(null);
			$group_user =& $gul->get_user_groups($user_id, true);

			$result = true;

			$pm =& $this->get_post_master();

			if (!empty($group_user)){
				$current_groups = $gul->get_property_list('group_id');
				$delete_groups = array_diff($current_groups, $group_ids);
				$add_groups = array_diff($group_ids, $current_groups);
				if (!empty($delete_groups)){
					foreach ($delete_groups as $dg){
						$result = $result && $this->remove_user_from_group($user_id, $dg);
					}
				}
			}

			if (!empty($add_groups)){
				$gu =& new group_user(null);
				$gu->set_user_id($user_id);
				foreach ($add_groups as $add_group){
					$gu->set_group_id($add_group);
					$pm->set_data_object($gu);
					$result = $result && $pm->sql_insert();
				}
			}
			return $result;
		}

		function set_user_group_expire($user_id, $group_id, $expire = 0){
			if (is_integer($user_id) && is_integer($group_id) && $user_id > 0 && $group_id > 0){
				$gul =& new group_user_list(null);
				$group_user =& $gul->check_user_group($user_id, $group_id);
				if ($group_user !== false){
					$pm =& $this->get_post_master();
					$pm->set_data_object($group_user);
					$group_user->set_expires($expire);
					return $pm->sql_update();
				}
				return false;
			}else{
				return false;
			}
		}

		/**
		 * Add privilege to user (merge with existing)
		 *
		 * @param integer 		$user_id	user_id
		 * @param string		$privilege	privilege name
		 * @param string		$value		Value 0|1 or object type
		 * @param array|integer	$ids		id restictions
		 *
		 * @return boolean
		 */
		function add_user_privileges($user_id, $privilege, $value = 1, $ids = 0){
			$user =& $this->get_user_by_id($user_id);
			if ($user){
				$current_privileges = $user->get_user_privileges();
				$current_privileges = $this->_add_privilege($current_privileges,  $privilege, $value , $ids);
				$this->_save_user_privileges($user, $current_privileges);
			}
			return false;
		}

		/**
		 * Remove user privilege
		 *
		 * @param integer 		$user_id	user_id
		 * @param string		$privilege	privilege name
		 * @param string		$value		Value 0|1 or object type
		 * @param array|integer	$ids		id restictions
		 *
		 * @return boolean
		 */
		function remove_user_privileges($user_id, $privilege, $value = false, $ids = false){
			$user =& $this->get_user_by_id($user_id);
			if ($user){
				$current_privileges = $user->get_user_privileges();
				$current_privileges = $this->_remove_privilege($current_privileges, $privilege, $value, $ids);
				$this->_save_user_privileges($user, $current_privileges);
			}
			return false;
		}

		/**
		 * Set user privilege (overwrite existing)
		 *
		 * @param integer 		$user_id	user_id
		 * @param string		$privilege	privilege name
		 * @param string		$value		Value 0|1 or object type
		 * @param array|integer	$ids		id restictions
		 *
		 * @return boolean
		 */
		function set_user_privileges($user_id, $privilege, $value = false, $ids = false){
			$user =& $this->get_user_by_id($user_id);
			if ($user){
				$current_privileges = $user->get_user_privileges();
				$current_privileges = $this->_set_privilege($current_privileges, $privilege, $value, $ids);
				$this->_save_user_privileges($user, $current_privileges);
			}
			return false;
		}

		/**
		 * Add privilege to group (merge with existing)
		 *
		 * @param integer 		$group_id	group_id
		 * @param string		$privilege	privilege name
		 * @param string		$value		Value 0|1 or object type
		 * @param array|integer	$ids		id restictions
		 *
		 * @return boolean
		 */
		function add_group_privileges($group_id, $privilege, $value = 1, $ids = 0){
			$group =& $this->get_group_by_id($group_id);
			if ($group){
				$current_privileges = $group->get_group_privileges();
				$current_privileges = $this->_add_privilege($current_privileges, $privilege, $value, $ids);
				return $this->save_group_privileges($group, $current_privileges);
			}
			return false;
		}

		/**
		 * Remove group privilege
		 *
		 * @param integer 		$group_id	group_id
		 * @param string		$privilege	privilege name
		 * @param string		$value		Value 0|1 or object type
		 * @param array|integer	$ids		id restictions
		 *
		 * @return boolean
		 */
		function remove_group_privileges($group_id, $privilege, $value = false, $ids = false){
			$group =& $this->get_group_by_id($group_id);
			if ($group){
				$current_privileges = $group->get_group_privileges();
				$current_privileges = $this->_remove_privilege($current_privileges, $privilege, $value, $ids);
				return $this->save_group_privileges($group, $current_privileges);
			}
			return false;
		}

		/**
		 * Set group privilege (overwrite existing)
		 *
		 * @param integer 		$group_id	group_id
		 * @param string		$privilege	privilege name
		 * @param string		$value		Value 0|1 or object type
		 * @param array|integer	$ids		id restictions
		 *
		 * @return boolean
		 */
		function set_group_privileges($group_id, $privilege, $obj_type = false, $ids = false){
			$group =& $this->get_group_by_id($group_id);
			if ($group){
				$current_privileges = $group->get_group_privileges();
				$current_privileges = $this->_set_privilege($current_privileges, $privilege, $value, $ids);
				return $this->save_group_privileges($group, $current_privileges);
			}
			return false;
		}

		function _add_privilege($current_privileges, $privilege, $value = 1, $ids = 0){
			if (!empty($privilege)){
				if (!isset($current_privileges[$privilege])){
					$current_privileges[$privilege] = array();
				}
				if ($value === 1 || $value === 0){
					$current_privileges[$privilege] = $value;
				}elseif (!empty($value)){
					if (is_numeric($ids)){
						$ids = array($ids);
					}

					if (is_array($ids)){
						if (!empty($current_privileges[$privilege][$value])){
							$current_privileges[$privilege][$value] = array_merge($ids, $current_privileges[$privilege][$value]);
							$current_privileges[$privilege][$value] = array_unique($current_privileges[$privilege][$value]);
						}else{
							$current_privileges[$privilege][$value] = $ids;
						}
					}
				}
			}
			return $current_privileges;
		}

		function _remove_privilege($current_privileges, $privilege, $value = false, $ids = false){
			if (!empty($privilege)){
				if (isset($current_privileges[$privilege])){
					if ($value === false){
						unset($current_privileges[$privilege]);
					}else{
						if ($ids === false){
							unset($current_privileges[$privilege][$value]);
						}else{
							if (is_numeric($ids)){
								$ids = array($ids);
							}
							if (is_array($current_privileges[$privilege][$value])){
								$current_privileges[$privilege][$value] = array_diff($current_privileges[$privilege][$value], $ids);
							}else{
								unset($current_privileges[$privilege][$value]);
							}
						}
					}
				}
			}
			return $current_privileges;
		}

		function _set_privilege($current_privileges, $privilege, $value = 1, $ids = 0){
			if (!empty($privilege)){
				if (!isset($current_privileges[$privilege])){
					$current_privileges[$privilege] = array();
				}
				if ($value === 1 || $value === 0){
					$current_privileges[$privilege] = $value;
				}elseif (!empty($value)){
					if (is_numeric($ids)){
						$ids = array($ids);
					}

					if (is_array($ids)){
						$current_privileges[$privilege][$value] = $ids;
					}
				}
			}
			return $current_privileges;
		}

		function save_group_privileges(&$group, $new_privileges){
			if ($group){
				$pm =& $this->get_post_master();

				$group_priv =& new group_privileges(null);
				if (is_array($new_privileges)){
					$new_privileges = json_encode($new_privileges);
				}
				$group_priv->set_privileges($new_privileges);


				if ($group->group_privileges_id){
					$group_priv->set_id($group->group_privileges_id);
					$pm->set_data_object($group_priv);
					return $pm->sql_update();
				}else{
					$group_priv->set_group_id($group->id);
					$group_priv->set_user_source(VIVVO_USER_SOURCE);
					$pm->set_data_object($group_priv);
					return $pm->sql_insert();
				}
			}
			return false;
		}

		function _save_user_privileges(&$user, $new_privileges){
			if ($user && is_array($new_privileges)){
				$pm =& $this->get_post_master();

				$user->set_user_privileges($new_privileges);

				$pm->set_data_object($user);
				return $pm->sql_update();
			}
			return false;
		}

		function is($privileges, $name, $type = false, $id = false){
			if (isset($privileges[$name])){
				if (is_array($privileges[$name])){
					if ($type !== false){
						if (isset($privileges[$name][$type])){
							if (is_array($privileges[$name][$type])){
								if ($id !== false){
									if (in_array($id, $privileges[$name][$type]) || in_array('0', $privileges[$name][$type])){
										return true;
									}
								}elseif (in_array(0, $privileges[$name][$type])){
									return true;
								}
							}elseif (!empty($privileges[$name][$type])){
								return true;
							}
						}elseif (in_array($type, $privileges[$name]) || in_array('0', $privileges[$name])){
							return true;
						}
					}elseif (!empty($privileges[$name])){
						//Hack for EDITOR privileges
						if (isset($privileges[$name]['Categories']) && empty($privileges[$name]['Categories'])){
							return false;
						}
						return true;
					}
				}elseif ($privileges[$name] == 1){
					return true;
				}
			}
			return false;
		}

		function can_change_status($privileges, $to, $from = false){
			if (isset($privileges['CHANGE_STATUS']) && !empty($privileges['CHANGE_STATUS'])){
				foreach ($privileges['CHANGE_STATUS'] as $priv){
					if ($from){
						if (in_array($from, $priv['from']) || in_array('*', $priv['from'])){
							if ($to == '_' || (in_array($to, $priv['to']) || in_array('*', $priv['to']))){
								return true;
							}
						}
					}else{
						if (in_array($to, $priv['to']) || in_array('*', $priv['to'])){
							return true;
						}
					}
				}
			}
			return false;
		}


		function get_privilege_object_ids ($privileges, $name, $type){
			if (isset($privileges[$name])){
				if (is_array($privileges[$name]) && $type){
					if (isset($privileges[$name][$type]) && is_array($privileges[$name][$type])){
						return $privileges[$name][$type];
					}
				}
			}
			return array();
		}

		function logout () {

			session_destroy();

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user) {

				class_exists('vivvo_post_master') or require VIVVO_FS_FRAMEWORK . 'vivvo_post.php';

				$post_master = new vivvo_post_master();
				$sm->user->reset_user_hash();
				$post_master->set_data_object($sm->user);
				$post_master->sql_update();
			}

			$sm->user = false;
			return true;
		}

		function activate_user_module($name){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->set_conf('VIVVO_USER_SOURCE', '', $name);
		}

		function register_user_module($name, $file, $user_object, $user_list, $group_object, $group_list){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->set_conf($name, 'file', $file, 'user');
			$configuration->set_conf($name, 'user_object', $user_object, 'user');
			$configuration->set_conf($name, 'user_list', $user_list, 'user');
			$configuration->set_conf($name, 'group_object', $group_object, 'user');
			$configuration->set_conf($name, 'group_list', $group_list, 'user');
		}

		function unregister_user_module($name){
			$configuration = vivvo_lite_site::get_instance()->get_configuration();
			$configuration->remove_from_module('user', $name);
		}

		function vivvo_user_manager($sm = null){
			$this->set__configuration();
			$this->set_login_tempalte();
		}
	}

	/**
	* Group object
	*
	* @license		http://www.vivvo.net/license.php
	* @copyright	SpoonLabs
	* @author		Slobodan Utvic <utvara@spoonlabs.com>
	* @see		vivvo_post_object
	* @version		Vivvo Lite - Generic Database Engine
	*/
	class group extends vivvo_post_object {

		/**
		* Name of table without tblprefix
		*
		* @var   string
		*/
		var $_sql_table = 'group';

		/**
		* id
		* Database field type:	int(10) unsigned
		* Null status:		NO
		*
		* @var	integer	$id
		*/
		var $id;

		/**
		* name
		* Database field type:	varchar(45)
		* Null status:		NO
		*
		* @var	string	$name
		*/
		var $name;

		/**
		* domain_id
		* Database field type:	int(10) unsigned
		* Null status:		YES
		*
		* @var	integer	$domain_id
		*/
		var $domain_id;

		/**
		* privileges
		* Database field type:	text
		* Null status:		YES
		*
		* @var	string	$privileges
		*/
		var $privileges;

		var $group_privileges;

		var $group_privileges_id;

		/**
		* Sets {@link $id}
		*
		* @param	integer	$id
		*/
		function set_id($id){
			$this->id = $id;
		}

		/**
		* Sets {@link $name}
		*
		* @param	string	$name
		*/
		function set_name($name){
			$this->name = $name;
		}

		/**
		* Sets {@link $domain_id}
		*
		* @param	integer	$domain_id
		*/
		function set_domain_id($domain_id){
			$this->domain_id = $domain_id;
		}

		/**
		* Sets {@link $privileges}
		*
		* @param	string	$privileges
		*/
		function set_privileges($privileges){
			if (is_array($privileges)){
				$this->privileges = json_encode($privileges);
				$this->group_privileges = $privileges;
			}else{
				$this->privileges = $privileges;
				$this->group_privileges = json_decode($this->privileges, true);
			}
		}

		function set_group_privileges_id($group_privileges_id){
			$this->group_privileges_id = $group_privileges_id;
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
		* Gets $name
		*
		* @return	string
		*/
		function get_name(){
			return $this->name;
		}

		/**
		* Gets $domain_id
		*
		* @return	integer
		*/
		function get_domain_id(){
			return $this->domain_id;
		}

		/**
		* Gets $privileges
		*
		* @return	string
		*/
		function get_privileges(){
			return $this->privileges;
		}

		function get_group_privileges_id(){
			return $this->group_privileges_id;
		}

		function is($name, $type = false, $id = false){
			return vivvo_user_manager::is($this->group_privileges, $name, $type, $id);
		}

		function can($name, $type = false, $id = false){
			return vivvo_user_manager::is($this->group_privileges, $name, $type, $id);
		}

		function has($name, $type = false, $id = false){
			return vivvo_user_manager::is($this->group_privileges, $name, $type, $id);
		}

		function can_change_status($to, $from = false){
			return vivvo_user_manager::can_change_status($this->group_privileges, $to, $from);
		}

		function get_privilege_object_ids ($name, $type){
			return vivvo_user_manager::get_privilege_object_ids($this->group_privileges, $name, $type);
		}
	}

	/**
	* Group list
	*
	* @license		http://www.vivvo.net/license.php
	* @copyright	SpoonLabs
	* @author		Slobodan Utvic <utvara@spoonlabs.com>
	* @see			vivvo_db_paged_list
	* @version		Vivvo Lite - Generic Database Engine
	*/
	class group_list extends vivvo_db_list {
		var $_sql_table = 'group';
		var $post_object_type = 'group';

		function _default_query(){
			$this->_query->set_from('`' . VIVVO_DB_PREFIX . 'group` AS g LEFT JOIN ' . VIVVO_DB_PREFIX . 'group_privileges AS gp ON g.id = gp.group_id AND gp.user_source=\'vivvo@localhost\'');
			$this->_query->add_fields('g.*');
			$this->_query->add_fields('gp.privileges');
			$this->_query->add_fields('gp.id as group_privileges_id');
		}

		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where('(g.id = \'' . $condition . '\')');
				break;
				case 'id_in':
					$this->_query->add_where('(g.id IN (' . $condition . '))');
				break;
				case 'name':
					$this->_query->add_where('(g.name = \'' . $condition . '\')');
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
			}
			if (isset($params['search_name'])){
				$this->add_filter('name', $params['search_name']);
			}

			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			$this->set_order($order, $search_direction);

			$limit = (int) $limit;
			$this->_query->set_limit($limit);
			$offset = (int) $offset;
			$this->_query->set_offset($offset);
			$this->_default_query(true);

			$this->set_list();
			return $this->list;
		}

		function set_order($order, $search_direction = ''){
			switch ($order){
				case 'id':
					$this->_query->add_order('g.id' . $search_direction);
					break;
				case 'name':
					$this->_query->add_order('g.name' . $search_direction);
					break;
				default:
					$order = 'id';
					$this->_query->add_order('g.id' . ' DESC');
					break;
			}
		}

		function get_all_groups(){
			$this->_default_query();
			$this->set_order('name');
			$this->set_list();
			if (!empty($this->list)){
				return $this->list;
			}else{
				return false;
			}
		}

		function get_group_by_id($id){
			$id = (int) $id;
			$this->_default_query();
			$this->add_filter('id', $id);
			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}

		function get_groups($group_ids){
			if (!empty($group_ids)){
				$this->_default_query();
				$this->add_filter('id_in', implode(',', $group_ids));
				$this->set_list();
				if (!empty($this->list)){
					return $this->list;
				}
			}
			return array();
		}

		function _array_merge ($array1, $array2){
			$result = array();
			$k1 = array_keys($array1);
			$k2 = array_keys($array2);

			$only_in_k1 = array_diff($k1,$k2);
			$only_in_k2 = array_diff($k2,$k1);
			$intersect = array_intersect($k1,$k2);

			if (!empty($only_in_k1)){
				foreach($only_in_k1 as $prop){
					$result[$prop] = $array1[$prop];
				}
			}

			if (!empty($only_in_k2)){
				foreach($only_in_k2 as $prop){
					$result[$prop] = $array2[$prop];
				}
			}

			if (!empty($intersect)){
				foreach($intersect as $prop){
					if (is_array($array2[$prop])){
						$result[$prop] = array_merge_recursive($array1[$prop], $array2[$prop]);
						$subprops = array_keys($result[$prop]);
						//foreach($subprops as $sp){
						//	if (!empty($result[$prop][$sp]) && is_array($result[$prop][$sp])){
						//		$result[$prop][$sp] = array_unique($result[$prop][$sp]);
						//	}
						//}
					}else{
						$result[$prop] = max ($array1[$prop], $array2[$prop]);
					}
				}
			}

			return $result;
		}

		function merge_privileges($group_ids){
			$result = array();
			if (!empty($group_ids)){
				foreach ($group_ids as $id){
					if (isset($this->list[$id]) && !empty($this->list[$id]->group_privileges)){
						$result = $this->_array_merge($result, $this->list[$id]->group_privileges);
					}
				}
			}
			return $result;
		}

	}


	/**
	* Group User object
	*
	* @license		http://www.vivvo.net/license.php
	* @copyright	SpoonLabs
	* @author		Slobodan Utvic <utvara@spoonlabs.com>
	* @see		vivvo_post_object
	* @version		Vivvo Lite - Generic Database Engine
	*/
	class group_user extends vivvo_post_object {

		/**
		* Name of table without tblprefix
		*
		* @var   string
		*/
		var $_sql_table = 'group_user';

		/**
		* id
		* Database field type:	int(10) unsigned
		* Null status:		NO
		*
		* @var	integer	$id
		*/
		var $id;

		/**
		* group_id
		* Database field type:	int(6) unsigned
		* Null status:		NO
		*
		* @var	integer	$group_id
		*/
		var $group_id;

		/**
		* user_id
		* Database field type:	int(9) unsigned
		* Null status:		NO
		*
		* @var	integer	$user_id
		*/
		var $user_id;

		/**
		* expires
		* Database field type:	datetime
		* Null status:		YES
		*
		* @var	string	$expires
		*/
		var $expires;

		/**
		* Sets {@link $id}
		*
		* @param	integer	$id
		*/
		function set_id($id){
			$this->id = $id;
		}

		/**
		* Sets {@link $group_id}
		*
		* @param	integer	$group_id
		*/
		function set_group_id($group_id){
			$this->group_id = $group_id;
		}

		/**
		* Sets {@link $user_id}
		*
		* @param	integer	$user_id
		*/
		function set_user_id($user_id){
			$this->user_id = $user_id;
		}

		/**
		* Sets {@link $expires}
		*
		* @param	string	$expires
		*/
		function set_expires($expires){
			$this->expires = $expires;
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
		* Gets $group_id
		*
		* @return	integer
		*/
		function get_group_id(){
			return $this->group_id;
		}

		/**
		* Gets $user_id
		*
		* @return	integer
		*/
		function get_user_id(){
			return $this->user_id;
		}

		/**
		* Gets $expires
		*
		* @return	string
		*/
		function get_expires(){
			return $this->expires;
		}

	}

	/**
	* Group User list
	*
	* @license		http://www.vivvo.net/license.php
	* @copyright	SpoonLabs
	* @author		Slobodan Utvic <utvara@spoonlabs.com>
	* @see			vivvo_db_paged_list
	* @version		Vivvo Lite - Generic Database Engine
	*/
	class group_user_list extends vivvo_db_list {
		var $_sql_table = 'group_user';
		var $post_object_type = 'group_user';

		function _default_query(){
			$this->_query->set_from(
									VIVVO_DB_PREFIX . 'group_user ');
			$this->_query->add_fields('*');
		}

		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where('(id = \'' . $condition . '\')');
				break;
				case 'group_id':
					$this->_query->add_where('(group_id = \'' . $condition . '\')');
				break;
				case 'user_id':
					$this->_query->add_where('(user_id = \'' . $condition . '\')');
				break;
				case 'expires':
					$this->_query->add_where('(expires = \'' . $condition . '\')');
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
			}
			if (isset($params['search_group_id'])){
				$this->add_filter('group_id', $params['search_group_id']);
			}
			if (isset($params['search_user_id'])){
				$this->add_filter('user_id', $params['search_user_id']);
			}
			if (isset($params['search_expires'])){
				$this->add_filter('expires', $params['search_expires']);
			}


			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'group_id':
					$this->_query->add_order('group_id' . $search_direction);
					break;
				case 'user_id':
					$this->_query->add_order('user_id' . $search_direction);
					break;
				case 'expires':
					$this->_query->add_order('expires' . $search_direction);
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

				$this->set_list();
				return $this->list;
		}

		function get_user_groups($user_id, $obj = false){
			$this->_default_query();
			$this->add_filter('user_id' , $user_id);

			$this->set_list();
			if (!empty($this->list)){
				if ($obj){
					return $this->list;
				}else{
					return $this->get_property_list('group_id');
				}
			}else{
				return array();
			}
		}

		function &check_user_group($user_id, $group_id){
			$this->_default_query();
			$this->add_filter('user_id' , $user_id);
			$this->add_filter('group_id' , $group_id);

			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}
	}

	/**
	 * Group Privileges object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see		vivvo_post_object
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class group_privileges extends vivvo_post_object {

		/**
		 * Name of table without tblprefix
		 *
		 * @var   string
		 */
		var $_sql_table = 'group_privileges';

		/**
		 * id
		 * Database field type:	int(10) unsigned
		 * Null status:		NO
		 *
		 * @var	integer	$id
		 */
		var $id;

		/**
		 * group_id
		 * Database field type:	int(10) unsigned
		 * Null status:		NO
		 *
		 * @var	integer	$group_id
		 */
		var $group_id;

		/**
		 * user_source
		 * Database field type:	varchar(50)
		 * Null status:		NO
		 *
		 * @var	string	$user_source
		 */
		var $user_source;

		/**
		 * privileges
		 * Database field type:	text
		 * Null status:		YES
		 *
		 * @var	string	$privileges
		 */
		var $privileges;



		/**
		 * Sets {@link $id}
		 *
		 * @param	integer	$id
		 */
		function set_id($id){
			$this->id = $id;
		}

		/**
		 * Sets {@link $group_id}
		 *
		 * @param	integer	$group_id
		 */
		function set_group_id($group_id){
			$this->group_id = $group_id;
		}

		/**
		 * Sets {@link $user_source}
		 *
		 * @param	string	$user_source
		 */
		function set_user_source($user_source){
			$this->user_source = $user_source;
		}

		/**
		 * Sets {@link $privileges}
		 *
		 * @param	string	$privileges
		 */
		function set_privileges($privileges){
			$this->privileges = $privileges;
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
		 * Gets $group_id
		 *
		 * @return	integer
		 */
		function get_group_id(){
			return $this->group_id;
		}

		/**
		 * Gets $user_source
		 *
		 * @return	string
		 */
		function get_user_source(){
			return $this->user_source;
		}

		/**
		 * Gets $privileges
		 *
		 * @return	string
		 */
		function get_privileges(){
			return $this->privileges;
		}

	}

	/**
	 * Group Privileges list
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @see			vivvo_db_paged_list
	 * @version		Vivvo Lite - Generic Database Engine
	 */
	class group_privileges_list extends vivvo_db_list {
		var $_sql_table = 'group_privileges';
		var $post_object_type = 'group_privileges';

		function _default_query(){
			$this->_query->set_from(
									VIVVO_DB_PREFIX . 'group_privileges ');
			$this->_query->add_fields('*');
		}

		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'id':
					$this->_query->add_where('(id = \'' . $condition . '\')');
				break;
				case 'group_id':
					$this->_query->add_where('(group_id = \'' . $condition . '\')');
				break;
				case 'group_id_in':
					$condition = secure_sql_in($condition);
					$this->_query->add_where('(group_id IN (' . $condition . '))');
				break;
				case 'user_source':
					$this->_query->add_where('(user_source = \'' . $condition . '\')');
				break;
				case 'privileges':
					$this->_query->add_where('(privileges = \'' . $condition . '\')');
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
			}
			if (isset($params['search_group_id'])){
				$this->add_filter('group_id', $params['search_group_id']);
			}
			if (isset($params['search_user_source'])){
				$this->add_filter('user_source', $params['search_user_source']);
			}
			if (isset($params['search_privileges'])){
				$this->add_filter('privileges', $params['search_privileges']);
			}


			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			switch ($order){
				case 'id':
					$this->_query->add_order('id' . $search_direction);
					break;
				case 'group_id':
					$this->_query->add_order('group_id' . $search_direction);
					break;
				case 'user_source':
					$this->_query->add_order('user_source' . $search_direction);
					break;
				case 'privileges':
					$this->_query->add_order('privileges' . $search_direction);
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

			$this->set_list();
			return $this->list;
		}

		function get_group_privileges_by_group_ids ($ids){
			$this->_default_query();
			$this->add_filter('group_id_in' , $ids);
			$this->add_filter('user_source' , VIVVO_USER_SOURCE);
			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}
	}


	/**
	* vivvo user object
	*
	* @license		http://www.vivvo.net/license.php
	* @copyright	SpoonLabs
	* @author		Slobodan Utvic <utvara@spoonlabs.com>
	* @see			vivvo_post_object
	* @version		Vivvo Lite - Generic Database Engine
	*/
	class vivvo_user extends vivvo_post_object {
		var $id_key = 'userid';
		var $_sql_table = 'users';

		var $user_privileges;

		var $privileges;

		var $groups = array();

		function set_user_privileges($user_privileges){
			if (is_array($user_privileges)){
				$this->user_privileges = json_encode($user_privileges);
			}else{
				$this->user_privileges = $user_privileges;
			}
			//$this->user_privileges = json_encode(array('READ' => array('Categories' => array (1,2))));
		}

		function _merge_privileges($user_priv, $group_priv){
			if (!empty($user_priv)){
				$result = array();
				$k1 = array_keys($user_priv);
				$k2 = array_keys($group_priv);

				$only_in_k1 = array_diff($k1,$k2);
				$only_in_k2 = array_diff($k2,$k1);
				$intersect = array_intersect($k1,$k2);

				if (!empty($only_in_k1)){
					foreach($only_in_k1 as $prop){
						$result[$prop] = $user_priv[$prop];
					}
				}

				if (!empty($only_in_k2)){
					foreach($only_in_k2 as $prop){
						$result[$prop] = $group_priv[$prop];
					}
				}

				if (!empty($intersect)){
					foreach($intersect as $prop){
						$result[$prop] = $user_priv[$prop];
					}
				}
				return $result;
			}else{
				return $group_priv;
			}
		}

		function get_name(){
			return $this->username;
		}

		function get_href(){
			return $this->format_href(vivvo_lite_site::get_instance(), $this->get_id(), $this->username);
		}

		function get_json_privileges(){
			return json_encode($this->privileges);
		}

		function format_href($sm = null, $id, $username){
			return make_proxied_url('author/' . $username . '/');
		}

		/**
		* Gets $keywords
		*
		* @return	string
		*/
		function get_keywords(){
			return $this->username . ',' . $this->get_name() . ',' . VIVVO_WEBSITE_TITLE;
		}
		/**
		* Gets $description
		*
		* @return	string
		*/
		function get_description(){
			return $this->get_name() . ' on ' . VIVVO_WEBSITE_TITLE;
		}

		function get_admin_logout_url(){
			return 'index.php?login&amp;action=login&amp;cmd=logout';
		}

		function get_user_privileges(){
			return json_decode($this->user_privileges, true);
		}

		function is_admin(){
			return array_key_exists(2, $this->groups);
		}

		function in_group($group_id = false) {
			if ($group_id !== false){
				return array_key_exists($group_id, $this->groups);
			}
			return false;
		}

		function is($name, $type = false, $id = false){
			if ($this->is_admin()) return true;
			return vivvo_user_manager::is($this->privileges, $name, $type, $id);
		}

		function can($name, $type = false, $id = false){
			if ($this->is_admin()) return true;
			return vivvo_user_manager::is($this->privileges, $name, $type, $id);
		}

		function has($name, $type = false, $id = false){
			if ($this->is_admin()) return true;
			return vivvo_user_manager::is($this->privileges, $name, $type, $id);
		}

		function can_change_status($to, $from = false){
			if ($this->is_admin()) return true;
			return vivvo_user_manager::can_change_status($this->privileges, $to, $from);
		}

		function can_edit_article($article){
            if (is_a($article, 'Articles')){
                if ($this->is_admin()){
                    return true;
                }
                if ($article->user_id == $this->get_id() && $article->user_domain == VIVVO_USER_SOURCE){
                    if ($this->can('WRITE', 'Categories', $article->category_id)){
                        return true;
                    }
                }

                if ($this->is('EDITOR', 'Categories', $article->category_id)){
                    return true;
                }
            }
            return false;
		}

		function get_privilege_object_ids ($name, $type){
			return vivvo_user_manager::get_privilege_object_ids($this->privileges, $name, $type);
		}

		/**
		 * Gets user IP address.
		 *
		 * @return string  User IP address
		 */
		public function user_ip() {
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				// this is comma-space separated list, we need the last one in the stack
				$parts = explode(', ', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$ip = trim(array_pop($parts));
			} elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP'])) {
				// in a cluster, IP is an internal private LAN address of the web server network
				$ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
			} elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				// last resort
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		}

	} // end class vivvo_user

	/**
	* vivvo user list
	*
	* @license		http://www.vivvo.net/license.php
	* @copyright	SpoonLabs
	* @author		Slobodan Utvic <utvara@spoonlabs.com>
	* @see			vivvo_db_paged_list
	* @version		Vivvo Lite - Generic Database Engine
	*/
	class vivvo_users_list extends vivvo_db_paged_list {
		function add_filter($type, $condition = ''){

			$condition = secure_sql($condition);
			switch ($type){
				case 'userid':

				break;
				case 'first_name':

				break;
				case 'last_name':

				break;
				case 'name':

				break;
				case 'email_address':

				break;
				case 'username':

				break;
				case 'activated':

				break;
				case 'group':

				break;
				case 'concat_first_last':

				break;
				case 'created_before':

				break;
				case 'created_after':

				break;
				case 'md5':

				break;
				case 'forgot':

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
				if (is_array($params['search_id'])){
					if (!in_array(0, $params['search_id'])){
						$params['search_id'] = implode(',', $params['search_id']);
						$this->add_filter('userid',$params['search_id']);
					}
				}else{
					if ($params['search_id'] != 0){
						$this->add_filter('userid',$params['search_id']);
					}
				}
			}

			if (isset($params['search_first_name'])){
				$this->add_filter('first_name',$params['search_first_name']);
			}
			if (isset($params['search_last_name'])){
				$this->add_filter('last_name',$params['search_last_name']);
			}
			if (isset($params['search_email_address'])){
				$this->add_filter('email_address',$params['search_email_address']);
			}
			if (isset($params['search_username'])){
				$this->add_filter('username',$params['search_username']);
			}
			if (isset($params['search_activated']) && ($params['search_activated'] !== '')){
				$this->add_filter('activated',$params['search_activated']);
			}

			if (isset($params['search_user_type'])){
				if (is_array($params['search_user_type']) && (count ($params['search_user_type']) < 2)){
					$params['search_user_type'] = implode($params['search_user_type']);
				}
				if (is_array($params['search_user_type'])){
					if (!in_array('', $params['search_user_type'])){
						$params['search_user_type'] = implode(',', $params['search_user_type']);
						$this->add_filter('group',$params['search_user_type']);
					}
				}elseif ($params['search_user_type'] == 'staff'){
					$user_manager = vivvo_lite_site::get_instance()->get_user_manager();
					$groups = $user_manager->get_groups_with_privilege('ACCESS_ADMIN');
					$params['search_user_type'] = implode(',', array_keys($groups));

					$this->add_filter('group',$params['search_user_type']);
				}elseif ($params['search_user_type'] == 'members'){
					$user_manager = vivvo_lite_site::get_instance()->get_user_manager();
					$groups = $user_manager->get_groups_without_privilege('ACCESS_ADMIN');
					$params['search_user_type'] = implode(',', array_keys($groups));

					$this->add_filter('group',$params['search_user_type']);
				}else{
					if ($params['search_user_type'] != ''){
						$this->add_filter('group',$params['search_user_type']);
					}
				}
			}

			if (isset($params['search_query']) && !empty($params['search_query'])){
				$this->add_filter('name',$params['search_query']);
			}

			if (intval($params['search_search_date']) !== 0){
				$this->add_filter((($params['search_before_after'] === '1') ? 'created_before' : 'created_after'), $params['search_search_date']);
			}


			// search order //
			$search_direction = ($direction === 'descending') ? ' DESC' : ' ASC';

			$this->set_order($order, $search_direction);


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

		function set_order($order, $search_direction){
			switch ($order){
				case 'id':
					break;
				case 'first_name':
					break;
				case 'last_name':
					break;
				case 'email_address':
					break;
				case 'username':
					break;
				case 'name':
					break;
				case 'random':
					break;
				default:
					break;
			}
		}

		function get_author_by_name_concat($name){
			$this->_default_query();
			$this->add_filter('concat_first_last' , $name);
			$this->_query->add_order('username');
			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}

		function get_author_by_exact_name($e_name){
			$this->_default_query();
			$this->add_filter('username' , $e_name);

			$user_manager = vivvo_lite_site::get_instance()->get_user_manager();
			$groups = $user_manager->get_groups_with_privilege('ACCESS_ADMIN');
			$this->add_filter('group', implode(',', array_keys($groups)));

			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}

		function get_user_by_exact_name($e_name){
			$this->_default_query();
			$this->add_filter('username' , $e_name);
			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}

		function get_user_from_session() {
			return false;
		}

		function get_user_by_id($id){
			$id = (int) $id;
			$this->_default_query();
			$this->add_filter('userid' , $id);
			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}

		function get_author_by_id($id){
			$id = (int) $id;
			$this->_default_query();
			$this->add_filter('userid' , $id);

			$user_manager = vivvo_lite_site::get_instance()->get_user_manager();
			$groups = $user_manager->get_groups_with_privilege('ACCESS_ADMIN');
			$this->add_filter('group', implode(',', array_keys($groups)));

			$this->set_list();
			if (!empty($this->list)){
				return current($this->list);
			}else{
				return false;
			}
		}


		function get_users_by_ids($ids){
			$val = trim(implode(',', $ids));
			if (empty($val)){
				return false;
			}
			$this->search(array('search_id'=>$ids));
			if (empty($this->list)){
				return false;
			}else{
				return $this->list;
			}
		}

		function get_user_data($username = null, $passwd = null){
			if (($username != null) && ($passwd != null)){
				$passwd = md5($passwd);
				$this->_default_query();
				$this->add_filter('username' , $username);
				$this->add_filter('password' , $passwd);
				$this->set_list();

				if (!empty($this->list)) {
					return current($this->list);
				} else {
					return false;
				}
			}else{
				return false;
			}
		}

		function get_email_exists($email_address, $id = false){
			$this->_default_query();
			$this->add_filter('email_address' , $email_address);
			$this->set_list();
			if (!empty($this->list)){
				if ($id){
					$user = current($this->list);
					return $user->get_id();
				}else{
					return true;
				}
			}else{
				return false;
			}
		}

		function get_user_exists($username){
			$this->_default_query();
			$this->add_filter('username' , $username);
			$this->set_list();
			if (!empty($this->list)){
				return true;
			}else{
				return false;
			}
		}

		function &get_user_by_md5($param){
			$this->_default_query();
			$this->add_filter('md5' , $param );

			$this->set_list();
			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}

		function &get_user_by_forgot_md5($param){
			$this->_default_query();
			$this->add_filter('forgot' , $param );

			$this->set_list();
			if (!empty($this->list)){
				return  current($this->list);
			}else{
				return false;
			}
		}

		function get_search_params($sm, $in_params){
			$params = array ();

			if (!empty($in_params['search_limit'])){
				$params['search_limit'] = $in_params['search_limit'];
			}else{
				$params['search_limit'] = 10;
			}

			$params['search_options'] = array();

			if (isset($in_params['search_options']) && is_array($in_params['search_options']) && !empty($in_params['search_options'])) $params['search_options'] = $in_params['search_options'];

			if (!empty($in_params['search_id'])) $params['search_options']['search_id'] = $in_params['search_id'];
			if (isset($in_params['search_user_type'])) $params['search_options']['search_user_type'] = $in_params['search_user_type'];
			if (defined('VIVVO_ADMIN_MODE')){
				if (!empty($in_params['search_query'])) $params['search_options']['search_query'] = $in_params['search_query'];
				if (isset($in_params['search_activated'])) $params['search_options']['search_activated'] = $in_params['search_activated'];
				if (!empty($in_params['search_search_date'])) $params['search_options']['search_search_date'] = $in_params['search_search_date'];
				if (!empty($in_params['search_before_after'])) $params['search_options']['search_before_after'] = $in_params['search_before_after'];
			}else{
				$params['search_options']['search_activated'] = 1;
			}

			if (!empty($in_params['search_sort_by'])){
				$params['search_sort_by'] = $in_params['search_sort_by'];
			}else{
				$params['search_sort_by'] = 'userid';
			}

			if (isset($in_params['search_order']) && !empty($in_params['search_order'])){
				$params['search_order'] = $in_params['search_order'];
			}else{
				$params['search_order'] = 'descending';
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

		function get_search_params_from_url($sm){
			$um = $sm->get_url_manager();
			$params = vivvo_users_list::get_search_params($sm, $um->list);
			return $params;
		}

		function get_login_tempalte(){
			return '';
		}
	}

#EOF