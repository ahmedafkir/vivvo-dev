<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<body id="layout_default">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
			<div id="content">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/ticker.tpl" />
				<div id="dynamic_box_right">
					<div id="box_right_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}{VIVVO_LOGIN_TEMPLATE}" nocache="1" /> 
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/most_popular_email.tpl" /> 
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/top_rated.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/tag_cloud.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/comment_tabs.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/featured_author.tpl" /> 
					</div>
				</div>
					<vte:include file="{VIVVO_TEMPLATE_DIR}box/rotating_headlines.tpl" />
				<div id="dynamic_box_left">
					<div id="box_left_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/sections.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_newsletter.tpl" nocache="1" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_poll.tpl" nocache="1" />
					</div>
				</div>
				<div id="dynamic_box_center">
					<div id="box_center_holder_home">
						<div id="content_features" class="features_equal_default">
                            <vte:include file="{VIVVO_TEMPLATE_DIR}box/latest_news.tpl" />
                            <vte:box module="box_sections">
                                <vte:params>
                                    <vte:param name="cache" value="1" />
                                    <vte:param name="search_ids" value="{VIVVO_MODULES_MORE_NEWS_CATEGORIES}" />
                                </vte:params>
                                <vte:template>
                                    <div id="category_news_box" >
                                        <div class="main_news_box_holder">
                                            <vte:for from="{categories}" step="2" key="category_index">
                                                <div class="main_news_box_row">
                                                    <vte:foreach item = "category" from = "{categories}" loop="2" start="{category_index}">
                                                        <div id="box_more_category_list_{category.get_id}" class="main_news_category">
                                                            <div class="box_title">
                                                                <h4>
                                                                    <a href="{category.get_href}"><vte:value select="{category.get_category_name}" /></a>
                                                                </h4>
                                                                <vte:foreach item = "sub_category" from = "{category.subcategories}" key="index">
                                                                    <span>
                                                                        <vte:if test="{index} != 1"> | </vte:if>
                                                                        <a href="{sub_category.get_href}"><vte:value select="{sub_category.get_category_name}" /></a>
                                                                    </span>
                                                                </vte:foreach>
                                                            </div>
                                                            <div class="box_body">
                                                                <vte:box module="box_article_list">
                                                                    <vte:params>
                                                                        <vte:param name="search_sort_by" value="created" />
                                                                        <vte:param name="search_limit" value="{VIVVO_MODULES_MORE_NEWS_ARTICLE_NUMBER}" />
                                                                        <vte:param name="search_cid" value="{category.id}" />
                                                                        <vte:param name="add_to_printed" value="true" />
                                                                        <vte:param name="exclude_printed" value="true" />
                                                                    </vte:params>
                                                                    <vte:template>
                                                                        <vte:foreach item = "article" from = "{article_list}" start="1" loop="1">
                                                                            <vte:include file="{VIVVO_TEMPLATE_DIR}summary/morenews_short.tpl" />
                                                                        </vte:foreach>
                                                                        <ul>
                                                                            <vte:foreach item = "article" from = "{article_list}" start="2">
                                                                                <li><a href="{article.get_href}"><vte:value select="{article.title}" /><vte:if test="{article.get_link}"> <img src="{VIVVO_THEME}img/external.png" alt="{LNG_VISIT_WEBSITE}"/></vte:if></a></li>
                                                                            </vte:foreach>
                                                                        </ul>
                                                                    </vte:template>
                                                                </vte:box>
                                                            </div>
                                                        </div>
                                                    </vte:foreach>
                                                </div>
                                            </vte:for>
                                        </div>
                                    </div>
                                </vte:template>
                            </vte:box>
                        </div>
					</div>
				</div>
			</div>
			<div id="footer">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/footer.tpl" />
			</div>
		</div>	
	</body>
</html>