<?php
/* =============================================================================
 * $Revision: 6070 $
 * $Date: 2010-12-09 15:33:24 +0100 (Thu, 09 Dec 2010) $
 *
 * Vivvo CMS v4.5.2r (build 6082)
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
	 * @see  'lib/vivvo/core/ArticlesTags.class.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesTags.class.php');
	/**
	 * @see  'lib/vivvo/core/Tags.class.php'
 	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');
	/**
	 * @see  'lib/vivvo/core/TagsGroups.class.php'
	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php');
	/**
	 * @see  'lib/vivvo/core/TagsToTagsGroups.class.php'
	 */
	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsToTagsGroups.class.php');
	/**
	 * @see  'lib/vivvo/service/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . "vivvo_service.class.php");


	/**
	 * Tag service object
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
	class tag_service extends vivvo_service {

		/**
		 * Add new tag
		 *
		 * @param	string	$name
		 * @param	string	$sefriendly
		 */
		public function add_tag($name, $sefriendly) {
            
            
			if( !vivvo_hooks_manager::call('tag_add', array(&$name, &$sefriendly) ) )
				return vivvo_hooks_manager::get_status();

			$user = vivvo_lite_site::get_instance()->user;

			if ($user && $user->can('MANAGE_TAGS')) {

				$tag_list = new Tags_list();
                
                if ($tag = $tag_list->get_tag_by_sefriendly($sefriendly)) {

                    if ($tag->get_id() < 100){
                        $this->set_error_code(2423);
                        return false;
                    }
                    return $tag->get_id();
                }    
//				if ($tag = $tag_list->get_tag_by_name(addslashes($name))) {

//					if ($tag->get_id() < 100){
//						$this->set_error_code(2423);
//						return false;
//					}
//					return $tag->get_id();
//				}

				if (empty($sefriendly)) {
					$sefriendly = make_sefriendly($name);
				} else {
				    $sefriendly = make_sefriendly($sefriendly);
				}

				$tag = new Tags();

               // $tag->set_name(htmlspecialchars($name, ENT_QUOTES, 'UTF-8'));
				$tag->set_name($name);
				$tag->set_sefriendly($sefriendly);

				$this->_post_master->set_data_object($tag);

				if (!$this->_post_master->sql_insert()) {
					$this->set_error_code(2401);
					return false;
				}

                $work_id = $this->_post_master->get_work_id();
				admin_log($user->get_username(), 'Created tag #' . $work_id);
				return $work_id;
			}

			$this->set_error_code(2410);
			return false;
		}

		/**
		 * Add tag group
		 *
		 * @param	string	$name
		 * @param	string	$url
		 * @param	string	$template
		 * @param	array	$metadata
		 */
		public function add_tag_group($name, $url, $template, $tag_template, $metadata, $return_id_if_exists = false, $new_tags = '') {

			if (!vivvo_hooks_manager::call('tag_addGroup', array(&$name, &$url, &$template, &$tag_template, &$metadata, &$return_id_if_exists, &$new_tags))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			$user = $sm->user;

			if ($user && $user->can('MANAGE_TAGS')) {

				$tag_group_list = new TagsGroups_list();
				$tag_group = new TagsGroups();

				if (!$tag_group->set_name($name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8'))) {
					$this->set_error_code(12, vivvo_lang::get_instance()->get_value('LNG_DB_tags_groups_name'));
					return false;
				}

				if ($existing_group = $tag_group_list->get_group_by_name($name)) {
					if (!$return_id_if_exists) {
						$this->set_error_code(2417);
						return false;
					}
					return $existing_group->get_id();
				}

				$um = $sm->get_url_manager();

				if (empty($url)) {
					$url = make_sefriendly($name);
				} else {
				    $url = make_sefriendly($url);
				}

				$um->set_param('TAG_GROUP_url', $url);

				$sefriendly = secure_sql($url);

				$sql = 'SELECT id FROM '.VIVVO_DB_PREFIX."categories WHERE sefriendly = '$sefriendly' LIMIT 1 UNION
						SELECT id FROM ".VIVVO_DB_PREFIX."tags_groups WHERE url = '$sefriendly' LIMIT 1";
				if (($res = $sm->get_db()->query($sql)) && ($res->numRows() > 0)) {
					$this->set_error_code(2418);
					return false;
				}

				$tag_group->set_url($url);
				$tag_group->set_template($template);
                $tag_group->set_tag_template($tag_template);
				$tag_group->set_metadata($metadata);

				$this->_post_master->set_data_object($tag_group);

				if (!$this->_post_master->sql_insert()) {
					$this->set_error_code(2412);
					return false;
				}

				$work_id = $this->_post_master->get_work_id();

				$um->register_url(urlencode($url), 'lib/vivvo/url_handlers/topic.php', 'topic_url_handler', 'topic_content_handler');

				$um->set_param('search_id', $work_id);

				if ($new_tags) {
					$this->add_tag_names_to_topic($new_tags, $work_id);
				}

				admin_log($user->get_username(), 'Added topic #' .$work_id);
				return $work_id;
			}

			$this->set_error_code(2410);
			return false;
		}

		/**
		 * Edit tag
		 *
		 * @param	int		$id
		 * @param	string	$name
		 * @param	string	$sefriendly
		 */
		public function edit_tag($id, $name, $sefriendly) {

			if (!vivvo_hooks_manager::call('tag_edit', array(&$id, &$name, &$sefriendly))) {
				return vivvo_hooks_manager::get_status();
			}

			$user = vivvo_lite_site::get_instance()->user;

			if ($user && $user->can('MANAGE_TAGS')) {

				if (empty($sefriendly)) {
					$sefriendly = make_sefriendly($name);
				} else {
				    $sefriendly = make_sefriendly($sefriendly);
				}

				$tag_list = new Tags_list();
				$tag = $tag_list->get_tag_by_id($id);

				if ($tag) {

					$tag->set_name(htmlspecialchars($name, ENT_QUOTES, 'UTF-8'));
					$tag->set_sefriendly($sefriendly);

					$this->_post_master->set_data_object($tag);
					if (!$this->_post_master->sql_update()) {
						$this->set_error_code(2411);
						return false;
					}

					admin_log($user->get_username(), 'Edited tag #' . $id);
					return true;
				}
			}

			$this->set_error_code(2410);
			return false;
		}


		/**
		 * Edit tag group
		 *
		 * @param	int		$id
		 * @param	string	$name
		 * @param	string	$url
		 * @param	string	$template
		 * @param	array	$metadata
		 */
		public function edit_tag_group($id, $name, $url, $template, $tag_template, $metadata, $new_tags = '') {

			if (!vivvo_hooks_manager::call('tag_editGroup', array(&$id, &$name, &$url, &$template, &$tag_template, &$metadata))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();
			$user = $sm->user;

			if ($user && $user->can('MANAGE_TAGS')) {

				if (empty($url)) {
					$url = make_sefriendly($name);
				} else {
				    $url = make_sefriendly($url);
				}

				$tag_group_list = new TagsGroups_list();

				$existing_group = $tag_group_list->get_group_by_name($name);
				$tag_group = $tag_group_list->get_group_by_id($id = (int)$id);

				if (is_object($existing_group) && $tag_group->id != $existing_group->id) {
					$this->set_error_code(2422);
					return false;
				}

				if ($tag_group) {

					$sefriendly = secure_sql($url);
					$sql = 'SELECT id FROM '.VIVVO_DB_PREFIX."categories WHERE sefriendly = '$sefriendly' LIMIT 1 UNION
							SELECT id FROM ".VIVVO_DB_PREFIX."tags_groups WHERE url = '$sefriendly' AND id <> $id LIMIT 1";
					if (($res = $sm->get_db()->query($sql) ) && ($res->numRows() > 0)) {
						$this->set_error_code(2418);
						return false;
					}

					$old_url = $tag_group->get_url();

					$tag_group->set_name(htmlspecialchars($name, ENT_QUOTES, 'UTF-8'));
					$tag_group->set_url($url);
					$tag_group->set_template($template);
                    $tag_group->set_tag_template($tag_template);
					$tag_group->set_metadata(array_merge($tag_group->get_meta(), $metadata));

					$this->_post_master->set_data_object($tag_group);
					if (!$this->_post_master->sql_update()) {
						$this->set_error_code(2413);
						return false;
					}

					if ($old_url != $url) {

						$um = $sm->get_url_manager();
						$um->unregister_url( urlencode($old_url) );
						$um->register_url(urlencode($url), 'lib/vivvo/url_handlers/topic.php', 'topic_url_handler', 'topic_content_handler');
					}

					if ($new_tags) {
						$this->add_tag_names_to_topic($new_tags, $id);
					}

					admin_log($user->get_username(), 'Edited topic #' . $tag_group->id);
					return true;
				}
			}

			$this->set_error_code(2410);
			return false;
		}


		/**
		 * Delete tag group
		 *
		 * @param	integer	$group_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_tag_group($group_id) {

			if (!vivvo_hooks_manager::call('tag_deleteGroup', array(&$group_id))) {
				return vivvo_hooks_manager::get_status();
			}

			if ($group_id <= 100) {
				$this->set_error_code(2420); // system topic can't be deleted.
				return false;
			}

			$user = vivvo_lite_site::get_instance()->user;

			if ( $user && $user->can('MANAGE_TAGS') ) {

				$tag_group_list = new TagsGroups_list();
				$tag_group_list->get_group_by_id($group_id);

				if ($tag_group_list->sql_delete_list($this->_post_master)) {
					admin_log($user->get_username(), 'Deleted topic #' . $group_id);
					return true;
				}

				$this->set_error_code(2414);
				return false;
			}

			$this->set_error_code(2410);
			return false;
		}


		/**
		 * Add new tag and tag link
		 * @param	integer	$article_id
		 * @param	array	$in_tag
		 * @return	boolean	true on success or false on fail
		 */
		function add_tag_link($article_id, $in_tag) {

			if (!vivvo_hooks_manager::call('tag_addLink', array(&$article_id, &$in_tag))) {
				return vivvo_hooks_manager::get_status();
			}

			$sm = vivvo_lite_site::get_instance();

			$in_tag = htmlspecialchars($in_tag, ENT_QUOTES, 'UTF-8');

			if ($sm->user && $sm->user->can('ARTICLE_TAG')) {
				$tag_array_finish = array();
				$tag_array = array_map('trim', explode(',', $in_tag) );
				foreach ($tag_array as $k => $v) {
					if (strlen($v) > 2 && !is_numeric($v) && !preg_match('/[\?\/\\&\.]/', $v)) {
						$tag_array_finish[] = strtolower($v);
					}
				}

				$tag_list = new Tags_list();
				$tag_list->get_tag_by_name($tag_array_finish);

				$new_tag_index = 0;
				$new_tag = array();

				//add missing tags
				foreach ($tag_list->list as $tag) {
					if (in_array($tag_name = strtolower($tag->get_name()), $tag_array_finish)) {
						unset($tag_array_finish[array_search($tag_name, $tag_array_finish)]);
					}
				}

				$tag_array_finish = array_filter($tag_array_finish);

				foreach ($tag_array_finish as $tag_name){
					$new_tag[$new_tag_index] = new Tags();
					$new_tag[$new_tag_index]->set_name($tag_name);
					$new_tag[$new_tag_index]->set_sefriendly(make_sefriendly($tag_name));
					$this->_post_master->set_data_object($new_tag[$new_tag_index]);
					if ($this->_post_master->sql_insert()){
						$tag_id = $this->_post_master->get_work_id();
						$tag_list->list[$tag_id] =& $new_tag[$new_tag_index];
					}else {
						$this->set_error_code(2401);
						return false;
					}
					$new_tag_index++;
				}

				$new_tag_ids = $tag_list->get_list_ids();
				$atl = new ArticlesTags_list();
				$atl->get_by_article_user($article_id, $sm->user->get_id());
				$article_tag_ids = $atl->get_property_list('tag_id');

				//add new tag links
				if (!empty($new_tag_ids)){
					$add_article_tags = $new_tag_ids;
					if (!empty($article_tag_ids)){
						$add_article_tags = array_diff($new_tag_ids, $article_tag_ids);
					}

					if (!empty($add_article_tags)){
						foreach ($add_article_tags as $tag_id){

							$at = new ArticlesTags();
							$at->set_tag_id($tag_id);
							$at->set_article_id($article_id);
							$at->set_user_id($sm->user->get_id());
							$this->_post_master->set_data_object($at);
							if (!$this->_post_master->sql_insert()) {
								$this->set_error_code(2402);
								return false;
							}
						}
					}
				}
				return true;
			}else{
				$this->set_error_code(2403);
				return false;
			}
		}

		/**
		 * Delete tag
		 *
		 * @param	integer	$tag_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_tag($tag_id, $all_matching = 0) {

			if (!$this->check_token()) {
				return false;
			}

			if (!vivvo_hooks_manager::call('tag_delete', array(&$tag_id))) {
				return vivvo_hooks_manager::get_status();
			}

			if ($tag_id <= 100) {
				$this->set_error_code(2421); // system tag can't be deleted.
				return false;
			}

			$sm = vivvo_lite_site::get_instance();
			$user = $sm->user;

			if ($sm->user && $sm->user->can('ARTICLE_TAG')){
				if($sm->user->is_admin() || $sm->user->is('EDITOR')){

					$tag_list = new Tags_list();

					if ($all_matching) {
						$params = Tags_list::get_search_params_from_url($sm);
						$tag_list->search($params['search_options'], '', 'ascending', 0, 0);
						if ($tag_list->sql_delete_list($this->_post_master, null, true)) {
							admin_log($user->get_username(), 'Deleted tags like ' . $params['search_options']);
							return true;
						}
					} else {
						$tag_list->get_tags_by_ids($tag_id, false);
						if ($tag_list->sql_delete_list($this->_post_master)) {
							admin_log($user->get_username(), 'Deleted tag #' . $tag_id);
							return true;
						}
					}
					$this->set_error_code(2404);
					return false;
				}else{
					$this->set_error_code(2405);
					return false;
				}
			}else{
				$this->set_error_code(2406);
				return false;
			}
		}

		/**
		 * Delete tag link
		 *
		 * @param	integer	$tag_id
		 * @return	boolean	true on success or false on fail
		 */
		function delete_tag_link($tag_id){

			if( !vivvo_hooks_manager::call('tag_deleteLink', array(&$tag_id) ) )
				return vivvo_hooks_manager::get_status();

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user && $sm->user->can('ARTICLE_TAG')){
				$articles_tag_list = new ArticlesTags_list();
				$article_tag = $articles_tag_list->get_articles_tags_by_id($tag_id);

				$system_tag = $article_tag->get_tags_group_id() > 0;

				if (($system_tag && $sm->user->can('MANAGE_TAGS')) || (!$system_tag && $article_tag->user_id == $sm->user->get_id())){
					$this->_post_master->set_data_object($article_tag);
					if ($this->_post_master->sql_delete()){
						if (!$system_tag) {
							$tags_normal = new Tags_list();
							$tags_normal->get_orphan_tags();
							$tags_normal->sql_delete_list($this->_post_master);
						}
						return true;
					}else{
						$this->set_error_code(2407);
						return false;
					}
				}else{
					$this->set_error_code(2408);
					return false;
				}
			}else{
				$this->set_error_code(2409);
				return false;
			}
		}

		public function add_tag_names_to_topic($tag_names, $topic_id) {

			if (!$this->check_token()) return false;

			if (!vivvo_hooks_manager::call('tag_addTagToGroup', array(&$tag_names, &$topic_id))) {
				return vivvo_hooks_manager::get_status();
			}

			if (!is_array($tag_names)) {
				$tag_names = array_map('trim', explode(',', $tag_names));
			}

			foreach ($tag_names as $tag_name) {

				$tag_name = preg_replace('/[\s\n\r]+/', ' ', $tag_name);

				if ($tag_name) {

					$tag_id = $this->add_tag($tag_name, make_sefriendly($tag_name));

					if ($tag_id === false) {
						return false;
					}

					if ($this->add_tag_to_group($tag_id, $topic_id) === false) {
						return false;
					}
				}
			}

			return true;
		}


		/**
		 * Add tag to tag group
		 *
		 * @param	int		$tag_id
		 * @param	int		$group_id
		 */
		public function add_tag_to_group($tag_id, $group_id, $article_id = 0) {

			if (!vivvo_hooks_manager::call('tag_addTagToGroup', array(&$tag_id, &$group_id, &$article_id))) {
				return vivvo_hooks_manager::get_status();
			}

			$user = vivvo_lite_site::get_instance()->user;

			if ( $user && $user->can('MANAGE_TAGS') ) {

				$tag_group_list = new TagsGroups_list();
				$tag_group = $tag_group_list->get_group_by_id($group_id);

				$tag_list = new Tags_list();
				$tag = $tag_list->get_tag_by_id($tag_id);

				if ($tag_group && $tag) {

					$tg_list = new TagsToTagsGroups_list();

					if( !$tg_list->get_rel($tag_id, $group_id) ) {

						$tag_group_rel = new TagsToTagsGroups();
						$tag_group_rel->set_tag_id($tag_id);
						$tag_group_rel->set_tags_group_id($group_id);

						$this->_post_master->set_data_object($tag_group_rel);

						if (!$this->_post_master->sql_insert()) {
							$this->set_error_code(2415);
							return false;
						}
					}

					if ($article_id > 0){

						$tg_list = new ArticlesTags_list();

						if( !$tg_list->search( array('search_tag_id'=>$tag_id, 'search_tags_group_id'=>$group_id, 'search_article_id'=>$article_id ) ) ) {

							$tag_link = new ArticlesTags();
							$tag_link->set_article_id($article_id);
							$tag_link->set_tag_id($tag_id);
							$tag_link->set_tags_group_id($group_id);
							$tag_link->set_user_id($user->get_id());

							$this->_post_master->set_data_object($tag_link);
							if (!$this->_post_master->sql_insert()) {
								$this->set_error_code(2415);
								return false;
							}
						}
					}

					return true;
				}

				$this->set_error_code(2415);
				return false;
			}

			$this->set_error_code(2410);
			return false;
		}

		/**
		 * Remove tag from tag group
		 *
		 * @param	int		$tag_id
		 * @param	int		$group_id
		 */
		public function remove_tag_from_group($tag_id, $group_id) {

			if( !vivvo_hooks_manager::call('tag_removeTagFromGroup', array(&$tag_id, &$group_id) ) )
				return vivvo_hooks_manager::get_status();

			$user = vivvo_lite_site::get_instance()->user;

			if ( $user && $user->can('MANAGE_TAGS') ) {

				$tag_group_rel = new TagsToTagsGroups_list();
				$tag_group_rel->get_rel($tag_id, $group_id);

				if ($tag_group_rel->sql_delete_list($this->_post_master)) {
					return true;
				}

				$this->set_error_code(2416);
				return false;
			}

			$this->set_error_code(2410);
			return false;
		}
	}
?>