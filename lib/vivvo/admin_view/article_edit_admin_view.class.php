<?php
/* =============================================================================
 * $Revision: 5022 $
 * $Date: 2010-04-19 13:39:19 +0200 (Mon, 19 Apr 2010) $
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
 * @see  'lib/vivvo/framework/vivvo_admin_view.php'
 */
require_once(VIVVO_FS_FRAMEWORK . 'vivvo_admin_view.php');

/*
 * @see  'lib/vivvo/core/Articles.class.php'
 */
require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

/*
 * @see  'lib/vivvo/core/TagsGroups.class.php'
 */
require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php');

/*
 * @see  'lib/vivvo/core/ArticlesTags.class.php'
 */
require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesTags.class.php');

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
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class article_edit_admin_view extends vivvo_admin_view {

		/**
		 * @var	array	List of available views
		 */
		public $views = array('frontend_edit', 'revisions', 'tags_feed');

		/**
		 * @var	string
		 */
		public $_template_root = 'templates/article_edit/';

		/**
		 * Checks if user has privileges to access asset manager
		 *
		 * @return	bool
		 */
		public function check_privileges() {
			$sm = vivvo_lite_site::get_instance();
			return $sm->user && $sm->user->can('WRITE');
		}

		/**
		 * Handles service action passed trough request
		 */
		public function handle_action(){

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();
			$dm = $sm->get_dump_manager();

			$action = $sm->execute_action();

			if ($action) {

				is_array($_SESSION['vivvo']) or $_SESSION['vivvo'] = array();
				is_array($_SESSION['vivvo']['save_plus']) or $_SESSION['vivvo']['save_plus'] = array();

				$_SESSION['vivvo']['save_plus']['article_edit'] = array();

				switch ($save_action = $um->get_param('save_action')) {

					case 'return':
						$url = 'articles.php';
					break;

					case 'add':
						$url = CURRENT_ADMIN_VIEW_FILE;
						$_SESSION['vivvo']['save_plus']['article_edit']['category_id'] = $um->get_param('ARTICLE_category_id');
						$_SESSION['vivvo']['save_plus']['article_edit']['author'] = $um->get_param('ARTICLE_author');
						$_SESSION['vivvo']['save_plus']['article_edit']['status'] = $um->get_param('ARTICLE_status');
					break;

					default:
						$save_action = 'continue';
						$url = CURRENT_ADMIN_VIEW_FILE .'?search_id=' . $um->get_param('ARTICLE_id');
					break;
				}

				$_SESSION['vivvo']['save_plus']['article_edit']['save_action'] = $save_action;

				$dm->store_dump(md5($_SERVER['HTTP_HOST'] . VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . $url));

				header('Location: ' . VIVVO_RELATIVE_URL . VIVVO_FS_ADMIN_DIR . $url);
				exit;
			}
		}

		/**
		 * Loads admin css/javascript
		 */
		public function load_admin_header () {
			$header = vivvo_lite_site::get_instance()->get_header_manager();
			$header->add_css(array(
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/tooltips.css',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'css/calendar.css'
			));
			$header->add_script(array(
				VIVVO_URL . 'js/tooltips.js',
				VIVVO_URL . VIVVO_FS_ADMIN_DIR . 'js/functions.js',
				VIVVO_URL . 'js/calendar/calendar_date_select.js',
				VIVVO_URL. VIVVO_FS_ADMIN_DIR . 'js/textboxlist.js',
				VIVVO_URL. VIVVO_FS_ADMIN_DIR . 'js/save_plus.js',
				VIVVO_URL. VIVVO_FS_ADMIN_DIR . 'js/article_edit.js'
			));
		}

		/**
		 * Assigns common template values
		 */
		public function _default_assignments() {

			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();

			if (isset($_SESSION['vivvo'], $_SESSION['vivvo']['save_plus'], $_SESSION['vivvo']['save_plus']['article_edit'])) {

				!empty($_SESSION['vivvo']['save_plus']['article_edit']['save_action']) or $_SESSION['vivvo']['save_plus']['article_edit']['save_action'] = 'continue';

				$template->assign('save_action', $_SESSION['vivvo']['save_plus']['article_edit']['save_action']);

				$um = $sm->get_url_manager();

				$um->set_param('save_action', $_SESSION['vivvo']['save_plus']['article_edit']['save_action']);

				empty($_SESSION['vivvo']['save_plus']['article_edit']['category_id']) or $um->set_param('ARTICLE_category_id', $_SESSION['vivvo']['save_plus']['article_edit']['category_id']);
				empty($_SESSION['vivvo']['save_plus']['article_edit']['author']) or $um->set_param('ARTICLE_author', $_SESSION['vivvo']['save_plus']['article_edit']['author']);
				empty($_SESSION['vivvo']['save_plus']['article_edit']['status']) or $um->set_param('ARTICLE_status', $_SESSION['vivvo']['save_plus']['article_edit']['status']);
			}
		}

		/**
		 * Default view
		 */
		public function _default_view() {

			$sm = vivvo_lite_site::get_instance();
			$template = $sm->get_template();
			$um = $sm->get_url_manager();
			$action = $sm->action_status;

			if (($um->isset_param('action') and $action === false) or ($um->get_param('save_action') == 'add' and !$um->get_param('search_id')) or $um->get_param('ref') == 'quick_post') {
				$in_article = $um->get_param_regexp('/^ARTICLE_/');
				$current_article = new Articles($sm, $in_article);
				$template->assign('article', $current_article);
			} else {
				$sm->set_content();
			}

			$tags_groups_list = new TagsGroups_list($sm);
			$template->assign('topics', $tags_groups_list->get_all_groups());

			$article_config = $sm->get_configuration();
			$tab_list = $article_config->get_configuration_property_list('article_tabs');

			$template->assign('article_tabs', $tab_list);
			$template->set_template_file($this->_template_root . 'frame.xml');

			$time_zone = date('Z', VIVVO_START_TIME);
			$template->assign('current_time', date('Y-m-d H:i:00', time()));

			class_exists('TagsGroups') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php';

			$topic = TagsGroups_list::factory()->get_group_by_id(0);
			$template->assign('default_topic', $topic);

			return $template;
		}

		/**
		 * Frontend admin view
		 */
		public function _frontend_edit() {
			return $this->load_template($this->_template_root . 'frontend_options.xml');
		}

		/**
		 * Revision list view
		 */
		public function _revisions() {

			$template = $this->load_template($this->_template_root . 'revisions.xml');

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			if ($um->isset_param('article_id') and ($article_id = $um->get_param('article_id'))) {

				class_exists('ArticlesRevisions') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesRevisions.class.php';

				$revision = ArticlesRevisions::save_auto_draft(
					$article_id,
					$um->get_param('title'),
					$um->get_param('summary'),
					$um->get_param('body'),
					date('Y-m-d H:i:s'),
					$sm->user->get_id()
				);

				if ($revision) {
					$article = $revision->get_article();
				}
			}

			if (empty($article)) {
				$article = new stdClass;
				$article->id = $um->get_param('article_id');
			}

			$template->assign('article', $article);

			return $template;
		}

		/**
		 * Tags feed view (autocomplete items)
		 */
		public function _tags_feed() {

			$results = array('total' => 0, 'items' => array());

			$sm = vivvo_lite_site::get_instance();
			$um = $sm->get_url_manager();

			if ($um->isset_param('q')) {

				$name = strtr(secure_sql($um->get_param('q')), array('%' => '\\%', '_' => '\\_'));

				if ($um->isset_param('restrict')) {
					$topic_id = ' AND topic_id = ' . (int)($um->get_param('restrict'));
				} else {
					$topic_id = '';
				}

				if ($um->isset_param('offset')) {
					$offset = (int)($um->get_param('offset'));
				} else {
					$offset = 0;
				}

				if ($um->isset_param('limit')) {
					$limit = (int)($um->get_param('limit'));
				} else {
					$limit = 30;
				}

				$res = $sm->get_db()->query(
					"SELECT CONCAT_WS(':', ttg.tags_group_id, ttg.tag_id) AS value, t.name AS caption, tg.name AS category
					 FROM " . VIVVO_DB_PREFIX . 'tags_to_tags_groups AS ttg
					 LEFT JOIN ' . VIVVO_DB_PREFIX . 'tags AS t ON ttg.tag_id = t.id
					 LEFT JOIN ' . VIVVO_DB_PREFIX . "tags_groups AS tg ON ttg.tags_group_id = tg.id
					 WHERE t.name LIKE '%$name%'$topic_id
					 LIMIT $offset,$limit"
				);

				if (!PEAR::isError($res)) {
					$results['items'] = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
					$res->free();
					$results['total'] = count($results['items']);
				}
			}

			header('Content-Type: application/json');
			echo json_encode($results);
			exit;
		}
	}
?>