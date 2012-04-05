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

function archive_url_handler(&$sm, $url_array){
	$um =& $sm->get_url_manager();
	$params = array('url_module' => 'archive');
	if (VIVVO_MODULES_ARCHIVE_VIEW == 1){
		$params['search_archive'] = 1;
		$last_chunk = end($url_array);
		
		if (preg_match('/index\.(\d+)?.*'. $um->list['output_type'].'?/i',$last_chunk, $filename)){
			if (!empty($filename[1])){
				$params['pg'] = $filename[1];
			}
			array_pop($url_array);
		}
		
		//Year
		if (preg_match('/^\d{4}$/', $url_array[1])){
			$params['search_by_year'] = $url_array[1];
			array_shift($url_array);
			
			//Month
			if (preg_match('/^\d{1,2}$/', $url_array[1])){
				$params['search_by_month'] = $url_array[1];
				array_shift($url_array);
				
				//Day
				if (preg_match('/^\d{1,2}$/', $url_array[1])){
					$params['search_by_day'] = $url_array[1];
				}
			}
		}
		
	}else{
		go_404();
	}		
	return $params;
}

function archive_content_handler(&$sm){
	$template =& $sm->get_template();
	$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
	$header =& $sm->get_header_manager();
	
	$um =& $sm->get_url_manager();

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
	
	if ((VIVVO_MODULES_ARCHIVE_VIEW == 1) && ($um->isset_param('search_archive'))){
		//Archive view page
		if ($um->get_param('search_do_advanced')){
			$sm->article_list_params = Articles_list::get_search_params_from_url($sm);
			$sm->article_list_params['search_options']['search_status'] = -1;
			$sm->article_list_params['search_options']['search_status_limit'] = 0;
			$sm->article_list_params['search_status'] = -1;
			$sm->article_list_params['search_sort_by'] = 'created';
			$sm->article_list_params['search_order'] = 'ascending';
			if ($um->isset_param('force_template') && $sm->user && $sm->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'archive/' . $um->get_param('force_template'))){
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'archive/' . $um->get_param('force_template'));
			}else{
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'archive/' . VIVVO_ARCHIVE_LAYOUT);
			}
			$template->assign('PAGE_TITLE', strval($sm->_lang->get_value('LNG_ARCHIVE')));
		}elseif ($um->get_param('search_by_year')){
			$sm->article_list_params = Articles_list::get_search_params_from_url($sm);	
			$sm->article_list_params['search_options'] = array();
			if ($um->get_param('search_by_year')) $sm->article_list_params['search_options']['search_by_year'] = $um->get_param('search_by_year');
			if ($um->get_param('search_by_month')) $sm->article_list_params['search_options']['search_by_month'] = $um->get_param('search_by_month');
			if ($um->get_param('search_by_day')) $sm->article_list_params['search_options']['search_by_day'] = $um->get_param('search_by_day');
			$sm->article_list_params['search_options']['search_status'] = -1;
			$sm->article_list_params['search_options']['search_status_limit'] = 0;
			$sm->article_list_params['search_status'] = -1;
			$sm->article_list_params['search_sort_by'] = 'created';
			$sm->article_list_params['search_order'] = 'ascending';
			if ($um->isset_param('force_template') && $sm->user && $sm->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'archive/' . $um->get_param('force_template'))){
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'archive/' . $um->get_param('force_template'));
			}else{
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'archive/' . VIVVO_ARCHIVE_LAYOUT);
			}
			$template->assign('PAGE_TITLE', strval($sm->_lang->get_value('LNG_ARCHIVE')));
		}else{
			$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');
			$content_template = new template($sm, $template);
			$content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/archive_search.tpl');
			$template->assign_template('PAGE_CONTENT', $content_template);
			$template->assign('PAGE_TITLE', strval($sm->_lang->get_value('LNG_SEARCH_BUTTON') . ' ' .  strtolower($sm->_lang->get_value('LNG_ARCHIVE'))));
		}		
		if (defined('VIVVO_CACHE_ENABLE') && (VIVVO_CACHE_ENABLE == 1) && !isset($sm->article_list_params['cache'])) $sm->article_list_params['cache'] = 1;
		
		$template->assign('content_params', $sm->article_list_params);
		$template->assign('CONTENT_PARAMS_OPTIONS', $sm->article_list_params['search_options']);												
	}else{
		go_404();
	}
}
?>