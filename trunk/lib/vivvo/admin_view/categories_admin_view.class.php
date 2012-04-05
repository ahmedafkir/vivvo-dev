<?php
/* =============================================================================
 * $Revision: 5439 $
 * $Date: 2010-06-03 17:24:55 +0200 (Thu, 03 Jun 2010) $
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
 * @see  'vivvo_plugin.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_admin_view.php');

/**
 * @see  'lib/vivvo/core/TagsGroups.class.php'
 */
require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php');

/**
 * @see  'lib/vivvo/core/Tags.class.php'
 */
require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');

	/**
	 * Plugin class
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		Vivvo
	 * @subpackage	plugin
	 * @version		0.1
	 * @category	CMS
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class categories_admin_view extends vivvo_admin_view {

		/**
		 * @var	array	List of available views
		 */
		public $views = array('category_form', 'link_form', 'topic_form', 'topics', 'tag_list', 'autocomplete_links');

		/**
		 * @var	string	Path to admin templates
		 */
		public $_template_root = 'templates/categories/';

		/**
		 * Checks if user has privileges to access asset manager
		 *
		 * @return	bool
		 */
		public function check_privileges() {
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->is_admin();
		}

		/**
		 * Handles service action passed trough request
		 */
		public function handle_action() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();

			if ($action and (empty($_SERVER['HTTP_X_REQUESTED_WITH']) or $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')) {

				$url = VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . CURRENT_ADMIN_VIEW_FILE;

				if ($query = $um->isset_param('category_form')) {
					$url .= '?category_form';
				} elseif ($query = $um->isset_param('link_form')) {
					$url .= '?link_form';
				} elseif ($query = $um->isset_param('topic_form')) {
					$url .= '?topic_form';
				}

				if ($um->isset_param('pg')) {
					$url .= ($query ? '&' : '?') . 'pg=' . $um->get_param('pg');
				}

				if ($um->isset_param('search_id')) {
					$url .= ($query ? '&' : '?') . 'search_id=' . $um->get_param('search_id');
				}

				$dm->store_dump(md5($_SERVER['HTTP_HOST'] . $url));

				header('Location: ' . $url);
				exit;
			}
		}

		/**
		 * Loads admin css/javascript
		 */
		public function load_admin_header() {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css');
			$header->add_script(array(
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/categories.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/tree.js'
			));
		}

		/**
		 * Default view
		 */
		public function _default_view() {

			$template = vivvo_lite_site::get_instance()->get_template();

			$template->assign_template('content', $this->_category_form());
			$template->assign_template('left', $this->_tabs());

			return $template;
		}

		/**
		 * Tabs "view" (required for topics list to be sorted corectly when default view is rendered)
		 */
		protected function _tabs() {
			$tabs_template = $this->load_template($this->_template_root . 'tabs.xml');
			$params = array(
				'search_sort_by' => 'name',
				'search_order' => 'ascending'
			);
			$tabs_template->assign('topic_list_params', $params);

			return $tabs_template;
		}

		/**
		 * Category form view
		 */
		public function _category_form() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$action = $sm->action_status;

			$content_template = $this->load_template($this->_template_root . 'category_form.xml');
			$content_template->assign('selected_tab', strval('categories_tree'));

			if ($um->isset_param('action') && $action === false) {
				$category = new Categories($sm, $um->get_param_regexp('/^CATEGORY_/'));
			} elseif ($um->isset_param('search_id')) {
				$categoies = $sm->get_categories();
				if (isset($categoies->list[$um->get_param('search_id') << 0])) {
					$category = $categoies->list[$um->get_param('search_id') << 0];
				} else {
					$category = false;
				}
			}

			$content_template->assign('current_category', $category);

			return $content_template;
		}

		/**
		 * Link form view
		 */
		public function _link_form() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$action = $sm->action_status;

			$complete = empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest';

			if ($complete) {
				$content_template = $sm->get_template();
				$content_template->assign('selected_tab', strval('categories_tree'));
				$content_template->assign_template('content', $this->load_template($this->_template_root . 'link_form.xml'));
				$content_template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));
			} else {
				$content_template = $this->load_template($this->_template_root . 'link_form.xml');
			}

			if ($um->isset_param('action') && $action === false) {
				$category = new Categories($sm, $um->get_param_regexp('/^CATEGORY_/'));
			} elseif ($um->isset_param('search_id')) {
				$categoies = $sm->get_categories();
				if (isset($categoies->list[$um->get_param('search_id') << 0])) {
					$category = $categoies->list[$um->get_param('search_id') << 0];
				} else {
					$category = false;
				}
			}

			$content_template->assign('current_category', $category);

			if ($complete) {
				echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
				echo $sm->get_output();
				exit;
			}

			return $content_template;
		}

		/**
		 * Topic form view
		 */
		public function _topic_form() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$action = $sm->action_status;

			$complete = empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest';

			if ($complete) {
				$content_template = $sm->get_template();
				$content_template->assign('selected_tab', 'topics');
				$content_template->assign_template('content', $this->load_template($this->_template_root . 'topic_form.xml'));
				$content_template->assign_template('left', $this->load_template($this->_template_root . 'tabs.xml'));
			} else {
				$content_template = $this->load_template($this->_template_root . 'topic_form.xml');
			}

			$content_template->assign('pg', $um->get_param('pg'));

			if ($um->isset_param('action') && $action === false) {
				$topic = new TagsGroups($sm, $um->get_param_regexp('/^TAG_GROUP_/'));
				$topic->set_id(-1);
			} elseif ($um->isset_param('search_id')) {
				if (($search_id = $um->get_param('search_id')) != '') {
					$topic = TagsGroups_list::factory()->get_group_by_id($search_id);
				}
			}

			$content_template->assign('topic', $topic);

			if ($complete) {
				echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
				echo $sm->get_output();
				exit;
			}

			return $content_template;
		}

		/**
		 * Topics list
		 */
		public function _topics() {
			$content_template = $this->load_template($this->_template_root . 'topic_list.xml');
			$params = TagsGroups_list::get_search_params_from_url(vivvo_lite_site::get_instance());
			$params['search_sort_by'] = 'name';
			$params['search_order'] = 'ascending';
			$content_template->assign('pg', $params['pg']);
			$content_template->assign('topic_list_params', $params);
			return $content_template;
		}

		/**
		 * Tags list (tags in topic, search results)
		 */
		public function _tag_list() {
			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			$list_template = $this->load_template($this->_template_root . 'tag_list.xml');

			$params = Tags_list::get_search_params_from_url($sm);
			$params['search_advanced_mode'] = 1;

			if ($um->isset_param('topic_id')) {

				$topic = TagsGroups_list::factory()->get_group_by_id($um->get_param('topic_id'));

				$params['search_topic_id'] = $topic->get_id();

				$list_template->assign('topic', $topic);
			}

			$list_template->assign('tag_list_params', $params);
			$search_sort_by = $params['search_sort_by'] . '.' . $params['search_order'];
			$list_template->assign('search_sort_by', $search_sort_by);
			$list_template->assign('search_limit', $params['search_limit']);

			return $list_template;
		}

		/**
		 * Autocomplete box feed
		 */
		public function _autocomplete_links() {

			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$pm = $sm->get_plugin_manager();
			$content_template = new template(null, $template);
			$content_template->set_template_file($this->_template_root . 'autocomplete_links.xml');

			$hrefs = array();

			$db = $sm->get_db();
			$name = secure_sql($um->get_param('name'));

			$sql = "(SELECT id, category_name AS title, sefriendly, '' AS category_id,  'Categories' AS type FROM ".VIVVO_DB_PREFIX."categories WHERE category_name LIKE '$name%')
					 UNION
				    (SELECT id, title, sefriendly, category_id, 'Articles' AS type FROM ".VIVVO_DB_PREFIX."articles WHERE title LIKE '$name%')";

			if ($pm->is_installed('pages')) {
				$sql .= "UNION (SELECT id, title, sefriendly, '' AS category_id, 'Pages' AS type FROM ".VIVVO_DB_PREFIX."pages WHERE title LIKE '$name%')";
			}

			$sql .= "UNION (SELECT t.id, CONCAT(tg.name,': ',t.name) AS title, t.sefriendly AS sefriendly, tg.url AS category_id, 'Tags' AS type FROM ".VIVVO_DB_PREFIX."tags AS t INNER JOIN ".VIVVO_DB_PREFIX."tags_to_tags_groups AS ttg ON ttg.tag_id = t.id INNER JOIN ".VIVVO_DB_PREFIX."tags_groups AS tg ON ttg.tags_group_id = tg.id WHERE t.name LIKE '$name%' GROUP BY t.id, tg.id) " .
			        "UNION (SELECT id, name AS title, url AS sefriendly, '' AS category_id,  'Topics' AS type FROM ".VIVVO_DB_PREFIX."tags_groups WHERE name LIKE '$name%')";

			$res = $db->query($sql);

			if (!PEAR::isError($res)) {
				$i = 0;
				while ($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
					$hrefs[$i] = array();
					$hrefs[$i]['title'] = $row['title'];
					$hrefs[$i]['id'] =  $row['id'];
					$hrefs[$i]['type'] =  $row['type'];
					switch($row['type']){
						case 'Articles':
							if (!$article){
								require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
								$article = new Articles();
							}
							$article->id = $row['id'];
							$article->title = $row['title'];
							$article->sefriendly = $row['sefriendly'];
							$article->category_id = $row['category_id'];
							$hrefs[$i]['href'] = $article->get_href();
						break;
						case 'Categories':
							if (!$cat){
								$cat = $sm->get_categories();
							}
							$hrefs[$i]['href'] = $cat->list[$row['id']]->get_href();
						break;
						case 'Pages':
							if (!$page){
								require_once (VIVVO_FS_PLUGIN_ROOT . 'plugins/pages/Pages.class.php');
								$page = new Pages();
							}
							$page->id = $row['id'];
							$page->title = $row['title'];
							$page->sefriendly = $row['sefriendly'];
							$hrefs[$i]['href'] = $page->get_href();
						break;
						case 'Tags':
							if (!$tag) {
								require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');
								$tag = new Tags();
							}
							$tag->id = $row['id'];
							$tag->name = $row['title'];
							$tag->sefriendly = $row['sefriendly'];
							$tag->group_url = $row['category_id'] . '/';
							$hrefs[$i]['href'] = $tag->get_href();
						break;
						case 'Topics':
							if (!$topic){
								require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php');
								$topic = new TagsGroups();
							}
							$topic->id = $row['id'];
							$topic->name = $row['title'];
							$topic->url = $row['sefriendly'];
							$hrefs[$i]['href'] = rtrim($topic->get_href(), '/');
						break;
					}
					$i++;
				}
			}

			$content_template->assign('link_list', $hrefs);
			return $content_template;
		}
	}
?>