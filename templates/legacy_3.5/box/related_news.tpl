<vte:if test="{VIVVO_ARTICLE_SHOW_RELATED}">
	<vte:box module="box_article_list">
		<vte:params>
			<vte:param name="search_sort_by" value="relevance" />
			<vte:param name="search_limit" value="5" />
			<vte:param name="search_related" value="{article.get_id}" />
			<vte:param name="fields" value="title,SEfriendly,category_id" />
		</vte:params>
		<vte:template>
			<vte:if test="{article_list}">
				<div id="box_related_news" class="box">
					<div class="box_title_holder"><div class="box_title"><vte:value select="{LNG_RELATED_NEWS}" /></div></div>
					<div class="box_body">
						<div class="box_content">
							<ul>
								<vte:foreach item = "article" from = "{article_list}">
									<li>
										<a href="{article.get_href}">
											<vte:value select="{article.get_title}" />
										</a>
									</li>
								</vte:foreach>
							</ul>
						</div>
					</div>
				</div>
			</vte:if>
		</vte:template>
	</vte:box>
</vte:if>