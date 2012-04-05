<?php
/* =============================================================================
 * $Revision: 5086 $
 * $Date: 2010-04-23 15:00:30 +0200 (Fri, 23 Apr 2010) $
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

function tag_url_handler(&$sm, $url_array){
	$um =& $sm->get_url_manager();
	$params = array('url_module' => 'tag');
	if (VIVVO_MODULES_TAGS == 1){
		array_shift($url_array);
		$last_chunk = end($url_array);
		$url_count = count ($_url_array);

		if (preg_match('/index\.(\d+)?.*'. $um->list['output_type'].'?/i',$last_chunk, $filename)){
			if (!empty($filename[1])){
				$params['pg'] = $filename[1];
			}
			array_pop($url_array);
			$last_chunk = end($url_array);
		}

		if ($last_chunk){
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');
			$tag_list = new Tags_list($sm);
			$tag = $tag_list->get_tag_by_sefriendly(urldecode($last_chunk));

			if ($tag){
				$params['search_tag_id'] = $tag->id;
			}else{
				go_404();
			}
		}else{
			// show tags main page
			$params['show_tags'] = 1;
		}
		return $params;
	}else{
		go_404();
	}
}

function tag_content_handler(&$sm){
	$template = $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
	$header = $sm->get_header_manager();

	$um = $sm->get_url_manager();

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

	$sm->article_list_params = Articles_list::get_search_params_from_url($sm);
	$sm->article_list_params['search_options'] = array();

	if (VIVVO_MODULES_TAGS == 1 && ($um->isset_param('show_tags') || (($um->get_param('search_tag_id') || $um->get_param('search_tag') ) && !$um->get_param('search_do_advanced')))){
		//Tag view page
		if ($um->isset_param('show_tags')){
			$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
			$content_template = new template($sm, $template);
			$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/tags_page.tpl');
			$template->assign_template('PAGE_CONTENT', $content_template);
			$template->assign('PAGE_TITLE', strval($sm->_lang->get_value('LNG_TAGS')));
		}else{
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');

			if ($um->get_param('search_tag_id')){
				$sm->article_list_params['search_options']['search_tag_id'] = $um->get_param('search_tag_id');
				$tag_list =& new Tags_list($sm);
				$tag =& $tag_list->get_tag_by_id($um->get_param('search_tag_id'));
			}else{
				$sm->article_list_params['search_options']['search_tag'] = $um->get_param('search_tag');
				$tag_list =& new Tags_list($sm);
				$tag =& $tag_list->get_tag_by_name($um->get_param('search_tag'));
			}

			if ($tag){

				if (VIVVO_MODULES_FEED == 1){
					$header->add_rss(switch_format('rss', CURRENT_URL), $tag->name);
				}

				$template->assign('PAGE_TITLE',  strval($sm->_lang->get_value('LNG_TAG') . ': ' . $tag->name));
				if ($um->isset_param('force_template') && $sm->user && $sm->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'tag/' . $um->get_param('force_template'))){
					$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'tag/' . $um->get_param('force_template'));
				}else{
					$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'tag/' . VIVVO_TAG_LAYOUT);
				}
			}else{
				go_404();
			}
		}

		if (defined('VIVVO_CACHE_ENABLE') && (VIVVO_CACHE_ENABLE == 1) && !isset($sm->article_list_params['cache'])) $sm->article_list_params['cache'] = 1;

		$template->assign('content_params', $sm->article_list_params);
		$template->assign('CONTENT_PARAMS_OPTIONS', $sm->article_list_params['search_options']);
	}else{
		go_404();
	}
}
?>