<vte:if test="{VIVVO_MODULES_MOST_COMMENTED}">
	<vte:box module="box_article_list">
		<vte:params>
			<vte:param name="cache" value="1" />
			<vte:param name="search_sort_by" value="most_commented" />
			<vte:param name="search_limit" value="5" />
			<vte:param name="fields" value="title,SEfriendly,category_id" />
		</vte:params>
		<vte:template>
			<div id="box_most_commented" class="box">
				<div class="box_title_holder"><div class="box_title"><vte:value select="{LNG_MOST_COMMENTED}" /></div></div>
				<div class="box_body">
					<div class="box_content">
						<ul>
							<vte:foreach item="article"  key="index" from="{article_list}">
								<vte:if test="{index} = 1">
									<li class="first_article">
										<vte:if test="{article.image}">
											<img src="{VIVVO_URL}thumbnail.php?file={article.image}&amp;size=summary_small" alt="image" />
										</vte:if>
										<a href="{article.get_href}">
											<vte:value select="{article.get_title}" />
										</a>
									</li>
									<vte:else>
										<li>
											<a href="{article.get_href}">
												<vte:value select="{article.get_title}" />
											</a>
										</li>
									</vte:else>
								</vte:if>
							</vte:foreach>
						</ul>
					</div>
				</div>
			</div>
		</vte:template>
	</vte:box>
</vte:if>