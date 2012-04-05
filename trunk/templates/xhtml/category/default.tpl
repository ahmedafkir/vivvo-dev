<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<vte:header type="keyword" value="{CURRENT_CATEGORY.get_keywords}" />
	<vte:header type="description" value="{CURRENT_CATEGORY.get_description}" />
	<body id="layout_two_column">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
            <div class="page_top"> </div>
			<div id="content">
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/category_breadcrumb.tpl" />
						<vte:box module="box_article_list" params = "{content_params}">
							<vte:template>
                                <vte:if test="{PAGE_TITLE}">
                                    <h1 class="page_title">
                                        <vte:value select="{PAGE_TITLE}" />
                                    </h1>
                                </vte:if>
                                <vte:if test="{article_list}">
                                    <vte:foreach item = "article" from = "{article_list}" start="1" loop="1">
                                        <vte:include file="{VIVVO_TEMPLATE_DIR}summary/vertical.tpl" />
                                    </vte:foreach>
                                    <vte:foreach item = "article" from = "{article_list}" start="2">
                                        <vte:include file="{VIVVO_TEMPLATE_DIR}summary/default.tpl" />
                                    </vte:foreach>
                                    <vte:else>
                                        <h5 class="subtitle"><vte:value select="{LNG_NO_ENTRIES}" /></h5>
                                    </vte:else>
                                    <vte:load module="box_pagination" list="{article_list_object}" />
                                    <vte:include file="{VIVVO_TEMPLATE_DIR}box/subcategory_headlines.tpl" />
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
        <vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
			<script type="text/javascript">_gaq.push(['_trackEvent', 'Category', 'View', '<vte:value select="{category.get_id}" />', 1]);</script>
		</vte:if>
	</body>
</html>