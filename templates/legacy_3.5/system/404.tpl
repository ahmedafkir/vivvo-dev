<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG}" xml:lang="{VIVVO_LANG}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_default">
		<div id="container">
			<div id="content" style="text-align:center;">
				<div style="padding:12px;">
					<h1 style="font-size:36px;"><vte:value select="{LNG_404_NOT_FOUND}" /></h1>
					<div style="font-size:14px; padding:4px;"><vte:value select="{LNG_404_NOT_FOUND_INFO}" /></div>
                    <div style="font-size:14px; padding:4px;"><vte:value select="{LNG_404_NOT_FOUND_NOTIFY}" /> <strong><a href="javascript:report404('{URL}');"><vte:value select="{LNG_404_NOT_FOUND_NOTIFY_LINK}" /></a></strong></div>
					<div id="report_404" style="font-size:14px; padding:4px; color:#6b99c5; font-weight:bold;"> </div>
                    <strong><a href="{VIVVO_URL}"><vte:value select="{LNG_404_GO_HOME}" /></a></strong>
				</div>
				<div id="box_search" class="search" style="text-align:center; position:static;">
					<form action="{VIVVO_PROXY_URL}search.html" method="post" name="search">
						<input type="hidden" name="search_do_advanced" />
						<input value="" class="text" type="text" name="search_query" id="search_query" />
						<button type="submit" name="search" value="0"><vte:value select="{LNG_SEARCH_BUTTON}" /></button> | 
						<a href="{VIVVO_PROXY_URL}search.html"> <vte:value select="{LNG_SEARCH_ADVANCED}" /></a>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			function report404(url){
				commentParam = {};
				commentParam.action = 'user';
				commentParam.cmd = 'report404';
				commentParam.url = url;
				commentParam.template_output = 'box/dump';
										
				new Ajax.Updater('report_404', '<vte:value select="{VIVVO_URL}" />index.php', {
					parameters: commentParam
				});
			}
		</script>
	</body>
</html>
		