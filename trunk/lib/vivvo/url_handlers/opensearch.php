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

function opensearch_url_handler(&$sm, $url_array) {

	defined('VIVVO_THEME') or define('VIVVO_THEME', VIVVO_URL . 'themes/' . VIVVO_DEFAULT_THEME . '/');

	header("Content-type: text/xml; charset=UTF-8");
	$output = '<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
	<ShortName>' . VIVVO_WEBSITE_TITLE . '</ShortName>
	<Description>Use ' . VIVVO_WEBSITE_TITLE . ' to search the Web.</Description>
	<Contact>' . VIVVO_ADMINISTRATORS_EMAIL . '</Contact>
	<Url type="text/html" template="' . VIVVO_URL . 'search.html?search_do_advanced&amp;search_query={searchTerms}&amp;pg={startPage?}" />
	<Url type="application/atom+xml" template="' . VIVVO_URL . 'feed.php?search_do_advanced&amp;search_query={searchTerms}&amp;pg={startPage?}&amp;output_type=atom" />
	<Url type="application/rss+xml" template="' . VIVVO_URL . 'feed.php?search_do_advanced&amp;search_query={searchTerms}&amp;pg={startPage?}&amp;output_type=rss" />
	<LongName>' . VIVVO_WEBSITE_TITLE . ' Search</LongName>
	<Image height="64" width="64" type="image/png">' . VIVVO_THEME . 'img/logo.png</Image>
	<Image height="16" width="16" type="image/vnd.microsoft.icon">' . VIVVO_THEME . 'img/favicon.ico</Image>
	<Query role="example" searchTerms="cat" />
	<Developer></Developer>
	<Attribution>
		Search data Copyright ' . date('Y') . ', ' . VIVVO_WEBSITE_TITLE . ' , All Rights Reserved
	</Attribution>
	<SyndicationRight>open</SyndicationRight>
	<AdultContent>false</AdultContent>
	<Language>en-us</Language>
	<OutputEncoding>UTF-8</OutputEncoding>
	<InputEncoding>UTF-8</InputEncoding>
</OpenSearchDescription>';
	echo $output;
	exit;
}
?>