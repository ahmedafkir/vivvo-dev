<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
 *
 * Vivvo CMS v4.6 (build 6082)
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
	 * @see  'lib/vivvo/core/Articles.class.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');

	/**
	 * @see  'lib/vivvo/service/Tags.service.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/service/Tags.service.php');


	/**
	 * Articles service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
 	 * @package		Vivvo
	 * @subpackage	service
	 * @see			vivvo_service
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class articles_service extends vivvo_service {

		private $tag_service;

		/**
		 * Add new article
		 *
		 * @param	array	$in_article
		 * @return	boolean	true on success or false on fail
		 */
		function add_article($in_article) {

			if (!$this->check_token()) {
				return false;
			}

			if (!vivvo_hooks_manager::call('article_add', array(&$in_article))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {

				$editor_restriction = $sm->user->get_privilege_object_ids('EDITOR', 'Categories');
				$writer_restriction = $sm->user->get_privilege_object_ids('WRITE', 'Categories');

				if (!empty($writer_restriction)) {

					$in_article['title'] = trim($in_article['title']);

					$article_list = new Articles_list();

					$new_order_num = $article_list->get_max('order_num');

					if ($new_order_num) {
						$in_article['order_num'] = $new_order_num + 1;
					} else {
						$in_article['order_num'] = 1;
					}

					if (!strlen(trim($in_article['sefriendly']))) {
						$in_article['sefriendly'] = make_sefriendly($in_article['title']);
						$source = 'title';
					} else {
						$in_article['sefriendly'] = make_sefriendly($in_article['sefriendly']);
						$source = 'sefriendly';
					}

					if (!empty($in_article['sefriendly'])) {
						$sql = 'SELECT id, sefriendly, status FROM ' . VIVVO_DB_PREFIX . 'articles WHERE `sefriendly` = \'' . secure_sql($in_article['sefriendly']) . '\' GROUP BY `status`; ';
						$res = $sm->get_db()->query($sql);

						$arrData = array();
						if (!PEAR::isError($res)) {
							$arrData = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
							$res->free();
						}

						// First check trashed articles, if sefriendly occupied, return error
						foreach ($arrData as $key => $value) {
							if ( $value['status'] == -2 ){
								$this->set_error_code(2037);
								return false;
							}
						}
						// If sefriendly found among Active articles
						foreach ($arrData as $key => $value) {
							if ( is_array($value) && count($value) > 0 && $value['status'] > -2 ) {
								if ($source == 'sefriendly') {
									$this->set_error_code(2001);
									return false;
								} else {
									$in_article['sefriendly'] = '';
								}
							}
						}
						unset($arrData);
					}

					if (!$sm->user->can_change_status($in_article['status'])){
						$in_article['status'] = 0;
					}

					if ($sm->user->is('EDITOR', 'Categories', $in_article['category_id'])) {
						if (!isset($in_article['user_id']) or $in_article['user_id'] == '') {
							$in_article['user_id'] = $sm->user->get_id();
						}
					} else {
						$in_article['user_id'] = $sm->user->get_id();
						$in_article['author'] = $sm->user->get_name();
					}

					$in_article['user_domain'] = VIVVO_USER_SOURCE;

					if (!$sm->user->is_admin()) {
						if (!in_array('0', $writer_restriction) && !in_array($in_article['category_id'], $writer_restriction)) {
							$this->set_error_code(2036);
							return false;
						}
					}

					$body = $in_article['body'];
					array_walk ($in_article, 'array_htmlspecialchars');
					$in_article['body'] = $body;

					$in_article['created'] = date('Y-m-d H:i:00', $in_article['created']);

					$article = new Articles($sm);

					if ($article->populate($in_article, true) === false) {
						$this->set_error_info($article->get_error_info());
						return false;
					}
					if ($article->author == '') {
						$article->author = $article->get_author_name();
					}

					$this->_post_master->set_data_object($article);

					if ($this->_post_master->sql_insert()) {
						$id = $this->_post_master->get_work_id();
						$article->set_id($id);

						if (!vivvo_hooks_manager::call('article_add_postinsert', array(&$article))) {
							return vivvo_hooks_manager::get_status();
						}

						$um = $sm->get_url_manager();
						$um->set_param('ARTICLE_id', $id);
						$fm = $sm->get_file_manager();

						if ($fm->is_uploaded('ARTICLE_abstact_image')) {
							$abstract_image = $fm->upload('ARTICLE_abstact_image', '', 'jpg,jpeg,gif,png');
							if ($abstract_image != false) {
								$article->set_image($abstract_image);
								$this->_post_master->set_data_object($article);
								$this->_post_master->sql_update();
							}
						}
						if ($fm->is_uploaded('ARTICLE_attachment')) {
							$attachment = $fm->upload('ARTICLE_attachment');
							if ($attachment != false) {
								$article->set_document($attachment);
								$this->_post_master->set_data_object($article);
								$this->_post_master->sql_update();
							}
						}
						if (defined('VIVVO_CUSTOM_FIELD_UPLOAD') && VIVVO_CUSTOM_FIELD_UPLOAD == 1) {
							$files = $fm->get_upload_keys();
							if ($files) {
								$dbm = $sm->get_db_manager();
								$fields = $dbm->get_table_fields(VIVVO_DB_PREFIX . 'articles');
								if (!empty($fields)) {
									foreach ($files as $file) {
										$file_field = substr($file, 8);
										if (preg_match('/^ARTICLE_file_/', $file) && in_array($file_field, $fields)) {
											$uploaded = $fm->upload($file);
											if ($uploaded != false) {
												$article->__set($file_field, $uploaded);
												$this->_post_master->set_data_object($article);
												$this->_post_master->sql_update();
											}
										}
									}
								}
							}
						}

						isset($in_article['tags']) or $in_article['tags'] = '';
						$this->manageTags(explode(',', $in_article['tags']), $id);

						admin_log($sm->user->get_username(), 'Created article #' . $this->_post_master->get_work_id());
						return true;
					} else {
						$this->set_error_code(2002);
						return false;
					}
				} else {
					$this->set_error_code(2003);
					return false;
				}
			} else {
				$this->set_error_code(2004);
				return false;
			}
		}

		/**
		 * Manages (add/remove) article tags
		 *
		 * @param	array	$tags
		 */
		public function manageTags($tags, $article_id) {

			$sm = vivvo_lite_site::get_instance();
			$db = $sm->get_db();

			$res = $db->query('SELECT CONCAT(tags_group_id, ":", tag_id) FROM '.VIVVO_DB_PREFIX."articles_tags WHERE article_id = $article_id");

			if (!PEAR::isError($res)) {

				$old = array_map(create_function('$x', 'return $x[0];'), $res->fetchAll());
				$new = array();

				class_exists('tag_service') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/service/Tags.service.php';

				$tag_service = new tag_service();

				foreach ($tags as $pair) {
					if (!preg_match('/^\d+:\d+$/', $pair)) {

						$name = trim(preg_replace('/(^\[|\]$)/', '', $pair));

						if (!strlen($name) or ($tag_id = $tag_service->add_tag($name, make_sefriendly($name))) === false) {
							continue;
						}

						$pair = '0:' . $tag_id;

						$tag_service->add_tag_to_group($tag_id, 0);
					}
					$new[] = $pair;
				}

				$new = array_unique($new);

				$del = empty($new) ? $old : array_diff($old, $new);
				$add = empty($old) ? $new : array_diff($new, $old);

				foreach ($del as $item) {
					list($topic_id, $tag_id) = explode(':', $item);
					$db->query('DELETE FROM '.VIVVO_DB_PREFIX."articles_tags WHERE tag_id = $tag_id AND tags_group_id = $topic_id AND article_id = $article_id");
				}

				if (!empty($add)) {

					$user_id = $sm->user->get_id();
					$values = array();

					foreach ($add as $item) {
						list($topic_id, $tag_id) = explode(':', $item);
						$values[] = "($tag_id, $topic_id, $article_id, $user_id)";
					}

					$db->query('INSERT INTO '.VIVVO_DB_PREFIX.'articles_tags (tag_id, tags_group_id, article_id, user_id) VALUES ' . implode(', ', $values));
				}
			}
		}

		/**
		 * Delete article
		 *
		 * @param	array	$articles_ids
		 * @param	integer	$all_matching
		 * @return	boolean	true on success or false on fail
		 */
		function delete_article($articles_ids, $all_matching = 0){
			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('article_delete', array(&$articles_ids, &$all_matching))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user) {

				$editor_restriction = $sm->user->get_privilege_object_ids('EDITOR', 'Categories');

				if (!empty($editor_restriction)) {
					$article_list = new Articles_list();
					if ($all_matching == 1) {
						$article_params = Articles_list::get_search_params_from_url($sm);
						$article_list->search($article_params['search_options'], '', 'ascending', 0, 0);
						if ($article_list->sql_delete_list($this->_post_master, NULL, true)) {
							admin_log($sm->user->get_username(), 'Deleted all selected articles');
							return true;
						} else {
							$this->set_error_code(2006);
							return false;
						}
					} else {
						$article_list->get_articles_by_ids($articles_ids, $editor_restriction);
						if ($article_list->sql_delete_list($this->_post_master)) {
							admin_log($sm->user->get_username(), 'Deleted articles #' . trim(implode(',', $articles_ids)));
							return true;
						} else {
							$this->set_error_code(2007);
							return false;
						}
					}
				} else {
					$this->set_error_code(2008);
					return false;
				}
			} else {
				$this->set_error_code(2009);
				return false;
			}
		}

		/**
		 * Set fields in article
		 *
		 * @param	array	$articles_ids
		 * @param	string	$field_name
		 * @param	string	$value
		 * @param	integer	$all_matching
		 * @return	boolean	true on success or false on fail
		 */
		function set_field($article_ids, $field_name, $value, $all_matching = 0) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('article_setField', array(&$article_ids, &$field_name, &$value, &$all_matching))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				$editor_restriction = $sm->user->get_privilege_object_ids('EDITOR', 'Categories');

				if(!empty($editor_restriction)){
					$article_list = new Articles_list();

					if (!in_array('0', $editor_restriction)){
						if (($field_name == 'category_id') && !in_array($value, $editor_restriction)) {
							$this->set_error_code(2012);
							return false;
						}
					}

					if (($field_name == 'status') && !$sm->user->can_change_status($value, '*')) {
						$this->set_error_code(2012);
						return false;
					}

					if ($all_matching == 1){
						$article_params = Articles_list::get_search_params_from_url($sm);
							if (!is_array($article_params['search_options']['search_cid'])) {
								$article_params['search_options']['search_cid'] = explode(',', $article_params['search_options']['search_cid']);
							}
						if (!in_array('0', $editor_restriction) && isset ($article_params['search_options']['search_cid'])){
							$article_params['search_options']['search_cid'] = array_intersect($editor_restriction, $article_params['search_options']['search_cid']);
						}
						$article_list->search($article_params['search_options'], '', 'ascending', 0, 0, false);

						if (!in_array($field_name, array('applyTags', 'editTags'))) {
							if ($article_list->sql_update_list($this->_post_master, array($field_name => $value), NULL, true)){
								admin_log($sm->user->get_username(), 'Edited articles #' . trim(implode(',', $article_ids)));
								return true;
							} else {
								$this->set_error_code(2010);
								return false;
							}
						} else {
							$res = $sm->get_db()->query(
								'SELECT a.id ' .
								$article_list->_query->get_from() . ' ' .
								$article_list->_query->get_join() . ' ' .
								$article_list->_query->get_where() . ' ' .
								$article_list->_query->get_group_by() . ' ' .
								$article_list->_query->get_having()
							);

							if (PEAR::isError($res)) {
								$this->set_error_code(2010);
								return false;
							}

							$article_ids = $res->fetchCol();
							$res->free();

						}
					} elseif (!in_array($field_name, array('applyTags', 'editTags'))) {
						$article_list->get_articles_by_ids($article_ids, $editor_restriction);

						if ($article_list->sql_update_list($this->_post_master, array($field_name => $value))){
							admin_log($sm->user->get_username(), 'Edited articles #' . trim(implode(',', $article_ids)));
							return true;
						}else{
							$this->set_error_code(2011);
							return false;
						}
					}

					if ($field_name == 'applyTags') {

						require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/service/Tags.service.php';

						$this->tag_service = new tag_service($sm);

						$tags = array_map(array($this, 'parse_tags'), explode(',', $value));

						foreach ($article_ids as $article_id) {
							foreach ($tags as $id) {
								$this->tag_service->add_tag_to_group($id[1], $id[0], $article_id);
							}
						}

						return true;

					} elseif ($field_name == 'editTags') {

						$tags = explode(',', $value);

						foreach ($article_ids as $article_id) {
							$this->manageTags($tags, $article_id);
						}

						return true;
					}

					$this->set_error_code(2011);
					return false;
				}else{
					$this->set_error_code(2012);
					return false;
				}
			}else{
				$this->set_error_code(2013);
				return false;
			}
		}

		/**
		 * Array-map method used by applyTags action
		 *
		 * @param	string	$tag
		 * @return	array
		 */
		private function parse_tags($tag) {
			if (preg_match('/^\[.+?\]$/', $tag)) {
				$tag = trim($tag, '[]');
				return array(0, $this->tag_service->add_tag($tag, make_sefriendly($tag)));
			}
			return explode(':', $tag);
		}

		/**
		 * Edit article
		 *
		 * @param	integer	$article_id
		 * @param	array	$data
		 * @return	boolean	true on success or false on fail
		 */
		function edit_article($article_id, $in_article) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('article_edit', array(&$article_id, &$in_article))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user){

				$editor_restriction = $sm->user->get_privilege_object_ids('EDITOR', 'Categories');
				$writer_restriction = $sm->user->get_privilege_object_ids('WRITE', 'Categories');

				if(!empty($writer_restriction)){
					$article_list = new Articles_list();

					//Try to load article with EDITOR privileges
					if(!empty($editor_restriction)){
						$article = $article_list->get_article_by_id($article_id, $editor_restriction);
					}

					//If no article try to load article with WRITE privileges
					if (empty($article)) {
						if(!empty($editor_restriction)) {
							$article_list->reset_list_query();
						}
						if ($article_list->get_user_articles_by_ids($article_id, $sm->user->get_id(), $writer_restriction)) {
							$article = current($article_list->list);
						} else {
							$article = false;
						}
					}

					if ($article !== false) {

						if (isset($in_article['title'])) {
							$in_article['title'] = trim($in_article['title']);
						}

						if (!strlen(trim($in_article['sefriendly']))) {
							$in_article['sefriendly'] = make_sefriendly($in_article['title']);
							$source = 'title';
						} else {
							$in_article['sefriendly'] = make_sefriendly($in_article['sefriendly']);
							$source = 'sefriendly';
						}

						if (!empty($in_article['sefriendly'])) {
							$sql = 'SELECT id FROM ' . VIVVO_DB_PREFIX . 'articles WHERE sefriendly = \'' . secure_sql($in_article['sefriendly']) . '\' AND id != ' . $article_id;
							$res = $sm->get_db()->query($sql);
							if ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
								if ($source == 'sefriendly') {
									$this->set_error_code(2014);
									return false;
								} else {
									$in_article['sefriendly'] = '';
								}
							}
						}

						if (!$sm->user->is_admin()) {
							if (!in_array('0', $writer_restriction) && !in_array($in_article['category_id'], $writer_restriction)){
								unset($in_article['category_id']);
							}
						}

						if (!$sm->user->can_change_status($in_article['status'], $article->status)) {
							unset($in_article['status']);
						}

						if ($sm->user->is('EDITOR', 'Categories', $in_article['category_id'])){
							if (!isset($in_article['user_id']) or $in_article['user_id'] == ''){
								$in_article['user_id'] = $sm->user->get_id();
							}
						}else{
							unset($in_article['user_id']);
							unset($in_article['author']);
						}

						$body = $in_article['body'];
						array_walk ($in_article, 'array_htmlspecialchars');
						$in_article['body'] = $body;
						$in_article['last_edited'] = date('Y-m-d H:i:s');

						if (isset($in_article['created'])) {
							$in_article['created'] = date('Y-m-d H:i:00', $in_article['created']);
						}

						if ($keep_revisions = (defined('VIVVO_REVISIONS_KEEP_COPIES') and VIVVO_REVISIONS_KEEP_COPIES)) {
							$old_content = array(
								'title' => $article->get_title(),
								'abstract' => $article->get_abstract(),
								'body' => $article->get_body()
							);
						}

						if ($article->populate($in_article, true) === false) {
							$this->set_error_info($article->get_error_info());
							return false;
						}
						$this->_post_master->set_data_object($article);

						if ($this->_post_master->sql_update()) {
							$fm = $sm->get_file_manager();
							if ($fm->is_uploaded('ARTICLE_abstact_image')) {
								$abstract_image = $fm->upload('ARTICLE_abstact_image', '', 'jpg,jpeg,gif,png');
								if ($abstract_image != false) {
									$article->set_id($article_id);
									$article->set_image($abstract_image);
									$this->_post_master->set_data_object($article);
									$this->_post_master->sql_update();
								} else {
									$this->set_error_code($fm->get_error_code());
									return false;
								}
							}
							if (defined('VIVVO_CUSTOM_FIELD_UPLOAD') && VIVVO_CUSTOM_FIELD_UPLOAD == 1){
								$files = $fm->get_upload_keys();
								if ($files) {
									$dbm = $sm->get_db_manager();
									$fields = $dbm->get_table_fields(VIVVO_DB_PREFIX . 'articles');
									if (!empty($fields)) {
										foreach ($files as $file){
											$file_field = substr($file, 8);
											if (preg_match('/^ARTICLE_file_/', $file) && in_array($file_field, $fields)) {
												$uploaded = $fm->upload($file);
												if ($uploaded != false) {
													$id = $this->_post_master->get_work_id();
													$article->set_id($id);
													$article->__set($file_field, $uploaded);
													$this->_post_master->set_data_object($article);
													$this->_post_master->sql_update();
												}
											}
										}
									}
								}
							}

							isset($in_article['tags']) or $in_article['tags'] = '';
							$this->manageTags(explode(',', $in_article['tags']), $article->get_id());

							// save revision
							if ($keep_revisions) {

								class_exists('ArticlesRevisions') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesRevisions.class.php';

								ArticlesRevisions::save_revision(
									$article->get_id(),
									$old_content['title'],
									$old_content['abstract'],
									$old_content['body'],
									$in_article['last_edited'],
									$sm->user->get_id()
								);
							}

							admin_log($sm->user->get_username(), 'Edited article #' . $article_id);
							return true;
						} else {
							$this->set_error_code(2015);
							return false;
						}
					} else {
						$this->set_error_code(2016);
						return false;
					}
				} else {
					$this->set_error_code(2017);
					return false;
				}
			} else {
				$this->set_error_code(2018);
				return false;
			}
		}

		function normalize_order() {

			if (!vivvo_hooks_manager::call('article_normalizeOrder')) {
				return vivvo_hooks_manager::get_status();
			}

			$article_list = new Articles_list();
			$article_list->search(array(),'order_num','descending');
			$count = $article_list->get_total_count();

			foreach($article_list->list as $k => $v) {
				$article_update = new Articles();
				$article_update->set_id($v->id);
				$article_update->set_order_num($count);

				$this->_post_master->set_data_object($article_update);
				$this->_post_master->sql_update();
				$count--;
			}
			return true;
		}

		/**
		 * Reorder article
		 *
		 * @param	array	$article_ids
		 * @return	boolean	true on success or false on fail
		 */
		function reorder_article($article_ids) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('article_reorderArticle', array(&$article_ids))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				$editor_restriction = $sm->user->get_privilege_object_ids('EDITOR', 'Categories');

				if (!empty($editor_restriction)) {

					$article_list_ids = $article_ids;
					$article_list = new Articles_list();
					$article_list->search(array('search_id'=>$article_ids),'order_num','descending');
					$order_nums = $article_list->get_property_list('order_num');

					foreach($article_list_ids as $k) {
						$new_order = array_shift($order_nums);
						$article_list->list[$k]->set_order_num($new_order);
						$this->_post_master->set_data_object($article_list->list[$k]);
						if (!$this->_post_master->sql_update()) {
							$this->set_error_code(2019);
							return false;
						}
					}
					admin_log($sm->user->get_username(), 'Reordered articles #' . trim(implode(',', $article_list_ids)));
					return true;
				} else {
					$this->set_error_code(2020);
					return false;
				}
			} else {
				$this->set_error_code(2021);
				return false;
			}
		}

		/**
		 * Delete image
		 *
		 * @param	integer	$article_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_image($article_id) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('article_deleteImage', array(&$article_id))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				$editor_restriction = $sm->user->get_privilege_object_ids('EDITOR', 'Categories');
				$writer_restriction = $sm->user->get_privilege_object_ids('WRITE', 'Categories');

				if (!empty($writer_restriction)) {

					$article_list = new Articles_list();
					$article = $article_list->get_article_by_id($article_id);

					if ($article !== false) {
						$fm = $sm->get_file_manager();
						if ($article->image != '') {
							if ($fm->delete_fs(VIVVO_FS_ROOT . VIVVO_FS_FILES_DIR . $article->image)) {
								$article->set_image('');
								$this->_post_master->set_data_object($article);
								if ($this->_post_master->sql_update()) {
									admin_log($sm->user->get_username(), 'Deleted article image #' . $article->get_id());
									return true;
								} else {
									$this->set_error_code(2022);
									return false;
								}
							}
						}
					} else {
						$this->set_error_code(2023);
						return false;
					}
				} else {
					$this->set_error_code(2024);
					return false;
				}
			} else {
				$this->set_error_code(2025);
				return false;
			}
		}

		/**
		 * Delete document
		 *
		 * @deprecated
		 * @param	integer	$article_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_document($article_id) {
			return false;
		}

		function vote($article_id, $vote) {

			if (!vivvo_hooks_manager::call('article_vote', array(&$article_id, &$vote))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			if ($sm->user && !$sm->user->can('ARTICLE_VOTE')) {
				return false;
			} elseif (!$sm->user && $sm->guest_group && !$sm->guest_group->can('ARTICLE_VOTE')) {
				return false;
			}

			$article_id = (int)$article_id;
			$vote = (int)$vote;
			if (isset($_SESSION['vivvo']) && isset($_SESSION['vivvo']['article_poll']) && $_SESSION['vivvo']['article_poll'][$article_id]) {
				return false;
			} else {
				if (!isset($_SESSION['vivvo'])) {
					$_SESSION['vivvo'] = array();
				}
				if (!isset($_SESSION['vivvo']['article_poll'])) {
					$_SESSION['vivvo']['article_poll'] = array();
				}
			}

			$article_list = new Articles_list();
			$article = $article_list->get_article_by_id($article_id);

			if ($article !== false) {

				if ($vote < 1) {
					$vote = 1;
				} elseif($vote > 5) {
					$vote = 5;
				} else {
					$vote = (int) $vote;
				}

				$article->set_vote_sum($article->vote_sum + $vote);
				$article->set_vote_num($article->vote_num + 1);

				$this->_post_master->set_data_object($article);

				if ($this->_post_master->sql_update()) {
					@$_SESSION['vivvo']['article_poll'][$article_id] = $vote;
					return true;
				} else {
					$this->set_error_code(2030);
					return false;
				}
			} else {
				$this->set_error_code(2031);
				return false;
			}
		}

		function email_to_a_friend($article_id, $to, $bcc, $your_email, $message) {

			require_once(VIVVO_FS_FRAMEWORK . 'PEAR/Mail.php');

			if (!vivvo_hooks_manager::call('article_mail', array(&$article_id, &$to, &$bcc, &$your_email, &$message))) {
				return vivvo_hooks_manager::get_status();
			}

			if (VIVVO_EMAIL_ENABLE == 1) {
				
				if (isset($_SESSION['vivvo']['email_to_friend_time']) && (($_SESSION['vivvo']['email_to_friend_time'] + VIVVO_EMAIL_FLOOD_CHECK) > VIVVO_START_TIME)) {
							$this->set_error_code(2202);
							return false;
			    }

				$article_list = new Articles_list();
				$article = $article_list->get_article_by_id($article_id);

				$lang = vivvo_lang::get_instance();

				if ($article !== false) {

					$to = strip_tags($to);
					$bcc = strip_tags($bcc);

					if (!preg_match('/^[a-zA-Z0-9_\-\/\.]+@[a-zA-Z0-9_\-\/]{2,}([\.][a-zA-Z0-9_\-\/]{2,}){1,}$/', $your_email)) {
						$this->set_error_code(2032);
						return false;
					}

					if (!preg_match('/^[a-zA-Z0-9_\-\/\.]+@[a-zA-Z0-9_\-\/]{2,}([\.][a-zA-Z0-9_\-\/]{2,}){1,}$/', $bcc)) {
						$bcc = '';
					}

					if (VIVVO_EMAIL_SEND_BCC) {
						if ($bcc) {
							$bcc .= ', ' . VIVVO_EMAIL_SEND_BCC;
						} else {
							$bcc = VIVVO_EMAIL_SEND_BCC;
						}
					}

					if (VIVVO_EMAIL_SEND_CC) {
						$headers['Cc'] = VIVVO_EMAIL_SEND_CC;
					}

					if (preg_match('/^[a-zA-Z0-9_\-\/\.]+@[a-zA-Z0-9_\-\/]{2,}([\.][a-zA-Z0-9_\-\/]{2,}){1,}$/', $to)) {

						$body_template = new template();
						$template_sting = xml_template_node::xmlentities_decode(VIVVO_EMAIL_TO_A_FRIEND_BODY);

						$body_template->set_string_template($template_sting);

						$body_template->assign('article', $article);
						$body_template->assign('user_email_address', $your_email);
						$body_template->assign('message', $message);

						$body = $body_template->get_output() . "\n\n";


						$headers['From'] = $your_email;
						$recipients = array();
						$recipients[] = $to;

						if ($bcc != '') {
							$headers['Bcc'] = $bcc;
						}

						$headers['Subject'] = "=?UTF-8?B?".base64_encode(VIVVO_EMAIL_TO_A_FRIEND_SUBJECT)."?=";
						$headers['Content-Type'] = "text/plain; charset=UTF-8;";

						if (VIVVO_EMAIL_SMTP_PHP == 1) {
							$mail_object = new Mail();
							$mail_object->send($to, $headers, $body);
						} else {
							$mail_options['driver']    = 'smtp';
							$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
							$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
							$mail_options['localhost'] = 'localhost';

							if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != '') {
								$mail_options['auth'] = true;
								$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
								$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
							} else {
								$mail_options['auth'] = false;
								$mail_options['username']  = '';
								$mail_options['password']  = '';
							}

							$mail_object = Mail::factory('smtp', $mail_options);
							$mail_object->send($to, $headers, $body);
						}

						$article->set_emailed($article->emailed + 1);

						$this->_post_master->set_data_object($article);

						if ($this->_post_master->sql_update()) {
							$_SESSION['vivvo']['email_to_friend_time'] = time();
							return true;
						} else {
							$this->set_error_code(2033);
							return false;
						}
					} else {
						$this->set_error_code(2034);
						return false;
					}
				} else {
					$this->set_error_code(2035);
					return false;
				}
			}
		}

		function purge_trashbin() {
			if (!$this->check_token()) return false;
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user) {
				$editor_restriction = $sm->user->get_privilege_object_ids('EDITOR', 'Categories');
				if (!empty($editor_restriction)) {

					if ($this->_post_master->sql_delete_list(null, "status='-2'", VIVVO_DB_PREFIX . 'articles')) {
						admin_log($sm->user->get_username(), 'Deleted all articles in trashbin');
						return true;
					} else {
						$this->set_error_code(2006);
						return false;
					}
				} else {
					$this->set_error_code(2008);
					return false;
				}
			} else {
				$this->set_error_code(2009);
				return false;
			}
		}
	}
?>