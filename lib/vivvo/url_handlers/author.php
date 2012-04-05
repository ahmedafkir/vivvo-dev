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

function author_url_handler(&$sm, $url_array){
	
	$um =& $sm->get_url_manager();
	$params = array('url_module' => 'author');
	if (VIVVO_MODULES_FEATURED_AUTHOR_PAGE == 1 && isset($url_array[1])){
		$last_chunk = end($url_array);
		
		if (preg_match('/index\.(\d+)?.*'. $um->list['output_type'].'?/i',$last_chunk, $filename)){
			if (!empty($filename[1])){
				$params['pg'] = $filename[1];
			}
			array_pop($url_array);
		}

		$user_manager =& $sm->get_user_manager();
		$user_list =& $user_manager->get_user_list($sm);

		if (preg_match('/^\d+$/',$url_array[1])){
			$user_list_a =& $user_list->get_user_by_id($url_array[1]);
		}elseif (preg_match('/([a-zA-Z0-9\_\-]+)/i',$url_array[1], $user_name)){
			$user_list_a =& $user_list->get_user_by_exact_name($user_name[1]);
		}
		
		if ($user_list_a){
			$params['search_user_id'] = $user_list_a->get_id();
			
			//Year
			if (preg_match('/^\d{4}$/', $url_array[2])){
				$params['search_by_year'] = $url_array[2];
				array_shift($url_array);
				
				//Month
				if (preg_match('/^\d{1,2}$/', $url_array[2])){
					$params['search_by_month'] = $url_array[2];
					array_shift($url_array);
				}
			}
		}else{
			go_404();
		}
	}elseif (VIVVO_MODULES_FEATURED_AUTHOR_PAGE == 1 && !isset($url_array[1])){
		// show authors main page
		$params['show_authors'] = 1;
	}else{
		go_404();
	}
	return $params;
}

function author_content_handler(&$sm){
	$template =& $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
	$header =& $sm->get_header_manager();
	
	$um =& $sm->get_url_manager();

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
	$sm->article_list_params = Articles_list::get_search_params_from_url($sm);
	
	$sm->article_list_params['search_options'] = array();
	
	
	if ((VIVVO_MODULES_FEATURED_AUTHOR_PAGE == 1) && ($um->isset_param('show_authors') || ($um->get_param('search_user_id') && !$um->get_param('search_do_advanced')))){
		if ($um->isset_param('show_authors')){
			$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
			$content_template = new template($sm, $template);
			$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/authors_page.tpl');
			$template->assign_template('PAGE_CONTENT', $content_template);
			$template->assign('PAGE_TITLE', strval($sm->_lang->get_value('LNG_AUTHORS')));
		}else{
			$user_manager =& $sm->get_user_manager();
			$user_list =& $user_manager->get_user_list($sm);
			$user_list_a =& $user_list->get_user_by_id($um->get_param('search_user_id'));
			
			if($user_list_a){
				$sm->article_list_params['search_options']['search_user_id'] = $user_list_a->get_id();
				if ($um->get_param('search_by_year')) $sm->article_list_params['search_options']['search_by_year'] = $um->get_param('search_by_year');
				if ($um->get_param('search_by_month')) $sm->article_list_params['search_options']['search_by_month'] = $um->get_param('search_by_month');
				
				if ($um->get_param('search_by_year')) {
					$sm->article_list_params['search_limit'] = 200;
				}
				
				
				$template->assign('CURRENT_AUTHOR', $user_list_a);
				$template->assign('PAGE_TITLE', strval($user_list_a->get_name()));
				if ($um->isset_param('force_template') && $sm->user && $sm->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'author/' . $um->get_param('force_template'))){
					$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'author/' . $um->get_param('force_template'));
				}else{
					$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'author/default.tpl');
				}
				if (VIVVO_MODULES_FEED == 1){
					$header->add_rss(switch_format('rss', CURRENT_URL), $user_list_a->get_name());
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