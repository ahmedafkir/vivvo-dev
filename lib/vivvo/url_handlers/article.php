<?php
/* =============================================================================
 * $Revision: 5185 $
 * $Date: 2010-04-28 16:43:00 +0200 (Wed, 28 Apr 2010) $
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

function article_url_handler(&$sm, $url_array) {
	$um = $sm->get_url_manager();
	$params = array('url_module' => 'article');
	$last_chunk = end($url_array);

	if (preg_match('/([^\.]+)\.' . $um->list['output_type'] . '?/i', $last_chunk, $article_url)) {
		if (isset($article_url[1])) {
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$article_list = Articles_list::factory();
			if (is_numeric($article_url[1])) {
				$article = $article_list->get_article_by_id($article_url[1], $um->list['search_cid']);
			} else {
				$article = $article_list->get_article_by_sefriendly(urldecode($article_url[1]), $um->list['search_cid']);
			}
			if ($article) {
				$params['search_id'] = $article->id;
			} else {
				return false;
			}
		}
	} else {
		return false;
	}

	return $params;
}

function article_content_handler(&$sm) {

	$template = $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
	$header = $sm->get_header_manager();

	$um = $sm->get_url_manager();

	if ($um->get_param('search_id')) {
		//Article page
		$id = $um->get_param('search_id');

		require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
		$al = Articles_list::factory();
		$sm->article = $al->get_article_by_id($id);
		if ($sm->article !== false) {
			$sm->set_categories();
			$category = $sm->categories->list[$sm->article->get_category_id()];

			if ($um->isset_param('force_template') && $sm->user && $sm->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'article/' . $um->get_param('force_template'))) {
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'article/' . $um->get_param('force_template'));
			}else{
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'article/' . $category->get_article_template());
			}

			// 1. is user and is admin
			// 2. is user and can read article category
			// 3. guest and can read article category

			if (($sm->user && ($sm->user->is_admin() || $sm->user->can('READ', 'Categories', $category->id)))
				|| (!$sm->user && $sm->guest_group && $sm->guest_group->can('READ', 'Categories', $category->id))
			) {
				if (!defined('VIVVO_ADMIN_MODE')) {
					if (($sm->article->status == 0 || strtotime($sm->article->created) > VIVVO_START_TIME) && !($sm->user && $sm->user->can('ACCESS_ADMIN'))) {
						go_404();
					}
					$sm->article->on_display();
				}

				$template->assign('CURRENT_CATEGORY', $category);
				if (isset($sm->article->author_obj)) {
					$template->assign('CURRENT_AUTHOR', $sm->article->author_obj);
				}

				// revision view:
				// 1. both 'news' and 'rev' parameters must be passed trough GET
				// 2. 'news' parameter must be equal to 'search_id' parameter from url_manager
				// 3. user must have EDITOR privilege for this category, or be an author of article (with WRITE privilege)

				if (!empty($_GET['news']) and $_GET['news'] == $sm->article->get_id() and
					!empty($_GET['rev']) and
					$sm->user and ($sm->user->is('EDITOR', 'Categories', $category->get_id()) or
					count($sm->user->get_privilege_object_ids('WRITE', 'Categories')) and
					$sm->user->get_id() == $sm->article->get_user_id())) {

					class_exists('ArticlesRevisions') or require VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/ArticlesRevisions.class.php';

					$revision = ArticlesRevisions_list::factory()->get_revision_by_article_id_ver($sm->article->get_id(), $_GET['rev'] << 0);

					if ($revision) {
						$sm->article->set_title($revision->get_title());
						$sm->article->set_abstract($revision->get_abstract());
						$sm->article->set_body($revision->get_body());
					}
				}

				// end revision view

				// add this article to printed list
				$sm->printed_articles[] = $sm->article->get_id();

				$template->assign('article', $sm->article);
				if (!defined('VIVVO_ADMIN_MODE')) {
					if (VIVVO_MODULES_FEED == 1) {
						$header->add_rss(switch_format('rss', CURRENT_URL), $sm->article->get_title());
						if (VIVVO_FRIENDY_URL) {
							$header->add_rss(switch_format('rss', VIVVO_URL . $category->get_href()), $category->category_name);
						} else {
							$header->add_rss(switch_format('rss', $category->get_href()), $category->category_name);
						}
					}

					$sm->page_title = $sm->article->get_title();
					if ($um->isset_param('print')) {
						$sm->set_theme($category->get_css(), true);
					} else {
						$sm->set_theme($category->get_css());
					}
				}

				$sm->_template->assign('PAGE_TITLE', strval($sm->article->title));
				$um->set_param('search_cid', $category->id);
			} else {
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
				$template->assign('PAGE_TITLE', strval($sm->_lang->get_value('LNG_RESTRICTED_ACCESS')));
				$content_template = new template($sm, $template);
				$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/restricted.tpl');
				$template->assign_template('PAGE_CONTENT', $content_template);
			}
		} else {
			go_404();
		}
	} else {
		go_404();
	}
}
?>