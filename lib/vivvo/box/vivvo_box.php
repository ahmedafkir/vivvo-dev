<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
 *
 * Vivvo CMS v4.6 (build 5661)
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
 * @package    Vivvo
 * @subpackage box
 * @author     Slobodan Utvic <utvara@spoonlabs.com>
 * @copyright  Spoonlbas
 * @license    http://www.vivvo.net/license.php
 * @version    0.1
 * @category   CMS
 */

 	/**
	 * Box comments
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage box
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class box_comments extends module {
		var $_default_template_file = 'system/box_default/box_comments.tpl';

		/**
		 * Generate box output
		 *
		 * @param array $params Parameters
		 */
		function generate_output ($params){
			$this->set_template($params);
			$params += array('pg' => 1, 'fields' => ''); // defaults
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Comments.class.php');
			$comments = new Comments_list(null, $params['fields']);
			$search_params = Comments_list::get_search_params(null, $params);

			if (isset($search_params['search_options']['search_article_id'])){
				$this->_template->assign('article_id', $search_params['search_options']['search_article_id']);
			}

			$comments->search($search_params['search_options'], $search_params['search_sort_by'], $search_params['search_order'], $search_params['search_limit'],$search_params['offset']);

			if (!empty($comments->list)){
				$comments->set_pagination($params['pg']);
				$this->_template->assign('comment_list', $comments->list);
			}

			$sm = vivvo_lite_site::get_instance();

			if(!$sm->user && $sm->guest_group){
				$this->_template->assign('guest_group_wait_comment_for_approval', $sm->guest_group->group_privileges['ARTICLE_COMMENT']);
			}
			$this->_template->assign('comment_list_object', $comments);
		}
	}

	/**
	 * Box users
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage box
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class box_users extends module {
		var $_default_template_file = 'system/box_default/box_users.tpl';

		/**
		 * Generate box output
		 *
		 * @param array $params Parameters
		 */
		function generate_output ($params){
			$this->set_template($params);

			$user_manager = vivvo_lite_site::get_instance()->get_user_manager();

			$users = $user_manager->get_user_list();
			$search_params = $users->get_search_params(null, $params);

			$users->search($search_params['search_options'], $search_params['search_sort_by'], $search_params['search_order'], $search_params['search_limit'], $search_params['offset']);
			if (!empty($users->list)){
				foreach($users->list as $k=>$v){
					$users->list[$k]->set_privileges();
				}
				$this->_template->assign('user_list_object', $users);
				$users->set_pagination($search_params['pg']);
				$this->_template->assign('user_list', $users->list);
				if (count($users->list) == 1){
					$this->_template->assign('user', current($users->list));
				}
			}
		}

		function _serialize_data(){
			return new stdClass();
		}
	}


	class box_files extends module {
		var $_default_template_file = 'system/box_default/box_files.tpl';

		/**
		 * Generate box output
		 *
		 * @param array $params Parameters
		 */
		function generate_output ($params){
			$this->set_template($params);
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/vivvo_file.php');

			if ($params['fs_root']){
				$dir = new dir_list(null, $params['dir'], $params['ext'],  $params['fs_root'], false);
			}else{
				$dir = new dir_list(null, $params['dir'], $params['ext'],  '', false);
			}

			if ($params['ext'] === 'dirext'){
				$dir->get_dirs();
			}else{
				$dir->get_files();
			}

			if (isset($params['template_string'])){
				$this->_template->assign('template_string', $params['template_string']);
			}

			if (isset($params['parent_dir'])){
				$this->_template->assign('parent_dir', $params['parent_dir']);
			}

			$this->_template->assign('dir', $params['dir']);
			$this->_template->assign('file_list_object', $dir);

			$this->_template->assign('file_list', $dir->list);
			$this->_template->assign('box_id', $params['box_id']);
			$this->_template->assign('prefix', $params['prefix']);
		}
	}


	/**
	 * Box sections
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage box
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class box_sections extends module {

		/**
		 * Generate box output
		 *
		 * @var category_list
		 */
		var $categories;

		var $_default_template_file = 'system/box_default/box_sections.tpl';
		/**
		 * Constuctor
		 *
		 * @param array $params Parameters
		 */
		function generate_output($params){
			$this->set_template($params);

			if (isset($params['template_string'])){
				$this->_template->assign('template_string', $params['template_string']);
			}

			$this->categories = vivvo_lite_site::get_instance()->get_categories();

			if (isset($params['search_ids'])){
				if (!is_array($params['search_ids'])){
					$array_ids = explode(',',$params['search_ids']);
				}else{
					$array_ids = $params['search_ids'];
				}
				if (defined('VIVVO_FORCE_CATEGORY_RESTRICTION') && VIVVO_FORCE_CATEGORY_RESTRICTION != ''){
					$restrict = explode(',', VIVVO_FORCE_CATEGORY_RESTRICTION);
					$array_ids = array_intersect($array_ids, $restrict);
				}
				$out_array = array();
				if (!empty($array_ids)){
					foreach($array_ids as $key){
						if (key_exists($key, $this->categories->list)){
							$out_array[$key] =& $this->categories->list[$key];
						}
					}
				}

				$this->_template->assign('categories', $out_array);

			}else{
				if (isset($params['id'])){
					$param_id = (int) $params['id'];
					if ($param_id == 0 && $this->categories->root_category->id){
						$param_id = $this->categories->root_category->id;
					}
				}else{
					$param_id = $this->categories->root_category->id;
				}

				if (isset($params['prefix'])){
					$this->_template->assign('prefix', $params['prefix']);
				}

				$this->_template->assign('categories', $this->categories->list[$param_id]->subcategories);

				$this->_template->assign('id', $param_id);
			}
		}

		function __destruct () {
			if (!empty($this->categories)) unset($this->categories);
			parent::__destruct ();
		}

		function _serialize_data(){
			$list =& $this->_template->_replacments;
			foreach($this->categories->list as $k=>$v){
				$this->categories->list[$k]->href = $this->categories->list[$k]->get_href();
			}
			return parent::_serialize_data();
		}
	}

	/**
	 * Box article list view
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage box
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class box_article_list extends module {
		var $cache = false;
		var $_default_template_file = 'system/box_default/box_article_list.tpl';

		/**
		 * Generate box output
		 *
		 * @param array $params Parameters
		 */
		function generate_output($params){
			$this->set_template($params);

			$params += array('pg' => 1, 'fields' => ''); // defaults
			require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

			$al = new Articles_list(null, $params['fields']);

			$sm = vivvo_lite_site::get_instance();

			if (isset($params['exclude_printed'])){
				$params['search_exclude_id'] = $sm->printed_articles;
			}

			if (isset($params['use_view']) && !empty($params['use_view'])){
				$al->_sql_table = secure_sql($params['use_view']);
			}

			$search_params = $al->get_search_params(null, $params);

			$al->search($search_params['search_options'], $search_params['search_sort_by'], $search_params['search_order'], $search_params['search_limit'], $search_params['offset']);

			if (isset($params['add_to_printed'])){
				$sm->printed_articles = array_merge($sm->printed_articles, $al->get_list_ids());
				$sm->printed_articles = array_unique($sm->printed_articles);
			}
			
			$search_params['search_do_advanced'] = true;
			$feed_url = make_absolute_url('index.html?search_options=' . rtrim(base64_encode(serialize($search_params)), '=') . '&type=rss');
			$this->_template->assign('feed_url', $feed_url);

			if (!empty ($al->list)){
				$this->_template->assign('article_list_object', $al);
				$al->set_pagination($params['pg']);
				$this->_template->assign('article_list', $al->list);
			}else{
				$this->_template->assign('article_list_object', strval('0'));
				$this->_template->assign('article_list',  strval('0'));
			}

			if (isset ($params['TITLE'])){
				$this->_template->assign('TITLE', $params['TITLE']);
			}

			if (isset ($params['BOX_ID'])){
				$this->_template->assign('BOX_ID', $params['BOX_ID']);
			}
		}

		function _serialize_data(){
			$list =& $this->_template->_replacments;
			if (is_array($list['article_list'])){
				foreach($list['article_list'] as $k => $v){
					$list['article_list'][$k]->href = $list['article_list'][$k]->get_href();
					$list['article_list'][$k]->category_href = $list['article_list'][$k]->get_category_href();
					$list['article_list'][$k]->author_href = $list['article_list'][$k]->get_author_href();
					$list['article_list'][$k]->author_name = $list['article_list'][$k]->get_author_name();
					unset($list['article_list'][$k]->author_obj);
				}
			}
			return parent::_serialize_data();
		}
	}

	class box_tags extends module {

		var $_default_template_file = 'system/box_default/box_tags.tpl';

		function generate_output($params){

			$this->set_template($params);

			if (!empty($params['search_advanced_mode'])) {

				class_exists('Tags') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php';

				$list = new Tags_list();
				$list->search_from_params($params);

				$this->_template->assign('tags_list', $list->list);
				$this->_template->assign('tag_list', $list->list);
				$this->_template->assign('tags', $list);

			} else {

				$params['search_limit'] = intval($params['search_limit']);
				if ($params['search_limit'] == 0) $params['search_limit'] = 20;
				if (!isset($params['offset'])) $params['offset'] = 0;
				if (!isset($params['search_sort_by'])) $params['search_sort_by'] = '';
				if (!isset($params['search_order'])) $params['search_order'] = '';

				$um = vivvo_lite_site::get_instance()->get_url_manager();

				if (isset($params['search_article_id'])){
					require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesTags.class.php');
					$articles_tags_list = new ArticlesTags_list(null, $params['fields']);
					if (!empty($params['search_user_id'])){
						$articles_tags_list->get_by_article_user($params['search_article_id'], $params['search_user_id']);
					}elseif(isset($params['search_topic_id'])){
						$articles_tags_list->get_articles_tags_by_topic_id($params['search_article_id'], $params['search_topic_id']);
					}else{
						$articles_tags_list->get_articles_tags_by_article($params['search_article_id']);
					}

					$this->_template->assign('tag_list', $articles_tags_list->list);
					$this->_template->assign('tags', $articles_tags_list);
				}else if (isset($params['search_starting_with']) && !empty($params['search_starting_with'])){
					require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');
					$tag_list = new Tags_list();
					$tag_list->search(array('search_starting_with' => $params['search_starting_with']), $params['search_sort_by'], $params['search_order'], $params['search_limit'], $params['offset']);
					$this->_template->assign('tag_list', $tag_list->list);
					$this->_template->assign('tags', $tag_list);
				}else if (!empty($params['search_sort_by'])){
					require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');
					$tag_list = new Tags_list();
					$tag_list->search(array(), $params['search_sort_by'], $params['search_order'], $params['search_limit'], $params['offset']);
					$this->_template->assign('tag_list', $tag_list->list);
					$this->_template->assign('tags', $tag_list);
				}else{
					require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');
					$tag_list = new Tags_list();
					$tag_list->get_rated_tags($params['search_limit'], isset($params['search_topic_id']) ? $params['search_topic_id'] : 0, isset($params['search_category_id']) ? $params['search_category_id'] : -1);
					$tag_list->set_fontsize();

					$this->_template->assign('tag_list', $tag_list->list);
					$this->_template->assign('tags', $tag_list);
				}
			}

			if (isset ($params['title'])){
				$this->_template->assign('title', $params['title']);
			}

			$this->_template->assign('box_id', $params['box_id']);
		}
	}


	class box_tags_groups extends module {

		function generate_output($params) {

			$this->set_template($params);

			require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php';

			$list = new TagsGroups_list();
			$list->search_from_params($params);

			$this->_template->assign('tags_groups_list', $list->list);
			$this->_template->assign('tags_groups', $list);
			$this->_template->assign('topics_list', $list->list);
			$this->_template->assign('topics', $list);

			if (isset($params['title'])) {
				$this->_template->assign('title', $params['title']);
			}

			$this->_template->assign('box_id', $params['box_id']);
		}
	}

	class box_user_filters extends module {
		var $_default_template_file = 'system/box_default/box_user_filters.tpl';

		function generate_output($params){
			$this->set_template($params);
			$sm = vivvo_lite_site::get_instance();
			if ($sm->user){
				require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/UserFilters.class.php');
				$uf_list = new UserFilters_list();
				$search_array = array ('search_user_id' => $sm->user->get_id(), 'search_section' => $params['search_section']);
				if (!empty($params['search_html_tag_id'])){
					$search_array['search_html_tag_id'] = $params['search_html_tag_id'];
				}
				$uf_list->search($search_array);

				$this->_template->assign('user_filters_list', $uf_list->list);
			}else{
				$this->_template->assign('user_filters_list', strval(''));
			}
		}
	}

	/**
	 * Box feed
	 *
	 * @version Release: @package_version@
	 * @license http://www.vivvo.net/license.php
	 * @copyright SpoonLabs
	 * @package Vivvo
	 * @subpackage box
	 * @author Slobodan Utvic <utvara@spoonlabs.com>
	 */

	class box_feed extends module {
		var $feed;
		var $_default_template_file = 'system/box_default/box_feed.tpl';

		/**
		 * Generate box output
		 *
		 * @param array $params Parameters
		 */
		function generate_output($params){
			$this->set_template($params);

			require_once (VIVVO_FS_INSTALL_ROOT . 'lib/simplepie/simplepie.php');

			$this->feed = new SimplePie();
			if ($params['url'] == '') $params['url'] = 'http://anonym.to/?http://www.vivvo.net/rss_latest.php';

			$this->feed->set_feed_url($params['url']);
			$this->feed->enable_cache(true);
			$this->feed->set_cache_location(VIVVO_FS_ROOT . 'cache/feed');
			if (isset($params['time']) && intval($params['time']) != 0){
				$this->feed->set_cache_duration(intval($params['time']) * 60);
			}else{
				$this->feed->set_cache_duration(60 * 60);
			}

			$this->feed->init();

			$params['limit'] = intval($params['limit']);

			if ($params['limit'] == 0) $params['limit'] = 10;

			$items = $this->feed->get_items(0, $params['limit']);
			$this->_template->assign('feed_list', $items);
		}
	}

	class box_timeline extends module {
		var $data = array();
		var $_default_template_file = 'system/box_default/box_timeline.tpl';

		function generate_output($params){
			$this->set_template($params);
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			if (isset($params['max_image_height'])){
				$max_image_height = intval($params['max_image_height']);
			}

			if ($max_image_height == 0){
				$max_image_height = 50;
			}

			$width = intval($params['width']);

			if (isset($params['search_by_year']) || $um->isset_param('search_by_year')){
				if (isset($params['search_by_year'])){
					$year = (int) $params['search_by_year'];
				}else{
					$year = (int) $um->get_param('search_by_year');
				}

				if (isset($params['search_by_month']) || $um->isset_param('search_by_month')){
					if (isset($params['search_by_month'])){
						$month = (int) $params['search_by_month'];
					}else{
						$month = (int) $um->get_param('search_by_month');
					}

					//Articles per day
					if ($month > 0 && $year > 1900){
						$query = "SELECT DAY( created ) AS day, count( * ) AS count FROM " . VIVVO_DB_PREFIX . "articles WHERE YEAR( created ) = $year AND MONTH (created) = $month AND status != 0 AND created < NOW() GROUP BY TO_DAYS( created )";
						$num_of_days = date ('t', mktime(0, 0, 0, $month, 1, $year));
						for ($i = 1; $i <= $num_of_days; $i++){
							$this->data[$i] = array();
							$this->data[$i]['label'] = $i;
							$this->data[$i]['count'] = 0;
							$this->data[$i]['url'] = '';
						}
						$max = 0;
						$res = $sm->get_db()->query($query);
						if (!is_a($res, 'mdb2_error')){
							while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))){
								if ($row['count'] > 0){
									$this->data[$row['day']]['count'] += $row['count'];
									$this->data[$row['day']]['url'] = make_proxied_url('date/' . $year . '/' . $month . '/' . $row['day']);
									$max = max($row['count'], $max);
								}
							}
						}
						$res->free();
					}

					if ($max > 0){
						for ($i = 1; $i <= $num_of_days; $i++){
							$this->data[$i]['count'] = intval($this->data[$i]['count']/$max * $max_image_height);
						}
					}

					$this->_template->assign('width', floor($width / $num_of_days));
					$this->_template->assign('count_list', $this->data);
				}else{
					$lang = vivvo_lang::get_instance();
					//Articles per month
					if ($year > 1900){
						$query = "SELECT  MONTH( created ) AS month, count( * ) AS count FROM " . VIVVO_DB_PREFIX . "articles WHERE YEAR( created ) = $year AND status != 0 AND created < NOW() GROUP BY MONTH( created )";
					}

					for ($i = 1; $i <= 12; $i++){
						$this->data[$i] = array();
						$this->data[$i]['label'] = substr($lang->get_value('LNG_MONTH_' . $i), 0, 3);
						$this->data[$i]['count'] = 0;
						$this->data[$i]['url'] = '';
					}
					$max = 0;
					$res = $sm->get_db()->query($query);
					if (!is_a($res, 'mdb2_error')){
						while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))){
							if ($row['count'] > 0){
								$this->data[$row['month']]['count'] += $row['count'];
								$this->data[$row['month']]['url'] = make_proxied_url('date/' . $year . '/' . $row['month']);
								$max = max($row['count'], $max);
							}
						}
					}
					if ($max > 0){
						for ($i = 1; $i <= 12; $i++){
							$this->data[$i]['count'] = intval($this->data[$i]['count']/$max * $max_image_height);
						}
					}
					$res->free();

					$this->_template->assign('width', floor($width / 12));
					$this->_template->assign('count_list', $this->data);
				}
			}
		}
	}

	class box_author_timeline extends module {
		var $data = array();
		var $_default_template_file = 'system/box_default/box_author_timeline.tpl';

		function generate_output($params){
			$this->set_template($params);
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$lang = $sm->get_lang();

			$search_author_id = $params['search_author_id'];
		//	$search_params = vivvo_lite_site::get_instance()->article_list_params;

			$base_url = CURRENT_URL;
			$base_url = preg_replace('/index(\.\d+)?\.html$/', '', $base_url);
			$base_url = preg_replace('/\/?\/\d{1,2}\/?$/', '', $base_url);
			$base_url = preg_replace('/\/?\d{4}$/', '', $base_url);

			$base_url = rtrim($base_url, '/');

			if ($search_author_id != 0){
				//Articles per day
				$query = "SELECT MONTH( created ) AS month, YEAR( created ) AS year,  count( * ) AS count FROM " . VIVVO_DB_PREFIX . "articles WHERE user_id = $search_author_id AND status > 0 AND created < NOW() GROUP BY ( EXTRACT(YEAR_MONTH FROM created)) ORDER BY year DESC, month DESC";

				$res = $sm->get_db()->query($query);
				$year = 100000;
				if (!is_a($res, 'mdb2_error')){
					while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))){
						if ($year > $row['year']){
							$year = $row['year'];
							$this->data[$year]['label'] = $year;
							$this->data[$year]['data'] = array();
							$this->data[$year]['url'] = $base_url . '/' . $year . '/';
						}
						if ($row['count'] > 0){
							$this->data[$year]['data'][$row['month']] = array();
							$this->data[$year]['data'][$row['month']]['data'] = $row['month'];
							$this->data[$year]['data'][$row['month']]['label'] = $lang->get_value('LNG_MONTH_' . $row['month']);
							$this->data[$year]['data'][$row['month']]['count'] = $row['count'];

							$this->data[$year]['data'][$row['month']]['url'] = $base_url . '/' . $year . '/' . $row['month'] . '/';
						}
					}
				}
				$res->free();
				$this->_template->assign('count_list', $this->data);
			}
		}
	}

	class box_calendar extends module {
		var $data = array();
		var $_default_template_file = 'system/box_default/box_calendar.tpl';

		function generate_output($params){
			$this->set_template($params);
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$lang = vivvo_lang::get_instance();

			if (!isset($params['search_by_year']) && !$um->isset_param('search_by_year')){
				$year = date ('Y', VIVVO_START_TIME);
				$params['search_by_year'] = $year;
			}else{
				if (isset($params['search_by_year'])){
					$year = (int) $params['search_by_year'];
				}else{
					$year = (int) $um->get_param('search_by_year');
					$params['search_by_year'] = $year;
				}
			}

			if (!isset($params['search_by_month']) && !$um->isset_param('search_by_month')){
				$month = date('n', VIVVO_START_TIME);
				$params['search_by_month'] = $month;
			}else{
				if (isset($params['search_by_month'])){
					$month = (int) $params['search_by_month'];
				}else{
					$month = (int) $um->get_param('search_by_month');
					$params['search_by_month'] = $month;
				}
			}


			//Articles per day
			if ($month > 0 && $year > 1900){
				if (!isset($params['search_do_advanced'])){
					$params['search_status'] = -1;
				}

				require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
				$article_params = Articles_list::get_search_params(null, $params);



				$article_list = new Articles_list();
				$article_list->search($article_params['search_options'], '', 'ascending', 0, 0, false);
				$article_list->_query->_fields = array();
				$article_list->_query->add_fields('DAYOFMONTH( created ) AS day');
				$article_list->_query->add_fields('count( * ) AS count');

				$article_list->_query->add_group_by('TO_DAYS( created )');

				$query = $article_list->_query->get_query();

				$num_of_days = date ('t', mktime(0, 0, 0, $month, 1, $year));
				$day_of_week = date ('w', mktime(0, 0, 0, $month, 1, $year));

				$prev_month = $month - 1;
				if ($prev_month == 0){
					$prev_month = 12;
					$prev_year = $year - 1;
				}else{
					$prev_year = $year;
				}

				$next_month = $month + 1;
				if ($next_month == 13){
					$next_month = 1;
					$next_year = $year + 1;
				}else{
					$next_year = $year;
				}

				$prev_num_of_days = date ('t', mktime(0, 0, 0, $prev_month , 1, $prev_year));

				if ($day_of_week != 0){
					for ($i = 1 -$day_of_week; $i <= 0 ; $i++){
						$this->data[$i] = array();
						$this->data[$i]['label'] = '';
						$this->data[$i]['count'] = 0;
						$this->data[$i]['url'] = '';
					}
				}

				for ($i = 1; $i <= $num_of_days; $i++){
					$this->data[$i] = array();
					$this->data[$i]['label'] = $i;
					$this->data[$i]['count'] = 0;
					$this->data[$i]['url'] = '';
				}

				$next_day_of_week = date ('w', mktime(0, 0, 0, $month, $num_of_days, $year));

				if ($next_day_of_week != 6){
					for ($i = $num_of_days + 1; $i < $num_of_days + 6 - $next_day_of_week + 1; $i++){
						$this->data[$i] = array();
						$this->data[$i]['label'] = '';
						$this->data[$i]['count'] = 0;
						$this->data[$i]['url'] = '';
					}
				}

				$count_sum = 0 ;

				$res = $sm->get_db()->query($query);
				if (!is_a($res, 'mdb2_error')){
					while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))){
						if ($row['count'] > 0){
							$count_sum += $row['count'];
							$this->data[$row['day']]['count'] += $row['count'];
							if (isset($params['search_status']) && $params['search_status'] == -1){
								$this->data[$row['day']]['url'] = make_proxied_url('archive/' . $year . '/' . $month . '/' . $row['day']);
							}else{
								$day_params = $article_params;
								$day_params['search_do_advanced'] = true;
								$day_params['search_by_day'] = $row['day'];
								$search_option = base64_encode(serialize($day_params));
								$this->data[$row['day']]['url'] = make_absolute_url('index.php?search_options=' . $search_option);
							}
						}
					}
				}
				$res->free();
			}

			if ($count_sum){
				$month_params = $article_params;
				if (isset($params['search_status']) && $params['search_status'] == -1){
					$this->_template->assign('month_url', make_proxied_url('archive/' . $year . '/' . $month . '/'));
				}else{
					$month_params['search_do_advanced'] = true;
					$search_option = base64_encode(serialize($month_params));
					$this->_template->assign('month_url', make_absolute_url('index.php?search_options=' . $search_option));
				}
			}

			$this->_template->assign('count_list', $this->data);
			$this->_template->assign('year', $year);
			$this->_template->assign('month', strval($lang->get_value('LNG_MONTH_' . $month)));
			$this->_template->assign('prev_year', intval($year - 1));
			$this->_template->assign('prev_month_year', $prev_year);
			$this->_template->assign('prev_month', $prev_month);
			$this->_template->assign('next_year', intval($year + 1));
			$this->_template->assign('next_month_year', $next_year);
			$this->_template->assign('next_month', $next_month);
		}
	}
?>