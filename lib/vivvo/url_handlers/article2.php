<?php
/* =============================================================================
 * $Revision: 5137 $
 * $Date: 2010-04-26 11:29:28 +0200 (Mon, 26 Apr 2010) $
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

function article2_url_handler(&$sm, $url_array) {

	$um = $sm->get_url_manager();
	$params = array('url_module' => 'article');
	$last_chunk = end($url_array);

	if (preg_match('/^(\d+)/',$last_chunk, $article_url)) {
		if (isset($article_url[1])) {
			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Articles.class.php');
			$article = Articles_list::factory()->get_article_by_id($article_url[1], $um->list['search_cid']);
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

function format_article_url2($article) {
	return make_proxied_url('permalink/' . $article->id . '.html');
}
?>