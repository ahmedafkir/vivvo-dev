<?php
/* =============================================================================
 * $Revision: 5101 $
 * $Date: 2010-04-23 16:30:18 +0200 (Fri, 23 Apr 2010) $
 *
 * Vivvo CMS v4.7 (build 6082)
 *
 * Copyright (c) 2012, Spoonlabs d.o.o.
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
	class articles_admin_view extends vivvo_admin_view {

		var $views = array('list_output', 'edit_tags', 'stats', 'autocomplete_tags');

		var $_template_root = 'templates/articles/';

		function handle_action() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();

			if ($action && !$um->isset_param('list_output')) {
				$dm->store_dump(md5(CURRENT_URL));
				header('Location: ' . VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . CURRENT_ADMIN_VIEW_FILE . preg_replace('/^[^\?]+/', '', CURRENT_URL));
				exit;
			}
		}

		/**
		 * Load admin javaScript
		 */
		function load_admin_header () {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(array(
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/modal.css',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/file_picker.css'
			));
			$header->add_script(array(VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . 'js/control_modal.js',
				VIVVO_URL . 'flash/amline/swfobject.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/list.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/articles.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/textboxlist.js'
			));
		}

		function _default_assignments() {

			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();

			$sm->set_content();
			$template->set_template_file(VIVVO_FS_ADMIN_ROOT . VIVVO_FS_ADMIN_DIR . 'templates/frame.xml');

			if ($um->isset_param('search_limit')) {
				$cur_page = (int) $um->get_param('pg');
				if (empty($cur_page)) $cur_page=1;
				$sm->article_list_params['search_limit'] = (int) $um->get_param('search_limit');
				$sm->article_list_params['offset'] = ($cur_page-1) * $sm->article_list_params['search_limit'];
			} else {
				$cur_page = (int) $um->get_param('pg');
				if (empty($cur_page)) $cur_page=1;
				$sm->article_list_params['search_limit'] = 10;
				$sm->article_list_params['offset'] = ($cur_page-1) * $sm->article_list_params['search_limit'];
			}

			if (!$sm->user->is_admin() && !$sm->user->is('EDITOR')) {
				$sm->article_list_params['search_options']['search_user_id'] = $sm->user->get_id();
			}
		}

		function _default_view() {

			$template = vivvo_lite_site::get_instance()->get_template();

			$template->assign_template('content', $this->_list_output());
			$template->assign_template('left', $this->_leftnav());

			return $template;
		}

		function _list_output() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			$content_template = $this->load_template($this->_template_root . 'list.xml');

			if (!$sm->user->is_admin()) {

				$editor_restriction = $sm->user->get_privilege_object_ids('WRITE', 'Categories');

				if (!in_array('0', $editor_restriction)) {

				    if (isset ($sm->article_list_params['search_options']['search_cid'])) {
                        if (!is_array($sm->article_list_params['search_options']['search_cid'])) {
                            $sm->article_list_params['search_options']['search_cid'] = explode(',', $sm->article_list_params['search_options']['search_cid']);
                        }
                        $sm->article_list_params['search_options']['search_cid'] = array_intersect($editor_restriction, $sm->article_list_params['search_options']['search_cid']);
                    }
                    if (empty($sm->article_list_params['search_options']['search_cid'])) {
                        $sm->article_list_params['search_options']['search_cid'] = $editor_restriction;
                    }
					if ($um->isset_param('search_category_id')) {
						$search_cid = array_intersect(
							explode(',', $um->get_param('search_category_id')),
							$sm->article_list_params['search_options']['search_cid']
						);
					}
                }
			}

			if (!$search_cid and $um->isset_param('search_category_id')) {
				$search_cid = explode(',', $um->get_param('search_category_id'));
			}

			if ($search_cid) {
				$sm->article_list_params['search_options']['search_cid'] = $search_cid;
				$content_template->assign('search_category_id', secure_sql_in($search_cid));
			}

			foreach (array('search_tag_id', 'search_topic_id', 'search_author', 'search_user_id') as $filter) {
				if ($um->isset_param($filter)) {
					$sm->article_list_params[$filter] = $um->get_param($filter);
					$content_template->assign($filter, $um->get_param($filter));
				}
			}

			$content_template->assign('search_limit', $sm->article_list_params['search_limit']);
			$content_template->assign('search_sort_by', strval($sm->article_list_params['search_sort_by'] . '.' . $sm->article_list_params['search_order']));

			if ($sm->article_list_params['search_options']['search_tag'] != '') {
				$content_template->assign('disable_all', intval(1));
			}

			$content_template->assign('list_output', intval(1));
			$content_template->assign('content_params', $sm->article_list_params);

			class_exists('TagsGroups') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php';

			$topic = TagsGroups_list::factory()->get_group_by_id(0);
			$content_template->assign('default_topic', $topic);

			return $content_template;
		}

		function _leftnav() {
			$content_template = $this->load_template($this->_template_root . 'tabs.xml');
			$content_template->assign('user_filters_section', strval('article'));

			return $content_template;
		}

		function _edit_tags() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			$template = $this->load_template($this->_template_root . 'edit_tags_modal.xml');

			if ($um->isset_param('article_id')) {

				$article = Articles_list::factory()->get_article_by_id($um->get_param('article_id'));

				if ($article) {
					$template->assign('existing_tags', $article->get_applied_tags());
				}
			}

			return $template;
		}

		function _stats() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			$template = $this->load_template($this->_template_root . 'stats_modal.xml');

			if ($um->isset_param('article_id')) {

				$article = Articles_list::factory()->get_article_by_id($um->get_param('article_id'));

				if ($article) {
					$template->assign('article', $article);
				}
			}

			return $template;
		}

		function _autocomplete_tags() {

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			$output = '';

			if ($um->isset_param('name')) {

				$name = escape_sql_like(secure_sql($um->get_param('name')));

				$res = $sm->get_db()->query(
					'SELECT name FROM ' . VIVVO_DB_PREFIX . "tags WHERE name LIKE '$name%'"
				);

				if (!PEAR::isError($res)) {

					$attributes[] = ENT_QUOTES;
					$attributes[] = 'UTF-8';
					$tags = array_map('html_entity_decode', $res->fetchCol(),$attributes);

					$res->free();

					$output = '<ul><li>' . implode('</li><li>', $tags) . '</li></ul>';
				}
			}

			echo $output;
			exit;
		}
	}
?>