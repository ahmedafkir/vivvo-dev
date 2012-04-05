<html xmlns="http://www.w3.org/1999/xhtml" lang="{VIVVO_LANG_CODE}" xml:lang="{VIVVO_LANG_CODE}">
	<vte:include file="{VIVVO_TEMPLATE_DIR}system/html_header.tpl" />
	<vte:header type="css" href="{VIVVO_THEME}css/article_styles.css" />
	<vte:header type="css" href="{VIVVO_THEME}css/print.css" media="print" />
	<vte:header type="keyword" value="{article.get_keywords}" />
	<vte:header type="description" value="{article.get_description}" />
	<body id="layout_default">
		<div id="container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}box/header.tpl" />
			<div id="content">
            <vte:include file="{VIVVO_TEMPLATE_DIR}box/article_breadcrumb.tpl" />
				<div id="dynamic_box_left">
					<div id="box_left_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/sections.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_newsletter.tpl" nocache="1" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_poll.tpl" nocache="1" />
					</div>
				</div>
				<div id="dynamic_box_center">
					<div id="box_center_holder">
						<div id="article_holder">
							<h1><vte:value select="{article.get_title}" /></h1>
							<div class="article_metadata">
								<vte:if test="{VIVVO_ARTICLE_SHOW_DATE}">
									<span class="metadata_time"><vte:value select="{article.get_created}" /></span>
								</vte:if> 
								<vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR}">
									<vte:if test="{VIVVO_ARTICLE_SHOW_AUTHOR_INFO}">
										<a href="{article.get_author_href}"><vte:value select="{article.get_author_name}" /></a>
										<vte:else>
											<vte:value select="{article.get_author_name}" />
										</vte:else>
									</vte:if>
								</vte:if> 
							</div>
							<vte:include file="{VIVVO_TEMPLATE_DIR}box/font_size.tpl" />
							<div id="article_body">
								<vte:if test="{article.image}">
									<div class="image" style="width:{VIVVO_ARTICLE_MEDIUM_IMAGE_WIDTH}px;">
										<vte:if test="{article.get_image_caption}">
                                            <vte:variable name="image_caption" value="{article.get_image_caption}" />
                                            <vte:else>
                                                <vte:variable name="image_caption" value="{article.get_title}" />
                                            </vte:else>
                                        </vte:if>
										<img src="{VIVVO_STATIC_URL}thumbnail.php?file={article.image}&amp;size=article_medium" alt="{image_caption}" />
										<span class="image_caption"><vte:value select="{article.get_image_caption}" /></span>
									</div>
								</vte:if>
                                <vte:if test="{article.get_abstract}">
                                    <p><vte:value select="{article.get_abstract}" /></p>
                                </vte:if>
								<vte:value select="{article.get_body}" />
							</div>
							<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_social_bookmarks.tpl" />
							<vte:if test="{article.show_comment}">
								<vte:include file="{VIVVO_TEMPLATE_DIR}box/comments.tpl" />
							</vte:if>
						</div>
					</div>							
				</div>
				<div id="dynamic_box_right">
					<div id="box_right_holder">
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_tools.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_image_gallery_lightbox.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_tags.tpl" nocache="1" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/article_vote.tpl" nocache="1" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/plugin_multiple_attachments.tpl" />
                        <vte:include file="{VIVVO_TEMPLATE_DIR}box/category_related.tpl" />
						<vte:include file="{VIVVO_TEMPLATE_DIR}box/related_news.tpl" />
					</div>
				</div>
			</div>
			<div id="footer">
				<vte:include file="{VIVVO_TEMPLATE_DIR}box/footer.tpl" />
			</div>
		</div>
        <vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
			<script type="text/javascript">_gaq.push(['_trackEvent', 'Article', 'View', '<vte:value select="{article.get_id}" />', 1]);</script>
		</vte:if>
	</body>
</html>