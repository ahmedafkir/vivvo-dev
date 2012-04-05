<vte:template>
	<vte:header type="script" href="{VIVVO_URL}js/framework/effects.js" />
	<vte:header type="script" href="{VIVVO_URL}js/glider.js" />
	<vte:header type="script" href="{VIVVO_URL}js/ticker.js" />
	<vte:header type="css" href="{VIVVO_THEME}css/ticker.css" />
	<div id="{BOX_ID}_mainTicker" class="ticker">
		<div style="overflow:hidden;" class="scroller">
			<vte:foreach item = "article" from = "{article_list}">
				<div id="{BOX_ID}_ticker_{article.id}" class="section">
					<span class="ticker_category"><a href=""><vte:value select="{article.get_category_name}" /></a> | </span>
					<a href="{article.get_href}"><vte:value select="{article.get_title}" /></a>
				</div>
			</vte:foreach>
		</div>
	</div>
	<script type="text/javascript">
		var mainTicker = new vivvoTicker('<vte:value select="{BOX_ID}" />_mainTicker');
	</script>
</vte:template>