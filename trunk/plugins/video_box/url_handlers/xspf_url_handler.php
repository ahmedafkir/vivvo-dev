<?php
/* =============================================================================
 * $Revision: 4834 $
 * $Date: 2010-03-30 11:39:23 +0200 (Tue, 30 Mar 2010) $
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
	 * XSPF URL handler
	 *
	 * @author	Slobodan Utvic <utvara@spoonlabs.com>
	 * @author	Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 *
	 * @url				/xspf/{search_url}
	 * @description		Custom article search URL
	 * @template		_syndication/xspf.tpl
	 * @assignments		{content_params}
	 */

	/**
	 * Parses url array
	 *
	 * @param	&vivvo_lite_site	$sm
	 * @param	array				$url_array
	 * @return	array
	 */

	function xspf_url_handler(&$sm, $url_array) {

		$um = $sm->get_url_manager();
		array_shift($um->_url_array);
		$um->parse_url();

		$template = $sm->get_template();
		$template->set_template_file(VIVVO_FS_INSTALL_ROOT . 'templates/_syndication/xspf.tpl');

		defined('VIVVO_CUSTOM_FIELD_SEARCH') or define('VIVVO_CUSTOM_FIELD_SEARCH', 1);

		$sm->define_proxy_url();

		$sm->article_list_params['search_field_video_attachment_neq'] = '';
		$sm->article_list_params['search_field_video_attachment_notnull'] = 1;

		$template->assign('content_params', $sm->article_list_params);

		header('Content-type: application/xspf+xml');

		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo $sm->get_output();

		ob_end_flush();

		exit;
	}
?>