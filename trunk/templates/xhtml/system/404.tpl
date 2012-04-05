<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_default" class="page_not_found">
		<div id="container">
			<div id="content">
				<div class="page_holder">
					<h1 class="page_title"><vte:value select="{LNG_404_NOT_FOUND}" /></h1>
					<div id="info">
						<div class="page_row" ><vte:value select="{LNG_404_NOT_FOUND_INFO}" /></div>
						<div class="page_row"  ><vte:value select="{LNG_404_NOT_FOUND_NOTIFY}" /> <strong><a href="javascript:report404('{URL}');"><vte:value select="{LNG_404_NOT_FOUND_NOTIFY_LINK}" /></a></strong></div>
					</div>
					<div id="report_404"> </div>
                    <strong><a href="{VIVVO_URL}"><vte:value select="{LNG_404_GO_HOME}" /></a></strong>
				</div>
                <div class="page_top"> </div>
				<div id="box_search" class="search">
					<form action="{VIVVO_PROXY_URL}search.html" method="post" name="search">
						<input type="hidden" name="search_do_advanced" />
						<input value="" class="text default_fields" type="text" name="search_query" id="search_query" />
						<button type="submit" name="search" value="0"><vte:value select="{LNG_SEARCH_BUTTON}" /></button> |
						<a href="{VIVVO_PROXY_URL}search.html"> <vte:value select="{LNG_SEARCH_ADVANCED}" /></a>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			if (!('Prototype' in this)) {
				document.write(unescape("%3Cscript src='<vte:value select="{VIVVO_URL}" />js/framework/prototype.js' type='text/javascript'%3E%3C/script%3E"));
			}
		</script>
		<script type="text/javascript">
			function report404(url) {
				new Ajax.Updater('report_404', '<vte:value select="{VIVVO_URL}" />index.php', {
					parameters: {
						action: 'user',
						cmd: 'report404',
						url: url,
						template_output: 'box/dump'
					},
					onComplete : function(){
						if ($('info')){
							$('info').remove();
						}
					}
				});
			}
		</script>
	</body>
</html>
