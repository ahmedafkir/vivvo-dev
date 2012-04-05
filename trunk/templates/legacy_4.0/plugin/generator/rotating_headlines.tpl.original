<vte:template>
	<vte:header type="script" href="{VIVVO_URL}js/rotating_headlines.js" />
	<vte:header type="css" href="{VIVVO_THEME}css/rotating_headlines.css" />
	<div id="{BOX_ID}">
		<div id="{BOX_ID}_rotating_headlines" class="box_headline">
			<vte:foreach item = "article" from = "{article_list}" key="index">
				<div class="headline_article">
					<div id="{BOX_ID}_rotating_headlines_{index}" class="headline_article_holder">
						<vte:attribute name="style">
							<vte:if test="{index} != 1">display:none;</vte:if>
						</vte:attribute>
						<vte:if test="{article.image}">
							<div class="headline_image">
								<a href="{article.get_href}"><img src="{VIVVO_URL}thumbnail.php?file={article.get_image}&amp;size={image_size}" align="left" alt="image" /></a>
								<div class="image_caption"><vte:value select="{article.get_image_caption}" /></div>
							</div>
						</vte:if>
						<div id="{BOX_ID}_rotating_headlines_article_{index}" style="height:101px;">
							<h1>
								<a href="{article.get_href}"><vte:value select="{article.get_title}" /></a>
							</h1>
							<div class="headline_body"><vte:value select="{article.get_summary}" /></div>
							<div class="headline_link"><a href="{article.get_href}"><vte:value select="{LNG_FULL_STORY}" /></a></div>
						</div>
					</div>	
				</div>
			</vte:foreach>
			<div class="player">
				<ul id="{BOX_ID}_rotating_headlines_player">
					<vte:foreach item = "article" from = "{article_list}" key="index">
						<li><a href="#{BOX_ID}_rotating_headlines_{index}"><vte:value select="{index}" /></a></li>	
					</vte:foreach>
				</ul>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		var <vte:value select="{BOX_ID}" />_rotating_headlines_tabs = new vivvoRotatingHeadlines('<vte:value select="{BOX_ID}" />_rotating_headlines', 5);
	</script>
</vte:template>