<vte:box module="box_article_list">
	<vte:params>
		<vte:param name="search_sort_by" value="{VIVVO_HOMEPAGE_ARTICLE_LIST_ORDER}" />
		<vte:param name="search_order" value="descending" />
		<vte:param name="search_cid" value="{VIVVO_HOMEPAGE_ARTICLE_LIST_CATEGORIES}" />
		<vte:param name="search_limit" value="{VIVVO_HOMEPAGE_ARTICLE_LIST_NUMBER}" />
		<vte:param name="add_to_printed" value="true" />
		<vte:param name="exclude_printed" value="true" />
	</vte:params>
	<vte:template>
		<div>
			<div class="box_body">
				<vte:if test="{VIVVO_HOMEPAGE_ARTICLE_LIST_COLUMNS} = 1">
					<vte:foreach item = "article" from = "{article_list}">
						<vte:include file="{VIVVO_TEMPLATE_DIR}summary/default.tpl" />
					</vte:foreach>
					<vte:else>
						<vte:for from = "{article_list}" key="latest_key" step="{VIVVO_HOMEPAGE_ARTICLE_LIST_COLUMNS}">
							<div style="overflow:hidden;width:100%;">
								<vte:foreach item = "article" from = "{article_list}" start="{latest_key}" loop="{VIVVO_HOMEPAGE_ARTICLE_LIST_COLUMNS}">
									<div>
										<vte:attribute name="style">
											<vte:if test="{VIVVO_HOMEPAGE_ARTICLE_LIST_COLUMNS} = 3">
												width:33.33%;float:left;
												<vte:else>
													width:50%;float:left;
												</vte:else>
											</vte:if>
										</vte:attribute>
										<vte:include file="{VIVVO_TEMPLATE_DIR}summary/default.tpl" />
									</div>
								</vte:foreach>
							</div>
						</vte:for>
					</vte:else>
				</vte:if>					
			</div>
		</div>
	</vte:template>
</vte:box>