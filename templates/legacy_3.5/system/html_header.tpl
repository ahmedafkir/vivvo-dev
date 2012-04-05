<head>
	<base href="{VIVVO_URL}" />
	<title><vte:value select="{VIVVO_WEBSITE_TITLE}" /><vte:if test="{PAGE_TITLE}"> - <vte:value select="{PAGE_TITLE}" /></vte:if></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="generator" content="Vivvo {VIVVO_VERSION}" />
	<meta name="generation-time" content="$generation_time$" />
	<link rel="search" type="application/opensearchdescription+xml" href="{VIVVO_PROXY_URL}opensearch" title="{VIVVO_WEBSITE_TITLE}" />
	{$meta_names__}
	{$rss__}
	{$css__}
	{$scripts__}
	<vte:if test="{VIVVO_CLOSE_SITE}">
		<vte:header type="script" href="{VIVVO_URL}js/closed_site.js" />
	</vte:if>
	<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
		<script type="text/javascript">var _gaq=[['_setAccount','<vte:value select="{VIVVO_ANALYTICS_TRACKER_ID}" />'],['_trackPageview']];</script>
	</vte:if>
</head>