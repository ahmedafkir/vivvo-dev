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


	require_once (dirname(__FILE__) . '/../admin_include.php');
	$sm->set_theme();
	header("content-type: application/x-javascript");
	echo 'tinyMCE.init({
	mode: "exact",
	elements: "article_body",
	theme: "advanced",
	plugins: "advimage,filepicker,media,inlinepopups,fullscreen",
	theme_advanced_buttons1: "bold,italic,underline,striketrough,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,styleselect,formatselect,fontselect,fontsizeselect,separator,removeformat,cleanup",
	theme_advanced_buttons2: "cut,copy,paste,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,link,unlink,anchor,separator,forecolor,backcolor,separator,image,filepicker,media,separator,code,separator,fullscreen",
	theme_advanced_buttons3: "",
	theme_advanced_toolbar_location: "top",
	theme_advanced_toolbar_align: "left",
	theme_advanced_path_location: "bottom",
	plugin_insertdate_dateFormat: "%Y-%m-%d",
	plugin_insertdate_timeFormat: "%H:%M:%S",
	theme_advanced_resize_horizontal: false,
	theme_advanced_resizing: true,
	extended_valid_elements: "a[name|href|target|title|onclick|rel],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|obj|param|embed|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style],object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width|height|src|*]",
	content_css: "' . VIVVO_THEME . 'css/article_styles.css",
	theme_advanced_styles: "Highlight right=quote_right;Highlight left=quote_left;Image right=right_image;Image left=left_image",
	relative_urls: false, // Default value
	document_base_url: "'. VIVVO_URL . '",
	convert_urls: false,
	fullscreen_new_window: false,
	fullscreen_settings: {theme_advanced_path_location : "top"},
	setup: function(ed) {
		"adminArticle" in window && ed.onChange.add(adminArticle.onContentChange.bind(adminArticle));
	}
});';

?>