<?php
/* =============================================================================
 * $Revision: 4902 $
 * $Date: 2010-04-07 14:28:50 +0200 (Wed, 07 Apr 2010) $
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

function topic_url_handler(&$sm, $url_array) {

	$um = $sm->get_url_manager();
	$params = array();

	require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php');

	$tags_groups_list = new TagsGroups_list($sm);

	$topic_url = urldecode($params['url_module'] = $url_array[0]);
	$label_url = false;

	if (($count = count($url_array)) > 1) {

		$label_url = urldecode($url_array[1]);

		if (preg_match('/index\.(\d+)\.html/', $label_url, $matches)) {

			$label_url = false;
			$pg = +$matches[1];

		} elseif ($count == 3) {

			if (preg_match('/index\.(\d+)\.html/', $url_array[2], $matches)) {
				$pg = +$matches[1];
			} else {
				go_404();
			}

		} elseif ($count != 2) {
			go_404();
		}

	} elseif (!$count) {
		go_404();
	}

	if ($topic = $tags_groups_list->get_group_by_url($topic_url)) {

		$params['search_tid'] = (int)$topic->get_id();

		if ($params['search_tid'] == 1) {	// 'System' topic
			go_404();
		}

		if ($label_url) {

			require_once(VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');

			$tags_list = new Tags_list($sm);

			if ($tag = $tags_list->get_tag_by_sefriendly($label_url)) {
				$params['search_lid'] = (int)$tag->get_id();
			} else {
				go_404();
			}
		}

		if ($pg) {
			$params['pg'] = $pg;
		}

		return $params;
	}

	go_404();
}

function topic_content_handler(&$sm) {

	$template = $sm->get_template();
	$header = $sm->get_header_manager();
	$um = $sm->get_url_manager();

	if (!$um->isset_param('search_tid')) {
		go_404();
	}

	require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/TagsGroups.class.php');

    $tags_groups_list = new TagsGroups_list($sm);
    $tags_group = $tags_groups_list->get_group_by_id($um->get_param('search_tid'));

    if (!$tags_group) {
        go_404();
    }

    $tag = false;

    if ($um->isset_param('search_lid')) {

        require_once (VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/core/Tags.class.php');

        $tags_list = new Tags_list($sm);
        $tag = $tags_list->get_tag_by_id($um->get_param('search_lid'));

        if (!$tag) {
            go_404();
        }

		$tag->set_group_url($tags_group->get_href());
		$tag->set_group_name($tags_group->get_name());
    }

    $template->assign('topic', $tags_group);
	$template->assign('tag', $tag);
	$template->assign('label', $tag);	// @deprecated

	if ($um->isset_param('pg')) {
		$template->assign('pg', $um->get_param('pg'));
	}

    if ($tag) {
        $dir = 'label/';
        $file = $tags_group->get_tag_template();
    } else {
        $dir = 'topic/';
        $file = $tags_group->get_template();
    }

    $base = VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR;

    if (!is_file($tpl = $base . $dir . $file)) {
        $tpl = $base . $dir . 'default.tpl';
    }

    $template->set_template_file($tpl);
}
?>