<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_two_column">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
            <div class="page_top"> </div>
			<div id="content">
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<vte:box module="box_article_list" params = "{content_params}">
							<vte:template>
								<vte:if test="{PAGE_TITLE}">
									<h1 class="page_title">
										<vte:value select="{PAGE_TITLE}" />
                                        <vte:if test="{VIVVO_MODULES_FEED}">
                                            <a href="{CURRENT_URL}&amp;type=rss"><img src="{VIVVO_THEME}img/icon_feed.gif" alt="{PAGE_TITLE}" title="{PAGE_TITLE}" /></a>
                                        </vte:if>
									</h1>
								</vte:if>
								<vte:load module="box_pagination" list="{article_list_object}" />
                                <vte:if test="{article_list}">
                                    <vte:foreach item = "article" from = "{article_list}">
                                        <vte:include file="{VIVVO_TEMPLATE_DIR}summary/default.tpl" />
                                    </vte:foreach>
                                    <vte:else>
                                        <h3 class="box_title title_white"><vte:value select="{LNG_NO_ENTRIES}" /></h3>
                                        <h5 class="subtitle"><vte:value select="{LNG_SEARCH_REFINE}" />:</h5>
                                        <vte:include file="{VIVVO_TEMPLATE_DIR}system/advanced_search.tpl" />
                                    </vte:else>
                                </vte:if>	
								<vte:load module="box_pagination" list="{article_list_object}" />
                                <vte:if test="{article_list_object.get_total_count} &gt; 100">
                                	<h5 class="subtitle"><vte:value select="{LNG_SEARCH_NARROW}" /></h5>
                                    <vte:include file="{VIVVO_TEMPLATE_DIR}system/advanced_search.tpl" />
                                </vte:if>
							</vte:template>
						</vte:box>
					</div>
				</div>
				<div id="dynamic_box_right">
					<div id="box_right_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}{VIVVO_LOGIN_TEMPLATE}" nocache="1" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/popular_box.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/featured_author.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/tag_cloud.tpl" />
					</div>
				</div>
			</div>
			<div id="footer">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/footer.tpl" />
			</div>
		</div>	
	</body>
</html>