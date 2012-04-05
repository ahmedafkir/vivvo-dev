<?php
/* =============================================================================
 * $Revision: 4834 $
 * $Date: 2010-03-30 11:39:23 +0200 (Tue, 30 Mar 2010) $
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
	 * @see  'lib/vivvo/core/UserFilters.class.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/UserFilters.class.php');
	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");

	/**
	 * User filter service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	class UserFilters_service extends vivvo_service {

		/**
		 * Add new user filter
		 * @param	string	$filter_name
		 * @param	string	$filter_section
		 * @param	array	$filter_data
		 * @return	boolean	true on success or false on fail
		 */
		function add_UserFilters($filter_name, $filter_section, $filter_data){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('UserFilter_add', array(&$filter_name, &$filter_section, &$filter_data) ) )
				return vivvo_hooks_manager::get_status();


			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				$UserFilter = new UserFilters();

				$data = array();

				$data['user_id'] = $sm->user->userid;
				$data['name'] = htmlspecialchars($filter_name, ENT_QUOTES, 'UTF-8');
				$data['section'] = htmlspecialchars($filter_section, ENT_QUOTES, 'UTF-8');
				$data['query'] = base64_encode(serialize($filter_data));

				if ($UserFilter->populate($data, true) === false){
					$this->set_error_info($UserFilter->get_error_info());
					return false;
				}

				$this->_post_master->set_data_object($UserFilter);

				if ($this->_post_master->sql_insert()){
					$id = $this->_post_master->get_work_id();
					$UserFilter->set_id($id);

					if (!vivvo_hooks_manager::call('UserFilter_add_postinsert', array(&$UserFilter))) {
						return vivvo_hooks_manager::get_status();
					}

					admin_log($sm->user->get_username(), 'Added user filter #' . $id);
					return true;
				} else {
					$this->set_error_code(2501);
					return false;
				}
			} else {
				$this->set_error_code(2502);
				return false;
			}
		}

		/**
		 * Delete user filter
		 *
		 * @param	integer	$filter_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_UserFilters($filter_id) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('UserFilter_delete', array(&$filter_id))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user) {
				$filter_list = new UserFilters_list();
				$filter_list->get_UserFilter_by_id($filter_id);

				if ($filter_list->sql_delete_list($this->_post_master)){
					admin_log($sm->user->get_username(), 'Deleted user filter #' . $filter_id);
					return true;
				}else{
					$this->set_error_code(2503);
					return false;
				}
			}else{
				$this->set_error_code(2504);
				return false;
			}
		}
	}
?>