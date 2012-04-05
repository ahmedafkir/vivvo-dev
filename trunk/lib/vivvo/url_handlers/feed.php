<?php
/* =============================================================================
 * $Revision: 6846 $
 * $Date: 2011-05-11 11:10:09 +0200 (Wed, 11 May 2011) $
 *
 * Vivvo CMS v4.6 (build 6082)
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

function format_time_zone($zone){
	if ($zone > 0){
		$hour = str_pad(strval(floor($zone / 60)), 2, "0", STR_PAD_LEFT);
		$minute = $zone % 60;
		return $hour . ':' . $minute;
	}else{
		$zone = - $zone;
		$hour = str_pad(strval(floor($zone / 60)), 2, "0", STR_PAD_LEFT);
		$minute = $zone % 60;
		return '-' . $hour . ':' . $minute;
	}
}

function format_date_atom($date){
	return date('Y-m-d\TH:i:s', strtotime($date)) . substr(date('O',strtotime($date)),0,3) . ':' . substr(date('O',strtotime($date)),3,2);
}

function xmlentities ( $string ){
	return str_replace ( array ('&', '<', '>', '"'), array ('&amp;', '&lt;', '&gt;', '&#34;' ), $string );
}



function feed_url_handler(&$sm, $url_array){

	if (!defined('VIVVO_PROXY_URL')){
		if (defined('VIVVO_SYSTEM_PROXY_URL')) {
			define('VIVVO_PROXY_URL', VIVVO_SYSTEM_PROXY_URL);
		} else {
			define('VIVVO_PROXY_URL', '');
		}
	}

	defined('VIVVO_ABSOLUTE_URL') or define('VIVVO_ABSOLUTE_URL', make_absolute_url(''));

	$template = $sm->get_template();

	$um = $sm->get_url_manager();
	array_shift($um->_url_array);

	if ($_GET['output_type'] == 'sitemap'){
		define ('VIVVO_SKIP_URL_PARSING', true);
	}
	$um->parse_url();

	$template->assign('CURRENT_TIME', date('Y-m-d H:i:s', VIVVO_START_TIME));

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
	if (empty($sm->article_list_params)){
		$sm->article_list_params = Articles_list::get_search_params_from_url($sm);
	}

	if (!$um->get_param('output_type')){
		$um->set_param('output_type', 'rss');
	}

	if ($um->get_param('output_type') == 'atom'){
		if (VIVVO_MODULES_FEED == 1){
			if (VIVVO_MODULES_CATEGORY_INCLUDE_FEED){
				if (empty($sm->article_list_params['search_options']['search_cid'])){
					$sm->article_list_params['search_options']['search_cid'] = VIVVO_MODULES_CATEGORY_INCLUDE_FEED;
				}else{
					$search_cid = explode(',', $sm->article_list_params['search_options']['search_cid']);
					$restrict_cid = explode(',', VIVVO_MODULES_CATEGORY_INCLUDE_FEED);
					$search_cid = array_intersect($search_cid, $restrict_cid);
					if (!empty($search_cid)){
						$sm->article_list_params['search_options']['search_cid'] = implode(',', $search_cid);
					}else{
						$sm->article_list_params['search_options']['search_cid'] = '-1';
					}
				}
			}
			$sm->article_list_params['search_options']['search_rss_feed'] = '1';

			header("Content-type: text/xml; charset=UTF-8");

			if (isset($sm->article_list_params['search_options']['search_id']) && ($sm->article_list_params['search_options']['search_id'] != '')){
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/atom1.0_comments.tpl');
			}else{
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/atom1.0.tpl');
			}

		}else{
			go_404();
		}
	}elseif ($um->get_param('output_type') == 'rss'){

		if (VIVVO_MODULES_FEED == 1){
			if (VIVVO_MODULES_CATEGORY_INCLUDE_FEED){
				if (empty($sm->article_list_params['search_options']['search_cid'])){
					$sm->article_list_params['search_options']['search_cid'] = VIVVO_MODULES_CATEGORY_INCLUDE_FEED;
				}else{
					$search_cid = explode(',', $sm->article_list_params['search_options']['search_cid']);
					$restrict_cid = explode(',', VIVVO_MODULES_CATEGORY_INCLUDE_FEED);
					$search_cid = array_intersect($search_cid, $restrict_cid);

					if (!empty($search_cid)){
						$sm->article_list_params['search_options']['search_cid'] = implode(',', $search_cid);
					}else{
						$sm->article_list_params['search_options']['search_cid'] = '-1';
					}
				}
			}

			$sm->article_list_params['search_options']['search_rss_feed'] = '1';

			header("Content-type: text/xml; charset=UTF-8");

			if (isset($sm->article_list_params['search_options']['search_id']) && ($sm->article_list_params['search_options']['search_id'] != '')){
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/rss2.0_comments.tpl');
			}else{
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/rss2.0.tpl');
			}

		}else{
			go_404();
		}
	}elseif ($um->get_param('output_type') == 'txt'){
		if (VIVVO_MODULES_PLAINTEXT == 1){
			header("Content-type: text/plain; charset=UTF-8");
			if ($um->get_param('search_id')){
				$sm->set_content();
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/article_text.tpl');
			}else{
				$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/text.tpl');
			}
		}else{
			go_404();
		}
	}elseif ($um->get_param('output_type') == 'sitemap'){
		header("Content-type: text/xml; charset=UTF-8");
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		$template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . 'templates/_syndication/google_sitemap.tpl');
	}else{
		go_404();
	}
	$template->assign('content_params', $sm->article_list_params);

	echo $sm->get_output();
	exit;
}

?>