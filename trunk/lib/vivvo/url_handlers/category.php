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

function category_url_handler(&$sm, $url_array){
	$um =& $sm->get_url_manager();
	$params = array('url_module' => 'category');

	$url_count = count ($url_array);
	$last_chunk = end($url_array);

	if ($url_count == 1){
		//first level cat
		$categories =& $sm->get_categories();
		$current_cat =& $categories->root_category;

		$found_cat = false;

		$sub_cat_keys = array_keys($current_cat->subcategories);
		$sub_cat_count = count ($sub_cat_keys);

		$last_chunk = urldecode($last_chunk); // @+++

		for ($j=0; $j < $sub_cat_count; $j++){

			if (($current_cat->subcategories[$sub_cat_keys[$j]]->sefriendly == $last_chunk)
				|| (strtolower(str_replace(' ','-', $current_cat->subcategories[$sub_cat_keys[$j]]->category_name)) == strtolower($last_chunk))
				|| ($current_cat->subcategories[$sub_cat_keys[$j]]->id == $last_chunk))
			{
				$params['search_cid'] = $current_cat->subcategories[$sub_cat_keys[$j]]->id;
				$found_cat = true;
				break;
			}
		}

		if (!$found_cat) {
			return false;
		}
	}else{
		//category pagination
		$pg = false;
		if (preg_match('/^index\.(\d+)?.*'. $um->list['output_type'].'?$/i',$last_chunk, $filename)){
			if (!empty($filename[1])){
				$pg = $filename[1];
			}
			array_pop($url_array);
		}
		$last_chunk = end($url_array);

		//article URL
		if (!empty($um->list['output_type'])){
			if (preg_match('/([\w\-]+)\.'. $um->list['output_type'].'?$/i',$last_chunk, $article_url)){
				array_pop($url_array);
			}
		}

		//category
		$categories =& $sm->get_categories();
		$current_cat =& $categories->root_category;

		$url_count = count ($url_array);
		$found_cat = false;

		for ($i=0; $i < ($url_count); $i++){
			if (!empty($current_cat->subcategories)){
				$sub_cat_keys = array_keys($current_cat->subcategories);
				$sub_cat_count = count ($sub_cat_keys);

				$sefriendly = urldecode($url_array[$i]);

				for ($j=0; $j < $sub_cat_count; $j++){

					if (($current_cat->subcategories[$sub_cat_keys[$j]]->sefriendly == $sefriendly)
						|| (strtolower(str_replace(' ','-', $current_cat->subcategories[$sub_cat_keys[$j]]->category_name)) == strtolower($url_array[$i]))
						|| ($current_cat->subcategories[$sub_cat_keys[$j]]->id == $url_array[$i]))
					{
						if ($url_count - 1 == $i){
							$params['search_cid'] = $current_cat->subcategories[$sub_cat_keys[$j]]->id;

							$found_cat = true;
							break;
						}else{
							$current_cat =& $current_cat->subcategories[$sub_cat_keys[$j]];
							break;
						}
					}
				}
			}
		}
		if ($pg) $params['pg'] = $pg;

		if (!isset($params['search_cid'])){
			return false;
		}
	}

	return $params;
}

function category_content_handler(&$sm){
	$template =& $sm->get_template();

	$header =& $sm->get_header_manager();
	$categories =& $sm->get_categories();

	$um =& $sm->get_url_manager();

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');

	//Article list page
	$cur_page = (int) $um->get_param('pg');
	if (empty($cur_page)) $cur_page=1;

	$sm->article_list_params['search_options'] = array();

	//Category page

	$category =& $categories->get_category($um->get_param('search_cid'));

	if ($category->redirect){
		header ('Location:' . $category->redirect);
		exit();
	}

	if ($category){
		$sm->article_list_params['search_options']['search_cid'] = $um->get_param('search_cid');

		if (VIVVO_MODULES_FEED == 1){
			$header->add_rss(switch_format('rss', CURRENT_URL), $category->category_name);
		}

		define('VIVVO_CURENT_CATEGORY', $um->get_param('search_cid'));
		$template->assign('CURRENT_CATEGORY', $category);

		if ($um->isset_param('force_template') && $sm->user && $sm->user->is_admin() && file_exists(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'category/' . $um->get_param('force_template'))){
			$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'category/' . $um->get_param('force_template'));
		}else{
			$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'category/' . $category->get_template());
		}
		$sm->article_list_params['search_limit'] = $category->article_num;

		$template->assign('PAGE_TITLE', strval($category->get_category_name()));

		$sm->set_theme($category->get_css());
	}else {
		go_404();
	}


	if (empty($sm->article_list_params['search_limit'])) $sm->article_list_params['search_limit'] = 10;

	$sm->article_list_params['pg'] = $cur_page;
	$sm->article_list_params['offset'] = ($cur_page-1) * $sm->article_list_params['search_limit'];

	if ($um->isset_param('search_sort_by')){
		$sm->article_list_params['search_sort_by'] = $um->get_param('search_sort_by');
	}

	if ($um->isset_param('search_order')){
		$sm->article_list_params['search_order'] = $um->get_param('search_order');
	}

	if (empty($sm->article_list_params['search_sort_by'])){
		$sm->article_list_params['search_sort_by'] = 'order_num';
		$sm->article_list_params['search_order'] = 'descending';
	}else{
		if (empty($sm->article_list_params['search_order'])){
			$sm->article_list_params['search_order'] = 'ascending';
		}
	}

	if (!defined('VIVVO_ADMIN_MODE')){
		if (defined('VIVVO_CACHE_ENABLE') && (VIVVO_CACHE_ENABLE == 1) && !isset($sm->article_list_params['cache'])) $sm->article_list_params['cache'] = 1;
	}

	$template->assign('content_params', $sm->article_list_params);
	if (isset($sm->article_list_params['search_options'])){
		$template->assign('CONTENT_PARAMS_OPTIONS', $sm->article_list_params['search_options']);
	}
}
?>