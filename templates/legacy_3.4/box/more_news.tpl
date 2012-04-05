<vte:box module="box_sections">
	<vte:params>
		<vte:param name="search_ids" value="{VIVVO_MODULES_MORE_NEWS_CATEGORIES}" />
	</vte:params>
	<vte:template>
		<div id="more_news">
			<h3 class="more_news_title"><vte:value select="{LNG_MORE_NEWS}" /></h3>
			<div class="more_news_box_body">
				<vte:foreach item = "category" from = "{categories}">
					<vte:if test="{category.category_name}">

						<div id="box_more_category_list_{category.get_id}" class="morenews_category">
							<div class="box_title">
								<h3 class="category_title">
									<vte:if test="{category.image}">
										<img src="{VIVVO_URL}files.php?file={category.get_image}" alt="{category.get_category_name}" />
									</vte:if>
									<a href="{category.get_href}">
										<vte:value select="{category.get_category_name}" />
									</a>
								</h3>
							</div>
							<div class="box_line">
								<!--  -->
							</div>
							<div class="box_body">
								<vte:box module="box_article_list">
									<vte:params>
										<vte:param name="search_sort_by" value="created" />
										<vte:param name="limit" value="{VIVVO_MODULES_MORE_NEWS_ARTICLE_NUMBER}" />
										<vte:param name="search_cid" value="{category.id}" />
										<vte:param name="add_to_printed" value="true" />
										<vte:param name="exclude_printed" value="true" />
									</vte:params>
									<vte:template>
										<vte:foreach item = "article" from = "{article_list}">
											<vte:include file="{VIVVO_TEMPLATE_DIR}summary/morenews_short.tpl" />
										</vte:foreach>
									</vte:template>
								</vte:box>
							</div>
						</div>
					</vte:if>
				</vte:foreach>
			</div>
		</div>
	</vte:template>
</vte:box>	