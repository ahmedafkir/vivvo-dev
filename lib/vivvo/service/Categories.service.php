<?php
/* =============================================================================
 * $Revision: 5696 $
 * $Date: 2010-08-02 11:46:28 +0200 (Mon, 02 Aug 2010) $
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
	 * @see  'lib/vivvo/core/Categories.class.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Categories.class.php');

	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');

	/**
	 * Category service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 */
	class category_service extends vivvo_service {

		/**
		 * Add new category
		 *
		 * @param	array	$in_category
		 * @return	boolean	true on success or false on fail
		 */
		function add_category($in_category) {
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('category_add', array(&$in_category))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user) {
				if($sm->user->is_admin()) {

					$in_category['category_name'] = trim($in_category['category_name']);

					if (empty($in_category['sefriendly'])) {
						$in_category['sefriendly'] = make_sefriendly($in_category['category_name']);
					} else {
						$in_category['sefriendly'] = make_sefriendly($in_category['sefriendly']);
					}

					$sefriendly = secure_sql($in_category['sefriendly']);
					$sql = 'SELECT id FROM '.VIVVO_DB_PREFIX."categories WHERE sefriendly = '$sefriendly' LIMIT 1 UNION
							SELECT id FROM ".VIVVO_DB_PREFIX."tags_groups WHERE url = '$sefriendly' LIMIT 1";
					if (($res = $sm->get_db()->query($sql)) && !($res instanceof MDB2_Error) && ($res->numRows() > 0)) {
						$this->set_error_code(2101);
						return false;
					}

					$redirect = $in_category['redirect'];
					array_walk($in_category, 'array_htmlspecialchars');
					$in_category['redirect'] = $redirect;

					if (!empty($in_category['form']) and $in_category['form'] == 'link' and empty($in_category['redirect'])) {
						$this->set_error_code(12, vivvo_lang::get_instance()->get_value('LNG_DB_categories_redirect'));
						return false;
					}

					$category = new Categories();

					$category_list = $sm->get_categories();
					$in_category['order_num'] = $category_list->get_number_of_category() + 1;

					if ($category->populate($in_category, true) === false){
						$this->set_error_info($category->get_error_info());
						return false;
					}

					$this->_post_master->set_data_object($category);

					if ($this->_post_master->sql_insert()) {

						$id = $this->_post_master->get_work_id();
						$category->set_id($id);
						$sm->get_url_manager()->set_param('search_id', $id);

						if (!vivvo_hooks_manager::call('category_add_postinsert', array(&$category))) {
							return vivvo_hooks_manager::get_status();
						}

						$fm = $sm->get_file_manager();
						if ($fm->is_uploaded('CATEGORY_abstact_image')) {
							$abstract_image = $fm->upload('CATEGORY_abstact_image');
							if ($abstract_image != false) {
								$category->set_image($abstract_image);
								$this->_post_master->set_data_object($category);
								$this->_post_master->sql_update();
							}
						}
						admin_log($sm->user->get_username(), 'Created category #' . $this->_post_master->get_work_id());
						return true;
					} else {
						$this->set_error_code(2102);
						return false;
					}
				} else {
					$this->set_error_code(2103);
					return false;
				}
			} else {
				$this->set_error_code(2104);
				return false;
			}
		}

		/**
		 * Delete category
		 *
		 * @param	integer	$category_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_category($category_id){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('category_delete', array(&$category_id) ) )
				return vivvo_hooks_manager::get_status();


			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					$category_id = (int) $category_id;
					$category_list = $sm->get_categories();
					$category = $category_list->list[$category_id];
					if (isset($category)) {
						$this->_post_master->set_data_object($category);
						if ($this->_post_master->sql_delete()){
							admin_log($sm->user->get_username(), 'Deleted category #' . $category_id);
							return true;
						}else{
							$this->set_error_code(2105);
							return false;
						}
					}else{
						$this->set_error_code(2106);
						return false;
					}
				}else{
					$this->set_error_code(2107);
					return false;
				}
			}else{
				$this->set_error_code(2108);
				return false;
			}
		}

		/**
		 * Edit category
		 *
		 * @param	integer	$category_id
		 * @param	array	$data
		 * @return	boolean	true on success or false on fail
		 */
		function edit_category($category_id, $data){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('category_edit', array(&$category_id, &$data) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					$category_id = (int)$category_id;

					if (isset($data['category_name'])){
						$data['category_name'] = trim($data['category_name']);
					}

					if (empty($data['sefriendly'])) {
						if (isset($data['category_name']) && !empty($data['category_name'])) {
							$data['sefriendly'] = make_sefriendly($data['category_name']);
						}
					} else {
						$data['sefriendly'] = make_sefriendly($data['sefriendly']);
					}

					if (isset($data['sefriendly'])) {
						$sefriendly = secure_sql($data['sefriendly']);
						$sql = 'SELECT id FROM '.VIVVO_DB_PREFIX."categories WHERE sefriendly = '$sefriendly' AND id <> $category_id LIMIT 1 UNION
								SELECT id FROM ".VIVVO_DB_PREFIX."tags_groups WHERE url = '$sefriendly' LIMIT 1";
						if (($res = $sm->get_db()->query($sql)) && ($res->numRows() > 0)) {
							$this->set_error_code(2101);
							return false;
						}
					}

					$category_list = $sm->get_categories();
					$category = $category_list->list[$category_id];

					$redirect = $in_category['redirect'];
					array_walk($in_category, 'array_htmlspecialchars');
					$in_category['redirect'] = $redirect;

					if (!empty($data['form']) and $data['form'] == 'link' and empty($data['redirect'])) {
						$this->set_error_code(12, vivvo_lang::get_instance()->get_value('LNG_DB_categories_redirect'));
						return false;
					}

					if (isset($category)) {
						if (!$category->populate($data, true)) {
							$this->set_error_info($category->get_error_info());
							return false;
						}
						$this->_post_master->set_data_object($category);
						if ($this->_post_master->sql_update()){
							$fm = $sm->get_file_manager();
							if ($fm->is_uploaded('CATEGORY_abstact_image')){
								$abstract_image = $fm->upload('CATEGORY_abstact_image');
								if ($abstract_image != false) {
									$category->set_image($abstract_image);
									$this->_post_master->set_data_object($category);
									$this->_post_master->sql_update();
								}
							}
							admin_log($sm->user->get_username(), 'Edited category #' . $category_id);
							return true;
						} else {
							$this->set_error_code(2110);
							return false;
						}
					}else {
						$this->set_error_code(2111);
						return false;
					}
				}else {
					$this->set_error_code(2112);
					return false;
				}
			}else{
				$this->set_error_code(2113);
				return false;
			}
		}

		/**
		 * Reorder category
		 *
		 * @param	integer	$parent_id
		 * @param	array	$data
		 * @param	integer	$order_num
		 * @return	boolean	true on success or false on fail
		 */
		function reorder_category($parent_id, $data, $order_num = 0){
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('category_reorder', array(&$parent_id, &$data, &$order_num) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()){
					$categories = $sm->get_categories();
					if (is_array($data) && !empty($data)){
						foreach ($data as $cat_id => $new_data){
							if (isset($categories->list[$cat_id])){
								$categories->list[$cat_id]->set_parent_cat($parent_id);
								$categories->list[$cat_id]->set_order_num($order_num);
								$order_num++;
								$this->_post_master->set_data_object($categories->list[$cat_id]);
								if (!$this->_post_master->sql_update()){
									$this->set_error_code(2114);
									return false;
								}
								if (!empty($new_data)) {
									$order_num = $this->reorder_category($cat_id, $new_data, $order_num);
								}
							}
						}
					}
					admin_log($sm->user->get_username(), 'Reordered category #' . $order_num);
					return $order_num;
				}else{
					$this->set_error_code(2115);
					return false;
				}
			}else{
				$this->set_error_code(2116);
				return false;
			}
		}

		/**
		 * Delete category image
		 *
		 * @param	integer	$category_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_image($category_id) {
			if (!$this->check_token()) return false;

			if( !vivvo_hooks_manager::call('category_deleteImage', array(&$category_id) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				if($sm->user->is_admin()) {
					$category_list = new Categories_list();
					$category = $category_list->get_category($category_id);

					if ($category !== false){
						$fm = $sm->get_file_manager();
						if ($category->image != ''){
							if ($fm->delete_fs(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $category->image)){
								$category->set_image('');
								$this->_post_master->set_data_object($category);
								if ($this->_post_master->sql_update()){
									admin_log($sm->user->get_username(), 'Edited category #' . $category_id);
									return true;
								}else{
									$this->set_error_code(2117);
									return false;
								}
							}
						}
					}else{
						$this->set_error_code(2118);
						return false;
					}
				}else{
					$this->set_error_code(2119);
					return false;
				}
			}else{
				$this->set_error_code(2120);
				return false;
			}
		}
	}
?>